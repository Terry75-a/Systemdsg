<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Credenciales de servicios externos (SMTP del formulario de contacto).
 *
 * Se leen desde variables de entorno (.env) para no versionar secretos:
 *
 *   SMTP_HOST=
 *   SMTP_PORT=587
 *   SMTP_ENCRYPTION=tls
 *   SMTP_USERNAME=
 *   SMTP_PASSWORD=
 *   SMTP_FROM_EMAIL=
 *   SMTP_FROM_NAME="DSG Peru"
 *   SMTP_TO_EMAIL=
 *   SMTP_TO_NAME="DSG Peru"
 *
 * Si alguna clave obligatoria viene vacía, el formulario responde con un
 * mensaje de "servicio no configurado" en vez de fallar con error 500.
 */
class ExternalServices extends BaseConfig
{
    public string $smtpHost        = '';
    public string $smtpPort        = '587';
    public string $smtpEncryption  = 'tls';
    public string $smtpUsername    = '';
    public string $smtpPassword    = '';
    public string $smtpFromEmail   = '';
    public string $smtpFromName    = 'DSG Peru';
    public string $smtpToEmail     = '';
    public string $smtpToName      = 'DSG Peru';

    public function __construct()
    {
        parent::__construct();

        $map = [
            'smtpHost'       => 'SMTP_HOST',
            'smtpPort'       => 'SMTP_PORT',
            'smtpEncryption' => 'SMTP_ENCRYPTION',
            'smtpUsername'   => 'SMTP_USERNAME',
            'smtpPassword'   => 'SMTP_PASSWORD',
            'smtpFromEmail'  => 'SMTP_FROM_EMAIL',
            'smtpFromName'   => 'SMTP_FROM_NAME',
            'smtpToEmail'    => 'SMTP_TO_EMAIL',
            'smtpToName'     => 'SMTP_TO_NAME',
        ];

        foreach ($map as $property => $envKey) {
            $value = env($envKey, null);
            if ($value !== null && $value !== false && $value !== '') {
                $this->{$property} = (string) $value;
            }
        }
    }
}
