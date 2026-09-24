<?php
namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table = 'empresa'; // Assuming company settings are in 'empresa' table
    protected $primaryKey = 'id_empresa';
    protected $allowedFields = ['nombre', 'ruc', 'direccion', 'telefono', 'correo', 'logo'];
}
