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
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'horas']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Horas por persona</h1>
            <p class="dev-topbar-subtitle">Horas reales frente a las programadas en el horario · <?= esc($fecha_inicio) ?> → <?= esc($fecha_fin) ?></p>
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

            <!-- Filtros -->
            <form method="get" action="<?= base_url('admin/horas') ?>" id="filtro-form" class="rp-bar">
                <div class="rp-bar-group">
                    <span class="rp-bar-cap">Desde</span>
                    <input type="date" class="rp-input" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio) ?>">
                </div>
                <div class="rp-bar-group">
                    <span class="rp-bar-cap">Hasta</span>
                    <input type="date" class="rp-input" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin) ?>">
                </div>
                <div class="rp-bar-group rp-bar-group--grow">
                    <span class="rp-bar-cap">Persona</span>
                    <input type="search" class="rp-input" name="persona" value="<?= esc($busqueda ?? '') ?>" placeholder="Nombre o DNI" autocomplete="off">
                </div>
                <div class="rp-bar-group">
                    <span class="rp-bar-cap">Periodo</span>
                    <div class="rp-chips">
                        <button type="button" class="rp-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Este mes</button>
                        <button type="button" class="rp-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                    </div>
                </div>
                <button type="submit" class="rp-go"><span class="material-symbols-outlined">search</span> Calcular</button>
            </form>

            <!-- KPIs -->
            <div class="rp-sum">
                <div class="rp-tile">
                    <div class="rp-tile-top"><span class="material-symbols-outlined">timer</span><span class="rp-tile-lbl">Horas reales</span></div>
                    <div class="rp-tile-num"><?= $fmt((int) $total['horas_reales']) ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top"><span class="material-symbols-outlined">event_available</span><span class="rp-tile-lbl">Programadas</span></div>
                    <div class="rp-tile-num"><?= $fmt((int) $total['horas_programadas']) ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top"><span class="material-symbols-outlined">balance</span><span class="rp-tile-lbl">Saldo equipo</span></div>
                    <div class="rp-tile-num <?= $total['saldo_min'] > 0 ? 'pos' : ($total['saldo_min'] < 0 ? 'neg' : '') ?>">
                        <?= $fmt((int) $total['saldo_min']) ?>
                    </div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top"><span class="material-symbols-outlined">schedule</span><span class="rp-tile-lbl">Tardanzas</span></div>
                    <div class="rp-tile-num"><?= (int) $total['tardanzas'] ?></div>
                </div>
                <div class="rp-tile">
                    <div class="rp-tile-top"><span class="material-symbols-outlined">cancel</span><span class="rp-tile-lbl">Faltas</span></div>
                    <div class="rp-tile-num"><?= (int) $total['faltas'] ?></div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="rp-sec-head rp-sec-head--split">
                <h2><span class="material-symbols-outlined">table_chart</span> Detalle por persona</h2>
                <form method="post" action="<?= site_url('admin/exportar/horas') ?>" id="csv-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="fecha_inicio" value="<?= esc($fecha_inicio) ?>">
                    <input type="hidden" name="fecha_fin" value="<?= esc($fecha_fin) ?>">
                    <input type="hidden" name="persona" value="<?= esc($busqueda ?? '') ?>">
                    <button type="submit" class="rp-export">
                        <span class="material-symbols-outlined">download</span> Exportar CSV
                    </button>
                </form>
            </div>

            <?php if (empty($filas)): ?>
                <div class="rp-empty">
                    No hay personas que coincidan con el filtro de este periodo.
                </div>
            <?php else: ?>
                <div class="tk-tbl-wrap" style="background: var(--sb-surface); border-radius: 16px; overflow: hidden;">
                    <table class="tk-tbl">
                        <thead>
                            <tr>
                                <th>Persona</th>
                                <th>Horario</th>
                                <th class="tk-r">Días<br><small style="font-weight:400;opacity:.6">lab. / trab.</small></th>
                                <th class="tk-r">Horas reales</th>
                                <th class="tk-r">Programadas</th>
                                <th class="tk-r">Saldo</th>
                                <th class="tk-r">Tard.</th>
                                <th class="tk-r">Faltas</th>
                                <th class="tk-r">Sin salida</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filas as $f):
                            $saldo = (int) $f['saldo_min'];
                            $cls   = $saldo > 0 ? 'pos' : ($saldo < 0 ? 'neg' : 'cero');
                            $nombre = $f['name'] !== '' ? $f['name'] : 'Sin nombre';
                        ?>
                            <tr>
                                <td>
                                    <div class="tk-cell-user">
                                        <span class="tk-cell-av"><?= strtoupper(mb_substr($nombre, 0, 1, 'UTF-8')) ?></span>
                                        <span>
                                            <span class="tk-cell-main"><?= esc($nombre) ?></span><br>
                                            <span class="tk-cell-sub tk-cell-mono"><?= $f['dni'] !== '' ? 'DNI ' . esc($f['dni']) : esc($f['role']) ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="tk-cell-mono" style="font-size:12px;"><?= esc($f['horario_txt']) ?></td>
                                <td class="tk-r tk-cell-mono"><?= (int) $f['dias_trabajados'] ?> / <?= (int) $f['dias_laborables'] ?></td>
                                <td class="tk-r tk-cell-mono"><?= esc($dec((int) $f['horas_reales'])) ?> h<?= (int) $f['estimadas'] > 0 ? ' <span class="zero" title="' . (int) $f['estimadas'] . ' jornadas estimadas por falta de salida">(' . (int) $f['estimadas'] . ' est.)</span>' : '' ?></td>
                                <td class="tk-r tk-cell-mono"><?= esc($dec((int) $f['horas_programadas'])) ?> h</td>
                                <td class="tk-r"><span class="saldo <?= $cls ?>"><?= ($saldo > 0 ? '+' : '') . esc($dec($saldo)) ?> h</span></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['tardanzas'] > 0 ? '' : 'zero' ?>"><?= (int) $f['tardanzas'] ?></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['faltas'] > 0 ? '' : 'zero' ?>"><?= (int) $f['faltas'] ?></td>
                                <td class="tk-r tk-cell-mono <?= (int) $f['sin_salida'] > 0 ? '' : 'zero' ?>"><?= (int) $f['sin_salida'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 2px solid color-mix(in srgb, var(--sb-outline-variant) 70%, transparent);">
                                <td colspan="2" style="font-weight:600;">Total · <?= count($filas) ?> personas</td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= (int) $total['dias_trabajados'] ?> / <?= (int) $total['dias_laborables'] ?></td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= esc($dec((int) $total['horas_reales'])) ?> h</td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= esc($dec((int) $total['horas_programadas'])) ?> h</td>
                                <td class="tk-r"><span class="saldo <?= $total['saldo_min'] > 0 ? 'pos' : ($total['saldo_min'] < 0 ? 'neg' : 'cero') ?>"><?= ($total['saldo_min'] > 0 ? '+' : '') . esc($dec((int) $total['saldo_min'])) ?> h</span></td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= (int) $total['tardanzas'] ?></td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= (int) $total['faltas'] ?></td>
                                <td class="tk-r tk-cell-mono" style="font-weight:600;"><?= (int) $total['sin_salida'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>

            <p class="rp-note">
                <span class="material-symbols-outlined">info</span>
                <span>
                    <b>Saldo</b> = horas reales − horas programadas del horario asignado (días laborables del periodo menos los festivos).
                    Un saldo <b>positivo</b> son horas extra; un <b>negativo</b>, horas pendientes.
                    <?php if ((int) $total['estimadas'] > 0): ?>
                        Las jornadas marcadas sin salida se estiman hasta la hora de salida del horario y se señalan con «est.».
                    <?php endif; ?>
                </span>
            </p>

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
