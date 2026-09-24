<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administradores · Dev Panel</title>
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

        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-box {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; padding: 16px;
            display: flex; align-items: center; gap: 14px; transition: border-color 200ms;
        }
        .stat-box:hover { border-color: var(--g-primary); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-icon .material-symbols-outlined { font-size: 22px; }
        .stat-info { flex: 1; }
        .stat-label { font-size: 12px; color: var(--g-text-secondary); }
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); letter-spacing: -0.02em; }

        .toolbar {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .search-box {
            flex: 1; position: relative;
        }
        .search-box input {
            width: 100%; height: 44px; padding: 0 14px 0 44px;
            border: 1px solid var(--g-border); border-radius: 12px;
            font-size: 14px; font-family: var(--g-font); color: var(--g-text);
            background: var(--g-surface); transition: border-color 200ms;
        }
        .search-box input:focus { outline: none; border-color: var(--g-primary); }
        .search-box input::placeholder { color: var(--g-text-disabled); }
        .search-box .material-symbols-outlined {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 20px; color: var(--g-text-disabled); pointer-events: none;
        }

        .dtable-wrap { border-radius: 16px; overflow: hidden; background: var(--g-surface); border: 1px solid var(--g-border); }

        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.45);
            display: flex; align-items: center; justify-content: center; z-index: 1000;
            padding: 20px;
            opacity: 0; visibility: hidden; transition: opacity 180ms, visibility 180ms;
        }
        .modal-overlay.is-open { opacity: 1; visibility: visible; }
        .modal {
            background: var(--g-surface); border-radius: 16px; width: 90%; max-width: 440px;
            max-height: 88vh; display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 12px 32px -8px rgba(0,0,0,0.18); transform: scale(0.96);
            transition: transform 180ms;
        }
        .modal-overlay.is-open .modal { transform: scale(1); }
        .modal-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 22px 14px; flex-shrink: 0;
        }
        .modal-head h3 {
            font-size: 17px; font-weight: 600; font-family: var(--g-font);
            display: flex; align-items: center; gap: 10px; margin: 0;
        }
        .modal-head h3 .material-symbols-outlined { font-size: 20px; color: var(--g-text-secondary); }
        .modal-close {
            width: 30px; height: 30px; border: none; border-radius: 8px;
            background: transparent; color: var(--g-text-secondary); cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: background 150ms;
            flex-shrink: 0;
        }
        .modal-close:hover { background: var(--g-surface-variant); }
        .modal-body { padding: 0 22px 18px; overflow-y: auto; }
        .modal-footer {
            display: flex; justify-content: flex-end; gap: 8px; padding: 14px 22px 20px;
            border-top: 1px solid var(--g-border); flex-shrink: 0;
        }

        /* Credenciales: lista simple, minimal */
        .creds-person { padding: 4px 0 14px; border-bottom: 1px solid var(--g-border); }
        .creds-person strong { display: block; font-size: 15px; font-weight: 600; }
        .creds-person span { font-size: 13px; color: var(--g-text-secondary); }
        .creds-list { padding: 6px 0; }
        .creds-row {
            display: flex; align-items: center; justify-content: space-between; gap: 14px;
            padding: 10px 0; border-bottom: 1px solid var(--g-border);
        }
        .creds-row:last-child { border-bottom: none; }
        .creds-row > span:first-child { font-size: 13px; color: var(--g-text-secondary); white-space: nowrap; }
        .creds-val { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .creds-val code, .creds-val span { font-family: monospace; font-size: 13.5px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .creds-copy {
            width: 28px; height: 28px; border: none; border-radius: 6px;
            background: transparent; color: var(--g-text-disabled); cursor: pointer;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: color 150ms, background 150ms;
        }
        .creds-copy:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .creds-copy .material-symbols-outlined { font-size: 16px; }
        .creds-hint { font-size: 12.5px; color: var(--g-text-secondary); margin: 14px 0 0; line-height: 1.5; }
        .form-stack { display: flex; flex-direction: column; gap: 16px; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 6px; }
        .field input {
            width: 100%; height: 44px; padding: 0 14px;
            border: 1px solid var(--g-border); border-radius: 12px;
            font-size: 14px; font-family: var(--g-font); color: var(--g-text);
            background: var(--g-surface); transition: border-color 200ms;
        }
        .field input:focus { outline: none; border-color: var(--g-primary); }
        .field input::placeholder { color: var(--g-text-disabled); }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field-pass { position: relative; }
        .field-pass input { padding-right: 44px; }
        .field-pass-toggle {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            width: 32px; height: 32px; border: none; border-radius: 8px;
            background: transparent; color: var(--g-text-secondary); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .field-pass-toggle:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .field-pass-toggle .material-symbols-outlined { font-size: 20px; }
        .modal-footer {
            display: flex; justify-content: flex-end; gap: 8px; padding: 16px 24px 24px;
        }

        .mobile-cards { display: none; flex-direction: column; gap: 12px; }
        .m-card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; padding: 16px;
        }
        .m-card-top { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .m-card-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--g-primary-light); color: var(--g-primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 500; flex-shrink: 0;
        }
        .m-card-name { font-size: 14px; font-weight: 500; }
        .m-card-dni { font-size: 12px; color: var(--g-text-secondary); }
        .m-card-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
        .m-card-field { display: flex; flex-direction: column; gap: 2px; }
        .m-card-label { font-size: 11px; color: var(--g-text-secondary); }
        .m-card-value { font-size: 13px; font-weight: 500; }
        .m-card-actions { display: flex; gap: 8px; justify-content: flex-end; }

        .alert {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 12px; font-size: 14px; margin-bottom: 16px;
        }
        .alert-success { background: var(--g-success-light); color: var(--g-success); }
        .alert-error { background: var(--g-error-light); color: var(--g-error); }
        .alert .material-symbols-outlined { font-size: 20px; }

        @media (max-width: 768px) {
            .stats-row { grid-template-columns: 1fr; }
            .toolbar { flex-direction: column; }
            .toolbar .btn { width: 100%; justify-content: center; }
            .field-row { grid-template-columns: 1fr; }
            .dtable-wrap { display: none; }
            .mobile-cards { display: flex !important; }

            /* Modal → bottom sheet en móvil */
            .modal-overlay { padding: 0; align-items: flex-end; }
            .modal {
                width: 100%; max-width: 100%; max-height: 92vh;
                border-radius: 16px 16px 0 0;
            }
            .modal-head { padding: 18px 18px 12px; }
            .modal-body { padding: 0 18px 16px; }
            .modal-footer { padding: 12px 18px 20px; flex-wrap: wrap; }
            .modal-footer .dev-btn { flex: 1; justify-content: center; }
            .creds-row { font-size: 13px; }
        }

        @media (max-width: 420px) {
            .creds-copy { color: var(--g-text-secondary); }
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
    <?php $activePage = 'admins'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Administradores</strong></h1>
                    <p>Gestiona los admins del sistema.</p>
                </div>
                <button class="dev-btn dev-btn-primary" onclick="openModal()">
                    <span class="material-symbols-outlined">add</span> Crear admin
                </button>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="alert alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                        <span class="material-symbols-outlined filled">admin_panel_settings</span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total admins</div>
                        <div class="stat-value"><?= count($admins) ?></div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background:var(--g-success-light);color:var(--g-success);">
                        <span class="material-symbols-outlined filled">login</span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Con sesion</div>
                        <div class="stat-value"><?= count(array_filter($admins, fn($a) => !empty($a['last_login']))) ?></div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background:var(--g-warning-light);color:#e37400;">
                        <span class="material-symbols-outlined filled">schedule</span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Sin sesion</div>
                        <div class="stat-value"><?= count(array_filter($admins, fn($a) => empty($a['last_login']))) ?></div>
                    </div>
                </div>
            </div>

            <div class="toolbar">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre, DNI o email..." oninput="filterTable()">
                </div>
            </div>

            <div class="dtable-wrap" id="adminsTable"></div>

            <div class="mobile-cards" id="mobileCards">
                <?php foreach (array_reverse($admins) as $u): ?>
                <div class="m-card" data-search="<?= strtolower(esc($u['name'] . ' ' . ($u['dni'] ?? '') . ' ' . ($u['email'] ?? ''))) ?>">
                    <div class="m-card-top">
                        <div class="m-card-avatar"><?= strtoupper(substr($u['name'], 0, 2)) ?></div>
                        <div>
                            <div class="m-card-name"><?= esc($u['name']) ?></div>
                            <div class="m-card-dni"><?= esc($u['admin_code'] ?? '') ?> · DNI <?= esc($u['dni'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div class="m-card-info">
                        <div class="m-card-field">
                            <span class="m-card-label">Email</span>
                            <span class="m-card-value"><?= esc($u['email'] ?: '-') ?></span>
                        </div>
                        <div class="m-card-field">
                            <span class="m-card-label">Ultimo login</span>
                            <span class="m-card-value"><?= esc($u['last_login'] ?? 'Nunca') ?></span>
                        </div>
                    </div>
                    <div class="m-card-actions">
                        <button class="dev-btn dev-btn-sm" style="background:var(--g-primary-light);color:var(--g-primary);" onclick='editAdmin(<?= json_encode($u) ?>)'>
                            <span class="material-symbols-outlined">edit</span> Editar
                        </button>
                        <button class="dev-btn dev-btn-sm" style="background:var(--g-error-light);color:var(--g-error);" onclick="openConfirmDelete(<?= $u['id'] ?>, '<?= addslashes(esc($u['name'])) ?>', 'admin')">
                            <span class="material-symbols-outlined">delete</span> Eliminar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-head">
            <h3>
                <span class="material-symbols-outlined" id="modalIcon">person_add</span>
                <span id="modalTitle">Crear admin</span>
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <form method="post" id="adminForm" action="<?= site_url('dios/create-admin') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="formUserId" value="">
                <div class="form-stack">
                    <div class="field-row">
                        <div class="field">
                        <label>Nombre y apellidos</label>
                        <input type="text" name="name" id="formName" placeholder="Carlos Ruiz" required>
                    </div>
                    <div class="field">
                        <label>DNI (8 digitos)</label>
                        <input type="text" name="dni" id="formDni" placeholder="12345678" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" required>
                    </div>
                </div>
                <div class="field">
                    <label>Email (opcional — si lo dejas vacio se genera automatico)</label>
                    <input type="email" name="email" id="formEmail" placeholder="se genera: nombre.apellido@dsg.pe">
                </div>
                <div class="field">
                    <label>Empresa</label>
                    <input type="text" name="empresa" id="formEmpresa" placeholder="Nombre de la empresa del admin">
                </div>
                <div class="field" id="passwordField">
                    <label id="passwordLabel">Nueva contrasena (opcional)</label>
                    <div class="field-pass">
                        <input type="password" name="password" id="formPassword" placeholder="Solo si deseas cambiarla">
                        <button type="button" class="field-pass-toggle" onclick="togglePassword()">
                            <span class="material-symbols-outlined" id="passIcon">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="field-note" style="font-size:12px;color:var(--g-text-secondary);background:var(--g-surface-variant);padding:10px 14px;border-radius:10px;" id="createNote">
                    La contraseña se genera automaticamente de forma segura y se te mostrara una sola vez. El código de admin tambien se genera solo (ADMIN-001, ADMIN-002...).
                </div>
            </div>
        </form>
        </div>
        <div class="modal-footer">
            <button class="dev-btn" onclick="closeModal()">Cancelar</button>
            <button class="dev-btn dev-btn-primary" onclick="submitForm()">
                <span class="material-symbols-outlined" id="submitIcon">add</span>
                <span id="submitText">Crear admin</span>
            </button>
        </div>
    </div>
</div>

<script src="<?= base_url('js/dev-table.js') ?>"></script>
<script>
var allAdmins = [
    <?php foreach (array_reverse($admins) as $u): ?>
    {
        id: <?= $u['id'] ?>,
        name: '<?= addslashes(esc($u['name'])) ?>',
        dni: '<?= esc($u['dni'] ?? '-') ?>',
        email: '<?= esc($u['email'] ?: '-') ?>',
        code: '<?= esc($u['admin_code'] ?? 'ADMIN-???') ?>',
        empresa: '<?= esc($u['empresa'] ?? '') ?>',
        empleadosCount: <?= (int) ($u['empleadosCount'] ?? 0) ?>,
        practicantesCount: <?= (int) ($u['practicantesCount'] ?? 0) ?>,
        created: '<?= esc($u['created']) ?>',
        lastLogin: '<?= esc($u['last_login'] ?? 'Nunca') ?>'
    },
    <?php endforeach; ?>
];

var table;

function initTable(data) {
    document.getElementById('adminsTable').innerHTML = '';
    table = new DevTable({
        containerId: 'adminsTable',
        data: data,
        rowsPerPage: 6,
        columns: [
            { key: 'name', label: 'Nombre', render: function(row) {
                return '<div style="display:flex;align-items:center;gap:10px;">' +
                    '<div style="width:36px;height:36px;border-radius:50%;background:var(--g-primary-light);color:var(--g-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:500;">' + row.name.substring(0,2).toUpperCase() + '</div>' +
                    '<strong style="font-weight:500;">' + row.name + '</strong></div>';
            }},
            { key: 'dni', label: 'DNI', render: function(row) {
                return '<code style="background:var(--g-surface-variant);padding:4px 10px;border-radius:8px;font-size:12px;font-family:monospace;">' + row.dni + '</code>';
            }},
            { key: 'code', label: 'Codigo', render: function(row) {
                return '<code style="background:var(--g-success-light);color:var(--g-success);padding:4px 10px;border-radius:8px;font-size:12px;font-family:monospace;font-weight:600;">' + row.code + '</code>';
            }},
            { key: 'personalCount', label: 'Personal', render: function(row) {
                return '<span style="font-size:13px;">' + row.empleadosCount + ' emp · ' + row.practicantesCount + ' pract</span>';
            }},
            { key: 'email', label: 'Email', render: function(row) {
                return '<span style="font-size:13px;color:var(--g-text-secondary);">' + row.email + '</span>';
            }},
            { key: 'lastLogin', label: 'Ultimo login', render: function(row) {
                if (row.lastLogin === 'Nunca') return '<span style="font-size:12px;color:var(--g-text-disabled);background:var(--g-surface-variant);padding:4px 10px;border-radius:16px;">Nunca</span>';
                return '<span style="font-size:13px;">' + row.lastLogin + '</span>';
            }},
            { key: 'id', label: '', render: function(row) {
                return '<div style="display:flex;gap:4px;">' +
                    '<button class="dev-btn-icon" title="Editar" onclick=\'editAdmin(' + JSON.stringify(row).replace(/'/g, "&#39;") + ')\'><span class="material-symbols-outlined">edit</span></button>' +
                    '<button class="dev-btn-icon" title="Eliminar" onclick="openConfirmDelete(' + row.id + ', \'' + row.name.replace(/'/g, "\\'") + '\', \'admin\')"><span class="material-symbols-outlined">delete</span></button>' +
                    '</div>';
            }}
        ]
    });
}

initTable(allAdmins);

function filterTable() {
    var q = document.getElementById('searchInput').value.toLowerCase().trim();
    var filtered = allAdmins.filter(function(a) {
        return (a.name + ' ' + a.dni + ' ' + a.email).toLowerCase().includes(q);
    });
    initTable(filtered);

    var cards = document.querySelectorAll('.m-card');
    cards.forEach(function(c) {
        var s = c.getAttribute('data-search') || '';
        c.style.display = s.includes(q) ? '' : 'none';
    });
}

function openModal() {
    document.getElementById('modalTitle').textContent = 'Crear admin';
    document.getElementById('modalIcon').textContent = 'person_add';
    document.getElementById('submitText').textContent = 'Crear admin';
    document.getElementById('submitIcon').textContent = 'add';
    document.getElementById('adminForm').action = '<?= site_url("dios/create-admin") ?>';
    document.getElementById('formUserId').value = '';
    document.getElementById('formName').value = '';
    document.getElementById('formDni').value = '';
    document.getElementById('formEmail').value = '';
    document.getElementById('formEmpresa').value = '';
    document.getElementById('formPassword').value = '';
    document.getElementById('formPassword').required = false;
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('createNote').style.display = 'block';
    document.getElementById('modalOverlay').classList.add('is-open');
}

function editAdmin(admin) {
    document.getElementById('modalTitle').textContent = 'Editar admin';
    document.getElementById('modalIcon').textContent = 'edit';
    document.getElementById('submitText').textContent = 'Guardar cambios';
    document.getElementById('submitIcon').textContent = 'save';
    document.getElementById('adminForm').action = '<?= site_url("dios/update-admin") ?>';
    document.getElementById('formUserId').value = admin.id;
    document.getElementById('formName').value = admin.name;
    document.getElementById('formDni').value = admin.dni;
    document.getElementById('formEmail').value = admin.email === '-' ? '' : admin.email;
    document.getElementById('formEmpresa').value = admin.empresa || '';
    document.getElementById('formPassword').value = '';
    document.getElementById('formPassword').required = false;
    document.getElementById('passwordLabel').textContent = 'Nueva contrasena (opcional)';
    document.getElementById('passwordField').style.display = '';
    document.getElementById('createNote').style.display = 'none';
    document.getElementById('modalOverlay').classList.add('is-open');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('is-open');
}

function submitForm() {
    document.getElementById('adminForm').submit();
}

function togglePassword() {
    var input = document.getElementById('formPassword');
    var icon = document.getElementById('passIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}

document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

/* Confirm Delete Modal */
function closeCredsModal() {
    var el = document.getElementById('credsModal');
    if (el) { el.classList.remove('is-open'); el.style.display = 'none'; }
}
function copyText(text, cb) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(cb, cb);
    } else {
        var ta = document.createElement('textarea');
        ta.value = text; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta); cb();
    }
}
function addCredsCopy() {
    var dst = document.getElementById('credsModal');
    if (!dst) return;
    dst.querySelectorAll('.creds-copy').forEach(function(btn) {
        var orig = btn.getAttribute('data-copy');
        btn.addEventListener('click', function() {
            var icon = btn.querySelector('.material-symbols-outlined');
            copyText(orig, function() {
                icon.textContent = 'check';
                setTimeout(function() { icon.textContent = 'content_copy'; }, 1500);
            });
        });
    });
}
function copyAllCreds() {
    var dst = document.getElementById('credsModal');
    if (!dst) return;
    var lines = [];
    dst.querySelectorAll('.creds-row').forEach(function(row) {
        var label = row.children[0].textContent.trim();
        var copyBtn = row.querySelector('.creds-copy');
        var val = copyBtn ? copyBtn.getAttribute('data-copy') : row.querySelector('.creds-val').textContent.trim();
        lines.push(label + ': ' + val);
    });
    var icon = document.getElementById('credsCopyIcon');
    var lbl = document.getElementById('credsCopyLabel');
    copyText(lines.join('\n'), function() {
        icon.textContent = 'check';
        lbl.textContent = 'Copiado';
        setTimeout(function() { icon.textContent = 'content_copy'; lbl.textContent = 'Copiar credenciales'; }, 1800);
    });
}
addCredsCopy();
var deleteType = '';
function openConfirmDelete(id, name, type) {
    deleteType = type;
    document.getElementById('confirmDeleteId').value = id;
    document.getElementById('confirmDeleteName').textContent = name;
    document.getElementById('confirmDeleteModal').classList.add('active');
}
function closeConfirmModal() {
    document.getElementById('confirmDeleteModal').classList.remove('active');
}
function submitConfirmDelete() {
    var form = document.getElementById('confirmDeleteForm');
    if (deleteType === 'admin') {
        form.action = '<?= site_url("dios/delete-admin") ?>';
    } else if (deleteType === 'code') {
        form.action = '<?= site_url("dios/delete-code") ?>';
    }
    form.submit();
}
document.getElementById('confirmDeleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});
</script>

<!-- Credenciales del admin recién creado -->
<?php $creds = session()->getFlashdata('creds'); ?>
<?php if ($creds): ?>
<div class="modal-overlay is-open" id="credsModal" style="z-index:3000;">
    <div class="modal">
        <div class="modal-head">
            <h3><span class="material-symbols-outlined">how_to_reg</span> Admin creado</h3>
            <button class="modal-close" onclick="closeCredsModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="creds-person">
                <strong><?= esc($creds['name']) ?></strong>
                <span><?= esc($creds['empresa']) ?></span>
            </div>
            <div class="creds-list">
                <div class="creds-row">
                    <span>C&oacute;digo</span>
                    <div class="creds-val">
                        <code><?= esc($creds['code']) ?></code>
                        <button type="button" class="creds-copy" data-copy="<?= esc($creds['code']) ?>"><span class="material-symbols-outlined">content_copy</span></button>
                    </div>
                </div>
                <div class="creds-row">
                    <span>DNI</span>
                    <div class="creds-val">
                        <code><?= esc($creds['dni']) ?></code>
                        <button type="button" class="creds-copy" data-copy="<?= esc($creds['dni']) ?>"><span class="material-symbols-outlined">content_copy</span></button>
                    </div>
                </div>
                <div class="creds-row">
                    <span>Email</span>
                    <div class="creds-val">
                        <span><?= esc($creds['email']) ?></span>
                        <button type="button" class="creds-copy" data-copy="<?= esc($creds['email']) ?>"><span class="material-symbols-outlined">content_copy</span></button>
                    </div>
                </div>
                <div class="creds-row">
                    <span>Contrase&ntilde;a</span>
                    <div class="creds-val">
                        <code><?= esc($creds['password']) ?></code>
                        <button type="button" class="creds-copy" data-copy="<?= esc($creds['password']) ?>"><span class="material-symbols-outlined">content_copy</span></button>
                    </div>
                </div>
            </div>
            <p class="creds-hint">La contrase&ntilde;a solo se muestra esta vez. Advi&eacute;rtele que la cambie en «Mi perfil» despu&eacute;s de entrar.</p>
        </div>
        <div class="modal-footer">
            <button class="dev-btn" onclick="copyAllCreds()">
                <span class="material-symbols-outlined" id="credsCopyIcon">content_copy</span>
                <span id="credsCopyLabel">Copiar credenciales</span>
            </button>
            <button class="dev-btn dev-btn-primary" onclick="closeCredsModal()">Listo</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Confirm Delete Modal -->
<div class="confirm-modal" id="confirmDeleteModal">
    <div class="confirm-modal-card">
        <div class="confirm-modal-icon">
            <span class="material-symbols-outlined">delete</span>
        </div>
        <div class="confirm-modal-title">Eliminar registro</div>
        <div class="confirm-modal-text">Se eliminara este admin, su empresa y todo su personal (empleados, practicantes, horarios, codigos). Esta accion no se puede deshacer.</div>
        <div class="confirm-modal-name" id="confirmDeleteName">-</div>
        <form id="confirmDeleteForm" method="POST">
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
