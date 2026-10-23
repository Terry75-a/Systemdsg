<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAbsentJustifiedStatus extends Migration
{
    public function up()
    {
        // Falta justificada por el administrador: cuenta históricamente,
        // pero NO se suma como falta en las estadísticas del mes.
        $this->db->query(
            "ALTER TABLE `attendance`
             MODIFY `status` ENUM('present','late','absent','no_exit','absent_justified')
             DEFAULT 'present'"
        );
    }

    public function down()
    {
        $this->db->query(
            "UPDATE `attendance` SET `status` = 'absent' WHERE `status` = 'absent_justified'"
        );
        $this->db->query(
            "ALTER TABLE `attendance`
             MODIFY `status` ENUM('present','late','absent','no_exit')
             DEFAULT 'present'"
        );
    }
}
