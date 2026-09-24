<?php
namespace App\Controllers;

class ConfiguracionController extends BaseController
{
    private function obtenerModulosAdministracion(): array
    {
        return [
            [
                'nombre' => 'Usuarios',
                'ruta' => base_url('usuarios'),
                'descripcion' => 'Gestión CRUD operativa con permisos por usuario y cambio de estado.',
                'estado' => 'Disponible',
            ],
            [
                'nombre' => 'Permisos',
                'ruta' => base_url('permisos'),
                'descripcion' => 'Administración de permisos existentes por módulo para usuarios.',
                'estado' => 'Disponible',
            ],
            [
                'nombre' => 'Menú',
                'ruta' => base_url('menu'),
                'descripcion' => 'Vista de referencia del menú actual, sin edición ni reordenamiento persistente.',
                'estado' => 'Solo lectura',
            ],
        ];
    }

    private function obtenerLimitaciones(): array
    {
        return [
            'Configuración no persiste preferencias visuales ni idioma porque no existe endpoint de guardado ni modelo conectado al flujo actual.',
            'La pantalla funciona como landing administrativa para centralizar accesos reales del scope vigente.',
            'Cualquier ajuste editable debe implementarse de punta a punta antes de exponerse nuevamente en la UI.',
        ];
    }

    public function index()
    {
        $redir = $this->requerirAdminBackend('Solo un administrador puede acceder a configuración.');
        if ($redir) {
            return $redir;
        }

        $data = $this->crearDatosVistaAdmin('Configuración', 'configuracion', [
            'modulosAdministracion' => $this->obtenerModulosAdministracion(),
            'limitaciones' => $this->obtenerLimitaciones(),
        ]);

        return $this->renderAdminPage('configuracion', $data);
    }
}
