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
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <style>
        .att-wrap { display: flex; flex-direction: column; gap: 22px; }

        /* ── Filtros ─────────────────────────── */
        .att-filters { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; padding: 18px; }
        .att-field { display: flex; flex-direction: column; gap: 6px; }
        .att-field > label { font-size: 0.78rem; font-weight: 500; color: var(--g-text-secondary); }
        .att-input {
            height: 40px; padding: 0 12px; border: 1px solid var(--g-border);
            border-radius: 10px; background: var(--g-surface); color: var(--g-text);
            font-size: 0.875rem; font-family: inherit; outline: none;
        }
        .att-input:focus { border-color: var(--g-primary); box-shadow: 0 0 0 3px rgba(27,122,66,.12); }
        .att-go {
            height: 40px; padding: 0 18px; border: 0; border-radius: 10px; cursor: pointer;
            background: var(--g-primary); color: #fff; font-weight: 600; font-size: 0.875rem;
            display: inline-flex; align-items: center; gap: 6px; font-family: inherit;
        }
        .att-go:hover { filter: brightness(1.07); }
        .att-chips { display: flex; gap: 8px; flex-wrap: wrap; padding: 0 18px 18px; }
        .att-chip {
            height: 30px; padding: 0 12px; border: 1px solid var(--g-border);
            border-radius: 999px; background: transparent; color: var(--g-text-secondary); cursor: pointer;
            font-size: 0.78rem; font-weight: 500; font-family: inherit;
        }
        .att-chip:hover { background: var(--g-surface-variant); color: var(--g-text); }
        .att-chip.on { background: var(--g-primary); border-color: var(--g-primary); color: #fff; }

        /* ── Resumen ─────────────────────────── */
        .att-sum { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .att-tile {
            background: var(--g-surface); border: 1px solid var(--g-border);
            border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        }
        .att-tile-ico { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex: none; }
        .att-tile-ico .material-symbols-outlined { font-size: 20px; }
        .att-tile-ico.plain { background: rgba(27,122,66,.10); color: var(--g-primary); }
        .att-tile-ico.green { background: rgba(34,197,94,.14); color: #16a34a; }
        .att-tile-ico.amber { background: rgba(245,158,11,.16); color: #d97706; }
        .att-tile-ico.gray  { background: rgba(148,163,184,.20); color: #64748b; }
        .att-tile-num { font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: var(--g-text); }
        .att-tile-lbl { font-size: 0.78rem; color: var(--g-text-secondary); margin-top: 2px; }

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
            width: 36px; height: 36px; border: 1px solid var(--g-border); border-radius: 10px;
            background: transparent; color: var(--g-text-secondary); cursor: pointer;
            display: grid; place-items: center; flex: none; transition: border-color 200ms, color 200ms;
        }
        .att-edit:hover { border-color: var(--g-primary); color: var(--g-primary); }
        .att-edit .material-symbols-outlined { font-size: 18px; }

        /* ── Detalle ─────────────────────────── */
        .detail-overlay {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,.4); backdrop-filter: blur(4px);
            opacity: 0; visibility: hidden; transition: opacity 200ms, visibility 200ms;
        }
        .detail-overlay.active { opacity: 1; visibility: visible; }
        .detail-card {
            background: var(--g-surface); border-radius: 24px; width: 90%; max-width: 460px;
            max-height: 85vh; overflow-y: auto;
            transform: scale(.96); transition: transform 200ms;
        }
        .detail-overlay.active .detail-card { transform: scale(1); }
        .detail-head { padding: 22px 24px 14px; display: flex; align-items: center; gap: 14px; }
        .detail-av {
            width: 52px; height: 52px; border-radius: 14px; color: #fff; font-size: 1.2rem; font-weight: 600;
            display: grid; place-items: center; flex: none;
        }
        .detail-name { font-size: 1.05rem; font-weight: 600; color: var(--g-text); }
        .detail-sub { font-size: 0.8rem; color: var(--g-text-secondary); margin-top: 2px; }
        .detail-close { margin-left: auto; }
        .detail-body { padding: 0 24px 8px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
        .detail-field { display: flex; flex-direction: column; gap: 4px; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: var(--g-text-secondary); text-transform: uppercase; letter-spacing: .04em; }
        .detail-value {
            font-size: 0.9rem; font-weight: 500; color: var(--g-text);
            padding: 10px 14px; background: var(--g-surface-variant); border-radius: 12px;
            font-variant-numeric: tabular-nums;
        }
        .detail-actions { display: flex; gap: 10px; padding: 16px 24px 22px; }

        @media (max-width: 760px) {
            .att-sum { grid-template-columns: repeat(2, 1fr); }
            .att-tile { padding: 14px; }
            .att-filters { flex-direction: column; align-items: stretch; }
            .att-go { justify-content: center; }
        }
    </style>
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
                $totalRegistros = 0;
                $totalPresentes = 0;
                $totalTardanzas = 0;
                $totalFaltas = 0;
                foreach ($attendance as $reg) {
                    $totalRegistros++;
                    $st = $reg['status'] ?? '';
                    if ($st === 'present') $totalPresentes++;
                    elseif ($st === 'late') $totalTardanzas++;
                    elseif ($st === 'absent') $totalFaltas++;
                }
                $hoy    = date('Y-m-d');
                $semana = date('Y-m-d', strtotime('monday this week'));
                $mesIni = date('Y-m-01');
                $mode   = empty($fecha_inicio) || empty($fecha_fin) ? 'todo' : ($fecha_inicio === $fecha_fin ? ($fecha_inicio === $hoy ? 'hoy' : '') : ($fecha_inicio === $semana && $fecha_fin === $hoy ? 'semana' : ($fecha_inicio === $mesIni ? 'mes' : '')));
            ?>

            <div class="att-wrap">
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">filter_alt</span> Filtros de fecha</h2>
                    </div>
                    <form method="get" action="<?= base_url('admin/asistencias') ?>" id="filtro-form">
                        <div class="att-filters">
                            <div class="att-field">
                                <label for="fecha_inicio">Fecha inicio</label>
                                <input type="date" class="att-input" id="fecha_inicio" name="fecha_inicio" value="<?= esc($fecha_inicio ?? '') ?>">
                            </div>
                            <div class="att-field">
                                <label for="fecha_fin">Fecha fin</label>
                                <input type="date" class="att-input" id="fecha_fin" name="fecha_fin" value="<?= esc($fecha_fin ?? '') ?>">
                            </div>
                            <button type="submit" class="att-go">
                                <span class="material-symbols-outlined">search</span> Filtrar
                            </button>
                        </div>
                        <div class="att-chips">
                            <button type="button" class="att-chip <?= $mode === 'hoy' ? 'on' : '' ?>" onclick="rango('hoy')">Hoy</button>
                            <button type="button" class="att-chip <?= $mode === 'semana' ? 'on' : '' ?>" onclick="rango('semana')">Esta semana</button>
                            <button type="button" class="att-chip <?= $mode === 'mes' ? 'on' : '' ?>" onclick="rango('mes')">Este mes</button>
                            <button type="button" class="att-chip <?= $mode === 'todo' ? 'on' : '' ?>" onclick="rango('todo')">Todo</button>
                        </div>
                    </form>
                </div>

                <div class="att-sum">
                    <div class="att-tile">
                        <div class="att-tile-ico plain"><span class="material-symbols-outlined">summarize</span></div>
                        <div>
                            <div class="att-tile-num"><?= $totalRegistros ?></div>
                            <div class="att-tile-lbl">Total registros</div>
                        </div>
                    </div>
                    <div class="att-tile">
                        <div class="att-tile-ico green"><span class="material-symbols-outlined">check_circle</span></div>
                        <div>
                            <div class="att-tile-num"><?= $totalPresentes ?></div>
                            <div class="att-tile-lbl">Presentes</div>
                        </div>
                    </div>
                    <div class="att-tile">
                        <div class="att-tile-ico amber"><span class="material-symbols-outlined">schedule</span></div>
                        <div>
                            <div class="att-tile-num"><?= $totalTardanzas ?></div>
                            <div class="att-tile-lbl">Tardanzas</div>
                        </div>
                    </div>
                    <div class="att-tile">
                        <div class="att-tile-ico gray"><span class="material-symbols-outlined">cancel</span></div>
                        <div>
                            <div class="att-tile-num"><?= $totalFaltas ?></div>
                            <div class="att-tile-lbl">Faltas</div>
                        </div>
                    </div>
                </div>

                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">history</span> Registros</h2>
                    </div>

                    <?php if (empty($attendance)): ?>
                        <div class="dev-empty">
                            <span class="material-symbols-outlined">event_busy</span>
                            <p>No hay registros de asistencia en este rango.</p>
                        </div>
                    <?php else: ?>
                        <div class="att-list">
                            <?php foreach ($attendance as $r):
                                $st     = $r['status'] ?? 'absent';
                                $estado = match($st) { 'present' => 'present', 'late' => 'late', 'absent' => 'absent', 'no_exit' => 'no_exit', default => 'absent' };
                                $label  = match($st) { 'present' => 'Presente', 'late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida', default => ucfirst($st) };
                                $nombre = $r['name'] ?? 'Empleado';
                            ?>
                            <div class="att-item" onclick="openDetail('<?= (int) $r['id'] ?>')">
                                <div class="att-av"><?= strtoupper(mb_substr($nombre, 0, 1, 'UTF-8')) ?></div>
                                <div class="att-main">
                                    <div class="att-name">
                                        <?= esc($nombre) ?>
                                        <span class="att-dni"><?= esc($r['dni'] ?? '') ?></span>
                                    </div>
                                    <div class="att-meta">
                                        <span><b><?= esc($r['date'] ?? '') ?></b></span>
                                        <span>Entrada <b><?= esc($r['time_in'] ?? '-') ?></b></span>
                                        <span>Salida <b><?= esc($r['time_out'] ?? '-') ?></b></span>
                                    </div>
                                </div>
                                <span class="att-estado <?= $estado ?>"><i></i><?= $label ?></span>
                                <button type="button" class="att-edit" onclick="event.stopPropagation(); openEdit(<?= (int) $r['id'] ?>)" title="Editar">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div class="detail-overlay" id="attDetailOverlay" onclick="if(event.target===this)closeDetail()">
        <div class="detail-card">
            <div class="detail-head">
                <div class="detail-av" id="attDetailAvatar">E</div>
                <div>
                    <div class="detail-name" id="attDetailName">-</div>
                    <div class="detail-sub" id="attDetailDni">-</div>
                </div>
                <button type="button" class="dev-modal-close detail-close" onclick="closeDetail()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="detail-body">
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
                </div>
            </div>
            <div class="detail-actions">
                <button type="button" class="dev-btn dev-btn-text" onclick="closeDetail()">Cerrar</button>
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
        document.getElementById('attDetailEditBtn').onclick = function () { closeDetail(); openEdit(r.id); };
        document.getElementById('attDetailOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeDetail() {
        document.getElementById('attDetailOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function openEdit(id) {
        var r = attRows[id];
        if (!r) return;
        document.getElementById('modal_att_id').value = r.id;
        document.getElementById('modal_time_in').value = r.time_in || '';
        document.getElementById('modal_time_out').value = r.time_out || '';
        document.getElementById('modal_status').value = r.status || 'absent';
        document.getElementById('modal_observacion').value = r.observacion || '';
        document.getElementById('editModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeEdit() {
        document.getElementById('editModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('editModal').addEventListener('click', function (e) { if (e.target === this) closeEdit(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeEdit(); closeDetail(); } });

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
    </script>
</body>
</html>