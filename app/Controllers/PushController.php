<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class PushController extends BaseController
{
    public function clave(): ResponseInterface
    {
        $svc = service('webpush');
        return $this->response->setJSON([
            'ok'    => $svc->isConfigured(),
            'clave' => $svc->publicKey(),
        ]);
    }

    public function suscribir(): ResponseInterface
    {
        $userId = (int) session('user_id');
        if (! $userId) {
            return $this->response->setStatusCode(401)->setJSON(['ok' => false, 'msj' => 'No autenticado']);
        }

        $json = json_decode((string) file_get_contents('php://input'), true);
        $endpoint = is_array($json) ? trim((string) ($json['endpoint'] ?? '')) : '';
        $p256dh   = is_array($json) ? trim((string) ($json['keys']['p256dh'] ?? '')) : '';
        $auth     = is_array($json) ? trim((string) ($json['keys']['auth'] ?? '')) : '';

        if ($endpoint === '' || $p256dh === '' || $auth === '' || strlen($endpoint) > 480) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Suscripción inválida']);
        }

        service('pushSubscription')->upsertParaUsuario($userId, $endpoint, $p256dh, $auth);
        return $this->response->setJSON(['ok' => true]);
    }

    public function cancelar(): ResponseInterface
    {
        $userId = (int) session('user_id');
        if (! $userId) {
            return $this->response->setStatusCode(401)->setJSON(['ok' => false, 'msj' => 'No autenticado']);
        }
        $json = json_decode((string) file_get_contents('php://input'), true);
        $endpoint = is_array($json) ? trim((string) ($json['endpoint'] ?? '')) : '';
        if ($endpoint === '') {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Endpoint vacío']);
        }
        service('pushSubscription')->quitar($endpoint, $userId);
        return $this->response->setJSON(['ok' => true]);
    }
}
