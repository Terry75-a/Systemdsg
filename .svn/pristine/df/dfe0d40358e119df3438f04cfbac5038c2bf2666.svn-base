<?php
namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = [ 'id_persona','username', 'password',  'id_rol' ];

    // Obtener datos del usuario + persona
    public function getPerfil($idUsuario)
    {
        return $this->select('usuarios.*, personas.nombre')
                    ->join('personas', 'usuarios.id_persona = personas.id_persona')
                    ->where('usuarios.id_usuario', $idUsuario)
                    ->first();
    }
    public function getContraseña($idUsuario)
    {
        return $this->select('password')
                    ->where('id_usuario', $idUsuario)
                    ->first();
    }
}
