<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotificadorVencimientos;

class EnviarVencimientos extends BaseCommand
{
    protected $group       = 'DSG';
    protected $name        = 'vencimientos:enviar';
    protected $description = 'Envía avisos de planes/pagos por vencer al cliente y al admin.';

    public function run(array $params)
    {
        CLI::write('[' . date('Y-m-d H:i:s') . '] Revisando vencimientos...', 'yellow');

        $r = (new NotificadorVencimientos())->procesarRecordatorios();

        CLI::write("Enviados: {$r['enviados']} | Errores: {$r['errores']}", $r['errores'] ? 'red' : 'green');
        foreach ($r['detalles'] as $d) {
            CLI::write(' - ' . $d);
        }
    }
}