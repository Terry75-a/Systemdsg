<?php
namespace App\Models;
use CodeIgniter\Model;

class ModuloModel extends Model
{
    protected $table = 'modulos'; // Assuming the table name is modulos
    protected $primaryKey = 'id_modulo';
    protected $allowedFields = ['nombre', 'descripcion', 'estado'];
}
