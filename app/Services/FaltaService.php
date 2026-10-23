<?php

namespace App\Services;

use App\Models\AttendanceModel;
use App\Models\ConfigModel;
use App\Models\FestivoModel;
use App\Models\IncidentModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;

/**
 * Genera faltas automáticas para empleados/practicantes activos
 * que no marcaron entrada el día de hoy (o de la fecha indicada).
 */
class FaltaService
{
    /**
     * @return int cantidad de faltas generadas
     */
    public function generarFaltas(?string $fecha = null): int
    {
        $fecha = $fecha ?: date('Y-m-d');
        if ($fecha > date('Y-m-d')) {
            return 0; // nada que generar en el futuro
        }
        $ahora = date('H:i:s');
        $hoyNombre = $this->nombreDia($fecha);

        $schedules = [];
        foreach ((new ScheduleModel())->where('estado', 'Activo')->findAll() as $s) {
            $schedules[(int) $s['id']] = $s;
        }
        if (! $schedules) {
            return 0;
        }

        $festivos = array_column((new FestivoModel())->getAll(), 'fecha');

        $q = (new UserModel())->whereIn('role', ['Empleado', 'Practicante'])->where('estado', 'Activo');
        $users = $q->findAll();
        $generadas = 0;

        foreach ($users as $user) {
            $scheduleId = (int) ($user['schedule_id'] ?? 0);
            if ($scheduleId <= 0 || ! isset($schedules[$scheduleId])) {
                continue;
            }
            $sc = $schedules[$scheduleId];

            // Día laborable según el string "Lun - Vie" / "Lun, Mié" / "Lunes a Viernes"
            if (! $this->esDiaLaborable((string) ($sc['dias'] ?? ''), $hoyNombre)) {
                continue;
            }

            // Festivo global o del admin
            if (in_array($fecha, $festivos, true)) {
                continue;
            }

            // Sólo después de entrada + tolerancia + 60 min de gracia (para poder marcar tarde)
            $tolerancia = (int) ($sc['tolerancia'] ?? 0);
            $entrada    = (string) ($sc['hora_entrada'] ?? '08:00');
            $limite     = strtotime($fecha . ' ' . $entrada) + ($tolerancia + 60) * 60;
            if ($fecha === date('Y-m-d') && time() < $limite) {
                continue;
            }

            $userId = (int) $user['id'];
            $att = (new AttendanceModel())->findByUserAndDate($userId, $fecha);
            if ($att) {
                continue; // ya marcó algo hoy
            }

            // Insertar falta
            (new AttendanceModel())->insert([
                'admin_id'    => (int) ($user['admin_id'] ?? 0) ?: null,
                'user_id'     => $userId,
                'name'        => $user['name'] ?? '',
                'dni'         => $user['dni'] ?? '',
                'date'        => $fecha,
                'time_in'     => null,
                'time_out'    => null,
                'status'      => 'absent',
                'observacion' => 'Falta automática: no registró entrada',
                'evidencia'   => '',
                'lat'         => null,
                'lng'         => null,
                'ip'          => null,
                'geo_ok'      => null,
            ]);
            $attId = (int) db_connect()->insertID();

            // Incidencia para que el admin pueda verla/revisarla
            (new IncidentModel())->insert([
                'admin_id'      => (int) ($user['admin_id'] ?? 0) ?: null,
                'att_id'        => $attId,
                'user_id'       => $userId,
                'name'          => $user['name'] ?? '',
                'tipo'          => 'Falta',
                'fecha'         => $fecha . ' 00:00:00',
                'detalle'       => "No registró entrada el {$fecha} (horario {$sc['nombre']})",
                'estado'        => 'Pendiente',
                'justificacion' => '',
            ]);

            // Aviso in-app + email (si SMTP configurado)
            (new NotificationService())->avisarFalta($user, $fecha);

            $generadas++;
        }

        return $generadas;
    }

    /** Convierte "Lun - Vie" / "Lunes a Viernes" / "Lun, Mié, Vie" en lista de nombres completos. */
    private function esDiaLaborable(string $dias, string $hoy): bool
    {
        $map = [
            'lun' => 'lunes', 'mar' => 'martes', 'mie' => 'miércoles', 'mié' => 'miércoles',
            'jue' => 'jueves', 'vie' => 'viernes', 'sab' => 'sábado', 'sáb' => 'sábado', 'dom' => 'domingo',
        ];
        $texto = strtolower(trim($dias));
        if ($texto === '') {
            return true;
        }
        // Normalizar sin acentos para comparar
        $hoyNorm = strtr($hoy, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
        ]);
        $textoNorm = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
        ]);
        $hoyAbrev = array_search($hoyNorm, array_map(
            fn ($v) => strtr($v, [
                'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            ]),
            $map
        ), true);

        // Contiene el día completo?
        if (str_contains($textoNorm, $hoyNorm)) {
            return true;
        }
        // Contiene la abreviatura (lun, mar, ...) como token?
        if ($hoyAbrev !== false && preg_match('/\b' . preg_quote($hoyAbrev, '/') . '\b/', $textoNorm)) {
            return true;
        }
        // Rango "lun - vie" / "lunes a viernes"
        if (preg_match('/(\w{3,10})\s*[-a]\s*(\w{3,10})/', $textoNorm, $m)) {
            $orden = ['domingo' => 0, 'lunes' => 1, 'martes' => 2, 'miercoles' => 3, 'jueves' => 4, 'viernes' => 5, 'sabado' => 6];
            $de = $this->abrevACompleto($m[1]);
            $a  = $this->abrevACompleto($m[2]);
            if (isset($orden[$de], $orden[$a], $orden[$hoyNorm])) {
                $d = $orden[$de];
                $h = $orden[$a];
                $y = $orden[$hoyNorm];
                return $d <= $h ? ($y >= $d && $y <= $h) : ($y >= $d || $y <= $h);
            }
        }
        return false;
    }

    private function abrevACompleto(string $s): string
    {
        $map = [
            'lun' => 'lunes', 'mar' => 'martes', 'mie' => 'miercoles', 'mié' => 'miercoles',
            'jue' => 'jueves', 'vie' => 'viernes', 'sab' => 'sabado', 'sáb' => 'sabado', 'dom' => 'domingo',
        ];
        return $map[$s] ?? $s;
    }

    private function nombreDia(string $fecha): string
    {
        $nombres = [0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sábado'];
        return $nombres[(int) date('w', strtotime($fecha))];
    }
}
