<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * QR de asistencia por HORARIO.
 *
 * Cada horario (Empleado o Practicante) tiene un token secreto que viaja
 * dentro del QR que descarga el admin desde la página de Horarios.
 * El personal escanea el QR de su horario, inicia sesión y confirma con
 * huella o rostro: la biométrica sigue obligatoria.
 */
class AddSchedulesQrToken extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('schedules', [
            'qr_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'estado',
            ],
            'qr_updated' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'qr_token',
            ],
        ]);

        $this->db->query('CREATE UNIQUE INDEX uq_schedules_qr_token ON schedules (qr_token)');
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX uq_schedules_qr_token ON schedules');

        if ($this->db->tableExists('schedules')) {
            foreach (['qr_token', 'qr_updated'] as $col) {
                if ($this->db->fieldExists($col, 'schedules')) {
                    $this->forge->dropColumn('schedules', $col);
                }
            }
        }
    }
}
