<?php
namespace App\Models;

use CodeIgniter\Model;

class FestivoModel extends Model
{
    protected $table            = 'festivos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = ['fecha', 'nombre', 'admin_id', 'created_at'];

    /** ¿La fecha es festivo (global o del admin)? */
    public function esFestivo(string $fecha, ?int $adminId = null): bool
    {
        $builder = $this->where('fecha', $fecha);
        if ($adminId !== null) {
            $builder->groupStart()->where('admin_id', $adminId)->orWhere('admin_id', null)->groupEnd();
        }
        return $builder->countAllResults() > 0;
    }

    /** Fechas festivas de un rango (para excluir de faltas). */
    public function fechasEnRango(string $desde, string $hasta, ?int $adminId = null): array
    {
        $builder = $this->where('fecha >=', $desde)->where('fecha <=', $hasta);
        if ($adminId !== null) {
            $builder->groupStart()->where('admin_id', $adminId)->orWhere('admin_id', null)->groupEnd();
        }
        return array_column($builder->orderBy('fecha', 'ASC')->findAll(), 'fecha');
    }

    public function getAll(?int $adminId = null): array
    {
        $builder = $this->orderBy('fecha', 'ASC');
        if ($adminId !== null) {
            $builder->groupStart()->where('admin_id', $adminId)->orWhere('admin_id', null)->groupEnd();
        }
        return $builder->findAll();
    }
}
