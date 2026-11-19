<?php
/**
 * Tarjeta "Marcar asistencia de hoy" (estilo Trackin) + biometría.
 * La usan los dashboards del Empleado y del Practicante.
 */
$hoy         = $hoyRegistro ?? null;
$hEnt        = $hoy['time_in']  ?? null;
$hSal        = $hoy['time_out'] ?? null;
$hOut1       = $hoy['time_out1'] ?? null;
$hIn2        = $hoy['time_in2'] ?? null;
// Qué marca toca ahora: entrada · salida_descanso · regreso · salida · completo
$marca       = \App\Libraries\Asistencia\Jornada::siguienteMarca($hoy, $miHorario ?? null, date('H:i:s'));
$doble       = \App\Libraries\Asistencia\Jornada::esDobleTurno($miHorario ?? null);
$bloquesH    = \App\Libraries\Asistencia\Jornada::bloques($miHorario ?? null);
// En doble turno se numeran: 1ª entrada · 1ª salida · 2ª entrada · 2ª salida
$marcaTxt    = ($doble ? [
    'entrada'         => ['login', 'Marcar 1ª entrada'],
    'salida_descanso' => ['logout', 'Marcar 1ª salida'],
    'regreso'         => ['login', 'Marcar 2ª entrada'],
    'salida'          => ['logout', 'Marcar 2ª salida'],
] : [
    'entrada'         => ['login', 'Registrar entrada'],
    'salida'          => ['logout', 'Registrar salida'],
])[$marca] ?? ['login', 'Registrar entrada'];
$pendiente   = $salidaPendiente ?? null;
$numPend     = is_array($salidasPendientes ?? null) ? count($salidasPendientes) : ($pendiente ? 1 : 0);
$pendFecha   = $pendiente ? date('d/m/Y', strtotime((string) $pendiente['date'])) : '';
$pendEntrada = $pendiente ? substr((string) ($pendiente['time_in'] ?? ''), 0, 5) : '';
$sinHorario  = empty($miHorario);
$horaEntrada = $miHorario['hora_entrada'] ?? null;
$horaSalida  = !empty($miHorario) ? \App\Libraries\Asistencia\Jornada::salidaFinal($miHorario) : null;
$diasHorario = $miHorario['dias'] ?? null;
$esPrac      = (session()->get('user_role') ?? '') === 'Practicante';
$now         = (int) date('Hi');
$hIn         = (int) str_replace(':', '', (string) $horaEntrada);
$hOut        = (int) str_replace(':', '', (string) $horaSalida);
$enHorario   = !$sinHorario && $now >= $hIn && $now <= $hOut;
?>
<section class="tk-card tk-mark tk-anim-item">
    <div class="tk-mark-clock">
        <div class="tk-mark-time" id="praClock">--:--</div>
        <div class="tk-mark-sec" id="praClockSec"></div>
    </div>

    <div class="tk-mark-info">
        <h3>Marcar asistencia de hoy</h3>
        <p>
            <?php if ($pendiente): ?>
                Tienes una <strong>salida pendiente del <?= esc($pendFecha) ?></strong><?= $numPend > 1 ? ' (y ' . ($numPend - 1) . ' más)' : '' ?>.
                Regístrala antes de marcar tu entrada de hoy.
            <?php elseif (!$hEnt): ?>
                Aún no has marcado tu entrada.
            <?php elseif ($marca === 'regreso'): ?>
                1ª salida <strong><?= esc(substr((string) $hOut1, 0, 5)) ?></strong> registrada · marca tu <strong>2ª entrada</strong><?= isset($bloquesH[1]) ? ' a las <strong>' . esc(substr($bloquesH[1][0], 0, 5)) . '</strong>' : '' ?>.
            <?php elseif ($marca === 'salida_descanso'): ?>
                1ª entrada <strong><?= esc(substr((string) $hEnt, 0, 5)) ?></strong> registrada · marca tu <strong>1ª salida</strong><?= isset($bloquesH[0]) ? ' a las <strong>' . esc(substr($bloquesH[0][1], 0, 5)) . '</strong>' : '' ?>.
            <?php elseif (!$hSal && $hIn2): ?>
                2ª entrada <strong><?= esc(substr((string) $hIn2, 0, 5)) ?></strong> registrada · marca tu <strong>2ª salida</strong><?= $horaSalida ? ' a las <strong>' . esc(substr($horaSalida, 0, 5)) . '</strong>' : '' ?>.
            <?php elseif (!$hSal): ?>
                Entrada <strong><?= esc(substr((string) $hEnt, 0, 5)) ?></strong> registrada · falta marcar la salida.
            <?php else: ?>
                Marcación completa de hoy.
            <?php endif; ?>
        </p>
        <div class="tk-mark-cells<?= ($doble && !$pendiente) ? ' tk-mark-cells-4' : '' ?>">
            <?php if ($pendiente): ?>
                <div><span>Entrada <?= esc($pendFecha) ?></span><strong><?= esc($pendEntrada ?: '—') ?></strong></div>
                <div><span>Salida</span><strong>Pendiente</strong></div>
                <div><span>Jornada</span><strong><?= esc($diasHorario ?: '—') ?></strong></div>
            <?php else: ?>
                <?php if ($doble): ?>
                    <div><span>1ª entrada</span><strong><?= esc($hEnt ? substr((string) $hEnt, 0, 5) : substr($bloquesH[0][0], 0, 5)) ?></strong></div>
                    <div><span>1ª salida</span><strong><?= esc($hOut1 ? substr((string) $hOut1, 0, 5) : substr($bloquesH[0][1], 0, 5)) ?></strong></div>
                    <div><span>2ª entrada</span><strong><?= esc($hIn2 ? substr((string) $hIn2, 0, 5) : substr($bloquesH[1][0], 0, 5)) ?></strong></div>
                    <div><span>2ª salida</span><strong><?= esc($hSal ? substr((string) $hSal, 0, 5) : substr($bloquesH[1][1], 0, 5)) ?></strong></div>
                    <div class="tk-mark-cells-full"><span>Jornada</span><strong><?= esc($diasHorario ?: '—') ?></strong></div>
                <?php else: ?>
                    <div><span>Entrada</span><strong><?= esc($hEnt ? substr((string) $hEnt, 0, 5) : ($horaEntrada ? substr($horaEntrada, 0, 5) : '—')) ?></strong></div>
                    <div><span>Salida</span><strong><?= esc($hSal ? substr((string) $hSal, 0, 5) : ($horaSalida ? substr($horaSalida, 0, 5) : '—')) ?></strong></div>
                    <div><span>Jornada</span><strong><?= esc($diasHorario ?: '—') ?></strong></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="tk-mark-state">
            <span class="pra-status-pill <?= $enHorario ? '' : 'off' ?>">
                <span class="material-symbols-outlined"><?= $enHorario ? 'work' : ($sinHorario ? 'schedule' : 'bedtime') ?></span>
                <?php if ($sinHorario): ?>
                    Sin horario configurado
                <?php else: ?>
                    <?= $enHorario ? 'En tu horario ' . ($esPrac ? 'de práctica' : 'laboral') : 'Fuera de tu horario' ?>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <div class="tk-mark-cta pra-marcar-cta">
        <?php if ($pendiente): ?>
            <button type="button" class="dev-btn dev-btn-primary" onclick="openBioCheck()">
                <span class="material-symbols-outlined">logout</span>
                Registrar salida pendiente
            </button>
        <?php elseif ($hEnt && $hSal): ?>
            <span class="tk-pill-state tk-st-ok" style="height:44px;padding:0 20px;font-size:13px;">
                <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span> Completado hoy
            </span>
        <?php else: ?>
            <button type="button" class="dev-btn dev-btn-primary" onclick="openBioCheck()">
                <span class="material-symbols-outlined"><?= $marcaTxt[0] ?></span>
                <?= $marcaTxt[1] ?>
            </button>
        <?php endif; ?>
    </div>
</section>

<?= view('partials/biometric_panel', ['bioMark' => [
    // Sólo la salida final pide motivo si es antes de hora (no el descanso)
    'exit'              => (bool) ($pendiente || $marca === 'salida'),
    'hora_salida'       => $sinHorario ? '' : substr((string) $horaSalida, 0, 5),
    'pending_id'        => (int) ($pendiente['id'] ?? 0),
    'pending_date'      => (string) ($pendiente['date'] ?? ''),
    'pending_time_in'   => (string) ($pendiente['time_in'] ?? ''),
    'pending_scheduled' => $sinHorario ? '' : substr((string) $horaSalida, 0, 5),
], 'userScheduleId' => (int) ($miInfo['schedule_id'] ?? 0)]) ?>

<style>
    /* Doble turno: 4 marcas en una fila y la jornada debajo */
    .tk-mark-cells.tk-mark-cells-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .tk-mark-cells.tk-mark-cells-4 .tk-mark-cells-full { grid-column: 1 / -1; }
    @media (max-width: 560px) { .tk-mark-cells.tk-mark-cells-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
<script>
    // Si hay una salida de un día anterior sin cerrar, el botón debe seguir
    // siendo el de "salida pendiente": los eventos de hoy no lo reemplazan.
    window.__salidaPendiente = <?= $pendiente ? 'true' : 'false' ?>;
    window.__etapaMarca = <?= json_encode(implode('|', [$hEnt ?: '', $hOut1 ?: '', $hIn2 ?: '', $hSal ?: ''])) ?>;

    // Tiempo real (SSE / polling): actualiza el botón y la pastilla de estado
    window.actualizarUIAsistencia = function (asistencia) {
        if (window.__salidaPendiente) return;
        asistencia = asistencia || {};
        var cta = document.querySelector('.pra-marcar-cta');
        if (!cta) return;
        var hEnt = asistencia.time_in || null;
        var hSal = asistencia.time_out || null;
        // El servidor ya pintó el botón correcto para la etapa actual
        // (1ª entrada, 1ª salida, 2ª entrada, 2ª salida). Si otra pestaña o
        // dispositivo marcó algo, la etapa cambia y se recarga la tarjeta.
        var etapa = [hEnt || '', asistencia.time_out1 || '', asistencia.time_in2 || '', hSal || ''].join('|');
        if (window.__etapaMarca !== etapa) { location.reload(); }
    };

    window.mostrarAlerta = function (datos) {
        if (!datos || !Array.isArray(datos)) return;
        datos.forEach(function (a) {
            var toast = document.createElement('div');
            toast.className = 'dev-toast dev-toast-' + (a.tipo === 'tardanza' ? 'warning' : 'danger');
            toast.innerHTML = '<span class="material-symbols-outlined">' + (a.tipo === 'tardanza' ? 'schedule' : 'warning') + '</span> ' + (a.mensaje || 'Alerta');
            document.body.appendChild(toast);
            setTimeout(function () { toast.remove(); }, 5000);
        });
    };

    // Barras de progreso: animan al cargar
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(function () {
            document.querySelectorAll('.tk-prog, .tk-hours, .tk-asum').forEach(function (el) { el.classList.add('tk-ready'); });
        });
    });
</script>
