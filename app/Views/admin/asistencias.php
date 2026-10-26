<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .att-wrap { display: flex; flex-direction: column; gap: 22px; }

        /* ── Toolbar de filtros · lenguaje sorteroOS / Educonecta ──────
           Una sola barra con todo el control en dos filas, en lugar de tres
           tarjetas de alturas desiguales que estiraban su fondo y dejaban
           huecos vacíos. Los controles siguen la receta de .sb-btn de
           packages/sb-ui/styles.css:307 —radio 12px, padding 10/20, fondo
           surface-container y hover surface-container-high— y el foco usa su
           doble anillo `0 0 0 2px surface, 0 0 0 4px primary`. */
        .att-toolbar {
            display: flex; flex-wrap: wrap; align-items: flex-start; gap: 14px;
            background: var(--sb-surface); border-radius: 16px; padding: 16px 18px;
        }
        .att-tb-main { flex: 1 1 620px; min-width: 0; display: flex; flex-direction: column; gap: 14px; }
        .att-tb-row { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
        .att-tb-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .att-tb-group--grow { flex: 1 1 320px; }
        .att-tb-label {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        /* Filete vertical entre bloques de la barra */
        .att-tb-div {
            align-self: stretch; flex: none; width: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 70%, transparent);
        }
        .att-dates { display: flex; align-items: center; gap: 8px; }
        .att-dates-sep { color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent); }
        .att-input {
            width: 158px; height: 38px; padding: 0 10px;
            border: 1px solid color-mix(in srgb, var(--sb-outline-variant) 60%, transparent);
            border-radius: 12px; background: var(--sb-surface); color: var(--sb-on-surface);
            font-size: 0.85rem; font-family: inherit; outline: none;
            transition: border-color 125ms ease, box-shadow 125ms ease;
        }
        .att-input:hover { border-color: var(--sb-outline-variant); }
        .att-input:focus, .att-input:focus-visible {
            border-color: var(--sb-primary);
            box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary);
        }
        /* Controles segmentados: Hoy/Semana/Mes/Todo y los estados */
        .att-seg { display: inline-flex; gap: 4px; flex-wrap: wrap; }
        .att-seg-btn {
            height: 38px; padding: 0 16px; border: 1px solid transparent; border-radius: 50px;
            background: var(--sb-surface-container); color: var(--sb-on-surface); cursor: pointer;
            font-size: 0.85rem; font-weight: 500; font-family: inherit; white-space: nowrap;
            display: inline-flex; align-items: center; gap: 6px;
            transition: none;
        }
        .att-seg-btn:hover { background: var(--sb-surface-container-high); }
        .att-seg-btn:focus-visible { outline: none; box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        .att-seg-btn.on { background: var(--sb-primary); color: #fff; }
        .att-seg-count {
            font-size: 11px; font-weight: 700; font-variant-numeric: tabular-nums; opacity: .55;
        }
        .att-seg-btn.on .att-seg-count { opacity: .85; }
        /* Acciones (sb-btn) */
        /* CTA EduconectaV2 — píldora .lg-submit: r999 · h48 · 14px/600 ·
           hover plano: cambia de color sin moverse (sin translate/scale) */
        .att-go {
            height: 48px; padding: 0 26px; border: 0; border-radius: 50px; cursor: pointer;
            background: var(--sb-primary); color: #fff; font-weight: 600; font-size: 14px;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit; flex: none;
            transition: none;
        }
        .att-go:hover { background: color-mix(in srgb, var(--sb-primary) 88%, #000); }
        .att-go:focus-visible { outline: none; box-shadow: 0 0 0 2px var(--sb-surface), 0 0 0 4px var(--sb-primary); }
        .att-go-ghost {
            background: var(--sb-surface-container); color: var(--sb-on-surface);
            width: auto; padding: 0 26px;
        }
        .att-go-ghost:hover { background: var(--sb-surface-container-high); filter: none; }
        /* Columna de disparadores: Filtrar + CSV juntos y alineados en una
           misma línea, centrados respecto a las dos filas de la barra.
           Los botones viven fuera de sus formularios y los disparan con
           form="" (los <form> no se pueden anidar). */
        .att-tb-actions {
            display: flex; align-items: center; gap: 10px; flex: none;
            align-self: center;
        }

        /* ── KPIs: estado "filtro aplicado" → ver el bloque de métricas más abajo ── */

        /* ── Registros ───────────────────────── */
        .att-list { display: flex; flex-direction: column; gap: 10px; padding: 14px 18px 18px; }
        .att-item {
            display: flex; align-items: center; gap: 14px; padding: 14px 16px;
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 14px;
            cursor: pointer; transition: border-color 200ms;
        }
        .att-item:hover { border-color: var(--g-primary); }
        .att-av {
            width: 42px; height: 42px; border-radius: 12px; background: var(--g-surface-variant);
            color: var(--g-text-secondary); font-weight: 600; font-size: 0.95rem;
            display: grid; place-items: center; flex: none;
        }
        .att-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px; }
        .att-name { font-weight: 600; font-size: 0.9rem; color: var(--g-text); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .att-dni { font-size: 0.78rem; color: var(--g-text-secondary); font-variant-numeric: tabular-nums; }
        .att-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.8rem; color: var(--g-text-secondary); }
        .att-meta b { color: var(--g-text); font-weight: 500; }
        .att-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; }
        .att-estado i { width: 8px; height: 8px; border-radius: 50%; }
        .att-estado.present { color: #16a34a; } .att-estado.present i { background: #22c55e; }
        .att-estado.late    { color: #d97706; } .att-estado.late    i { background: #f59e0b; }
        .att-estado.absent  { color: #64748b; } .att-estado.absent  i { background: #94a3b8; }
        .att-estado.no_exit { color: #2563eb; } .att-estado.no_exit i { background: #3b82f6; }
        .att-edit {
            width: 36px; height: 36px; border: 1px solid var(--g-border); border-radius: 50px;
            background: transparent; color: var(--g-text-secondary); cursor: pointer;
            display: grid; place-items: center; flex: none; transition: none;
        }
        .att-edit:hover { border-color: var(--g-primary); color: var(--g-primary); }
        .att-edit .material-symbols-outlined { font-size: 18px; }

        /* ── Detalle — canon EduconectaV2: dev-modal (r20 · hairline ·
           overlay neutro) con footer de dev-btn; dentro del body sólo
           la fila de identidad (chip de estado + DNI) ── */
        .detail-who { display: flex; align-items: center; gap: 14px; padding: 4px 0 18px; }
        .detail-avatar {
            width: 48px; height: 48px; border-radius: 14px; color: #fff; font-size: 1.2rem; font-weight: 600;
            display: grid; place-items: center; flex: none;
        }
        .detail-sub { font-size: 0.8rem; color: var(--g-text-secondary); }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: var(--g-text-secondary); text-transform: uppercase; letter-spacing: .04em; }
        .detail-value {
            font-size: 0.9rem; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--sb-surface-container); border-radius: 12px;
            font-variant-numeric: tabular-nums;
        }

        @media (max-width: 760px) {
            /* La barra se apila: los inputs de fecha ocupan su fila */
            .att-tb-row { align-items: stretch; }
            .att-tb-div { display: none; }
            .att-dates { width: 100%; }
            .att-dates .att-input { flex: 1 1 120px; width: auto; }
            .att-seg { width: 100%; }
            .att-tb-actions { flex: 1 1 100%; align-self: stretch; }
            .att-tb-actions .att-go { flex: 1 1 calc(50% - 5px); justify-content: center; }
        }
        /* ── Paginación ─────────────────────── */
        .att-pager { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; padding: 16px 18px 20px; }
        .att-page {
            min-width: 34px; height: 34px; padding: 0 9px; display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--g-border); border-radius: 50px; background: transparent;
            color: var(--g-text); font-size: .82rem; font-weight: 600; text-decoration: none; font-family: inherit;
        }
        .att-page:hover { background: var(--g-surface-variant); }
        .att-page.on { background: var(--g-primary); border-color: var(--g-primary); color: var(--g-bg, #fff); }
        .att-page-info { font-size: .78rem; color: var(--g-text-secondary); margin-left: 8px; }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
    <style>
        /* ═══════════════════════════════════════════════════════════════
           ASISTENCIAS · diseño premium (scoped a esta página).
           Estética tipo Next.js/Vercel: bordes sutiles, sombras en capas,
           tipografía con tracking ajustado y micro-interacciones suaves.
           Colocado DESPUÉS de next-panel.css para ganar la cascada sin
           tocar los CSS compartidos.
           ═══════════════════════════════════════════════════════════════ */

        /* ── Los controles de la barra de filtros se definen en el primer
           bloque <style>, antes que next-panel.css: aquí no se pisan ── */

        /* ── KPIs · rejilla de métricas sorteroOS ────────────────────────
           Réplica de minimalist-dashboard.tsx: contenedor rounded-2xl con
           hairlines (gap-px + bg outline-variant/20), y cada celda
           bg-surface p-5 flex-col gap-3 con una fila arriba que lleva el
           icono (h-4 w-4, tenue y a color en hover) y, debajo, label 10px
           uppercase tracking-wider + valor text-xl bold tracking-tight.

           Estas celdas son enlaces que aplican filtro, así que además
           heredan hover y focus-visible. Los `!important` del icono son
           inevitables: next-panel.css:439 fija su color con !important
           (educonecta.css agrupa .dev-stat-icon del mismo modo). */
        body:has(.dev-main) .dev-stats-grid.att-kpis {
            gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px;
            overflow: hidden;
            grid-auto-flow: initial;
            grid-auto-columns: initial;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-bottom: 24px;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .dev-stat-card {
            background: var(--sb-surface);
            border: 0;
            border-radius: 0;
            padding: 20px;                    /* p-5 */
            display: flex;
            flex-direction: column;           /* gap-3 va por margen: así label→valor
                                                 conserva su mt-0.5 de 2px */
            transition: background-color 150ms ease;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .dev-stat-card:hover {
            background: var(--sb-surface-container-low);
            border-color: transparent;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .dev-stat-card:focus-visible {
            outline: 2px solid color-mix(in srgb, var(--sb-primary) 70%, transparent);
            outline-offset: -2px;
            z-index: 1;
        }
        /* Fila del icono: desnuda, sin chip de color, a la izquierda */
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon {
            width: auto; height: auto; margin-bottom: 12px; border-radius: 0;  /* gap-3 */
            display: flex; align-items: center; justify-content: space-between;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon .material-symbols-outlined {
            font-size: 16px;                   /* h-4 w-4 */
            opacity: .55;
            transition: opacity 150ms ease;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-accent .material-symbols-outlined { color: var(--sb-primary) !important; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-green  .material-symbols-outlined { color: #16a34a !important; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-amber  .material-symbols-outlined { color: #b45309 !important; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-red    .material-symbols-outlined { color: #dc2626 !important; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-blue   .material-symbols-outlined { color: #2563eb !important; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat:hover .dev-stat-icon .material-symbols-outlined { opacity: 1; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .dev-stat-label {
            font-size: 10px; font-weight: 500; letter-spacing: .05em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            margin-bottom: 2px;
        }
        body:has(.dev-main) .dev-stats-grid.att-kpis .dev-stat-value {
            font-size: 20px;                   /* text-xl */
            font-weight: 700;
            letter-spacing: -.025em;           /* tracking-tight */
            line-height: 1.1;
            font-variant-numeric: tabular-nums;
            color: var(--sb-on-surface);
        }
        /* Filtro aplicado (radio-cards): tinte de color según estado, sin
           anillo — el anillo rompía la rejilla de hairlines */
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on { box-shadow: none; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) { background: color-mix(in srgb, var(--sb-primary) 7%, var(--sb-surface)); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  { background: color-mix(in srgb, #16a34a 7%, var(--sb-surface)); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  { background: color-mix(in srgb, #b45309 7%, var(--sb-surface)); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    { background: color-mix(in srgb, #dc2626 7%, var(--sb-surface)); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   { background: color-mix(in srgb, #2563eb 7%, var(--sb-surface)); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on .dev-stat-icon .material-symbols-outlined { opacity: 1; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) .dev-stat-value,
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) .dev-stat-label { color: var(--sb-primary); }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  .dev-stat-value,
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  .dev-stat-label { color: #16a34a; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  .dev-stat-value,
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  .dev-stat-label { color: #b45309; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    .dev-stat-value,
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    .dev-stat-label { color: #dc2626; }
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   .dev-stat-value,
        body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   .dev-stat-label { color: #2563eb; }

        @media (min-width: 768px) {
            body:has(.dev-main) .dev-stats-grid.att-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (min-width: 1200px) {
            body:has(.dev-main) .dev-stats-grid.att-kpis { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }

        /* ── Registros · lista moderna ─────────────────────────────── */
        .att-list { padding: 8px; gap: 2px; }
        .att-item {
            border: 1px solid transparent; border-radius: 12px;
            transition: background .15s ease, border-color .15s ease;
        }
        .att-item:hover { background: var(--g-surface-variant); border-color: var(--g-border); }
        .att-av { border-radius: 11px; font-weight: 600; }
        .att-name { font-weight: 600; letter-spacing: -.01em; }
        .att-estado {
            padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: .78rem;
        }
        .att-estado.present { color: #15803d; background: rgba(34,197,94,.13); }
        .att-estado.late    { color: #b45309; background: rgba(245,158,11,.15); }
        .att-estado.absent  { color: #475569; background: rgba(100,116,139,.14); }
        .att-estado.no_exit { color: #1d4ed8; background: rgba(59,130,246,.13); }
        /* avatar con tinte sutil del estado */
        .att-item:has(.att-estado.present) .att-av { background: rgba(34,197,94,.12); color: #16a34a; }
        .att-item:has(.att-estado.late)    .att-av { background: rgba(245,158,11,.14); color: #b45309; }
        .att-item:has(.att-estado.absent)  .att-av { background: rgba(100,116,139,.13); color: #64748b; }
        .att-item:has(.att-estado.no_exit) .att-av { background: rgba(59,130,246,.12); color: #2563eb; }
        .att-edit { border-radius: 50px; transition: none; }
        .att-edit:hover { background: var(--g-surface-variant); }

        /* ── Tarjeta Registros · cabecera pulida ───────────────────── */
        .dev-card { border: 1px solid var(--g-border); border-radius: 16px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }

        /* ── Botón Filtrar · verde consistente en oscuro ─────────────
           :not(.att-go-ghost) para no pintar el botón CSV (outline) */
        html.dark .att-go:not(.att-go-ghost) { background: #1b7a42; color: #ffffff; }
        html.dark .att-go:not(.att-go-ghost):hover { background: #1f8c4d; }

        /* ═══════════════════════════════════════════════════════════════
           MODO OSCURO · ajustes de superficie, sombras y acentos
           ═══════════════════════════════════════════════════════════════ */
        html.dark .dev-card {
            box-shadow: 0 1px 2px rgba(0,0,0,.4);
        }
        /* KPIs · oscuro: mismo diseño, iconos en tono claro y sin sombras */
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-accent .material-symbols-outlined { color: #4ade80 !important; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-green  .material-symbols-outlined { color: #4ade80 !important; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-amber  .material-symbols-outlined { color: #fbbf24 !important; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-red    .material-symbols-outlined { color: #f87171 !important; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat .dev-stat-icon.si-blue   .material-symbols-outlined { color: #60a5fa !important; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) { background: color-mix(in srgb, #4ade80 12%, var(--sb-surface)); }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  { background: color-mix(in srgb, #4ade80 12%, var(--sb-surface)); }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  { background: color-mix(in srgb, #fbbf24 12%, var(--sb-surface)); }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    { background: color-mix(in srgb, #f87171 12%, var(--sb-surface)); }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   { background: color-mix(in srgb, #60a5fa 12%, var(--sb-surface)); }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) .dev-stat-value,
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-accent) .dev-stat-label { color: #4ade80; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  .dev-stat-value,
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-green)  .dev-stat-label { color: #4ade80; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  .dev-stat-value,
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-amber)  .dev-stat-label { color: #fbbf24; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    .dev-stat-value,
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-red)    .dev-stat-label { color: #f87171; }
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   .dev-stat-value,
        html.dark body:has(.dev-main) .dev-stats-grid.att-kpis .att-stat.on:has(.si-blue)   .dev-stat-label { color: #60a5fa; }
        html.dark .att-item:hover { background: #161616; border-color: #262626; }
        html.dark .att-input:hover { border-color: #3f3f46; }
        html.dark .att-estado.present { color: #4ade80; background: rgba(34,197,94,.14); }
        html.dark .att-estado.late    { color: #fbbf24; background: rgba(251,191,36,.13); }
        html.dark .att-estado.absent  { color: #94a3b8; background: rgba(148,163,184,.13); }
        html.dark .att-estado.no_exit { color: #60a5fa; background: rgba(96,165,250,.13); }
        html.dark .att-item:has(.att-estado.present) .att-av { background: rgba(34,197,94,.14); color: #4ade80; }
        html.dark .att-item:has(.att-estado.late)    .att-av { background: rgba(251,191,36,.13); color: #fbbf24; }
        html.dark .att-item:has(.att-estado.absent)  .att-av { background: rgba(148,163,184,.13); color: #94a3b8; }
        html.dark .att-item:has(.att-estado.no_exit) .att-av { background: rgba(96,165,250,.13); color: #60a5fa; }

        /* ═══════════════════════════════════════════════════════════════
           ASISTENCIAS · lenguaje sorteroOS  (igual que el dashboard)
           sorteroOS no usa card con borde: el título queda apoyado sobre el
           fondo de página (11px, mayúsculas, tracking amplio, muy apagado)
           y el "card" lo forma el bloque de contenido de abajo —redondo, sin
           marco ni sombra, con hairlines de 1px—.

           Aquí NO se toca el markup de Registros: el .dev-card pasa a
           transparente, su cabecera queda viviendo sobre el fondo y
           .att-list es el que se convierte en el bloque blanco.

           Todos los selectores llevan `body:has(.dev-main)` + una clase más:
           así superan las reglas de este mismo archivo (0,2,1 / 0,3,1) y las
           de next-panel.css sin necesidad de !important. */
        body:has(.dev-main) .dev-card {
            background: transparent;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        /* Cabecera: micro-título sobre el fondo, sin divisoria ni padding lateral */
        body:has(.dev-main) .dev-card .dev-card-head {
            padding: 0 0 16px;
            border-bottom: 0;
            border-radius: 0;
            align-items: baseline;
        }
        body:has(.dev-main) .dev-card .dev-card-head h2 {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            gap: 6px;
        }
        body:has(.dev-main) .dev-card .dev-card-head h2 .material-symbols-outlined {
            font-size: 15px;
            color: inherit;
        }

        /* ── Registros · lista con hairlines (rounded-2xl + space-y-px) ── */
        body:has(.dev-main) .dev-card .att-list {
            display: flex;
            flex-direction: column;
            gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 10%, transparent);
            border-radius: 16px;
            overflow: hidden;
            padding: 0;
        }
        body:has(.dev-main) .dev-card .att-item {
            background: var(--sb-surface);
            border: 0;
            border-radius: 0;
            padding: 16px 20px;
            transition: background .15s ease;
        }
        body:has(.dev-main) .dev-card .att-item:hover {
            background: var(--sb-surface-container-low);
            border-color: transparent;
        }
        /* Caja del avatar: 32px / r8 / surface-container y neutra — se
           apagan los tintes por estado. Lleva .att-item en la cadena para
           ganarle a `html.dark .att-item:has(.att-estado.x) .att-av`
           (0,4,2), que si no repondría el tinte en modo oscuro. */
        body:has(.dev-main) .dev-card .att-item .att-av {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--sb-surface-container);
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            font-size: 11px;
            font-weight: 600;
        }
        body:has(.dev-main) .dev-card .att-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--sb-on-surface);
        }
        body:has(.dev-main) .dev-card .att-dni {
            font-size: 12px;
            font-weight: 400;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        /* Meta (fecha · entrada · salida): una línea tenue, sin negritas negras */
        body:has(.dev-main) .dev-card .att-meta {
            font-size: 12px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        body:has(.dev-main) .dev-card .att-meta b {
            color: inherit;
            font-weight: 500;
        }
        /* Acciones de fila: sin marco */
        body:has(.dev-main) .dev-card .att-edit {
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 50px;
            background: transparent;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        body:has(.dev-main) .dev-card .att-edit:hover {
            border-color: transparent;
            background: var(--sb-surface-container);
            color: var(--sb-primary);
        }
        body:has(.dev-main) .dev-card .att-pager { margin-top: 12px; }
        /* Estado vacío: ese bloque es el card */
        body:has(.dev-main) .dev-card .dev-empty {
            background: var(--sb-surface);
            border-radius: 16px;
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
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
    <?= view('partials/admin-sidebar', ['activePage' => 'asistencias']) ?>

    <main class="dev-main">
        <div class="dev-topbar">
            <h1 class="dev-topbar-title">Asistencias</h1>
            <p class="dev-topbar-subtitle">Registro de asistencia del equipo</p>
        </div>

        <div class="dev-content">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                    <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= session()->getFlashdata('msg') ?></span>
                </div>
            <?php endif; ?>

            <?php
                // Totales de TODO el rango filtrado (no de la página visible)
                $r = $resumen ?? ['total' => 0, 'presentes' => 0, 'tardanzas' => 0, 'faltas' => 0, 'sin_salida' => 0];
                $totalRegistros = $r['total'];
                $totalPresentes = $r['presentes'];
                $totalTardanzas = $r['tardanzas'];
                $totalFaltas    = $r['faltas'];
                $totalSinSalida = $r['sin_salida'];
                $filtroEstado   = trim((string) ($estado ?? ''));
                $hoy    = date('Y-m-d');
                $semana = date('Y-m-d', strtotime('monday this week'));
                $mesIni = date('Y-m-01');
                $mode   = empty($fecha_inicio) || empty($fecha_fin) ? 'todo' : ($fecha_inicio === $fecha_fin ? ($fecha_inicio === $hoy ? 'hoy' : '') : ($fecha_inicio === $semana && $fecha_fin === $hoy ? 'semana' : ($fecha_inicio === $mesIni ? 'mes' : '')));
            ?>

            <div class="att-wrap">
                <?php
                    // Links de los KPI: conservan el rango actual y cambian sólo el estado
                    $qsBase = array_merge($pag_qs ?? [], ['page' => 1]);
                    $kpiLink = static function (string $est) use ($qsBase): string {
                        $qs = $est === '' ? array_diff_key($qsBase, ['estado' => 1]) : array_merge($qsBase, ['estado' => $est]);
                        return '?' . http_build_query($qs);
                    };
                ?>

                <!-- Barra de filtros · una sola barra con todo el control,
                     en lugar de tres tarjetas de alturas desiguales -->
                <div class="att-toolbar">
                    <form method="get" action="<?= base_url('admin/asistencias') ?>" id="filtro-form" class="att-tb-main">
                        <input type="hidden" name="estado" id="estado" value="<?= esc($filtroEstado) ?>">

                        <div class="att-tb-row">
                            <div class="att-tb-group">
                                <span class="att-tb-label" id="lbl-rango">Rango de fechas</span>
                                <div class="att-dates" role="group" aria-labelledby="lbl-rango">
                                    <input type="date" class="att-input" id="fecha_inicio" name="fecha_inicio"
                                           value="<?= esc($fecha_inicio ?? '') ?>" aria-label="Desde">
                                    <span class="att-dates-sep" aria-hidden="true">→</span>
                                    <input type="date" class="att-input" id="fecha_fin" name="fecha_fin"
                                           value="<?= esc($fecha_fin ?? '') ?>" aria-label="Hasta">
                                </div>
                            </div>

                            <span class="att-tb-div" aria-hidden="true"></span>

                            <div class="att-tb-group">
                                <span class="att-tb-label" id="lbl-periodo">Periodo</span>
                                <div class="att-seg" role="group" aria-labelledby="lbl-periodo">
                                    <button type="button" class="att-seg-btn <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                                    <button type="button" class="att-seg-btn <?= $mode === 'semana' ? 'on' : '' ?>" onclick="rango('semana')">Semana</button>
                                    <button type="button" class="att-seg-btn <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Mes</button>
                                    <button type="button" class="att-seg-btn <?= $mode === 'todo' ? 'on' : '' ?>" onclick="rango('todo')">Todo</button>
                                </div>
                            </div>
                        </div>

                        <div class="att-tb-row">
                            <div class="att-tb-group att-tb-group--grow">
                                <span class="att-tb-label" id="lbl-estado">Estado</span>
                                <div class="att-seg" role="group" aria-labelledby="lbl-estado">
                                    <?php
                                        // Los conteos sólo tienen sentido sin filtro de estado (con filtro, el resto queda en 0)
                                        $conteos = $filtroEstado === '' ? [
                                            ''         => $totalRegistros,
                                            'present'  => $totalPresentes,
                                            'late'     => $totalTardanzas,
                                            'absent'   => $totalFaltas,
                                            'no_exit'  => $totalSinSalida,
                                        ] : [];
                                        $estados = [
                                            ''         => 'Todos',
                                            'present'  => 'Presentes',
                                            'late'     => 'Tardanzas',
                                            'absent'   => 'Faltas',
                                            'no_exit'  => 'Sin salida',
                                        ];
                                        foreach ($estados as $val => $txt): ?>
                                            <button type="button" class="att-seg-btn <?= $filtroEstado === $val ? 'on' : '' ?>" onclick="estado('<?= $val ?>')">
                                                <?= $txt ?><?= isset($conteos[$val]) ? '<span class="att-seg-count">' . $conteos[$val] . '</span>' : '' ?>
                                            </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Filtrar y CSV juntos y alineados: Filtrar dispara el
                         form GET y CSV el form POST vía el atributo form=""
                         (los <form> no se pueden anidar) -->
                    <div class="att-tb-actions">
                        <button type="submit" form="filtro-form" class="att-go">
                            <span class="material-symbols-outlined">search</span> Filtrar
                        </button>
                        <button type="submit" form="csv-form" class="att-go att-go-ghost" title="Descargar el rango filtrado en CSV">
                            <span class="material-symbols-outlined">download</span> CSV
                        </button>
                    </div>

                    <!-- El CSV en su propio form, oculto: sólo aporta los
                         campos (fechas + estado) que envía el botón de arriba -->
                    <form method="post" action="<?= site_url('admin/exportar/asistencias') ?>" id="csv-form" hidden>
                        <?= csrf_field() ?>
                        <input type="hidden" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                        <input type="hidden" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                        <input type="hidden" name="estado" value="<?= esc($filtroEstado) ?>">
                    </form>
                </div>

                <!-- KPIs · rejilla de métricas sorteroOS (también filtran por estado) -->
                <div class="dev-stats-grid att-kpis">
                    <?php
                        $kpis = [
                            [''        , 'summarize'   , 'Total registros', $totalRegistros, 'si-accent'],
                            ['present' , 'check_circle', 'Presentes'      , $totalPresentes, 'si-green'],
                            ['late'    , 'schedule'    , 'Tardanzas'      , $totalTardanzas, 'si-amber'],
                            ['absent'  , 'cancel'      , 'Faltas'         , $totalFaltas   , 'si-red'],
                            ['no_exit' , 'logout'      , 'Sin salida'     , $totalSinSalida, 'si-blue'],
                        ];
                        foreach ($kpis as [$est, $ico, $lbl, $num, $cls]): ?>
                            <a class="dev-stat-card att-stat <?= $filtroEstado === $est ? 'on' : '' ?>"
                               href="<?= $kpiLink($est) ?>" title="Filtrar por <?= $lbl ?>">
                                <div class="dev-stat-icon <?= $cls ?>"><span class="material-symbols-outlined filled"><?= $ico ?></span></div>
                                <div class="dev-stat-label"><?= $lbl ?></div>
                                <div class="dev-stat-value"><?= $num ?></div>
                            </a>
                        <?php endforeach; ?>
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">history</span> Registros</h2>
                    </div>

                    <?php if (empty($attendance)): ?>
                        <div class="dev-empty">
                            <span class="material-symbols-outlined">event_busy</span>
                            <p>
                                <strong>Sin marcaciones en el rango elegido.</strong><br>
                                <?php
                                    // El mensaje dice la verdad de ambos filtros:
                                    // si hay rango lo nombra, y si hay filtro de estado lo señala
                                    $etiquetaEstado = ['present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida'][$estado] ?? '';
                                    if ($fecha_inicio && $fecha_fin) {
                                        echo 'Buscando entre el ', date('d/m/Y', strtotime($fecha_inicio)), ' y el ', date('d/m/Y', strtotime($fecha_fin));
                                    } else {
                                        echo 'Buscando todo el histórico';
                                    }
                                    echo $etiquetaEstado !== '' ? ' con estado «' . esc($etiquetaEstado) . '»' : '';
                                ?>.
                                Elige «Todo» para ver el listado completo, o cambia el periodo.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="tk-tbl-wrap">
                        <table class="tk-tbl tk-tbl-click">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Estado</th>
                                    <th class="tk-r">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($attendance as $r):
                                $st     = $r['status'] ?? 'absent';
                                $estado = match($st) { 'present' => 'present', 'late' => 'late', 'absent' => 'absent', 'no_exit' => 'no_exit', default => 'absent' };
                                $label  = match($st) { 'present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', 'absent_justified' => 'Falta justificada', default => ucfirst($st) };
                                $nombre = $r['name'] ?? 'Empleado';
                                $fechaTxt = !empty($r['date']) ? date('d/m/Y', strtotime($r['date'])) : '—';
                            ?>
                                <tr class="att-row" data-id="<?= (int) $r['id'] ?>" onclick="openDetail('<?= (int) $r['id'] ?>')">
                                    <td>
                                        <div class="tk-cell-user">
                                            <span class="tk-cell-av"><?= strtoupper(mb_substr($nombre, 0, 1, 'UTF-8')) ?></span>
                                            <span>
                                                <span class="tk-cell-main"><?= esc($nombre) ?></span><br>
                                                <span class="tk-cell-sub tk-cell-mono">DNI <?= esc($r['dni'] ?? '—') ?></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="tk-cell-mono"><?= esc($fechaTxt) ?></td>
                                    <td class="tk-cell-mono"><?= !empty($r['time_in']) ? esc(substr($r['time_in'], 0, 5)) : '<span class="tk-cell-muted">—</span>' ?></td>
                                    <td class="tk-cell-mono"><?= !empty($r['time_out']) ? esc(substr($r['time_out'], 0, 5)) : '<span class="tk-cell-muted">—</span>' ?></td>
                                    <td><span class="att-estado <?= $estado ?>"><i></i><?= $label ?></span></td>
                                    <td class="tk-r" onclick="event.stopPropagation()">
                                        <span class="tk-actions">
                                            <a class="dev-btn-icon" href="<?= site_url('admin/auditoria/' . (int) $r['id']) ?>" title="Auditoría">
                                                <span class="material-symbols-outlined">manage_search</span>
                                            </a>
                                            <button type="button" class="dev-btn-icon" onclick="openEdit(<?= (int) $r['id'] ?>)" title="Editar">
                                                <span class="material-symbols-outlined">edit</span>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php endif; ?>

                    <?php if (($paginas ?? 1) > 1): ?>
                        <div class="att-pager">
                            <?php if ($pagina > 1): ?>
                                <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $pagina - 1]))) ?>">&laquo;</a>
                            <?php endif; ?>
                            <?php
                                $desde = max(1, $pagina - 3);
                                $hastaPag = min($paginas, $pagina + 3);
                                if ($desde > 1): ?>
                                    <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => 1]))) ?>">1</a>
                                    <?php if ($desde > 2): ?><span class="att-page-info">…</span><?php endif; ?>
                                <?php endif;
                                for ($n = $desde; $n <= $hastaPag; $n++): ?>
                                    <?php if ($n === $pagina): ?>
                                        <span class="att-page on"><?= $n ?></span>
                                    <?php else: ?>
                                        <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $n]))) ?>"><?= $n ?></a>
                                    <?php endif; ?>
                                <?php endfor;
                                if ($hastaPag < $paginas): ?>
                                    <?php if ($hastaPag < $paginas - 1): ?><span class="att-page-info">…</span><?php endif; ?>
                                    <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $paginas]))) ?>"><?= $paginas ?></a>
                                <?php endif; ?>
                            <?php if ($pagina < $paginas): ?>
                                <a class="att-page" href="?<?= esc(http_build_query(array_merge($pag_qs ?? [], ['page' => $pagina + 1]))) ?>">&raquo;</a>
                            <?php endif; ?>
                            <span class="att-page-info">
                                Página <?= $pagina ?> de <?= $paginas ?> · <?= number_format((int) ($totalFilas ?? 0)) ?> registros
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div class="dev-modal-overlay" id="attDetailOverlay" onclick="if(event.target===this)closeDetail()">
        <div class="dev-modal" style="max-width:460px;">
            <div class="dev-modal-head">
                <h3><span class="material-symbols-outlined">fact_check</span> <span id="attDetailName">-</span></h3>
                <button type="button" class="dev-btn-icon" onclick="closeDetail()"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="dev-modal-body">
                <div class="detail-who">
                    <div class="detail-avatar" id="attDetailAvatar">E</div>
                    <div class="detail-sub" id="attDetailDni">-</div>
                </div>
                <div class="detail-grid">
                    <div class="detail-field">
                        <div class="detail-label">Fecha</div>
                        <div class="detail-value" id="attDetailDate">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Estado</div>
                        <div class="detail-value" id="attDetailStatus">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Hora entrada</div>
                        <div class="detail-value" id="attDetailTimeIn">-</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Hora salida</div>
                        <div class="detail-value" id="attDetailTimeOut">-</div>
                    </div>
                    <div class="detail-field" style="grid-column:span 2;">
                        <div class="detail-label">Observación</div>
                        <div class="detail-value" id="attDetailObs">-</div>
                    </div>

                    <!-- Mapa de ubicación -->
                    <div class="detail-field" style="grid-column:span 2;">
                        <div class="detail-label">Ubicación</div>
                        <div class="detail-value" style="padding:0; min-height:200px;">
                            <div id="mapContainer" style="width:100%; height:0; border-radius:12px; overflow:hidden; background:var(--g-surface-variant); display:none;"></div>
                            <div id="mapMsg" style="text-align:center; padding:40px; color:var(--g-text-secondary); font-size:0.9rem;">
                                <span class="material-symbols-outlined" style="font-size:2rem; display:block; margin-bottom:8px;">location_off</span>
                                <span>Esta marcación no tiene ubicación registrada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn dev-btn-text" onclick="closeDetail()">Cerrar</button>
                <a class="dev-btn dev-btn-outline" id="attDetailAuditBtn" href="<?= site_url('admin/asistencias') ?>" style="text-decoration:none;">
                    <span class="material-symbols-outlined">manage_search</span> Auditoría
                </a>
                <button type="button" class="dev-btn dev-btn-primary" id="attDetailEditBtn">
                    <span class="material-symbols-outlined">edit</span> Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="dev-modal-overlay" id="editModal">
        <div class="dev-modal">
            <div class="dev-modal-head">
                <h2><span class="material-symbols-outlined">edit</span> Editar asistencia</h2>
                <button type="button" class="dev-modal-close" onclick="closeEdit()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="post" action="<?= site_url('admin/update-attendance') ?>">
                <?= csrf_field() ?>
                <div class="dev-modal-body">
                    <input type="hidden" name="att_id" id="modal_att_id">
                    <div class="dev-form-grid">
                        <div class="dev-field">
                            <label for="modal_time_in">Hora entrada</label>
                            <input type="time" name="time_in" id="modal_time_in" required>
                        </div>
                        <div class="dev-field">
                            <label for="modal_time_out">Hora salida</label>
                            <input type="time" name="time_out" id="modal_time_out">
                        </div>
                        <div class="dev-field">
                            <label for="modal_status">Estado</label>
                            <select name="status" id="modal_status" required>
                                <option value="present">Presente</option>
                                <option value="late">Tardanza</option>
                                <option value="absent">Falta</option>
                                <option value="no_exit">Sin salida</option>
                            </select>
                        </div>
                        <div class="dev-field">
                            <label for="modal_observacion">Observación</label>
                            <input type="text" name="observacion" id="modal_observacion" placeholder="Nota opcional...">
                        </div>
                        <div class="dev-field" style="grid-column:1 / -1;">
                            <label for="modal_motivo">Motivo del cambio <span style="color:var(--g-text-secondary);font-weight:400;">(queda registrado en la auditoría)</span></label>
                            <input type="text" name="motivo" id="modal_motivo" maxlength="255" placeholder="Ej. El empleado marcó tarde por motivo personal">
                        </div>
                    </div>
                </div>
                <div class="dev-modal-footer">
                    <button type="button" class="dev-btn dev-btn-text" onclick="closeEdit()">Cancelar</button>
                    <button type="submit" class="dev-btn dev-btn-primary">
                        <span class="material-symbols-outlined">save</span> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    var attRows = <?php $attRowsMap = [];
        foreach ($attendance as $r) {
            $attRowsMap[(int) $r['id']] = [
                'id' => (int) $r['id'], 'name' => $r['name'] ?? 'Empleado', 'dni' => $r['dni'] ?? '',
                'date' => $r['date'] ?? '', 'time_in' => $r['time_in'] ?? '', 'time_out' => $r['time_out'] ?? '',
                'status' => $r['status'] ?? '', 'observacion' => $r['observacion'] ?? '',
                'lat' => $r['lat'] ?? null, 'lng' => $r['lng'] ?? null,
            ];
        }
        echo json_encode($attRowsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    var statusMeta = {
        present: {label: 'Presente',  color: '#16a34a', bg: '#22c55e'},
        late:    {label: 'Tardanza',  color: '#d97706', bg: '#f59e0b'},
        absent:  {label: 'Falta',     color: '#64748b', bg: '#94a3b8'},
        no_exit: {label: 'Sin salida',color: '#2563eb', bg: '#3b82f6'}
    };

    function openDetail(id) {
        var r = attRows[id];
        if (!r) return;
        var m = statusMeta[r.status] || statusMeta.absent;
        document.getElementById('attDetailAvatar').textContent = (r.name || 'E').charAt(0).toUpperCase();
        document.getElementById('attDetailAvatar').style.background = m.bg;
        document.getElementById('attDetailName').textContent = r.name;
        document.getElementById('attDetailDni').textContent = 'DNI ' + (r.dni || '---');
        document.getElementById('attDetailDate').textContent = r.date || '-';
        document.getElementById('attDetailTimeIn').textContent = r.time_in || '-';
        document.getElementById('attDetailTimeOut').textContent = r.time_out || '-';
        var st = document.getElementById('attDetailStatus');
        st.textContent = m.label;
        st.style.color = m.color;
        document.getElementById('attDetailObs').textContent = r.observacion || 'Sin observación';
        document.getElementById('attDetailAuditBtn').href = '<?= site_url('admin/auditoria/') ?>' + r.id;
        document.getElementById('attDetailEditBtn').onclick = function () { closeDetail(); openEdit(r.id); };

        // Mapa
        var mapContainer = document.getElementById('mapContainer');
        var mapMsg = document.getElementById('mapMsg');
        var lat = parseFloat(r.lat);
        var lng = parseFloat(r.lng);
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            // Mostrar mapa
            document.getElementById('mapMsg').style.display = 'none';
            mapContainer.style.display = 'block';
            // Inicializar mapa después de que el modal sea visible
            setTimeout(function() {
                if (!document.getElementById('mapContainer')._leaflet_id) {
                    var map = L.map('mapContainer').setView([parseFloat(r.lat), parseFloat(r.lng)], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    L.marker([parseFloat(r.lat), parseFloat(r.lng)]).addTo(map)
                        .bindPopup('Ubicación de la marcación').openPopup();
                    document.getElementById('mapContainer')._leaflet_map = map;
                    setTimeout(function() { map.invalidateSize(); }, 100);
                }
            }, 100);
        } else {
            // No hay ubicación
            document.getElementById('mapMsg').style.display = 'block';
            mapContainer.style.display = 'none';
        }

        document.getElementById('attDetailOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var mapCache = {};
        function openMap(lat, lng) {
            if (!lat || !lng || lat === '0' || lng === '0') {
                alert('Esta asistencia no tiene ubicación registrada');
                return;
            }
            var mapDiv = document.getElementById('mapContainer');
            if (mapDiv._leaflet_id) {
                mapDiv._leaflet_map.remove();
                mapDiv._leaflet_map = null;
            }
            mapDiv.style.height = '300px';
            var map = L.map('mapContainer').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map)
                .bindPopup('Ubicación de la marcación').openPopup();
            mapDiv._leaflet_map = map;
            // Force map to recalculate size after modal is visible
            setTimeout(function() { map.invalidateSize(); }, 100);
        }

        function closeDetail() {
            document.getElementById('attDetailOverlay').classList.remove('active');
            document.body.style.overflow = '';
            // Clean up map if open
            var mapDiv = document.getElementById('mapContainer');
            if (mapDiv && mapDiv._leaflet_map) {
                mapDiv._leaflet_map.remove();
                mapDiv._leaflet_map = null;
            }
        }

    function openEdit(id) {
        var r = attRows[id];
        if (!r) return;
        document.getElementById('modal_att_id').value = r.id;
        document.getElementById('modal_time_in').value = r.time_in || '';
        document.getElementById('modal_time_out').value = r.time_out || '';
        document.getElementById('modal_status').value = r.status || 'absent';
        document.getElementById('modal_observacion').value = r.observacion || '';
        document.getElementById('modal_motivo').value = '';
        document.getElementById('editModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeEdit() {
        document.getElementById('editModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('editModal').addEventListener('click', function (e) { if (e.target === this) closeEdit(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeEdit(); closeDetail(); } });

    function estado(valor) {
        document.getElementById('estado').value = valor;
        document.getElementById('filtro-form').submit();
    }

    function rango(tipo) {
        var form = document.getElementById('filtro-form');
        var todo = form.querySelector('input[name="todo"]');
        if (!todo) {
            todo = document.createElement('input');
            todo.type = 'hidden';
            todo.name = 'todo';
            todo.value = '0';
            form.appendChild(todo);
        }
        todo.value = tipo === 'todo' ? '1' : '0';
        var fi = document.getElementById('fecha_inicio');
        var ff = document.getElementById('fecha_fin');
        var d = new Date();
        function fmt(dt) { return dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0'); }
        if (tipo === 'hoy') { fi.value = fmt(d); ff.value = fmt(d); }
        else if (tipo === 'semana') {
            var day = (d.getDay() + 6) % 7;
            var monday = new Date(d); monday.setDate(d.getDate() - day);
            fi.value = fmt(monday); ff.value = fmt(d);
        } else if (tipo === 'mes') {
            fi.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-01';
            ff.value = fmt(d);
        } else { fi.value = ''; ff.value = ''; }
        form.submit();
    }

    // ════════════════════════════════════════════
    // TIEMPO REAL - Helpers para SSE
    // ════════════════════════════════════════════
    // Celdas de la tabla (mismo HTML que la fila del servidor)
    function attEsc(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }
    function attHora(v) { return v ? attEsc(String(v).slice(0, 5)) : '<span class="tk-cell-muted">—</span>'; }
    function attFecha(v) {
        if (!v) return '—';
        var p = String(v).split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : attEsc(v);
    }
    function attCeldas(datos) {
        var st = datos.status || 'absent';
        var m = window.statusMeta ? window.statusMeta[st] : null;
        var label = m ? m.label : st;
        var nombre = datos.name || 'Empleado';
        return '<td><div class="tk-cell-user"><span class="tk-cell-av">' + attEsc(nombre.charAt(0).toUpperCase()) + '</span>' +
                '<span><span class="tk-cell-main">' + attEsc(nombre) + '</span><br><span class="tk-cell-sub tk-cell-mono">DNI ' + attEsc(datos.dni || '—') + '</span></span></div></td>' +
            '<td class="tk-cell-mono">' + attFecha(datos.date) + '</td>' +
            '<td class="tk-cell-mono">' + attHora(datos.time_in) + '</td>' +
            '<td class="tk-cell-mono">' + attHora(datos.time_out) + '</td>' +
            '<td><span class="att-estado ' + attEsc(st) + '"><i></i>' + attEsc(label) + '</span></td>' +
            '<td class="tk-r" onclick="event.stopPropagation()"><span class="tk-actions">' +
                '<a class="dev-btn-icon" href="' + window.location.origin + '/admin/auditoria/' + parseInt(datos.id, 10) + '" title="Auditoría"><span class="material-symbols-outlined">manage_search</span></a>' +
                '<button type="button" class="dev-btn-icon" onclick="openEdit(' + parseInt(datos.id, 10) + ')" title="Editar"><span class="material-symbols-outlined">edit</span></button>' +
            '</span></td>';
    }

    window.agregarFilaAsistencia = function(datos) {
        // Insertar nueva fila al principio de la tabla
        var tbody = document.querySelector('.tk-tbl tbody');
        if (!tbody) return;
        var tr = document.createElement('tr');
        tr.className = 'att-row';
        tr.setAttribute('data-id', parseInt(datos.id, 10));
        tr.onclick = function() { openDetail(datos.id); };
        tr.innerHTML = attCeldas(datos);
        tbody.insertBefore(tr, tbody.firstChild);
    };

    window.actualizarFilaAsistencia = function(datos) {
        var tr = document.querySelector('.att-row[data-id="' + parseInt(datos.id, 10) + '"]');
        if (!tr) return;
        tr.innerHTML = attCeldas(datos);
    };
    </script>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
</html>