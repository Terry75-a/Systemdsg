<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeoConfig extends Migration
{
    public function up()
    {
        $fields = [
            'geo_lat'      => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'notif_email_dest'],
            'geo_lng'      => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'geo_lat'],
            'geo_radio_m'  => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'geo_lng'],
            'geo_requerido'=> ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'geo_radio_m'],
        ];
        $this->forge->addColumn('admin_config', $fields);

        $this->forge->addColumn('attendance', [
            'geo_ok' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'after' => 'lng'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admin_config', ['geo_lat', 'geo_lng', 'geo_radio_m', 'geo_requerido']);
        $this->forge->dropColumn('attendance', 'geo_ok');
    }
}
