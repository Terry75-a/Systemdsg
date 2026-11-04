<?php
/**
 * Tarjeta "Marcar asistencia de hoy" (estilo Trackin) + biometría.
 * La usan los dashboards del Empleado y del Practicante.
 */
$hoy         = $hoyRegistro ?? null;
$hEnt        = $hoy['time_in']  ?? null;
$hSal        = $hoy['time_out'] ?? null;
$pendiente   = $salidaPendiente ?? null;
$numPend     = is_array($salidasPendientes ?? null) ? count($salidasPendientes) : ($pendiente ? 1 : 0);
$pendFecha   = $pendiente ? date('d/m/Y', strtotime((string) $pendiente['date'])) : '';
$pendEntrada = $pendiente ? substr((string) ($pendiente['time_in'] ?? ''), 0, 5) : '';
$sinHorario  = empty($miHorario);
$horaEntrada = $miHorario['hora_entrada'] ?? null;
$horaSalida  = $miHorario['hora_salida']  ?? null;
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
            <?php elseif (!$hSal): ?>
                Entrada <strong><?= esc($hEnt) ?></strong> registrada · falta marcar la salida.
            <?php else: ?>
                Marcación completa de hoy.
            <?php endif; ?>
        </p>
        <div class="tk-mark-cells">
            <?php if ($pendiente): ?>
                <div><span>Entrada <?= esc($pendFecha) ?></span><strong><?= esc($pendEntrada ?: '—') ?></strong></div>
                <div><span>Salida</span><strong>Pendiente</strong></div>
                <div><span>Jornada</span><strong><?= esc($diasHorario ?: '—') ?></strong></div>
            <?php else: ?>
                <div><span>Entrada</span><strong><?= esc($hEnt ?: ($horaEntrada ? substr($horaEntrada, 0, 5) : '—')) ?></strong></div>
                <div><span>Salida</span><strong><?= esc($hSal ?: ($horaSalida ? substr($horaSalida, 0, 5) : '—')) ?></strong></div>
                <div><span>Jornada</span><strong><?= esc($diasHorario ?: '—') ?></strong></div>
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
                <span class="material-symbols-outlined"><?= $hEnt ? 'logout' : 'login' ?></span>
                <?= $hEnt ? 'Registrar salida' : 'Registrar entrada' ?>
            </button>
        <?php endif; ?>
    </div>
</section>

<?= view('partials/biometric_panel', ['bioMark' => [
    'exit'              => (bool) ($pendiente || ($hEnt && !$hSal)),
    'hora_salida'       => $sinHorario ? '' : substr((string) $horaSalida, 0, 5),
    'pending_id'        => (int) ($pendiente['id'] ?? 0),
    'pending_date'      => (string) ($pendiente['date'] ?? ''),
    'pending_time_in'   => (string) ($pendiente['time_in'] ?? ''),
    'pending_scheduled' => $sinHorario ? '' : substr((string) $horaSalida, 0, 5),
], 'userScheduleId' => (int) ($miInfo['schedule_id'] ?? 0)]) ?>

<script>
    // Si hay una salida de un día anterior sin cerrar, el botón debe seguir
    // siendo el de "salida pendiente": los eventos de hoy no lo reemplazan.
    window.__salidaPendiente = <?= $pendiente ? 'true' : 'false' ?>;

    // Tiempo real (SSE / polling): actualiza el botón y la pastilla de estado
    window.actualizarUIAsistencia = function (asistencia) {
        if (window.__salidaPendiente) return;
        var cta = document.querySelector('.pra-marcar-cta');
        if (!cta) return;
        var hEnt = asistencia.time_in || null;
        var hSal = asistencia.time_out || null;
        if (hEnt && hSal) {
            cta.innerHTML = '<span class="tk-pill-state tk-st-ok" style="height:44px;padding:0 20px;font-size:13px;">'
                + '<span class="material-symbols-outlined" style="font-size:16px;">check_circle</span> Completado hoy</span>';
            return;
        }
        var btn = cta.querySelector('.dev-btn');
        if (!btn) return;
        btn.innerHTML = hEnt
            ? '<span class="material-symbols-outlined">logout</span> Registrar salida'
            : '<span class="material-symbols-outlined">login</span> Registrar entrada';
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
