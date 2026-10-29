<?php
$uri = service('uri');
$seg = (string) $uri->getSegment(1);
$isAdmin = (int) session()->get('id_rol') === 1;
$uname = session()->get('username') ?? 'Usuario';
$ini = strtoupper(substr(trim((string) $uname), 0, 2) ?: 'US');
$nombre = session()->get('nombre') ?? $uname;
$hora = (int) date('G');
$saludo = match (true) {
    $hora >= 6 && $hora < 12 => 'Buenos días',
    $hora >= 12 && $hora < 19 => 'Buenas tardes',
    default => 'Buenas noches',
};

$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fecha = $meses[(int) date('n')] . ' ' . date('j') . ', ' . date('Y');
?>

<div class="dev-main">
    <div class="dsg-page">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div class="dash-greeting">
                    <h1 class="dash-greeting-title"><?= esc($saludo) ?>, <strong><?= esc($nombre) ?></strong></h1>
                    <p class="dash-greeting-sub">Panel de Administración DSG Perú</p>
                    <div class="dash-date">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <?= date('d/m/Y') ?>
                    </div>
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
                            <span class="dsg-user-avatar"><?= esc($ini) ?></span>
                            <span class="dsg-user-meta">
                                <span class="dsg-user-name"><?= esc($nombre) ?></span>
                                <span class="dsg-user-role"><?= $isAdmin ? 'Administrador' : 'Usuario' ?></span>
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
            $(document).ready(function() {
                var $btn = $('#dsgNotificationBtn');
                var $drop = $('#dsgNotifDropdown');
                var $badge = $('#dsgNotifBadge');
                var $list = $('#dsgNotifList');
                var $countText = $('#dsgNotifCountText');
                var $backdrop = $('#dsgNotifBackdrop');
                var $closeBtn = $('#dsgNotifClose');

                // Mover backdrop + modal al <body>
                $('body').append($backdrop, $drop);

                function openNotif() {
                    $drop.css('display', 'flex').addClass('is-open');
                    $backdrop.addClass('is-open');
                    document.body.classList.add('dsg-notif-open');
                    $btn.attr('aria-expanded', 'true');
                }

                function closeNotif() {
                    $drop.removeClass('is-open').hide();
                    $backdrop.removeClass('is-open');
                    document.body.classList.remove('dsg-notif-open');
                    $btn.attr('aria-expanded', 'false');
                }

                function toggleNotif() {
                    if ($drop.hasClass('is-open')) closeNotif(); else openNotif();
                }

                $btn.attr('aria-haspopup', 'dialog').attr('aria-expanded', 'false');
                $btn.on('click', function(e) {
                    e.stopPropagation();
                    toggleNotif();
                });
                $backdrop.on('click', closeNotif);
                $closeBtn.on('click', closeNotif);
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape' && $drop.hasClass('is-open')) closeNotif();
                });

                var emptyHtml = '<div class="dsg-notif-empty">' +
                    '<span class="material-symbols-outlined">notifications_none</span>' +
                    '<small>No tienes notificaciones pendientes</small>' +
                '</div>';

                function cargarNotificaciones() {
                    $.getJSON("<?= base_url('notificaciones/alertas') ?>")
                        .done(function(data) {
                            var total = data.total || 0;
                            var items = data.items || [];

                            if (total > 0) {
                                $badge.text(total > 99 ? '99+' : total).show();
                                $btn.addClass('has-unread');
                                $countText.text(total + (total === 1 ? ' pendiente' : ' pendientes'));
                                $list.empty();

                                items.forEach(function(item) {
                                    var inicial = (item.cliente || item.titulo || 'N').charAt(0).toUpperCase();
                                    $list.append(
                                        '<a href="' + (item.url || '#') + '" class="dsg-notif-item">' +
                                            '<span class="dsg-notif-dot"></span>' +
                                            '<span class="dsg-notif-avatar">' + inicial + '</span>' +
                                            '<span class="dsg-notif-content">' +
                                                '<span class="dsg-notif-title"><strong>' + item.titulo + ':</strong> ' + item.mensaje + '</span>' +
                                                '<span class="dsg-notif-time">' +
                                                    '<span class="material-symbols-outlined" style="font-size:13px;">schedule</span> ' +
                                                    (item.tiempo || 'Reciente') +
                                                '</span>' +
                                            '</span>' +
                                        '</a>'
                                    );
                                });

                                // Si hay alertas hoy cuyos correos aún no se han enviado, los enviamos en segundo plano (0 lag)
                                if (data.disparar_correos_hoy) {
                                    fetch("<?= base_url('notificaciones/enviar-correos') ?>", { keepalive: true }).catch(function() {});
                                }
                            } else {
                                $badge.hide();
                                $btn.removeClass('has-unread');
                                $countText.text('0 pendientes');
                                $list.html(emptyHtml);
                            }
                        })
                        .fail(function() {
                            $badge.hide();
                            $btn.removeClass('has-unread');
                        });
                }

                // Exponer globalmente por si se asigna un plan nuevo desde un modal
                window.cargarNotificacionesDsg = cargarNotificaciones;

                // ¡IMPORTANTE! Ejecutar al cargar la página y actualizar cada 60 segundos
                cargarNotificaciones();
                setInterval(cargarNotificaciones, 60000);
            });
        </script>