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

        
        $data['username'] = session()->get('username');

        
        
        $dashboardModel = new DashboardModel();
        $data = [
            'username' => session()->get('username'),
            'totalClientes' => $dashboardModel->totalClientes(),
            'nuevosClientes' => $dashboardModel->nuevosClientesEsteMes(),
            'totalPlanes' => $dashboardModel->totalPlanes(),
            'totalTipoPlan' => $dashboardModel->totalTipoPlan(),
            'totalUsuarios' => $dashboardModel->totalUsuarios(),
            
        ];

   

        $data['titulo'] = 'Panel de Control';
        echo view('layouts/header');
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('dashboard', $data);
        echo view('layouts/footer');
    }
}
