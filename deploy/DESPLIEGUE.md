# Subir Asisten DSG + Panel de clientes a cPanel

Este zip trae los dos sistemas (Asisten y el panel de clientes). No trae
`.env`, logs, sesiones ni datos: eso vive en el servidor.

## Primera vez

1. **PHP** · cPanel → *Seleccionar versión de PHP* → **8.2 o superior**.
   En *Extensiones* activa: `intl`, `mbstring`, `mysqli`, `curl`, `openssl`,
   `sodium`, `gd`, `fileinfo`, `json`.
2. **SSL** · cPanel → *SSL/TLS Status* → *Run AutoSSL*. Sin HTTPS el navegador
   bloquea la cámara (rostro), la huella y las notificaciones.
3. **Base de datos** · cPanel → *Bases de datos MySQL*:
   crea la base y un usuario, y dale **todos los privilegios** sobre la base.
   Luego en *phpMyAdmin* → la base → **Importar** → `produccion_base.sql`
   (lo genera Stev aparte; trae las tablas de los dos sistemas y los datos).
4. **Archivos** · *Administrador de archivos* → sube `asisten-cpanel.zip`
   a tu carpeta de inicio `/home/USUARIO/` (**fuera de `public_html`**) y
   extráelo: queda `/home/USUARIO/asisten/`.
5. **Dominio** · cPanel → *Dominios* → edita el dominio o subdominio →
   **Raíz del documento** = `asisten/public`.
   - Si el hosting no deja cambiarla: extrae el zip dentro de `public_html`,
     mueve el contenido de `asisten/` a `public_html/` y renombra
     `htaccess-raiz` a `.htaccess`. Ese archivo manda todo a `public/` y
     protege `app/`, `writable/`, `vendor/` y `.env`.
6. **.env** · renombra `env.ejemplo` a `.env` (junto a `spark`) y llénalo:
   dominio con `https://`, datos de la base, `CRON_TOKEN`, claves VAPID
   (copiarlas del `.env` local) y el correo SMTP de cPanel.
7. **Permisos** · la carpeta `writable/` y todo lo de adentro: **755**
   (o 775 si el hosting lo pide).
8. **Fotos de rostro y evidencias** · la primera vez el zip se arma con
   `--con-uploads` y ya vienen en `writable/uploads/` (solo las que la base
   usa). En las actualizaciones no van, así no se pisan las del servidor.
9. **Huellas** · quedan atadas al dominio donde se registraron: cada persona
   debe registrar su huella otra vez desde el panel en el dominio nuevo.
10. Abre el dominio. Las tablas nuevas de Asisten se crean solas.
    Si la base vino vacía, crea la cuenta Dev desde *Terminal* de cPanel:
    `cd ~/asisten && php spark asisten:crear-dev`

## Tareas automáticas (cPanel → Cron Jobs)

Cambia `USUARIO` y la ruta de PHP (en cPanel suele ser `/usr/local/bin/php`
o `/opt/cpanel/ea-php82/root/usr/bin/php`). La hora del cron es la del
**servidor**: si está en UTC, las 23:55 de Lima son las **04:55**.

| Qué | Cuándo (hora de Lima) | Comando |
|---|---|---|
| Cierre del día de asistencia (faltas y salidas sin marcar) | 23:55 todos los días | `cd /home/USUARIO/asisten && /usr/local/bin/php spark asistencia:cierre >> writable/logs/cron.log 2>&1` |
| Avisos de vencimiento del panel (correo/WhatsApp) | 08:00 todos los días | `cd /home/USUARIO/asisten && /usr/local/bin/php spark vencimientos:enviar >> writable/logs/cron.log 2>&1` |

## Actualizar después (cuando haya cambios)

1. En la PC: `bash deploy/armar-zip.sh` (sin `--con-uploads`).
2. En cPanel: sube el zip a `/home/USUARIO/` y extráelo ahí (sobrescribir).
   No pisa el `.env`, los logs, las sesiones ni las fotos del servidor.
3. Abre el sistema una vez: si hay tablas o columnas nuevas, se crean solas.

## Datos

- Pasar los datos de Asisten de una PC a otra:
  `php spark asisten:exportar` → `php spark asisten:importar <archivo>`.
- Los `.sql` con datos (DNIs, correos, contraseñas cifradas) nunca se suben
  a GitHub ni a carpetas públicas.
