<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría de asistencia - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .ad-wrap { display: flex; flex-direction: column; gap: 20px; }
        .ad-back {
            display: inline-flex; align-items: center; gap: 6px; font-size: .85rem;
            font-weight: 600; color: var(--g-primary); text-decoration: none;
        }
        .ad-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding: 6px 18px 18px; }
        .ad-kv { padding: 10px 0; }
        .ad-kv .k { font-size: .72rem; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 4px; }
        .ad-kv .v { font-size: .95rem; font-weight: 600; color: var(--g-text); word-break: break-word; }
        .ad-kv .v.muted { color: var(--g-text-secondary); font-weight: 400; }
        .badge { display: inline-flex; align-items: center; gap: 6px; height: 26px; padding: 0 10px; border-radius: 999px; font-size: .74rem; font-weight: 600; }
        .badge.present { background: rgba(34,197,94,.14); color: #16a34a; }
        .badge.late    { background: rgba(245,158,11,.16); color: #d97706; }
        .badge.absent  { background: rgba(239,68,68,.14);  color: #dc2626; }
        .badge.no_exit { background: rgba(148,163,184,.20); color: #64748b; }
        table.ad-log { width: 100%; border-collapse: collapse; font-size: .84rem; }
        table.ad-log th, table.ad-log td { padding: 11px 14px; text-align: left; border-bottom: 1px solid var(--g-border); vertical-align: top; }
        table.ad-log th { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: var(--g-text-secondary); }
        table.ad-log td.campo { font-weight: 600; color: var(--g-text); }
        .ad-val { display: inline-block; padding: 2px 8px; border-radius: 6px; background: var(--g-surface-variant); font-family: ui-monospace, monospace; font-size: .8rem; }
        .ad-val.nuevo { background: rgba(27,122,66,.12); color: var(--g-primary); }
        .ad-arrow { color: var(--g-text-secondary); margin: 0 6px; }
        .ad-empty { padding: 26px 18px; text-align: center; color: var(--g-text-secondary); font-size: .87rem; }

        /* Mapa en auditoría */
        .ad-map-wrap { grid-column: 1 / -1; margin-top: 6px; }
        .ad-map-container {
            width: 100%; height: 300px; border-radius: 12px;
            background: var(--g-surface-variant); overflow: hidden;
        }
        .ad-map-msg { text-align: center; padding: 40px 20px; color: var(--g-text-secondary); font-size: .9rem; }
        .ad-map-msg .material-symbols-outlined { font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: .5; }

        @media (max-width: 860px) { .ad-grid { grid-template-columns: 1fr 1fr; } }

        /* ═══════════════════════════════════════════════════════════════
           AUDITORÍA · panel admin de sorteroOS
           Las tarjetas con marco se vuelven secciones: título sobre el
           fondo de página y el contenido en un bloque blanco redondeado.
           ═══════════════════════════════════════════════════════════════ */
        .ad-sec-head { margin: 0 0 12px; }
        .ad-sec-head--split { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .ad-sec-head h2 {
            margin: 0; font-family: inherit; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .ad-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }
        /* La cabecera de la entidad lleva el nombre de la persona, no un rótulo */
        .ad-sec-head--entity {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .ad-sec-head--entity h2 {
            font-size: 16px; font-weight: 500; letter-spacing: 0; text-transform: none;
            color: var(--sb-on-surface);
        }
        .ad-sec-head--entity h2 .material-symbols-outlined { font-size: 19px; color: var(--sb-primary); }
        .ad-count {
            padding: 4px 11px; border-radius: 999px;
            background: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface));
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            color: var(--sb-on-surface-variant); font-size: 12px; font-weight: 500;
        }
        .ad-block { background: var(--sb-surface); border-radius: 16px; overflow: hidden; }
        .ad-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0 24px; padding: 4px 20px 18px; }
        .ad-kv { padding: 12px 0; border-bottom: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent); }
        .ad-kv .k {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent); margin-bottom: 4px;
        }
        .ad-kv .v { font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); word-break: break-word; }
        .ad-kv .v.muted { color: var(--sb-on-surface-variant); font-weight: 400; }
        .ad-map-wrap { grid-column: 1 / -1; margin-top: 6px; padding: 0 20px 20px; }
        .ad-map-container { width: 100%; height: 300px; border-radius: 12px; background: var(--sb-surface-container); overflow: hidden; }
        .ad-map-msg { text-align: center; padding: 40px 20px; color: var(--sb-on-surface-variant); font-size: 13px; }
        .ad-empty { padding: 26px 20px; text-align: center; color: var(--sb-on-surface-variant); font-size: 13px; }
        .ad-val { background: var(--sb-surface-container); }
        .ad-val.nuevo { background: color-mix(in srgb, var(--sb-primary) 12%, transparent); color: var(--sb-primary); }
        .ad-arrow { color: var(--sb-on-surface-variant); }
        .ad-back { color: var(--sb-primary); font-weight: 500; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => $activePage ?? 'asistencias']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Auditoría de la marcación</h1>
            <p class="dev-topbar-subtitle">Historial de cambios: quién modificó qué, cuándo y por qué</p>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <div class="ad-wrap">
                <a class="ad-back" href="<?= site_url('admin/asistencias') ?>">
                    <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span> Volver a asistencias
                </a>

                <div class="ad-sec-head ad-sec-head--entity">
                    <h2><span class="material-symbols-outlined">fact_check</span> <?= esc($att['name'] ?? '') ?></h2>
                    <span class="badge <?= esc($att['status'] ?? '') ?>"><?= esc($att['status'] ?? '') ?></span>
                </div>
                <div class="ad-block">
                    <div class="ad-grid">
                        <div class="ad-kv"><div class="k">Fecha</div><div class="v"><?= esc($att['date'] ?? '') ?></div></div>
                        <div class="ad-kv"><div class="k">DNI</div><div class="v"><?= esc($att['dni'] ?? '—') ?></div></div>
                        <div class="ad-kv"><div class="k">Entrada</div><div class="v"><?= esc(substr((string) ($att['time_in'] ?? ''), 0, 5)) ?: '—' ?></div></div>
                        <div class="ad-kv"><div class="k">Salida</div><div class="v"><?= esc(substr((string) ($att['time_out'] ?? ''), 0, 5)) ?: '—' ?></div></div>
                        <div class="ad-kv"><div class="k">Observación</div><div class="v <?= empty($att['observacion']) ? 'muted' : '' ?>"><?= esc($att['observacion'] ?? '—') ?></div></div>
                        <div class="ad-kv"><div class="k">Geolocalización</div><div class="v <?= empty($att['lat']) ? 'muted' : '' ?>"><?= ! empty($att['lat']) ? esc($att['lat'] . ', ' . $att['lng']) : '—' ?></div></div>
                        <div class="ad-kv"><div class="k">IP de origen</div><div class="v <?= empty($att['ip']) ? 'muted' : '' ?>"><?= esc($att['ip'] ?? '—') ?></div></div>
                        <div class="ad-kv"><div class="k">Evidencia</div><div class="v <?= empty($att['evidencia']) ? 'muted' : '' ?>"><?= ! empty($att['evidencia']) ? '<a href="' . base_url('uploads/' . esc($att['evidencia'])) . '" target="_blank" rel="noopener">Ver selfie</a>' : '—' ?></div></div>
                    </div>

                    <!-- Mapa de ubicación -->
                    <div class="ad-map-wrap">
                        <div class="ad-map-container" id="mapContainer" style="display:<?= empty($att['lat']) ? 'none' : 'block' ?>;">
                            <div id="map" style="width:100%; height:100%;"></div>
                        </div>
                        <div class="ad-map-msg" id="mapMsg" style="<?= empty($att['lat']) ? 'display:block;' : 'display:none;' ?>">
                            <span class="material-symbols-outlined">location_on</span>
                            Esta marcación no tiene ubicación registrada
                        </div>
                    </div>
                </div>

                <div class="ad-sec-head ad-sec-head--split">
                    <h2><span class="material-symbols-outlined">history</span> Historial de cambios</h2>
                    <span class="ad-count"><?= count($logs ?? []) ?> movimiento(s)</span>
                </div>
                <div class="ad-block">
                    <?php if (empty($logs)): ?>
                        <div class="ad-empty">Esta marcación nunca fue editada a mano.</div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="ad-log">
                                <thead>
                                    <tr>
                                        <th>Fecha y hora</th>
                                        <th>Autor</th>
                                        <th>Acción</th>
                                        <th>Campo</th>
                                        <th>Cambio</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($logs as $l): ?>
                                    <tr>
                                        <td style="white-space:nowrap;"><?= esc($l['created_at'] ?? '') ?></td>
                                        <td><?= esc($l['autor'] ?? 'sistema') ?></td>
                                        <td style="text-transform:capitalize;"><?= esc($l['accion'] ?? '') ?></td>
                                        <td class="campo"><?= esc($l['campo'] ?? '—') ?></td>
                                        <td>
                                            <span class="ad-val"><?= $l['valor_anterior'] !== null && $l['valor_anterior'] !== '' ? esc($l['valor_anterior']) : 'vacío' ?></span>
                                            <span class="ad-arrow">→</span>
                                            <span class="ad-val nuevo"><?= $l['valor_nuevo'] !== null && $l['valor_nuevo'] !== '' ? esc($l['valor_nuevo']) : 'vacío' ?></span>
                                        </td>
                                        <td style="color:var(--g-text-secondary);"><?= esc($l['motivo'] ?? '—') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var lat = <?= json_encode($att['lat'] ?? null) ?>;
            var lng = <?= json_encode($att['lng'] ?? null) ?>;
            var mapContainer = document.getElementById('map');

            if (lat !== null && lng !== null && lat !== 0 && lng !== 0) {
                var map = L.map('map').setView([parseFloat(lat), parseFloat(lng)], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map)
                    .bindPopup('Ubicación de la marcación').openPopup();
                setTimeout(function() { map.invalidateSize(); }, 100);
            }
        });
    </script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
</html>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
