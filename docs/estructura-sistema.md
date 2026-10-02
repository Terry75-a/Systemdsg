# Estructura del sistema DSG Perú — documentación detallada

> Visión completa de cómo funciona el proyecto: arquitectura, rutas, seguridad,
> módulos, base de datos, frontend, tests y despliegue.
>
> **Stack:** PHP 8.2+ (probado en 8.3) · CodeIgniter 4 · MySQL/MariaDB · vistas PHP · Bootstrap 5 + jQuery
>
> **Última revisión de código:** estructura real de `Systemdsg-main/` (Sprint 6+).

---

## Índice

1. [Resumen ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura y árbol de directorios](#2-arquitectura-y-árbol-de-directorios)
3. [Ciclo de vida de una petición](#3-ciclo-de-vida-de-una-petición)
4. [Sistema de rutas](#4-sistema-de-rutas)
5. [Seguridad: filtros, sesiones y roles](#5-seguridad-filtros-sesiones-y-roles)
6. [Controladores](#6-controladores)
7. [Modelos y capa de datos](#7-modelos-y-capa-de-datos)
8. [Servicios (lógica de negocio)](#8-servicios-lógica-de-negocio)
9. [Módulo de asistencia / RH](#9-módulo-de-asistencia--rh)
10. [Módulo ERP (personas, planes, usuarios, permisos)](#10-módulo-erp-personas-planes-usuarios-permisos)
11. [Base de datos](#11-base-de-datos)
12. [Vistas y frontend](#12-vistas-y-frontend)
13. [Configuración](#13-configuración)
14. [Tests](#14-tests)
15. [Despliegue y operación](#15-despliegue-y-operación)
16. [Hallazgos y deuda técnica](#16-hallazgos-y-deuda-técnica)

---

## 1. Resumen ejecutivo

El proyecto es **híbrido**: conviven dos sistemas completos en la misma base de
código, en la misma base de datos y en **la misma sesión PHP**, pero con
mecanismos de autorización distintos.

| | **ERP / CRM** | **Asistencia DSG** |
|---|---|---|
| Login | `GET/POST /login` | `GET/POST /login-verde` |
| Controlador | `LoginController` | `AuthController` |
| Panel principal | `/dashboard`, `/personas`, `/usuarios` | `/admin`, `/mi-panel`, `/dios` |
| Filtro | `auth` → `App\Filters\Auth` | `asistencia` → `App\Filters\Asistencia` |
| Clave de sesión de rol | `id_rol` (numérico: 1=Admin, 2=Vendedor, 4=Contador) | `user_role` (texto: `Dev`/`Admin`/`Empleado`/`Practicante`) |
| Autorización | Tabla `permisos` por `menu_id` + `roles` | Campo `users.role` + multi-tenant por `users.admin_id` |
| Vistas | `app/Views/personas`, `usuarios.php`, `Permisos.php`… | `app/Views/admin/`, `empleado/`, `dev-*.php` |

**Clave compartida:** ambos escriben `logged_in` en `$_SESSION`, por lo que una
sesión activa de un sistema "se ve" desde el otro (ver §16.6).

```
                ┌──────────────────────────────────────────┐
                │            Navegador (HTML/JS)           │
                └──────────────────────────────────────────┘
                                   │
                ┌──────────────────────────────────────────┐
                │   public/index.php  (front controller)   │
                └──────────────────────────────────────────┘
                                   │
        ┌──────────────────────────┼──────────────────────────┐
        │                          │                          │
   Filtro CSRF              Filtro auth/               Sin filtro
  (todas las POST)          asistencia                  (público)
        │                          │                          │
        ▼                          ▼                          ▼
  ┌──────────┐            ┌──────────────┐            ┌─────────────┐
  │  Rutas   │───────────▶│ Controlador  │───────────▶│   Modelo    │──▶ MySQL
  └──────────┘            └──────────────┘            └─────────────┘
                               │      ▲
                    Services ◀─┘      └── Libraries (Jornada, Query, CSV)
                               │
                               ▼
                        ┌──────────┐
                        │  Vista   │  (layouts + partials + página)
                        └──────────┘
```

---

## 2. Arquitectura y árbol de directorios

```
Systemdsg-main/
├── app/                        ← núcleo de la aplicación
│   ├── Commands/               ← comandos CLI (spark)
│   │   ├── AsistenciaCierre.php    php spark asistencia:cierre
│   │   └── AsistenciaSaneo.php     php spark asistencia:saneo
│   ├── Config/                 ← configuración de CI4 (40 archivos)
│   │   ├── Routes.php              todas las rutas (311 líneas)
│   │   ├── Filters.php             registro de filtros + globals
│   │   ├── Database.php            conexión MySQLi
│   │   ├── App.php, Session.php, Security.php, Email.php…
│   │   └── Boot/{development,production,testing}.php
│   ├── Controllers/
│   │   ├── BaseController.php      base común (carga helpers url+auth)
│   │   ├── Admin/                  base + controladores del panel admin
│   │   │   ├── AdminBaseController.php     guard(), paginar(), validar()
│   │   │   ├── AsistenciaAdminController.php
│   │   │   ├── ConfigAdminController.php
│   │   │   └── IncidenciaAdminController.php
│   │   ├── AuthController.php      login verde (asistencia)
│   │   ├── LoginController.php     login ERP
│   │   ├── AdminController.php     personal y horarios
│   │   ├── EmployeeController.php  marcación biométrica del empleado
│   │   ├── DevController.php       panel /dios
│   │   ├── DashboardController.php, Personas*, Usuarios*, Permisos*…
│   │   └── Home.php                landing pública
│   ├── Database/
│   │   ├── Migrations/             2 migraciones del módulo asistencia
│   │   └── Seeds/
│   ├── Filters/
│   │   ├── Auth.php                filtro ERP (sesión + rol numérico)
│   │   └── Asistencia.php          filtro asistencia (sesión + rol texto)
│   ├── Helpers/                    auth_helper.php (respuestas 401/403)
│   ├── Libraries/Asistencia/
│   │   ├── Jornada.php             días laborables, tolerancia, cierre
│   │   ├── AsistenciaQuery.php     filtros + resumir() de KPIs
│   │   └── ExportCsv.php           exportación CSV con BOM
│   ├── Models/                     ~40 modelos (ver §7)
│   ├── Services/
│   │   ├── Shared/                 ServiceResult, BusinessException
│   │   ├── Usuarios/               UsuarioWriterService
│   │   └── Permisos/               PermisoWriterService
│   ├── ThirdParty/PHPMailer-master/
│   └── Views/                      ver §12
├── build/                          artefactos de build
├── config/php.ini
├── docs/
│   ├── asistencia.md               guía operativa del módulo
│   ├── estructura-sistema.md       ← este archivo
│   └── (resto de docs sprint4...)  pueden no estar en esta copia
├── public/                         DOCUMENT ROOT
│   ├── index.php                   front controller
│   ├── .htaccess                   reescritura Apache
│   ├── assets/libs/                Bootstrap, tabler-icons, simplebar…
│   ├── css/, js/, images/, videos/
│   ├── uploads/{evidencias,rostros}  imágenes biométricas
│   └── info.php                    ⚠ phpinfo() — eliminar en producción
├── tests/                          suites Unit/Database/Feature/Integration
├── vendor/                         dependencias composer
├── writable/                       cache, session, logs, uploads
├── composer.json                   scripts test*
├── Dockerfile                      php:8.3-cli
├── .env                            entorno y credenciales
└── spark                           CLI de CodeIgniter
```

**Convenciones:**

- `public/` es el único directorio expuesto al web server.
- `writable/` debe ser escribible por `www-data`.
- `autoRoute = false` (`app/Config/Routes.php:15`): **toda ruta debe estar declarada**; cualquier URI no listada da 404.

---

## 3. Ciclo de vida de una petición

1. **Entra** por `public/index.php` (front controller estándar CI4).
2. **Carga de configuración**: `.env` pisa valores de `app/Config/*.php`.
3. **Filtros globales** (`app/Config/Filters.php`):
   - `$globals['before']` incluye **`csrf`** → todo POST/PUT/PATCH/DELETE lleva token.
   - `toolbar` solo si `CI_ENVIRONMENT !== 'production'`.
   - `forcehttps` solo en producción.
4. **Resolución de ruta** en `app/Config/Routes.php` → namespace `App\Controllers`.
5. **Filtro de la ruta** (si la ruta define `filter`):
   - `auth` → `App\Filters\Auth` (sesión ERP + rol numérico)
   - `asistencia` → `App\Filters\Asistencia` (sesión asistencia + rol texto)
6. **Controlador** → `initController()` de `BaseController` carga helpers `url` y `auth`.
7. **Servicio** (si hay lógica de negocio) → devuelve `ServiceResult`.
8. **Modelo** → consulta a MySQL con `$allowedFields` explícito.
9. **Vista** → composición `layouts/*` + `partials/*` + página, o JSON si es AJAX.

---

## 4. Sistema de rutas

Archivo: `app/Config/Routes.php` (311 líneas). Control por defecto `Home`.

### 4.1 Públicas (sin filtro)

| Método | Ruta | Controlador |
|---|---|---|
| GET | `/`, `/dsg`, `/precio`, `/servicios`, `/asisten-dsg`, `/precio-asisten` | `Home::*` |
| GET | `/registro` | `AuthController::registerForm` |
| POST | `/auth/register` | `AuthController::register` |
| GET | `/login-verde` | `AuthController::loginForm` |
| POST | `/auth/login` | `AuthController::login` |
| GET | `/auth/logout` | `AuthController::logout` |
| GET | `/dashboard-verde` | `AuthController::dashboard` |
| GET/POST | `/login`, `/login/auth`, `/login/authentication`, `/logout` | `LoginController` |
| GET | `/contacto-demo` | `ContactosController::index` |
| POST | `/enviar` | `EmailController::enviar` |
| GET | `/registro-empleado` | `DevController::registerForm` |
| POST | `/dios/register-employee` | `DevController::registerEmployee` |

### 4.2 Bloque DEV — `/dios` (sin filtro de ruta)

La protección **no** está en el filtro: cada método empieza con
`if ($redir = $this->guardDev()) return $redir;` (`DevController.php`).

| GET | POST |
|---|---|
| `/dios`, `/dios/admins`, `/dios/codigos`, `/dios/empleados`, `/dios/estructura`, `/dios/auditoria`, `/dios/perfil` | `update-password`, `create-admin`, `update-admin`, `delete-admin`, `create-code`, `delete-code`, `delete-user` |

Excepciones públicas por diseño: `registerForm()` y `registerEmployee()`.

### 4.3 Grupo `/admin` — filtro `asistencia` (`Routes.php:62-101`)

| Bloque | GET | POST |
|---|---|---|
| General | `''` (dashboard), `personal`, `horarios` | — |
| Asistencia | `asistencias`, `reportes`, `auditoria/(:num)` | `update-attendance`, `exportar/asistencias` |
| Incidencias | `incidencias` | `create-incident`, `update-incident` |
| Config | `configuracion`, `festivos/eliminar/(:num)` | `save-config`, `festivos/guardar`, `festivos/eliminar` |
| Personal | — | `create-employee`, `update-employee`, `reset-password`, `delete-user`, `fire-employee`, `end-practicante`, `create-code`, `delete-code` |
| Horarios | — | `create-schedule`, `update-schedule`, `delete-schedule`, `assign-schedule` |

### 4.4 Grupo `/mi-panel` — filtro `asistencia` (`Routes.php:107-117`)

| GET | POST |
|---|---|
| `''` (dashboard), `asistencias`, `horario`, `incidencias`, `bio-session` | `registrar`, `justificar-incidencia`, `guardar-huella`, `guardar-rostro` |

### 4.5 Rutas ERP — filtro `auth`

| Grupo/ ruta | Controlador | Notas |
|---|---|---|
| `GET /dashboard` | `DashboardController::index` | |
| `/perfil` | `PerfilController` | + `POST perfil/actualizar` |
| `/calendario` | `CalendarioController::index` | |
| `/tipo_plan` | `TipoPlanController::index` | |
| `/ubigeo/*` | `UbigeoController` | `departamentos`, `provincias/(:any)`, `distritos/(:any)` — API JSON |
| `/personas/*` | `PersonasController`, `PersonasAddController` | `listar`, `buscar-dni`, `buscar-ruc`, `Asignarplanes`, `eliminar/(:num)`, `guardar-todo`, `guardarPlan` |
| `/usuarios/*` | `UsuariosController` | `listar`, `obtener/(:num)`, `registrar`, `actualizar`, `permisos`, `estado` |
| `/permisos/*` | `PermisosController` | `listar`, `obtener`, `usuario/(:num)`, `guardar`, `editar`, `eliminar` |
| `/menu/*` | `MenuController` | `guardar`, `estado/(:num)` |
| `GET /configuracion` | `ConfiguracionController` | **`auth:1`** → solo rol Admin |
| `/planes*`, `/tipo_plan*` | `PlanesController`, `TipoplanaddController` | CRUD |
| `GET /personasadd` | `PersonasAddController::index` | |
| `GET /notificaciones` | `NotificacionesController` | |

### 4.6 Módulos deshabilitados

- **Clientes** (`Routes.php:171-182`): rutas **comentadas** → `ClientesController` y `ClientesAddController` no son alcanzables.
- **Productos, Ventas, Reportes** (`Routes.php:284-304`): plantillas comentadas (`auth:1,2`, `auth:1,4`).

### 4.7 Duplicados existentes

`GET registro` (`:32` y `:136`), `POST auth/register` (`:33` y `:138`),
`GET auth/logout` (`:36` y `:133`). CI4 usa la primera definida; no rompen nada,
pero son ruido.

---

## 5. Seguridad: filtros, sesiones y roles

### 5.1 Registro de filtros — `app/Config/Filters.php`

| Línea | Alias | Clase |
|---|---|---|
| `:43` | `csrf` | `CodeIgniter\Filters\CSRF` |
| `:44` | `toolbar` | DebugToolbar (solo no-production) |
| `:49` | `forcehttps` | ForceHTTPS (solo producción) |
| `:52` | `auth` | `App\Filters\Auth` |
| `:53` | `asistencia` | `App\Filters\Asistencia` |
| `:88-91` | **`$globals['before']`** | incluye **`csrf`** en todas las peticiones no-GET |

### 5.2 Filtro ERP — `app/Filters/Auth.php`

```
¿session('logged_in')?
   NO  → redirect /login   (o 401 si es AJAX)
   SÍ  → ¿la ruta define argumentos auth:1,2,...?
              SÍ → comparar contra session('id_rol'); si no coincide → 403
              NO → pasar
```

Usa `app/Helpers/auth_helper.php` (`respuesta_auth_requerida()`, `respuesta_auth_denegada()`), cargado por `BaseController`.

### 5.3 Filtro asistencia — `app/Filters/Asistencia.php` (80 líneas)

```
:20-26  ¿URI es admin, admin/*, mi-panel o mi-panel/*?  NO → null (no actúa)
:30     ¿session('logged_in')?  NO → 401 JSON (si AJAX) o redirect /login-verde
:34-41  /admin:
            rol 'Dev'    → redirect /dios
            rol !== 'Admin' → redirect home(rol)
            Admin        → null (permitir)
:44-52  /mi-panel:
            'Dev'    → /dios
            'Admin'  → /admin
            otro     → null (permitir)   ← no valida lista de roles
:73-80  salir(): 401 si AJAX/JSON, si no redirect con flashdata
```

> En `/mi-panel` la validación final la hace
> `EmployeeController::checkEmployee()` (`EmployeeController.php:27-59`):
> recupera el usuario por `session('user_id')` y **destruye la sesión** si no
> existe o si `estado !== 'Activo'`.

### 5.4 Otras capas

| Capa | Archivo | Detalle |
|---|---|---|
| CSRF | `app/Config/Security.php` | `tokenName = 'csrf_test_name'`, `regenerate = false`, `redirect` solo en producción (en dev devuelve 403) |
| Sesión | `app/Config/Session.php` | driver `FileHandler`, cookie `ci_session`, **expiración 7200 s (2 h)**, `WRITEPATH.'session'`, `matchIP = false`, `timeToUpdate = 300` |
| Rate limit login | `AuthController` | 5 intentos fallidos por usuario o 20 por IP cada 10 min |
| Validación | `AdminBaseController::validar()` | envuelve `$this->validate()` |
| Imágenes biométricas | `EmployeeController::saveBioImage()` | ver §9.4 |
| Auditoría | `attendance_log` | campo, valor anterior/nuevo, motivo, autor, fecha |
| CSP | `App.php` | **`CSPEnabled = false`** |
| HTTPS | `App.php` | `forceGlobalSecureRequests = false` |

---

## 6. Controladores

### 6.1 Herencia

```
CodeIgniter\Controller
 └── BaseController            helpers url+auth, protección CI_DEBUG
      ├── LoginController      (ERP)
      ├── DashboardController, Personas*, Usuarios*, Permisos*, Menu*…
      ├── AuthController       (asistencia)
      ├── AdminController      (asistencia personal/horarios)
      ├── EmployeeController   (marcación)
      ├── DevController        (/dios)
      └── Admin\AdminBaseController   guard(), adminId(), viewData(), paginar(), validar()
           ├── Admin\AsistenciaAdminController
           ├── Admin\ConfigAdminController
           └── Admin\IncidenciaAdminController

Controller (sin BaseController)
 └── TipoPlanController        ⚠ sin helpers ni protección propia
```

### 6.2 Controladores ERP

| Controlador | Métodos principales |
|---|---|
| `LoginController` | `index()` GET `/login`, `auth()` POST, `logout()` |
| `DashboardController` | `index()` con chequeo manual de sesión adicional |
| `PersonasController` | `index`, `listar`, `eliminar`, `Asignarplanes`, `obtenerNombrePersona`, `obtenerDetalle`, `planesDisponibles`, `mantenerPlanSeleccionado`, `tipoplan`, `buscar` |
| `PersonasAddController` | `index($id)`, `buscarDni()`, `buscarRuc()`, `guardar_todo()`, `guardarPlan()` — usa `verificarSesion()` propio |
| `UsuariosController` | CRUD + `asignarPermisos()`, `cambiarEstado()` |
| `PermisosController` | `index`, `listar`, `guardar`, `obtener`, `editar`, `eliminar`, `porUsuario` + `verificarAccesoAdmin()`, `renderAdminPage()` |
| `MenuController` / `ConfiguracionController` / `NotificacionesController` / `CalendarioController` | vistas con `obtenerLimitaciones()` (placeholders) |
| `PlanesController` / `TipoPlanController` | CRUD de planes |
| `UbigeoController` | API JSON de departamentos/provincias/distritos |
| `EmailController` | `enviar()` — formulario de contacto |

### 6.3 Controladores asistencia

| Controlador | Responsabilidad |
|---|---|
| `AuthController` | registro, login, logout, rate limit |
| `AdminController` (530 líneas) | `dashboard`, `personal`, `horarios`, `createEmployee`, `updateEmployee`, `resetPassword`, `fireEmployee`, `endPracticante`, `deleteUser`, `createCode`, `deleteCode`, `createSchedule`, `updateSchedule`, `deleteSchedule`, `assignSchedule` |
| `EmployeeController` (528 líneas) | `dashboard`, `asistencias`, `horario`, `incidencias`, `registrar`, `justificarIncidencia`, `bioSession`, `guardarHuella`, `guardarRostro` |
| `Admin\AsistenciaAdminController` | listado, reportes, edición auditada, export CSV |
| `Admin\ConfigAdminController` | configuración por admin + festivos |
| `Admin\IncidenciaAdminController` | incidencias |
| `DevController` (555 líneas) | superusuario: admins, códigos, empleados, estructura, auditoría, perfil |

**Rasgos de `DevController`:** crea admins con código automático `ADMIN-XXX`,
email autogenerado `nombre.apellido@dsg.pe`, contraseña de 12 caracteres con
`random_int`, `cascadeDeleteAdmin()` y `deleteUser()` que borra `attendance` e
`incidents` a mano.

---

## 7. Modelos y capa de datos

Todos declaran `$table` y `$allowedFields` explícito (CI4 no escribe columnas no
listadas).

### 7.1 Dominio asistencia

| Modelo | Tabla | Notas |
|---|---|---|
| `UserModel` | `users` | `findDev()`, `findByDni()`, `findByEmail()`, `nextAdminCode()`, `nextPersonalCode($adminId,$role)`, `cascadeDeleteAdmin($id)` |
| `AttendanceModel` | `attendance` | `findByUserAndDate($user,$date)` — clave de unicidad |
| `AttendanceLogModel` | `attendance_log` | auditoría: campo / viejo / nuevo / autor |
| `IncidentModel` | `incidents` | estados `Pendiente/Revisión/Justificada/Rechazada/Desestimada` |
| `ScheduleModel` | `schedules` | `getActive($adminId)` |
| `FestivoModel` | `festivos` | `getAll($adminId)` — por admin o globales |
| `ConfigModel` | `admin_config` | `saveConfig($arr, $adminId)` |
| `CodeModel` | `codes` | `findActiveByCode()`, `findByDniActive()`, `markUsed()` |

### 7.2 Dominio ERP

| Modelo | Tabla |
|---|---|
| `PersonaModel`, `PersonasAddModel` | `personas` |
| `UsuarioModel` | `usuarios` (+ roles) |
| `RolModel` | `roles` |
| `PermisosModel` | `permisos` (`read/insert/update/delete` por menú) |
| `MenuModel` | `menus` |
| `PlanModel`, `planesaddModel` | `planes` |
| `TipoPlanModel`, `tipoPlanModel` | `tipo_plan` |
| `ClientesModel`, `ClientesaddModel` | `clientes` |
| `EmpresaModel`, `empresaSucursalModel` | `empresa`, `empresa_sucursales` |
| `DepartamentoModel`, `ProvinciaModel`, `DistritoModel`, `PaisModel` | `ubigeo_peru_*`, `pais` |
| `EventosModel` | `calendario_eventos` |
| `DashboardModel`, `ConfiguracionModel`, `ConfigModel`, `PerfilModel`, `NombreModel`, `ContactosModel` | varias |

---

## 8. Servicios (lógica de negocio)

```
app/Services/
├── Shared/
│   ├── ServiceResult.php        estado ok/err, errors[], data
│   └── BusinessException.php    excepción de negocio → 422/409
├── Usuarios/
│   └── UsuarioWriterService.php crear/actualizar usuario ERP (hash bcrypt)
└── Permisos/
    └── PermisoWriterService.php guardar/eliminar permiso por rol+menú
```

**Patrón:** el controlador invoca el servicio, recibe un `ServiceResult` y lo
traduce a JSON o a flashdata/redirect mediante un helper `respuesta()`.

> ⚠ `app/Config/Services.php` importa clases que **no existen** en esta copia
> (`App\Services\External\*`, `App\Services\Clientes\*`, `App\Services\Personas\*`)
> y llama a `config('ExternalServices')` (archivo ausente). Ver §16.1.

---

## 9. Módulo de asistencia / RH

Módulo propio del proyecto (login verde). Guía operativa complementaria:
`docs/asistencia.md`.

### 9.1 Tablas implicadas

`users`, `attendance`, `attendance_log`, `schedules`, `incidents`, `codes`,
`admin_config`, `festivos`.

- Índice único **`uq_attendance_user_date`** → un solo registro por persona y fecha.
- Columnas añadidas: `attendance.lat/lng/ip`, `attendance_log.*`,
  `incidents.resuelta_por/resuelta_en`.

### 9.2 Estados válidos

| Campo | Valores |
|---|---|
| `attendance.status` | `present`, `late`, `absent`, `no_exit` |
| `incidents.estado` | `Pendiente`, `Revisión`, `Justificada`, `Rechazada`, `Desestimada` |
| `users.estado` | `Activo`, `Inactivo`, `Despedido`, `Retirado` |

### 9.3 Flujo de marcación — `EmployeeController::registrar()` (`:197-315`)

```
1. checkEmployee()                    sesión, rol, usuario existe, estado 'Activo'
2. ¿huella o rostro registrados?      NO → rechaza (biometría obligatoria)
3. ¿bio_ok === '1'?                   NO → rechaza (verificación hecha en el front)
4. Guardar evidencia selfie           saveBioImage()
5. attendanceModel->findByUserAndDate()
   ├─ sin registro → INSERT entrada
   │     ahora <= hora_entrada + tolerancia → status 'present'
   │     si no                             → 'late' + incidencia tipo Tardanza
   ├─ con registro y sin time_out → UPDATE salida
   │     si sale antes de hora_salida → incidencia 'Salida anticipada' (Revisión)
   └─ ya completo → aviso "Hoy ya registraste tu entrada y salida"
6. Guardar lat/lng (si son válidos) e IP
```

### 9.4 Seguridad de la imagen biométrica (`:340-391`)

- Límites: `BIO_MAX_BIN = 3 MB`, `BIO_MAX_B64 = 4 MB`, `BIO_MIN_BIN = 200` bytes.
- Whitelist de subdirectorios: `['evidencias', 'rostros']` (bloquea traversal).
- MIME permitido: `jpeg`, `png`, `webp`.
- Validación real con **`getimagesizefromstring()`** → bloquea SVG/HTML embebido.
- Nombre aleatorio en `FCPATH.'uploads/'.$subdir`.

### 9.5 Geolocalización (`:321-338`)

`lat`/`lng` solo si son numéricas, dentro de ±90/±180 y **no** `(0,0)`; en caso
contrario se guarda `NULL` ("nunca inventado"). La IP se guarda siempre
(truncada a 45 caracteres).

### 9.6 Lógica de jornada — `app/Libraries/Asistencia/`

| Clase | Responsabilidad |
|---|---|
| `Jornada` | días laborables, rangos `Lun - Vie`, tolerancia, `diaCerrado()` |
| `AsistenciaQuery` | filtros de fecha/estado y **`resumir()`** — los KPIs se calculan sobre el rango completo, nunca sobre la página visible |
| `ExportCsv` | CSV con separador `;`, BOM UTF-8, nombre `asistencia_AAAA-MM-DD_AAAA-MM-DD.csv`, estados en español |

### 9.7 Comandos CLI — `app/Commands/`

| Comando | Función |
|---|---|
| `asistencia:cierre` | Cierra el día: `absent` a quien no marcó entrada, `no_exit` a quien olvidó la salida, crea la incidencia y lo auditado. Flags `--fecha`, `--admin`, `--dry-run`. **Idempotente**, salta festivos y días no laborables, no corre si el día está abierto. |
| `asistencia:saneo` | Detecta/corrige marcas huérfanas, `admin_id NULL`, estados inválidos, config sin admin, `admin_code` faltante, festivos duplicados. Flags `--apply`, `--desde`, `--hasta`. |

### 9.8 Cron sugerido

```cron
# Cierre del día a las 23:55
55 23 * * * cd /ruta/al/proyecto && php spark asistencia:cierre >> writable/logs/cron-asistencia.log 2>&1

# Saneo semanal (domingo 04:00), sólo reporte
0 4 * * 0 cd /ruta/al/proyecto && php spark asistencia:saneo >> writable/logs/cron-asistencia.log 2>&1
```

Si el servidor usa `www-data`, lanzar el cron con ese usuario o dar permisos de
escritura a `writable/`.

### 9.9 Reportes y exportación

- `/admin/asistencias` y `/admin/reportes` filtran por `?fecha_inicio`,
  `?fecha_fin`, `?todo=1`, `?estado=...` y paginan 50 filas (`?page=`).
- Chips de rango: Hoy / Semana / Mes / Todo.
- Botón **Descargar CSV** → `POST /admin/exportar/asistencias`.
- `/admin/incidencias` filtra en el navegador por estado, con contadores.
- Sidebar y tarjeta del dashboard muestran incidencias `Pendiente`/`Revisión`
  (`$incPendientes` en `viewData()`).
- **"Sin marcar hoy"** = activos sin marcación de hoy (0 en festivos o fin de
  semana), calculado en `AdminBaseController::sinMarcarHoy()`.

---

## 10. Módulo ERP (personas, planes, usuarios, permisos)

### 10.1 Capas

```
Ruta (filter: auth)
   → Controlador (verificarAccesoAdmin / verificarSesion)
      → Service (UsuarioWriterService / PermisoWriterService)   [solo escrituras críticas]
         → Model (PersonaModel, UsuarioModel, PermisosModel…)
            → MySQL
   → Vista (layouts/header + sidebar + topbar + página + footer)
```

### 10.2 Autorización ERP

- A nivel de ruta casi solo se usa `auth` (sin roles); **solo** `/configuracion`
  usa `auth:1`.
- El control real está dentro de `PermisosController::verificarAccesoAdmin()`
  y en la tabla `permisos` (`menu_id` × rol, con flags
  `read/insert/update/delete`).
- El sidebar se construye según los permisos del usuario (`MenuModel`).

### 10.3 Consultas externas (DNI/RUC)

`PersonasAddController::buscarDni()` / `buscarRuc()` y
`PersonasController::buscar-dni` / `buscar-ruc` consultan un servicio de lookup
(controlado por tests de integración).

### 10.4 Bug conocido

`POST /personas/guardarPlan` → 500 `Unknown column 'id_plan'`
(`PersonasAddController.php:417`). Documentado en `docs/asistencia.md:167-170`
y fuera del alcance del módulo de asistencia.

---

## 11. Base de datos

### 11.1 Fuentes

| Fuente | Contenido |
|---|---|
| `app/Database/Migrations/2026-09-30-172500_CreateAsistenciaSchema.php` | esquema base del módulo asistencia |
| `app/Database/Migrations/2026-09-30-180000_AsistenciaMejoras.php` | `attendance_log`, `festivos`, `attendance.lat/lng/ip`, `incidents.resuelta_por/resuelta_en`, índice `uq_attendance_user_date` |
| `dsg_sistemapo.sql` (raíz del proyecto) | dump del módulo asistencia |
| `sistemapo.sql` (raíz del proyecto) | dump del ERP |

> **Fuente de verdad del esquema: las migraciones.** Los dumps son snapshots.

### 11.2 Esquema asistencia

```
users ──┬──< attendance          (1 usuario × N marcas; UNIQUE(user,fecha))
        ├──< attendance_log      (auditoría de ediciones)
        ├──< incidents           (tardanzas, salidas anticipadas, justificaciones)
        ├──< codes               (códigos de registro de empleados)
        └──< schedules           (horarios asignados)

admin_config  (config por admin: tolerancia, etc.)
festivos      (días no laborables, por admin o globales)
```

Multi-tenant: casi todo lleva `admin_id` → cada Admin ve solo su gente.

### 11.3 Esquema ERP

`personas`, `usuarios`, `roles`, `permisos`, `menus`, `clientes`, `empresa`,
`empresa_sucursales`, `planes`, `tipo_plan`, `calendario_eventos`, `pais`,
`tipo_documento`, `ubigeo_peru_*`.

### 11.4 Conexión

- Driver **MySQLi**, host `127.0.0.1`, base de datos **`sistemapo`**.
- Definida en `app/Config/Database.php` y sobrescrita por `.env`.
- **Tests**: SQLite3 `:memory:` con prefix `db_`.

---

## 12. Vistas y frontend

### 12.1 Estructura de `app/Views/`

| Grupo | Archivos |
|---|---|
| **Layouts** | `layouts/{header, header_dashboard, nav, sidebar, topbar, footer, practicante-panel}.php` |
| **Partials** | `partials/{admin-sidebar, dev-sidebar, practicante-sidebar, biometric_panel}.php` |
| **Panel admin (8)** | `admin/{dashboard, personal, horarios, asistencias, reportes, auditoria, incidencias, configuracion}.php` |
| **Empleado** | `empleado/{dashboard, practicante-dashboard, practicante-asistencias, practicante-horario, practicante-incidencias}.php` |
| **Dev (7)** | `dev-{panel, admins, empleados, codigos, estructura, auditoria, perfil}.php` |
| **ERP** | `personas/{personas, personasadd, personasadd_frame, tabla}`, `Usuarios.php`, `Permisos.php`, `Menu.php`, `perfil.php`, `calendario/`, `mantenedor/{planes, tipo_plan, tipo_planadd}`, `clientes/`, `notificaciones/` |
| **Landing / auth** | `index`, `dsg`, `precio`, `servicios`, `asisten-dsg`, `precio-asisten`, `login`, `login-asisten`, `registro`, `contacto-demo`, `dashboard`, `dashboard-verde` |
| **Errores** | `errors/{html,cli}/*` |

**Patrón de composición ERP:**

```php
echo view('layouts/header', $data);
echo view('layouts/sidebar', $data);
echo view('layouts/topbar', $data);
echo view('personas/personas', $data);
echo view('layouts/footer', $data);
```

### 12.2 Parcial biométrico

`app/Views/partials/biometric_panel.php` (641 líneas): tarjeta con estado de
huella/rostro (`bioHuella`, `bioRostro`, `bioRostroPath`, `bioHuellaCredId`),
modal propio con `<video>` de cámara, pasos y miniaturas; recibe
`bioMark['exit']` y `bioMark['hora_salida']` para detectar salida anticipada.
CSS propio con variables `--g-*`.

### 12.3 Stack de dependencias (sin bundler)

| Tipo | Detalle |
|---|---|
| **Local** (`public/assets/libs/`) | Bootstrap 5, tabler-icons, simplebar, wow, aos, lightgallery |
| **CDN** | jQuery 3.6, DataTables, Font Awesome 6, ApexCharts, Swiper, Google Fonts + Material Symbols |
| **Plantilla base** | DashLite (`public/assets/css/styles.min.css`) |
| **CSS propio** | `public/css/{index, dsg, login, dashboard, perfil}.css` |
| **JS propio** | `public/js/*` — `buscardni.js`, `buscarRuc.js`, `persona-editor.js`, `plane-editor.js`, `tipo-plan-editor.js`, `ubigeos.js`, `dev-table.js`, `menu.js`, `main.js`… |

Nav pública (`layouts/nav.php`): Inicio · Servicios · DSG · Precios · `login` · `/#contacto`.

---

## 13. Configuración

| Archivo | Hallazgo clave |
|---|---|
| `.env` | `CI_ENVIRONMENT=development`, `app.baseURL=http://localhost:8080/`, credenciales DB |
| `app/Config/App.php` | `baseURL` por defecto apunta a `http://localhost/project-dsgperu/public/` (lo pisa `.env`); `forceGlobalSecureRequests=false`, **`CSPEnabled=false`**, `tokenRandomize=false` |
| `app/Config/Database.php` | MySQLi por defecto |
| `app/Config/Security.php` | `tokenName='csrf_test_name'`, `regenerate=false`, `redirect` solo en producción |
| `app/Config/Session.php` | FileHandler, cookie `ci_session`, 7200 s |
| `app/Config/Email.php` | sin SMTP: `protocol='mail'`, `fromEmail` vacío → el envío real depende de `Config\ExternalServices` (ausente) |
| `app/Config/Filters.php` / `Routes.php` | ver §4 y §5 |
| `app/Config/Autoload.php:91` | `$helpers = []` (los helpers los carga `BaseController`) |
| `app/Config/Routing.php` | `autoRoute=false`, `defaultController='Home'` |
| `app/Config/Events.php` | solo eventos por defecto de CI4 — **no hay eventos personalizados** |
| `config/php.ini` | overrides de PHP |

**Third-party:** PHPMailer 7.0 en `app/ThirdParty/PHPMailer-master` y vía composer.

---

## 14. Tests

Suites definidas en `phpunit.xml.dist`: **Unit**, **Database**, **Feature**, **Integration**.

| Archivo | Cubre |
|---|---|
| `tests/unit/JornadaTest.php` | días laborables, rangos `Lun - Vie`, horario asignado vs rol, tolerancia, cierre de día |
| `tests/unit/AsistenciaLibrariesTest.php` | normalización de fechas, `resumir()`, filtro por estado, CSV |
| `tests/feature/AsistenciaAuthTest.php` | redirecciones de `/admin` y `/mi-panel` sin sesión + bloqueo CSRF |
| `tests/unit/TipoPlanEditorTest.php` | ERP; requiere `php8.3-sqlite3` (3 errores preexistentes sin ella) |
| `tests/unit/HealthTest.php`, `CURLRequestCompatibilityTest.php` | plantilla CI4 |
| `tests/database/`, `tests/session/` | plantillas CI4 |

```bash
vendor/bin/phpunit --testsuite Unit,Feature --no-coverage
# o
composer test          # todos
composer test:unit
composer test:feature
composer test:database
composer test:integration
```

Detalle operativo adicional en `tests/README.md`.

---

## 15. Despliegue y operación

### 15.1 Arranque local

```bash
composer install            # si falta vendor/
php spark migrate           # esquema (incluye mejoras de asistencia)
php spark serve --port 8080
```

- Login asistencia: `http://localhost:8080/login-verde`
- Login ERP: `http://localhost:8080/login`
- Acceso de prueba asistencia: `admin@dsg.pe` / `admin`

### 15.2 Entorno

| Archivo | Contenido |
|---|---|
| `composer.json` | PHP `^8.2`, `codeigniter4/framework ^4.0`, `phpmailer/phpmailer ^7.0`, scripts `test*` |
| `Dockerfile` | `php:8.3-cli` + extensiones `intl`, `mysqli`, `pdo_mysql` |
| `public/index.php` | front controller CI4, requiere PHP ≥ 8.2 |
| `public/.htaccess` | reescritura a `index.php` (Apache) |
| `spark` | CLI de CodeIgniter (presente en esta copia) |
| `writable/` | `cache`, `session`, `logs`, `debugbar`, `uploads/{evidencias,rostros}` — permisos `www-data` |
| `server.log` | salida de `php spark serve` |

### 15.3 Checklist de producción

- [ ] `.env` → `CI_ENVIRONMENT = production`
- [ ] Revisar `baseURL` real en `.env`
- [ ] Credenciales de DB fuertes y usuario no-root
- [ ] **Eliminar `public/info.php`** (expone `phpinfo()`)
- [ ] Activar `forceGlobalSecureRequests` y `CSPEnabled`
- [ ] Habilitar SMTP real en `Config\Email` / `ExternalServices`
- [ ] Configurar los 2 cron de asistencia (§9.8)
- [ ] Permisisos `writable/` a `www-data`
- [ ] Limpiar `writable/*.json` (restos de una versión con archivos)

---

## 16. Hallazgos y deuda técnica

### Críticos

1. **`app/Config/Services.php` roto** — importa `App\Services\External\*`,
   `App\Services\Clientes\*`, `App\Services\Personas\*` (no existen) y llama
   `config('ExternalServices')` (archivo ausente). Ahora no es alcanzable porque
   `clientes/*` está comentado, pero cualquier uso futuro revienta.
2. **`POST /enviar` público** (`Routes.php:102`) depende de
   `EmailController::getMailConfig(): \Config\ExternalServices`
   (`EmailController.php:10`) → *class not found*.
3. **`public/info.php` con `phpinfo()`** — información sensible del servidor.

### Medios

4. **`/dios/*` sin filtro de ruta** — la seguridad depende de `guardDev()` en
   cada método; un método nuevo sin esa llamada queda abierto.
   `POST /dios/register-employee` crea cuentas `Empleado` con solo un código de
   6 hex (`DevController.php:446`).
5. **Roles ERP ausentes de las rutas**: solo `/configuracion` usa `auth:1`, así
   que `roles`/`permisos` no se aplican a nivel de ruta, sino dentro de
   `PermisosController::verificarAccesoAdmin()`.
6. **Cruce de sistemas por sesión compartida**: un usuario ERP (`logged_in` +
   `id_rol`, sin `user_role`) que entre a `/mi-panel` **pasa el filtro**
   (`Asistencia.php:52`), pero `checkEmployee()` no encuentra `user_id` y
   **destruye toda la sesión ERP**. No deja pasar a nadie, pero es confuso.
7. **`TipoPlanController` hereda `Controller`**, no `BaseController` — sin
   helpers ni protección coherente.
8. **Hardening bajo**: CSRF con `regenerate = false`, `CSPEnabled = false`,
   `forceGlobalSecureRequests = false`.

### Menores

9. Duplicados de rutas: `registro`, `auth/register`, `auth/logout`
   (`Routes.php:32/136`, `:33/138`, `:36/133`).
10. `writable/*.json` = restos de una versión basada en archivos
    (`users.json`, `attendance.json`, `codes.json`, `incidents.json`,
    `schedules.json`, `admin-config.json`) con hashes bcrypt reales.
11. `README.md` desactualizado (`spark` sí existe; `docs/` solo contiene
    `asistencia.md` en esta copia).
12. Bug ERP conocido: `POST /personas/guardarPlan` → 500 `Unknown column 'id_plan'`.
13. `.env` en `development` y `baseURL` de `App.php` apuntando a otra ruta.
14. Git no funciona en la copia (objetos locales faltantes).

---

## Referencias rápidas

| Quiero… | Ir a |
|---|---|
| Levantar el proyecto | §15.1 |
| Ver todas las rutas | `app/Config/Routes.php` |
| Entender la seguridad | §5 |
| Ver el flujo de marcación | §9.3 |
| Comandos de cierre/saneo | §9.7 |
| Cron | §9.8 |
| Estados válidos | §9.2 |
| Tablas | §11 |
| Ejecutar tests | §14 |
| Guía operativa del módulo | `docs/asistencia.md` |
