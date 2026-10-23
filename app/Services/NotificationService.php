<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;
use App\Models\AttendanceModel;

class NotificationService
{
    protected NotificationModel $notificationModel;
    protected UserModel $userModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;
    protected AttendanceModel $attendanceModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
        $this->incidentModel = new IncidentModel();
        $this->attendanceModel = new AttendanceModel();
    }

    // ════════════════════════════════════════════════════════
    // NOTIFICACIONES PARA ADMIN
    // ════════════════════════════════════════════════════════

    /**
     * Notificar cuando se crea un horario
     */
    public function horarioCreado(int $scheduleId, int $adminId): void
    {
        $schedule = (new \App\Models\ScheduleModel())->find($scheduleId);
        if (!$schedule) return;

        $admins = $this->getAdminsToNotify($adminId, $schedule['admin_id']);
        
        foreach ($admins as $admin) {
            $this->createNotification([
                'admin_id' => $admin['id'],
                'tipo'     => 'horario_creado',
                'titulo'   => 'Nuevo horario creado',
                'mensaje'  => "Se creó el horario «{$schedule['nombre']}» ({$schedule['tipo']}) para {$schedule['dias']} {$schedule['hora_entrada']}-{$schedule['hora_salida']}",
                'datos'    => ['schedule_id' => $scheduleId, 'tipo' => $schedule['tipo'], 'link' => 'admin/horarios'],
            ]);
        }
    }

    /**
     * Notificar cuando un empleado/practicante marca asistencia
     */
    public function asistenciaMarcada(int $attendanceId, int $adminId): void
    {
        $att = (new \App\Models\AttendanceModel())->find($attendanceId);
        if (!$att) return;

        $admins = $this->getAdminsToNotify($adminId, $att['admin_id']);
        $tipo = $att['status'] === 'late' ? 'tardanza' : ($att['time_out'] ? 'salida' : 'entrada');

        foreach ($admins as $admin) {
            $this->createNotification([
                'admin_id' => $admin['id'],
                'tipo'     => 'asistencia_' . $tipo,
                'titulo'   => $this->getTituloAsistencia($tipo),
                'mensaje'  => "{$att['name']} ({$att['dni']}) marcó {$tipo} a las " . substr($att[$tipo === 'entrada' ? 'time_in' : 'time_out'], 0, 5),
                'datos'    => ['attendance_id' => $attendanceId, 'tipo' => $tipo, 'link' => 'admin/asistencias'],
            ]);
        }
    }

    /**
     * Notificar cuando se crea una incidencia
     */
    public function incidenciaCreada(int $incidentId, int $adminId): void
    {
        $inc = (new \App\Models\IncidentModel())->find($incidentId);
        if (!$inc) return;

        $admins = $this->getAdminsToNotify($adminId, $inc['admin_id']);

        foreach ($admins as $admin) {
            $this->createNotification([
                'admin_id' => $admin['id'],
                'tipo'     => 'incidencia_creada',
                'titulo'   => 'Nueva incidencia: ' . $inc['tipo'],
                'mensaje'  => "{$inc['name']} - {$inc['detalle']}",
                'datos'    => ['incident_id' => $incidentId, 'tipo' => $inc['tipo'], 'link' => 'admin/incidencias'],
            ]);
        }
    }

    /**
     * Notificar cuando se resuelve/rechaza una incidencia
     */
    public function incidenciaActualizada(int $incidentId, int $adminId, string $nuevoEstado): void
    {
        $inc = (new \App\Models\IncidentModel())->find($incidentId);
        if (!$inc) return;

        $admins = $this->getAdminsToNotify($adminId, $inc['admin_id']);

        foreach ($admins as $admin) {
            $this->createNotification([
                'admin_id' => $admin['id'],
                'tipo'     => 'incidencia_actualizada',
                'titulo'   => 'Incidencia actualizada: ' . $inc['tipo'],
                'mensaje'  => "La incidencia de {$inc['name']} cambió a: {$nuevoEstado}",
                'datos'    => ['incident_id' => $incidentId, 'estado' => $nuevoEstado, 'link' => 'admin/incidencias'],
            ]);
        }

        // Avisar también al empleado dueño de la incidencia
        $empleadoId = (int) ($inc['user_id'] ?? 0);
        if ($empleadoId > 0) {
            $this->createNotification([
                'admin_id' => null,
                'user_id'  => $empleadoId,
                'tipo'     => 'incidencia_estado',
                'titulo'   => 'Tu incidencia cambió a: ' . $nuevoEstado,
                'mensaje'  => "Incidencia «{$inc['tipo']}» del " . date('d/m/Y', strtotime((string) ($inc['fecha'] ?? 'now')))
                    . ' — ' . $inc['detalle'],
                'datos'    => ['incident_id' => $incidentId, 'estado' => $nuevoEstado, 'link' => 'mi-panel/incidencias'],
            ]);
        }
    }

    // ════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════

    private function getAdminsToNotify(int $triggerAdminId, int $ownerAdminId): array
    {
        $userModel = new UserModel();
        
        // Notificar al admin dueño del recurso
        $admins = [$userModel->find($ownerAdminId)];
        
        // Si quien dispara la acción es otro admin, también notificarle
        if ($triggerAdminId !== $ownerAdminId) {
            $triggerAdmin = $userModel->find($triggerAdminId);
            if ($triggerAdmin && $triggerAdmin['role'] === 'Admin') {
                $admins[] = $triggerAdmin;
            }
        }
        
        // Filtrar nulos y duplicados
        return array_values(array_filter(array_unique($admins, SORT_REGULAR), fn($a) => $a !== null));
    }

    private function createNotification(array $data): void
    {
        $datos = array_merge($data['datos'] ?? [], $data['extra_datos'] ?? []);
        $link = (string) ($datos['link'] ?? '');
        if ($link === '') {
            $link = $data['user_id'] ?? null
                ? 'mi-panel'
                : 'admin';
        }
        $datos['link'] = $link;

        $insertId = $this->notificationModel->insert([
            'admin_id'  => $data['admin_id'] ?? null,
            'user_id'   => $data['user_id'] ?? null,
            'tipo'      => $data['tipo'],
            'titulo'    => $data['titulo'],
            'mensaje'   => $data['mensaje'],
            'datos'     => json_encode($datos),
            'leida'     => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Web Push (silencioso: si falla, no interrumpe)
        try {
            $targets = [];
            if (! empty($data['admin_id'])) {
                $targets[] = (int) $data['admin_id'];
            }
            if (! empty($data['user_id'])) {
                $targets[] = (int) $data['user_id'];
            }
            if ($targets !== []) {
                $url = $link;
                if (strpos($url, 'http') !== 0) {
                    $url = base_url($link);
                }
                service('webpush')->enviarAUsuarios(
                    $targets,
                    (string) $data['titulo'],
                    (string) $data['mensaje'],
                    $url,
                    (string) ($data['tipo'] ?? 'dsg')
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'Web Push falló para notificación {id}: {m}', [
                'id' => (int) $insertId,
                'm'  => $e->getMessage(),
            ]);
        }
    }

    private function getTituloAsistencia(string $tipo): string
    {
        return match ($tipo) {
            'entrada' => 'Entrada registrada',
            'salida'  => 'Salida registrada',
            'tardanza'=> '⚠️ Tardanza detectada',
            default   => 'Asistencia registrada',
        };
    }

    /** Notificación in-app + Web Push para un empleado. */
    private function notifyEmpleado(array $user, string $tipo, string $titulo, string $mensaje, string $link = 'mi-panel/asistencias'): void
    {
        $uid = (int) ($user['id'] ?? 0);
        if ($uid <= 0) {
            return;
        }
        $this->createNotification([
            'admin_id' => null,
            'user_id'  => $uid,
            'tipo'     => $tipo,
            'titulo'   => $titulo,
            'mensaje'  => $mensaje,
            'datos'    => ['link' => $link],
        ]);
    }

    // ════════════════════════════════════════════════════════
    // CORREOS AL EMPLEADO / PRACTICANTE
    // ════════════════════════════════════════════════════════

    /**
     * Avisa al empleado que llegó tarde: debe intentar llegar más temprano.
     */
    public function avisarTardanza(array $user, string $horaBase, string $horaMarcada, int $tolerancia): void
    {
        $nombre = (string) ($user['name'] ?? 'Empleado');
        $this->notifyEmpleado($user, 'tardanza_aviso', 'Aviso de tardanza', "Registraste tu entrada a las {$horaMarcada} (tu horario empieza {$horaBase}, tolerancia {$tolerancia} min). Llega más temprano.");
        $this->emailAlEmpleado(
            $user,
            'Aviso de tardanza · ' . date('d/m/Y'),
            "<p>Hola <strong>{$nombre}</strong>,</p>"
            . "<p>Registraste tu entrada a las <strong>{$horaMarcada}</strong>, "
            . "pero tu horario empieza a las <strong>{$horaBase}</strong> "
            . "(tolerancia de {$tolerancia} min).</p>"
            . "<p>Por favor intenta llegar más temprano para evitar incidencias.</p>"
            . '<p>-- Panel de asistencia DSG</p>'
        );
    }

    /**
     * Avisa al empleado que se retiró antes de su hora de salida.
     */
    public function avisarSalidaAnticipada(array $user, string $horaSalida, string $horaMarcada): void
    {
        $nombre = (string) ($user['name'] ?? 'Empleado');
        $this->notifyEmpleado($user, 'salida_anticipada', 'Salida anticipada', "Registraste tu salida a las {$horaMarcada}, pero tu jornada termina {$horaSalida}. Quedó en revisión.");
        $this->emailAlEmpleado(
            $user,
            'Aviso de salida anticipada · ' . date('d/m/Y'),
            "<p>Hola <strong>{$nombre}</strong>,</p>"
            . "<p>Registraste tu salida a las <strong>{$horaMarcada}</strong>, "
            . "pero tu jornada termina a las <strong>{$horaSalida}</strong>.</p>"
            . "<p>La salida quedó en revisión. Si fue un permiso, coméntalo con tu administrador.</p>"
            . '<p>-- Panel de asistencia DSG</p>'
        );
    }

    /**
     * Avisa al empleado que se le registró falta por no marcar entrada.
     */
    public function avisarFalta(array $user, string $fecha): void
    {
        $nombre = (string) ($user['name'] ?? 'Empleado');
        $fechaL = date('d/m/Y', strtotime($fecha));
        $this->notifyEmpleado($user, 'falta', 'Falta registrada', "El {$fechaL} no registraste tu entrada; se marcó una falta. Repórtala con tu administrador si fue justificada.");
        $this->emailAlEmpleado(
            $user,
            'Aviso de falta · ' . $fechaL,
            "<p>Hola <strong>{$nombre}</strong>,</p>"
            . "<p>El <strong>{$fechaL}</strong> no registraste tu entrada, "
            . "por lo que se marcó una <strong>falta</strong> en tu asistencia.</p>"
            . "<p>Si hubo un motivo justificado, repórtalo con tu administrador.</p>"
            . '<p>-- Panel de asistencia DSG</p>'
        );
    }

    /**
     * Avisa al empleado que olvidó marcar su salida.
     */
    public function avisarSalidaNoRegistrada(array $user, string $fecha): void
    {
        $nombre = (string) ($user['name'] ?? 'Empleado');
        $fechaL = date('d/m/Y', strtotime($fecha));
        $this->notifyEmpleado($user, 'salida_no_registrada', 'Salida no registrada', "El {$fechaL} registraste tu entrada pero no marcaste tu salida.");
        $this->emailAlEmpleado(
            $user,
            'Salida no registrada · ' . $fechaL,
            "<p>Hola <strong>{$nombre}</strong>,</p>"
            . "<p>El <strong>{$fechaL}</strong> registraste tu entrada pero <strong>no marcaste tu salida</strong>.</p>"
            . "<p>Recuerda marcarla siempre para que tu asistencia sea correcta.</p>"
            . '<p>-- Panel de asistencia DSG</p>'
        );
    }

    /**
     * Envía un correo HTML al empleado. Nunca rompe el flujo:
     * si el correo falla, sólo se registra el error.
     */
    private function emailAlEmpleado(array $user, string $subject, string $html): void
    {
        try {
            $destino = trim((string) ($user['email'] ?? ''));
            if ($destino === '' || ! filter_var($destino, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            $cfg   = (new \App\Models\ConfigModel())->getConfig((int) ($user['admin_id'] ?? 0));
            $email = service('email');
            $email->setTo($destino);
            $email->setFrom(
                trim((string) ($cfg['email'] ?? '')) ?: 'no-reply@dsg.pe',
                (string) ($cfg['empresa'] ?? 'DSG')
            );
            $email->setSubject($subject);
            $email->setMessage($html);
            $email->setMailType('html');
            $email->send(false);
        } catch (\Throwable $e) {
            log_message('error', 'Correo al empleado (user {id}) falló: {m}', [
                'id' => (int) ($user['id'] ?? 0),
                'm'  => $e->getMessage(),
            ]);
        }
    }
}
