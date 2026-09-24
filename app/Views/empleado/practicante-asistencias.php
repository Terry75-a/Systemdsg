<style>
        /* ── Resumen de puntualidad ── */
        .as-summary {
            display: flex; align-items: center; gap: 22px; flex-wrap: wrap;
            background: linear-gradient(135deg, var(--g-primary-light), var(--g-surface));
            border: 1px solid var(--g-border); border-radius: 18px;
            padding: 20px 24px; margin-bottom: 18px;
        }
        .as-pct { flex-shrink: 0; text-align: center; }
        .as-pct b { display: block; font-size: 40px; font-family: var(--g-font-display); letter-spacing: -0.03em; line-height: 1; }
        .as-pct span { font-size: 11.5px; color: var(--g-text-secondary); }
        .as-pct.good b { color: var(--g-success); }
        .as-pct.mid b { color: var(--g-warning); }
        .as-pct.bad b { color: var(--g-error); }
        .as-bar { flex: 1; min-width: 200px; }
        .as-bar-track { height: 12px; border-radius: 999px; background: var(--g-surface-variant); overflow: hidden; }
        .as-bar-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--g-warning), var(--g-success)); transition: width 600ms; }
        .as-bar-fill.bad { background: var(--g-error); }
        .as-bar-meta { display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: var(--g-text-secondary); }
        .as-legend { display: flex; gap: 14px; flex-wrap: wrap; font-size: 12px; }
        .as-legend span { display: inline-flex; align-items: center; gap: 6px; color: var(--g-text-secondary); }
        .as-legend i { width: 10px; height: 10px; border-radius: 3px; background: var(--g-success); display: inline-block; }
        .as-legend .lg-late i { background: var(--g-warning); }
        .as-legend .lg-absent i { background: var(--g-error); }
        .as-legend .lg-none i { background: var(--g-surface-variant); }

        /* ── Calendario del mes ── */
        .as-cal-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 18px; padding: 20px; margin-bottom: 18px; }
        .as-cal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .as-cal-head h3 { margin: 0; font-size: 15px; font-weight: 600; font-family: var(--g-font-display); display: flex; align-items: center; gap: 8px; }
        .as-cal-head .material-symbols-outlined { color: var(--g-primary); font-size: 20px; }
        .as-cal { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
        .as-dow { text-align: center; font-size: 11px; font-weight: 600; color: var(--g-text-secondary); padding: 4px 0; }
        .as-dow.weekend { color: var(--g-text-disabled); }
        .as-cell {
            aspect-ratio: 1; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 500; color: var(--g-text-secondary);
            background: var(--g-surface-variant); position: relative; transition: transform 120ms;
            border: none; padding: 0; font-family: var(--g-font); cursor: pointer;
        }
        .as-cell:not(.empty):hover { transform: scale(1.06); }
        .as-cell.empty { background: transparent; }
        .as-cell.present { background: var(--g-success-light); color: var(--g-success); font-weight: 700; }
        .as-cell.late { background: var(--g-warning-light); color: #e37400; font-weight: 700; }
        .as-cell.absent { background: var(--g-error-light); color: var(--g-error); font-weight: 700; }
        .as-cell.today { box-shadow: 0 0 0 2px var(--g-surface), 0 0 0 4px var(--g-primary); }
        .as-cell.today:not(.present):not(.late):not(.absent) { color: var(--g-primary); font-weight: 700; }
        .as-cell .as-st-dot { position: absolute; bottom: 5px; width: 5px; height: 5px; border-radius: 50%; }
        .as-cell.present .as-st-dot { background: var(--g-success); }
        .as-cell.late .as-st-dot { background: #e37400; }
        .as-cell.absent .as-st-dot { background: var(--g-error); }

        /* ── Modal del día ── */
        .dm-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.45); z-index: 2000;
            display: flex; align-items: center; justify-content: center; padding: 20px;
            opacity: 0; visibility: hidden; transition: opacity 180ms, visibility 180ms;
        }
        .dm-overlay.is-open { opacity: 1; visibility: visible; }
        .dm { background: var(--g-surface); border-radius: 16px; width: 92%; max-width: 420px; box-shadow: 0 12px 32px -8px rgba(0,0,0,0.18); overflow: hidden; }
        .dm-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px 12px; }
        .dm-head-in { display: flex; align-items: center; gap: 10px; }
        .dm-head h3 { font-size: 15.5px; font-weight: 700; font-family: var(--g-font-display); margin: 0; }
        .dm-head .dm-date-sub { font-size: 12px; color: var(--g-text-secondary); margin-top: 1px; }
        .dm-head-ic {
            width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .dm-head-ic.ic-present { background: var(--g-success-light); color: var(--g-success); }
        .dm-head-ic.ic-late { background: var(--g-warning-light); color: #e37400; }
        .dm-head-ic.ic-absent { background: var(--g-error-light); color: var(--g-error); }
        .dm-head-ic.ic-none { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .dm-close { width: 30px; height: 30px; border: none; border-radius: 8px; background: transparent; cursor: pointer; color: var(--g-text-secondary); display: flex; align-items: center; justify-content: center; }
        .dm-close:hover { background: var(--g-surface-variant); }
        .dm-body { padding: 4px 22px 2px; }
        .dm-badge-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
        .dm-badge-row .dm-txt { font-size: 13px; color: var(--g-text-secondary); }
        .dm-times { display: flex; gap: 10px; }
        .dm-cell {
            flex: 1; background: var(--g-surface-variant); border: 1px solid var(--g-border);
            border-radius: 13px; padding: 12px 14px;
        }
        .dm-cell span { font-size: 11px; color: var(--g-text-secondary); display: block; }
        .dm-cell b { display: block; font-family: 'SF Mono', Consolas, monospace; font-size: 21px; font-weight: 700; margin-top: 2px; letter-spacing: 0.01em; }
        .dm-cell.off { opacity: 0.75; }
        .dm-tags { display: flex; gap: 7px; flex-wrap: wrap; margin-top: 12px; }
        .dm-tag {
            display: inline-flex; align-items: center; gap: 5px; font-size: 12px;
            background: var(--g-surface-variant); border: 1px solid var(--g-border);
            border-radius: 999px; padding: 5px 11px; color: var(--g-text-secondary);
        }
        .dm-tag .material-symbols-outlined { font-size: 15px; color: var(--g-primary); }
        .dm-note { display: flex; gap: 8px; align-items: flex-start; font-size: 12.5px; margin-top: 12px; padding: 11px 13px; border-radius: 12px; line-height: 1.5; }
        .dm-note .material-symbols-outlined { font-size: 17px; flex-shrink: 0; }
        .dm-note.warn-inc { background: var(--g-error-light); color: var(--g-error); }
        .dm-note.plain { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .dm-note a { color: inherit; font-weight: 700; text-decoration: none; border-bottom: 1px dashed currentColor; }
        .dm-empty { text-align: center; padding: 22px 8px 14px; color: var(--g-text-secondary); }
        .dm-empty .material-symbols-outlined { font-size: 40px; opacity: 0.3; display: block; margin: 0 auto 8px; }
        .dm-empty p { margin: 0; font-size: 13.5px; }
        .dm-empty small { font-size: 12px; display: block; margin-top: 4px; }
        .dm-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 22px 20px; }
        .dm-foot a.dev-btn { text-decoration: none; }

        @media (max-width: 768px) {
            .dm-overlay { padding: 0; align-items: flex-end; }
            .dm { width: 100%; max-width: 100%; border-radius: 16px 16px 0 0; }
        }

        /* ── Historial (lista) ── */
        .as-list-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 18px; overflow: hidden; }
        .as-list-head { display: flex; align-items: center; gap: 8px; padding: 16px 20px; border-bottom: 1px solid var(--g-border); }
        .as-list-head h3 { margin: 0; font-size: 15px; font-weight: 600; font-family: var(--g-font-display); }
        .as-list-head .material-symbols-outlined { color: var(--g-primary); font-size: 20px; }
        .as-row { display: flex; align-items: center; gap: 14px; padding: 14px 20px; }
        .as-row + .as-row { border-top: 1px solid var(--g-border); }
        .as-row:hover { background: var(--g-surface-variant); }
        .as-datebox {
            flex-shrink: 0; width: 52px; height: 58px; border-radius: 14px;
            background: var(--g-surface-variant); display: flex; flex-direction: column;
            align-items: center; justify-content: center; border: 1px solid var(--g-border);
        }
        .as-datebox b { font-size: 20px; font-family: var(--g-font-display); line-height: 1; }
        .as-datebox span { font-size: 10px; color: var(--g-text-secondary); text-transform: uppercase; }
        .as-datebox.todaybox { border-color: var(--g-primary); background: var(--g-primary-light); }
        .as-datebox.todaybox b { color: var(--g-primary); }
        .as-main { flex: 1; min-width: 0; }
        .as-status { display: flex; align-items: center; gap: 7px; font-size: 13.5px; font-weight: 600; flex-wrap: wrap; }
        .as-status .material-symbols-outlined { font-size: 17px; }
        .as-status.ok { color: var(--g-success); }
        .as-status.warn { color: #e37400; }
        .as-status.err { color: var(--g-error); }
        .as-status .st-tag { font-weight: 600; }
        .as-meta { font-size: 12px; color: var(--g-text-secondary); margin-top: 3px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .as-meta .bio-ic { display: inline-flex; align-items: center; gap: 4px; }
        .as-meta .bio-ic .material-symbols-outlined { font-size: 14px; }
        .as-hoy { background: var(--g-primary-light); color: var(--g-primary); font-size: 10.5px; font-weight: 600; padding: 2px 9px; border-radius: 999px; }
        .as-times { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }
        .as-time {
            background: var(--g-surface-variant); border: 1px solid var(--g-border);
            padding: 5px 10px; border-radius: 10px; font-family: 'SF Mono', Consolas, monospace;
            font-size: 12.5px; font-weight: 600;
        }
        .as-time.out { color: var(--g-text-secondary); font-weight: 500; }
        .as-arrow { color: var(--g-text-disabled); font-size: 12px; }

        .as-empty { text-align: center; padding: 50px 20px; color: var(--g-text-secondary); font-size: 13.5px; }
        .as-empty .material-symbols-outlined { font-size: 46px; display: block; margin: 0 auto 12px; opacity: 0.3; }

        @media (max-width: 640px) {
            .as-cal { gap: 4px; }
            .as-cell { font-size: 12px; border-radius: 10px; }
            .as-summary { padding: 16px 18px; gap: 16px; }
            .as-pct b { font-size: 32px; }
            .as-row { padding: 12px 14px; gap: 12px; }
            .as-datebox { width: 46px; height: 52px; }
            .as-time { padding: 4px 8px; font-size: 11.5px; }
        }
    </style>

<?php
    $year  = (int) date('Y');
    $month = (int) date('n');
    $hoy   = date('Y-m-d');
    $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
    $wdFirst = (int) date('N', mktime(0, 0, 0, $month, 1, $year)); // 1=lunes..7=domingo
    $leading = $wdFirst - 1;
    $dowEs = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'];
    $dowLargo = ['', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    $mesesEs = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $mesShort = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    $dayInfo = [];
    foreach ($misRegistros ?? [] as $r) {
        $d = $r['date'] ?? '';
        $dayInfo[$d] = [
            'st'  => $r['status'] ?? 'absent',
            'in'  => substr($r['time_in'] ?? '', 0, 5),
            'out' => substr($r['time_out'] ?? '', 0, 5),
            'bio' => !empty($r['evidencia']) ? 1 : 0,
            'obs' => trim((string) str_replace(["\r", "\n"], ' ', $r['observacion'] ?? '')),
        ];
    }
    $dayInc = [];
    foreach ($misIncidencias ?? [] as $inc) {
        $fd = date('Y-m-d', strtotime($inc['fecha'] ?? 'now'));
        $dayInc[$fd] = true;
    }

    $total = count($misRegistros ?? []);
    $pct = $total > 0 ? (int) round(((int) ($misAsistencias ?? 0) / $total) * 100) : 0;
    $asPctClass = $pct >= 80 ? 'good' : ($pct >= 60 ? 'mid' : 'bad');
?>

<div class="page-head">
    <a class="page-back" href="<?= base_url('mi-panel') ?>">
        <span class="material-symbols-outlined">arrow_back</span> Volver a Mi Panel
    </a>
    <div class="page-title">
        <span class="material-symbols-outlined">fact_check</span>
        <h2>Mi Asistencia</h2>
    </div>
    <p class="page-sub">Historial de marcas de <?= esc($mesesEs[$month]) ?> de <?= $year ?> (<?= date('Y-m') ?>).</p>
</div>

<!-- Resumen de puntualidad -->
<div class="as-summary">
    <div class="as-pct <?= $asPctClass ?>">
        <b><?= $pct ?>%</b>
        <span>puntualidad</span>
    </div>
    <div class="as-bar">
        <div class="as-bar-track"><div class="as-bar-fill <?= $asPctClass === 'bad' ? 'bad' : '' ?>" style="width:<?= $pct ?>%;"></div></div>
        <div class="as-bar-meta">
            <span><?= $misAsistencias ?? 0 ?> puntual · <?= $misTardanzas ?? 0 ?> tardanza<?= ($misTardanzas ?? 0) === 1 ? '' : 's' ?></span>
            <span><?= $misFaltas ?? 0 ?> falta<?= ($misFaltas ?? 0) === 1 ? '' : 's' ?></span>
        </div>
    </div>
    <div class="as-legend">
        <span><i></i> Puntual</span>
        <span class="lg-late"><i></i> Tardanza</span>
        <span class="lg-absent"><i></i> Falta</span>
        <span class="lg-none"><i></i> Sin marcar</span>
    </div>
</div>

<!-- Calendario del mes -->
<div class="as-cal-card">
    <div class="as-cal-head">
        <h3><span class="material-symbols-outlined">calendar_month</span> <?= esc(ucfirst($mesesEs[$month])) ?> <?= $year ?></h3>
        <span class="as-hoy">Hoy · <?= date('d/m') ?></span>
    </div>
    <div class="as-cal">
        <?php foreach ($dowEs as $i => $d): ?>
            <div class="as-dow <?= $i >= 5 ? 'weekend' : '' ?>"><?= $d ?></div>
        <?php endforeach; ?>
        <?php for ($i = 0; $i < $leading; $i++): ?>
            <div class="as-cell empty"></div>
        <?php endfor; ?>
        <?php for ($d = 1; $d <= $daysInMonth; $d++):
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $rec = $dayInfo[$date] ?? null;
            $st = $rec ? $rec['st'] : null;
            $cls = $st ? ' ' . ($st === 'present' ? 'present' : ($st === 'late' ? 'late' : 'absent')) : '';
            $isToday = $date === $hoy;
        ?>
            <button type="button" class="as-cell<?= $cls ?> <?= $isToday ? 'today' : '' ?> js-cal-day"
                data-date="<?= $date ?>"
                data-st="<?= $st ? esc($rec['st']) : '' ?>"
                data-in="<?= $rec ? esc($rec['in']) : '' ?>"
                data-out="<?= $rec ? esc($rec['out']) : '' ?>"
                data-bio="<?= $rec ? $rec['bio'] : 0 ?>"
                data-obs="<?= $rec ? esc($rec['obs']) : '' ?>"
                data-inc="<?= isset($dayInc[$date]) ? 1 : 0 ?>"
                aria-label="<?= $date ?>">
                <?= $d ?>
                <?php if ($st): ?><span class="as-st-dot"></span><?php endif; ?>
            </button>
        <?php endfor; ?>
    </div>
</div>

<!-- Historial -->
<div class="as-list-card">
    <div class="as-list-head">
        <span class="material-symbols-outlined">history</span>
        <h3>Historial del mes</h3>
    </div>
    <div>
        <?php if (empty($misRegistros ?? [])): ?>
            <div class="as-empty">
                <span class="material-symbols-outlined">inbox</span>
                Aún no hay registros este mes. Marca tu asistencia desde Mi Panel.
            </div>
        <?php else: ?>
            <?php foreach ($misRegistros as $r):
                $st = $r['status'] ?? 'absent';
                $ic = match($st) { 'present' => 'check_circle', 'late' => 'schedule', 'absent' => 'cancel', 'no_exit' => 'logout', default => 'help' };
                $stCls = match($st) { 'present' => 'ok', 'late' => 'warn', 'absent' => 'err', default => 'warn' };
                $label = match($st) { 'present' => 'Puntual', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', default => ucfirst($st) };
                $ts = strtotime($r['date'] ?? 'now');
                $isToday = ($r['date'] ?? '') === $hoy;
            ?>
                <div class="as-row">
                    <div class="as-datebox <?= $isToday ? 'todaybox' : '' ?>">
                        <b><?= date('d', $ts) ?></b>
                        <span><?= esc($mesShort[$month]) ?></span>
                    </div>
                    <div class="as-main">
                        <div class="as-status <?= $stCls ?>">
                            <span class="material-symbols-outlined"><?= $ic ?></span>
                            <span class="st-tag"><?= $label ?></span>
                            <?php if ($isToday): ?><span class="as-hoy">Hoy</span><?php endif; ?>
                        </div>
                        <div class="as-meta">
                            <span><?= esc(ucfirst($dowLargo[(int) date('N', $ts)])) ?>, <?= (int) date('d', $ts) ?></span>
                            <?php if (!empty($r['evidencia'])): ?>
                                <span class="bio-ic"><span class="material-symbols-outlined" title="Verificación biométrica">fingerprint</span> biometría</span>
                            <?php endif; ?>
                            <?php if (!empty($r['observacion'])): ?>
                                <span>· <?= esc($r['observacion']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="as-times">
                        <?php if (!empty($r['time_in'])): ?><span class="as-time"><?= esc(substr($r['time_in'], 0, 5)) ?></span><span class="as-arrow">→</span><?php endif; ?>
                        <span class="as-time out"><?= esc(!empty($r['time_out']) ? substr($r['time_out'], 0, 5) : '--:--') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal · detalle del día -->
<div class="dm-overlay" id="dayModal">
    <div class="dm">
        <div class="dm-head">
            <div class="dm-head-in">
                <div class="dm-head-ic" id="dmIcon"><span class="material-symbols-outlined">event</span></div>
                <div>
                    <h3 id="dmTitle">Detalle del día</h3>
                    <div class="dm-date-sub" id="dmDate">—</div>
                </div>
            </div>
            <button class="dm-close" id="dmClose"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dm-body" id="dmBody"></div>
        <div class="dm-foot" id="dmFoot"></div>
    </div>
</div>

<script>
(function() {
    var modal   = document.getElementById('dayModal');
    var body    = document.getElementById('dmBody');
    var foot    = document.getElementById('dmFoot');
    var icBox   = document.getElementById('dmIcon');
    var titleEl = document.getElementById('dmTitle');
    var dateEl  = document.getElementById('dmDate');
    var today   = '<?= date('Y-m-d') ?>';
    var incUrl  = '<?= site_url('mi-panel/incidencias') ?>';
    var panelUrl = '<?= site_url('mi-panel') ?>';

    var CFG = {
        present: { icon: 'check_circle',  cls: 'ic-present', label: 'Puntual',      badge: 'dev-badge-green',
                   txt: 'Registraste tu asistencia a tiempo.' },
        late:    { icon: 'schedule',      cls: 'ic-late',    label: 'Tardanza',     badge: 'dev-badge-amber',
                   txt: 'Marcaste después del rango permitido.' },
        absent:  { icon: 'cancel',        cls: 'ic-absent',  label: 'Falta',        badge: 'dev-badge-red',
                   txt: 'No registraste asistencia este día.' }
    };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function fmtFecha(d) {
        var dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
        var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        return dias[d.getDay()] + ', ' + d.getDate() + ' de ' + meses[d.getMonth()] + ' de ' + d.getFullYear();
    }

    function abrir(cell) {
        var st  = cell.dataset.st || '';
        var date = cell.dataset.date;
        var d = date ? new Date(date + 'T00:00:00') : new Date();
        dateEl.textContent = fmtFecha(d);

        var html, footHtml = '';
        if (!st) {
            icBox.className = 'dm-head-ic ic-none';
            icBox.querySelector('.material-symbols-outlined').textContent = 'event_busy';
            titleEl.textContent = 'Sin registro';
            html = '<div class="dm-empty">'
                 + '<span class="material-symbols-outlined">event_busy</span>'
                 + '<p>No registraste asistencia este día.</p>'
                 + (date === today ? '<small>Puedes marcar tu asistencia desde Mi Panel.</small>' : '')
                 + '</div>';
            if (date === today) {
                footHtml = '<a class="dev-btn dev-btn-primary" href="' + panelUrl + '">'
                         + '<span class="material-symbols-outlined">login</span> Registrar asistencia</a>';
            }
        } else {
            var c = CFG[st] || CFG.absent;
            icBox.className = 'dm-head-ic ' + c.cls;
            icBox.querySelector('.material-symbols-outlined').textContent = c.icon;
            titleEl.textContent = c.label;
            html = '<div class="dm-badge-row">'
                 + '<span class="dev-badge ' + c.badge + '">' + c.label + '</span>'
                 + '<span class="dm-txt">' + c.txt + '</span>'
                 + '</div>'
                 + '<div class="dm-times">'
                 + '<div class="dm-cell' + (cell.dataset.in ? '' : ' off') + '"><span>Entrada</span><b>' + esc(cell.dataset.in || '--:--') + '</b></div>'
                 + '<div class="dm-cell' + (cell.dataset.out ? '' : ' off') + '"><span>Salida</span><b>' + esc(cell.dataset.out || '--:--') + '</b></div>'
                 + '</div>'
                 + '<div class="dm-tags">'
                 + (cell.dataset.bio === '1' ? '<span class="dm-tag"><span class="material-symbols-outlined">fingerprint</span> Verificada con biometría</span>' : '')
                 + (cell.dataset.obs ? '<span class="dm-tag"><span class="material-symbols-outlined">sticky_note_2</span> ' + esc(cell.dataset.obs) + '</span>' : '')
                 + '</div>';
            if ((st === 'late' || st === 'absent') && cell.dataset.inc === '1') {
                html += '<div class="dm-note warn-inc">'
                      + '<span class="material-symbols-outlined">warning</span>'
                      + '<div>Se generó una incidencia por esta situación. Puedes <a href="' + incUrl + '">verla y justificarla</a> desde Mis Incidencias.</div>'
                      + '</div>';
            } else if (st === 'late' || st === 'absent') {
                html += '<div class="dm-note plain">'
                      + '<span class="material-symbols-outlined">info</span>'
                      + '<div>Si consideras que hay una razón, puedes registrarla desde <a href="' + incUrl + '">Mis Incidencias</a>.</div>'
                      + '</div>';
            }
        }
        body.innerHTML = html;
        foot.innerHTML = footHtml || '<button type="button" class="dev-btn" data-close-dm>Cerrar</button>';
        modal.classList.add('is-open');
    }

    function cerrar() { modal.classList.remove('is-open'); }

    document.querySelector('.as-cal').addEventListener('click', function(e) {
        var cell = e.target.closest('.js-cal-day');
        if (cell) abrir(cell);
    });
    document.getElementById('dmClose').addEventListener('click', cerrar);
    foot.addEventListener('click', function(e) {
        if (e.target.closest('[data-close-dm]')) cerrar();
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) cerrar();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) cerrar();
    });
})();
</script>