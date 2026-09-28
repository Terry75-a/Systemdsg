<?php 

namespace App\Models;

use CodeIgniter\Model;

class empresaSucursalModel extends Model {
    protected $table = 'empresa_sucursales';
    protected $primaryKey = 'id_sucursal';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id_empresa', 
        'ruc', 
        'codigo_cliente', 
        'nombre_sucursal', 
        'representante',
        'tipo', 
        'direccion', 
        'telefono', 
        'correo', 
        'id_pais',
        'id_departamento', 
        'id_provincia', 
        'id_distrito', 
        'estado', 
        'id_plan', 
        'id_tipo_plan', 
        'fecha_de_inicio', 
        'fecha_de_vencimiento', 
        'precio', 
        'observaciones',
        'created_at', 
        'updated_at'
    ];
}