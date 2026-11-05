<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Columnas que usan «Otros pagos» y los avisos de vencimiento
 * (PersonasAddController::guardarOtrosPagos, NotificadorVencimientos,
 * NotificacionesController) y que faltaban en algunas bases:
 *
 *   id_tipo_pago        1 = plan, 2 = otro pago
 *   concepto            descripción del otro pago
 *   correo_notificacion correo propio para el aviso
 *   ultimo_aviso        fecha del último aviso enviado (uno por día)
 *
 * Cada columna se agrega solo si no existe, así que es segura en bases
 * que ya las tienen. Los registros existentes se clasifican: con plan → 1,
 * sin plan → 2.
 */
class AddPagosTipoPagoColumns extends Migration
{
    private array $agregadas = [];

    public function up(): void
    {
        $columnas = [
            'id_tipo_pago' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 1,
                'after'      => 'id_tipo_plan',
            ],
            'concepto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'id_tipo_pago',
            ],
            'correo_notificacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'observaciones',
            ],
            'ultimo_aviso' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'correo_notificacion',
            ],
        ];

        foreach ($columnas as $nombre => $def) {
            if (! $this->db->fieldExists($nombre, 'pagos')) {
                $this->forge->addColumn('pagos', [$nombre => $def]);
                $this->agregadas[] = $nombre;
            }
        }

        // Si id_tipo_pago es nueva, los pagos sin plan son «otros pagos»
        if (in_array('id_tipo_pago', $this->agregadas, true)) {
            $this->db->table('pagos')->where('id_plan', null)->update(['id_tipo_pago' => 2]);
        }
    }

    public function down(): void
    {
        foreach (['ultimo_aviso', 'correo_notificacion', 'concepto', 'id_tipo_pago'] as $nombre) {
            if ($this->db->fieldExists($nombre, 'pagos')) {
                $this->forge->dropColumn('pagos', $nombre);
            }
        }
    }
}
