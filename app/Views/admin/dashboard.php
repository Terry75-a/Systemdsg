<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control · DSG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .dev-stats-grid { grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); }
        .dash-greeting { margin-bottom: 24px; }
        .dash-greeting h1 { font-size: 22px; font-weight: 400; font-family: var(--g-font-display); margin: 0; }
        .dash-greeting h1 strong { font-weight: 700; }
        .dash-greeting p { font-size: 13px; color: var(--g-text-secondary); margin: 4px 0 0; }
        .dash-date { display: inline-flex; align-items: center; gap: 6px; background: var(--g-surface-variant); padding: 6px 12px; border-radius: 20px; font-size: 12px; color: var(--g-text-secondary); margin-top: 10px; }
        .dash-date .material-symbols-outlined { font-size: 16px; }

        .dash-section-title { font-size: 14px; font-weight: 600; color: var(--g-text-secondary); margin-bottom: 12px; font-family: var(--g-font-display); }

        /* ── Secciones al estilo sorteroOS ─────────────────────────────────
           sorteroOS NO usa card con borde: el título vive FUERA del bloque,
           apoyado sobre el fondo de página (11px, mayúsculas, tracking amplio,
           muy apagado, mb-4) y debajo va el bloque con hairlines —un contenedor
           rounded-2xl con overflow:hidden cuyo fondo asoma en el gap de 1px—.
           Por eso estas secciones van sin background, sin borde y sin padding:
           el "card" lo forma la propia lista, no un marco alrededor. */
        .dash-sec { background: transparent; border: 0; padding: 0; }
        .dash-sec-head {
            display: flex; align-items: baseline; justify-content: space-between;
            gap: 12px; margin-bottom: 16px;
        }
        .dash-sec-head h2 {
            margin: 0; font-family: inherit;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .dash-sec-head h2 .material-symbols-outlined { font-size: 15px; color: inherit; }
        .dash-sec-link {
            font-size: 11px; font-weight: 500; text-decoration: none;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 60%, transparent);
            display: inline-flex; align-items: center; gap: 1px;
            transition: color 150ms ease;
        }
        .dash-sec-link:hover { color: var(--sb-primary); }
        .dash-sec-link .material-symbols-outlined { font-size: 15px; }

        /* ── Última actividad — réplica del feed "Actividad reciente" de sorteroOS ──
           Fila: caja de icono 32px/r8 (surface-container) con icono semántico al
           50% · título 13px/500 · descripción 12px @50% a una línea con elipsis
           · tiempo 10px @40%. La caja de icono, el hover y el stagger los aporta
           next-panel.css (que ya es un port de ese patrón); acá sólo se ajusta el
           fondo hairline, el padding p-4 y las opacidades.

           Los selectores llevan 3 clases a propósito: next-panel.css define estos
           mismos estilos como `body:has(.dev-main) .clase` (especificidad 0,2,1)
           y hace falta superarlo sin caer en !important.

           Los tokens --sb-* existen en educonecta.css (light) y se redefinen en
           next-panel.css para dark, así que ambos modos quedan cubiertos. */
        .dash-sec.dash-act .dev-recent-list {
            background: color-mix(in srgb, var(--sb-outline-variant) 10%, transparent);
            gap: 1px;
            border-radius: 16px;
            overflow: hidden;
        }
        .dash-sec.dash-act .dev-recent-item {
            background: var(--sb-surface);
            padding: 16px;                 /* p-4 */
            align-items: flex-start;
            gap: 12px;
        }
        .dash-sec.dash-act .dev-recent-item:hover {
            background: var(--sb-surface-container-low);
        }
        /* Icono dentro de la caja: 16px al 50% (su h-4 w-4 /50) */
        .dash-act-ico {
            font-size: 16px;
            line-height: 1;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }
        /* Descripción: una sola línea con puntos suspensivos */
        .dash-act-desc {
            font-size: 12px;
            line-height: 1.45;
            margin-top: 2px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Tiempo: sorteroOS lo deja muy tenue (40%) */
        .dash-act-when {
            font-size: 10px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 40%, transparent);
            white-space: nowrap;
        }
        /* Estado vacío: caja neutra centrada (su emptyState) */
        .dash-act-empty {
            background: var(--sb-surface);
            border-radius: 16px;
            padding: 32px 16px;
            text-align: center;
            font-size: 14px;
            color: color-mix(in srgb, var(--sb-on-surface-variant) 50%, transparent);
        }

        .dash-link-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* ── Módulos — mismo lenguaje que la rejilla de métricas de sorteroOS:
           un único contenedor con gap de 1px y fondo tintado (outline-variant
           al 20%), celdas blancas a ras sin borde ni radio individual. Sobre
           escribe a next-panel.css por la misma razón de especificidad. */
        .dash-sec.dash-mods .dash-link-grid {
            gap: 1px;
            background: color-mix(in srgb, var(--sb-outline-variant) 20%, transparent);
            border-radius: 16px;
            overflow: hidden;
        }
        .dash-sec.dash-mods .dash-link {
            background: var(--sb-surface);
            border: 0;
            border-radius: 0;
            padding: 16px 20px;
            gap: 12px;
        }
        .dash-sec.dash-mods .dash-link:hover {
            background: var(--sb-surface-container-low);
        }

        /* ── Módulos — paleta verde/blanco ──
           Tres intensidades en vez de 5 colores distintos: sólido (verde con
           icono blanco), suave (verde claro con icono verde) y contorno (blanco
           con borde verde). Los tonos viven en vars locales para que el dark
           mode los redefina sin tocar el markup. */
        .dash-mod-icon {
            width: 38px; height: 38px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 1px solid transparent;
            transition: background 150ms ease, color 150ms ease, border-color 150ms ease;
        }
        .dash-mod-icon .material-symbols-outlined { font-size: 19px; }
        /* Sólido: verde pleno, icono blanco.
           Usa --sb-primary, no --g-primary: next-panel.css redefine
           --g-primary a #fafafa en dark mode (el acento oscuro es blanco),
           lo que dejaba el icono blanco sobre fondo blanco. --sb-primary se
           mantiene verde (#1b7a42) en ambos modos. */
        .dash-mod-icon.mi-solid {
            background: var(--sb-primary); color: var(--sb-on-primary, #fff);
            border-color: var(--sb-primary);
        }
        /* Suave: verde claro, icono verde */
        .dash-mod-icon.mi-soft { background: var(--g-primary-light); color: var(--g-primary); border-color: transparent; }
        /* Contorno: blanco, borde e icono verde */
        .dash-mod-icon.mi-outline {
            background: var(--g-surface); color: var(--g-primary);
            border-color: color-mix(in srgb, var(--g-primary) 32%, transparent);
        }
        html.dark:has(.dev-main) .dash-mod-icon.mi-outline {
            background: transparent;
            border-color: color-mix(in srgb, var(--g-primary) 45%, transparent);
        }
        /* El hover de next-panel.css fuerza fondo verde suave con !important;
           lo respetamos y bajamos el borde para que no compita. */
        .dash-link:hover .dash-mod-icon { border-color: transparent; }

        /* Contador de incidencias pendientes — --sb-primary para que siga
           verde en dark mode (ver nota en .mi-solid) */
        .dash-mod-badge {
            margin-left: auto;
            background: var(--sb-primary); color: var(--sb-on-primary, #fff);
            font-size: 10px; font-weight: 700; letter-spacing: 0.01em;
            padding: 3px 8px; border-radius: 999px;
            white-space: nowrap; align-self: center;
        }

        /* ── Primeros pasos (cuenta nueva sin personal) ── */
        .dash-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .dash-step {
            display: flex; flex-direction: column; gap: 8px;
            padding: 16px; border: 1px solid var(--g-border); border-radius: 16px;
            background: var(--g-surface); text-decoration: none; color: inherit;
            transition: border-color 200ms;
        }
        .dash-step:hover { border-color: var(--g-primary); }
        .dash-step-n {
            width: 26px; height: 26px; border-radius: 50%; display: grid; place-items: center;
            background: var(--g-primary-light); color: var(--g-primary); font-size: 12px; font-weight: 700;
        }
        .dash-step-t { font-size: 13.5px; font-weight: 600; }
        .dash-step-d { font-size: 12px; color: var(--g-text-secondary); line-height: 1.45; }
        .dash-count {
            margin-left: auto; background: var(--g-error-light); color: var(--g-error);
            font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
        }
        @media (max-width: 768px) { .dash-steps { grid-template-columns: 1fr; } }
        .dash-link {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 16px;
            background: var(--g-surface); border: 1px solid var(--g-border);
            text-decoration: none; color: inherit;
            transition: border-color 200ms;
        }
        .dash-link:hover { border-color: var(--g-primary); }
        .dash-link-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .dash-link-icon .material-symbols-outlined { font-size: 22px; }
        .dash-link-text { font-size: 14px; font-weight: 500; line-height: 1.3; }
        .dash-link-text span { display: block; font-size: 12px; color: var(--g-text-secondary); font-weight: 400; margin-top: 2px; }

        @media (max-width: 768px) {
            .dash-link-grid { grid-template-columns: 1fr; }
        }
    </style>
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/tablas-botones.css?v=' . filemtime(FCPATH . 'css/index/components/tablas-botones.css')) ?>">
</head>
<body>
<div class="dev-layout">
    <?= view('partials/admin-sidebar', ['activePage' => $activePage ?? 'dashboard']) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div class="dash-greeting">
                    <h1>Hola, <strong><?= esc(session()->get('user_name') ?? 'Admin') ?></strong></h1>
                    <p>Resumen del sistema de asistencia.</p>
                    <div class="dash-date">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <?php if ((int) ($totalEmpleados ?? 0) === 0): ?>
                <!-- Cuenta nueva: qué hacer primero -->
                <section class="dev-card" style="margin-bottom:24px;">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">rocket_launch</span> Primeros pasos</h2>
                    </div>
                    <div style="padding:18px 20px;">
                        <div class="dash-steps">
                            <a href="<?= base_url('admin/personal') ?>" class="dash-step">
                                <span class="dash-step-n">1</span>
                                <span class="dash-step-t">Crea tu personal</span>
                                <span class="dash-step-d">Registra empleados y practicantes con su DNI y horario.</span>
                            </a>
                            <a href="<?= base_url('admin/horarios') ?>" class="dash-step">
                                <span class="dash-step-n">2</span>
                                <span class="dash-step-t">Define los horarios</span>
                                <span class="dash-step-d">Establece jornadas, días laborables y festivos.</span>
                            </a>
                            <a href="<?= base_url('admin/asistencias') ?>" class="dash-step">
                                <span class="dash-step-n">3</span>
                                <span class="dash-step-t">Revisa asistencias</span>
                                <span class="dash-step-d">Aquí aparecerán las marcaciones y sus incidencias.</span>
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Stats -->
            <section class="dev-stats-grid">
                <a href="<?= base_url('admin/personal') ?>" class="dev-stat-card" title="Ver personal">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div class="dev-stat-label">Personal</div>
                    <div class="dev-stat-value"><?= $totalEmpleados ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>&amp;estado=present" class="dev-stat-card" title="Ver presentes de hoy">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div class="dev-stat-label">Presentes hoy</div>
                    <div class="dev-stat-value"><?= $hoyPresentes ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>&amp;estado=late" class="dev-stat-card" title="Ver tardanzas de hoy">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">schedule</span></div>
                    <div class="dev-stat-label">Tardanzas hoy</div>
                    <div class="dev-stat-value"><?= $hoyTardanzas ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/asistencias') ?>?fecha_inicio=<?= date('Y-m-d') ?>&amp;fecha_fin=<?= date('Y-m-d') ?>" class="dev-stat-card" title="Ver el día de hoy">
                    <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">person_off</span></div>
                    <div class="dev-stat-label">Sin marcar hoy</div>
                    <div class="dev-stat-value"><?= $hoyFaltas ?? 0 ?></div>
                </a>
                <a href="<?= base_url('admin/incidencias') ?>" class="dev-stat-card" title="Ver incidencias pendientes">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">warning</span></div>
                    <div class="dev-stat-label">Incidencias pendientes</div>
                    <div class="dev-stat-value"><?= $incPendientes ?? 0 ?></div>
                </a>
            </section>

            <div class="dev-grid-2">
                <!-- Última actividad — sorteroOS: título fuera, bloque con hairlines -->
                <section class="dash-sec dash-act">
                    <div class="dash-sec-head">
                        <h2><span class="material-symbols-outlined">history</span> Última actividad</h2>
                        <a href="<?= base_url('admin/asistencias') ?>" class="dash-sec-link">Ver todo<span class="material-symbols-outlined">chevron_right</span></a>
                    </div>
                    <?php
                    $recent = array_values($attendance ?? []);
                    usort($recent, function ($a, $b) {
                        $ka = ($a['date'] ?? '') . ' ' . ($a['time_in'] ?? '') . ' ' . str_pad((string) ($a['id'] ?? 0), 10, '0', STR_PAD_LEFT);
                        $kb = ($b['date'] ?? '') . ' ' . ($b['time_in'] ?? '') . ' ' . str_pad((string) ($b['id'] ?? 0), 10, '0', STR_PAD_LEFT);
                        return strcmp($kb, $ka);
                    });
                    $recent = array_slice($recent, 0, 8);

                    // timeAgo idéntico al de sorteroOS: "Ahora" · "Hace 5m" ·
                    // "Hace 3h" · "Hace 2d" · y a partir de 7 días, fecha corta.
                    $dashRelativo = function (string $fecha, string $hora): string {
                        $ts = strtotime(trim($fecha . ' ' . $hora));
                        if (!$ts) return '';
                        $diff = time() - $ts;
                        if ($diff < 60)    return 'Ahora';
                        if ($diff < 3600)  return 'Hace ' . (int) floor($diff / 60) . 'm';
                        if ($diff < 86400) return 'Hace ' . (int) floor($diff / 3600) . 'h';
                        $dias = (int) floor($diff / 86400);
                        if ($dias < 7)     return 'Hace ' . $dias . 'd';
                        $meses = [1=>'ene','feb','mar','abr','may','jun',
                                  'jul','ago','sep','oct','nov','dic'];
                        return (int) date('j', $ts) . ' ' . $meses[(int) date('n', $ts)];
                    };

                    // El estado lo cargan el icono y la primera palabra de la
                    // descripción (sorteroOS no usa chips de color).
                    $dashEstado = [
                        'present'          => ['Presente',     'check_circle'],
                        'late'             => ['Tardanza',     'schedule'],
                        'absent'           => ['Falta',        'close'],
                        'absent_justified' => ['Justificada',  'verified'],
                        'no_exit'          => ['Sin salida',   'logout'],
                    ];
                    ?>
                    <?php if (empty($recent)): ?>
                        <div class="dev-recent-list">
                            <div class="dash-act-empty">
                                Sin actividad reciente — las marcaciones de tu equipo aparecerán aquí
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="dev-recent-list">
                            <?php foreach ($recent as $r): ?>
                                <?php
                                $st      = (string) ($r['status'] ?? 'unknown');
                                [$labelSt, $iconoSt] = $dashEstado[$st]
                                    ?? [ucfirst(str_replace('_', ' ', $st)), 'help'];

                                $fecha   = (string) ($r['date'] ?? '');
                                $hora    = (string) ($r['time_in'] ?? '');
                                $salida  = (string) ($r['time_out'] ?? '');

                                // Descripción: identidad + jornada, una sola línea
                                $parts = [];
                                if (!empty($r['dni'])) $parts[] = 'DNI ' . $r['dni'];
                                if ($hora !== '') {
                                    $jornada = mb_substr($hora, 0, 5);
                                    if ($salida !== '') $jornada .= ' → ' . mb_substr($salida, 0, 5);
                                    $parts[] = $jornada;
                                }
                                $meta = ($labelSt ?: 'Registro') . ($parts ? ' · ' . implode(' · ', $parts) : '');
                                ?>
                                <div class="dev-recent-item">
                                    <div class="dev-recent-avatar">
                                        <span class="material-symbols-outlined dash-act-ico"><?= esc($iconoSt) ?></span>
                                    </div>
                                    <div class="dev-recent-info">
                                        <div class="dev-recent-name"><?= esc($r['name'] ?? 'Empleado') ?></div>
                                        <div class="dash-act-desc"><?= esc($meta) ?></div>
                                    </div>
                                    <div class="dev-recent-right">
                                        <div class="dash-act-when"><?= esc($dashRelativo($fecha, $hora)) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Módulos — sorteroOS: título fuera, celdas a ras con hairlines -->
                <section class="dash-sec dash-mods">
                    <div class="dash-sec-head">
                        <h2><span class="material-symbols-outlined">apps</span> Módulos</h2>
                    </div>
                    <div class="dash-link-grid">
                        <a href="<?= base_url('admin/personal') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-solid"><span class="material-symbols-outlined">people</span></div>
                            <div class="dash-link-text">Personal<span>Empleados y códigos</span></div>
                        </a>
                        <a href="<?= base_url('admin/asistencias') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-soft"><span class="material-symbols-outlined">fingerprint</span></div>
                            <div class="dash-link-text">Asistencias<span>Registro diario</span></div>
                        </a>
                        <a href="<?= base_url('admin/horarios') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-soft"><span class="material-symbols-outlined">schedule</span></div>
                            <div class="dash-link-text">Horarios<span>Jornadas de trabajo</span></div>
                        </a>
                        <a href="<?= base_url('admin/incidencias') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-outline"><span class="material-symbols-outlined">warning</span></div>
                            <?php if (($incPendientes ?? 0) > 0): ?><span class="dash-mod-badge"><?= (int) $incPendientes ?><?= $incPendientes > 1 ? ' pendientes' : ' pendiente' ?></span><?php endif; ?>
                            <div class="dash-link-text">Incidencias<span>Casos y justificaciones</span></div>
                        </a>
                        <a href="<?= base_url('admin/reportes') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-soft"><span class="material-symbols-outlined">analytics</span></div>
                            <div class="dash-link-text">Reportes<span>Estadísticas</span></div>
                        </a>
                        <a href="<?= base_url('admin/configuracion') ?>" class="dash-link">
                            <div class="dash-mod-icon mi-outline"><span class="material-symbols-outlined">settings</span></div>
                            <div class="dash-link-text">Configuración<span>Ajustes y festivos</span></div>
                        </a>
                    </div>
                </section>
            </div>

        </div>
    </div>
</div>

<!-- Campana de notificaciones (compartida en todas las secciones admin) -->
<?= view('partials/admin-notif-bell') ?>
<?= view('partials/pwa-js') ?>
</body>
</html>
<!-- PWA -->
    <?= view('partials/pwa-head') ?>
