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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
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
        @media (max-width: 860px) { .ad-grid { grid-template-columns: 1fr 1fr; } }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=20261004g') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=1') ?>">
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

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">fact_check</span> <?= esc($att['name'] ?? '') ?></h2>
                        <span class="badge <?= esc($att['status'] ?? '') ?>"><?= esc($att['status'] ?? '') ?></span>
                    </div>
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
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">history</span> Historial de cambios</h2>
                        <span style="font-size:.78rem;color:var(--g-text-secondary);"><?= count($logs ?? []) ?> movimiento(s)</span>
                    </div>

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
</body>
</html>
