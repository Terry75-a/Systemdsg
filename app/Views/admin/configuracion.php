<!--[ configuracion · estilos v3 · EduconectaV2: píldora .lg-submit · modales r20 · si esta línea NO sale en tu "ver código fuente", tu navegador está sirviendo copia vieja ]-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración · Panel de Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
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
            height: 44px; padding: 0 24px; border: 0; border-radius: 50px; cursor: pointer;
            background: var(--g-primary); color: #fff; font-weight: 600;
            font-size: 0.875rem; font-family: var(--g-font);
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: none; transition: none;
        }
        .cfg-btn:hover { background: color-mix(in srgb, var(--g-primary) 88%, #000); }
        .cfg-btn .material-symbols-outlined { font-size: 18px; }

        .cfg-notif-btn {
            width: 100%; height: 44px; padding: 0 18px; box-sizing: border-box;
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            border: 1px solid color-mix(in srgb, var(--g-primary) 45%, transparent); border-radius: 50px;
            background: var(--g-primary-light); color: var(--g-primary);
            font-family: var(--g-font); font-size: 0.875rem; transition: none;
        }
        .cfg-notif-btn:hover { border-color: var(--sb-primary); }
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
            height: 40px; padding: 0 18px; border: 1px solid var(--g-border); border-radius: 50px;
            background: transparent; color: var(--g-text); cursor: pointer;
            font-family: var(--g-font); font-weight: 600; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 8px; transition: none;
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
            display: block; width: 42px; height: 24px; border-radius: 50px;
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

        @media (max-width: 720px) {            .cfg-grid { grid-template-columns: 1fr; }
            .cfg-view-grid { grid-template-columns: 1fr; }
            .cfg-tiles { grid-template-columns: 1fr 1fr; }
        }

        /* Push toggle: mismo estilo que dark-mode del sidebar; estado ON propio */
        #pushWrap:not(.is-on) .dark-toggle-track { background: var(--g-border); }
        #pushWrap:not(.is-on) .dark-toggle-thumb { transform: translateX(0); }
        #pushWrap.is-on .dark-toggle-track { background: var(--g-primary); }
        #pushWrap.is-on .dark-toggle-thumb { transform: translateX(20px); }

        /* ── Toast estilo snackbar (mismo lenguaje visual del panel) ── */
        .push-toast-wrap { position: fixed; right: 18px; bottom: 18px; display: flex; flex-direction: column; gap: 10px; z-index: 1100; }
        .push-toast {
            display: flex; align-items: flex-start; gap: 12px;
            background: #1f1f1f; color: #e8eaed;
            border-radius: 12px; padding: 14px 16px;
            min-width: 300px; max-width: 380px;
            box-shadow: 0 6px 20px rgba(0,0,0,.28);
            animation: pushToastIn .3s cubic-bezier(.16,1,.3,1);
        }
        .push-toast .material-symbols-outlined { font-size: 20px; color: #81c995; flex-shrink: 0; margin-top: 1px; }
        .push-toast b { display: block; font-size: .875rem; font-weight: 600; color: #fff; }
        .push-toast span { display: block; margin-top: 2px; font-size: .78rem; color: #bdc1c6; line-height: 1.45; }
        .push-toast.t-error .material-symbols-outlined { color: #f28b82; }
        .push-toast.t-warn .material-symbols-outlined { color: #fdd663; }
        html.dark .push-toast { background: #2a2a2a; border: 1px solid #3c4043; }
        @keyframes pushToastIn { from { opacity: 0; transform: translateY(12px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* ═══════════════════════════════════════════════════════════════
           CONFIGURACIÓN · panel admin de sorteroOS
           Las tres tarjetas con marco se vuelven secciones: el título
           (11px, mayúsculas) sobre el fondo de página y el contenido en
           un bloque blanco redondeado, sin borde ni sombra.
           ═══════════════════════════════════════════════════════════════ */
        body { --cfg-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }

        /* ── Título de sección ── */
        .cfg-sec-head { margin: 0 0 12px; }
        .cfg-sec-head--split { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .cfg-sec-head h2 {
            margin: 0; font-family: inherit; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .cfg-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }
        .cfg-hint { font-size: 12px; color: var(--sb-on-surface-variant); }

        /* ── La tarjeta de la empresa queda transparente ── */
        body:has(.dev-main) .cfg-card {
            background: transparent; border: 0; border-radius: 0; box-shadow: none;
        }
        .cfg-view, .cfg-form {
            background: var(--sb-surface); border-radius: 16px;
        }
        .cfg-view { padding: 6px 24px 22px; }
        .cfg-form { padding: 20px 24px 24px; }
        .cfg-view-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 28px; }
        .cfg-view-item { padding: 14px 2px; border-bottom: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent); }
        .cfg-view-item:last-child, .cfg-view-item:nth-last-child(2):nth-child(odd) { border-bottom: 0; }
        .cfg-view-label {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent); margin-bottom: 4px;
        }
        .cfg-view-value { font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); display: flex; align-items: center; gap: 8px; }
        .cfg-view-value.muted { color: var(--sb-on-surface-variant); font-weight: 400; }

        /* ── Campos: superficie gris sin borde ── */
        .cfg-field label, .cfg-cap {
            display: block; font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent);
            margin-bottom: 6px; font-family: var(--g-font);
        }
        .cfg-field input {
            width: 100%; height: 44px; padding: 0 14px; box-sizing: border-box;
            border: 0; border-radius: 12px;
            background: var(--sb-surface-container); color: var(--sb-on-surface);
            font-family: var(--g-font); font-size: 13.5px;
            transition: box-shadow 150ms ease;
        }
        .cfg-field input::placeholder { color: color-mix(in srgb, var(--sb-on-surface-variant) 45%, transparent); }
        .cfg-field input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }

        /* ── Botones en píldora ── */
        .cfg-btn {
            height: 40px; padding: 0 20px; border: 0; border-radius: 50px; cursor: pointer;
            background: var(--sb-primary); color: #fff; font-weight: 600;
            font-size: 13.5px; font-family: var(--g-font);
            display: inline-flex; align-items: center; gap: 8px;
            transition: none;
        }
        .cfg-btn:hover { background: color-mix(in srgb, var(--sb-primary) 88%, #000); }
        .cfg-btn .material-symbols-outlined { font-size: 18px; }
        .cfg-btn-ghost {
            height: 36px; padding: 0 16px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 50px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            cursor: pointer; font-family: var(--g-font); font-weight: 500; font-size: 13px;
            display: inline-flex; align-items: center; gap: 7px;
            transition: none;
        }
        .cfg-btn-ghost:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }
        .cfg-btn-ghost .material-symbols-outlined { font-size: 17px; }
        .cfg-btn-primary-ghost { border-color: color-mix(in srgb, var(--sb-primary) 45%, transparent); color: var(--sb-primary); }
        .cfg-btn-soft { background: var(--sb-surface-container); color: var(--sb-on-surface); }
        .cfg-btn-soft:hover { filter: none; background: var(--sb-surface-container-high); }
        .cfg-check {
            display: flex; align-items: center; gap: 8px; font-size: 13px;
            cursor: pointer; color: var(--sb-on-surface);
            text-transform: none; letter-spacing: 0; font-weight: 400; margin-bottom: 0;
        }
        .cfg-btn-primary-ghost:hover { background: color-mix(in srgb, var(--sb-primary) 8%, transparent); color: var(--sb-primary); }
        .cfg-head-actions { display: flex; gap: 8px; align-items: center; }
        .cfg-actions {
            display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; padding-top: 18px;
            border-top: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent);
        }
        .cfg-notif-btn {
            width: 100%; height: 44px; padding: 0 14px; box-sizing: border-box;
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent); border-radius: 12px;
            background: var(--sb-surface-container); color: var(--sb-on-surface);
            font-family: var(--g-font); font-size: 13.5px;
            transition: background 150ms ease, border-color 150ms ease;
        }
        .cfg-notif-btn:hover { border-color: var(--sb-outline-variant); background: var(--sb-surface-container-high); box-shadow: none; }
        .cfg-notif-btn .cfg-notif-ico { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 8px; background: color-mix(in srgb, var(--sb-primary) 12%, transparent); color: var(--sb-primary); flex: none; }
        .cfg-notif-btn .cfg-notif-ico .material-symbols-outlined { font-size: 16px; }

        /* ── Métricas del sistema ── */
        .cfg-tiles { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 16px; }
        .cfg-tile {
            background: var(--cfg-surface);
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 40%, transparent);
            border-radius: 16px; padding: 16px;
            display: flex; flex-direction: column; align-items: stretch;
        }
        .cfg-tile b {
            display: block; font-size: 24px; font-weight: 700; letter-spacing: -.02em; line-height: 1.2;
            color: var(--sb-on-surface); order: 2; font-variant-numeric: tabular-nums;
        }
        .cfg-tile span {
            display: block; font-size: 12px; font-weight: 500; color: var(--sb-on-surface-variant);
            margin-top: 0; order: 1;
        }
        .cfg-tile span::before { content: ''; }

        /* ── Fila del toggle ── */
        .cfg-push {
            background: var(--sb-surface); border: 0; border-radius: 16px;
            padding: 6px 18px; margin: 0;
        }

        /* ── Días festivos ── */
        .cfg-fest-form { margin-bottom: 14px; }
        .cfg-fest-row { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; }
        .cfg-field--date { flex: 0 0 180px; }
        .cfg-field--grow { flex: 1 1 240px; }
        .cfg-fest-list {
            display: flex; flex-direction: column; gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px; overflow: hidden;
        }
        .cfg-fest-list > p { margin: 0; padding: 18px 20px; background: var(--sb-surface); font-size: 13px; color: var(--sb-on-surface-variant); }
        .cfg-fest-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px;
            background: var(--sb-surface);
        }
        .cfg-fest-item > .material-symbols-outlined { color: var(--sb-primary); font-size: 19px; }
        .cfg-fest-item > div { flex: 1; }
        .cfg-fest-item b { display: block; font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); }
        .cfg-fest-item span { display: block; font-size: 12px; color: var(--sb-on-surface-variant); }
        .cfg-fest-item .cfg-btn-ghost { color: #dc2626; border-color: color-mix(in srgb, #dc2626 35%, transparent); }
        .cfg-fest-item .cfg-btn-ghost:hover { background: color-mix(in srgb, #dc2626 10%, transparent); color: #dc2626; }

        @media (max-width: 900px) {
            .cfg-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            .cfg-view-grid { grid-template-columns: 1fr; }
            .cfg-view-item:nth-last-child(2):nth-child(odd) { border-bottom: 1px solid color-mix(in srgb, var(--sb-outline-variant) 55%, transparent); }
            .cfg-fest-row { flex-direction: column; align-items: stretch; }
            .cfg-field--date, .cfg-field--grow { flex: 1 1 auto; }
        }

        /* ── Red de seguridad EduconectaV2 ──
           Mismas decisiones que educonecta.css (4c píldora · 7 modales
           r20 flat): si el SW todavía sirve una hoja vieja, la página
           se ve bien igual porque el <style> llega siempre fresco. */
        button, [class*="btn"], [class*="-close"], [class*="-chip"] {
            border-radius: 50px !important;
        }
        .dev-modal {
            border-radius: 20px;
            border: 1px solid var(--sb-outline-variant);
            box-shadow: none;
        }
        .dev-modal-overlay {
            background: rgba(0, 0, 0, 0.32);
            backdrop-filter: none !important;
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'configuracion']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1 class="dev-topbar-title">Configuración</h1>
                    <p class="dev-topbar-subtitle">Ajustes del sistema de asistencia</p>
                </div>
            </div>
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
                <div class="cfg-card" id="cfg-card">
                    <div class="cfg-sec-head cfg-sec-head--split">
                        <h2><span class="material-symbols-outlined">settings</span> Configuración de la empresa</h2>
                        <div class="cfg-head-actions">
                            <button type="button" class="cfg-btn-ghost" id="btn-cancel" style="display:none;">
                                <span class="material-symbols-outlined">close</span> Cancelar
                            </button>
                            <button type="button" class="cfg-btn-ghost cfg-btn-primary-ghost" id="btn-edit">
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
                            <div class="cfg-view-item">
                                <div class="cfg-view-label">Zona de trabajo (geo)</div>
                                <div class="cfg-view-value">
                                    <?php if ((int) ($config['geo_requerido'] ?? 0) === 1 && ! empty($config['geo_lat'])): ?>
                                        Radio <?= (int) ($config['geo_radio_m'] ?? 0) ?> m · <?= esc($config['geo_lat'] . ', ' . $config['geo_lng']) ?>
                                    <?php else: ?>
                                        <span class="muted">No requerida</span>
                                    <?php endif; ?>
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
                                <div class="cfg-grid" style="margin-top:14px;">
                                    <div class="cfg-field" style="grid-column:1/-1;">
                                        <label>Zona de trabajo (geolocalización para marcar)</label>
                                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                                            <div class="cfg-field" style="flex:1;min-width:130px;margin:0;">
                                                <label for="geo_lat" style="font-size:12px;">Latitud</label>
                                                <input type="text" id="geo_lat" name="cfg_geo_lat" inputmode="decimal" value="<?= esc($config['geo_lat'] ?? '') ?>" placeholder="-12.0464">
                                            </div>
                                            <div class="cfg-field" style="flex:1;min-width:130px;margin:0;">
                                                <label for="geo_lng" style="font-size:12px;">Longitud</label>
                                                <input type="text" id="geo_lng" name="cfg_geo_lng" inputmode="decimal" value="<?= esc($config['geo_lng'] ?? '') ?>" placeholder="-77.0428">
                                            </div>
                                            <div class="cfg-field" style="flex:1;min-width:130px;margin:0;">
                                                <label for="geo_radio_m" style="font-size:12px;">Radio (metros)</label>
                                                <input type="number" id="geo_radio_m" name="cfg_geo_radio_m" min="0" max="100000" value="<?= esc($config['geo_radio_m'] ?? '0') ?>">
                                            </div>
                                            <button type="button" class="cfg-btn cfg-btn-soft" id="cfg-geo-btn">
                                                <span class="material-symbols-outlined">my_location</span> Mi ubicación
                                            </button>
                                            <label class="cfg-check">
                                                <input type="checkbox" name="cfg_geo_requerido" value="1" <?= (int) ($config['geo_requerido'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                Obligatoria para marcar
                                            </label>
                                        </div>
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

                <div class="cfg-sec-head">
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
                <div class="dark-toggle-wrap cfg-push" id="pushWrap">
                    <button type="button" class="dark-toggle" id="pushBtn">
                        <span class="material-symbols-outlined">notifications_active</span>
                        <span>Notificaciones push</span>
                    </button>
                    <div class="dark-toggle-track" id="pushTrack">
                        <div class="dark-toggle-thumb" id="pushThumb"></div>
                    </div>
                </div>

                <div class="cfg-sec-head cfg-sec-head--split">
                    <h2><span class="material-symbols-outlined">event_busy</span> Días festivos</h2>
                    <span class="cfg-hint">Esos días no se cierra asistencia ni se marcan faltas</span>
                </div>
                <div class="cfg-form cfg-fest-form">
                    <form action="<?= site_url('admin/festivos/guardar') ?>" method="POST" class="cfg-fest-row">
                        <?= csrf_field() ?>
                        <div class="cfg-field cfg-field--date">
                            <span class="cfg-cap">Fecha</span>
                            <input type="date" id="fest_fecha" name="fest_fecha" required>
                        </div>
                        <div class="cfg-field cfg-field--grow">
                            <span class="cfg-cap">Nombre del día</span>
                            <input type="text" id="fest_nombre" name="fest_nombre" maxlength="120" placeholder="Fiestas Patrias" required>
                        </div>
                        <button type="submit" class="cfg-btn">
                            <span class="material-symbols-outlined">add</span> Agregar
                        </button>
                    </form>
                </div>
                <div class="cfg-fest-list">
                            <?php $listaFestivos = $festivos ?? []; ?>
                            <?php if (empty($listaFestivos)): ?>
                                <p style="font-size:.85rem;color:var(--g-text-secondary);margin:6px 0 0;">Aún no hay festivos registrados.</p>
                            <?php else: ?>
                                    <?php foreach ($listaFestivos as $f): ?>
                                        <div class="cfg-fest-item">
                                            <span class="material-symbols-outlined">event</span>
                                            <div>
                                                <b><?= esc($f['nombre']) ?></b>
                                                <span><?= date('d/m/Y', strtotime($f['fecha'])) ?></span>
                                            </div>
                                            <form action="<?= site_url('admin/festivos/eliminar') ?>" method="POST" data-confirm-delete='{"message":"¿Eliminar este festivo?"}'>
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="fest_id" value="<?= (int) $f['id'] ?>">
                                                <button type="submit" class="cfg-btn-ghost">
                                                    <span class="material-symbols-outlined">delete</span> Quitar
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                            <?php endif; ?>
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

    <script>window.__sseDisponible = <?= json_encode(PHP_SAPI !== 'cli-server') ?>;</script>
    <script src="<?= base_url('js/realtime.js') ?>"></script>
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

        // Botón "Mi ubicación" → rellena lat/lng del formulario
        const geoBtn = document.getElementById('cfg-geo-btn');
        if (geoBtn && navigator.geolocation) {
            geoBtn.addEventListener('click', function () {
                geoBtn.disabled = true;
                navigator.geolocation.getCurrentPosition(function (pos) {
                    document.getElementById('geo_lat').value = pos.coords.latitude.toFixed(7);
                    document.getElementById('geo_lng').value = pos.coords.longitude.toFixed(7);
                    if (!document.getElementById('geo_radio_m').value || document.getElementById('geo_radio_m').value === '0') {
                        document.getElementById('geo_radio_m').value = '150';
                    }
                    geoBtn.disabled = false;
                }, function () {
                    geoBtn.disabled = false;
                    alert('No se pudo obtener tu ubicación. Revisa el permiso del navegador.');
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
        } else if (geoBtn) {
            geoBtn.disabled = true;
        }
    })();

    // Push notifications toggle (mismo estilo que dark-mode del sidebar)
    (function () {
        const wrap = document.getElementById('pushWrap');
        const btn = document.getElementById('pushBtn');
        const track = document.getElementById('pushTrack');
        if (!wrap || !btn || !track) return;

        let pollTimer = null;
        let lastNotifId = 0;

        function iconoNotif(tipo) {
            tipo = String(tipo || '');
            if (tipo.indexOf('horario') >= 0) return 'event';
            if (tipo.indexOf('incidencia') >= 0) return 'report';
            if (tipo.indexOf('asistencia') >= 0) return 'fact_check';
            return 'notifications_active';
        }

        function toastPush(n, opts) {
            opts = opts || {};
            let box = document.querySelector('.push-toast-wrap');
            if (!box) {
                box = document.createElement('div');
                box.className = 'push-toast-wrap';
                document.body.appendChild(box);
            }
            const t = document.createElement('div');
            t.className = 'push-toast' + (opts.type ? ' t-' + opts.type : '');
            t.innerHTML = '<span class="material-symbols-outlined">' + (opts.icon || 'notifications_active') + '</span>'
                + '<div><b>' + (n.titulo || 'Notificación') + '</b><span>' + (n.mensaje || '') + '</span></div>';
            box.appendChild(t);
            setTimeout(function () { t.remove(); }, 6000);

            // También notificación del navegador si hay permiso
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    new Notification(n.titulo || 'Notificación', {
                        body: n.mensaje || '',
                        icon: '<?= base_url('images/logo_3.1.png') ?>',
                        tag: 'dsg-push'
                    });
                } catch (e) { /* soporte limitado */ }
            }
        }

        function ensureClient() {
            if (window.realtimeAdmin) return window.realtimeAdmin;
            window.realtimeAdmin = new RealtimeClient('/admin/realtime/asistencias');
            window.realtimeAdmin.on('notificacion', function (data) {
                const n = data.datos || data;
                toastPush(n, { icon: iconoNotif(n.tipo) });
                if (window._notifBadgeInc) window._notifBadgeInc();
            });
            return window.realtimeAdmin;
        }

        function startPolling() {
            if (pollTimer) return;
            pollTimer = setInterval(function () {
                fetch('<?= base_url('admin/notificaciones/poll') ?>?since=' + lastNotifId, { credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        if (d.ok && d.notificaciones && d.notificaciones.length) {
                            d.notificaciones.forEach(function (n) {
                                lastNotifId = Math.max(lastNotifId, parseInt(n.id) || 0);
                                toastPush(n, { icon: iconoNotif(n.tipo) });
                                if (window._notifBadgeInc) window._notifBadgeInc();
                            });
                        }
                    })
                    .catch(function () {});
            }, 20000);
        }

        function stopPolling() {
            if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
        }

        function connectPush() {
            if ('EventSource' in window && window.__sseDisponible !== false) {
                ensureClient().connect();
            } else {
                startPolling();
            }
        }

        function setPush(on, recordar) {
            if (recordar !== false) {
                localStorage.setItem('admin_push_enabled', on ? 'true' : 'false');
            }
            wrap.classList.toggle('is-on', on);

            if (on) {
                connectPush();
            } else {
                if (window.realtimeAdmin) window.realtimeAdmin.disconnect();
                stopPolling();
            }
        }

        function togglePush() {
            const activando = !wrap.classList.contains('is-on');

            if (activando) {
                // ═══ ACTIVAR: pedir permiso del navegador ═══
                if ('Notification' in window) {
                    if (Notification.permission === 'granted') {
                        setPush(true);
                        toastPush({ titulo: 'Notificaciones activadas', mensaje: 'Recibirás alertas en tiempo real de asistencias e incidencias.' }, { icon: 'check_circle' });
                        return;
                    }
                    if (Notification.permission === 'denied') {
                        wrap.classList.remove('is-on');
                        localStorage.setItem('admin_push_enabled', 'false');
                        toastPush({ titulo: 'Permiso denegado', mensaje: 'Actívalo en los ajustes del navegador (candado en la barra de direcciones) para recibir alertas.' }, { icon: 'block', type: 'error' });
                        return;
                    }
                    // permission === 'default' → pedir (gesto de clic válido)
                    Notification.requestPermission().then(function (perm) {
                        if (perm === 'granted') {
                            setPush(true);
                            toastPush({ titulo: 'Notificaciones activadas', mensaje: 'Recibirás alertas en tiempo real de asistencias e incidencias.' }, { icon: 'check_circle' });
                        } else {
                            wrap.classList.remove('is-on');
                            localStorage.setItem('admin_push_enabled', 'false');
                            toastPush({ titulo: 'Permiso denegado', mensaje: 'Sin permiso no se pueden mostrar alertas. Actívalo en los ajustes del navegador.' }, { icon: 'block', type: 'error' });
                        }
                    });
                    return;
                }
                // Navegador sin soporte de Notification: solo toasts en pantalla
                setPush(true);
                toastPush({ titulo: 'Notificaciones activadas (en pantalla)', mensaje: 'Este navegador no muestra alertas del sistema; verás los avisos dentro del panel.' }, { icon: 'check_circle' });
                return;
            }

            // ═══ DESACTIVAR: mensaje de desactivación ═══
            setPush(false);
            toastPush({ titulo: 'Notificaciones desactivadas', mensaje: 'Ya no recibirás alertas en tiempo real. Puedes activarlas de nuevo cuando quieras.' }, { icon: 'notifications_off', type: 'warn' });
        }

        btn.addEventListener('click', togglePush);
        track.addEventListener('click', togglePush);

        // Restaurar estado guardado al cargar (sin pedir permiso: requiere clic)
        if (localStorage.getItem('admin_push_enabled') === 'true') {
            setPush(true, false);
        }
    })();
    </script>

    <!-- Campana de notificaciones (compartida en todas las secciones admin) -->
    <?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>