## DSG Perú — guía operativa rápida

Si necesitás levantar, configurar, testear y validar lo principal del proyecto, arrancá por acá:

- `docs/operacion-rapida-sprint4.md`
- `docs/testing-strategy-sprint4.md`
- `tests/README.md`
- `docs/admin-modulos-criticos.md`
- `docs/checklist-smoke-sprint4.md`

> Estado documental: varios archivos conservan nombre `sprint4`, pero su contenido quedó actualizado al estado real después de Sprint 6.

> Cobertura útil hoy: existen suites reales para `auth`, autorización admin, `usuarios`, `permisos`, `perfil`, `personas`, `clientes`, `ubigeo` e integración controlada de lookup DNI/RUC. El detalle operativo vive en `tests/README.md`, `docs/testing-strategy-sprint4.md` y `docs/admin-modulos-criticos.md`.

> Nota: esta copia del proyecto no incluye el archivo `spark`. Los comandos de migraciones/seeders están documentados, pero requieren restaurar ese entrypoint de CodeIgniter para ejecutarse desde esta copia.

> Nota Git: se corrigió `.git/config` para quitar marcadores de conflicto que rompían la CLI. `git diff --stat` y `git config --local --list` ya funcionan; `git status` sigue fallando por objetos Git locales faltantes. Ver `docs/operacion-rapida-sprint4.md`.

---

