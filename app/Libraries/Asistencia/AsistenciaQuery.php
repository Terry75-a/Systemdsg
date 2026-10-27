<?php
namespace App\Libraries\Asistencia;

use App\Models\AttendanceModel;

/**
 * Consultas de asistencia compartidas por las páginas de
 * /admin/asistencias, /admin/reportes y la exportación CSV.
 */
class AsistenciaQuery
{
    public function __construct(private AttendanceModel $attendance)
    {
    }

    /**
     * Aplica los filtros de la barra (?fecha_inicio, ?fecha_fin, ?todo,
     * ?estado, ?persona) y devuelve las filas junto con el rango usado.
     *
     * @return array{filas: array, fecha_inicio: ?string, fecha_fin: ?string}
     */
    public function filtrar(array $params, int $adminId): array
    {
        if (! empty($params['todo'])) {
            $fi = $ff = null;
            $filas = $this->attendance->getAll($adminId);
        } else {
            [$fi, $ff] = self::rango($params);
            $filas = $this->porRango($fi, $ff, $adminId);
        }

        return [
            'filas'        => self::porEstado(self::porPersona($filas, (string) ($params['persona'] ?? '')), (string) ($params['estado'] ?? '')),
            'fecha_inicio' => $fi,
            'fecha_fin'    => $ff,
        ];
    }

    /**
     * Deja sólo las filas de una persona concreta: casa por DNI exacto o
     * por nombre parcial (sin distinguir mayúsculas). Vacío = sin filtrar.
     */
    public static function porPersona(array $filas, string $persona): array
    {
        $q = mb_strtolower(trim($persona));
        if ($q === '') {
            return $filas;
        }

        return array_values(array_filter($filas, function ($f) use ($q) {
            $dni  = mb_strtolower(trim((string) ($f['dni'] ?? '')));
            $name = mb_strtolower(trim((string) ($f['name'] ?? '')));

            if ($dni !== '' && $dni === $q) {
                return true;
            }

            return $name !== '' && mb_strpos($name, $q) !== false;
        }));
    }

    /** Deja sólo las filas con el estado pedido. Vacío / 'todos' = sin filtrar. */
    public static function porEstado(array $filas, string $estado): array
    {
        $estado = trim($estado);
        if ($estado === '' || $estado === 'todos') {
            return $filas;
        }

        return array_values(array_filter($filas, fn ($f) => ($f['status'] ?? '') === $estado));
    }

    /** Filas de un rango ya normalizado (días iguales incluidos). */
    public function porRango(string $fi, string $ff, int $adminId): array
    {
        return ($fi === $ff)
            ? $this->attendance->getByDate($fi, $adminId)
            : $this->attendance->getByRange($fi, $ff, $adminId);
    }

    /** Todo el histórico del admin (para exportar sin rango). */
    public function getAllFilas(int $adminId): array
    {
        return $this->attendance->getAll($adminId);
    }

    /**
     * Totales de TODO el rango filtrado (no de la página actual).
     * Es lo que alimenta los KPIs de /admin/asistencias y /admin/reportes.
     *
     * @return array{total: int, presentes: int, tardanzas: int, faltas: int, sin_salida: int}
     */
    public static function resumir(array $filas): array
    {
        $resumen = [
            'total'      => count($filas),
            'presentes'  => 0,
            'tardanzas'  => 0,
            'faltas'     => 0,
            'sin_salida' => 0,
        ];

        foreach ($filas as $fila) {
            $estado = (string) ($fila['status'] ?? '');
            if     ($estado === 'present')  $resumen['presentes']++;
            elseif ($estado === 'late')     $resumen['tardanzas']++;
            elseif ($estado === 'absent')   $resumen['faltas']++;
            elseif ($estado === 'no_exit')  $resumen['sin_salida']++;
        }

        return $resumen;
    }

    /**
     * Normaliza el rango de fechas: vacío = hoy, invertido = corregido.
     * Ignora entradas que no sean texto plano (arrays, etc.).
     *
     * @return array{0: string, 1: string} [fecha_inicio, fecha_fin]
     */
    public static function rango(array $params): array
    {
        $fi = self::texto($params['fecha_inicio'] ?? '');
        $ff = self::texto($params['fecha_fin'] ?? '');

        if ($fi === '' || $ff === '') {
            $fi = $ff = date('Y-m-d');
        } elseif ($fi > $ff) {
            [$fi, $ff] = [$ff, $fi];
        }

        return [$fi, $ff];
    }

    private static function texto($valor): string
    {
        return is_string($valor) ? trim($valor) : '';
    }
}
