<?php
// app/Helpers/auth_helper.php

// ══════════════════════════════════════════════════════════════
// VERIFICACIÓN DE SESIÓN
// ══════════════════════════════════════════════════════════════

/**
 * ¿El usuario tiene sesión activa?
 */
if (!function_exists('esta_logueado')) {
    function esta_logueado(): bool
    {
        return (bool) session()->get('logged_in');
    }
}

/**
 * Obtiene un dato específico de la sesión del usuario
 * Uso: dato_sesion('nombre'), dato_sesion('rol_nombre')
 */
if (!function_exists('dato_sesion')) {
    function dato_sesion(string $clave): mixed
    {
        return session()->get($clave);
    }
}

// ══════════════════════════════════════════════════════════════
// VERIFICACIÓN DE PERMISOS (Lee desde sesión, sin consultar BD)
// ══════════════════════════════════════════════════════════════

/**
 * Verifica si el usuario tiene un permiso específico en un menú
 * $accion puede ser: 'read', 'insert', 'update', 'delete'
 *
 * Uso: tiene_permiso(2, 'read')
 */
if (!function_exists('tiene_permiso')) {
    function tiene_permiso(int $menu_id, string $accion): bool
    {
        if (es_admin()) {
            return true;
        }

        $permisos = session()->get('permisos'); // array indexado por menu_id
        return !empty($permisos[$menu_id]) && ($permisos[$menu_id]->$accion ?? 0) == 1;
    }
}

/**
 * Atajos para cada tipo de permiso
 * Uso: puede_leer(2), puede_insertar(2), puede_editar(2), puede_eliminar(2)
 */
if (!function_exists('puede_leer')) {
    function puede_leer(int $menu_id): bool
    {
        return tiene_permiso($menu_id, 'read');
    }
}

if (!function_exists('puede_insertar')) {
    function puede_insertar(int $menu_id): bool
    {
        return tiene_permiso($menu_id, 'insert');
    }
}

if (!function_exists('puede_editar')) {
    function puede_editar(int $menu_id): bool
    {
        return tiene_permiso($menu_id, 'update');
    }
}

if (!function_exists('puede_eliminar')) {
    function puede_eliminar(int $menu_id): bool
    {
        return tiene_permiso($menu_id, 'delete');
    }
}

// ══════════════════════════════════════════════════════════════
// VERIFICACIÓN DE ROL
// ══════════════════════════════════════════════════════════════

/**
 * Verifica si el usuario tiene un rol específico por id
 * Uso: tiene_rol(1) → true si es admin
 */
if (!function_exists('tiene_rol')) {
    function tiene_rol(int $id_rol): bool
    {
        return (int) session()->get('id_rol') === $id_rol;
    }
}

/**
 * Verifica si el usuario es administrador (id_rol = 1 según tu BD)
 * Uso: es_admin()
 */
if (!function_exists('es_admin')) {
    function es_admin(): bool
    {
        return tiene_rol(1); // rol 'admin' tiene id_rol = 1 en tu tabla roles
    }
}

// ══════════════════════════════════════════════════════════════
// REDIRECCIONAMIENTO DE PROTECCIÓN
// ══════════════════════════════════════════════════════════════

/**
 * Redirige al login si no hay sesión activa
 * Uso: requerir_sesion() al inicio de métodos en controladores sin filtro
 */
if (!function_exists('requerir_sesion')) {
    function requerir_sesion(): mixed
    {
        if (!esta_logueado()) {
            return respuesta_auth_requerida();
        }
        return null;
    }
}

/**
 * Detecta si la petición espera una respuesta JSON/AJAX.
 */
if (!function_exists('peticion_auth_es_ajax')) {
    function peticion_auth_es_ajax(): bool
    {
        $request = service('request');

        return $request->isAJAX()
            || str_contains(strtolower($request->getHeaderLine('Accept')), 'application/json');
    }
}

/**
 * Respuesta uniforme cuando la sesión no existe o expiró.
 */
if (!function_exists('respuesta_auth_requerida')) {
    function respuesta_auth_requerida(string $message = 'La sesión expiró. Volvé a iniciar sesión.'): mixed
    {
        if (peticion_auth_es_ajax()) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => $message,
                ]);
        }

        session()->set('redirect_url', current_url());

        return redirect()->to(base_url('login'));
    }
}

/**
 * Respuesta uniforme cuando el usuario no tiene autorización suficiente.
 */
if (!function_exists('respuesta_auth_denegada')) {
    function respuesta_auth_denegada(string $message = 'No tienes permiso para realizar esta acción', int $status = 403): mixed
    {
        if (peticion_auth_es_ajax()) {
            return service('response')
                ->setStatusCode($status)
                ->setJSON([
                    'success' => false,
                    'message' => $message,
                ]);
        }

        session()->setFlashdata('msg', $message);
        session()->setFlashdata('tipo', 'warning');

        return redirect()->to(base_url('dashboard'));
    }
}

/**
 * Endurece acciones críticas de administración: solo admin real.
 */
if (!function_exists('requerir_admin')) {
    function requerir_admin(string $message = 'Solo un administrador puede realizar esta acción.'): mixed
    {
        if (!esta_logueado()) {
            return respuesta_auth_requerida();
        }

        if (!es_admin()) {
            return respuesta_auth_denegada($message);
        }

        return null;
    }
}

/**
 * Redirige con mensaje de acceso denegado si no tiene el permiso requerido
 * Uso: requerir_permiso(2, 'insert')
 */
if (!function_exists('requerir_permiso')) {
    function requerir_permiso(int $menu_id, string $accion = 'read'): mixed
    {
        if (!esta_logueado()) {
            return respuesta_auth_requerida();
        }

        if (!tiene_permiso($menu_id, $accion)) {
            return respuesta_auth_denegada('No tienes permiso para realizar esta acción');
        }

        return null;
    }
}
