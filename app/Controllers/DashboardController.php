<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        session()->set('last_page', 'dashboard');

        $dashboardModel = new DashboardModel();
        $seguimiento = $dashboardModel->seguimientoPlanes(7);

        $hoy = date('Y-m-d');
        $estadoPrevio = session()->get('dash_toasts_state');
        $esPrimeraVez = !is_array($estadoPrevio);

        $mostrarToastBienvenida = $esPrimeraVez;
        $mostrarToastProximos = false;
        $mostrarToastVencidos = false;

        if ($esPrimeraVez) {
            $mostrarToastProximos = $seguimiento['proximos'] > 0;
            $mostrarToastVencidos = $seguimiento['vencidos'] > 0;
        } else {
            $cambioFecha = ($estadoPrevio['fecha'] ?? '') !== $hoy;
            $cambioProximos = (int) ($estadoPrevio['proximos'] ?? -1) !== $seguimiento['proximos'];
            $cambioVencidos = (int) ($estadoPrevio['vencidos'] ?? -1) !== $seguimiento['vencidos'];

            if ($seguimiento['proximos'] > 0) {
                if ($cambioFecha) {
                    $mostrarToastProximos = true;
                } elseif ($cambioProximos) {
                    $mostrarToastProximos = true;
                }
            }

            if ($seguimiento['vencidos'] > 0) {
                if ($cambioFecha) {
                    $mostrarToastVencidos = true;
                } elseif ($cambioVencidos) {
                    $mostrarToastVencidos = true;
                }
            }
        }

        session()->set('dash_toasts_state', [
            'fecha'    => $hoy,
            'proximos' => $seguimiento['proximos'],
            'vencidos' => $seguimiento['vencidos'],
        ]);

        $data = [
            'titulo'                 => 'Panel de Control',
            'username'               => session()->get('username'),
            'totalClientes'          => $dashboardModel->totalClientes(),
            'nuevosClientes'         => $dashboardModel->nuevosClientesEsteMes(),
            'totalEmpresas'          => $dashboardModel->totalEmpresas(),
            'totalSucursales'        => $dashboardModel->totalSucursales(),
            'totalPlanesActivos'     => $dashboardModel->totalPlanesActivos(),
            'totalPlanes'            => $dashboardModel->totalPlanes(),
            'totalTipoPlan'          => $dashboardModel->totalTipoPlan(),
            'totalUsuarios'          => $dashboardModel->totalUsuarios(),
            'planesProximos'         => $seguimiento['proximos'],
            'planesVencidos'         => $seguimiento['vencidos'],
            'mostrarToastBienvenida' => $mostrarToastBienvenida,
            'mostrarToastProximos'   => $mostrarToastProximos,
            'mostrarToastVencidos'   => $mostrarToastVencidos,
        ];

        echo view('layouts/header');
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('dashboard', $data);
        echo view('layouts/footer');
    }
}