<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\PersonaModel;
use App\Support\ClientesCatalog;

class ClientesController extends BaseController
{
    protected $db;
    protected ClientesModel $clienteModel;
    protected PersonaModel $personaModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->clienteModel = new ClientesModel();
        $this->personaModel = new PersonaModel();
    }

    private function verificarSesion(): mixed
    {
        return $this->verificarSesionActiva();
    }

    private function requerirPermisoClientes(string $accion): mixed
    {
        helper('auth');

        return requerir_permiso(ClientesCatalog::MENU_ID, $accion);
    }

    /**
     * @return array<string, string>
     */
    private function reglasActualizacion(): array
    {
        return [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido_paterno' => 'required|max_length[50]',
            'apellido_materno' => 'permit_empty|max_length[50]',
            'dni' => 'required|exact_length[8]|numeric',
            'telefono' => 'permit_empty|max_length[20]',
            'correo' => 'required|valid_email|max_length[100]',
            'id_empresa' => 'permit_empty|integer|greater_than[0]',
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function erroresActualizacion(): array
    {
        return [
            'nombre' => [
                'required' => 'El nombre es obligatorio.',
            ],
            'apellido_paterno' => [
                'required' => 'El apellido paterno es obligatorio.',
            ],
            'dni' => [
                'required' => 'El DNI es obligatorio.',
                'exact_length' => 'El DNI debe tener 8 dígitos.',
                'numeric' => 'El DNI debe contener solo números.',
            ],
            'correo' => [
                'required' => 'El correo es obligatorio.',
                'valid_email' => 'Ingresá un correo válido.',
            ],
            'id_empresa' => [
                'integer' => 'La empresa seleccionada no es válida.',
                'greater_than' => 'La empresa seleccionada no es válida.',
            ],
        ];
    }

    private function normalizarTexto(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function index()
    {
        $redir = $this->verificarSesion();
        if ($redir) {
            return $redir;
        }

        $permiso = $this->requerirPermisoClientes('read');
        if ($permiso) {
            return $permiso;
        }

        $data = $this->crearDatosVistaAdmin('Gestión de Clientes', 'clientes', [
            'clientes' => $this->clienteModel->obtenerClientesConDatos(),
        ]);

        $data['totalClientes'] = count($data['clientes']);

        return $this->renderAdminPage('clientes/clientes', $data);
    }

    public function obtener(int $id)
    {
        $redir = $this->verificarSesion();
        if ($redir) {
            return $redir;
        }

        $permiso = $this->requerirPermisoClientes('read');
        if ($permiso) {
            return $permiso;
        }

        try {
            $cliente = $this->clienteModel->obtenerDetalleClientePorId($id);

            if ($cliente === null) {
                return $this->responderNoEncontrado('Cliente no encontrado.');
            }

            return $this->responderJson(true, 'Cliente cargado correctamente.', 200, [
                'data' => $cliente,
            ]);
        } catch (\Throwable $e) {
            return $this->responderExcepcion($e, 'No se pudo cargar el cliente.', 'clientes.obtener');
        }
    }

    public function actualizar(int $id)
    {
        $redir = $this->verificarSesion();
        if ($redir) {
            return $redir;
        }

        $permiso = $this->requerirPermisoClientes('update');
        if ($permiso) {
            return $permiso;
        }

        $clienteActual = $this->clienteModel->obtenerClientePorId($id);
        if ($clienteActual === null) {
            return $this->responderNoEncontrado('Cliente no encontrado.');
        }

        if (! $this->validate($this->reglasActualizacion(), $this->erroresActualizacion())) {
            return $this->responderValidacion($this->validator?->getErrors() ?? []);
        }

        $dni = trim((string) $this->request->getPost('dni'));
        $correo = trim((string) $this->request->getPost('correo'));

        if ($this->personaModel->dniExiste($dni, $id)) {
            return $this->responderValidacion([
                'dni' => 'Ya existe una persona registrada con ese DNI.',
            ]);
        }

        $correoDuplicado = $this->db
            ->table('personas')
            ->where('correo', $correo)
            ->where('id_persona !=', $id)
            ->countAllResults();

        if ($correoDuplicado > 0) {
            return $this->responderValidacion([
                'correo' => 'Ya existe una persona registrada con ese correo.',
            ]);
        }

        $personaPayload = [
            'nombre' => trim((string) $this->request->getPost('nombre')),
            'apellido_paterno' => trim((string) $this->request->getPost('apellido_paterno')),
            'apellido_materno' => $this->normalizarTexto($this->request->getPost('apellido_materno')),
            'dni' => $dni,
            'telefono' => $this->normalizarTexto($this->request->getPost('telefono')),
            'correo' => $correo,
        ];

        $clientePayload = [
            'id_empresa' => $this->normalizarTexto($this->request->getPost('id_empresa')),
        ];

        $this->db->transBegin();

        try {
            if (! $this->personaModel->update($id, $personaPayload)) {
                throw new \RuntimeException('No se pudo actualizar la persona del cliente.');
            }

            if (! $this->clienteModel->update($id, $clientePayload)) {
                throw new \RuntimeException('No se pudo actualizar la relación comercial del cliente.');
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('La transacción quedó en estado inválido.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responderExcepcion($e, 'No se pudo actualizar el cliente.', 'clientes.actualizar');
        }

        $this->db->transCommit();

        return $this->responderJson(true, 'Cliente actualizado correctamente.', 200, [
            'data' => $this->clienteModel->obtenerDetalleClientePorId($id),
        ]);
    }

    public function cambiarEstado(int $id)
    {
        $redir = $this->verificarSesion();
        if ($redir) {
            return $redir;
        }

        $permiso = $this->requerirPermisoClientes('update');
        if ($permiso) {
            return $permiso;
        }

        $cliente = $this->clienteModel->obtenerClientePorId($id);
        if ($cliente === null) {
            return $this->responderNoEncontrado('Cliente no encontrado.');
        }

        $nuevoEstado = ((int) ($cliente->estado ?? 1)) === 1 ? 0 : 1;

        try {
            if (! $this->clienteModel->actualizarEstadoPorId($id, $nuevoEstado)) {
                throw new \RuntimeException('No se pudo actualizar el estado del cliente.');
            }

            return $this->responderJson(true, 'Estado del cliente actualizado correctamente.', 200, [
                'data' => [
                    'id_cliente' => $id,
                    'estado' => $nuevoEstado,
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->responderExcepcion($e, 'No se pudo actualizar el estado del cliente.', 'clientes.estado');
        }
    }
}



