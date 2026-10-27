<?php
namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'admin_id', 'nombre', 'tipo', 'dias', 'hora_entrada', 'hora_salida',
        'tolerancia', 'estado',
    ];

    public function getActive(?int $adminId = null, ?string $tipo = null): array
    {
        $builder = $this->where('estado', 'Activo');
        if ($adminId !== null) {
            $builder->where('admin_id', $adminId);
        }
        if ($tipo !== null && $tipo !== '') {
            $builder->where('tipo', $tipo);
        }
        return $builder->orderBy('id', 'DESC')->findAll();
    }

    public function getByTipo(int $adminId, string $tipo): array
    {
        return $this->where('admin_id', $adminId)
            ->where('tipo', $tipo)
            ->orderBy('id', 'DESC')
            ->findAll();
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