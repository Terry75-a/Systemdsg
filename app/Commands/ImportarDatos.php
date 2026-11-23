<?php

namespace App\Commands;

use App\Libraries\Asistencia\SyncDatos;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

/**
 * Carga un .sql hecho con asisten:exportar. Antes pone la base al día con las
 * migraciones, así funciona aunque a esta base le falten tablas.
 * Reemplaza los datos de Asisten de esta base; no toca las tablas del panel.
 *
 *   php spark asisten:importar                       → writable/sync/asisten_datos.sql
 *   php spark asisten:importar C:\ruta\asisten_datos.sql
 */
class ImportarDatos extends BaseCommand
{
    protected $group       = 'Asisten';
    protected $name        = 'asisten:importar';
    protected $description = 'Carga los datos de Asisten desde un .sql hecho con asisten:exportar.';
    protected $usage       = 'asisten:importar [archivo.sql] [--si]';
    protected $arguments   = ['archivo' => 'Ruta del .sql (por defecto writable/sync/asisten_datos.sql)'];
    protected $options     = ['--si' => 'No preguntar antes de reemplazar'];

    public function run(array $params)
    {
        $archivo = $params[0] ?? WRITEPATH . 'sync/asisten_datos.sql';
        // spark trabaja desde public/: una ruta relativa se busca desde la raíz del proyecto
        if (! is_file($archivo) && is_file(ROOTPATH . $archivo)) {
            $archivo = ROOTPATH . $archivo;
        }
        if (! is_file($archivo)) {
            CLI::error("No existe el archivo: $archivo");
            return EXIT_ERROR;
        }

        $db = db_connect();
        CLI::write('Base: ' . $db->getDatabase() . ' · archivo: ' . $archivo);
        if (CLI::getOption('si') === null
            && CLI::prompt('Se reemplazarán los datos de Asisten de esta base. ¿Continuar?', ['n', 's']) !== 's') {
            CLI::write('Cancelado.');
            return EXIT_SUCCESS;
        }

        try {
            $runner = service('migrations');
            $runner->setNamespace('App');
            $runner->latest();
            CLI::write('Tablas al día.', 'green');

            $sync = new SyncDatos($db);
            $n = $sync->importar($archivo);
            if ($sync->saltadas) {
                CLI::write('Saltadas (no existen en esta base): ' . implode(', ', array_keys($sync->saltadas)), 'yellow');
            }
        } catch (Throwable $e) {
            CLI::error('No se pudo importar: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        foreach (SyncDatos::TABLAS as $tabla) {
            if ($db->tableExists($tabla)) {
                CLI::write(str_pad($tabla, 22) . $db->table($tabla)->countAllResults() . ' filas');
            }
        }
        CLI::write("Listo: $n sentencias aplicadas.", 'green');
        return EXIT_SUCCESS;
    }
}
