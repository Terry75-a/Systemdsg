<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CodeModel;

class DevController extends BaseController
{
    protected UserModel $userModel;
    protected CodeModel $codeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->codeModel = new CodeModel();
    }

    // Solo el Dev (dios) entra aquí
    protected function guardDev(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }
        if ((session()->get('user_role') ?? '') !== 'Dev') {
            return redirect()->to(base_url(session()->get('user_role') === 'Admin' ? 'admin' : 'mi-panel'));
        }
        return null;
    }

    protected function getModalData(): array
    {
        return [
            'admins'    => $this->userModel->getAdmins(),
            'empleados' => $this->userModel->getEmpleados(),
            'codes'     => $this->codeModel->getAll(),
        ];
    }

    // ════════════════════════════════════════
    // PANEL DEV — Dashboard
    // ════════════════════════════════════════
    public function index()
    {
        if ($redir = $this->guardDev()) return $redir;
        $adminsWithStats = $this->userModel->getAdminsWithStats();
        $admins       = $this->userModel->getAdmins();
        $empleados    = $this->userModel->getEmpleados();
        $codes        = $this->codeModel->getAll();
        $activeCodes  = $this->codeModel->getActive();

        return view('dev-panel', [
            'admins'        => $admins,
            'adminsWithStats' => $adminsWithStats,
            'empleados'     => $empleados,
            'codes'         => $codes,
            'adminCount'    => count($admins),
            'empleadoCount' => count($empleados),
            'activeCodes'   => count($activeCodes),
            'totalUsers'    => $this->userModel->countAllResults(),
            'modalData'     => $this->getModalData(),
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Administradores
    // ════════════════════════════════════════
    public function admins()
    {
        if ($redir = $this->guardDev()) return $redir;
        return view('dev-admins', [
            'admins'    => $this->userModel->getAdminsWithStats(),
            'modalData' => $this->getModalData(),
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Códigos
    // ════════════════════════════════════════
    public function codigos()
    {
        if ($redir = $this->guardDev()) return $redir;
        return view('dev-codigos', [
            'codes'     => $this->codeModel->getAll(),
            'admins'    => $this->userModel->getAdmins(),
            'modalData' => $this->getModalData(),
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Empleados (vista global del Dev)
    // ════════════════════════════════════════
    public function empleados()
    {
        if ($redir = $this->guardDev()) return $redir;
        return view('dev-empleados', [
            'empleados' => $this->userModel->getEmpleados(),
            'modalData' => $this->getModalData(),
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Estructura
    // ════════════════════════════════════════
    public function estructura()
    {
        if ($redir = $this->guardDev()) return $redir;
        return view('dev-estructura', ['modalData' => $this->getModalData()]);
    }

    // ════════════════════════════════════════
    // PAGE: Perfil
    // ════════════════════════════════════════
    public function perfil()
    {
        if ($redir = $this->guardDev()) return $redir;
        $dev = $this->userModel->findDev();
        if (!$dev) {
            $dev = ['name' => 'Superadmin', 'dni' => '00000000', 'email' => '', 'role' => 'Dev', 'admin_code' => 'DEV-001', 'created' => '-', 'last_login' => null];
        }
        return view('dev-perfil', ['user' => $dev]);
    }

    public function updatePassword()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        if (empty($current) || empty($new)) {
            $s->setFlashdata('msg', 'Todos los campos son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/perfil'));
        }
        if (strlen($new) < 6) {
            $s->setFlashdata('msg', 'La nueva contraseña debe tener al menos 6 caracteres');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/perfil'));
        }
        if ($new !== $confirm) {
            $s->setFlashdata('msg', 'Las contraseñas no coinciden');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/perfil'));
        }

        $dev = $this->userModel->findDev();
        if (!$dev || !password_verify($current, $dev['password'])) {
            $s->setFlashdata('msg', 'La contraseña actual es incorrecta');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/perfil'));
        }

        $this->userModel->update($dev['id'], ['password' => $new]);

        $s->setFlashdata('msg', 'Contraseña actualizada correctamente');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('dios/perfil'));
    }

    // ════════════════════════════════════════
    // CREAR ADMIN → código automático ADMIN-XXX,
    // email autogenerado (u opcional), contraseña segura automática
    // ════════════════════════════════════════
    public function createAdmin()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $name     = trim($this->request->getPost('name'));
        $dni      = trim($this->request->getPost('dni'));
        $email    = trim($this->request->getPost('email'));
        $empresa  = trim($this->request->getPost('empresa'));

        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/admins'));
        }
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            $s->setFlashdata('msg', 'El DNI debe tener 8 dígitos');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/admins'));
        }

        if ($this->userModel->findByDni($dni)) {
            $s->setFlashdata('msg', 'Este DNI ya está registrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/admins'));
        }

        // Email autogenerado (nombre.apellido@dsg.pe) si no lo escriben
        if (empty($email)) {
            $email = $this->autoEmail($name);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $s->setFlashdata('msg', 'El correo no es válido');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/admins'));
        }

        $adminCode = $this->userModel->nextAdminCode();
        $password  = $this->generateSecurePassword();

        $this->userModel->insert([
            'name'       => $name,
            'email'      => $email,
            'dni'        => $dni,
            'password'   => $password,
            'role'       => 'Admin',
            'admin_code' => $adminCode,
            'empresa'    => $empresa ?: "Empresa {$adminCode}",
            'created_by' => 'dev',
            'estado'     => 'Activo',
        ]);

        // Configuración propia del nuevo admin
        $configModel = new \App\Models\ConfigModel();
        $configModel->saveConfig(['empresa' => $empresa ?: "Empresa {$adminCode}"], (int) db_connect()->insertID());

        // Mostrar credenciales generadas una sola vez
        $s->setFlashdata('creds', [
            'name'          => $name,
            'role'          => 'Admin',
            'dni'           => $dni,
            'email'         => $email,
            'password'      => $password,
            'code'          => $adminCode,
            'empresa'       => $empresa ?: "Empresa {$adminCode}",
        ]);
        $s->setFlashdata('msg', "Admin {$name} creado con código {$adminCode}");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('dios/admins'));
    }

    // Genera un correo nombre.apellido@dsg.pe único (sin repetir en la BD)
    private function autoEmail(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $first = strtolower(preg_replace('/[^a-zA-Z]/', '', $parts[0] ?? 'admin'));
        $last  = strtolower(preg_replace('/[^a-zA-Z]/', '', end($parts)));
        $email = ($first === $last) ? $first . '@dsg.pe' : $first . '.' . $last . '@dsg.pe';

        $counter = 1;
        $base = $email;
        while ($this->userModel->findByEmail($email)) {
            $email = $counter . '.' . $base;
            $counter++;
        }
        return $email;
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
    // ACTUALIZAR ADMIN
    // ════════════════════════════════════════
    public function updateAdmin()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $userId   = (int) $this->request->getPost('user_id');
        $name     = trim($this->request->getPost('name'));
        $dni      = trim($this->request->getPost('dni'));
        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $empresa  = trim($this->request->getPost('empresa'));

        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/admins'));
        }
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            $s->setFlashdata('msg', 'El DNI debe tener 8 dígitos');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/admins'));
        }

        $data = ['name' => $name, 'dni' => $dni, 'email' => $email];
        if ($empresa !== '') {
            $data['empresa'] = $empresa;
        }
        if (!empty($password) && strlen($password) >= 6) {
            $data['password'] = $password;
        }

        if (!$this->userModel->update($userId, $data)) {
            $s->setFlashdata('msg', 'Admin no encontrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/admins'));
        }

        $s->setFlashdata('msg', "Admin {$name} actualizado");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('dios/admins'));
    }

    // ════════════════════════════════════════
    // ELIMINAR ADMIN → borra TODO lo suyo (cascada)
    // ════════════════════════════════════════
    public function deleteAdmin()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $adminId = (int) $this->request->getPost('user_id');
        $admin   = $this->userModel->find($adminId);

        if (!$admin || ($admin['role'] ?? '') !== 'Admin') {
            $s->setFlashdata('msg', 'Admin no encontrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/admins'));
        }

        if ($this->userModel->cascadeDeleteAdmin($adminId)) {
            $s->setFlashdata('msg', "Admin {$admin['name']} ({$admin['admin_code']}) y todo su personal fueron eliminados");
            $s->setFlashdata('tipo', 'success');
        } else {
            $s->setFlashdata('msg', 'No se pudo eliminar el admin. Intenta de nuevo.');
            $s->setFlashdata('tipo', 'danger');
        }
        return redirect()->to(base_url('dios/admins'));
    }

    // ════════════════════════════════════════
    // GENERAR CÓDIGO PARA EMPLEADO
    // ════════════════════════════════════════
    public function createCode()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $name    = trim($this->request->getPost('name'));
        $dni     = trim($this->request->getPost('dni'));
        $adminId = (int) $this->request->getPost('admin_id');

        if (empty($name) || empty($dni)) {
            $s->setFlashdata('msg', 'Nombre y DNI son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/codigos'));
        }
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            $s->setFlashdata('msg', 'El DNI debe tener 8 dígitos');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/codigos'));
        }

        if ($this->userModel->findByDni($dni)) {
            $s->setFlashdata('msg', 'Este DNI ya está registrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/codigos'));
        }

        if ($this->codeModel->findByDniActive($dni)) {
            $s->setFlashdata('msg', 'Ya existe un código activo para este DNI');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('dios/codigos'));
        }

        $code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $this->codeModel->insert([
            'code'     => $code,
            'name'     => $name,
            'dni'      => $dni,
            'admin_id' => $adminId,
            'status'   => 'active',
        ]);

        $s->setFlashdata('msg', "Código generado: {$code}");
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('dios/codigos'));
    }

    public function deleteCode()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $codeId = (int) $this->request->getPost('code_id');
        $this->codeModel->delete($codeId);
        $s->setFlashdata('msg', 'Código eliminado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('dios/codigos'));
    }

    // ════════════════════════════════════════
    // ELIMINAR USUARIO GLOBAL (dev/empleados)
    // ════════════════════════════════════════
    public function deleteUser()
    {
        if ($redir = $this->guardDev()) return $redir;
        $s = session();
        $userId = (int) $this->request->getPost('user_id');
        $user = $this->userModel->find($userId);

        if ($user) {
            $db = \Config\Database::connect();
            $db->table('attendance')->where('user_id', $userId)->delete();
            $db->table('incidents')->where('user_id', $userId)->delete();
            $this->userModel->delete($userId);
        }

        $s->setFlashdata('msg', 'Usuario eliminado');
        $s->setFlashdata('tipo', 'success');
        $role = $user['role'] ?? '';
        if ($role === 'Admin') return redirect()->to(base_url('dios/admins'));
        return redirect()->to(base_url('dios/empleados'));
    }

    // ════════════════════════════════════════
    // REGISTRO EMPLEADO CON CÓDIGO
    // ════════════════════════════════════════
    public function registerForm() { return view('registro-empleado'); }

    public function registerEmployee()
    {
        $s = session();
        $code     = strtoupper(trim($this->request->getPost('code')));
        $password = $this->request->getPost('password');
        $confirm  = $this->request->getPost('password_confirm');

        if (empty($code) || empty($password)) {
            $s->setFlashdata('msg', 'Código y contraseña son obligatorios');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro-empleado'));
        }
        if (strlen($password) < 6) {
            $s->setFlashdata('msg', 'La contraseña debe tener al menos 6 caracteres');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro-empleado'));
        }
        if ($password !== $confirm) {
            $s->setFlashdata('msg', 'Las contraseñas no coinciden');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro-empleado'));
        }

        $foundCode = $this->codeModel->findActiveByCode($code);
        if (!$foundCode) {
            $s->setFlashdata('msg', 'Código inválido o ya utilizado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('registro-empleado'));
        }

        $adminId = $foundCode['admin_id'] ?? null;
        $admin   = $adminId ? $this->userModel->find($adminId) : null;

        $role = ($admin && ($admin['role'] ?? '') === 'Admin') ? 'Empleado' : 'Empleado';
        $personalCode = $adminId ? $this->userModel->nextPersonalCode($adminId, $role) : null;

        $newId = $this->userModel->insert([
            'name'          => $foundCode['name'],
            'email'         => '',
            'dni'           => $foundCode['dni'],
            'password'      => $password,
            'role'          => $role,
            'admin_id'      => $adminId,
            'admin_code'    => $admin ? ($admin['admin_code'] ?? null) : null,
            'personal_code' => $personalCode,
            'created_by'    => 'code',
            'estado'        => 'Activo',
        ]);

        $this->codeModel->markUsed($foundCode['id'], $newId);

        $s->setFlashdata('msg', 'Cuenta creada. Ahora puedes iniciar sesión con tu DNI');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('login-verde'));
    }
}