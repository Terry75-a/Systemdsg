<?php 

namespace App\Models;

use CodeIgniter\Model;

class TipoPlanModel extends Model{

      protected $table = 'tipo_plan';
      protected $primaryKey = 'id_tipo_plan';
      protected $returnType = 'object';

      protected $allowedFields = ['nombre_tipo'];

}