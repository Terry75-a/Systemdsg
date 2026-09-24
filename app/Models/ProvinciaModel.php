<?php 
namespace App\Models;

use CodeIgniter\Model;

class ProvinciaModel extends Model
{
    protected $table = 'ubigeo_peru_provinces';
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['id', 'name' , 'department_id'];
    public $timestamps = false;
}