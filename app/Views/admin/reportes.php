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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .rp-wrap { display: flex; flex-direction: column; gap: 22px; }

        /* ── Filtros ─────────────────────────── */
        .rp-filters { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; padding: 18px; }
        .rp-field { display: flex; flex-direction: column; gap: 6px; }
        .rp-field > label { font-size: 0.78rem; font-weight: 500; color: var(--g-text-secondary); }
        .rp-input {
            height: 40px; padding: 0 12px; border: 1px solid var(--g-border);
            border-radius: 10px; background: var(--g-surface); color: var(--g-text);
            font-size: 0.875rem; font-family: inherit; outline: none;
        }
        .rp-input:focus { border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.14); }
        .rp-go {
            height: 40px; padding: 0 18px; border: 0; border-radius: 10px; cursor: pointer;
            background: var(--g-primary); color: #fff; font-weight: 600; font-size: 0.875rem;
            display: inline-flex; align-items: center; gap: 6px; font-family: inherit;
        }
        .rp-go:hover { filter: brightness(1.07); }
        .rp-chips { display: flex; gap: 8px; flex-wrap: wrap; padding: 0 18px 18px; }
        .rp-chip {
            height: 30px; padding: 0 12px; border: 1px solid var(--g-border);
            border-radius: 999px; background: transparent; color: var(--g-text-secondary); cursor: pointer;
            font-size: 0.78rem; font-weight: 500; font-family: inherit;
        }
        .rp-chip:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .rp-chip.on { background: var(--g-primary); border-color: var(--g-primary); color: #fff; }

        /* ── Resumen ─────────────────────────── */
        .rp-sum { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .rp-tile {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        }
        .rp-tile-ico { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex: none; }
        .rp-tile-ico .material-symbols-outlined { font-size: 20px; }
        .rp-tile-ico.plain  { background: rgba(27,122,66,.10); color: var(--g-primary); }
        .rp-tile-ico.green  { background: rgba(34,197,94,.14);  color: #16a34a; }
        .rp-tile-ico.amber  { background: rgba(245,158,11,.16); color: #d97706; }
        .rp-tile-ico.gray   { background: rgba(148,163,184,.20); color: #64748b; }
        .rp-tile-num { font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: var(--g-text); }
        .rp-tile-lbl { font-size: 0.78rem; color: var(--g-text-secondary); margin-top: 2px; }

        /* ── Barras ──────────────────────────── */
        .rp-bars { display: flex; flex-direction: column; gap: 16px; padding: 6px 18px 20px; }
        .rp-bar-row { display: grid; grid-template-columns: 110px 1fr 40px; align-items: center; gap: 14px; }
        .rp-bar-label { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 500; color: var(--g-text); }
        .rp-dot { width: 9px; height: 9px; border-radius: 50%; flex: none; }
        .rp-dot.green { background: #22c55e; }
        .rp-dot.amber { background: #f59e0b; }
        .rp-dot.gray  { background: #94a3b8; }
        .rp-track { height: 10px; background: var(--g-surface-variant); border-radius: 999px; overflow: hidden; }
        .rp-track-fill { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
        .rp-track-fill.green { background: #22c55e; }
        .rp-track-fill.amber { background: #f59e0b; }
        .rp-track-fill.gray  { background: #94a3b8; }
        .rp-bar-count { text-align: right; font-size: 0.85rem; font-weight: 600; color: var(--g-text); }

        /* ── Detalle ─────────────────────────── */
        .rp-list { display: flex; flex-direction: column; gap: 10px; padding: 14px 18px 18px; }
        .rp-item {
            display: flex; align-items: center; gap: 14px; padding: 16px 18px;
            background: var(--g-surface);
            border: 1px solid var(--g-border);
            border-radius: 14px;
        }
        .rp-item:hover { border-color: rgba(128,128,128,.35); }
        .rp-av {
            width: 42px; height: 42px; border-radius: 12px; background: var(--g-surface-variant);
            color: var(--g-text-secondary); font-weight: 600; font-size: 0.95rem; display: grid; place-items: center; flex: none;
        }
        .rp-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
        .rp-name { font-weight: 600; font-size: 0.9rem; color: var(--g-text); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .rp-dni { font-size: 0.78rem; color: var(--g-text-secondary); font-variant-numeric: tabular-nums; }
        .rp-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.8rem; color: var(--g-text-secondary); }
        .rp-meta b { color: var(--g-text); font-weight: 500; }
        .rp-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; }
        .rp-estado i { width: 8px; height: 8px; border-radius: 50%; }
        .rp-estado.present { color: #16a34a; } .rp-estado.present i { background: #22c55e; }
        .rp-estado.late    { color: #d97706; } .rp-estado.late    i { background: #f59e0b; }
        .rp-estado.absent  { color: #64748b; } .rp-estado.absent  i { background: #94a3b8; }

        @media (max-width: 760px) {
            .rp-sum { grid-template-columns: repeat(2, 1fr); }
            .rp-tile { padding: 14px; }
            .rp-bar-row { grid-template-columns: 1fr 40px; }
            .rp-bar-label { padding-left: 0; }
            .rp-track { grid-column: 1 / -1; grid-row: 2; }
            .rp-filters { flex-direction: column; align-items: stretch; }
            .rp-go { justify-content: center; }
        }
    </style>
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
                $totalRegistros = count($attendance);
                $totalPresentes = 0;
                $totalTardanzas = 0;
                $totalFaltas = 0;
                foreach ($attendance as $r) {
                    $st = $r['status'] ?? '';
                    if ($st === 'present') $totalPresentes++;
                    elseif ($st === 'late') $totalTardanzas++;
                    elseif ($st === 'absent') $totalFaltas++;
                }
                $pctPresentes = $totalRegistros > 0 ? round(($totalPresentes / $totalRegistros) * 100) : 0;
                $pctTardanzas = $totalRegistros > 0 ? round(($totalTardanzas / $totalRegistros) * 100) : 0;
                $pctFaltas = $totalRegistros > 0 ? round(($totalFaltas / $totalRegistros) * 100) : 0;

                $hoy     = date('Y-m-d');
                $semana  = date('Y-m-d', strtotime('monday this week'));
                $mesIni  = date('Y-m-01');
                $mode    = empty($fecha_inicio) || empty($fecha_fin) ? 'todo' : ($fecha_inicio === $fecha_fin ? ($fecha_inicio === $hoy ? 'hoy' : '') : ($fecha_inicio === $semana && $fecha_fin === $hoy ? 'semana' : ($fecha_inicio === $mesIni ? 'mes' : '')));
            ?>

            <div class="dev-card">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">filter_alt</span> Filtros de fecha</h2>
                </div>
                <form method="get" action="<?= base_url('admin/reportes') ?>" id="filtro-form">
                    <div class="rp-filters">
                        <div class="rp-field">
                            <label for="fecha_inicio">Fecha inicio</label>
                            <input type="date" class="rp-input" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                        </div>
                        <div class="rp-field">
                            <label for="fecha_fin">Fecha fin</label>
                            <input type="date" class="rp-input" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                        </div>
                        <button type="submit" class="rp-go">
                            <span class="material-symbols-outlined">search</span> Filtrar
                        </button>
                    </div>
                    <div class="rp-chips">
                        <button type="button" class="rp-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                        <button type="button" class="rp-chip <?= $mode === 'semana' ? 'on' : '' ?>" onclick="rango('semana')">Esta semana</button>
                        <button type="button" class="rp-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Este mes</button>
                        <button type="button" class="rp-chip <?= $mode === 'todo' ? 'on' : '' ?>" onclick="rango('todo')">Todo</button>
                    </div>
                </form>
            </div>

            <div class="rp-sum">
                <div class="rp-tile">
                    <div class="rp-tile-ico plain"><span class="material-symbols-outlined">analytics</span></div>
                    <div>
                        <div class="rp-tile-num"><?= $totalRegistros ?></div>
                        <div class="rp-tile-lbl">Total registros</div>
                    </div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-ico green"><span class="material-symbols-outlined">check_circle</span></div>
                    <div>
                        <div class="rp-tile-num"><?= $totalPresentes ?></div>
                        <div class="rp-tile-lbl">Presentes</div>
                    </div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-ico amber"><span class="material-symbols-outlined">schedule</span></div>
                    <div>
                        <div class="rp-tile-num"><?= $totalTardanzas ?></div>
                        <div class="rp-tile-lbl">Tardanzas</div>
                    </div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-ico gray"><span class="material-symbols-outlined">cancel</span></div>
                    <div>
                        <div class="rp-tile-num"><?= $totalFaltas ?></div>
                        <div class="rp-tile-lbl">Faltas</div>
                    </div>
                </div>
            </div>

            <div class="dev-card">
                <div class="dev-card-head">
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
                </div>
            </div>

            <div class="dev-card">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">table_chart</span> Detalle de asistencia</h2>
                    <div style="margin-left:auto;">
                        <button type="button" class="dev-btn dev-btn-outline" onclick="exportarCSV()">
                            <span class="material-symbols-outlined">download</span> Exportar CSV
                        </button>
                    </div>
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
</body>
</html>