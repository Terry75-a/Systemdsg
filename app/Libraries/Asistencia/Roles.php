<?php

namespace App\Libraries\Asistencia;

/**
 * Puente entre los roles de Asisten y la tabla `roles` del panel.
 *
 * Asisten maneja 4 perfiles (Dev, Admin, Empleado, Practicante). Cada uno
 * es un registro de la tabla `roles` y el usuario apunta a él con
 * `users.id_rol`. La columna de texto `users.role` se sigue llenando para
 * que el código existente funcione igual.
 */
class Roles
{
    /** Rol de Asisten → `roles.nombre` */
    public const MAPA = [
        'Dev'         => 'dev',
        'Admin'       => 'admin',
        'Empleado'    => 'empleado',
        'Practicante' => 'practicante',
    ];

    private static ?array $cache = null;

    /** [id_rol => nombre] de la tabla roles (una consulta por petición). */
    private static function tabla(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (db_connect()->table('roles')->select('id_rol, nombre')->get()->getResultArray() as $r) {
                    self::$cache[(int) $r['id_rol']] = mb_strtolower(trim((string) $r['nombre']));
                }
            } catch (\Throwable $e) {
                log_message('error', 'Roles: no se pudo leer la tabla roles: ' . $e->getMessage());
            }
        }
        return self::$cache;
    }

    /** id_rol que corresponde a un rol de Asisten, o null si no existe en la tabla. */
    public static function idDe(?string $role): ?int
    {
        $nombre = self::MAPA[(string) $role] ?? null;
        if ($nombre === null) return null;
        $id = array_search($nombre, self::tabla(), true);
        return $id === false ? null : (int) $id;
    }

    /**
     * Rol de Asisten de un usuario. Manda `id_rol` (la tabla roles); si no
     * tiene o no es un rol de Asisten, se usa la columna `role`.
     */
    public static function deUsuario(array $u): string
    {
        $id = isset($u['id_rol']) ? (int) $u['id_rol'] : 0;
        if ($id > 0) {
            $nombre = self::tabla()[$id] ?? null;
            $role   = $nombre !== null ? array_search($nombre, self::MAPA, true) : false;
            if ($role !== false) return $role;
        }
        return (string) ($u['role'] ?? 'Empleado');
    }
}
