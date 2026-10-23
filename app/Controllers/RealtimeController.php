<?php

namespace App\Controllers;

use CodeIgniter\HTTP\DownloadResponse;

/**
 * Server-Sent Events para actualizaciones en tiempo real
 * 
 * Canales disponibles:
 * - /realtime/admin/asistencias    → Admin: nuevas asistencias, ediciones, incidencias
 * - /realtime/empleado/asistencias → Empleado: su estado de marcación, alertas
 */
class RealtimeController extends BaseController
{
    private $logModel;
    private $attendanceModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->logModel        = new \App\Models\AttendanceLogModel();
        $this->attendanceModel = new \App\Models\AttendanceModel();
    }

    private function guard(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }
        $role = (string) (session()->get('user_role') ?? '');
        if ($role === 'Dev') {
            return redirect()->to(base_url('dios'));
        }
        if ($role !== 'Admin') {
            return redirect()->to(base_url('mi-panel'));
        }
        return null;
    }

    private function checkEmployee(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login-verde'));
        }
        $rol = (string) (session()->get('user_role') ?? '');
        if ($rol === 'Dev') {
            return redirect()->to(base_url('dios'));
        }
        if ($rol === 'Admin') {
            return redirect()->to(base_url('admin'));
        }
        return null;
    }

    public function adminAsistencias()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = (int) session()->get('user_id');
        return $this->sseStream(function ($send) use ($adminId) {
            $lastId = 0;
            $notifModel = new \App\Models\NotificationModel();
            $notifLast = (int) ($notifModel->selectMax('id')->get()->getRow()->id ?? 0);
            while (true) {
                // Verificar nuevas asistencias, ediciones, incidencias
                $eventos = $this->obtenerEventosAdmin($lastId);
                foreach ($eventos as $evt) {
                    $send('message', json_encode($evt), $evt['id']);
                    $lastId = $evt['id'];
                }
                // Notificaciones del admin (horarios, asistencias, incidencias)
                $notifs = $notifModel
                    ->where('admin_id', $adminId)
                    ->where('id >', $notifLast)
                    ->orderBy('id', 'ASC')
                    ->limit(20)
                    ->findAll();
                foreach ($notifs as $n) {
                    $send('message', json_encode([
                        'id'        => (int) $n['id'],
                        'tipo'      => 'notificacion',
                        'datos'     => $n,
                        'timestamp' => time(),
                    ]), (string) $n['id']);
                    $notifLast = (int) $n['id'];
                }
                sleep(3); // Poll cada 3 segundos
            }
        });
    }

    public function empleadoAsistencias()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;
        return $this->sseStream(function ($send) {
            $userId = (int) session()->get('user_id');
            $ultimaAsistencia = $this->obtenerUltimaAsistencia($userId);
            $enviado = false;
            
            while (true) {
                $actual = $this->obtenerUltimaAsistencia($userId);
                
                // Detectar cambios en su asistencia
                if (!$enviado || $this->haCambiado($ultimaAsistencia, $actual)) {
                    $send('message', json_encode([
                        'tipo'        => 'asistencia_actualizada',
                        'asistencia'  => $actual,
                        'timestamp'   => time(),
                    ]));
                    $ultimaAsistencia = $actual;
                    $enviado = true;
                }
                
                // Verificar alertas (tardanza, salida anticipada, etc.)
                $alertas = $this->verificarAlertas($userId);
                if ($alertas) {
                    $send('message', json_encode([
                        'tipo'   => 'alerta',
                        'datos'  => $alertas,
                    ]));
                }
                
                sleep(5);
            }
        });
    }

    private function sseStream(callable $handler)
    {
        // CI 4.7+ no soporta Iterator en el body: streaming directo + exit
        @set_time_limit(0);
        while (ob_get_level() > 0) { @ob_end_clean(); }
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        $send = function ($event, $data, $id = null) {
            if ($id !== null) echo "id: $id\n";
            echo "event: $event\n";
            echo "data: $data\n\n";
            @flush();
        };

        try {
            $handler($send);
        } catch (\Throwable $e) {
            // Cliente desconectado o error interno: fin del stream
            log_message('error', 'SSE stream: ' . $e->getMessage());
        }

        // Terminar la petición: el EventSource del cliente se reconecta solo
        exit;
    }

    private function obtenerEventosAdmin(int $lastId): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('attendance a')
            ->select('a.id, a.user_id, a.name, a.date, a.time_in, a.time_out, a.status, u.dni')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->where('a.id >', $lastId)
            ->orderBy('a.id', 'ASC')
            ->limit(50);
        
        $rows = $builder->get()->getResultArray();
        
        $eventos = [];
        foreach ($rows as $r) {
            $eventos[] = [
                'id'        => (int) $r['id'],
                'tipo'      => 'asistencia_nueva',
                'datos'     => $r,
                'timestamp' => time(),
            ];
        }
        
        // También verificar ediciones recientes
        $logs = $this->logModel->orderBy('id', 'DESC')->where('id >', $lastId)->limit(20)->findAll();
        foreach ($logs as $l) {
            $eventos[] = [
                'id'        => (int) $l['id'],
                'tipo'      => 'asistencia_editada',
                'datos'     => $l,
                'timestamp' => strtotime($l['created_at']),
            ];
        }
        
        usort($eventos, fn($a, $b) => $a['id'] <=> $b['id']);
        return $eventos;
    }

    private function obtenerUltimaAsistencia(int $userId): ?array
    {
        return $this->attendanceModel
            ->where('user_id', $userId)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    private function haCambiado(?array $a, ?array $b): bool
    {
        if (!$a && !$b) return false;
        if (!$a || !$b) return true;
        return $a['status'] !== $b['status'] 
            || $a['time_in'] !== $b['time_in'] 
            || $a['time_out'] !== $b['time_out'];
    }

    private function verificarAlertas(int $userId): ?array
    {
        $hoy = date('Y-m-d');
        $att = $this->attendanceModel->where('user_id', $userId)->where('date', $hoy)->first();
        if (!$att) return null;
        
        $alertas = [];
        if ($att['status'] === 'late' && !$this->yaNotificado('late', $att['id'])) {
            $alertas[] = ['tipo' => 'tardanza', 'mensaje' => 'Llegada tardía registrada'];
        }
        if (empty($att['time_out']) && $this->esHoraSalida($att)) {
            $alertas[] = ['tipo' => 'sin_salida', 'mensaje' => 'No has marcado la salida'];
        }
        return $alertas ?: null;
    }

    private function yaNotificado(string $tipo, int $attId): bool
    {
        // Simple cache en sesión para no spamear
        $key = "notif_{$tipo}_{$attId}";
        if (session()->has($key)) return true;
        session()->set($key, true);
        return false;
    }

    private function esHoraSalida(array $att): bool
    {
        if (!empty($att['time_out'])) return false;
        $scheduleModel = model(\App\Models\ScheduleModel::class);
        $user = model(\App\Models\UserModel::class)->find((int) session()->get('user_id'));
        $schedule = $scheduleModel->find((int) ($att['schedule_id'] ?? 0));
        if (!$schedule) return false;
        
        $ahora = date('H:i:s');
        return $ahora >= $schedule['hora_salida'];
    }
}
