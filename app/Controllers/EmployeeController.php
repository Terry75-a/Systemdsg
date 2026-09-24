<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;

class EmployeeController extends BaseController
{
    protected UserModel $userModel;
    protected AttendanceModel $attendanceModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel   = new ScheduleModel();
        $this->incidentModel   = new IncidentModel();
    }

    private function checkEmployee(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }
        return null;
    }

    private function getMyData(): array
    {
        $userId = (int) session()->get('user_id');
        $user   = $this->userModel->find($userId) ?: [];
        $adminId = (int) ($user['admin_id'] ?? 0);

        $hoy  = date('Y-m-d');
        $mes  = date('Y-m');

        // Horario del propio admin del practicante (o asignado directamente)
        $schedules  = $this->scheduleModel->getActive($adminId > 0 ? $adminId : null);
        $miHorario  = $this->pickSchedule($schedules, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '');

        $hoyRegistro = $this->attendanceModel->findByUserAndDate($userId, $hoy) ?: null;

        $misRegistros = $this->attendanceModel
            ->where('user_id', $userId)
            ->like('date', $mes, 'after')
            ->orderBy('date', 'DESC')
            ->findAll();

        $misAsistencias = 0;
        $misTardanzas   = 0;
        $misFaltas      = 0;
        foreach ($misRegistros as $r) {
            if     ($r['status'] === 'present') $misAsistencias++;
            elseif ($r['status'] === 'late')    $misTardanzas++;
            if     ($r['status'] === 'absent')  $misFaltas++;
        }

        $misIncidencias = $this->incidentModel
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $pendientes = 0;
        $enRevision = 0;
        foreach ($misIncidencias as $inc) {
            if     ($inc['estado'] === 'Pendiente') $pendientes++;
            elseif ($inc['estado'] === 'Revisión')  $enRevision++;
        }

        return [
            'miInfo'          => $user,
            'miHorario'       => $miHorario,
            'hoyRegistro'     => $hoyRegistro,
            'misRegistros'    => $misRegistros,
            'misIncidencias'  => $misIncidencias,
            'misAsistencias'  => $misAsistencias,
            'misTardanzas'    => $misTardanzas,
            'misFaltas'       => $misFaltas,
            'incPendientes'   => $pendientes,
            'incEnRevision'   => $enRevision,
            'bioHuella'       => (int) ($user['huella_registrada'] ?? 0),
            'bioRostro'       => (int) ($user['rostro_registrado'] ?? 0),
            'bioRostroPath'   => (string) ($user['rostro_path'] ?? ''),
            'bioHuellaCredId' => (string) ($user['huella_cred_id'] ?? ''),
        ];
    }

    public function dashboard()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $role = session()->get('user_role');
        $data = $this->getMyData();
        $data['activePage']  = 'dashboard';
        $data['pageTitle']   = 'Mi Panel · DSG';
        $data['greetingSub'] = $role === 'Practicante'
            ? 'Marca tu asistencia y revisa tu estado del día.'
            : 'Bienvenido a tu panel, marcas y notificaciones.';
        $data['slot'] = view('empleado/practicante-dashboard', $data);

        if ($role === 'Practicante') {
            return view('layouts/practicante-panel', $data);
        }
        return view('empleado/dashboard', $data);
    }

    // ════════════════════════════════════════
    // MI ASISTENCIA / HORARIO / INCIDENCIAS (páginas propias)
    // ════════════════════════════════════════
    public function asistencias()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'asistencias';
        $data['pageTitle']   = 'Mi Asistencia · DSG';
        $data['greetingSub'] = 'Todo tu historial de marcas del mes.';
        $data['slot'] = view('empleado/practicante-asistencias', $data);
        return view('layouts/practicante-panel', $data);
    }

    public function horario()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'horario';
        $data['pageTitle']   = 'Mi Horario · DSG';
        $data['greetingSub'] = 'Tu jornada de práctica configurada.';
        $data['slot'] = view('empleado/practicante-horario', $data);
        return view('layouts/practicante-panel', $data);
    }

    public function incidencias()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'incidencias';
        $data['pageTitle']   = 'Mis Incidencias · DSG';
        $data['greetingSub'] = 'Tus incidencias y sus justificaciones.';
        $data['slot'] = view('empleado/practicante-incidencias', $data);
        return view('layouts/practicante-panel', $data);
    }

    // ════════════════════════════════════════
    // MARCAR ASISTENCIA (entrada / salida)
    // ════════════════════════════════════════
    public function registrar()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $s = session();
        $userId = (int) $s->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            $s->setFlashdata('msg', 'Usuario no encontrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('mi-panel'));
        }

        // Biometria obligatoria: huella o rostro. Al marcar se pide uno de los dos.
        if (!(int) ($user['huella_registrada'] ?? 0) && !(int) ($user['rostro_registrado'] ?? 0)) {
            $s->setFlashdata('msg', 'Registra tu huella o tu rostro en la seccion Biometria antes de marcar asistencia');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }
        if ($this->request->getPost('bio_ok') !== '1') {
            $s->setFlashdata('msg', 'Verifica tu huella o tu rostro para marcar asistencia');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }

        // Evidencia del rostro (selfie de verificacion)
        $evidencia = $this->saveBioImage($this->request->getPost('evidencia') ?? '', 'evidencias');

        $hoy = date('Y-m-d');
        $ahora = date('H:i:s');
        $adminId = (int) ($user['admin_id'] ?? 0);

        $att = $this->attendanceModel->findByUserAndDate($userId, $hoy);

        if (!$att) {
            // ENTRADA
            $entrada = date('H:i');
            $tolerancia = (int) ($this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '')['tolerancia'] ?? 10);
            $horaBase = $this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '')['hora_entrada'] ?? '08:00';

            $limite = strtotime($horaBase) + $tolerancia * 60;
            $status = strtotime($ahora) <= $limite ? 'present' : 'late';

            $this->attendanceModel->insert([
                'admin_id'   => $adminId ?: null,
                'user_id'    => $userId,
                'name'       => $user['name'] ?? '',
                'dni'        => $user['dni'] ?? '',
                'date'       => $hoy,
                'time_in'    => $ahora,
                'time_out'   => null,
                'status'     => $status,
                'observacion'=> '',
                'evidencia'  => $evidencia,
            ]);
            $attId = (int) db_connect()->insertID();

            if ($status === 'late') {
                $this->incidentModel->insert([
                    'admin_id'      => $adminId ?: null,
                    'att_id'        => $attId,
                    'user_id'       => $userId,
                    'name'          => $user['name'] ?? '',
                    'tipo'          => 'Tardanza',
                    'fecha'         => date('Y-m-d H:i:s'),
                    'detalle'       => "Llegó a las {$entrada}",
                    'estado'        => 'Pendiente',
                    'justificacion' => '',
                ]);
            }

            $s->setFlashdata('msg', "Entrada registrada a las {$entrada}" . ($status === 'late' ? ' · Tardanza' : ' · Puntual'));
            $s->setFlashdata('tipo', $status === 'late' ? 'warning' : 'success');

        } elseif (empty($att['time_out'])) {
            // SALIDA
            $salida = date('H:i');
            $this->attendanceModel->update($att['id'], ['time_out' => $ahora, 'evidencia' => $evidencia ?: ($att['evidencia'] ?? '')]);

            // Salida anticipada: si se retira antes de su hora de salida prevista
            $sc = $this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '');
            $horaSalidaPrevista = $sc['hora_salida'] ?? '';
            $salidaAnticipada = ($horaSalidaPrevista !== '' && strtotime($ahora) < strtotime($horaSalidaPrevista));
            if ($salidaAnticipada) {
                $just = trim((string) $this->request->getPost('justificacion'));
                $this->incidentModel->insert([
                    'admin_id'      => $adminId ?: null,
                    'att_id'        => (int) $att['id'],
                    'user_id'       => $userId,
                    'name'          => $user['name'] ?? '',
                    'tipo'          => 'Salida anticipada',
                    'fecha'         => date('Y-m-d H:i:s'),
                    'detalle'       => "Salió a las {$salida} · salida prevista " . substr($horaSalidaPrevista, 0, 5),
                    'estado'        => 'Revisión',
                    'justificacion' => $just !== '' ? $just : 'Sin motivo registrado',
                ]);
            }

            $s->setFlashdata('msg', "Salida registrada a las {$salida}" . ($salidaAnticipada ? ' · salida anticipada JUSTIFICADA, en revisión' : ''));
            $s->setFlashdata('tipo', 'success');

        } else {
            $s->setFlashdata('msg', 'Hoy ya registraste tu entrada y salida');
            $s->setFlashdata('tipo', 'warning');
        }

        return redirect()->to(base_url('mi-panel'));
    }

    private function saveBioImage(string $dataUrl, string $subdir): string
    {
        $dataUrl = trim($dataUrl);
        if ($dataUrl === '' || !str_starts_with($dataUrl, 'data:image/')) {
            return '';
        }
        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $bin = base64_decode($base64);
        if ($bin === false || strlen($bin) < 200) {
            return '';
        }
        $dir = FCPATH . 'uploads/' . $subdir;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $name = 'user_' . (int) session()->get('user_id') . '_' . date('YmdHis') . '.jpg';
        if (@file_put_contents($dir . '/' . $name, $bin) === false) {
            return '';
        }
        return $subdir . '/' . $name;
    }

    public function bioSession()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Sesion invalida']);
        }

        $challenge = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $this->response->setJSON([
            'ok'               => true,
            'csrfHash'         => csrf_hash(),
            'challenge'        => $challenge,
            'userName'         => $user['name'] ?? 'Usuario',
            'userEmail'        => $user['email'] ?? '',
            'credId'           => $user['huella_cred_id'] ?? '',
            'huellaRegistrada' => (int) ($user['huella_registrada'] ?? 0),
            'rostroRegistrado' => (int) ($user['rostro_registrado'] ?? 0),
            'rostroPath'       => base_url('uploads/' . ($user['rostro_path'] ?? '')),
        ]);
    }

    public function guardarHuella()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $credId = trim($this->request->getPost('credential_id') ?? '');
        $mode   = (string) $this->request->getPost('mode');

        if ($credId === '') {
            return $this->response->setJSON(['ok' => false, 'msj' => 'No se recibio la huella']);
        }
        if (strlen($credId) > 300) $credId = substr($credId, 0, 300);

        $flag = $mode === 'sim' ? 2 : 1; // 2 = simulada de prueba, 1 = real (WebAuthn)
        $this->userModel->update($userId, [
            'huella_registrada' => $flag,
            'huella_fecha'      => date('Y-m-d H:i:s'),
            'huella_cred_id'    => $credId,
        ]);
        return $this->response->setJSON(['ok' => true, 'msj' => $mode === 'sim' ? 'Huella registrada (simulacion de prueba)' : 'Huella registrada correctamente']);
    }

    public function guardarRostro()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');

        $dataUrl = trim($this->request->getPost('imagen') ?? '');
        $path = '';
        if ($dataUrl !== '') {
            $path = $this->saveBioImage($dataUrl, 'rostros');
        } else {
            $file = $this->request->getFile('rostro');
            if ($file && $file->isValid() && $file->getSize() > 0) {
                $dir = FCPATH . 'uploads/rostros';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
                $name = 'user_' . $userId . '_' . date('YmdHis') . '.jpg';
                $file->move($dir, $name);
                $path = 'rostros/' . $name;
            }
        }

        if ($path === '') {
            return $this->response->setJSON(['ok' => false, 'msj' => 'No se pudo guardar el rostro']);
        }

        $this->userModel->update($userId, [
            'rostro_registrado' => 1,
            'rostro_fecha'      => date('Y-m-d H:i:s'),
            'rostro_path'       => $path,
        ]);
        return $this->response->setJSON(['ok' => true, 'msj' => 'Rostro registrado correctamente', 'path' => base_url('uploads/' . $path)]);
    }

    private function pickSchedule(array $schedules, int $scheduleId, string $role): array
    {
        $tipoRole = $role === 'Practicante' ? 'Practicante' : 'Empleado';
        if ($scheduleId > 0) {
            foreach ($schedules as $sch) {
                if ((int) ($sch['id'] ?? 0) === $scheduleId) return $sch;
            }
        }
        foreach ($schedules as $sch) {
            if (($sch['tipo'] ?? '') === $tipoRole) return $sch;
        }
        return $schedules[0] ?? [];
    }

    private function scheduleScore(int $adminId, int $scheduleId = 0, string $role = ''): array
    {
        $schedules = $this->scheduleModel->getActive($adminId > 0 ? $adminId : null);
        return $this->pickSchedule($schedules, $scheduleId, $role);
    }

    // ════════════════════════════════════════
    // JUSTIFICAR UNA INCIDENCIA PROPIA
    // ════════════════════════════════════════
    public function justificarIncidencia()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $s = session();
        $userId = (int) $s->get('user_id');
        $incId  = (int) $this->request->getPost('inc_id');

        $inc = $this->incidentModel->find($incId);
        if (!$inc || (int) ($inc['user_id'] ?? -1) !== $userId) {
            $s->setFlashdata('msg', 'Esta incidencia no te pertenece');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('mi-panel'));
        }

        $just = trim($this->request->getPost('justificacion') ?? '');
        if ($just === '') {
            $s->setFlashdata('msg', 'Escribe tu justificacion');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }

        $this->incidentModel->update($incId, [
            'estado'        => 'Revisión',
            'justificacion' => $just,
        ]);

        $s->setFlashdata('msg', 'Justificacion enviada · queda en revisión');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('mi-panel/incidencias'));
    }
}