<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion - Panel de Administracion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .cfg-wrap { display: flex; flex-direction: column; gap: 20px; }

        /* ── Modo edición: form ──────────────── */
        .cfg-edit { display: none; }
        .cfg-card.is-editing .cfg-edit { display: block; }
        .cfg-form { padding: 20px 24px 24px; }
        .cfg-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 22px; }
        .cfg-field label {
            display: block; font-size: 0.8rem; font-weight: 500; color: var(--g-text-secondary);
            margin-bottom: 7px; font-family: var(--g-font);
        }
        .cfg-field input {
            width: 100%; height: 44px; padding: 0 14px; box-sizing: border-box;
            border: 1px solid var(--g-border); border-radius: 10px;
            background: var(--g-surface); color: var(--g-text);
            font-family: var(--g-font); font-size: 0.875rem; transition: border-color 200ms, box-shadow 200ms;
        }
        .cfg-field input:focus { outline: none; border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.10); }
        .cfg-field input::placeholder { color: var(--g-text-disabled); }

        .cfg-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--g-border); }
        .cfg-btn {
            height: 44px; padding: 0 24px; border: 0; border-radius: 10px; cursor: pointer;
            background: var(--g-primary); color: #fff; font-weight: 600;
            font-size: 0.875rem; font-family: var(--g-font);
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,.10); transition: filter 200ms, transform 200ms;
        }
        .cfg-btn:hover { filter: brightness(1.06); }
        .cfg-btn:active { transform: scale(.98); }
        .cfg-btn .material-symbols-outlined { font-size: 18px; }

        .cfg-notif-btn {
            width: 100%; height: 44px; padding: 0 14px; box-sizing: border-box;
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            border: 1px solid var(--g-border); border-radius: 10px;
            background: var(--g-surface); color: var(--g-text);
            font-family: var(--g-font); font-size: 0.875rem; transition: border-color 200ms, box-shadow 200ms;
        }
        .cfg-notif-btn:hover { border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.10); }
        .cfg-notif-btn .cfg-notif-ico { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 8px; background: rgba(27,122,66,.10); color: var(--g-primary); flex: none; }
        .cfg-notif-btn .cfg-notif-ico .material-symbols-outlined { font-size: 16px; }
        .cfg-notif-btn .cfg-notif-txt { flex: 1; text-align: left; }
        .cfg-notif-btn .cfg-notif-state { display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; }
        .cfg-notif-btn .cfg-notif-state i { width: 8px; height: 8px; border-radius: 50%; }
        .cfg-notif-state.on { color: #16a34a; } .cfg-notif-state.on i { background: #22c55e; }
        .cfg-notif-state.off { color: var(--g-text-secondary); } .cfg-notif-state.off i { background: var(--g-border); }

        /* ── Modo visor ──────────────────────── */
        .cfg-view { display: block; padding: 8px 24px 24px; }
        .cfg-card.is-editing .cfg-view { display: none; }
        .cfg-view-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 28px; }
        .cfg-view-item { padding: 14px 2px; border-bottom: 1px solid var(--g-border); }
        .cfg-view-label { font-size: 0.72rem; font-weight: 500; color: var(--g-text-secondary); margin-bottom: 4px; }
        .cfg-view-value { font-size: 0.9rem; font-weight: 500; color: var(--g-text); display: flex; align-items: center; gap: 8px; }
        .cfg-view-value.muted { color: var(--g-text-secondary); font-weight: 400; }
        .cfg-view-state { display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; }
        .cfg-view-state i { width: 8px; height: 8px; border-radius: 50%; }
        .cfg-view-state.on { color: #16a34a; } .cfg-view-state.on i { background: #22c55e; }
        .cfg-view-state.off { color: var(--g-text-secondary); } .cfg-view-state.off i { background: var(--g-border); }

        .cfg-btn-ghost {
            height: 40px; padding: 0 18px; border: 1px solid var(--g-border); border-radius: 10px;
            background: transparent; color: var(--g-text); cursor: pointer;
            font-family: var(--g-font); font-weight: 600; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 8px; transition: border-color 200ms, background 200ms;
        }
        .cfg-btn-ghost:hover { border-color: var(--g-primary); color: var(--g-primary); background: rgba(27,122,66,.06); }

        .cfg-tiles { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; padding: 18px 20px 20px; }
        .cfg-tile {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 12px;
            padding: 16px 18px;
        }
        .cfg-tile b { display: block; font-size: 1.6rem; font-weight: 700; color: var(--g-text); line-height: 1.1; }
        .cfg-tile span { display: block; font-size: 0.78rem; color: var(--g-text-secondary); margin-top: 3px; }

        /* ── Modal ───────────────────────────── */
        .cfg-modal-row { display: flex; align-items: center; gap: 14px; padding: 12px 14px; border: 1px solid var(--g-border); border-radius: 12px; margin-bottom: 18px; }
        .cfg-modal-row .cfg-modal-row-txt { flex: 1; }
        .cfg-modal-row .cfg-modal-row-txt b { display: block; font-size: 0.875rem; color: var(--g-text); }
        .cfg-modal-row .cfg-modal-row-txt span { font-size: 0.75rem; color: var(--g-text-secondary); }
        .cfg-switch { position: relative; display: inline-block; flex: none; }
        .cfg-switch input { position: absolute; opacity: 0; width: 100%; height: 100%; margin: 0; cursor: pointer; }
        .cfg-switch-track {
            display: block; width: 42px; height: 24px; border-radius: 999px;
            background: var(--g-surface-variant); border: 1px solid var(--g-border);
            position: relative; transition: background 200ms;
        }
        .cfg-switch-thumb {
            content: ""; width: 18px; height: 18px; border-radius: 50%; background: #fff; position: absolute;
            top: 2px; left: 2px; transition: transform 200ms; box-shadow: 0 1px 2px rgba(0,0,0,.25);
        }
        .cfg-switch input:checked + .cfg-switch-track { background: var(--g-primary); border-color: var(--g-primary); }
        .cfg-switch input:checked + .cfg-switch-track .cfg-switch-thumb { transform: translateX(18px); }
        .cfg-modal-hint { display: flex; gap: 8px; align-items: flex-start; font-size: 0.78rem; color: var(--g-text-secondary); margin-top: 8px; }
        .cfg-modal-hint .material-symbols-outlined { font-size: 16px; margin-top: 1px; }

        @media (max-width: 720px) {
            .cfg-grid { grid-template-columns: 1fr; }
            .cfg-view-grid { grid-template-columns: 1fr; }
            .cfg-tiles { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'configuracion']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Configuración</h1>
            <p class="dev-topbar-subtitle">Ajustes del sistema de asistencia</p>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <div class="cfg-wrap">
                <?php
                    $notifOn = !empty($config['notif_email']);
                    $notifDest = trim($config['notif_email_dest'] ?? '');
                ?>
                <div class="dev-card cfg-card" id="cfg-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">settings</span> Configuración de la empresa</h2>
                        <div class="cfg-head-actions" style="display:flex;gap:8px;align-items:center;">
                            <button type="button" class="cfg-btn-ghost" id="btn-cancel" style="display:none;">
                                <span class="material-symbols-outlined">close</span> Cancelar
                            </button>
                            <button type="button" class="cfg-btn-ghost" id="btn-edit" style="border-color:var(--g-primary);color:var(--g-primary);">
                                <span class="material-symbols-outlined">edit</span> Editar
                            </button>
                        </div>
                    </div>

                    <div class="cfg-view" id="cfg-view">
                        <div class="cfg-view-grid">
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Empresa</div>
                                <div class="cfg-view-value"><?= esc($config['empresa'] ?? 'DSG PERU TECHNOLOGY SAC') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">RUC</div>
                                <div class="cfg-view-value <?= empty($config['ruc']) ? 'muted' : '' ?>"><?= esc($config['ruc'] ?? '—') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Dirección</div>
                                <div class="cfg-view-value <?= empty($config['direccion']) ? 'muted' : '' ?>"><?= esc($config['direccion'] ?? '—') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Email</div>
                                <div class="cfg-view-value <?= empty($config['email']) ? 'muted' : '' ?>"><?= esc($config['email'] ?? '—') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Teléfono</div>
                                <div class="cfg-view-value <?= empty($config['telefono']) ? 'muted' : '' ?>"><?= esc($config['telefono'] ?? '—') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Horario default</div>
                                <div class="cfg-view-value"><?= esc($config['horario_default'] ?? '08:00-17:00') ?></div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Tolerancia default</div>
                                <div class="cfg-view-value"><?= (int) ($config['tolerancia_default'] ?? 15) ?> minutos</div>
                            </div>
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Notificaciones por email</div>
                                <div class="cfg-view-value">
                                    <span class="cfg-view-state <?= $notifOn ? 'on' : 'off' ?>"><i></i> <?= $notifOn ? 'Activo' : 'Inactivo' ?></span>
                                    <?php if ($notifOn && $notifDest): ?><span class="cfg-view-label" style="margin:0;">· <?= esc($notifDest) ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cfg-edit" id="cfg-edit">
                        <form action="<?= site_url('admin/save-config') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="cfg-form">
                                <div class="cfg-grid">
                                    <div class="cfg-field">
                                        <label for="empresa">Empresa</label>
                                        <input type="text" id="empresa" name="cfg_empresa" value="<?= esc($config['empresa'] ?? '') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="ruc">RUC</label>
                                        <input type="text" id="ruc" name="cfg_ruc" value="<?= esc($config['ruc'] ?? '') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" id="direccion" name="cfg_direccion" value="<?= esc($config['direccion'] ?? '') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="cfg_email" value="<?= esc($config['email'] ?? '') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" id="telefono" name="cfg_telefono" value="<?= esc($config['telefono'] ?? '') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="horario_default">Horario default</label>
                                        <input type="text" id="horario_default" name="cfg_horario_default" value="<?= esc($config['horario_default'] ?? '08:00-17:00') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label for="tolerancia_default">Tolerancia default (minutos)</label>
                                        <input type="number" id="tolerancia_default" name="cfg_tolerancia" min="0" max="60" value="<?= esc($config['tolerancia_default'] ?? '15') ?>" required>
                                    </div>
                                    <div class="cfg-field">
                                        <label>Notificaciones por email</label>
                                        <button type="button" class="cfg-notif-btn" id="btn-notif">
                                            <span class="cfg-notif-ico"><span class="material-symbols-outlined">notifications</span></span>
                                            <span class="cfg-notif-txt">Configurar correo</span>
                                            <span class="cfg-notif-state <?= $notifOn ? 'on' : 'off' ?>"><i></i> <?= $notifOn ? 'Activo' : 'Inactivo' ?></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="cfg-actions">
                                    <button type="submit" class="cfg-btn">
                                        <span class="material-symbols-outlined">save</span> Guardar configuración
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">info</span> Información del sistema</h2>
                    </div>
                    <div class="cfg-tiles">
                        <div class="cfg-tile">
                            <b><?= $totalEmpleados ?? 0 ?></b>
                            <span>Empleados</span>
                        </div>
                        <div class="cfg-tile">
                            <b><?= count($schedules ?? []) ?></b>
                            <span>Horarios</span>
                        </div>
                        <div class="cfg-tile">
                            <b><?= count($incidents ?? []) ?></b>
                            <span>Incidencias</span>
                        </div>
                        <div class="cfg-tile">
                            <b>1.0.0</b>
                            <span>Versión</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="dev-modal-overlay" id="modal-notif">
        <div class="dev-modal">
            <div class="dev-modal-head">
                <h2><span class="material-symbols-outlined">notifications</span> Notificaciones por email</h2>
                <button type="button" class="dev-modal-close" data-mc><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="<?= site_url('admin/save-config') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="dev-modal-body">
                    <div class="cfg-modal-row">
                        <div class="cfg-modal-row-txt">
                            <b>Activar notificaciones</b>
                            <span>Avisos de asistencias e incidencias</span>
                        </div>
                        <label class="cfg-switch">
                            <input type="hidden" name="cfg_notif_email" value="0">
                            <input type="checkbox" name="cfg_notif_email" value="1" <?= $notifOn ? 'checked' : '' ?>>
                            <span class="cfg-switch-track"><span class="cfg-switch-thumb"></span></span>
                        </label>
                    </div>
                    <div class="cfg-field">
                        <label for="notif_email_dest">Email destinatario</label>
                        <input type="email" id="notif_email_dest" name="cfg_notif_email_dest" value="<?= esc($config['notif_email_dest'] ?? '') ?>" placeholder="correo@empresa.com">
                    </div>
                    <div class="cfg-modal-hint">
                        <span class="material-symbols-outlined">info</span>
                        <span>Los avisos de asistencia e incidencias del personal se enviarán a esta dirección.</span>
                    </div>
                </div>
                <div class="dev-modal-footer">
                    <button type="button" class="dev-btn dev-btn-text" data-mc>Cancelar</button>
                    <button type="submit" class="cfg-btn">
                        <span class="material-symbols-outlined">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const card = document.getElementById('cfg-card');
        const btnEdit = document.getElementById('btn-edit');
        const btnCancel = document.getElementById('btn-cancel');
        const btnNotif = document.getElementById('btn-notif');
        const overlay = document.getElementById('modal-notif');
        if (!card || !btnEdit || !btnCancel) return;

        btnEdit.addEventListener('click', function () { card.classList.add('is-editing'); btnEdit.style.display = 'none'; btnCancel.style.display = ''; });
        btnCancel.addEventListener('click', function () { card.classList.remove('is-editing'); btnEdit.style.display = ''; btnCancel.style.display = 'none'; });

        if (btnNotif && overlay) {
            btnNotif.addEventListener('click', function () { overlay.classList.add('is-open'); });
            overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) overlay.classList.remove('is-open'); });
            overlay.querySelectorAll('[data-mc]').forEach(function (b) { b.addEventListener('click', function () { overlay.classList.remove('is-open'); }); });
        }
    })();
    </script>
</body>
</html>