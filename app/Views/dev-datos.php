<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de datos · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=' . filemtime(FCPATH . 'css/index/components/dev-table.css')) ?>">
    <style>
        .page-content { max-width: 960px; }

        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
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
        .si-amber { background: var(--g-warning-light); color: #e37400; }
        .si-green { background: var(--g-success-light); color: var(--g-success); }
        .si-accent { background: var(--g-primary-light); color: var(--g-primary); }
        .si-blue { background: #e3f0ff; color: #1b6ac9; }
        .si-red { background: var(--g-error-light); color: var(--g-error); }
        .si-gray { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .stat-label { font-size: 12px; color: var(--g-text-secondary); }
        .stat-value { font-size: 24px; font-weight: 600; font-family: var(--g-font-display); color: var(--g-text); letter-spacing: -0.02em; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .card {
            background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 16px;
            overflow: hidden; margin-bottom: 20px;
        }
        .card-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; }
        .card-head h2 {
            font-size: 16px; font-weight: 500; font-family: var(--g-font-display);
            display: flex; align-items: center; gap: 10px;
        }
        .card-head h2 .material-symbols-outlined { font-size: 20px; color: var(--g-text-secondary); }
        .card-head .card-sub { font-size: 12px; color: var(--g-text-secondary); }
        .card-body { padding: 8px 20px 16px; }

        .kv-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--g-border); }
        .kv-row:last-child { border-bottom: none; }
        .kv-label { font-size: 13px; min-width: 130px; color: var(--g-text); }
        .kv-bar { flex: 1; height: 8px; background: var(--g-surface-variant); border-radius: 4px; overflow: hidden; min-width: 40px; }
        .kv-bar i { display: block; height: 100%; background: var(--g-primary); border-radius: 4px; min-width: 2px; }
        .kv-count { font-size: 13px; font-weight: 600; min-width: 52px; text-align: right; font-family: var(--g-font-display); }
        .kv-pct { font-size: 11px; color: var(--g-text-secondary); min-width: 38px; text-align: right; }

        .db-table { width: 100%; border-collapse: collapse; }
        .db-table th {
            text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.06em; color: var(--g-text-secondary);
            padding: 10px 12px; border-bottom: 1px solid var(--g-border);
        }
        .db-table td { font-size: 13px; padding: 9px 12px; border-bottom: 1px solid var(--g-border); color: var(--g-text); }
        .db-table tr:last-child td { border-bottom: none; }
        .db-table tr:hover td { background: var(--g-surface-variant); }
        .db-table .num { text-align: right; font-variant-numeric: tabular-nums; }
        .db-table .tbl-name { font-family: 'SF Mono', Monaco, monospace; font-size: 12.5px; }
        .db-table tfoot td { font-weight: 700; background: var(--g-surface-variant); border-top: 2px solid var(--g-border); }

        .badge-soft {
            display: inline-flex; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 500;
            background: var(--g-primary-light); color: var(--g-primary);
        }

        .table-scroll { overflow-x: auto; }
        .note { font-size: 12px; color: var(--g-text-secondary); padding: 0 20px 16px; }

        /* ── Todas las tablas: grupos, tarjetas y modal ── */
        .dbx-split { padding: 0 20px 14px; }
        .dbx-bar { display: flex; gap: 3px; height: 14px; border-radius: 8px; overflow: hidden; }
        .dbx-seg { border: 0; padding: 0; background: var(--c); cursor: pointer; transition: filter .2s, transform .2s; min-width: 6px; }
        .dbx-seg:hover { filter: brightness(1.12); }
        .dbx-bar.has-pick .dbx-seg:not(.on) { opacity: .25; }
        .dbx-legend { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .dbx-chip {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px 6px 8px; border-radius: 999px; cursor: pointer;
            border: 1px solid var(--g-border); background: var(--g-surface); color: var(--g-text); font: 500 12px var(--g-font);
            transition: border-color .2s, background .2s;
        }
        .dbx-chip .material-symbols-outlined { font-size: 16px; color: var(--c); }
        .dbx-chip-n { color: var(--g-text-secondary); font-weight: 400; }
        .dbx-chip:hover { border-color: var(--c); }
        .dbx-chip.on { background: color-mix(in srgb, var(--c) 12%, var(--g-surface)); border-color: var(--c); }

        .dbx-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 0 20px 14px; }
        .dbx-search {
            flex: 1; min-width: 180px; display: flex; align-items: center; gap: 8px; height: 40px; padding: 0 12px;
            border: 1px solid var(--g-border); border-radius: 12px; background: var(--g-surface);
        }
        .dbx-search:focus-within { border-color: var(--g-primary); }
        .dbx-search .material-symbols-outlined { font-size: 20px; color: var(--g-text-secondary); }
        .dbx-search input { flex: 1; border: 0; outline: 0; background: transparent; color: var(--g-text); font: 14px var(--g-font); }
        .dbx-seg-ctl { display: inline-flex; gap: 4px; padding: 4px; border-radius: 9999px; background: var(--sb-surface-container, var(--g-surface-variant)); }
        .dbx-seg-ctl button { height: 32px; border: 0; background: transparent; color: var(--sb-on-surface-variant, var(--g-text-secondary)); padding: 0 14px;
            border-radius: 9999px; font: 500 12px var(--tk-font, var(--g-font)); cursor: pointer; transition: var(--tk-breathe, background-color 125ms); }
        .dbx-seg-ctl button.on { background: var(--sb-primary, var(--g-primary)); color: var(--sb-on-primary, #fff); font-weight: 600;
            box-shadow: 0 2px 8px -2px color-mix(in oklab, var(--sb-primary, var(--g-primary)) 45%, transparent); }
        .dbx-check { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--g-text-secondary); cursor: pointer; }
        .dbx-check input { accent-color: var(--g-primary); }

        .dbx-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; padding: 0 20px 16px; }
        .dbx-card {
            position: relative; display: flex; flex-direction: column; gap: 6px; text-align: left; cursor: pointer;
            padding: 12px 14px 14px; border-radius: 14px; border: 1px solid var(--g-border); background: var(--g-surface); color: var(--g-text);
            transition: transform .18s ease, border-color .18s, box-shadow .18s; animation: dbx-in .35s ease both;
        }
        .dbx-card:hover { transform: translateY(-2px); border-color: var(--c); box-shadow: 0 8px 20px -12px color-mix(in srgb, var(--c) 60%, transparent); }
        .dbx-card:focus-visible { outline: 2px solid var(--c); outline-offset: 2px; }
        .dbx-card.is-empty { opacity: .62; }
        .dbx-card-top { display: flex; align-items: center; justify-content: space-between; }
        .dbx-ico { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; font-size: 18px;
            color: var(--c); background: color-mix(in srgb, var(--c) 13%, var(--g-surface)); }
        .dbx-pct { font: 600 12px var(--g-font-display); color: var(--g-text-secondary); }
        .dbx-name { font: 500 13px 'SF Mono', Monaco, Consolas, monospace; word-break: break-all; }
        .dbx-meta { display: flex; justify-content: space-between; font-size: 12px; color: var(--g-text-secondary); }
        .dbx-meta b { color: var(--g-text); font-family: var(--g-font-display); }
        .dbx-meter { height: 5px; border-radius: 4px; background: var(--g-surface-variant); overflow: hidden; }
        .dbx-meter i { display: block; height: 100%; border-radius: 4px; background: var(--c); }
        .dbx-tag { position: absolute; top: 12px; right: 44px; font-size: 10px; padding: 1px 7px; border-radius: 999px; background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .dbx-none { text-align: center; color: var(--g-text-secondary); font-size: 13px; padding: 12px 0 20px; }
        @keyframes dbx-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

        .dbx-modal { max-width: 760px; width: 94%; display: flex; flex-direction: column; overflow: hidden; }
        .dbx-mhead h3 { font-size: 20px; }
        .dbx-mhead h3 small { display: block; font-size: 12px; color: var(--g-text-secondary); font-family: var(--g-font); margin-top: 2px; }
        .dbx-mhead #dbxTitle { font-family: 'SF Mono', Monaco, Consolas, monospace; font-size: 18px; }
        .dbx-mico { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
            color: var(--c) !important; background: color-mix(in srgb, var(--c) 13%, var(--g-surface)); }
        /* Pestañas del modal: control segmentado de System-Trackin (cápsula, activa en verde) */
        .dbx-tabs { display: flex; gap: 4px; margin: 0 24px 4px; padding: 4px; width: max-content; max-width: calc(100% - 48px);
            border-radius: 9999px; background: var(--sb-surface-container, var(--g-surface-variant)); overflow-x: auto; scrollbar-width: none; }
        .dbx-tabs::-webkit-scrollbar { display: none; }
        .dbx-tabs button { display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 14px; border: 0; border-radius: 9999px;
            background: transparent; color: var(--sb-on-surface-variant, var(--g-text-secondary)); font: 500 12px var(--tk-font, var(--g-font));
            cursor: pointer; white-space: nowrap; transition: var(--tk-breathe, background-color 125ms, color 125ms); }
        .dbx-tabs button:hover { color: var(--sb-on-surface, var(--g-text)); }
        .dbx-tabs button.on { background: var(--sb-primary, var(--g-primary)); color: var(--sb-on-primary, #fff); font-weight: 600;
            box-shadow: 0 2px 8px -2px color-mix(in oklab, var(--sb-primary, var(--g-primary)) 45%, transparent); }
        .dbx-tabs button span:not(:empty) { min-width: 18px; padding: 0 6px; border-radius: 9999px; font-size: 10px; line-height: 18px; text-align: center;
            background: color-mix(in srgb, currentColor 14%, transparent); }
        .dbx-mbody { padding-top: 14px; overflow-y: auto; max-height: 60vh; }
        .dbx-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .dbx-kpi { padding: 10px 12px; border-radius: 12px; background: var(--g-surface-variant); }
        .dbx-kpi span { display: block; font-size: 11px; color: var(--g-text-secondary); }
        .dbx-kpi b { font: 600 18px var(--g-font-display); color: var(--g-text); }
        .dbx-split2 { margin: 16px 0 6px; font-size: 12px; color: var(--g-text-secondary); display: flex; justify-content: space-between; }
        .dbx-bar2 { display: flex; height: 10px; border-radius: 6px; overflow: hidden; background: var(--g-surface-variant); }
        .dbx-bar2 i:first-child { background: var(--c); } .dbx-bar2 i:last-child { background: color-mix(in srgb, var(--c) 40%, var(--g-surface)); }
        .dbx-facts { margin-top: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
        .dbx-fact { display: flex; justify-content: space-between; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--g-border); font-size: 13px; }
        .dbx-fact span { color: var(--g-text-secondary); }
        .dbx-rel { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
        .dbx-rel a, .dbx-rel button { font-size: 12px; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--g-border); background: var(--g-surface); color: var(--g-text); cursor: pointer; }
        .dbx-rel button:hover { border-color: var(--g-primary); }
        .dbx-list { display: flex; flex-direction: column; }
        .dbx-li { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--g-border); font-size: 13px; }
        .dbx-li code { font: 500 12.5px 'SF Mono', Monaco, Consolas, monospace; color: var(--g-text); min-width: 0; flex: 1; word-break: break-all; }
        .dbx-type { font: 12px 'SF Mono', Monaco, Consolas, monospace; color: var(--g-text-secondary); }
        .dbx-k { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 999px; letter-spacing: .03em; }
        .dbx-k.pri { background: var(--g-warning-light); color: #b06000; }
        .dbx-k.uni { background: #e3f0ff; color: #1b6ac9; }
        .dbx-k.mul { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .dbx-k.nul { background: transparent; color: var(--g-text-disabled); border: 1px dashed var(--g-border); }
        .dbx-rows { overflow-x: auto; border: 1px solid var(--g-border); border-radius: 12px; }
        .dbx-rows table { border-collapse: collapse; width: 100%; font-size: 12px; }
        .dbx-rows th { position: sticky; top: 0; background: var(--g-surface-variant); text-align: left; padding: 8px 10px; font: 600 11px var(--g-font); color: var(--g-text-secondary); white-space: nowrap; }
        .dbx-rows td { padding: 7px 10px; border-top: 1px solid var(--g-border); white-space: nowrap; color: var(--g-text); font-family: 'SF Mono', Monaco, Consolas, monospace; }
        .dbx-rows td.nil { color: var(--g-text-disabled); font-style: italic; }
        .dbx-empty { text-align: center; padding: 28px 10px; color: var(--g-text-secondary); font-size: 13px; }
        .dbx-empty .material-symbols-outlined { display: block; font-size: 36px; margin-bottom: 6px; color: var(--g-text-disabled); }
        .dbx-loading { display: flex; justify-content: center; gap: 6px; padding: 40px 0; }
        .dbx-loading i { width: 8px; height: 8px; border-radius: 50%; background: var(--c, var(--g-primary)); animation: dbx-dot 1s ease-in-out infinite; }
        .dbx-loading i:nth-child(2) { animation-delay: .15s; } .dbx-loading i:nth-child(3) { animation-delay: .3s; }
        @keyframes dbx-dot { 0%, 100% { opacity: .25; transform: scale(.8); } 50% { opacity: 1; transform: scale(1); } }
        @media (max-width: 640px) {
            .dbx-grid { grid-template-columns: 1fr 1fr; padding: 0 12px 12px; gap: 8px; }
            .dbx-split, .dbx-tools { padding-left: 12px; padding-right: 12px; }
            .dbx-kpis { grid-template-columns: 1fr 1fr; }
            .dbx-facts { grid-template-columns: 1fr; }
            .dev-modal-overlay .dbx-modal { width: 100%; max-height: 92vh; border-radius: 24px 24px 0 0; align-self: flex-end; }
            .dbx-tabs { margin: 0 16px 4px; width: auto; max-width: none; }
            .dbx-tabs button { flex: 1; justify-content: center; padding: 0 6px; font-size: 11.5px; }
            .dbx-tabs button span { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { .dbx-card { animation: none; transition: none; } }

        /* ── Resumen: anillos y filas que se abren ── */
        .sx-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .sx-card { margin-bottom: 0; }
        .sx-body { display: flex; align-items: center; gap: 20px; padding: 4px 20px 18px; }
        .sx-ring { --ring: none; position: relative; flex: none; width: 116px; height: 116px; border-radius: 50%; background: var(--ring);
            animation: sx-spin .8s cubic-bezier(.22,1,.36,1) both; }
        .sx-ring::after { content: ''; position: absolute; inset: 14px; border-radius: 50%; background: var(--g-surface); }
        .sx-ring span { position: absolute; inset: 0; z-index: 1; display: grid; place-content: center; text-align: center; }
        .sx-ring b { font: 600 26px var(--g-font-display); color: var(--g-text); letter-spacing: -.02em; line-height: 1; }
        .sx-ring small { font-size: 11px; color: var(--g-text-secondary); }
        @keyframes sx-spin { from { opacity: 0; transform: rotate(-90deg) scale(.85); } to { opacity: 1; transform: none; } }
        .sx-rows { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
        .sx-row { display: grid; grid-template-columns: 10px 1fr auto 38px 18px; grid-template-rows: auto 4px; align-items: center; column-gap: 10px; row-gap: 5px;
            width: 100%; padding: 7px 8px; border: 0; border-radius: 12px; background: transparent; color: var(--g-text); text-align: left; cursor: pointer;
            transition: background .15s ease, transform .15s ease; }
        .sx-row:hover:not(:disabled) { background: color-mix(in srgb, var(--c) 9%, transparent); }
        .sx-row:hover:not(:disabled) .sx-go { opacity: 1; transform: translateX(2px); }
        .sx-row:disabled { cursor: default; }
        .sx-row.is-zero { opacity: .5; }
        .sx-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--c); }
        .sx-lbl { font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .sx-n { font: 600 14px var(--g-font-display); }
        .sx-p { font-size: 11px; color: var(--g-text-secondary); text-align: right; }
        .sx-go { font-size: 18px; color: var(--g-text-secondary); opacity: 0; transition: opacity .15s, transform .15s; }
        .sx-meter { grid-column: 2 / 5; height: 4px; border-radius: 4px; background: var(--g-surface-variant); overflow: hidden; }
        .sx-meter i { display: block; height: 100%; border-radius: 4px; background: var(--c); animation: sx-grow .7s cubic-bezier(.22,1,.36,1) both; transform-origin: left; }
        @keyframes sx-grow { from { transform: scaleX(0); } to { transform: none; } }
        .sx-tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .sx-tile { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 16px; border: 1px solid var(--g-border);
            background: var(--g-surface); color: var(--g-text); text-align: left; cursor: pointer; transition: transform .18s, border-color .18s, box-shadow .18s; }
        .sx-tile:hover:not(:disabled) { transform: translateY(-2px); border-color: var(--c); box-shadow: 0 8px 20px -12px color-mix(in srgb, var(--c) 60%, transparent); }
        .sx-tile:disabled { cursor: default; opacity: .6; }
        .sx-tile-ico { width: 40px; height: 40px; flex: none; border-radius: 12px; display: grid; place-items: center; font-size: 22px;
            color: var(--c); background: color-mix(in srgb, var(--c) 13%, var(--g-surface)); }
        .sx-tile-txt { flex: 1; min-width: 0; display: flex; flex-direction: column; font-size: 13px; font-weight: 500; }
        .sx-tile-txt small { font-size: 11px; font-weight: 400; color: var(--g-text-secondary); }
        .sx-tile b { font: 600 24px var(--g-font-display); letter-spacing: -.02em; }
        .sx-mtitle { font-family: var(--g-font-display) !important; font-size: 20px !important; }
        .sx-mtools { padding: 0 24px 6px; }
        .sx-item { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid var(--g-border); animation: dbx-in .3s ease both; }
        .sx-item:last-child { border-bottom: 0; }
        .sx-av { width: 34px; height: 34px; flex: none; border-radius: 50%; display: grid; place-items: center; font: 600 12px var(--g-font-display);
            color: var(--c); background: color-mix(in srgb, var(--c) 13%, var(--g-surface)); }
        .sx-item-main { flex: 1; min-width: 0; }
        .sx-item-main b { display: block; font-size: 13.5px; font-weight: 500; color: var(--g-text); }
        .sx-item-main span { display: block; font-size: 12px; color: var(--g-text-secondary); }
        .sx-item-main em { display: block; margin-top: 3px; font-style: normal; font-size: 12px; color: var(--g-text); opacity: .8; }
        .sx-date { flex: none; font-size: 11px; color: var(--g-text-secondary); white-space: nowrap; }
        .sx-more { text-align: center; font-size: 12px; color: var(--g-text-secondary); padding: 10px 0 4px; }
        @media (max-width: 900px) { .sx-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) {
            .sx-body { flex-direction: column; align-items: stretch; padding: 0 12px 14px; }
            .sx-ring { align-self: center; }
            .sx-tiles { grid-template-columns: 1fr; }
            .sx-mtools { padding: 0 16px 6px; }
        }
        @media (prefers-reduced-motion: reduce) { .sx-ring, .sx-meter i, .sx-item { animation: none; } }

        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr 1fr; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?php $activePage = 'datos'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1><strong>Base de datos</strong></h1>
                    <p>Resumen general de todo lo acumulado en el sistema.</p>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php
            $fmtBytes = function (int $b): string {
                if ($b >= 1048576) return number_format($b / 1048576, 2, ',', '.') . ' MB';
                if ($b >= 1024) return number_format($b / 1024, 1, ',', '.') . ' KB';
                return $b . ' B';
            };
            $pct = function (int $n, int $t): int { return $t > 0 ? (int) round($n * 100 / $t) : 0; };

            $rolLabels = ['Dev' => 'Desarrollador', 'Admin' => 'Administradores', 'Empleado' => 'Empleados', 'Practicante' => 'Practicantes'];
            $asistLabels = ['present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', 'absent_justified' => 'Falta justificada'];
            $incLabels = ['Pendiente' => 'Pendiente', 'Revisión' => 'En revisión', 'Justificada' => 'Justificada', 'Rechazada' => 'Rechazada', 'Desestimada' => 'Desestimada'];

            $tUs = (int) ($totales['usuarios'] ?? 0);
            $tAs = (int) ($totales['asistencia'] ?? 0);
            $tInc = (int) ($totales['incidencias'] ?? 0);
            $tCod = (int) ($totales['codigos'] ?? 0);
            ?>

            <section class="stats">
                <div class="stat-card">
                    <div class="stat-icon si-accent"><span class="material-symbols-outlined filled">group</span></div>
                    <div>
                        <div class="stat-label">Usuarios</div>
                        <div class="stat-value"><?= number_format($tUs) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><span class="material-symbols-outlined filled">fact_check</span></div>
                    <div>
                        <div class="stat-label">Registros de asistencia</div>
                        <div class="stat-value"><?= number_format($tAs) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-amber"><span class="material-symbols-outlined filled">report</span></div>
                    <div>
                        <div class="stat-label">Incidencias</div>
                        <div class="stat-value"><?= number_format($tInc) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-blue"><span class="material-symbols-outlined filled">vpn_key</span></div>
                    <div>
                        <div class="stat-label">Códigos</div>
                        <div class="stat-value"><?= number_format($tCod) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-red"><span class="material-symbols-outlined filled">history</span></div>
                    <div>
                        <div class="stat-label">Eventos de sesión</div>
                        <div class="stat-value"><?= number_format((int) ($totales['sesiones'] ?? 0)) ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-gray"><span class="material-symbols-outlined filled">hard_drive</span></div>
                    <div>
                        <div class="stat-label">Peso total (índices + datos)</div>
                        <div class="stat-value" style="font-size:20px;"><?= $fmtBytes((int) $bytesTotal) ?></div>
                    </div>
                </div>
            </section>

            <?php
            // Resumen: cada tarjeta con su anillo de colores; cada fila abre sus registros
            $COL = ['#1b7a42', '#e37400', '#d93025', '#1b6ac9', '#7b5cc4', '#9aa0a6'];
            $bloques = [
                ['rol', 'badge', 'Usuarios por rol', 'en total', $tUs, array_values(array_filter(array_merge(
                    array_map(fn ($k, $l, $c) => [$k, $l, (int) ($porRol[$k] ?? 0), $c], array_keys($rolLabels), $rolLabels, ['#7b5cc4', '#1b6ac9', '#1b7a42', '#e37400']),
                    array_map(fn ($k) => [$k, $k !== '' ? $k : '(sin rol)', (int) $porRol[$k], '#9aa0a6'], array_diff(array_keys($porRol), array_keys($rolLabels)))
                )))],
                ['asist', 'fact_check', 'Asistencia por estado', 'registros', $tAs,
                    array_map(fn ($k, $l, $c) => [$k, $l, (int) ($porEstadoAsist[$k] ?? 0), $c], array_keys($asistLabels), $asistLabels, ['#1b7a42', '#e37400', '#d93025', '#7b5cc4', '#1b6ac9'])],
                ['inc', 'report', 'Incidencias por estado', 'en total', $tInc, array_merge(
                    array_map(fn ($k, $l, $c) => [$k, $l, (int) ($porEstadoInc[$k] ?? 0), $c], array_keys($incLabels), $incLabels, ['#e37400', '#1b6ac9', '#1b7a42', '#d93025', '#9aa0a6']),
                    array_map(fn ($k) => [$k, $k !== '' ? $k : '(sin estado)', (int) $porEstadoInc[$k], '#5f6368'], array_diff(array_keys($porEstadoInc), array_keys($incLabels)))
                )],
                ['code', 'key', 'Códigos de registro', 'en total', $tCod, [
                    ['active', 'Activos', (int) ($porEstadoCode['active'] ?? 0), '#1b7a42'],
                    ['used', 'Usados', (int) ($porEstadoCode['used'] ?? 0), '#9aa0a6'],
                ]],
            ];
            $anillo = function (array $items, int $total): string {
                if ($total <= 0) return 'var(--sb-surface-container, var(--g-surface-variant)) 0 100%';
                $acc = 0; $parts = [];
                foreach ($items as $it) {
                    if ($it[2] <= 0) continue;
                    $a = $acc * 100 / $total; $acc += $it[2]; $b = $acc * 100 / $total;
                    $parts[] = "{$it[3]} " . round($a, 2) . "% " . round($b, 2) . "%";
                }
                return implode(', ', $parts);
            };
            ?>
            <div class="sx-grid">
                <?php foreach ($bloques as [$tipo, $ico, $titulo, $unidad, $total, $items]): ?>
                <section class="card sx-card">
                    <div class="card-head">
                        <h2><span class="material-symbols-outlined"><?= $ico ?></span> <?= esc($titulo) ?></h2>
                        <span class="card-sub"><?= number_format($total) ?> <?= $unidad ?></span>
                    </div>
                    <div class="sx-body">
                        <div class="sx-ring" style="--ring: conic-gradient(<?= $anillo($items, $total) ?>)">
                            <span><b><?= number_format($total) ?></b><small><?= $unidad ?></small></span>
                        </div>
                        <div class="sx-rows">
                            <?php foreach ($items as [$k, $lbl, $n, $c]): $p = $pct($n, $total); ?>
                            <button type="button" class="sx-row<?= $n === 0 ? ' is-zero' : '' ?>" style="--c:<?= $c ?>"
                                data-tipo="<?= $tipo ?>" data-v="<?= esc($k, 'attr') ?>" data-label="<?= esc($lbl, 'attr') ?>" data-ico="<?= $ico ?>" data-n="<?= $n ?>" <?= $n === 0 ? 'disabled' : '' ?>>
                                <i class="sx-dot"></i>
                                <span class="sx-lbl"><?= esc($lbl) ?></span>
                                <span class="sx-n"><?= number_format($n) ?></span>
                                <span class="sx-p"><?= $p ?>%</span>
                                <span class="sx-meter"><i style="width:<?= $p ?>%"></i></span>
                                <span class="material-symbols-outlined sx-go">chevron_right</span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>

            <!-- Accesos del navegador: tres cifras que también se abren -->
            <div class="sx-tiles">
                <?php foreach ([
                    ['notif', 'notifications', 'Notificaciones', (int) ($totales['notificaciones'] ?? 0), '#1b6ac9', 'Avisos enviados a los usuarios'],
                    ['passkey', 'fingerprint', 'Passkeys (WebAuthn)', (int) ($totales['passkeys'] ?? 0), '#1b7a42', 'Huellas registradas en dispositivos'],
                    ['push', 'notifications_active', 'Suscripciones push', (int) ($totales['push'] ?? 0), '#e37400', 'Navegadores que reciben avisos'],
                ] as [$tipo, $ico, $lbl, $n, $c, $sub]): ?>
                <button type="button" class="sx-tile<?= $n === 0 ? ' is-zero' : '' ?>" style="--c:<?= $c ?>" data-tipo="<?= $tipo ?>" data-v="" data-label="<?= esc($lbl, 'attr') ?>" data-ico="<?= $ico ?>" data-n="<?= $n ?>" <?= $n === 0 ? 'disabled' : '' ?>>
                    <span class="sx-tile-ico material-symbols-outlined"><?= $ico ?></span>
                    <span class="sx-tile-txt"><span><?= esc($lbl) ?></span><small><?= esc($sub) ?></small></span>
                    <b><?= number_format($n) ?></b>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Modal: registros de una cifra -->
            <div class="dev-modal-overlay" id="sxModal" aria-hidden="true">
                <div class="dev-modal dbx-modal" role="dialog" aria-modal="true" aria-labelledby="sxTitle">
                    <div class="dev-modal-head dbx-mhead">
                        <h3><span class="dbx-mico material-symbols-outlined" id="sxIco">list</span><span><span id="sxTitle" class="sx-mtitle"></span><small id="sxSub"></small></span></h3>
                        <button type="button" class="dev-modal-close" data-close aria-label="Cerrar"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="sx-mtools">
                        <label class="dbx-search"><span class="material-symbols-outlined">search</span><input type="search" id="sxQ" placeholder="Buscar en la lista…" autocomplete="off"></label>
                    </div>
                    <div class="dev-modal-body dbx-mbody" id="sxBody"></div>
                </div>
            </div>

            <?php
            // Cada tabla en su grupo, con su color e ícono
            $grupos = [
                'personal'  => ['Personal',            'groups',          '#1b7a42', ['users', 'usuarios', 'roles', 'personas']],
                'asist'     => ['Asistencia',          'event_available', '#1b6ac9', ['attendance', 'attendance_log', 'incidents', 'schedules', 'permisos', 'festivos', 'calendario_eventos', '_bak_incidents_utc', '_bak_attendance_utc']],
                'seguridad' => ['Seguridad y sesiones', 'shield_lock',    '#d93025', ['session_log', 'webauthn_credentials', 'codes', 'push_subscriptions', 'notifications']],
                'empresa'   => ['Empresa y pagos',     'apartment',       '#e37400', ['empresa', 'empresa_sucursales', 'pagos', 'planes', 'tipo_plan', 'clientes']],
                'catalogo'  => ['Catálogos y sistema', 'menu_book',       '#7b5cc4', []],
            ];
            $grupoDe = function (string $t) use ($grupos): string {
                foreach ($grupos as $k => $g) if (in_array($t, $g[3], true)) return $k;
                return 'catalogo';
            };
            $porGrupo = [];
            foreach ($tablas as $t) {
                $k = $grupoDe($t['nombre']);
                $porGrupo[$k]['bytes'] = ($porGrupo[$k]['bytes'] ?? 0) + (int) $t['bytes'];
                $porGrupo[$k]['filas'] = ($porGrupo[$k]['filas'] ?? 0) + (int) $t['filas'];
                $porGrupo[$k]['n']     = ($porGrupo[$k]['n'] ?? 0) + 1;
            }
            $maxBytes = max(1, ...array_map(fn ($t) => (int) $t['bytes'], $tablas ?: [['bytes' => 1]]));
            $vacias = count(array_filter($tablas, fn ($t) => (int) $t['filas'] === 0));
            ?>
            <div class="card dbx">
                <div class="card-head">
                    <h2><span class="material-symbols-outlined">storage</span> Todas las tablas</h2>
                    <span class="card-sub"><?= count($tablas) ?> tablas · <?= number_format((int) $filasTotal) ?> filas · <?= $fmtBytes((int) $bytesTotal) ?></span>
                </div>

                <!-- Peso por grupo: una barra con un segmento por grupo -->
                <div class="dbx-split">
                    <div class="dbx-bar" role="img" aria-label="Peso de la base por grupo">
                        <?php foreach ($grupos as $k => $g): if (empty($porGrupo[$k])) continue; ?>
                            <button type="button" class="dbx-seg" data-grupo="<?= $k ?>" style="--c:<?= $g[2] ?>;flex:<?= max(1, $porGrupo[$k]['bytes']) ?>"
                                title="<?= esc($g[0]) ?> · <?= $fmtBytes($porGrupo[$k]['bytes']) ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="dbx-legend">
                        <?php foreach ($grupos as $k => $g): if (empty($porGrupo[$k])) continue; ?>
                            <button type="button" class="dbx-chip" data-grupo="<?= $k ?>" style="--c:<?= $g[2] ?>">
                                <span class="material-symbols-outlined"><?= $g[1] ?></span>
                                <span class="dbx-chip-t"><?= esc($g[0]) ?></span>
                                <span class="dbx-chip-n"><?= $porGrupo[$k]['n'] ?> · <?= $fmtBytes($porGrupo[$k]['bytes']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Buscar y ordenar -->
                <div class="dbx-tools">
                    <label class="dbx-search">
                        <span class="material-symbols-outlined">search</span>
                        <input type="search" id="dbxQ" placeholder="Buscar tabla…" autocomplete="off">
                    </label>
                    <div class="dbx-seg-ctl" role="group" aria-label="Ordenar">
                        <button type="button" data-sort="bytes" class="on">Peso</button>
                        <button type="button" data-sort="filas">Filas</button>
                        <button type="button" data-sort="nombre">A–Z</button>
                    </div>
                    <label class="dbx-check"><input type="checkbox" id="dbxEmpty"> Ocultar vacías (<?= $vacias ?>)</label>
                </div>

                <!-- Una tarjeta por tabla: clic para ver el detalle -->
                <div class="dbx-grid" id="dbxGrid">
                    <?php foreach ($tablas as $t): $k = $grupoDe($t['nombre']); $g = $grupos[$k]; $b = (int) $t['bytes']; $f = (int) $t['filas']; ?>
                        <button type="button" class="dbx-card<?= $f === 0 ? ' is-empty' : '' ?>" style="--c:<?= $g[2] ?>"
                            data-t="<?= esc($t['nombre'], 'attr') ?>" data-grupo="<?= $k ?>" data-bytes="<?= $b ?>" data-filas="<?= $f ?>">
                            <span class="dbx-card-top">
                                <span class="dbx-ico material-symbols-outlined"><?= $g[1] ?></span>
                                <span class="dbx-pct"><?= $pct($b, (int) $bytesTotal) ?>%</span>
                            </span>
                            <span class="dbx-name"><?= esc($t['nombre']) ?></span>
                            <span class="dbx-meta">
                                <span><b><?= number_format($f) ?></b> <?= $f === 1 ? 'fila' : 'filas' ?></span>
                                <span><?= $fmtBytes($b) ?></span>
                            </span>
                            <span class="dbx-meter"><i style="width:<?= max(2, round($b * 100 / $maxBytes)) ?>%"></i></span>
                            <?php if ($f === 0): ?><span class="dbx-tag">Vacía</span><?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p class="dbx-none" id="dbxNone" hidden>Ninguna tabla coincide.</p>
                <div class="note">Filas contadas en vivo con COUNT(*); el tamaño usa data + index de information_schema. Toca una tabla para ver sus columnas, índices, relaciones y sus últimas filas.</div>
            </div>

            <!-- Modal: detalle de una tabla -->
            <div class="dev-modal-overlay" id="dbxModal" aria-hidden="true">
                <div class="dev-modal dbx-modal" role="dialog" aria-modal="true" aria-labelledby="dbxTitle">
                    <div class="dev-modal-head dbx-mhead">
                        <h3><span class="dbx-mico material-symbols-outlined" id="dbxIco">table</span><span><span id="dbxTitle">tabla</span><small id="dbxGroup"></small></span></h3>
                        <button type="button" class="dev-modal-close" data-close aria-label="Cerrar"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="dbx-tabs" role="tablist">
                        <button type="button" role="tab" data-tab="res" class="on">Resumen</button>
                        <button type="button" role="tab" data-tab="col">Columnas <span id="dbxNCol"></span></button>
                        <button type="button" role="tab" data-tab="idx">Índices <span id="dbxNIdx"></span></button>
                        <button type="button" role="tab" data-tab="row">Últimas filas</button>
                    </div>
                    <div class="dev-modal-body dbx-mbody" id="dbxBody"><div class="dbx-loading"><i></i><i></i><i></i></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
    const grid = document.getElementById('dbxGrid');
    if (!grid) return;
    const cards = [...grid.querySelectorAll('.dbx-card')];
    const q = document.getElementById('dbxQ'), empty = document.getElementById('dbxEmpty'), none = document.getElementById('dbxNone');
    const bar = document.querySelector('.dbx-bar');
    let grupo = '', sort = 'bytes';
    const GRUPOS = {};
    document.querySelectorAll('.dbx-chip').forEach((c) => { GRUPOS[c.dataset.grupo] = { t: c.querySelector('.dbx-chip-t').textContent, c: c.style.getPropertyValue('--c'), i: c.querySelector('.material-symbols-outlined').textContent }; });

    function apply() {
        const s = q.value.trim().toLowerCase();
        const list = cards.slice().sort((a, b) => sort === 'nombre' ? a.dataset.t.localeCompare(b.dataset.t) : (+b.dataset[sort]) - (+a.dataset[sort]));
        let n = 0;
        list.forEach((c, i) => {
            const ok = (!grupo || c.dataset.grupo === grupo) && (!s || c.dataset.t.toLowerCase().includes(s)) && !(empty.checked && +c.dataset.filas === 0);
            c.hidden = !ok; if (ok) { c.style.animationDelay = Math.min(n, 20) * 18 + 'ms'; n++; }
            grid.appendChild(c);
        });
        none.hidden = n > 0;
        document.querySelectorAll('.dbx-chip, .dbx-seg').forEach((b) => b.classList.toggle('on', b.dataset.grupo === grupo));
        bar.classList.toggle('has-pick', !!grupo);
    }
    document.querySelectorAll('.dbx-chip, .dbx-seg').forEach((b) => b.addEventListener('click', () => { grupo = grupo === b.dataset.grupo ? '' : b.dataset.grupo; apply(); }));
    document.querySelectorAll('.dbx-seg-ctl button').forEach((b) => b.addEventListener('click', () => {
        sort = b.dataset.sort; document.querySelectorAll('.dbx-seg-ctl button').forEach((x) => x.classList.toggle('on', x === b)); apply();
    }));
    q.addEventListener('input', apply); empty.addEventListener('change', apply);

    // ── Modal ──
    const modal = document.getElementById('dbxModal'), body = document.getElementById('dbxBody');
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    const kb = (b) => b >= 1048576 ? (b / 1048576).toFixed(2).replace('.', ',') + ' MB' : b >= 1024 ? (b / 1024).toFixed(1).replace('.', ',') + ' KB' : b + ' B';
    const num = (n) => Number(n || 0).toLocaleString('es-PE');
    const fecha = (d) => d ? new Date(d.replace(' ', 'T')).toLocaleString('es-PE', { dateStyle: 'medium', timeStyle: 'short' }) : '—';
    let data = null, tab = 'res', lastFocus = null;

    function render() {
        document.querySelectorAll('.dbx-tabs button').forEach((b) => b.classList.toggle('on', b.dataset.tab === tab));
        if (!data) return;
        const d = data;
        if (tab === 'res') {
            const tot = (+d.info.data) + (+d.info.idx) || 1;
            const rel = d.relaciones.length
                ? `<div class="dbx-split2"><span>Se relaciona con</span></div><div class="dbx-rel">${d.relaciones.map((r) => `<button type="button" data-open="${esc(r.tabla)}">${esc(r.columna)} → <b>${esc(r.tabla)}</b>.${esc(r.ref)}</button>`).join('')}</div>` : '';
            body.innerHTML = `
                <div class="dbx-kpis">
                    <div class="dbx-kpi"><span>Filas</span><b>${num(d.filas)}</b></div>
                    <div class="dbx-kpi"><span>Peso total</span><b>${kb(tot === 1 ? 0 : tot)}</b></div>
                    <div class="dbx-kpi"><span>Columnas</span><b>${d.columnas.length}</b></div>
                    <div class="dbx-kpi"><span>Índices</span><b>${d.indices.length}</b></div>
                </div>
                <div class="dbx-split2"><span>Datos ${kb(+d.info.data)}</span><span>Índices ${kb(+d.info.idx)}</span></div>
                <div class="dbx-bar2"><i style="width:${(+d.info.data) * 100 / tot}%"></i><i style="width:${(+d.info.idx) * 100 / tot}%"></i></div>
                <div class="dbx-facts">
                    <div class="dbx-fact"><span>Motor</span><b>${esc(d.info.engine || '—')}</b></div>
                    <div class="dbx-fact"><span>Próximo ID</span><b>${d.info.auto_increment ? num(d.info.auto_increment) : '—'}</b></div>
                    <div class="dbx-fact"><span>Creada</span><b>${fecha(d.info.create_time)}</b></div>
                    <div class="dbx-fact"><span>Último cambio</span><b>${fecha(d.info.update_time)}</b></div>
                    <div class="dbx-fact"><span>Cotejamiento</span><b>${esc(d.info.collation || '—')}</b></div>
                    <div class="dbx-fact"><span>Peso por fila</span><b>${d.filas ? kb(Math.round(tot / d.filas)) : '—'}</b></div>
                </div>${rel}`;
        } else if (tab === 'col') {
            body.innerHTML = `<div class="dbx-list">${d.columnas.map((c) => `
                <div class="dbx-li"><code>${esc(c.nombre)}</code>
                    ${c.llave === 'PRI' ? '<span class="dbx-k pri">PK</span>' : c.llave === 'UNI' ? '<span class="dbx-k uni">ÚNICA</span>' : c.llave === 'MUL' ? '<span class="dbx-k mul">ÍNDICE</span>' : ''}
                    ${c.nulo === 'YES' ? '<span class="dbx-k nul">NULL</span>' : ''}
                    <span class="dbx-type">${esc(c.tipo)}${c.extra ? ' · ' + esc(c.extra) : ''}</span></div>`).join('')}</div>`;
        } else if (tab === 'idx') {
            body.innerHTML = d.indices.length ? `<div class="dbx-list">${d.indices.map((i) => `
                <div class="dbx-li"><code>${esc(i.nombre)}</code>${i.nombre === 'PRIMARY' ? '<span class="dbx-k pri">PK</span>' : +i.unico ? '<span class="dbx-k uni">ÚNICO</span>' : ''}
                    <span class="dbx-type">${esc(i.columnas)}</span></div>`).join('')}</div>`
                : '<div class="dbx-empty"><span class="material-symbols-outlined">key_off</span>Sin índices</div>';
        } else {
            if (!d.muestra.length) { body.innerHTML = '<div class="dbx-empty"><span class="material-symbols-outlined">inbox</span>Esta tabla está vacía</div>'; return; }
            const keys = Object.keys(d.muestra[0]);
            body.innerHTML = `<p class="dbx-split2" style="margin-top:0"><span>Las ${d.muestra.length} más recientes · los datos sensibles van ocultos</span></p>
                <div class="dbx-rows"><table><thead><tr>${keys.map((k) => `<th>${esc(k)}</th>`).join('')}</tr></thead>
                <tbody>${d.muestra.map((r) => `<tr>${keys.map((k) => r[k] === null ? '<td class="nil">null</td>' : `<td>${esc(r[k])}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`;
        }
    }

    async function open(t) {
        const card = cards.find((c) => c.dataset.t === t);
        const g = GRUPOS[card?.dataset.grupo] || { t: '', c: 'var(--g-primary)', i: 'table' };
        modal.style.setProperty('--c', g.c);
        document.getElementById('dbxTitle').textContent = t;
        document.getElementById('dbxGroup').textContent = g.t;
        document.getElementById('dbxIco').textContent = g.i;
        document.getElementById('dbxNCol').textContent = ''; document.getElementById('dbxNIdx').textContent = '';
        data = null; tab = 'res'; render();
        body.innerHTML = '<div class="dbx-loading"><i></i><i></i><i></i></div>';
        if (!modal.classList.contains('is-open')) { lastFocus = document.activeElement; modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
        try {
            const r = await fetch('<?= base_url('dios/datos/tabla') ?>?t=' + encodeURIComponent(t), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const j = await r.json();
            if (!j.ok) throw new Error(j.msj || 'Error');
            data = j;
            document.getElementById('dbxNCol').textContent = j.columnas.length;
            document.getElementById('dbxNIdx').textContent = j.indices.length;
            render();
            modal.querySelector('.dev-modal-close').focus();
        } catch (e) {
            body.innerHTML = `<div class="dbx-empty"><span class="material-symbols-outlined">error</span>${esc(e.message)}</div>`;
        }
    }
    function close() {
        modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = '';
        lastFocus && lastFocus.focus();
    }
    cards.forEach((c) => c.addEventListener('click', () => open(c.dataset.t)));
    document.querySelectorAll('.dbx-tabs button').forEach((b) => b.addEventListener('click', () => { tab = b.dataset.tab; render(); }));
    body.addEventListener('click', (e) => { const b = e.target.closest('[data-open]'); if (b) open(b.dataset.open); });
    modal.addEventListener('click', (e) => { if (e.target === modal || e.target.closest('[data-close]')) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('is-open')) close(); });
})();
</script>
<script>
(() => {
    const modal = document.getElementById('sxModal');
    if (!modal) return;
    const body = document.getElementById('sxBody'), q = document.getElementById('sxQ');
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    const ini = (t) => String(t || '?').trim().split(/\s+/).slice(0, 2).map((w) => w[0]).join('').toUpperCase();
    const fecha = (d) => { if (!d) return '—'; const x = new Date(String(d).replace(' ', 'T')); return isNaN(x) ? esc(d) : x.toLocaleString('es-PE', String(d).length > 10 ? { dateStyle: 'medium', timeStyle: 'short' } : { dateStyle: 'medium' }); };
    let rows = [], total = 0, lastFocus = null;

    function draw() {
        const s = q.value.trim().toLowerCase();
        const list = s ? rows.filter((r) => [r.titulo, r.sub, r.extra].join(' ').toLowerCase().includes(s)) : rows;
        if (!list.length) { body.innerHTML = `<div class="dbx-empty"><span class="material-symbols-outlined">search_off</span>${rows.length ? 'Nada coincide con la búsqueda' : 'Sin registros'}</div>`; return; }
        body.innerHTML = list.map((r, i) => `
            <div class="sx-item" style="animation-delay:${Math.min(i, 20) * 15}ms">
                <span class="sx-av">${esc(ini(r.titulo))}</span>
                <span class="sx-item-main"><b>${esc(r.titulo || '—')}</b>${r.sub ? `<span>${esc(r.sub)}</span>` : ''}${r.extra ? `<em>${esc(r.extra)}</em>` : ''}</span>
                <span class="sx-date">${fecha(r.fecha)}</span>
            </div>`).join('') + (total > rows.length ? `<p class="sx-more">Mostrando los ${rows.length} más recientes de ${total.toLocaleString('es-PE')}</p>` : '');
    }

    async function open(btn) {
        const c = btn.style.getPropertyValue('--c') || 'var(--g-primary)';
        modal.style.setProperty('--c', c);
        document.getElementById('sxIco').textContent = btn.dataset.ico;
        document.getElementById('sxTitle').textContent = btn.dataset.label;
        document.getElementById('sxSub').textContent = `${Number(btn.dataset.n).toLocaleString('es-PE')} registro(s)`;
        q.value = ''; rows = [];
        body.innerHTML = '<div class="dbx-loading"><i></i><i></i><i></i></div>';
        lastFocus = document.activeElement;
        modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden';
        try {
            const r = await fetch('<?= base_url('dios/datos/grupo') ?>?' + new URLSearchParams({ tipo: btn.dataset.tipo, v: btn.dataset.v }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const j = await r.json();
            if (!j.ok) throw new Error(j.msj || 'Error');
            rows = j.rows; total = j.total; draw();
            modal.querySelector('.dev-modal-close').focus();
        } catch (e) { body.innerHTML = `<div class="dbx-empty"><span class="material-symbols-outlined">error</span>${esc(e.message)}</div>`; }
    }
    function close() { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; lastFocus && lastFocus.focus(); }
    document.querySelectorAll('.sx-row, .sx-tile').forEach((b) => b.addEventListener('click', () => open(b)));
    q.addEventListener('input', draw);
    modal.addEventListener('click', (e) => { if (e.target === modal || e.target.closest('[data-close]')) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('is-open')) close(); });
})();
</script>
</body>
</html>
