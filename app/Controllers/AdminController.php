<?php
namespace App\Controllers;

use App\Controllers\Admin\AdminBaseController;
use App\Services\NotificationService;

/**
 * Panel /admin — personal, horarios y códigos.
 *
 * La asistencia, su auditoría, la configuración y las incidencias
 * viven en App\Controllers\Admin\* (refactor #14).
 */
class AdminController extends AdminBaseController
{
    protected NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = new NotificationService();
    }

    public function dashboard()
    {
        if ($redirect = $this->guard()) return $redirect;
        $data = $this->viewData(['activePage' => 'dashboard']);
        $data['tk'] = \App\Libraries\Asistencia\DashboardStats::build(
            $data['attendance'], $data['personal'], $data['incidents'], $this->request->getGet() ?? []
        );
        return view('admin/dashboard', $data);
    }

    public function personal()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/personal', $this->viewData(['activePage' => 'personal']));
    }

    public function horarios()
    {
        if ($redirect = $this->guard()) return $redirect;

        $personal = $this->getPersonal();
        // Una persona puede estar en varios horarios (L-V + sábado)
        $scheduleUsers = [];
        foreach ($personal as $u) {
            foreach (\App\Libraries\Asistencia\HorariosPersona::ids($u) as $sid) {
                $scheduleUsers[$sid][] = $u;
            }
        }
        $schedules = $this->scheduleModel->getAll($this->adminId());
        foreach ($schedules as &$sch) {
            $sch['integrantes']['total'] = count($scheduleUsers[(int) $sch['id']] ?? []);
            $sch['integrantes']['practicantes'] = count(array_filter($scheduleUsers[(int) $sch['id']] ?? [], fn($u) => $u['role'] === 'Practicante'));
        }
        unset($sch);

        return view('admin/horarios', $this->viewData([
            'activePage'    => 'horarios',
            'schedules'     => $schedules,
            'scheduleUsers' => $scheduleUsers,
        ]));
    }

    // ════════════════════════════════════════
    // PERSONAL — crear (código EMPL-XXX / PRCT-XXX por admin)
    // ════════════════════════════════════════
    /**
     * GET /admin/dni/{dni} — datos del DNI para autocompletar «Crear empleado».
     * Avisa si el DNI ya está registrado en Asisten.
     */
    public function consultarDni($dni = '')
    {
        if ($redirect = $this->guard()) return $redirect;
        $dni = (string) $dni;
        if (! preg_match('/^\d{8}$/', $dni)) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'El DNI debe tener 8 dígitos.']);
        }
        if ($existe = $this->userModel->findByDni($dni)) {
            return $this->response->setJSON([
                'ok' => false, 'existe' => true,
                'msg' => 'Este DNI ya está registrado en Asisten (' . ($existe['name'] ?? '') . ').',
            ]);
        }
        $datos = \App\Libraries\DniApi::consultar($dni);
        if (! $datos) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'No se encontró el DNI en el servicio. Escribe el nombre a mano.']);
        }
        return $this->response->setJSON(['ok' => true] + $datos);
    }

    public function createEmployee()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        if ($redirect = $this->validar([
            'emp_name'           => 'required|trim|min_length[3]|max_length[100]',
            'emp_dni'            => 'required|regex_match[/^[0-9]{8}$/]',
            'emp_role'           => 'permit_empty|in_list[Empleado,Practicante]',
            'emp_semestre'       => 'permit_empty|max_length[50]',
            'emp_institucion'    => 'permit_empty|max_length[150]',
            'emp_contract_start' => 'permit_empty|valid_date',
            'emp_contract_end'   => 'permit_empty|valid_date',
        ], 'admin/personal')) {
            return $redirect;
        }

        $name     = trim((string) $this->request->getPost('emp_name'));
        $dni      = trim((string) $this->request->getPost('emp_dni'));
        $role     = $this->request->getPost('emp_role') ?: 'Empleado';
        $area     = trim((string) ($this->request->getPost('emp_area') ?? ''));
        $cargo    = trim((string) ($this->request->getPost('emp_cargo') ?? ''));
        $estado   = $this->request->getPost('emp_estado') ?: 'Activo';
        $institucion = trim((string) ($this->request->getPost('emp_institucion') ?? ''));
        $semestre    = trim((string) ($this->request->getPost('emp_semestre') ?? ''));

        if (!in_array($role, ['Empleado', 'Practicante'])) $role = 'Empleado';

        if ($this->userModel->findByDni($dni)) {
            $s->setFlashdata('msg', 'Este DNI ya esta registrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }

        if ($role === 'Practicante' && $semestre === '') {
            $s->setFlashdata('msg', 'Indica el semestre del practicante');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }

        // Código personal automático por admin (EMPLEADOS y PRACTICANTES por separado)
        $personalCode = $this->userModel->nextPersonalCode($adminId, $role);

        $parts = explode(' ', $name);
        $first = strtolower(preg_replace('/[^a-zA-Z]/', '', $parts[0]));
        $last  = strtolower(preg_replace('/[^a-zA-Z]/', '', end($parts)));
        $email = ($first === $last) ? $first . '@dsg.pe' : $first . '.' . $last . '@dsg.pe';

        $counter = 1;
        $baseEmail = $email;
        while ($this->userModel->findByEmail($email)) {
            $email = $counter . '.' . $baseEmail;
            $counter++;
        }

        $password = $this->generateSecurePassword();

        $this->userModel->insert(array_merge([
            'name'          => $name,
            'email'         => $email,
            'dni'           => $dni,
            'password'      => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
            'area'          => $area,
            'cargo'         => $cargo,
            'institucion'   => $institucion,
            'semestre'      => $semestre,
            'admin_id'      => $adminId,
            'admin_code'    => $this->adminCode(),
            'personal_code' => $personalCode,
            'created_by'    => session()->get('user_name'),
            'estado'        => $estado,
        ], $this->contractFields(), $this->firmaFields($name)));

        $s->setFlashdata('emp_creds', [
            'name'          => $name,
            'email'         => $email,
            'dni'           => $dni,
            'password'      => $password,
            'role'          => $role,
            'personal_code' => $personalCode,
        ]);
        $s->setFlashdata('msg', $role === 'Practicante' ? "Practicante {$name} creado ({$personalCode})" : "Empleado {$name} creado ({$personalCode})");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // PERSONAL — actualizar (solo de mi equipo)
    // ════════════════════════════════════════
    public function updateEmployee()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        if ($redirect = $this->validar([
            'user_id'    => 'required|integer|greater_than[0]',
            'emp_name'   => 'required|trim|min_length[3]|max_length[100]',
            'emp_dni'    => 'required|regex_match[/^[0-9]{8}$/]',
            'emp_email'  => 'permit_empty|max_length[150]',
            'emp_estado' => 'permit_empty|in_list[Activo,Inactivo,Despedido,Retirado]',
            'emp_password' => 'permit_empty|min_length[6]|max_length[72]',
            'emp_contract_start' => 'permit_empty|valid_date',
            'emp_contract_end'   => 'permit_empty|valid_date',
        ], 'admin/personal')) {
            return $redirect;
        }

        $userId   = (int) $this->request->getPost('user_id');
        $name     = trim((string) $this->request->getPost('emp_name'));
        $dni      = trim((string) $this->request->getPost('emp_dni'));
        $email    = trim((string) ($this->request->getPost('emp_email') ?? ''));
        $area     = trim((string) ($this->request->getPost('emp_area') ?? ''));
        $cargo    = trim((string) ($this->request->getPost('emp_cargo') ?? ''));
        $password = $this->request->getPost('emp_password');
        $estado   = $this->request->getPost('emp_estado') ?? 'Activo';

        $member = $this->userModel->find($userId);
        if (!$member || (int) ($member['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este miembro no pertenece a tu equipo');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }

        $data = [
            'name'   => $name,
            'dni'    => $dni,
            'email'  => $email,
            'area'   => $area,
            'cargo'  => $cargo,
            'estado' => $estado,
        ];
        if (($member['role'] ?? '') === 'Practicante') {
            $data['institucion'] = trim($this->request->getPost('emp_institucion') ?? '');
            $data['semestre']    = trim($this->request->getPost('emp_semestre') ?? '');
        }
        $data = array_merge($data, $this->contractFields(), $this->firmaFields($name));
        if (!empty($password) && strlen($password) >= 6) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $this->userModel->update($userId, $data);

        $s->setFlashdata('msg', "{$member['role']} {$name} actualizado");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // PERSONAL — regenerar contrasena (solo de mi equipo)
    // ════════════════════════════════════════
    public function resetPassword()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $userId  = (int) $this->request->getPost('user_id');

        $member = $this->userModel->find($userId);
        if (!$member || (int) ($member['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este miembro no pertenece a tu equipo');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }

        $password = $this->generateSecurePassword();
        $this->userModel->update($userId, ['password' => password_hash($password, PASSWORD_DEFAULT)]);

        $s->setFlashdata('emp_reset', [
            'name'         => $member['name'],
            'personal_code'=> $member['personal_code'] ?? '',
            'email'        => $member['email'] ?? '',
            'dni'          => $member['dni'] ?? '',
            'password'     => $password,
        ]);
        $s->setFlashdata('msg', "Nueva contrasena generada para {$member['name']}");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // DESPEDIR EMPLEADO (estado → Despedido)
    // ════════════════════════════════════════
    public function fireEmployee()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $userId  = (int) $this->request->getPost('user_id');

        $member = $this->userModel->find($userId);
        if (!$member || (int) ($member['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este miembro no pertenece a tu equipo');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }
        if (($member['role'] ?? '') !== 'Empleado') {
            $s->setFlashdata('msg', 'Solo se puede despedir a un Empleado');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }

        $this->userModel->update($userId, ['estado' => 'Despedido']);
        $s->setFlashdata('msg', "Empleado {$member['name']} despedido");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // TERMINAR LABOR DE PRACTICANTE (estado → Retirado)
    // ════════════════════════════════════════
    public function endPracticante()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $userId  = (int) $this->request->getPost('user_id');

        $member = $this->userModel->find($userId);
        if (!$member || (int) ($member['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este miembro no pertenece a tu equipo');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }
        if (($member['role'] ?? '') !== 'Practicante') {
            $s->setFlashdata('msg', 'Solo se puede terminar la labor de un Practicante');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }

        $this->userModel->update($userId, ['estado' => 'Retirado']);
        $s->setFlashdata('msg', "Labor del practicante {$member['name']} terminada");
        $s->setFlashdata('tipo', 'success');
        $s->setFlashdata('auto_delete', ['id' => $userId, 'name' => $member['name']]);
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // ELIMINAR PERSONAL (borrado físico real, solo de mi equipo)
    // ════════════════════════════════════════
    public function deleteUser()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $userId = $this->request->getPost('user_id') ?? $this->request->getPost('id');
        if (empty($userId) || !ctype_digit((string) $userId)) {
            $s->setFlashdata('msg', 'Usuario invalido');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }

        $member = $this->userModel->find((int) $userId);
        if (!$member || (int) ($member['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este miembro no pertenece a tu equipo');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }

        $db = \Config\Database::connect();
        $db->table('attendance')->where('user_id', (int) $userId)->delete();
        $db->table('incidents')->where('user_id', (int) $userId)->delete();
        $this->userModel->delete((int) $userId);

        $s->setFlashdata('msg', "{$member['name']} fue eliminado definitivamente");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // CÓDIGOS (solo los de mi admin)
    // ════════════════════════════════════════
    public function createCode()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();

        if ($redirect = $this->validar([
            'name' => 'required|trim|min_length[3]|max_length[100]',
            'dni'  => 'required|regex_match[/^[0-9]{8}$/]',
        ], 'admin/personal')) {
            return $redirect;
        }

        $name = trim((string) $this->request->getPost('name'));
        $dni  = trim((string) $this->request->getPost('dni'));
        $code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $this->codeModel->insert([
            'code'     => $code,
            'name'     => $name,
            'dni'      => $dni,
            'admin_id' => $this->adminId(),
            'status'   => 'active',
        ]);
        $s->setFlashdata('msg', "Código generado: {$code}");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    public function deleteCode()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $codeId = $this->request->getPost('code_id') ?? $this->request->getPost('id');
        if (empty($codeId) || !ctype_digit((string) $codeId)) {
            $s->setFlashdata('msg', 'Código invalido');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }
        $code = $this->codeModel->find((int) $codeId);
        if (!$code || (int) ($code['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este código no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/personal'));
        }
        $this->codeModel->delete((int) $codeId);
        $s->setFlashdata('msg', 'Código eliminado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/personal'));
    }

    // ════════════════════════════════════════
    // HORARIOS (solo los de mi admin)
    // ════════════════════════════════════════
    public function createSchedule()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        if ($redirect = $this->validar([
            'sch_nombre'        => 'required|trim|min_length[3]|max_length[100]',
            'sch_tipo'          => 'permit_empty|in_list[Empleado,Practicante]',
            'sch_jornada'       => 'permit_empty|in_list[full,part]',
            'sch_dias'          => 'permit_empty|max_length[100]',
            'sch_hora_entrada'  => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_hora_salida'   => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_hora_entrada2' => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_hora_salida2'  => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_tolerancia'    => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[120]',
        ], 'admin/horarios')) {
            return $redirect;
        }

        $nombre     = trim((string) $this->request->getPost('sch_nombre'));
        $tipo       = $this->request->getPost('sch_tipo') ?? 'Empleado';
        $dias       = trim((string) ($this->request->getPost('sch_dias') ?: 'Lun - Vie'));
        $entrada    = $this->request->getPost('sch_hora_entrada') ?: '08:00';
        $salida     = $this->request->getPost('sch_hora_salida') ?: '17:00';
        $tolerancia = (int) ($this->request->getPost('sch_tolerancia') ?: 10);
        if (!in_array($tipo, ['Practicante', 'Empleado'], true)) $tipo = 'Empleado';

        // Segundo turno opcional (jornada partida / doble turno)
        $segundo = $this->segundoTurno($entrada, $salida);
        if (is_string($segundo)) {
            $s->setFlashdata('msg', $segundo);
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/horarios'));
        }

        $this->scheduleModel->insert([
            'admin_id'      => $adminId,
            'nombre'        => $nombre,
            'tipo'          => $tipo,
            'jornada'       => $this->jornadaPost($entrada, $salida, $segundo),
            'dias'          => $dias,
            'hora_entrada'  => $entrada,
            'hora_salida'   => $salida,
            'hora_entrada2' => $segundo[0],
            'hora_salida2'  => $segundo[1],
            'tolerancia'    => $tolerancia,
            'estado'        => 'Activo',
        ]);

        // Notificar a admins sobre horario creado
        $schId = (int) db_connect()->insertID();
        $this->notificationService->horarioCreado($schId, $adminId);

        $s->setFlashdata('msg', "Horario {$nombre} creado");
        $s->setFlashdata('tipo', 'success');

        return redirect()->to(base_url('admin/horarios'));
    }

    public function updateSchedule()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $schId = (int) $this->request->getPost('sch_id');

        $sch = $this->scheduleModel->find($schId);
        if (!$sch || (int) ($sch['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este horario no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/horarios'));
        }

        $nombre     = trim($this->request->getPost('sch_nombre'));
        $tipo       = $this->request->getPost('sch_tipo') ?? ($sch['tipo'] ?? 'Empleado');
        $dias       = trim($this->request->getPost('sch_dias') ?: 'Lun - Vie');
        $entrada    = $this->request->getPost('sch_hora_entrada') ?: '08:00';
        $salida     = $this->request->getPost('sch_hora_salida') ?: '17:00';
        $tolerancia = (int) ($this->request->getPost('sch_tolerancia') ?: 10);
        $estado     = $this->request->getPost('sch_estado') ?? 'Activo';
        if (!in_array($tipo, ['Practicante', 'Empleado'], true)) $tipo = 'Empleado';

        // Segundo turno opcional (jornada partida / doble turno)
        $segundo = $this->segundoTurno($entrada, $salida);
        if (is_string($segundo)) {
            $s->setFlashdata('msg', $segundo);
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/horarios'));
        }

        $this->scheduleModel->update($schId, [
            'nombre'        => $nombre,
            'tipo'          => $tipo,
            'jornada'       => $this->jornadaPost($entrada, $salida, $segundo),
            'dias'          => $dias,
            'hora_entrada'  => $entrada,
            'hora_salida'   => $salida,
            'hora_entrada2' => $segundo[0],
            'hora_salida2'  => $segundo[1],
            'tolerancia'    => $tolerancia,
            'estado'        => $estado,
        ]);
        $s->setFlashdata('msg', 'Horario actualizado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/horarios'));
    }

    /**
     * Jornada elegida en el formulario (full / part). Si no llega, se
     * detecta por las horas del día.
     */
    private function jornadaPost(string $entrada, string $salida, array $segundo): string
    {
        $j = (string) $this->request->getPost('sch_jornada');
        if (isset(\App\Libraries\Asistencia\Jornada::JORNADAS[$j])) {
            return $j;
        }
        return \App\Libraries\Asistencia\Jornada::detectar([
            'hora_entrada' => $entrada, 'hora_salida' => $salida,
            'hora_entrada2' => $segundo[0], 'hora_salida2' => $segundo[1],
        ]);
    }

    /**
     * Normaliza y valida el segundo turno (doble turno / jornada partida).
     *
     * Devuelve [entrada2, salida2] —con NULL cuando no hay segundo turno—
     * o un mensaje de error cuando el par está incompleto o mal ordenado.
     *
     * @return array{0: ?string, 1: ?string}|string
     */
    private function segundoTurno(string $entrada, string $salida)
    {
        $e2 = trim((string) $this->request->getPost('sch_hora_entrada2'));
        $s2 = trim((string) $this->request->getPost('sch_hora_salida2'));

        if ($e2 === '' && $s2 === '') {
            return [null, null];
        }
        if ($e2 === '' || $s2 === '') {
            return 'El segundo turno necesita hora de entrada y hora de salida (o déjalas vacías)';
        }

        $e2 = substr($e2, 0, 5);
        $s2 = substr($s2, 0, 5);

        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $e2) || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $s2)) {
            return 'La hora del segundo turno no es válida (usa el formato HH:MM)';
        }

        if (strtotime($e2) < strtotime(substr($salida, 0, 5))) {
            return 'El segundo turno debe empezar cuando termina el primero (o después)';
        }
        if (strtotime($s2) <= strtotime($e2)) {
            return 'La salida del segundo turno debe ser posterior a su entrada';
        }

        return [$e2, $s2];
    }

    public function deleteSchedule()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $schId = (int) $this->request->getPost('sch_id');

        $sch = $this->scheduleModel->find($schId);
        if (!$sch || (int) ($sch['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este horario no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/horarios'));
        }
        // Quitar el horario a quienes lo tenían (se conservan sus otros horarios)
        foreach ($this->userModel->where('admin_id', $adminId)->findAll() as $u) {
            $ids = \App\Libraries\Asistencia\HorariosPersona::ids($u);
            if (in_array($schId, $ids, true)) {
                \App\Libraries\Asistencia\HorariosPersona::guardar((int) $u['id'], array_diff($ids, [$schId]));
            }
        }
        $this->scheduleModel->delete($schId);
        $s->setFlashdata('msg', 'Horario eliminado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/horarios'));
    }

    /**
     * Asigna integrantes a un horario (solo personas del propio admin,
     * del rol que coincida con el tipo del horario).
     */
    public function assignSchedule()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $schId   = (int) $this->request->getPost('sch_id');
        $sel     = $this->request->getPost('sch_users') ?? [];

        $sch = $this->scheduleModel->find($schId);
        if (!$sch || (int) ($sch['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este horario no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/horarios'));
        }
        if (!is_array($sel)) $sel = [];
        $sel = array_map('intval', $sel);
        $sel = array_values(array_unique(array_filter($sel)));

        $tipo   = $sch['tipo'] ?? 'Empleado';
        $roles  = $tipo === 'Practicante' ? ['Practicante'] : ['Empleado'];

        // Una persona puede tener varios horarios, pero no dos que caigan el
        // mismo día (no se sabría cuál usar). Se agrega/quita SOLO este horario.
        $diasEste = \App\Libraries\Asistencia\Jornada::diasDeSemana($sch);
        $todos    = [];
        foreach ($this->scheduleModel->getAll($adminId) as $x) $todos[(int) $x['id']] = $x;
        $nombresDia = [1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sábado', 7 => 'domingo'];
        $choques = [];

        $personas = $this->userModel->where('admin_id', $adminId)->whereIn('role', $roles)->findAll();
        foreach ($personas as $u) {
            $uid   = (int) $u['id'];
            $ids   = \App\Libraries\Asistencia\HorariosPersona::ids($u);
            $tiene = in_array($schId, $ids, true);
            $quiere = in_array($uid, $sel, true);
            if ($tiene === $quiere) continue;

            if (! $quiere) {
                \App\Libraries\Asistencia\HorariosPersona::guardar($uid, array_diff($ids, [$schId]));
                continue;
            }
            // ¿Choca con otro horario suyo el mismo día?
            $choque = null;
            foreach ($ids as $otro) {
                if (! isset($todos[$otro])) continue;
                $comunes = array_intersect($diasEste, \App\Libraries\Asistencia\Jornada::diasDeSemana($todos[$otro]));
                if ($comunes) {
                    $choque = ($u['name'] ?? 'Alguien') . ' ya tiene «' . ($todos[$otro]['nombre'] ?? '') . '» el ' . $nombresDia[reset($comunes)];
                    break;
                }
            }
            if ($choque !== null) {
                $choques[] = $choque;
                continue;
            }
            \App\Libraries\Asistencia\HorariosPersona::guardar($uid, array_merge($ids, [$schId]));
        }

        if ($choques) {
            $s->setFlashdata('msg', 'Integrantes actualizados, pero no se pudo asignar a todos: ' . implode('; ', $choques) . '. Quítale primero ese horario o ajusta los días.');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/horarios'));
        }

        $s->setFlashdata('msg', 'Integrantes actualizados para «' . ($sch['nombre'] ?? '') . '»');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/horarios'));
    }

    // ════════════════════════════════════════
    // BIOMETRÍA DEL PERSONAL — la registra el admin
    // ════════════════════════════════════════

    /**
     * Devuelve la persona sólo si es Empleado/Practicante del equipo de
     * este admin. Un admin jamás toca el personal de otro.
     */
    private function personaBio(int $userId): ?array
    {
        if ($userId <= 0) return null;

        $member = $this->userModel->find($userId);
        if (! $member) return null;

        if (! in_array((string) ($member['role'] ?? ''), ['Empleado', 'Practicante'], true)) return null;

        return (int) ($member['admin_id'] ?? -1) === $this->adminId() ? $member : null;
    }

    /** Borra una imagen biométrica sólo si es de esa persona. */
    private function borrarImagenBio(?string $relPath, int $userId): void
    {
        if ($relPath === null || $relPath === '') return;

        $nombre = basename($relPath);
        $dir    = dirname($relPath);
        if (! in_array($dir, self::BIO_DIRS, true)) return;
        if (! preg_match('/^user_' . $userId . '_[A-Za-z0-9_\-]+\.(jpg|png|webp)$/', $nombre)) return;

        $ruta = WRITEPATH . 'uploads/' . $dir . '/' . $nombre;
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    /**
     * Foto de referencia del rostro. La captura el admin (cámara o archivo)
     * para que nadie pueda registrar una foto que no es la suya.
     * Con estado=off quita la foto que ya existía.
     */
    public function biometriaRostro()
    {
        if ($redirect = $this->guard()) return $redirect;

        $member = $this->personaBio((int) $this->request->getPost('user_id'));
        if ($member === null) {
            return $this->response->setStatusCode(403)
                ->setJSON(['ok' => false, 'msj' => 'Esta persona no pertenece a tu equipo']);
        }

        $id     = (int) $member['id'];
        $nombre = (string) ($member['name'] ?? '');

        if ((string) $this->request->getPost('estado') === 'off') {
            $this->borrarImagenBio($member['rostro_path'] ?? null, $id);
            $this->userModel->update($id, [
                'rostro_registrado' => 0,
                'rostro_fecha'      => null,
                'rostro_path'       => null,
            ]);

            return $this->response->setJSON([
                'ok'  => true,
                'msj' => "Foto de referencia de {$nombre} eliminada",
            ]);
        }

        $path = $this->saveBioImage(trim((string) $this->request->getPost('imagen')), 'rostros', $id);
        if ($path === '') {
            return $this->response->setJSON([
                'ok'  => false,
                'msj' => 'No se pudo guardar la foto. Usa una imagen JPG, PNG o WebP de hasta 3 MB.',
            ]);
        }

        $this->borrarImagenBio($member['rostro_path'] ?? null, $id);
        $this->userModel->update($id, [
            'rostro_registrado' => 1,
            'rostro_fecha'      => date('Y-m-d H:i:s'),
            'rostro_path'       => $path,
        ]);

        return $this->response->setJSON([
            'ok'   => true,
            'msj'  => "Rostro registrado para {$nombre}",
            'path' => base_url('admin/evidencia/rostros/' . basename($path)),
        ]);
    }

    /**
     * Huella: la registra el admin (biometriaHuellaRegistrar). Aquí sólo se
     * REINICIA (borra) para volver a registrarla. Ya no hay modo «sin lector».
     */
    public function biometriaHuella()
    {
        if ($redirect = $this->guard()) return $redirect;

        $member = $this->personaBio((int) $this->request->getPost('user_id'));
        if ($member === null) {
            return $this->response->setStatusCode(403)
                ->setJSON(['ok' => false, 'msj' => 'Esta persona no pertenece a tu equipo']);
        }
        if ((string) $this->request->getPost('estado') !== 'off') {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Usa «Registrar huella en este equipo».']);
        }

        $id     = (int) $member['id'];
        $nombre = (string) ($member['name'] ?? '');

        // Se borran también las credenciales WebAuthn: no queda una huella «fantasma».
        (new \App\Models\WebAuthnCredentialModel())->clearUser($id);
        $this->userModel->update($id, [
            'huella_registrada' => 0,
            'huella_fecha'      => null,
            'huella_cred_id'    => null,
        ]);

        return $this->response->setJSON([
            'ok'  => true,
            'msj' => "Huella reiniciada para {$nombre}.",
        ]);
    }

    /**
     * Huella REAL (WebAuthn) registrada por el admin para una persona de su
     * equipo, en el equipo donde está el admin. La huella queda guardada en
     * ESE equipo: la persona marca con huella allí; en otro equipo, con rostro.
     *
     * Paso 1: reto de un solo uso atado a esa persona.
     */
    public function biometriaHuellaReto()
    {
        if ($redirect = $this->guard()) return $redirect;

        $member = $this->personaBio((int) $this->request->getPost('user_id'));
        if ($member === null) {
            return $this->response->setStatusCode(403)
                ->setJSON(['ok' => false, 'msj' => 'Esta persona no pertenece a tu equipo']);
        }

        $wa = new \App\Libraries\WebAuthnService();
        $challenge = \App\Libraries\WebAuthnService::b64e(random_bytes(32));
        session()->set(['wa_adm_challenge' => $challenge, 'wa_adm_uid' => (int) $member['id'], 'wa_adm_ts' => time()]);

        return $this->response->setJSON([
            'ok'         => true,
            'csrfHash'   => csrf_hash(),
            'challenge'  => $challenge,
            'rpId'       => $wa->host(),
            'userHandle' => $wa->userHandleB64((int) $member['id']),
            'userName'   => (string) ($member['name'] ?? ''),
            'userEmail'  => (string) ($member['email'] ?? ''),
        ]);
    }

    /** Paso 2: valida la huella leída y la guarda para esa persona. */
    public function biometriaHuellaRegistrar()
    {
        if ($redirect = $this->guard()) return $redirect;

        $member = $this->personaBio((int) $this->request->getPost('user_id'));
        if ($member === null) {
            return $this->response->setStatusCode(403)
                ->setJSON(['ok' => false, 'msj' => 'Esta persona no pertenece a tu equipo']);
        }
        $id = (int) $member['id'];

        $challenge = (string) session()->get('wa_adm_challenge');
        $uid       = (int) session()->get('wa_adm_uid');
        $ts        = (int) session()->get('wa_adm_ts');
        session()->remove(['wa_adm_challenge', 'wa_adm_uid', 'wa_adm_ts']);
        if ($challenge === '' || $uid !== $id || (time() - $ts) > 300) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'El registro expiró. Vuelve a intentarlo.']);
        }

        $wa    = new \App\Libraries\WebAuthnService();
        $creds = new \App\Models\WebAuthnCredentialModel();
        try {
            $options = $wa->creationOptions($challenge, $id, (string) ($member['name'] ?? ''), (string) ($member['email'] ?? ''));
            $record  = $wa->verifyAttestation($challenge, $options, $this->request->getPost());
            $row     = $wa->toRow($record, $id);

            $otro = $creds->findByCredentialId($row['credential_id']);
            if ($otro !== null && (int) $otro['user_id'] !== $id) {
                return $this->response->setJSON(['ok' => false, 'msj' => 'Esa huella ya está registrada para otra persona.']);
            }

            $creds->clearUser($id);
            $creds->insert($row);
            $this->userModel->update($id, [
                'huella_registrada' => 1,
                'huella_fecha'      => date('Y-m-d H:i:s'),
                'huella_cred_id'    => $row['credential_id'],
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella registrada para ' . ($member['name'] ?? 'la persona') . '.']);
        } catch (\Throwable $e) {
            log_message('error', 'WebAuthn (registro admin) fallido: ' . $e->getMessage());

            return $this->response->setJSON(['ok' => false, 'msj' => 'No se pudo validar la huella. Inténtalo de nuevo.']);
        }
    }

    /**
     * Ejecuta el cierre de jornada para el equipo del admin logueado.
     * Llama al comando CLI `asistencia:cierre` y devuelve su salida.
     */
    public function ejecutarCierre()
    {
        // En sesión de asistencia el user_id del admin logueado es su propio id.
        $adminId = (int) (session()->get('user_id') ?? 0);
        $fecha   = (string) ($this->request->getPost('fecha') ?: date('Y-m-d'));
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Fecha inválida (YYYY-MM-DD).']);
        }

        $cmd = 'asistencia:cierre --fecha=' . $fecha . ' --admin=' . $adminId;
        $out = command($cmd);
        return $this->response->setJSON(['ok' => true, 'msj' => 'Cierre ejecutado.', 'salida' => (string) $out]);
    }

    /**
     * Ejecuta el saneo (por defecto dry-run; --apply si se solicita).
     */
    public function ejecutarSaneo()
    {
        $apply = (bool) $this->request->getPost('apply');
        $cmd   = 'asistencia:saneo' . ($apply ? ' --apply' : '');
        $out   = command($cmd);
        return $this->response->setJSON(['ok' => true, 'msj' => 'Saneo ejecutado.', 'salida' => (string) $out]);
    }

}