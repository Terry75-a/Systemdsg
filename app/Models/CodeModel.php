<?php
namespace App\Models;

use CodeIgniter\Model;

class CodeModel extends Model
{
    protected $table            = 'codes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'code', 'name', 'dni', 'admin_id', 'status', 'used_by',
    ];

    public function findActiveByCode(string $code): ?array
    {
        return $this->where('code', $code)->where('status', 'active')->first();
    }

    public function findByDniActive(string $dni): ?array
    {
        return $this->where('dni', $dni)->where('status', 'active')->first();
    }

    public function markUsed(int $codeId, int $userId): bool
    {
        return $this->update($codeId, ['status' => 'used', 'used_by' => $userId]);
    }

    public function getActive(?int $adminId = null): array
    {
        $builder = $this->where('status', 'active');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->orderBy('id', 'DESC')->findAll();
    }

    public function getAll(?int $adminId = null): array
    {
        $builder = $this->orderBy('id', 'DESC');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->findAll();
    }
}