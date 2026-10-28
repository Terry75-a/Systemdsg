<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horas por persona - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        /* ═══════════════════════════════════════════════════════════
           HORAS · resumen por persona (misma gramática visual que
           reportes.php: filtros en barra, tiles de KPI, tabla con
           saldo coloreado y export en la cabecera de la sección)
           ═══════════════════════════════════════════════════════════ */
        body { --rp-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }
        .rp-wrap { display: flex; flex-direction: column; gap: 22px; }

        .rp-sec-head { margin: 0 0 12px; }
        .rp-sec-head--split { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .rp-sec-head h2 {
            margin: 0; font-family: inherit; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .rp-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }

        .rp-bar {
            display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px;
            background: var(--sb-surface); border-radius: 16px; padding: 16px 18px;
        }
        .rp-bar-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .rp-bar-group--grow { flex: 1 1 200px; }
        .rp-bar-cap {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            white-space: nowrap;
        }
        .rp-input {
            height: 38px; padding: 0 10px; border: none; border-radius: 12px;
            background: var(--sb-surface-container); color: var(--sb-on-surface);
            font-size: .85rem; font-family: inherit; outline: none;
            transition: box-shadow 125ms ease;
        }
        .rp-input:hover { background: var(--sb-surface-container-high); }
        .rp-input:focus, .rp-input:focus-visible { box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        .rp-go {
            height: 38px; padding: 0 18px; border: 0; border-radius: 999px; cursor: pointer;
            background: var(--sb-primary); color: #fff; font: inherit; font-size: .85rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .rp-go:hover { filter: brightness(1.06); }
        .rp-go:active { transform: scale(.97); }

        .rp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .rp-chip {
            height: 38px; padding: 0 14px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent);
            border-radius: 999px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            font: inherit; font-size: .8rem; font-weight: 600; cursor: pointer;
        }
        .rp-chip:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }
        .rp-chip.on { background: var(--sb-primary); border-color: var(--sb-primary); color: #fff; }

        /* ── Tiles ── */
        .rp-sum { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; }
        .rp-tile {
            background: var(--rp-surface);
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            border-radius: 16px; padding: 16px;
            display: flex; flex-direction: column; transition: border-color 200ms ease;
        }
        .rp-tile:hover { border-color: color-mix(in srgb, var(--sb-outline-variant) 70%, transparent); }
        .rp-tile-top { display: flex; align-items: center; gap: 8px; color: var(--sb-on-surface-variant); }
        .rp-tile-top .material-symbols-outlined { font-size: 16px; }
        .rp-tile-lbl { font-size: 12px; font-weight: 500; color: var(--sb-on-surface-variant); }
        .rp-tile-num {
            font-size: 24px; font-weight: 700; letter-spacing: -.02em; line-height: 1.2;
            color: var(--sb-on-surface); margin-top: 8px; font-variant-numeric: tabular-nums;
        }
        .rp-tile-num small { font-size: 13px; font-weight: 500; color: var(--sb-on-surface-variant); margin-left: 4px; }
        .rp-tile-num.pos { color: var(--sb-primary); }
        .rp-tile-num.neg { color: var(--sb-error, #e34948); }

        /* ── Tabla ── */
        .rp-note {
            font-size: 12px; color: var(--sb-on-surface-variant); margin: 10px 2px 0;
            display: flex; gap: 6px; align-items: flex-start;
        }
        .rp-note .material-symbols-outlined { font-size: 15px; margin-top: 1px; }

        .rp-export {
            height: 34px; padding: 0 14px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent);
            border-radius: 999px; background: var(--sb-surface); color: var(--sb-on-surface);
            font: inherit; font-size: .8rem; font-weight: 600; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .rp-export:hover { background: var(--sb-surface-container-high); }
        .rp-export .material-symbols-outlined { font-size: 17px; }

        .saldo { font-weight: 700; font-variant-numeric: tabular-nums; }
        .saldo.pos { color: var(--sb-primary); }
        .saldo.neg { color: var(--sb-error, #e34948); }
        .saldo.cero { color: var(--sb-on-surface-variant); font-weight: 500; }

        .zero { color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent); }

        .rp-empty {
            background: var(--sb-surface); border-radius: 16px; padding: 34px 16px;
            text-align: center; color: var(--sb-on-surface-variant); font-size: 13.5px;
        }

        @media (max-width: 1100px) { .rp-sum { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 760px) {
            .rp-sum { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .rp-bar { flex-direction: column; align-items: stretch; }
            .rp-bar-group, .rp-bar-group--grow { flex: 1 1 auto; }
            .rp-go { justify-content: center; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tk-dashboard.css?v=' . filemtime(FCPATH . 'css/index/components/tk-dashboard.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'horas']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <div class="tk-title">
                <div>
                    <h1>Horas por persona</h1>
                    <p>Horas reales frente a las programadas en el horario · <?= esc(date('d/m/Y', strtotime($fecha_inicio))) ?> → <?= esc(date('d/m/Y', strtotime($fecha_fin))) ?></p>
                </div>
                <div class="tk-title-actions">
                    <button type="submit" form="csv-form" class="dev-btn dev-btn-sm" title="Descargar el periodo en CSV">
                        <span class="material-symbols-outlined">download</span> Exportar CSV
                    </button>
                </div>
            </div>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <div class="rp-wrap tk-cfg">

            <?php
                $total = $horasTotal ?? [
                    'personas' => 0, 'dias_laborables' => 0, 'dias_trabajados' => 0,
                    'horas_reales' => 0, 'horas_programadas' => 0, 'saldo_min' => 0,
                    'tardanzas' => 0, 'faltas' => 0, 'sin_salida' => 0, 'estimadas' => 0,
                ];
                $filas = $horas ?? [];

                $hoy    = date('Y-m-d');
                $mesIni = date('Y-m-01');
                $mode   = ($fecha_inicio === $mesIni && $fecha_fin === $hoy) ? 'mes'
                         : (($fecha_inicio === $fecha_fin && $fecha_inicio === $hoy) ? 'hoy' : '');

                $fmt = static fn (int $min): string => \App\Libraries\Asistencia\HorasQuery::formatoMinutos($min);
                $dec = static fn (int $min): string => \App\Libraries\Asistencia\HorasQuery::horasDecimales($min);
            ?>

            <!-- Periodo -->
            <section class="tk-card tk-ovh">
                <div class="tk-card-head">
                    <div><h3>Periodo</h3><p>Elige las fechas y, si quieres, una persona.</p></div>
                </div>
                <form method="get" action="<?= base_url('admin/horas') ?>" id="filtro-form" class="tk-cfg-fest">
                    <label class="tk-cfg-field tk-cfg-field--date">
                        <span>Desde</span>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio) ?>">
                    </label>
                    <label class="tk-cfg-field tk-cfg-field--date">
                        <span>Hasta</span>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin) ?>">
                    </label>
                    <label class="tk-cfg-field tk-cfg-field--grow">
                        <span>Persona</span>
                        <input type="search" name="persona" value="<?= esc($busqueda ?? '') ?>" placeholder="Nombre o DNI" autocomplete="off">
                    </label>
                    <div class="tk-cfg-field">
                        <span>Atajos</span>
                        <div class="tk-hs-chips">
                            <button type="button" class="tk-hs-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Este mes</button>
                            <button type="button" class="tk-hs-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                        </div>
                    </div>
                    <button type="submit" class="dev-btn dev-btn-primary dev-btn-sm"><span class="material-symbols-outlined">calculate</span> Calcular</button>
                </form>
            </section>
            <form method="post" action="<?= site_url('admin/exportar/horas') ?>" id="csv-form" hidden>
                <?= csrf_field() ?>
                <input type="hidden" name="fecha_inicio" value="<?= esc($fecha_inicio) ?>">
                <input type="hidden" name="fecha_fin" value="<?= esc($fecha_fin) ?>">
                <input type="hidden" name="persona" value="<?= esc($busqueda ?? '') ?>">
            </form>

            <!-- KPIs -->
            <?php
                $sT    = (int) $total['saldo_min'];
                $pctEq = (int) $total['horas_programadas'] > 0 ? (int) round($total['horas_reales'] / $total['horas_programadas'] * 100) : 0;
            ?>
            <section class="tk-stats tk-stats-5">
                <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">timer</span><div><p class="tk-stat-lbl">Horas reales</p><p class="tk-stat-val"><?= $fmt((int) $total['horas_reales']) ?></p></div></div>
                <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">event_available</span><div><p class="tk-stat-lbl">Programadas</p><p class="tk-stat-val"><?= $fmt((int) $total['horas_programadas']) ?></p></div></div>
                <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">balance</span><div><p class="tk-stat-lbl">Saldo del equipo</p><p class="tk-stat-val <?= $sT > 0 ? 'tk-hs-pos' : ($sT < 0 ? 'tk-hs-neg' : '') ?>"><?= $fmt($sT) ?></p></div></div>
                <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">percent</span><div><p class="tk-stat-lbl">Cumplimiento</p><p class="tk-stat-val"><?= $pctEq ?>%</p></div></div>
                <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">warning</span><div><p class="tk-stat-lbl">Tardanzas · faltas</p><p class="tk-stat-val"><?= (int) $total['tardanzas'] ?> · <?= (int) $total['faltas'] ?></p></div></div>
            </section>

            <!-- Detalle por persona -->
            <section class="tk-card tk-ovh">
                <div class="tk-card-head">
                    <div><h3>Detalle por persona</h3><p><?= count($filas) ?> <?= count($filas) === 1 ? 'persona' : 'personas' ?> · días trabajados, horas y saldo del periodo.</p></div>
                </div>
            <?php if (empty($filas)): ?>
                <p class="tk-cfg-empty">No hay personas que coincidan con el filtro de este periodo.</p>
            <?php else: ?>
                <div class="tk-scroll">
                    <table class="tk-tbl tk-hs-tbl">
                        <thead>
                            <tr>
                                <th>Persona</th>
                                <th>Horario</th>
                                <th class="tk-r">Días</th>
                                <th class="tk-r">Reales</th>
                                <th class="tk-r">Programadas</th>
                                <th>Cumplimiento</th>
                                <th class="tk-r">Saldo</th>
                                <th class="tk-r">Tard.</th>
                                <th class="tk-r">Faltas</th>
                                <th class="tk-r">Sin salida</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filas as $i => $f):
                            $saldo  = (int) $f['saldo_min'];
                            $nombre = $f['name'] !== '' ? $f['name'] : 'Sin nombre';
                            $pctP   = (int) $f['horas_programadas'] > 0 ? (int) min(100, round($f['horas_reales'] / $f['horas_programadas'] * 100)) : 0;
                        ?>
                            <tr style="--i: <?= $i ?>">
                                <td>
                                    <div class="tk-cell-user">
                                        <span class="tk-cell-av"><?= strtoupper(mb_substr($nombre, 0, 1, 'UTF-8')) ?></span>
                                        <span>
                                            <span class="tk-cell-main"><?= esc($nombre) ?></span><br>
                                            <span class="tk-cell-sub tk-cell-mono"><?= $f['dni'] !== '' ? 'DNI ' . esc($f['dni']) : esc($f['role']) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="tk-cell-muted"><?= esc($f['horario_txt']) ?></td>
                                <td class="tk-r tk-cell-mono"><?= (int) $f['dias_trabajados'] ?> / <?= (int) $f['dias_laborables'] ?></td>
                                <td class="tk-r tk-cell-mono"><?= esc($dec((int) $f['horas_reales'])) ?> h<?= (int) $f['estimadas'] > 0 ? '<span class="tk-cell-sub" title="' . (int) $f['estimadas'] . ' jornadas estimadas por falta de salida"> · ' . (int) $f['estimadas'] . ' est.</span>' : '' ?></td>
                                <td class="tk-r tk-cell-mono tk-cell-muted"><?= esc($dec((int) $f['horas_programadas'])) ?> h</td>
                                <td>
                                    <div class="tk-hs-bar"><i style="width: <?= $pctP ?>%"></i></div>
                                    <span class="tk-cell-sub"><?= $pctP ?>%</span>
                                </td>
                                <td class="tk-r"><span class="tk-chip-soft <?= $saldo > 0 ? 'tk-chip-ok' : ($saldo < 0 ? 'tk-chip-bad' : 'tk-chip-neutral') ?>"><?= ($saldo > 0 ? '+' : '') . esc($dec($saldo)) ?> h</span></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['tardanzas'] > 0 ? '' : 'tk-cell-muted' ?>"><?= (int) $f['tardanzas'] ?></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['faltas'] > 0 ? '' : 'tk-cell-muted' ?>"><?= (int) $f['faltas'] ?></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['sin_salida'] > 0 ? '' : 'tk-cell-muted' ?>"><?= (int) $f['sin_salida'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="tk-hs-total">
                                <td colspan="2">Total · <?= count($filas) ?> personas</td>
                                <td class="tk-r tk-cell-mono"><?= (int) $total['dias_trabajados'] ?> / <?= (int) $total['dias_laborables'] ?></td>
                                <td class="tk-r tk-cell-mono"><?= esc($dec((int) $total['horas_reales'])) ?> h</td>
                                <td class="tk-r tk-cell-mono"><?= esc($dec((int) $total['horas_programadas'])) ?> h</td>
                                <td>
                                    <div class="tk-hs-bar"><i style="width: <?= min(100, $pctEq) ?>%"></i></div>
                                    <span class="tk-cell-sub"><?= $pctEq ?>%</span>
                                </td>
                                <td class="tk-r"><span class="tk-chip-soft <?= $sT > 0 ? 'tk-chip-ok' : ($sT < 0 ? 'tk-chip-bad' : 'tk-chip-neutral') ?>"><?= ($sT > 0 ? '+' : '') . esc($dec($sT)) ?> h</span></td>
                                <td class="tk-r tk-cell-mono"><?= (int) $total['tardanzas'] ?></td>
                                <td class="tk-r tk-cell-mono"><?= (int) $total['faltas'] ?></td>
                                <td class="tk-r tk-cell-mono"><?= (int) $total['sin_salida'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
                <p class="tk-card-foot tk-hs-note">
                    <b>Saldo</b> = horas reales − horas programadas del horario (días laborables del periodo menos festivos).
                    Positivo son horas extra; negativo, horas pendientes.
                    <?php if ((int) $total['estimadas'] > 0): ?>
                        Las jornadas sin salida se estiman hasta la hora de salida del horario («est.»).
                    <?php endif; ?>
                </p>
            </section>

            </div>
        </div>
    </main>

    <script>
    function rango(tipo) {
        const form = document.getElementById('filtro-form');
        const fi = document.getElementById('fecha_inicio');
        const ff = document.getElementById('fecha_fin');
        const d = new Date();
        const fmt = function (dt) {
            return dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0');
        };
        if (tipo === 'hoy') { fi.value = fmt(d); ff.value = fmt(d); }
        else { fi.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-01'; ff.value = fmt(d); }
        form.submit();
    }
    </script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>
