<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal · Panel Admin DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=20260922') ?>">
    <style>
        .personal-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
        }
        .personal-stat {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px; padding: 20px;
            display: flex; align-items: center; gap: 14px; transition: border-color 200ms;
        }
        .personal-stat:hover { border-color: var(--g-primary); }
        .personal-stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .personal-stat-icon .material-symbols-outlined { font-size: 24px; }
        .psi-total { background: var(--g-primary-light); color: var(--g-primary); }
        .psi-active { background: var(--g-success-light); color: var(--g-success); }
        .psi-admin { background: var(--g-warning-light); color: #e37400; }
        .psi-codes { background: #e8f0fe; color: #1a73e8; }
        .personal-stat-value { font-size: 28px; font-weight: 600; font-family: var(--g-font-display); letter-spacing: -0.04em; }
        .personal-stat-label { font-size: 12px; color: var(--g-text-secondary); margin-top: 4px; }

        .personal-section { margin-bottom: 24px; }
        .personal-section-head {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
        }
        .personal-section-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 16px; font-weight: 500; font-family: var(--g-font-display); margin: 0;
        }
        .personal-section-title .material-symbols-outlined { font-size: 22px; color: var(--g-primary); }

        .personal-table-wrap { overflow-x: auto; border-radius: 16px; border: 1px solid var(--g-border); background: var(--g-surface); }
        .personal-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .personal-table th {
            text-align: left; padding: 14px 16px;
            font-size: 12px; font-weight: 500; color: var(--g-text-secondary);
            background: var(--g-surface); border-bottom: 1px solid var(--g-border);
        }
        .personal-table td { padding: 14px 16px; border-bottom: 1px solid var(--g-border); }
        .personal-table tr:last-child td { border-bottom: none; }
        .personal-table tbody tr { transition: background 200ms; }
        .personal-table tbody tr:hover td { background: var(--g-surface-variant); }

        .personal-user { display: flex; align-items: center; gap: 12px; }
        .personal-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 500; color: white;
            font-family: var(--g-font-display); flex-shrink: 0;
        }
        .pa-dev { background: #9334e6; }
        .pa-admin { background: var(--g-primary); }
        .pa-empleado { background: #1a73e8; }
        .personal-user-name { font-weight: 500; }
        .personal-mono { font-family: 'SF Mono', Consolas, monospace; font-size: 13px; }
        .personal-dim { color: var(--g-text-secondary); font-size: 13px; }

        .personal-role {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 16px;
        }
        .role-dev { background: #f3e8fd; color: #9334e6; }
        .role-admin { background: var(--g-primary-light); color: var(--g-primary); }
        .role-empleado { background: #e8f0fe; color: #1a73e8; }
        .role-practicante { background: #fef3c7; color: #b45309; }

        .personal-actions { display: flex; gap: 4px; }

        .personal-codes { display: flex; flex-direction: column; }
        .personal-code-item {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 14px 20px; border-bottom: 1px solid var(--g-border);
        }
        .personal-code-item:last-child { border-bottom: none; }
        .personal-code-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
        .personal-code-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--g-primary-light); color: var(--g-primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 500; font-family: var(--g-font-display); flex-shrink: 0;
        }
        .personal-code-name { font-size: 14px; font-weight: 500; }
        .personal-code-meta { font-size: 12px; color: var(--g-text-secondary); }
        .personal-code-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .dev-code {
            font-family: 'SF Mono', Consolas, monospace;
            font-size: 13px; font-weight: 500;
            background: var(--g-surface-variant); color: var(--g-text);
            padding: 6px 12px; border-radius: 8px; cursor: pointer;
            transition: background 200ms; letter-spacing: 0.05em;
        }
        .dev-code:hover { background: var(--g-border); }

        .empty-state {
            text-align: center; padding: 48px 20px; color: var(--g-text-secondary);
        }
        .empty-state .material-symbols-outlined { font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.2; }
        .empty-state p { font-size: 14px; margin: 0; }

        .personal-form-card {
            background: var(--g-surface); border-radius: 16px; overflow: hidden; border: 1px solid var(--g-border);
        }
        .code-status-active { background: var(--g-success-light); color: var(--g-success); }
        .code-status-used, .code-status-inactive { background: var(--g-surface-variant); color: var(--g-text-secondary); }

        .dev-table-mobile { display: none; }

        @media (max-width: 1024px) {
            .personal-stats { grid-template-columns: repeat(2, 1fr); }
            .personal-table-wrap { display: none; }
            .dev-table-mobile { display: flex !important; flex-direction: column; gap: 12px; }
            .personal-code-item { padding: 16px; flex-wrap: wrap; }
            .personal-codes { gap: 10px; }
            .personal-code-right { width: 100%; justify-content: space-between; }
        }
        @media (max-width: 768px) {
            .personal-stats { grid-template-columns: 1fr 1fr; gap: 10px; }
            .personal-stat { padding: 14px; gap: 10px; }
            .personal-stat-icon { width: 36px; height: 36px; border-radius: 10px; }
            .personal-stat-icon .material-symbols-outlined { font-size: 20px; }
            .personal-stat-value { font-size: 22px; }
            .m-card-actions { justify-content: flex-end; }
            .m-card-actions .m-card-btn {
                position: relative;
                flex: 0 0 auto; width: 42px; height: 42px; min-width: 42px; padding: 0;
                border-radius: 50%; font-size: 0; justify-content: center;
            }
            .m-card-actions .m-card-btn .material-symbols-outlined {
                position: absolute; inset: 0;
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; line-height: 1;
                font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 20;
            }
        }

        /* Mobile Cards */
        .m-card {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 16px; overflow: hidden;
        }
        .m-card-top {
            display: flex; align-items: center; gap: 12px;
            padding: 16px;
        }
        .m-card-info { flex: 1; min-width: 0; }
        .m-card-name { font-size: 14px; font-weight: 500; font-family: var(--g-font-display); }
        .m-card-sub { font-size: 12px; color: var(--g-text-secondary); margin-top: 2px; }
        .m-card-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 16px 14px; }
        .m-card-chips .dev-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; padding: 4px 10px; border-radius: 999px;
        }
        .m-card-chips .dev-badge .material-symbols-outlined { font-size: 14px; }
        .m-card-details { display: flex; gap: 12px; padding: 0 16px 14px; flex-wrap: wrap; }
        .m-card-detail {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: var(--g-text-secondary);
        }
        .m-card-detail .material-symbols-outlined { font-size: 16px; }
        .m-card-actions {
            display: flex; gap: 8px; padding: 0 16px 16px;
        }
        .m-card-btn {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
            height: 44px; border: none; border-radius: 12px;
            font-family: var(--g-font); font-size: 14px; font-weight: 500;
            cursor: pointer; transition: background 200ms;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
        }
        .m-card-btn:hover { background: var(--g-border); color: var(--g-text); }
        .m-card-btn .material-symbols-outlined { font-size: 18px; }
        .m-card-btn-danger { color: var(--g-error); background: var(--g-error-light); }
        .m-card-btn-danger:hover { background: #f8d7da; }
        .personal-table tbody tr { cursor: pointer; }

        /* Detail Modal */
        .detail-overlay {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
            opacity: 0; visibility: hidden; transition: opacity 200ms, visibility 200ms;
        }
        .detail-overlay.active { opacity: 1; visibility: visible; }
        .detail-card {
            background: var(--g-surface); border-radius: 28px; width: 90%; max-width: 480px;
            max-height: 85vh; overflow-y: auto;
            transform: scale(0.95); transition: transform 200ms;
        }
        .detail-overlay.active .detail-card { transform: scale(1); }
        .detail-cover {
            height: 80px; border-radius: 28px 28px 0 0; position: relative;
        }
        .detail-avatar-wrap {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--g-surface); padding: 4px;
            display: flex; align-items: center; justify-content: center;
            position: absolute; bottom: -40px; left: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .detail-avatar {
            width: 72px; height: 72px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 500; color: white;
            font-family: var(--g-font-display);
        }
        .detail-header { padding: 48px 24px 16px; }
        .detail-name { font-size: 22px; font-weight: 500; font-family: var(--g-font-display); }
        .detail-sub { font-size: 14px; color: var(--g-text-secondary); margin-top: 4px; }
        .detail-body { padding: 0 24px 24px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label {
            font-size: 11px; font-weight: 500; color: var(--g-text-secondary);
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .detail-value {
            font-size: 14px; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--g-surface-variant); border-radius: 12px;
        }
        .detail-value code { font-family: 'SF Mono', Consolas, monospace; font-size: 13px; }
        .detail-actions {
            display: flex; gap: 12px; padding: 0 24px 24px;
        }
        .detail-btn {
            flex: 1; height: 48px; border: none; border-radius: 24px;
            font-family: var(--g-font); font-size: 14px; font-weight: 500;
            cursor: pointer; transition: all 200ms;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .detail-btn-edit { background: var(--g-primary); color: white; }
        .detail-btn-edit:hover { background: var(--g-primary-hover); }
        .detail-btn-close { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .detail-btn-close:hover { background: var(--g-border); }
        .detail-btn .material-symbols-outlined { font-size: 18px; }
    </style>
</head>
<body>

<?= view('partials/admin-sidebar', ['activePage' => 'personal']) ?>

<div class="dev-main">
    <div class="dev-topbar">
        <div>
            <h1><strong>Personal</strong></h1>
            <p>Gestiona tu equipo de trabajo, crea empleados y genera codigos de registro.</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span style="background:var(--g-success-light);color:var(--g-success);padding:8px 14px;border-radius:10px;font-family:'SF Mono',Consolas,monospace;font-size:13px;font-weight:600;"><?= esc($adminCode ?? 'ADMIN-???') ?></span>
            <?php if (!empty($adminEmpresa)): ?>
            <span style="background:var(--g-surface-variant);color:var(--g-text-secondary);padding:8px 14px;border-radius:10px;font-size:13px;font-weight:500;"><?= esc($adminEmpresa) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="dev-content">

        <?php if ($msg = session()->getFlashdata('msg')): ?>
            <?php $tipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <div class="dev-alert dev-alert-<?= $tipo === 'success' ? 'success' : ($tipo === 'warning' ? 'warning' : 'danger') ?>" style="margin-bottom: 20px;">
                <span class="material-symbols-outlined"><?= $tipo === 'success' ? 'check_circle' : ($tipo === 'warning' ? 'warning' : 'error') ?></span>
                <?= esc($msg) ?>
            </div>
        <?php endif; ?>

        <div class="personal-stats">
            <div class="personal-stat">
                <div class="personal-stat-icon psi-total"><span class="material-symbols-outlined">group</span></div>
                <div>
                    <div class="personal-stat-value"><?= count($empleados) ?></div>
                    <div class="personal-stat-label">Total equipo</div>
                </div>
            </div>
            <div class="personal-stat">
                <div class="personal-stat-icon psi-active"><span class="material-symbols-outlined">person</span></div>
                <div>
                    <div class="personal-stat-value"><?= count(array_filter($empleados, fn($e) => ($e['role'] ?? '') === 'Empleado')) ?></div>
                    <div class="personal-stat-label">Empleados</div>
                </div>
            </div>
            <div class="personal-stat">
                <div class="personal-stat-icon psi-admin"><span class="material-symbols-outlined">school</span></div>
                <div>
                    <div class="personal-stat-value"><?= count(array_filter($empleados, fn($e) => ($e['role'] ?? '') === 'Practicante')) ?></div>
                    <div class="personal-stat-label">Practicantes</div>
                </div>
            </div>
            <div class="personal-stat">
                <div class="personal-stat-icon psi-codes"><span class="material-symbols-outlined">qr_code</span></div>
                <div>
                    <div class="personal-stat-value"><?= count(array_filter($codes, fn($c) => ($c['status'] ?? '') === 'active')) ?></div>
                    <div class="personal-stat-label">Codigos activos</div>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
            <button type="button" class="dev-btn dev-btn-primary" onclick="openModal('createEmployeeModal')">
                <span class="material-symbols-outlined">person_add</span> Crear empleado
            </button>
            <button type="button" class="dev-btn dev-btn-primary" onclick="openModal('createCodeModal')">
                <span class="material-symbols-outlined">vpn_key</span> Generar codigo
            </button>
        </div>

        <div class="personal-section">
            <div class="personal-section-head">
                <h2 class="personal-section-title">
                    <span class="material-symbols-outlined">group</span>
                    Equipo
                    <span style="font-size:13px; color:var(--g-text-secondary); font-weight:400; margin-left:4px;"><?= count($empleados) ?> personas</span>
                </h2>
            </div>

            <div class="personal-form-card">
                <div class="personal-table-wrap">
                    <table class="personal-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Codigo</th>
                                <th>DNI</th>
                                <th>Semestre</th>
                                <th>Institucion</th>
                                <th>Area</th>
                                <th>Cargo</th>
                                <th>Rol</th>
                                <th>Biometria</th>
                                <th>Contrato</th>
                                <th>Creado</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($empleados)): ?>
                                <tr><td colspan="11"><div class="empty-state"><span class="material-symbols-outlined">person_off</span><p>No hay empleados registrados</p></div></td></tr>
                            <?php else: ?>
                                <?php foreach ($empleados as $emp):
                                    $role = $emp['role'] ?? 'Empleado';
                                    $roleClass = match($role) { 'Dev' => 'role-dev', 'Admin' => 'role-admin', 'Practicante' => 'role-practicante', default => 'role-empleado' };
                                    $avatarClass = $role === 'Dev' ? 'pa-dev' : ($role === 'Admin' ? 'pa-admin' : 'pa-empleado');
                                    $name = $emp['name'] ?? $emp['nombre'] ?? 'Sin nombre';
                                    $initial = strtoupper(mb_substr($name, 0, 1, 'UTF-8'));
                                ?>
                                    <tr onclick="openDetailModal(<?= htmlspecialchars(json_encode($emp), ENT_QUOTES, 'UTF-8') ?>)">
                                        <td>
                                            <div class="personal-user">
                                                <div class="personal-avatar <?= $avatarClass ?>"><?= $initial ?></div>
                                                <div class="personal-user-name"><?= esc($name) ?></div>
                                            </div>
                                        </td>
                                        <td><code class="dev-code"><?= esc($emp['personal_code'] ?? '—') ?></code></td>
                                        <td class="personal-mono"><?= esc($emp['dni'] ?? '---') ?></td>
                                        <td><?= esc(($emp['role'] ?? '') === 'Practicante' ? ($emp['semestre'] ?? '—') : '—') ?></td>
                                        <td><?= esc(($emp['role'] ?? '') === 'Practicante' ? ($emp['institucion'] ?? '—') : '—') ?></td>
                                        <td><?= esc($emp['area'] ?? '-') ?></td>
                                        <td><?= esc($emp['cargo'] ?? '-') ?></td>
                                        <td><span class="personal-role <?= $roleClass ?>"><?= esc($role) ?></span></td>
                                        <td>
                                            <?php
                                                $bH = (int) ($emp['huella_registrada'] ?? 0);
                                                $bR = (int) ($emp['rostro_registrado'] ?? 0);
                                            ?>
                                            <span class="dev-badge" style="font-size:11px; <?= $bH && $bR ? 'background:rgba(46,204,113,.15);color:var(--g-success);' : 'background:rgba(239,68,68,.12);color:var(--g-error);' ?>">
                                                <?= $bH && $bR ? 'H.+R. ✓' : ($bH || $bR ? 'Incompleta' : 'Pendiente') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $ct = $emp['contract_type'] ?? 'Indefinido'; $cd = (int) ($emp['contract_duration'] ?? 0); ?>
                                            <span class="dev-badge" style="font-size:11px; <?= $ct === 'Indefinido' ? 'background:rgba(120,144,156,.12);color:#90a4ae;' : 'background:rgba(26,115,232,.12);color:#1a73e8;' ?>">
                                                <?= $ct === 'Indefinido' ? 'Indefinido' : ($ct === 'Meses' ? $cd . ' mes(es)' : $cd . ' año(s)') ?>
                                            </span>
                                        </td>
                                        <td class="personal-dim"><?= esc($emp['created'] ?? 'N/A') ?></td>
                                        <td onclick="event.stopPropagation()">
                                            <div class="personal-actions">
                                                <?php if (($emp['role'] ?? '') === 'Empleado' && ($emp['estado'] ?? 'Activo') !== 'Despedido'): ?>
                                                <button type="button" class="dev-btn-icon" title="Despedir empleado" style="color:#e37400;" onclick="openConfirmDelete(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>', 'fire')">
                                                    <span class="material-symbols-outlined">person_remove</span>
                                                </button>
                                                <?php endif; ?>
                                                <?php if (($emp['role'] ?? '') === 'Practicante' && ($emp['estado'] ?? 'Activo') !== 'Retirado'): ?>
                                                <button type="button" class="dev-btn-icon" title="Terminar" style="color:#b45309;" onclick="openConfirmDelete(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>', 'end')">
                                                    <span class="material-symbols-outlined">school</span>
                                                </button>
                                                <?php endif; ?>
                                                <button type="button" class="dev-btn-icon" title="Generar nueva contrasena" style="color:#7c4dff;" onclick="openResetPwd(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>')">
                                                    <span class="material-symbols-outlined">password</span>
                                                </button>
                                                <button type="button" class="dev-btn-icon" title="Editar" onclick='openEditModal(<?= json_encode($emp) ?>)'>
                                                    <span class="material-symbols-outlined">edit</span>
                                                </button>
                                                <button type="button" class="dev-btn-icon" title="Eliminar (definitivo)" style="color:var(--g-error);" onclick="openConfirmDelete(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>', 'user')">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="dev-table-mobile">
                    <?php if (empty($empleados)): ?>
                        <div class="empty-state"><span class="material-symbols-outlined">person_off</span><p>No hay empleados registrados</p></div>
                    <?php else: ?>
                        <?php foreach ($empleados as $emp):
                            $role = $emp['role'] ?? 'Empleado';
                            $roleClass = match($role) { 'Dev' => 'role-dev', 'Admin' => 'role-admin', 'Practicante' => 'role-practicante', default => 'role-empleado' };
                            $avatarClass = $role === 'Dev' ? 'pa-dev' : ($role === 'Admin' ? 'pa-admin' : 'pa-empleado');
                            $name = $emp['name'] ?? 'Sin nombre';
                            $initial = strtoupper(mb_substr($name, 0, 1, 'UTF-8'));
                        ?>
                            <div class="m-card" style="cursor:pointer;" onclick="event.target.closest('.m-card-actions') || openDetailModal(<?= htmlspecialchars(json_encode($emp), ENT_QUOTES, 'UTF-8') ?>)">
                                <div class="m-card-top">
                                    <div class="personal-avatar <?= $avatarClass ?>" style="width:42px;height:42px;font-size:16px;flex-shrink:0;"><?= $initial ?></div>
                                    <div class="m-card-info">
                                        <div class="m-card-name"><?= esc($name) ?></div>
                                        <div class="m-card-sub"><code style="font-family:monospace;"><?= esc($emp['personal_code'] ?? '') ?></code> · DNI <?= esc($emp['dni'] ?? '---') ?></div>
                                        <?php if (($emp['role'] ?? '') === 'Practicante'): ?>
                                        <div class="m-card-sub" style="margin-top:2px;">
                                            <span class="material-symbols-outlined" style="font-size:13px;vertical-align:-2px;">school</span>
                                            <?= esc($emp['semestre'] ?? '—') ?> · <?= esc($emp['institucion'] ?? '—') ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="personal-role <?= $roleClass ?>"><?= esc($role) ?></span>
                                </div>
                                <?php
                                    $bH = (int) ($emp['huella_registrada'] ?? 0);
                                    $bR = (int) ($emp['rostro_registrado'] ?? 0);
                                    $_ct = $emp['contract_type'] ?? 'Indefinido';
                                    $_cd = (int) ($emp['contract_duration'] ?? 0);
                                ?>
                                <div class="m-card-chips">
                                    <span class="dev-badge" <?= $bH && $bR ? 'style="background:rgba(46,204,113,.15);color:var(--g-success);"' : 'style="background:rgba(239,68,68,.12);color:var(--g-error);"' ?>>
                                        <span class="material-symbols-outlined">fingerprint</span> <?= $bH && $bR ? 'Biometría ✓' : 'Biometría pendiente' ?>
                                    </span>
                                    <span class="dev-badge" <?= $_ct === 'Indefinido' ? 'style="background:rgba(120,144,156,.12);color:#90a4ae;"' : 'style="background:rgba(26,115,232,.12);color:#1a73e8;"' ?>>
                                        <span class="material-symbols-outlined">description</span> <?= $_ct === 'Indefinido' ? 'Contrato indefinido' : 'Contrato ' . $_cd . ($_ct === 'Meses' ? ' mes(es)' : ' año(s)') ?>
                                    </span>
                                </div>
                                <div class="m-card-actions" onclick="event.stopPropagation()">
                                    <button type="button" class="m-card-btn" title="Nueva contrasena" onclick="openResetPwd(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>')">
                                        <span class="material-symbols-outlined">password</span> Contrasena
                                    </button>
                                    <button type="button" class="m-card-btn" title="Editar" onclick='openEditModal(<?= json_encode($emp) ?>)'>
                                        <span class="material-symbols-outlined">edit</span> Editar
                                    </button>
                                    <button type="button" class="m-card-btn m-card-btn-danger" title="Eliminar" onclick="openConfirmDelete(<?= $emp['id'] ?>, '<?= addslashes(esc($name)) ?>', 'user')">
                                        <span class="material-symbols-outlined">delete</span> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="personal-section">
            <div class="personal-section-head">
                <h2 class="personal-section-title">
                    <span class="material-symbols-outlined">vpn_key</span>
                    Codigos de registro
                    <span style="font-size:13px; color:var(--g-text-secondary); font-weight:400; margin-left:4px;"><?= count($codes) ?> total</span>
                </h2>
            </div>

            <div class="personal-form-card">
                <?php if (empty($codes)): ?>
                    <div class="empty-state"><span class="material-symbols-outlined">qr_code_2</span><p>No hay codigos generados.</p></div>
                <?php else: ?>
                    <div class="personal-codes">
                        <?php foreach ($codes as $code):
                            $codeStatus = $code['status'] ?? '';
                            $codeName = $code['name'] ?? 'Sin nombre';
                            $codeInitial = strtoupper(mb_substr($codeName, 0, 1, 'UTF-8'));
                        ?>
                            <div class="personal-code-item" style="cursor:pointer;" onclick="openCodeDetail(<?= htmlspecialchars(json_encode(array_merge($code, ['_name' => $codeName, '_initial' => $codeInitial, '_status' => $codeStatus])), ENT_QUOTES, 'UTF-8') ?>)">
                                <div class="personal-code-left">
                                    <div class="personal-code-avatar"><?= $codeInitial ?></div>
                                    <div>
                                        <div class="personal-code-name"><?= esc($codeName) ?></div>
                                        <div class="personal-code-meta">DNI <?= esc($code['dni'] ?? '---') ?></div>
                                    </div>
                                </div>
                                <div class="personal-code-right" onclick="event.stopPropagation()">
                                    <code class="dev-code" id="code-<?= $code['id'] ?>"><?= esc($code['code'] ?? '') ?></code>
                                    <button type="button" class="dev-btn-icon" title="Copiar" onclick="copyCode('code-<?= $code['id'] ?>', this)">
                                        <span class="material-symbols-outlined">content_copy</span>
                                    </button>
                                    <span class="personal-role code-status-<?= $codeStatus === 'active' ? 'active' : 'inactive' ?>">
                                        <?= $codeStatus === 'active' ? 'Activo' : 'Usado' ?>
                                    </span>
                                    <button type="button" class="dev-btn-icon" title="Eliminar" onclick="openConfirmDelete(<?= $code['id'] ?>, '<?= addslashes(esc($codeName)) ?>', 'code')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Crear empleado -->
<div class="dev-modal-overlay" id="createEmployeeModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">person_add</span> Crear empleado</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('createEmployeeModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= site_url('admin/create-employee') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <div class="dev-form-grid">
                    <div class="dev-field" style="grid-column:1/-1;">
                        <label>Encargado (Admin)</label>
                        <div style="display:flex;align-items:center;gap:10px;background:var(--g-success-light);border:1px solid var(--g-border);padding:10px 14px;border-radius:12px;">
                            <span class="material-symbols-outlined" style="color:var(--g-success);">admin_panel_settings</span>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-family:var(--g-font-display);"><?= esc(session()->get('user_name') ?? 'Admin') ?></div>
                                <div style="font-size:11px;color:var(--g-text-secondary);">Será el encargado de este empleado/practicante</div>
                            </div>
                            <span style="font-family:monospace;font-weight:600;font-size:12px;color:var(--g-success);"><?= esc($adminCode ?? 'ADMIN-???') ?></span>
                        </div>
                    </div>
                    <div class="dev-field"><label>Nombre completo</label><input type="text" id="create_emp_name" name="emp_name" placeholder="Ej. Juan Perez" required></div>
                    <div class="dev-field"><label>DNI</label><input type="text" name="emp_dni" placeholder="8 digitos (ej. 71234567)" pattern="\d{8}" minlength="8" maxlength="8" inputmode="numeric" title="El DNI peruano tiene 8 digitos" required></div>
                    <div class="dev-field" style="grid-column:1/-1;"><label>Rol</label><select name="emp_role" required id="create_emp_role" onchange="toggleCreatePracticante(this.value); autofillCargo()"><option value="Empleado">Empleado</option><option value="Practicante">Practicante</option></select></div>
                    <div id="create_practica_fields" style="display:none;grid-column:1/-1;background:var(--g-surface-variant);border:1px solid var(--g-border);border-radius:12px;padding:14px;margin-top:-4px;">
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                            <div class="dev-field" style="flex:1;min-width:200px;margin:0;">
                                <label><span class="material-symbols-outlined" style="font-size:14px;vertical-align:-2px;">school</span> Institucion educativa (opcional)</label>
                                <input type="text" name="emp_institucion" placeholder="Ej. SENATI, UNI, TECSUP...">
                            </div>
                            <div class="dev-field" style="min-width:160px;margin:0;">
                                <label><span class="material-symbols-outlined" style="font-size:14px;vertical-align:-2px;">grade</span> Semestre / Ciclo *</label>
                                <select name="emp_semestre" id="create_emp_semestre">
                                    <option value="">Elegir...</option>
                                    <option value="Ciclo I">Ciclo I</option>
                                    <option value="Ciclo II">Ciclo II</option>
                                    <option value="Ciclo III">Ciclo III</option>
                                    <option value="Ciclo IV">Ciclo IV</option>
                                    <option value="Ciclo V">Ciclo V</option>
                                    <option value="Ciclo VI">Ciclo VI</option>
                                    <option value="Ciclo VII">Ciclo VII</option>
                                    <option value="Ciclo VIII">Ciclo VIII</option>
                                    <option value="Ciclo IX">Ciclo IX</option>
                                    <option value="Ciclo X">Ciclo X</option>
                                </select>
                            </div>
                        </div>
                        <small style="color:var(--g-text-secondary);font-size:11px;display:block;margin-top:8px;">Campo exclusivo de practicantes: indica en que semestre esta.</small>
                    </div>
                    <div class="dev-field" style="grid-column:1/-1;">
                        <label>Area especializada</label>
                        <div style="position:relative;">
                            <input type="text" id="create_emp_area" name="emp_area" placeholder="Toca para elegir area" readonly onclick="openAreaModal()" style="cursor:pointer;padding-right:36px;">
                            <span class="material-symbols-outlined" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--g-text-secondary);pointer-events:none;">expand_more</span>
                        </div>
                        <small style="color:var(--g-text-secondary);font-size:11px;">Se abre una busqueda de areas especializadas.</small>
                    </div>
                    <div class="dev-field" style="grid-column:1/-1;">
                        <label>Cargo</label>
                        <input type="text" id="create_emp_cargo" name="emp_cargo" list="cargoOptions" placeholder="Elige o escribe, ej. Asistente de Sistemas">
                        <small style="color:var(--g-text-secondary);font-size:11px;">Sugerencias al hacer clic, tambien puedes escribir libre.</small>
                    </div>
                    <div style="grid-column:1/-1;border-top:1px dashed var(--g-border);margin-top:4px;padding-top:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--g-primary);">description</span>
                            <strong style="font-size:13px;font-family:var(--g-font-display);">Contrato</strong>
                        </div>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Tipo</label>
                                <select name="emp_contract_type" id="create_contract_type" onchange="contractTypeChanged('create', this)">
                                    <option value="Indefinido">Indefinido</option>
                                    <option value="Meses">Por meses</option>
                                    <option value="Años">Por años</option>
                                </select>
                            </div>
                            <div class="dev-field" style="min-width:120px;margin:0;" id="create_contract_dur_wrap">
                                <label id="create_contract_dur_label">Duración (meses)</label>
                                <input type="number" name="emp_contract_duration" id="create_contract_dur" min="1" max="600" placeholder="1" disabled>
                            </div>
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Fecha de inicio</label>
                                <input type="date" name="emp_contract_start" id="create_contract_start">
                            </div>
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Fecha de fin <span style="font-weight:400;color:var(--g-text-secondary);">(auto)</span></label>
                                <input type="date" name="emp_contract_end" id="create_contract_end" readonly>
                            </div>
                        </div>
                        <small style="color:var(--g-text-secondary);font-size:11px;">La fecha de fin se calcula sola. Deja "Indefinido" si no hay contrato por tiempo.</small>
                    </div>
                    <div style="grid-column:1/-1;border-top:1px dashed var(--g-border);padding-top:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--g-primary);">draw</span>
                            <strong style="font-size:13px;font-family:var(--g-font-display);">Firma <span style="font-weight:400;color:var(--g-text-secondary);">(opcional)</span></strong>
                        </div>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                            <div class="dev-field" style="flex:1;min-width:200px;margin:0;">
                                <label>Tipo de firma</label>
                                <select name="emp_firma_tipo" id="create_firma_tipo" onchange="firmaTypeChanged('create', this.value)">
                                    <option value="">Sin firma</option>
                                    <option value="nombre">Por nombre (automática)</option>
                                    <option value="firma">Firma digital (dibujada)</option>
                                </select>
                            </div>
                            <div class="dev-field" style="flex:1;min-width:220px;margin:0;">
                                <label>Firma</label>
                                <div id="create_firma_show" style="min-height:46px;border:1px dashed var(--g-border);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:'Segoe Script','Brush Script MT',cursive;font-size:22px;color:var(--g-text-secondary);background:var(--g-surface-variant);">Sin firma</div>
                                <input type="hidden" name="emp_firma_datos" id="create_firma_datos">
                            </div>
                        </div>
                        <div id="create_pad_wrap" style="display:none;margin-top:10px;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                <span class="material-symbols-outlined" style="font-size:15px;">ink_pen</span>
                                <small style="color:var(--g-text-secondary);">Dibuja tu firma con el mouse o el dedo:</small>
                            </div>
                            <canvas id="create_firma_canvas" width="600" height="160" style="width:100%;height:150px;border:1px solid var(--g-border);border-radius:12px;background:#fff;touch-action:none;cursor:crosshair;"></canvas>
                            <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                                <button type="button" class="dev-btn" onclick="clearFirma('create')">Limpiar</button>
                                <span id="create_pad_preview_done" style="display:none;align-items:center;gap:6px;color:var(--g-success);font-size:12px;font-weight:600;"><span class="material-symbols-outlined" style="font-size:16px;">check_circle</span> Firma dibujada. Usar esta</span>
                            </div>
                        </div>
                        <small style="color:var(--g-text-secondary);font-size:11px;">"Por nombre" usa su nombre y apellido como firma. "Firma digital" se guarda como imagen.</small>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeModal('createEmployeeModal')">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">add</span> Crear empleado</button>
            </div>
        </form>
    </div>
</div>

<!-- Sugerencias de cargos -->
<datalist id="cargoOptions">
    <option value="Practicante de Sistemas">
    <option value="Practicante de Contabilidad">
    <option value="Practicante de Marketing">
    <option value="Practicante de Administracion">
    <option value="Asistente Administrativo">
    <option value="Asistente de Sistemas">
    <option value="Asistente de Contabilidad">
    <option value="Analista de Procesos">
    <option value="Tecnico de Soporte">
    <option value="Soporte TI">
    <option value="Ejecutivo de Ventas">
    <option value="Vendedor">
    <option value="Cajero">
    <option value="Reponedor">
    <option value="Delivery">
    <option value="Mesero">
    <option value="Cocinero">
    <option value="Farmaceutico">
    <option value="Tecnico Farmaceutico">
    <option value="Operador de Almacen">
    <option value="Supervisor de Turno">
    <option value="Administrador de Tienda">
    <option value="Contador">
    <option value="Jefe de Operaciones">
    <option value="Jefe de Logistica">
    <option value="Desarrollador">
    <option value="Disenador Grafico">
    <option value="Community Manager">
</datalist>

<!-- Modal: Elegir area especializada -->
<div class="dev-modal-overlay" id="areaModal">
    <div class="dev-modal" style="max-width:540px;">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">business_center</span> Buscar area especializada</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('areaModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body">
            <div style="position:relative;margin-bottom:14px;">
                <span class="material-symbols-outlined" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--g-text-secondary);pointer-events:none;">search</span>
                <input type="text" id="areaSearch" placeholder="Buscar area (ej. Sistemas, Ventas, Almacen...)" style="width:100%;padding:11px 14px 11px 40px;border:1px solid var(--g-border);border-radius:12px;background:var(--g-surface);color:var(--g-text);outline:none;font-size:14px;">
            </div>
            <div id="areaGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;"></div>
        </div>
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn" onclick="closeModal('areaModal')">Cancelar</button>
            <button type="button" class="dev-btn" onclick="clearArea()">Sin area</button>
        </div>
    </div>
</div>

<!-- Modal: Generar codigo -->
<div class="dev-modal-overlay" id="createCodeModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">vpn_key</span> Generar codigo</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('createCodeModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= site_url('admin/create-code') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <div class="dev-field">
                    <label>Empleado</label>
                    <select name="name" required>
                        <option value="">Selecciona un empleado</option>
                        <?php foreach ($empleados as $emp): ?>
                            <option value="<?= esc($emp['name'] ?? '') ?>"><?= esc($emp['name'] ?? '') ?> — DNI <?= esc($emp['dni'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dev-field"><label>DNI del empleado</label><input type="text" name="dni" placeholder="8 digitos" pattern="\d{8}" maxlength="8" required></div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeModal('createCodeModal')">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">add</span> Generar codigo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Credenciales generadas (al crear) -->
<?php $creds = session()->getFlashdata('emp_creds'); ?>
<?php if ($creds): ?>
<div class="dev-modal-overlay active one-shot" id="credsModal">
    <div class="dev-modal">
        <div class="dev-modal-head" style="background:var(--g-success-light);">
            <h3><span class="material-symbols-outlined" style="color:var(--g-success);">verified_user</span> Credenciales generadas</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('credsModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body" style="text-align:center;">
            <span class="material-symbols-outlined" style="font-size:48px;color:var(--g-success);">badge</span>
            <h3 style="margin:8px 0;font-family:var(--g-font-display);"><?= esc($creds['name']) ?></h3>
            <div style="background:var(--g-surface-variant);border-radius:12px;padding:16px;margin:16px 0;text-align:left;">
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Codigo</span><div style="font-family:monospace;font-weight:600;"><?= esc($creds['personal_code'] ?? '') ?></div></div>
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Correo</span><div style="font-family:monospace;font-weight:500;"><?= esc($creds['email']) ?></div></div>
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">DNI</span><div style="font-family:monospace;font-weight:500;"><?= esc($creds['dni']) ?></div></div>
                <div><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Contrasena</span><div style="font-family:monospace;font-weight:500;background:var(--g-surface);padding:6px 10px;border-radius:8px;border:1px solid var(--g-border);"><?= esc($creds['password']) ?></div></div>
            </div>
            <div style="font-size:11.5px;color:var(--g-text-secondary);display:flex;gap:6px;align-items:center;"><span class="material-symbols-outlined" style="font-size:16px;">fingerprint</span> Al ingresar por primera vez debera registrar su <strong>huella</strong> y <strong>rostro</strong> para poder marcar asistencia.</div>
        </div>
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn dev-btn-primary" onclick="navigator.clipboard.writeText('Codigo: <?= esc($creds['personal_code'] ?? '') ?>\nCorreo: <?= esc($creds['email']) ?>\nDNI: <?= esc($creds['dni']) ?>\nContrasena: <?= esc($creds['password']) ?>'); this.innerHTML='<span class=\'material-symbols-outlined\'>check</span> Copiado';"><span class="material-symbols-outlined">content_copy</span> Copiar credenciales</button>
            <button type="button" class="dev-btn" onclick="closeModal('credsModal')">Cerrar</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal: Contrasena regenerada -->
<?php $reset = session()->getFlashdata('emp_reset'); ?>
<?php if ($reset): ?>
<div class="dev-modal-overlay active one-shot" id="credsModal">
    <div class="dev-modal">
        <div class="dev-modal-head" style="background:var(--g-success-light);">
            <h3><span class="material-symbols-outlined" style="color:var(--g-success);">password</span> Contrasena regenerada</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('credsModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body" style="text-align:center;">
            <span class="material-symbols-outlined" style="font-size:48px;color:var(--g-success);">manage_accounts</span>
            <h3 style="margin:8px 0;font-family:var(--g-font-display);"><?= esc($reset['name']) ?></h3>
            <p style="color:var(--g-text-secondary);font-size:13px;margin:0 0 8px;">Nueva contrasena generada. Copiala y entregasela.</p>
            <div style="background:var(--g-surface-variant);border-radius:12px;padding:16px;margin:16px 0;text-align:left;">
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Codigo</span><div style="font-family:monospace;font-weight:600;"><?= esc($reset['personal_code'] ?? '') ?></div></div>
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Correo</span><div style="font-family:monospace;font-weight:500;"><?= esc($reset['email']) ?></div></div>
                <div style="margin-bottom:10px;"><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">DNI</span><div style="font-family:monospace;font-weight:500;"><?= esc($reset['dni']) ?></div></div>
                <div><span style="font-size:11px;color:var(--g-text-secondary);text-transform:uppercase;">Contrasena</span><div style="font-family:monospace;font-weight:600;background:var(--g-surface);padding:6px 10px;border-radius:8px;border:1px solid var(--g-border);"><?= esc($reset['password']) ?></div></div>
            </div>
        </div>
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn dev-btn-primary" onclick="navigator.clipboard.writeText('Codigo: <?= esc($reset['personal_code'] ?? '') ?>\nCorreo: <?= esc($reset['email']) ?>\nDNI: <?= esc($reset['dni']) ?>\nContrasena: <?= esc($reset['password']) ?>'); this.innerHTML='<span class=\'material-symbols-outlined\'>check</span> Copiado';"><span class="material-symbols-outlined">content_copy</span> Copiar credenciales</button>
            <button type="button" class="dev-btn" onclick="closeModal('credsModal')">Cerrar</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal: Confirmar regenerar contrasena -->
<div class="dev-modal-overlay" id="resetPwdModal">
    <div class="dev-modal" style="max-width:420px;">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">manage_accounts</span> Regenerar contrasena</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('resetPwdModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= site_url('admin/reset-password') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <p style="color:var(--g-text-secondary);margin:0 0 10px;">Se generara una <strong>nueva contrasena segura</strong> para <strong class="reset-pwd-name"></strong>.</p>
                <p style="color:var(--g-text-secondary);margin:0;">Se mostrara una <strong style="color:var(--g-warning);">sola vez</strong> en pantalla: copiala y entregasela.</p>
                <input type="hidden" name="user_id" id="resetPwdUserId">
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeModal('resetPwdModal')">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">password</span> Generar nueva contrasena</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Editar empleado -->
<div class="dev-modal-overlay" id="editEmployeeModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">edit</span> Editar empleado</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('editEmployeeModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= site_url('admin/update-employee') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="dev-modal-body">
                <div class="dev-form-grid">
                    <div class="dev-field"><label>Nombre</label><input type="text" id="edit_name" name="emp_name" required></div>
                    <div class="dev-field"><label>DNI</label><input type="text" id="edit_dni" name="emp_dni" pattern="\d{8}" minlength="8" maxlength="8" inputmode="numeric" title="El DNI peruano tiene 8 digitos" required></div>
                    <div class="dev-field"><label>Correo</label><input type="email" id="edit_email" name="emp_email"></div>
                    <div class="dev-field"><label>Codigo personal</label><input type="text" id="edit_code" disabled style="background:var(--g-surface-variant);font-family:monospace;font-size:14px;font-weight:600;"></div>
                    <div class="dev-field"><label>Rol</label><select id="edit_role" name="emp_role" disabled><option value="Empleado">Empleado</option><option value="Practicante">Practicante</option><option value="Admin">Admin</option></select><small style="color:var(--g-text-secondary);font-size:11px;">No se puede cambiar</small></div>
                    <div class="dev-field"><label>Area</label><input type="text" id="edit_area" name="emp_area"></div>
                    <div class="dev-field"><label>Cargo</label><input type="text" id="edit_cargo" name="emp_cargo"></div>
                    <div style="grid-column:1/-1;border-top:1px dashed var(--g-border);margin-top:4px;padding-top:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--g-primary);">description</span>
                            <strong style="font-size:13px;font-family:var(--g-font-display);">Contrato</strong>
                        </div>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Tipo</label>
                                <select name="emp_contract_type" id="edit_contract_type" onchange="contractTypeChanged('edit', this)">
                                    <option value="Indefinido">Indefinido</option>
                                    <option value="Meses">Por meses</option>
                                    <option value="Años">Por años</option>
                                </select>
                            </div>
                            <div class="dev-field" style="min-width:120px;margin:0;" id="edit_contract_dur_wrap">
                                <label id="edit_contract_dur_label">Duración (meses)</label>
                                <input type="number" name="emp_contract_duration" id="edit_contract_dur" min="1" max="600" disabled>
                            </div>
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Fecha de inicio</label>
                                <input type="date" name="emp_contract_start" id="edit_contract_start">
                            </div>
                            <div class="dev-field" style="flex:1;min-width:150px;margin:0;">
                                <label>Fecha de fin <span style="font-weight:400;color:var(--g-text-secondary);">(auto)</span></label>
                                <input type="date" name="emp_contract_end" id="edit_contract_end" readonly>
                            </div>
                        </div>
                    </div>
                    <div style="grid-column:1/-1;border-top:1px dashed var(--g-border);padding-top:14px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--g-primary);">draw</span>
                            <strong style="font-size:13px;font-family:var(--g-font-display);">Firma <span style="font-weight:400;color:var(--g-text-secondary);">(opcional)</span></strong>
                        </div>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                            <div class="dev-field" style="flex:1;min-width:200px;margin:0;">
                                <label>Tipo de firma</label>
                                <select name="emp_firma_tipo" id="edit_firma_tipo" onchange="firmaTypeChanged('edit', this.value)">
                                    <option value="">Sin firma</option>
                                    <option value="nombre">Por nombre (automática)</option>
                                    <option value="firma">Firma digital (dibujada)</option>
                                </select>
                            </div>
                            <div class="dev-field" style="flex:1;min-width:220px;margin:0;">
                                <label>Firma</label>
                                <div id="edit_firma_show" style="min-height:46px;border:1px dashed var(--g-border);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:'Segoe Script','Brush Script MT',cursive;font-size:22px;color:var(--g-text-secondary);background:var(--g-surface-variant);">Sin firma</div>
                                <input type="hidden" name="emp_firma_datos" id="edit_firma_datos">
                            </div>
                        </div>
                        <div id="edit_pad_wrap" style="display:none;margin-top:10px;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                <span class="material-symbols-outlined" style="font-size:15px;">ink_pen</span>
                                <small style="color:var(--g-text-secondary);">Dibuja tu firma con el mouse o el dedo:</small>
                            </div>
                            <canvas id="edit_firma_canvas" width="600" height="160" style="width:100%;height:150px;border:1px solid var(--g-border);border-radius:12px;background:#fff;touch-action:none;cursor:crosshair;"></canvas>
                            <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                                <button type="button" class="dev-btn" onclick="clearFirma('edit')">Limpiar</button>
                                <span id="edit_pad_preview_done" style="display:none;align-items:center;gap:6px;color:var(--g-success);font-size:12px;font-weight:600;"><span class="material-symbols-outlined" style="font-size:16px;">check_circle</span> Firma dibujada. Usar esta</span>
                            </div>
                        </div>
                    </div>
                    <div class="dev-field" style="grid-column:1/-1;display:none;" id="edit_practica_fields">
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:var(--g-surface-variant);border:1px solid var(--g-border);border-radius:12px;padding:14px;">
                            <div class="dev-field" style="flex:1;min-width:200px;margin:0;"><label>Institucion educativa</label><input type="text" id="edit_institucion" name="emp_institucion"></div>
                            <div class="dev-field" style="min-width:160px;margin:0;"><label>Semestre / Ciclo</label><select id="edit_semestre" name="emp_semestre"><option value="">Elegir...</option><option value="Ciclo I">Ciclo I</option><option value="Ciclo II">Ciclo II</option><option value="Ciclo III">Ciclo III</option><option value="Ciclo IV">Ciclo IV</option><option value="Ciclo V">Ciclo V</option><option value="Ciclo VI">Ciclo VI</option><option value="Ciclo VII">Ciclo VII</option><option value="Ciclo VIII">Ciclo VIII</option><option value="Ciclo IX">Ciclo IX</option><option value="Ciclo X">Ciclo X</option></select></div>
                        </div>
                    </div>
                    <div class="dev-field"><label>Estado</label><select id="edit_estado" name="emp_estado"><option value="Activo">Activo</option><option value="Inactivo">Inactivo</option></select></div>
                    <div class="dev-field"><label>Nueva contrasena (opcional)</label><input type="password" id="edit_password" name="emp_password" placeholder="Dejar vacio para mantener actual"></div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeModal('editEmployeeModal')">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">save</span> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Confirmar eliminacion -->
<div class="dev-modal-overlay" id="confirmDeleteModal">
    <div class="dev-modal" style="max-width:400px;text-align:center;">
        <div class="dev-modal-body" style="padding:32px 28px 24px;">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--g-error-light);color:var(--g-error);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <span class="material-symbols-outlined" style="font-size:32px;" id="confirmIcon">delete</span>
            </div>
            <h3 style="font-size:20px;font-weight:500;font-family:var(--g-font-display);margin-bottom:8px;" id="confirmTitle">Eliminar registro</h3>
            <p style="font-size:14px;color:var(--g-text-secondary);margin-bottom:8px;">Esta accion no se puede deshacer.</p>
            <div id="confirmName" style="font-size:16px;font-weight:600;padding:12px 16px;background:var(--g-surface-variant);border-radius:12px;margin-bottom:20px;">-</div>
            <div style="display:flex;gap:12px;">
                <button type="button" class="dev-btn" style="flex:1;height:48px;border-radius:24px;" onclick="closeModal('confirmDeleteModal')"><span class="material-symbols-outlined">close</span> Cancelar</button>
                <button type="button" class="dev-btn dev-btn-danger" style="flex:1;height:48px;border-radius:24px;" id="confirmBtn" onclick="submitConfirmDelete()">                <span class="material-symbols-outlined">delete</span> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="detail-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetailModal()">
    <div class="detail-card">
        <div class="detail-cover" id="detailCover">
            <div class="detail-avatar-wrap">
                <div class="detail-avatar" id="detailAvatar"></div>
            </div>
        </div>
        <div class="detail-header">
            <div class="detail-name" id="detailName">-</div>
            <div class="detail-sub" id="detailRole">-</div>
        </div>
        <div class="detail-body">
            <div class="detail-grid">
                <div class="detail-field">
                    <div class="detail-label">DNI</div>
                    <div class="detail-value"><code id="detailDni">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Codigo</div>
                    <div class="detail-value"><code id="detailCode">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Rol</div>
                    <div class="detail-value" id="detailRolBadge">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Area</div>
                    <div class="detail-value" id="detailArea">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Cargo</div>
                    <div class="detail-value" id="detailCargo">-</div>
                </div>
                <div class="detail-field" id="detailPracticaBlock" style="display:none;">
                    <div class="detail-label">Semestre</div>
                    <div class="detail-value" id="detailSemestre">-</div>
                </div>
                <div class="detail-field" id="detailInstBlock" style="display:none;">
                    <div class="detail-label">Institucion</div>
                    <div class="detail-value" id="detailInstitucion">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Estado</div>
                    <div class="detail-value" id="detailEstado">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Creado</div>
                    <div class="detail-value" id="detailCreated">-</div>
                </div>
                <div class="detail-field" style="grid-column:span 2;">
                    <div class="detail-label">Contrato</div>
                    <div class="detail-value" id="detailContract">Indefinido</div>
                </div>
                <div class="detail-field" style="grid-column:span 2;">
                    <div class="detail-label">Firma</div>
                    <div class="detail-value" id="detailFirma">Sin firma</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Email</div>
                    <div class="detail-value" id="detailEmail" style="grid-column:span 2;">-</div>
                </div>
            </div>
        </div>
        <div class="detail-actions">
            <button type="button" class="detail-btn detail-btn-close" onclick="closeDetailModal()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="detail-btn" id="detailFireBtn" style="background:#fef3c7;color:#b45309;">
                <span class="material-symbols-outlined">person_remove</span> Despedir
            </button>
            <button type="button" class="detail-btn detail-btn-edit" id="detailEditBtn">
                <span class="material-symbols-outlined">edit</span> Editar
            </button>
        </div>
    </div>
</div>

<div class="detail-overlay" id="codeDetailOverlay" onclick="if(event.target===this)closeDetailModal()">
    <div class="detail-card">
        <div class="detail-cover" id="codeDetailCover" style="background:linear-gradient(135deg,#e8f0fe22,#e8f0fe44);">
            <div class="detail-avatar-wrap">
                <div class="detail-avatar" id="codeDetailAvatar" style="background:#1a73e8;"></div>
            </div>
        </div>
        <div class="detail-header">
            <div class="detail-name" id="codeDetailName">-</div>
            <div class="detail-sub" id="codeDetailSub">-</div>
        </div>
        <div class="detail-body">
            <div class="detail-grid">
                <div class="detail-field">
                    <div class="detail-label">DNI</div>
                    <div class="detail-value"><code id="codeDetailDni">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Estado</div>
                    <div class="detail-value" id="codeDetailStatus">-</div>
                </div>
                <div class="detail-field" style="grid-column:span 2;">
                    <div class="detail-label">Codigo</div>
                    <div class="detail-value"><code id="codeDetailCode" style="font-size:16px;letter-spacing:0.08em;">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Creado</div>
                    <div class="detail-value" id="codeDetailCreated">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Usado por</div>
                    <div class="detail-value" id="codeDetailUsedBy">-</div>
                </div>
            </div>
        </div>
        <div class="detail-actions">
            <button type="button" class="detail-btn detail-btn-close" onclick="closeDetailModal()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="detail-btn detail-btn-edit" onclick="copyCodeDetail()" style="background:var(--g-success);color:white;">
                <span class="material-symbols-outlined">content_copy</span> Copiar codigo
            </button>
        </div>
    </div>
</div>

<script>
var confirmType = '';
var confirmId = '';

function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
    if (id === 'createEmployeeModal') toggleCreatePracticante(document.getElementById('create_emp_role').value);
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

function toggleCreatePracticante(role) {
    var box = document.getElementById('create_practica_fields');
    if (box) box.style.display = role === 'Practicante' ? 'block' : 'none';
}

function openResetPwd(userId, name) {
    document.getElementById('resetPwdUserId').value = userId;
    document.querySelector('#resetPwdModal .reset-pwd-name').textContent = name;
    openModal('resetPwdModal');
}

var EMP_AREAS = [
    'Sistemas','Soporte TI','Desarrollo de Software','Redes y Comunicaciones',
    'Operaciones','Ventas','Atencion al Cliente','Marketing','Comercial',
    'Almacen','Logistica','Compras','Produccion','Control de Calidad',
    'Contabilidad','Finanzas','Tesorería','Facturacion','Cobranzas',
    'Recursos Humanos','Administracion','Diseño','Proyectos',
    'Restaurante / Cocina','Botica / Farmacia','Minimarket / Bodega','Delivery'
];

function renderAreaGrid(q) {
    var grid = document.getElementById('areaGrid');
    var list = EMP_AREAS.filter(function(a) {
        return !q || a.toLowerCase().indexOf(q.toLowerCase()) !== -1;
    });
    grid.innerHTML = '';
    if (!list.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--g-text-disabled);padding:20px;">No hay areas que coincidan</div>';
        return;
    }
    list.forEach(function(a) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = a;
        btn.style.cssText = 'display:flex;align-items:center;gap:8px;justify-content:flex-start;padding:10px 12px;border:1px solid var(--g-border);border-radius:12px;background:var(--g-surface-variant);color:var(--g-text);font-size:13px;cursor:pointer;transition:border-color 150ms,box-shadow 150ms;';
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;color:var(--g-primary);">folder</span>' + a;
        btn.onmouseover = function() { this.style.borderColor = 'var(--g-primary)'; this.style.boxShadow = '0 1px 4px rgba(0,0,0,.08)'; };
        btn.onmouseout = function() { this.style.borderColor = 'var(--g-border)'; this.style.boxShadow = 'none'; };
        btn.onclick = function() {
            document.getElementById('create_emp_area').value = a;
            autofillCargo();
            closeModal('areaModal');
        };
        grid.appendChild(btn);
    });
}

function openAreaModal() {
    var search = document.getElementById('areaSearch');
    search.value = '';
    renderAreaGrid('');
    openModal('areaModal');
    search.focus();
}

function clearArea() {
    document.getElementById('create_emp_area').value = '';
    autofillCargo();
    closeModal('areaModal');
}

/* ─── Autorellenado de cargo segun rol + area ─── */
var CARGO_BY_AREA = {
    'Sistemas': 'Analista de Sistemas',
    'Soporte TI': 'Tecnico de Soporte',
    'Desarrollo de Software': 'Desarrollador',
    'Redes y Comunicaciones': 'Especialista en Redes',
    'Operaciones': 'Analista de Operaciones',
    'Ventas': 'Ejecutivo de Ventas',
    'Atencion al Cliente': 'Asesor de Atencion al Cliente',
    'Marketing': 'Community Manager',
    'Comercial': 'Ejecutivo Comercial',
    'Almacen': 'Operador de Almacen',
    'Logistica': 'Analista de Logistica',
    'Compras': 'Analista de Compras',
    'Produccion': 'Operario de Produccion',
    'Control de Calidad': 'Analista de Calidad',
    'Contabilidad': 'Asistente de Contabilidad',
    'Finanzas': 'Asistente de Finanzas',
    'Tesoreria': 'Asistente de Tesoreria',
    'Facturacion': 'Asistente de Facturacion',
    'Cobranzas': 'Asistente de Cobranzas',
    'Recursos Humanos': 'Asistente de Recursos Humanos',
    'Administracion': 'Asistente Administrativo',
    'Diseno': 'Disenador Grafico',
    'Proyectos': 'Analista de Proyectos',
    'Restaurante / Cocina': 'Personal de Restaurante',
    'Botica / Farmacia': 'Tecnico Farmaceutico',
    'Minimarket / Bodega': 'Reponedor',
    'Delivery': 'Delivery'
};
var __autoCargo = false;

function autofillCargo() {
    var cargo = document.getElementById('create_emp_cargo');
    if (!cargo) return;
    if (cargo.value.trim() !== '' && !__autoCargo) return;
    var role = document.getElementById('create_emp_role').value;
    var area = document.getElementById('create_emp_area').value.trim();
    var suggest = '';
    if (role === 'Practicante') {
        suggest = area ? 'Practicante de ' + area : 'Practicante';
    } else {
        suggest = (area && CARGO_BY_AREA[area]) ? CARGO_BY_AREA[area] : (area ? 'Personal de ' + area : '');
    }
    cargo.value = suggest;
    __autoCargo = !!suggest;
}

var cargoInputListener = function() {
    if (this.value.trim() !== '') __autoCargo = false;
};
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        var ci = document.getElementById('create_emp_cargo');
        if (ci) ci.addEventListener('input', cargoInputListener);
    });
} else {
    var ci = document.getElementById('create_emp_cargo');
    if (ci) ci.addEventListener('input', cargoInputListener);
}

(function() {
    var search = document.getElementById('areaSearch');
    if (search) {
        search.addEventListener('input', function() { renderAreaGrid(this.value); });
        search.addEventListener('keydown', function(e) { if (e.key === 'Enter') e.preventDefault(); });
    }
})();

/* ─── Contrato ─── */
function contractTypeChanged(prefix, sel) {
    var type = sel.value;
    var durWrap = document.getElementById(prefix + '_contract_dur_wrap');
    var dur = document.getElementById(prefix + '_contract_dur');
    var durLabel = document.getElementById(prefix + '_contract_dur_label');
    var end = document.getElementById(prefix + '_contract_end');
    if (type === 'Indefinido') {
        dur.disabled = true;
        durLabel.textContent = 'Duracion (meses)';
        end.value = '';
    } else {
        dur.disabled = false;
        durLabel.textContent = 'Duracion (' + (type === 'Meses' ? 'meses' : 'años') + ')';
        calcContractEnd(prefix);
    }
}

function calcContractEnd(prefix) {
    var type = document.getElementById(prefix + '_contract_type').value;
    var dur = parseInt(document.getElementById(prefix + '_contract_dur').value, 10);
    var start = document.getElementById(prefix + '_contract_start').value;
    var end = document.getElementById(prefix + '_contract_end');
    if (type === 'Indefinido' || !dur || !start) { end.value = ''; return; }
    var d = new Date(start + 'T00:00:00');
    if (type === 'Meses') { d.setMonth(d.getMonth() + dur); }
    else { d.setFullYear(d.getFullYear() + dur); }
    end.value = d.toISOString().slice(0, 10);
}

function initContract(prefix) {
    var dur = document.getElementById(prefix + '_contract_dur');
    var start = document.getElementById(prefix + '_contract_start');
    if (dur) dur.addEventListener('input', function() { calcContractEnd(prefix); });
    if (start) start.addEventListener('input', function() { calcContractEnd(prefix); });
    calcContractEnd(prefix);
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { initContract('create'); initContract('edit'); });
} else {
    initContract('create'); initContract('edit');
}

/* ─── Firma ─── */
function firmaNameChanged(prefix) {
    var tipo = document.getElementById(prefix + '_firma_tipo').value;
    if (tipo !== 'nombre') return;
    var nameInput = document.getElementById(prefix === 'create' ? 'create_emp_name' : 'edit_name');
    var show = document.getElementById(prefix + '_firma_show');
    var name = (nameInput && nameInput.value.trim()) || '';
    show.textContent = name || 'Escribe el nombre para ver la firma';
    show.style.color = name ? 'var(--g-text)' : 'var(--g-text-secondary)';
    document.getElementById(prefix + '_firma_show').innerHTML = name ? '<span style="font-family:\'Segoe Script\',\'Brush Script MT\',cursive;font-size:24px;line-height:1;">' + name.replace(/</g,'&lt;') + '</span>' : 'Escribe el nombre para ver la firma';
}

function firmaTypeChanged(prefix, val) {
    var show = document.getElementById(prefix + '_firma_show');
    var padWrap = document.getElementById(prefix + '_pad_wrap');
    var done = document.getElementById(prefix + '_pad_preview_done');
    var datos = document.getElementById(prefix + '_firma_datos');
    done.style.display = 'none';
    datos.value = '';
    if (val === 'nombre') {
        padWrap.style.display = 'none';
        firmaNameChanged(prefix);
        return;
    }
    if (val === 'firma') {
        padWrap.style.display = '';
        show.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;color:var(--g-text-secondary);">ink_pen</span> Firma digital';
        initFirmaPad(prefix);
        return;
    }
    padWrap.style.display = 'none';
    show.innerHTML = 'Sin firma';
}

function initFirmaPad(prefix) {
    var canvas = document.getElementById(prefix + '_firma_canvas');
    if (!canvas) return;
    if (canvas.__bound) return;
    canvas.__bound = true;
    var ctx = canvas.getContext('2d');
    ctx.lineWidth = 2.4;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#111';

    function pos(e) {
        var r = canvas.getBoundingClientRect();
        var x = (e.clientX - r.left) * (canvas.width / r.width);
        var y = (e.clientY - r.top) * (canvas.height / r.height);
        return { x: x, y: y };
    }
    var drawing = false;
    canvas.addEventListener('pointerdown', function(e) {
        e.preventDefault();
        canvas.setPointerCapture(e.pointerId);
        drawing = true;
        var p = pos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
    });
    canvas.addEventListener('pointermove', function(e) {
        if (!drawing) return;
        var p = pos(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
    });
    var end = function() { if (drawing) { drawing = false; saveFirma(prefix); } };
    canvas.addEventListener('pointerup', end);
    canvas.addEventListener('pointercancel', end);
}

function saveFirma(prefix) {
    var canvas = document.getElementById(prefix + '_firma_canvas');
    var datos = document.getElementById(prefix + '_firma_datos');
    var done = document.getElementById(prefix + '_pad_preview_done');
    var dataURL = canvas.toDataURL('image/png');
    var empty = true;
    var ctx = canvas.getContext('2d');
    var d = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
    for (var i = 3; i < d.length; i += 4) { if (d[i] !== 0) { empty = false; break; } }
    if (empty) { datos.value = ''; done.style.display = 'none'; return; }
    datos.value = dataURL;
    done.style.display = 'flex';
}

function clearFirma(prefix) {
    var canvas = document.getElementById(prefix + '_firma_canvas');
    if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById(prefix + '_firma_datos').value = '';
    document.getElementById(prefix + '_pad_preview_done').style.display = 'none';
}

(function() {
    var nm = document.getElementById('create_emp_name');
    if (nm) nm.addEventListener('input', function() { firmaNameChanged('create'); });
    var em = document.getElementById('edit_name');
    if (em) em.addEventListener('input', function() { firmaNameChanged('edit'); });
})();

function setFirmaState(prefix, emp) {
    var tipo = document.getElementById(prefix + '_firma_tipo');
    var datos = document.getElementById(prefix + '_firma_datos');
    var show = document.getElementById(prefix + '_firma_show');
    var padWrap = document.getElementById(prefix + '_pad_wrap');
    var ft = emp.firma_tipo || '';
    var fd = emp.firma_datos || '';
    var name = emp.name || '';
    tipo.value = ft;
    datos.value = (ft === 'firma' && fd) ? fd : '';
    padWrap.style.display = (ft === 'firma') ? '' : 'none';
    if (ft === 'nombre') {
        show.innerHTML = '<span style="font-family:\'Segoe Script\',\'Brush Script MT\',cursive;font-size:24px;line-height:1;">' + name.replace(/</g,'&lt;') + '</span>';
    } else if (ft === 'firma') {
        show.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;color:var(--g-text-secondary);">ink_pen</span> Firma digital';
        document.getElementById(prefix + '_pad_preview_done').style.display = 'none';
        var canvas = document.getElementById(prefix + '_firma_canvas');
        if (canvas && fd) {
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            var img = new Image();
            img.onload = function() { ctx.drawImage(img, 10, 10, Math.min(canvas.width - 20, img.width * (canvas.height - 20) / img.height), canvas.height - 20); datos.value = fd; };
            img.src = fd;
        }
    } else {
        show.innerHTML = 'Sin firma';
    }
}

function openDetailModal(emp) {
    var name = emp.name || 'Sin nombre';
    var initial = name.charAt(0).toUpperCase();
    var role = emp.role || 'Empleado';
    var roleColor = {Dev:'#9334e6',Admin:'var(--g-primary)',Practicante:'#b45309',default:'#1a73e8'};
    var coverColor = roleColor[role] || roleColor.default;
    var avatarBg = {Dev:'#9334e6',Admin:'var(--g-primary)',Practicante:'#b45309',default:'#1a73e8'};

    document.getElementById('detailCover').style.background = 'linear-gradient(135deg, ' + coverColor + '22, ' + coverColor + '44)';
    document.getElementById('detailAvatar').style.background = avatarBg[role] || avatarBg.default;
    document.getElementById('detailAvatar').textContent = initial;
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailRole').textContent = role + (emp.area ? ' - ' + emp.area : '');
    document.getElementById('detailDni').textContent = emp.dni || '---';
    document.getElementById('detailCode').textContent = emp.personal_code || '—';
    document.getElementById('detailRolBadge').textContent = role;
    document.getElementById('detailRolBadge').style.color = coverColor;
    document.getElementById('detailArea').textContent = emp.area || '-';
    document.getElementById('detailCargo').textContent = emp.cargo || '-';
    document.getElementById('detailEstado').textContent = emp.estado || 'Activo';
    document.getElementById('detailCreated').textContent = emp.created || 'N/A';
    document.getElementById('detailEmail').textContent = emp.email || '-';
    var isPract = emp.role === 'Practicante';
    document.getElementById('detailPracticaBlock').style.display = isPract ? '' : 'none';
    document.getElementById('detailInstBlock').style.display = isPract ? '' : 'none';
    document.getElementById('detailSemestre').textContent = emp.semestre || '-';
    document.getElementById('detailInstitucion').textContent = emp.institucion || '-';

    var ct = emp.contract_type || 'Indefinido';
    var dur = emp.contract_duration;
    var cs = emp.contract_start || '';
    var ce = emp.contract_end || '';
    var contractTxt;
    if (ct === 'Indefinido') {
        contractTxt = 'Indefinido';
    } else {
        var per = ct === 'Meses' ? (dur === 1 ? 'mes' : 'meses') : (dur === 1 ? 'ano' : 'anos');
        contractTxt = 'Por ' + dur + ' ' + per;
        if (cs) contractTxt += ' - ' + cs + (ce ? ' a ' + ce : '');
    }
    document.getElementById('detailContract').textContent = contractTxt;

    var firmaEl = document.getElementById('detailFirma');
    firmaEl.innerHTML = '';
    var ft = emp.firma_tipo || '';
    var fd = emp.firma_datos || '';
    if (ft === 'nombre') {
        var s = document.createElement('span');
        s.style.cssText = "font-family:'Segoe Script','Brush Script MT',cursive;font-size:20px;color:var(--g-text);";
        s.textContent = fd || emp.name || '';
        firmaEl.appendChild(s);
    } else if (ft === 'firma' && fd) {
        var img = document.createElement('img');
        img.src = fd;
        img.style.cssText = 'max-height:44px;max-width:240px;';
        firmaEl.appendChild(img);
    } else {
        firmaEl.textContent = 'Sin firma';
    }

    document.getElementById('detailEditBtn').onclick = function() { closeDetailModal(); openEditModal(emp); };

    var fireBtn = document.getElementById('detailFireBtn');
    var fireAction = '';
    if ((emp.role === 'Empleado' && emp.estado !== 'Despedido')) {
        fireAction = 'fire';
        fireBtn.style.display = '';
        fireBtn.innerHTML = '<span class="material-symbols-outlined">person_remove</span> Despedir';
    } else if ((emp.role === 'Practicante' && emp.estado !== 'Retirado')) {
        fireAction = 'end';
        fireBtn.style.display = '';
        fireBtn.innerHTML = '<span class="material-symbols-outlined">school</span> Terminar';
    } else {
        fireBtn.style.display = 'none';
    }
    fireBtn.onclick = function() { closeDetailModal(); openConfirmDelete(emp.id, name, fireAction); };
    openModal('detailOverlay');
}

function closeDetailModal() {
    closeModal('detailOverlay');
    closeModal('codeDetailOverlay');
}

function openCodeDetail(code) {
    var name = code._name || code.name || 'Sin nombre';
    var initial = code._initial || name.charAt(0).toUpperCase();
    var status = code._status || code.status || '';
    var statusColor = status === 'active' ? 'var(--g-success)' : 'var(--g-text-secondary)';
    var statusText = status === 'active' ? 'Activo' : 'Usado';

    document.getElementById('codeDetailCover').style.background = 'linear-gradient(135deg, #e8f0fe22, #e8f0fe44)';
    document.getElementById('codeDetailAvatar').textContent = initial;
    document.getElementById('codeDetailName').textContent = name;
    document.getElementById('codeDetailSub').textContent = 'Codigo de registro';
    document.getElementById('codeDetailDni').textContent = code.dni || '---';
    document.getElementById('codeDetailStatus').textContent = statusText;
    document.getElementById('codeDetailStatus').style.color = statusColor;
    document.getElementById('codeDetailCode').textContent = code.code || '-';
    document.getElementById('codeDetailCreated').textContent = code.created || code.created_at || 'N/A';
    document.getElementById('codeDetailUsedBy').textContent = code.used_by || code.usedByName || '-';
    window._currentCodeText = code.code || '';
    openModal('codeDetailOverlay');
}

function copyCodeDetail() {
    if (window._currentCodeText) {
        navigator.clipboard.writeText(window._currentCodeText).then(function() {
            var btn = document.querySelector('#codeDetailOverlay .detail-btn-edit .material-symbols-outlined');
            if (btn) { btn.textContent = 'check'; setTimeout(function(){ btn.textContent = 'content_copy'; }, 1500); }
        });
    }
}

function openEditModal(emp) {
    document.getElementById('edit_user_id').value = emp.id || '';
    document.getElementById('edit_name').value = emp.name || '';
    document.getElementById('edit_dni').value = emp.dni || '';
    document.getElementById('edit_email').value = emp.email || '';
    document.getElementById('edit_role').value = emp.role || 'Empleado';
    document.getElementById('edit_code').value = emp.personal_code || '';
    document.getElementById('edit_area').value = emp.area || '';
    document.getElementById('edit_cargo').value = emp.cargo || '';
    document.getElementById('edit_estado').value = emp.estado || 'Activo';
    document.getElementById('edit_password').value = '';
    var isPract = emp.role === 'Practicante';
    document.getElementById('edit_practica_fields').style.display = isPract ? '' : 'none';
    document.getElementById('edit_institucion').value = emp.institucion || '';
    document.getElementById('edit_semestre').value = emp.semestre || '';

    document.getElementById('edit_contract_type').value = emp.contract_type || 'Indefinido';
    document.getElementById('edit_contract_dur').value = emp.contract_duration || 1;
    document.getElementById('edit_contract_start').value = emp.contract_start || '';
    document.getElementById('edit_contract_end').value = emp.contract_end || '';
    contractTypeChanged('edit', document.getElementById('edit_contract_type'));
    setFirmaState('edit', emp);
    openModal('editEmployeeModal');
}

function openConfirmDelete(id, name, type) {
    confirmType = type;
    confirmId = id;
    document.getElementById('confirmName').textContent = name;
    var title = document.getElementById('confirmTitle');
    var btn = document.getElementById('confirmBtn');
    if (type === 'fire') {
        title.textContent = 'Despedir empleado';
        btn.innerHTML = '<span class="material-symbols-outlined">person_remove</span> Despedir';
        document.getElementById('confirmIcon').textContent = 'person_remove';
    } else if (type === 'end') {
        title.textContent = 'Terminar';
        btn.innerHTML = '<span class="material-symbols-outlined">school</span> Terminar';
        document.getElementById('confirmIcon').textContent = 'school';
    } else if (type === 'code') {
        title.textContent = 'Eliminar codigo';
        btn.innerHTML = '<span class="material-symbols-outlined">delete</span> Eliminar';
        document.getElementById('confirmIcon').textContent = 'delete';
    } else {
        title.textContent = 'Eliminar de forma definitiva';
        btn.innerHTML = '<span class="material-symbols-outlined">delete</span> Eliminar';
        document.getElementById('confirmIcon').textContent = 'delete';
    }
    openModal('confirmDeleteModal');
}

function submitConfirmDelete() {
    var form = document.createElement('form');
    form.method = 'POST';
    if (confirmType === 'fire') {
        form.action = '<?= site_url("admin/fire-employee") ?>';
    } else if (confirmType === 'end') {
        form.action = '<?= site_url("admin/end-practicante") ?>';
    } else if (confirmType === 'code') {
        form.action = '<?= site_url("admin/delete-code") ?>';
    } else {
        form.action = '<?= site_url("admin/delete-user") ?>';
    }

    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_test_name';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = confirmType === 'code' ? 'id' : 'user_id';
    input.value = confirmId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function copyCode(elementId, btn) {
    var el = document.getElementById(elementId);
    if (!el) return;
    navigator.clipboard.writeText(el.textContent).then(function() {
        var icon = btn.querySelector('.material-symbols-outlined');
        icon.textContent = 'check';
        icon.style.color = 'var(--g-success)';
        setTimeout(function() { icon.textContent = 'content_copy'; icon.style.color = ''; }, 1500);
    });
}

document.querySelectorAll('.dev-modal-overlay').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this && !this.classList.contains('one-shot')) {
            this.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.dev-modal-overlay.active').forEach(function(m) {
            if (!m.classList.contains('one-shot')) {
                m.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
});

var autoDelete = <?= json_encode(session()->getFlashdata('auto_delete') ?: null, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
if (autoDelete) {
    openConfirmDelete(autoDelete.id, autoDelete.name, 'user');
}
</script>

</body>
</html>
