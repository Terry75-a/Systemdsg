# Testing del proyecto

Guía operativa para correr y ubicar tests en `project-dsgperu`.

La estrategia completa del Sprint 4 vive en `docs/testing-strategy-sprint4.md`.

## Estado actual

- Framework: CodeIgniter 4
- Runner: PHPUnit 10.5
- Config principal: `phpunit.xml.dist`
- Grupo de base de datos para tests: `Config\Database::$tests`
- Validación liviana verificada en esta copia (`--no-coverage`):
  - `Unit`: 2 tests, 3 assertions
  - `Database`: 2 tests, 3 assertions
  - `Feature`: 92 tests, 448 assertions
  - `Integration`: 7 tests, 21 assertions

## Cobertura real hoy

### Feature (`tests/feature/Admin`)

Cobertura útil existente sobre flujos admin reales:

- `AuthFeatureTest`: login, logout y redirecciones básicas
- `AdminAuthorizationFeatureTest`: denegación para usuarios sin sesión o sin rol admin
- `UsuariosFeatureTest`: altas, edición, estado y validaciones del módulo
- `PermisosFeatureTest`: persistencia y contratos principales del módulo
- `PerfilFeatureTest`: edición del perfil propio sin exponer IDs sensibles
- `PersonasFeatureTest`: alta de persona, validaciones, transacción con cliente/empresa y redirects
- `ClientesFeatureTest`: listado real, alta con reglas explícitas de empresa/RUC, detalle JSON útil, update y cambio de estado
- `UbigeoFeatureTest`: endpoints JSON de departamentos/provincias/distritos y manejo de sesión

### Integration (`tests/integration`)

- `DocumentoLookupServiceTest`: lookup DNI/RUC con cliente HTTP fake, sin depender del servicio externo real.

### Suites todavía básicas

- `tests/unit` y `tests/database` siguen teniendo cobertura mínima/base del starter.
- Hoy el peso real del negocio testeado está en `Feature` + `Integration`.

## Suites disponibles

- suite completa: `composer test` corre todo `tests/`
- `Unit`: `tests/unit`
- `Database`: `tests/database`
- `Feature`: `tests/feature`
- `Integration`: `tests/integration`

## Comandos

```bash
composer test
composer test:unit
composer test:database
composer test:feature
composer test:integration
```

En Windows también podés ejecutar directamente:

```powershell
php vendor/bin/phpunit --configuration phpunit.xml.dist tests
```

## Organización esperada

- `tests/unit`: servicios, helpers y reglas sin HTTP real.
- `tests/database`: modelos, queries y casos con `DatabaseTestTrait`.
- `tests/feature`: endpoints HTTP, filtros, redirects y contratos JSON/HTML.
- `tests/integration`: integración externa controlada, especialmente DNI/RUC.
- `tests/_support`: seeders, fixtures, factories y utilidades compartidas.

## Convenciones mínimas

- Un archivo por comportamiento o endpoint relevante.
- Nombre de archivo terminado en `Test.php`.
- Nombre de clase alineado al sujeto probado: `UsuarioWriterServiceTest`, `UsuariosControllerFeatureTest`.
- Los datos reproducibles deben vivir en `tests/_support/Database/Seeds` o builders locales del test.
- No pegarle a servicios externos reales dentro de la suite por defecto.

## Base mínima reproducible del dominio admin

Ahora existe una base reproducible suficiente para los flujos priorizados de Sprint 4/5, pero está repartida en dos capas.

### 1. Base canónica de la app (`app/Database`)

Migraciones reales disponibles:

- `roles`
- `personas`
- `usuarios`
- `menus`
- `permisos`

Seeder raíz:

```bash
php spark db:seed AdminBaseSeeder
```

Ese seeder hoy también intenta dejar datos mínimos para:

- `pais`
- `ubigeo_peru_departments`
- `ubigeo_peru_provinces`
- `ubigeo_peru_districts`
- `empresa`

### 2. Complementos de testing (`tests/_support/Database/Migrations`)

La suite feature agrega migraciones de soporte para poder ejecutar en `testing` los flujos nuevos de Sprint 5:

- `pais`
- `ubigeo_peru_departments`
- `ubigeo_peru_provinces`
- `ubigeo_peru_districts`
- `empresa`
- `clientes`

Esto es lo que hace reproducibles en tests los escenarios de `perfil`, `personas` y `ubigeo` sin depender de la BD histórica completa.

Usuario base:

- username: `admin`
- password: `admin123`
- rol: `Administrador` (`id_rol = 1`)

> Para tests, la base canónica sigue saliendo de `app/Database`; `tests/_support` queda disponible para seeders auxiliares o wrappers específicos por suite.

## Advertencias del setup actual

- La suite real hoy está en `Feature` + `Integration`; `Unit` y `Database` todavía no representan el negocio admin completo.
- La base reproducible NO replica todo el esquema histórico: cubre auth/admin y los soportes mínimos para `perfil`, `personas`, `ubigeo`, `empresa` y `clientes` de testing.
- En esta copia falta `spark`, así que los comandos de migración/seed quedan documentados pero no ejecutables hasta restaurar ese entrypoint.
- Sin `--no-coverage`, PHPUnit puede devolver warning/error si no hay Xdebug o PCOV por `failOnWarning="true"`.
- Si un caso depende de SQL legacy o tablas fuera de esta base mínima, va a requerir migraciones/fixtures adicionales.

## Referencias

- `docs/testing-strategy-sprint4.md`
- https://codeigniter.com/user_guide/testing/index.html
- https://docs.phpunit.de/
