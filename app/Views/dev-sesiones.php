<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesiones · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <style>
        .page-content { max-width: 960px; }

        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; padding: 16px;
            display: flex; align-items: center; gap: 14px; transition: border-color 200ms;
        }
        .stat-card:hover { border-color: var(--g-primary); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-icon .material-symbols-outlined { font-size: 22px; }
        .si-amber { background: var(--g-warning-light); color: #e37400; }
        .si-green { background: var(--g-success-light); color: var(--g-success); }
        .si-accent { background: var(--g-primary-light); color: var(--g-primary); }
        .stat-label { font-size: 12px; color: var(--g-text-secondary); }
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); color: var(--g-text); letter-spacing: -0.02em; }

        .card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px;
            overflow: hidden; margin-bottom: 20px;
        }
        .card-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px;
        }
        .card-head h2 {
            font-size: 16px; font-weight: 500; font-family: var(--g-font-display);
            display: flex; align-items: center; gap: 10px;
        }
        .card-head h2 .material-symbols-outlined { font-size: 20px; color: var(--g-text-secondary); }
        .card-head .card-sub { font-size: 12px; color: var(--g-text-secondary); }
        .card-body { padding: 20px; }

        .filter-bar { display: grid; grid-template-columns: 1fr 180px auto auto; gap: 12px; align-items: end; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 6px; }
        .field input, .field select {
            width: 100%; height: 44px; padding: 0 14px;
            border: 1px solid var(--g-border); border-radius: 12px;
            font-size: 14px; font-family: var(--g-font); color: var(--g-text);
            background: var(--g-surface); transition: border-color 200ms;
        }
        .field input:focus, .field select:focus { outline: none; border-color: var(--g-primary); }

        .dev-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            height: 44px; padding: 0 20px; border-radius: 12px; border: 1px solid var(--g-border);
            background: var(--g-surface); color: var(--g-text); font-size: 14px; font-weight: 500;
            font-family: var(--g-font); cursor: pointer; transition: all 200ms; text-decoration: none;
        }
        .dev-btn:hover { background: var(--g-surface-variant); }
        .dev-btn-primary { background: var(--g-primary); border-color: var(--g-primary); color: #fff; }
        .dev-btn-primary:hover { filter: brightness(1.08); background: var(--g-primary); }
        .dev-btn .material-symbols-outlined { font-size: 18px; }

        .status { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 16px; font-size: 12px; font-weight: 500; }
        .status .material-symbols-outlined { font-size: 12px; }
        .status-in { background: var(--g-success-light); color: var(--g-success); }
        .status-out { background: var(--g-warning-light); color: #e37400; }

        .note { font-size: 12px; color: var(--g-text-secondary); margin-top: 12px; }

        .empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 48px 16px;
            color: var(--g-text-secondary); text-align: center; gap: 12px;
        }
        .empty .material-symbols-outlined { font-size: 48px; opacity: 0.2; }
        .empty span { font-size: 14px; }

        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            .filter-bar { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr 1fr; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'sesiones'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Sesiones</strong></h1>
                    <p>Historial de entradas y salidas de todos los usuarios.</p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <section class="stats">
                <div class="stat-card">
                    <div class="stat-icon si-green"><span class="material-symbols-outlined filled">login</span></div>
                    <div>
                        <div class="stat-label">Entradas</div>
                        <div class="stat-value"><?= (int) ($totales['login'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-amber"><span class="material-symbols-outlined filled">logout</span></div>
                    <div>
                        <div class="stat-label">Salidas</div>
                        <div class="stat-value"><?= (int) ($totales['logout'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-accent"><span class="material-symbols-outlined filled">group</span></div>
                    <div>
                        <div class="stat-label">Usuarios distintos</div>
                        <div class="stat-value"><?= (int) ($totales['usuarios'] ?? 0) ?></div>
                    </div>
                </div>
            </section>

            <div class="card">
                <div class="card-head">
                    <h2><span class="material-symbols-outlined">filter_alt</span> Filtrar</h2>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= site_url('dios/sesiones') ?>" class="filter-bar">
                        <div class="field">
                            <label>Buscar (nombre o IP)</label>
                            <input type="text" name="q" value="<?= esc($fQ ?? '') ?>" placeholder="Ej: Ana, 192.168…">
                        </div>
                        <div class="field">
                            <label>Evento</label>
                            <select name="evento">
                                <option value="" <?= ($fEvento ?? '') === '' ? 'selected' : '' ?>>Todos</option>
                                <option value="login" <?= ($fEvento ?? '') === 'login' ? 'selected' : '' ?>>Entradas</option>
                                <option value="logout" <?= ($fEvento ?? '') === 'logout' ? 'selected' : '' ?>>Salidas</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>&nbsp;</label>
                            <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">search</span> Aplicar</button>
                        </div>
                        <div class="field">
                            <label>&nbsp;</label>
                            <a href="<?= site_url('dios/sesiones') ?>" class="dev-btn"><span class="material-symbols-outlined">close</span> Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($registros)): ?>
                <div class="card">
                    <div class="empty">
                        <span class="material-symbols-outlined">history</span>
                        <span><?= ($fQ ?? '') !== '' || ($fEvento ?? '') !== '' ? 'Sin resultados para ese filtro' : 'Aún no hay sesiones registradas' ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="dtable-wrap" id="sesionesTable"></div>
                <div class="note">Se muestran los últimos <?= count($registros) ?> registros<?= count($registros) >= 300 ? ' (máximo 300)' : '' ?>. El registro empezó el <?= esc(substr($registros[count($registros) - 1]['creado'] ?? '-', 0, 10)) ?>.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($registros)): ?>
<script src="<?= base_url('js/dev-table.js') ?>"></script>
<script>
var sesionesData = [
    <?php foreach ($registros as $r): ?>
    {
        fecha: '<?= esc(substr($r['creado'] ?? '', 0, 10)) ?>',
        hora: '<?= esc(substr($r['creado'] ?? '', 11, 8)) ?>',
        name: '<?= addslashes(esc($r['name'] ?? '')) ?>',
        role: '<?= addslashes(esc($r['role'] ?? '')) ?>',
        evento: '<?= esc($r['evento'] ?? '') ?>',
        ip: '<?= esc($r['ip'] ?? '') ?>'
    },
    <?php endforeach; ?>
];

new DevTable({
    containerId: 'sesionesTable',
    data: sesionesData,
    rowsPerPage: 10,
    columns: [
        { key: 'fecha', label: 'Fecha' },
        { key: 'hora', label: 'Hora' },
        { key: 'name', label: 'Usuario', render: function(row) {
            return '<strong style="font-weight:500;">' + row.name + '</strong>';
        }},
        { key: 'role', label: 'Rol', render: function(row) {
            return '<span style="font-size:13px;color:var(--g-text-secondary);">' + (row.role || '-') + '</span>';
        }},
        { key: 'evento', label: 'Evento', render: function(row) {
            return row.evento === 'login'
                ? '<span class="status status-in">Entrada</span>'
                : '<span class="status status-out">Salida</span>';
        }},
        { key: 'ip', label: 'IP', render: function(row) {
            return '<span style="font-size:13px;font-family:monospace;">' + (row.ip || '-') + '</span>';
        }}
    ]
});
</script>
<?php endif; ?>
</body>
</html>
