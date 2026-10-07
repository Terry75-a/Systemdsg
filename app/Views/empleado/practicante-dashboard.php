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

        /* ── Bloques del mundo practicante ── */
        .pr-irow {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 0; border-bottom: 1px solid var(--g-border);
        }
        .pr-irow:last-child { border-bottom: none; }
        .pr-iic {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            background: var(--g-surface-variant);
            display: flex; align-items: center; justify-content: center;
        }
        .pr-iic .material-symbols-outlined { font-size: 18px; color: var(--g-text-secondary); }
        .pr-ilab {
            font-size: 11px; color: var(--g-text-disabled);
            letter-spacing: 0.03em; text-transform: uppercase;
        }
        .pr-ival { font-size: 13.5px; font-weight: 500; margin-top: 1px; }
        .pr-mono { font-family: 'SF Mono', Consolas, monospace; font-size: 12.5px; }

        /* Barra de progreso */
        .pr-track {
            height: 10px; border-radius: 999px;
            background: var(--g-surface-variant); overflow: hidden;
            border: 1px solid var(--g-border);
        }
        .pr-track i {
            display: block; height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--g-primary), #5ac8fa);
            transition: width 500ms ease;
        }
        .pr-track.pr-track-blue i { background: linear-gradient(90deg, #3b82f6, #22d3ee); }
        .pr-pct {
            font-size: 30px; font-weight: 700; line-height: 1;
            font-family: var(--g-font-display);
        }
        .pr-meta {
            display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;
            font-size: 12px; color: var(--g-text-secondary); margin-top: 9px;
        }
        .pr-meta strong { color: var(--g-text); font-weight: 600; }
        .pr-hbig {
            font-size: 26px; font-weight: 700; line-height: 1;
            font-family: var(--g-font-display);
        }
        .pr-minis { display: flex; gap: 12px; flex-wrap: wrap; }
        .pr-mini {
            flex: 1; min-width: 130px;
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 12px; padding: 11px 14px;
        }
        .pr-mini .pr-ival { font-size: 15px; }

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
                Marcación completa de hoy.
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
        <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">schedule</span></div>
        <div class="dev-stat-label">Horas (semana)</div>
        <div class="dev-stat-value"><?= number_format($horasSemana ?? 0, 1) ?> h</div>
    </a>
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
    <a class="dev-stat-card stat-link" href="<?= base_url('mi-panel/incidencias') ?>">
        <span class="material-symbols-outlined stat-go">arrow_forward</span>
        <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">warning</span></div>
        <div class="dev-stat-label">Incidencias pendientes</div>
        <div class="dev-stat-value"><?= $incPendientes ?? 0 ?></div>
    </a>
</section>

<?php
    $miInfo   = $miInfo ?? [];
    $pct      = (int) ($pctPractica ?? 0);
    $iniP     = (string) ($inicioPractica ?? '');
    $finP     = (string) ($finPractica ?? '');
    $restaDia = $iniP && $finP ? (int) round((strtotime($finP) - strtotime(date('Y-m-d'))) / 86400) : 0;
    $diasTotales = $iniP && $finP ? max(1, (int) round((strtotime($finP) - strtotime($iniP)) / 86400)) : 0;
    $diasCumpl   = $iniP ? max(0, (int) round((strtotime(date('Y-m-d')) - strtotime($iniP)) / 86400)) : 0;
    $mesRegistros = (int) ($misAsistencias ?? 0) + (int) ($misTardanzas ?? 0) + (int) ($misFaltas ?? 0);
    $espH     = (float) ($horasSemanaEsp ?? 0);
    $heH      = (float) ($horasSemana ?? 0);
    $pctH     = $espH > 0 ? (int) min(100, round($heH / $espH * 100)) : 0;
    $iniSem   = date('d/m/Y', strtotime('monday this week'));
    $finSem   = date('d/m/Y', strtotime('sunday this week'));
?>

<div class="dev-grid-2" style="margin-top:16px;">
    <!-- Mi práctica -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">school</span> Mi práctica</h2>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <div style="padding: 4px 20px 14px;">
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">account_balance</span></div>
                <div>
                    <div class="pr-ilab">Institución</div>
                    <div class="pr-ival"><?= esc($miInfo['institucion'] ?? '—') ?></div>
                </div>
            </div>
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">auto_stories</span></div>
                <div>
                    <div class="pr-ilab">Semestre</div>
                    <div class="pr-ival"><?= esc($miInfo['semestre'] ?? '—') ?></div>
                </div>
            </div>
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">corporate_fare</span></div>
                <div>
                    <div class="pr-ilab">Empresa</div>
                    <div class="pr-ival"><?= esc($miInfo['empresa'] ?? '—') ?></div>
                </div>
            </div>
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">badge</span></div>
                <div>
                    <div class="pr-ilab">Supervisor</div>
                    <div class="pr-ival pr-mono"><?= esc($miInfo['admin_code'] ?? '—') ?></div>
                </div>
            </div>
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">qr_code</span></div>
                <div>
                    <div class="pr-ilab">Código</div>
                    <div class="pr-ival pr-mono"><?= esc($miInfo['personal_code'] ?? '—') ?></div>
                </div>
            </div>
            <div class="pr-irow">
                <div class="pr-iic"><span class="material-symbols-outlined">event_available</span></div>
                <div>
                    <div class="pr-ilab">Alta en el sistema</div>
                    <div class="pr-ival pr-mono"><?= $iniP ? esc(date('d/m/Y', strtotime($iniP))) : '—' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso de la práctica -->
    <div class="dev-card" style="display:flex; flex-direction:column;">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">timeline</span> Progreso de la práctica</h2>
            <span class="dev-badge <?= $pct >= 100 ? 'dev-badge-green' : 'dev-badge-blue' ?>"><?= $pct ?>%</span>
        </div>
        <div style="padding: 14px 20px 16px; flex:1; display:flex; flex-direction:column; justify-content:space-between; gap:16px;">
            <div>
                <div style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">
                    <div class="pr-pct"><?= $pct ?>%</div>
                    <div style="font-size:13px; color:var(--g-text-secondary); padding-bottom:3px;">
                        <?= $restaDia > 0 ? 'Faltan <strong>' . $restaDia . ' días</strong>' : 'Práctica finalizada' ?>
                    </div>
                </div>
                <div class="pr-track" style="margin-top:14px;"><i style="width: <?= $pct ?>%"></i></div>
                <div class="pr-meta">
                    <span>Inicio <strong><?= $iniP ? esc(date('d/m/Y', strtotime($iniP))) : '—' ?></strong></span>
                    <span>Fin estimado <strong><?= $finP ? esc(date('d/m/Y', strtotime($finP))) : '—' ?></strong></span>
                </div>
                <div class="pr-meta" style="margin-top:4px;">
                    <span>Jornada <strong><?= esc($diasHorario) ?> · <?= esc(substr($horaEntrada, 0, 5)) ?> - <?= esc(substr($horaSalida, 0, 5)) ?></strong></span>
                    <span><?= ($diasHorarioSem ?? 0) > 0 ? $diasHorarioSem . ' días/semana' : 'Sin horario' ?></span>
                </div>
            </div>
            <div class="pr-minis">
                <div class="pr-mini">
                    <div class="pr-ilab">Días de práctica</div>
                    <div class="pr-ival pr-mono"><?= $diasCumpl ?> / <?= $diasTotales ?></div>
                </div>
                <div class="pr-mini">
                    <div class="pr-ilab">Marcas (mes)</div>
                    <div class="pr-ival pr-mono"><?= $mesRegistros ?> en total</div>
                </div>
                <div class="pr-mini">
                    <div class="pr-ilab">Horas (semana)</div>
                    <div class="pr-ival pr-mono"><?= number_format($heH, 1) ?> h</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen semanal de horas -->
<div class="dev-card" style="margin-top:16px;">
    <div class="dev-card-head">
        <h2><span class="material-symbols-outlined">hourglass_bottom</span> Resumen semanal de horas</h2>
        <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver asistencias</a>
    </div>
    <div style="padding: 14px 20px 16px;">
        <?php if ($espH <= 0): ?>
            <div class="dev-empty" style="padding:26px 16px;">
                <span class="material-symbols-outlined">event_busy</span>
                <p>Sin horario asignado · pide a tu supervisor que registre tu horario de práctica</p>
            </div>
        <?php else: ?>
            <div style="display:flex; align-items:center; gap:22px; flex-wrap:wrap;">
                <div>
                    <div class="pr-hbig"><?= number_format($heH, 1) ?> h</div>
                    <div class="pr-ilab" style="margin-top:5px;">Cumplidas</div>
                </div>
                <div style="font-size:20px; color:var(--g-text-disabled); font-weight:400;">/</div>
                <div>
                    <div class="pr-hbig" style="color:var(--g-text-secondary);"><?= number_format($espH, 1) ?> h</div>
                    <div class="pr-ilab" style="margin-top:5px;">Programadas</div>
                </div>
                <div style="flex:1; min-width:200px;">
                    <div class="pr-track pr-track-blue"><i style="width: <?= $pctH ?>%"></i></div>
                    <div class="pr-meta">
                        <span>Semana del <strong><?= $iniSem ?></strong> al <strong><?= $finSem ?></strong></span>
                        <span><strong><?= $pctH ?>%</strong> de la semana</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
