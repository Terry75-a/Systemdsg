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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
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
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tk-dashboard.css?v=' . filemtime(FCPATH . 'css/index/components/tk-dashboard.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'dashboard'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
<?php
        /* ══════════════════════════════════════════════════════════
           CONSOLA DEV · réplica de la página /dev de System-Trackin
           Datos: App\Libraries\Asistencia\DevConsole ($consola)
           ══════════════════════════════════════════════════════════ */
        $c   = $consola['counts'];
        $num = fn ($n) => number_format((int) $n, 0, '.', ',');
        $LINKS = [
            ['Gestión', 'apartment', [
                ['Administradores', 'dios/admins', 'admin_panel_settings'],
                ['Empleados', 'dios/empleados', 'groups'],
                ['Códigos de registro', 'dios/codigos', 'vpn_key'],
            ]],
            ['Base de datos', 'database', [
                ['Tablas y datos', 'dios/datos', 'dns'],
                ['Estructura del sistema', 'dios/estructura', 'account_tree'],
                ['Auditoría de marcas', 'dios/auditoria', 'history'],
            ]],
            ['Actividad', 'monitoring', [
                ['Sesiones', 'dios/sesiones', 'login'],
                ['Mi perfil', 'dios/perfil', 'person'],
                ['Sitio web', '', 'language'],
            ]],
        ];
        $ROLE_ICO = ['Dev' => 'shield_person', 'Admin' => 'verified_user', 'Empleado' => 'badge', 'Practicante' => 'school'];
        ?>
        <header class="dev-topbar">
            <div class="dev-topbar-row tk-dash">
                <div class="tk-title">
                    <div>
                        <h1>Consola DSG Perú</h1>
                        <p>Lo que pasa en la plataforma: empresas creadas, cuentas, marcaciones y base de datos.</p>
                    </div>
                    <div class="tk-title-actions">
                        <a href="<?= base_url('dios/admins') ?>" class="tk-chip active"><span class="material-symbols-outlined">add</span> Crear admin</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content tk-dash tk-console">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <!-- KPIs -->
            <?php $kpis = [
                ['Empresas', $num($c['empresas']), 'apartment'],
                ['Cuentas', $num($c['cuentas']) . ' · ' . $num($c['online']) . ' en línea', 'group'],
                ['Personal', $num($c['personal']) . ' · ' . $num($c['codigos']) . ' códigos', 'badge'],
                ['Marcaciones', $num($c['marcaciones']) . ' · ' . $num($c['marcHoy']) . ' hoy', 'fingerprint'],
                ['Base de datos', $consola['db']['sizeMb'] . ' MB · ' . $consola['db']['tables'] . ' tablas', 'hard_drive'],
            ]; ?>
            <section class="tk-stats tk-stats-5">
                <?php foreach ($kpis as $i => [$label, $valor, $icono]): ?>
                    <div class="tk-stat" style="animation-delay: <?= 100 + $i * 40 ?>ms">
                        <span class="material-symbols-outlined tk-stat-ico"><?= $icono ?></span>
                        <div>
                            <p class="tk-stat-lbl"><?= esc($label) ?></p>
                            <p class="tk-stat-val"><?= esc($valor) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- Accesos -->
            <div class="tk-grid-3">
                <?php foreach ($LINKS as $i => [$titulo, $ico, $items]): ?>
                    <section class="tk-card tk-ovh tk-rise" style="animation-delay: <?= 50 * $i ?>ms">
                        <div class="tk-group-head">
                            <span class="tk-group-ico"><span class="material-symbols-outlined"><?= $ico ?></span></span>
                            <span><?= $titulo ?></span>
                        </div>
                        <?php foreach ($items as [$lbl, $url, $iIco]): ?>
                            <a href="<?= base_url($url) ?>" class="tk-group-link">
                                <span class="material-symbols-outlined"><?= $iIco ?></span>
                                <span class="tk-grow"><?= $lbl ?></span>
                                <span class="material-symbols-outlined tk-chev">chevron_right</span>
                            </a>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </div>

            <!-- Empresas · Cuentas por tipo -->
            <div class="tk-grid-2">
                <section class="tk-card tk-p5 tk-anim-item">
                    <h3 class="tk-h3 tk-mb3">Empresas</h3>
                    <div class="tk-tree-root">
                        <span class="tk-tree-ico tk-tree-ico--root">DSG</span>
                        <span class="tk-grow"><b>DSG Perú Technology</b> <span class="tk-muted">· desarrolla y supervisa la plataforma</span></span>
                    </div>
                    <?php if ($consola['empresas']): ?>
                        <div class="tk-tree">
                            <?php foreach ($consola['empresas'] as $e): ?>
                                <div class="tk-row">
                                    <span class="tk-tree-ico"><span class="material-symbols-outlined">apartment</span></span>
                                    <span class="tk-grow tk-min0">
                                        <span class="tk-trunc tk-medium"><?= esc($e['name']) ?></span>
                                        <span class="tk-trunc tk-sub11">
                                            <?= esc($e['code']) ?> · <?= esc($e['admin']) ?> · desde <?= esc($e['created']) ?><?= $e['last_seen'] ? ' · última conexión ' . esc($e['last_seen']) : '' ?>
                                        </span>
                                    </span>
                                    <span class="tk-pill"><?= $e['cuentas'] ?> <?= $e['cuentas'] === 1 ? 'cuenta' : 'cuentas' ?><?= $e['practicantes'] ? ' · ' . $e['practicantes'] . ' pract.' : '' ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="tk-empty-p">Todavía no hay empresas. Créalas en Administradores.</p>
                    <?php endif; ?>
                </section>

                <section class="tk-card tk-p5 tk-anim-item">
                    <h3 class="tk-h3 tk-mb3">Cuentas por tipo</h3>
                    <div>
                        <?php foreach (\App\Libraries\Asistencia\DevConsole::ROLES as $rol => $lbl): ?>
                            <div class="tk-row">
                                <span class="material-symbols-outlined tk-row-ico"><?= $ROLE_ICO[$rol] ?></span>
                                <span class="tk-grow"><?= $lbl ?></span>
                                <b><?= $num($consola['roles'][$rol] ?? 0) ?></b>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="tk-sub11 tk-mt3">MariaDB/MySQL <?= esc($consola['db']['version']) ?> · <?= $num($c['marcaciones']) ?> marcaciones (<?= $num($c['marcHoy']) ?> hoy) · <?= $num($c['incPend']) ?> incidencias por resolver</p>
                </section>
            </div>

            <!-- Últimas conexiones · Últimas marcaciones -->
            <div class="tk-grid-2">
                <section class="tk-card tk-p5 tk-anim-item">
                    <h3 class="tk-h3 tk-mb3">Últimas conexiones</h3>
                    <?php foreach ($consola['recentUsers'] as $u): ?>
                        <div class="tk-row">
                            <span class="tk-online <?= $u['online'] ? 'is-on' : '' ?>" title="<?= $u['online'] ? 'En línea' : 'Desconectado' ?>"></span>
                            <span class="tk-grow tk-min0 tk-trunc"><b class="tk-medium"><?= esc($u['name']) ?></b> <span class="tk-muted">· <?= esc($u['role']) ?><?= $u['company'] ? ' · ' . esc($u['company']) : '' ?></span></span>
                            <span class="tk-muted tk-none"><?= esc($u['last']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$consola['recentUsers']): ?><p class="tk-empty-p">Nadie ha ingresado todavía.</p><?php endif; ?>
                </section>

                <section class="tk-card tk-p5 tk-anim-item">
                    <h3 class="tk-h3 tk-mb3">Últimas marcaciones</h3>
                    <?php foreach ($consola['events'] as $e): ?>
                        <div class="tk-row">
                            <span class="tk-grow tk-min0 tk-trunc">
                                <b class="tk-medium"><?= esc($e['name']) ?></b>
                                <span class="tk-muted">· <?= esc($e['estado']) ?><?= $e['jornada'] !== '' ? ': ' . esc($e['jornada']) : '' ?><?= $e['company'] ? ' · ' . esc($e['company']) : '' ?></span>
                            </span>
                            <span class="tk-muted tk-none"><?= date('d/m/Y', strtotime($e['date'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$consola['events']): ?><p class="tk-empty-p">Sin marcaciones todavía.</p><?php endif; ?>
                </section>
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
<script src="<?= base_url('js/tk-dashboard.js?v=' . filemtime(FCPATH . 'js/tk-dashboard.js')) ?>" defer></script>
</body>
</html>
