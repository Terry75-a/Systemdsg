<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'admin_id' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'titulo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'mensaje' => ['type' => 'TEXT', 'null' => false],
            'datos' => ['type' => 'JSON', 'null' => true],
            'leida' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'leida_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id');
        $this->forge->addKey('leida');
        $this->forge->addKey('created_at');
        $this->forge->createTable('notifications');
    }

    public function down(): void
    {
        $this->forge->dropTable('notifications');
    }
}
