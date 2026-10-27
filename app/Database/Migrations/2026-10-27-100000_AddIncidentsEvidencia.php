<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Foto opcional que el colaborador adjunta al justificar una incidencia
 * (nombre plano en writable/uploads/evidencias, servida por
 * EvidenciaController).
 */
class AddIncidentsEvidencia extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('incidents', [
            'evidencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'justificacion',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('incidents', 'evidencia');
    }
}
