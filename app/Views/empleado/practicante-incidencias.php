<style>
        /* ── Resumen minimalista ── */
        .mi-sum {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 16px; padding: 18px 22px; margin-bottom: 18px;
            display: flex; align-items: center; gap: 22px; flex-wrap: wrap;
        }
        .mi-stat { display: flex; flex-direction: column; gap: 2px; min-width: 64px; }
        .mi-stat + .mi-stat { padding-left: 22px; border-left: 1px solid var(--g-border); }
        .mi-stat b { font-size: 26px; font-weight: 600; font-family: var(--g-font-display); line-height: 1; display: flex; align-items: baseline; gap: 6px; }
        .mi-stat b i { width: 9px; height: 9px; border-radius: 50%; display: inline-block; transform: translateY(-4px); }
        .mi-stat span { font-size: 11.5px; color: var(--g-text-secondary); }
        .mi-track { flex: 1; min-width: 160px; display: flex; height: 6px; border-radius: 999px; overflow: hidden; background: var(--g-surface-variant); }
        .mi-track i { height: 100%; }

        /* ── Lista minimalista ── */
        .mi-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; overflow: hidden; }
        .mi-card-head { padding: 14px 20px; border-bottom: 1px solid var(--g-border); }
        .mi-card-head h3 { margin: 0; font-size: 14px; font-weight: 600; font-family: var(--g-font-display); color: var(--g-text); }
        .mi-item { display: flex; gap: 14px; padding: 16px 20px; align-items: center; }
        .mi-item + .mi-item { border-top: 1px solid var(--g-border); }
        .mi-ic {
            flex-shrink: 0; width: 40px; height: 40px; border-radius: 12px;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .mi-main { flex: 1; min-width: 0; }
        .mi-top { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .mi-tipo { font-size: 14px; font-weight: 600; font-family: var(--g-font-display); }
        .mi-fecha { font-size: 11.5px; color: var(--g-text-secondary); font-family: 'SF Mono', Consolas, monospace; margin-top: 3px; }
        .mi-detalle { font-size: 12.5px; color: var(--g-text-secondary); margin-top: 6px; line-height: 1.5; }
        .mi-detalle:empty { display: none; }
        .mi-just {
            margin-top: 10px; font-size: 12.5px; color: var(--g-text-secondary);
            background: var(--g-surface-variant); border-radius: 10px; padding: 9px 13px;
            display: flex; gap: 8px; align-items: flex-start;
        }
        .mi-just .material-symbols-outlined { font-size: 16px; flex-shrink: 0; margin-top: 1px; opacity: 0.6; }
        .mi-just p { margin: 0; }
        .mi-just small { display: block; font-weight: 600; color: var(--g-text); margin-bottom: 2px; }
        .mi-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; color: var(--g-text-secondary); }
        .mi-estado i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .mi-estado.dot-pendiente i { background: #f59e0b; }
        .mi-estado.dot-revision i { background: #3b82f6; }
        .mi-estado.dot-resuelta i { background: #22c55e; }
        .mi-estado.dot-desest i { background: #94a3b8; }
        .mi-estado.dot-justificada i { background: #22c55e; }
        .mi-estado.dot-rechazada i { background: #ef4444; }
        .mi-act { flex-shrink: 0; }
        .mi-btn {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600;
            color: var(--g-text); background: transparent; border: 1px solid var(--g-border);
            border-radius: 999px; padding: 7px 14px; cursor: pointer; transition: all 160ms;
            font-family: var(--g-font);
        }
        .mi-btn .material-symbols-outlined { font-size: 16px; }
        .mi-btn:hover { border-color: var(--g-primary); color: var(--g-primary); background: var(--g-primary-light); }
        .mi-empty { text-align: center; padding: 48px 20px; color: var(--g-text-secondary); font-size: 13px; }
        .mi-empty .material-symbols-outlined { font-size: 40px; display: block; margin: 0 auto 12px; opacity: 0.35; }

        @media (max-width: 640px) {
            .mi-stat + .mi-stat { padding-left: 16px; }
            .mi-item { padding: 14px; gap: 12px; flex-wrap: wrap; }
            .mi-act { width: 100%; padding-left: 52px; }
        }

        /* ── Modal justificar ── */
        .in-modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.45); z-index: 2000;
            display: flex; align-items: center; justify-content: center; padding: 20px;
            opacity: 0; visibility: hidden; transition: opacity 180ms, visibility 180ms;
        }
        .in-modal-overlay.is-open { opacity: 1; visibility: visible; }
        .in-modal { background: var(--g-surface); border-radius: 16px; width: 92%; max-width: 420px; box-shadow: 0 12px 32px -8px rgba(0,0,0,0.18); overflow: hidden; }
        .in-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px 12px; }
        .in-modal-head h3 { font-size: 16px; font-weight: 600; font-family: var(--g-font-display); margin: 0; display: flex; align-items: center; gap: 10px; }
        .in-modal-head h3 .material-symbols-outlined { font-size: 20px; color: var(--g-warning); }
        .in-modal-close { width: 30px; height: 30px; border: none; border-radius: 8px; background: transparent; cursor: pointer; color: var(--g-text-secondary); display: flex; align-items: center; justify-content: center; }
        .in-modal-close:hover { background: var(--g-surface-variant); }
        .in-modal-body { padding: 4px 22px 0; }
        .in-modal-body textarea { width: 100%; border: 1px solid var(--g-border); border-radius: 12px; padding: 12px 14px; font-size: 14px; font-family: var(--g-font); color: var(--g-text); background: var(--g-surface); resize: vertical; min-height: 90px; box-sizing: border-box; }
        .in-modal-body textarea:focus { outline: none; border-color: var(--g-warning); }
        .in-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 22px 20px; }

        @media (max-width: 768px) {
            .in-modal-overlay { padding: 0; align-items: flex-end; }
            .in-modal { width: 100%; max-width: 100%; border-radius: 16px 16px 0 0; }
        }
    </style>

<?php
    $resueltas = 0; $revision = 0; $desest = 0; $pendientes = 0;
    foreach ($misIncidencias ?? [] as $inc) {
        $e = $inc['estado'] ?? 'Pendiente';
        if ($e === 'Resuelta' || $e === 'Justificada') $resueltas++;
        elseif ($e === 'Revisión') $revision++;
        elseif ($e === 'Desestimada' || $e === 'Rechazada') $desest++;
        else $pendientes++;
    }
    $totalInc = count($misIncidencias ?? []);

    $tInfo = [
        'Tardanza'          => ['icon' => 'schedule'],
        'Falta'             => ['icon' => 'cancel'],
        'Salida anticipada' => ['icon' => 'logout'],
        'Sin marcación'     => ['icon' => 'event_busy'],
        'Otro'              => ['icon' => 'help'],
    ];
    $eDot = [
        'Pendiente'   => 'dot-pendiente',
        'Revisión'    => 'dot-revision',
        'Resuelta'    => 'dot-resuelta',
        'Justificada' => 'dot-justificada',
        'Rechazada'   => 'dot-rechazada',
        'Desestimada' => 'dot-desest',
    ];
    $sPend = $totalInc > 0 ? round(($pendientes / $totalInc) * 100) : 0;
    $sRes  = $totalInc > 0 ? round((($resueltas + $desest) / $totalInc) * 100) : 0;
    if (($sPend + $sRes) > 100 && $totalInc > 0) { $sRes = 100 - $sPend; }
?>

<div class="page-head">
    <a class="page-back" href="<?= base_url('mi-panel') ?>">
        <span class="material-symbols-outlined">arrow_back</span> Volver a Mi Panel
    </a>
    <div class="page-title">
        <span class="material-symbols-outlined">warning</span>
        <h2>Mis Incidencias</h2>
    </div>
    <p class="page-sub">Tardanzas, faltas y otras situaciones registradas sobre tu asistencia.</p>
</div>

<!-- Resumen -->
<div class="mi-sum">
    <div class="mi-stat"><b><?= $totalInc ?></b><span><?= $totalInc === 1 ? 'incidencia' : 'incidencias' ?></span></div>
    <div class="mi-stat"><b><i style="background:#f59e0b;"></i><?= $pendientes ?></b><span><?= $pendientes === 1 ? 'pendiente' : 'pendientes' ?></span></div>
    <div class="mi-stat"><b><i style="background:#3b82f6;"></i><?= $revision ?></b><span><?= $revision === 1 ? 'en revisión' : 'en revisión' ?></span></div>
    <div class="mi-stat"><b><i style="background:#22c55e;"></i><?= $resueltas + $desest ?></b><span><?= ($resueltas + $desest) === 1 ? 'resuelta' : 'resueltas' ?></span></div>
    <div class="mi-track">
        <i style="width:<?= $sPend ?>%; background:#f59e0b;"></i>
        <i style="width:<?= max(0, $totalInc > 0 ? ($revision / $totalInc) * 100 : 0) ?>%; background:#3b82f6;"></i>
        <i style="width:<?= $sRes ?>%; background:#22c55e;"></i>
    </div>
</div>

<!-- Lista -->
<div class="mi-card">
    <div class="mi-card-head">
        <h3>Todas mis incidencias</h3>
    </div>
    <div>
        <?php if ($totalInc === 0): ?>
            <div class="mi-empty">
                <span class="material-symbols-outlined">sentiment_satisfied</span>
                No tienes incidencias registradas. Bien hecho.
            </div>
        <?php else: ?>
            <?php foreach ($misIncidencias as $inc):
                $iest = $inc['estado'] ?? 'Pendiente';
                $tipo = $inc['tipo'] ?? 'Otro';
                $ico  = $tInfo[$tipo]['icon'] ?? 'help';
                $dot  = $eDot[$iest] ?? 'dot-pendiente';
                $hasJust = !empty($inc['justificacion']);
                $justMsg = match($iest) {
                    'Resuelta', 'Justificada' => 'Justificación aceptada',
                    'Revisión' => 'Justificación en revisión',
                    'Desestimada' => 'Desestimada por el administrador',
                    'Rechazada' => 'Rechazada por el administrador',
                    default => 'Justificación enviada',
                };
                $justIco = match($iest) {
                    'Revisión' => 'hourglass_top',
                    'Desestimada', 'Rechazada' => 'do_not_disturb_on',
                    default => 'verified',
                };
            ?>
                <div class="mi-item">
                    <div class="mi-ic"><span class="material-symbols-outlined"><?= esc($ico) ?></span></div>
                    <div class="mi-main">
                        <div class="mi-top">
                            <span class="mi-tipo"><?= esc($tipo) ?></span>
                            <span class="mi-estado <?= $dot ?>"><i></i> <?= esc($iest) ?></span>
                        </div>
                        <div class="mi-fecha"><?= esc(date('d/m/Y H:i', strtotime($inc['fecha'] ?? 'now'))) ?></div>
                        <?php if (!empty($inc['detalle'])): ?>
                            <div class="mi-detalle"><?= esc($inc['detalle']) ?></div>
                        <?php endif; ?>
                        <?php if ($hasJust): ?>
                            <div class="mi-just">
                                <span class="material-symbols-outlined"><?= $justIco ?></span>
                                <p><small><?= $justMsg ?></small><?= esc($inc['justificacion']) ?></p>
                            </div>
                        <?php elseif ($iest === 'Revisión' && !$hasJust): ?>
                            <div class="mi-just">
                                <span class="material-symbols-outlined">hourglass_top</span>
                                <p><small>En revisión</small>Tu administrador está revisando esta incidencia.</p>
                            </div>
                        <?php elseif (($iest === 'Resuelta' || $iest === 'Justificada') && !$hasJust): ?>
                            <div class="mi-just">
                                <span class="material-symbols-outlined">verified</span>
                                <p><small>Resuelta</small>Tu administrador la marcó como resuelta.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($iest === 'Pendiente'): ?>
                        <div class="mi-act">
                            <button type="button" class="mi-btn" onclick="openJustify(<?= (int) $inc['id'] ?>)">
                                <span class="material-symbols-outlined">edit_note</span> Justificar
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal justificar incidencia -->
<div class="in-modal-overlay" id="justifyModal">
    <div class="in-modal">
        <div class="in-modal-head">
            <h3><span class="material-symbols-outlined">edit_note</span> Justificar incidencia</h3>
            <button class="in-modal-close" onclick="closeJustify()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="post" action="<?= site_url('mi-panel/justificar-incidencia') ?>" id="justifyForm">
            <?= csrf_field() ?>
            <input type="hidden" name="inc_id" id="justifyIncId" value="">
            <div class="in-modal-body">
                <p style="font-size:13px;color:var(--g-text-secondary);margin:0 0 10px;">Cuenta qué pasó. Quedará en <strong>revisión</strong> para tu administrador.</p>
                <textarea name="justificacion" id="justifyText" placeholder="Ej: El bus se demoró por el tráfico..." required></textarea>
            </div>
            <div class="in-modal-foot">
                <button type="button" class="dev-btn" onclick="closeJustify()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">
                    <span class="material-symbols-outlined">send</span> Enviar justificación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openJustify(id) {
    document.getElementById('justifyIncId').value = id;
    document.getElementById('justifyText').value = '';
    document.getElementById('justifyModal').classList.add('is-open');
}
function closeJustify() {
    document.getElementById('justifyModal').classList.remove('is-open');
}
document.getElementById('justifyModal').addEventListener('click', function(e) {
    if (e.target === this) closeJustify();
});
// Mantener fresco el token CSRF antes de enviar
(function() {
    function freshCsrf() {
        return fetch('<?= site_url('mi-panel/bio-session') ?>', { credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(d) { return (d && d.csrfHash) ? d.csrfHash : null; })
            .catch(function() { return null; });
    }
    var f = document.getElementById('justifyForm');
    f.addEventListener('submit', function(e) {
        e.preventDefault();
        freshCsrf().then(function(tok) {
            var i = f.querySelector('input[name=csrf_test_name]');
            if (i && tok) i.value = tok;
            f.submit();
        });
    });
})();
</script>