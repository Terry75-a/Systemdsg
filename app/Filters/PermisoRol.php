<?php

namespace App\Filters;

use App\Libraries\Asistencia\PermisosRol;
use App\Libraries\Asistencia\Roles;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Bloquea las acciones que el rol tiene desactivadas en «Permisos por rol»
 * (tabla asisten_permisos). Sólo actúa dentro del panel del propio rol;
 * sesión y acceso al panel los siguen validando los filtros dev/asistencia.
 */
class PermisoRol implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $s = session();
        if (! $s->get('logged_in')) {
            return null;
        }
        $role   = (string) ($s->get('user_role') ?? '');
        $accion = PermisosRol::accionDeRuta($role, uri_string());
        if ($accion === null) {
            return null;
        }
        $idRol = $s->get('user_id_rol');
        $idRol = $idRol ? (int) $idRol : Roles::idDe($role);
        if (PermisosRol::puede($role, $accion, $idRol)) {
            return null;
        }

        $msg = 'Tu rol no tiene permiso para: ' . mb_strtolower(PermisosRol::ACCIONES[$accion]['label']) . '.';
        $esAjax = $request->isAJAX()
            || str_contains(strtolower($request->getHeaderLine('Accept')), 'application/json');
        if ($esAjax) {
            return service('response')->setStatusCode(403)->setJSON(['ok' => false, 'msg' => $msg]);
        }

        $s->setFlashdata('msg', $msg);
        $s->setFlashdata('tipo', 'danger');
        $home = ['Dev' => 'dios', 'Admin' => 'admin'][$role] ?? 'mi-panel';
        // Volver a la página anterior si era del mismo panel; si no, al inicio del panel
        $prev = (string) previous_url();
        $destino = ($prev !== '' && str_contains($prev, '/' . $home) && strtolower($request->getMethod()) === 'post')
            ? $prev : base_url($home);
        return redirect()->to($destino);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
