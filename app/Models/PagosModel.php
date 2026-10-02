<?php

namespace App\Models;

use CodeIgniter\Model;

class PagosModel extends Model{

   protected $table = "pagos";
   protected $primaryKey = 'id_pago';
   protected $returnType = 'object';
   protected $allowedFields = ['id_sucursal' , 'id_plan' , 'id_tipo_plan' , 'fecha_de_inicio' , 'fecha_de_vencimiento' , 'tipo_fin' , 'num_repeticiones' , 'fecha_fin' ,'precio' , 'observaciones' , 'estado' , 'created_at' , 'updated_at'];

}