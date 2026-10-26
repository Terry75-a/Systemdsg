<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .rp-wrap { display: flex; flex-direction: column; gap: 22px; }

        /* ═══════════════════════════════════════════════════════════════
           REPORTES · panel admin de sorteroOS
           Toolbar de filtros en una fila, métricas con el label arriba y
           el número abajo, y las tres secciones (filtros, distribución y
           detalle) como bloques con título apoyado sobre el fondo de página.
           ═══════════════════════════════════════════════════════════════ */
        body { --rp-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }

        /* ── Título de sección ── */
        .rp-sec-head { margin: 0 0 12px; }
        .rp-sec-head--split { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .rp-sec-head h2 {
            margin: 0; font-family: inherit; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .rp-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }

        /* ── Toolbar de filtros ── */
        .rp-bar {
            display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px;
            background: var(--sb-surface); border-radius: 16px; padding: 16px 18px;
            margin-bottom: 22px;
        }
        .rp-bar-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .rp-bar-group--grow { flex: 1 1 240px; }
        .rp-bar-cap {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            white-space: nowrap;
        }
        .rp-input {
            height: 38px; padding: 0 10px; border: none; border-radius: 12px;
            background: var(--sb-surface-container); color: var(--sb-on-surface);
            font-size: 0.85rem; font-family: inherit; outline: none;
            transition: box-shadow 125ms ease;
        }
        .rp-input:hover { background: var(--sb-surface-container-high); }
        .rp-input:focus, .rp-input:focus-visible {
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }
        .rp-go {
            height: 38px; padding: 0 18px; border: 0; border-radius: 999px; cursor: pointer;
            background: var(--sb-primary); color: #fff; font-weight: 500; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 8px; font-family: inherit; flex: none;
            transition: filter 150ms ease, transform 200ms ease;
        }
        .rp-go:hover { filter: brightness(1.08); }
        .rp-go:active { transform: scale(.97); }
        .rp-go .material-symbols-outlined { font-size: 17px; }
        .rp-chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .rp-chip {
            height: 38px; padding: 0 15px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 999px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            cursor: pointer; font-size: 13px; font-weight: 500; font-family: inherit;
            transition: background 150ms ease, color 150ms ease, border-color 150ms ease;
        }
        .rp-chip:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }
        .rp-chip.on { background: var(--sb-primary); border-color: var(--sb-primary); color: #fff; }
        .rp-export {
            height: 34px; padding: 0 14px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 999px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            cursor: pointer; font-family: inherit; font-size: 13px; font-weight: 500;
            display: inline-flex; align-items: center; gap: 7px;
            transition: background 150ms ease, color 150ms ease;
        }
        .rp-export:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }
        .rp-export .material-symbols-outlined { font-size: 17px; }

        /* ── Métricas (sorteroOS admin/clubes) ── */
        .rp-sum { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
        .rp-tile {
            background: var(--rp-surface);
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            border-radius: 16px; padding: 16px;
            display: flex; flex-direction: column; align-items: stretch;
            transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), border-color 200ms ease;
        }
        .rp-tile:hover { border-color: color-mix(in srgb, var(--sb-outline-variant) 70%, transparent); }
        .rp-tile-top { display: flex; align-items: center; gap: 8px; color: var(--sb-on-surface-variant); }
        .rp-tile-top .material-symbols-outlined { font-size: 16px; }
        .rp-tile-lbl { font-size: 12px; font-weight: 500; color: var(--sb-on-surface-variant); }
        .rp-tile-num {
            font-size: 24px; font-weight: 700; letter-spacing: -.02em; line-height: 1.2;
            color: var(--sb-on-surface); margin-top: 8px; font-variant-numeric: tabular-nums;
        }

        /* ── Bloques de contenido ── */
        .rp-bars {
            display: flex; flex-direction: column; gap: 16px;
            background: var(--sb-surface); border-radius: 16px; padding: 18px 20px 20px;
            margin-bottom: 22px;
        }
        .rp-bar-row { display: grid; grid-template-columns: 110px 1fr 74px; align-items: center; gap: 14px; }
        .rp-bar-label { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--sb-on-surface); text-transform: none; letter-spacing: 0; }
        .rp-dot { width: 9px; height: 9px; border-radius: 50%; flex: none; }
        .rp-dot.green { background: #22c55e; }
        .rp-dot.amber { background: #f59e0b; }
        .rp-dot.gray  { background: #94a3b8; }
        .rp-dot.blue  { background: #3b82f6; }
        .rp-track { height: 10px; background: var(--sb-surface-container); border-radius: 999px; overflow: hidden; }
        .rp-track-fill { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
        .rp-track-fill.green { background: #22c55e; }
        .rp-track-fill.amber { background: #f59e0b; }
        .rp-track-fill.gray  { background: #94a3b8; }
        .rp-track-fill.blue  { background: #3b82f6; }
        .rp-bar-count { text-align: right; font-size: 13px; font-weight: 600; color: var(--sb-on-surface); }
        .rp-bar-count small { font-weight: 400; color: var(--sb-on-surface-variant); }

        .rp-list {
            display: flex; flex-direction: column; gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px; overflow: hidden;
        }
        .rp-item {
            display: flex; align-items: center; gap: 14px; padding: 14px 20px;
            background: var(--sb-surface); border: 0; border-radius: 0;
        }
        .rp-item:hover { background: var(--sb-surface-container-low); }
        .rp-av {
            width: 32px; height: 32px; border-radius: 8px; background: var(--sb-surface-container);
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
            font-weight: 600; font-size: 12px; display: grid; place-items: center; flex: none;
        }
        .rp-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
        .rp-name { font-weight: 500; font-size: 13.5px; color: var(--sb-on-surface); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .rp-dni { font-size: 12px; color: var(--sb-on-surface-variant); font-variant-numeric: tabular-nums; }
        .rp-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 12px; color: var(--sb-on-surface-variant); }
        .rp-meta b { color: var(--sb-on-surface); font-weight: 500; }
        .rp-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
        .rp-estado i { width: 8px; height: 8px; border-radius: 50%; }
        .rp-estado.present { color: #16a34a; } .rp-estado.present i { background: #22c55e; }
        .rp-estado.late    { color: #d97706; } .rp-estado.late    i { background: #f59e0b; }
        .rp-estado.absent  { color: #64748b; } .rp-estado.absent  i { background: #94a3b8; }
        .rp-list + .dev-empty, .dev-empty { background: var(--sb-surface); border-radius: 16px; }

        @media (max-width: 1100px) {
            .rp-sum { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            .rp-sum { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .rp-bar-row { grid-template-columns: 1fr 64px; }
            .rp-track { grid-column: 1 / -1; grid-row: 2; }
            .rp-bar { flex-direction: column; align-items: stretch; }
            .rp-bar-group, .rp-bar-group--grow { flex: 1 1 auto; }
            .rp-go { justify-content: center; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'reportes']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Reportes</h1>
            <p class="dev-topbar-subtitle">Análisis de asistencia del equipo</p>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <div class="rp-wrap">

            <?php
                // Totales de TODO el rango filtrado (no de la página visible)
                $r = $resumen ?? ['total' => 0, 'presentes' => 0, 'tardanzas' => 0, 'faltas' => 0, 'sin_salida' => 0];
                $totalRegistros = $r['total'];
                $totalPresentes = $r['presentes'];
                $totalTardanzas = $r['tardanzas'];
                $totalFaltas    = $r['faltas'];
                $totalSinSalida = $r['sin_salida'];
                $pct = static fn (int $n): int => $totalRegistros > 0 ? (int) round(($n / $totalRegistros) * 100) : 0;
                $pctPresentes = $pct($totalPresentes);
                $pctTardanzas = $pct($totalTardanzas);
                $pctFaltas    = $pct($totalFaltas);
                $pctSinSalida = $pct($totalSinSalida);

                $hoy     = date('Y-m-d');
                $semana  = date('Y-m-d', strtotime('monday this week'));
                $mesIni  = date('Y-m-01');
                $mode    = empty($fecha_inicio) || empty($fecha_fin) ? 'todo' : ($fecha_inicio === $fecha_fin ? ($fecha_inicio === $hoy ? 'hoy' : '') : ($fecha_inicio === $semana && $fecha_fin === $hoy ? 'semana' : ($fecha_inicio === $mesIni ? 'mes' : '')));
            ?>

            <div class="rp-sec-head">
                <h2><span class="material-symbols-outlined">filter_alt</span> Filtros de fecha</h2>
            </div>
            <form method="get" action="<?= base_url('admin/reportes') ?>" id="filtro-form" class="rp-bar">
                <div class="rp-bar-group">
                    <span class="rp-bar-cap">Fecha inicio</span>
                    <input type="date" class="rp-input" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                </div>
                <div class="rp-bar-group">
                    <span class="rp-bar-cap">Fecha fin</span>
                    <input type="date" class="rp-input" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                </div>
                <div class="rp-bar-group rp-bar-group--grow">
                    <span class="rp-bar-cap">Periodo</span>
                    <div class="rp-chips">
                        <button type="button" class="rp-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                        <button type="button" class="rp-chip <?= $mode === 'semana' ? 'on' : '' ?>" onclick="rango('semana')">Esta semana</button>
                        <button type="button" class="rp-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Este mes</button>
                        <button type="button" class="rp-chip <?= $mode === 'todo' ? 'on' : '' ?>" onclick="rango('todo')">Todo</button>
                    </div>
                </div>
                <button type="submit" class="rp-go">
                    <span class="material-symbols-outlined">search</span> Filtrar
                </button>
            </form>

            <div class="rp-sum">
                <div class="rp-tile">
                    <div class="rp-tile-top">
                        <span class="material-symbols-outlined">analytics</span>
                        <span class="rp-tile-lbl">Total registros</span>
                    </div>
                    <div class="rp-tile-num"><?= $totalRegistros ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="rp-tile-lbl">Presentes</span>
                    </div>
                    <div class="rp-tile-num"><?= $totalPresentes ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top">
                        <span class="material-symbols-outlined">schedule</span>
                        <span class="rp-tile-lbl">Tardanzas</span>
                    </div>
                    <div class="rp-tile-num"><?= $totalTardanzas ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top">
                        <span class="material-symbols-outlined">cancel</span>
                        <span class="rp-tile-lbl">Faltas</span>
                    </div>
                    <div class="rp-tile-num"><?= $totalFaltas ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="rp-tile-lbl">Sin salida</span>
                    </div>
                    <div class="rp-tile-num"><?= $totalSinSalida ?></div>
                </div>
            </div>

            <div class="rp-sec-head">
                <h2><span class="material-symbols-outlined">bar_chart</span> Distribución de asistencia</h2>
            </div>
            <div class="rp-bars">
                    <div class="rp-bar-row">
                        <span class="rp-bar-label"><i class="rp-dot green"></i> Presentes</span>
                        <div class="rp-track">
                            <div class="rp-track-fill green" style="width: <?= $pctPresentes ?>%;"></div>
                        </div>
                        <span class="rp-bar-count"><?= $totalPresentes ?><small> · <?= $pctPresentes ?>%</small></span>
                    </div>
                    <div class="rp-bar-row">
                        <span class="rp-bar-label"><i class="rp-dot amber"></i> Tardanzas</span>
                        <div class="rp-track">
                            <div class="rp-track-fill amber" style="width: <?= $pctTardanzas ?>%;"></div>
                        </div>
                        <span class="rp-bar-count"><?= $totalTardanzas ?><small> · <?= $pctTardanzas ?>%</small></span>
                    </div>
                    <div class="rp-bar-row">
                        <span class="rp-bar-label"><i class="rp-dot gray"></i> Faltas</span>
                        <div class="rp-track">
                            <div class="rp-track-fill gray" style="width: <?= $pctFaltas ?>%;"></div>
                        </div>
                        <span class="rp-bar-count"><?= $totalFaltas ?><small> · <?= $pctFaltas ?>%</small></span>
                    </div>
                    <div class="rp-bar-row">
                        <span class="rp-bar-label"><i class="rp-dot blue"></i> Sin salida</span>
                        <div class="rp-track">
                            <div class="rp-track-fill blue" style="width: <?= $pctSinSalida ?>%;"></div>
                        </div>
                        <span class="rp-bar-count"><?= $totalSinSalida ?><small> · <?= $pctSinSalida ?>%</small></span>
                    </div>
                </div>
            </div>

            <div class="rp-sec-head rp-sec-head--split">
                <h2><span class="material-symbols-outlined">table_chart</span> Detalle de asistencia</h2>
                <button type="button" class="rp-export" onclick="exportarCSV()">
                    <span class="material-symbols-outlined">download</span> Exportar CSV
                </button>
            </div>

                <?php if (!empty($attendance)): ?>
                    <div class="rp-list" id="tabla-asistencia">
                        <?php foreach ($attendance as $r):
                            $st   = $r['status'] ?? '';
                            $estado = match($st) { 'present' => 'present', 'late' => 'late', 'absent' => 'absent', default => 'absent' };
                            $label  = match($st) { 'present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', default => ucfirst($st) };
                            $nombre = $r['name'] ?? 'Empleado';
                        ?>
                        <div class="rp-item" data-nombre="<?= esc($nombre) ?>" data-dni="<?= esc($r['dni'] ?? '') ?>" data-fecha="<?= esc($r['date'] ?? '') ?>" data-entrada="<?= esc($r['time_in'] ?? '') ?>" data-salida="<?= esc($r['time_out'] ?? '') ?>" data-estado="<?= esc($label) ?>">
                            <div class="rp-av"><?= strtoupper(substr($nombre, 0, 1)) ?></div>
                            <div class="rp-main">
                                <div class="rp-name">
                                    <?= esc($nombre) ?>
                                    <span class="rp-dni"><?= esc($r['dni'] ?? '') ?></span>
                                </div>
                                <div class="rp-meta">
                                    <span><b><?= esc($r['date'] ?? '') ?></b></span>
                                    <span>Entrada <b><?= esc($r['time_in'] ?? '-') ?></b></span>
                                    <span>Salida <b><?= esc($r['time_out'] ?? '-') ?></b></span>
                                </div>
                            </div>
                            <span class="rp-estado <?= $estado ?>"><i></i><?= $label ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dev-empty">
                        <span class="material-symbols-outlined">inbox</span>
                        <p>No hay registros de asistencia en este rango.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
    function rango(tipo) {
        const form = document.getElementById('filtro-form');
        let todo = form.querySelector('input[name="todo"]');
        if (!todo) {
            todo = document.createElement('input');
            todo.type = 'hidden';
            todo.name = 'todo';
            todo.value = '0';
            form.appendChild(todo);
        }
        todo.value = tipo === 'todo' ? '1' : '0';
        const fi = document.getElementById('fecha_inicio');
        const ff = document.getElementById('fecha_fin');
        const d = new Date();
        const fmt = function(dt) {
            return dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0');
        };
        if (tipo === 'hoy') { fi.value = fmt(d); ff.value = fmt(d); }
        else if (tipo === 'semana') {
            const day = (d.getDay() + 6) % 7;
            const monday = new Date(d); monday.setDate(d.getDate() - day);
            fi.value = fmt(monday); ff.value = fmt(d);
        } else if (tipo === 'mes') {
            fi.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-01';
            ff.value = fmt(d);
        } else { fi.value = ''; ff.value = ''; }
        form.submit();
    }
    function exportarCSV() {
        const items = document.querySelectorAll('.rp-item');
        if (!items.length) return;
        const filas = [['Empleado', 'DNI', 'Fecha', 'Hora entrada', 'Hora salida', 'Estado']];
        items.forEach(function(item) {
            filas.push([item.dataset.nombre, item.dataset.dni, item.dataset.fecha, item.dataset.entrada, item.dataset.salida, item.dataset.estado]);
        });
        const csv = filas.map(function(fila) { return fila.map(function(c) { return '"' + (c || '').replace(/"/g, '""') + '"'; }).join(','); }).join('\n');
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'reporte_asistencia_' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    </script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>