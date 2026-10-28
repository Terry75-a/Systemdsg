<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Mi Panel · DSG') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/practicante.css?v=20260924') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tk-dashboard.css?v=' . filemtime(FCPATH . 'css/index/components/tk-dashboard.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tk-panel.css?v=' . filemtime(FCPATH . 'css/index/components/tk-panel.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?= view('partials/practicante-sidebar', ['activePage' => $activePage ?? 'dashboard']) ?>

    <div class="dev-main">
        <?php
            $diasEs  = ['', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
            $nombre  = (string) (session()->get('user_name') ?? 'Colaborador');
            $tkTitle = ($activePage ?? 'dashboard') === 'dashboard'
                ? 'Hola, ' . (explode(' ', trim($nombre))[0] ?: $nombre)
                : trim(str_replace('· DSG', '', (string) ($pageTitle ?? 'Mi Panel')));
        ?>
        <div class="dev-content tk-dash tk-mp">
            <div class="tk-title">
                <div>
                    <h1><?= esc($tkTitle) ?></h1>
                    <p><?= esc($greetingSub ?? 'Bienvenido a tu panel.') ?></p>
                </div>
                <div class="tk-title-actions">
                    <span class="tk-mp-chip">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <?= esc(ucfirst($diasEs[(int) date('N')])) ?>, <?= date('d/m/Y') ?>
                    </span>
                    <span class="tk-mp-chip tk-mp-num" id="praClockTrack">--:--</span>
                </div>
            </div>
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>
            <?= $slot ?? '' ?>
        </div>
    </div>
</div>

<script>window.__sseDisponible = <?= json_encode(PHP_SAPI !== 'cli-server') ?>;</script>
<script src="<?= base_url('js/realtime.js') ?>"></script>
    <script>
        // Reloj en vivo
        (function() {
            var clock = document.getElementById('praClock');
            var clockSec = document.getElementById('praClockSec');
            var clockTrack = document.getElementById('praClockTrack');
            function tick() {
                var d = new Date();
                var hh = String(d.getHours()).padStart(2, '0');
                var mm = String(d.getMinutes()).padStart(2, '0');
                var ss = String(d.getSeconds()).padStart(2, '0');
                if (clock) clock.textContent = hh + ':' + mm;
                if (clockSec) clockSec.textContent = hh + ':' + mm + ':' + ss;
                if (clockTrack) clockTrack.textContent = hh + ':' + mm;
            }
            tick();
            setInterval(tick, 1000);
        })();

        // Auto-inicialización SSE según página
        document.addEventListener('DOMContentLoaded', function() {
            // Admin asistencias
            if (window.location.pathname.startsWith('/admin/asistencias')) {
                window.realtimeAdmin = new RealtimeClient('/admin/realtime/asistencias');
                window.realtimeAdmin.on('asistencia_nueva', function(data) {
                    if (window.agregarFilaAsistencia) window.agregarFilaAsistencia(data.datos);
                });
                window.realtimeAdmin.on('asistencia_editada', function(data) {
                    if (window.actualizarFilaAsistencia) window.actualizarFilaAsistencia(data.datos);
                });
                window.realtimeAdmin.connect();
            }

            // Empleado panel
            if (window.location.pathname.startsWith('/mi-panel')) {
                window.realtimeEmpleado = new RealtimeClient('/mi-panel/realtime/asistencias');
                window.realtimeEmpleado.on('asistencia_actualizada', function(data) {
                    if (window.actualizarUIAsistencia) window.actualizarUIAsistencia(data.asistencia);
                });
                window.realtimeEmpleado.on('alerta', function(data) {
                    if (window.mostrarAlerta) window.mostrarAlerta(data.datos);
                });
                window.realtimeEmpleado.connect();
            }

            // ════════════════════════════════════════════
            // POLLING FALLBACK (cPanel compatible, universal)
            // ════════════════════════════════════════════
            (function() {
                var ultimoEstado = null;
                var pollingActivo = false;

                window.iniciarPollingEstado = function() {
                    if (pollingActivo) return;
                    pollingActivo = true;
                    
                    function poll() {
                        fetch('/mi-panel/estado-actual', { credentials: 'same-origin' })
                            .then(function(r) { return r.json(); })
                            .then(function(data) {
                                if (data.ok && data.asistencia) {
                                    var haCambiado = !ultimoEstado || 
                                        ultimoEstado.status !== data.asistencia.status ||
                                        ultimoEstado.time_in !== data.asistencia.time_in ||
                                        ultimoEstado.time_out !== data.asistencia.time_out;
                                    
                                    if (haCambiado || !window.ultimoPollEnviado) {
                                        window.ultimoPollEnviado = true;
                                        if (window.actualizarUIAsistencia) {
                                            window.actualizarUIAsistencia(data.asistencia);
                                        }
                                    }
                                    ultimoEstado = data.asistencia;
                                }
                            })
                            .catch(function(err) {
                                console.warn('Polling falló:', err);
                            })
                            .finally(function() {
                                setTimeout(poll, 15000 + Math.random() * 5000); // 15-20s con jitter
                            });
                    }
                    poll();
                };

                // Auto-iniciar en páginas de empleado
                if (window.location.pathname.startsWith('/mi-panel')) {
                    window.iniciarPollingEstado();
                }

                // Reanudar al volver a la pestaña
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && window.location.pathname.startsWith('/mi-panel')) {
                        window.iniciarPollingEstado();
                    }
                });
            })();
        });
    </script>

    <!-- Campana de notificaciones del empleado -->
    <?= view('partials/notif-bell', ['contexto' => 'empleado']) ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>