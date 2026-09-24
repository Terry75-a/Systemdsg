<?php

namespace App\Services\Usuarios;

use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use App\Models\PermisosModel;
use App\Services\Shared\BusinessException;
use App\Services\Shared\ServiceResult;

class UsuarioWriterService
{
    protected PersonaModel $personaModel;
    protected UsuarioModel $usuarioModel;
    protected PermisosModel $permisosModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->permisosModel = new PermisosModel();
    }

    public function registrarUsuario(array $personaData, array $usuarioData): ServiceResult
    {
        $dni = trim($personaData['dni'] ?? '');
        if ($dni !== '' && $this->personaModel->dniExiste($dni)) {
            throw new BusinessException('El DNI ya está registrado.', 422);
        }

        $username = trim($usuarioData['username'] ?? '');
        if ($username !== '' && $this->usuarioModel->where('username', $username)->first()) {
            throw new BusinessException('El nombre de usuario ya existe.', 422);
        }

        $personaId = $this->personaModel->insert($personaData, false);
        if (!$personaId) {
            throw new BusinessException('No se pudo crear la persona.', 500);
        }

        $usuarioData['id_persona'] = $personaId;
        $usuarioId = $this->usuarioModel->insert($usuarioData, false);
        if (!$usuarioId) {
            $this->personaModel->delete($personaId);
            throw new BusinessException('No se pudo crear el usuario.', 500);
        }

        return new ServiceResult(true, 'Usuario registrado correctamente.', 201, [
            'id_usuario' => $usuarioId,
            'id_persona' => $personaId,
        ]);
    }

    public function actualizarUsuario(int $usuarioId, array $personaData, array $usuarioData): ServiceResult
    {
        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario) {
            throw new BusinessException('Usuario no encontrado.', 404);
        }

        $idPersona = (int) $usuario->id_persona;

        $dni = trim($personaData['dni'] ?? '');
        if ($dni !== '' && $this->personaModel->dniExiste($dni, $idPersona)) {
            throw new BusinessException('El DNI ya está registrado por otra persona.', 422);
        }

        $username = trim($usuarioData['username'] ?? '');
        if ($username !== '' && $this->usuarioModel
            ->where('username', $username)
            ->where('id_usuario !=', $usuarioId)
            ->first()) {
            throw new BusinessException('El nombre de usuario ya existe.', 422);
        }

        $okPersona = $this->personaModel->update($idPersona, $personaData);

        $usuarioUpdate = [];
        if (isset($usuarioData['username'])) $usuarioUpdate['username'] = $usuarioData['username'];
        if (isset($usuarioData['password']) && $usuarioData['password'] !== '') $usuarioUpdate['password'] = $usuarioData['password'];
        if (isset($usuarioData['id_rol'])) $usuarioUpdate['id_rol'] = (int) $usuarioData['id_rol'];
        $okUsuario = $usuarioUpdate ? $this->usuarioModel->update($usuarioId, $usuarioUpdate) : true;

        if (!$okPersona || !$okUsuario) {
            throw new BusinessException('No se pudo actualizar el usuario.', 500);
        }

        return new ServiceResult(true, 'Usuario actualizado correctamente.', 200);
    }

    public function actualizarPermisosPorUsuario(int $usuarioId, array $permisos): ServiceResult
    {
        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario) {
            throw new BusinessException('Usuario no encontrado.', 404);
        }

        $idPersona = (int) $usuario->id_persona;
        $idRol = (int) $usuario->id_rol;

        foreach ($permisos as $permiso) {
            $menuId = (int) ($permiso['menu_id'] ?? 0);
            if ($menuId <= 0) continue;

            $flags = [
                'read'   => !empty($permiso['read']) ? 1 : 0,
                'insert' => !empty($permiso['insert']) ? 1 : 0,
                'update' => !empty($permiso['update']) ? 1 : 0,
                'delete' => !empty($permiso['delete']) ? 1 : 0,
            ];

            $this->permisosModel->guardarPorRolYMenu($idRol, $menuId, $flags);
        }

        return new ServiceResult(true, 'Permisos actualizados correctamente.', 200);
    }

    public function cambiarEstadoUsuario(int $usuarioId): ServiceResult
    {
        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario) {
            throw new BusinessException('Usuario no encontrado.', 404);
        }

        $nuevoEstado = (int) $usuario->estado === 1 ? 0 : 1;
        $ok = $this->usuarioModel->actualizarEstadoPorId($usuarioId, $nuevoEstado);

        if (!$ok) {
            throw new BusinessException('No se pudo cambiar el estado.', 500);
        }

        return new ServiceResult(true, 'Estado actualizado correctamente.', 200, [
            'estado' => $nuevoEstado,
        ]);
    }
}