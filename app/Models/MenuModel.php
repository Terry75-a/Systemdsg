<?php
namespace App\Models;
use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['nombre', 'link'];
}
