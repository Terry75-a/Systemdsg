<?php
namespace App\Controllers;

use App\Libraries\WebAuthnService;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;
use App\Models\FestivoModel;
use App\Models\WebAuthnCredentialModel;

class EmployeeController extends BaseController
{
    protected UserModel $userModel;
    protected AttendanceModel $attendanceModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;
    protected FestivoModel $festivoModel;
    protected WebAuthnCredentialModel $credentialModel;
    protected WebAuthnService $webauthn;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel   = new ScheduleModel();
        $this->incidentModel   = new IncidentModel();
        $this->festivoModel    = new FestivoModel();
        $this->credentialModel = new WebAuthnCredentialModel();
        $this->webauthn        = new WebAuthnService();
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

        $user = $this->userModel->find((int) session()->get('user_id'));
        if (! $user) {
            session()->destroy();
            session()->setFlashdata('msg', 'Tu cuenta ya no existe. Contacta con Recursos Humanos.');
            session()->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('login-verde'));
        }

        $estado = (string) ($user['estado'] ?? 'Activo');
        if ($estado !== 'Activo') {
            $motivo = $estado === 'Despedido' ? 'Has sido dado de baja.' : 'Tu vinculación ha finalizado.';
            session()->destroy();
            session()->setFlashdata('msg', $motivo . ' No puedes usar el panel.');
            session()->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('login-verde'));
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

        // Festivos (los del mes van marcados en el calendario)
        $festivosMes    = [];
        $festivosProx   = [];
        foreach ($this->festivoModel->getAll($adminId > 0 ? $adminId : null) as $f) {
            $fecha             = (string) ($f['fecha'] ?? '');
            $nombre            = (string) ($f['nombre'] ?? 'Festivo');
            $festivosMes[$fecha] = $nombre;
            if ($fecha >= $hoy) {
                $festivosProx[$fecha] = $nombre;
            }
        }
        ksort($festivosProx);

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
            'festivosMes'     => $festivosMes,
            'festivosProximos'=> array_slice($festivosProx, 0, 4, true),
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
        $data['slot'] = view($role === 'Practicante'
            ? 'empleado/practicante-dashboard'
            : 'empleado/mi-panel-dashboard', $data);

        return view('layouts/practicante-panel', $data);
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
        $geo = $this->ubicacionMarcacion();

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
                'lat'        => $geo['lat'],
                'lng'        => $geo['lng'],
                'ip'         => $geo['ip'],
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
            $this->attendanceModel->update($att['id'], [
                'time_out'  => $ahora,
                'evidencia' => $evidencia ?: ($att['evidencia'] ?? ''),
                'lat'       => $geo['lat'],
                'lng'       => $geo['lng'],
                'ip'        => $geo['ip'],
            ]);

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

    /**
     * Geolocalización enviada por el navegador (opcional) + IP de origen.
     * Lo que no sea un par válido se guarda como NULL, nunca inventado.
     */
    private function ubicacionMarcacion(): array
    {
        $datos = ['lat' => null, 'lng' => null, 'ip' => substr((string) $this->request->getIPAddress(), 0, 45)];

        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');

        if (is_string($lat) && is_string($lng) && is_numeric($lat) && is_numeric($lng)) {
            $lat = (float) $lat;
            $lng = (float) $lng;
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180 && ! ($lat === 0.0 && $lng === 0.0)) {
                $datos['lat'] = $lat;
                $datos['lng'] = $lng;
            }
        }

        return $datos;
    }

    /** Límites de la imagen biométrica (bytes binarios). */
    private const BIO_MAX_BIN   = 3145728;   // 3 MB
    private const BIO_MAX_B64   = 4194304;   // 4 MB en base64
    private const BIO_MIN_BIN   = 200;
    private const BIO_DIRS      = ['evidencias', 'rostros'];
    private const BIO_MIME      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    private function saveBioImage(string $dataUrl, string $subdir): string
    {
        // Sólo los directorios previstos: nada de traversal.
        if (! in_array($subdir, self::BIO_DIRS, true)) {
            return '';
        }

        $dataUrl = trim($dataUrl);
        if ($dataUrl === '' || ! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/i', $dataUrl)) {
            return '';
        }

        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        if (strlen($base64) > self::BIO_MAX_B64) {
            return '';
        }

        $bin = base64_decode($base64, true);
        if ($bin === false || strlen($bin) < self::BIO_MIN_BIN || strlen($bin) > self::BIO_MAX_BIN) {
            return '';
        }

        // Debe ser una imagen real del tipo declarado (bloquea SVG/HTML embebido).
        $info = @getimagesizefromstring($bin);
        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if (! isset(self::BIO_MIME[$mime])) {
            return '';
        }

        $dir = FCPATH . 'uploads/' . $subdir;
        if (! is_dir($dir) && ! @mkdir($dir, 0775, true)) {
            return '';
        }

        $name = 'user_' . (int) session()->get('user_id')
            . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4))
            . '.' . self::BIO_MIME[$mime];

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

        $challenge = $this->currentChallenge($this->request->getGet('fresh') !== null);

        return $this->response->setJSON([
            'ok'               => true,
            'csrfHash'         => csrf_hash(),
            'challenge'        => $challenge,
            'userHandle'       => $this->webauthn->userHandleB64($userId),
            'rpId'             => $this->webauthn->host(),
            'origin'           => $this->webauthn->origin(),
            'userName'         => $user['name'] ?? 'Usuario',
            'userEmail'        => $user['email'] ?? '',
            'credId'           => $user['huella_cred_id'] ?? '',
            'huellaRegistrada' => (int) ($user['huella_registrada'] ?? 0),
            'rostroRegistrado' => (int) ($user['rostro_registrado'] ?? 0),
            'rostroPath'       => base_url('uploads/' . ($user['rostro_path'] ?? '')),
        ]);
    }

    /**
     * Challenge vigente de la sesion WebAuthn. Solo se regenera si el flujo lo
     * pide (`?fresh=1`), si no existe o si expiro: asi el refresco de CSRF que
     * hace el cliente no invalida la ceremonia que esta en curso.
     */
    private function currentChallenge(bool $force): string
    {
        $challenge = (string) session()->get('wa_challenge');
        $ts        = (int) session()->get('wa_challenge_ts');

        if ($force || $challenge === '' || (time() - $ts) > 300) {
            $challenge = WebAuthnService::b64e(random_bytes(32));
            session()->set(['wa_challenge' => $challenge, 'wa_challenge_ts' => time()]);
        }

        return $challenge;
    }

    /** Lee y consume el challenge (uso unico) antes de validar la respuesta. */
    private function takeChallenge(): ?string
    {
        $challenge = (string) session()->get('wa_challenge');
        session()->remove(['wa_challenge', 'wa_challenge_ts']);

        return $challenge === '' ? null : $challenge;
    }

    public function guardarHuella()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $mode   = (string) $this->request->getPost('mode');

        if ($mode === 'sim') {
            $credId = trim((string) $this->request->getPost('credential_id'));
            if ($credId === '') {
                return $this->response->setJSON(['ok' => false, 'msj' => 'No se recibio la huella']);
            }
            if (strlen($credId) > 300) $credId = substr($credId, 0, 300);

            $this->credentialModel->clearUser($userId);
            $this->userModel->update($userId, [
                'huella_registrada' => 2, // 2 = simulada de prueba, 1 = real (WebAuthn)
                'huella_fecha'      => date('Y-m-d H:i:s'),
                'huella_cred_id'    => $credId,
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella registrada (simulacion de prueba)']);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Sesion invalida']);
        }

        $challenge = $this->takeChallenge();
        if ($challenge === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'El registro expiro. Vuelve a intentarlo.']);
        }

        try {
            $options = $this->webauthn->creationOptions(
                $challenge,
                $userId,
                (string) ($user['name'] ?? ''),
                (string) ($user['email'] ?? '')
            );
            $record = $this->webauthn->verifyAttestation($challenge, $options, $this->request->getPost());
            $row    = $this->webauthn->toRow($record, $userId);

            if ($this->credentialModel->findByCredentialId($row['credential_id']) !== null) {
                return $this->response->setJSON(['ok' => false, 'msj' => 'Esa huella ya esta registrada en otra cuenta.']);
            }

            $this->credentialModel->clearUser($userId);
            $this->credentialModel->insert($row);

            $this->userModel->update($userId, [
                'huella_registrada' => 1,
                'huella_fecha'      => date('Y-m-d H:i:s'),
                'huella_cred_id'    => $row['credential_id'],
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella registrada correctamente']);
        } catch (\Throwable $e) {
            log_message('error', 'WebAuthn (registro) fallido: ' . $e->getMessage());

            return $this->response->setJSON([
                'ok'  => false,
                'msj' => 'No se pudo validar la huella. ' . $e->getMessage(),
            ]);
        }
    }

    /** Comprueba en el servidor la firma de la assertion (huella real). */
    public function verifyHuella()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $row    = $this->credentialModel->findByUser($userId);
        if ($row === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'No tienes una huella registrada.']);
        }

        $challenge = $this->takeChallenge();
        if ($challenge === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'La verificacion expiro. Pulsa de nuevo.']);
        }

        try {
            $record  = $this->webauthn->fromRow($row);
            $updated = $this->webauthn->verifyAssertion($record, $challenge, $this->request->getPost());

            $this->credentialModel->update($row['id'], [
                'counter'      => $updated->counter,
                'last_used_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella verificada']);
        } catch (\Throwable $e) {
            log_message('error', 'WebAuthn (verificacion) fallida: ' . $e->getMessage());

            return $this->response->setJSON(['ok' => false, 'msj' => 'Huella no reconocida. Reintenta o verifica con el rostro.']);
        }
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