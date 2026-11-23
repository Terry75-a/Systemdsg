<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Une los roles de Asisten con la tabla `roles` del panel.
 *
 *  · Crea en `roles` los perfiles de Asisten que faltan (dev, empleado,
 *    practicante); el Admin usa el rol `admin` (id 1) que ya existe.
 *  · Agrega `users.id_rol` (FK a roles.id_rol) y lo llena según `users.role`.
 *
 * La columna `users.role` se mantiene para no romper nada: el sistema la
 * sigue escribiendo en paralelo (ver App\Libraries\Asistencia\Roles).
 */
class AddUsersIdRol extends Migration
{
    private const ROLES = [
        'admin'       => 'Administrador de empresa (Asisten)',
        'dev'         => 'Superadmin del sistema (Asisten)',
        'empleado'    => 'Personal de planilla (Asisten)',
        'practicante' => 'Practicante (Asisten)',
    ];

    public function up(): void
    {
        // `roles` es del panel de clientes. En una base sin el panel se crea con
        // su misma estructura, para que Asisten funcione solo.
        if (! $this->db->tableExists('roles')) {
            $this->db->query("CREATE TABLE `roles` (
                `id_rol` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(50) NOT NULL,
                `descripcion` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT current_timestamp(),
                `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id_rol`),
                UNIQUE KEY `uq_nombre_rol` (`nombre`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        }

        $ahora = date('Y-m-d H:i:s');
        foreach (self::ROLES as $nombre => $desc) {
            $existe = $this->db->table('roles')->where('nombre', $nombre)->countAllResults();
            if (! $existe) {
                $this->db->table('roles')->insert([
                    'nombre' => $nombre, 'descripcion' => $desc,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }
        }

        if (! $this->db->fieldExists('id_rol', 'users')) {
            $this->forge->addColumn('users', [
                'id_rol' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'role'],
            ]);
        }

        $this->db->query('UPDATE users u JOIN roles r ON r.nombre = LOWER(u.role) SET u.id_rol = r.id_rol WHERE u.id_rol IS NULL');

        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_id_rol FOREIGN KEY (id_rol) REFERENCES roles (id_rol) ON UPDATE CASCADE ON DELETE RESTRICT');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_id_rol');
        $this->forge->dropColumn('users', 'id_rol');
        // Los roles creados en `roles` se dejan: el panel podría estar usándolos.
    }
}
