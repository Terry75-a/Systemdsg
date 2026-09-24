<?php

namespace App\Services\Permisos;

use App\Models\PermisosModel;
use App\Services\Shared\BusinessException;
use App\Services\Shared\ServiceResult;

class PermisoWriterService
{
    protected PermisosModel $model;

    public function __construct()
    {
        $this->model = new PermisosModel();
    }

    public function guardarPorRolYMenu(int $rolId, int $menuId, array $permissionFlags): ServiceResult
    {
        if ($rolId <= 0) {
            throw new BusinessException('ID de rol inválido.', 422);
        }
        if ($menuId <= 0) {
            throw new BusinessException('ID de módulo inválido.', 422);
        }

        $flags = [
            'read'   => !empty($permissionFlags['read']) ? 1 : 0,
            'insert' => !empty($permissionFlags['insert']) ? 1 : 0,
            'update' => !empty($permissionFlags['update']) ? 1 : 0,
            'delete' => !empty($permissionFlags['delete']) ? 1 : 0,
        ];

        $ok = $this->model->guardarPorRolYMenu($rolId, $menuId, $flags);

        if (!$ok) {
            throw new BusinessException('No se pudo guardar el permiso.', 500);
        }

        return new ServiceResult(true, 'Permiso guardado correctamente.', 200);
    }

    public function editarPorId(int $idPermiso, array $permissionFlags): ServiceResult
    {
        if ($idPermiso <= 0) {
            throw new BusinessException('ID de permiso inválido.', 422);
        }

        if (!$this->model->existePorId($idPermiso)) {
            throw new BusinessException('Permiso no encontrado.', 404);
        }

        $flags = [
            'read'   => !empty($permissionFlags['read']) ? 1 : 0,
            'insert' => !empty($permissionFlags['insert']) ? 1 : 0,
            'update' => !empty($permissionFlags['update']) ? 1 : 0,
            'delete' => !empty($permissionFlags['delete']) ? 1 : 0,
        ];

        $ok = $this->model->actualizarPermisosPorId($idPermiso, $flags);

        if (!$ok) {
            throw new BusinessException('No se pudo actualizar el permiso.', 500);
        }

        return new ServiceResult(true, 'Permiso actualizado correctamente.', 200);
    }

    public function eliminarPorId(int $idPermiso): ServiceResult
    {
        if ($idPermiso <= 0) {
            throw new BusinessException('ID de permiso inválido.', 422);
        }

        if (!$this->model->existePorId($idPermiso)) {
            throw new BusinessException('Permiso no encontrado.', 404);
        }

        $ok = $this->model->eliminarPorId($idPermiso);

        if (!$ok) {
            throw new BusinessException('No se pudo eliminar el permiso.', 500);
        }

        return new ServiceResult(true, 'Permiso eliminado correctamente.', 200);
    }
}