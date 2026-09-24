<?php

namespace Config;

use App\Services\External\Http\CurlDocumentoLookupHttpClient;
use App\Services\External\Http\DocumentoLookupHttpClientInterface;
use App\Services\External\DocumentoLookupService;
use App\Services\Clientes\ClienteFormService;
use App\Services\Clientes\ClienteValidationService;
use App\Services\Permisos\PermisoWriterService;
use App\Services\Personas\PersonaFormService;
use App\Services\Personas\PersonaValidationService;
use App\Services\Usuarios\UsuarioWriterService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function documentoLookup(bool $getShared = true): DocumentoLookupService
    {
        if ($getShared) {
            return static::getSharedInstance('documentoLookup');
        }

        return new DocumentoLookupService(
            config('ExternalServices'),
            static::documentoLookupHttpClient(false)
        );
    }

    public static function documentoLookupHttpClient(bool $getShared = true): DocumentoLookupHttpClientInterface
    {
        if ($getShared) {
            return static::getSharedInstance('documentoLookupHttpClient');
        }

        return new CurlDocumentoLookupHttpClient();
    }

    public static function permisoWriter(bool $getShared = true): PermisoWriterService
    {
        if ($getShared) {
            return static::getSharedInstance('permisoWriter');
        }

        return new PermisoWriterService();
    }

    public static function usuarioWriter(bool $getShared = true): UsuarioWriterService
    {
        if ($getShared) {
            return static::getSharedInstance('usuarioWriter');
        }

        return new UsuarioWriterService();
    }

    public static function personaForm(bool $getShared = true): PersonaFormService
    {
        if ($getShared) {
            return static::getSharedInstance('personaForm');
        }

        return new PersonaFormService();
    }

    public static function personaValidation(bool $getShared = true): PersonaValidationService
    {
        if ($getShared) {
            return static::getSharedInstance('personaValidation');
        }

        return new PersonaValidationService();
    }

    public static function clienteForm(bool $getShared = true): ClienteFormService
    {
        if ($getShared) {
            return static::getSharedInstance('clienteForm');
        }

        return new ClienteFormService();
    }

    public static function clienteValidation(bool $getShared = true): ClienteValidationService
    {
        if ($getShared) {
            return static::getSharedInstance('clienteValidation');
        }

        return new ClienteValidationService();
    }
}
