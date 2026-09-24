<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencias - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260924') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260924') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        /* Sin recuadro azul al hacer clic */
        .dev-layout button, .dev-layout a, .dev-layout input, .dev-layout select, .dev-layout textarea { outline: none; -webkit-tap-highlight-color: transparent; }
        .dev-layout button:focus-visible, .dev-layout a:focus-visible { outline: 2px solid var(--g-primary); outline-offset: 2px; }

        /* ── Resumen ── */
        .in-sum {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 18px; padding: 22px 24px; margin-bottom: 18px;
        }
        .in-sum-top { display: flex; align-items: center; gap: 26px; flex-wrap: wrap; }
        .in-big { flex-shrink: 0; text-align: center; }
        .in-big b { display: block; font-size: 44px; line-height: 1; font-family: var(--g-font-display); letter-spacing: -0.03em; color: var(--g-text); }
        .in-big span { font-size: 12px; color: var(--g-text-secondary); }
        .in-bar { flex: 1; min-width: 220px; }
        .in-bar-track { display: flex; height: 14px; border-radius: 999px; overflow: hidden; background: var(--g-surface-variant); }
        .in-bar-seg { height: 100%; transition: width 500ms; }
        .in-bar-pend { background: var(--g-warning); }
        .in-bar-gap  { background: #94c0f0; }
        .in-bar-res  { background: var(--g-success); }
        .in-bar-rej  { background: var(--g-error); }
        .in-legend { display: flex; justify-content: space-between; gap: 12px; margin-top: 12px; font-size: 12.5px; color: var(--g-text-secondary); flex-wrap: wrap; }
        .in-legend span { display: inline-flex; align-items: center; gap: 7px; }
        .in-legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
        .in-legend .lg-p { color: #b45309; } .in-legend .lg-p i { background: var(--g-warning); }
        .in-legend .lg-g { color: var(--g-success); } .in-legend .lg-g i { background: var(--g-success); }
        .in-legend .lg-e { color: var(--g-error); } .in-legend .lg-e i { background: var(--g-error); }
        .in-legend b { color: var(--g-text); font-weight: 600; }

        /* ── Lista ── */
        .in-card { background: var(--g-surface); border: 1px solid var(--g-border); border-radius: 18px; overflow: hidden; }
        .in-card-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--g-border); flex-wrap: wrap; }
        .in-card-head h3 { margin: 0; font-size: 15px; font-weight: 600; font-family: var(--g-font-display); display: flex; align-items: center; gap: 8px; }
        .in-card-head h3 .material-symbols-outlined { color: var(--g-warning); font-size: 20px; }

        .in-item { display: flex; gap: 14px; padding: 16px 20px; align-items: flex-start; }
        .in-item + .in-item { border-top: 1px solid var(--g-border); }

        .in-av {
            flex-shrink: 0; width: 42px; height: 42px; border-radius: 12px;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; font-family: var(--g-font-display);
        }

        .in-main { flex: 1; min-width: 0; }
        .in-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .in-name strong { font-size: 14.5px; font-family: var(--g-font-display); }
        .in-role { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--g-text-secondary); background: var(--g-surface-variant); padding: 2px 8px; border-radius: 999px; }
        .in-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin: 7px 0 4px; }
        .in-tag { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: 999px; background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .in-tag .material-symbols-outlined { font-size: 13px; }
        .in-date { font-size: 11.5px; color: var(--g-text-secondary); font-family: 'SF Mono', Consolas, monospace; }
        .in-detalle { font-size: 12.5px; color: var(--g-text-secondary); line-height: 1.5; }
        .in-detalle:empty { display: none; }

        .in-just {
            margin-top: 10px; border-radius: 10px; padding: 9px 13px; font-size: 12.5px;
            display: flex; gap: 8px; align-items: flex-start;
            background: var(--g-surface-variant); color: var(--g-text-secondary);
        }
        .in-just .material-symbols-outlined { font-size: 16px; flex-shrink: 0; margin-top: 1px; opacity: 0.6; }
        .in-just p { margin: 0; }
        .in-just small { display: block; font-weight: 600; color: var(--g-text); margin-bottom: 2px; }

        .in-estado { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; color: var(--g-text-secondary); }
        .in-estado i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .in-estado.dot-pendiente i { background: #f59e0b; }
        .in-estado.dot-revision i { background: #3b82f6; }
        .in-estado.dot-justificada i { background: #22c55e; }
        .in-estado.dot-rechazada i { background: #94a3b8; }
        .in-estado.dot-desest i { background: #94a3b8; }

        .in-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; flex-wrap: wrap; }
        .in-btn {
            display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--g-border);
            background: transparent; color: var(--g-text); font-family: var(--g-font);
            font-size: 12px; font-weight: 600; padding: 6px 11px; border-radius: 999px; cursor: pointer; transition: all 160ms;
        }
        .in-btn .material-symbols-outlined { font-size: 15px; }
        .in-btn:hover { border-color: var(--g-primary); color: var(--g-primary); background: var(--g-primary-light); }

        .in-empty { text-align: center; padding: 54px 20px; color: var(--g-text-secondary); font-size: 13.5px; }
        .in-empty .material-symbols-outlined { font-size: 48px; display: block; margin: 0 auto 14px; opacity: 0.3; }
        .in-empty .in-empty-act { margin-top: 16px; }

        @media (max-width: 720px) {
            .in-item { padding: 14px; gap: 12px; flex-wrap: wrap; }
            .in-actions { width: 100%; padding-left: 58px; }
            .in-sum { padding: 18px; }
            .in-big b { font-size: 34px; }
        }
    </style>
</head>
<body>

<?= view('partials/admin-sidebar', ['activePage' => 'incidencias']) ?>

<main class="dev-main">
    <div class="dev-topbar">
        <h1 class="dev-topbar-title">Incidencias</h1>
        <p class="dev-topbar-subtitle">Incidencias generadas desde la asistencia y asignadas a tu personal</p>
    </div>

    <div class="dev-content">

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                <span><?= session()->getFlashdata('msg') ?></span>
            </div>
        <?php endif; ?>

        <?php
        $totalIncidencias = count($incidents);
        $pendientes = 0; $justificadas = 0; $rechazadas = 0; $revision = 0;
        foreach ($incidents as $inc) {
            $e = $inc['estado'] ?? 'Pendiente';
            if ($e === 'Pendiente')  $pendientes++;
            elseif ($e === 'Revisión') $revision++;
            elseif ($e === 'Justificada') $justificadas++;
            elseif ($e === 'Rechazada' || $e === 'Desestimada') $rechazadas++;
        }
        $resueltas = $justificadas + $rechazadas;
        $sPend = $totalIncidencias > 0 ? round(($pendientes / $totalIncidencias) * 100) : 0;
        $sRev  = $totalIncidencias > 0 ? round(($revision  / $totalIncidencias) * 100) : 0;
        $sRes  = $totalIncidencias > 0 ? round(($resueltas  / $totalIncidencias) * 100) : 0;
        if (($sPend + $sRev + $sRes) !== 100 && $totalIncidencias > 0) { $sRes += 100 - ($sPend + $sRev + $sRes); }

        $roleMap = [];
        foreach (($personal ?? []) as $p) {
            $roleMap[(int) $p['id']] = ($p['role'] ?? 'Personal') . (!empty($p['cargo']) ? ' · ' . $p['cargo'] : '');
        }

        $tipoInfo = [
            'Tardanza'          => ['label' => 'Tardanza',          'icon' => 'schedule'],
            'Falta'             => ['label' => 'Falta',             'icon' => 'cancel'],
            'Salida anticipada' => ['label' => 'Salida anticipada', 'icon' => 'logout'],
            'Sin marcación'     => ['label' => 'Sin marcación',     'icon' => 'event_busy'],
            'Sin marcacion'     => ['label' => 'Sin marcación',     'icon' => 'event_busy'],
            'Otro'              => ['label' => 'Otro',              'icon' => 'help'],
        ];
        $estadoInfo = [
            'Pendiente'   => 'dot-pendiente',
            'Revisión'    => 'dot-revision',
            'Justificada' => 'dot-justificada',
            'Rechazada'   => 'dot-rechazada',
            'Desestimada' => 'dot-desest',
        ];
        ?>

        <!-- Resumen de estados -->
        <div class="in-sum">
            <div class="in-sum-top">
                <div class="in-big">
                    <b><?= $totalIncidencias ?></b>
                    <span>incidencias</span>
                </div>
                <div class="in-bar">
                    <div class="in-bar-track">
                        <div class="in-bar-seg in-bar-pend" style="width:<?= $sPend ?>%;"></div>
                        <div class="in-bar-seg in-bar-gap"  style="width:<?= $sRev ?>%;"></div>
                        <div class="in-bar-seg in-bar-res"  style="width:<?= $sRes ?>%;"></div>
                    </div>
                    <div class="in-legend">
                        <span class="lg-p"><i></i> <b><?= $pendientes ?></b> pendiente<?= $pendientes === 1 ? '' : 's' ?></span>
                        <span class="lg-g"><i></i> <b><?= $revision ?></b> en revisión</span>
                        <span class="lg-g" style="color:var(--g-error);"><i style="background:var(--g-error);"></i> <b><?= $rechazadas ?></b> rechazada<?= $rechazadas === 1 ? '' : 's' ?></span>
                        <span class="lg-g"><i></i> <b><?= $justificadas ?></b> justificada<?= $justificadas === 1 ? '' : 's' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista -->
        <div class="in-card">
            <div class="in-card-head">
                <h3><span class="material-symbols-outlined">list_alt</span> Lista de incidencias</h3>
                <button type="button" class="dev-btn dev-btn-primary dev-btn-sm" onclick="openCreateModal()">
                    <span class="material-symbols-outlined">add</span> Nueva incidencia
                </button>
            </div>

            <?php if ($totalIncidencias === 0): ?>
                <div class="in-empty">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p style="margin:0 0 2px;">No hay incidencias registradas</p>
                    <p style="font-size:12.5px;max-width:320px;margin:0 auto;">Registra una incidencia y asígnala a una persona de tu personal.</p>
                    <div class="in-empty-act">
                        <button type="button" class="dev-btn dev-btn-primary" onclick="openCreateModal()">
                            <span class="material-symbols-outlined">add</span> Registrar y asignar
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($incidents as $inc):
                    $t = $inc['tipo'] ?? 'Otro';
                    $ti = $tipoInfo[$t] ?? $tipoInfo['Otro'];
                    $e  = $inc['estado'] ?? 'Pendiente';
                    $eb = $estadoInfo[$e] ?? 'dot-pendiente';
                    $uName = $inc['name'] ?? 'Empleado';
                    $ini = strtoupper(mb_substr($uName, 0, 1));
                    $uRole = $roleMap[(int) ($inc['user_id'] ?? 0)] ?? ($inc['role'] ?? 'Personal');
                    $hasJust = !empty($inc['justificacion']);
                    $jb = $e === 'Rechazada' ? 'do_not_disturb_on' : ($e === 'Revisión' ? 'hourglass_top' : ($e === 'Pendiente' ? 'description' : 'verified'));
                    $jMsg = $e === 'Rechazada' ? 'Rechazada por el administrador' : ($e === 'Revisión' ? 'Justificación en revisión' : ($e === 'Pendiente' ? 'Justificación enviada' : 'Justificación aceptada'));
                ?>
                    <div class="in-item">
                        <div class="in-av"><?= esc($ini) ?></div>
                        <div class="in-main">
                            <div class="in-name">
                                <strong><?= esc($uName) ?></strong>
                                <span class="in-role"><?= esc($uRole) ?></span>
                                <span class="in-estado <?= $eb ?>"><i></i> <?= esc($e) ?></span>
                            </div>
                            <div class="in-meta">
                                <span class="in-tag"><span class="material-symbols-outlined"><?= $ti['icon'] ?></span> <?= esc($ti['label']) ?></span>
                                <span class="in-date"><?= esc(date('d/m/Y H:i', strtotime($inc['fecha'] ?? 'now'))) ?></span>
                            </div>
                            <?php if (!empty($inc['detalle'])): ?>
                                <div class="in-detalle"><?= esc($inc['detalle']) ?></div>
                            <?php endif; ?>
                            <?php if ($hasJust): ?>
                                <div class="in-just">
                                    <span class="material-symbols-outlined"><?= $jb ?></span>
                                    <p><small><?= $jMsg ?></small><?= esc($inc['justificacion']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="in-actions">
                            <?php if ($e === 'Pendiente' || $e === 'Revisión'): ?>
                                <form action="<?= site_url('admin/update-incident') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="inc_id" value="<?= (int) $inc['id'] ?>">
                                    <input type="hidden" name="inc_estado" value="Justificada">
                                    <button type="submit" class="in-btn" onclick="return confirm('¿Marcar como justificada?')">
                                        <span class="material-symbols-outlined">check</span> Justificar
                                    </button>
                                </form>
                                <form action="<?= site_url('admin/update-incident') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="inc_id" value="<?= (int) $inc['id'] ?>">
                                    <input type="hidden" name="inc_estado" value="Rechazada">
                                    <button type="submit" class="in-btn" onclick="return confirm('¿Rechazar esta incidencia?')">
                                        <span class="material-symbols-outlined">close</span> Rechazar
                                    </button>
                                </form>
                            <?php endif; ?>
                            <button type="button" class="in-btn" onclick='openEditModal(<?= (int) $inc['id'] ?>, <?= json_encode($e, JSON_HEX_APOS | JSON_HEX_TAG) ?>, <?= json_encode($inc['justificacion'] ?? '', JSON_HEX_APOS | JSON_HEX_TAG) ?>)'>
                                <span class="material-symbols-outlined">edit</span> Editar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<!-- Modal · Nueva incidencia -->
<div class="dev-modal-overlay" id="createModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2><span class="material-symbols-outlined">add_circle</span> Nueva incidencia</h2>
            <button class="dev-btn-icon" onclick="closeCreateModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/create-incident') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <div class="dev-modal-body">
                <?php
                $gente = array_values(array_filter($personal ?? [], fn($p) => ($p['estado'] ?? '') === 'Activo'));
                ?>
                <div class="dev-field">
                    <label for="inc_user">Asignar a</label>
                    <?php if (empty($gente)): ?>
                        <p style="font-size:12.5px;color:var(--g-text-secondary);margin:0;">No tienes personal activo todavía. Añade personas desde <a href="<?= site_url('admin/personal') ?>">Personal</a>.</p>
                        <input type="hidden" name="inc_user" value="0">
                    <?php else: ?>
                        <select id="inc_user" name="inc_user" required>
                            <?php foreach ($gente as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?> — <?= esc($p['role']) ?><?= !empty($p['cargo']) ? ' · ' . esc($p['cargo']) : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="dev-form-grid">
                    <div class="dev-field">
                        <label for="inc_tipo">Tipo</label>
                        <select id="inc_tipo" name="inc_tipo" required>
                            <option value="Tardanza">Tardanza</option>
                            <option value="Falta">Falta</option>
                            <option value="Salida anticipada">Salida anticipada</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="dev-field">
                        <label for="inc_fecha">Fecha</label>
                        <input type="datetime-local" id="inc_fecha" name="inc_fecha" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                </div>
                <div class="dev-field">
                    <label for="inc_detalle">Detalle</label>
                    <textarea id="inc_detalle" name="inc_detalle" rows="4" required placeholder="Describe qué ocurrió..."></textarea>
                </div>
            </div>
            <div class="dev-modal-footer">
                <button type="button" class="dev-btn" onclick="closeCreateModal()">Cancelar</button>
                <button type="submit" class="dev-btn dev-btn-primary">
                    <span class="material-symbols-outlined">assignment_add</span> Registrar incidencia
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal · Editar incidencia -->
<div class="dev-modal-overlay" id="editModal">
    <div class="dev-modal">
        <div class="dev-modal-head">
            <h2>Editar incidencia</h2>
            <button class="dev-btn-icon" onclick="closeEditModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= site_url('admin/update-incident') ?>" method="POST" class="dev-form">
            <?= csrf_field() ?>
            <input type="hidden" name="inc_id" id="edit_inc_id">
            <div class="dev-modal-body">
                <div class="dev-form-grid">
                    <div class="dev-field">
                        <label for="edit_inc_status">Estado</label>
                        <select id="edit_inc_status" name="inc_estado" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Revisión">En revisión</option>
                            <option value="Justificada">Justificada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="dev-field">
                        <label for="edit_inc_justification">Justificacion</label>
                        <textarea id="edit_inc_justification" name="inc_justificacion" rows="4" placeholder="Ingrese la justificacion..."></textarea>
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

<script>
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
function openEditModal(id, estado, justificacion) {
    document.getElementById('edit_inc_id').value = id;
    document.getElementById('edit_inc_status').value = estado;
    document.getElementById('edit_inc_justification').value = justificacion || '';
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
</script>

</body>
</html>