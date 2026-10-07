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

    /* ── Jornada del mes ── */
    .mp-track {
        height: 10px; border-radius: 999px;
        background: var(--g-surface-variant); overflow: hidden;
        border: 1px solid var(--g-border);
    }
    .mp-track i {
        display: block; height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--g-primary), #5ac8fa);
        transition: width 500ms ease;
    }
    .mp-track.mp-track-amber i { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .mp-hbig { font-size: 26px; font-weight: 700; line-height: 1; font-family: var(--g-font-display); }
    .mp-meta {
        display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        font-size: 12px; color: var(--g-text-secondary); margin-top: 9px;
    }
    .mp-meta strong { color: var(--g-text); font-weight: 600; }

    /* ── Próximos festivos ── */
    .mp-fes {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 0; border-bottom: 1px solid var(--g-border);
    }
    .mp-fes:last-child { border-bottom: none; }
    .mp-fes-date {
        font-family: 'SF Mono', Consolas, monospace; font-size: 12.5px;
        font-weight: 600; width: 78px; flex-shrink: 0;
    }
    .mp-fes-day { font-size: 11.5px; color: var(--g-text-secondary); text-transform: capitalize; }
    .mp-fes-name { font-size: 13.5px; font-weight: 500; }

    /* ── Incidencias recientes ── */
    .mp-inc {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 0; border-bottom: 1px solid var(--g-border);
    }
    .mp-inc:last-child { border-bottom: none; }
    .mp-inc-main { flex: 1; min-width: 0; }
    .mp-inc-tipo { font-size: 13.5px; font-weight: 600; }
    .mp-inc-fecha {
        font-size: 12px; color: var(--g-text-secondary); margin-top: 1px;
        font-family: 'SF Mono', Consolas, monospace;
    }

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
                <div class="mp-iic"><span class="material-symbols-outlined">schedule</span></div>
                <div>
                    <div class="mp-ilab">Horario</div>
                    <div class="mp-ival mp-mono"><?= esc($horaEntrada) ?> - <?= esc($horaSalida) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mi contrato y puesto -->
    <?php
        $u         = $miInfo ?? [];
        $diasRestC = !empty($u['contract_end'])
            ? (int) round((strtotime((string) $u['contract_end']) - strtotime(date('Y-m-d'))) / 86400)
            : null;
        $estBadge  = match ($u['estado'] ?? 'Activo') {
            'Activo'   => 'dev-badge-green',
            'Inactivo' => 'dev-badge-red',
            default    => 'dev-badge-blue',
        };
        $durTxt = !empty($u['contract_duration'])
            ? $u['contract_duration'] . ' ' . mb_strtolower((string) ($u['contract_type'] ?? 'meses'))
            : '—';
    ?>
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">description</span> Mi contrato y puesto</h2>
            <span class="dev-badge <?= $estBadge ?>"><?= esc($u['estado'] ?? 'Activo') ?></span>
        </div>
        <div style="padding: 4px 20px 14px;">
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">gavel</span></div>
                <div>
                    <div class="mp-ilab">Tipo de contrato</div>
                    <div class="mp-ival"><?= esc($u['contract_type'] ?? '—') ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">hourglass_top</span></div>
                <div>
                    <div class="mp-ilab">Duración</div>
                    <div class="mp-ival mp-mono"><?= esc($durTxt) ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">event</span></div>
                <div>
                    <div class="mp-ilab">Inicio</div>
                    <div class="mp-ival mp-mono"><?= !empty($u['contract_start']) ? esc(date('d/m/Y', strtotime((string) $u['contract_start']))) : '—' ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">event_available</span></div>
                <div>
                    <div class="mp-ilab">Fin<?= $diasRestC !== null && $diasRestC >= 0 ? ' · faltan ' . $diasRestC . ' días' : '' ?></div>
                    <div class="mp-ival mp-mono"><?= !empty($u['contract_end']) ? esc(date('d/m/Y', strtotime((string) $u['contract_end']))) : 'Indefinido' ?></div>
                </div>
            </div>
            <div class="mp-irow">
                <div class="mp-iic"><span class="material-symbols-outlined">workspace_premium</span></div>
                <div>
                    <div class="mp-ilab">Área / Cargo</div>
                    <div class="mp-ival"><?= esc($u['area'] ?? '-') ?> / <?= esc($u['cargo'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jornada del mes -->
    <?php
        $pctJ   = $horasMesEsp > 0 ? (int) min(100, round($horasMes / $horasMesEsp * 100)) : 0;
        $mesesEs = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
                    7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];
        $mesLbl = $mesesEs[(int) date('n')] ?? '';
    ?>
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">timelapse</span> Jornada del mes</h2>
            <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver detalle</a>
        </div>
        <div style="padding: 14px 20px 16px;">
            <?php if ($horasMesEsp <= 0): ?>
                <div class="dev-empty" style="padding:34px 16px;">
                    <span class="material-symbols-outlined">event_busy</span>
                    <p>Sin horario asignado · contacta a RR.HH. para registrar tu jornada</p>
                </div>
            <?php else: ?>
                <div style="display:flex; align-items:center; gap:22px; flex-wrap:wrap;">
                    <div>
                        <div class="mp-hbig"><?= number_format($horasMes, 1) ?> h</div>
                        <div class="mp-ilab" style="margin-top:5px;">Trabajadas</div>
                    </div>
                    <div style="font-size:20px; color:var(--g-text-disabled); font-weight:400;">/</div>
                    <div>
                        <div class="mp-hbig" style="color:var(--g-text-secondary);"><?= number_format($horasMesEsp, 1) ?> h</div>
                        <div class="mp-ilab" style="margin-top:5px;">Programadas</div>
                    </div>
                </div>
                <div class="mp-track" style="margin-top:14px;"><i style="width: <?= $pctJ ?>%"></i></div>
                <div class="mp-meta">
                    <span><?= esc($mesLbl) ?> 2026 · <strong><?= $pctJ ?>%</strong> de la jornada</span>
                    <span>Marca <strong><?= $misAsistencias ?? 0 ?></strong> · tarda <strong><?= $misTardanzas ?? 0 ?></strong> · falta <strong><?= $misFaltas ?? 0 ?></strong></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Próximos festivos -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">celebration</span> Próximos festivos</h2>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <div style="padding: 4px 20px 14px;">
            <?php if (empty($festivosProximos)): ?>
                <div class="dev-empty" style="padding:34px 16px;">
                    <span class="material-symbols-outlined">event_busy</span>
                    <p>Sin festivos registrados por ahora</p>
                </div>
            <?php else: ?>
                <?php
                    $diasEs = ['Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
                               'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'];
                ?>
                <?php foreach ($festivosProximos as $fecha => $nombre): ?>
                    <div class="mp-fes">
                        <div class="mp-iic"><span class="material-symbols-outlined">event</span></div>
                        <div>
                            <div class="mp-fes-date"><?= esc(date('d/m/Y', strtotime((string) $fecha))) ?></div>
                            <div class="mp-fes-day"><?= esc($diasEs[date('l', strtotime((string) $fecha))] ?? '') ?></div>
                        </div>
                        <div style="flex:1;"></div>
                        <div class="mp-fes-name"><?= esc($nombre) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="dev-grid-2" style="margin-top:16px;">
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

    <!-- Incidencias recientes -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">report</span> Incidencias recientes</h2>
            <a href="<?= base_url('mi-panel/incidencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todas</a>
        </div>
        <div style="padding: 4px 20px 14px;">
            <?php if (empty($misIncidencias)): ?>
                <div class="dev-empty" style="padding:34px 16px;">
                    <span class="material-symbols-outlined">inbox</span>
                    <p>Sin incidencias registradas</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($misIncidencias, 0, 5) as $inc):
                    $e     = $inc['estado'] ?? 'Pendiente';
                    $badge = match ($e) {
                        'Pendiente'                 => 'dev-badge-amber',
                        'Revisión'                  => 'dev-badge-blue',
                        'Aprobada', 'Justificada'   => 'dev-badge-green',
                        'Rechazada', 'Desestimada'  => 'dev-badge-red',
                        default                     => 'dev-badge',
                    };
                ?>
                    <div class="mp-inc">
                        <div class="mp-iic"><span class="material-symbols-outlined">warning</span></div>
                        <div class="mp-inc-main">
                            <div class="mp-inc-tipo"><?= esc($inc['tipo'] ?? 'Otro') ?></div>
                            <div class="mp-inc-fecha"><?= esc(date('d/m/Y H:i', strtotime($inc['fecha'] ?? 'now'))) ?></div>
                        </div>
                        <span class="dev-badge <?= $badge ?>"><?= esc($e) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
