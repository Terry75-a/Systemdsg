<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanModel extends Model
{
    protected $table = 'planes';
    protected $primaryKey = 'id_plan';
    protected $allowedFields = [
        'nombre_plan', 'descripcion', 'precio', 'fecha_de_inicio', 'fecha_de_vencimiento' ,'estado_pago'
        
    ];
    public $useTimestamps = false;
}