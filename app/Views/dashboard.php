<?php
$esAdmin            = (int) session()->get('id_rol') === 1;
$totalClientes      = (int) ($totalClientes ?? 0);
$nuevosClientes     = (int) ($nuevosClientes ?? 0);
$totalEmpresas      = (int) ($totalEmpresas ?? 0);
$totalSucursales    = (int) ($totalSucursales ?? 0);
$totalPlanesActivos = (int) ($totalPlanesActivos ?? 0);
$totalPlanes        = (int) ($totalPlanes ?? 0);
$totalTipoPlan      = (int) ($totalTipoPlan ?? 0);
$totalUsuarios      = (int) ($totalUsuarios ?? 0);
$planesProximos     = (int) ($planesProximos ?? 0);
$planesVencidos     = (int) ($planesVencidos ?? 0);
$hayAlertasPlan     = ($planesProximos + $planesVencidos) > 0;
$pctNuevos          = $totalClientes > 0 ? round($nuevosClientes / $totalClientes * 100, 1) : 0;
$mesesEs            = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$nombreSesion = trim((string) (session()->get('nombre') ?? session()->get('username') ?? 'Usuario'));
$primerNombre = explode(' ', $nombreSesion)[0] ?: $nombreSesion;
$horaActual   = (int) date('G');
$saludo       = match (true) {
    $horaActual >= 6 && $horaActual < 12  => 'Buenos días',
    $horaActual >= 12 && $horaActual < 19 => 'Buenas tardes',
    default                               => 'Buenas noches',
};

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

<style>
    .tk-plan-track-head-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tk-plan-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
    }

    .tk-plan-badge .material-symbols-outlined {
        font-size: 15px;
    }

    .tk-plan-badge-warn {
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
    }

    .tk-plan-badge-ok {
        background: rgba(22, 163, 74, 0.12);
        color: #15803d;
    }

    .tk-plan-track-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        padding: 0 20px 20px;
    }

    .tk-plan-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(248, 250, 252, 0.85);
        text-decoration: none;
        color: inherit;
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .tk-plan-metric:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        color: inherit;
    }

    .tk-plan-metric-warn:hover {
        border-color: rgba(245, 158, 11, 0.45);
    }

    .tk-plan-metric-danger:hover {
        border-color: rgba(239, 68, 68, 0.45);
    }

    .tk-plan-metric-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .tk-plan-metric-ico {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tk-plan-metric-ico .material-symbols-outlined {
        font-size: 21px;
    }

    .tk-plan-metric-warn .tk-plan-metric-ico {
        background: rgba(245, 158, 11, 0.14);
        color: #d97706;
    }

    .tk-plan-metric-danger .tk-plan-metric-ico {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .tk-plan-metric-lbl {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: inherit;
    }

    .tk-plan-metric-sub {
        margin: 2px 0 0;
        font-size: 12px;
        opacity: 0.68;
    }

    .tk-plan-metric-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .tk-plan-metric-val {
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .tk-plan-metric-warn .tk-plan-metric-val {
        color: #b45309;
    }

    .tk-plan-metric-danger .tk-plan-metric-val {
        color: #dc2626;
    }

    .tk-plan-metric-go {
        font-size: 18px;
        opacity: 0.45;
    }

    html.dark .tk-plan-metric {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
    }

    html.dark .tk-plan-badge-warn {
        background: rgba(245, 158, 11, 0.18);
        color: #fbbf24;
    }

    html.dark .tk-plan-badge-ok {
        background: rgba(34, 197, 94, 0.18);
        color: #4ade80;
    }

    html.dark .tk-plan-metric-warn .tk-plan-metric-ico,
    html.dark .tk-plan-metric-warn .tk-plan-metric-val {
        color: #fbbf24;
    }

    html.dark .tk-plan-metric-danger .tk-plan-metric-ico,
    html.dark .tk-plan-metric-danger .tk-plan-metric-val {
        color: #f87171;
    }

    @media (max-width: 640px) {
        .tk-plan-track-grid {
            grid-template-columns: 1fr;
            padding: 0 16px 16px;
        }
    }
</style>

<?php $flashMsg = session()->getFlashdata('msg'); ?>
<?php $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
<?php if (!empty($flashMsg)): ?>
    <div class="dev-alert dev-alert-<?= esc($flashTipo) ?>">
        <span class="material-symbols-outlined filled"><?= $flashTipo === 'success' ? 'check_circle' : ($flashTipo === 'warning' ? 'warning' : 'error') ?></span>
        <span><?= esc($flashMsg) ?></span>
    </div>
<?php endif; ?>

<div class="tk-dash tk-sys">

    <!-- Hero de bienvenida -->
    <section class="tk-dash-hero" aria-labelledby="dashHeroTitle">
        <div class="tk-dash-hero-copy">
            <span class="tk-dash-hero-eyebrow">
                <span class="material-symbols-outlined" aria-hidden="true">space_dashboard</span>
                Panel de control · <?= esc(ucfirst($mesesEs[(int) date('n')])) ?> <?= date('Y') ?>
            </span>
            <h1 id="dashHeroTitle"><?= esc($saludo) ?>, <?= esc($primerNombre) ?></h1>
            <p>Resumen de clientes, empresas, sucursales y planes de DSG Perú. Todo lo importante, en un solo lugar.</p>
            <div class="tk-dash-hero-actions">
                <a href="<?= base_url('personas') ?>" class="tk2-btn tk2-btn-ghost">
                    <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                    <span>Ver clientes</span>
                </a>
                <a href="<?= base_url('personasadd') ?>" class="tk2-btn tk2-btn-primary">
                    <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                    <span>Registrar persona</span>
                </a>
            </div>
        </div>
        <div class="tk-dash-hero-art" aria-hidden="true">
            <span class="tk-dash-orbit tk-dash-orbit-1"></span>
            <span class="tk-dash-orbit tk-dash-orbit-2"></span>
            <span class="material-symbols-outlined tk-dash-hero-icon">monitoring</span>
        </div>
    </section>

    <!-- KPIs (StatGrid) -->
    <section class="tk-stats <?= $esAdmin ? 'tk-stats-5' : 'tk-stats-4' ?>">
        <a class="tk-stat" href="<?= base_url('personas') ?>">
            <span class="material-symbols-outlined tk-stat-ico">groups</span>
            <div><p class="tk-stat-lbl">Clientes registrados</p><p class="tk-stat-val"><?= number_format($totalClientes) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('personas') ?>">
            <span class="material-symbols-outlined tk-stat-ico">apartment</span>
            <div><p class="tk-stat-lbl">Empresas</p><p class="tk-stat-val"><?= number_format($totalEmpresas) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('personas') ?>">
            <span class="material-symbols-outlined tk-stat-ico">store</span>
            <div><p class="tk-stat-lbl">Sucursales</p><p class="tk-stat-val"><?= number_format($totalSucursales) ?></p></div>
        </a>
        <a class="tk-stat" href="<?= base_url('planes') ?>">
            <span class="material-symbols-outlined tk-stat-ico">workspace_premium</span>
            <div><p class="tk-stat-lbl">Planes activos</p><p class="tk-stat-val"><?= number_format($totalPlanesActivos) ?></p></div>
        </a>
        <?php if ($esAdmin): ?>
        <a class="tk-stat" href="<?= base_url('usuarios') ?>">
            <span class="material-symbols-outlined tk-stat-ico">manage_accounts</span>
            <div><p class="tk-stat-lbl">Usuarios</p><p class="tk-stat-val"><?= number_format($totalUsuarios) ?></p></div>
        </a>
        <?php endif; ?>
    </section>

    <!-- Seguimiento de planes -->
    <section class="tk-card tk-ovh tk-anim-item" aria-label="Seguimiento de planes">
        <div class="tk-card-head">
            <div>
                <h3>Seguimiento de planes</h3>
                <p>Control de vencimientos próximos y planes vencidos.</p>
            </div>
            <div class="tk-plan-track-head-right">
                <span class="tk-plan-badge <?= $hayAlertasPlan ? 'tk-plan-badge-warn' : 'tk-plan-badge-ok' ?>">
                    <span class="material-symbols-outlined" aria-hidden="true"><?= $hayAlertasPlan ? 'warning' : 'check_circle' ?></span>
                    <?= $hayAlertasPlan ? 'Atención' : 'Al día' ?>
                </span>
            </div>
        </div>
        <div class="tk-plan-track-grid">
            <a class="tk-plan-metric tk-plan-metric-warn" href="<?= base_url('notificaciones') ?>">
                <div class="tk-plan-metric-left">
                    <span class="tk-plan-metric-ico" aria-hidden="true">
                        <span class="material-symbols-outlined">schedule</span>
                    </span>
                    <div>
                        <p class="tk-plan-metric-lbl">Próximos a vencer</p>
                        <p class="tk-plan-metric-sub">Vencen hoy o en los próximos 7 días</p>
                    </div>
                </div>
                <div class="tk-plan-metric-right">
                    <span class="tk-plan-metric-val"><?= number_format($planesProximos) ?></span>
                    <span class="material-symbols-outlined tk-plan-metric-go" aria-hidden="true">chevron_right</span>
                </div>
            </a>

            <a class="tk-plan-metric tk-plan-metric-danger" href="<?= base_url('notificaciones') ?>">
                <div class="tk-plan-metric-left">
                    <span class="tk-plan-metric-ico" aria-hidden="true">
                        <span class="material-symbols-outlined">event_busy</span>
                    </span>
                    <div>
                        <p class="tk-plan-metric-lbl">Planes vencidos</p>
                        <p class="tk-plan-metric-sub">Requieren renovación o verificación</p>
                    </div>
                </div>
                <div class="tk-plan-metric-right">
                    <span class="tk-plan-metric-val"><?= number_format($planesVencidos) ?></span>
                    <span class="material-symbols-outlined tk-plan-metric-go" aria-hidden="true">chevron_right</span>
                </div>
            </a>
        </div>
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
                    <span>Empresas y sucursales</span>
                    <span><?= number_format($totalEmpresas) ?> empresas · <?= number_format($totalSucursales) ?> sucursales</span>
                </div>
                <div class="tk-sys-row">
                    <span>Planes</span>
                    <span><?= number_format($totalPlanesActivos) ?> activos · <?= number_format($totalPlanes) ?> disponibles</span>
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
    requestAnimationFrame(function () {
        document.querySelectorAll('.tk-sys-split').forEach(function (el) {
            el.classList.add('tk-ready');
        });
    });

    if (typeof showToast !== 'function') return;

    var mostrarBienvenida = <?= !empty($mostrarToastBienvenida) ? 'true' : 'false' ?>;
    var mostrarProximos   = <?= !empty($mostrarToastProximos) ? 'true' : 'false' ?>;
    var mostrarVencidos   = <?= !empty($mostrarToastVencidos) ? 'true' : 'false' ?>;
    var planesProximos    = <?= (int) $planesProximos ?>;
    var planesVencidos    = <?= (int) $planesVencidos ?>;

    var colaToasts = [];

    if (mostrarBienvenida) {
        colaToasts.push({
            titulo: 'Bienvenido',
            mensaje: 'Panel de administración sincronizado.',
            tipo: 'success'
        });
    }

    if (mostrarProximos && planesProximos > 0) {
        var txtProx = planesProximos === 1
            ? 'Tienes 1 plan próximo a vencer.'
            : 'Tienes ' + planesProximos + ' planes próximos a vencer.';
        colaToasts.push({
            titulo: 'Atención',
            mensaje: txtProx,
            tipo: 'warning'
        });
    }

    if (mostrarVencidos && planesVencidos > 0) {
        var txtVenc = planesVencidos === 1
            ? 'Existe 1 plan vencido pendiente de renovación.'
            : 'Existen ' + planesVencidos + ' planes vencidos pendientes de renovación.';
        colaToasts.push({
            titulo: 'Planes vencidos',
            mensaje: txtVenc,
            tipo: 'danger'
        });
    }

    colaToasts.forEach(function (toast, index) {
        setTimeout(function () {
            showToast(toast.titulo, toast.mensaje, toast.tipo);
        }, 400 + (index * 700));
    });
});
</script>