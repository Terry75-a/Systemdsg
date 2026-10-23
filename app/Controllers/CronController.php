<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class CronController extends BaseController
{
    /** GET /cron/faltas?token=... (o header X-Cron-Token) */
    public function faltas(): ResponseInterface
    {
        $tokenEsperado = (string) (env('CRON_TOKEN') ?: '');
        $token = (string) ($this->request->getGet('token')
            ?: $this->request->getHeaderLine('X-Cron-Token'));

        if ($tokenEsperado === '' || ! hash_equals($tokenEsperado, $token)) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'msj' => 'Token inválido']);
        }

        $fecha  = (string) ($this->request->getGet('fecha') ?: date('Y-m-d'));
        $generadas = (new \App\Services\FaltaService())->generarFaltas($fecha);

        return $this->response->setJSON([
            'ok'        => true,
            'fecha'     => $fecha,
            'generadas' => $generadas,
        ]);
    }
}
