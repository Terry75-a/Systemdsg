<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CodeModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected CodeModel $codeModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->codeModel  = new CodeModel();
    }

    // ════════════════════════════════════════
    // FORMULARIO DE REGISTRO
    // ════════════════════════════════════════
    public function registerForm()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard-verde'));
        }
        return view('registro');
    }

    // ════════════════════════════════════════
    // PROCESAR REGISTRO (POST)
    // ════════════════════════════════════════
    public function register()
    {
        $session = session();
        $name     = trim($this->request->getPost('name'));
        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $confirm  = $this->request->getPost('password_confirm');

        if (empty($name) || empty($email) || empty($password)) {
            $session->setFlashdata('msg', 'Todos los campos son obligatorios');
            $session->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->setFlashdata('msg', 'El correo no es válido');
            $session->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro'));
        }

        if (strlen($password) < 6) {
            $session->setFlashdata('msg', 'La contraseña debe tener al menos 6 caracteres');
            $session->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro'));
        }

        if ($password !== $confirm) {
            $session->setFlashdata('msg', 'Las contraseñas no coinciden');
            $session->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('registro'));
        }

        if ($this->userModel->findByEmail($email)) {
            $session->setFlashdata('msg', 'Este correo ya está registrado');
            $session->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('registro'));
        }

        $this->userModel->insert([
            'name'     => $name,
            'email'    => $email,
            'dni'      => '',
            'password' => $password,
            'role'     => 'Usuario',
        ]);

        $session->setFlashdata('msg', 'Cuenta creada Ahora puedes iniciar sesión');
        $session->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('login-verde'));
    }

    // ════════════════════════════════════════
    // FORMULARIO DE LOGIN
    // ════════════════════════════════════════
    public function loginForm()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->homeFor(session()->get('user_role')));
        }
        return view('login-asisten');
    }

    // Redirige según el rol a su panel
    private function homeFor(string $role): string
    {
        if ($role === 'Dev') return 'dios';
        if ($role === 'Admin') return 'admin';
        return 'mi-panel';
    }

    // Guarda en sesión: logged_in, ids, códigos (ADMIN-XXX / EMPL-XXX / PRCT-XXX), empresa y rol
    private function setSessionUser(array $u): void
    {
        session()->regenerate(true);
        session()->set([
            'logged_in'      => true,
            'user_id'        => $u['id'],
            'user_name'      => $u['name'],
            'user_email'     => $u['email'],
            'user_role'      => $u['role'] ?? 'Empleado',
            'user_dni'       => $u['dni'] ?? '',
            'user_code'      => $u['personal_code'] ?? ($u['admin_code'] ?? ''),
            'user_admin_code' => $u['admin_code'] ?? '',
            'user_empresa'   => $u['empresa'] ?? '',
        ]);
    }

    // ════════════════════════════════════════
    // PROCESAR LOGIN (POST)
    // ════════════════════════════════════════
    public function login()
    {
        $session   = session();
        $loginType = $this->request->getPost('login_type') ?? 'email';

        if ($loginType === 'dni') {
            $dni      = trim($this->request->getPost('dni'));
            $password = $this->request->getPost('password');

            if (empty($dni) || empty($password)) {
                $session->setFlashdata('msg', 'Ingresa tu DNI y contraseña');
                $session->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('login-verde'));
            }
            if (strlen($dni) !== 8 || !ctype_digit($dni)) {
                $session->setFlashdata('msg', 'El DNI debe tener 8 dígitos');
                $session->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('login-verde'));
            }

            $found = $this->userModel->findByDni($dni);

            if (!$found || !password_verify($password, $found['password'])) {
                $session->setFlashdata('msg', 'DNI o contraseña incorrectos');
                $session->setFlashdata('tipo', 'danger');
                return redirect()->to(base_url('login-verde'));
            }

            $this->userModel->updateLastLogin($found['id']);
            $this->setSessionUser($found);

            $role = $found['role'] ?? 'Empleado';
            return redirect()->to(base_url($this->homeFor($role)));
        }

        // Login por codigo
        if ($loginType === 'code') {
            $code = strtoupper(trim($this->request->getPost('code')));
            $dni  = trim($this->request->getPost('dni'));

            if (empty($code) || empty($dni)) {
                $session->setFlashdata('msg', 'Ingresa el codigo y tu DNI');
                $session->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('login-verde'));
            }
            if (strlen($dni) !== 8 || !ctype_digit($dni)) {
                $session->setFlashdata('msg', 'El DNI debe tener 8 digitos');
                $session->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('login-verde'));
            }

            $foundCode = $this->codeModel->findActiveByCode($code);
            if (!$foundCode) {
                $session->setFlashdata('msg', 'Codigo invalido o ya fue usado');
                $session->setFlashdata('tipo', 'danger');
                return redirect()->to(base_url('login-verde'));
            }
            if ($foundCode['dni'] !== $dni) {
                $session->setFlashdata('msg', 'El codigo no corresponde a este DNI');
                $session->setFlashdata('tipo', 'danger');
                return redirect()->to(base_url('login-verde'));
            }

            $user = $this->userModel->findByDni($dni);
            if (!$user) {
                $session->setFlashdata('msg', 'No se encontro usuario con ese DNI');
                $session->setFlashdata('tipo', 'danger');
                return redirect()->to(base_url('login-verde'));
            }

            $this->codeModel->markUsed($foundCode['id'], $user['id']);
            $this->userModel->updateLastLogin($user['id']);
            $this->setSessionUser($user);

            $role = $user['role'] ?? 'Empleado';
            return redirect()->to(base_url($this->homeFor($role)));
        }

        // Login por email
        $email    = trim($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            $session->setFlashdata('msg', 'Ingresa tu correo y contraseña');
            $session->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('login-verde'));
        }

        $found = $this->userModel->findByEmail($email);

        if (!$found || !password_verify($password, $found['password'])) {
            $session->setFlashdata('msg', 'Correo o contraseña incorrectos');
            $session->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('login-verde'));
        }

        $this->userModel->updateLastLogin($found['id']);
        $this->setSessionUser($found);

        $role = $found['role'] ?? 'Empleado';
        return redirect()->to(base_url($this->homeFor($role)));
    }

    // ════════════════════════════════════════
    // LOGOUT
    // ════════════════════════════════════════
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login-verde'));
    }

    // ════════════════════════════════════════
    // DASHBOARD
    // ════════════════════════════════════════
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login-verde'));
        }
        return view('dashboard-verde');
    }
}
