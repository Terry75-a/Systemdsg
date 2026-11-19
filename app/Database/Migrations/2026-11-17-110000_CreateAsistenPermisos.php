<?php

namespace App\Database\Migrations;

use App\Libraries\Asistencia\PermisosRol;
use CodeIgniter\Database\Migration;

/**
 * Permisos por rol de Asisten (editables desde /dios/estructura).
 *
 * Una fila por (rol, acción). El rol es un registro de la tabla `roles`
 * del panel; si se borra el rol, se borran sus permisos.
 */
class CreateAsistenPermisos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_rol'     => ['type' => 'INT', 'constraint' => 11],
            'accion'     => ['type' => 'VARCHAR', 'constraint' => 40],
            'permitido'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_rol', 'accion']);
        $this->forge->addForeignKey('id_rol', 'roles', 'id_rol', 'CASCADE', 'CASCADE');
        $this->forge->createTable('asisten_permisos', true);

        // Valores iniciales = lo que el sistema ya hacía
        $ahora = date('Y-m-d H:i:s');
        foreach (PermisosRol::ACCIONES as $accion => $def) {
            foreach ($def['aplica'] as $role) {
                $idRol = \App\Libraries\Asistencia\Roles::idDe($role);
                if ($idRol === null) continue;
                $this->db->table('asisten_permisos')->ignore(true)->insert([
                    'id_rol' => $idRol, 'accion' => $accion,
                    'permitido' => (int) in_array($role, $def['default'], true),
                    'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('asisten_permisos', true);
    }
}
