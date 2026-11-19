<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <style>
        .page-content { max-width: 1100px; }

        .aud-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .aud-stat {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px;
            padding: 16px; display: flex; align-items: center; gap: 14px;
        }
        .aud-stat .stat-icon {
            width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .aud-stat b { display: block; font-size: 24px; font-weight: 600; font-family: var(--g-font-display); }
        .aud-stat span { font-size: 12px; color: var(--g-text-secondary); }

        .aud-note {
            display: flex; gap: 12px; align-items: flex-start;
            background: var(--g-primary-light); border: 1px solid var(--g-border);
            border-radius: 14px; padding: 14px 16px; margin-top: 20px;
            font-size: 13px; color: var(--g-text-secondary); line-height: 1.55;
        }
        .aud-note .material-symbols-outlined { color: var(--g-primary); font-size: 20px; }

        .aud-change { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .aud-change code {
            background: var(--g-surface-variant); border: 1px solid var(--g-border);
            padding: 3px 8px; border-radius: 7px; font-size: 11.5px; font-family: monospace;
            max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .aud-change code.new { background: var(--g-success-light); border-color: transparent; color: var(--g-success); font-weight: 600; }
        .aud-change .material-symbols-outlined { font-size: 15px; color: var(--g-text-disabled); }
        .aud-empty { color: var(--g-text-disabled); font-style: italic; }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/lumen.css?v=' . filemtime(FCPATH . 'css/index/components/lumen.css')) ?>">
    <script src="<?= base_url('js/lumen-mobile.js?v=' . filemtime(FCPATH . 'js/lumen-mobile.js')) ?>" defer></script>
</head>
<body>
<div class="dev-layout">
    <?= view('partials/dev-sidebar', ['activePage' => 'auditoria']) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Auditoría</strong> de asistencia</h1>
                    <p>Todas las ediciones manuales sobre marcas, con autor, motivo y valores. <span class="aud-live" id="aud-live" title="Se actualiza sola cada 10 segundos"><i></i> En vivo</span></p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <div class="aud-stats">
                <div class="aud-stat">
                    <div class="stat-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <div><b id="aud-total"><?= (int) $total ?></b><span>cambios registrados</span></div>
                </div>
                <div class="aud-stat">
                    <div class="stat-icon" style="background:var(--g-warning-light);color:#e37400;">
                        <span class="material-symbols-outlined">edit_document</span>
                    </div>
                    <div><b id="aud-ediciones"><?= count(array_filter($filas, static fn ($f) => $f['accion'] === 'editar')) ?></b><span>ediciones</span></div>
                </div>
                <div class="aud-stat">
                    <div class="stat-icon" style="background:var(--g-success-light);color:var(--g-success);">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>
                    <div><b id="aud-personas"><?= count(array_unique(array_filter(array_column($filas, 'empleado')))) ?></b><span>personas afectadas</span></div>
                </div>
            </div>

            <div class="dtable-wrap" id="auditoriaTable"></div>

            <div class="aud-note">
                <span class="material-symbols-outlined">shield</span>
                <div>
                    Cada modificación de una marcación desde <strong>/admin/asistencias</strong> deja un registro aquí y en
                    <strong>Admin · Historial</strong>. Los registros no se pueden editar ni borrar desde la interfaz.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/dev-table.js') ?>"></script>
<script>
var auditoriaData = <?= json_encode($filas, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

var auditoriaTabla = new DevTable({
    containerId: 'auditoriaTable',
    data: auditoriaData,
    rowsPerPage: 8,
    columns: [
        { key: 'fecha', label: 'Marca', render: function(row) {
            if (!row.fecha) return '<span class="aud-empty">—</span>';
            return '<div style="line-height:1.35;"><strong style="font-weight:500;">' + row.fecha + '</strong>'
                 + (row.marca ? '<br><span style="font-size:11.5px;color:var(--g-text-secondary);">' + row.marca + '</span>' : '')
                 + '</div>';
        }},
        { key: 'empleado', label: 'Empleado', render: function(row) {
            return row.empleado ? '<strong style="font-weight:500;">' + row.empleado + '</strong>' : '<span class="aud-empty">—</span>';
        }},
        { key: 'campo', label: 'Campo', render: function(row) {
            var etiqueta = row.campo || row.accion;
            return '<span class="badge badge-green" style="text-transform:capitalize;">' + etiqueta + '</span>'
                 + '<div style="font-size:11px;color:var(--g-text-secondary);margin-top:4px;text-transform:capitalize;">' + row.accion + '</div>';
        }},
        { key: 'cambio', label: 'Cambio', render: function(row) {
            var antes = row.anterior === '' ? '—' : row.anterior;
            var despues = row.nuevo === '' ? '—' : row.nuevo;
            return '<div class="aud-change"><code>' + antes + '</code>'
                 + '<span class="material-symbols-outlined">arrow_forward</span>'
                 + '<code class="new">' + despues + '</code></div>';
        }},
        { key: 'motivo', label: 'Motivo', render: function(row) {
            return row.motivo ? row.motivo : '<span class="aud-empty">sin motivo</span>';
        }},
        { key: 'autor', label: 'Autor', render: function(row) {
            return row.autor || '<span class="aud-empty">—</span>';
        }},
        { key: 'cuando', label: 'Cuándo', render: function(row) {
            return '<span style="font-size:12px;color:var(--g-text-secondary);">' + (row.cuando || '—') + '</span>';
        }}
    ]
});

// En vivo: cada 10 s pregunta si hay cambios nuevos y redibuja la tabla
(function () {
    var ultimoId = <?= (int) ($ultimoId ?? 0) ?>;
    var live = document.getElementById('aud-live');
    function contar(filas) {
        var ed = 0, per = {};
        filas.forEach(function (f) { if (f.accion === 'editar') ed++; if (f.empleado) per[f.empleado] = 1; });
        document.getElementById('aud-total').textContent = filas.length;
        document.getElementById('aud-ediciones').textContent = ed;
        document.getElementById('aud-personas').textContent = Object.keys(per).length;
    }
    function revisar() {
        if (document.hidden) return;
        fetch('<?= site_url('dios/auditoria/datos') ?>?desde=' + ultimoId, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { if (!r.ok) throw r; return r.json(); })
            .then(function (d) {
                live.classList.remove('off');
                if (!d.cambios) return;
                ultimoId = d.ultimoId;
                auditoriaTabla.data = d.filas;
                auditoriaTabla.totalPages = Math.ceil(d.filas.length / auditoriaTabla.rowsPerPage);
                auditoriaTabla.currentPage = 1;
                auditoriaTabla.render();
                contar(d.filas);
                live.classList.add('flash');
                setTimeout(function () { live.classList.remove('flash'); }, 1500);
            })
            .catch(function () { live.classList.add('off'); });
    }
    setInterval(revisar, 10000);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) revisar(); });
})();
</script>
<style>
.aud-live { display: inline-flex; align-items: center; gap: 6px; margin-left: 8px; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; background: var(--g-success-light); color: var(--g-success); vertical-align: middle; transition: box-shadow .3s; }
.aud-live i { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: audPulso 1.6s ease-in-out infinite; }
.aud-live.off { background: var(--g-error-light); color: var(--g-error); }
.aud-live.flash { box-shadow: 0 0 0 4px var(--g-success-light); }
@keyframes audPulso { 50% { opacity: .3; } }
@media (prefers-reduced-motion: reduce) { .aud-live i { animation: none; } }
</style>
</body>
</html>
