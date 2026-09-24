<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel · DSG Perú</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=20260922') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-table.css?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>

<div class="dev-layout">
    <?php $activePage = 'dashboard'; ?>
    <?= view('partials/admin-sidebar', ['activePage' => $activePage]) ?>

    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1>Bienvenido, <strong>Admin</strong></h1>
                    <p>Gestiona a tu personal, genera códigos y revisa la asistencia.</p>
                </div>
                <div class="dev-topbar-actions">
                    <a href="#crear-empleado" class="dev-btn dev-btn-primary">
                        <span class="material-symbols-outlined">person_add</span> Crear empleado
                    </a>
                </div>
            </div>
        </header>

        <div class="dev-content">
            <?php $flashMsg = session()->getFlashdata('msg'); ?>
            <?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
                    <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : 'warning' ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <!-- ═══════ STATS ═══════ -->
            <section class="dev-stats-grid">
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">badge</span></div>
                    <div class="dev-stat-label">Mis empleados</div>
                    <div class="dev-stat-value"><?= $totalEmpleados ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">check_circle</span></div>
                    <div class="dev-stat-label">Presentes hoy</div>
                    <div class="dev-stat-value"><?= $hoyPresentes ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">schedule</span></div>
                    <div class="dev-stat-label">Tardanzas hoy</div>
                    <div class="dev-stat-value"><?= $hoyTardanzas ?></div>
                </div>
                <div class="dev-stat-card">
                    <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">vpn_key</span></div>
                    <div class="dev-stat-label">Códigos activos</div>
                    <div class="dev-stat-value"><?= count(array_filter($codes ?? [], fn($c) => ($c['status'] ?? '') === 'active')) ?></div>
                </div>
            </section>

            <div class="dev-grid-2">
                <!-- ═══════ CREAR EMPLEADO ═══════ -->
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">person_add</span> Crear empleado</h2>
                    </div>
                    <form class="dev-form" method="post" action="<?= site_url('admin/create-employee') ?>">
                        <?= csrf_field() ?>
                        <div class="dev-form-body">
                            <div class="dev-form-grid">
                                <div class="dev-field">
                                    <label for="emp_name">Nombre completo</label>
                                    <input type="text" id="emp_name" name="emp_name" placeholder="Rosa Campos" required>
                                </div>
                                <div class="dev-field">
                                    <label for="emp_dni">DNI (8 dígitos)</label>
                                    <input type="text" id="emp_dni" name="emp_dni" placeholder="12345678" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" required>
                                </div>
                                <div class="dev-field">
                                    <label for="emp_password">Contraseña</label>
                                    <input type="password" id="emp_password" name="emp_password" placeholder="Mínimo 6 caracteres" required minlength="6">
                                </div>
                            </div>
                            <button type="submit" class="dev-btn dev-btn-primary">
                                <span class="material-symbols-outlined">person_add</span> Crear empleado
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ═══════ GENERAR CÓDIGO ═══════ -->
                <div class="dev-card">
                    <div class="dev-card-head">
                        <h2><span class="material-symbols-outlined">vpn_key</span> Generar código</h2>
                    </div>
                    <form class="dev-form" method="post" action="<?= site_url('admin/create-code') ?>">
                        <?= csrf_field() ?>
                        <div class="dev-form-body">
                            <div class="dev-form-grid">
                                <div class="dev-field">
                                    <label for="code_name">Nombre del empleado</label>
                                    <input type="text" id="code_name" name="name" placeholder="Juan Pérez" required>
                                </div>
                                <div class="dev-field">
                                    <label for="code_dni">DNI (8 dígitos)</label>
                                    <input type="text" id="code_dni" name="dni" placeholder="12345678" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" required>
                                </div>
                            </div>
                            <button type="submit" class="dev-btn dev-btn-primary">
                                <span class="material-symbols-outlined">add</span> Generar código
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ═══════ MIS EMPLEADOS ═══════ -->
            <section class="dev-card" style="margin-top: 12px;">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">badge</span> Mis empleados</h2>
                    <span class="dev-badge dev-badge-blue"><?= count($empleados) ?> total</span>
                </div>
                <?php if (empty($empleados)): ?>
                    <div class="dev-empty">
                        <span class="material-symbols-outlined">badge</span>
                        Aún no tienes empleados. Crea uno o genera un código.
                    </div>
                <?php else: ?>
                    <div class="dev-table-wrap">
                        <table class="dev-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Creado</th>
                                    <th>Último login</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $u): ?>
                                <tr>
                                    <td><span class="dev-table-id">#<?= $u['id'] ?></span></td>
                                    <td>
                                        <div class="dev-table-user">
                                            <div class="dev-table-avatar"><?= strtoupper(substr($u['name'], 0, 2)) ?></div>
                                            <span><?= esc($u['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="dev-table-mono"><?= esc($u['dni'] ?? '-') ?></span></td>
                                    <td><?= esc($u['created']) ?></td>
                                    <td><?= esc($u['last_login'] ?? 'Nunca') ?></td>
                                    <td>
                                        <div class="dev-table-actions">
                                            <form method="post" action="<?= site_url('admin/delete-user') ?>" onsubmit="return confirm('¿Eliminar este empleado?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="dev-btn-icon" title="Eliminar">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile cards -->
                    <div class="dev-table-mobile">
                        <?php foreach ($empleados as $u): ?>
                        <div class="dev-table-card">
                            <div class="dev-table-card-header">
                                <div class="dev-table-user">
                                    <div class="dev-table-avatar"><?= strtoupper(substr($u['name'], 0, 2)) ?></div>
                                    <div>
                                        <div class="dev-table-card-title"><?= esc($u['name']) ?></div>
                                        <div class="dev-table-card-sub">DNI <?= esc($u['dni'] ?? '-') ?></div>
                                    </div>
                                </div>
                                <form method="post" action="<?= site_url('admin/delete-user') ?>" onsubmit="return confirm('¿Eliminar este empleado?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="dev-btn-icon" title="Eliminar">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                            <div class="dev-table-card-meta">
                                <span>Creado: <?= esc($u['created']) ?></span>
                                <span>Login: <?= esc($u['last_login'] ?? 'Nunca') ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ═══════ ASISTENCIA ═══════ -->
            <section class="dev-card" style="margin-top: 12px;">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">calendar_month</span> Registro de asistencia</h2>
                    <span class="dev-badge dev-badge-blue"><?= count($attendance) ?> registros</span>
                </div>
                <?php if (empty($attendance)): ?>
                    <div class="dev-empty">
                        <span class="material-symbols-outlined">calendar_month</span>
                        No hay registros de asistencia aún.
                    </div>
                <?php else: ?>
                    <div class="dev-table-wrap">
                        <table class="dev-table">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>DNI</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($attendance) as $a): ?>
                                <tr>
                                    <td>
                                        <div class="dev-table-user">
                                            <div class="dev-table-avatar"><?= strtoupper(substr($a['name'] ?? '??', 0, 2)) ?></div>
                                            <span><?= esc($a['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="dev-table-mono"><?= esc($a['dni'] ?? '-') ?></span></td>
                                    <td><?= esc($a['date']) ?></td>
                                    <td><span class="dev-table-mono"><?= esc($a['time']) ?></span></td>
                                    <td>
                                        <?php if ($a['status'] === 'present'): ?>
                                            <span class="dev-badge dev-badge-green"><span class="material-symbols-outlined filled">check_circle</span> Puntual</span>
                                        <?php else: ?>
                                            <span class="dev-badge dev-badge-amber"><span class="material-symbols-outlined">schedule</span> Tardanza</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile cards -->
                    <div class="dev-table-mobile">
                        <?php foreach (array_reverse($attendance) as $a): ?>
                        <div class="dev-table-card">
                            <div class="dev-table-card-header">
                                <div class="dev-table-user">
                                    <div class="dev-table-avatar"><?= strtoupper(substr($a['name'] ?? '??', 0, 2)) ?></div>
                                    <div>
                                        <div class="dev-table-card-title"><?= esc($a['name']) ?></div>
                                        <div class="dev-table-card-sub">DNI <?= esc($a['dni'] ?? '-') ?></div>
                                    </div>
                                </div>
                                <?php if ($a['status'] === 'present'): ?>
                                    <span class="dev-badge dev-badge-green"><span class="material-symbols-outlined filled">check_circle</span> Puntual</span>
                                <?php else: ?>
                                    <span class="dev-badge dev-badge-amber"><span class="material-symbols-outlined">schedule</span> Tardanza</span>
                                <?php endif; ?>
                            </div>
                            <div class="dev-table-card-meta">
                                <span><?= esc($a['date']) ?></span>
                                <span><?= esc($a['time']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ═══════ CÓDIGOS RECIENTES ═══════ -->
            <?php if (!empty($codes)): ?>
            <section class="dev-card" style="margin-top: 12px;">
                <div class="dev-card-head">
                    <h2><span class="material-symbols-outlined">vpn_key</span> Códigos recientes</h2>
                </div>
                <div class="dev-recent-list">
                    <?php foreach (array_slice($codes, -5, 5, true) as $c): ?>
                    <div class="dev-recent-item">
                        <code class="dev-code"><?= esc($c['code']) ?></code>
                        <div class="dev-recent-info">
                            <div class="dev-recent-name"><?= esc($c['name']) ?></div>
                            <div class="dev-recent-meta">DNI <?= esc($c['dni']) ?></div>
                        </div>
                        <div class="dev-recent-right">
                            <?php if ($c['status'] === 'active'): ?>
                                <span class="dev-status is-active"><span class="material-symbols-outlined filled">circle</span> Activo</span>
                            <?php else: ?>
                                <span class="dev-status is-used"><span class="material-symbols-outlined">circle</span> Usado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('.dev-code').forEach(function (el) {
    el.title = 'Clic para copiar';
    el.addEventListener('click', function () {
        navigator.clipboard.writeText(el.textContent).then(function () {
            var o = el.textContent;
            el.textContent = '¡Copiado!';
            setTimeout(function () { el.textContent = o; }, 1200);
        });
    });
});
</script>
</body>
</html>
