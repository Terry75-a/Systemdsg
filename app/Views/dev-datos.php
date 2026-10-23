<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de datos · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <style>
        .page-content { max-width: 960px; }

        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; padding: 16px;
            display: flex; align-items: center; gap: 14px; transition: border-color 200ms;
        }
        .stat-card:hover { border-color: var(--g-primary); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-icon .material-symbols-outlined { font-size: 22px; }
        .si-amber { background: var(--g-warning-light); color: #e37400; }
        .si-green { background: var(--g-success-light); color: var(--g-success); }
        .si-accent { background: var(--g-primary-light); color: var(--g-primary); }
        .si-blue { background: #e3f0ff; color: #1b6ac9; }
        .si-red { background: var(--g-error-light); color: var(--g-error); }
        .si-gray { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .stat-label { font-size: 12px; color: var(--g-text-secondary); }
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); color: var(--g-text); letter-spacing: -0.02em; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px;
            overflow: hidden; margin-bottom: 20px;
        }
        .card-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; }
        .card-head h2 {
            font-size: 16px; font-weight: 500; font-family: var(--g-font-display);
            display: flex; align-items: center; gap: 10px;
        }
        .card-head h2 .material-symbols-outlined { font-size: 20px; color: var(--g-text-secondary); }
        .card-head .card-sub { font-size: 12px; color: var(--g-text-secondary); }
        .card-body { padding: 8px 20px 16px; }

        .kv-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--g-border); }
        .kv-row:last-child { border-bottom: none; }
        .kv-label { font-size: 13px; min-width: 130px; color: var(--g-text); }
        .kv-bar { flex: 1; height: 8px; background: var(--g-surface-variant); border-radius: 4px; overflow: hidden; min-width: 40px; }
        .kv-bar i { display: block; height: 100%; background: var(--g-primary); border-radius: 4px; min-width: 2px; }
        .kv-count { font-size: 13px; font-weight: 600; min-width: 52px; text-align: right; font-family: var(--g-font-display); }
        .kv-pct { font-size: 11px; color: var(--g-text-secondary); min-width: 38px; text-align: right; }

        .db-table { width: 100%; border-collapse: collapse; }
        .db-table th {
            text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.06em; color: var(--g-text-secondary);
            padding: 10px 12px; border-bottom: 1px solid var(--g-border);
        }
        .db-table td { font-size: 13px; padding: 9px 12px; border-bottom: 1px solid var(--g-border); color: var(--g-text); }
        .db-table tr:last-child td { border-bottom: none; }
        .db-table tr:hover td { background: var(--g-surface-variant); }
        .db-table .num { text-align: right; font-variant-numeric: tabular-nums; }
        .db-table .tbl-name { font-family: 'SF Mono', Monaco, monospace; font-size: 12.5px; }
        .db-table tfoot td { font-weight: 700; background: var(--g-surface-variant); border-top: 2px solid var(--g-border); }

        .badge-soft {
            display: inline-flex; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 500;
            background: var(--g-primary-light); color: var(--g-primary);
        }

        .table-scroll { overflow-x: auto; }
        .note { font-size: 12px; color: var(--g-text-secondary); padding: 0 20px 16px; }

        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr 1fr; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'datos'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Base de datos</strong></h1>
                    <p>Resumen general de todo lo acumulado en el sistema.</p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php
            $fmtBytes = function (int $b): string {
                if ($b >= 1048576) return number_format($b / 1048576, 2, ',', '.') . ' MB';
                if ($b >= 1024) return number_format($b / 1024, 1, ',', '.') . ' KB';
                return $b . ' B';
            };
            $pct = function (int $n, int $t): int { return $t > 0 ? (int) round($n * 100 / $t) : 0; };

            $rolLabels = ['Dev' => 'Desarrollador', 'Admin' => 'Administradores', 'Empleado' => 'Empleados', 'Practicante' => 'Practicantes'];
            $asistLabels = ['present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', 'absent_justified' => 'Falta justificada'];
            $incLabels = ['Pendiente' => 'Pendiente', 'Revisión' => 'En revisión', 'Justificada' => 'Justificada', 'Rechazada' => 'Rechazada', 'Desestimada' => 'Desestimada'];

            $tUs = (int) ($totales['usuarios'] ?? 0);
            $tAs = (int) ($totales['asistencia'] ?? 0);
            $tInc = (int) ($totales['incidencias'] ?? 0);
            $tCod = (int) ($totales['codigos'] ?? 0);
            ?>

            <section class="stats">
                <div class="stat-card">
                    <div class="stat-icon si-accent"><span class="material-symbols-outlined filled">group</span></div>
                    <div>
                        <div class="stat-label">Usuarios</div>
                        <div class="stat-value"><?= number_format($tUs) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><span class="material-symbols-outlined filled">fact_check</span></div>
                    <div>
                        <div class="stat-label">Registros de asistencia</div>
                        <div class="stat-value"><?= number_format($tAs) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-amber"><span class="material-symbols-outlined filled">report</span></div>
                    <div>
                        <div class="stat-label">Incidencias</div>
                        <div class="stat-value"><?= number_format($tInc) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-blue"><span class="material-symbols-outlined filled">vpn_key</span></div>
                    <div>
                        <div class="stat-label">Códigos</div>
                        <div class="stat-value"><?= number_format($tCod) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-red"><span class="material-symbols-outlined filled">history</span></div>
                    <div>
                        <div class="stat-label">Eventos de sesión</div>
                        <div class="stat-value"><?= number_format((int) ($totales['sesiones'] ?? 0)) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-gray"><span class="material-symbols-outlined filled">hard_drive</span></div>
                    <div>
                        <div class="stat-label">Peso total (índices + datos)</div>
                        <div class="stat-value" style="font-size:20px;"><?= $fmtBytes((int) $bytesTotal) ?></div>
                    </div>
                </div>
            </section>

            <div class="grid-2">
                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">badge</span> Usuarios por rol</h2>
                        <span class="card-sub"><?= number_format($tUs) ?> en total</span>
                    </div>
                    <div class="card-body">
                        <?php $rolVisto = []; foreach ($rolLabels as $k => $lbl): $n = (int) ($porRol[$k] ?? 0); $rolVisto[$k] = true; ?>
                        <div class="kv-row">
                            <span class="kv-label"><?= esc($lbl) ?></span>
                            <span class="kv-bar"><i style="width:<?= $pct($n, $tUs) ?>%"></i></span>
                            <span class="kv-count"><?= number_format($n) ?></span>
                            <span class="kv-pct"><?= $pct($n, $tUs) ?>%</span>
                        </div>
                        <?php endforeach; ?>
                        <?php foreach ($porRol as $rol => $n): if (isset($rolVisto[$rol])) continue; ?>
                        <div class="kv-row">
                            <span class="kv-label"><?= esc($rol !== '' ? $rol : '(sin rol)') ?></span>
                            <span class="kv-bar"><i style="width:<?= $pct((int) $n, $tUs) ?>%"></i></span>
                            <span class="kv-count"><?= number_format((int) $n) ?></span>
                            <span class="kv-pct"><?= $pct((int) $n, $tUs) ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">fact_check</span> Asistencia por estado</h2>
                        <span class="card-sub"><?= number_format($tAs) ?> registros</span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($asistLabels as $k => $lbl): $n = (int) ($porEstadoAsist[$k] ?? 0); ?>
                        <div class="kv-row">
                            <span class="kv-label"><?= esc($lbl) ?></span>
                            <span class="kv-bar"><i style="width:<?= $pct($n, $tAs) ?>%"></i></span>
                            <span class="kv-count"><?= number_format($n) ?></span>
                            <span class="kv-pct"><?= $pct($n, $tAs) ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">report</span> Incidencias por estado</h2>
                        <span class="card-sub"><?= number_format($tInc) ?> en total</span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($incLabels as $k => $lbl): $n = (int) ($porEstadoInc[$k] ?? 0); ?>
                        <div class="kv-row">
                            <span class="kv-label"><?= esc($lbl) ?></span>
                            <span class="kv-bar"><i style="width:<?= $pct($n, $tInc) ?>%"></i></span>
                            <span class="kv-count"><?= number_format($n) ?></span>
                            <span class="kv-pct"><?= $pct($n, $tInc) ?>%</span>
                        </div>
                        <?php endforeach; ?>
                        <?php foreach ($porEstadoInc as $est => $n): if (isset($incLabels[$est])) continue; ?>
                        <div class="kv-row">
                            <span class="kv-label"><?= esc($est !== '' ? $est : '(sin estado)') ?></span>
                            <span class="kv-bar"><i style="width:<?= $pct((int) $n, $tInc) ?>%"></i></span>
                            <span class="kv-count"><?= number_format((int) $n) ?></span>
                            <span class="kv-pct"><?= $pct((int) $n, $tInc) ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">devices</span> Códigos y accesos</h2>
                    </div>
                    <div class="card-body">
                        <div class="kv-row">
                            <span class="kv-label">Códigos activos</span>
                            <span class="kv-bar"><i style="width:<?= $pct((int) ($porEstadoCode['active'] ?? 0), $tCod) ?>%"></i></span>
                            <span class="kv-count"><?= number_format((int) ($porEstadoCode['active'] ?? 0)) ?></span>
                            <span class="kv-pct"><?= $pct((int) ($porEstadoCode['active'] ?? 0), $tCod) ?>%</span>
                        </div>
                        <div class="kv-row">
                            <span class="kv-label">Códigos usados</span>
                            <span class="kv-bar"><i style="width:<?= $pct((int) ($porEstadoCode['used'] ?? 0), $tCod) ?>%"></i></span>
                            <span class="kv-count"><?= number_format((int) ($porEstadoCode['used'] ?? 0)) ?></span>
                            <span class="kv-pct"><?= $pct((int) ($porEstadoCode['used'] ?? 0), $tCod) ?>%</span>
                        </div>
                        <div class="kv-row">
                            <span class="kv-label">Notificaciones</span>
                            <span class="kv-count" style="min-width:92px;"><?= number_format((int) ($totales['notificaciones'] ?? 0)) ?></span>
                            <span class="kv-pct"></span>
                        </div>
                        <div class="kv-row">
                            <span class="kv-label">Passkeys (WebAuthn)</span>
                            <span class="kv-count" style="min-width:92px;"><?= number_format((int) ($totales['passkeys'] ?? 0)) ?></span>
                            <span class="kv-pct"></span>
                        </div>
                        <div class="kv-row">
                            <span class="kv-label">Suscripciones push</span>
                            <span class="kv-count" style="min-width:92px;"><?= number_format((int) ($totales['push'] ?? 0)) ?></span>
                            <span class="kv-pct"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2><span class="material-symbols-outlined">storage</span> Todas las tablas</h2>
                    <span class="card-sub"><?= count($tablas) ?> tablas · <?= number_format((int) $filasTotal) ?> filas · <?= $fmtBytes((int) $bytesTotal) ?></span>
                </div>
                <div class="table-scroll">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Tabla</th>
                                <th class="num">Filas</th>
                                <th class="num">Tamaño</th>
                                <th class="num">% del total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tablas as $t): ?>
                            <tr>
                                <td class="tbl-name"><?= esc($t['nombre']) ?></td>
                                <td class="num"><?= number_format($t['filas']) ?></td>
                                <td class="num"><?= $fmtBytes((int) $t['bytes']) ?></td>
                                <td class="num" style="color:var(--g-text-secondary);"><?= $pct((int) $t['bytes'], (int) $bytesTotal) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><?= count($tablas) ?> tablas</td>
                                <td class="num"><?= number_format((int) $filasTotal) ?></td>
                                <td class="num"><?= $fmtBytes((int) $bytesTotal) ?></td>
                                <td class="num">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="note">Filas contadas en vivo con COUNT(*); el tamaño usa data + index de information_schema. Los datos crecen con cada asistencia, incidencia y sesión registrada.</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
