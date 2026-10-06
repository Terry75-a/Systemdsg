<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Esquema del módulo de asistencia DSG (users, attendance, schedules,
 * incidents, codes, admin_config).
 *
 * Es idempotente: crea las tablas que no existen y agrega las columnas que
 * falten en las que ya existen, para poder aplicarla tanto sobre una BD nueva
 * como sobre la histórica sin perder datos.
 */
class CreateAsistenciaSchema extends Migration
{
    /** Columnas que este módulo agrega por encima del dump histórico. */
    private array $aditivas = [
        'users'          => ['admin_code', 'personal_code', 'empresa', 'institucion', 'semestre', 'huella_registrada', 'rostro_registrado', 'huella_fecha', 'rostro_fecha', 'rostro_path', 'huella_cred_id', 'contract_type', 'contract_duration', 'contract_start', 'contract_end', 'firma_tipo', 'firma_datos', 'schedule_id'],
        'attendance'     => ['admin_id', 'evidencia'],
        'schedules'      => ['admin_id', 'tipo'],
        'incidents'      => ['admin_id'],
        'admin_config'   => ['admin_id', 'notif_email_dest'],
    ];

    public function up(): void
    {
        $this->crearUsers();
        $this->crearAttendance();
        $this->crearSchedules();
        $this->crearIncidents();
        $this->crearCodes();
        $this->crearAdminConfig();

        $this->completarColumnas();
        $this->crearIndices();
    }

    public function down(): void
    {
        foreach ($this->aditivas as $table => $columns) {
            if (! $this->db->tableExists($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if ($this->db->fieldExists($column, $table)) {
                    $this->forge->dropColumn($table, $column);
                }
            }
        }

        $this->borrarIndice('attendance', 'idx_admin_date');
        $this->borrarIndice('incidents', 'idx_admin');
        $this->borrarIndice('schedules', 'idx_admin');
        $this->borrarIndice('users', 'idx_admin');
    }

    // ══════════════════════════════════════════════════════════
    // CREACIÓN (sólo si la tabla no existe)
    // ══════════════════════════════════════════════════════════

    private function crearUsers(): void
    {
        if ($this->db->tableExists('users')) {
            return;
        }

        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name'               => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'              => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'dni'                => ['type' => 'VARCHAR', 'constraint' => 8, 'null' => true],
            'password'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'               => ['type' => 'ENUM', 'constraint' => ['Dev', 'Admin', 'Empleado', 'Practicante', 'Usuario'], 'default' => 'Empleado'],
            'area'               => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cargo'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'admin_id'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_by'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'admin_code'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'personal_code'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'empresa'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'institucion'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'semestre'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'huella_registrada'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'rostro_registrado'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'huella_fecha'       => ['type' => 'DATETIME', 'null' => true],
            'rostro_fecha'       => ['type' => 'DATETIME', 'null' => true],
            'rostro_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'huella_cred_id'     => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'contract_type'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Indefinido'],
            'contract_duration'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'contract_start'     => ['type' => 'DATE', 'null' => true],
            'contract_end'       => ['type' => 'DATE', 'null' => true],
            'firma_tipo'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'firma_datos'        => ['type' => 'TEXT', 'null' => true],
            'schedule_id'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'estado'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Activo'],
            'created'            => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'last_login'         => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('dni');
        $this->forge->createTable('users', true);
    }

    private function crearAttendance(): void
    {
        if ($this->db->tableExists('attendance')) {
            return;
        }

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'admin_id'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'dni'         => ['type' => 'VARCHAR', 'constraint' => 8, 'null' => true],
            'date'        => ['type' => 'DATE'],
            'time_in'     => ['type' => 'TIME', 'null' => true],
            'time_out'    => ['type' => 'TIME', 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['present', 'late', 'absent', 'no_exit'], 'default' => 'present', 'null' => true],
            'observacion' => ['type' => 'TEXT', 'null' => true],
            'evidencia'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'date'], false, false, 'idx_user_date');
        $this->forge->addKey('date', false, false, 'idx_date');
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->addKey(['admin_id', 'date'], false, false, 'idx_admin_date');
        $this->forge->createTable('attendance', true);
    }

    private function crearSchedules(): void
    {
        if ($this->db->tableExists('schedules')) {
            return;
        }

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'admin_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'tipo'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Empleado'],
            'nombre'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'dias'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => 'Lun - Vie'],
            'hora_entrada'  => ['type' => 'TIME', 'null' => true, 'default' => '08:00:00'],
            'hora_salida'   => ['type' => 'TIME', 'null' => true, 'default' => '17:00:00'],
            'tolerancia'    => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 10],
            'estado'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'default' => 'Activo'],
            'created'       => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('schedules', true);
    }

    private function crearIncidents(): void
    {
        if ($this->db->tableExists('incidents')) {
            return;
        }

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'admin_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'att_id'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tipo'           => ['type' => 'VARCHAR', 'constraint' => 50],
            'fecha'          => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'detalle'        => ['type' => 'TEXT', 'null' => true],
            'estado'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'default' => 'Pendiente'],
            'justificacion'  => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('att_id', false, false, 'idx_att_id');
        $this->forge->addKey('user_id', false, false, 'idx_user_id');
        $this->forge->addKey('estado', false, false, 'idx_estado');
        $this->forge->createTable('incidents', true);
    }

    private function crearCodes(): void
    {
        if ($this->db->tableExists('codes')) {
            return;
        }

        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'code'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'dni'       => ['type' => 'VARCHAR', 'constraint' => 8],
            'admin_id'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status'    => ['type' => 'ENUM', 'constraint' => ['active', 'used', 'inactive'], 'default' => 'active', 'null' => true],
            'created'   => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'used_by'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('dni', false, false, 'idx_dni');
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->createTable('codes', true);
    }

    private function crearAdminConfig(): void
    {
        if ($this->db->tableExists('admin_config')) {
            return;
        }

        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'admin_id'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'empresa'            => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true, 'default' => 'DSG PERU TECHNOLOGY SAC'],
            'ruc'                => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'direccion'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email'              => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'telefono'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'horario_default'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => '08:00-17:00'],
            'tolerancia_default' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 15],
            'notif_email'        => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => 0],
            'notif_email_dest'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('admin_config', true);
    }

    // ══════════════════════════════════════════════════════════
    // COMPLETADO DE COLUMNAS SOBRE TABLAS EXISTENTES
    // ══════════════════════════════════════════════════════════

    private function completarColumnas(): void
    {
        $defs = $this->definiciones();

        foreach ($this->aditivas as $table => $columns) {
            if (! $this->db->tableExists($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if (! $this->db->fieldExists($column, $table)) {
                    $this->forge->addColumn($table, [$column => $defs[$column]]);
                }
            }
        }
    }

    private function definiciones(): array
    {
        return [
            'admin_code'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'personal_code'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'empresa'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'institucion'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'semestre'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'huella_registrada'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'rostro_registrado'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'huella_fecha'       => ['type' => 'DATETIME', 'null' => true],
            'rostro_fecha'       => ['type' => 'DATETIME', 'null' => true],
            'rostro_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'huella_cred_id'     => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'contract_type'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Indefinido'],
            'contract_duration'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'contract_start'     => ['type' => 'DATE', 'null' => true],
            'contract_end'       => ['type' => 'DATE', 'null' => true],
            'firma_tipo'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'firma_datos'        => ['type' => 'TEXT', 'null' => true],
            'schedule_id'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'admin_id'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'evidencia'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tipo'               => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Empleado'],
            'notif_email_dest'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ];
    }

    // ══════════════════════════════════════════════════════════
    // ÍNDICES
    // ══════════════════════════════════════════════════════════

    private function crearIndices(): void
    {
        $indices = [
            'attendance' => ['idx_admin_date' => ['admin_id', 'date']],
            'incidents'  => ['idx_admin'      => ['admin_id']],
            'schedules'  => ['idx_admin'      => ['admin_id']],
            'users'      => ['idx_admin'      => ['admin_id']],
        ];

        foreach ($indices as $table => $list) {
            if (! $this->db->tableExists($table)) {
                continue;
            }
            foreach ($list as $name => $columns) {
                if (! $this->indiceExiste($table, $name)) {
                    $this->db->query('ALTER TABLE `' . $table . '` ADD INDEX `' . $name . '` (`' . implode('`, `', $columns) . '`)');
                }
            }
        }
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
