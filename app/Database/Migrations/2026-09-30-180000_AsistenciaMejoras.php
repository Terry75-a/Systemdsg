<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Mejoras del módulo de asistencia:
 *  - attendance_log : auditoría de ediciones (quién cambió qué)
 *  - festivos       : calendario de días no laborables
 *  - attendance.lat / lng / ip : geolocalización de la marcación
 *  - incidents.resuelta_por / resuelta_en : flujo de aprobación
 *  - índice único (user_id, date) para evitar marcaciones dobles
 */
class AsistenciaMejoras extends Migration
{
    public function up(): void
    {
        $this->crearAttendanceLog();
        $this->crearFestivos();
        $this->completarAttendance();
        $this->completarIncidents();
        $this->indiceUnicoMarcacion();
    }

    public function down(): void
    {
        $this->borrarIndice('attendance', 'uq_attendance_user_date');

        if ($this->db->tableExists('attendance')) {
            foreach (['lat', 'lng', 'ip'] as $col) {
                if ($this->db->fieldExists($col, 'attendance')) {
                    $this->forge->dropColumn('attendance', $col);
                }
            }
        }

        if ($this->db->tableExists('incidents')) {
            foreach (['resuelta_por', 'resuelta_en'] as $col) {
                if ($this->db->fieldExists($col, 'incidents')) {
                    $this->forge->dropColumn('incidents', $col);
                }
            }
        }

        if ($this->db->tableExists('festivos')) {
            $this->forge->dropTable('festivos', true);
        }
        if ($this->db->tableExists('attendance_log')) {
            $this->forge->dropTable('attendance_log', true);
        }
    }

    private function crearAttendanceLog(): void
    {
        if ($this->db->tableExists('attendance_log')) {
            return;
        }

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'attendance_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'admin_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'accion'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'campo'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'valor_anterior'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'valor_nuevo'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'motivo'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'autor'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('attendance_id', false, false, 'idx_attendance');
        $this->forge->addKey('admin_id', false, false, 'idx_admin');
        $this->forge->createTable('attendance_log', true);
    }

    private function crearFestivos(): void
    {
        if ($this->db->tableExists('festivos')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'fecha'      => ['type' => 'DATE'],
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 120],
            'admin_id'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('fecha', false, false, 'idx_fecha');
        $this->forge->createTable('festivos', true);
    }

    private function completarAttendance(): void
    {
        if (! $this->db->tableExists('attendance')) {
            return;
        }

        $cols = [
            'lat' => ['type' => 'DECIMAL', 'constraint' => [10, 7], 'null' => true],
            'lng' => ['type' => 'DECIMAL', 'constraint' => [10, 7], 'null' => true],
            'ip'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
        ];

        foreach ($cols as $col => $def) {
            if (! $this->db->fieldExists($col, 'attendance')) {
                $this->forge->addColumn('attendance', [$col => $def]);
            }
        }
    }

    private function completarIncidents(): void
    {
        if (! $this->db->tableExists('incidents')) {
            return;
        }

        $cols = [
            'resuelta_por' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'resuelta_en'  => ['type' => 'DATETIME', 'null' => true],
        ];

        foreach ($cols as $col => $def) {
            if (! $this->db->fieldExists($col, 'incidents')) {
                $this->forge->addColumn('incidents', [$col => $def]);
            }
        }
    }

    /**
     * Una marcación por persona y día. Se aplica sólo si no hay duplicados
     * previos; si los hay se deja para saneo manual (asistencia:saneo).
     */
    private function indiceUnicoMarcacion(): void
    {
        if (! $this->db->tableExists('attendance')) {
            return;
        }
        if ($this->indiceExiste('attendance', 'uq_attendance_user_date')) {
            return;
        }

        $duplicadas = $this->db->query(
            'SELECT COUNT(*) AS total FROM (
                SELECT user_id, date FROM attendance
                 GROUP BY user_id, date HAVING COUNT(*) > 1
             ) dup'
        )->getRow()->total;

        if ((int) $duplicadas > 0) {
            log_message('warning', 'AsistenciaMejoras: hay {n} fechas con marcaciones duplicadas; no se creó el índice único.', ['n' => $duplicadas]);
            return;
        }

        $this->db->query('ALTER TABLE `attendance` ADD UNIQUE KEY `uq_attendance_user_date` (`user_id`, `date`)');
    }

    private function borrarIndice(string $table, string $name): void
    {
        if ($this->db->tableExists($table) && $this->indiceExiste($table, $name)) {
            $this->db->query('ALTER TABLE `' . $table . '` DROP INDEX `' . $name . '`');
        }
    }

    private function indiceExiste(string $table, string $name): bool
    {
        $sql = 'SELECT COUNT(*) AS total FROM information_schema.statistics
                WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?';

        return (int) $this->db->query($sql, [$table, $name])->getRow()->total > 0;
    }
}
