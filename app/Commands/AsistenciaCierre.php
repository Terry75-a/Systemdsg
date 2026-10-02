<?php

namespace App\Commands;

use App\Libraries\Asistencia\Jornada;
use App\Models\AttendanceLogModel;
use App\Models\AttendanceModel;
use App\Models\FestivoModel;
use App\Models\IncidentModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use DateTimeImmutable;

/**
 * Cierra la jornada de un día:
 *  - marca 'absent' a quien no marcó entrada
 *  - marca 'no_exit' a quien no marcó salida
 *  - crea la incidencia 'Falta' correspondiente
 *
 * Sólo actúa cuando el día ya terminó (o sobre fechas pasadas).
 */
class AsistenciaCierre extends BaseCommand
{
    protected $group       = 'Asistencia';
    protected $name        = 'asistencia:cierre';
    protected $description = 'Cierra un día de asistencia: marca faltas y salidas no registradas.';
    protected $usage       = 'asistencia:cierre [--fecha=YYYY-MM-DD] [--admin=ID] [--dry-run]';
    protected $options     = [
        '--fecha'   => 'Día a cerrar (default: hoy)',
        '--admin'   => 'Sólo el equipo de ese admin_id',
        '--dry-run' => 'Muestra lo que haría sin escribir nada',
    ];

    public function run(array $params)
    {
        $params = self::normalizarParams($params);

        $fecha   = (string) ($params['fecha'] ?? date('Y-m-d'));
        $adminId = array_key_exists('admin', $params) && $params['admin'] !== null && $params['admin'] !== ''
            ? (int) $params['admin']
            : null;
        $dryRun  = array_key_exists('dry-run', $params);

        if (! DateTimeImmutable::createFromFormat('Y-m-d', $fecha)
            || DateTimeImmutable::createFromFormat('Y-m-d', $fecha)->format('Y-m-d') !== $fecha) {
            CLI::error("Fecha inválida: {$fecha} (usa --fecha=YYYY-MM-DD)");
            return 1;
        }

        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        /** @var AttendanceModel $attendanceModel */
        $attendanceModel = model(AttendanceModel::class);
        /** @var IncidentModel $incidentModel */
        $incidentModel = model(IncidentModel::class);
        /** @var AttendanceLogModel $logModel */
        $logModel = model(AttendanceLogModel::class);
        /** @var FestivoModel $festivoModel */
        $festivoModel = model(FestivoModel::class);
        /** @var ScheduleModel $scheduleModel */
        $scheduleModel = model(ScheduleModel::class);

        $users = $userModel
            ->where('estado', 'Activo')
            ->whereIn('role', ['Empleado', 'Practicante'])
            ->orderBy('admin_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $users = array_values(array_filter($users, static fn ($u) => ! empty($u['admin_id'])));

        if ($adminId !== null) {
            $users = array_values(array_filter($users, static fn ($u) => (int) $u['admin_id'] === $adminId));
        }

        $cierre = 0;
        $plan   = [];

        foreach ($users as $user) {
            $uid    = (int) $user['id'];
            $userAd = (int) $user['admin_id'];

            if ($festivoModel->esFestivo($fecha, $userAd)) {
                continue;
            }

            $schedules = $scheduleModel->getActive($userAd);
            $schedule  = Jornada::elegir(
                $schedules,
                (int) ($user['schedule_id'] ?? 0),
                (string) ($user['role'] ?? 'Empleado')
            );

            if (! Jornada::esDiaLaborable($schedule, $fecha)) {
                continue;
            }

            if (! Jornada::diaCerrado($schedule, $fecha)) {
                $cierre++;
                continue; // el día sigue abierto: nada que cerrar todavía
            }

            $att = $attendanceModel->findByUserAndDate($uid, $fecha);

            if (! $att) {
                $plan[] = [
                    'tipo' => 'absent',
                    'user' => $user,
                    'motivo' => 'No registró entrada',
                ];
                continue;
            }

            $tieneEntrada = trim((string) ($att['time_in'] ?? '')) !== '';
            $tieneSalida  = trim((string) ($att['time_out'] ?? '')) !== '';

            if ($tieneEntrada && ! $tieneSalida && ($att['status'] ?? '') !== 'no_exit') {
                $plan[] = [
                    'tipo' => 'no_exit',
                    'user' => $user,
                    'att'  => $att,
                    'motivo' => 'Entrada registrada sin salida',
                ];
            }
        }

        if ($plan === []) {
            $mensaje = $cierre > 0
                ? "Día {$fecha} aún no cerrado: {$cierre} personas esperan a que termine su jornada."
                : "Día {$fecha}: nada que cerrar.";
            CLI::write($mensaje, 'green');
            return 0;
        }

        if ($dryRun) {
            CLI::write(sprintf('[dry-run] %d acciones para %s:', count($plan), $fecha), 'yellow');
            foreach ($plan as $item) {
                CLI::write(sprintf('  - %-8s %s (%s)', $item['tipo'], $item['user']['name'], $item['motivo']));
            }
            return 0;
        }

        $hechos = ['absent' => 0, 'no_exit' => 0];
        $errores = 0;

        foreach ($plan as $item) {
            $user = $item['user'];
            $uid  = (int) $user['id'];

            $db = null;
            try {
                $db = \Config\Database::connect();
                $db->transBegin();

                if ($item['tipo'] === 'absent') {
                    $attId = $attendanceModel->insert([
                        'admin_id'    => (int) $user['admin_id'],
                        'user_id'     => $uid,
                        'name'        => $user['name'],
                        'dni'         => $user['dni'] ?? '',
                        'date'        => $fecha,
                        'status'      => 'absent',
                        'observacion' => 'Cierre automático: sin registro de entrada',
                    ], false);

                    $this->incidenciaFalta($incidentModel, $user, (int) $attId, $fecha, 'Cierre automático: no marcó entrada');
                    $this->auditar($logModel, (int) $attId, (int) $user['admin_id'], $uid, 'cierre', 'status', null, 'absent');
                    $hechos['absent']++;
                } else {
                    $att = $item['att'];
                    $attendanceModel->update((int) $att['id'], [
                        'status'      => 'no_exit',
                        'observacion' => trim((string) ($att['observacion'] ?? '')) . ' [salida no registrada]',
                    ]);
                    $this->auditar(
                        $logModel,
                        (int) $att['id'],
                        (int) $user['admin_id'],
                        $uid,
                        'cierre',
                        'status',
                        (string) $att['status'],
                        'no_exit'
                    );
                    $hechos['no_exit']++;
                }

                $db->transCommit();
            } catch (\Throwable $e) {
                $db?->transRollback();
                $errores++;
                log_message('error', 'asistencia:cierre falló para user {id}: {m}', ['id' => $uid, 'm' => $e->getMessage()]);
                CLI::error("  ✗ {$user['name']}: {$e->getMessage()}");
            }
        }

        CLI::write(sprintf(
            'Cierre de %s: %d faltas, %d sin salida%s.',
            $fecha,
            $hechos['absent'],
            $hechos['no_exit'],
            $errores > 0 ? ", {$errores} errores" : ''
        ), $errores > 0 ? 'yellow' : 'green');

        return $errores > 0 ? 1 : 0;
    }

    /**
     * spark no soporta `--clave=valor`: lo deja como clave única.
     * Normalizamos para poder usar ambas formas.
     */
    private static function normalizarParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (! is_string($key) || ! str_contains($key, '=')) {
                continue;
            }
            [$name, $val] = explode('=', $key, 2);
            unset($params[$key]);
            $params[$name] = $val;
        }

        return $params;
    }

    private function incidenciaFalta(IncidentModel $model, array $user, int $attId, string $fecha, string $detalle): void
    {
        if ($attId <= 0 || $model->findByAttId($attId)) {
            return;
        }

        $model->insert([
            'admin_id' => (int) $user['admin_id'],
            'att_id'   => $attId,
            'user_id'  => (int) $user['id'],
            'name'     => $user['name'],
            'tipo'     => 'Falta',
            'fecha'    => $fecha,
            'detalle'  => $detalle,
            'estado'   => 'Pendiente',
        ], false);
    }

    private function auditar(AttendanceLogModel $log, int $attId, int $adminId, int $userId, string $accion, string $campo, ?string $antes, string $despues): void
    {
        $log->registrar([
            'attendance_id'  => $attId > 0 ? $attId : null,
            'admin_id'       => $adminId,
            'user_id'        => $userId,
            'accion'         => $accion,
            'campo'          => $campo,
            'valor_anterior' => $antes,
            'valor_nuevo'    => $despues,
            'motivo'         => 'Cierre automático de jornada',
            'autor'          => 'sistema',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
