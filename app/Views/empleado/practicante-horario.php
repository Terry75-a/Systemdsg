<style>
        .hg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px; }
        .hg-cell {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 14px;
            padding: 16px; text-align: center;
        }
        .hg-cell .material-symbols-outlined { font-size: 26px; color: var(--g-primary); }
        .hg-cell b { display: block; font-size: 20px; font-family: var(--g-font-display); margin-top: 6px; }
        .hg-cell span { font-size: 11.5px; color: var(--g-text-secondary); }
        .hg-note { font-size: 12.5px; color: var(--g-text-secondary); display: flex; gap: 8px; align-items: center; background: var(--g-warning-light); padding: 10px 14px; border-radius: 12px; color: #b26a00; }
        .hg-week { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 2px; }
        .hg-day {
            width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600; background: var(--g-surface-variant); color: var(--g-text-secondary);
        }
        .hg-day.on { background: var(--g-primary); color: #fff; }
        .hg-day.today { box-shadow: 0 0 0 2px var(--g-surface), 0 0 0 4px var(--g-primary); }
        .hg-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; }
        .hg-row + .hg-row { border-top: 1px solid var(--g-border); }
        .hg-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--g-surface-variant); display: flex; align-items: center; justify-content: center; }
        .hg-icon .material-symbols-outlined { font-size: 19px; color: var(--g-text-secondary); }
    </style>

<div class="page-head">
    <a class="page-back" href="<?= base_url('mi-panel') ?>">
        <span class="material-symbols-outlined">arrow_back</span> Volver a Mi Panel
    </a>
    <div class="page-title">
        <span class="material-symbols-outlined">schedule</span>
        <h2>Mi Horario</h2>
    </div>
    <p class="page-sub">Tu jornada de práctica configurada por tu administrador.</p>
</div>

<?php
    $sinHorario = empty($miHorario);
    $horaEntrada = $miHorario['hora_entrada'] ?? '08:00';
    $horaSalida  = $miHorario['hora_salida']  ?? '17:00';
    $diasHorario = $miHorario['dias'] ?? '';
    $tolerancia  = (int) ($miHorario['tolerancia'] ?? 10);

    $diasOrder = ['lun' => 'L', 'mar' => 'M', 'mié' => 'X', 'mier' => 'X', 'jue' => 'J', 'vie' => 'V', 'sáb' => 'S', 'sab' => 'S', 'dom' => 'D'];
    $diasMap   = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sábado', 'D' => 'Domingo'];
    $activeDias = [];
    if (!$sinHorario && $diasHorario !== '') {
        $lower = mb_strtolower($diasHorario);
        foreach ($diasOrder as $k => $letter) {
            if (mb_strpos($lower, $k) !== false && !in_array($letter, $activeDias, true)) {
                $activeDias[] = $letter;
            }
        }
    }
    $semana = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    $hoyIdx = (int) date('N') - 1; // 1=lunes..7=domingo
?>

<?php if ($sinHorario): ?>
    <div class="dev-card">
        <div style="text-align:center; padding:60px 20px; color:var(--g-text-secondary); font-size:13.5px;">
            <span class="material-symbols-outlined" style="font-size:48px; display:block; margin:0 auto 12px; opacity:0.3;">schedule</span>
            Tu administrador aún no configura un horario para ti.
            <div style="margin-top:8px;">En cuanto lo active, verás aquí tus días y horas de práctica.</div>
        </div>
    </div>
<?php else: ?>
    <?php if (!empty($activeDias)): ?>
        <div class="hg-week" style="margin-bottom:16px;">
            <?php foreach ($semana as $i => $letra): ?>
                <div class="hg-day <?= in_array($letra, $activeDias, true) ? 'on' : '' ?> <?= $i === $hoyIdx ? 'today' : '' ?>" title="<?= esc($diasMap[$letra] ?? $letra) ?>"><?= $letra ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="hg-grid">
        <div class="hg-cell">
            <span class="material-symbols-outlined">login</span>
            <b class="hg-mono"><?= esc(substr($horaEntrada, 0, 5)) ?></b>
            <span>Entrada</span>
        </div>
        <div class="hg-cell">
            <span class="material-symbols-outlined">logout</span>
            <b><?= esc(substr($horaSalida, 0, 5)) ?></b>
            <span>Salida</span>
        </div>
        <div class="hg-cell">
            <span class="material-symbols-outlined">event_repeat</span>
            <b><?= $diasHorario !== '' ? esc($diasHorario) : '—' ?></b>
            <span>Días de práctica</span>
        </div>
        <div class="hg-cell">
            <span class="material-symbols-outlined">timer</span>
            <b><?= $tolerancia ?></b>
            <span>Tolerancia (min)</span>
        </div>
    </div>

    <div class="dev-card" style="margin-bottom:14px;">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">timer</span> ¿Cómo se calcula tu puntualidad?</h2>
        </div>
        <div style="padding: 6px 18px 12px;">
            <div class="hg-row">
                <div class="hg-icon"><span class="material-symbols-outlined">bolt</span></div>
                <div>
                    <div style="font-size:13.5px;">Si marcas antes de <strong><?= esc(substr($horaEntrada, 0, 5)) ?></strong> + <?= $tolerancia ?> min, tu asistencia se registra <span class="dev-badge dev-badge-green">Puntual</span>.</div>
                    <div style="font-size:12px;color:var(--g-text-secondary);margin-top:4px;">Después de ese rango se registra <span class="dev-badge dev-badge-amber">Tardanza</span> y se genera una incidencia automática.</div>
                </div>
            </div>
            <?php if (!empty($activeDias)): ?>
                <div class="hg-row">
                    <div class="hg-icon"><span class="material-symbols-outlined">local_fire_department</span></div>
                    <div>
                        <div style="font-size:13.5px;">Trabajas: <strong><?php foreach ($activeDias as $i => $d) { echo esc($diasMap[$d] ?? $d) . ($i < count($activeDias)-1 ? ', ' : ''); } ?></strong></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="hg-note">
        <span class="material-symbols-outlined" style="font-size:17px;">info</span>
        Tu administrador puede ajustar este horario en cualquier momento. Las modificaciones aplican desde el día siguiente.
    </div>
<?php endif; ?>