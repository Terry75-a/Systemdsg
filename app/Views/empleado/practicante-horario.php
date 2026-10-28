<?php
    $miHorario   = $miHorario ?? [];
    $sinHorario  = empty($miHorario);
    $horaEntrada = $miHorario['hora_entrada'] ?? '08:00';
    $horaSalida  = $miHorario['hora_salida']  ?? '17:00';
    $diasHorario = (string) ($miHorario['dias'] ?? '');
    $tolerancia  = (int) ($miHorario['tolerancia'] ?? 10);
    $esPrac      = (session()->get('user_role') ?? '') === 'Practicante';

    // Días activos: entiende listas ("Lun, Mié, Vie") y rangos ("Lun - Vie")
    $orden   = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    $claves  = ['lun' => 'L', 'mar' => 'M', 'mié' => 'X', 'mie' => 'X', 'jue' => 'J', 'vie' => 'V', 'sáb' => 'S', 'sab' => 'S', 'dom' => 'D'];
    $nombres = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sábado', 'D' => 'Domingo'];
    $activos = [];
    if (!$sinHorario && $diasHorario !== '') {
        $txt = mb_strtolower($diasHorario);
        $aLetra = static function (string $t) use ($claves): ?string {
            foreach ($claves as $k => $l) {
                if (mb_strpos($t, $k) !== false) return $l;
            }
            return null;
        };
        foreach (preg_split('/[,;\/]|\sy\s/u', $txt) as $parte) {
            $rango = preg_split('/\s*[-–]\s*|\s+a\s+/u', trim($parte));
            if (count($rango) === 2 && ($ini = $aLetra($rango[0])) && ($fin = $aLetra($rango[1]))) {
                $i = array_search($ini, $orden, true);
                $f = array_search($fin, $orden, true);
                for ($k = $i; $k !== false && $f !== false; $k = ($k + 1) % 7) {
                    $activos[$orden[$k]] = true;
                    if ($k === $f) break;
                }
            } elseif ($l = $aLetra($parte)) {
                $activos[$l] = true;
            }
        }
    }
    $hoyIdx  = (int) date('N') - 1;
    $limite  = date('H:i', strtotime(substr($horaEntrada, 0, 5)) + $tolerancia * 60);
    $hEntTs  = strtotime(substr($horaEntrada, 0, 5));
    $hSalTs  = strtotime(substr($horaSalida, 0, 5));
    $horasDia = $hSalTs > $hEntTs ? round(($hSalTs - $hEntTs) / 3600, 1) : 0;
    $diasLista = array_values(array_filter($orden, static fn ($l) => isset($activos[$l])));
?>

<?php if ($sinHorario): ?>
    <section class="tk-card">
        <div class="tk-void" style="padding:56px 16px;">
            <span class="material-symbols-outlined">schedule</span>
            Tu administrador aún no configura un horario para ti.<br>
            En cuanto lo active, verás aquí tus días y horas.
        </div>
    </section>
<?php else: ?>
    <!-- KPIs del horario -->
    <section class="tk-stats tk-stats-4">
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">login</span><div><p class="tk-stat-lbl">Entrada</p><p class="tk-stat-val"><?= esc(substr($horaEntrada, 0, 5)) ?></p></div></div>
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">logout</span><div><p class="tk-stat-lbl">Salida</p><p class="tk-stat-val"><?= esc(substr($horaSalida, 0, 5)) ?></p></div></div>
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">hourglass_top</span><div><p class="tk-stat-lbl">Horas por día</p><p class="tk-stat-val"><?= $horasDia ?> h</p></div></div>
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">timer</span><div><p class="tk-stat-lbl">Tolerancia</p><p class="tk-stat-val"><?= $tolerancia ?> min</p></div></div>
    </section>

    <!-- Semana -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>Mi semana</h3><p><?= esc($diasHorario ?: '—') ?> · <?= count($diasLista) ?> <?= count($diasLista) === 1 ? 'día' : 'días' ?> <?= $esPrac ? 'de práctica' : 'laborables' ?>.</p></div>
            <span class="tk-pill-state tk-st-neutral">Hoy · <?= esc($nombres[$orden[$hoyIdx]]) ?></span>
        </div>
        <div class="tk-week">
            <?php foreach ($orden as $i => $l): $on = isset($activos[$l]); ?>
                <div class="tk-wday <?= $on ? 'on' : '' ?> <?= $i === $hoyIdx ? 'today' : '' ?>">
                    <span class="tk-wday-n"><?= esc(mb_substr($nombres[$l], 0, 3)) ?></span>
                    <?php if ($on): ?>
                        <span class="tk-wday-h"><?= esc(substr($horaEntrada, 0, 5)) ?></span>
                        <span class="tk-wday-h"><?= esc(substr($horaSalida, 0, 5)) ?></span>
                    <?php else: ?>
                        <span class="tk-wday-off">Libre</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Reglas -->
    <section class="tk-card tk-ovh tk-anim-item">
        <div class="tk-card-head">
            <div><h3>¿Cómo se calcula tu puntualidad?</h3><p>Las reglas que aplica el sistema al marcar.</p></div>
        </div>
        <div class="tk-kv">
            <div class="tk-kv-row"><span>Puntual</span><span>Marcas hasta las <strong class="tk-mono"><?= esc($limite) ?></strong> <span class="tk-st tk-st-present" style="margin-left:6px;">Puntual</span></span></div>
            <div class="tk-kv-row"><span>Tardanza</span><span>Después de las <strong class="tk-mono"><?= esc($limite) ?></strong> · se genera una incidencia <span class="tk-st tk-st-late" style="margin-left:6px;">Tardanza</span></span></div>
            <div class="tk-kv-row"><span>Días que trabajas</span><span><?= esc(implode(', ', array_map(static fn ($l) => $nombres[$l], $diasLista)) ?: '—') ?></span></div>
        </div>
    </section>

    <div class="tk-note">
        <span class="material-symbols-outlined">info</span>
        Tu administrador puede ajustar este horario en cualquier momento. Los cambios aplican desde el día siguiente.
    </div>
<?php endif; ?>
