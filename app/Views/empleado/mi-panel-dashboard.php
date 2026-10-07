<style>
    /* ── Hero marcar asistencia ── */
    .pra-status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11.5px; font-weight: 600; padding: 5px 12px; border-radius: 999px;
        background: var(--g-success-light); color: var(--g-success);
    }
    .pra-status-pill.off { background: var(--g-surface-variant); color: var(--g-text-secondary); }
    .pra-status-pill .material-symbols-outlined { font-size: 15px; }

    .pra-marcar {
        display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
        background: linear-gradient(135deg, var(--g-primary-light), var(--g-surface));
        border: 1px solid var(--g-border); border-radius: 18px;
        padding: 22px 26px; margin-bottom: 20px;
    }
    .pra-clock { flex-shrink: 0; }
    .pra-clock-time { font-size: 34px; font-weight: 700; font-family: var(--g-font-display); line-height: 1; }
    .pra-clock-sec { font-size: 14px; color: var(--g-text-secondary); margin-top: 4px; font-family: monospace; }
    .pra-marcar-info { flex: 1; min-width: 220px; }
    .pra-marcar-info h3 { margin: 0 0 4px; font-size: 16px; font-weight: 600; font-family: var(--g-font-display); }
    .pra-marcar-info p { margin: 0; font-size: 13px; color: var(--g-text-secondary); }
    .pra-marcar-times { display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap; }
    .pra-marcar-times > div {
        background: var(--g-surface); border: 1px solid var(--g-border);
        border-radius: 12px; padding: 8px 14px; font-size: 12.5px;
    }
    .pra-marcar-times span { display: block; color: var(--g-text-secondary); font-size: 11px; margin-bottom: 2px; }
    .pra-marcar-times strong { font-family: monospace; font-size: 15px; }

    /* ── Atajos stats ── */
    .stat-link { position: relative; display: block; cursor: pointer; }
    .stat-link:hover { transform: translateY(-3px); }
    .stat-go {
        position: absolute; top: 18px; right: 16px; font-size: 18px;
        color: var(--g-text-secondary); opacity: 0; transform: translateX(-6px);
        transition: opacity 200ms, transform 200ms;
    }
    .stat-link:hover .stat-go { opacity: 1; transform: translateX(0); color: var(--g-primary); }

    /* ── Mi información ── */
    .mp-irow {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 0; border-bottom: 1px solid var(--g-border);
    }
    .mp-irow:last-child { border-bottom: none; }
    .mp-iic {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        background: var(--g-surface-variant);
        display: flex; align-items: center; justify-content: center;
    }
    .mp-iic .material-symbols-outlined { font-size: 18px; color: var(--g-text-secondary); }
    .mp-ilab {
        font-size: 11px; color: var(--g-text-disabled);
        letter-spacing: 0.03em; text-transform: uppercase;
    }
    .mp-ival { font-size: 13.5px; font-weight: 500; margin-top: 1px; }
    .mp-mono { font-family: 'SF Mono', Consolas, monospace; font-size: 12.5px; }

    /* ── Últimas asistencias ── */
    .mp-att {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 0; border-bottom: 1px solid var(--g-border);
    }
    .mp-att:last-child { border-bottom: none; }
    .mp-att-main { flex: 1; min-width: 0; }
    .mp-att-date { font-size: 13.5px; font-weight: 600; }
    .mp-att-time { font-size: 12px; color: var(--g-text-secondary); margin-top: 1px; font-family: 'SF Mono', Consolas, monospace; }

    @media (max-width: 768px) {
        .pra-marcar { padding: 18px; gap: 16px; }
        .pra-clock-time { font-size: 30px; }
        .pra-marcar-cta { width: 100%; }
        .pra-marcar-cta .dev-btn { width: 100%; justify-content: center; }
    }
</style>

<?php
    $hoy = $hoyRegistro ?? null;
    $hEnt = $hoy['time_in']  ?? null;
    $hSal = $hoy['time_out'] ?? null;
    $horaEntrada = $miHorario['hora_entrada'] ?? '08:00';
    $horaSalida  = $miHorario['hora_salida']  ?? '17:00';
    $diasHorario = $miHorario['dias'] ?? 'Lun - Vie';
    $sinHorario  = empty($miHorario);
    $esPrac      = (session()->get('user_role') ?? '') === 'Practicante';
?>

<!-- Marcar asistencia hoy -->
<section class="pra-marcar">
    <div class="pra-clock">
        <div class="pra-clock-time" id="praClock">--:--</div>
        <div class="pra-clock-sec" id="praClockSec"></div>
    </div>
    <div class="pra-marcar-info">
        <h3>Marcar asistencia de hoy</h3>
        <p>
            <?php if (!$hEnt): ?>
                Aún no has marcado tu entrada.
            <?php elseif (!$hSal): ?>
                Entrada <strong><?= esc($hEnt) ?></strong> registrada · falta marcar la salida.
            <?php else: ?>
                Marcación completa de hoy.
            <?php endif; ?>
        </p>
        <div class="pra-marcar-times">
            <div><span>Entrada</span><strong><?= esc($hEnt ?: substr($horaEntrada, 0, 5)) ?></strong></div>
            <div><span>Salida</span><strong><?= esc($hSal ?: substr($horaSalida, 0, 5)) ?></strong></div>
            <div><span>Jornada</span><strong><?= esc($diasHorario) ?></strong></div>
        </div>
        <?php
            $now       = (int) date('Hi');
            $hIn       = (int) str_replace(':', '', $horaEntrada);
            $hOut      = (int) str_replace(':', '', $horaSalida);
            $enHorario = !$sinHorario && $now >= $hIn && $now <= $hOut;
        ?>
        <div style="margin-top:12px;">
            <span class="pra-status-pill <?= $enHorario ? '' : 'off' ?>">
                <span class="material-symbols-outlined filled"><?= $enHorario ? 'work' : 'bedtime' ?></span>
                <?= $enHorario ? 'En tu horario ' . ($esPrac ? 'de práctica' : 'laboral') : 'Fuera de tu horario' ?>
            </span>
        </div>
    </div>
    <div class="pra-marcar-cta" style="flex-shrink:0;">
        <?php if ($hEnt && $hSal): ?>
            <div class="dev-badge dev-badge-green" style="font-size:13px;padding:10px 16px;">
                <span class="material-symbols-outlined filled" style="font-size:18px;vertical-align:-4px;">check_circle</span> Completado hoy
            </div>
        <?php else: ?>
            <button type="button" class="dev-btn <?= $hEnt ? 'dev-btn-primary' : 'dev-btn' ?>" style="padding:12px 22px;font-size:15px;" onclick="openBioCheck()">
                <span class="material-symbols-outlined"><?= $hEnt ? 'logout' : 'login' ?></span>
                <?= $hEnt ? 'Registrar salida' : 'Registrar entrada' ?>
            </button>
        <?php endif; ?>
    </div>
</section>

<?= view('partials/biometric_panel', ['bioMark' => ['exit' => (bool) ($hEnt && !$hSal), 'hora_salida' => $sinHorario ? '' : substr($horaSalida, 0, 5)]]) ?>

<!-- Stats · atajos a cada página -->
<section class="dev-stats-grid">
    <a class="dev-stat-card stat-link" href="<?= base_url('mi-panel/asistencias') ?>">
        <span class="material-symbols-outlined stat-go">arrow_forward</span>
        <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
        <div class="dev-stat-label">Asistencias (mes)</div>
        <div class="dev-stat-value"><?= $misAsistencias ?? 0 ?></div>
    </a>
    <a class="dev-stat-card stat-link" href="<?= base_url('mi-panel/asistencias') ?>">
        <span class="material-symbols-outlined stat-go">arrow_forward</span>
        <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">schedule</span></div>
        <div class="dev-stat-label">Tardanzas (mes)</div>
        <div class="dev-stat-value"><?= $misTardanzas ?? 0 ?></div>
    </a>
    <a class="dev-stat-card stat-link" href="<?= base_url('mi-panel/asistencias') ?>">
        <span class="material-symbols-outlined stat-go">arrow_forward</span>
        <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">cancel</span></div>
        <div class="dev-stat-label">Faltas (mes)</div>
        <div class="dev-stat-value"><?= $misFaltas ?? 0 ?></div>
    </a>
    <a class="dev-stat-card stat-link" href="<?= base_url('mi-panel/incidencias') ?>">
        <span class="material-symbols-outlined stat-go">arrow_forward</span>
        <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">warning</span></div>
        <div class="dev-stat-label">Incidencias pendientes</div>
        <div class="dev-stat-value"><?= $incPendientes ?? 0 ?></div>
    </a>
</section>

<div class="dev-grid-2" style="margin-top:16px;">
    <!-- Mi información -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">person</span> Mi información</h2>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <div style="padding: 4px 20px 14px;">
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">badge</span></div>
                <div>
                    <div class="mp-ilab">Nombre</div>
                    <div class="mp-ival"><?= esc($miInfo['name'] ?? '') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">credit_card</span></div>
                <div>
                    <div class="mp-ilab">DNI</div>
                    <div class="mp-ival mp-mono"><?= esc($miInfo['dni'] ?? '') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">qr_code</span></div>
                <div>
                    <div class="mp-ilab">Código</div>
                    <div class="mp-ival mp-mono"><?= esc($miInfo['personal_code'] ?? '—') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">mail</span></div>
                <div>
                    <div class="mp-ilab">Correo</div>
                    <div class="mp-ival"><?= esc($miInfo['email'] ?? '') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">business</span></div>
                <div>
                    <div class="mp-ilab">Área / Cargo</div>
                    <div class="mp-ival"><?= esc($miInfo['area'] ?? '-') ?> / <?= esc($miInfo['cargo'] ?? '-') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">schedule</span></div>
                <div>
                    <div class="mp-ilab">Horario</div>
                    <div class="mp-ival mp-mono"><?= esc($horaEntrada) ?> - <?= esc($horaSalida) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas asistencias -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">history</span> Mis últimas asistencias</h2>
            <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todo</a>
        </div>
        <div style="padding: 4px 20px 14px;">
            <?php if (empty($misRegistros)): ?>
                <div class="dev-empty" style="padding:48px 16px;">
                    <span class="material-symbols-outlined">inbox</span>
                    <p>Sin registros aún</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($misRegistros, 0, 6) as $r):
                    $st     = $r['status'] ?? 'absent';
                    $badge  = match ($st) { 'present' => 'dev-badge-green', 'late' => 'dev-badge-amber', 'no_exit' => 'dev-badge-blue', default => 'dev-badge-red' };
                    $label  = match ($st) { 'present' => 'Puntual', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', default => ucfirst($st) };
                ?>
                    <div class="mp-att">
                        <div class="mp-iic"><span class="material-symbols-outlined">calendar_today</span></div>
                        <div class="mp-att-main">
                            <div class="mp-att-date"><?= esc($r['date'] ?? '') ?></div>
                            <div class="mp-att-time"><?= esc($r['time_in'] ?? '-') ?> — <?= esc($r['time_out'] ?? '-') ?></div>
                        </div>
                        <span class="dev-badge <?= $badge ?>"><?= $label ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
