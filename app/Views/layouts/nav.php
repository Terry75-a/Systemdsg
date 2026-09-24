<?php $seg = strtolower(service('uri')->getSegment(1) ?? ''); ?>
<header class="nav-shell">
    <div class="nav-progress" aria-hidden="true"><span id="navProgress"></span></div>

    <nav class="navbar-stripe" id="dsgNav" aria-label="Navegación principal">
        <a href="<?= base_url('/') ?>" class="nav-logo" aria-label="DSG Perú - Inicio">
            <span class="nav-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
            <span class="nav-logo-text">DSG Perú<small>TECHNOLOGY</small></span>
        </a>

        <ul class="nav-center" id="navLinks">
            <li><a href="<?= base_url('/') ?>" class="link<?= $seg === '' ? ' is-active' : '' ?>"<?= $seg === '' ? ' aria-current="page"' : '' ?>>Inicio</a></li>
            <li><a href="<?= base_url('servicios') ?>" class="link<?= $seg === 'servicios' ? ' is-active' : '' ?>"<?= $seg === 'servicios' ? ' aria-current="page"' : '' ?>>Servicios</a></li>
            <li><a href="<?= base_url('dsg') ?>" class="link<?= $seg === 'dsg' ? ' is-active' : '' ?>"<?= $seg === 'dsg' ? ' aria-current="page"' : '' ?>>DSG</a></li>
            <li><a href="<?= base_url('precio') ?>" class="link<?= $seg === 'precio' || $seg === 'precios' ? ' is-active' : '' ?>"<?= ($seg === 'precio' || $seg === 'precios') ? ' aria-current="page"' : '' ?>>Precios</a></li>
        </ul>

        <div class="nav-right">
            <a href="<?= base_url('login') ?>" class="btn-sign-in"><i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i> Iniciar sesión</a>
            <a href="<?= base_url('/#contacto') ?>" class="btn-contact-sales">
                <span>Solicitar demo</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <button class="mobile-toggle" id="mobile-menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="navMobile">
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <div class="nav-mobile" id="navMobile" hidden>
        <ul class="nav-mobile-links">
            <li><a href="<?= base_url('/') ?>" class="link<?= $seg === '' ? ' is-active' : '' ?>">Inicio</a></li>
            <li><a href="<?= base_url('servicios') ?>" class="link<?= $seg === 'servicios' ? ' is-active' : '' ?>">Servicios</a></li>
            <li><a href="<?= base_url('dsg') ?>" class="link<?= $seg === 'dsg' ? ' is-active' : '' ?>">DSG</a></li>
            <li><a href="<?= base_url('precio') ?>" class="link<?= $seg === 'precio' || $seg === 'precios' ? ' is-active' : '' ?>">Precios</a></li>
        </ul>
        <div class="nav-mobile-actions">
            <a href="<?= base_url('/#contacto') ?>" class="btn-contact-sales">
                <span>Solicitar demo</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="<?= base_url('login') ?>" class="btn-sign-in"><i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i> Iniciar sesión</a>
        </div>
    </div>
</header>
