<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard · DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .dash-greeting { margin-bottom: 24px; }
        .dash-greeting h1 { font-size: 22px; font-weight: 400; font-family: var(--g-font-display); margin: 0; }
        .dash-greeting h1 strong { font-weight: 700; }
        .dash-greeting p { font-size: 13px; color: var(--g-text-secondary); margin: 4px 0 0; }
        .dash-date { display: inline-flex; align-items: center; gap: 6px; background: var(--g-surface-variant); padding: 6px 12px; border-radius: 20px; font-size: 12px; color: var(--g-text-secondary); margin-top: 10px; }
        .dash-date .material-symbols-outlined { font-size: 16px; }

        .dash-section-title { font-size: 14px; font-weight: 600; color: var(--g-text-secondary); margin-bottom: 12px; font-family: var(--g-font-display); }

        .dash-activity-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 0;
        }
        .dash-activity-item + .dash-activity-item { border-top: 1px solid var(--g-border); }
        .dash-activity-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .dash-activity-dot.dot-green { background: var(--g-success); }
        .dash-activity-dot.dot-amber { background: var(--g-warning); }
        .dash-activity-dot.dot-red { background: var(--g-error); }
        .dash-activity-dot.dot-gray { background: var(--g-text-disabled); }
        .dash-activity-info { flex: 1; min-width: 0; }
        .dash-activity-name { font-size: 14px; font-weight: 500; }
        .dash-activity-meta { font-size: 12px; color: var(--g-text-secondary); }
        .dash-activity-time { font-size: 12px; color: var(--g-text-secondary); white-space: nowrap; }

        .dash-link-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .dash-link {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 16px;
            background: var(--g-surface); border: 1px solid var(--g-border);
            text-decoration: none; color: inherit;
            transition: border-color 200ms;
        }
        .dash-link:hover { border-color: var(--g-primary); }
        .dash-link-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .dash-link-icon .material-symbols-outlined { font-size: 22px; }
        .dash-link-text { font-size: 14px; font-weight: 500; line-height: 1.3; }
        .dash-link-text span { display: block; font-size: 12px; color: var(--g-text-secondary); font-weight: 400; margin-top: 2px; }

        @media (max-width: 768px) {
            .dash-link-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dev-layout">
    <?= view('partials/admin-sidebar', ['activePage' => $activePage ?? 'dashboard']) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div class="dash-greeting">
                    <h1>Hola, <strong><?= esc(session()->get('user_name') ?? 'Admin') ?></strong></h1>
                    <p>Resumen del sistema de asistencia.</p>
                    <div class="dash-date">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <section class="dev-stats-grid">
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div class="dev-stat-label">Empleados</div>
                    <div class="dev-stat-value"><?= $totalEmpleados ?? 0 ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div class="dev-stat-label">Presentes hoy</div>
                    <div class="dev-stat-value"><?= $hoyPresentes ?? 0 ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">schedule</span></div>
                    <div class="dev-stat-label">Tardanzas hoy</div>
                    <div class="dev-stat-value"><?= $hoyTardanzas ?? 0 ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">cancel</span></div>
                    <div class="dev-stat-label">Faltas hoy</div>
                    <div class="dev-stat-value"><?= $hoyFaltas ?? 0 ?></div>
                </div>
            </section>

            <div class="dev-grid-2">
                <!-- Última actividad -->
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">history</span> Última actividad</h2>
                        <a href="<?= base_url('admin/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todo</a>
                    </div>
                    <div style="padding: 4px 16px 12px;">
                        <?php
                        $recent = [];
                        if (!empty($attendance)) {
                            $recent = array_slice(array_reverse($attendance), 0, 8);
                        }
                        ?>
                        <?php if (empty($recent)): ?>
                            <div style="text-align:center; padding:48px 16px; color:var(--g-text-secondary); font-size:14px;">
                                <span class="material-symbols-outlined" style="font-size:48px; display:block; margin:0 auto 12px; opacity:0.2; color:var(--g-text-disabled);">inbox</span>
                                Sin registros de asistencia
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent as $r): ?>
                                <?php
                                $st = $r['status'] ?? 'unknown';
                                $dotClass = match($st) { 'present' => 'dot-green', 'late' => 'dot-amber', 'absent' => 'dot-red', default => 'dot-gray' };
                                $label = match($st) { 'present' => 'Puntual', 'late' => 'Tardanza', 'absent' => 'Falta', default => ucfirst($st) };
                                ?>
                                <div class="dash-activity-item">
                                    <div class="dash-activity-dot <?= $dotClass ?>"></div>
                                    <div class="dash-activity-info">
                                        <div class="dash-activity-name"><?= esc($r['name'] ?? 'Empleado') ?></div>
                                        <div class="dash-activity-meta"><?= $label ?> · <?= esc($r['date'] ?? '') ?></div>
                                    </div>
                                    <div class="dash-activity-time"><?= esc($r['time_in'] ?? '') ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Accesos directos -->
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">apps</span> Módulos</h2>
                    </div>
                    <div style="padding: 4px 16px 12px;">
                        <div class="dash-link-grid">
                            <a href="<?= base_url('admin/personal') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-success-light);color:var(--g-success);"><span class="material-symbols-outlined">people</span></div>
                                <div class="dash-link-text">Personal<span>Empleados y codigos</span></div>
                            </a>
                            <a href="<?= base_url('admin/asistencias') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-warning-light);color:#e37400;"><span class="material-symbols-outlined">fingerprint</span></div>
                                <div class="dash-link-text">Asistencias<span>Registro diario</span></div>
                            </a>
                            <a href="<?= base_url('admin/horarios') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">schedule</span></div>
                                <div class="dash-link-text">Horarios<span>Jornadas de trabajo</span></div>
                            </a>
                            <a href="<?= base_url('admin/incidencias') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-error-light);color:var(--g-error);"><span class="material-symbols-outlined">warning</span></div>
                                <div class="dash-link-text">Incidencias<span>Ingresos auto.</span></div>
                            </a>
                            <a href="<?= base_url('admin/reportes') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">analytics</span></div>
                                <div class="dash-link-text">Reportes<span>Estadisticas</span></div>
                            </a>
                            <a href="<?= base_url('admin/configuracion') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);"><span class="material-symbols-outlined">settings</span></div>
                                <div class="dash-link-text">Configuracion<span>Ajustes</span></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
