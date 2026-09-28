<?php

namespace App\Controllers;


class NotificacionesController extends BaseController 
{
    public function index ()
    {
        $data['titulo'] = 'Notificaciones';
        session()->set('last_page', 'notificaciones');

        echo view('layouts/header');
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('notificaciones/notificaciones', $data);
        echo view('layouts/footer');
    }
}

