<?php

namespace App\Libraries\Session;

use CodeIgniter\Session\Handlers\FileHandler;

/**
 * Handler de sesión para dev con doble servidor.
 *
 * Problema: XAMPP-Apache corre como `daemon` y `php spark serve`
 * corre como el usuario actual. FileHandler de CI4 hace chmod 0600,
 * entonces un archivo creado por uno es ilegible para el otro y
 * revienta con "Failed to open stream: Permission denied".
 *
 * Este handler:
 *  - Si encuentra un archivo stale ilegible (0600 de otro usuario),
 *    lo elimina (el directorio es 777, se puede) y crea sesión nueva
 *    en vez de lanzar 500.
 *  - Normaliza a 0666 los archivos que toca para que ambos usuarios
 *    puedan compartirlos en dev local.
 */
class SharedFileHandler extends FileHandler
{
    public function read($id): false|string
    {
        $path = $this->filePath . $id;

        if (is_file($path) && (! is_readable($path) || ! is_writable($path))) {
            // Archivo legacy 0600 de otro usuario OS. Borrable por perms del dir.
            @unlink($path);
        }

        $result = parent::read($id);

        if (is_file($path)) {
            @chmod($path, 0666);
        }

        return $result;
    }
}
