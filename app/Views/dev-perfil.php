<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <style>
        .page-content { max-width: 960px; }

        /* Profile Hero */
        .profile-hero {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 20px;
            margin-bottom: 24px; overflow: hidden; position: relative;
        }
        .profile-cover {
            height: 120px; background: linear-gradient(135deg, var(--g-primary) 0%, #0d5c30 100%);
            position: relative;
        }
        .profile-cover::after {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .profile-main {
            display: flex; align-items: flex-end; gap: 20px;
            padding: 0 28px; margin-top: -40px; position: relative; z-index: 1;
        }
        .profile-avatar-wrap {
            width: 88px; height: 88px; border-radius: 50%;
            background: var(--g-surface); padding: 4px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .profile-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--g-primary); color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 500; font-family: var(--g-font-display);
        }
        .profile-info { flex: 1; padding-bottom: 4px; }
        .profile-name { font-size: 24px; font-weight: 500; font-family: var(--g-font-display); letter-spacing: -0.01em; }
        .profile-role { font-size: 14px; color: var(--g-text-secondary); margin-top: 2px; }
        .profile-meta {
            display: flex; gap: 24px; padding: 16px 28px 20px;
            border-top: 1px solid var(--g-border); margin-top: 16px;
            flex-wrap: wrap;
        }
        .profile-meta-item {
            display: flex; align-items: center; gap: 8px;
        }
        .profile-meta-item .material-symbols-outlined { font-size: 18px; color: var(--g-text-secondary); }
        .profile-meta-item span { font-size: 13px; color: var(--g-text-secondary); }
        .profile-meta-item strong { font-weight: 500; color: var(--g-text); }

        /* Stats Row */
        .profile-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
        }
        .profile-stat {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px;
            padding: 18px; text-align: center; transition: border-color 200ms;
        }
        .profile-stat:hover { border-color: var(--g-primary); }
        .profile-stat-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 10px;
        }
        .profile-stat-icon .material-symbols-outlined { font-size: 22px; }
        .profile-stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); letter-spacing: -0.02em; }
        .profile-stat-label { font-size: 12px; color: var(--g-text-secondary); margin-top: 4px; }

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

        .field-list { display: flex; flex-direction: column; }
        .field-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid var(--g-border);
        }
        .field-item:last-child { border-bottom: none; }
        .field-item-left {
            display: flex; align-items: center; gap: 12px;
        }
        .field-item-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .field-item-icon .material-symbols-outlined { font-size: 18px; }
        .field-label { font-size: 13px; color: var(--g-text-secondary); }
        .field-value { font-size: 14px; font-weight: 500; }
        .field-value code {
            background: var(--g-surface-variant); padding: 4px 10px; border-radius: 8px;
            font-size: 13px; font-family: monospace;
        }

        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 6px; }
        .field input {
            width: 100%; height: 44px; padding: 0 14px;
            border: 1px solid var(--g-border); border-radius: 12px;
            font-size: 14px; font-family: var(--g-font); color: var(--g-text);
            background: var(--g-surface); transition: border-color 200ms;
        }
        .field input:focus { outline: none; border-color: var(--g-primary); }
        .field input::placeholder { color: var(--g-text-disabled); }

        .alert {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 12px;
            font-size: 14px; margin-bottom: 16px;
        }
        .alert-success { background: var(--g-success-light); color: var(--g-success); }
        .alert-error { background: var(--g-error-light); color: var(--g-error); }
        .alert .material-symbols-outlined { font-size: 20px; }

        .badge { display: inline-block; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 16px; }
        .badge-green { background: var(--g-success-light); color: var(--g-success); }
        .badge-primary { background: var(--g-primary-light); color: var(--g-primary); }

        /* Activity Timeline */
        .activity-list { display: flex; flex-direction: column; }
        .activity-item {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--g-border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 10px; height: 10px; border-radius: 50%;
            margin-top: 5px; flex-shrink: 0;
        }
        .activity-content { flex: 1; }
        .activity-text { font-size: 14px; }
        .activity-time { font-size: 12px; color: var(--g-text-secondary); margin-top: 2px; }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .profile-stats { grid-template-columns: 1fr 1fr; }
            .profile-main { flex-direction: column; align-items: center; text-align: center; padding-top: 16px; }
            .profile-meta { justify-content: center; }
        }
    </style>
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'perfil'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Mi Perfil</strong></h1>
                    <p>Gestiona tu informacion personal.</p>
                </div>
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

            <!-- Profile Hero Card -->
            <div class="profile-hero">
                <div class="profile-cover"></div>
                <div class="profile-main">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar">DV</div>
                    </div>
                    <div class="profile-info">
                        <div class="profile-name"><?= esc($user['name'] ?? 'Superadmin') ?></div>
                        <div class="profile-role">Desarrollador</div>
                    </div>
                </div>
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <span class="material-symbols-outlined">shield</span>
                        <span class="badge badge-green">Dev</span>
                    </div>
                    <div class="profile-meta-item">
                        <span class="material-symbols-outlined">event</span>
                        <span>Ultimo login: <strong><?= esc($user['last_login'] ?? 'Primera vez') ?></strong></span>
                    </div>
                    <div class="profile-meta-item">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <span>Miembro desde: <strong><?= esc($user['created'] ?? '-') ?></strong></span>
                    </div>
                    <div class="profile-meta-item">
                        <span class="material-symbols-outlined">dns</span>
                        <span>DNI: <strong><?= esc($user['dni'] ?? '-') ?></strong></span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="profile-stat-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                        <span class="material-symbols-outlined">shield</span>
                    </div>
                    <div class="profile-stat-value">Dev</div>
                    <div class="profile-stat-label">Rol</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon" style="background:var(--g-success-light);color:var(--g-success);">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                    </div>
                    <div class="profile-stat-value">Ilimitado</div>
                    <div class="profile-stat-label">Acceso</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon" style="background:var(--g-warning-light);color:#e37400;">
                        <span class="material-symbols-outlined">tune</span>
                    </div>
                    <div class="profile-stat-value">Total</div>
                    <div class="profile-stat-label">Control</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-icon" style="background:#e8f0fe;color:#1a73e8;">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <div class="profile-stat-value">Activo</div>
                    <div class="profile-stat-label">Estado</div>
                </div>
            </div>

            <div class="grid-2">
                <!-- Info personal -->
                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">person</span> Informacion personal</h2>
                    </div>
                    <div class="field-list">
                        <div class="field-item">
                            <div class="field-item-left">
                                <div class="field-item-icon" style="background:var(--g-primary-light);color:var(--g-primary);">
                                    <span class="material-symbols-outlined">badge</span>
                                </div>
                                <span class="field-label">Nombre</span>
                            </div>
                            <span class="field-value"><?= esc($user['name'] ?? '-') ?></span>
                        </div>
                        <div class="field-item">
                            <div class="field-item-left">
                                <div class="field-item-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);">
                                    <span class="material-symbols-outlined">credit_card</span>
                                </div>
                                <span class="field-label">DNI</span>
                            </div>
                            <span class="field-value"><code><?= esc($user['dni'] ?? '-') ?></code></span>
                        </div>
                        <div class="field-item">
                            <div class="field-item-left">
                                <div class="field-item-icon" style="background:#e8f0fe;color:#1a73e8;">
                                    <span class="material-symbols-outlined">mail</span>
                                </div>
                                <span class="field-label">Email</span>
                            </div>
                            <span class="field-value"><?= esc($user['email'] ?: 'No registrado') ?></span>
                        </div>
                        <div class="field-item">
                            <div class="field-item-left">
                                <div class="field-item-icon" style="background:var(--g-success-light);color:var(--g-success);">
                                    <span class="material-symbols-outlined">shield</span>
                                </div>
                                <span class="field-label">Rol</span>
                            </div>
                            <span class="badge badge-green">Dev</span>
                        </div>
                        <div class="field-item">
                            <div class="field-item-left">
                                <div class="field-item-icon" style="background:var(--g-warning-light);color:#e37400;">
                                    <span class="material-symbols-outlined">schedule</span>
                                </div>
                                <span class="field-label">Ultimo login</span>
                            </div>
                            <span class="field-value" style="font-size:13px;"><?= esc($user['last_login'] ?? 'Primera vez') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Cambiar contrasena -->
                <div class="card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined">lock</span> Cambiar contrasena</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= site_url('dios/update-password') ?>">
                            <?= csrf_field() ?>
                            <div style="display:flex;flex-direction:column;gap:16px;">
                                <div class="field">
                                    <label for="current_password">Contrasena actual</label>
                                    <input type="password" id="current_password" name="current_password" placeholder="Tu contrasena actual" required>
                                </div>
                                <div class="field">
                                    <label for="new_password">Nueva contrasena</label>
                                    <input type="password" id="new_password" name="new_password" placeholder="Minimo 6 caracteres" required minlength="6">
                                </div>
                                <div class="field">
                                    <label for="confirm_password">Confirmar contrasena</label>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la nueva contrasena" required minlength="6">
                                </div>
                            </div>
                            <button type="submit" class="dev-btn dev-btn-primary dev-btn-block" style="margin-top:20px;">
                                <span class="material-symbols-outlined">save</span> Guardar cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="card" style="margin-top:16px;">
                <div class="card-head">
                    <h2><span class="material-symbols-outlined">history</span> Actividad reciente</h2>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--g-success);"></div>
                        <div class="activity-content">
                            <div class="activity-text">Inicio de sesion exitoso</div>
                            <div class="activity-time">Hoy</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--g-primary);"></div>
                        <div class="activity-content">
                            <div class="activity-text">Panel de administracion accedido</div>
                            <div class="activity-time">Hoy</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#e37400;"></div>
                        <div class="activity-content">
                            <div class="activity-text">Sesion activa</div>
                            <div class="activity-time">En curso</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
