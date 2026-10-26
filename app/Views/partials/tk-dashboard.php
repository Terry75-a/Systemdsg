<?php
/* ══════════════════════════════════════════════════════════════
   DASHBOARD · réplica del dashboard de System-Trackin
   Datos: App\Libraries\Asistencia\DashboardStats ($tk)
   ══════════════════════════════════════════════════════════════ */
$cfg = array_merge([
    'base'       => 'admin',
    'title'      => 'Dashboard · Asistencia del equipo',
    'sub'        => 'Puntualidad de tu personal. A tiempo = marcó entrada dentro de la tolerancia; fuera de tiempo = tardanza o falta sin justificar.',
    'grupo'      => 'Áreas',
    'asist'      => 'admin/asistencias',
    'inc'        => 'admin/incidencias',
    'report'     => 'admin/reportes',
    'reportLbl'  => 'Descargar reporte',
    'bannerLbl'  => 'Ver asistencias de hoy →',
    'actions'    => '',
    'onboarding' => true,
], $tkCfg ?? []);
$tk     = $tk ?? [];
$MESES  = \App\Libraries\Asistencia\DashboardStats::MESES;
$ESTADOS = \App\Libraries\Asistencia\DashboardStats::ESTADOS;
$year   = (int) ($tk['year'] ?? date('Y'));
$target = (int) ($tk['target'] ?? 90);
$fmtPct = fn ($v) => $v === null ? '—' : $v . '%';
$num    = fn ($n) => number_format((int) $n, 0, '.', ',');
$ROLES  = ['emp' => 'Empleados', 'prac' => 'Practicantes'];

// URL del dashboard conservando filtros (anio · areas · meses)
$estado = [
    'anio'  => $year,
    'areas' => implode(',', $tk['areas'] ?? []),
    'meses' => isset($_GET['meses']) ? implode(',', $tk['mesesSel'] ?? []) : '',
];
$dashUrl = function (array $cambios) use ($estado, $cfg) {
    $q = array_filter(array_merge($estado, $cambios), fn ($v) => $v !== '' && $v !== null);
    return base_url($cfg['base']) . ($q ? '?' . http_build_query($q) : '');
};
$toggle = function (array $lista, $v) {
    return in_array($v, $lista, true) ? array_values(array_diff($lista, [$v])) : array_merge($lista, [$v]);
};

// Link a Asistencias con el mes (y estado) ya filtrados
$drill = function (int $m, ?string $estadoAtt = null) use ($year, $cfg) {
    $ini = sprintf('%04d-%02d-01', $year, $m);
    $q = ['fecha_inicio' => $ini, 'fecha_fin' => date('Y-m-t', strtotime($ini))];
    if ($estadoAtt) $q['estado'] = $estadoAtt;
    return base_url($cfg['asist']) . '?' . http_build_query($q);
};

// Fondo de celda: más intenso cuanto mayor el número (azul a tiempo / rojo fuera)
$heat = function (string $sv, int $v, int $max) {
    if (!$v) return '';
    $p = (int) round(12 + 48 * ($v / max(1, $max)));
    $var = $sv === 'on' ? '--tk-ontime' : '--tk-late';
    return "background: color-mix(in oklab, var($var) {$p}%, transparent);";
};

$splitBar = function (int $on, int $late, bool $labels = true, string $cls = '') {
    $t = $on + $late;
    $p = $t ? $on / $t * 100 : 0;
    ob_start(); ?>
    <div class="tk-split <?= $cls ?>">
        <div class="tk-split-track" role="img" aria-label="A tiempo <?= round($p) ?>%, fuera de tiempo <?= round(100 - $p) ?>%">
            <?php if ($on > 0): ?><div class="tk-split-on" style="--w: <?= round($p, 2) ?>%"></div><?php endif; ?>
            <?php if ($late > 0): ?><div class="tk-split-late"></div><?php endif; ?>
        </div>
        <?php if ($labels && $t > 0): ?>
            <div class="tk-split-lbl">
                <span><i class="tk-dot tk-dot-on"></i><?= round($p) ?>% a tiempo</span>
                <span><?= round(100 - $p) ?>% fuera<i class="tk-dot tk-dot-late"></i></span>
            </div>
        <?php endif; ?>
    </div>
    <?php return ob_get_clean();
};

$badge = function (string $sv, string $size = 'xs') {
    $on = $sv === 'on';
    return '<span class="tk-svc tk-svc-' . ($on ? 'on' : 'late') . ' tk-svc-' . $size . '">'
        . '<span class="material-symbols-outlined">' . ($on ? 'check_circle' : 'warning') . '</span>'
        . ($on ? 'A tiempo' : 'Fuera de tiempo') . '</span>';
};

$eqOk = ($tk['eqPct'] ?? null) !== null && $tk['eqPct'] >= $target;
?>
        <header class="dev-topbar">
            <div class="dev-topbar-row tk-dash">
                <div class="tk-title">
                    <div>
                        <h1><?= esc($cfg['title']) ?></h1>
                        <p><?= esc($cfg['sub']) ?></p>
                    </div>
                    <div class="tk-title-actions">
                        <?= $cfg['actions'] ?>
                        <a href="<?= base_url($cfg['report']) ?>" class="tk-chip" title="Resumen por persona, estados y exportación a CSV">
                            <span class="material-symbols-outlined">download</span> <?= esc($cfg['reportLbl']) ?>
                        </a>
                        <form method="get" action="<?= base_url($cfg['base']) ?>">
                            <?php if (!empty($estado['areas'])): ?><input type="hidden" name="areas" value="<?= esc($estado['areas']) ?>"><?php endif; ?>
                            <label class="tk-select">
                                <select name="anio" aria-label="Año" onchange="this.form.submit()">
                                    <?php foreach ($tk['years'] ?? [] as $y): ?>
                                        <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="material-symbols-outlined">expand_more</span>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content tk-dash">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($cfg['onboarding'] && (int) ($totalEmpleados ?? 0) === 0): ?>
                <!-- Cuenta nueva: qué hacer primero -->
                <section class="tk-card tk-anim-item">
                    <div class="tk-card-head"><h3>Primeros pasos</h3></div>
                    <div class="tk-steps">
                        <a href="<?= base_url('admin/personal') ?>"><b>1 · Crea tu personal</b><span>Registra empleados y practicantes con su DNI y horario.</span></a>
                        <a href="<?= base_url('admin/horarios') ?>"><b>2 · Define los horarios</b><span>Jornadas, días laborables y festivos.</span></a>
                        <a href="<?= base_url('admin/asistencias') ?>"><b>3 · Revisa asistencias</b><span>Aquí aparecerán las marcaciones y sus incidencias.</span></a>
                    </div>
                </section>
            <?php endif; ?>

            <!-- KPIs · celdas unidas por líneas de 1px -->
            <?php
            $kpis = [
                ['Marcaciones ' . $year, $tk['total'] ?? 0, 'fingerprint', true],
                ['Completas', $tk['completas'] ?? 0, 'task_alt', true],
                ['Sin salida', $tk['sinSalida'] ?? 0, 'pending_actions', true],
                ['Empleados a tiempo', $fmtPct($tk['empPct'] ?? null), 'schedule', false],
                ['Practicantes a tiempo', $fmtPct($tk['pracPct'] ?? null), 'schedule', false],
                ['Equipo a tiempo · meta ' . $target . '%', $fmtPct($tk['eqPct'] ?? null), $eqOk ? 'track_changes' : 'speed', false],
            ];
            ?>
            <section class="tk-stats">
                <?php foreach ($kpis as $i => [$label, $valor, $icono, $esNum]): ?>
                    <div class="tk-stat" style="animation-delay: <?= 100 + $i * 40 ?>ms">
                        <span class="material-symbols-outlined tk-stat-ico"><?= $icono ?></span>
                        <div>
                            <p class="tk-stat-lbl"><?= esc($label) ?></p>
                            <p class="tk-stat-val"><?php if ($esNum): ?><span data-count="<?= (int) $valor ?>"><?= $num($valor) ?></span><?php else: ?><?= esc($valor) ?><?php endif; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <?php if (($hoyFaltas ?? 0) > 0 || ($hoyTardanzas ?? 0) > 0 || ($incPendientes ?? 0) > 0): ?>
                <a href="<?= base_url($cfg['asist']) ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>" class="tk-banner">
                    <span><b><?= $num($hoyFaltas ?? 0) ?></b> sin marcar hoy · <b><?= $num($hoyTardanzas ?? 0) ?></b> tardanzas hoy · <b><?= $num($incPendientes ?? 0) ?></b> incidencias por resolver</span>
                    <span class="tk-banner-link"><?= esc($cfg['bannerLbl']) ?></span>
                </a>
            <?php endif; ?>

            <!-- Cierre de mes -->
            <?php
            $cl = $tk['closing'] ?? ['months' => [], 'oldest' => [], 'overdue' => 0, 'current' => date('Y-m')];
            $mesNombre = fn ($ym) => $MESES[(int) substr($ym, 5, 2) - 1] . ' ' . substr($ym, 0, 4);
            $STATE = [
                'closed'  => ['Cerrado', 'check_circle', 'tk-st-on'],
                'overdue' => ['Debe cerrarse', 'warning', 'tk-st-late'],
                'current' => ['En curso', 'schedule', 'tk-st-neutral'],
            ];
            ?>
            <section class="tk-card tk-anim-item">
                <div class="tk-card-head tk-card-head--top">
                    <div>
                        <h3>Cierre de mes · asistencia</h3>
                        <p>Cada mes anterior a <?= mb_strtolower($mesNombre($cl['current'])) ?> ya debería estar cerrado: sin marcaciones abiertas ni incidencias por resolver.</p>
                    </div>
                    <span class="tk-pill-state <?= $cl['overdue'] ? 'tk-st-late' : 'tk-st-on' ?>">
                        <?= $cl['overdue'] ? $cl['overdue'] . ($cl['overdue'] === 1 ? ' mes pendiente de cerrar' : ' meses pendientes de cerrar') : 'Meses anteriores cerrados' ?>
                    </span>
                </div>
                <div class="tk-closing">
                    <div class="tk-scroll">
                        <table class="tk-table">
                            <thead>
                                <tr>
                                    <th class="tk-pl">Mes</th>
                                    <th class="tk-r">Marcaciones</th>
                                    <th class="tk-r">Completas</th>
                                    <th class="tk-r">Por cerrar</th>
                                    <th class="tk-pr">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cl['months'] as $m): [$stLbl, $stIco, $stCls] = $STATE[$m['state']]; ?>
                                    <tr>
                                        <td class="tk-pl tk-strong"><?= $mesNombre($m['ym']) ?></td>
                                        <td class="tk-r"><?= $num($m['total']) ?></td>
                                        <td class="tk-r"><?= $num($m['completas']) ?></td>
                                        <td class="tk-r">
                                            <?php if ($m['open']): ?>
                                                <span class="tk-links">
                                                    <?php if ($m['sinSalida']): ?><a href="<?= base_url($cfg['asist']) ?>?<?= http_build_query(['fecha_inicio' => $m['ym'] . '-01', 'fecha_fin' => date('Y-m-t', strtotime($m['ym'] . '-01')), 'estado' => 'no_exit']) ?>">Sin salida <?= $m['sinSalida'] ?></a><?php endif; ?>
                                                    <?php if ($m['incid']): ?><a href="<?= base_url($cfg['inc']) ?>">Incid. <?= $m['incid'] ?></a><?php endif; ?>
                                                </span>
                                            <?php else: ?>—<?php endif; ?>
                                        </td>
                                        <td class="tk-pr">
                                            <span class="tk-pill-state tk-pill-xs <?= $stCls ?>"><span class="material-symbols-outlined"><?= $stIco ?></span> <?= $stLbl ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tk-oldest">
                        <p class="tk-cap">Lo más antiguo sin cerrar</p>
                        <?php if ($cl['oldest']): ?>
                            <?php foreach ($cl['oldest'] as $o): ?>
                                <a href="<?= base_url($o['url']) ?>" class="tk-oldest-row">
                                    <span class="tk-oldest-main"><b><?= esc($o['name']) ?></b> <span>· <?= esc($o['tipo']) ?></span></span>
                                    <span class="tk-oldest-age"><?= date('d/m/Y', strtotime($o['fecha'])) ?> · <?= $o['age'] ?> <?= $o['age'] === 1 ? 'día' : 'días' ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="tk-muted">Todo cerrado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Recorrido de una marcación -->
            <?php
            $ANTES = [['Alta en Personal', 'person_add'], ['Horario asignado', 'calendar_month'], ['Huella y rostro', 'face'], ['Código personal', 'qr_code_2'], ['Listo para marcar', 'how_to_reg']];
            $RUTAS = [
                'normal' => ['Día normal', [
                    ['1', 'Marca entrada', 'QR, huella o rostro', 'fingerprint'],
                    ['2', 'Validación', 'Ubicación y dispositivo', 'location_on'],
                    ['3', 'Estado', 'Presente o tardanza', 'schedule'],
                    ['4', 'Jornada', 'En curso', 'work'],
                    ['5', 'Marca salida', 'Fin de la jornada', 'logout'],
                    ['6', 'Día cerrado', 'Cierre automático', 'task_alt'],
                ]],
                'incidencia' => ['Con incidencia', [
                    ['1', 'Fuera de tiempo', 'Tardanza o falta', 'running_with_errors'],
                    ['2', 'Incidencia', 'Se registra el caso', 'report'],
                    ['3', 'Justificación', 'Envía evidencia', 'upload_file'],
                    ['4', 'Revisión', 'El admin revisa', 'manage_search'],
                    ['5', 'Resuelta', 'Justificada o rechazada', 'verified'],
                ]],
            ];
            ?>
            <section class="tk-card tk-anim-item">
                <details open class="tk-details">
                    <summary>Recorrido de una marcación <span>(clic para ocultar / mostrar)</span></summary>
                    <div class="tk-details-body">
                        <div class="tk-before">
                            <div class="tk-cap">Antes de marcar · alta del personal</div>
                            <ol>
                                <?php foreach ($ANTES as $i => [$lbl, $ico]): $ult = $i === count($ANTES) - 1; ?>
                                    <li style="animation-delay: <?= $i * 50 ?>ms">
                                        <?php if ($i > 0): ?><span class="tk-before-sep"></span><?php endif; ?>
                                        <span class="tk-before-pill <?= $ult ? 'is-last' : '' ?>"><span class="material-symbols-outlined"><?= $ico ?></span> <?= $lbl ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="tk-before-note">→ marca y empieza el seguimiento ↓</li>
                            </ol>
                        </div>
                        <div class="tk-route-tabs" role="tablist">
                            <?php $first = true; foreach ($RUTAS as $key => [$titulo]): ?>
                                <button type="button" class="tk-chip <?= $first ? 'active' : '' ?>" data-route="<?= $key ?>"><?= $titulo ?></button>
                            <?php $first = false; endforeach; ?>
                        </div>
                        <?php $first = true; foreach ($RUTAS as $key => [$titulo, $pasos]): ?>
                            <ol class="tk-route" data-route-panel="<?= $key ?>" <?= $first ? '' : 'hidden' ?>>
                                <?php foreach ($pasos as $i => [$n, $lbl, $lugar, $ico]): ?>
                                    <li class="tk-station" style="--i: <?= $i ?>">
                                        <?php if ($i < count($pasos) - 1): ?><span class="tk-station-line"><span></span></span><?php endif; ?>
                                        <span class="tk-station-dot">
                                            <span class="material-symbols-outlined"><?= $ico ?></span>
                                            <span class="tk-station-n"><?= $n ?></span>
                                        </span>
                                        <div class="tk-station-txt">
                                            <div class="tk-station-lbl"><?= $lbl ?></div>
                                            <div class="tk-station-place"><?= $lugar ?></div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php $first = false; endforeach; ?>
                        <p class="tk-foot-note">Cada marcación pasa por estos pasos en orden · las que salen fuera de tiempo pueden justificarse desde Incidencias.</p>
                    </div>
                </details>
            </section>

            <!-- Puntualidad por mes -->
            <section class="tk-card tk-anim-item tk-ovh">
                <div class="tk-card-head">
                    <h3>Puntualidad por mes</h3>
                    <div class="tk-chips">
                        <?= esc($cfg['grupo']) ?>:
                        <?php foreach ($tk['centers'] ?? [] as $c):
                            $sel = $toggle($tk['areaSel'], $c);
                            sort($sel);
                            $todas = count($sel) === count($tk['centers']);
                        ?>
                            <a class="tk-chip <?= in_array($c, $tk['areaSel'], true) ? 'active' : '' ?>"
                               href="<?= $sel ? $dashUrl(['areas' => $todas ? '' : implode(',', $sel)]) : '#' ?>"><?= esc($c) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="tk-scroll">
                    <table class="tk-table tk-table-hover tk-minw">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th class="tk-r">Empleados a tiempo</th>
                                <th class="tk-r">Empleados fuera</th>
                                <th class="tk-r">Practicantes a tiempo</th>
                                <th class="tk-r">Practicantes fuera</th>
                                <th class="tk-r">Equipo a tiempo</th>
                                <th class="tk-r">Equipo fuera</th>
                                <th class="tk-r">Meta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tk['monthStats'])): ?>
                                <tr><td colspan="8" class="tk-empty">Sin marcaciones en <?= $year ?> para estos filtros.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($tk['monthStats'] ?? [] as $row):
                                $ok = $row['eq'] !== null && $row['eq'] >= $target; ?>
                                <tr>
                                    <td class="tk-medium"><?= $MESES[$row['m'] - 1] ?></td>
                                    <?php foreach (['emp', 'prac'] as $g): $d = $row[$g]; ?>
                                        <td class="tk-r"><?php if ($d['n']): ?><a href="<?= $drill($row['m'], 'present') ?>"><i class="tk-dot tk-dot-on"></i><?= $fmtPct($d['p']) ?></a><?php else: ?>—<?php endif; ?></td>
                                        <td class="tk-r tk-muted"><?php if ($d['n']): ?><a href="<?= $drill($row['m'], 'late') ?>"><i class="tk-dot tk-dot-late"></i><?= $fmtPct(100 - ($d['p'] ?? 0)) ?></a><?php else: ?>—<?php endif; ?></td>
                                    <?php endforeach; ?>
                                    <td class="tk-r">
                                        <div class="tk-hsp">
                                            <span class="tk-hsp-val <?= $ok ? 'is-ok' : '' ?>"><?= $fmtPct($row['eq']) ?><?= $ok ? ' ✓' : '' ?></span>
                                            <?php if ($row['eq'] !== null): ?><?= $splitBar($row['eq'], 100 - $row['eq'], false, 'tk-w-full') ?><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="tk-r tk-muted"><?= $row['eq'] === null ? '—' : $fmtPct(100 - $row['eq']) ?></td>
                                    <td class="tk-r tk-muted"><?= $target ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="tk-total">
                                <td>Total general</td>
                                <td class="tk-r"><?= $fmtPct($tk['empPct'] ?? null) ?></td>
                                <td class="tk-r"><?= ($tk['empPct'] ?? null) === null ? '—' : $fmtPct(100 - $tk['empPct']) ?></td>
                                <td class="tk-r"><?= $fmtPct($tk['pracPct'] ?? null) ?></td>
                                <td class="tk-r"><?= ($tk['pracPct'] ?? null) === null ? '—' : $fmtPct(100 - $tk['pracPct']) ?></td>
                                <td class="tk-r"><?= $fmtPct($tk['eqPct'] ?? null) ?></td>
                                <td class="tk-r"><?= ($tk['eqPct'] ?? null) === null ? '—' : $fmtPct(100 - $tk['eqPct']) ?></td>
                                <td class="tk-r"><?= $target ?>%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="tk-card-foot">Equipo = promedio de Empleados y Practicantes. Azul con ✓ = cumple la meta. Haz clic en un % para ver esas marcaciones.</p>
            </section>

            <!-- Meses en las tablas de estado -->
            <?php if (!empty($tk['activeMonths'])): ?>
                <div class="tk-chips">
                    Meses en las tablas de estado:
                    <?php foreach ($tk['activeMonths'] as $m):
                        $sel = $toggle($tk['mesesSel'], $m);
                        sort($sel); ?>
                        <a class="tk-chip <?= in_array($m, $tk['mesesSel'], true) ? 'active' : '' ?>"
                           href="<?= $sel ? $dashUrl(['meses' => implode(',', $sel)]) : '#' ?>"><?= $MESES[$m - 1] ?></a>
                    <?php endforeach; ?>
                    <a class="tk-chip" href="<?= $dashUrl(['meses' => implode(',', $tk['activeMonths'])]) ?>">Todos</a>
                </div>
            <?php endif; ?>

            <!-- Tablas por estado (mapa de calor) -->
            <div class="tk-grid-2 tk-grid-2--wide">
                <?php foreach ($ROLES as $rol => $titulo): $pv = $tk['pivot'][$rol] ?? ['rows' => [], 'maxOn' => 1, 'maxLate' => 1]; ?>
                    <section class="tk-card tk-anim-table tk-ovh">
                        <div class="tk-card-head">
                            <h3><?= $titulo ?> · marcaciones por estado</h3>
                            <a href="<?= base_url($cfg['asist']) ?>" class="tk-link">Ver módulo</a>
                        </div>
                        <div class="tk-pivot-legend">
                            <?= $badge('on') ?><?= $badge('late') ?>
                            <span>Color más intenso = más marcaciones. Pasa el mouse por una celda para ver el detalle; haz clic para ver las marcaciones.</span>
                        </div>
                        <div class="tk-scroll">
                            <table class="tk-pivot tk-minw">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <?php foreach ($ESTADOS as $lbl): ?><th class="tk-r"><?= $lbl ?></th><?php endforeach; ?>
                                        <th class="tk-r">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$pv['rows']): ?>
                                        <tr><td colspan="<?= count($ESTADOS) + 2 ?>" class="tk-empty">Sin marcaciones para estos filtros.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($pv['rows'] as $i => $f): ?>
                                        <tr class="tk-pivot-main" style="animation-delay: <?= $i * 80 ?>ms">
                                            <td>
                                                <div><?= $MESES[$f['m'] - 1] ?></div>
                                                <?= $splitBar($f['on'], $f['late'], true, 'tk-w-40') ?>
                                            </td>
                                            <?php foreach ($f['st'] as $st => $v): ?>
                                                <td class="tk-r"><?php if ($v): ?><a href="<?= $drill($f['m'], $st) ?>"><span data-count="<?= $v ?>"><?= $num($v) ?></span></a><?php endif; ?></td>
                                            <?php endforeach; ?>
                                            <td class="tk-r"><a href="<?= $drill($f['m']) ?>"><span data-count="<?= $f['total'] ?>"><?= $num($f['total']) ?></span></a></td>
                                        </tr>
                                        <?php foreach (['on', 'late'] as $j => $sv): ?>
                                            <tr class="tk-pivot-sub" style="animation-delay: <?= $i * 80 + 50 * ($j + 1) ?>ms">
                                                <td><?= $badge($sv) ?></td>
                                                <?php foreach ($f['st'] as $st => $v):
                                                    $esDeEste = \App\Libraries\Asistencia\DashboardStats::servicio($st) === $sv; ?>
                                                    <td class="tk-r tk-heat-td">
                                                        <?php if ($esDeEste && $v > 0): ?>
                                                            <a href="<?= $drill($f['m'], $st) ?>"
                                                               title="<?= $MESES[$f['m'] - 1] ?> · <?= $ESTADOS[$st] ?> · <?= $sv === 'on' ? 'A tiempo' : 'Fuera de tiempo' ?>: <?= $v ?> (<?= round($v / max(1, $f['total']) * 100) ?>% del mes)"
                                                               class="tk-heat" style="<?= $heat($sv, $v, $sv === 'on' ? $pv['maxOn'] : $pv['maxLate']) ?>"><span data-count="<?= $v ?>"><?= $num($v) ?></span></a>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="tk-r">
                                                    <span class="tk-sum"><i class="tk-dot tk-dot-<?= $sv ?>"></i><span data-count="<?= $sv === 'on' ? $f['on'] : $f['late'] ?>"><?= $sv === 'on' ? $f['on'] : $f['late'] ?></span></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    <?php if ($pv['rows']): ?>
                                        <tr class="tk-pivot-total">
                                            <td>Total</td>
                                            <?php foreach (array_keys($ESTADOS) as $st):
                                                $t = array_sum(array_map(fn ($f) => $f['st'][$st], $pv['rows'])); ?>
                                                <td class="tk-r"><?= $t ? $num($t) : '' ?></td>
                                            <?php endforeach; ?>
                                            <td class="tk-r"><?= $num(array_sum(array_column($pv['rows'], 'total'))) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <!-- Motivos de incidencia -->
            <div class="tk-grid-2">
                <?php foreach ($ROLES as $rol => $titulo):
                    $lista = $tk['reasons'][$rol] ?? [];
                    $max = max(1, ...array_values($lista ?: [0]));
                    $sum = array_sum($lista); ?>
                    <section class="tk-card tk-anim-item tk-p5">
                        <h3 class="tk-h3"><?= $titulo ?> · motivos de incidencia</h3>
                        <p class="tk-sub">Incidencias en <?= $year ?>: <?= $num($sum) ?></p>
                        <?php if (!$lista): ?><p class="tk-muted tk-xs">Sin incidencias.</p><?php endif; ?>
                        <div class="tk-reasons">
                            <?php foreach ($lista as $tipo => $n): ?>
                                <a href="<?= base_url($cfg['inc']) ?>" class="tk-reason">
                                    <div class="tk-reason-top">
                                        <span class="tk-reason-name"><?= esc($tipo) ?></span>
                                        <span class="tk-muted"><?= $num($n) ?> · <?= round($n / max(1, $sum) * 100) ?>%</span>
                                    </div>
                                    <div class="tk-reason-track"><div style="--w: <?= round($n / $max * 100, 2) ?>%"></div></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
