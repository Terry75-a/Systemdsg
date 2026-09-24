<?php
namespace App\Models;

use CodeIgniter\Model;

class PermisosModel extends Model
{
    protected $table      = 'permisos';
    protected $primaryKey = 'id_permiso';
    protected $returnType = 'object';

    protected $allowedFields = [
        'id_rol',
        'menu_id',
        'read',
        'insert',
        'update',
        'delete',
    ];

    private function normalizarPermisos(array $permisos): array
    {
        return [
            'read' => !empty($permisos['read']) ? 1 : 0,
            'insert' => !empty($permisos['insert']) ? 1 : 0,
            'update' => !empty($permisos['update']) ? 1 : 0,
            'delete' => !empty($permisos['delete']) ? 1 : 0,
        ];
    }

    private function obtenerDuplicadosRolMenu(int $id_rol, int $menu_id): array
    {
        return $this->where('id_rol', $id_rol)
            ->where('menu_id', $menu_id)
            ->orderBy('id_permiso', 'ASC')
            ->findAll();
    }

    private function limpiarDuplicadosRolMenu(array $permisos): void
    {
        if (count($permisos) <= 1) {
            return;
        }

        $idsDuplicados = array_map(
            static fn(object $permiso): int => (int) $permiso->id_permiso,
            array_slice($permisos, 1)
        );

        if ($idsDuplicados !== []) {
            $this->whereIn('id_permiso', $idsDuplicados)->delete();
        }
    }

    // ══════════════════════════════════════════════════════════════
    // GUARDAR O ACTUALIZAR — por id_rol + menu_id
    // El diseño actual espera una fila por combinación rol+módulo,
    // aunque existan datos legacy duplicados.
    // ══════════════════════════════════════════════════════════════
    public function guardarOActualizar(int $id_rol, int $menu_id, array $permisos): bool
    {
        $existentes = $this->obtenerDuplicadosRolMenu($id_rol, $menu_id);
        $existente = $existentes[0] ?? null;

        $data = [
            'id_rol'  => $id_rol,
            'menu_id' => $menu_id,
        ] + $this->normalizarPermisos($permisos);

        if ($existente) {
            $actualizado = $this->update($existente->id_permiso, $data);

            if ($actualizado) {
                $this->limpiarDuplicadosRolMenu($existentes);
            }

            return $actualizado;
        }

        return $this->insert($data) !== false;
    }

    public function guardarPorRolYMenu(int $rolId, int $menuId, array $permissionFlags): bool
    {
        return $this->guardarOActualizar($rolId, $menuId, $permissionFlags);
    }

    // ══════════════════════════════════════════════════════════════
    // OBTENER TODOS — JOIN con roles y menus
    // ══════════════════════════════════════════════════════════════
    public function obtenerTodos(): array
    {
        return $this->db->table('permisos p')
            ->select('
                p.id_permiso,
                p.id_rol,
                p.menu_id,
                p.read,
                p.insert,
                p.update,
                p.delete,
                r.nombre AS rol_nombre,
                m.nombre AS menu_nombre,
                m.link   AS menu_link
            ')
            ->join('roles r', 'p.id_rol  = r.id_rol', 'left')
            ->join('menus m', 'p.menu_id = m.id',     'left')
            ->orderBy('r.nombre', 'ASC')
            ->get()
            ->getResultObject();
    }

    // ══════════════════════════════════════════════════════════════
    // OBTENER POR ROL — todos los permisos de un rol específico
    // Usado en LoginController para cargar permisos en sesión
    // ══════════════════════════════════════════════════════════════
    public function obtenerPermisosPorRol(int $id_rol): array
    {
        return $this->db->table('permisos p')
            ->select('p.*, r.nombre AS rol_nombre, m.nombre AS menu_nombre, m.link')
            ->join('roles r', 'p.id_rol  = r.id_rol', 'left')
            ->join('menus m', 'p.menu_id = m.id',     'left')
            ->where('p.id_rol', $id_rol)
            ->get()
            ->getResultObject();
    }

    // ══════════════════════════════════════════════════════════════
    // OBTENER POR ID — un permiso específico para pre-llenar modal
    // ══════════════════════════════════════════════════════════════
    public function obtenerPorId(int $id_permiso): ?object
    {
        return $this->db->table('permisos p')
            ->select('
                p.id_permiso, p.id_rol, p.menu_id,
                p.read, p.insert, p.update, p.delete,
                r.nombre AS rol_nombre,
                m.nombre AS menu_nombre
            ')
            ->join('roles r', 'p.id_rol  = r.id_rol', 'left')
            ->join('menus m', 'p.menu_id = m.id',     'left')
            ->where('p.id_permiso', $id_permiso)
            ->get()
            ->getRowObject();
    }

    public function existePorId(int $permisoId): bool
    {
        return $this->find($permisoId) !== null;
    }

    public function actualizarPermisosPorId(int $permisoId, array $permissionFlags): bool
    {
        return $this->update($permisoId, $this->normalizarPermisos($permissionFlags));
    }

    // ══════════════════════════════════════════════════════════════
    // EDITAR — actualiza solo los 4 checks de un permiso
    // ══════════════════════════════════════════════════════════════
    public function editarPermisos(int $id_permiso, array $permisos): bool
    {
        if (!$this->existePorId($id_permiso)) {
            return false;
        }

        return $this->actualizarPermisosPorId($id_permiso, $permisos);
    }

    public function eliminarPorId(int $permisoId): bool
    {
        return $this->delete($permisoId);
    }

    public function eliminarPermiso(int $id_permiso): bool
    {
        if (!$this->existePorId($id_permiso)) {
            return false;
        }

        return $this->eliminarPorId($id_permiso);
    }

    public function obtenerPermisosUsuario(int $id_persona, ?int $id_rol = null): array
    {
        if ($id_rol === null) {
            $usuario = $this->db->table('usuarios')
                ->select('id_rol')
                ->where('id_persona', $id_persona)
                ->orderBy('id_usuario', 'DESC')
                ->get()
                ->getRowObject();

            $id_rol = $usuario?->id_rol !== null ? (int) $usuario->id_rol : null;
        }

        if (empty($id_rol)) {
            return [];
        }

        return $this->db->table('permisos p')
            ->select('p.id_permiso, p.id_rol, p.menu_id, p.read, p.insert, p.update, p.delete, m.nombre AS menu_nombre, m.link AS menu_link')
            ->join('menus m', 'p.menu_id = m.id', 'left')
            ->where('p.id_rol', $id_rol)
            ->orderBy('m.nombre', 'ASC')
            ->get()
            ->getResultObject();
    }

    public function asignarPermisos(int $id_persona, int $id_rol, int $menu_id, array $permisos): bool
    {
        unset($id_persona);

        if ($id_rol <= 0 || $menu_id <= 0) {
            return false;
        }

        return $this->guardarPorRolYMenu($id_rol, $menu_id, $permisos);
    }

    // ══════════════════════════════════════════════════════════════
    // VERIFICAR — ¿Puede este rol hacer esta acción en este menú?
    // Se llama desde Auth.php con el id_rol de la sesión
    // ══════════════════════════════════════════════════════════════
    public function verificarPermiso(int $id_rol, int $menu_id, string $accion): bool
    {
        $permiso = $this->where('id_rol',  $id_rol)
                        ->where('menu_id', $menu_id)
                        ->first();

        return $permiso && isset($permiso->$accion) && $permiso->$accion == 1;
    }

    // ══════════════════════════════════════════════════════════════
    // ELIMINAR POR ROL — borra todos los permisos de un rol
    // ══════════════════════════════════════════════════════════════
    public function eliminarPermisosPorRol(int $id_rol): bool
    {
        return $this->where('id_rol', $id_rol)->delete();
    }
}
