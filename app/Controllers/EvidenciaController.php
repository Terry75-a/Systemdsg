<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Sirve evidencias biométricas (rostros / evidencias) que viven en
 * writable/uploads, nunca en public/. Requiere sesión.
 */
class EvidenciaController extends BaseController
{
    private const TIPOS = ['rostros', 'evidencias'];

    public function ver(string $tipo, string $nombre): ResponseInterface
    {
        $userId = (int) session('user_id');
        $rol    = (string) session('role');
        if (! $userId) {
            return $this->response->setStatusCode(401)->setBody('No autorizado');
        }

        // Sin traversal: sólo nombre plano seguro.
        if (! in_array($tipo, self::TIPOS, true)
            || ! preg_match('/^user_\d+_[A-Za-z0-9_\-]+\.(jpg|png|webp)$/', $nombre)) {
            return $this->response->setStatusCode(404)->setBody('No encontrado');
        }

        $ruta = WRITEPATH . 'uploads/' . $tipo . '/' . $nombre;
        if (! is_file($ruta)) {
            return $this->response->setStatusCode(404)->setBody('No encontrado');
        }

        // Empleado: sólo sus propios archivos. Admin: cualquiera.
        $esAdmin = in_array($rol, ['Admin', 'admin'], true);
        if (! $esAdmin) {
            $own = 'user_' . $userId . '_';
            $mine = str_starts_with($nombre, $own);
            if (! $mine) {
                // Evidencias de asistencia del propio usuario (por si el nombre no coincide)
                $att = (new \App\Models\AttendanceModel())
                    ->where('user_id', $userId)
                    ->like('evidencia', $nombre)
                    ->first();
                if (! $att) {
                    return $this->response->setStatusCode(403)->setBody('Sin permiso');
                }
            }
        }

        $mime = match (pathinfo($nombre, PATHINFO_EXTENSION)) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody((string) file_get_contents($ruta));
    }
}
