<?php

namespace App\Database\Migrations;

use App\Libraries\Asistencia\Jornada;
use CodeIgniter\Database\Migration;

/**
 * Tipo de jornada de cada horario: full time o part time.
 *
 * Los horarios que ya existen se clasifican por su nombre («fulltime»,
 * «part time»…) y, si el nombre no lo dice, por sus horas al día
 * (8 h o más = full time).
 */
class AddSchedulesJornada extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('jornada', 'schedules')) {
            $this->forge->addColumn('schedules', [
                'jornada' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'after' => 'tipo'],
            ]);
        }
        foreach ($this->db->table('schedules')->get()->getResultArray() as $sch) {
            $nombre = mb_strtolower((string) $sch['nombre']);
            $plano  = preg_replace('/[^a-z]/', '', $nombre);
            if (str_contains($plano, 'full') || str_contains($nombre, 'completo')) {
                $j = Jornada::FULL;
            } elseif (str_contains($plano, 'part') || str_contains($nombre, 'parcial')) {
                $j = Jornada::PART;
            } else {
                $j = Jornada::detectar($sch);
            }
            $this->db->table('schedules')->where('id', $sch['id'])->update(['jornada' => $j]);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('schedules', 'jornada');
    }
}
