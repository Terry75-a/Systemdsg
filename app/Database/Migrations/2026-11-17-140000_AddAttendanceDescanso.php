<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Doble turno con marcas reales del descanso.
 *
 *   time_in   entrada del día        (08:00)
 *   time_out1 salida al descanso     (13:00)
 *   time_in2  regreso del descanso   (14:00)
 *   time_out  salida final del día   (19:00)
 *
 * En horarios de un solo bloque time_out1/time_in2 quedan NULL.
 */
class AddAttendanceDescanso extends Migration
{
    public function up(): void
    {
        $campos = [];
        if (! $this->db->fieldExists('time_out1', 'attendance')) {
            $campos['time_out1'] = ['type' => 'TIME', 'null' => true, 'after' => 'time_in'];
        }
        if (! $this->db->fieldExists('time_in2', 'attendance')) {
            $campos['time_in2'] = ['type' => 'TIME', 'null' => true, 'after' => 'time_out1'];
        }
        if ($campos) {
            $this->forge->addColumn('attendance', $campos);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('attendance', ['time_out1', 'time_in2']);
    }
}
