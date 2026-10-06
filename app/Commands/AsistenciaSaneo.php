<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use DateTimeImmutable;

/**
 * Revisa la salud de los datos del módulo de asistencia.
 *
 * Por defecto sólo reporta; con --apply corrige lo seguro
 * (backfills y borrado de huérfanos).
 */
class AsistenciaSaneo extends BaseCommand
{
    protected $group       = 'Asistencia';
    protected $name        = 'asistencia:saneo';
    protected $description = 'Detecta (y con --apply corrige) datos inconsistentes de asistencia.';
    protected $usage       = 'asistencia:saneo [--apply] [--desde=YYYY-MM-DD] [--hasta=YYYY-MM-DD]';
    protected $options     = [
        '--apply'  => 'Ejecuta las correcciones (sin él sólo reporta)',
        '--desde'  => 'Límite inferior de fechas a revisar',
        '--hasta'  => 'Límite superior de fechas a revisar',
    ];

    private array $problemas = [];
    private int   $corregidos = 0;

    public function run(array $params)
    {
        $params     = self::normalizarParams($params);
        $apply      = array_key_exists('apply', $params);
        $this->aplicar = $apply;
        $desde  = $this->fecha($params['desde'] ?? null, date('Y-m-d', strtotime('-6 months')));
        $hasta  = $this->fecha($params['hasta'] ?? null, date('Y-m-d'));

        if ($desde === null || $hasta === null) {
            CLI::error('Fechas inválidas (usa --desde=YYYY-MM-DD --hasta=YYYY-MM-DD).');
            return 1;
        }

        $db = \Config\Database::connect();

        $this->revisarDuplicados($db);
        $this->revisarHuerfanos($db, 'attendance', 'users');
        $this->revisarHuerfanos($db, 'incidents', 'users');
        $this->revisarIncidenciasHuerfanas($db);
        $this->revisarAdminId($db, 'attendance', 'users');
        $this->revisarAdminId($db, 'incidents', 'users');
        $this->revisarMarcacionesRotas($db, $desde, $hasta);
        $this->revisarConfigSinAdmin($db);
        $this->revisarCodigosAdmin($db);
        $this->revisarFestivosDuplicados($db);

        if ($this->problemas === []) {
            CLI::write("Sin problemas en {$desde} → {$hasta}.", 'green');
            return 0;
        }

        foreach ($this->problemas as $linea) {
            CLI::write('  ' . $linea);
        }
        CLI::newLine();
        CLI::write(sprintf('%d problema(s) detectado(s).', count($this->problemas)), 'yellow');

        if (! $apply) {
            CLI::write('Modo reporte. Repite con --apply para corregir los marcados con [fix].', 'cyan');
            return 0;
        }

        if ($this->corregidos > 0) {
            CLI::write("Corregidos: {$this->corregidos}.", 'green');
        }

        return 0;
    }

    // ─────────────────────────────────────────────
    private function revisarDuplicados($db): void
    {
        $sql = 'SELECT user_id, date, COUNT(*) c FROM attendance
                 GROUP BY user_id, date HAVING c > 1';
        foreach ($db->query($sql)->getResult() as $row) {
            $this->problemas[] = "[!] Marcaciones duplicadas user_id={$row->user_id} fecha={$row->date} ({$row->c}) — revisar a mano";
        }
    }

    /** Filas cuyo dueño ya no existe. */
    private function revisarHuerfanos($db, string $tabla, string $padre): void
    {
        $sql = "SELECT COUNT(*) c FROM {$tabla} t LEFT JOIN {$padre} p ON p.id = t.user_id WHERE p.id IS NULL";
        $c   = (int) $db->query($sql)->getRow()->c;
        if ($c === 0) {
            return;
        }

        $this->problemas[] = "[fix] {$c} fila(s) en {$tabla} sin usuario — se borran";
        if ($this->apply()) {
            $db->query("DELETE t FROM {$tabla} t LEFT JOIN {$padre} p ON p.id = t.user_id WHERE p.id IS NULL");
            $this->corregidos += $c;
        }
    }

    private function revisarIncidenciasHuerfanas($db): void
    {
        $sql = 'SELECT COUNT(*) c FROM incidents i
                 LEFT JOIN attendance a ON a.id = i.att_id
                WHERE i.att_id IS NOT NULL AND i.att_id > 0 AND a.id IS NULL';
        $c = (int) $db->query($sql)->getRow()->c;
        if ($c === 0) {
            return;
        }
        $this->problemas[] = "[fix] {$c} incidencia(s) apuntando a asistencia inexistente — se limpia att_id";
        if ($this->apply()) {
            $db->query('UPDATE incidents i LEFT JOIN attendance a ON a.id = i.att_id
                          SET i.att_id = 0 WHERE a.id IS NULL AND i.att_id > 0');
            $this->corregidos += $c;
        }
    }

    /** admin_id huérfano → se hereda del usuario dueño. */
    private function revisarAdminId($db, string $tabla, string $padre): void
    {
        $sql = "SELECT COUNT(*) c FROM {$tabla} t JOIN {$padre} p ON p.id = t.user_id
                 WHERE t.admin_id IS NULL AND p.admin_id IS NOT NULL";
        $c = (int) $db->query($sql)->getRow()->c;
        if ($c === 0) {
            return;
        }
        $this->problemas[] = "[fix] {$c} fila(s) en {$tabla} sin admin_id — se hereda del usuario";
        if ($this->apply()) {
            $db->query("UPDATE {$tabla} t JOIN {$padre} p ON p.id = t.user_id
                          SET t.admin_id = p.admin_id WHERE t.admin_id IS NULL AND p.admin_id IS NOT NULL");
            $this->corregidos += $c;
        }
    }

    /** Filas rotas: entradas sin hora, o sin sentido. */
    private function revisarMarcacionesRotas($db, string $desde, string $hasta): void
    {
        $sql = 'SELECT COUNT(*) c FROM attendance
                WHERE date >= ? AND date <= ? AND time_in IS NULL AND time_out IS NULL AND status != "absent"';
        $c = (int) $db->query($sql, [$desde, $hasta])->getRow()->c;
        if ($c === 0) {
            return;
        }
        $this->problemas[] = "[fix] {$c} marcación(es) sin ninguna hora y que no son falta — se marcan absent";
        if ($this->apply()) {
            $db->query('UPDATE attendance SET status = "absent",
                        observacion = CONCAT(IFNULL(observacion,""), " [saneo: sin horas]")
                        WHERE date >= ? AND date <= ? AND time_in IS NULL AND time_out IS NULL AND status != "absent"',
                [$desde, $hasta]);
            $this->corregidos += $c;
        }
    }

    private function revisarConfigSinAdmin($db): void
    {
        $c = (int) $db->query('SELECT COUNT(*) c FROM admin_config WHERE admin_id IS NULL')->getRow()->c;
        if ($c === 0) {
            return;
        }
        $this->problemas[] = "[fix] {$c} configuración(es) sin admin_id — se asigna al primer Admin";
        if ($this->apply()) {
            $db->query('UPDATE admin_config c JOIN users u ON u.role = "Admin"
                          SET c.admin_id = u.id WHERE c.admin_id IS NULL ORDER BY u.id LIMIT 1');
            $this->corregidos += $c;
        }
    }

    private function revisarCodigosAdmin($db): void
    {
        $c = (int) $db->query('SELECT COUNT(*) c FROM users WHERE role = "Admin" AND (admin_code IS NULL OR admin_code = "")')
            ->getRow()->c;
        if ($c === 0) {
            return;
        }
        $this->problemas[] = "[fix] {$c} admin(s) sin admin_code — se genera ADMIN-###";
        if ($this->apply()) {
            $fila = $db->query('SELECT id FROM users WHERE role = "Admin" AND (admin_code IS NULL OR admin_code = "")
                                ORDER BY id')->getRow();
            if ($fila) {
                $db->query('UPDATE users SET admin_code = ? WHERE id = ?', ['ADMIN-' . str_pad((string) $fila->id, 3, '0', STR_PAD_LEFT), $fila->id]);
                $this->corregidos++;
            }
        }
    }

    private function revisarFestivosDuplicados($db): void
    {
        $sql = 'SELECT fecha, COUNT(*) c FROM festivos GROUP BY fecha HAVING c > 1';
        foreach ($db->query($sql)->getResult() as $row) {
            $this->problemas[] = "[fix] Festivo repetido {$row->fecha} ({$row->c}) — se deja el primero";
            if ($this->apply()) {
                $db->query('DELETE f1 FROM festivos f1 JOIN festivos f2 ON f1.fecha = f2.fecha AND f1.id > f2.id');
                $this->corregidos += (int) ($row->c - 1);
            }
        }
    }

    // ─────────────────────────────────────────────
    private bool $aplicar = false;

    private function apply(): bool
    {
        return $this->aplicar;
    }

    private function fecha($valor, string $default): ?string
    {
        if ($valor === null || $valor === '') {
            return $default;
        }
        $d = DateTimeImmutable::createFromFormat('Y-m-d', (string) $valor);
        return ($d !== false && $d->format('Y-m-d') === $valor) ? $valor : null;
    }

    private static function normalizarParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (! is_string($key) || ! str_contains($key, '=')) {
                continue;
            }
            [$name, $val] = explode('=', $key, 2);
            unset($params[$key]);
            $params[$name] = $val;
        }
        return $params;
    }
}
