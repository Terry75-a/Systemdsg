<?php
$esAdmin        = (int) session()->get('id_rol') === 1;
$totalClientes  = (int) ($totalClientes ?? 0);
$nuevosClientes = (int) ($nuevosClientes ?? 0);
$totalPlanes    = (int) ($totalPlanes ?? 0);
$totalTipoPlan  = (int) ($totalTipoPlan ?? 0);
$totalUsuarios  = (int) ($totalUsuarios ?? 0);
$pctNuevos      = $totalClientes > 0 ? round($nuevosClientes / $totalClientes * 100, 1) : 0;
$mesesEs        = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$accesos = [
    ['personas',     'groups',          'Clientes',          'Gestión de personas'],
    ['personasadd',  'person_add',      'Registrar persona', 'Nuevo cliente'],
    ['planes',       'sell',            'Planes',            $totalPlanes . ' disponibles'],
    ['tipo_planadd', 'category',        'Tipo de plan',      $totalTipoPlan . ' categorías'],
    ['calendario',   'calendar_month',  'Calendario',        'Fechas y eventos'],
];
if ($esAdmin) {
    $accesos[] = ['usuarios',      'manage_accounts', 'Usuarios',      $totalUsuarios . ' cuentas'];
    $accesos[] = ['configuracion', 'settings',        'Configuración', 'Ajustes del sistema'];
}
$accesos[] = ['perfil', 'person', 'Mi perfil', 'Mis datos'];
if (!$esAdmin) {
    $accesos[] = ['', 'language', 'Sitio web', 'Página principal'];
}
?>

<?php $flashMsg = session()->getFlashdata('msg'); ?>
<?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
<?php if (!empty($flashMsg)): ?>
    <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
        <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
        <span><?= esc($flashMsg) ?></span>
    </div>
<?php endif; ?>

<div class="tk-dash tk-sys">

    <!-- PageTitle -->
    <div class="tk-title">
        <div>
            <h1>Panel de control</h1>
            <p>Resumen de clientes, planes y accesos de DSG Perú · <?= esc(ucfirst($mesesEs[(int) date('n')])) ?> <?= date('Y') ?>.</p>
        </div>
        <div class="tk-title-actions">
            <a href="<?= base_url('personas') ?>" class="dev-btn dev-btn-sm">
                <span class="material-symbols-outlined">groups</span> Ver clientes
            </a>
            <a href="<?= base_url('personasadd') ?>" class="dev-btn dev-btn-primary dev-btn-sm">
                <span class="material-symbols-outlined">person_add</span> Registrar persona
            </a>
        </div>
    </div>

    <!-- KPIs (StatGrid) -->
    <section class="tk-stats <?= $esAdmin ? 'tk-stats-5' : 'tk-stats-4' ?>">
        <a class="tk-stat" href="<?= base_url('personas') ?>">
            <span class="material-symbols-outlined tk-stat-ico">groups</span>
            <div><p class="tk-stat-lbl">Clientes registrados</p><p class="tk-stat-val"><?= number_format($totalClientes) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('personas') ?>">
            <span class="material-symbols-outlined tk-stat-ico">person_add</span>
            <div><p class="tk-stat-lbl">Nuevos este mes</p><p class="tk-stat-val"><?= number_format($nuevosClientes) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('planes') ?>">
            <span class="material-symbols-outlined tk-stat-ico">sell</span>
            <div><p class="tk-stat-lbl">Planes disponibles</p><p class="tk-stat-val"><?= number_format($totalPlanes) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('tipo_planadd') ?>">
            <span class="material-symbols-outlined tk-stat-ico">category</span>
            <div><p class="tk-stat-lbl">Tipos de plan</p><p class="tk-stat-val"><?= number_format($totalTipoPlan) ?></p></div>
        </a>
        <?php if ($esAdmin): ?>
        <a class="tk-stat" href="<?= base_url('usuarios') ?>">
            <span class="material-symbols-outlined tk-stat-ico">manage_accounts</span>
            <div><p class="tk-stat-lbl">Usuarios</p><p class="tk-stat-val"><?= number_format($totalUsuarios) ?></p></div>
        </a>
        <?php endif; ?>
    </section>

    <div class="tk-grid-2">
        <!-- Accesos rápidos -->
        <section class="tk-card tk-ovh tk-anim-item">
            <div class="tk-card-head">
                <div><h3>Accesos rápidos</h3><p>Los módulos que más usas.</p></div>
            </div>
            <div class="tk-sys-links">
                <?php foreach ($accesos as $i => [$url, $ico, $lbl, $sub]): ?>
                    <a class="tk-sys-link" href="<?= base_url($url) ?>" style="--i: <?= $i ?>">
                        <span class="tk-sys-link-ico"><span class="material-symbols-outlined"><?= $ico ?></span></span>
                        <span class="tk-sys-link-txt"><b><?= esc($lbl) ?></b><span><?= esc($sub) ?></span></span>
                        <span class="material-symbols-outlined tk-sys-link-go">chevron_right</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Estado del panel -->
        <section class="tk-card tk-ovh tk-anim-item">
            <div class="tk-card-head">
                <div><h3>Estado del panel</h3><p>Cómo están tus módulos hoy.</p></div>
                <span class="tk-pill-state tk-st-on">En línea</span>
            </div>
            <div class="tk-p5">
                <p class="tk-cap">Clientes nuevos este mes</p>
                <div class="tk-split tk-sys-split">
                    <div class="tk-split-track">
                        <?php if ($totalClientes > 0): ?>
                            <div class="tk-split-on" style="--w: <?= $pctNuevos ?>%"></div>
                            <div class="tk-split-late tk-sys-rest"></div>
                        <?php endif; ?>
                    </div>
                    <div class="tk-split-lbl">
                        <span><i class="tk-dot tk-dot-on"></i><?= $nuevosClientes ?> nuevos (<?= $pctNuevos ?>%)</span>
                        <span><?= $totalClientes - $nuevosClientes ?> anteriores</span>
                    </div>
                </div>
            </div>
            <div class="tk-sys-rows">
                <div class="tk-sys-row">
                    <span>Clientes y personas</span>
                    <span><?= number_format($totalClientes) ?> registrados · <?= number_format($nuevosClientes) ?> este mes</span>
                </div>
                <div class="tk-sys-row">
                    <span>Mantenedor</span>
                    <span><?= number_format($totalPlanes) ?> planes · <?= number_format($totalTipoPlan) ?> tipos</span>
                </div>
                <?php if ($esAdmin): ?>
                <div class="tk-sys-row">
                    <span>Accesos</span>
                    <span><?= number_format($totalUsuarios) ?> cuentas de usuario</span>
                </div>
                <?php else: ?>
                <div class="tk-sys-row">
                    <span>Calendario</span>
                    <span>Fechas y eventos</span>
                </div>
                <?php endif; ?>
                <div class="tk-sys-row">
                    <span>Sesión</span>
                    <span><span class="tk-chip-soft tk-chip-ok">Activa</span></span>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Barra de clientes nuevos: anima al cargar
    requestAnimationFrame(function () {
        document.querySelectorAll('.tk-sys-split').forEach(function (el) { el.classList.add('tk-ready'); });
    });

    // Notificaciones que salen al entrar / recargar la página
    setTimeout(function() {
        showToast('Bienvenido', 'Panel de administración sincronizado.', 'success');
    }, 400);

    setTimeout(function() {
        showToast('Atención', 'Tienes 2 planes próximos a vencer este mes.', 'warning');
    }, 1200);

    setTimeout(function() {
        showToast('Pago pendiente', 'Existe 1 pago pendiente de verificación.', 'danger');
    }, 2000);
});
</script>
