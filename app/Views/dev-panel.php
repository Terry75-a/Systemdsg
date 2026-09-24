<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Panel · DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .dash-greeting { margin-bottom: 24px; }
        .dash-greeting h1 { font-size: 28px; font-weight: 400; font-family: var(--g-font-display); margin: 0; letter-spacing: -0.02em; }
        .dash-greeting h1 strong { font-weight: 600; }
        .dash-greeting p { font-size: 14px; color: var(--g-text-secondary); margin: 4px 0 0; }
        .dash-date { display: inline-flex; align-items: center; gap: 6px; background: var(--g-surface-variant); padding: 6px 14px; border-radius: 20px; font-size: 13px; color: var(--g-text-secondary); margin-top: 12px; }
        .dash-date .material-symbols-outlined { font-size: 18px; }

        .dash-activity-item {
            display: flex; align-items: center; gap: 12px;
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
    <?php $activePage = 'dashboard'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div class="dash-greeting">
                    <h1>Bienvenido, <strong>Dev</strong></h1>
                    <p>Resumen del sistema de asistencia DSG Peru.</p>
                    <div class="dash-date">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <?= date('d/m/Y') ?>
                    </div>
                </div>
                <div class="dev-topbar-actions">
                    <a href="<?= base_url('dios/admins') ?>" class="dev-btn dev-btn-primary">
                        <span class="material-symbols-outlined">add</span> Crear Admin
                    </a>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <section class="dev-stats-grid">
                <a href="<?= base_url('dios/admins') ?>" class="dev-stat-card">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">admin_panel_settings</span></div>
                    <div class="dev-stat-label">Administradores</div>
                    <div class="dev-stat-value"><?= $adminCount ?></div>
                </a>
                <a href="<?= base_url('dios/empleados') ?>" class="dev-stat-card">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">groups</span></div>
                    <div class="dev-stat-label">Empleados</div>
                    <div class="dev-stat-value"><?= $empleadoCount ?></div>
                </a>
                <a href="<?= base_url('dios/codigos') ?>" class="dev-stat-card">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">vpn_key</span></div>
                    <div class="dev-stat-label">Codigos activos</div>
                    <div class="dev-stat-value"><?= $activeCodes ?></div>
                </a>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">person</span></div>
                    <div class="dev-stat-label">Total usuarios</div>
                    <div class="dev-stat-value"><?= $totalUsers ?></div>
                </div>
            </section>

            <div class="dev-grid-2">
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">admin_panel_settings</span> Ultimos admins</h2>
                        <a href="<?= base_url('dios/admins') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todos</a>
                    </div>
                    <div class="dev-recent-list">
                        <?php if (empty($admins)): ?>
                            <div class="dev-empty">
                                <span class="material-symbols-outlined">admin_panel_settings</span>
                                No hay admins
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($admins, -5, 5, true) as $a): ?>
                            <div class="dev-recent-item">
                                <div class="dev-recent-avatar"><?= strtoupper(substr($a['name'], 0, 2)) ?></div>
                                <div class="dev-recent-info">
                                    <div class="dev-recent-name"><?= esc($a['name']) ?></div>
                                    <div class="dev-recent-meta">DNI <?= esc($a['dni']) ?></div>
                                </div>
                                <div class="dev-recent-right">
                                    <div class="dev-recent-time"><?= esc($a['created']) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">vpn_key</span> Ultimos codigos</h2>
                        <a href="<?= base_url('dios/codigos') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todos</a>
                    </div>
                    <div class="dev-recent-list">
                        <?php if (empty($codes)): ?>
                            <div class="dev-empty">
                                <span class="material-symbols-outlined">vpn_key</span>
                                No hay codigos
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($codes, -5, 5, true) as $c): ?>
                            <div class="dev-recent-item">
                                <code class="dev-code"><?= esc($c['code']) ?></code>
                                <div class="dev-recent-info">
                                    <div class="dev-recent-name"><?= esc($c['name']) ?></div>
                                    <div class="dev-recent-meta">DNI <?= esc($c['dni']) ?></div>
                                </div>
                                <div class="dev-recent-right">
                                    <?php if ($c['status'] === 'active'): ?>
                                        <span class="dev-status is-active"><span class="material-symbols-outlined filled">circle</span> Activo</span>
                                    <?php else: ?>
                                        <span class="dev-status is-used"><span class="material-symbols-outlined">circle</span> Usado</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="dev-card" style="margin-top:16px;">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">apps</span> Accesos directos</h2>
                </div>
                <div class="dev-card-body">
                    <div class="dash-link-grid">
                        <a href="<?= base_url('dios/admins') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">admin_panel_settings</span></div>
                            <div class="dash-link-text">Administradores<span>Gestionar admins</span></div>
                        </a>
                        <a href="<?= base_url('dios/codigos') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-success-light);color:var(--g-success);"><span class="material-symbols-outlined">vpn_key</span></div>
                            <div class="dash-link-text">Codigos<span>Generar codigos</span></div>
                        </a>
                        <a href="<?= base_url('dios/empleados') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-warning-light);color:#e37400;"><span class="material-symbols-outlined">groups</span></div>
                            <div class="dash-link-text">Empleados<span>Ver todos</span></div>
                        </a>
                        <a href="<?= base_url('dios/estructura') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-error-light);color:var(--g-error);"><span class="material-symbols-outlined">account_tree</span></div>
                            <div class="dash-link-text">Estructura<span>Arbol organizacional</span></div>
                        </a>
                        <a href="<?= base_url('dios/perfil') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);"><span class="material-symbols-outlined">person</span></div>
                            <div class="dash-link-text">Mi perfil<span>Configuracion</span></div>
                        </a>
                        <a href="<?= base_url('admin') ?>" class="dash-link">
                            <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">analytics</span></div>
                            <div class="dash-link-text">Panel Admin<span>Ir al admin</span></div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.dev-code').forEach(function (el) {
    el.title = 'Clic para copiar';
    el.addEventListener('click', function () {
        navigator.clipboard.writeText(el.textContent).then(function () {
            var o = el.textContent;
            el.textContent = 'Copiado!';
            setTimeout(function () { el.textContent = o; }, 1200);
        });
    });
});
</script>
</body>
</html>
