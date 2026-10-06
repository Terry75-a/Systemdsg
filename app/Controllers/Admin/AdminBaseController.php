<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CodeModel;
use App\Models\AttendanceModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;
use App\Models\ConfigModel;
use App\Models\AttendanceLogModel;
use App\Models\FestivoModel;
use App\Libraries\Asistencia\Jornada;

/**
 * Base de los controladores del panel /admin.
 *
 * Aquí viven lo que todos comparten: sesión/rol, datos del admin actual,
 * armado de la data común de las vistas, paginación (#13) y validación (#6).
 */
abstract class AdminBaseController extends BaseController
{
    /** Filas por página en los listados largos (#13). */
    protected const POR_PAGINA = 50;

    protected UserModel $userModel;
    protected CodeModel $codeModel;
    protected AttendanceModel $attendanceModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;
    protected ConfigModel $configModel;
    protected AttendanceLogModel $logModel;
    protected FestivoModel $festivoModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->codeModel       = new CodeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel   = new ScheduleModel();
        $this->incidentModel   = new IncidentModel();
        $this->configModel     = new ConfigModel();
        $this->logModel        = new AttendanceLogModel();
        $this->festivoModel    = new FestivoModel();
    }

    // Solo un Admin entra aquí (cada uno ve únicamente lo suyo)
    protected function guard()
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

    protected function adminId(): int
    {
        return (int) session()->get('user_id');
    }

    /**
     * (#6) Valida el POST con las reglas de CodeIgniter y, si falla,
     * devuelve el redirect hacia $destino con los errores en el flash.
     * Devuelve null cuando todo está bien.
     */
    protected function validar(array $reglas, string $destino, array $mensajes = [])
    {
        if ($this->validate($reglas, $mensajes)) {
            return null;
        }

        $errores = implode(' · ', array_values($this->validator->getErrors()));
        session()->setFlashdata('msg', $errores !== '' ? $errores : 'Revisa los datos del formulario');
        session()->setFlashdata('tipo', 'warning');

        return redirect()->to(base_url($destino));
    }

    protected function admin(): array
    {
        return $this->userModel->find($this->adminId()) ?? [];
    }

    protected function adminCode(): string
    {
        $a = $this->admin();
        return (string) ($a['admin_code'] ?? 'ADMIN-???');
    }

    protected function adminEmpresa(): string
    {
        $a = $this->admin();
        return (string) ($a['empresa'] ?? '');
    }

    protected function getPersonal(): array
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

    protected function viewData(array $extra = []): array
    {
        $adminId      = $this->adminId();
        $personal     = $this->getPersonal();
        $empleados    = array_values(array_filter($personal, fn($e) => $e['role'] === 'Empleado'));
        $practicantes = array_values(array_filter($personal, fn($e) => $e['role'] === 'Practicante'));
        $attendance   = $this->attendanceModel->getAll($adminId);
        $stats        = $this->userModel->getTeamStats($adminId, $empleados, $practicantes);
        $incidents    = $this->incidentModel->getAll($adminId);

        return array_merge([
            'empleados'       => $personal,
            'personal'        => $personal,
            'empleadosOnly'   => $empleados,
            'practicantes'    => $practicantes,
            'attendance'      => $attendance,
            'schedules'       => $this->scheduleModel->getAll($adminId),
            'incidents'       => $incidents,
            'codes'           => $this->codeModel->getAll($adminId),
            'totalEmpleados'  => $stats['totalPersonal'],
            'empleadosCount'  => $stats['empleadosCount'],
            'practicantesCount' => $stats['practicantesCount'],
            'hoyPresentes'    => $this->attendanceModel->countTodayByStatus('present', $adminId),
            'hoyTardanzas'    => $this->attendanceModel->countTodayByStatus('late', $adminId),
            'hoyFaltas'       => $this->sinMarcarHoy($adminId, $personal),
            'incPendientes'   => $this->contarPendientes($incidents),
            'adminCode'       => $this->adminCode(),
            'adminEmpresa'    => $this->adminEmpresa(),
        ], $extra);
    }

    /**
     * (#2) Personas activas que todavía no marcaron hoy.
     * En festivos o fin de semana devuelve 0 (nadie debe marcar).
     */
    protected function sinMarcarHoy(int $adminId, array $personal): int
    {
        $hoy = date('Y-m-d');

        if ($this->festivoModel->esFestivo($hoy, $adminId) || ! Jornada::esDiaLaborable(null, $hoy)) {
            return 0;
        }

        $marcados = array_map('intval', array_column($this->attendanceModel->getByDate($hoy, $adminId), 'user_id'));

        $activos = array_filter($personal, fn ($u) =>
            ($u['estado'] ?? '') === 'Activo'
            && in_array($u['role'] ?? '', ['Empleado', 'Practicante'], true)
        );

        return count(array_filter($activos, fn ($u) => ! in_array((int) $u['id'], $marcados, true)));
    }

    /** (#5) Incidencias que siguen esperando acción del admin. */
    protected function contarPendientes(array $incidents): int
    {
        $pendientes = 0;
        foreach ($incidents as $inc) {
            if (in_array($inc['estado'] ?? '', ['Pendiente', 'Revisión'], true)) {
                $pendientes++;
            }
        }
        return $pendientes;
    }

    /**
     * (#13) Corta el listado en páginas de 50 y arma los datos
     * que necesita el paginador de la vista.
     */
    protected function paginar(array $filas, int $pagina, array $params): array
    {
        $total   = count($filas);
        $paginas = max(1, (int) ceil($total / self::POR_PAGINA));
        $pagina  = max(1, min($pagina, $paginas));

        unset($params['page']);

        return [
            'filas'      => array_slice($filas, ($pagina - 1) * self::POR_PAGINA, self::POR_PAGINA),
            'pagina'     => $pagina,
            'paginas'    => $paginas,
            'totalFilas' => $total,
            'pag_qs'     => $params,
        ];
    }

    // Datos de contrato (tipo, duracion y fechas) desde el POST
    protected function contractFields(): array
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
    protected function firmaFields(string $name): array
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

    protected function generateSecurePassword(): string
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
}
