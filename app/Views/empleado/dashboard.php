<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel · DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        :root {
            --bg: var(--g-bg);
            --surface: var(--g-surface);
            --surface-alt: var(--g-surface-variant);
            --border: var(--g-border);
            --text: var(--g-text);
            --text-sec: var(--g-text-secondary);
            --text-dim: var(--g-text-disabled);
            --radius: 16px;
            --shadow: 0 1px 2px rgba(0,0,0,.06), 0 2px 6px rgba(0,0,0,.04);
            --font-heading: var(--g-font-display);
            --accent: var(--g-primary);
            --accent-soft: var(--g-primary-light);
            --green: var(--g-success);
            --green-soft: var(--g-success-light);
            --amber-soft: var(--g-warning-light);
        }
        .emp-layout { display: flex; min-height: 100vh; background: var(--bg); }
        .emp-sidebar {
            width: 260px; background: var(--surface); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; flex-shrink: 0;
        }
        .emp-sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 20px; border-bottom: 1px solid var(--border);
        }
        .emp-sidebar-brand img { width: 36px; height: 36px; border-radius: 10px; }
        .emp-sidebar-brand-text { font-size: 14px; font-weight: 700; font-family: var(--font-heading); }
        .emp-sidebar-brand-text small { display: block; font-size: 10px; font-weight: 400; color: var(--text-dim); letter-spacing: 0.05em; text-transform: uppercase; }
        .emp-sidebar-nav { flex: 1; padding: 12px; }
        .emp-nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: var(--radius);
            text-decoration: none; color: var(--text-sec);
            font-size: 13px; font-weight: 500; transition: all 120ms;
            margin-bottom: 2px; cursor: pointer;
        }
        .emp-nav-item:hover { background: var(--surface-alt); color: var(--text); }
        .emp-nav-item.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .emp-nav-item .material-symbols-outlined { font-size: 20px; }
        .emp-sidebar-footer {
            padding: 16px; border-top: 1px solid var(--border);
        }
        .emp-user-card {
            display: flex; align-items: center; gap: 10px; padding: 10px;
            border-radius: var(--radius); background: var(--surface-alt);
        }
        .emp-user-avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--accent); color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600; font-family: var(--font-heading);
        }
        .emp-user-name { font-size: 13px; font-weight: 600; font-family: var(--font-heading); }
        .emp-user-role { font-size: 11px; color: var(--text-dim); }
        .emp-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .emp-topbar {
            padding: 20px 28px; border-bottom: 1px solid var(--border);
            background: var(--surface);
        }
        .emp-topbar h1 { font-size: 22px; font-weight: 400; font-family: var(--font-heading); margin: 0; }
        .emp-topbar h1 strong { font-weight: 700; }
        .emp-topbar p { font-size: 13px; color: var(--text-sec); margin: 4px 0 0; }
        .emp-content { flex: 1; padding: 24px 28px; }

        .emp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px; }
        .emp-stat {
            background: var(--surface); border-radius: var(--radius); padding: 20px;
            display: flex; align-items: center; gap: 14px; transition: all 150ms;
        }
        .emp-stat:hover { box-shadow: var(--shadow); }
        .emp-stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .emp-stat-icon .material-symbols-outlined { font-size: 22px; }
        .esi-green { background: var(--green-soft); color: var(--green); }
        .esi-amber { background: var(--amber-soft); color: #e37400; }
        .esi-blue { background: #e8f0fe; color: #1a73e8; }
        .emp-stat-value { font-size: 24px; font-weight: 700; font-family: var(--font-heading); }
        .emp-stat-label { font-size: 11px; color: var(--text-dim); margin-top: 2px; }

        .emp-card { background: var(--surface); border-radius: var(--radius); overflow: hidden; margin-bottom: 16px; }
        .emp-card-head {
            display: flex; align-items: center; gap: 8px;
            padding: 16px 20px; border-bottom: 1px solid var(--border);
        }
        .emp-card-head .material-symbols-outlined { font-size: 20px; color: var(--accent); }
        .emp-card-head h2 { font-size: 15px; font-weight: 600; font-family: var(--font-heading); margin: 0; }
        .emp-card-body { padding: 20px; }

        .emp-info-row {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 0; border-bottom: 1px solid var(--border);
        }
        .emp-info-row:last-child { border-bottom: none; }
        .emp-info-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--surface-alt); display: flex; align-items: center; justify-content: center;
        }
        .emp-info-icon .material-symbols-outlined { font-size: 18px; color: var(--text-sec); }
        .emp-info-label { font-size: 11px; color: var(--text-dim); }
        .emp-info-value { font-size: 13px; font-weight: 500; }

        .emp-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .emp-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 500; padding: 4px 10px; border-radius: 20px;
        }
        .emp-badge-green { background: var(--green-soft); color: var(--green); }
        .emp-badge-amber { background: var(--amber-soft); color: #e37400; }
        .emp-badge-red { background: #fce8e6; color: #d93025; }

        .emp-mono { font-family: 'SF Mono', Consolas, monospace; font-size: 12px; }

        @media (max-width: 1024px) {
            .emp-sidebar { display: none; }
            .emp-stats { grid-template-columns: 1fr; }
            .emp-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="emp-layout">
    <aside class="emp-sidebar">
        <div class="emp-sidebar-brand">
            <img src="<?= base_url('images/logo_circular.png') ?>" alt="DSG">
            <div class="emp-sidebar-brand-text">DSG<small>Empleado</small></div>
        </div>
        <nav class="emp-sidebar-nav">
            <a class="emp-nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('mi-panel') ?>"><span class="material-symbols-outlined">dashboard</span> Mi Panel</a>
            <a class="emp-nav-item <?= ($activePage ?? '') === 'asistencias' ? 'active' : '' ?>" href="<?= base_url('mi-panel/asistencias') ?>"><span class="material-symbols-outlined">fact_check</span> Mi Asistencia</a>
            <a class="emp-nav-item <?= ($activePage ?? '') === 'horario' ? 'active' : '' ?>" href="<?= base_url('mi-panel/horario') ?>"><span class="material-symbols-outlined">schedule</span> Mi Horario</a>
            <a class="emp-nav-item <?= ($activePage ?? '') === 'incidencias' ? 'active' : '' ?>" href="<?= base_url('mi-panel/incidencias') ?>"><span class="material-symbols-outlined">warning</span> Mis Incidencias</a>
        </nav>
        <div class="emp-sidebar-footer">
            <div class="emp-user-card">
                <div class="emp-user-avatar"><?= strtoupper(substr(session()->get('user_name') ?? 'E', 0, 1)) ?></div>
                <div>
                    <div class="emp-user-name"><?= esc(session()->get('user_name') ?? '') ?></div>
                    <div class="emp-user-role">Empleado</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="emp-main">
        <header class="emp-topbar">
            <h1>Hola, <strong><?= esc(session()->get('user_name') ?? '') ?></strong></h1>
            <p>Bienvenido a tu panel de empleado. <?= date('d/m/Y') ?></p>
        </header>

        <div class="emp-content">
            <?php if ($msg = session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>" style="margin-bottom:20px;">
                    <span class="material-symbols-outlined filled">check_circle</span>
                    <span><?= esc($msg) ?></span>
                </div>
            <?php endif; ?>

            <?php
                $hEnt  = $hoyRegistro['time_in']  ?? null;
                $hSal  = $hoyRegistro['time_out'] ?? null;
                $horaEntrada  = $miHorario['hora_entrada'] ?? '08:00';
                $horaSalida   = $miHorario['hora_salida']  ?? '17:00';
            ?>

            <div class="emp-card" style="display:flex; align-items:center; gap:20px; flex-wrap:wrap; padding:20px;">
                <div style="flex:1; min-width:240px;">
                    <div class="emp-card-head" style="border:none; padding:0 0 8px;">
                        <span class="material-symbols-outlined">login</span>
                        <h2>Marcar asistencia de hoy</h2>
                    </div>
                    <div style="display:flex; gap:20px; flex-wrap:wrap; font-size:12.5px;">
                        <div><div class="emp-info-label">Entrada</div><div class="emp-mono" style="font-size:15px; font-weight:700;"><?= esc($hEnt ?: $horaEntrada) ?></div></div>
                        <div><div class="emp-info-label">Salida</div><div class="emp-mono" style="font-size:15px; font-weight:700;"><?= esc($hSal ?: $horaSalida) ?></div></div>
                        <div><div class="emp-info-label">Estado</div><div><?= !$hEnt ? 'Sin marcar' : (!$hSal ? 'Entrada registrada' : 'Completado') ?></div></div>
                    </div>
                </div>
                <div>
                    <?php if ($hEnt && $hSal): ?>
                        <span class="emp-badge emp-badge-green" style="padding:10px 16px; font-size:13px;">Completado hoy</span>
                    <?php else: ?>
                        <button type="button" class="dev-btn <?= $hEnt ? 'dev-btn-primary' : 'dev-btn' ?>" style="padding:12px 22px;font-size:15px;" onclick="openBioCheck()">
                            <span class="material-symbols-outlined"><?= $hEnt ? 'logout' : 'login' ?></span>
                            <?= $hEnt ? 'Registrar salida' : 'Registrar entrada' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?= view('partials/biometric_panel', ['bioMark' => ['exit' => (bool) ($hEnt && !$hSal), 'hora_salida' => empty($miHorario) ? '' : substr($horaSalida, 0, 5)]]) ?>

            <div class="emp-stats">
                <div class="emp-stat">
                    <div class="emp-stat-icon esi-green"><span class="material-symbols-outlined">check_circle</span></div>
                    <div>
                        <div class="emp-stat-value"><?= $misAsistencias ?? 0 ?></div>
                        <div class="emp-stat-label">Mis asistencias este mes</div>
                    </div>
                </div>
                <div class="emp-stat">
                    <div class="emp-stat-icon esi-amber"><span class="material-symbols-outlined">schedule</span></div>
                    <div>
                        <div class="emp-stat-value"><?= $misTardanzas ?? 0 ?></div>
                        <div class="emp-stat-label">Tardanzas</div>
                    </div>
                </div>
                <div class="emp-stat">
                    <div class="emp-stat-icon esi-blue"><span class="material-symbols-outlined">warning</span></div>
                    <div>
                        <div class="emp-stat-value"><?= $incPendientes ?? 0 ?></div>
                        <div class="emp-stat-label">Incidencias pendientes</div>
                    </div>
                </div>
            </div>

            <div class="emp-grid-2">
                <div class="emp-card">
                    <div class="emp-card-head">
                        <span class="material-symbols-outlined">person</span>
                        <h2>Mi Informacion</h2>
                    </div>
                    <div class="emp-card-body">
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">badge</span></div>
                            <div>
                                <div class="emp-info-label">Nombre</div>
                                <div class="emp-info-value"><?= esc($miInfo['name'] ?? '') ?></div>
                            </div>
                        </div>
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">credit_card</span></div>
                            <div>
                                <div class="emp-info-label">DNI</div>
                                <div class="emp-info-value emp-mono"><?= esc($miInfo['dni'] ?? '') ?></div>
                            </div>
                        </div>
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">qr_code</span></div>
                            <div>
                                <div class="emp-info-label">Codigo</div>
                                <div class="emp-info-value emp-mono"><?= esc($miInfo['personal_code'] ?? '—') ?></div>
                            </div>
                        </div>
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">mail</span></div>
                            <div>
                                <div class="emp-info-label">Correo</div>
                                <div class="emp-info-value"><?= esc($miInfo['email'] ?? '') ?></div>
                            </div>
                        </div>
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">business</span></div>
                            <div>
                                <div class="emp-info-label">Area / Cargo</div>
                                <div class="emp-info-value"><?= esc($miInfo['area'] ?? '-') ?> / <?= esc($miInfo['cargo'] ?? '-') ?></div>
                            </div>
                        </div>
                        <div class="emp-info-row">
                            <div class="emp-info-icon"><span class="material-symbols-outlined">schedule</span></div>
                            <div>
                                <div class="emp-info-label">Horario</div>
                                <div class="emp-info-value"><?= esc($miHorario['hora_entrada'] ?? '08:00') ?> - <?= esc($miHorario['hora_salida'] ?? '17:00') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="emp-card">
                    <div class="emp-card-head">
                        <span class="material-symbols-outlined">history</span>
                        <h2>Mis Ultimas Asistencias</h2>
                    </div>
                    <div class="emp-card-body">
                        <?php if (empty($misRegistros)): ?>
                            <div style="text-align:center; padding:30px; color:var(--text-dim); font-size:13px;">
                                <span class="material-symbols-outlined" style="font-size:36px; display:block; margin:0 auto 8px; opacity:0.3;">inbox</span>
                                Sin registros aun
                            </div>
                        <?php else: ?>
                            <?php foreach ($misRegistros as $r):
                                $st = $r['status'] ?? 'absent';
                                $badge = match($st) { 'present' => 'emp-badge-green', 'late' => 'emp-badge-amber', default => 'emp-badge-red' };
                                $label = match($st) { 'present' => 'Puntual', 'late' => 'Tardanza', 'absent' => 'Falta', default => ucfirst($st) };
                            ?>
                                <div class="emp-info-row">
                                    <div class="emp-info-icon"><span class="material-symbols-outlined">calendar_today</span></div>
                                    <div style="flex:1;">
                                        <div class="emp-info-value emp-mono"><?= esc($r['date'] ?? '') ?></div>
                                        <div class="emp-info-label"><?= esc($r['time_in'] ?? '-') ?> - <?= esc($r['time_out'] ?? '-') ?></div>
                                    </div>
                                    <span class="emp-badge <?= $badge ?>"><?= $label ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
