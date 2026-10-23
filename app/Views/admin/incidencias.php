<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencias - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        /* Sin recuadro azul al hacer clic */
        .dev-layout button, .dev-layout a, .dev-layout input, .dev-layout select, .dev-layout textarea { outline: none; -webkit-tap-highlight-color: transparent; }
        .dev-layout button:focus-visible, .dev-layout a:focus-visible { outline: 2px solid var(--g-primary); outline-offset: 2px; }

        /* ── Resumen ── */
        .in-sum {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 18px; padding: 22px 24px; margin-bottom: 18px;
        }
        .in-sum-top { display: flex; align-items: center; gap: 26px; flex-wrap: wrap; }
        .in-big { flex-shrink: 0; text-align: center; }
        .in-big b { display: block; font-size: 44px; line-height: 1; font-family: var(--g-font-display); letter-spacing: -0.03em; color: var(--g-text); }
        .in-big span { font-size: 12px; color: var(--g-text-secondary); }
        .in-bar { flex: 1; min-width: 220px; }
        .in-bar-track { display: flex; height: 14px; border-radius: 999px; overflow: hidden; background: var(--g-surface-variant); }
        .in-bar-seg { height: 100%; transition: width 500ms; }
        .in-bar-pend { background: var(--g-warning); }
        .in-bar-gap  { background: #94c0f0; }
        .in-bar-res  { background: var(--g-success); }
        .in-bar-rej  { background: var(--g-error); }
        .in-legend { display: flex; justify-content: space-between; gap: 12px; margin-top: 12px; font-size: 12.5px; color: var(--g-text-secondary); flex-wrap: wrap; }
        .in-legend span { display: inline-flex; align-items: center; gap: 7px; }
        .in-legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
        .in-legend .lg-p { color: #b45309; } .in-legend .lg-p i { background: var(--g-warning); }
        .in-legend .lg-g { color: var(--g-success); } .in-legend .lg-g i { background: var(--g-success); }
        .in-legend .lg-e { color: var(--g-error); } .in-legend .lg-e i { background: var(--g-error); }
        .in-legend b { color: var(--g-text); font-weight: 600; }

        /* ── Lista ── */
        .in-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 18px; overflow: hidden; }
        .in-card-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--g-border); flex-wrap: wrap; }
        .in-card-head h3 { margin: 0; font-size: 15px; font-weight: 600; font-family: var(--g-font-display); display: flex; align-items: center; gap: 8px; }
        .in-card-head h3 .material-symbols-outlined { color: var(--g-warning); font-size: 20px; }

        .in-item { display: flex; gap: 14px; padding: 16px 20px; align-items: flex-start; }
        .in-item + .in-item { border-top: 1px solid var(--g-border); }

        .in-av {
            flex-shrink: 0; width: 42px; height: 42px; border-radius: 12px;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; font-family: var(--g-font-display);
        }

        .in-main { flex: 1; min-width: 0; }
        .in-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .in-name strong { font-size: 14.5px; font-family: var(--g-font-display); }
        .in-role { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--g-text-secondary); background: var(--g-surface-variant); padding: 2px 8px; border-radius: 999px; }
        .in-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin: 7px 0 4px; }
        .in-tag { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: 999px; background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .in-tag .material-symbols-outlined { font-size: 13px; }
        .in-date { font-size: 11.5px; color: var(--g-text-secondary); font-family: 'SF Mono', Consolas, monospace; }
        .in-detalle { font-size: 12.5px; color: var(--g-text-secondary); line-height: 1.5; }
        .in-detalle:empty { display: none; }

        .in-just {
            margin-top: 10px; border-radius: 10px; padding: 9px 13px; font-size: 12.5px;
            display: flex; gap: 8px; align-items: flex-start;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
        }
        .in-just .material-symbols-outlined { font-size: 16px; flex-shrink: 0; margin-top: 1px; opacity: 0.6; }
        .in-just p { margin: 0; }
        .in-just small { display: block; font-weight: 600; color: var(--g-text); margin-bottom: 2px; }

        .in-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; color: var(--g-text-secondary); }
        .in-estado i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .in-estado.dot-pendiente i { background: #f59e0b; }
        .in-estado.dot-revision i { background: #3b82f6; }
        .in-estado.dot-justificada i { background: #22c55e; }
        .in-estado.dot-rechazada i { background: #94a3b8; }
        .in-estado.dot-desest i { background: #94a3b8; }

        .in-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; flex-wrap: wrap; }
        .in-btn {
            display: inline-flex; align-items: center; gap: 5px;
            border: 1px solid color-mix(in srgb, var(--g-primary) 45%, transparent);
            background: var(--g-primary-light); color: var(--g-primary); font-family: var(--g-font);
            font-size: 12px; font-weight: 600; padding: 6px 11px; border-radius: 50px; cursor: pointer;
            transition: none;
        }
        .in-btn .material-symbols-outlined { font-size: 15px; }
        .in-btn:hover { border-color: var(--sb-primary); color: var(--sb-on-primary); background: var(--sb-primary); }

        .in-empty { text-align: center; padding: 54px 20px; color: var(--g-text-secondary); font-size: 13.5px; }
        .in-empty .material-symbols-outlined { font-size: 48px; display: block; margin: 0 auto 14px; opacity: 0.3; }
        .in-empty .in-empty-act { margin-top: 16px; }

        /* ── Filtro por estado ───────────────── */
        .in-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
        .in-chip {
            display: inline-flex; align-items: center; gap: 7px;
            height: 32px; padding: 0 13px; border: 1px solid var(--g-border);
            border-radius: 999px; background: transparent; color: var(--g-text-secondary);
            font-size: .8rem; font-weight: 500; font-family: inherit; cursor: pointer;
        }
        .in-chip:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .in-chip.on { background: var(--g-primary); border-color: var(--g-primary); color: #fff; }
        .in-chip b { font-weight: 700; font-size: .75rem; opacity: .85; }
        .in-filtered-empty { display: none; text-align: center; padding: 34px 16px; color: var(--g-text-secondary); font-size: 13.5px; }

        @media (max-width: 720px) {
            .in-item { padding: 14px; gap: 12px; flex-wrap: wrap; }
            .in-actions { width: 100%; padding-left: 58px; }
            .in-sum { padding: 18px; }
            .in-big b { font-size: 34px; }
        }

        /* ═══════════════════════════════════════════════════════════════
           INCIDENCIAS · panel admin de sorteroOS

           Se va la tarjeta resumen con barra de estados (componente
           propio, lo más "PHP" de la página) y se sustituye por las
           métricas de sorteroOS (admin/clubes): 4 tarjetas con el icono +
           label arriba y el número abajo.

           La lista pasa a ser el bloque con hairlines del feed de
           sorteroOS: título sobre el fondo de página y dentro un
           rounded-2xl con gap de 1px por el que se ve la línea.
           ═══════════════════════════════════════════════════════════════ */
        body { --in-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }

        /* Chip del total en la cabecera (MetaChip del PageHeader de sorteroOS) */
        /* .in-topbar duplica la clase para pasar a (0,3,1) y ganarle al
           padding que fija next-panel.css sobre .dev-topbar */
        body:has(.dev-main) .dev-topbar.in-topbar {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 16px; flex-wrap: wrap;
            padding-right: 78px; /* hueco para la campana fija (right:24px + 42px) */
        }
        body:has(.dev-main) .in-total-chip {
            flex: none;
            padding: 5px 12px; border-radius: 999px;
            background: var(--in-surface);
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            color: var(--sb-on-surface-variant);
            font-size: 12px; font-weight: 500; white-space: nowrap;
        }

        /* ── Métricas ── */
        body:has(.dev-main) .in-metrics-row { display: flex; align-items: stretch; gap: 16px; margin-bottom: 16px; }
        body:has(.dev-main) .in-metrics {
            flex: 1 1 auto; min-width: 0;
            display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;
        }
        body:has(.dev-main) .in-metric {
            background: var(--in-surface);
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            border-radius: 16px; padding: 16px;
            display: flex; flex-direction: column; align-items: stretch;
            transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), border-color 200ms ease;
        }
        body:has(.dev-main) .in-metric:hover { border-color: color-mix(in srgb, var(--sb-outline-variant) 70%, transparent); }
        body:has(.dev-main) .in-metric-top { display: flex; align-items: center; gap: 8px; color: var(--sb-on-surface-variant); }
        body:has(.dev-main) .in-metric-top .material-symbols-outlined { font-size: 16px; }
        body:has(.dev-main) .in-metric-label { font-size: 12px; font-weight: 500; color: var(--sb-on-surface-variant); }
        body:has(.dev-main) .in-metric b {
            font-size: 24px; font-weight: 700; letter-spacing: -.02em; line-height: 1.2;
            color: var(--sb-on-surface); margin-top: 8px; font-variant-numeric: tabular-nums;
        }

        /* ── Acción primaria ── */
        body:has(.dev-main) .in-new {
            flex: none; align-self: center; height: 48px; padding: 0 24px;
            border: 0; border-radius: 50px; background: var(--sb-primary); color: #fff;
            cursor: pointer; font-family: inherit; font-size: 14px; font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;
            transition: none;
        }
        body:has(.dev-main) .in-new:hover { background: color-mix(in srgb, var(--sb-primary) 90%, #000); }
        body:has(.dev-main) .in-new:focus-visible { outline: none; box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        body:has(.dev-main) .in-new .material-symbols-outlined { font-size: 18px; }

        /* ── Filtros en píldora ── */
        body:has(.dev-main) .in-filters { gap: 8px; margin-bottom: 18px; }
        body:has(.dev-main) .in-chip {
            height: 34px; padding: 0 14px;
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 50px; background: var(--sb-surface);
            color: var(--sb-on-surface-variant); font-size: 13px; font-weight: 600;
            transition: none;
        }
        body:has(.dev-main) .in-chip:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }
        body:has(.dev-main) .in-chip.on { background: var(--sb-primary); border-color: var(--sb-primary); color: #fff; }
        body:has(.dev-main) .in-chip b { font-weight: 700; font-size: 12px; opacity: .6; }
        body:has(.dev-main) .in-chip.on b { opacity: .9; }

        /* ── Lista: bloque con hairlines ── */
        body:has(.dev-main) .in-sec-head { margin: 0 0 12px; }
        body:has(.dev-main) .in-sec-head h2 {
            margin: 0; font-family: inherit; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        body:has(.dev-main) .in-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }
        body:has(.dev-main) .in-list {
            display: flex; flex-direction: column; gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px; overflow: hidden;
        }
        body:has(.dev-main) .in-item { background: var(--sb-surface); padding: 16px 20px; gap: 14px; }
        body:has(.dev-main) .in-item + .in-item { border-top: 0; }
        body:has(.dev-main) .in-item:hover { background: var(--sb-surface-container-low); }
        body:has(.dev-main) .in-av {
            width: 32px; height: 32px; border-radius: 8px; font-size: 12px; font-weight: 600;
            background: var(--sb-surface-container);
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
        }
        body:has(.dev-main) .in-name strong { font-size: 13.5px; font-weight: 500; font-family: inherit; }
        body:has(.dev-main) .in-role {
            font-size: 11.5px; font-weight: 400; text-transform: none; letter-spacing: 0;
            background: none; padding: 0; color: var(--sb-on-surface-variant);
        }
        body:has(.dev-main) .in-tag {
            font-size: 11.5px; font-weight: 500; background: var(--sb-surface-container);
            color: var(--sb-on-surface-variant);
        }
        body:has(.dev-main) .in-date { font-size: 11.5px; color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent); }
        body:has(.dev-main) .in-detalle { font-size: 12.5px; color: var(--sb-on-surface-variant); }
        body:has(.dev-main) .in-just { background: var(--sb-surface-container); border-radius: 10px; }
        body:has(.dev-main) .in-estado { font-size: 11.5px; font-weight: 500; }
        body:has(.dev-main) .in-list .in-empty,
        body:has(.dev-main) .in-list .in-filtered-empty { background: var(--sb-surface); }

        /* ── Acciones de la fila: píldoras planas ── */
        body:has(.dev-main) .in-btn {
            height: 32px; padding: 0 12px;
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 999px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            font-size: 12.5px; font-weight: 500;
            transition: background 150ms ease, color 150ms ease, border-color 150ms ease;
        }
        body:has(.dev-main) .in-btn:hover {
            background: var(--sb-surface-container-high); color: var(--sb-on-surface);
            border-color: transparent; box-shadow: none;
        }
        body:has(.dev-main) .in-btn-danger { color: #dc2626; }
        body:has(.dev-main) .in-btn-danger:hover { background: color-mix(in srgb, #dc2626 10%, transparent); color: #dc2626; }

        /* ── Responsive · al final para ganar por orden ── */
        @media (max-width: 1100px) {
            body:has(.dev-main) .in-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            body:has(.dev-main) .in-metrics-row { flex-wrap: wrap; }
            body:has(.dev-main) .in-metrics { flex: 1 1 100%; }
            body:has(.dev-main) .in-new { flex: 1 1 100%; justify-content: center; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
</head>
<body>

<?= view('partials/admin-sidebar', ['activePage' => 'incidencias']) ?>

<main class="dev-main">
    <div class="dev-topbar in-topbar">
        <div>
            <h1 class="dev-topbar-title">Incidencias</h1>
            <p class="dev-topbar-subtitle">Incidencias generadas desde la asistencia y asignadas a tu personal</p>
        </div>
        <span class="in-total-chip"><?= count($incidents) ?> incidencias</span>
    </div>

    <div class="dev-content">

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                <span><?= session()->getFlashdata('msg') ?></span>
            </div>
        <?php endif; ?>

        <?php
        $totalIncidencias = count($incidents);
        $pendientes = 0; $justificadas = 0; $rechazadas = 0; $revision = 0;
        foreach ($incidents as $inc) {
            $e = $inc['estado'] ?? 'Pendiente';
            if ($e === 'Pendiente')  $pendientes++;
            elseif ($e === 'Revisión') $revision++;
            elseif ($e === 'Justificada') $justificadas++;
            elseif ($e === 'Rechazada' || $e === 'Desestimada') $rechazadas++;
        }

        $roleMap = [];
        foreach (($personal ?? []) as $p) {
            $roleMap[(int) $p['id']] = ($p['role'] ?? 'Personal') . (!empty($p['cargo']) ? ' · ' . $p['cargo'] : '');
        }

        $tipoInfo = [
            'Tardanza'          => ['label' => 'Tardanza',          'icon' => 'schedule'],
            'Falta'             => ['label' => 'Falta',             'icon' => 'cancel'],
            'Salida anticipada' => ['label' => 'Salida anticipada', 'icon' => 'logout'],
            'Sin salida'        => ['label' => 'Sin salida',        'icon' => 'event_busy'],
            'Sin marcación'     => ['label' => 'Sin salida',        'icon' => 'event_busy'],
            'Sin marcacion'     => ['label' => 'Sin salida',        'icon' => 'event_busy'],
            'no_exit'           => ['label' => 'Sin salida',        'icon' => 'event_busy'],
            'Otro'              => ['label' => 'Otro',              'icon' => 'help'],
        ];
        $estadoInfo = [
            'Pendiente'   => 'dot-pendiente',
            'Revisión'    => 'dot-revision',
            'Justificada' => 'dot-justificada',
            'Rechazada'   => 'dot-rechazada',
            'Desestimada' => 'dot-desest',
        ];
        ?>

        <!-- Métricas + acción primaria (sorteroOS admin/clubes + admin/usuarios) -->
        <div class="in-metrics-row">
            <div class="in-metrics">
                <div class="in-metric">
                    <div class="in-metric-top">
                        <span class="material-symbols-outlined">pending_actions</span>
                        <span class="in-metric-label">Pendientes</span>
                    </div>
                    <b><?= $pendientes ?></b>
                </div>
                <div class="in-metric">
                    <div class="in-metric-top">
                        <span class="material-symbols-outlined">hourglass_top</span>
                        <span class="in-metric-label">En revisión</span>
                    </div>
                    <b><?= $revision ?></b>
                </div>
                <div class="in-metric">
                    <div class="in-metric-top">
                        <span class="material-symbols-outlined">verified</span>
                        <span class="in-metric-label">Justificadas</span>
                    </div>
                    <b><?= $justificadas ?></b>
                </div>
                <div class="in-metric">
                    <div class="in-metric-top">
                        <span class="material-symbols-outlined">do_not_disturb_on</span>
                        <span class="in-metric-label">Rechazadas</span>
                    </div>
                    <b><?= $rechazadas ?></b>
                </div>
            </div>
            <button type="button" class="in-new" onclick="openCreateModal()">
                <span class="material-symbols-outlined">add</span> Nueva incidencia
            </button>
        </div>

        <!-- Filtro por estado -->
        <div class="in-filters" id="inFilters">
            <button type="button" class="in-chip on" onclick="filtrarEstado('', this)">Todas <b><?= $totalIncidencias ?></b></button>
            <button type="button" class="in-chip" onclick="filtrarEstado('Pendiente', this)">Pendientes <b><?= $pendientes ?></b></button>
            <button type="button" class="in-chip" onclick="filtrarEstado('Revisión', this)">En revisión <b><?= $revision ?></b></button>
            <button type="button" class="in-chip" onclick="filtrarEstado('Justificada', this)">Justificadas <b><?= $justificadas ?></b></button>
            <button type="button" class="in-chip" onclick="filtrarEstado('Rechazada', this)">Rechazadas <b><?= $rechazadas ?></b></button>
        </div>

        <!-- Lista -->
        <div class="in-sec-head">
            <h2><span class="material-symbols-outlined">list_alt</span> Lista de incidencias</h2>
        </div>
        <div class="in-list">

            <?php if ($totalIncidencias === 0): ?>
                <div class="in-empty">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p style="margin:0 0 2px;">No hay incidencias registradas</p>
                    <p style="font-size:12.5px;max-width:320px;margin:0 auto;">Registra una incidencia y asígnala a una persona de tu personal.</p>
                    <div class="in-empty-act">
                        <button type="button" class="dev-btn dev-btn-primary" onclick="openCreateModal()">
                            <span class="material-symbols-outlined">add</span> Registrar y asignar
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($incidents as $inc):
                    $t = $inc['tipo'] ?? 'Otro';
                    $ti = $tipoInfo[$t] ?? $tipoInfo['Otro'];
                    $e  = $inc['estado'] ?? 'Pendiente';
                    $eb = $estadoInfo[$e] ?? 'dot-pendiente';
                    $uName = $inc['name'] ?? 'Empleado';
                    $ini = strtoupper(mb_substr($uName, 0, 1));
                    $uRole = $roleMap[(int) ($inc['user_id'] ?? 0)] ?? ($inc['role'] ?? 'Personal');
                    $hasJust = !empty($inc['justificacion']);
                    $jb = $e === 'Rechazada' ? 'do_not_disturb_on' : ($e === 'Revisión' ? 'hourglass_top' : ($e === 'Pendiente' ? 'description' : 'verified'));
                    $jMsg = $e === 'Rechazada' ? 'Rechazada por el administrador' : ($e === 'Revisión' ? 'Justificación en revisión' : ($e === 'Pendiente' ? 'Justificación enviada' : 'Justificación aceptada'));
                ?>
                    <div class="in-item" data-estado="<?= esc($e) ?>">
                        <div class="in-av"><?= esc($ini) ?></div>
                        <div class="in-main">
                            <div class="in-name">
                                <strong><?= esc($uName) ?></strong>
                                <span class="in-role"><?= esc($uRole) ?></span>
                                <span class="in-estado <?= $eb ?>"><i></i> <?= esc($e) ?></span>
                            </div>
                            <div class="in-meta">
                                <span class="in-tag"><span class="material-symbols-outlined"><?= $ti['icon'] ?></span> <?= esc($ti['label']) ?></span>
                                <span class="in-date"><?= esc(date('d/m/Y H:i', strtotime($inc['fecha'] ?? 'now'))) ?></span>
                            </div>
                            <?php if (!empty($inc['detalle'])): ?>
                                <div class="in-detalle"><?= esc($inc['detalle']) ?></div>
                            <?php endif; ?>
                            <?php if ($hasJust): ?>
                                <div class="in-just">
                                    <span class="material-symbols-outlined"><?= $jb ?></span>
                                    <p><small><?= $jMsg ?></small><?= esc($inc['justificacion']) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($inc['resuelta_en'])): ?>
                                <div class="in-detalle" style="font-size:11.5px;color:var(--g-text-secondary);">
                                    <span class="material-symbols-outlined" style="font-size:14px;vertical-align:-2px;">verified_user</span>
                                    Cerrada por <b><?= esc($inc['resuelta_por'] ?? '—') ?></b>
                                    · <?= esc(date('d/m/Y H:i', strtotime($inc['resuelta_en']))) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="in-actions">
                            <?php if ($e === 'Pendiente' || $e === 'Revisión'): ?>
                                <form action="<?= site_url('admin/update-incident') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="inc_id" value="<?= (int) $inc['id'] ?>">
                                    <input type="hidden" name="inc_estado" value="Justificada">
                                    <button type="submit" class="in-btn" onclick="return confirm('¿Marcar como justificada?')">
                                        <span class="material-symbols-outlined">check</span> Justificar
                                    </button>
                                </form>
                                <button type="button" class="in-btn in-btn-danger" onclick="openRejectModal(<?= (int) $inc['id'] ?>, '<?= esc($inc['nombre'] ?? '') ?>')">
                                    <span class="material-symbols-outlined">close</span> Rechazar
                                </button>
                            <?php endif; ?>
                            <button type="button" class="in-btn" onclick='openEditModal(<?= (int) $inc['id'] ?>, <?= json_encode($e, JSON_HEX_APOS | JSON_HEX_TAG) ?>, <?= json_encode($inc['justificacion'] ?? '', JSON_HEX_APOS | JSON_HEX_TAG) ?>)'>
                                <span class="material-symbols-outlined">edit</span> Editar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="in-filtered-empty" id="inFilteredEmpty">
                    <span class="material-symbols-outlined" style="font-size:40px;display:block;margin:0 auto 8px;opacity:.25;color:var(--g-text-disabled);">filter_alt_off</span>
                    Ninguna incidencia con este estado.
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<!-- Modal · Nueva incidencia -->
<div class="dev-modal-overlay" id="createModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2><span class="material-symbols-outlined">add_circle</span> Nueva incidencia</h2>
            <button class="dev-btn-icon" onclick="closeCreateModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/create-incident') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <?php
                $gente = array_values(array_filter($personal ?? [], fn($p) => ($p['estado'] ?? '') === 'Activo'));
                ?>
                <div class="dev-field">
                    <label for="inc_user">Asignar a</label>
                    <?php if (empty($gente)): ?>
                        <p style="font-size:12.5px;color:var(--g-text-secondary);margin:0;">No tienes personal activo todavía. Añade personas desde <a href="<?= site_url('admin/personal') ?>">Personal</a>.</p>
                        <input type="hidden" name="inc_user" value="0">
                    <?php else: ?>
                        <select id="inc_user" name="inc_user" required>
                            <?php foreach ($gente as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?> — <?= esc($p['role']) ?><?= !empty($p['cargo']) ? ' · ' . esc($p['cargo']) : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="dev-form-grid">
                    <div class="dev-field">
                        <label for="inc_tipo">Tipo</label>
                        <select id="inc_tipo" name="inc_tipo" required>
                            <option value="Tardanza">Tardanza</option>
                            <option value="Falta">Falta</option>
                            <option value="Salida anticipada">Salida anticipada</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="dev-field">
                        <label for="inc_fecha">Fecha</label>
                        <input type="datetime-local" id="inc_fecha" name="inc_fecha" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                </div>
                <div class="dev-field">
                    <label for="inc_detalle">Detalle</label>
                    <textarea id="inc_detalle" name="inc_detalle" rows="4" required placeholder="Describe qué ocurrió..."></textarea>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeCreateModal()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">
                    <span class="material-symbols-outlined">assignment_add</span> Registrar incidencia
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Editar incidencia -->
<div class="dev-modal-overlay" id="editModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2>Editar incidencia</h2>
            <button class="dev-btn-icon" onclick="closeEditModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/update-incident') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <input type="hidden" name="inc_id" id="edit_inc_id">
            <div class="dev-modal-body">
                <div class="dev-form-grid">
                    <div class="dev-field">
                        <label for="edit_inc_status">Estado</label>
                        <select id="edit_inc_status" name="inc_estado" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Revisión">En revisión</option>
                            <option value="Justificada">Justificada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="dev-field">
                        <label for="edit_inc_justification">Justificacion</label>
                        <textarea id="edit_inc_justification" name="inc_justificacion" rows="4" placeholder="Ingrese la justificacion..."></textarea>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Rechazar incidencia -->
<div class="dev-modal-overlay" id="rejectModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2><span class="material-symbols-outlined">cancel</span> Rechazar incidencia</h2>
            <button class="dev-btn-icon" onclick="closeRejectModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/update-incident') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <input type="hidden" name="inc_id" id="reject_inc_id">
            <input type="hidden" name="inc_estado" value="Rechazada">
            <div class="dev-modal-body">
                <div class="dev-form-grid">
                    <div class="dev-field" style="grid-column:1 / -1;">
                        <label for="reject_motivo">Motivo del rechazo <span style="color:var(--g-error)">*</span></label>
                        <textarea id="reject_motivo" name="inc_justificacion" rows="4" placeholder="Ingrese el motivo del rechazo..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeRejectModal()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-danger">
                    <span class="material-symbols-outlined">cancel</span> Confirmar rechazo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filtrarEstado(estado, btn) {
        document.querySelectorAll('#inFilters .in-chip').forEach(function (c) { c.classList.remove('on'); });
        btn.classList.add('on');

        var visibles = 0;
        document.querySelectorAll('.in-item[data-estado]').forEach(function (el) {
            var e = el.getAttribute('data-estado');
            var ok = estado === '' || (estado === 'Rechazada'
                ? (e === 'Rechazada' || e === 'Desestimada')
                : e === estado);
            el.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });

        var vacio = document.getElementById('inFilteredEmpty');
        if (vacio) vacio.style.display = visibles === 0 ? 'block' : 'none';
    }
</script>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
function openEditModal(id, estado, justificacion) {
    document.getElementById('edit_inc_id').value = id;
    document.getElementById('edit_inc_status').value = estado;
    document.getElementById('edit_inc_justification').value = justificacion || '';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

function openRejectModal(id, nombre) {
    document.getElementById('reject_inc_id').value = id;
    document.getElementById('reject_motivo').value = '';
    document.getElementById('rejectModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(function() { document.getElementById('reject_motivo').focus(); }, 100);
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>