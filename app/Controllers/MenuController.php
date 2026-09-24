<?php
namespace App\Controllers;

class MenuController extends BaseController
{
    private function obtenerMenuItems(): array
    {
        return [
            ['icon' => 'fa-chart-line', 'label' => 'Inicio', 'type' => 'directo'],
            ['icon' => 'fa-users', 'label' => 'Personas', 'type' => 'directo'],
            ['icon' => 'fa-calendar', 'label' => 'Calendario', 'type' => 'directo'],
            ['icon' => 'fa-user', 'label' => 'Mi Perfil', 'type' => 'directo'],
            [
                'icon' => 'fa-layer-group',
                'label' => 'Módulos de administración',
                'type' => 'grupo',
                'children' => ['Permisos', 'Usuarios', 'Menú'],
            ],
            ['icon' => 'fa-cog', 'label' => 'Configuración', 'type' => 'directo'],
        ];
    }

    private function obtenerLimitaciones(): array
    {
        return [
            'Este módulo hoy solo expone una vista informativa del sidebar administrable.',
            'No existe backend para reordenar, crear, editar ni activar/desactivar entradas del menú.',
            'Cualquier cambio estructural del menú requiere implementación adicional en rutas, controlador, persistencia y UI.',
        ];
    }

    public function index()
    {
        $redir = $this->requerirAdminBackend('Solo un administrador puede gestionar el menú del sistema.');
        if ($redir) {
            return $redir;
        }

        $data = $this->crearDatosVistaAdmin('Menú del sistema', 'menu', [
            'menuItems' => $this->obtenerMenuItems(),
            'limitaciones' => $this->obtenerLimitaciones(),
        ]);

        return $this->renderAdminPage('Menu', $data);
    }
}
