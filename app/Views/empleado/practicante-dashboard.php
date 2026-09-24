<style>
        /* ── Stats: accesos directos a cada pagina ── */
        .dev-stat-card {
            position: relative; display: block; cursor: pointer;
            transition: border-color 200ms, transform 200ms, box-shadow 200ms;
        }
        .stat-link:hover { transform: translateY(-3px); box-shadow: 0 10px 24px -10px rgba(0,0,0,0.16); }
        .stat-go {
            position: absolute; top: 18px; right: 16px; font-size: 18px;
            color: var(--g-text-secondary); opacity: 0; transform: translateX(-6px);
            transition: opacity 200ms, transform 200ms;
        }
        .stat-link:hover .stat-go { opacity: 1; transform: translateX(0); color: var(--g-primary); }

        /* ── Hero marcar asistencia ── */
        .pra-status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11.5px; font-weight: 600; padding: 5px 12px; border-radius: 999px;
            background: var(--g-success-light); color: var(--g-success);
        }
        .pra-status-pill.off { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .pra-status-pill .material-symbols-outlined { font-size: 15px; }
        .pra-marcar-times { gap: 10px; }
        .pra-marcar-times > div {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 12px; padding: 8px 14px;
        }
        .pra-marcar-times span { margin-bottom: 2px; }

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
        .pra-marcar-times div { font-size: 12.5px; }
        .pra-marcar-times span { display: block; color: var(--g-text-secondary); font-size: 11px; }
        .pra-marcar-times strong { font-family: monospace; font-size: 15px; }

        @media (max-width: 768px) {
            .pra-marcar { padding: 18px; gap: 16px; }
            .pra-clock-time { font-size: 30px; }
            .pra-marcar > div:last-child { width: 100%; }
            .pra-marcar > div:last-child .dev-btn,
            .pra-marcar > div:last-child .dev-badge {
                width: 100%; justify-content: center;
            }
        }
    </style>

<?php
    $hoy = $hoyRegistro ?? null;
    $hEnt  = $hoy['time_in']  ?? null;
    $hSal  = $hoy['time_out'] ?? null;
    $horaEntrada = $miHorario['hora_entrada'] ?? '08:00';
    $horaSalida  = $miHorario['hora_salida']  ?? '17:00';
    $diasHorario = $miHorario['dias'] ?? 'Lun - Vie';
    $sinHorario  = empty($miHorario);
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
                Marcacion completa de hoy.
            <?php endif; ?>
        </p>
        <div class="pra-marcar-times">
            <div><span>Entrada</span><strong><?= esc($hEnt ?: substr($horaEntrada, 0, 5)) ?></strong></div>
            <div><span>Salida</span><strong><?= esc($hSal ?: substr($horaSalida, 0, 5)) ?></strong></div>
            <div><span>Jornada</span><strong><?= esc($diasHorario) ?></strong></div>
        </div>
        <?php
            $now = (int) date('Hi');
            $hIn = (int) str_replace(':', '', $horaEntrada);
            $hOut = (int) str_replace(':', '', $horaSalida);
            $enHorario = !$sinHorario && $now >= $hIn && $now <= $hOut;
        ?>
        <div style="margin-top:12px;">
            <span class="pra-status-pill <?= $enHorario ? '' : 'off' ?>">
                <span class="material-symbols-outlined filled"><?= $enHorario ? 'work' : 'bedtime' ?></span>
                <?= $enHorario ? 'En tu horario de practica' : 'Fuera de tu horario' ?>
            </span>
        </div>
    </div>
    <div style="flex-shrink:0;">
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

<!-- Stats · atajos a cada pagina -->
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