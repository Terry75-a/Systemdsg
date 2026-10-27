<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;

/**
 * Filtro del panel Dev (rutas /dios/* y registro-empleado).
 *
 * 1. Autorización: sesión iniciada con rol 'Dev' (lo que ya hacía
 *    guardDev() en cada método, ahora también en la entrada al router,
 *    para que ni siquiera se llegue al controlador).
 * 2. Rate limit por sesión + IP en ventana fija de 60 s (caché):
 *    240 peticiones de lectura y 40 de escritura (POST). Si se supera,
 *    responde 429 con Retry-After.
 *
 * El controlador conserva sus llamadas a guardDev() (defensa en
 * profundidad); si la caché no estuviera disponible, el límite se salta
 * pero la autorización nunca se salta.
 */
class DevGuard implements FilterInterface
{
    /** Ventana del contador, en segundos. */
    private const VENTANA = 60;

    /** Peticiones máximas por ventana: GET/lectura. */
    private const MAX_GET = 240;

    /** Peticiones máximas por ventana: POST/escritura. */
    private const MAX_POST = 40;

    public function before(RequestInterface $request, $arguments = null)
    {
        $s = session();

        if (! $s->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $rol = (string) ($s->get('user_role') ?? '');
        if ($rol !== 'Dev') {
            return redirect()->to(base_url($rol === 'Admin' ? 'admin' : 'mi-panel'));
        }

        $limite = $request->getMethod() === 'POST' ? self::MAX_POST : self::MAX_GET;
        $key    = 'dev_rl_' . md5((string) $s->get('user_id') . '|' . $request->getIPAddress());

        try {
            $cache = service('cache');
            $dato  = $cache->get($key);

            // Ventana fija: se reinicia cuando la anterior ya venció
            if (! is_array($dato) || ! isset($dato['t'], $dato['n'])
                || (time() - (int) $dato['t']) >= self::VENTANA) {
                $dato = ['t' => time(), 'n' => 0];
            }
            $dato['n']++;
            $cache->save($key, $dato, self::VENTANA);

            if ((int) $dato['n'] > $limite) {
                $espera = max(1, self::VENTANA - (time() - (int) $dato['t']));

                return service('response')
                    ->setStatusCode(429)
                    ->setHeader('Retry-After', (string) $espera)
                    ->setBody('Demasiadas solicitudes al panel Dev. Intenta de nuevo en '
                        . $espera . ' segundos.');
            }
        } catch (\Throwable $e) {
            // Caché no disponible: seguimos dejando pasar (el filtro de
            // rol de arriba nunca se omite).
            log_message('error', 'Rate limit del panel Dev no aplicable: ' . $e->getMessage());
        }

        return null;
    }

    public function after(RequestInterface $request, $response, $arguments = null)
    {
        // Sin nada que hacer tras la petición.
    }
}
