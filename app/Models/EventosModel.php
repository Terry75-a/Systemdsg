<?php 

namespace App\Models;
use CodeIgniter\Model;

class EventosModel extends Model
{
    protected $table = 'calendario_eventos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'start', 'end', 'id_cliente', 'id_usuario', 'color', 'created_at', 'updated_at'];
    public $useTimestamps = false;
}
