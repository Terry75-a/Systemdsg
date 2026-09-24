<?php
$uri = service('uri');
$seg = (string) $uri->getSegment(1);
$isAdmin = (int) session()->get('id_rol') === 1;
$uname = session()->get('username') ?? 'Usuario';
$ini = strtoupper(substr(trim((string) $uname), 0, 2) ?: 'US');
$nombre = session()->get('nombre') ?? $uname;
$hora = (int) date('G');
$saludo = match (true) {
    $hora >= 6 && $hora < 12 => 'Buenos días',
    $hora >= 12 && $hora < 19 => 'Buenas tardes',
    default => 'Buenas noches',
};

$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fecha = $meses[(int) date('n')] . ' ' . date('j') . ', ' . date('Y');
?>

<div class="dev-main">
    <div class="dsg-page">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div class="dev-topbar-actions">
                    <button type="button" class="dsg-dark-btn" id="dsgDarkBtn" title="Modo oscuro" aria-label="Alternar modo oscuro">
                        <span class="material-symbols-outlined" id="dsgDarkIcon">dark_mode</span>
                    </button>
                    <div class="dsg-user-chip">
                        <button type="button" class="dsg-user-btn" aria-haspopup="true" aria-expanded="false">
                            <span class="dsg-user-avatar"><?= esc($ini) ?></span>
                            <span class="dsg-user-meta">
                                <span class="dsg-user-name"><?= esc($nombre) ?></span>
                                <span class="dsg-user-role"><?= $isAdmin ? 'Administrador' : 'Usuario' ?></span>
                            </span>
                            <span class="material-symbols-outlined dsg-user-chevron">expand_more</span>
                        </button>
                        <div class="dsg-user-menu">
                            <a href="<?= base_url('perfil') ?>" class="dsg-user-menu-link">
                                <span class="material-symbols-outlined">person</span> Mi Perfil
                            </a>
                            <?php if ($isAdmin): ?>
                                <a href="<?= base_url('configuracion') ?>" class="dsg-user-menu-link">
                                    <span class="material-symbols-outlined">settings</span> Configuración
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('') ?>" class="dsg-user-menu-link">
                                <span class="material-symbols-outlined">language</span> Sitio web
                            </a>
                            <div class="dsg-user-menu-sep"></div>
                            <form method="post" action="<?= base_url('logout') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="dsg-user-menu-link dsg-user-menu-logout">
                                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dev-content">