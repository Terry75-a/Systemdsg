<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    /**
     * Se ejecuta ANTES de cada request protegido
     *
     * Soporta argumentos desde las rutas:
     *   ['filter' => 'auth']           → solo verifica sesión activa
     *   ['filter' => 'auth:1']         → verifica sesión + que sea rol id=1 (admin)
     *   ['filter' => 'auth:1,2']       → verifica sesión + que sea rol 1 o 2
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('auth');

        // 1. Verificar si hay sesión activa
        if (!session()->get('logged_in')) {
            return respuesta_auth_requerida();
        }

        // 2. Si se pasaron roles como argumento, verificar que el usuario tenga uno de ellos
        // Ejemplo en rutas: 'filter' => 'auth:1'  o  'filter' => 'auth:1,2,3'
        if (!empty($arguments)) {
            $rolesPermitidos = array_map('intval', $arguments); // ['1','2'] → [1, 2]
            $rolUsuario      = (int) session()->get('id_rol');

            if (!in_array($rolUsuario, $rolesPermitidos)) {
                return respuesta_auth_denegada('No tienes acceso a este módulo');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita lógica aquí
    }
}
