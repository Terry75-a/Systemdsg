<?php

namespace App\Libraries\Asistencia;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

/**
 * Copia de los DATOS de Asisten entre dos máquinas (la estructura la ponen
 * las migraciones, que se aplican solas: ver AutoMigracion).
 *
 *   exportar → un .sql con una sentencia por línea:
 *              borra y vuelve a llenar cada tabla de Asisten con lo de esta base.
 *   importar → aplica migraciones pendientes y luego ese .sql.
 *
 * Sólo toca tablas de Asisten; las del panel de clientes (usuarios, pagos,
 * clientes, menus…) no se tocan. Los ids de `roles` pueden ser distintos en
 * cada base, por eso users.id_rol y asisten_permisos.id_rol se recalculan por
 * el NOMBRE del rol.
 */
class SyncDatos
{
    /** Orden de carga (padres antes que hijos). */
    public const TABLAS = [
        'users', 'admin_config', 'schedules', 'user_schedules', 'codes', 'festivos',
        'attendance', 'attendance_log', 'incidents', 'notifications', 'asisten_permisos',
        'webauthn_credentials', 'calendario_eventos',
    ];

    /** Roles de Asisten que deben existir en `roles`. */
    private const ROLES = [
        'admin'       => 'Administrador de empresa (Asisten)',
        'dev'         => 'Superadmin del sistema (Asisten)',
        'empleado'    => 'Personal de planilla (Asisten)',
        'practicante' => 'Practicante (Asisten)',
    ];

    private const LOTE = 200;

    public function __construct(private BaseConnection $db)
    {
    }

    /** @return array<string,int> filas exportadas por tabla */
    public function exportar(string $archivo): array
    {
        $dir = dirname($archivo);
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new RuntimeException("No se pudo crear la carpeta $dir");
        }
        $fh = fopen($archivo, 'wb');
        if ($fh === false) {
            throw new RuntimeException("No se pudo escribir $archivo");
        }

        $l = fn (string $sql) => fwrite($fh, $sql . "\n");
        $l('-- Datos de Asisten · ' . date('Y-m-d H:i:s') . ' · base: ' . $this->db->getDatabase());
        $l('-- Cargar con:  php spark asisten:importar <este archivo>');
        $l('-- (o: mysql -uroot <base> < este archivo, con las tablas ya migradas)');
        $l('SET NAMES utf8mb4;');
        $l('SET FOREIGN_KEY_CHECKS = 0;');
        foreach (self::ROLES as $nombre => $desc) {
            $l('INSERT IGNORE INTO `roles` (`nombre`, `descripcion`) VALUES (' . $this->db->escape($nombre) . ', ' . $this->db->escape($desc) . ');');
        }

        // Nombre de cada id_rol de ESTA base (para recalcularlo allá)
        $nombreRol = [];
        if ($this->db->tableExists('roles')) {
            foreach ($this->db->table('roles')->select('id_rol, nombre')->get()->getResultArray() as $r) {
                $nombreRol[(int) $r['id_rol']] = $r['nombre'];
            }
        }

        $conteo = [];
        foreach (self::TABLAS as $tabla) {
            if (! $this->db->tableExists($tabla)) {
                continue;
            }
            $l('');
            $l("-- $tabla");
            $l("DELETE FROM `$tabla`;");
            $campos = $this->db->getFieldNames($tabla);
            $cols = '(`' . implode('`, `', $campos) . '`)';
            $n = 0;

            if ($tabla === 'asisten_permisos') {
                // id_rol por nombre: INSERT … SELECT desde roles
                foreach ($this->db->table($tabla)->get()->getResultArray() as $r) {
                    $rol = $nombreRol[(int) $r['id_rol']] ?? null;
                    if ($rol === null) continue;
                    $otros = array_values(array_diff($campos, ['id', 'id_rol']));
                    $vals = array_map(fn ($c) => $this->valor($r[$c]), $otros);
                    $l('INSERT INTO `asisten_permisos` (`id_rol`, `' . implode('`, `', $otros) . '`) SELECT r.id_rol, ' . implode(', ', $vals)
                        . ' FROM `roles` r WHERE r.nombre = ' . $this->db->escape($rol) . ';');
                    $n++;
                }
                $conteo[$tabla] = $n;
                continue;
            }

            $offset = 0;
            while (true) {
                $filas = $this->db->table($tabla)->limit(self::LOTE, $offset)->get()->getResultArray();
                if (! $filas) break;
                $valores = [];
                foreach ($filas as $f) {
                    $valores[] = '(' . implode(', ', array_map(fn ($c) => $this->valor($f[$c]), $campos)) . ')';
                }
                $l("INSERT INTO `$tabla` $cols VALUES " . implode(', ', $valores) . ';');
                $n += count($filas);
                $offset += self::LOTE;
            }
            $conteo[$tabla] = $n;
        }

        $l('');
        $l('-- id_rol de cada usuario según el nombre de su rol en ESTA base');
        $l('UPDATE `users` u JOIN `roles` r ON r.nombre = LOWER(u.role) SET u.id_rol = r.id_rol;');
        $l('SET FOREIGN_KEY_CHECKS = 1;');
        fclose($fh);

        return $conteo;
    }

    /** Tablas que venían en el archivo pero no existen en esta base (se saltaron). */
    public array $saltadas = [];

    /** Ejecuta un .sql generado por exportar() (una sentencia por línea). */
    public function importar(string $archivo): int
    {
        $existe = [];
        $fh = fopen($archivo, 'rb');
        if ($fh === false) {
            throw new RuntimeException("No se pudo leer $archivo");
        }
        $n = 0;
        $this->db->transStart();
        try {
            while (($linea = fgets($fh)) !== false) {
                $sql = trim($linea);
                if ($sql === '' || str_starts_with($sql, '--')) continue;
                // Una tabla que esta base no tiene (p. ej. sin migración) se salta
                if (preg_match('/^(?:DELETE FROM|INSERT INTO) `([a-z0-9_]+)`/i', $sql, $m)) {
                    $existe[$m[1]] ??= $this->db->tableExists($m[1]);
                    if (! $existe[$m[1]]) {
                        $this->saltadas[$m[1]] = true;
                        continue;
                    }
                }
                $this->db->query(rtrim($sql, ';'));
                $n++;
            }
        } finally {
            fclose($fh);
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new RuntimeException('La carga falló y se deshizo. Revisa writable/logs.');
        }
        return $n;
    }

    /** Valor SQL en una sola línea (escape() convierte saltos de línea en \n). */
    private function valor($v): string
    {
        if ($v === null) return 'NULL';
        return (string) $this->db->escape($v);
    }
}
