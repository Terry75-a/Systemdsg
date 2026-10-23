<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSessionLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 120],
            'role'    => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => ''],
            'evento'  => ['type' => 'ENUM', 'constraint' => ['login', 'logout']],
            'ip'      => ['type' => 'VARCHAR', 'constraint' => 45, 'default' => ''],
            'creado'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('creado');
        $this->forge->addKey('user_id');
        $this->forge->addKey('evento');
        $this->forge->createTable('session_log');
    }

    public function down()
    {
        $this->forge->dropTable('session_log');
    }
}
