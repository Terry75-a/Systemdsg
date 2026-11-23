<?php

namespace Config;

use App\Services\Permisos\PermisoWriterService;
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

    public static function webpush(bool $getShared = true): \App\Services\WebPushService
    {
        if ($getShared) {
            return static::getSharedInstance('webpush');
        }

        return new \App\Services\WebPushService();
    }

    public static function pushSubscription(bool $getShared = true): \App\Models\PushSubscriptionModel
    {
        if ($getShared) {
            return static::getSharedInstance('pushSubscription');
        }

        return new \App\Models\PushSubscriptionModel();
    }
}
