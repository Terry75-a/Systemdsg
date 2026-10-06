# Módulo de asistencia (DSG) — guía operativa

Documentación del módulo propio (login verde, panel admin, panel del empleado).
Todo lo del sistema del compañero (personas, clientes, plan, ventas) queda fuera de este alcance.

---

## 1. Dónde vive cada cosa

| Capa | Archivo |
|------|---------|
| Rutas (`/admin`, `/mi-panel`, `/dios`) | `app/Config/Routes.php` |
| Filtro de sesión + rol | `app/Filters/Asistencia.php` (alias `asistencia` en `app/Config/Filters.php`) |
| Login / rate limit | `app/Controllers/AuthController.php` (`/login-verde`) |
| Panel admin — personal y horarios | `app/Controllers/AdminController.php` |
| Panel admin — asistencia, reportes, auditoría, CSV | `app/Controllers/Admin/AsistenciaAdminController.php` |
| Panel admin — configuración y festivos | `app/Controllers/Admin/ConfigAdminController.php` |
| Panel admin — incidencias | `app/Controllers/Admin/IncidenciaAdminController.php` |
| Base compartida del panel admin (sesión, validación, paginación) | `app/Controllers/Admin/AdminBaseController.php` |
| Panel del empleado (marcación biométrica) | `app/Controllers/EmployeeController.php` |
| Auditoría global para el Dev | `DevController::auditoria()` → `/dios/auditoria` |
| Lógica de jornada | `app/Libraries/Asistencia/Jornada.php` |
| Filtros de fecha / export CSV | `app/Libraries/Asistencia/AsistenciaQuery.php`, `ExportCsv.php` |
| Comandos CLI | `app/Commands/AsistenciaCierre.php`, `app/Commands/AsistenciaSaneo.php` |
| Migraciones | `app/Database/Migrations/2026-09-30-*.php` |
| Modelos | `AttendanceModel`, `AttendanceLogModel`, `IncidentModel`, `FestivoModel`, `ScheduleModel`, `ConfigModel` |

### Tablas

`users`, `attendance`, `attendance_log`, `schedules`, `incidents`, `codes`,
`admin_config`, `festivos`.

Columnas añadidas: `attendance.lat/lng/ip`, `attendance_log.*`,
`incidents.resuelta_por/resuelta_en`.
Índice único `uq_attendance_user_date` (un solo registro por persona y fecha).

---

## 2. Arranque

```bash
composer install                 # si falta vendor/
php spark migrate                # esquema (incluye las mejoras del módulo)
php spark serve --port 8080
```

- Login del módulo: `http://localhost:8080/login-verde`
- Acceso de prueba por defecto: `admin@dsg.pe` / `admin`
- `/login` es el login del sistema del compañero (otro flujo).

---

## 3. Comandos CLI

### `asistencia:cierre` (#1)

Cierra el día: marca `absent` a quien no marcó entrada y `no_exit` a quien se
olvidó la salida, crea la incidencia correspondiente y lo deja auditado.

```bash
php spark asistencia:cierre                      # cierra ayer
php spark asistencia:cierre --fecha 2026-09-30   # una fecha puntual
php spark asistencia:cierre --admin 3            # sólo un admin
php spark asistencia:cierre --dry-run            # qué haría, sin escribir
```

- Es **idempotente**: volver a correrlo no duplica incidencias.
- Salta festivos (`festivos`) y días no laborables del horario.
- No corre si el día todavía está abierto (`Jornada::diaCerrado`).

### `asistencia:saneo` (#2)

Repara datos rotos del módulo.

```bash
php spark asistencia:saneo              # sólo reporta
php spark asistencia:saneo --apply      # corrige
php spark asistencia:saneo --desde 2026-09-01 --hasta 2026-09-30
```

Detecta: marcas huérfanas (sin usuario), `admin_id` NULL, marcas sin estado
válido, configuración sin admin y `admin_code` faltante, festivos duplicados.

---

## 4. Cron sugerido (#20)

```cron
# Cierre del día a las 23:55
55 23 * * * cd /ruta/al/proyecto && php spark asistencia:cierre >> writable/logs/cron-asistencia.log 2>&1

# Saneo semanal (domingo 04:00), sólo reporte
0 4 * * 0 cd /ruta/al/proyecto && php spark asistencia:saneo >> writable/logs/cron-asistencia.log 2>&1
```

Si el servidor usa `www-data`, lanza el cron con ese usuario o dale permisos de
escritura a `writable/`.

---

## 5. Seguridad del módulo

- **Rutas protegidas** (`app/Filters/Asistencia.php`): `/admin*` y `/mi-panel*`
  piden sesión y rol; sin sesión mandan a `/login-verde`, Dev → `/dios`,
  Admin → `/admin`.
- **Rate limit de login** (`AuthController`): 5 intentos fallidos por usuario o
  20 por IP cada 10 minutos.
- **Validaciones** con `$this->validate()` (`AdminBaseController::validar()`).
- **Imágenes biométricas** (`saveBioImage`): sólo data-URL de jpg/png/webp,
  máximo 3 MB binarios, nombre aleatorio, verificación con `getimagesizefromstring()`.
- **Auditoría** (#10): cada edición de una marcación guarda campo, valor
  anterior/nuevo, motivo, autor y fecha en `attendance_log`; historial en
  `/admin/asistencias` → Historial y `/dios/auditoria`.
- **Festivos** (#3): días en los que no se cuentan faltas, por admin o globales.

---

## 6. Exportación y reportes (#13, #15)

- `/admin/asistencias` y `/admin/reportes` filtran por `?fecha_inicio`,
  `?fecha_fin` y `?todo=1`, con paginación de 50 filas (`?page=`).
- Barra compacta con chips de rango (Hoy / Semana / Mes / Todo) y de estado
  (`?estado=present|late|absent|no_exit`); el CSV respeta el mismo rango y estado.
- **Los KPIs salen de `AsistenciaQuery::resumir()` sobre el rango completo**,
  nunca de las filas de la página visible (con más de 50 registros los números
  no cuadraban).
- Botón **Descargar CSV** → `POST /admin/exportar/asistencias`
  (separador `;`, BOM UTF-8, nombre `asistencia_AAAA-MM-DD_AAAA-MM-DD.csv`,
  estados en español: Presente / Tardanza / Falta / Sin salida).
- `/admin/incidencias` filtra en el navegador por estado, con contadores.
- El sidebar y la tarjeta de Incidencias del dashboard muestran el número de
  incidencias `Pendiente` / `Revisión` (`$incPendientes` en `viewData()`).
- Dashboard: `Sin marcar hoy` = activos sin marcación de hoy (0 en festivos o
  fin de semana), calculado en `AdminBaseController::sinMarcarHoy()`.

---

## 7. Tests (#16)

```bash
vendor/bin/phpunit --testsuite Unit,Feature --no-coverage
```

Tests del módulo:

- `tests/unit/JornadaTest.php` — días laborables, rangos (`Lun - Vie`),
  horario asignado vs. rol, tolerancia, cierre de día.
- `tests/unit/AsistenciaLibrariesTest.php` — normalización de fechas, resumen de
  totales, filtro por estado y CSV.
- `tests/feature/AsistenciaAuthTest.php` — redirecciones de `/admin` y
  `/mi-panel` sin sesión y bloqueo de POST.

> Nota: `tests/unit/TipoPlanEditorTest.php` (sistema del compañero) necesita la
> extensión `php8.3-sqlite3`; sin ella aparecen 3 errores preexistentes ajenos a
> este módulo.

---

## 8. Estados válidos

- `attendance.status`: `present`, `late`, `absent`, `no_exit`
- `incidents.estado`: `Pendiente`, `Revisión`, `Justificada`, `Rechazada`, `Desestimada`
- `users.estado`: `Activo`, `Inactivo`, `Despedido`, `Retirado`

---

## 9. Pendiente conocido (no tocado a propósito)

`POST /personas/guardarPlan` → 500 `Unknown column 'id_plan'`: bug del módulo
del compañero, fuera de este alcance.
