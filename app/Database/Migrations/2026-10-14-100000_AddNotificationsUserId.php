<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Notificaciones para empleados: user_id opcional; admin_id pasa a nullable
 * (una notificación es del admin O del empleado, no de ambos).
 */
class AddNotificationsUserId extends Migration
{
    public function up()
    {
        $this->forge->addColumn('notifications', [
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'admin_id',
            ],
        ]);

        $this->db->query('ALTER TABLE notifications MODIFY admin_id INT(10) UNSIGNED NULL');
        $this->db->query('ALTER TABLE notifications ADD INDEX idx_notifications_user (user_id, leida)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE notifications DROP INDEX idx_notifications_user');
        $this->forge->dropColumn('notifications', 'user_id');
        $this->db->query('ALTER TABLE notifications MODIFY admin_id INT(10) UNSIGNED NOT NULL');
    }
}
