<?php
    $miInfo      = $miInfo ?? [];
    $miHorario   = $miHorario ?? [];
    $sinHorario  = empty($miHorario);
    $horaEntrada = $miHorario['hora_entrada'] ?? null;
    $horaSalida  = $miHorario['hora_salida']  ?? null;
    $u           = $miInfo;
    $saldo       = (int) ($saldoMesMin ?? 0);
    $fmt         = static fn ($f) => !empty($f) ? date('d/m/Y', strtotime((string) $f)) : '—';

    $diasRestC = !empty($u['contract_end'])
        ? (int) round((strtotime((string) $u['contract_end']) - strtotime(date('Y-m-d'))) / 86400)
        : null;
    $estado    = (string) ($u['estado'] ?? 'Activo');
    $durTxt    = !empty($u['contract_duration'])
        ? $u['contract_duration'] . ' ' . mb_strtolower((string) ($u['contract_type'] ?? 'meses'))
        : '—';

    $horasMes    = (float) ($horasMes ?? 0);
    $horasMesEsp = (float) ($horasMesEsp ?? 0);
    $pctJ    = $horasMesEsp > 0 ? (int) min(100, round($horasMes / $horasMesEsp * 100)) : 0;
    $mesesEs = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
                7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];
    $diasEs  = ['Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
                'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'];
?>

<?= view('partials/tk-marcar', [
    'hoyRegistro' => $hoyRegistro ?? null,
    'miHorario'   => $miHorario,
    'miInfo'      => $miInfo,
]) ?>

<!-- KPIs · cada uno lleva a su página -->
<section class="tk-stats tk-stats-5">
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">check_circle</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Asistencias (mes)</p><p class="tk-stat-val"><?= (int) ($misAsistencias ?? 0) ?></p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">timer</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Tardanzas (mes)</p><p class="tk-stat-val"><?= (int) ($misTardanzas ?? 0) ?></p></div>
    </a>
    <a class="tk-stat" href="<?= base_url('mi-panel/asistencias') ?>">
        <div class="tk-stat-top"><span class="material-symbols-outlined tk-stat-ico">event_busy</span><span class="material-symbols-outlined tk-stat-go">arrow_forward</span></div>
        <div><p class="tk-stat-lbl">Faltas (mes)</p><p class="tk-stat-val"><?= (int) ($misFaltas ?? 0) ?></p></div>
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
    <!-- Mi información -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Mi información</h3><p>Tus datos personales y de horario.</p></div>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <div class="tk-kv">
            <div class="tk-kv-row"><span>Nombre</span><span><?= esc($u['name'] ?? '') ?></span></div>
            <div class="tk-kv-row"><span>DNI</span><span class="tk-mono"><?= esc($u['dni'] ?? '—') ?></span></div>
            <div class="tk-kv-row"><span>Código</span><span><span class="tk-code"><?= esc($u['personal_code'] ?? '—') ?></span></span></div>
            <div class="tk-kv-row"><span>Correo</span><span><?= esc($u['email'] ?? '') ?></span></div>
            <div class="tk-kv-row"><span>Horario</span><span class="tk-mono"><?= $sinHorario ? 'Sin horario asignado' : esc(substr((string) $horaEntrada, 0, 5) . ' - ' . substr((string) $horaSalida, 0, 5)) ?></span></div>
        </div>
    </section>

    <!-- Mi contrato y puesto -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Mi contrato y puesto</h3><p>Condiciones registradas por RR.HH.</p></div>
            <span class="tk-pill-state <?= $estado === 'Activo' ? 'tk-st-ok' : ($estado === 'Inactivo' ? 'tk-st-late' : 'tk-st-on') ?>"><?= esc($estado) ?></span>
        </div>
        <div class="tk-kv">
            <div class="tk-kv-row"><span>Tipo de contrato</span><span><?= esc(($u['contract_type'] ?? '') ?: '—') ?></span></div>
            <div class="tk-kv-row"><span>Duración</span><span class="tk-mono"><?= esc($durTxt) ?></span></div>
            <div class="tk-kv-row"><span>Inicio</span><span class="tk-mono"><?= esc($fmt($u['contract_start'] ?? '')) ?></span></div>
            <div class="tk-kv-row"><span>Fin<?= $diasRestC !== null && $diasRestC >= 0 ? ' · faltan ' . $diasRestC . ' días' : '' ?></span><span class="tk-mono"><?= !empty($u['contract_end']) ? esc($fmt($u['contract_end'])) : 'Indefinido' ?></span></div>
            <div class="tk-kv-row"><span>Área / Cargo</span><span><?= esc(($u['area'] ?? '') ?: '—') ?> / <?= esc(($u['cargo'] ?? '') ?: '—') ?></span></div>
        </div>
    </section>

    <!-- Jornada del mes -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Jornada del mes</h3><p><?= esc(ucfirst($mesesEs[(int) date('n')] ?? '')) ?> <?= date('Y') ?> · horas trabajadas frente a las programadas.</p></div>
            <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver detalle</a>
        </div>
        <?php if ($horasMesEsp <= 0): ?>
            <div class="tk-void">
                <span class="material-symbols-outlined">event_busy</span>
                Sin horario asignado · contacta a RR.HH. para registrar tu jornada.
            </div>
        <?php else: ?>
            <div class="tk-prog">
                <div class="tk-hours" style="padding:0;">
                    <div class="tk-hours-n"><b><?= number_format($horasMes, 1) ?> h</b><span>Trabajadas</span></div>
                    <div class="tk-hours-sep">/</div>
                    <div class="tk-hours-n is-muted"><b><?= number_format($horasMesEsp, 1) ?> h</b><span>Programadas</span></div>
                </div>
                <div class="tk-bar"><i style="--w: <?= $pctJ ?>%"></i></div>
                <div class="tk-prog-meta">
                    <span><strong><?= $pctJ ?>%</strong> de la jornada</span>
                    <span>Marca <strong><?= (int) ($misAsistencias ?? 0) ?></strong> · tarda <strong><?= (int) ($misTardanzas ?? 0) ?></strong> · falta <strong><?= (int) ($misFaltas ?? 0) ?></strong></span>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Próximos festivos -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Próximos festivos</h3><p>Días no laborables de tu empresa.</p></div>
            <a href="<?= base_url('mi-panel/horario') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver horario</a>
        </div>
        <?php if (empty($festivosProximos)): ?>
            <div class="tk-void">
                <span class="material-symbols-outlined">event_available</span>
                Sin festivos registrados por ahora.
            </div>
        <?php else: ?>
            <div class="tk-scroll">
                <table class="tk-table tk-table-hover">
                    <thead><tr><th>Fecha</th><th>Día</th><th>Festivo</th></tr></thead>
                    <tbody>
                    <?php $i = 0; foreach ($festivosProximos as $fecha => $nombre): ?>
                        <tr style="animation-delay: <?= ($i++) * 50 ?>ms">
                            <td class="tk-medium tk-mono"><?= esc(date('d/m/Y', strtotime((string) $fecha))) ?></td>
                            <td class="tk-muted"><?= esc(ucfirst($diasEs[date('l', strtotime((string) $fecha))] ?? '')) ?></td>
                            <td><?= esc($nombre) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="tk-grid-2">
    <!-- Últimas asistencias -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Mis últimas asistencias</h3><p>Tus seis marcaciones más recientes.</p></div>
            <a href="<?= base_url('mi-panel/asistencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todo</a>
        </div>
        <?php if (empty($misRegistros)): ?>
            <div class="tk-void"><span class="material-symbols-outlined">inbox</span>Sin registros aún.</div>
        <?php else: ?>
            <div class="tk-scroll">
                <table class="tk-table tk-table-hover">
                    <thead><tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th class="tk-r">Estado</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($misRegistros, 0, 6) as $i => $r):
                        $st    = $r['status'] ?? 'absent';
                        $cls   = match ($st) { 'present' => 'tk-st-present', 'late' => 'tk-st-late', 'absent_justified', 'no_exit' => 'tk-st-justified', default => 'tk-st-absent' };
                        $label = match ($st) { 'present' => 'Puntual', 'late' => 'Tardanza', 'absent' => 'Falta', 'absent_justified' => 'Falta justificada', 'no_exit' => 'Sin salida', default => ucfirst($st) };
                    ?>
                        <tr style="animation-delay: <?= $i * 50 ?>ms">
                            <td class="tk-medium tk-mono"><?= esc($fmt($r['date'] ?? '')) ?></td>
                            <td class="tk-mono"><?= esc(!empty($r['time_in']) ? substr($r['time_in'], 0, 5) : '—') ?></td>
                            <td class="tk-mono"><?= esc(!empty($r['time_out']) ? substr($r['time_out'], 0, 5) : '—') ?></td>
                            <td class="tk-r"><span class="tk-st <?= $cls ?>"><?= $label ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <!-- Incidencias recientes -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Incidencias recientes</h3><p>Tardanzas, faltas y casos registrados.</p></div>
            <a href="<?= base_url('mi-panel/incidencias') ?>" class="dev-btn dev-btn-outline dev-btn-sm">Ver todas</a>
        </div>
        <?php if (empty($misIncidencias)): ?>
            <div class="tk-void"><span class="material-symbols-outlined">inbox</span>Sin incidencias registradas.</div>
        <?php else: ?>
            <div class="tk-scroll">
                <table class="tk-table tk-table-hover">
                    <thead><tr><th>Tipo</th><th>Fecha</th><th class="tk-r">Estado</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($misIncidencias, 0, 5) as $i => $inc):
                        $e   = (string) ($inc['estado'] ?? 'Pendiente');
                        $cls = match ($e) {
                            'Aprobada', 'Justificada'  => 'tk-st-ok2',
                            'Rechazada', 'Desestimada' => 'tk-st-bad',
                            'Pendiente'                => 'tk-st-late',
                            default                    => 'tk-st-wait',
                        };
                    ?>
                        <tr style="animation-delay: <?= $i * 50 ?>ms">
                            <td class="tk-medium"><?= esc($inc['tipo'] ?? 'Otro') ?></td>
                            <td class="tk-mono tk-muted"><?= esc(date('d/m/Y H:i', strtotime($inc['fecha'] ?? 'now'))) ?></td>
                            <td class="tk-r"><span class="tk-st <?= $cls ?>"><?= esc($e) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
