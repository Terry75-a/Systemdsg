<?php
namespace App\Controllers;

use App\Controllers\Admin\AdminBaseController;

/**
 * Panel /admin — personal, horarios y códigos.
 *
 * La asistencia, su auditoría, la configuración y las incidencias
 * viven en App\Controllers\Admin\* (refactor #14).
 */
class AdminController extends AdminBaseController
{
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

    // ════════════════════════════════════════
    // PERSONAL — crear (código EMPL-XXX / PRCT-XXX por admin)
    // ════════════════════════════════════════
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
            'sch_dias'          => 'permit_empty|max_length[100]',
            'sch_hora_entrada'  => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_hora_salida'   => 'permit_empty|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/]',
            'sch_tolerancia'    => 'permit_empty|integer|between[0,120]',
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

}