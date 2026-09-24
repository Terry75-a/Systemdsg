<?php
namespace App\Models;

use CodeIgniter\Model;

class IncidentModel extends Model
{
    protected $table            = 'incidents';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'att_id', 'user_id', 'name', 'tipo', 'fecha',
        'detalle', 'estado', 'justificacion',
    ];

    public function findByAttId(int $attId): ?array
    {
        return $this->where('att_id', $attId)->first();
    }

    public function getAll(?int $adminId = null): array
    {
        $builder = $this->orderBy('id', 'DESC');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->findAll();
    }

    public function getPending(?int $adminId = null): array
    {
        $builder = $this->where('estado', 'Pendiente');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->orderBy('id', 'DESC')->findAll();
    }

    public function countPending(?int $adminId = null): int
    {
        $builder = $this->where('estado', 'Pendiente');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        return $builder->countAllResults();
    }
}