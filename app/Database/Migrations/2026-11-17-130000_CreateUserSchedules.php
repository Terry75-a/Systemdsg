<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Una persona puede tener VARIOS horarios (p. ej. «L-V» y «Sábado»).
 *
 * `user_schedules` guarda todos; `users.schedule_id` se mantiene como el
 * horario principal (el primero) para lo que solo necesita uno.
 * Cada día se usa el horario cuyos días incluyen esa fecha.
 */
class CreateUserSchedules extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'user_id'     => ['type' => 'INT', 'constraint' => 11],
            'schedule_id' => ['type' => 'INT', 'constraint' => 11],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey(['user_id', 'schedule_id']);
        $this->forge->addKey('schedule_id');
        $this->forge->createTable('user_schedules', true);

        // Llaves foráneas solo si los tipos coinciden (users.id / schedules.id)
        try {
            $this->db->query('ALTER TABLE user_schedules ADD CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
            $this->db->query('ALTER TABLE user_schedules ADD CONSTRAINT fk_us_schedule FOREIGN KEY (schedule_id) REFERENCES schedules (id) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            log_message('warning', 'user_schedules sin FK: ' . $e->getMessage());
        }

        $this->db->query('INSERT IGNORE INTO user_schedules (user_id, schedule_id, created_at)
                          SELECT u.id, u.schedule_id, NOW() FROM users u JOIN schedules s ON s.id = u.schedule_id
                          WHERE u.schedule_id IS NOT NULL');
    }

    public function down(): void
    {
        $this->forge->dropTable('user_schedules', true);
    }
}
