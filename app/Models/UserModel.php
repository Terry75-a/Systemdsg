<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'name', 'email', 'dni', 'password', 'role',
        'area', 'cargo', 'admin_id', 'created_by',
        'estado', 'last_login',
        'admin_code', 'personal_code', 'empresa',
        'institucion', 'semestre',
        'huella_registrada', 'rostro_registrado',
        'huella_fecha', 'rostro_fecha', 'rostro_path', 'huella_cred_id',
        'contract_type', 'contract_duration', 'contract_start', 'contract_end',
        'firma_tipo', 'firma_datos', 'schedule_id',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== null && $data['data']['password'] !== '') {
            $pwd = $data['data']['password'];
            if (str_starts_with($pwd, '$2y$') || str_starts_with($pwd, '$argon')) {
                return $data;
            }
            $data['data']['password'] = password_hash($pwd, PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function findByDni(string $dni): ?array
    {
        return $this->where('dni', $dni)->first();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findDev(): ?array
    {
        return $this->where('role', 'Dev')->first();
    }

    // ════════════════════════════════════════════════════════
    // GENERACIÓN DE CÓDIGOS
    // Dev = dios → DEV-001
    // Admin → ADMIN-001, ADMIN-002, ... (secuencia global)
    // Personal → EMPL-001 / PRCT-001, ... (secuencia por admin)
    // ════════════════════════════════════════════════════════

    private function nextSequence(string $prefix, ?int $adminId): int
    {
        $builder = $this->like('personal_code', "{$prefix}-");
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        $max = 0;
        foreach ($builder->findAll() as $row) {
            $n = (int) substr((string) $row['personal_code'], strlen($prefix) + 1);
            $max = max($max, $n);
        }
        return $max + 1;
    }

    public function nextAdminCode(): string
    {
        $max = 0;
        foreach ($this->where('role', 'Admin')->like('admin_code', 'ADMIN-%', 'after')->findAll() as $row) {
            $n = (int) substr((string) $row['admin_code'], 6);
            $max = max($max, $n);
        }
        return sprintf('ADMIN-%03d', $max + 1);
    }

    public function nextPersonalCode(int $adminId, string $role): string
    {
        $prefix = $role === 'Practicante' ? 'PRCT' : 'EMPL';
        return sprintf('%s-%03d', $prefix, $this->nextSequence($prefix, $adminId));
    }

    public function publicCodeOf(array $user): string
    {
        return (string) ($user['personal_code'] ?? ($user['admin_code'] ?? ''));
    }

    // ════════════════════════════════════════════════════════
    // CONSULTAS GLOBALES (DEV)
    // ════════════════════════════════════════════════════════

    public function getAdmins(): array
    {
        return $this->where('role', 'Admin')->orderBy('admin_code', 'ASC')->findAll();
    }

    public function getAdminsWithStats(): array
    {
        $admins = $this->getAdmins();
        $result = [];
        foreach ($admins as $admin) {
            $result[] = [
                'id'              => $admin['id'],
                'name'            => $admin['name'],
                'dni'             => $admin['dni'],
                'email'           => $admin['email'],
                'admin_code'      => $admin['admin_code'],
                'empresa'         => $admin['empresa'] ?? '',
                'estado'          => $admin['estado'] ?? 'Activo',
                'created'         => $admin['created'] ?? '',
                'last_login'      => $admin['last_login'] ?? null,
                'totalPersonal'   => $this->where('admin_id', $admin['id'])->whereIn('role', ['Empleado', 'Practicante'])->countAllResults(),
                'empleadosCount'  => $this->where('admin_id', $admin['id'])->where('role', 'Empleado')->countAllResults(),
                'practicantesCount' => $this->where('admin_id', $admin['id'])->where('role', 'Practicante')->countAllResults(),
            ];
        }
        return $result;
    }

    public function getEmpleados(): array
    {
        return $this->whereIn('role', ['Empleado', 'Practicante'])->orderBy('id', 'DESC')->findAll();
    }

    // ════════════════════════════════════════════════════════
    // CONSULTAS SCOPEADAS POR ADMIN (cada admin ve lo suyo)
    // ════════════════════════════════════════════════════════

    public function getPersonalByAdmin(int $adminId): array
    {
        return $this->where('admin_id', $adminId)
            ->whereIn('role', ['Empleado', 'Practicante'])
            ->orderBy('personal_code', 'ASC')
            ->findAll();
    }

    public function getEmpleadosByAdmin(int $adminId): array
    {
        return $this->where('admin_id', $adminId)
            ->where('role', 'Empleado')
            ->orderBy('personal_code', 'ASC')
            ->findAll();
    }

    public function getPracticantesByAdmin(int $adminId): array
    {
        return $this->where('admin_id', $adminId)
            ->where('role', 'Practicante')
            ->orderBy('personal_code', 'ASC')
            ->findAll();
    }

    public function getTeamStats(int $adminId, array $empleados = [], array $practicantes = []): array
    {
        $empel   = $empleados ?: $this->getEmpleadosByAdmin($adminId);
        $practic = $practicantes ?: $this->getPracticantesByAdmin($adminId);
        return [
            'totalPersonal'   => count($empel) + count($practic),
            'empleadosCount'  => count($empel),
            'practicantesCount' => count($practic),
            'activosCount'    => count(array_filter(array_merge($empel, $practic), fn($p) => ($p['estado'] ?? 'Activo') === 'Activo')),
        ];
    }

    public function hasPersonal(int $adminId): bool
    {
        return $this->where('admin_id', $adminId)->whereIn('role', ['Empleado', 'Practicante'])->countAllResults() > 0;
    }

    // ════════════════════════════════════════════════════════
    // ELIMINACIÓN EN CASCADA DE UN ADMIN (todo lo suyo)
    // ════════════════════════════════════════════════════════

    public function cascadeDeleteAdmin(int $adminId): bool
    {
        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            // Códigos de registro generados por ese admin
            $db->table('codes')->where('admin_id', $adminId)->delete();

            // Horarios del admin
            $db->table('schedules')->where('admin_id', $adminId)->delete();

            // Configuración propia del admin
            $db->table('admin_config')->where('admin_id', $adminId)->delete();

            // Asistencias e incidencias del personal del admin
            $personal = $this->where('admin_id', $adminId)->whereIn('role', ['Empleado', 'Practicante'])->findAll();
            foreach ($personal as $p) {
                $db->table('attendance')->where('user_id', $p['id'])->delete();
                $db->table('incidents')->where('user_id', $p['id'])->delete();
            }

            // El personal del admin
            $db->table('users')->where('admin_id', $adminId)->whereIn('role', ['Empleado', 'Practicante'])->delete();

            // El propio admin
            $db->table('users')->where('id', $adminId)->delete();

            $db->transCommit();
            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'cascadeDeleteAdmin falló: ' . $e->getMessage());
            return false;
        }
    }

    // ════════════════════════════════════════════════════════
    // MISC
    // ════════════════════════════════════════════════════════

    public function updateLastLogin(int $userId): void
    {
        $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}