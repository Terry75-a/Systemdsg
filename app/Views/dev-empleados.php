<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados · Dev Panel</title>
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

        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
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
        .si-accent { background: var(--g-primary-light); color: var(--g-primary); }
        .si-green { background: var(--g-success-light); color: var(--g-success); }
        .stat-label { font-size: 12px; color: var(--g-text-secondary); }
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); letter-spacing: -0.02em; }

        .badge { display: inline-block; font-size: 11px; font-weight: 500; padding: 4px 10px; border-radius: 16px; }
        .badge-green { background: var(--g-success-light); color: var(--g-success); }
        .badge-accent { background: var(--g-primary-light); color: var(--g-primary); }
        .badge-dim { background: var(--g-surface-variant); color: var(--g-text-secondary); }

        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 500; flex-shrink: 0;
        }
        .avatar-green { background: var(--g-primary-light); color: var(--g-primary); }

        .alert {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 12px;
            font-size: 14px; margin-bottom: 16px;
        }
        .alert-success { background: var(--g-success-light); color: var(--g-success); }
        .alert .material-symbols-outlined { font-size: 20px; }

        .mobile-cards { display: none; flex-direction: column; gap: 12px; }
        .emp-card {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 16px; padding: 16px;
        }
        .emp-card-top { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .emp-card-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .emp-card-field { display: flex; flex-direction: column; gap: 2px; }
        .emp-card-label { font-size: 11px; color: var(--g-text-secondary); }
        .emp-card-value { font-size: 13px; font-weight: 500; }
        .emp-card-actions {
            display: flex; justify-content: flex-end; margin-top: 12px;
            padding-top: 12px; border-top: 1px solid var(--g-border);
        }

        @media (max-width: 768px) {
            .stats { gap: 10px; }
            .dtable-wrap { display: none; }
            .mobile-cards { display: flex !important; }
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
    <?php $activePage = 'empleados'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Empleados</strong></h1>
                    <p>Todos los empleados registrados en el sistema.</p>
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
                    <div class="stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div>
                        <div class="stat-label">Total empleados</div>
                        <div class="stat-value"><?= count($empleados) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div>
                        <div class="stat-label">Con sesion</div>
                        <div class="stat-value"><?= count(array_filter($empleados, fn($e) => !empty($e['last_login']))) ?></div>
                    </div>
                </div>
            </section>

            <div class="dtable-wrap" id="empleadosTable"></div>

            <div class="mobile-cards">
                <?php foreach (array_reverse($empleados) as $u): ?>
                <div class="emp-card">
                    <div class="emp-card-top">
                        <div class="avatar avatar-green"><?= strtoupper(substr($u['name'], 0, 2)) ?></div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:500;"><?= esc($u['name']) ?></div>
                            <div style="font-size:12px;color:var(--g-text-secondary);">DNI <?= esc($u['dni'] ?? '-') ?></div>
                        </div>
                        <span class="badge badge-green"><?= esc($u['role']) ?></span>
                    </div>
                    <div class="emp-card-info">
                        <div class="emp-card-field">
                            <span class="emp-card-label">Creado</span>
                            <span class="emp-card-value"><?= esc($u['created']) ?></span>
                        </div>
                        <div class="emp-card-field">
                            <span class="emp-card-label">Ultimo login</span>
                            <span class="emp-card-value"><?= esc($u['last_login'] ?? 'Nunca') ?></span>
                        </div>
                    </div>
                    <div class="emp-card-actions">
                        <button type="button" class="dev-btn dev-btn-sm" style="background:var(--g-error-light);color:var(--g-error);" onclick="openConfirmDelete(<?= $u['id'] ?>, '<?= addslashes(esc($u['name'])) ?>')">
                            <span class="material-symbols-outlined">delete</span> Eliminar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/dev-table.js') ?>"></script>
<script>
var empleadosData = [
    <?php foreach (array_reverse($empleados) as $u): ?>
    {
        id: <?= $u['id'] ?>,
        name: '<?= addslashes(esc($u['name'])) ?>',
        dni: '<?= esc($u['dni'] ?? '-') ?>',
        role: '<?= esc($u['role']) ?>',
        created: '<?= esc($u['created']) ?>',
        lastLogin: '<?= esc($u['last_login'] ?? 'Nunca') ?>'
    },
    <?php endforeach; ?>
];

new DevTable({
    containerId: 'empleadosTable',
    data: empleadosData,
    rowsPerPage: 6,
    columns: [
        { key: 'name', label: 'Nombre', render: function(row) {
            return '<div style="display:flex;align-items:center;gap:10px;">' +
                '<div class="avatar avatar-green">' + row.name.substring(0,2).toUpperCase() + '</div>' +
                '<strong style="font-weight:500;">' + row.name + '</strong></div>';
        }},
        { key: 'dni', label: 'DNI', render: function(row) {
            return '<code style="background:var(--g-surface-variant);padding:4px 10px;border-radius:8px;font-size:12px;font-family:monospace;">' + row.dni + '</code>';
        }},
        { key: 'role', label: 'Rol', render: function(row) {
            return '<span class="badge badge-green">' + row.role + '</span>';
        }},
        { key: 'created', label: 'Creado' },
        { key: 'lastLogin', label: 'Ultimo login', render: function(row) {
            if (row.lastLogin === 'Nunca') {
                return '<span class="badge badge-dim">Nunca</span>';
            }
            return '<span style="font-size:13px;">' + row.lastLogin + '</span>';
        }},
        { key: 'id', label: '', render: function(row) {
            return '<button class="dev-btn-icon" title="Eliminar" onclick="openConfirmDelete(' + row.id + ', \'' + row.name.replace(/'/g, "\\'") + '\')"><span class="material-symbols-outlined">delete</span></button>';
        }}
    ]
});

/* Confirm Delete Modal */
function openConfirmDelete(id, name) {
    document.getElementById('confirmDeleteId').value = id;
    document.getElementById('confirmDeleteName').textContent = name;
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
        <div class="confirm-modal-title">Eliminar empleado</div>
        <div class="confirm-modal-text">Estas seguro que deseas eliminar a este empleado? Esta accion no se puede deshacer.</div>
        <div class="confirm-modal-name" id="confirmDeleteName">-</div>
        <form id="confirmDeleteForm" method="POST" action="<?= site_url('dios/delete-user') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="confirmDeleteId" value="">
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
