<?php 

namespace App\Models;

use CodeIgniter\Model;

class empresaSucursalModel extends Model {
    protected $table = 'empresa_sucursales';
    protected $primaryKey = 'id_sucursal';
    protected $returnType = 'object';
    protected $allowedFields = [
        'id_empresa', 'ruc', 'codigo_cliente', 'nombre_sucursal', 'representante','tipo', 'direccion', 'telefono', 'correo', 'id_pais',
        'id_departamento', 'id_provincia', 'id_distrito', 'estado', 'created_at', 'updated_at'
    ];
}