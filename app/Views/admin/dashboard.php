<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control · DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .dev-stats-grid { grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); }
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
        .dash-activity-dot.dot-blue  { background: #3b82f6; }
        .dash-activity-info { flex: 1; min-width: 0; }
        .dash-activity-name { font-size: 14px; font-weight: 500; }
        .dash-activity-meta { font-size: 12px; color: var(--g-text-secondary); }
        .dash-activity-time { font-size: 12px; color: var(--g-text-secondary); white-space: nowrap; }

        .dash-link-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* ── Primeros pasos (cuenta nueva sin personal) ── */
        .dash-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .dash-step {
            display: flex; flex-direction: column; gap: 8px;
            padding: 16px; border: 1px solid var(--g-border); border-radius: 16px;
            background: var(--g-surface); text-decoration: none; color: inherit;
            transition: border-color 200ms;
        }
        .dash-step:hover { border-color: var(--g-primary); }
        .dash-step-n {
            width: 26px; height: 26px; border-radius: 50%; display: grid; place-items: center;
            background: var(--g-primary-light); color: var(--g-primary); font-size: 12px; font-weight: 700;
        }
        .dash-step-t { font-size: 13.5px; font-weight: 600; }
        .dash-step-d { font-size: 12px; color: var(--g-text-secondary); line-height: 1.45; }
        .dash-count {
            margin-left: auto; background: var(--g-error-light); color: var(--g-error);
            font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
        }
        @media (max-width: 768px) { .dash-steps { grid-template-columns: 1fr; } }
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
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=20261004g') ?>">
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

            <?php if ((int) ($totalEmpleados ?? 0) === 0): ?>
                <!-- Cuenta nueva: qué hacer primero -->
                <section class="dev-card" style="margin-bottom:24px;">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">rocket_launch</span> Primeros pasos</h2>
                    </div>
                    <div style="padding:18px 20px;">
                        <div class="dash-steps">
                            <a href="<?= base_url('admin/personal') ?>" class="dash-step">
                                <span class="dash-step-n">1</span>
                                <span class="dash-step-t">Crea tu personal</span>
                                <span class="dash-step-d">Registra empleados y practicantes con su DNI y horario.</span>
                            </a>
                            <a href="<?= base_url('admin/horarios') ?>" class="dash-step">
                                <span class="dash-step-n">2</span>
                                <span class="dash-step-t">Define los horarios</span>
                                <span class="dash-step-d">Establece jornadas, días laborables y festivos.</span>
                            </a>
                            <a href="<?= base_url('admin/asistencias') ?>" class="dash-step">
                                <span class="dash-step-n">3</span>
                                <span class="dash-step-t">Revisa asistencias</span>
                                <span class="dash-step-d">Aquí aparecerán las marcaciones y sus incidencias.</span>
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Stats -->
            <section class="dev-stats-grid">
                <a href="<?= base_url('admin/personal') ?>" class="dev-stat-card" title="Ver personal">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div class="dev-stat-label">Personal</div>
                    <div class="dev-stat-value"><?= $totalEmpleados ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>&amp;estado=present" class="dev-stat-card" title="Ver presentes de hoy">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div class="dev-stat-label">Presentes hoy</div>
                    <div class="dev-stat-value"><?= $hoyPresentes ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>&amp;estado=late" class="dev-stat-card" title="Ver tardanzas de hoy">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">schedule</span></div>
                    <div class="dev-stat-label">Tardanzas hoy</div>
                    <div class="dev-stat-value"><?= $hoyTardanzas ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>" class="dev-stat-card" title="Ver el día de hoy">
                    <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">person_off</span></div>
                    <div class="dev-stat-label">Sin marcar hoy</div>
                    <div class="dev-stat-value"><?= $hoyFaltas ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/incidencias') ?>" class="dev-stat-card" title="Ver incidencias pendientes">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">warning</span></div>
                    <div class="dev-stat-label">Incidencias pendientes</div>
                    <div class="dev-stat-value"><?= $incPendientes ?? 0 ?></div>
                </a>
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
                        $recent = array_values($attendance ?? []);
                        usort($recent, function ($a, $b) {
                            $ka = ($a['date'] ?? '') . ' ' . ($a['time_in'] ?? '') . ' ' . str_pad((string) ($a['id'] ?? 0), 10, '0', STR_PAD_LEFT);
                            $kb = ($b['date'] ?? '') . ' ' . ($b['time_in'] ?? '') . ' ' . str_pad((string) ($b['id'] ?? 0), 10, '0', STR_PAD_LEFT);
                            return strcmp($kb, $ka);
                        });
                        $recent = array_slice($recent, 0, 8);
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
                                $dotClass = match($st) { 'present' => 'dot-green', 'late' => 'dot-amber', 'absent' => 'dot-red', 'no_exit' => 'dot-blue', default => 'dot-gray' };
                                $label = match($st) {
                                    'present' => 'Presente',
                                    'late'    => 'Tardanza',
                                    'absent'  => 'Falta',
                                    'no_exit' => 'Sin salida',
                                    default   => ucfirst(str_replace('_', ' ', (string) $st)),
                                };
                                $fechaCorta = !empty($r['date']) ? date('d/m/Y', strtotime($r['date'])) : '';
                                ?>
                                <div class="dash-activity-item">
                                    <div class="dash-activity-dot <?= $dotClass ?>"></div>
                                    <div class="dash-activity-info">
                                        <div class="dash-activity-name"><?= esc($r['name'] ?? 'Empleado') ?></div>
                                        <div class="dash-activity-meta"><?= esc($label) ?> · <?= esc($fechaCorta) ?></div>
                                    </div>
                                    <div class="dash-activity-time"><?= esc(mb_substr((string) ($r['time_in'] ?? ''), 0, 5)) ?></div>
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
                                <div class="dash-link-text">Personal<span>Empleados y códigos</span></div>
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
                                <?php if (($incPendientes ?? 0) > 0): ?><span class="dash-count"><?= (int) $incPendientes ?> pendientes</span><?php endif; ?>
                                <div class="dash-link-text">Incidencias<span>Casos y justificaciones<?= ($incPendientes ?? 0) > 0 ? ' · ' . (int) $incPendientes . ' pendientes' : '' ?></span></div>
                            </a>
                            <a href="<?= base_url('admin/reportes') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">analytics</span></div>
                                <div class="dash-link-text">Reportes<span>Estadísticas</span></div>
                            </a>
                            <a href="<?= base_url('admin/configuracion') ?>" class="dash-link">
                                <div class="dash-link-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);"><span class="material-symbols-outlined">settings</span></div>
                                <div class="dash-link-text">Configuración<span>Ajustes y festivos</span></div>
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
