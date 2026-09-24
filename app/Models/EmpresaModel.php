<?php 


namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model{

      protected $table = 'empresa';
      protected $primaryKey = 'id_empresa';
      protected $returnType = 'object';

      protected $allowedFields = [
        'ruc', 'razon_social', 'direccion', 'id_persona', 'id_pais', 
        'id_departamento', 'id_provincia', 'id_distrito', 'telefono', 
        'correo', 'codigo_cliente', 'id_plan', 'id_tipo_plan', 'estado',
        'fecha_de_inicio', 'fecha_de_vencimiento', 'precio', 'estado_pago', 'descripcion'
    ];


     public function ExisteRuc($ruc){
         return $this->where('ruc' , $ruc)-> first() !==null;
     }

}
