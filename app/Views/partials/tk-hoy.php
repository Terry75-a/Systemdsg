<?php
/**
 * Resumen de hoy del admin — estructura del diseño de referencia (pin "celoxis"):
 *   fila 1 · Resumen (frase) · Alertas de hoy (puntos + barra de rango + 7 días) · Puntualidad del mes (2 barras)
 *   fila 2 · Personal de hoy (tabla de filas redondeadas con estados en píldora)
 *   fila 3 · Avance del día (medidor) · Por cerrar (lista con número)
 * Todo sale de los datos reales que ya recibe el dashboard: $personal, $attendance,
 * $hoyFaltas, $hoyTardanzas, $incPendientes y $tk['closing'].
 */
$hoy      = date('Y-m-d');
$mesAct   = date('Y-m');
$mesAnt   = date('Y-m', strtotime('first day of last month'));
$MESES_L  = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$mesTxt   = fn ($ym) => $MESES_L[(int) substr($ym, 5, 2) - 1];
$numH     = fn ($n) => number_format((int) $n, 0, '.', ',');
$hm       = fn ($t) => $t ? substr((string) $t, 0, 5) : null;

// Personal activo que marca (empleados y practicantes)
$equipo = array_values(array_filter($personal ?? [], fn ($u) =>
    in_array($u['role'] ?? '', ['Empleado', 'Practicante'], true) && ($u['estado'] ?? 'Activo') === 'Activo'));
$activos = count($equipo);

// Marcas de hoy por persona y totales por día / mes
$deHoy = [];
$dias = [];
$mes = [$mesAct => ['on' => 0, 'fuera' => 0], $mesAnt => ['on' => 0, 'fuera' => 0]];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $dias[$d] = ['on' => 0, 'late' => 0, 'abs' => 0];
}
foreach ($attendance ?? [] as $r) {
    $d  = (string) ($r['date'] ?? '');
    $st = (string) ($r['status'] ?? '');
    if ($d === $hoy) $deHoy[(int) $r['user_id']] = $r;
    if (isset($dias[$d])) {
        if ($st === 'present') $dias[$d]['on']++;
        elseif ($st === 'late') $dias[$d]['late']++;
        elseif ($st === 'absent') $dias[$d]['abs']++;
    }
    $ym = substr($d, 0, 7);
    if (isset($mes[$ym])) {
        if ($st === 'present') $mes[$ym]['on']++;
        elseif (in_array($st, ['late', 'absent'], true)) $mes[$ym]['fuera']++;
    }
}
$puntuales = count(array_filter($deHoy, fn ($r) => ($r['status'] ?? '') === 'present'));
$tardes    = count(array_filter($deHoy, fn ($r) => ($r['status'] ?? '') === 'late'));
$enTurno   = count(array_filter($deHoy, fn ($r) => !empty($r['time_in']) && empty($r['time_out'])));
$sinMarcar = (int) ($hoyFaltas ?? 0);
$marcaron  = $puntuales + $tardes;
$avance    = $activos ? (int) round($marcaron * 100 / $activos) : 0;
$puntHoy   = $marcaron ? (int) round($puntuales * 100 / $marcaron) : null;

$pct = fn ($a) => ($a['on'] + $a['fuera']) ? (int) round($a['on'] * 100 / ($a['on'] + $a['fuera'])) : null;
$pMes = $pct($mes[$mesAct]);
$pAnt = $pct($mes[$mesAnt]);
$mesTot = $mes[$mesAct]['on'] + $mes[$mesAct]['fuera'];

// Frase del resumen (lo que el admin necesita saber primero)
if ($pMes === null) {
    $frase = 'Aún no hay marcaciones en ' . $mesTxt($mesAct) . '. Cuando tu personal marque, aquí verás cómo va su puntualidad.';
} elseif ($pAnt === null) {
    $frase = 'La puntualidad de ' . $mesTxt($mesAct) . ' va en <b>' . $pMes . '%</b> sobre ' . $numH($mesTot) . ' marcaciones.';
} else {
    $dif = $pMes - $pAnt;
    $frase = 'La puntualidad de ' . $mesTxt($mesAct) . ' va en <b>' . $pMes . '%</b>, '
        . ($dif === 0 ? 'igual que en ' . $mesTxt($mesAnt) : ($dif > 0 ? '<b>' . $dif . ' pts más</b>' : '<b>' . abs($dif) . ' pts menos</b>') . ' que en ' . $mesTxt($mesAnt)) . '.';
}

$cl = $tk['closing'] ?? ['oldest' => []];
$urlHoy = base_url($cfg['asist']) . '?fecha_inicio=' . $hoy . '&fecha_fin=' . $hoy;

// Medidor: arco de 270° (r = 80)
$R = 80; $C = 2 * M_PI * $R; $L = $C * 0.75;
$segOn   = $activos ? $L * $puntuales / $activos : 0;
$segLate = $activos ? $L * $tardes / $activos : 0;

$DIA = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
?>
<section class="hy" aria-label="Resumen de hoy">

    <!-- Fila 1 -->
    <div class="hy-row hy-row-3">
        <a class="hy-card hy-insight" href="<?= base_url($cfg['report']) ?>">
            <span class="hy-head"><span>Resumen</span><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></span>
            <p class="hy-insight-txt"><?= $frase ?></p>
            <span class="hy-insight-foot">
                <span class="hy-mini"><b><?= $numH($activos) ?></b> en el equipo</span>
                <span class="hy-mini"><b><?= $numH($incPendientes ?? 0) ?></b> incidencias por revisar</span>
            </span>
        </a>

        <a class="hy-card hy-alerts" href="<?= $urlHoy ?>">
            <span class="hy-head"><span>Alertas de hoy</span><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></span>
            <div class="hy-alert-nums">
                <div><span class="hy-n"><i class="hy-dot is-bad"></i><?= $numH($sinMarcar) ?></span><span class="hy-l">Sin marcar</span></div>
                <div><span class="hy-n"><i class="hy-dot is-warn"></i><?= $numH($tardes) ?></span><span class="hy-l">Tardanzas</span></div>
                <div><span class="hy-n"><i class="hy-dot is-ok"></i><?= $numH($enTurno) ?></span><span class="hy-l">En turno</span></div>
            </div>
            <div class="hy-range" role="img" aria-label="Puntualidad de hoy: <?= $puntHoy === null ? 'sin marcas' : $puntHoy . '%' ?>">
                <div class="hy-range-lbl"><span>Crítico</span><span>Aceptable</span></div>
                <div class="hy-range-bar"><?php if ($puntHoy !== null): ?><i style="left: <?= $puntHoy ?>%"></i><?php endif; ?></div>
            </div>
            <ol class="hy-week" aria-label="Últimos 7 días">
                <?php foreach ($dias as $d => $c):
                    $cls = $c['abs'] ? 'is-bad' : ($c['late'] ? 'is-warn' : ($c['on'] ? 'is-ok' : 'is-none'));
                    $tit = $c['on'] + $c['late'] + $c['abs'] ? "{$c['on']} a tiempo · {$c['late']} tardanzas · {$c['abs']} faltas" : 'Sin marcas'; ?>
                    <li class="<?= $d === $hoy ? 'is-today' : '' ?>" title="<?= date('d/m', strtotime($d)) ?>: <?= $tit ?>">
                        <i class="hy-dot <?= $cls ?>"></i><span><?= $DIA[(int) date('w', strtotime($d))] ?> <?= date('j', strtotime($d)) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </a>

        <a class="hy-card hy-month" href="<?= base_url($cfg['report']) ?>">
            <span class="hy-head"><span>Puntualidad de <?= $mesTxt($mesAct) ?></span><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></span>
            <?php $tOn = $mes[$mesAct]['on']; $tF = $mes[$mesAct]['fuera']; $pOn = $mesTot ? round($tOn * 100 / $mesTot) : 0; ?>
            <div class="hy-bar-row">
                <div class="hy-bar-top"><b><?= $numH($tOn) ?></b><span>(A tiempo / <?= $pOn ?>%)</span></div>
                <div class="hy-bar"><i class="is-ok" style="width: <?= $pOn ?>%"></i></div>
            </div>
            <div class="hy-bar-row">
                <div class="hy-bar-top"><b><?= $numH($tF) ?></b><span>(Fuera de tiempo / <?= $mesTot ? 100 - $pOn : 0 ?>%)</span></div>
                <div class="hy-bar"><i class="is-warn" style="width: <?= $mesTot ? 100 - $pOn : 0 ?>%"></i></div>
            </div>
        </a>
    </div>

    <!-- Fila 2 · Personal de hoy -->
    <div class="hy-block">
        <div class="hy-block-head">
            <h2>Personal de hoy</h2>
            <a class="hy-icon-link" href="<?= $urlHoy ?>" aria-label="Ver asistencias de hoy"><span class="material-symbols-outlined" aria-hidden="true">open_in_new</span></a>
        </div>
        <?php if (!$equipo): ?>
            <div class="hy-empty">Todavía no tienes personal. <a href="<?= base_url('admin/personal') ?>">Crea tu primer empleado</a>.</div>
        <?php else: ?>
        <div class="hy-table-wrap">
            <table class="hy-table">
                <thead>
                    <tr><th>Persona</th><th>Entrada</th><th>Salida</th><th>Hora</th><th>Área</th><th><span class="sr-only">Abrir</span></th></tr>
                </thead>
                <tbody>
                <?php foreach (array_slice($equipo, 0, 8) as $u):
                    $r = $deHoy[(int) $u['id']] ?? null;
                    $st = $r['status'] ?? null;
                    [$eLbl, $eCls] = match ($st) {
                        'present' => ['Puntual', 'is-ok'],
                        'late' => ['Tardanza', 'is-warn'],
                        'absent' => ['Falta', 'is-bad'],
                        'absent_justified' => ['Justificada', 'is-info'],
                        default => ['Sin marcar', $sinMarcar ? 'is-bad' : 'is-none'],
                    };
                    if (!$r || in_array($st, ['absent', 'absent_justified'], true)) { [$sLbl, $sCls] = ['—', 'is-none']; }
                    elseif (!empty($r['time_out'])) { [$sLbl, $sCls] = ['Completa', 'is-ok']; }
                    elseif (!empty($r['time_out1']) && empty($r['time_in2'])) { [$sLbl, $sCls] = ['En descanso', 'is-info']; }
                    else { [$sLbl, $sCls] = ['En turno', 'is-warn']; }
                    $hora = $hm($r['time_in'] ?? null);
                    $ini = mb_strtoupper(mb_substr(trim((string) ($u['name'] ?? '?')), 0, 1)); ?>
                    <tr>
                        <td><span class="hy-person"><span class="hy-av" aria-hidden="true"><?= esc($ini) ?></span><span><b><?= esc($u['name'] ?? '') ?></b><small><?= esc($u['role'] ?? '') ?></small></span></span></td>
                        <td><span class="hy-pill <?= $eCls ?>"><?= $eLbl ?></span></td>
                        <td><?php if ($sLbl === '—'): ?><span class="hy-faint">—</span><?php else: ?><span class="hy-pill <?= $sCls ?>"><?= $sLbl ?></span><?php endif; ?></td>
                        <td class="hy-num"><?= $hora ?? '<span class="hy-faint">--:--</span>' ?></td>
                        <td><?= esc(trim((string) ($u['area'] ?? '')) ?: '—') ?></td>
                        <td class="hy-go"><a class="hy-icon-link" href="<?= $urlHoy ?>" aria-label="Ver asistencia de hoy de <?= esc($u['name'] ?? '') ?>"><span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($equipo) > 8): ?><a class="hy-more" href="<?= $urlHoy ?>">Ver las <?= count($equipo) ?> personas →</a><?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Fila 3 · Avance del día + Por cerrar -->
    <div class="hy-block">
        <div class="hy-block-head"><h2>Avance del día</h2></div>
        <div class="hy-row hy-row-2">
            <div class="hy-card hy-gauge-card">
                <svg class="hy-gauge" viewBox="0 0 200 200" role="img" aria-label="<?= $avance ?>% del equipo ya marcó entrada">
                    <circle class="hy-g-track" cx="100" cy="100" r="<?= $R ?>" stroke-dasharray="<?= round($L, 2) ?> <?= round($C, 2) ?>" transform="rotate(135 100 100)"/>
                    <?php if ($segOn > 0): ?><circle class="hy-g-on" cx="100" cy="100" r="<?= $R ?>" stroke-dasharray="<?= round($segOn, 2) ?> <?= round($C, 2) ?>" transform="rotate(135 100 100)"/><?php endif; ?>
                    <?php if ($segLate > 0): ?><circle class="hy-g-late" cx="100" cy="100" r="<?= $R ?>" stroke-dasharray="<?= round($segLate, 2) ?> <?= round($C, 2) ?>" stroke-dashoffset="<?= -round($segOn, 2) ?>" transform="rotate(135 100 100)"/><?php endif; ?>
                </svg>
                <div class="hy-gauge-txt">
                    <span>Ya marcaron</span>
                    <strong><span data-count="<?= $avance ?>"><?= $avance ?></span>%<small>/100%</small></strong>
                </div>
                <ul class="hy-legend">
                    <li><i class="hy-dot is-ok"></i><b><?= $numH($puntuales) ?></b><span>Puntuales</span></li>
                    <li><i class="hy-dot is-warn"></i><b><?= $numH($tardes) ?></b><span>Tardanzas</span></li>
                    <li><i class="hy-dot is-bad"></i><b><?= $numH($sinMarcar) ?></b><span>Sin marcar</span></li>
                </ul>
            </div>
            <div class="hy-card hy-pend">
                <span class="hy-head"><span>Por cerrar<br><small>Lo más antiguo sin resolver</small></span>
                    <a class="hy-icon-link" href="<?= base_url($cfg['asist']) ?>" aria-label="Ir a asistencias"><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></a></span>
                <?php if (!empty($cl['oldest'])): ?>
                    <ul class="hy-pend-list">
                        <?php foreach (array_slice($cl['oldest'], 0, 4) as $o): ?>
                            <li><a href="<?= base_url($o['url']) ?>"><span><b><?= esc($o['name']) ?></b><small><?= esc($o['tipo']) ?> · <?= date('d/m', strtotime($o['fecha'])) ?></small></span><em><?= (int) $o['age'] ?><small><?= (int) $o['age'] === 1 ? 'día' : 'días' ?></small></em></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="hy-pend-empty"><span class="material-symbols-outlined" aria-hidden="true">task_alt</span>Todo cerrado. No hay salidas ni incidencias pendientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
