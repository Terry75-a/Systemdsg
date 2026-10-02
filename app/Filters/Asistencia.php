<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Protege los rieles del módulo de asistencia:
 *   /admin/*     → sólo rol Admin (Dev se manda a /dios)
 *   /mi-panel/*  → sólo personal (Empleado / Practicante / Usuario)
 *
 * Es la primera barrera; los controladores conservan su guard() propio.
 */
class Asistencia implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri       = uri_string();
        $esAdmin   = $uri === 'admin' || str_starts_with($uri, 'admin/');
        $esMiPanel = $uri === 'mi-panel' || str_starts_with($uri, 'mi-panel/');

        if (! $esAdmin && ! $esMiPanel) {
            return null;
        }

        $rol = (string) (session()->get('user_role') ?? '');

        if (! session()->get('logged_in')) {
            return $this->salir($request, 'login-verde', 'Tu sesión expiró. Inicia sesión de nuevo.');
        }

        if ($esAdmin) {
            if ($rol === 'Dev') {
                return redirect()->to(base_url('dios'));
            }
            if ($rol !== 'Admin') {
                return $this->salir($request, $this->home($rol), 'Este panel es sólo para administradores.');
            }
            return null;
        }

        // mi-panel
        if ($rol === 'Dev') {
            return redirect()->to(base_url('dios'));
        }
        if ($rol === 'Admin') {
            return redirect()->to(base_url('admin'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function home(string $rol): string
    {
        if ($rol === 'Dev') {
            return 'dios';
        }
        if ($rol === 'Admin') {
            return 'admin';
        }
        return 'mi-panel';
    }

    /**
     * En AJAX/JSON responde 401/403; en el navegador redirige con aviso.
     */
    private function salir(RequestInterface $request, string $ruta, string $mensaje)
    {
        $esAjax = $request->isAJAX()
            || str_contains(strtolower($request->getHeaderLine('Accept')), 'application/json');

        if ($esAjax) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => $mensaje]);
        }

        session()->set('redirect_url', current_url());
        if ($ruta !== 'login-verde') {
            session()->setFlashdata('msg', $mensaje);
            session()->setFlashdata('tipo', 'warning');
        }

        return redirect()->to(base_url($ruta));
    }
}
