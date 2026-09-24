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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260924') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260924') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=20260924') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        /* Sin recuadro azul al hacer clic */
        .dev-layout button, .dev-layout a, .dev-layout input, .dev-layout select, .dev-layout textarea { outline: none; -webkit-tap-highlight-color: transparent; }
        .dev-layout button:focus-visible, .dev-layout a:focus-visible { outline: 2px solid var(--g-primary); outline-offset: 2px; }

        /* ── Selector de tipo (Practicante / Empleado) ── */
        .hr-tipo { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
        .hr-tipo-card { display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 16px; border: 2px solid var(--g-border); background: var(--g-surface-variant); cursor: pointer; transition: border-color 200ms, background 200ms, transform 200ms; position: relative; }
        .hr-tipo-card:hover { transform: translateY(-2px); }
        .hr-tipo-card input { position: absolute; opacity: 0; pointer-events: none; }
        .hr-tipo-card .mt-ic { width: 44px; height: 44px; border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--g-primary-light); color: var(--g-primary); }
        .hr-tipo-card .mt-ic .material-symbols-outlined { font-size: 24px; }
        .hr-tipo-card b { display: block; font-size: 14.5px; font-family: var(--g-font-display); }
        .hr-tipo-card small { font-size: 11.5px; color: var(--g-text-secondary); }
        .hr-tipo-card.on { border-color: var(--g-primary); background: var(--g-primary-light); }
        .hr-tipo-card.on::after { content: ''; position: absolute; right: 12px; top: 12px; width: 20px; height: 20px; border-radius: 50%; background: var(--g-primary); display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 0 4px var(--g-surface); }
        .hr-tipo-card.emp .mt-ic { background: var(--g-success-light); color: var(--g-success); }
        .hr-tipo-card.emp.on { border-color: var(--g-success); background: var(--g-success-light); }
        .hr-tipo-card.emp.on::after { background: var(--g-success); }

        /* ── Badges de tipo ── */
        .hr-tag { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 999px; }
        .hr-tag .material-symbols-outlined { font-size: 14px; }
        .hr-tag.hr-tag-pract { background: var(--g-primary-light); color: var(--g-primary); }
        .hr-tag.hr-tag-emp { background: var(--g-success-light); color: var(--g-success); }

        /* ── Chips de horario ── */
        .hr-chips { display: inline-flex; align-items: center; gap: 6px; }
        .hr-chip { background: var(--g-surface-variant); border: 1px solid var(--g-border); border-radius: 9px; padding: 4px 10px; font-family: 'SF Mono', Consolas, monospace; font-size: 12.5px; font-weight: 600; color: var(--g-text); }
        .hr-arrow { color: var(--g-text-disabled); font-size: 12px; }

        /* ── Botón integrantes ── */
        .hr-int-btn { display: inline-flex; align-items: center; gap: 6px; background: var(--g-primary-light); color: var(--g-primary); border: 1px solid var(--g-primary); padding: 6px 13px; border-radius: 999px; font-family: var(--g-font); font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 180ms; }
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
        .int-count { font-size: 12px; color: var(--g-text-secondary); padding: 2px 10px; border-radius: 999px; background: var(--g-surface-variant); }
        .int-none { text-align: center; color: var(--g-text-secondary); padding: 26px 10px; font-size: 13px; }
        .int-none .material-symbols-outlined { font-size: 38px; opacity: 0.3; display: block; margin: 0 auto 8px; }

        /* ── Detail ── */
        .detail-overlay {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
            opacity: 0; visibility: hidden; transition: opacity 200ms, visibility 200ms;
        }
        .detail-overlay.active { opacity: 1; visibility: visible; }
        .detail-card {
            background: var(--g-surface); border-radius: 28px; width: 90%; max-width: 440px;
            max-height: 85vh; overflow-y: auto;
            transform: scale(0.95); transition: transform 200ms;
        }
        .detail-overlay.active .detail-card { transform: scale(1); }
        .detail-cover { height: 80px; border-radius: 28px 28px 0 0; position: relative; }
        .detail-avatar-wrap {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--g-surface); padding: 4px;
            display: flex; align-items: center; justify-content: center;
            position: absolute; bottom: -40px; left: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .detail-avatar {
            width: 72px; height: 72px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 500; color: white;
            font-family: var(--g-font-display);
        }
        .detail-header { padding: 48px 24px 16px; }
        .detail-name { font-size: 22px; font-weight: 500; font-family: var(--g-font-display); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .detail-sub { font-size: 14px; color: var(--g-text-secondary); margin-top: 4px; }
        .detail-body { padding: 0 24px 24px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label { font-size: 11px; font-weight: 500; color: var(--g-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; }
        .detail-value { font-size: 14px; font-weight: 500; color: var(--g-text); padding: 10px 14px; background: var(--g-surface-variant); border-radius: 12px; }
        .detail-value code { font-family: 'SF Mono', Consolas, monospace; font-size: 13px; }
        .detail-actions { display: flex; gap: 12px; padding: 0 24px 24px; }
        .detail-btn { flex: 1; height: 48px; border: none; border-radius: 24px; font-family: var(--g-font); font-size: 14px; font-weight: 500; cursor: pointer; transition: all 200ms; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .detail-btn-edit { background: var(--g-primary); color: white; }
        .detail-btn-edit:hover { background: var(--g-primary-hover); }
        .detail-btn-close { background: var(--g-surface-variant); color: var(--g-text-secondary); }
        .detail-btn-close:hover { background: var(--g-border); }
        .detail-btn .material-symbols-outlined { font-size: 18px; }

        @media (max-width: 640px) {
            .hr-tipo { grid-template-columns: 1fr 1fr; gap: 8px; }
            .hr-tipo-card { padding: 12px; gap: 9px; }
            .hr-tipo-card .mt-ic { width: 38px; height: 38px; }
        }
    </style>
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
        <h1 class="dev-topbar-title">Horarios</h1>
        <p class="dev-topbar-subtitle">Gestiona los horarios por tipo y asigna a tu personal</p>
    </div>

    <div class="dev-content">

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="dev-alert dev-alert-<?= session()->getFlashdata('tipo') ?? 'success' ?>">
                <span class="material-symbols-outlined filled"><?= (session()->getFlashdata('tipo') ?? 'success') === 'success' ? 'check_circle' : 'warning' ?></span>
                <span><?= session()->getFlashdata('msg') ?></span>
            </div>
        <?php endif; ?>

        <div class="dev-card">
            <div class="dev-card-head">
                <h2><span class="material-symbols-outlined">add</span> Crear horario</h2>
            </div>
            <form action="<?= site_url('admin/create-schedule') ?>" method="POST" class="dev-form" id="createScheduleForm">
                <?= csrf_field() ?>
                <div style="padding: 16px;">
                    <div class="hr-tipo" id="hrTipoPick">
                        <label class="hr-tipo-card">
                            <input type="radio" name="sch_tipo" value="Practicante">
                            <div class="mt-ic"><span class="material-symbols-outlined">school</span></div>
                            <div>
                                <b>Practicante</b>
                                <small>Horario para practicantes</small>
                            </div>
                        </label>
                        <label class="hr-tipo-card emp on">
                            <input type="radio" name="sch_tipo" value="Empleado" checked>
                            <div class="mt-ic"><span class="material-symbols-outlined">work</span></div>
                            <div>
                                <b>Empleado</b>
                                <small>Horario para empleados</small>
                            </div>
                        </label>
                    </div>
                    <div class="dev-form-grid">
                        <div class="dev-field">
                            <label for="sch_nombre">Nombre</label>
                            <input type="text" id="sch_nombre" name="sch_nombre" required placeholder="Ej: Horario Manana">
                        </div>
                        <div class="dev-field">
                            <label for="sch_dias">Dias laborales</label>
                            <input type="text" id="sch_dias" name="sch_dias" value="Lun - Vie" required>
                        </div>
                        <div class="dev-field">
                            <label for="sch_hora_entrada">Hora entrada</label>
                            <input type="time" id="sch_hora_entrada" name="sch_hora_entrada" required>
                        </div>
                        <div class="dev-field">
                            <label for="sch_hora_salida">Hora salida</label>
                            <input type="time" id="sch_hora_salida" name="sch_hora_salida" required>
                        </div>
                        <div class="dev-field">
                            <label for="sch_tolerancia">Tolerancia (min)</label>
                            <input type="number" id="sch_tolerancia" name="sch_tolerancia" value="10" min="0" required>
                        </div>
                    </div>
                    <div style="margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="dev-btn dev-btn-primary"><span class="material-symbols-outlined">add</span> Crear horario</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="dev-card">
            <div class="dev-card-head">
                <h2><span class="material-symbols-outlined">schedule</span> Horarios registrados</h2>
            </div>

            <?php if (empty($schedules)): ?>
                <div class="dev-empty">
                    <span class="material-symbols-outlined">schedule</span>
                    <p>No hay horarios registrados</p>
                </div>
            <?php else: ?>
                <div class="dev-table-wrap">
                    <table class="dev-table">
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
                                <tr onclick="openScheduleDetail(JSON.parse(<?= $detail ?>))">
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
                                        <form action="<?= site_url('admin/delete-schedule') ?>" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="sch_id" value="<?= (int) $s['id'] ?>">
                                            <button type="submit" class="dev-btn-icon dev-btn-danger" title="Eliminar" onclick="return confirm('¿Eliminar este horario? Se desasignarán sus integrantes.')">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
                                <form action="<?= site_url('admin/delete-schedule') ?>" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="sch_id" value="<?= (int) $s['id'] ?>">
                                    <button type="submit" class="dev-btn-icon dev-btn-danger" onclick="return confirm('¿Eliminar este horario?')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

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
                <div class="hr-tipo" id="hrTipoEdit" style="margin-bottom:12px;">
                    <label class="hr-tipo-card">
                        <input type="radio" name="sch_tipo" value="Practicante">
                        <div class="mt-ic"><span class="material-symbols-outlined">school</span></div>
                        <div><b>Practicante</b><small>Horario para practicantes</small></div>
                    </label>
                    <label class="hr-tipo-card emp">
                        <input type="radio" name="sch_tipo" value="Empleado">
                        <div class="mt-ic"><span class="material-symbols-outlined">work</span></div>
                        <div><b>Empleado</b><small>Horario para empleados</small></div>
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
            <button class="dev-btn-icon" onclick="closeIntegrantes()">
                <span class="material-symbols-outlined">close</span>
            </button>
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
<div class="detail-overlay" id="scheduleDetailOverlay" onclick="if(event.target===this)closeScheduleDetail()">
    <div class="detail-card">
        <div class="detail-cover" style="background:linear-gradient(135deg,var(--g-primary-light),#e8f0fe44);">
            <div class="detail-avatar-wrap">
                <div class="detail-avatar" id="schDetailAvatar" style="background:var(--g-primary);">
                    <span class="material-symbols-outlined" style="font-size:32px;">schedule</span>
                </div>
            </div>
        </div>
        <div class="detail-header">
            <div class="detail-name" id="schDetailName">-</div>
            <div class="detail-sub" id="schDetailDias">-</div>
        </div>
        <div class="detail-body">
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
        <div class="detail-actions">
            <button type="button" class="detail-btn detail-btn-close" onclick="closeScheduleDetail()">
                <span class="material-symbols-outlined">close</span> Cerrar
            </button>
            <button type="button" class="detail-btn detail-btn-edit" id="schDetailEditBtn">
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
        pick.querySelectorAll('.hr-tipo-card').forEach(function(c) {
            c.classList.toggle('on', c.querySelector('input').checked);
        });
    });
})();
(function() {
    var pick = document.getElementById('hrTipoEdit');
    pick.addEventListener('change', function(e) {
        if (e.target.name !== 'sch_tipo') return;
        pick.querySelectorAll('.hr-tipo-card').forEach(function(c) {
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
    pick.querySelectorAll('.hr-tipo-card').forEach(function(c) {
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

</body>
</html>