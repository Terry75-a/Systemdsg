<?php

namespace App\Libraries\Asistencia;

use CodeIgniter\Database\BaseConnection;

/**
 * Borrado de los datos de UNA empresa (todo lo que pertenece a un admin_id).
 *
 * Categorías que el admin puede elegir:
 *   personal       empleados y practicantes, con su biometría, sesiones, avisos
 *                  y TODAS sus asistencias e incidencias (no quedan huérfanas)
 *   asistencias    marcaciones y su auditoría (las horas se calculan de aquí)
 *   incidencias    incidencias y justificaciones, con sus fotos de evidencia
 *   horarios       horarios (al personal que queda se le quita el horario)
 *   notificaciones avisos del panel del admin y de su personal
 *
 * Nunca toca la cuenta del admin, su configuración, sus festivos ni los
 * datos de otras empresas. Las fotos se borran del disco solo después de
 * que la transacción se confirma.
 */
class BorradoDatos
{
    public const CATEGORIAS = ['personal', 'asistencias', 'incidencias', 'horarios', 'notificaciones'];

    /** Carpetas de writable/uploads con archivos «user_<id>_…» */
    private const CARPETAS = ['rostros', 'evidencias'];

    public function __construct(private BaseConnection $db, private int $adminId)
    {
    }

    /** Ids del personal (empleados y practicantes) de esta empresa. */
    private function idsPersonal(): array
    {
        $rows = $this->db->table('users')->select('id')
            ->where('admin_id', $this->adminId)
            ->whereIn('role', ['Empleado', 'Practicante'])
            ->get()->getResultArray();
        return array_map('intval', array_column($rows, 'id'));
    }

    /** Filtro «de esta empresa»: por admin_id o por pertenecer a su personal. */
    private function deLaEmpresa(string $tabla, array $ids)
    {
        $b = $this->db->table($tabla)->groupStart()->where('admin_id', $this->adminId);
        if ($ids) $b->orWhereIn('user_id', $ids);
        return $b->groupEnd();
    }

    /** Cuántos registros hay hoy en cada categoría (para mostrarlo antes de borrar). */
    public function contar(): array
    {
        $ids = $this->idsPersonal();
        return [
            'personal'       => count($ids),
            'asistencias'    => $this->deLaEmpresa('attendance', $ids)->countAllResults(),
            'incidencias'    => $this->deLaEmpresa('incidents', $ids)->countAllResults(),
            'horarios'       => $this->db->table('schedules')->where('admin_id', $this->adminId)->countAllResults(),
            'notificaciones' => $this->deLaEmpresa('notifications', $ids)->countAllResults(),
        ];
    }

    /**
     * Borra las categorías elegidas. Devuelve cuántos registros se borraron
     * de cada una. Lanza una excepción (y no borra nada) si algo falla.
     */
    public function borrar(array $categorias): array
    {
        $cats = array_values(array_intersect(self::CATEGORIAS, $categorias));
        if (! $cats) return [];
        // Borrar el personal arrastra sus asistencias e incidencias.
        if (in_array('personal', $cats, true)) {
            $cats = array_values(array_unique(array_merge($cats, ['asistencias', 'incidencias'])));
        }

        $ids      = $this->idsPersonal();
        $archivos = [];
        $hecho    = [];

        $this->db->transException(true)->transStart();

        if (in_array('asistencias', $cats, true)) {
            $archivos = array_merge($archivos, $this->evidencias('attendance', $ids));
            $this->deLaEmpresa('attendance_log', $ids)->delete();
            $hecho['asistencias'] = $this->borrarYContar('attendance', $ids);
        }

        if (in_array('incidencias', $cats, true)) {
            $archivos = array_merge($archivos, $this->evidencias('incidents', $ids));
            $hecho['incidencias'] = $this->borrarYContar('incidents', $ids);
        }

        if (in_array('notificaciones', $cats, true)) {
            $hecho['notificaciones'] = $this->borrarYContar('notifications', $ids);
        }

        if (in_array('horarios', $cats, true)) {
            $this->db->table('users')->where('admin_id', $this->adminId)
                ->whereIn('role', ['Empleado', 'Practicante'])
                ->update(['schedule_id' => null]);
            $this->db->table('schedules')->where('admin_id', $this->adminId)->delete();
            $hecho['horarios'] = $this->db->affectedRows();
        }

        if (in_array('personal', $cats, true) && $ids) {
            foreach (['webauthn_credentials', 'push_subscriptions', 'session_log', 'notifications'] as $t) {
                if ($this->db->tableExists($t)) {
                    $this->db->table($t)->whereIn('user_id', $ids)->delete();
                }
            }
            $this->db->table('users')->whereIn('id', $ids)
                ->where('admin_id', $this->adminId)
                ->whereIn('role', ['Empleado', 'Practicante'])
                ->delete();
            $hecho['personal'] = $this->db->affectedRows();
            // Fotos del rostro y cualquier evidencia del personal borrado
            foreach ($ids as $id) {
                foreach (self::CARPETAS as $c) {
                    foreach (glob(WRITEPATH . 'uploads/' . $c . '/user_' . $id . '_*') ?: [] as $f) {
                        $archivos[] = $f;
                    }
                }
            }
        } elseif (in_array('personal', $cats, true)) {
            $hecho['personal'] = 0;
        }

        $this->db->transComplete();

        // Recién confirmada la transacción, se quitan los archivos.
        foreach (array_unique($archivos) as $f) {
            if (is_file($f)) @unlink($f);
        }

        return $hecho;
    }

    private function borrarYContar(string $tabla, array $ids): int
    {
        $this->deLaEmpresa($tabla, $ids)->delete();
        return $this->db->affectedRows();
    }

    /** Rutas en disco de las fotos de evidencia referenciadas en una tabla. */
    private function evidencias(string $tabla, array $ids): array
    {
        $rows = $this->deLaEmpresa($tabla, $ids)->select('evidencia')
            ->where('evidencia IS NOT NULL')->where('evidencia !=', '')
            ->get()->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $nombre = basename((string) $r['evidencia']);
            // Solo nombres generados por el sistema (sin rutas ni traversal)
            if (! preg_match('/^user_\d+_[A-Za-z0-9_\-]+\.(jpe?g|png|webp)$/', $nombre)) continue;
            $out[] = WRITEPATH . 'uploads/evidencias/' . $nombre;
        }
        return $out;
    }
}
