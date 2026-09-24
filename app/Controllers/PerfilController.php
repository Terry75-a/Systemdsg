<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\PersonaModel;
use App\Models\UsuarioModel;

class PerfilController extends BaseController
{
    protected PerfilModel $perfilModel;
    protected PersonaModel $personaModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilModel();
        $this->personaModel = new PersonaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    private function verificarAccesoPerfil(): mixed
    {
        return $this->verificarSesionActiva();
    }

    private function obtenerUsuarioAutenticado(): ?object
    {
        $idUsuario = (int) session()->get('id_usuario');

        if ($idUsuario <= 0) {
            return null;
        }

        return $this->usuarioModel->find($idUsuario);
    }

    private function obtenerPayloadUsuario(): array
    {
        $dataUsuario = [];

        $username = trim((string) $this->request->getPost('username'));
        if ($username !== '') {
            $dataUsuario['username'] = $username;
        }

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $dataUsuario['password'] = $password;
        }

        return $dataUsuario;
    }

    private function responderErrorActualizacion(string $message, int $status = 400, array $extra = [])
    {
        if ($this->esperaJson()) {
            return $this->responderJson(false, $message, $status, $extra);
        }

        session()->setFlashdata('msg', $message);
        session()->setFlashdata('tipo', $status >= 500 ? 'danger' : 'warning');

        return redirect()->back()->withInput();
    }

    private function responderActualizacionExitosa(string $message)
    {
        if ($this->esperaJson()) {
            return $this->responderJson(true, $message);
        }

        return $this->redirigirConFlash('perfil', $message);
    }

    public function index()
    {
        $redir = $this->verificarAccesoPerfil();
        if ($redir) {
            return $redir;
        }

        $usuario = $this->obtenerUsuarioAutenticado();
        if ($usuario === null) {
            return $this->redirigirConFlash('dashboard', 'No se pudo determinar el perfil autenticado.', 'warning');
        }

        $perfil = $this->perfilModel->getPerfil((int) $usuario->id_usuario);
        if ($perfil === null) {
            return $this->redirigirConFlash('dashboard', 'No se encontró el perfil del usuario autenticado.', 'warning');
        }

        $data = $this->crearDatosVistaAdmin('Mi Perfil', 'perfil', [
            'perfil' => $perfil,
        ]);

        return $this->renderAdminPage('perfil', $data);
    }

    public function actualizar()
    {
        $redir = $this->verificarAccesoPerfil();
        if ($redir) {
            return $redir;
        }

        $usuario = $this->obtenerUsuarioAutenticado();
        if ($usuario === null || empty($usuario->id_persona)) {
            return $this->responderErrorActualizacion('No se pudo determinar el perfil autenticado.', 403);
        }

        if (! $this->validate([
            'nombre' => 'required|min_length[2]|max_length[100]',
            'password' => 'permit_empty|min_length[6]',
        ])) {
            return $this->responderErrorActualizacion(
                'Hay errores de validación.',
                422,
                ['errors' => $this->validator->getErrors()]
            );
        }

        $dataPersona = [
            'nombre' => trim((string) $this->request->getPost('nombre')),
        ];
        $dataUsuario = $this->obtenerPayloadUsuario();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if (! $this->personaModel->update((int) $usuario->id_persona, $dataPersona)) {
                throw new \RuntimeException('Falló la actualización de la persona autenticada.');
            }

            if ($dataUsuario !== [] && ! $this->usuarioModel->update((int) $usuario->id_usuario, $dataUsuario)) {
                throw new \RuntimeException('Falló la actualización del usuario autenticado.');
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('La transacción del perfil quedó en estado inválido.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                '[perfil.actualizar] {exception}: {message} in {file}:{line}',
                [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return $this->responderErrorActualizacion('No se pudo actualizar el perfil', 500);
        }

        return $this->responderActualizacionExitosa('Perfil actualizado correctamente');
    }
}
