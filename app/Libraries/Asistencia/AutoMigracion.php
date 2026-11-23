<?php

namespace App\Libraries\Asistencia;

use Throwable;

/**
 * Aplica sola las migraciones pendientes de Asisten al recibir la primera
 * petición después de un `git pull`.
 *
 * Así, quien baje los cambios no tiene que acordarse de `php spark migrate`:
 * si a su base le faltan tablas o columnas nuevas (p. ej. «Table
 * 'sistemapo.users' doesn't exist»), se crean al abrir el sistema.
 *
 *  · Sólo corre en peticiones web (los comandos `spark` se manejan solos).
 *  · Se comprueba una vez por versión del código: el resultado queda en
 *    writable/cache/asisten_migraciones.txt con la huella de los archivos de
 *    migración. Mientras no llegue una migración nueva, no toca la base.
 *  · Un candado evita que dos peticiones migren a la vez.
 *  · Si falla, lo deja en el log y la página sigue (no tumba el sistema).
 *  · Se apaga con `asisten.autoMigrate = false` en el .env.
 */
class AutoMigracion
{
    private const MARCA = 'asisten_migraciones.txt';

    public static function ejecutar(): void
    {
        if (is_cli() || ENVIRONMENT === 'testing') {
            return;
        }
        if (filter_var(env('asisten.autoMigrate', true), FILTER_VALIDATE_BOOLEAN) === false) {
            return;
        }

        $huella = self::huella();
        $marca  = WRITEPATH . 'cache/' . self::MARCA;
        if (is_file($marca) && trim((string) @file_get_contents($marca)) === $huella) {
            return; // ya está al día con este código
        }

        $candado = @fopen(WRITEPATH . 'cache/asisten_migraciones.lock', 'c');
        if ($candado === false || ! flock($candado, LOCK_EX | LOCK_NB)) {
            return; // otra petición está migrando
        }

        try {
            $runner = service('migrations');
            $runner->setNamespace('App');
            $runner->latest();
            @file_put_contents($marca, $huella);
            log_message('info', 'AutoMigracion: base de datos al día (' . $huella . ').');
        } catch (Throwable $e) {
            log_message('error', 'AutoMigracion: no se pudo migrar: ' . $e->getMessage()
                . ' — corre «php spark migrate» para ver el detalle.');
        } finally {
            flock($candado, LOCK_UN);
            fclose($candado);
        }
    }

    /** Huella de los archivos de migración: cambia cuando llega una nueva. */
    private static function huella(): string
    {
        $archivos = glob(APPPATH . 'Database/Migrations/*.php') ?: [];
        sort($archivos);
        $db = config('Database')->default;
        // Incluye la base: si se cambia de base en el .env, se vuelve a comprobar
        return md5(implode('|', array_map('basename', $archivos)) . '@' . ($db['hostname'] ?? '') . '/' . ($db['database'] ?? ''));
    }
}
