<?php
    $u = $miInfo ?? [];
    $sinHorario = empty($miHorario);
    $horaEntrada = $miHorario['hora_entrada'] ?? null;
    $horaSalida  = $miHorario['hora_salida']  ?? null;
    $diasHorario = $miHorario['dias'] ?? null;
    $esPrac = (session()->get('user_role') ?? '') === 'Practicante';
?>
<style>
    .pf-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    @media (min-width: 900px) { .pf-grid { grid-template-columns: 1fr 1fr; } }
    .pf-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; overflow: hidden; }
    .pf-card-head { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--g-border); }
    .pf-card-head h2 { margin: 0; font-size: 15px; font-weight: 600; font-family: var(--g-font-display); display: flex; align-items: center; gap: 8px; }
    .pf-card-head h2 .material-symbols-outlined { font-size: 20px; color: var(--g-primary); }
    .pf-row { display: flex; gap: 12px; align-items: center; padding: 13px 20px; }
    .pf-row + .pf-row { border-top: 1px solid var(--g-border); }
    .pf-ic { width: 38px; height: 38px; border-radius: 11px; background: var(--g-surface-variant); color: var(--g-text-secondary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pf-ic .material-symbols-outlined { font-size: 19px; }
    .pf-lab { font-size: 11.5px; color: var(--g-text-secondary); }
    .pf-val { font-size: 14px; font-weight: 500; color: var(--g-text); margin-top: 1px; }
    .pf-val.mono { font-family: 'SF Mono', Consolas, monospace; }

    /* Toggle settings */
    .pf-set { display: flex; align-items: center; gap: 14px; padding: 16px 20px; }
    .pf-set + .pf-set { border-top: 1px solid var(--g-border); }
    .pf-set-info { flex: 1; min-width: 0; }
    .pf-set-title { font-size: 14px; font-weight: 600; color: var(--g-text); display: flex; align-items: center; gap: 8px; }
    .pf-set-title .material-symbols-outlined { font-size: 18px; color: var(--g-text-secondary); }
    .pf-set-desc { font-size: 12px; color: var(--g-text-secondary); margin-top: 3px; line-height: 1.45; }
    .pf-toggle { position: relative; width: 44px; height: 26px; border-radius: 999px; background: var(--g-border); border: none; cursor: pointer; flex-shrink: 0; transition: background 180ms; padding: 0; }
    .pf-toggle::after { content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.25); transition: transform 180ms; }
    .pf-toggle.is-on { background: var(--g-primary); }
    .pf-toggle.is-on::after { transform: translateX(18px); }
    .pf-set-state { font-size: 11.5px; font-weight: 600; color: var(--g-text-secondary); min-width: 28px; text-align: right; }
</style>

<?php
    $iniciales = mb_strtoupper(implode('', array_map(static fn ($p) => mb_substr($p, 0, 1), array_slice(preg_split('/\s+/', trim((string) ($u['name'] ?? 'U'))), 0, 2))));
?>

<!-- Identidad -->
<section class="tk-card tk-pf-id tk-anim-item">
    <div class="tk-pf-avatar"><?= esc($iniciales) ?></div>
    <div class="tk-pf-who">
        <h3><?= esc($u['name'] ?? '') ?></h3>
        <p><?= esc($u['email'] ?? '—') ?></p>
        <div class="tk-pf-pills">
            <span class="tk-pill-state tk-st-ok"><?= $esPrac ? 'Practicante' : 'Colaborador' ?></span>
            <?php if (!empty($u['personal_code'])): ?><span class="tk-code"><?= esc($u['personal_code']) ?></span><?php endif; ?>
            <?php if (!empty($u['cargo'])): ?><span class="tk-pill-state tk-st-neutral"><?= esc($u['cargo']) ?></span><?php endif; ?>
        </div>
    </div>
</section>

<div class="tk-grid-2">
    <!-- Mis datos -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head"><div><h3>Mis datos</h3><p>Los cambia tu administrador desde Personal.</p></div></div>
        <div class="tk-kv">
            <div class="tk-kv-row"><span>Nombre</span><span><?= esc($u['name'] ?? '') ?></span></div>
            <div class="tk-kv-row"><span>DNI</span><span class="tk-mono"><?= esc($u['dni'] ?? '—') ?></span></div>
            <div class="tk-kv-row"><span>Correo</span><span><?= esc($u['email'] ?? '—') ?></span></div>
            <div class="tk-kv-row"><span>Cargo</span><span><?= esc(($u['cargo'] ?? '') ?: ($esPrac ? 'Practicante' : 'Colaborador')) ?></span></div>
            <div class="tk-kv-row"><span>Horario</span><span class="tk-mono"><?= $sinHorario ? 'Sin horario asignado' : esc(($horaEntrada ? substr($horaEntrada, 0, 5) : '?') . ' - ' . ($horaSalida ? substr($horaSalida, 0, 5) : '?') . ($diasHorario ? ' · ' . $diasHorario : '')) ?></span></div>
            <div class="tk-kv-row"><span>Biometría</span><span>
                <span class="tk-st <?= !empty($u['huella_registrada']) ? 'tk-st-present' : 'tk-st-justified' ?>">Huella <?= !empty($u['huella_registrada']) ? 'sí' : 'no' ?></span>
                <span class="tk-st <?= !empty($u['rostro_registrado']) ? 'tk-st-present' : 'tk-st-justified' ?>">Rostro <?= !empty($u['rostro_registrado']) ? 'sí' : 'no' ?></span>
            </span></div>
        </div>
    </section>

    <!-- Preferencias -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head"><div><h3>Preferencias</h3><p>Ajustes de este navegador.</p></div></div>
        <div class="tk-kv">
            <div class="tk-set">
                <div class="tk-set-info">
                    <strong>Modo oscuro</strong>
                    <span>Cambia la apariencia del panel a colores oscuros.</span>
                </div>
                <span class="tk-set-state" id="pfDarkState">—</span>
                <button type="button" class="pf-toggle" id="pfDarkToggle" aria-label="Modo oscuro"></button>
            </div>
            <div class="tk-set">
                <div class="tk-set-info">
                    <strong>Notificaciones</strong>
                    <span>Avisos cuando tu administrador resuelva tus incidencias.</span>
                </div>
                <span class="tk-set-state" id="pfNotifState">—</span>
                <button type="button" class="pf-toggle" id="pfNotifToggle" aria-label="Notificaciones"></button>
            </div>
            <div class="tk-set">
                <div class="tk-set-info">
                    <strong>Cuenta</strong>
                    <span>Sesión iniciada como <?= esc($u['email'] ?? session()->get('user_email') ?? '') ?>.</span>
                </div>
                <a href="<?= base_url('logout') ?>" class="dev-btn dev-btn-outline dev-btn-sm">
                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                </a>
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    /* ── Modo oscuro (mismo localStorage que el sidebar: devDarkMode) ── */
    var darkBtn = document.getElementById('pfDarkToggle');
    var darkSt  = document.getElementById('pfDarkState');

    function renderDark() {
        var on = localStorage.getItem('devDarkMode') === 'true';
        darkBtn.classList.toggle('is-on', on);
        darkSt.textContent = on ? 'Activado' : 'Desactivado';
        document.documentElement.classList.toggle('dark', on);
    }
    darkBtn.addEventListener('click', function () {
        var on = localStorage.getItem('devDarkMode') !== 'true';
        localStorage.setItem('devDarkMode', String(on));
        renderDark();
        // Sincronizar iconos del sidebar si existen
        var di = document.getElementById('darkModeIcon');
        var dl = document.getElementById('darkModeLabel');
        var mi = document.getElementById('mobileDarkIcon');
        if (di) di.textContent = on ? 'light_mode' : 'dark_mode';
        if (dl) dl.textContent = on ? 'Modo claro' : 'Modo oscuro';
        if (mi) mi.textContent = on ? 'light_mode' : 'dark_mode';
    });
    renderDark();

    /* ── Notificaciones push reales (Web Push) ── */
    var notifBtn = document.getElementById('pfNotifToggle');
    var notifSt  = document.getElementById('pfNotifState');
    var notifBusy = false;

    function renderNotif() {
        var supported = window.dsgPush && window.dsgPush.soportado();
        var perm = supported ? window.dsgPush.permiso() : 'unsupported';
        var enabled = localStorage.getItem('empleado_push_enabled') === 'true';
        var on = supported && perm === 'granted' && enabled;
        notifBtn.classList.toggle('is-on', on);
        if (!supported)          notifSt.textContent = 'No disponible';
        else if (perm === 'denied') notifSt.textContent = 'Bloqueado';
        else if (on)             notifSt.textContent = 'Activado';
        else                     notifSt.textContent = 'Desactivado';
    }
    notifBtn.addEventListener('click', function () {
        if (notifBusy) return;
        if (!window.dsgPush || !window.dsgPush.soportado()) {
            toastPush('Tu navegador no permite notificaciones push', 'error', 'error');
            return;
        }
        var enabled = localStorage.getItem('empleado_push_enabled') === 'true';
        notifBusy = true;
        if (enabled) {
            window.dsgPush.desactivar().then(function () {
                notifBusy = false;
                renderNotif();
                toastPush('Notificaciones desactivadas', 'notifications_off', 'warn');
            });
            return;
        }
        window.dsgPush.activar().then(function (r) {
            notifBusy = false;
            renderNotif();
            if (r.ok) {
                toastPush('Notificaciones activadas', 'check_circle', 'check');
            } else if (r.msj && r.msj.indexOf('denegado') !== -1) {
                toastPush('Permiso bloqueado. Actívalo en la configuración del navegador.', 'block', 'error');
            } else {
                toastPush(r.msj || 'No se activaron las notificaciones', 'notifications_off', 'warn');
            }
        }).catch(function () {
            notifBusy = false;
            toastPush('Error al activar notificaciones', 'error', 'error');
        });
    });
    renderNotif();
    // dsgPush se define en pwa-js (va después del contenido): re-render cuando exista
    (function () {
        var n = 0;
        var t = setInterval(function () {
            n++;
            if (window.dsgPush || n > 50) {
                clearInterval(t);
                renderNotif();
            }
        }, 100);
    })();

    /* ── Toast estilo snackbar ── */
    function toastPush(msg, icon, tipo) {
        var wrap = document.getElementById('pfToastWrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'pfToastWrap';
            wrap.style.cssText = 'position:fixed;right:18px;bottom:18px;display:flex;flex-direction:column;gap:10px;z-index:1100;';
            document.body.appendChild(wrap);
        }
        var t = document.createElement('div');
        var color = tipo === 'check' ? '#81c995' : (tipo === 'error' ? '#f28b82' : '#fdd663');
        t.style.cssText = 'display:flex;align-items:flex-start;gap:12px;background:#1f1f1f;color:#e8eaed;border-radius:12px;padding:13px 16px;min-width:280px;max-width:360px;box-shadow:0 8px 24px rgba(0,0,0,.25);font-size:13.5px;transform:translateY(12px);opacity:0;transition:all .28s cubic-bezier(.2,.9,.3,1);';
        t.innerHTML = '<span class="material-symbols-outlined" style="font-size:19px;color:' + color + ';">' + (icon || 'info') + '</span><span style="flex:1;">' + msg + '</span>';
        wrap.appendChild(t);
        requestAnimationFrame(function () { t.style.transform = 'translateY(0)'; t.style.opacity = '1'; });
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 3400);
    }
})();
</script>
