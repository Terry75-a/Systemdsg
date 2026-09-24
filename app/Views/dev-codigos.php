<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codigos · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=20260922') ?>">
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
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); letter-spacing: -0.02em; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; overflow: hidden;
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
        .card-body { padding: 20px; }

        .form-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 6px; }
        .field input, .field select {
            width: 100%; height: 44px; padding: 0 14px;
            border: 1px solid var(--g-border); border-radius: 12px;
            font-size: 14px; font-family: var(--g-font); color: var(--g-text);
            background: var(--g-surface); transition: border-color 200ms;
        }
        .field input:focus, .field select:focus { outline: none; border-color: var(--g-primary); }
        .field input::placeholder { color: var(--g-text-disabled); }

        .status { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 16px; font-size: 12px; font-weight: 500; }
        .status .material-symbols-outlined { font-size: 12px; }
        .status-active { background: var(--g-success-light); color: var(--g-success); }
        .status-used { background: var(--g-surface-variant); color: var(--g-text-secondary); }

        .recent-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px;
        }
        .recent-code {
            background: var(--g-surface-variant); padding: 4px 10px; border-radius: 8px;
            font-size: 12px; font-family: 'SF Mono', Monaco, monospace;
            color: var(--g-text); font-weight: 500; cursor: pointer;
            transition: background 200ms; white-space: nowrap;
        }
        .recent-code:hover { background: var(--g-border); }
        .recent-info { flex: 1; min-width: 0; }
        .recent-name { font-size: 14px; font-weight: 500; }
        .recent-meta { font-size: 12px; color: var(--g-text-secondary); }

        .empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 48px 16px;
            color: var(--g-text-secondary); text-align: center; gap: 12px;
        }
        .empty .material-symbols-outlined { font-size: 48px; opacity: 0.2; }
        .empty span { font-size: 14px; }

        .alert {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 12px;
            font-size: 14px; margin-bottom: 16px;
        }
        .alert-success { background: var(--g-success-light); color: var(--g-success); }
        .alert .material-symbols-outlined { font-size: 20px; }

        .mobile-cards { display: none; flex-direction: column; gap: 12px; }
        .code-card {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 16px; padding: 16px;
        }
        .code-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .code-card-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .code-card-field { display: flex; flex-direction: column; gap: 2px; }
        .code-card-label { font-size: 11px; color: var(--g-text-secondary); }
        .code-card-value { font-size: 13px; font-weight: 500; }
        .code-card-actions {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--g-border);
        }

        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .dtable-wrap { display: none; }
            .mobile-cards { display: flex !important; }
        }
        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr 1fr; }
            .stat-card:last-child { grid-column: span 2; }
        }

        /* Confirm Modal */
        .confirm-modal {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
            opacity: 0; visibility: hidden; transition: opacity 200ms, visibility 200ms;
        }
        .confirm-modal.active { opacity: 1; visibility: visible; }
        .confirm-modal-card {
            background: var(--g-surface); border-radius: 28px; width: 90%; max-width: 400px;
            padding: 32px 28px 24px; text-align: center;
            transform: scale(0.9); transition: transform 200ms;
        }
        .confirm-modal.active .confirm-modal-card { transform: scale(1); }
        .confirm-modal-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--g-error-light); color: var(--g-error);
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .confirm-modal-icon .material-symbols-outlined { font-size: 32px; }
        .confirm-modal-title {
            font-size: 20px; font-weight: 500; font-family: var(--g-font-display);
            margin-bottom: 8px;
        }
        .confirm-modal-text {
            font-size: 14px; color: var(--g-text-secondary);
            margin-bottom: 8px; line-height: 1.5;
        }
        .confirm-modal-name {
            font-size: 16px; font-weight: 600; font-family: var(--g-font-display);
            color: var(--g-text); margin-bottom: 20px;
            padding: 12px 16px; background: var(--g-surface-variant); border-radius: 12px;
        }
        .confirm-modal-actions {
            display: flex; gap: 12px;
        }
        .confirm-modal-btn {
            flex: 1; height: 48px; border: none; border-radius: 24px;
            font-family: var(--g-font); font-size: 14px; font-weight: 500;
            cursor: pointer; transition: all 200ms;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .confirm-modal-btn-cancel {
            background: var(--g-surface-variant); color: var(--g-text-secondary);
        }
        .confirm-modal-btn-cancel:hover { background: var(--g-border); color: var(--g-text); }
        .confirm-modal-btn-delete {
            background: var(--g-error); color: white;
        }
        .confirm-modal-btn-delete:hover { background: #c62828; }
        .confirm-modal-btn .material-symbols-outlined { font-size: 18px; }
    </style>
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'codigos'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Codigos</strong></h1>
                    <p>Genera y gestiona codigos para empleados.</p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="alert alert-success">
                    <span class="material-symbols-outlined filled">check_circle</span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <section class="stats">
                <div class="stat-card">
                    <div class="stat-icon si-amber"><span class="material-symbols-outlined filled">vpn_key</span></div>
                    <div>
                        <div class="stat-label">Activos</div>
                        <div class="stat-value"><?= count(array_filter($codes, fn($c) => $c['status'] === 'active')) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div>
                        <div class="stat-label">Usados</div>
                        <div class="stat-value"><?= count(array_filter($codes, fn($c) => $c['status'] === 'used')) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-accent"><span class="material-symbols-outlined filled">list</span></div>
                    <div>
                        <div class="stat-label">Total</div>
                        <div class="stat-value"><?= count($codes) ?></div>
                    </div>
                </div>
            </section>

            <div class="grid-2">
                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">add_circle</span> Generar codigo</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= site_url('dios/create-code') ?>">
                            <?= csrf_field() ?>
                            <div class="form-grid">
                                <div class="field">
                                    <label>Nombre del empleado</label>
                                    <input type="text" name="name" placeholder="Juan Perez" required>
                                </div>
                                <div class="field">
                                    <label>DNI (8 digitos)</label>
                                    <input type="text" name="dni" placeholder="12345678" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" required>
                                </div>
                                <div class="field">
                                    <label>Asignar a Admin</label>
                                    <select name="admin_id">
                                        <option value="">Sin admin asignado</option>
                                        <?php foreach ($admins as $a): ?>
                                            <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="dev-btn dev-btn-primary dev-btn-block" style="margin-top:20px;">
                                <span class="material-symbols-outlined">add</span> Generar
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">history</span> Recientes</h2>
                    </div>
                    <?php if (empty($codes)): ?>
                        <div class="empty"><span class="material-symbols-outlined">vpn_key</span><span>No hay codigos</span></div>
                    <?php else: ?>
                        <?php foreach (array_reverse(array_slice($codes, -5)) as $c): ?>
                        <div class="recent-item">
                            <code class="recent-code"><?= esc($c['code']) ?></code>
                            <div class="recent-info">
                                <div class="recent-name"><?= esc($c['name']) ?></div>
                                <div class="recent-meta">DNI <?= esc($c['dni']) ?></div>
                            </div>
                            <?php if ($c['status'] === 'active'): ?>
                                <span class="status status-active"><span class="material-symbols-outlined filled">circle</span> Activo</span>
                            <?php else: ?>
                                <span class="status status-used"><span class="material-symbols-outlined">circle</span> Usado</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dtable-wrap" id="codigosTable"></div>

            <div class="mobile-cards">
                <?php foreach (array_reverse($codes) as $c): ?>
                <?php
                    $adminName = '-';
                    foreach ($admins as $a) {
                        if ($a['id'] == ($c['admin_id'] ?? 0)) { $adminName = $a['name']; break; }
                    }
                ?>
                <div class="code-card">
                    <div class="code-card-top">
                        <code class="recent-code"><?= esc($c['code']) ?></code>
                        <?php if ($c['status'] === 'active'): ?>
                            <span class="status status-active"><span class="material-symbols-outlined filled">circle</span> Activo</span>
                        <?php else: ?>
                            <span class="status status-used"><span class="material-symbols-outlined">circle</span> Usado</span>
                        <?php endif; ?>
                    </div>
                    <div class="code-card-info">
                        <div class="code-card-field">
                            <span class="code-card-label">Nombre</span>
                            <span class="code-card-value"><?= esc($c['name']) ?></span>
                        </div>
                        <div class="code-card-field">
                            <span class="code-card-label">DNI</span>
                            <span class="code-card-value"><?= esc($c['dni']) ?></span>
                        </div>
                        <div class="code-card-field">
                            <span class="code-card-label">Admin</span>
                            <span class="code-card-value"><?= esc($adminName) ?></span>
                        </div>
                        <div class="code-card-field">
                            <span class="code-card-label">Creado</span>
                            <span class="code-card-value"><?= esc($c['created']) ?></span>
                        </div>
                    </div>
                    <?php if ($c['status'] === 'active'): ?>
                    <div class="code-card-actions">
                        <button class="dev-btn dev-btn-sm" style="background:var(--g-surface-variant);color:var(--g-text-secondary);" onclick="copyCode(this, '<?= esc($c['code']) ?>')">
                            <span class="material-symbols-outlined">content_copy</span> Copiar
                        </button>
                        <button class="dev-btn dev-btn-sm" style="background:var(--g-error-light);color:var(--g-error);" onclick="openConfirmDelete(<?= $c['id'] ?>, '<?= addslashes(esc($c['name'])) ?>', '<?= esc($c['code']) ?>')">
                            <span class="material-symbols-outlined">delete</span> Eliminar
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode(btn, code) {
    navigator.clipboard.writeText(code).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined">check</span> Copiado';
        btn.disabled = true;
        setTimeout(function() { btn.innerHTML = original; btn.disabled = false; }, 1200);
    });
}
</script>
<script src="<?= base_url('js/dev-table.js') ?>"></script>
<script>
var codigosData = [
    <?php foreach (array_reverse($codes) as $c): ?>
    <?php
        $adminName = '-';
        foreach ($admins as $a) {
            if ($a['id'] == ($c['admin_id'] ?? 0)) { $adminName = $a['name']; break; }
        }
    ?>
    {
        id: <?= $c['id'] ?>,
        code: '<?= esc($c['code']) ?>',
        name: '<?= addslashes(esc($c['name'])) ?>',
        dni: '<?= esc($c['dni']) ?>',
        admin: '<?= esc($adminName) ?>',
        status: '<?= $c['status'] ?>',
        created: '<?= esc($c['created']) ?>'
    },
    <?php endforeach; ?>
];

new DevTable({
    containerId: 'codigosTable',
    data: codigosData,
    rowsPerPage: 6,
    columns: [
        { key: 'code', label: 'Codigo', render: function(row) {
            return '<code class="recent-code">' + row.code + '</code>';
        }},
        { key: 'name', label: 'Nombre', render: function(row) {
            return '<strong style="font-weight:500;">' + row.name + '</strong>';
        }},
        { key: 'dni', label: 'DNI' },
        { key: 'admin', label: 'Admin', render: function(row) {
            return '<span style="font-size:13px;color:var(--g-text-secondary);">' + row.admin + '</span>';
        }},
        { key: 'status', label: 'Estado', render: function(row) {
            if (row.status === 'active') {
                return '<span class="status status-active"><span class="material-symbols-outlined filled" style="font-size:12px;">circle</span> Activo</span>';
            }
            return '<span class="status status-used"><span class="material-symbols-outlined" style="font-size:12px;">circle</span> Usado</span>';
        }},
        { key: 'created', label: 'Creado', render: function(row) {
            return '<span style="font-size:13px;color:var(--g-text-secondary);">' + row.created + '</span>';
        }},
        { key: 'id', label: '', render: function(row) {
            if (row.status !== 'active') return '';
            return '<button class="dev-btn-icon" title="Eliminar" onclick="openConfirmDelete(' + row.id + ', \'' + row.name.replace(/'/g, "\\'") + '\', \'' + row.code + '\')"><span class="material-symbols-outlined">delete</span></button>';
        }}
    ]
});

/* Confirm Delete Modal */
function openConfirmDelete(id, name, code) {
    document.getElementById('confirmDeleteId').value = id;
    document.getElementById('confirmDeleteName').textContent = name + ' (' + code + ')';
    document.getElementById('confirmDeleteModal').classList.add('active');
}
function closeConfirmModal() {
    document.getElementById('confirmDeleteModal').classList.remove('active');
}
function submitConfirmDelete() {
    document.getElementById('confirmDeleteForm').submit();
}
document.getElementById('confirmDeleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});
</script>

<!-- Confirm Delete Modal -->
<div class="confirm-modal" id="confirmDeleteModal">
    <div class="confirm-modal-card">
        <div class="confirm-modal-icon">
            <span class="material-symbols-outlined">delete</span>
        </div>
        <div class="confirm-modal-title">Eliminar codigo</div>
        <div class="confirm-modal-text">Estas seguro que deseas eliminar este codigo? Esta accion no se puede deshacer.</div>
        <div class="confirm-modal-name" id="confirmDeleteName">-</div>
        <form id="confirmDeleteForm" method="POST" action="<?= site_url('dios/delete-code') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="code_id" id="confirmDeleteId" value="">
            <div class="confirm-modal-actions">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-cancel" onclick="closeConfirmModal()">
                    <span class="material-symbols-outlined">close</span> Cancelar
                </button>
                <button type="button" class="confirm-modal-btn confirm-modal-btn-delete" onclick="submitConfirmDelete()">
                    <span class="material-symbols-outlined">delete</span> Eliminar
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
