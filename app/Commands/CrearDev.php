<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Crea la primera cuenta Dev (superadmin) de Asisten.
 *
 * Sirve cuando la base se creó desde cero con las migraciones y todavía no
 * hay con quién entrar. Pide los datos por consola (o se pasan como opciones).
 *
 *   php spark asisten:crear-dev
 *   php spark asisten:crear-dev --nombre "Ana Pérez" --email ana@dsg.pe --dni 12345678
 */
class CrearDev extends BaseCommand
{
    protected $group       = 'Asisten';
    protected $name        = 'asisten:crear-dev';
    protected $description = 'Crea la cuenta Dev (superadmin) para entrar a Asisten en una base nueva.';
    protected $usage       = 'asisten:crear-dev [--nombre "..."] [--email ...] [--dni ...] [--password ...]';
    protected $options     = [
        '--nombre'   => 'Nombre completo',
        '--email'    => 'Correo con el que entrará',
        '--dni'      => 'DNI de 8 dígitos',
        '--password' => 'Contraseña (mín. 8 caracteres). Si no se pasa, se pide.',
    ];

    public function run(array $params)
    {
        $db = db_connect();
        if (! $db->tableExists('users')) {
            CLI::error('La tabla users no existe. Corre primero: php spark migrate');
            return EXIT_ERROR;
        }

        $model = new UserModel();
        if ($dev = $model->findDev()) {
            CLI::write('Ya existe una cuenta Dev: ' . $dev['email'], 'yellow');
            if (CLI::prompt('¿Crear otra de todas formas?', ['n', 's']) !== 's') {
                return EXIT_SUCCESS;
            }
        }

        $nombre = trim((string) (CLI::getOption('nombre') ?? CLI::prompt('Nombre completo', null, 'required')));
        $email  = mb_strtolower(trim((string) (CLI::getOption('email') ?? CLI::prompt('Correo', null, 'required|valid_email'))));
        $dni    = trim((string) (CLI::getOption('dni') ?? CLI::prompt('DNI (8 dígitos)', null, 'required')));
        $pass   = (string) (CLI::getOption('password') ?? CLI::prompt('Contraseña (mín. 8 caracteres)', null, 'required'));

        $errores = [];
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es válido.';
        if (! preg_match('/^\d{8}$/', $dni))             $errores[] = 'El DNI debe tener 8 dígitos.';
        if (mb_strlen($pass) < 8)                        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        if ($model->findByEmail($email))                 $errores[] = 'Ese correo ya está registrado.';
        if ($model->findByDni($dni))                     $errores[] = 'Ese DNI ya está registrado.';
        if ($errores) {
            foreach ($errores as $e) CLI::error($e);
            return EXIT_ERROR;
        }

        $id = $model->insert([
            'name' => $nombre, 'email' => $email, 'dni' => $dni,
            'password' => $pass, 'role' => 'Dev', 'estado' => 'Activo',
        ]);
        if (! $id) {
            CLI::error('No se pudo crear: ' . implode(' ', $model->errors()));
            return EXIT_ERROR;
        }

        CLI::write('Cuenta Dev creada (id ' . $id . '). Entra en /login-verde con ' . $email . '.', 'green');
        return EXIT_SUCCESS;
    }
}
