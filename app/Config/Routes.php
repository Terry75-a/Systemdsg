<?php

namespace Config;

use App\Controllers\TipoPlanController;
use Config\Services;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // IMPORTANTE: desactivar autorutas en CI4

// ══════════════════════════════════════════════════════════════
// RUTAS PÚBLICAS — Sin filtro de autenticación
// ══════════════════════════════════════════════════════════════

// Página web pública (landing page)
$routes->get('/', 'Home::index');
$routes->get('dsg', 'Home::dsg');
$routes->get('precio', 'Home::precio');
$routes->get('servicios', 'Home::servicios');
$routes->get('asisten-dsg', 'Home::asistencia');
$routes->get('precio-asisten', 'Home::precioAsisten');

// ══════════════════════════════════════════════════════════════
// AUTH — Registro, Login, Dashboard (sistema independiente JSON)
// ══════════════════════════════════════════════════════════════
$routes->get('registro', 'AuthController::registerForm');
$routes->post('auth/register', 'AuthController::register');
$routes->get('login-verde', 'AuthController::loginForm');
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('dashboard-verde', 'AuthController::dashboard');

// ══════════════════════════════════════════════════════════════
// DEV — Panel de desarrollador (superpoder)
// ══════════════════════════════════════════════════════════════
$routes->get('dios', 'DevController::index');
$routes->get('dios/admins', 'DevController::admins');
$routes->get('dios/codigos', 'DevController::codigos');
$routes->get('dios/empleados', 'DevController::empleados');
$routes->get('dios/estructura', 'DevController::estructura');
$routes->get('dios/perfil', 'DevController::perfil');
$routes->post('dios/update-password', 'DevController::updatePassword');
$routes->post('dios/create-admin', 'DevController::createAdmin');
$routes->post('dios/update-admin', 'DevController::updateAdmin');
$routes->post('dios/delete-admin', 'DevController::deleteAdmin');
$routes->post('dios/create-code', 'DevController::createCode');
$routes->post('dios/delete-code', 'DevController::deleteCode');
$routes->post('dios/delete-user', 'DevController::deleteUser');
$routes->get('registro-empleado', 'DevController::registerForm');
$routes->post('dios/register-employee', 'DevController::registerEmployee');

// ══════════════════════════════════════════════════════════════
// ADMIN — Panel de administrador
// ══════════════════════════════════════════════════════════════
$routes->get('admin', 'AdminController::dashboard');
$routes->get('admin/personal', 'AdminController::personal');
$routes->get('admin/asistencias', 'AdminController::asistencias');
$routes->get('admin/horarios', 'AdminController::horarios');
$routes->get('admin/incidencias', 'AdminController::incidencias');
$routes->get('admin/reportes', 'AdminController::reportes');
$routes->get('admin/configuracion', 'AdminController::configuracion');

// Admin CRUD
$routes->post('admin/create-employee', 'AdminController::createEmployee');
$routes->post('admin/update-employee', 'AdminController::updateEmployee');
$routes->post('admin/reset-password', 'AdminController::resetPassword');
$routes->post('admin/delete-user', 'AdminController::deleteUser');
$routes->post('admin/fire-employee', 'AdminController::fireEmployee');
$routes->post('admin/end-practicante', 'AdminController::endPracticante');
$routes->post('admin/create-code', 'AdminController::createCode');
$routes->post('admin/delete-code', 'AdminController::deleteCode');

// Admin Horarios
$routes->post('admin/create-schedule', 'AdminController::createSchedule');
$routes->post('admin/update-schedule', 'AdminController::updateSchedule');
$routes->post('admin/delete-schedule', 'AdminController::deleteSchedule');
$routes->post('admin/assign-schedule', 'AdminController::assignSchedule');

// Admin Asistencia
$routes->post('admin/update-attendance', 'AdminController::updateAttendance');

// Admin Incidencias
$routes->post('admin/create-incident', 'AdminController::createIncident');
$routes->post('admin/update-incident', 'AdminController::updateIncident');

// Admin Config
$routes->post('admin/save-config', 'AdminController::saveConfig');
$routes->post('enviar', 'EmailController::enviar'); // Formulario de contacto

// ══════════════════════════════════════════════════════════════
// EMPLEADO / PRACTICANTE — Dashboard propio
// ══════════════════════════════════════════════════════════════
$routes->get('mi-panel', 'EmployeeController::dashboard');
$routes->get('mi-panel/asistencias', 'EmployeeController::asistencias');
$routes->get('mi-panel/horario', 'EmployeeController::horario');
$routes->get('mi-panel/incidencias', 'EmployeeController::incidencias');
$routes->post('mi-panel/registrar', 'EmployeeController::registrar');
$routes->post('mi-panel/justificar-incidencia', 'EmployeeController::justificarIncidencia');
$routes->get('mi-panel/bio-session', 'EmployeeController::bioSession');
$routes->post('mi-panel/guardar-huella', 'EmployeeController::guardarHuella');
$routes->post('mi-panel/guardar-rostro', 'EmployeeController::guardarRostro');

// Demo pública (sin autenticación, solo para mostrar funcionalidades)
$routes->get('contacto-demo', 'ContactosController::index');



// Login / Logout — Sistema principal (LoginController)
$routes->get('login', 'LoginController::index');
$routes->post('login', 'LoginController::auth');
$routes->post('login/auth', 'LoginController::auth');
$routes->post('login/authentication', 'LoginController::auth');
$routes->post('logout', 'LoginController::logout');

// Asistencia: GET logout / auth/logout → panel de asistencia (login-verde)
$routes->get('logout', 'AuthController::logout');
$routes->get('auth/logout', 'AuthController::logout');

// Registro
$routes->get('registro', 'AuthController::registerForm');
$routes->post('registro', 'AuthController::register');
$routes->post('auth/register', 'AuthController::register');

// ══════════════════════════════════════════════════════════════
// RUTAS PROTEGIDAS — Solo sesión activa (filter: auth)
// ══════════════════════════════════════════════════════════════

// Dashboard
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Perfil del usuario logueado
$routes->get('perfil', 'PerfilController::index', ['filter' => 'auth']);
$routes->post('perfil/actualizar', 'PerfilController::actualizar', ['filter' => 'auth']);

// Calendario
$routes->get('calendario', 'CalendarioController::index', ['filter' => 'auth']);


//tipo plan 
$routes->get('tipo_plan', 'TipoPlanController::index', ['filter' => 'auth']);

// ── UBIGEO (API interna — requiere sesión para no quedar expuesta)
// CORRECCIÓN: se agrega 'filter' => 'auth' que faltaba
$routes->group('ubigeo', ['filter' => 'auth'], function ($routes) {
    $routes->get('departamentos', 'UbigeoController::obtenerdepartamento');
    $routes->get('provincias/(:any)', 'UbigeoController::obtenerProvincia/$1');
    $routes->get('distritos/(:any)', 'UbigeoController::obtenerDistrito/$1');
});

// ══════════════════════════════════════════════════════════════
// MÓDULO: CLIENTES
// CORRECCIÓN: 'CLientesAddController' → 'ClientesAddController'
//             'registar_cliente'      → 'registrar_cliente'
// ══════════════════════════════════════════════════════════════
//$routes->get('clientes',    'ClientesController::index',    ['filter' => 'auth']);
//$routes->get('clientesadd', 'ClientesAddController::index', ['filter' => 'auth']);

//$routes->group('clientes', ['filter' => 'auth'], function ($routes) {
//$routes->post('buscar-dni',  'ClientesAddController::buscarDni');
//$routes->post('buscar-ruc',  'ClientesAddController::buscarRuc');   // ← CORREGIDO typo 'CLientes'
//$routes->post('registrar',   'ClientesAddController::registrar_cliente'); // ← CORREGIDO 'registar'
// NUEVAS — faltaban estas rutas para completar el CRUD
//$routes->get( 'obtener/(:num)',    'ClientesController::obtener/$1');
//$routes->post('actualizar/(:num)', 'ClientesController::actualizar/$1');
//$routes->post('estado/(:num)',     'ClientesController::cambiarEstado/$1');
//});

// ══════════════════════════════════════════════════════════════
// MÓDULO: PERSONAS
// ══════════════════════════════════════════════════════════════
// Rutas sueltas (No pertenecen al controlador Personas ni usan el prefijo directo)
$routes->get('personasadd', 'PersonasAddController::index', ['filter' => 'auth']);

$routes->get('tipo_planadd', 'TipoplanaddController::index', ['filter' => 'auth']);

$routes->post('tipo_plan/guardar', 'TipoplanaddController::guardar', ['filter' => 'auth']);

$routes->get('tipo_plan/listar', 'TipoPlanController::listar', ['filter' => 'auth']);

$routes->post('tipo_plan/eliminar', 'TipoPlanController::eliminar', ['filter' => 'auth']);

$routes->get('planes', 'PlanesController::index', ['filter' => 'auth']);
    $routes->get('planes/listar', 'PlanesController::listar', ['filter' => 'auth']);
    $routes->post('planes/guardar', 'PlanesController::guardar', ['filter' => 'auth']);
    $routes->post('planes/eliminar', 'PlanesController::eliminar', ['filter' => 'auth']);



// GRUPO DEFINITIVO PARA PERSONAS (Filtro 'auth' aplicado a todo el grupo)
$routes->group('personas', ['filter' => 'auth'], function ($routes) {


    
    // Rutas GET (Se transforman automáticamente en personas, personas/listar, etc.)
    $routes->get('/', 'PersonasController::index');
    $routes->get('listar', 'PersonasController::listar');
    $routes->get('planesDisponibles', 'PersonasController::planesDisponibles');
    $routes->get('mantenerPlanSeleccionado', 'PersonasController::mantenerPlanSeleccionado');
    $routes->get('tipoplan', 'PersonasController::tipoplan');
    
    $routes->get('obtenerNombrePersona', 'PersonasController::obtenerNombrePersona');
     $routes->get('obtenerDetalle', 'PersonasController::obtenerDetalle');

    // Rutas POST (Aquí se arregla tu AJAX de Asignarplanes)
    $routes->post('Asignarplanes', 'PersonasController::Asignarplanes');
    $routes->post('buscar-dni', 'PersonasAddController::buscarDni');
    $routes->post('buscar-ruc', 'PersonasAddController::buscarRuc');
    
    // Otras rutas
    $routes->match(['GET', 'POST'], 'eliminar/(:num)', 'PersonasController::eliminar/$1');
});

// ══════════════════════════════════════════════════════════════
// MÓDULO: USUARIOS
// CORRECCIÓN: 'Usuarios' en mayúscula → 'usuarios' en minúscula (convención CI4)
// NUEVAS: registrar, obtener, actualizar, permisos, estado
// ══════════════════════════════════════════════════════════════
$routes->group('usuarios', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'UsuariosController::index');
    $routes->get('listar', 'UsuariosController::listar');     // JSON para DataTables
    $routes->post('registrar', 'UsuariosController::registrar'); // ← NUEVA
    $routes->get('obtener/(:num)', 'UsuariosController::obtener/$1'); // ← NUEVA
    $routes->post('actualizar/(:num)', 'UsuariosController::actualizar/$1'); // ← NUEVA
    $routes->post('permisos/(:num)', 'UsuariosController::asignarPermisos/$1'); // ← NUEVA
    $routes->post('estado/(:num)', 'UsuariosController::cambiarEstado/$1'); // ← NUEVA
});

// ══════════════════════════════════════════════════════════════
// MÓDULO: PERMISOS
// CORRECCIÓN: 'Permisos' → 'permisos' (minúscula)
// NUEVAS: guardar y eliminar permisos
// ══════════════════════════════════════════════════════════════
$routes->group('permisos', ['filter' => 'auth'], function ($routes) {

    $routes->get('', 'PermisosController::index');
    $routes->get('listar', 'PermisosController::listar');     // JSON para DataTables
    $routes->post('guardar', 'PermisosController::guardar');  // Crear nuevo
    $routes->get('obtener/(:num)', 'PermisosController::obtener/$1'); // Cargar datos para editar
    $routes->post('editar/(:num)', 'PermisosController::editar/$1');  // Guardar cambios
    $routes->post('eliminar/(:num)', 'PermisosController::eliminar/$1'); // Eliminar
    $routes->get('usuario/(:num)', 'PermisosController::porUsuario/$1'); // Permisos de una persona

});

// ══════════════════════════════════════════════════════════════
// MÓDULO: MENÚS
// CORRECCIÓN: 'Menu' → 'menu' (minúscula)
// NUEVAS: guardar y cambiar estado de menú
// ══════════════════════════════════════════════════════════════
$routes->group('menu', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'MenuController::index');
    $routes->post('guardar', 'MenuController::guardar'); // ← NUEVA
    $routes->post('estado/(:num)', 'MenuController::cambiarEstado/$1'); // ← NUEVA
});

// ══════════════════════════════════════════════════════════════
// MÓDULO: CONFIGURACIÓN
// Solo accesible por administrador (id_rol = 1)
// ══════════════════════════════════════════════════════════════
$routes->get('configuracion', 'ConfiguracionController::index', ['filter' => 'auth:1']);

// ══════════════════════════════════════════════════════════════
// PLANTILLAS para futuros módulos del ERP
// Descomenta cuando crees los controladores correspondientes
// ══════════════════════════════════════════════════════════════

// ── PRODUCTOS / INVENTARIO
// $routes->group('productos', ['filter' => 'auth'], function ($routes) {
//     $routes->get('',                    'ProductosController::index');
//     $routes->post('registrar',          'ProductosController::registrar');
//     $routes->get('obtener/(:num)',      'ProductosController::obtener/$1');
//     $routes->post('actualizar/(:num)',  'ProductosController::actualizar/$1');
//     $routes->post('estado/(:num)',      'ProductosController::cambiarEstado/$1');
// });

// ── VENTAS (roles: admin=1, vendedor=2)
// $routes->group('ventas', ['filter' => 'auth:1,2'], function ($routes) {
//     $routes->get('',                    'VentasController::index');
//     $routes->post('registrar',          'VentasController::registrar');
//     $routes->get('obtener/(:num)',      'VentasController::obtener/$1');
// });

// ── REPORTES (roles: admin=1, contador=4)
// $routes->group('reportes', ['filter' => 'auth:1,4'], function ($routes) {
//     $routes->get('',            'ReportesController::index');
//     $routes->get('ventas',      'ReportesController::ventas');
//     $routes->get('inventario',  'ReportesController::inventario');
// });

$routes->get('buscar', 'PersonasController::buscar', ['filter' => 'auth']);
$routes->post('personas/guardar-todo', 'PersonasAddController::guardar_todo', ['filter' => 'auth']);

$routes->get('personasadd/(:num)?', 'PersonasAddController::index/$1', ['filter' => 'auth']);