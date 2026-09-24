<?php
namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{ 

    protected $table = 'personas';  
    protected $primaryKey = 'id_persona';
   
    // Total de clientes/personas registradas
    public function totalClientes()
    {
        return $this->countAllResults();
    }

    // Personas registradas en el mes actual
    public function nuevosClientesEsteMes()
    {
        $startDate = date('Y-m-01 00:00:00');
        return $this->where('created_at >=', $startDate)->countAllResults();
    }

    // Planes activos
    public function totalPlanes()
    {
        $db = \Config\Database::connect();
        return $db->table('planes')->countAllResults();
    }

    // Tipos de plan
    public function totalTipoPlan()
    {
        $db = \Config\Database::connect();
        return $db->table('tipo_plan')->countAllResults();
    }

    // Usuarios del sistema
    public function totalUsuarios()
    {
        $db = \Config\Database::connect();
        return $db->table('usuarios')->countAllResults();
    }
}