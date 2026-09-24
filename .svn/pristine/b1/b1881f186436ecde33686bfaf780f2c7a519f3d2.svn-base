<?php 

namespace App\Controllers;


use App\Models\DepartamentoModel;
use App\Models\DistritoModel;
use App\Models\ProvinciaModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;
use CodeIgniter\RESTful\ResourceController;

class UbigeoController extends ResourceController
{        
        use ResponseTrait;
        public function obtenerdepartamento()
        {
            $model = new DepartamentoModel();
            return $this->respond($model->findAll());


        }
        public function obtenerDistrito($province_id)
        {
             $model = new DistritoModel();
             return $this->respond(
            $model->where('province_id', $province_id)->findAll()
        );

        }
        
        

        public function obtenerProvincia($department_id)
        {
               $model = new ProvinciaModel();
               return $this->respond(
               $model->where('department_id', $department_id)->findAll()
        );
             
        }
}