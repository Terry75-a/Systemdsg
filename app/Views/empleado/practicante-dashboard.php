<?php
    $miInfo      = $miInfo ?? [];
    $miHorario   = $miHorario ?? [];
    $horaEntrada = $miHorario['hora_entrada'] ?? null;
    $horaSalida  = $miHorario['hora_salida']  ?? null;
    $diasHorario = $miHorario['dias'] ?? null;

    $pct      = (int) ($pctPractica ?? 0);
    $iniP     = (string) ($inicioPractica ?? '');
    $finP     = (string) ($finPractica ?? '');
    $restaDia = $iniP && $finP ? (int) round((strtotime($finP) - strtotime(date('Y-m-d'))) / 86400) : 0;
    $diasTotales = $iniP && $finP ? max(1, (int) round((strtotime($finP) - strtotime($iniP)) / 86400)) : 0;
    $diasCumpl   = $iniP ? max(0, (int) round((strtotime(date('Y-m-d')) - strtotime($iniP)) / 86400)) : 0;
    $mesRegistros = (int) ($misAsistencias ?? 0) + (int) ($misTardanzas ?? 0) + (int) ($misFaltas ?? 0);
    $espH  = (float) ($horasSemanaEsp ?? 0);
    $heH   = (float) ($horasSemana ?? 0);
    $pctH  = $espH > 0 ? (int) min(100, round($heH / $espH * 100)) : 0;
    $saldo = (int) ($saldoMesMin ?? 0);
    $fmt   = static fn ($f) => $f ? date('d/m/Y', strtotime($f)) : '—';
?>

<?= view('partials/tk-marcar', [
    'hoyRegistro' => $hoyRegistro ?? null,
    'miHorario'   => $miHorario,
    'miInfo'      => $miInfo,
]) ?>

<!-- KPIs · cada uno lleva a su página -->
<section class="tk-stats tk-stats-5">
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">schedule</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Horas (semana)</p><p class="tk-stat-val"><?= number_format($heH, 1) ?> h</p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">check_circle</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Asistencias (mes)</p><p class="tk-stat-val"><?= (int) ($misAsistencias ?? 0) ?></p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">timer</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Tardanzas (mes)</p><p class="tk-stat-val"><?= (int) ($misTardanzas ?? 0) ?></p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/incidencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">warning</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Incidencias pendientes</p><p class="tk-stat-val"><?= (int) ($incPendientes ?? 0) ?></p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">balance</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div>
            <p class="tk-stat-lbl">Horas del mes</p>
            <p class="tk-stat-val"><?= esc($horasMesReales ?? '0.00') ?> h</p>
            <p class="tk-stat-note"><span class="<?= $saldo > 0 ? 'tk-pos' : ($saldo < 0 ? 'tk-neg' : '') ?>">Saldo <?= esc($saldoMesTxt ?? '—') ?></span> · prog. <?= esc($horasMesProg ?? '0.00') ?> h</p>
        </div>
    </a>
</section>

<div class="tk-grid-2">
    <!-- Mi práctica -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Mi práctica</h3><p>Tus datos como practicante.</p></div>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <div class="tk-kv">
            <div class="tk-kv-row"><span>Institución</span><span><?= esc($miInfo['institucion'] ?? '—') ?></span></div>
            <div class="tk-kv-row"><span>Semestre</span><span><?= esc($miInfo['semestre'] ?? '—') ?></span></div>
            <div class="tk-kv-row"><span>Empresa</span><span><?= esc(($miInfo['empresa'] ?? '') ?: '—') ?></span></div>
            <div class="tk-kv-row"><span>Supervisor</span><span><span class="tk-code"><?= esc($miInfo['admin_code'] ?? '—') ?></span></span></div>
            <div class="tk-kv-row"><span>Código</span><span><span class="tk-code"><?= esc($miInfo['personal_code'] ?? '—') ?></span></span></div>
            <div class="tk-kv-row"><span><?= !empty($inicioPracticaContrato) ? 'Inicio de práctica' : 'Alta en el sistema' ?></span><span class="tk-mono"><?= esc($fmt($iniP)) ?></span></div>
        </div>
    </section>

    <!-- Progreso de la práctica -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Progreso de la práctica</h3><p><?= !empty($finPracticaEstimado) ? 'Del inicio al fin estimado (sin fecha de fin en tu contrato).' : 'Del inicio al fin de tu contrato.' ?></p></div>
            <span class="tk-pill-state <?= $pct >= 100 ? 'tk-st-ok' : 'tk-st-on' ?>"><?= $pct ?>%</span>
        </div>
        <div class="tk-prog">
            <div class="tk-prog-big">
                <b><?= $pct ?>%</b>
                <span><?= $restaDia > 0 ? 'Faltan <strong>' . $restaDia . ' días</strong>' : 'Práctica finalizada' ?></span>
            </div>
            <div class="tk-bar"><i style="--w: <?= $pct ?>%"></i></div>
            <div class="tk-prog-meta">
                <span>Inicio <strong><?= esc($fmt($iniP)) ?></strong></span>
                <span><?= !empty($finPracticaEstimado) ? 'Fin estimado' : 'Fin' ?> <strong><?= esc($fmt($finP)) ?></strong></span>
            </div>
            <div class="tk-prog-meta">
                <span>Jornada <strong><?= esc($diasHorario ?: '—') ?><?= $horaEntrada ? ' · ' . esc(substr($horaEntrada, 0, 5)) . ' - ' . esc(substr((string) $horaSalida, 0, 5)) : '' ?></strong></span>
                <span><?= ($diasHorarioSem ?? 0) > 0 ? (int) $diasHorarioSem . ' días/semana' : 'Sin horario' ?></span>
            </div>
            <div class="tk-minis">
                <div><span>Días de práctica</span><strong><?= $diasCumpl ?> / <?= $diasTotales ?></strong></div>
                <div><span>Marcas (mes)</span><strong><?= $mesRegistros ?></strong></div>
                <div><span>Horas (semana)</span><strong><?= number_format($heH, 1) ?> h</strong></div>
            </div>
        </div>
    </section>
</div>

<!-- Resumen semanal de horas -->
<section class="tk-card tk-ovh tk-anim-item">
    <div class="tk-card-head">
        <div><h3>Resumen semanal de horas</h3><p>Semana del <?= date('d/m/Y', strtotime('monday this week')) ?> al <?= date('d/m/Y', strtotime('sunday this week')) ?>.</p></div>
        <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver asistencias</a>
    </div>
    <?php if ($espH <= 0): ?>
        <div class="tk-void">
            <span class="material-symbols-outlined">event_busy</span>
            Sin horario asignado · pide a tu supervisor que registre tu horario de práctica.
        </div>
    <?php else: ?>
        <div class="tk-hours">
            <div class="tk-hours-n"><b><?= number_format($heH, 1) ?> h</b><span>Cumplidas</span></div>
            <div class="tk-hours-sep">/</div>
            <div class="tk-hours-n is-muted"><b><?= number_format($espH, 1) ?> h</b><span>Programadas</span></div>
            <div class="tk-hours-bar">
                <div class="tk-bar tk-bar-blue"><i style="--w: <?= $pctH ?>%"></i></div>
                <div class="tk-prog-meta"><span>Avance de la semana</span><span><strong><?= $pctH ?>%</strong></span></div>
            </div>
        </div>
    <?php endif; ?>
</section>
