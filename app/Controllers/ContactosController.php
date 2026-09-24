<?php

namespace App\Controllers;


use CodeIgniter\Controller;

class ContactosController extends Controller
{
    public function index(){    
        

      return view('/layouts/header')
            . view('/layouts/nav')
            . view('contacto-demo')
            . view('/layouts/footer');
        

      

    }
}
