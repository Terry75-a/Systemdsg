<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'user_id', 'name', 'dni', 'date', 'time_in', 'time_out',
        'status', 'observacion', 'evidencia', 'lat', 'lng', 'ip', 'geo_ok',
    ];

    public function findByUserAndDate(int $userId, string $date): ?array
    {
        return $this->where('user_id', $userId)->where('date', $date)->first();
    }

    /**
     * Salidas sin registrar de días anteriores (entrada marcada, salida vacía).
     * Se ordenan de la más antigua a la más reciente: el colaborador debe
     * cerrarlas en ese orden antes de poder marcar la entrada de hoy.
     */
    public function findPendingExits(int $userId, ?string $before = null): array
    {
        return $this->where('user_id', $userId)
            ->where('date <', $before ?? date('Y-m-d'))
            ->where('time_in IS NOT NULL', null, false)
            ->where('time_out', null)
            ->orderBy('date', 'ASC')
            ->findAll();
    }

    /** La salida pendiente más antigua del colaborador (o null). */
    public function findOldestPendingExit(int $userId, ?string $before = null): ?array
    {
        return $this->findPendingExits($userId, $before)[0] ?? null;
    }

    public function getToday(?int $adminId = null): array
    {
        $builder = $this->where('date', date('Y-m-d'));
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->orderBy('time_in', 'DESC')->findAll();
    }

    public function getByDate(string $date, ?int $adminId = null): array
    {
        $builder = $this->where('date', $date);
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->orderBy('time_in', 'DESC')->findAll();
    }

    public function getByRange(string $from, string $to, ?int $adminId = null): array
    {
        $builder = $this->where('date >=', $from)->where('date <=', $to);
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->orderBy('date', 'DESC')->orderBy('time_in', 'DESC')->findAll();
    }

    public function getAll(?int $adminId = null): array
    {
        $builder = $this->orderBy('id', 'DESC');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->findAll();
    }

    public function countByStatus(string $date, string $status, ?int $adminId = null): int
    {
        $builder = $this->where('date', $date)->where('status', $status);
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->countAllResults();
    }

    public function countTodayByStatus(string $status, ?int $adminId = null): int
    {
        return $this->countByStatus(date('Y-m-d'), $status, $adminId);
    }

    public function countTodayTotal(?int $adminId = null): int
    {
        $builder = $this->where('date', date('Y-m-d'));
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->countAllResults();
    }
}