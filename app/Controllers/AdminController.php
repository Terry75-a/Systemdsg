<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CodeModel;
use App\Models\AttendanceModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;
use App\Models\ConfigModel;

class AdminController extends BaseController
{
    protected UserModel $userModel;
    protected CodeModel $codeModel;
    protected AttendanceModel $attendanceModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;
    protected ConfigModel $configModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->codeModel       = new CodeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel   = new ScheduleModel();
        $this->incidentModel   = new IncidentModel();
        $this->configModel     = new ConfigModel();
    }

    // Solo un Admin entra aquí (cada uno ve únicamente lo suyo)
    private function guard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }
        $role = session()->get('user_role') ?? '';
        if ($role === 'Dev') {
            return redirect()->to(base_url('dios'));
        }
        if ($role !== 'Admin') {
            return redirect()->to(base_url('mi-panel'));
        }
        return null;
    }

    private function adminId(): int
    {
        return (int) session()->get('user_id');
    }

    private function admin(): array
    {
        return $this->userModel->find($this->adminId()) ?? [];
    }

    private function adminCode(): string
    {
        $a = $this->admin();
        return (string) ($a['admin_code'] ?? 'ADMIN-???');
    }

    private function adminEmpresa(): string
    {
        $a = $this->admin();
        return (string) ($a['empresa'] ?? '');
    }

    private function getPersonal(): array
    {
        $adminId = $this->adminId();
        $rows = $this->userModel->getPersonalByAdmin($adminId);
        return array_map(fn($u) => [
            'id'            => $u['id'],
            'name'          => $u['name'] ?? '',
            'dni'           => $u['dni'] ?? '',
            'email'         => $u['email'] ?? '',
            'area'          => $u['area'] ?? '',
            'cargo'         => $u['cargo'] ?? '',
            'institucion'   => $u['institucion'] ?? '',
            'semestre'      => $u['semestre'] ?? '',
            'huella_registrada' => (int) ($u['huella_registrada'] ?? 0),
            'rostro_registrado' => (int) ($u['rostro_registrado'] ?? 0),
            'rostro_path'       => $u['rostro_path'] ?? '',
            'contract_type'     => $u['contract_type'] ?? 'Indefinido',
            'contract_duration' => $u['contract_duration'] ?? null,
            'contract_start'    => $u['contract_start'] ?? null,
            'contract_end'      => $u['contract_end'] ?? null,
            'firma_tipo'        => $u['firma_tipo'] ?? '',
            'firma_datos'       => $u['firma_datos'] ?? '',
            'role'          => $u['role'] ?? '',
            'estado'        => $u['estado'] ?? 'Activo',
            'personal_code' => $u['personal_code'] ?? '',
            'schedule_id'   => (int) ($u['schedule_id'] ?? 0),
            'created'       => $u['created'] ?? '',
        ], $rows);
    }

    private function viewData(array $extra = []): array
    {
        $adminId      = $this->adminId();
        $personal     = $this->getPersonal();
        $empleados    = array_values(array_filter($personal, fn($e) => $e['role'] === 'Empleado'));
        $practicantes = array_values(array_filter($personal, fn($e) => $e['role'] === 'Practicante'));
        $attendance   = $this->attendanceModel->getAll($adminId);
        $stats        = $this->userModel->getTeamStats($adminId, $empleados, $practicantes);

        return array_merge([
            'empleados'       => $personal,
            'personal'        => $personal,
            'empleadosOnly'   => $empleados,
            'practicantes'    => $practicantes,
            'attendance'      => $attendance,
            'schedules'       => $this->scheduleModel->getAll($adminId),
            'incidents'       => $this->incidentModel->getAll($adminId),
            'codes'           => $this->codeModel->getAll($adminId),
            'totalEmpleados'  => $stats['totalPersonal'],
            'empleadosCount'  => $stats['empleadosCount'],
            'practicantesCount' => $stats['practicantesCount'],
            'hoyPresentes'    => $this->attendanceModel->countTodayByStatus('present', $adminId),
            'hoyTardanzas'    => $this->attendanceModel->countTodayByStatus('late', $adminId),
            'hoyFaltas'       => $stats['activosCount'] - $this->attendanceModel->countTodayTotal($adminId),
            'adminCode'       => $this->adminCode(),
            'adminEmpresa'    => $this->adminEmpresa(),
        ], $extra);
    }

    // ════════════════════════════════════════
    // PAGES (cada admin ve solo lo suyo)
    // ════════════════════════════════════════
    public function dashboard()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/dashboard', $this->viewData(['activePage' => 'dashboard']));
    }

    public function personal()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/personal', $this->viewData(['activePage' => 'personal']));
    }

    public function asistencias()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = $this->adminId();
        $get     = $this->request->getGet();
        $fechaInicio = null;
        $fechaFin    = null;

        if (!empty($get['todo'])) {
            $attendance = $this->attendanceModel->getAll($adminId);
        } else {
            $fi = trim((string) ($get['fecha_inicio'] ?? ''));
            $ff = trim((string) ($get['fecha_fin'] ?? ''));
            if ($fi === '' || $ff === '') {
                $fi = $ff = date('Y-m-d');
            } elseif ($fi > $ff) {
                [$fi, $ff] = [$ff, $fi];
            }
            $fechaInicio = $fi;
            $fechaFin    = $ff;
            $attendance  = ($fi === $ff)
                ? $this->attendanceModel->getByDate($fi, $adminId)
                : $this->attendanceModel->getByRange($fi, $ff, $adminId);
        }

        return view('admin/asistencias', $this->viewData([
            'activePage'   => 'asistencias',
            'attendance'   => $attendance,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
        ]));
    }

    public function horarios()
    {
        if ($redirect = $this->guard()) return $redirect;

        $personal = $this->getPersonal();
        $scheduleUsers = [];
        foreach ($personal as $u) {
            $sid = (int) ($u['schedule_id'] ?? 0);
            if ($sid > 0) {
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

    public function incidencias()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/incidencias', $this->viewData(['activePage' => 'incidencias']));
    }

    public function reportes()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = $this->adminId();
        $get     = $this->request->getGet();

        $fechaInicio = null;
        $fechaFin    = null;

        if (!empty($get['todo'])) {
            $attendance = $this->attendanceModel->getAll($adminId);
        } else {
            $fi = trim((string) ($get['fecha_inicio'] ?? ''));
            $ff = trim((string) ($get['fecha_fin'] ?? ''));
            if ($fi === '' || $ff === '') {
                $fi = $ff = date('Y-m-d');
            } elseif ($fi > $ff) {
                [$fi, $ff] = [$ff, $fi];
            }
            $fechaInicio = $fi;
            $fechaFin    = $ff;
            $attendance  = ($fi === $ff)
                ? $this->attendanceModel->getByDate($fi, $adminId)
                : $this->attendanceModel->getByRange($fi, $ff, $adminId);
        }

        return view('admin/reportes', $this->viewData([
            'activePage'   => 'reportes',
            'attendance'   => $attendance,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
        ]));
    }

    public function configuracion()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/configuracion', $this->viewData([
            'activePage' => 'configuracion',
            'config'     => $this->configModel->getConfig($this->adminId()),
        ]));
    }

    // ════════════════════════════════════════
    // PERSONAL — crear (código EMPL-XXX / PRCT-XXX por admin)
    // ════════════════════════════════════════
    public function createEmployee()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        $name     = trim($this->request->getPost('emp_name'));
        $dni      = trim($this->request->getPost('emp_dni'));
        $role     = $this->request->getPost('emp_role') ?: 'Empleado';
        $area     = trim($this->request->getPost('emp_area') ?? '');
        $cargo    = trim($this->request->getPost('emp_cargo') ?? '');
        $estado   = $this->request->getPost('emp_estado') ?: 'Activo';
        $institucion = trim($this->request->getPost('emp_institucion') ?? '');
        $semestre    = trim($this->request->getPost('emp_semestre') ?? '');

        if (!in_array($role, ['Empleado', 'Practicante'])) $role = 'Empleado';

        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            $s->setFlashdata('msg', 'El DNI debe tener 8 digitos');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }

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
            'password'      => $password,
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

    // Datos de contrato (tipo, duracion y fechas) desde el POST
    private function contractFields(): array
    {
        $type = trim($this->request->getPost('emp_contract_type') ?? '') ?: 'Indefinido';
        if (!in_array($type, ['Indefinido', 'Meses', 'Años'], true)) {
            $type = 'Indefinido';
        }
        $dur   = (int) ($this->request->getPost('emp_contract_duration') ?? 0);
        $start = trim($this->request->getPost('emp_contract_start') ?? '');
        $end   = trim($this->request->getPost('emp_contract_end') ?? '');

        if ($type === 'Indefinido') {
            $dur = 0;
            $end = '';
        } elseif ($dur < 1) {
            $dur = 1;
        }

        if ($type !== 'Indefinido' && $start !== '' && $end === '') {
            $unit = $type === 'Meses' ? 'month' : 'year';
            $end  = date('Y-m-d', strtotime("+{$dur} {$unit}", strtotime($start)));
        }

        return [
            'contract_type'     => $type,
            'contract_duration' => $dur > 0 ? $dur : null,
            'contract_start'    => $start !== '' ? $start : null,
            'contract_end'      => $end !== '' ? $end : null,
        ];
    }

    // Firma opcional: por nombre/generada o digital (canvas)
    private function firmaFields(string $name): array
    {
        $tipo = trim($this->request->getPost('emp_firma_tipo') ?? '');
        if (!in_array($tipo, ['', 'nombre', 'firma'], true)) {
            $tipo = '';
        }
        $datos = '';
        if ($tipo === 'nombre') {
            $datos = $name;
        } elseif ($tipo === 'firma') {
            $datos = trim($this->request->getPost('emp_firma_datos') ?? '');
            if (!str_starts_with($datos, 'data:image/png;base64,') || strlen($datos) > 200000) {
                $datos = '';
                $tipo  = '';
            }
        }
        return ['firma_tipo' => $tipo, 'firma_datos' => $datos];
    }

    private function generateSecurePassword(): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '@#$%';

        $pw  = $upper[random_int(0, strlen($upper) - 1)];
        $pw .= $upper[random_int(0, strlen($upper) - 1)];
        $pw .= $lower[random_int(0, strlen($lower) - 1)];
        $pw .= $lower[random_int(0, strlen($lower) - 1)];
        $pw .= $digits[random_int(0, strlen($digits) - 1)];
        $pw .= $digits[random_int(0, strlen($digits) - 1)];
        $pw .= $special[random_int(0, strlen($special) - 1)];

        $all = $upper . $lower . $digits . $special;
        for ($i = strlen($pw); $i < 12; $i++) {
            $pw .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($pw);
    }

    // ════════════════════════════════════════
    // PERSONAL — actualizar (solo de mi equipo)
    // ════════════════════════════════════════
    public function updateEmployee()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        $userId   = (int) $this->request->getPost('user_id');
        $name     = trim($this->request->getPost('emp_name'));
        $dni      = trim($this->request->getPost('emp_dni'));
        $email    = trim($this->request->getPost('emp_email') ?? '');
        $area     = trim($this->request->getPost('emp_area') ?? '');
        $cargo    = trim($this->request->getPost('emp_cargo') ?? '');
        $password = $this->request->getPost('emp_password');
        $estado   = $this->request->getPost('emp_estado') ?? 'Activo';

        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            $s->setFlashdata('msg', 'El DNI debe tener 8 digitos');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }

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
            $data['password'] = $password;
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
        $this->userModel->update($userId, ['password' => $password]);

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
        $name = trim($this->request->getPost('name'));
        $dni  = trim($this->request->getPost('dni'));
        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/personal'));
        }
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
        $nombre     = trim($this->request->getPost('sch_nombre'));
        $tipo       = $this->request->getPost('sch_tipo') ?? 'Empleado';
        $dias       = trim($this->request->getPost('sch_dias') ?: 'Lun - Vie');
        $entrada    = $this->request->getPost('sch_hora_entrada') ?: '08:00';
        $salida     = $this->request->getPost('sch_hora_salida') ?: '17:00';
        $tolerancia = (int) ($this->request->getPost('sch_tolerancia') ?: 10);
        if (!in_array($tipo, ['Practicante', 'Empleado'], true)) $tipo = 'Empleado';
        if (empty($nombre)) {
            $s->setFlashdata('msg', 'El nombre es obligatorio');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/horarios'));
        }
        $this->scheduleModel->insert([
            'admin_id'      => $adminId,
            'nombre'        => $nombre,
            'tipo'          => $tipo,
            'dias'          => $dias,
            'hora_entrada'  => $entrada,
            'hora_salida'   => $salida,
            'tolerancia'    => $tolerancia,
            'estado'        => 'Activo',
        ]);
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
        $this->scheduleModel->update($schId, [
            'nombre'       => $nombre,
            'tipo'         => $tipo,
            'dias'         => $dias,
            'hora_entrada' => $entrada,
            'hora_salida'  => $salida,
            'tolerancia'   => $tolerancia,
            'estado'       => $estado,
        ]);
        $s->setFlashdata('msg', 'Horario actualizado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/horarios'));
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
        $this->scheduleModel->delete($schId);
        // Liberar a quienes tenian este horario asignado
        $this->userModel->where('schedule_id', $schId)->set('schedule_id', null)->update();
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

        // Quitar el horario a quienes ya no estan en la lista
        if (empty($sel)) {
            $this->userModel
                ->where('admin_id', $adminId)
                ->where('schedule_id', $schId)
                ->set('schedule_id', null)
                ->update();
        } else {
            $this->userModel
                ->where('admin_id', $adminId)
                ->where('schedule_id', $schId)
                ->whereNotIn('id', $sel)
                ->set('schedule_id', null)
                ->update();
        }

        // Asignar el horario a los seleccionados (validando rol y admin)
        foreach ($sel as $uid) {
            $u = $this->userModel->find($uid);
            if (!$u || (int) ($u['admin_id'] ?? -1) !== $adminId) continue;
            if (!in_array($u['role'] ?? '', $roles, true)) continue;
            $this->userModel->update($uid, ['schedule_id' => $schId]);
        }

        $s->setFlashdata('msg', 'Integrantes actualizados para «' . ($sch['nombre'] ?? '') . '»');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/horarios'));
    }

    // ════════════════════════════════════════
    // ASISTENCIA (solo las de mi admin)
    // ════════════════════════════════════════
    public function updateAttendance()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $attId   = (int) $this->request->getPost('att_id');

        $att = $this->attendanceModel->find($attId);
        if (!$att || (int) ($att['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este registro no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/asistencias'));
        }

        $timeIn  = $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('time_out');
        $status  = $this->request->getPost('status');
        $obs     = trim($this->request->getPost('observacion') ?? '');

        $data = [
            'status'      => $status,
            'observacion' => $obs,
        ];
        if ($timeIn)  $data['time_in']  = $timeIn;
        if ($timeOut) $data['time_out'] = $timeOut;
        $this->attendanceModel->update($attId, $data);

        if (in_array($status, ['late', 'absent', 'no_exit'])) {
            if (!$this->incidentModel->findByAttId($attId)) {
                $tipoMap = ['late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin marcación'];
                $this->incidentModel->insert([
                    'admin_id'      => $adminId,
                    'att_id'        => $attId,
                    'user_id'       => $att['user_id'] ?? 0,
                    'name'          => $att['name'] ?? '',
                    'tipo'          => $tipoMap[$status] ?? 'Otro',
                    'detalle'       => $obs,
                    'estado'        => 'Pendiente',
                    'justificacion' => '',
                ]);
            }
        }

        $s->setFlashdata('msg', 'Asistencia actualizada');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/asistencias'));
    }

    // ════════════════════════════════════════
    // INCIDENCIAS (solo las de mi admin)
    // ════════════════════════════════════════
    public function createIncident()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $userId  = (int) $this->request->getPost('inc_user');
        $tipo    = $this->request->getPost('inc_tipo') ?? 'Otro';
        $fecha   = trim((string) ($this->request->getPost('inc_fecha') ?? ''));
        $detalle = trim($this->request->getPost('inc_detalle') ?? '');

        $u = $this->userModel->find($userId);
        if (!$u || (int) ($u['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Elige una persona de tu personal');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/incidencias'));
        }
        $tipos = ['Tardanza', 'Falta', 'Salida anticipada', 'Otro'];
        if (!in_array($tipo, $tipos, true)) $tipo = 'Otro';
        if ($detalle === '') {
            $s->setFlashdata('msg', 'Describe el detalle de la incidencia');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/incidencias'));
        }

        $this->incidentModel->insert([
            'admin_id'      => $adminId,
            'att_id'        => null,
            'user_id'       => $userId,
            'name'          => $u['name'] ?? '',
            'tipo'          => $tipo,
            'fecha'         => $fecha !== '' ? date('Y-m-d H:i:s', strtotime($fecha)) : date('Y-m-d H:i:s'),
            'detalle'       => $detalle,
            'estado'        => 'Pendiente',
            'justificacion' => '',
        ]);

        $s->setFlashdata('msg', 'Incidencia registrada y asignada a ' . ($u['name'] ?? 'la persona'));
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/incidencias'));
    }

    public function updateIncident()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $incId  = (int) $this->request->getPost('inc_id');

        $inc = $this->incidentModel->find($incId);
        if (!$inc || (int) ($inc['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/incidencias'));
        }

        $estado = $this->request->getPost('inc_estado');
        $just   = trim($this->request->getPost('inc_justificacion') ?? '');
        $this->incidentModel->update($incId, [
            'estado'        => $estado,
            'justificacion' => $just,
        ]);
        $s->setFlashdata('msg', 'Incidencia actualizada');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/incidencias'));
    }

    // ════════════════════════════════════════
    // CONFIGURACION (por admin)
    // ════════════════════════════════════════
    public function saveConfig()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $post = $this->request->getPost();

        $data = [];
        $map = [
            'cfg_empresa'         => ['empresa',            fn ($v) => trim($v) ?: 'DSG PERU TECHNOLOGY SAC'],
            'cfg_ruc'             => ['ruc',                fn ($v) => trim($v)],
            'cfg_direccion'       => ['direccion',          fn ($v) => trim($v)],
            'cfg_email'           => ['email',              fn ($v) => trim($v)],
            'cfg_telefono'        => ['telefono',           fn ($v) => trim($v)],
            'cfg_horario_default' => ['horario_default',    fn ($v) => trim($v) ?: '08:00-17:00'],
            'cfg_tolerancia'      => ['tolerancia_default', fn ($v) => (int) $v],
            'cfg_notif_email'     => ['notif_email',        fn ($v) => (bool) $v],
            'cfg_notif_email_dest' => ['notif_email_dest',  fn ($v) => trim($v)],
        ];
        foreach ($map as $field => [$column, $process]) {
            if (array_key_exists($field, $post)) {
                $data[$column] = $process($post[$field]);
            }
        }

        if (empty($data)) {
            $s->setFlashdata('msg', 'No hay cambios para guardar');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/configuracion'));
        }

        $this->configModel->saveConfig($data, $adminId);
        $s->setFlashdata('msg', 'Configuración guardada');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/configuracion'));
    }
}