<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FaltasCron extends BaseCommand
{
    protected $group       = 'Asistencia';
    protected $name        = 'faltas:run';
    protected $description = 'Genera faltas automáticas de hoy (o de la fecha indicada) para quien no marcó entrada.';

    public function run(array $params)
    {
        $fecha = $params[0] ?? date('Y-m-d');
        $n = (new \App\Services\FaltaService())->generarFaltas($fecha);
        CLI::write("Faltas generadas para {$fecha}: {$n}", 'green');
    }
}
