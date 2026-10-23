<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPushSubscriptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'endpoint'   => ['type' => 'VARCHAR', 'constraint' => 500],
            'p256dh'     => ['type' => 'VARCHAR', 'constraint' => 128],
            'auth'       => ['type' => 'VARCHAR', 'constraint' => 64],
            'content_coding' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'aes128gcm'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'last_seen'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('endpoint');
        $this->forge->addKey('user_id');
        $this->forge->createTable('push_subscriptions', true);
    }

    public function down()
    {
        $this->forge->dropTable('push_subscriptions', true);
    }
}
