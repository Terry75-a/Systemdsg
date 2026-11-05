<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CodeModel;
use App\Models\AttendanceModel;
use App\Models\AttendanceLogModel;
use App\Models\IncidentModel;
use App\Models\SessionLogModel;

class DevController extends BaseController
{
    protected UserModel $userModel;
    protected CodeModel $codeModel;
    protected AttendanceModel $attendanceModel;
    protected AttendanceLogModel $attendanceLogModel;
    protected IncidentModel $incidentModel;
    protected SessionLogModel $sessionLogModel;

    public function __construct()
    {
        $this->userModel          = new UserModel();
        $this->codeModel          = new CodeModel();
        $this->attendanceModel    = new AttendanceModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->incidentModel      = new IncidentModel();
        $this->sessionLogModel    = new SessionLogModel();

        // Igual que el panel admin: el navegador nunca guarda copia del HTML,
        // así los cambios de vistas/CSS se ven al recargar.
        service('response')->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        service('response')->setHeader('Pragma', 'no-cache');
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

        // Consola Dev (réplica de la página /dev de System-Trackin)
        $consola = \App\Libraries\Asistencia\DevConsole::build();

        return view('dev-panel', [
            'consola'       => $consola,
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
            'empleados' => $this->userModel->getEmpleados(),
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
    // PAGE: Auditoría del módulo de asistencia
    // ════════════════════════════════════════
    public function auditoria()
    {
        if ($redir = $this->guardDev()) return $redir;

        $logs = $this->attendanceLogModel->orderBy('id', 'DESC')->limit(200)->findAll();

        $attIds  = [];
        $userIds = [];
        foreach ($logs as $log) {
            if (!empty($log['attendance_id'])) {
                $attIds[] = (int) $log['attendance_id'];
            }
            if (!empty($log['user_id'])) {
                $userIds[] = (int) $log['user_id'];
            }
        }

        $atts = $attIds !== [] ? $this->attendanceModel->whereIn('id', array_unique($attIds))->findAll() : [];
        $attMap = [];
        foreach ($atts as $att) {
            $attMap[(int) $att['id']] = $att;
            if (!empty($att['user_id'])) {
                $userIds[] = (int) $att['user_id'];
            }
        }

        $users   = $userIds !== [] ? $this->userModel->whereIn('id', array_unique($userIds))->findAll() : [];
        $userMap = array_column($users, null, 'id');

        $filas = [];
        foreach ($logs as $log) {
            $att  = $attMap[(int) ($log['attendance_id'] ?? 0)] ?? null;
            $user = $userMap[(int) ($log['user_id'] ?? 0)] ?? ($att !== null ? ($userMap[(int) ($att['user_id'] ?? 0)] ?? null) : null);

            $filas[] = [
                'fecha'    => (string) ($att['date'] ?? ''),
                'marca'    => $att !== null
                    ? trim(($att['time_in'] ?? '') . ' → ' . ($att['time_out'] ?? ''))
                    : '',
                'empleado' => (string) ($att['name'] ?? ($user['name'] ?? '')),
                'accion'   => (string) ($log['accion'] ?? ''),
                'campo'    => (string) ($log['campo'] ?? ''),
                'anterior' => (string) ($log['valor_anterior'] ?? ''),
                'nuevo'    => (string) ($log['valor_nuevo'] ?? ''),
                'motivo'   => (string) ($log['motivo'] ?? ''),
                'autor'    => (string) ($log['autor'] ?? ''),
                'cuando'   => (string) ($log['created_at'] ?? ''),
                'attId'    => (int) ($log['attendance_id'] ?? 0),
                'adminId'  => (int) ($log['admin_id'] ?? 0),
            ];
        }

        return view('dev-auditoria', [
            'filas'     => $filas,
            'total'     => count($filas),
            'modalData' => $this->getModalData(),
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Sesiones (historial de entradas y salidas)
    // ════════════════════════════════════════
    public function sesiones()
    {
        if ($redir = $this->guardDev()) return $redir;

        $evento = $this->request->getGet('evento');
        $evento = in_array($evento, ['login', 'logout'], true) ? $evento : '';
        $q      = trim((string) $this->request->getGet('q'));

        return view('dev-sesiones', [
            'totales'   => $this->sessionLogModel->contarEventos(),
            'registros' => $this->sessionLogModel->filtrar($evento, $q),
            'fEvento'   => $evento,
            'fQ'        => $q,
        ]);
    }

    // ════════════════════════════════════════
    // PAGE: Base de datos (resumen general de lo acumulado)
    // ════════════════════════════════════════
    public function datos()
    {
        if ($redir = $this->guardDev()) return $redir;

        $db = \Config\Database::connect();

        $porRol = [];
        foreach ($this->userModel->select('role, COUNT(*) AS c')->groupBy('role')->findAll() as $r) {
            $porRol[(string) $r['role']] = (int) $r['c'];
        }

        $porEstadoAsist = [];
        foreach ($this->attendanceModel->select('status, COUNT(*) AS c')->groupBy('status')->findAll() as $r) {
            $porEstadoAsist[(string) $r['status']] = (int) $r['c'];
        }

        $porEstadoInc = [];
        foreach ($this->incidentModel->select('estado, COUNT(*) AS c')->groupBy('estado')->findAll() as $r) {
            $porEstadoInc[(string) $r['estado']] = (int) $r['c'];
        }

        $porEstadoCode = [];
        foreach ($this->codeModel->select('status, COUNT(*) AS c')->groupBy('status')->findAll() as $r) {
            $porEstadoCode[(string) $r['status']] = (int) $r['c'];
        }

        $totales = [
            'usuarios'        => (int) $this->userModel->countAllResults(),
            'asistencia'      => (int) $this->attendanceModel->countAllResults(),
            'incidencias'     => (int) $this->incidentModel->countAllResults(),
            'codigos'         => (int) $this->codeModel->countAllResults(),
            'sesiones'        => (int) $this->sessionLogModel->countAllResults(),
            'notificaciones'  => (int) $db->table('notifications')->countAllResults(),
            'passkeys'        => (int) $db->table('webauthn_credentials')->countAllResults(),
            'push'            => (int) $db->table('push_subscriptions')->countAllResults(),
        ];

        // Todas las tablas: filas exactas + tamaño
        $tablas = $db->query(
            "SELECT table_name AS nombre,
                    COALESCE(data_length, 0) + COALESCE(index_length, 0) AS bytes
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
             ORDER BY bytes DESC"
        )->getResultArray();

        $filasTotal   = 0;
        $bytesTotal   = 0;
        $tablaDetalle = [];
        foreach ($tablas as $t) {
            $filas = (int) $db->table($t['nombre'])->countAllResults();
            $bytes = (int) $t['bytes'];
            $filasTotal += $filas;
            $bytesTotal += $bytes;
            $tablaDetalle[] = ['nombre' => $t['nombre'], 'filas' => $filas, 'bytes' => $bytes];
        }

        return view('dev-datos', [
            'porRol'        => $porRol,
            'porEstadoAsist' => $porEstadoAsist,
            'porEstadoInc'  => $porEstadoInc,
            'porEstadoCode' => $porEstadoCode,
            'totales'       => $totales,
            'tablas'        => $tablaDetalle,
            'filasTotal'    => $filasTotal,
            'bytesTotal'    => $bytesTotal,
        ]);
    }

    /**
     * Registros detrás de cada cifra del resumen (para el modal de «Base de datos»):
     * ?tipo=rol|asist|inc|code|notif|passkey|push &v=valor (opcional). Máximo 100, los más recientes.
     */
    public function datosGrupo()
    {
        if ($redir = $this->guardDev()) return $redir;
        $db   = \Config\Database::connect();
        $tipo = (string) $this->request->getGet('tipo');
        $v    = (string) $this->request->getGet('v');

        $q = match ($tipo) {
            'rol'     => $db->table('users')->select("id, name AS titulo, COALESCE(NULLIF(cargo, ''), NULLIF(area, ''), role) AS sub, COALESCE(email, dni) AS extra, last_login AS fecha")->where('role', $v)->orderBy('last_login', 'DESC'),
            'asist'   => $db->table('attendance')->select("id, name AS titulo, CONCAT(COALESCE(time_in, '—'), ' → ', COALESCE(time_out, '—')) AS sub, observacion AS extra, date AS fecha")->where('status', $v)->orderBy('date', 'DESC')->orderBy('id', 'DESC'),
            'inc'     => $db->table('incidents')->select("id, name AS titulo, tipo AS sub, detalle AS extra, fecha")->where('estado', $v)->orderBy('fecha', 'DESC')->orderBy('id', 'DESC'),
            'code'    => $db->table('codes')->select("id, CONCAT(code, ' · ', COALESCE(name, '')) AS titulo, dni AS sub, used_by AS extra, created AS fecha")->where('status', $v)->orderBy('created', 'DESC'),
            'notif'   => $db->table('notifications n')->select("n.id, n.titulo, COALESCE(u.name, CONCAT('Usuario ', n.user_id)) AS sub, IF(n.leida, 'Leída', 'Sin leer') AS extra, n.created_at AS fecha")->join('users u', 'u.id = n.user_id', 'left')->orderBy('n.created_at', 'DESC'),
            'passkey' => $db->table('webauthn_credentials w')->select("w.id, COALESCE(u.name, CONCAT('Usuario ', w.user_id)) AS titulo, COALESCE(w.transports, '') AS sub, CONCAT('Usada ', COALESCE(w.last_used_at, 'nunca')) AS extra, w.created_at AS fecha")->join('users u', 'u.id = w.user_id', 'left')->orderBy('w.created_at', 'DESC'),
            'push'    => $db->table('push_subscriptions p')->select("p.id, COALESCE(u.name, CONCAT('Usuario ', p.user_id)) AS titulo, SUBSTRING_INDEX(SUBSTRING_INDEX(p.endpoint, '/', 3), '/', -1) AS sub, CONCAT('Visto ', COALESCE(p.last_seen, '—')) AS extra, p.created_at AS fecha")->join('users u', 'u.id = p.user_id', 'left')->orderBy('p.created_at', 'DESC'),
            default   => null,
        };
        if ($q === null) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msj' => 'Categoría no válida']);
        }
        $total = (int) (clone $q)->countAllResults(false);
        $rows  = $q->limit(100)->get()->getResultArray();
        foreach ($rows as &$r) {
            foreach (['titulo', 'sub', 'extra'] as $k) {
                $t = (string) ($r[$k] ?? '');
                $r[$k] = mb_strlen($t) > 120 ? mb_substr($t, 0, 117) . '…' : $t;
            }
        }
        unset($r);
        return $this->response->setJSON(['ok' => true, 'total' => $total, 'rows' => $rows]);
    }

    /**
     * Detalle de una tabla para el modal de «Base de datos»: estructura, índices,
     * tamaño y las últimas filas. Las columnas sensibles (contraseñas, llaves,
     * firmas, tokens) se devuelven ocultas.
     */
    public function datosTabla()
    {
        if ($redir = $this->guardDev()) return $redir;
        $db = \Config\Database::connect();
        $t  = (string) $this->request->getGet('t');

        $info = $db->query(
            "SELECT table_name AS nombre, engine, table_collation AS collation, auto_increment,
                    COALESCE(data_length, 0) AS data, COALESCE(index_length, 0) AS idx,
                    create_time, update_time, table_comment AS comentario
             FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
            [$t]
        )->getRowArray();
        if (! $info) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'msj' => 'Tabla no encontrada']);
        }

        $cols = $db->query(
            "SELECT column_name AS nombre, column_type AS tipo, is_nullable AS nulo, column_key AS llave,
                    column_default AS defecto, extra
             FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?
             ORDER BY ordinal_position",
            [$t]
        )->getResultArray();

        $indices = $db->query(
            "SELECT index_name AS nombre, NOT non_unique AS unico,
                    GROUP_CONCAT(column_name ORDER BY seq_in_index SEPARATOR ', ') AS columnas
             FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ?
             GROUP BY index_name, non_unique ORDER BY index_name = 'PRIMARY' DESC, index_name",
            [$t]
        )->getResultArray();

        $relaciones = $db->query(
            "SELECT column_name AS columna, referenced_table_name AS tabla, referenced_column_name AS ref
             FROM information_schema.key_column_usage
             WHERE table_schema = DATABASE() AND table_name = ? AND referenced_table_name IS NOT NULL",
            [$t]
        )->getResultArray();

        $filas = (int) $db->table($t)->countAllResults();

        // Últimas filas (por la llave primaria si existe)
        $pk = null;
        foreach ($cols as $c) {
            if ($c['llave'] === 'PRI') { $pk = $c['nombre']; break; }
        }
        $q = $db->table($t)->limit(8);
        if ($pk) $q->orderBy($pk, 'DESC');
        $muestra = $q->get()->getResultArray();

        $sensible = '/pass|token|secret|hash|credential|public_key|firma_datos|p256dh|auth|clave|codigo_verif/i';
        foreach ($muestra as &$row) {
            foreach ($row as $k => $v) {
                if ($v === null) continue;
                if (preg_match($sensible, (string) $k)) { $row[$k] = '•••• oculto'; continue; }
                $v = (string) $v;
                if (strlen($v) > 80) $row[$k] = mb_substr($v, 0, 77) . '…';
            }
        }
        unset($row);

        return $this->response->setJSON([
            'ok'         => true,
            'info'       => $info,
            'filas'      => $filas,
            'columnas'   => $cols,
            'indices'    => $indices,
            'relaciones' => $relaciones,
            'muestra'    => $muestra,
        ]);
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

        $this->userModel->update($dev['id'], ['password' => password_hash($new, PASSWORD_DEFAULT)]);

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
        // Contraseña personalizada opcional desde el modal; si no, se genera
        $postPw    = (string) $this->request->getPost('password');
        $password  = (strlen($postPw) >= 6) ? $postPw : $this->generateSecurePassword();

        $this->userModel->insert([
            'name'       => $name,
            'email'      => $email,
            'dni'        => $dni,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
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
        $realPassword = null;
        if (!empty($password) && strlen($password) >= 6) {
            $data['password']    = password_hash($password, PASSWORD_DEFAULT);
            $realPassword        = $password;
        }

        if (!$this->userModel->update($userId, $data)) {
            $s->setFlashdata('msg', 'Admin no encontrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/admins'));
        }

        $s->setFlashdata('msg', "Admin {$name} actualizado");
        $s->setFlashdata('tipo', 'success');

        // Si cambió la contraseña → mostrar la contraseña REAL una sola vez
        if ($realPassword !== null) {
            $u = $this->userModel->find($userId) ?? [];
            $s->setFlashdata('creds', [
                'mode'    => 'update',
                'name'    => $u['name'] ?? $name,
                'role'    => 'Admin',
                'dni'     => $u['dni'] ?? $dni,
                'email'   => $u['email'] ?? $email,
                'password'=> $realPassword,
                'code'    => $u['admin_code'] ?? 'ADMIN-???',
                'empresa' => $u['empresa'] ?? ($empresa ?: ''),
            ]);
        }
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

        // Selector: "__new__" = persona que aún no tiene cuenta (invitación de registro)
        $isNew = ($name === '__new__');
        if ($isNew) {
            $name = trim($this->request->getPost('name_new'));
        }

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

        // Solo las invitaciones de registro exigen DNI sin cuenta:
        // los códigos para empleados ya creados son para entrar con Código+DNI.
        if ($isNew && $this->userModel->findByDni($dni)) {
            $s->setFlashdata('msg', 'Este DNI ya tiene cuenta: elígelo en el selector en vez de crearlo como persona nueva');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('dios/codigos'));
        }

        // Coherencia: si el DNI pertenece a un usuario creado, el nombre es el suyo
        if (!$isNew) {
            $dueno = $this->userModel->findByDni($dni);
            if ($dueno) $name = $dueno['name'];
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
    public function registerForm()
    {
        if ($redir = $this->guardDev()) return $redir;

        return view('registro-empleado');
    }

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
            'password'      => password_hash($password, PASSWORD_DEFAULT),
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