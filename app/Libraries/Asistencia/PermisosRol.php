<?php

namespace App\Libraries\Asistencia;

/**
 * Permisos por rol de Asisten.
 *
 * Cada acción tiene:
 *  · aplica   roles que tienen una pantalla para hacerla (los demás no
 *             pueden ni aunque se marque, por eso no se pueden editar)
 *  · default  quién la tiene permitida si aún no hay fila en la tabla
 *  · rutas    URIs que la ejecutan; el filtro PermisoRol las bloquea si el
 *             rol no tiene el permiso. Un «*» final vale para todo lo que sigue.
 *
 * Los valores se guardan en `asisten_permisos` (id_rol + acción).
 */
class PermisosRol
{
    public const ROLES = ['Dev', 'Admin', 'Empleado', 'Practicante'];

    public const ACCIONES = [
        'crear_admin' => [
            'label'   => 'Crear y editar admins',
            'aplica'  => ['Dev'],
            'default' => ['Dev'],
            'rutas'   => ['dios/create-admin', 'dios/update-admin'],
        ],
        'crear_empleado' => [
            'label'   => 'Crear y editar personal',
            'aplica'  => ['Dev', 'Admin'],
            'default' => ['Dev', 'Admin'],
            'rutas'   => [
                'dios/register-employee', 'registro-empleado',
                'admin/create-employee', 'admin/update-employee', 'admin/reset-password', 'admin/biometria/*', 'admin/dni/*',
            ],
        ],
        'generar_codigo' => [
            'label'   => 'Generar códigos',
            'aplica'  => ['Dev', 'Admin'],
            'default' => ['Dev', 'Admin'],
            'rutas'   => ['dios/create-code', 'dios/delete-code', 'admin/create-code', 'admin/delete-code'],
        ],
        'ver_asistencia' => [
            'label'   => 'Ver asistencia',
            'nota'    => 'Empleado y practicante: solo la suya',
            'aplica'  => ['Dev', 'Admin', 'Empleado', 'Practicante'],
            'default' => ['Dev', 'Admin', 'Empleado', 'Practicante'],
            'rutas'   => [
                'dios/auditoria', 'dios/auditoria/*',
                'admin/asistencias', 'admin/reportes', 'admin/auditoria/*', 'admin/horas', 'admin/exportar/*',
                'mi-panel/asistencias',
            ],
        ],
        'editar_asistencia' => [
            'label'   => 'Editar asistencia',
            'aplica'  => ['Admin'],
            'default' => ['Admin'],
            'rutas'   => ['admin/update-attendance'],
        ],
        'marcar_asistencia' => [
            'label'   => 'Marcar asistencia',
            'aplica'  => ['Empleado', 'Practicante'],
            'default' => ['Empleado', 'Practicante'],
            'rutas'   => ['mi-panel/registrar'],
        ],
        'eliminar_usuarios' => [
            'label'   => 'Eliminar usuarios',
            'aplica'  => ['Dev', 'Admin'],
            'default' => ['Dev', 'Admin'],
            'rutas'   => ['dios/delete-admin', 'dios/delete-user', 'admin/delete-user'],
        ],
    ];

    /** Panel (primer segmento de la URI) que usa cada rol. */
    private const PANEL = [
        'Dev' => ['dios', 'registro-empleado'],
        'Admin' => ['admin'],
        'Empleado' => ['mi-panel'],
        'Practicante' => ['mi-panel'],
    ];

    private static ?array $cache = null;

    /** [id_rol][accion] => bool, leído una vez por petición. */
    private static function tabla(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                $rows = db_connect()->table('asisten_permisos')->get()->getResultArray();
                foreach ($rows as $r) {
                    self::$cache[(int) $r['id_rol']][$r['accion']] = (bool) $r['permitido'];
                }
            } catch (\Throwable $e) {
                log_message('error', 'PermisosRol: ' . $e->getMessage());
            }
        }
        return self::$cache;
    }

    public static function limpiarCache(): void
    {
        self::$cache = null;
    }

    public static function aplica(string $role, string $accion): bool
    {
        return in_array($role, self::ACCIONES[$accion]['aplica'] ?? [], true);
    }

    /** ¿Puede este rol hacer esta acción? */
    public static function puede(string $role, string $accion, ?int $idRol = null): bool
    {
        if (! isset(self::ACCIONES[$accion]) || ! self::aplica($role, $accion)) {
            return false;
        }
        $idRol ??= Roles::idDe($role);
        $tabla = self::tabla();
        if ($idRol !== null && isset($tabla[$idRol][$accion])) {
            return $tabla[$idRol][$accion];
        }
        return in_array($role, self::ACCIONES[$accion]['default'], true);
    }

    /** Matriz [accion][rol] => bool para la pantalla de edición. */
    public static function matriz(): array
    {
        $out = [];
        foreach (self::ACCIONES as $accion => $def) {
            foreach (self::ROLES as $role) {
                $out[$accion][$role] = self::aplica($role, $accion) ? self::puede($role, $accion) : null;
            }
        }
        return $out;
    }

    /** Acción que ejecuta una URI dentro del panel de ese rol, o null. */
    public static function accionDeRuta(string $role, string $uri): ?string
    {
        $uri = trim($uri, '/');
        $panel = explode('/', $uri)[0];
        if (! in_array($panel, self::PANEL[$role] ?? [], true)) {
            return null; // otro panel: lo resuelven los filtros de cada panel
        }
        foreach (self::ACCIONES as $accion => $def) {
            foreach ($def['rutas'] as $ruta) {
                $ok = str_ends_with($ruta, '/*')
                    ? str_starts_with($uri, substr($ruta, 0, -1))
                    : $uri === $ruta;
                if ($ok) return $accion;
            }
        }
        return null;
    }

    /**
     * Guarda la matriz enviada desde el panel Dev.
     * $marcados = [accion => [rol => '1', ...], ...]; lo no marcado queda en 0.
     */
    public static function guardar(array $marcados, int $autorId): int
    {
        $db = db_connect();
        $ahora = date('Y-m-d H:i:s');
        $n = 0;
        foreach (self::ACCIONES as $accion => $def) {
            foreach ($def['aplica'] as $role) {
                $idRol = Roles::idDe($role);
                if ($idRol === null) continue;
                $valor = ! empty($marcados[$accion][$role]) ? 1 : 0;
                $db->query(
                    'INSERT INTO asisten_permisos (id_rol, accion, permitido, updated_by, updated_at) VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE permitido = VALUES(permitido), updated_by = VALUES(updated_by), updated_at = VALUES(updated_at)',
                    [$idRol, $accion, $valor, $autorId, $ahora]
                );
                $n++;
            }
        }
        self::limpiarCache();
        return $n;
    }
}
