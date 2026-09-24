<?php 

namespace App\Models;
use CodeIgniter\Model;


class planesaddModel extends Model
{
    protected $table = 'tipo_plan';
    protected $primaryKey = 'id_tipo_plan';
    protected $allowedFields = [
        'nombre_tipo',
        
        
    ];
    public $useTimestamps = false;
}