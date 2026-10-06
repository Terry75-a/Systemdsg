<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .att-wrap { display: flex; flex-direction: column; gap: 22px; }

        /* ── Filtros en tarjetas ──────────────── */
        .att-filters {
            display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr) minmax(0, 1.25fr);
            gap: 14px;
        }
        .att-fcard { display: flex; flex-direction: column; min-width: 0; }
        .att-fcard .dev-card-head { flex: none; }
        .att-fcard .dev-card-head h2 { font-size: 0.95rem; letter-spacing: -0.01em; }
        .att-fbody {
            padding: 16px 18px 18px; display: flex; flex-direction: column; gap: 14px; flex: 1;
        }
        .att-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .att-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .att-field > label { font-size: 0.78rem; font-weight: 500; color: var(--g-text-secondary); }
        .att-input {
            height: 40px; padding: 0 12px; border: 1px solid var(--g-border); width: 100%;
            border-radius: 12px; background: var(--g-surface); color: var(--g-text);
            font-size: 0.875rem; font-family: inherit; outline: none;
        }
        .att-input:focus { border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.12); }
        .att-go {
            height: 40px; padding: 0 18px; border: 0; border-radius: 12px; cursor: pointer;
            background: var(--g-primary); color: var(--g-bg, #fff); font-weight: 600; font-size: 0.875rem;
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            font-family: inherit; width: 100%;
        }
        .att-go:hover { filter: brightness(1.07); }
        .att-go-ghost {
            background: transparent; color: var(--g-text); border: 1px solid var(--g-border);
            height: 36px; border-radius: 12px; width: auto; padding: 0 16px;
        }
        .att-go-ghost:hover { background: var(--g-surface-variant); filter: none; }
        .att-quick { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .att-quick .att-chip { height: 36px; justify-content: center; }
        .att-chips { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; align-content: flex-start; }
        .att-chip {
            height: 34px; padding: 0 14px; border: 1px solid var(--g-border);
            border-radius: 999px; background: var(--g-surface); color: var(--g-text-secondary); cursor: pointer;
            font-size: 0.8rem; font-weight: 500; font-family: inherit;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 150ms, color 150ms, border-color 150ms;
        }
        .att-chip:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .att-chip.on { background: var(--g-primary); border-color: var(--g-primary); color: var(--g-bg, #fff); }
        .att-chip b { font-size: 0.72rem; font-weight: 700; opacity: .85; }

        /* ── KPIs: métricas unidas (card del dashboard) ── */
        body:has(.dev-main) .dev-stats-grid .dev-stat-card.att-stat.on {
            background: var(--sb-surface-container-low);
            box-shadow: inset 0 0 0 1px var(--g-primary) !important;
        }
        body:has(.dev-main) .dev-stats-grid .dev-stat-card.att-stat.on .dev-stat-value { color: var(--g-primary); }
        body:has(.dev-main) .dev-stats-grid .dev-stat-card.att-stat.on .dev-stat-label { color: var(--g-text-secondary); }

        /* ── Registros ───────────────────────── */
        .att-list { display: flex; flex-direction: column; gap: 10px; padding: 14px 18px 18px; }
        .att-item {
            display: flex; align-items: center; gap: 14px; padding: 14px 16px;
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 14px;
            cursor: pointer; transition: border-color 200ms;
        }
        .att-item:hover { border-color: var(--g-primary); }
        .att-av {
            width: 42px; height: 42px; border-radius: 12px; background: var(--g-surface-variant);
            color: var(--g-text-secondary); font-weight: 600; font-size: 0.95rem;
            display: grid; place-items: center; flex: none;
        }
        .att-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
        .att-name { font-weight: 600; font-size: 0.9rem; color: var(--g-text); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .att-dni { font-size: 0.78rem; color: var(--g-text-secondary); font-variant-numeric: tabular-nums; }
        .att-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.8rem; color: var(--g-text-secondary); }
        .att-meta b { color: var(--g-text); font-weight: 500; }
        .att-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; }
        .att-estado i { width: 8px; height: 8px; border-radius: 50%; }
        .att-estado.present { color: #16a34a; } .att-estado.present i { background: #22c55e; }
        .att-estado.late    { color: #d97706; } .att-estado.late    i { background: #f59e0b; }
        .att-estado.absent  { color: #64748b; } .att-estado.absent  i { background: #94a3b8; }
        .att-estado.no_exit { color: #2563eb; } .att-estado.no_exit i { background: #3b82f6; }
        .att-edit {
            width: 36px; height: 36px; border: 1px solid var(--g-border); border-radius: 10px;
            background: transparent; color: var(--g-text-secondary); cursor: pointer;
            display: grid; place-items: center; flex: none; transition: border-color 200ms, color 200ms;
        }
        .att-edit:hover { border-color: var(--g-primary); color: var(--g-primary); }
        .att-edit .material-symbols-outlined { font-size: 18px; }

        /* ── Detalle ─────────────────────────── */
        .detail-overlay {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,.4); backdrop-filter: blur(4px);
            opacity: 0; visibility: hidden; transition: opacity 200ms, visibility 200ms;
        }
        .detail-overlay.active { opacity: 1; visibility: visible; }
        .detail-card {
            background: var(--g-surface); border-radius: 24px; width: 90%; max-width: 460px;
            max-height: 85vh; overflow-y: auto;
            transform: scale(.96); transition: transform 200ms;
        }
        .detail-overlay.active .detail-card { transform: scale(1); }
        .detail-head { padding: 22px 24px 14px; display: flex; align-items: center; gap: 14px; }
        .detail-av {
            width: 52px; height: 52px; border-radius: 14px; color: #fff; font-size: 1.2rem; font-weight: 600;
            display: grid; place-items: center; flex: none;
        }
        .detail-name { font-size: 1.05rem; font-weight: 600; color: var(--g-text); }
        .detail-sub { font-size: 0.8rem; color: var(--g-text-secondary); margin-top: 2px; }
        .detail-close { margin-left: auto; }
        .detail-body { padding: 0 24px 8px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: var(--g-text-secondary); text-transform: uppercase; letter-spacing: .04em; }
        .detail-value {
            font-size: 0.9rem; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--g-surface-variant); border-radius: 12px;
            font-variant-numeric: tabular-nums;
        }
        .detail-actions { display: flex; gap: 10px; padding: 16px 24px 22px; }

        @media (max-width: 1024px) {
            .att-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .att-fcard--estado { grid-column: 1 / -1; }
        }
        @media (max-width: 760px) {
            .att-filters { grid-template-columns: 1fr; }
            .att-fcard--estado { grid-column: auto; }
            .att-fields { grid-template-columns: 1fr; }
            .att-go { justify-content: center; }
        }
        /* ── Paginación ─────────────────────── */
        .att-pager { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; padding: 16px 18px 20px; }
        .att-page {
            min-width: 34px; height: 34px; padding: 0 9px; display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--g-border); border-radius: 9px; background: transparent;
            color: var(--g-text); font-size: .82rem; font-weight: 600; text-decoration: none; font-family: inherit;
        }
        .att-page:hover { background: var(--g-surface-variant); }
        .att-page.on { background: var(--g-primary); border-color: var(--g-primary); color: var(--g-bg, #fff); }
        .att-page-info { font-size: .78rem; color: var(--g-text-secondary); margin-left: 8px; }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=20261004g') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=1') ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'asistencias']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Asistencias</h1>
            <p class="dev-topbar-subtitle">Registro de asistencia del equipo</p>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <?php
                // Totales de TODO el rango filtrado (no de la página visible)
                $r = $resumen ?? ['total' => 0, 'presentes' => 0, 'tardanzas' => 0, 'faltas' => 0, 'sin_salida' => 0];
                $totalRegistros = $r['total'];
                $totalPresentes = $r['presentes'];
                $totalTardanzas = $r['tardanzas'];
                $totalFaltas    = $r['faltas'];
                $totalSinSalida = $r['sin_salida'];
                $filtroEstado   = trim((string) ($estado ?? ''));
                $hoy    = date('Y-m-d');
                $semana = date('Y-m-d', strtotime('monday this week'));
                $mesIni = date('Y-m-01');
                $mode   = empty($fecha_inicio) || empty($fecha_fin) ? 'todo' : ($fecha_inicio === $fecha_fin ? ($fecha_inicio === $hoy ? 'hoy' : '') : ($fecha_inicio === $semana && $fecha_fin === $hoy ? 'semana' : ($fecha_inicio === $mesIni ? 'mes' : '')));
            ?>

            <div class="att-wrap">
                <?php
                    // Links de los KPI: conservan el rango actual y cambian sólo el estado
                    $qsBase = array_merge($pag_qs ?? [], ['page' => 1]);
                    $kpiLink = static function (string $est) use ($qsBase): string {
                        $qs = $est === '' ? array_diff_key($qsBase, ['estado' => 1]) : array_merge($qsBase, ['estado' => $est]);
                        return '?' . http_build_query($qs);
                    };
                ?>

                <!-- Filtros en tarjetas -->
                <div class="att-filters">
                    <div class="dev-card att-fcard">
                        <div class="dev-card-head">
                            <h2><span class="material-symbols-outlined">date_range</span> Rango de fechas</h2>
                            <form method="post" action="<?= site_url('admin/exportar/asistencias') ?>" class="att-csv">
                                <?= csrf_field() ?>
                                <input type="hidden" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                                <input type="hidden" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                                <input type="hidden" name="estado" value="<?= esc($filtroEstado) ?>">
                                <button type="submit" class="att-go att-go-ghost" title="Descargar el rango filtrado en CSV">
                                    <span class="material-symbols-outlined">download</span> CSV
                                </button>
                            </form>
                        </div>
                        <form method="get" action="<?= base_url('admin/asistencias') ?>" id="filtro-form" class="att-fbody">
                            <input type="hidden" name="estado" id="estado" value="<?= esc($filtroEstado) ?>">
                            <div class="att-fields">
                                <div class="att-field">
                                    <label for="fecha_inicio">Desde</label>
                                    <input type="date" class="att-input" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                                </div>
                                <div class="att-field">
                                    <label for="fecha_fin">Hasta</label>
                                    <input type="date" class="att-input" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                                </div>
                            </div>
                            <button type="submit" class="att-go">
                                <span class="material-symbols-outlined">search</span> Filtrar
                            </button>
                        </form>
                    </div>

                    <div class="dev-card att-fcard">
                        <div class="dev-card-head">
                            <h2><span class="material-symbols-outlined">history</span> Periodo</h2>
                        </div>
                        <div class="att-fbody">
                            <div class="att-quick">
                                <button type="button" class="att-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                                <button type="button" class="att-chip <?= $mode === 'semana' ? 'on' : '' ?>" onclick="rango('semana')">Semana</button>
                                <button type="button" class="att-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Mes</button>
                                <button type="button" class="att-chip <?= $mode === 'todo' ? 'on' : '' ?>" onclick="rango('todo')">Todo</button>
                            </div>
                        </div>
                    </div>

                    <div class="dev-card att-fcard att-fcard--estado">
                        <div class="dev-card-head">
                            <h2><span class="material-symbols-outlined">filter_alt</span> Estado</h2>
                        </div>
                        <div class="att-fbody">
                            <div class="att-chips">
                                <?php
                                    // Los conteos sólo tienen sentido sin filtro de estado (con filtro, el resto queda en 0)
                                    $conteos = $filtroEstado === '' ? [
                                        ''         => $totalRegistros,
                                        'present'  => $totalPresentes,
                                        'late'     => $totalTardanzas,
                                        'absent'   => $totalFaltas,
                                        'no_exit'  => $totalSinSalida,
                                    ] : [];
                                    $estados = [
                                        ''         => 'Todos',
                                        'present'  => 'Presentes',
                                        'late'     => 'Tardanzas',
                                        'absent'   => 'Faltas',
                                        'no_exit'  => 'Sin salida',
                                    ];
                                    foreach ($estados as $val => $txt): ?>
                                        <button type="button" class="att-chip <?= $filtroEstado === $val ? 'on' : '' ?>" onclick="estado('<?= $val ?>')">
                                            <?= $txt ?><?= isset($conteos[$val]) ? ' <b>' . $conteos[$val] . '</b>' : '' ?>
                                        </button>
                                    <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPIs · métricas unidas (card del dashboard) -->
                <div class="dev-stats-grid">
                    <?php
                        $kpis = [
                            [''        , 'summarize'   , 'Total registros', $totalRegistros, 'si-accent'],
                            ['present' , 'check_circle', 'Presentes'      , $totalPresentes, 'si-green'],
                            ['late'    , 'schedule'    , 'Tardanzas'      , $totalTardanzas, 'si-amber'],
                            ['absent'  , 'cancel'      , 'Faltas'         , $totalFaltas   , 'si-red'],
                            ['no_exit' , 'logout'      , 'Sin salida'     , $totalSinSalida, 'si-accent'],
                        ];
                        foreach ($kpis as [$est, $ico, $lbl, $num, $cls]): ?>
                            <a class="dev-stat-card att-stat <?= $filtroEstado === $est ? 'on' : '' ?>"
                               href="<?= $kpiLink($est) ?>" title="Filtrar por <?= $lbl ?>">
                                <div class="dev-stat-icon <?= $cls ?>"><span class="material-symbols-outlined filled"><?= $ico ?></span></div>
                                <div class="dev-stat-label"><?= $lbl ?></div>
                                <div class="dev-stat-value"><?= $num ?></div>
                            </a>
                        <?php endforeach; ?>
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">history</span> Registros</h2>
                    </div>

                    <?php if (empty($attendance)): ?>
                        <div class="dev-empty">
                            <span class="material-symbols-outlined">event_busy</span>
                            <p>No hay registros de asistencia en este rango.</p>
                        </div>
                    <?php else: ?>
                        <div class="att-list">
                            <?php foreach ($attendance as $r):
                                $st     = $r['status'] ?? 'absent';
                                $estado = match($st) { 'present' => 'present', 'late' => 'late', 'absent' => 'absent', 'no_exit' => 'no_exit', default => 'absent' };
                                $label  = match($st) { 'present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', default => ucfirst($st) };
                                $nombre = $r['name'] ?? 'Empleado';
                            ?>
                            <div class="att-item" onclick="openDetail('<?= (int) $r['id'] ?>')">
                                <div class="att-av"><?= strtoupper(mb_substr($nombre, 0, 1, 'UTF-8')) ?></div>
                                <div class="att-main">
                                    <div class="att-name">
                                        <?= esc($nombre) ?>
                                        <span class="att-dni"><?= esc($r['dni'] ?? '') ?></span>
                                    </div>
                                    <div class="att-meta">
                                        <span><b><?= esc($r['date'] ?? '') ?></b></span>
                                        <span>Entrada <b><?= esc($r['time_in'] ?? '-') ?></b></span>
                                        <span>Salida <b><?= esc($r['time_out'] ?? '-') ?></b></span>
                                    </div>
                                </div>
                                <span class="att-estado <?= $estado ?>"><i></i><?= $label ?></span>
                                <a class="att-edit" style="margin-right:8px;text-decoration:none;" href="<?= site_url('admin/auditoria/' . (int) $r['id']) ?>" title="Auditoría" onclick="event.stopPropagation();">
                                    <span class="material-symbols-outlined">manage_search</span>
                                </a>
                                <button type="button" class="att-edit" onclick="event.stopPropagation(); openEdit(<?= (int) $r['id'] ?>)" title="Editar">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (($paginas ?? 1) > 1): ?>
                        <div class="att-pager">
                            <?php if ($pagina > 1): ?>
                                <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $pagina - 1]))) ?>">&laquo;</a>
                            <?php endif; ?>
                            <?php
                                $desde = max(1, $pagina - 3);
                                $hastaPag = min($paginas, $pagina + 3);
                                if ($desde > 1): ?>
                                    <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => 1]))) ?>">1</a>
                                    <?php if ($desde > 2): ?><span class="att-page-info">…</span><?php endif; ?>
                                <?php endif;
                                for ($n = $desde; $n <= $hastaPag; $n++): ?>
                                    <?php if ($n === $pagina): ?>
                                        <span class="att-page on"><?= $n ?></span>
                                    <?php else: ?>
                                        <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $n]))) ?>"><?= $n ?></a>
                                    <?php endif; ?>
                                <?php endfor;
                                if ($hastaPag < $paginas): ?>
                                    <?php if ($hastaPag < $paginas - 1): ?><span class="att-page-info">…</span><?php endif; ?>
                                    <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $paginas]))) ?>"><?= $paginas ?></a>
                                <?php endif; ?>
                            <?php if ($pagina < $paginas): ?>
                                <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $pagina + 1]))) ?>">&raquo;</a>
                            <?php endif; ?>
                            <span class="att-page-info">
                                Página <?= $pagina ?> de <?= $paginas ?> · <?= number_format((int) ($totalFilas ?? 0)) ?> registros
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div class="detail-overlay" id="attDetailOverlay" onclick="if(event.target===this)closeDetail()">
        <div class="detail-card">
            <div class="detail-head">
                <div class="detail-av" id="attDetailAvatar">E</div>
                <div>
                    <div class="detail-name" id="attDetailName">-</div>
                    <div class="detail-sub" id="attDetailDni">-</div>
                </div>
                <button type="button" class="dev-modal-close detail-close" onclick="closeDetail()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="detail-body">
                <div class="detail-grid">
                    <div class="detail-field">
                        <div class="detail-label">Fecha</div>
                        <div class="detail-value" id="attDetailDate">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Estado</div>
                        <div class="detail-value" id="attDetailStatus">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Hora entrada</div>
                        <div class="detail-value" id="attDetailTimeIn">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Hora salida</div>
                        <div class="detail-value" id="attDetailTimeOut">-</div>
                    </div>
                    <div class="detail-field" style="grid-column:span 2;">
                        <div class="detail-label">Observación</div>
                        <div class="detail-value" id="attDetailObs">-</div>
                    </div>
                </div>
            </div>
            <div class="detail-actions">
                <button type="button" class="dev-btn dev-btn-text" onclick="closeDetail()">Cerrar</button>
                <a class="dev-btn dev-btn-outline" id="attDetailAuditBtn" href="<?= site_url('admin/asistencias') ?>" style="text-decoration:none;">
                    <span class="material-symbols-outlined">manage_search</span> Auditoría
                </a>
                <button type="button" class="dev-btn dev-btn-primary" id="attDetailEditBtn">
                    <span class="material-symbols-outlined">edit</span> Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="dev-modal-overlay" id="editModal">
        <div class="dev-modal">
            <div class="dev-modal-head">
                <h2><span class="material-symbols-outlined">edit</span> Editar asistencia</h2>
                <button type="button" class="dev-modal-close" onclick="closeEdit()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="post" action="<?= site_url('admin/update-attendance') ?>">
                <?= csrf_field() ?>
                <div class="dev-modal-body">
                    <input type="hidden" name="att_id" id="modal_att_id">
                    <div class="dev-form-grid">
                        <div class="dev-field">
                            <label for="modal_time_in">Hora entrada</label>
                            <input type="time" name="time_in" id="modal_time_in" required>
                        </div>
                        <div class="dev-field">
                            <label for="modal_time_out">Hora salida</label>
                            <input type="time" name="time_out" id="modal_time_out">
                        </div>
                        <div class="dev-field">
                            <label for="modal_status">Estado</label>
                            <select name="status" id="modal_status" required>
                                <option value="present">Presente</option>
                                <option value="late">Tardanza</option>
                                <option value="absent">Falta</option>
                                <option value="no_exit">Sin salida</option>
                            </select>
                        </div>
                        <div class="dev-field">
                            <label for="modal_observacion">Observación</label>
                            <input type="text" name="observacion" id="modal_observacion" placeholder="Nota opcional...">
                        </div>
                        <div class="dev-field" style="grid-column:1 / -1;">
                            <label for="modal_motivo">Motivo del cambio <span style="color:var(--g-text-secondary);font-weight:400;">(queda registrado en la auditoría)</span></label>
                            <input type="text" name="motivo" id="modal_motivo" maxlength="255" placeholder="Ej. El empleado marcó tarde por motivo personal">
                        </div>
                    </div>
                </div>
                <div class="dev-modal-footer">
                    <button type="button" class="dev-btn dev-btn-text" onclick="closeEdit()">Cancelar</button>
                    <button type="submit" class="dev-btn dev-btn-primary">
                        <span class="material-symbols-outlined">save</span> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    var attRows = <?php $attRowsMap = [];
        foreach ($attendance as $r) {
            $attRowsMap[(int) $r['id']] = [
                'id' => (int) $r['id'], 'name' => $r['name'] ?? 'Empleado', 'dni' => $r['dni'] ?? '',
                'date' => $r['date'] ?? '', 'time_in' => $r['time_in'] ?? '', 'time_out' => $r['time_out'] ?? '',
                'status' => $r['status'] ?? '', 'observacion' => $r['observacion'] ?? '',
            ];
        }
        echo json_encode($attRowsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    var statusMeta = {
        present: {label: 'Presente',  color: '#16a34a', bg: '#22c55e'},
        late:    {label: 'Tardanza',  color: '#d97706', bg: '#f59e0b'},
        absent:  {label: 'Falta',     color: '#64748b', bg: '#94a3b8'},
        no_exit: {label: 'Sin salida',color: '#2563eb', bg: '#3b82f6'}
    };

    function openDetail(id) {
        var r = attRows[id];
        if (!r) return;
        var m = statusMeta[r.status] || statusMeta.absent;
        document.getElementById('attDetailAvatar').textContent = (r.name || 'E').charAt(0).toUpperCase();
        document.getElementById('attDetailAvatar').style.background = m.bg;
        document.getElementById('attDetailName').textContent = r.name;
        document.getElementById('attDetailDni').textContent = 'DNI ' + (r.dni || '---');
        document.getElementById('attDetailDate').textContent = r.date || '-';
        document.getElementById('attDetailTimeIn').textContent = r.time_in || '-';
        document.getElementById('attDetailTimeOut').textContent = r.time_out || '-';
        var st = document.getElementById('attDetailStatus');
        st.textContent = m.label;
        st.style.color = m.color;
        document.getElementById('attDetailObs').textContent = r.observacion || 'Sin observación';
        document.getElementById('attDetailAuditBtn').href = '<?= site_url('admin/auditoria/') ?>' + r.id;
        document.getElementById('attDetailEditBtn').onclick = function () { closeDetail(); openEdit(r.id); };
        document.getElementById('attDetailOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeDetail() {
        document.getElementById('attDetailOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function openEdit(id) {
        var r = attRows[id];
        if (!r) return;
        document.getElementById('modal_att_id').value = r.id;
        document.getElementById('modal_time_in').value = r.time_in || '';
        document.getElementById('modal_time_out').value = r.time_out || '';
        document.getElementById('modal_status').value = r.status || 'absent';
        document.getElementById('modal_observacion').value = r.observacion || '';
        document.getElementById('modal_motivo').value = '';
        document.getElementById('editModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeEdit() {
        document.getElementById('editModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('editModal').addEventListener('click', function (e) { if (e.target === this) closeEdit(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeEdit(); closeDetail(); } });

    function estado(valor) {
        document.getElementById('estado').value = valor;
        document.getElementById('filtro-form').submit();
    }

    function rango(tipo) {
        var form = document.getElementById('filtro-form');
        var todo = form.querySelector('input[name="todo"]');
        if (!todo) {
            todo = document.createElement('input');
            todo.type = 'hidden';
            todo.name = 'todo';
            todo.value = '0';
            form.appendChild(todo);
        }
        todo.value = tipo === 'todo' ? '1' : '0';
        var fi = document.getElementById('fecha_inicio');
        var ff = document.getElementById('fecha_fin');
        var d = new Date();
        function fmt(dt) { return dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0'); }
        if (tipo === 'hoy') { fi.value = fmt(d); ff.value = fmt(d); }
        else if (tipo === 'semana') {
            var day = (d.getDay() + 6) % 7;
            var monday = new Date(d); monday.setDate(d.getDate() - day);
            fi.value = fmt(monday); ff.value = fmt(d);
        } else if (tipo === 'mes') {
            fi.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-01';
            ff.value = fmt(d);
        } else { fi.value = ''; ff.value = ''; }
        form.submit();
    }
    </script>
</body>
</html>