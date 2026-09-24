<?php
$esAdmin = (int) session()->get('id_rol') === 1;
$totalClientes = (int) ($totalClientes ?? 0);
$nuevosClientes = (int) ($nuevosClientes ?? 0);
$totalPlanes = (int) ($totalPlanes ?? 0);
$totalTipoPlan = (int) ($totalTipoPlan ?? 0);
$totalUsuarios = (int) ($totalUsuarios ?? 0);

$uname = session()->get('username') ?? 'Usuario';
$nombre = session()->get('nombre') ?? $uname;
$hora = (int) date('G');
$saludo = match (true) {
    $hora >= 6 && $hora < 12 => 'Buenos días',
    $hora >= 12 && $hora < 19 => 'Buenas tardes',
    default => 'Buenas noches',
};
?>

<!-- Saludo del panel -->
<div class="dash-greeting">
    <h1 class="dash-greeting-title"><?= esc($saludo) ?>, <?= esc($nombre) ?></h1>
    <p class="dash-greeting-sub">Panel de Administración DSG Perú</p>
</div>

<?php $flashMsg = session()->getFlashdata('msg'); ?>
<?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
<?php if (!empty($flashMsg)): ?>
    <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
        <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
        <span><?= esc($flashMsg) ?></span>
    </div>
<?php endif; ?>

<!-- Stats -->
<section class="dev-stats-grid">
    <div class="dev-stat-card">
        <div class="dev-stat-icon si-accent"><span class="material-symbols-outlined filled">groups</span></div>
        <div class="dev-stat-label">Clientes registrados</div>
        <div class="dev-stat-value"><?= esc((string) $totalClientes) ?></div>
    </div>
    <div class="dev-stat-card">
        <div class="dev-stat-icon si-green"><span class="material-symbols-outlined filled">person_added</span></div>
        <div class="dev-stat-label">Nuevos este mes</div>
        <div class="dev-stat-value"><?= esc((string) $nuevosClientes) ?></div>
    </div>
    <div class="dev-stat-card">
        <div class="dev-stat-icon si-amber"><span class="material-symbols-outlined filled">sell</span></div>
        <div class="dev-stat-label">Planes disponibles</div>
        <div class="dev-stat-value"><?= esc((string) $totalPlanes) ?></div>
    </div>
    <div class="dev-stat-card">
        <div class="dev-stat-icon si-red"><span class="material-symbols-outlined filled">category</span></div>
        <div class="dev-stat-label">Tipos de plan</div>
        <div class="dev-stat-value"><?= esc((string) $totalTipoPlan) ?></div>
    </div>
</section>

<div class="dev-grid-2">
    <!-- Accesos / Módulos -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">apps</span> Accesos rápidos</h2>
        </div>
        <div style="padding: 4px 16px 12px;">
            <div class="dash-link-grid">
                <a href="<?= base_url('personas') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-success-light);color:var(--g-success);"><span class="material-symbols-outlined">groups</span></div>
                    <div class="dash-link-text">Clientes<span>Gestión de personas</span></div>
                </a>
                <a href="<?= base_url('personasadd') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">person_add</span></div>
                    <div class="dash-link-text">Registrar persona<span>Nuevo cliente</span></div>
                </a>
                <a href="<?= base_url('planes') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-warning-light);color:#e37400;"><span class="material-symbols-outlined">sell</span></div>
                    <div class="dash-link-text">Planes<span><?= esc((string) $totalPlanes) ?> disponibles</span></div>
                </a>
                <a href="<?= base_url('tipo_planadd') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-error-light);color:var(--g-error);"><span class="material-symbols-outlined">category</span></div>
                    <div class="dash-link-text">Tipo Plan<span><?= esc((string) $totalTipoPlan) ?> categorías</span></div>
                </a>
                <a href="<?= base_url('calendario') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">calendar_month</span></div>
                    <div class="dash-link-text">Calendario<span>Fechas y eventos</span></div>
                </a>
                <?php if ($esAdmin): ?>
                <a href="<?= base_url('usuarios') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);"><span class="material-symbols-outlined">manage_accounts</span></div>
                    <div class="dash-link-text">Usuarios<span><?= esc((string) $totalUsuarios) ?> cuentas</span></div>
                </a>
                <a href="<?= base_url('configuracion') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">settings</span></div>
                    <div class="dash-link-text">Configuración<span>Ajustes</span></div>
                </a>
                <a href="<?= base_url('perfil') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-success-light);color:var(--g-success);"><span class="material-symbols-outlined">person</span></div>
                    <div class="dash-link-text">Mi Perfil<span>Mis datos</span></div>
                </a>
                <?php else: ?>
                <a href="<?= base_url('perfil') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-surface-variant);color:var(--g-text-secondary);"><span class="material-symbols-outlined">person</span></div>
                    <div class="dash-link-text">Mi Perfil<span>Mis datos</span></div>
                </a>
                <a href="<?= base_url('') ?>" class="dash-link">
                    <div class="dash-link-icon" style="background:var(--g-primary-light);color:var(--g-primary);"><span class="material-symbols-outlined">language</span></div>
                    <div class="dash-link-text">Sitio web<span>Página principal</span></div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estado del panel -->
    <div class="dev-card">
        <div class="dev-card-head">
            <h2><span class="material-symbols-outlined">assessment</span> Estado del panel</h2>
        </div>
        <div style="padding: 8px 16px 12px;">
            <div class="dash-activity-item">
                <div class="dash-activity-dot dot-green"></div>
                <div class="dash-activity-info">
                    <div class="dash-activity-name">Clientes y Personas</div>
                    <div class="dash-activity-meta"><?= esc((string) $totalClientes) ?> registrados · <?= esc((string) $nuevosClientes) ?> este mes</div>
                </div>
            </div>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dot-amber"></div>
                <div class="dash-activity-info">
                    <div class="dash-activity-name">Mantenedor</div>
                    <div class="dash-activity-meta"><?= esc((string) $totalPlanes) ?> planes · <?= esc((string) $totalTipoPlan) ?> tipos de plan</div>
                </div>
            </div>
            <?php if ($esAdmin): ?>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dot-red"></div>
                <div class="dash-activity-info">
                    <div class="dash-activity-name">Accesos</div>
                    <div class="dash-activity-meta"><?= esc((string) $totalUsuarios) ?> cuentas de usuario</div>
                </div>
            </div>
            <?php else: ?>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dot-green"></div>
                <div class="dash-activity-info">
                    <div class="dash-activity-name">Calendario</div>
                    <div class="dash-activity-meta">Gestión visual de fechas y eventos</div>
                </div>
            </div>
            <?php endif; ?>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dot-gray"></div>
                <div class="dash-activity-info">
                    <div class="dash-activity-name">Sesión activa</div>
                    <div class="dash-activity-meta">Perfil y datos verificados</div>
                </div>
            </div>
        </div>
    </div>
</div>