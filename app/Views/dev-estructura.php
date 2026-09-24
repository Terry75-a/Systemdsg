<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estructura · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <style>
        .page-content { max-width: 960px; }

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

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-full { grid-column: span 2; }

        /* ═══ HIERARCHY ═══ */
        .hierarchy { display: flex; flex-direction: column; align-items: center; gap: 0; padding: 8px 0; }
        .h-node {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px; border-radius: 14px;
            border: 1px solid var(--g-border); background: var(--g-surface);
            min-width: 260px; position: relative;
            transition: border-color 200ms;
        }
        .h-node:hover { border-color: var(--g-primary); }
        .h-node-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .h-node-icon .material-symbols-outlined { font-size: 24px; }
        .h-node-info { flex: 1; }
        .h-node-name { font-size: 15px; font-weight: 500; font-family: var(--g-font-display); }
        .h-node-desc { font-size: 12px; color: var(--g-text-secondary); margin-top: 2px; }
        .h-node-badge {
            font-size: 11px; font-weight: 500; padding: 4px 10px;
            border-radius: 16px; white-space: nowrap;
        }
        .h-connector {
            width: 2px; height: 28px; background: var(--g-border); margin: 0 auto;
        }
        .h-connector-arrow {
            width: 0; height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid var(--g-border);
            margin: 0 auto;
        }

        /* ═══ FLOW ═══ */
        .flow { display: flex; flex-direction: column; gap: 0; }
        .flow-step {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 16px 0; position: relative;
        }
        .flow-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 19px; top: 48px; bottom: -2px;
            width: 2px; background: var(--g-border);
        }
        .flow-num {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 600; font-family: var(--g-font-display);
            flex-shrink: 0; position: relative; z-index: 1;
        }
        .flow-content { flex: 1; padding-top: 8px; }
        .flow-title { font-size: 14px; font-weight: 500; }
        .flow-desc { font-size: 13px; color: var(--g-text-secondary); margin-top: 2px; }

        /* ═══ STORAGE ═══ */
        .storage-list { display: flex; flex-direction: column; gap: 10px; }
        .storage-item {
            display: flex; align-items: center; gap: 14px;
            padding: 14px; border-radius: 14px;
            border: 1px solid var(--g-border);
            transition: border-color 200ms;
        }
        .storage-item:hover { border-color: var(--g-primary); }
        .storage-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .storage-icon .material-symbols-outlined { font-size: 22px; }
        .storage-name { font-size: 14px; font-weight: 500; font-family: 'SF Mono', Monaco, monospace; }
        .storage-desc { font-size: 12px; color: var(--g-text-secondary); margin-top: 2px; }

        /* ═══ PERMISSIONS ═══ */
        .perm-table { width: 100%; border-collapse: collapse; }
        .perm-table th {
            font-size: 11px; font-weight: 500; color: var(--g-text-secondary);
            text-transform: uppercase; letter-spacing: 0.04em;
            padding: 12px 14px; text-align: center;
            border-bottom: 1px solid var(--g-border);
        }
        .perm-table th:first-child { text-align: left; }
        .perm-table td {
            padding: 12px 14px; font-size: 14px;
            border-bottom: 1px solid var(--g-border);
            text-align: center;
        }
        .perm-table td:first-child { text-align: left; font-weight: 500; }
        .perm-table tr:last-child td { border-bottom: none; }
        .perm-table tr:hover td { background: var(--g-surface-variant); }
        .perm-check { font-size: 20px; }
        .perm-yes { color: var(--g-success); }
        .perm-no { color: var(--g-error); opacity: 0.5; }
        .perm-partial { color: #e37400; }

        .perm-mobile { display: none; }
        .perm-card {
            background: var(--g-surface);
            border: 1px solid var(--g-border);
            border-radius: 14px;
            padding: 16px;
        }
        .perm-card-title {
            font-size: 15px; font-weight: 500; font-family: var(--g-font-display);
            margin-bottom: 12px;
        }
        .perm-card-roles {
            display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;
        }
        .perm-role {
            display: flex; flex-direction: column; align-items: center;
            gap: 4px; padding: 10px; border-radius: 12px;
            background: var(--g-surface-variant);
        }
        .perm-role-name { font-size: 11px; color: var(--g-text-secondary); }
        .perm-role-icon { font-size: 20px; }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .grid-full { grid-column: span 1; }
            .h-node { min-width: auto; width: 100%; }
            .hierarchy { align-items: stretch; }
            .flow-step { gap: 10px; }
            .flow-num { width: 36px; height: 36px; font-size: 14px; }
            .perm-table-wrap { display: none; }
            .perm-mobile { display: flex !important; flex-direction: column; gap: 12px; padding: 16px; }
        }
    </style>
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'estructura'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Estructura</strong> del sistema</h1>
                    <p>Visualiza como esta organizado el sistema de asistencia.</p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <div class="grid-2">

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">account_tree</span> Jerarquia de roles</h2>
                    </div>
                    <div class="card-body">
                        <div class="hierarchy">
                            <div class="h-node">
                                <div class="h-node-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                                    <span class="material-symbols-outlined filled">shield</span>
                                </div>
                                <div class="h-node-info">
                                    <div class="h-node-name">Dev</div>
                                    <div class="h-node-desc">Superadmin del sistema</div>
                                </div>
                                <span class="h-node-badge" style="background:var(--g-primary-light);color:var(--g-primary);">Nivel 1</span>
                            </div>

                            <div class="h-connector"></div>
                            <div class="h-connector-arrow"></div>

                            <div class="h-node">
                                <div class="h-node-icon" style="background:var(--g-success-light);color:var(--g-success);">
                                    <span class="material-symbols-outlined filled">admin_panel_settings</span>
                                </div>
                                <div class="h-node-info">
                                    <div class="h-node-name">Admin</div>
                                    <div class="h-node-desc">Gestiona personal y codigos</div>
                                </div>
                                <span class="h-node-badge" style="background:var(--g-success-light);color:var(--g-success);">Nivel 2</span>
                            </div>

                            <div class="h-connector"></div>
                            <div class="h-connector-arrow"></div>

                            <div class="h-node">
                                <div class="h-node-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);">
                                    <span class="material-symbols-outlined">badge</span>
                                </div>
                                <div class="h-node-info">
                                    <div class="h-node-name">Empleado</div>
                                    <div class="h-node-desc">Marca asistencia diaria</div>
                                </div>
                                <span class="h-node-badge" style="background:var(--g-surface-variant);color:var(--g-text-secondary);">Nivel 3</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">timeline</span> Flujo de creacion</h2>
                    </div>
                    <div class="card-body">
                        <div class="flow">
                            <div class="flow-step">
                                <div class="flow-num" style="background:var(--g-primary-light);color:var(--g-primary);">1</div>
                                <div class="flow-content">
                                    <div class="flow-title">Dev crea Admin</div>
                                    <div class="flow-desc">Registra nombre, DNI y contrasena</div>
                                </div>
                            </div>
                            <div class="flow-step">
                                <div class="flow-num" style="background:var(--g-success-light);color:var(--g-success);">2</div>
                                <div class="flow-content">
                                    <div class="flow-title">Admin crea Empleado</div>
                                    <div class="flow-desc">Directamente o genera codigo de invitacion</div>
                                </div>
                            </div>
                            <div class="flow-step">
                                <div class="flow-num" style="background:var(--g-warning-light);color:#e37400;">3</div>
                                <div class="flow-content">
                                    <div class="flow-title">Empleado se registra</div>
                                    <div class="flow-desc">Usa codigo y crea su contrasena</div>
                                </div>
                            </div>
                            <div class="flow-step">
                                <div class="flow-num" style="background:var(--g-surface-variant);color:var(--g-text-secondary);">4</div>
                                <div class="flow-content">
                                    <div class="flow-title">Marca asistencia</div>
                                    <div class="flow-desc">Una vez por dia ingresando su DNI</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">folder_open</span> Almacenamiento</h2>
                    </div>
                    <div class="card-body">
                        <div class="storage-list">
                            <div class="storage-item">
                                <div class="storage-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                                    <span class="material-symbols-outlined filled">description</span>
                                </div>
                                <div>
                                    <div class="storage-name">users.json</div>
                                    <div class="storage-desc">Dev, Admin y Empleados del sistema</div>
                                </div>
                            </div>
                            <div class="storage-item">
                                <div class="storage-icon" style="background:var(--g-warning-light);color:#e37400;">
                                    <span class="material-symbols-outlined filled">vpn_key</span>
                                </div>
                                <div>
                                    <div class="storage-name">codes.json</div>
                                    <div class="storage-desc">Codigos de invitacion activos y usados</div>
                                </div>
                            </div>
                            <div class="storage-item">
                                <div class="storage-icon" style="background:var(--g-success-light);color:var(--g-success);">
                                    <span class="material-symbols-outlined filled">event_available</span>
                                </div>
                                <div>
                                    <div class="storage-name">attendance.json</div>
                                    <div class="storage-desc">Registro diario de asistencia</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">lock</span> Permisos por rol</h2>
                    </div>

                    <div class="perm-table-wrap">
                        <table class="perm-table">
                            <thead>
                                <tr>
                                    <th>Accion</th>
                                    <th>Dev</th>
                                    <th>Admin</th>
                                    <th>Empleado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Crear Admin</td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                </tr>
                                <tr>
                                    <td>Crear Empleado</td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                </tr>
                                <tr>
                                    <td>Generar codigo</td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                </tr>
                                <tr>
                                    <td>Ver asistencia</td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-partial">remove</span></td>
                                </tr>
                                <tr>
                                    <td>Marcar asistencia</td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                </tr>
                                <tr>
                                    <td>Eliminar usuarios</td>
                                    <td><span class="material-symbols-outlined perm-check perm-yes">check_circle</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                    <td><span class="material-symbols-outlined perm-check perm-no">cancel</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="perm-mobile">
                        <?php
                        $perms = [
                            ['action' => 'Crear Admin',       'dev' => true,  'admin' => false, 'emp' => false],
                            ['action' => 'Crear Empleado',    'dev' => true,  'admin' => true,  'emp' => false],
                            ['action' => 'Generar codigo',    'dev' => true,  'admin' => true,  'emp' => false],
                            ['action' => 'Ver asistencia',    'dev' => true,  'admin' => true,  'emp' => 'partial'],
                            ['action' => 'Marcar asistencia', 'dev' => false, 'admin' => false, 'emp' => true],
                            ['action' => 'Eliminar usuarios', 'dev' => true,  'admin' => false, 'emp' => false],
                        ];
                        foreach ($perms as $p):
                        ?>
                        <div class="perm-card">
                            <div class="perm-card-title"><?= $p['action'] ?></div>
                            <div class="perm-card-roles">
                                <div class="perm-role">
                                    <span class="perm-role-name">Dev</span>
                                    <span class="perm-role-icon"><?= $p['dev'] ? '✅' : '❌' ?></span>
                                </div>
                                <div class="perm-role">
                                    <span class="perm-role-name">Admin</span>
                                    <span class="perm-role-icon"><?= $p['admin'] ? '✅' : '❌' ?></span>
                                </div>
                                <div class="perm-role">
                                    <span class="perm-role-name">Empleado</span>
                                    <span class="perm-role-icon"><?= $p['emp'] === true ? '✅' : ($p['emp'] === 'partial' ? '⚠️' : '❌') ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>
