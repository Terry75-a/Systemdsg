<?php
namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = [ 'id_persona','username', 'password',  'id_rol' ];

    // Obtener datos del usuario + persona + rol
    public function getPerfil($idUsuario)
    {
        return $this->select('usuarios.id_usuario, usuarios.username, usuarios.id_rol, usuarios.id_persona,
                              usuarios.estado, usuarios.created_at,
                              personas.nombre, personas.apellido_paterno, personas.apellido_materno,
                              personas.dni, personas.correo, personas.telefono,
                              roles.nombre AS rol_nombre')
                    ->join('personas', 'usuarios.id_persona = personas.id_persona')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol', 'left')
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
