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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <style>
        /* Métricas: mismas tarjetas del dashboard (dev-stats-grid +
           dev-stat-card, sobreescritas en next-panel.css:420-462).
           Sólo se añade la variante azul del chip QR, la que ya
           llevaban los códigos de registro (el resto de si-* están
           en dev-panel.css). */
        .dev-stat-icon.si-blue { background: #e8f0fe; color: #1a73e8; }
        html.dark .dev-stat-icon.si-blue { background: rgba(26,115,232,.16); color: #8ab4f8; }

        .personal-section { margin-bottom: 24px; scroll-margin-top: 84px; } /* salta las tarjetas sin quedar bajo el topbar sticky */
        .personal-section-head {
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
            gap: 12px; margin-bottom: 16px;
        }
        .pf-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .pf-input {
            height: 36px; padding: 0 12px 0 34px; border: 1px solid var(--g-border); border-radius: 10px;
            background: var(--g-surface); color: var(--g-text); font-size: 13px; font-family: inherit;
            outline: none; min-width: 210px;
        }
        .pf-input:focus { border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.12); }
        .pf-search { position: relative; display: flex; align-items: center; }
        .pf-search > .material-symbols-outlined {
            position: absolute; left: 10px; font-size: 17px; color: var(--g-text-secondary); pointer-events: none;
        }
        .pf-select {
            height: 36px; padding: 0 30px 0 11px; border: 1px solid var(--g-border); border-radius: 10px;
            background: var(--g-surface); color: var(--g-text); font-size: 13px; font-family: inherit;
            outline: none; cursor: pointer;
        }
        .pf-select:focus { border-color: var(--g-primary); }
        .pf-clear {
            height: 36px; padding: 0 12px; border: 1px solid var(--g-border); border-radius: 10px;
            background: transparent; color: var(--g-text-secondary); font-size: 13px; font-family: inherit; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .pf-clear:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .pf-clear .material-symbols-outlined { font-size: 16px; }
        .pf-empty { display: none; text-align: center; padding: 40px 16px; color: var(--g-text-secondary); font-size: 14px; }
        @media (max-width: 768px) {
            .pf-bar { width: 100%; }
            .pf-search, .pf-input { width: 100%; min-width: 0; }
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
            .personal-table-wrap { display: none; }
            .dev-table-mobile { display: flex !important; flex-direction: column; gap: 12px; }
            .personal-code-item { padding: 16px; flex-wrap: wrap; }
            .personal-codes { gap: 10px; }
            .personal-code-right { width: 100%; justify-content: space-between; }
        }
        @media (max-width: 768px) {
            .m-card-actions { justify-content: flex-end; }
            .m-card-actions .m-card-btn {
                position: relative;
                flex: 0 0 auto; width: 42px; height: 42px; min-width: 42px; padding: 0;
                font-size: 0; justify-content: center;
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
            font-size: 11px; padding: 4px 10px; border-radius: 50px;
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
            background: var(--g-primary-light); color: var(--g-primary);
        }
        .m-card-btn:hover { background: color-mix(in srgb, var(--sb-primary) 22%, var(--g-primary-light)); color: var(--g-primary); }
        .m-card-btn .material-symbols-outlined { font-size: 18px; }
        .m-card-btn-danger { color: var(--g-error); background: var(--g-error-light); }
        .m-card-btn-danger:hover { background: #f8d7da; }
        .personal-table tbody tr { cursor: pointer; }

        /* Detail Modal — canon EduconectaV2: los dos modales de
           detalle usan dev-modal-overlay/dev-modal (r20 · hairline ·
           overlay neutro sin blur · sin elevación · footer con los
           dev-btn del sistema). Aquí sólo va la fila de identidad
           (chip de avatar + rol) que va dentro del body. */
        .detail-who {
            display: flex; align-items: center; gap: 14px;
            padding: 4px 0 18px;
        }
        /* El JS ya no pinta el gradiente, pero si algún día vuelve a
           hacerlo con estilo inline, Educonecta es flat: se neutraliza */
        #detailCover, #codeDetailCover { background: transparent !important; }
        .detail-avatar {
            width: 48px; height: 48px; border-radius: 14px; flex: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 19px; font-weight: 500;
            background: var(--g-primary-light); color: var(--g-primary);
            font-family: var(--g-font-display);
        }
        .detail-sub { font-size: 13px; color: var(--g-text-secondary); min-width: 0; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label {
            font-size: 11px; font-weight: 500; color: var(--g-text-secondary);
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .detail-value {
            font-size: 14px; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--sb-surface-container); border-radius: 12px;
            overflow-wrap: anywhere;
        }
        .detail-value code { font-family: 'SF Mono', Consolas, monospace; font-size: 13px; }

        /* ═══════════════════════════════════════════════════════════════
           PERSONAL · panel admin de sorteroOS

           Mismo patrón que en Horarios y taken del diseño real en
           ejecución de sorteroOS (/admin/usuarios y /admin/clubes):

             · métricas con la acción principal al lado
             · buscador y filtros en píldora, no en cajas con borde
             · tabla con la tipografía de .sb-table (cabecera 10px
               uppercase al 70%, filas de 48px, hairlines de 1px)
             · acciones de fila como iconos sueltos, sin recuadro

           La app usa fondo gris (#f4f4f5), así que —igual que en
           sorteroOS, donde su página es blanca— las superficies se marcan
           mezclando el outline-variant con la surface.
           ═══════════════════════════════════════════════════════════════ */
        body { --pf-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }

        /* ── Métricas + acciones ──
           Las métricas son ahora las MISMAS tarjetas del dashboard:
           dev-stats-grid + dev-stat-card con sus overrides de
           next-panel.css:420-462 (bloque pegado con hairline, icono
           16px neutro arriba, label 10px uppercase, valor bold).
           Aquí sólo se reserva el ancho dentro de la fila para que
           los botones queden al lado, igual que en admin/usuarios. */
        body:has(.dev-main) .personal-stats-row {
            display: flex; align-items: stretch; gap: 16px; margin-bottom: 18px;
        }
        body:has(.dev-main) .personal-stats-row .dev-stats-grid {
            flex: 1 1 auto; min-width: 0; margin-bottom: 0;
        }
        /* ── Acciones: botones EduconectaV2 píldora (regla del .lg-submit
           del login verde) · SIN animaciones: hover plano sin moverse,
           sin sombra y sin rebote */
        body:has(.dev-main) .personal-new-actions {
            display: flex; align-items: center; gap: 12px; flex: none;
        }
        body:has(.dev-main) .personal-new-actions .dev-btn {
            height: 48px; padding: 0 26px; border-radius: 50px !important;
            font-size: 14px; font-weight: 600; letter-spacing: 0;
            border: 0; box-shadow: none;
            transform: none; transition: none; will-change: auto;
        }
        body:has(.dev-main) .personal-new-actions .dev-btn .material-symbols-outlined { font-size: 19px; }

        /* ── Buscador y filtros en píldora ──
           El buscador usa .sb-input (sb-ui/styles.css:497): sin borde,
           superficie surface-container-high, radio 16. Las píldoras de
           filtro usan rounded-full border px-3.5 py-1.5 text-[13px]
           font-medium, con la activa en primary y la inactiva con borde
           outline-variant/50. */
        body:has(.dev-main) .pf-bar { gap: 8px; width: 100%; }
        body:has(.dev-main) .personal-section-head { flex-direction: column; align-items: stretch; gap: 14px; }
        body:has(.dev-main) .pf-search { flex: 1 1 240px; max-width: 460px; }
        body:has(.dev-main) .pf-input {
            width: 100%; min-width: 0; height: 44px; padding: 0 18px 0 42px;
            border: none; border-radius: 16px; background: var(--sb-surface-container-high);
            color: var(--sb-on-surface); font-size: 13.5px; font-weight: 400;
            transition: box-shadow 250ms ease;
        }
        body:has(.dev-main) .pf-input::placeholder { color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent); }
        body:has(.dev-main) .pf-input:focus {
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }
        body:has(.dev-main) .pf-search > .material-symbols-outlined {
            left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;
            color: var(--sb-on-surface-variant);
        }
        /* Píldoras de filtro */
        body:has(.dev-main) .pf-select {
            height: 36px; padding: 0 36px 0 14px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 50px; background: var(--sb-surface); color: var(--sb-on-surface-variant);
            font-size: 13px; font-weight: 500; appearance: none; -webkit-appearance: none;
            cursor: pointer; transition: background 150ms ease, border-color 150ms ease;
        }
        body:has(.dev-main) .pf-select:hover { background: var(--sb-surface-container-high); }
        body:has(.dev-main) .pf-select:focus {
            border-color: var(--sb-primary);
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }
        body:has(.dev-main) .pf-select-wrap { position: relative; display: inline-flex; align-items: center; }
        body:has(.dev-main) .pf-select-wrap > .material-symbols-outlined {
            position: absolute; right: 12px; font-size: 16px; pointer-events: none;
            color: var(--sb-on-surface-variant);
        }
        /* Píldora con filtro aplicado: la activa de sorteroOS (primary) */
        body:has(.dev-main) .pf-select-wrap.on .pf-select {
            background: var(--sb-primary); border-color: var(--sb-primary); color: #fff;
        }
        body:has(.dev-main) .pf-select-wrap.on > .material-symbols-outlined { color: #fff; }
        body:has(.dev-main) .pf-clear {
            height: 36px; padding: 0 14px; border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 50%, transparent);
            border-radius: 50px; background: transparent; color: var(--sb-on-surface-variant);
            font-size: 13px; font-weight: 500;
            transition: background 150ms ease, color 150ms ease;
        }
        body:has(.dev-main) .pf-clear:hover { background: var(--sb-surface-container-high); color: var(--sb-on-surface); }

        /* ── Títulos de sección: el contador va en píldora ── */
        body:has(.dev-main) .personal-section-title { font-size: 16px; font-weight: 500; }
        body:has(.dev-main) .personal-section-title .material-symbols-outlined { font-size: 20px; }
        body:has(.dev-main) .personal-count {
            margin-left: 2px; padding: 3px 10px; border-radius: 50px;
            background: var(--pf-surface); color: var(--sb-on-surface-variant);
            font-size: 11.5px; font-weight: 500; letter-spacing: 0;
        }

        /* ── Tabla · sb-table ── */
        body:has(.dev-main) .personal-form-card {
            background: transparent; border: 0; border-radius: 0; overflow: visible;
        }
        body:has(.dev-main) .personal-table-wrap {
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border: 0; border-radius: 16px; overflow-x: auto;
        }
        body:has(.dev-main) .personal-table-wrap .personal-table thead tr { background: var(--sb-surface-container); }
        body:has(.dev-main) .personal-table-wrap .personal-table tbody tr { background: var(--sb-surface); }
        body:has(.dev-main) .personal-table-wrap .personal-table th {
            height: 36px; padding: 0 12px;
            font-size: 10px; font-weight: 500; letter-spacing: .04em; text-transform: uppercase;
            color: var(--sb-on-surface-variant); opacity: .7; vertical-align: middle;
            background: var(--sb-surface-container);
            border-bottom: 1px solid var(--sb-outline-variant);
        }
        body:has(.dev-main) .personal-table-wrap .personal-table td {
            height: 48px; padding: 0 12px; font-size: 13px;
            border-bottom: none; vertical-align: middle; color: var(--sb-on-surface);
        }
        body:has(.dev-main) .personal-table-wrap .personal-table tbody tr:hover { background: var(--sb-surface-container-high); }
        body:has(.dev-main) .personal-table-wrap .personal-table tbody tr:hover td { background: transparent; }
        body:has(.dev-main) .personal-user-name { font-size: 13.5px; font-weight: 500; }
        body:has(.dev-main) .personal-avatar { width: 32px; height: 32px; font-size: 12px; }
        body:has(.dev-main) .personal-mono { font-size: 12.5px; }
        body:has(.dev-main) .personal-dim { font-size: 12.5px; color: var(--sb-on-surface-variant); }
        body:has(.dev-main) .personal-role { font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 50px; }
        body:has(.dev-main) .personal-table .dev-code {
            background: none; border: 0; padding: 0; font-size: 12.5px; font-weight: 600;
            color: var(--sb-on-surface); border-radius: 0;
        }
        body:has(.dev-main) .personal-table .dev-code:hover { background: none; color: var(--sb-primary); }

        /* ── Acciones de fila: iconos sin recuadro ── */
        body:has(.dev-main) .personal-actions { gap: 2px; }
        body:has(.dev-main) .personal-actions .dev-btn-icon {
            width: 32px; height: 32px; border: 0; border-radius: 50px;
            background: transparent; color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        body:has(.dev-main) .personal-actions .dev-btn-icon .material-symbols-outlined { font-size: 18px; }
        body:has(.dev-main) .personal-actions .dev-btn-icon:hover { background: var(--sb-surface-container); filter: none; }

        /* ── Códigos de registro: bloque con hairlines ── */
        body:has(.dev-main) .personal-codes { gap: 1px; }
        body:has(.dev-main) .personal-code-item {
            background: var(--sb-surface); border-bottom: none; padding: 12px 16px;
        }
        body:has(.dev-main) .personal-code-avatar { width: 32px; height: 32px; font-size: 12px; }
        body:has(.dev-main) .personal-code-name { font-size: 13.5px; font-weight: 500; }
        body:has(.dev-main) .personal-code-meta { font-size: 12px; color: var(--sb-on-surface-variant); }
        /* Acciones del código: iconos sueltos también aquí */
        body:has(.dev-main) .personal-code-right .dev-btn-icon {
            width: 32px; height: 32px; border: 0; border-radius: 50px;
            background: transparent; color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        body:has(.dev-main) .personal-code-right .dev-btn-icon .material-symbols-outlined { font-size: 18px; }
        body:has(.dev-main) .personal-code-right .dev-btn-icon:hover { background: var(--sb-surface-container); filter: none; }

        /* ── Responsive · al final para ganar por orden ──
           ≤1099: las métricas pasan a 2x2. next-panel reparte su
           propio grid a ≤1024, pero aquí el bloque comparte la fila
           con los botones y conviene partir un poco antes. */
        @media (max-width: 1099px) {
            body:has(.dev-main) .personal-stats-row .dev-stats-grid {
                grid-auto-flow: row;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            body:has(.dev-main) .personal-stats-row { flex-wrap: wrap; }
            body:has(.dev-main) .personal-stats-row .dev-stats-grid { flex: 1 1 100%; }
            body:has(.dev-main) .personal-new-actions { flex: 1 1 100%; }
            body:has(.dev-main) .personal-new-actions .dev-btn { flex: 1 1 auto; justify-content: center; }
            body:has(.dev-main) .pf-bar { width: 100%; }
            body:has(.dev-main) .pf-search, body:has(.dev-main) .pf-input { width: 100%; min-width: 0; max-width: none; }
            body:has(.dev-main) .pf-select-wrap { flex: 1 1 auto; }
            body:has(.dev-main) .pf-select { width: 100%; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <!-- PWA -->
    <?= view('partials/pwa-head') ?>
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

        <!-- Métricas + acciones: las MISMAS tarjetas del dashboard
             (dev-stats-grid + dev-stat-card); cada una lleva a su
             sección de esta misma página, como en el dashboard -->
        <div class="personal-stats-row">
            <div class="dev-stats-grid">
                <a href="#equipo" class="dev-stat-card" title="Ver el equipo">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div class="dev-stat-label">Total equipo</div>
                    <div class="dev-stat-value"><?= count($empleados) ?></div>
                </a>
                <a href="#equipo" class="dev-stat-card" title="Ver el equipo">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">person</span></div>
                    <div class="dev-stat-label">Empleados</div>
                    <div class="dev-stat-value"><?= count(array_filter($empleados, fn($e) => ($e['role'] ?? '') === 'Empleado')) ?></div>
                </a>
                <a href="#equipo" class="dev-stat-card" title="Ver el equipo">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">school</span></div>
                    <div class="dev-stat-label">Practicantes</div>
                    <div class="dev-stat-value"><?= count(array_filter($empleados, fn($e) => ($e['role'] ?? '') === 'Practicante')) ?></div>
                </a>
                <a href="#codigos" class="dev-stat-card" title="Ver códigos de registro">
                    <div class="dev-stat-icon si-blue"><span class="material-symbols-outlined filled">qr_code</span></div>
                    <div class="dev-stat-label">Códigos activos</div>
                    <div class="dev-stat-value"><?= count(array_filter($codes, fn($c) => ($c['status'] ?? '') === 'active')) ?></div>
                </a>
            </div>
            <div class="personal-new-actions">
                <button type="button" class="dev-btn dev-btn-primary" onclick="openModal('createEmployeeModal')">
                    <span class="material-symbols-outlined">person_add</span> Crear empleado
                </button>
                <button type="button" class="dev-btn" onclick="openModal('createCodeModal')">
                    <span class="material-symbols-outlined">vpn_key</span> Generar código
                </button>
            </div>
        </div>

        <div class="personal-section" id="equipo">
            <?php
                $areasDisponibles = [];
                foreach ($empleados as $p) {
                    $a = trim((string) ($p['area'] ?? ''));
                    if ($a !== '') { $areasDisponibles[$a] = true; }
                }
                $areasDisponibles = array_keys($areasDisponibles);
                sort($areasDisponibles, SORT_NATURAL | SORT_FLAG_CASE);
                $totalPersonas = count($empleados);
            ?>
            <div class="personal-section-head">
                <h2 class="personal-section-title">
                    <span class="material-symbols-outlined">group</span>
                    Equipo
                    <span class="personal-count" id="personCount"><?= $totalPersonas ?> personas</span>
                </h2>

                <div class="pf-bar">
                    <div class="pf-search">
                        <span class="material-symbols-outlined">search</span>
                        <input type="search" class="pf-input" id="personSearch" placeholder="Buscar por nombre, DNI, código, área..." oninput="filtrarPersonas()">
                    </div>
                    <span class="pf-select-wrap">
                        <select class="pf-select" id="personRol" onchange="filtrarPersonas()" aria-label="Filtrar por rol">
                            <option value="">Rol: todos</option>
                            <option value="Empleado">Empleado</option>
                            <option value="Practicante">Practicante</option>
                            <option value="Admin">Admin</option>
                        </select>
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                    <span class="pf-select-wrap">
                        <select class="pf-select" id="personEstado" onchange="filtrarPersonas()" aria-label="Filtrar por estado">
                            <option value="">Estado: todos</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="Despedido">Despedido</option>
                            <option value="Retirado">Retirado</option>
                        </select>
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                    <?php if ($areasDisponibles): ?>
                    <span class="pf-select-wrap">
                        <select class="pf-select" id="personArea" onchange="filtrarPersonas()" aria-label="Filtrar por área">
                            <option value="">Área: todas</option>
                            <?php foreach ($areasDisponibles as $a): ?>
                                <option value="<?= esc($a) ?>"><?= esc($a) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                    <?php endif; ?>
                    <button type="button" class="pf-clear" onclick="limpiarPersonas()" title="Quitar filtros">
                        <span class="material-symbols-outlined">filter_alt_off</span> Limpiar
                    </button>
                </div>
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
                                    <?php
                                        $busqueda = mb_strtolower(preg_replace('/\s+/', ' ', trim(
                                            $name . ' ' . ($emp['dni'] ?? '') . ' ' . ($emp['personal_code'] ?? '') . ' ' . ($emp['email'] ?? '')
                                            . ' ' . ($emp['area'] ?? '') . ' ' . ($emp['cargo'] ?? '') . ' ' . $role . ' ' . ($emp['estado'] ?? 'Activo')
                                            . ' ' . (($emp['role'] ?? '') === 'Practicante' ? (($emp['semestre'] ?? '') . ' ' . ($emp['institucion'] ?? '')) : '')
                                        )));
                                    ?>
                                    <tr data-buscar="<?= esc($busqueda) ?>" data-rol="<?= esc($role) ?>" data-estado="<?= esc($emp['estado'] ?? 'Activo') ?>" data-area="<?= esc($emp['area'] ?? '') ?>" onclick="openDetailModal(<?= htmlspecialchars(json_encode($emp), ENT_QUOTES, 'UTF-8') ?>)">
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
                            <tr id="personNoResults" style="display:none;">
                                <td colspan="12">
                                    <div class="pf-empty" style="display:block;">
                                        <span class="material-symbols-outlined" style="font-size:44px;display:block;margin:0 auto 8px;opacity:.25;color:var(--g-text-disabled);">person_search</span>
                                        Ninguna persona coincide con el filtro.
                                    </div>
                                </td>
                            </tr>
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
                            <?php
                                $busquedaM = mb_strtolower(preg_replace('/\s+/', ' ', trim(
                                    $name . ' ' . ($emp['dni'] ?? '') . ' ' . ($emp['personal_code'] ?? '') . ' ' . ($emp['email'] ?? '')
                                    . ' ' . ($emp['area'] ?? '') . ' ' . ($emp['cargo'] ?? '') . ' ' . $role . ' ' . ($emp['estado'] ?? 'Activo')
                                    . ' ' . (($emp['role'] ?? '') === 'Practicante' ? (($emp['semestre'] ?? '') . ' ' . ($emp['institucion'] ?? '')) : '')
                                )));
                            ?>
                            <div class="m-card" data-buscar="<?= esc($busquedaM) ?>" data-rol="<?= esc($role) ?>" data-estado="<?= esc($emp['estado'] ?? 'Activo') ?>" data-area="<?= esc($emp['area'] ?? '') ?>" style="cursor:pointer;" onclick="event.target.closest('.m-card-actions') || openDetailModal(<?= htmlspecialchars(json_encode($emp), ENT_QUOTES, 'UTF-8') ?>)">
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
                    <div class="pf-empty" id="personNoResultsMobile">Ninguna persona coincide con el filtro.</div>
                </div>
            </div>
        </div>

        <div class="personal-section" id="codigos">
            <div class="personal-section-head">
                <h2 class="personal-section-title">
                    <span class="material-symbols-outlined">vpn_key</span>
                    Códigos de registro
                    <span class="personal-count"><?= count($codes) ?> total</span>
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

<!-- Modal: Generar código -->
<div class="dev-modal-overlay" id="createCodeModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">vpn_key</span> Generar código</h3>
            <button type="button" class="dev-btn-icon" onclick="closeModal('createCodeModal')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form action="<?= site_url('admin/create-code') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <div class="dev-field">
                    <label>Empleado</label>
                    <select name="name" id="codeEmpSelect" required>
                        <option value="">Selecciona un empleado</option>
                        <?php foreach ($empleados as $emp): ?>
                            <option value="<?= esc($emp['name'] ?? '') ?>" data-dni="<?= esc($emp['dni'] ?? '') ?>"><?= esc($emp['name'] ?? '') ?> — DNI <?= esc($emp['dni'] ?? 'sin registro') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dev-field"><label>DNI del empleado (se completa al elegir)</label><input type="text" id="codeEmpDni" name="dni" placeholder="8 digitos" pattern="\d{8}" maxlength="8" required></div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeModal('createCodeModal')">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">add</span> Generar código</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Credenciales generadas (al crear) -->
<?php $creds = session()->getFlashdata('emp_creds'); ?>
<?php if ($creds): ?>
<div class="dev-modal-overlay active one-shot" id="credsModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
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
        <div class="dev-modal-head">
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
                <button type="button" class="dev-btn" style="flex:1;" onclick="closeModal('confirmDeleteModal')"><span class="material-symbols-outlined">close</span> Cancelar</button>
                <button type="button" class="dev-btn dev-btn-danger" style="flex:1;" id="confirmBtn" onclick="submitConfirmDelete()">                <span class="material-symbols-outlined">delete</span> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="dev-modal-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetailModal()">
    <div class="dev-modal" style="max-width:500px;">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">badge</span> <span id="detailName">-</span></h3>
            <button type="button" class="dev-btn-icon" onclick="closeDetailModal()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body">
            <div class="detail-who" id="detailCover">
                <div class="detail-avatar" id="detailAvatar"></div>
                <div class="detail-sub" id="detailRole">-</div>
            </div>
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
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn" onclick="closeDetailModal()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="dev-btn dev-btn-danger" id="detailFireBtn">
                <span class="material-symbols-outlined">person_remove</span> Despedir
            </button>
            <button type="button" class="dev-btn dev-btn-primary" id="detailEditBtn">
                <span class="material-symbols-outlined">edit</span> Editar
            </button>
        </div>
    </div>
</div>

<div class="dev-modal-overlay" id="codeDetailOverlay" onclick="if(event.target===this)closeDetailModal()">
    <div class="dev-modal" style="max-width:440px;">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">vpn_key</span> <span id="codeDetailName">-</span></h3>
            <button type="button" class="dev-btn-icon" onclick="closeDetailModal()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body">
            <div class="detail-who" id="codeDetailCover">
                <div class="detail-avatar" id="codeDetailAvatar"></div>
                <div class="detail-sub" id="codeDetailSub">-</div>
            </div>
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
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn" onclick="closeDetailModal()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="dev-btn dev-btn-primary detail-btn-edit" onclick="copyCodeDetail()">
                <span class="material-symbols-outlined">content_copy</span> Copiar codigo
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        var total = <?= (int) $totalPersonas ?>;
        window.filtrarPersonas = function () {
            var q   = (document.getElementById('personSearch').value || '').toLowerCase().trim();
            var rol = document.getElementById('personRol').value;
            var est = document.getElementById('personEstado').value;
            var are = (document.getElementById('personArea') || {}).value || '';
            var visibles = 0;

            document.querySelectorAll('[data-buscar]').forEach(function (el) {
                var ok = (!q || (el.getAttribute('data-buscar') || '').indexOf(q) !== -1)
                      && (!rol || el.getAttribute('data-rol') === rol)
                      && (!est || el.getAttribute('data-estado') === est)
                      && (!are || el.getAttribute('data-area') === are);
                el.style.display = ok ? '' : 'none';
                if (ok && el.tagName === 'TR') visibles++;
            });

            var cnt = document.getElementById('personCount');
            if (cnt) cnt.textContent = (q || rol || est || are)
                ? visibles + ' de ' + total + ' personas'
                : total + ' personas';

            var nd = document.getElementById('personNoResults');
            if (nd) nd.style.display = (q || rol || est || are) && visibles === 0 ? '' : 'none';
            var nm = document.getElementById('personNoResultsMobile');
            if (nm) nm.style.display = (q || rol || est || are) && visibles === 0 ? 'block' : 'none';
        };

        window.limpiarPersonas = function () {
            document.getElementById('personSearch').value = '';
            document.getElementById('personRol').value = '';
            document.getElementById('personEstado').value = '';
            var ar = document.getElementById('personArea');
            if (ar) ar.value = '';
            filtrarPersonas();
            marcarFiltros();
        };

        /* Un filtro con valor se pinta como la píldora activa de sorteroOS */
        window.marcarFiltros = function () {
            ['personRol', 'personEstado', 'personArea'].forEach(function (id) {
                var s = document.getElementById(id);
                if (!s) return;
                var wrap = s.closest('.pf-select-wrap');
                if (wrap) wrap.classList.toggle('on', s.value !== '');
            });
        };
        ['personRol', 'personEstado', 'personArea'].forEach(function (id) {
            var s = document.getElementById(id);
            if (s) s.addEventListener('change', marcarFiltros);
        });
        marcarFiltros();
    })();
</script>

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

// Generar código: al elegir el empleado del selector, el DNI se completa solo
(function () {
    var sel = document.getElementById('codeEmpSelect');
    var dni = document.getElementById('codeEmpDni');
    if (!sel || !dni) return;
    function fillDni() {
        var opt = sel.options[sel.selectedIndex];
        var val = opt ? (opt.getAttribute('data-dni') || '') : '';
        dni.value = val;
        dni.readOnly = val.length === 8;
        dni.required = !dni.readOnly;
        if (!dni.readOnly && !dni.value) dni.focus();
    }
    sel.addEventListener('change', fillDni);
    fillDni();
})();

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

    document.getElementById('detailAvatar').style.background = avatarBg[role] || avatarBg.default;
    document.getElementById('detailAvatar').style.color = '#fff';
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

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
</html>
