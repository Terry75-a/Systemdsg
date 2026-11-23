<?php
$uri = service('uri');
$seg = (string) $uri->getSegment(1);
$isAdmin = (int) session()->get('id_rol') === 1;
$uname = session()->get('username') ?? 'Usuario';
$notifUserKey = (string) (session()->get('id_usuario') ?? $uname);
$ini = strtoupper(substr(trim((string) $uname), 0, 2) ?: 'US');
$nombre = session()->get('nombre') ?? $uname;
$avatarPath = trim((string) (session()->get('imagen') ?? ''));
if ($avatarPath === '') {
    $idPersona = (int) session()->get('id_persona');
    if ($idPersona > 0) {
        $personaAvatar = (new \App\Models\PersonaModel())->select('imagen')->find($idPersona);
        if (!empty($personaAvatar->imagen)) {
            $avatarPath = trim((string) $personaAvatar->imagen);
            session()->set('imagen', $avatarPath);
        }
    }
}

$avatarUrl = '';
if ($avatarPath !== '') {
    if (preg_match('#^https?://#i', $avatarPath)) {
        $avatarUrl = $avatarPath;
    } else {
        $rel = ltrim($avatarPath, '/');
        if (is_file(FCPATH . $rel)) {
            $avatarUrl = base_url($rel) . '?v=' . filemtime(FCPATH . $rel);
        }
    }
}
$hora = (int) date('G');
$saludo = match (true) {
    $hora >= 6 && $hora < 12 => 'Buenos días',
    $hora >= 12 && $hora < 19 => 'Buenas tardes',
    default => 'Buenas noches',
};

$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fecha = $meses[(int) date('n')] . ' ' . date('j') . ', ' . date('Y');
?>
<style>
    .dsg-user-avatar,
    .dev-sidebar-avatar {
        overflow: hidden;
        padding: 0;
    }

    .dsg-user-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 50%;
    }
</style>
<div class="dev-main">
    <div class="dsg-page">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <?php
                // Nombre de la página en la barra (como el header del AppShell de Trackin)
                $paginas = [
                    'dashboard' => 'Inicio',
                    'personas' => 'Clientes',
                    'personasadd' => 'Registrar persona',
                    'planes' => 'Planes',
                    'tipo_plan' => 'Tipos de plan',
                    'tipo_planadd' => 'Tipos de plan',
                    'calendario' => 'Calendario',
                    'notificaciones' => 'Notificaciones',
                    'perfil' => 'Mi perfil',
                    'usuarios' => 'Usuarios',
                    'permisos' => 'Permisos',
                    'menu' => 'Menú',
                    'configuracion' => 'Configuración',
                ];
                $paginaActual = $paginas[strtolower($seg)] ?? ($titulo ?? 'Panel');
                ?>
                <div class="dash-greeting tk-shell-head">
                    <h1 class="tk-shell-title"><?= esc($paginaActual) ?></h1>
                </div>
                <div class="dev-topbar-actions">
                    <!-- Notificaciones estilo asisten -->
                    <div class="dsg-notification-wrap">
                        <button type="button" class="dsg-dark-btn dsg-notification-btn" id="dsgNotificationBtn" title="Notificaciones" aria-label="Ver notificaciones">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="dsg-notif-badge" id="dsgNotifBadge" style="display: none;">0</span>
                        </button>

                        <!-- Backdrop del modal de notificaciones -->
                        <div class="dsg-notif-backdrop" id="dsgNotifBackdrop" aria-hidden="true"></div>

                        <!-- Modal minimal de notificaciones (centrado en desktop · bottom-sheet en mobile) -->
                        <div class="dsg-notif-dropdown" id="dsgNotifDropdown" role="dialog" aria-modal="true" aria-labelledby="dsgNotifTitle" style="display: none;">
                            <div class="dsg-notif-header">
                                <span id="dsgNotifTitle"><span class="material-symbols-outlined" style="font-size:19px;">notifications</span> Notificaciones</span>
                                <small class="dsg-notif-count" id="dsgNotifCountText">0 pendientes</small>
                                <button type="button" class="dsg-notif-close" id="dsgNotifClose" aria-label="Cerrar notificaciones">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <div class="dsg-notif-list" id="dsgNotifList">
                                <div class="dsg-notif-empty">
                                    <span class="material-symbols-outlined">notifications_none</span>
                                    <small>No tienes notificaciones pendientes</small>
                                </div>
                            </div>
                            <div class="dsg-notif-foot">
                                <a href="<?= base_url('notificaciones') ?>">
                                    <span class="material-symbols-outlined" style="font-size:17px;">rule_settings</span> Ver todas las alertas
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="dsg-dark-btn" id="dsgDarkBtn" title="Modo oscuro" aria-label="Alternar modo oscuro">
                        <span class="material-symbols-outlined" id="dsgDarkIcon">dark_mode</span>
                    </button>
                    <div class="dsg-user-chip">
                        <button type="button" class="dsg-user-btn" aria-haspopup="true" aria-expanded="false">
                            <span class="dsg-user-avatar">
                                <?php if ($avatarUrl !== ''): ?>
                                    <img src="<?= esc($avatarUrl) ?>"
                                        alt="<?= esc($nombre) ?>"
                                        class="dsg-user-avatar-img"
                                        onerror="this.parentElement.textContent='<?= esc($ini, 'js') ?>';">
                                <?php else: ?>
                                    <?= esc($ini) ?>
                                <?php endif; ?>
                            </span>
                            <span class="material-symbols-outlined dsg-user-chevron">expand_more</span>
                        </button>
                        <div class="dsg-user-menu">
                            <a href="<?= base_url('perfil') ?>" class="dsg-user-menu-link">
                                <span class="material-symbols-outlined">person</span> Mi Perfil
                            </a>
                            <?php if ($isAdmin): ?>
                                <a href="<?= base_url('configuracion') ?>" class="dsg-user-menu-link">
                                    <span class="material-symbols-outlined">settings</span> Configuración
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('') ?>" class="dsg-user-menu-link">
                                <span class="material-symbols-outlined">language</span> Sitio web
                            </a>
                            <div class="dsg-user-menu-sep"></div>
                            <form method="post" action="<?= base_url('logout') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="dsg-user-menu-link dsg-user-menu-logout">
                                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content">

            <script>
                jQuery(document).ready(function() {
                    var btn = jQuery('#dsgNotificationBtn');
                    var drop = jQuery('#dsgNotifDropdown');
                    var badge = jQuery('#dsgNotifBadge');
                    var list = jQuery('#dsgNotifList');
                    var countText = jQuery('#dsgNotifCountText');
                    var backdrop = jQuery('#dsgNotifBackdrop');
                    var closeBtn = jQuery('#dsgNotifClose');

                    var SEEN_STORAGE_KEY = 'dsg_notif_seen_' + <?= json_encode($notifUserKey) ?>;
                    var SEEN_TTL_MS = 30 * 24 * 60 * 60 * 1000;
                    var currentItems = [];

                    // Mover backdrop + modal al <body>
                    jQuery('body').append(backdrop, drop);

                    function escNotif(value) {
                        var map = {
                            '&': '&' + 'amp;',
                            '<': '&' + 'lt;',
                            '>': '&' + 'gt;',
                            '"': '&' + 'quot;',
                            "'": '&' + '#039;'
                        };
                        return String(value == null ? '' : value).replace(/[&<>"']/g, function(c) {
                            return map[c];
                        });
                    }

                    function getItemKey(item) {
                        if (item && item.id != null && item.id !== '') {
                            return 'id:' + String(item.id) + '|' + String(item.tiempo || '');
                        }
                        return [
                            item && item.url ? item.url : '',
                            item && item.titulo ? item.titulo : '',
                            item && item.mensaje ? item.mensaje : '',
                            item && item.tiempo ? item.tiempo : ''
                        ].join('|');
                    }

                    function loadSeenMap() {
                        try {
                            var raw = localStorage.getItem(SEEN_STORAGE_KEY);
                            var parsed = raw ? JSON.parse(raw) : {};
                            if (!parsed || typeof parsed !== 'object') return {};

                            var now = Date.now();
                            var cleaned = {};
                            Object.keys(parsed).forEach(function(k) {
                                if (now - Number(parsed[k] || 0) < SEEN_TTL_MS) {
                                    cleaned[k] = parsed[k];
                                }
                            });
                            return cleaned;
                        } catch (e) {
                            return {};
                        }
                    }

                    function saveSeenMap(map) {
                        try {
                            localStorage.setItem(SEEN_STORAGE_KEY, JSON.stringify(map));
                        } catch (e) {}
                    }

                    function actualizarBadge(unreadCount, totalCount) {
                        if (unreadCount > 0) {
                            badge.text(unreadCount > 99 ? '99+' : unreadCount).show();
                            btn.addClass('has-unread');
                            countText.text(unreadCount + (unreadCount === 1 ? ' nueva' : ' nuevas'));
                        } else {
                            badge.hide().text('0');
                            btn.removeClass('has-unread');
                            if (totalCount > 0) {
                                countText.text(totalCount + (totalCount === 1 ? ' vista' : ' vistas'));
                            } else {
                                countText.text('0 pendientes');
                            }
                        }
                    }

                    function marcarActualesComoVistas() {
                        if (!currentItems.length) {
                            actualizarBadge(0, 0);
                            return;
                        }
                        var seenMap = loadSeenMap();
                        var now = Date.now();
                        currentItems.forEach(function(item) {
                            seenMap[getItemKey(item)] = now;
                        });
                        saveSeenMap(seenMap);
                        actualizarBadge(0, currentItems.length);
                    }

                    function openNotif() {
                        drop.css('display', 'flex').addClass('is-open');
                        backdrop.addClass('is-open');
                        document.body.classList.add('dsg-notif-open');
                        btn.attr('aria-expanded', 'true');
                        // Al abrir la campana, las alertas cargadas pasan a vistas y el badge desaparece
                        marcarActualesComoVistas();
                    }

                    function closeNotif() {
                        drop.removeClass('is-open').hide();
                        backdrop.removeClass('is-open');
                        document.body.classList.remove('dsg-notif-open');
                        btn.attr('aria-expanded', 'false');
                        // Quitar el punto verde de no leído en los ítems que ya se mostraron
                        list.find('.dsg-notif-dot').css('visibility', 'hidden');
                    }

                    function toggleNotif() {
                        if (drop.hasClass('is-open')) closeNotif();
                        else openNotif();
                    }

                    btn.attr('aria-haspopup', 'dialog').attr('aria-expanded', 'false');
                    btn.on('click', function(e) {
                        e.stopPropagation();
                        toggleNotif();
                    });
                    backdrop.on('click', closeNotif);
                    closeBtn.on('click', closeNotif);
                    jQuery(document).on('keydown', function(e) {
                        if (e.key === 'Escape' && drop.hasClass('is-open')) closeNotif();
                    });

                    var emptyHtml = '<div class="dsg-notif-empty">' +
                        '<span class="material-symbols-outlined">notifications_none</span>' +
                        '<small>No tienes notificaciones pendientes</small>' +
                        '</div>';

                    function cargarNotificaciones() {
                        jQuery.getJSON("<?= base_url('notificaciones/alertas') ?>")
                            .done(function(data) {
                                var items = (data && Array.isArray(data.items)) ? data.items : [];
                                currentItems = items;

                                if (items.length > 0) {
                                    var seenMap = loadSeenMap();
                                    var isOpen = drop.hasClass('is-open');
                                    var unreadCount = 0;

                                    list.empty();

                                    items.forEach(function(item) {
                                        var key = getItemKey(item);
                                        var isSeen = Boolean(seenMap[key]);

                                        if (isOpen && !isSeen) {
                                            seenMap[key] = Date.now();
                                            isSeen = true;
                                        } else if (!isSeen) {
                                            unreadCount++;
                                        }

                                        var inicial = (item.cliente || item.titulo || 'N').charAt(0).toUpperCase();
                                        var dotStyle = isSeen ? ' style="visibility:hidden;"' : '';

                                        list.append(
                                            '<a href="' + escNotif(item.url || '#') + '" class="dsg-notif-item">' +
                                            '<span class="dsg-notif-dot"' + dotStyle + '></span>' +
                                            '<span class="dsg-notif-avatar">' + escNotif(inicial) + '</span>' +
                                            '<span class="dsg-notif-content">' +
                                            '<span class="dsg-notif-title"><strong>' + escNotif(item.titulo) + ':</strong> ' + escNotif(item.mensaje) + '</span>' +
                                            '<span class="dsg-notif-time">' +
                                            '<span class="material-symbols-outlined" style="font-size:13px;">schedule</span> ' +
                                            escNotif(item.tiempo || 'Reciente') +
                                            '</span>' +
                                            '</span>' +
                                            '</a>'
                                        );
                                    });

                                    if (isOpen) {
                                        saveSeenMap(seenMap);
                                    }

                                    actualizarBadge(unreadCount, items.length);

                                    if (data.disparar_correos_hoy) {
                                        fetch("<?= base_url('notificaciones/enviar-correos') ?>", {
                                            keepalive: true
                                        }).catch(function() {});
                                    }
                                } else {
                                    actualizarBadge(0, 0);
                                    list.html(emptyHtml);
                                }
                            })
                            .fail(function() {
                                badge.hide();
                                btn.removeClass('has-unread');
                            });
                    }

                    // Exponer globalmente por si se asigna un plan nuevo desde un modal
                    window.cargarNotificacionesDsg = cargarNotificaciones;

                    cargarNotificaciones();
                    setInterval(cargarNotificaciones, 60000);
                });
            </script>