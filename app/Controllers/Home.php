<?php

namespace App\Controllers;
use Config\Database;

class Home extends BaseController
{
    public function index(): string
    {
        // Home con diseño propio (estilo recent.design): no usa header/nav/footer del sitio
        return view('layouts/site', [
            'slot' => view('index'),
        ]);
    }
    
    public function dsg(): string
    {
        return view('layouts/site', [
            'titulo' => 'DSG Perú · Quiénes somos',
            'slot'   => view('dsg'),
        ]);
    }

    public function precio(): string
    {
        return view('layouts/site', [
            'titulo' => 'DSG Perú · Precios',
            'slot'   => view('precio'),
        ]);
    }

    public function servicios(): string
    {
        return view('layouts/site', [
            'titulo' => 'DSG Perú · Servicios',
            'slot'   => view('servicios'),
        ]);
    }

    public function terminos(): string
    {
        return view('layouts/site', [
            'titulo' => 'DSG Perú · Términos y políticas',
            'slot'   => view('terminos'),
        ]);
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