<?php
namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'object';

    protected $beforeInsert = ['hashPasswordIfNeeded'];
    protected $beforeUpdate = ['hashPasswordIfNeeded'];

    protected $allowedFields = [
        'username',
        'password',
        'estado',
        'id_rol',
        'id_persona',
    ];

    public function isPasswordHash(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        return password_get_info($value)['algo'] !== null;
    }

    protected function hashPasswordIfNeeded(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $password = $data['data']['password'];

        if ($password === null || $password === '') {
            unset($data['data']['password']);
            return $data;
        }

        if (! is_string($password)) {
            return $data;
        }

        if ($this->isPasswordHash($password)) {
            return $data;
        }

        $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);

        return $data;
    }

    // ══════════════════════════════════════════════════════════════
    // OBTENER POR ID — para ver/editar un usuario
    // ══════════════════════════════════════════════════════════════
    public function obtenerPorId(int $id): object|null
    {
        return $this->db->table('usuarios u')
            ->select('
                u.id_usuario,
                u.username,
                u.estado,
                u.id_rol,
                u.id_persona,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.dni,
                p.correo,
                p.telefono,
                p.imagen,
                r.nombre AS rol_nombre
            ')
            ->join('personas p', 'u.id_persona = p.id_persona', 'left')
            ->join('roles r', 'u.id_rol     = r.id_rol', 'left')
            ->where('u.id_usuario', $id)
            ->get()->getRow();
    }

    /**
     * Mantiene compatibilidad con controladores admin que todavía usan
     * el contrato legacy getUsuarioCompleto().
     */
    public function getUsuarioCompleto(int $id): object|null
    {
        return $this->obtenerPorId($id);
    }

    public function actualizarCredencialesYRol(int $usuarioId, array $usuarioData): bool
    {
        return $this->update($usuarioId, $usuarioData);
    }

    public function actualizarEstadoPorId(int $usuarioId, int $estado): bool
    {
        return $this->update($usuarioId, ['estado' => $estado]);
    }

    // ══════════════════════════════════════════════════════════════
    // LISTAR TODOS — tabla de usuarios con datos personales y rol
    // ══════════════════════════════════════════════════════════════
    public function listarUsuarios(): array
    {
        return $this->db->table('usuarios u')
            ->select('
                u.id_usuario,
                u.username,
                u.estado,
                u.id_rol,
                u.id_persona,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.dni,
                p.correo,
                r.nombre AS rol_nombre
            ')
            ->join('personas p', 'u.id_persona = p.id_persona', 'left')
            ->join('roles r', 'u.id_rol     = r.id_rol', 'left')
            ->orderBy('u.id_usuario', 'DESC')
            ->get()->getResultObject();
    }

    // ══════════════════════════════════════════════════════════════
    // PARA LOGIN — busca por username con todos los datos necesarios
    // El LoginController usa: $user->id_usuario, $user->id_rol,
    //   $user->id_persona, $user->nombre, $user->apellido_paterno,
    //   $user->imagen, $user->rol_nombre, $user->password
    // ══════════════════════════════════════════════════════════════
    public function buscarParaLoginCompleto(string $username): object|null
    {
        return $this->db->table('usuarios u')
            ->select('
                u.id_usuario,
                u.username,
                u.password,
                u.estado,
                u.id_rol,
                u.id_persona,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.imagen,
                r.nombre AS rol_nombre
            ')
            ->join('personas p', 'u.id_persona = p.id_persona', 'left')
            ->join('roles r', 'u.id_rol     = r.id_rol', 'left')
            ->where('u.username', $username)
            ->where('u.estado', 1)
            ->get()->getRow();
    }
}
