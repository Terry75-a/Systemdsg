<!--[ horarios · estilos v3 · EduconectaV2: píldora .lg-submit · modales r20 · si esta línea NO sale en tu "ver código fuente", tu navegador está sirviendo copia vieja ]-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        /* Sin recuadro azul al hacer clic */
        .dev-layout button, .dev-layout a, .dev-layout input, .dev-layout select, .dev-layout textarea { outline: none; -webkit-tap-highlight-color: transparent; }
        .dev-layout button:focus-visible, .dev-layout a:focus-visible { outline: 2px solid var(--g-primary); outline-offset: 2px; }

        /* Control segmentado del tipo */
        .hr-seg { display: inline-flex; gap: 4px; }
        .hr-seg-btn {
            position: relative;
            height: 38px; padding: 0 14px; border: 1px solid transparent; border-radius: 12px;
            background: var(--hr-surface); color: var(--sb-on-surface);
            cursor: pointer; font-size: 0.85rem; font-weight: 500; font-family: inherit;
            display: inline-flex; align-items: center; gap: 7px; white-space: nowrap;
            transition: background 125ms ease, color 125ms ease;
        }
        .hr-seg-btn input { position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0; }
        .hr-seg-btn .material-symbols-outlined {
            font-size: 16px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
            transition: color 125ms ease;
        }
        .hr-seg-btn:hover { background: var(--sb-surface-container-high); }
        .hr-seg-btn:hover .material-symbols-outlined { color: var(--sb-primary); }
        .hr-seg-btn.on { background: var(--sb-primary); color: #fff; }
        .hr-seg-btn.on .material-symbols-outlined { color: #fff; }

        /* ── Badges de tipo ── */
        .hr-tag { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 50px; }
        .hr-tag .material-symbols-outlined { font-size: 14px; }
        .hr-tag.hr-tag-pract { background: var(--g-primary-light); color: var(--g-primary); }
        .hr-tag.hr-tag-emp { background: var(--g-success-light); color: var(--g-success); }

        /* ── Chips de horario ── */
        .hr-chips { display: inline-flex; align-items: center; gap: 6px; }
        .hr-chip { background: var(--g-surface-variant); border: 1px solid var(--g-border); border-radius: 9px; padding: 4px 10px; font-family: 'SF Mono', Consolas, monospace; font-size: 12.5px; font-weight: 600; color: var(--g-text); }
        .hr-arrow { color: var(--g-text-disabled); font-size: 12px; }

        /* ── Botón integrantes ── */
        .hr-int-btn { display: inline-flex; align-items: center; gap: 6px; background: var(--g-primary-light); color: var(--g-primary); border: 1px solid var(--g-primary); padding: 6px 13px; border-radius: 50px; font-family: var(--g-font); font-size: 12.5px; font-weight: 600; cursor: pointer; transition: none; }
        .hr-int-btn:hover { background: var(--g-primary); color: #fff; }
        .hr-int-btn .material-symbols-outlined { font-size: 16px; }

        /* ── Tabla ── */
        .dev-table th { white-space: nowrap; }
        .sch-name { display: flex; flex-direction: column; gap: 3px; }
        .dev-table tbody tr { cursor: pointer; transition: background 200ms; }
        .dev-table tbody tr:hover td { background: var(--g-surface-variant); }

        /* ── Modal integrantes ── */
        .int-list { max-height: 46vh; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding-right: 4px; }
        .int-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1px solid var(--g-border); border-radius: 14px; background: var(--g-surface); transition: border-color 180ms, background 180ms; }
        .int-item:has(input:checked) { border-color: var(--g-primary); background: var(--g-primary-light); }
        .int-item input[type=checkbox] { width: 18px; height: 18px; accent-color: var(--g-primary); flex-shrink: 0; }
        .int-av { width: 38px; height: 38px; border-radius: 50%; background: var(--g-primary-light); color: var(--g-primary); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; font-family: var(--g-font-display); flex-shrink: 0; }
        .int-item.emp .int-av { background: var(--g-success-light); color: var(--g-success); }
        .int-main { flex: 1; min-width: 0; }
        .int-main b { display: block; font-size: 13.5px; }
        .int-main span { font-size: 11.5px; color: var(--g-text-secondary); }
        .int-count { font-size: 12px; color: var(--g-text-secondary); padding: 2px 10px; border-radius: 50px; background: var(--g-surface-variant); }
        .int-none { text-align: center; color: var(--g-text-secondary); padding: 26px 10px; font-size: 13px; }
        .int-none .material-symbols-outlined { font-size: 38px; opacity: 0.3; display: block; margin: 0 auto 8px; }

        /* ── Detalle — canon EduconectaV2: dev-modal (r20 · hairline ·
           overlay neutro) con footer de dev-btn; dentro del body sólo
           la fila de identidad (chip del horario + días) ── */
        .detail-who { display: flex; align-items: center; gap: 14px; padding: 4px 0 18px; }
        .detail-avatar {
            width: 48px; height: 48px; border-radius: 14px; color: #fff;
            display: flex; align-items: center; justify-content: center; flex: none;
        }
        .detail-sub { font-size: 13px; color: var(--g-text-secondary); }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: var(--g-text-secondary); text-transform: uppercase; letter-spacing: .04em; }
        .detail-value {
            font-size: 0.9rem; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--sb-surface-container); border-radius: 12px;
        }
        .detail-value code { font-family: 'SF Mono', Consolas, monospace; font-size: 13px; }


        /* ═══════════════════════════════════════════════════════════════
           HORARIOS · panel admin de sorteroOS

           Copiado del diseño REAL en ejecución (sorteoOS:3000/admin/usuarios
           y /admin/clubes), no sólo de su CSS:

             · fila de tarjetas de métrica          (admin/clubes)
             · acción primaria redondeada           (admin/usuarios)
             · buscador en píldora + filtros pill    (ambos)
             · tabla dentro de un bloque redondeado  (admin/usuarios)
             · el alta en un modal, no en un card    (admin/usuarios)

           En esta página ya no queda ningún <div class="dev-card">: las
           métricas, el buscador y la tabla son bloques con superficie
           propia, y el formulario de alta vive en un modal.

           Los contadores salen de $schData, o sea de los datos reales del
           sistema: horarios por tipo e integrantes ya asignados.
           ═══════════════════════════════════════════════════════════════ */

        /* En sorteroOS las tarjetas de métrica y el buscador usan un tono
           MÁS oscuro que su página blanca (lavanda). Aquí la página ya es
           gris (#f4f4f5), así que el mismo efecto se consigue mezclando
           el outline-variant con la surface: #ececee en claro, #1d1d1f en
           oscuro. Con --sb-surface-container (#f6f6f7) quedaba invisible
           porque era más claro que el fondo de la app. Se define en body y
           no en .dev-main porque los modales viven fuera del <main>. */
        body { --hr-surface: color-mix(in srgb, var(--sb-outline-variant) 55%, var(--sb-surface)); }

        /* ── Tarjetas de métrica (admin/clubes) ── */
        .hr-metrics-row { display: flex; align-items: stretch; gap: 14px; margin-bottom: 16px; }
        .hr-metrics { flex: 1 1 auto; min-width: 0; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .hr-metric {
            background: var(--hr-surface);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex; flex-direction: column; gap: 12px;
        }
        .hr-metric-top { display: flex; align-items: center; gap: 8px; }
        .hr-metric-top .material-symbols-outlined { font-size: 16px; color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent); }
        .hr-metric-label { font-size: 12.5px; font-weight: 500; color: var(--sb-on-surface-variant); }
        .hr-metric b {
            font-size: 26px; font-weight: 700; letter-spacing: -.025em; line-height: 1;
            color: var(--sb-on-surface); font-variant-numeric: tabular-nums;
        }

        /* ── CTA Nuevo horario — píldora réplica del botón "Añadir" de
           personas (.pdsg-add-btn): alto 40px, padding 0 20px, radio 999px,
           tipografía 13px/500 y verde sólido. Sin animaciones ── */
        .hr-new {
            flex: none; align-self: center;
            height: 40px; padding: 0 20px; border: 0; border-radius: 999px; cursor: pointer;
            background: var(--sb-primary); color: #fff;
            font-family: inherit; font-size: 13px; font-weight: 500; letter-spacing: .02em; line-height: 1;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap;
            transition: none;
        }
        .hr-new:hover { background: color-mix(in srgb, var(--sb-primary) 90%, #000); }
        .hr-new:focus-visible { outline: none; box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        .hr-new .material-symbols-outlined { font-size: 17px; }

        /* ── Buscador + filtros (admin/usuarios) ── */
        .hr-toolbar { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .hr-search { flex: 1 1 auto; min-width: 0; position: relative; display: flex; align-items: center; }
        .hr-search > .material-symbols-outlined {
            position: absolute; left: 16px; font-size: 18px; pointer-events: none;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        .hr-search input {
            width: 100%; height: 40px; padding: 0 18px 0 44px;
            border: 0; border-radius: 50px; outline: none;
            background: var(--hr-surface); color: var(--sb-on-surface);
            font-family: inherit; font-size: 13.5px;
            transition: none;
        }
        .hr-search input::placeholder { color: color-mix(in srgb, var(--sb-on-surface-variant) 45%, transparent); }
        .hr-search input:focus { box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        .hr-filters { display: inline-flex; gap: 8px; flex: none; flex-wrap: wrap; }
        /* Filtros de la barra: píldoras con radio 50px, planas,
           sin animaciones */
        .hr-pill {
            height: 40px; padding: 0 18px; border: 1px solid var(--sb-outline-variant);
            border-radius: 50px; background: var(--sb-surface); color: var(--sb-on-surface);
            cursor: pointer; font-family: inherit; font-size: 13.5px; font-weight: 600;
            display: inline-flex; align-items: center; gap: 7px; white-space: nowrap;
            transition: none;
        }
        .hr-pill:hover { border-color: color-mix(in srgb, var(--sb-on-surface-variant) 45%, transparent); }
        .hr-pill.on { background: var(--sb-primary); border-color: var(--sb-primary); color: #fff; }
        .hr-pill b { font-size: 12px; font-weight: 700; opacity: .65; font-variant-numeric: tabular-nums; }
        .hr-pill.on b { opacity: .9; }
        .hr-pill:focus-visible { outline: none; box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }

        /* ── Bloque de la tabla ── */
        .hr-block {
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px;
            overflow: hidden;
        }
        body:has(.dev-main) .dev-table-wrap.hr-block .dev-table thead tr { background: var(--sb-surface-container); }
        body:has(.dev-main) .dev-table-wrap.hr-block .dev-table tbody tr { background: var(--sb-surface); }
        body:has(.dev-main) .dev-table-wrap.hr-block .dev-table th {
            height: 36px; padding: 0 12px;
            font-size: 10px; font-weight: 500; letter-spacing: .04em; text-transform: uppercase;
            color: var(--sb-on-surface-variant); opacity: .7; vertical-align: middle;
        }
        body:has(.dev-main) .dev-table-wrap.hr-block .dev-table td {
            height: 48px; padding: 0 12px;
            font-size: 13px; line-height: 16px; vertical-align: middle; color: var(--sb-on-surface);
        }
        .hr-vacio {
            text-align: center; padding: 34px 12px !important;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 70%, transparent);
        }
        .hr-vacio .material-symbols-outlined { font-size: 24px; vertical-align: middle; margin-right: 8px; opacity: .55; }

        /* ── Contenido de la tabla ── */
        /* Tipo: pastilla, como el badge de estado de sorteroOS */
        body:has(.dev-main) .hr-tag {
            border-radius: 50px; padding: 3px 10px;
            font-size: 11px; font-weight: 500;
        }
        body:has(.dev-main) .hr-tag.hr-tag-pract { background: color-mix(in srgb, var(--g-primary) 12%, transparent); color: var(--g-primary); }
        body:has(.dev-main) .hr-tag.hr-tag-emp { background: color-mix(in srgb, var(--g-success) 12%, transparent); color: var(--g-success); }
        /* Horas: monoespaciado plano, sin caja con borde */
        body:has(.dev-main) .hr-chip {
            background: none; border: 0; padding: 0; border-radius: 0;
            font-size: 12.5px; font-weight: 600; color: var(--sb-on-surface);
        }
        body:has(.dev-main) .hr-arrow { color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent); }
        /* Integrantes: sin pastilla con borde */
        body:has(.dev-main) .hr-int-btn {
            background: none; border: 0; padding: 4px 8px; border-radius: 50px;
            color: var(--sb-on-surface-variant);
            font-size: 12.5px; font-weight: 500;
            transition: none;
        }
        body:has(.dev-main) .hr-int-btn:hover { background: var(--sb-surface-container); color: var(--sb-primary); }
        body:has(.dev-main) .hr-int-btn .material-symbols-outlined { font-size: 16px; }
        /* Nombre del horario */
        body:has(.dev-main) .sch-name strong { font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); }
        /* Acciones: iconos tenues sin recuadro (educonecta.css:177 les pone
           borde de 1px y fondo; aquí vuelven a ser sólo icono). Se cubren
           también las tarjetas de móvil (.dev-table-card). */
        body:has(.dev-main) .dev-table .dev-btn-icon,
        body:has(.dev-main) .dev-table-card .dev-btn-icon {
            width: 32px; height: 32px; border: 0; border-radius: 50px;
            background: transparent; color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        body:has(.dev-main) .dev-table .dev-btn-icon .material-symbols-outlined,
        body:has(.dev-main) .dev-table-card .dev-btn-icon .material-symbols-outlined { font-size: 18px; }
        body:has(.dev-main) .dev-table .dev-btn-icon:hover,
        body:has(.dev-main) .dev-table-card .dev-btn-icon:hover { background: var(--sb-surface-container); color: var(--sb-primary); filter: none; }
        body:has(.dev-main) .dev-table .dev-btn-icon.dev-btn-danger,
        body:has(.dev-main) .dev-table-card .dev-btn-icon.dev-btn-danger { color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent); }
        body:has(.dev-main) .dev-table .dev-btn-icon.dev-btn-danger:hover,
        body:has(.dev-main) .dev-table-card .dev-btn-icon.dev-btn-danger:hover { background: var(--sb-surface-container); color: #dc2626; }

        /* ── Modales · sorteroOS ──────────────────────────────────────
           Cabecera con icono en cuadro tintado, título + bajada, campos
           sobre superficie gris SIN borde, y pie con botones en píldora. */
        body:has(.dev-main) .dev-modal {
            border-radius: 20px; max-width: 720px;
            border: 1px solid var(--sb-outline-variant);
            box-shadow: none;
        }
        body:has(.dev-main) .hr-modal-head { display: flex; align-items: flex-start; gap: 14px; padding: 22px 24px 4px; }
        .hr-modal-icon {
            width: 36px; height: 36px; border-radius: 10px; flex: none;
            background: color-mix(in srgb, var(--sb-primary) 12%, transparent);
            color: var(--sb-primary);
            display: flex; align-items: center; justify-content: center;
        }
        .hr-modal-icon .material-symbols-outlined { font-size: 19px; }
        .hr-modal-titles { flex: 1 1 auto; min-width: 0; }
        .hr-modal-titles h2 { margin: 0; font-size: 18px; font-weight: 600; letter-spacing: -.015em; color: var(--sb-on-surface); }
        .hr-modal-titles p { margin: 3px 0 0; font-size: 13px; color: color-mix(in srgb, var(--sb-on-surface-variant) 70%, transparent); }
        body:has(.dev-main) .hr-modal .dev-modal-body { padding: 18px 24px 4px; }
        body:has(.dev-main) .hr-modal .dev-form-grid,
        body:has(.dev-main) .hr-modal .hr-modal-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
        body:has(.dev-main) .hr-modal .dev-field--full { grid-column: 1 / -1; }
        body:has(.dev-main) .hr-modal .dev-field label { font-size: 13px; font-weight: 500; color: var(--sb-on-surface); }
        body:has(.dev-main) .hr-modal .dev-field input,
        body:has(.dev-main) .hr-modal .dev-field select {
            height: 44px; border: 0; border-radius: 12px;
            background: var(--hr-surface); color: var(--sb-on-surface);
            font-size: 13.5px;
        }
        body:has(.dev-main) .hr-modal .dev-field input:focus,
        body:has(.dev-main) .hr-modal .dev-field select:focus {
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }
        /* En el pie del modal, el submit del alta usa la misma píldora de
           40px que el CTA para emparejar con "Cancelar" */
        body:has(.dev-main) .hr-modal .dev-modal-footer .hr-new {
            height: 40px; padding: 0 20px;
            font-size: 13px; font-weight: 500;
        }
        .hr-req { color: #dc2626; }
        body:has(.dev-main) .hr-modal .dev-modal-footer {
            gap: 12px; padding: 14px 24px 18px;
            /* el pie se queda pegado: aunque el modal desborde, "Crear
               horario" siempre está a la vista */
            position: sticky; bottom: 0; z-index: 2;
            background: var(--sb-surface);
            border-top: 1px solid color-mix(in srgb, var(--sb-outline-variant) 45%, transparent);
        }
        /* La X de cerrar, sin el recuadro que le pone educonecta.css:177 */
        body:has(.dev-main) .hr-modal .dev-modal-head .dev-btn-icon {
            width: 34px; height: 34px; border: 0; border-radius: 50px;
            background: transparent; color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
        }
        body:has(.dev-main) .hr-modal .dev-modal-head .dev-btn-icon:hover {
            background: var(--hr-surface); color: var(--sb-on-surface); filter: none;
        }
        .hr-btn-ghost {
            height: 40px; padding: 0 20px; border-radius: 50px; cursor: pointer;
            border: 1px solid var(--sb-outline-variant); background: var(--sb-surface);
            color: var(--sb-on-surface); font-family: inherit; font-size: 13.5px; font-weight: 500;
            transition: none;
        }
        .hr-btn-ghost:hover { background: var(--sb-surface-container); }
        /* ── Modal de alta · detalle ──────────────────────────────────
           Etiquetas de sección al estilo de la "CONTRASEÑA" de sorteroOS,
           opciones de tipo en mitades con icono, presets de días, flecha
           entre las horas y vista previa en vivo de como quedará la fila. */
        .hr-sec-label {
            display: block;
            font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent);
            margin: 18px 0 9px;
        }
        .hr-modal-body > .hr-sec-label:first-child { margin-top: 0; }

        /* Opciones de tipo: dos mitades con el icono en cuadro tintado */
        .hr-choice { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .hr-choice-opt {
            position: relative;
            display: flex; align-items: center; gap: 10px;
            padding: 12px 14px;
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 60%, transparent);
            border-radius: 12px; background: var(--sb-surface); cursor: pointer;
            transition: border-color 150ms ease, background 150ms ease;
        }
        .hr-choice-opt input { position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0; }
        .hr-choice-ic {
            width: 30px; height: 30px; border-radius: 8px; flex: none;
            display: flex; align-items: center; justify-content: center;
            background: var(--hr-surface);
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
            transition: background 150ms ease, color 150ms ease;
        }
        .hr-choice-ic .material-symbols-outlined { font-size: 17px; }
        .hr-choice-tx { font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); line-height: 1.25; }
        .hr-choice-tx small {
            display: block; font-size: 11px; font-weight: 400;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
        }
        .hr-choice-opt:hover { border-color: var(--sb-outline-variant); }
        .hr-choice-opt.on {
            border-color: color-mix(in srgb, var(--sb-primary) 45%, transparent);
            background: color-mix(in srgb, var(--sb-primary) 6%, var(--sb-surface));
        }
        .hr-choice-opt.on .hr-choice-ic { background: var(--sb-primary); color: #fff; }

        /* Rejilla del modal y fila de horas con flecha */
        .hr-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .hr-modal-grid .dev-field--full { grid-column: 1 / -1; }
        .hr-time-row { display: flex; align-items: flex-end; gap: 10px; }
        .hr-time-row .dev-field { flex: 1 1 0; min-width: 0; }
        .hr-time-row .hr-field-tol { flex: 0 0 150px; }
        .hr-time-sep {
            flex: none; padding-bottom: 13px; font-size: 14px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 45%, transparent);
        }

        /* Campo con su unidad pegada a la derecha */
        .hr-with-unit { position: relative; display: flex; }
        .hr-with-unit input { width: 100%; padding-right: 38px; }
        .hr-unit {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 11.5px; pointer-events: none;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 55%, transparent);
        }

        /* Presets de días laborales */
        .hr-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .hr-preset {
            height: 28px; padding: 0 12px; border-radius: 50px; cursor: pointer;
            border: 1px solid var(--sb-outline-variant); background: transparent;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 80%, transparent);
            font-family: inherit; font-size: 11.5px; font-weight: 500;
            transition: none;
        }
        .hr-preset:hover { background: var(--hr-surface); color: var(--sb-on-surface); }
        .hr-preset.on { background: var(--sb-primary); border-color: var(--sb-primary); color: #fff; }

        /* Vista previa: la fila tal como quedará en la tabla */
        .hr-preview {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 14px; border-radius: 12px;
            background: var(--hr-surface);
        }
        .hr-preview-ic {
            width: 34px; height: 34px; border-radius: 9px; flex: none;
            display: flex; align-items: center; justify-content: center;
            background: var(--sb-primary); color: #fff;
            transition: background 150ms ease;
        }
        .hr-preview-ic .material-symbols-outlined { font-size: 18px; }
        .hr-preview-body { min-width: 0; }
        .hr-preview-body b { display: block; font-size: 13.5px; font-weight: 500; color: var(--sb-on-surface); }
        .hr-preview-meta {
            display: flex; align-items: center; flex-wrap: wrap; gap: 7px; margin-top: 3px;
            font-size: 11.5px; color: color-mix(in srgb, var(--sb-on-surface-variant) 65%, transparent);
        }
        .hr-dot { width: 3px; height: 3px; border-radius: 50%; background: currentColor; opacity: .5; }

        /* ── Responsive · va al FINAL porque la regla base de .hr-metrics
           está en este mismo bloque y, sin !important, el orden manda ── */
        @media (max-width: 900px) {
            .hr-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .hr-metrics-row { flex-wrap: wrap; }
            .hr-metrics { flex: 1 1 100%; }
            .hr-new { flex: 1 1 100%; justify-content: center; }
            .hr-toolbar { flex-wrap: wrap; }
            .hr-search { flex: 1 1 100%; }
            .hr-filters { flex: 1 1 100%; }
            .hr-pill { flex: 1 1 auto; justify-content: center; }
        }
        @media (max-width: 560px) {
            /* En el modal las opciones y los campos se apilan */
            body:has(.dev-main) .dev-modal { max-width: 100%; max-height: 92vh; }
            .hr-choice, .hr-modal-grid { grid-template-columns: 1fr; }
            .hr-time-row { flex-wrap: wrap; }
            .hr-time-row .dev-field { flex: 1 1 130px; }
            .hr-time-row .hr-field-tol { flex: 1 1 100%; }
            .hr-time-sep { display: none; }
            .hr-modal-body > .hr-sec-label:first-child { margin-top: 0; }
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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tk-dashboard.css?v=' . filemtime(FCPATH . 'css/index/components/tk-dashboard.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>

<?php
    $personalAll = $personal ?? [];
    $schData = [];
    $tipoLabels = ['Practicante' => 'Practicantes', 'Empleado' => 'Empleados'];
    foreach (($schedules ?? []) as $sch) {
        $sid = (int) ($sch['id'] ?? 0);
        $tipo = $sch['tipo'] ?? 'Empleado';
        $cands = array_values(array_filter($personalAll, fn($u) => ($u['role'] ?? '') === $tipo));
        $assignedIds = array_map(fn($u) => (int) ($u['id'] ?? 0), $scheduleUsers[$sid] ?? []);
        $schData[] = [
            'id'        => $sid,
            'nombre'    => $sch['nombre'] ?? '',
            'tipo'      => $tipo,
            'dias'      => $sch['dias'] ?? 'Lun - Vie',
            'entrada'   => substr($sch['hora_entrada'] ?? '08:00', 0, 5),
            'salida'    => substr($sch['hora_salida'] ?? '17:00', 0, 5),
            'tolerancia'=> (int) ($sch['tolerancia'] ?? 10),
            'estado'    => $sch['estado'] ?? 'Activo',
            'total'     => count($assignedIds),
            'cands'     => array_map(fn($u) => [
                'id' => (int) $u['id'], 'name' => $u['name'] ?? '',
                'cargo' => $u['cargo'] ?? '', 'code' => $u['personal_code'] ?? '',
            ], $cands),
            'assigned'  => array_values(array_unique($assignedIds)),
        ];
    }
    $intJson = json_encode($schData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<?= view('partials/admin-sidebar', ['activePage' => 'horarios']) ?>

<main class="dev-main">
    <div class="dev-topbar">
        <div class="tk-title">
            <div>
                <h1>Horarios</h1>
                <p>Gestiona los horarios por tipo y asigna a tu personal.</p>
            </div>
            <div class="tk-title-actions">
                <button type="button" class="dev-btn dev-btn-primary dev-btn-sm" onclick="openCreateModal()">
                    <span class="material-symbols-outlined">add</span> Nuevo horario
                </button>
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

        <?php
            // Métricas reales: cuántos horarios hay y de qué tipo, y cuántos
            // integrantes tienen asignados. Es la adaptación del bloque de
            // métricas de sorteroOS (admin/clubes) a los datos del sistema.
            $totalHorarios = count($schData);
            $horariosEmp   = 0;
            $horariosPract = 0;
            $totalIntegra  = 0;
            foreach ($schData as $row) {
                if (($row['tipo'] ?? '') === 'Practicante') { $horariosPract++; } else { $horariosEmp++; }
                $totalIntegra += (int) ($row['total'] ?? 0);
            }
        ?>


        <section class="tk-stats tk-stats-4">
            <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">schedule</span><div><p class="tk-stat-lbl">Horarios</p><p class="tk-stat-val"><?= $totalHorarios ?></p></div></div>
            <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">work</span><div><p class="tk-stat-lbl">De empleados</p><p class="tk-stat-val"><?= $horariosEmp ?></p></div></div>
            <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">school</span><div><p class="tk-stat-lbl">De practicantes</p><p class="tk-stat-val"><?= $horariosPract ?></p></div></div>
            <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">group</span><div><p class="tk-stat-lbl">Integrantes</p><p class="tk-stat-val"><?= $totalIntegra ?></p></div></div>
        </section>

        <!-- Buscador + filtros por tipo (admin/usuarios) -->
        <div class="hr-toolbar">
            <div class="hr-search">
                <span class="material-symbols-outlined">search</span>
                <input type="search" id="hrSearch" placeholder="Buscar por nombre, tipo o días..." autocomplete="off">
            </div>
            <div class="hr-filters" id="hrFiltros">
                <button type="button" class="hr-pill on" data-tipo="">Todos <b><?= $totalHorarios ?></b></button>
                <button type="button" class="hr-pill" data-tipo="Empleado">Empleados <b><?= $horariosEmp ?></b></button>
                <button type="button" class="hr-pill" data-tipo="Practicante">Practicantes <b><?= $horariosPract ?></b></button>
            </div>
        </div>

        <?php if (empty($schedules)): ?>
            <div class="hr-block">
                <div class="dev-empty">
                    <span class="material-symbols-outlined">schedule</span>
                    <p>No hay horarios registrados</p>
                </div>
            </div>
        <?php else: ?>
            <div class="dev-table-wrap hr-block">
                <table class="dev-table" id="hrTabla">
                        <thead>
                            <tr>
                                <th>Horario</th>
                                <th>Tipo</th>
                                <th>Dias</th>
                                <th>Hora</th>
                                <th>Tolerancia</th>
                                <th>Integrantes</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schData as $sd):
                                $s = $sch = $sd;
                                $isActive = ($s['estado'] ?? '') === 'Activo';
                                $isPract = ($s['tipo'] ?? '') === 'Practicante';
                                $detail = htmlspecialchars(json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                            ?>
                                <tr onclick="openScheduleDetail(JSON.parse(<?= $detail ?>))"
                                    data-tipo="<?= esc($s['tipo'] ?? 'Empleado') ?>"
                                    data-busca="<?= esc(mb_strtolower(trim(($s['nombre'] ?? '') . ' ' . ($s['tipo'] ?? '') . ' ' . ($s['dias'] ?? '')))) ?>">
                                    <td>
                                        <div class="sch-name">
                                            <strong><?= esc($s['nombre']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="hr-tag <?= $isPract ? 'hr-tag-pract' : 'hr-tag-emp' ?>">
                                            <span class="material-symbols-outlined"><?= $isPract ? 'school' : 'work' ?></span>
                                            <?= esc($tipoLabels[$s['tipo'] ?? 'Empleado'] ?? 'Empleados') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($s['dias']) ?></td>
                                    <td>
                                        <span class="hr-chips">
                                            <span class="hr-chip"><?= esc($s['entrada']) ?></span>
                                            <span class="hr-arrow">→</span>
                                            <span class="hr-chip"><?= esc($s['salida']) ?></span>
                                        </span>
                                    </td>
                                    <td><?= esc($s['tolerancia']) ?> min</td>
                                    <td>
                                        <button type="button" class="hr-int-btn" onclick="event.stopPropagation(); openIntegrantes(<?= (int) $s['id'] ?>)">
                                            <span class="material-symbols-outlined">group</span> <?= (int) $s['total'] ?>
                                        </button>
                                    </td>
                                    <td><span class="dev-badge <?= $isActive ? 'dev-badge-green' : 'dev-badge-red' ?>"><?= $isActive ? 'Activo' : 'Inactivo' ?></span></td>
                                    <td class="dev-table-actions" onclick="event.stopPropagation()">
                                        <button class="dev-btn-icon" title="Editar" onclick="openEditModal(<?= (int) $s['id'] ?>, '<?= esc($s['nombre'], 'attr') ?>', '<?= esc($s['tipo']) ?>', '<?= esc($s['dias'], 'attr') ?>', '<?= esc($s['entrada']) ?>', '<?= esc($s['salida']) ?>', <?= (int) $s['tolerancia'] ?>, '<?= empty($s['estado']) ? '' : esc($s['estado']) ?>')">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button class="dev-btn-icon" title="Integrantes" onclick="openIntegrantes(<?= (int) $s['id'] ?>)">
                                            <span class="material-symbols-outlined">group_add</span>
                                        </button>
                                        <form action="<?= site_url('admin/delete-schedule') ?>" method="POST" style="display:inline;" data-confirm-delete='{"message":"¿Eliminar este horario? Se desasignarán sus integrantes."}'>
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="sch_id" value="<?= (int) $s['id'] ?>">
                                            <button type="submit" class="dev-btn-icon dev-btn-danger" title="Eliminar">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="hrVacio" style="display:none;">
                                <td colspan="8" class="hr-vacio">
                                    <span class="material-symbols-outlined">search_off</span>
                                    Ningún horario coincide con la búsqueda
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="dev-table-mobile">
                    <?php foreach ($schData as $s):
                        $isActive = ($s['estado'] ?? '') === 'Activo';
                        $isPract = ($s['tipo'] ?? '') === 'Practicante';
                    ?>
                        <div class="dev-table-card">
                            <div class="dev-table-card-header">
                                <strong><?= esc($s['nombre']) ?></strong>
                                <span class="dev-badge <?= $isActive ? 'dev-badge-green' : 'dev-badge-red' ?>"><?= $isActive ? 'Activo' : 'Inactivo' ?></span>
                            </div>
                            <div class="dev-table-card-body">
                                <p style="margin-top:0;">
                                    <span class="hr-tag <?= $isPract ? 'hr-tag-pract' : 'hr-tag-emp' ?>">
                                        <span class="material-symbols-outlined"><?= $isPract ? 'school' : 'work' ?></span>
                                        <?= esc($tipoLabels[$s['tipo'] ?? 'Empleado'] ?? 'Empleados') ?>
                                    </span>
                                </p>
                                <p><strong>Dias:</strong> <?= esc($s['dias']) ?></p>
                                <p>
                                    <strong>Horario:</strong>
                                    <span class="hr-chips">
                                        <span class="hr-chip"><?= esc($s['entrada']) ?></span>
                                        <span class="hr-arrow">→</span>
                                        <span class="hr-chip"><?= esc($s['salida']) ?></span>
                                    </span>
                                </p>
                                <p><strong>Tolerancia:</strong> <?= esc($s['tolerancia']) ?> min</p>
                                <p style="margin-bottom:0;">
                                    <button type="button" class="hr-int-btn" onclick="openIntegrantes(<?= (int) $s['id'] ?>)">
                                        <span class="material-symbols-outlined">group</span> <?= (int) $s['total'] ?> integrantes
                                    </button>
                                </p>
                            </div>
                            <div class="dev-table-card-actions">
                                <button class="dev-btn-icon" onclick="openEditModal(<?= (int) $s['id'] ?>, '<?= esc($s['nombre'], 'attr') ?>', '<?= esc($s['tipo']) ?>', '<?= esc($s['dias'], 'attr') ?>', '<?= esc($s['entrada']) ?>', '<?= esc($s['salida']) ?>', <?= (int) $s['tolerancia'] ?>, '<?= empty($s['estado']) ? '' : esc($s['estado']) ?>')">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <form action="<?= site_url('admin/delete-schedule') ?>" method="POST" style="display:inline;" data-confirm-delete='{"message":"¿Eliminar este horario?"}'>
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="sch_id" value="<?= (int) $s['id'] ?>">
                                    <button type="submit" class="dev-btn-icon dev-btn-danger">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

    </div>
</main>

<!-- Modal · Nuevo horario (en sorteroOS el alta vive en un modal, no en un card) -->
<div class="dev-modal-overlay" id="createModal">
    <div class="dev-modal hr-modal">
        <div class="dev-modal-head hr-modal-head">
            <div class="hr-modal-icon"><span class="material-symbols-outlined">schedule</span></div>
            <div class="hr-modal-titles">
                <h2>Nuevo horario</h2>
                <p>Define el turno y luego asígnale a tu personal.</p>
            </div>
            <button type="button" class="dev-btn-icon" onclick="closeCreateModal()" aria-label="Cerrar">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/create-schedule') ?>" method="POST" class="dev-form" id="createScheduleForm">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <span class="hr-sec-label">Tipo</span>
                <div class="hr-choice" id="hrTipoPick">
                    <label class="hr-choice-opt">
                        <input type="radio" name="sch_tipo" value="Practicante">
                        <span class="hr-choice-ic"><span class="material-symbols-outlined">school</span></span>
                        <span class="hr-choice-tx">Practicante<small>Horario para practicantes</small></span>
                    </label>
                    <label class="hr-choice-opt on">
                        <input type="radio" name="sch_tipo" value="Empleado" checked>
                        <span class="hr-choice-ic"><span class="material-symbols-outlined">work</span></span>
                        <span class="hr-choice-tx">Empleado<small>Horario para empleados</small></span>
                    </label>
                </div>

                <span class="hr-sec-label">Turno</span>
                <div class="hr-modal-grid">
                    <div class="dev-field dev-field--full">
                        <label for="sch_nombre">Nombre <span class="hr-req">*</span></label>
                        <input type="text" id="sch_nombre" name="sch_nombre" required placeholder="Ej: Horario Mañana">
                    </div>
                    <div class="dev-field dev-field--full">
                        <label for="sch_dias">Días laborales <span class="hr-req">*</span></label>
                        <input type="text" id="sch_dias" name="sch_dias" value="Lun - Vie" required>
                        <div class="hr-presets" id="hrDiasPresets">
                            <button type="button" class="hr-preset on" data-dias="Lun - Vie">Lun - Vie</button>
                            <button type="button" class="hr-preset" data-dias="Lun - Sáb">Lun - Sáb</button>
                            <button type="button" class="hr-preset" data-dias="Todos los días">Todos los días</button>
                        </div>
                    </div>
                </div>

                <span class="hr-sec-label">Horario</span>
                <div class="hr-modal-grid">
                    <div class="hr-time-row dev-field--full">
                        <div class="dev-field">
                            <label for="sch_hora_entrada">Hora de entrada <span class="hr-req">*</span></label>
                            <input type="time" id="sch_hora_entrada" name="sch_hora_entrada" required>
                        </div>
                        <span class="hr-time-sep" aria-hidden="true">→</span>
                        <div class="dev-field">
                            <label for="sch_hora_salida">Hora de salida <span class="hr-req">*</span></label>
                            <input type="time" id="sch_hora_salida" name="sch_hora_salida" required>
                        </div>
                        <div class="dev-field hr-field-tol">
                            <label for="sch_tolerancia">Tolerancia (min) <span class="hr-req">*</span></label>
                            <div class="hr-with-unit">
                                <input type="number" id="sch_tolerancia" name="sch_tolerancia" value="10" min="0" required>
                                <span class="hr-unit" aria-hidden="true">min</span>
                            </div>
                        </div>
                    </div>
                </div>

                <span class="hr-sec-label">Vista previa</span>
                <div class="hr-preview" aria-live="polite">
                    <span class="hr-preview-ic" id="hrPvIcon"><span class="material-symbols-outlined">work</span></span>
                    <div class="hr-preview-body">
                        <b id="hrPvNombre">Nuevo horario</b>
                        <div class="hr-preview-meta">
                            <span id="hrPvTipo">Empleados</span>
                            <i class="hr-dot"></i>
                            <span id="hrPvDias">Lun - Vie</span>
                            <i class="hr-dot"></i>
                            <span id="hrPvHora">--:-- → --:--</span>
                            <i class="hr-dot"></i>
                            <span id="hrPvTol">10 min</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="hr-btn-ghost" onclick="closeCreateModal()">Cancelar</button>
                <button type="submit" class="hr-new">
                    <span class="material-symbols-outlined">add</span> Crear horario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Editar horario -->
<div class="dev-modal-overlay" id="editModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2>Editar horario</h2>
            <button class="dev-btn-icon" onclick="closeEditModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/update-schedule') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <input type="hidden" name="sch_id" id="edit_sch_id">
            <div class="dev-modal-body">
                <div class="hr-seg" id="hrTipoEdit" style="margin-bottom:12px;">
                    <label class="hr-seg-btn">
                        <input type="radio" name="sch_tipo" value="Practicante">
                        <span class="material-symbols-outlined">school</span> Practicante
                    </label>
                    <label class="hr-seg-btn">
                        <input type="radio" name="sch_tipo" value="Empleado">
                        <span class="material-symbols-outlined">work</span> Empleado
                    </label>
                </div>
                <div class="dev-form-grid">
                    <div class="dev-field">
                        <label for="edit_sch_nombre">Nombre</label>
                        <input type="text" id="edit_sch_nombre" name="sch_nombre" required>
                    </div>
                    <div class="dev-field">
                        <label for="edit_sch_dias">Dias laborales</label>
                        <input type="text" id="edit_sch_dias" name="sch_dias" required>
                    </div>
                    <div class="dev-field">
                        <label for="edit_sch_hora_entrada">Hora entrada</label>
                        <input type="time" id="edit_sch_hora_entrada" name="sch_hora_entrada" required>
                    </div>
                    <div class="dev-field">
                        <label for="edit_sch_hora_salida">Hora salida</label>
                        <input type="time" id="edit_sch_hora_salida" name="sch_hora_salida" required>
                    </div>
                    <div class="dev-field">
                        <label for="edit_sch_tolerancia">Tolerancia (min)</label>
                        <input type="number" id="edit_sch_tolerancia" name="sch_tolerancia" min="0" required>
                    </div>
                    <div class="dev-field">
                        <label for="edit_sch_estado">Estado</label>
                        <select id="edit_sch_estado" name="sch_estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Integrantes -->
<div class="dev-modal-overlay" id="integrantesModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2><span class="material-symbols-outlined">group</span> Integrantes</h2>
            <div style="display:flex; gap:6px; align-items:center;">
                <button class="dev-btn-icon" onclick="closeIntegrantes()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <form action="<?= site_url('admin/assign-schedule') ?>" method="POST" class="dev-form" id="integrantesForm">
            <?= csrf_field() ?>
            <input type="hidden" name="sch_id" id="int_sch_id">
            <div class="dev-modal-body">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
                    <strong style="font-size:15px; font-family:var(--g-font-display);" id="intTitle">—</strong>
                    <span class="hr-tag hr-tag-pract" id="intTag">—</span>
                    <span class="int-count" id="intCount">0 asignados</span>
                </div>
                <div class="int-list" id="intList"></div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeIntegrantes()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">
                    <span class="material-symbols-outlined">save</span> Guardar integrantes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Detalle -->
<div class="dev-modal-overlay" id="scheduleDetailOverlay" onclick="if(event.target===this)closeScheduleDetail()">
    <div class="dev-modal" style="max-width:440px;">
        <div class="dev-modal-head">
            <h3><span class="material-symbols-outlined">schedule</span> <span id="schDetailName">-</span></h3>
            <button type="button" class="dev-btn-icon" onclick="closeScheduleDetail()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="dev-modal-body">
            <div class="detail-who">
                <div class="detail-avatar" id="schDetailAvatar" style="background:var(--g-primary);">
                    <span class="material-symbols-outlined" style="font-size:26px;">schedule</span>
                </div>
                <div class="detail-sub" id="schDetailDias">-</div>
            </div>
            <div class="detail-grid">
                <div class="detail-field">
                    <div class="detail-label">Tipo</div>
                    <div class="detail-value" id="schDetailTipo">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Integrantes</div>
                    <div class="detail-value" id="schDetailCount">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Hora entrada</div>
                    <div class="detail-value"><code id="schDetailEntrada">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Hora salida</div>
                    <div class="detail-value"><code id="schDetailSalida">-</code></div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Tolerancia</div>
                    <div class="detail-value" id="schDetailTolerancia">-</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Estado</div>
                    <div class="detail-value" id="schDetailEstado">-</div>
                </div>
            </div>
        </div>
        <div class="dev-modal-footer">
            <button type="button" class="dev-btn dev-btn-text" onclick="closeScheduleDetail()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="dev-btn dev-btn-primary" id="schDetailEditBtn">
                <span class="material-symbols-outlined">edit</span> Editar
            </button>
        </div>
    </div>
</div>

<script>
var SCHEDULES = JSON.parse('<?= $intJson ?>');

// ── Selector de tipo (crear) ──
(function() {
    var pick = document.getElementById('hrTipoPick');
    if (!pick) return;
    pick.addEventListener('change', function(e) {
        if (e.target.name !== 'sch_tipo') return;
        pick.querySelectorAll('.hr-choice-opt').forEach(function(c) {
            c.classList.toggle('on', c.querySelector('input').checked);
        });
    });
})();
(function() {
    var pick = document.getElementById('hrTipoEdit');
    pick.addEventListener('change', function(e) {
        if (e.target.name !== 'sch_tipo') return;
        pick.querySelectorAll('.hr-seg-btn').forEach(function(c) {
            c.classList.toggle('on', c.querySelector('input').checked);
        });
    });
})();

function initials(n) {
    return String(n || '?').trim().split(/\s+/).map(function(w) { return w[0]; }).slice(0, 2).join('').toUpperCase();
}

function openEditModal(id, nombre, tipo, dias, entrada, salida, tolerancia, estado) {
    document.getElementById('edit_sch_id').value = id;
    document.getElementById('edit_sch_nombre').value = nombre;
    document.getElementById('edit_sch_dias').value = dias;
    document.getElementById('edit_sch_hora_entrada').value = entrada;
    document.getElementById('edit_sch_hora_salida').value = salida;
    document.getElementById('edit_sch_tolerancia').value = tolerancia;
    document.getElementById('edit_sch_estado').value = estado;
    var pick = document.getElementById('hrTipoEdit');
    pick.querySelectorAll('.hr-seg-btn').forEach(function(c) {
        var r = c.querySelector('input');
        r.checked = (r.value === tipo);
        c.classList.toggle('on', r.checked);
    });
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// ── Modal de alta (en sorteroOS el alta vive en un modal, no en un card) ──
function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});

// ── Presets de días laborales ──
(function() {
    var box   = document.getElementById('hrDiasPresets');
    var input = document.getElementById('sch_dias');
    if (!box || !input) return;
    function marcar() {
        box.querySelectorAll('.hr-preset').forEach(function(x) {
            x.classList.toggle('on', x.getAttribute('data-dias') === input.value.trim());
        });
    }
    box.addEventListener('click', function(e) {
        var b = e.target.closest('.hr-preset');
        if (!b) return;
        input.value = b.getAttribute('data-dias');
        marcar();
        input.dispatchEvent(new Event('input', { bubbles: true }));
    });
    input.addEventListener('input', marcar);
})();

// ── Vista previa: cómo quedará la fila en la tabla ──
(function() {
    var form = document.getElementById('createScheduleForm');
    if (!form) return;
    var f = {
        nombre: document.getElementById('sch_nombre'),
        dias:   document.getElementById('sch_dias'),
        ent:    document.getElementById('sch_hora_entrada'),
        sal:    document.getElementById('sch_hora_salida'),
        tol:    document.getElementById('sch_tolerancia')
    };
    var pv = {
        nombre: document.getElementById('hrPvNombre'),
        tipo:   document.getElementById('hrPvTipo'),
        icon:   document.getElementById('hrPvIcon'),
        dias:   document.getElementById('hrPvDias'),
        hora:   document.getElementById('hrPvHora'),
        tol:    document.getElementById('hrPvTol')
    };
    function txt(el, v) { if (el) el.textContent = v; }
    function pintar() {
        var sel  = form.querySelector('input[name="sch_tipo"]:checked');
        var tipo = sel ? sel.value : 'Empleado';
        var prac = tipo === 'Practicante';
        txt(pv.nombre, f.nombre && f.nombre.value.trim() ? f.nombre.value.trim() : 'Nuevo horario');
        txt(pv.tipo, prac ? 'Practicantes' : 'Empleados');
        if (pv.icon) {
            pv.icon.innerHTML = '<span class="material-symbols-outlined">' + (prac ? 'school' : 'work') + '</span>';
        }
        txt(pv.dias, f.dias && f.dias.value.trim() ? f.dias.value.trim() : 'Sin días');
        txt(pv.hora, (f.ent && f.ent.value ? f.ent.value : '--:--') + ' → ' + (f.sal && f.sal.value ? f.sal.value : '--:--'));
        txt(pv.tol, (f.tol && f.tol.value ? f.tol.value : '0') + ' min');
    }
    form.addEventListener('input', pintar);
    form.addEventListener('change', pintar);
    pintar();
})();

// ── Buscador + filtros por tipo (admin/usuarios) ──
(function() {
    var tabla = document.getElementById('hrTabla');
    if (!tabla) return;
    var input = document.getElementById('hrSearch');
    var pills = Array.prototype.slice.call(document.querySelectorAll('#hrFiltros .hr-pill'));
    var filas = Array.prototype.slice.call(tabla.querySelectorAll('tbody tr:not(#hrVacio)'));
    var vacio = document.getElementById('hrVacio');
    var tipo  = '';
    function aplicar() {
        var q = (((input && input.value) || '').trim()).toLowerCase();
        var visibles = 0;
        filas.forEach(function(f) {
            var okTipo = !tipo || f.getAttribute('data-tipo') === tipo;
            var okTxt  = !q || (f.getAttribute('data-busca') || '').indexOf(q) !== -1;
            var ok = okTipo && okTxt;
            f.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });
        if (vacio) vacio.style.display = visibles ? 'none' : '';
    }
    if (input) input.addEventListener('input', aplicar);
    pills.forEach(function(p) {
        p.addEventListener('click', function() {
            pills.forEach(function(x) { x.classList.remove('on'); });
            p.classList.add('on');
            tipo = p.getAttribute('data-tipo') || '';
            aplicar();
        });
    });
})();

function openIntegrantes(id) {
    var sch = SCHEDULES.find(function(s) { return s.id === id; });
    if (!sch) return;
    document.getElementById('int_sch_id').value = id;
    document.getElementById('intTitle').textContent = sch.nombre;
    var tag = document.getElementById('intTag');
    var isPract = sch.tipo === 'Practicante';
    tag.textContent = isPract ? 'Practicantes' : 'Empleados';
    tag.className = 'hr-tag ' + (isPract ? 'hr-tag-pract' : 'hr-tag-emp');
    var list = document.getElementById('intList');
    if (!sch.cands.length) {
        list.innerHTML = '<div class="int-none"><span class="material-symbols-outlined">person_off</span>No hay personal del tipo ' + (isPract ? 'Practicante' : 'Empleado') + ' para asignar todavía.</div>';
    } else {
        list.innerHTML = sch.cands.map(function(u) {
            var on = sch.assigned.indexOf(u.id) !== -1;
            return '<label class="int-item ' + (isPract ? '' : 'emp') + '">'
                 + '<input type="checkbox" name="sch_users[]" value="' + u.id + '"' + (on ? ' checked' : '') + '>'
                 + '<div class="int-av">' + initials(u.name) + '</div>'
                 + '<div class="int-main"><b>' + (u.name || 'Sin nombre') + '</b><span>' + (u.cargo || '—') + ' · ' + (u.code || '') + '</span></div>'
                 + '</label>';
        }).join('');
    }
    document.getElementById('intCount').textContent = sch.assigned.length + ' de ' + sch.cands.length + ' asignados';
    document.getElementById('integrantesModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeIntegrantes() {
    document.getElementById('integrantesModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('integrantesModal').addEventListener('click', function(e) {
    if (e.target === this) closeIntegrantes();
});

function openScheduleDetail(sch) {
    var isActive = (sch.estado || '') === 'Activo';
    var isPract = sch.tipo === 'Practicante';
    document.getElementById('schDetailName').textContent = sch.nombre || '-';
    document.getElementById('schDetailDias').textContent = sch.dias || 'Lun - Vie';
    document.getElementById('schDetailEntrada').textContent = sch.entrada || '08:00';
    document.getElementById('schDetailSalida').textContent = sch.salida || '17:00';
    document.getElementById('schDetailTolerancia').textContent = (sch.tolerancia || 10) + ' min';
    var tipoEl = document.getElementById('schDetailTipo');
    tipoEl.textContent = isPract ? 'Practicantes' : 'Empleados';
    tipoEl.style.color = isPract ? 'var(--g-primary)' : 'var(--g-success)';
    document.getElementById('schDetailCount').textContent = (sch.total || 0) + ' integrantes';
    var estadoEl = document.getElementById('schDetailEstado');
    estadoEl.textContent = isActive ? 'Activo' : 'Inactivo';
    estadoEl.style.color = isActive ? 'var(--g-success)' : 'var(--g-text-secondary)';
    document.getElementById('schDetailEditBtn').onclick = function() {
        closeScheduleDetail();
        openEditModal(sch.id, sch.nombre, sch.tipo, sch.dias, sch.entrada, sch.salida, sch.tolerancia, sch.estado);
    };
    document.getElementById('scheduleDetailOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeScheduleDetail() {
    document.getElementById('scheduleDetailOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.detail-overlay.active, .dev-modal-overlay.active').forEach(function(m) {
            m.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
});
</script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>