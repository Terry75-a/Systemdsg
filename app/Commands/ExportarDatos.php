<?php

namespace App\Commands;

use App\Libraries\Asistencia\SyncDatos;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

/**
 * Exporta todos los datos de Asisten a un .sql para pasarlos a otra máquina.
 *
 *   php spark asisten:exportar                      → writable/sync/asisten_datos.sql
 *   php spark asisten:exportar --archivo ruta.sql
 */
class ExportarDatos extends BaseCommand
{
    protected $group       = 'Asisten';
    protected $name        = 'asisten:exportar';
    protected $description = 'Exporta los datos de Asisten (usuarios, horarios, asistencias…) a un .sql.';
    protected $usage       = 'asisten:exportar [--archivo ruta.sql]';
    protected $options     = ['--archivo' => 'Ruta del .sql (por defecto writable/sync/asisten_datos.sql)'];

    public function run(array $params)
    {
        $archivo = (string) (CLI::getOption('archivo') ?? WRITEPATH . 'sync/asisten_datos.sql');
        // spark trabaja desde public/: una ruta relativa se toma desde la raíz del proyecto
        if (! preg_match('#^([a-zA-Z]:)?[\\/]#', $archivo)) {
            $archivo = ROOTPATH . $archivo;
        }
        try {
            $conteo = (new SyncDatos(db_connect()))->exportar($archivo);
        } catch (Throwable $e) {
            CLI::error('No se pudo exportar: ' . $e->getMessage());
            return EXIT_ERROR;
        }
        foreach ($conteo as $tabla => $n) {
            CLI::write(str_pad($tabla, 22) . $n . ' filas');
        }
        CLI::write('Listo: ' . realpath($archivo), 'green');
        CLI::write('Pásalo por un canal privado (tiene datos personales). En la otra PC: php spark asisten:importar <archivo>', 'yellow');
        return EXIT_SUCCESS;
    }
}
