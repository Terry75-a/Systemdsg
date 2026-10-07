<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Registro de credenciales WebAuthn reales (llave publica COSE + contador),
 * necesario para verificar criptograficamente la huella en el servidor.
 *  - webauthn_credentials : una fila por credencial del usuario
 *  - users.huella_cred_id  : se amplia para no truncar credential ids largos
 */
class CreateWebauthnCredentials extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('webauthn_credentials')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'user_id'         => ['type' => 'INT', 'unsigned' => true],
                'credential_id'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'public_key'      => ['type' => 'TEXT'],
                'counter'         => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                'user_handle'     => ['type' => 'VARCHAR', 'constraint' => 100],
                'aaguid'          => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'attestation_type'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'transports'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'backup_eligible' => ['type' => 'TINYINT', 'null' => true],
                'backup_status'   => ['type' => 'TINYINT', 'null' => true],
                'uv_initialized'  => ['type' => 'TINYINT', 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'last_used_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addUniqueKey('credential_id');
            $this->forge->createTable('webauthn_credentials', true);
        }

        if ($this->db->fieldExists('huella_cred_id', 'users')) {
            $this->db->query('ALTER TABLE `users` MODIFY `huella_cred_id` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('webauthn_credentials')) {
            $this->forge->dropTable('webauthn_credentials', true);
        }

        if ($this->db->fieldExists('huella_cred_id', 'users')) {
            $this->db->query('ALTER TABLE `users` MODIFY `huella_cred_id` VARCHAR(64) NULL');
        }
    }
}
