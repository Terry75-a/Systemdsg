<?php

namespace App\Controllers;

use App\Models\ClientesaddModel;
use App\Models\ClientesModel;
use App\Services\Clientes\ClienteFormService;
use App\Services\Clientes\ClienteValidationService;
use App\Services\External\DocumentoLookupService;
use App\Support\ClientesCatalog;

class ClientesAddController extends BaseController
{
    protected $db;
    protected DocumentoLookupService $documentoLookupService;
    protected ClienteFormService $clienteFormService;
    protected ClienteValidationService $clienteValidationService;
    protected ClientesaddModel $personaModel;
    protected ClientesModel $clienteModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->documentoLookupService = service('documentoLookup');
        $this->clienteFormService = service('clienteForm');
        $this->clienteValidationService = service('clienteValidation');
        $this->personaModel = new ClientesaddModel();
        $this->clienteModel = new ClientesModel();
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

    private function validarAccesoClientes(): mixed
    {
        $redir = $this->verificarSesion();
        if ($redir) {
            return $redir;
        }

        return $this->requerirPermisoClientes('insert');
    }

    private function obtenerPrimerErrorValidacion(): string
    {
        $errors = $this->validator?->getErrors() ?? [];

        if ($errors === []) {
            return 'Hay errores de validación al registrar el cliente.';
        }

        return (string) array_values($errors)[0];
    }

    private function responderErrorRegistro(string $message, string $type = 'warning')
    {
        if ($this->esperaJson()) {
            $status = $type === 'danger' ? 500 : 422;

            return $this->responderJson(false, $message, $status, [
                'errors' => $this->validator?->getErrors() ?? [],
            ]);
        }

        session()->setFlashdata('msg', $message);
        session()->setFlashdata('tipo', $type);

        return redirect()->to(base_url('clientes/add'))->withInput();
    }

    private function responderRegistroExitoso(): mixed
    {
        if ($this->esperaJson()) {
            return $this->responderJson(true, 'Cliente registrado correctamente.', 201, [
                'redirect' => base_url('clientes'),
            ]);
        }

        return $this->redirigirConFlash('clientes', 'Cliente registrado correctamente.');
    }

    /**
     * @return array<int, object>
     */
    private function obtenerDepartamentos(): array
    {
        try {
            return $this->db
                ->table('ubigeo_peru_departments')
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultObject();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function index()
    {
        $acceso = $this->validarAccesoClientes();
        if ($acceso) {
            return $acceso;
        }

        $data = $this->crearDatosVistaAdmin('Agregar Cliente', 'clientes', [
            'departamentos' => $this->obtenerDepartamentos(),
        ]);

        return $this->renderAdminPage('clientes/clientesadd', $data);
    }

    public function buscarDni()
    {
        $acceso = $this->validarAccesoClientes();
        if ($acceso) {
            return $acceso;
        }

        $serviceResult = $this->documentoLookupService->buscarDni((string) $this->request->getPost('dni'));

        return $this->responderResultadoServicio($serviceResult);
    }

    public function buscarRuc()
    {
        $acceso = $this->validarAccesoClientes();
        if ($acceso) {
            return $acceso;
        }

        $serviceResult = $this->documentoLookupService->buscarRuc((string) $this->request->getPost('ruc'));

        return $this->responderResultadoServicio($serviceResult);
    }

    public function registrar_cliente()
    {
        $acceso = $this->validarAccesoClientes();
        if ($acceso) {
            return $acceso;
        }

        if (! $this->validate(
            $this->clienteValidationService->reglasRegistro(),
            $this->clienteValidationService->erroresRegistro()
        )) {
            return $this->responderErrorRegistro($this->obtenerPrimerErrorValidacion());
        }

        $input = $this->request->getPost() ?? [];
        $dni = trim((string) ($input['dni'] ?? ''));
        $correo = trim((string) ($input['correo'] ?? ''));

        $dataPersona = $this->clienteFormService->construirDataPersona($input, $dni, $correo);
        $dataEmpresa = $this->clienteFormService->construirDataEmpresa($input);

        $errorEmpresa = $this->clienteFormService->validarRegistroEmpresa($dataEmpresa);

        if ($errorEmpresa !== null) {
            return $this->responderErrorRegistro($errorEmpresa);
        }

        $this->db->transBegin();

        try {
            if (! $this->personaModel->insert($dataPersona)) {
                throw new \RuntimeException('No se pudo guardar la persona del cliente.');
            }

            $idGenerado = (int) $this->personaModel->getInsertID();
            $idEmpresa = $this->clienteFormService->resolverEmpresaId($dataEmpresa, $idGenerado);

            $dataCliente = [
                'id_cliente' => $idGenerado,
                'id_persona' => $idGenerado,
                'id_empresa' => $idEmpresa,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if (! $this->clienteModel->insert($dataCliente)) {
                throw new \RuntimeException('No se pudo registrar la relación comercial del cliente.');
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('La transacción quedó en estado inválido.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();

            log_message(
                'error',
                '[clientes.registrar] {exception}: {message} in {file}:{line}',
                [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return $this->responderErrorRegistro('No se pudo registrar el cliente.', 'danger');
        }

        $this->db->transCommit();

        return $this->responderRegistroExitoso();
    }

    public function registar_cliente()
    {
        return $this->registrar_cliente();
    }
}
