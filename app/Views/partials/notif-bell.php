<?php
/**
 * Campana de notificaciones + modal (compartida).
 * $contexto = 'admin' | 'empleado' — elige los endpoints correctos.
 */
$contexto = $contexto ?? 'admin';
$baseList = $contexto === 'empleado' ? 'mi-panel/notificaciones' : 'admin/notificaciones';
$basePost = $baseList . '/marcar-leidas';
?>
<style>
    /* ── Campana (fija arriba-derecha, alineada al topbar) ── */
    .admin-notif-bell {
        position: fixed; top: 18px; right: 24px; z-index: 900;
        width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--g-surface, #fff);
        border: 1px solid var(--g-border, #e5e7eb);
        color: var(--g-text, #1f2937);
        cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: background 160ms, box-shadow 160ms;
    }
    .admin-notif-bell:hover { background: var(--g-surface-variant, #f3f4f6); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
    .admin-notif-bell .material-symbols-outlined { font-size: 22px; }
    .admin-notif-badge {
        position: absolute; top: 2px; right: 2px;
        min-width: 18px; height: 18px; padding: 0 5px;
        border-radius: 9px; background: var(--g-error, #ea4335); color: #fff;
        font-size: 11px; font-weight: 600; line-height: 18px; text-align: center;
        box-shadow: 0 0 0 2px var(--g-surface, #fff);
        font-family: var(--g-font, inherit);
    }

    /* ── Lista dentro del modal ── */
    .notif-list { display: flex; flex-direction: column; }
    .notif-item {
        display: flex; gap: 12px; align-items: flex-start;
        padding: 12px; border-radius: 12px; cursor: pointer;
        transition: background 160ms;
    }
    .notif-item:hover { background: var(--g-surface-variant); }
    .notif-item.unread { background: rgba(27,122,66,.07); }
    .notif-item-ico {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(27,122,66,.10); color: var(--g-primary);
    }
    .notif-item-ico .material-symbols-outlined { font-size: 18px; }
    .notif-item-body { flex: 1; min-width: 0; }
    .notif-item-title { font-size: .875rem; font-weight: 600; color: var(--g-text); }
    .notif-item-msg { font-size: .78rem; color: var(--g-text-secondary); margin-top: 2px; line-height: 1.45; }
    .notif-item-time { font-size: .7rem; color: var(--g-text-secondary); margin-top: 4px; opacity: .8; }
    .notif-empty { text-align: center; padding: 36px 12px; color: var(--g-text-secondary); font-size: .85rem; }
    .notif-empty .material-symbols-outlined { font-size: 40px; display: block; margin: 0 auto 8px; opacity: .45; }
    .notif-mark-all {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--g-primary); color: #fff; border: none;
        border-radius: 10px; padding: 9px 14px; cursor: pointer;
        font-size: .82rem; font-weight: 600; font-family: inherit;
    }
    .notif-mark-all:hover { filter: brightness(1.08); }
    .notif-mark-all .material-symbols-outlined { font-size: 17px; }
    @media (max-width: 640px) {
        .admin-notif-bell { top: 12px; right: 14px; }
    }
</style>

<button type="button" class="admin-notif-bell" id="notifBellBtn" title="Notificaciones" aria-label="Ver notificaciones">
    <span class="material-symbols-outlined">notifications</span>
    <span class="admin-notif-badge" id="notifBadge" hidden>0</span>
</button>

<!-- Token CSRF propio (no depender de formularios de la página) -->
<input type="hidden" name="csrf_test_name" value="<?= csrf_hash() ?>">

<!-- Modal: bandeja de notificaciones -->
<div class="dev-modal-overlay" id="notifModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2><span class="material-symbols-outlined">notifications</span> Notificaciones</h2>
            <button type="button" class="dev-modal-close" data-mcnotif><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body">
            <div class="notif-list" id="notifList">
                <div class="notif-empty"><span class="material-symbols-outlined">progress_activity</span>Cargando…</div>
            </div>
        </div>
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn dev-btn-text" data-mcnotif>Cerrar</button>
            <button type="button" class="notif-mark-all" id="notifMarkAll"><span class="material-symbols-outlined">done_all</span> Marcar todas como leídas</button>
        </div>
    </div>
</div>

<script>
(function () {
    const bell  = document.getElementById('notifBellBtn');
    const badge = document.getElementById('notifBadge');
    const modal = document.getElementById('notifModal');
    const list  = document.getElementById('notifList');
    const markAllBtn = document.getElementById('notifMarkAll');
    if (!bell || !badge || !modal || !list) return;

    const csrfInput = document.querySelector('input[name="csrf_test_name"]');
    const URL_LIST  = '<?= base_url($baseList) ?>';
    const URL_POST  = '<?= base_url($basePost) ?>';
    const URL_BADGE = '<?= base_url($baseList) ?>/badge';
    const URL_HOME  = '<?= rtrim(base_url('/'), '/') ?>';

    function setBadge(n) {
        n = parseInt(n) || 0;
        if (n > 0) { badge.hidden = false; badge.textContent = n > 99 ? '99+' : String(n); }
        else { badge.hidden = true; }
        // Badge del ícono (Android/iOS/Chrome)
        try {
            if (n > 0 && navigator.setAppBadge) navigator.setAppBadge(n);
            else if (n <= 0 && navigator.clearAppBadge) navigator.clearAppBadge();
        } catch (e) {}
    }

    // Hook para que el tiempo real (SSE/polling) incremente el contador
    window._notifBadgeInc = function () {
        setBadge((parseInt(badge.textContent) || 0) + 1);
    };

    function iconoNotif(tipo) {
        tipo = String(tipo || '');
        if (tipo.indexOf('horario') >= 0) return 'event';
        if (tipo.indexOf('incidencia') >= 0) return 'report';
        if (tipo.indexOf('asistencia') >= 0) return 'fact_check';
        return 'notifications_active';
    }

    function fmtFecha(fecha) {
        if (!fecha) return '';
        try {
            const d = new Date(String(fecha).replace(' ', 'T'));
            if (isNaN(d.getTime())) return fecha;
            return d.toLocaleString('es-PE', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        } catch (e) { return fecha; }
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function abrir() {
        modal.classList.add('is-open');
        cargar();
    }
    function cerrar() { modal.classList.remove('is-open'); }

    function cargar() {
        list.innerHTML = '<div class="notif-empty"><span class="material-symbols-outlined">progress_activity</span>Cargando…</div>';
        fetch(URL_LIST, { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) return;
                setBadge(d.no_leidas);
                if (!d.notificaciones || !d.notificaciones.length) {
                    list.innerHTML = '<div class="notif-empty"><span class="material-symbols-outlined">notifications_off</span>No tienes notificaciones</div>';
                    return;
                }
                list.innerHTML = d.notificaciones.map(function (n) {
                    var link = '';
                    try { link = (JSON.parse(n.datos || '{}') || {}).link || ''; } catch (e) {}
                    return '<div class="notif-item' + (parseInt(n.leida) ? '' : ' unread') + '" data-id="' + parseInt(n.id) + '"' + (link ? ' data-link="' + escapeHtml(link) + '"' : '') + '>'
                        + '<div class="notif-item-ico"><span class="material-symbols-outlined">' + iconoNotif(n.tipo) + '</span></div>'
                        + '<div class="notif-item-body">'
                        + '<div class="notif-item-title">' + escapeHtml(n.titulo) + '</div>'
                        + '<div class="notif-item-msg">' + escapeHtml(n.mensaje) + '</div>'
                        + '<div class="notif-item-time">' + fmtFecha(n.created_at) + '</div>'
                        + '</div></div>';
                }).join('');
                list.querySelectorAll('.notif-item').forEach(function (el) {
                    el.addEventListener('click', function () { marcarUna(el.getAttribute('data-id'), el); });
                });
            })
            .catch(function () {
                list.innerHTML = '<div class="notif-empty"><span class="material-symbols-outlined">error</span>No se pudo cargar. Intenta de nuevo.</div>';
            });
    }

    function postLeidas(id, alTerminar) {
        const fd = new FormData();
        if (csrfInput) fd.append(csrfInput.name, csrfInput.value);
        if (id) fd.append('id', id);
        fetch(URL_POST, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d.ok) { setBadge(d.no_leidas); if (alTerminar) alTerminar(); } })
            .catch(function () {});
    }

    function marcarUna(id, el) {
        var link = el ? el.getAttribute('data-link') : '';
        function ir() {
            if (el) el.classList.remove('unread');
            if (link) window.location.href = URL_HOME + '/' + link.replace(/^\//, '');
        }
        if (!el || !el.classList.contains('unread')) { ir(); return; }
        postLeidas(id, ir);
    }

    function marcarTodas() {
        postLeidas(null, function () {
            list.querySelectorAll('.notif-item').forEach(function (el) { el.classList.remove('unread'); });
        });
    }

    bell.addEventListener('click', abrir);
    if (markAllBtn) markAllBtn.addEventListener('click', marcarTodas);
    modal.querySelectorAll('[data-mcnotif]').forEach(function (b) { b.addEventListener('click', cerrar); });
    modal.addEventListener('mousedown', function (e) { if (e.target === modal) cerrar(); });

    // Contador inicial + refresco ligero del badge (endpoint mínimo, no la lista)
    function refrescarBadge() {
        fetch(URL_BADGE, { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d.ok) setBadge(d.no_leidas); })
            .catch(function () {});
    }
    refrescarBadge();
    setInterval(refrescarBadge, 60000);
})();
</script>
