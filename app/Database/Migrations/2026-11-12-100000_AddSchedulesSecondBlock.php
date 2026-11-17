<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Jornada partida / doble turno en HORARIOS.
 *
 * Un horario puede tener un SEGUNDO bloque opcional (hora_entrada2 →
 * hora_salida2) con el descanso en medio:
 *
 *   · Empleado  doble turno : 08:00 → 13:00  +  16:00 → 20:00
 *   · Practicante que extiende: 09:00 → 13:00 + 15:00 → 17:00 (6 h)
 *
 * Sigue habiendo UNA entrada y UNA salida por día en `attendance`; lo que
 * cambia es cómo se cuentan las horas (se descuenta el descanso) y contra
 * qué hora se mide la salida prevista / el cierre del día.
 *
 * Las columnas quedan NULL cuando el horario es de un solo bloque, así que
 * los horarios existentes siguen comportándose igual que antes.
 */
class AddSchedulesSecondBlock extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('schedules', [
            'hora_entrada2' => [
                'type'    => 'TIME',
                'null'    => true,
                'after'   => 'hora_salida',
            ],
            'hora_salida2' => [
                'type'    => 'TIME',
                'null'    => true,
                'after'   => 'hora_entrada2',
            ],
        ]);
    }

    public function down(): void
    {
        if ($this->db->tableExists('schedules')) {
            foreach (['hora_entrada2', 'hora_salida2'] as $col) {
                if ($this->db->fieldExists($col, 'schedules')) {
                    $this->forge->dropColumn('schedules', $col);
                }
            }
        }
    }
}
