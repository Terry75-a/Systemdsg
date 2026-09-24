<?php

namespace App\Controllers;
use Config\Database;

class Home extends BaseController
{
    public function index(): string
    {
        return view('/layouts/header')
            .view('/layouts/nav')
            .view('index')
            .view('/layouts/footer');
    }
    
    public function dsg(): string
    {
        return view('/layouts/header')
            .view('/layouts/nav')
            .view('dsg')
            .view('/layouts/footer');
    }
    
    public function precio(): string
    {
        return view('/layouts/header')
          .view('/layouts/nav')
          .view('precio')
          .view('/layouts/footer');
    }
    
    public function servicios(): string
    {
        return view('/layouts/header')
          .view('/layouts/nav')
          .view('servicios')
          .view('/layouts/footer');
    }

    // Página independiente: no usa los layouts del sitio principal
    public function asistencia(): string
    {
        return view('asisten-dsg');
    }

    // Página de precios de Asisten DSG
    public function precioAsisten(): string
    {
        return view('precio-asisten');
    }

    // Login standalone: paleta verde Asisten DSG
    public function loginVerde(): string
    {
        return view('login-asisten');
    }

    // Para probar conexión BD
    public function testDB()
    {
        $db = Database::connect();
        if ($db->connect()) {
            echo "Base de datos conectada correctamente";
        } else {
            echo "Error al conectar a la base de datos";
        }
    }
}