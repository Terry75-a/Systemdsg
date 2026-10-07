<?php $seg = strtolower(service('uri')->getSegment(1) ?? ''); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software especializado para restaurantes, boticas y minimarkets. Soluciones tecnologicas que impulsan tu negocio.">
    <title><?= esc($titulo ?? 'DSG PERU TECHNOLOGY') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/daybase-home.css?v=' . filemtime(FCPATH . 'css/index/components/daybase-home.css')) ?>">
</head>

<body class="db-page">

<header class="db-navwrap">
    <nav class="db-nav" aria-label="Navegación principal">
        <a href="<?= base_url('/') ?>" class="db-logo">
            <img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Perú">
            <span>DSG Perú<small>TECHNOLOGY</small></span>
        </a>

        <ul class="db-nav-links">
            <li><a href="<?= base_url('/') ?>" class="<?= $seg === '' ? 'is-active' : '' ?>">Inicio</a></li>
            <li><a href="<?= base_url('servicios') ?>" class="<?= $seg === 'servicios' ? 'is-active' : '' ?>">Servicios</a></li>
            <li><a href="<?= base_url('dsg') ?>" class="<?= $seg === 'dsg' ? 'is-active' : '' ?>">DSG</a></li>
            <li><a href="<?= base_url('precio') ?>" class="<?= $seg === 'precio' ? 'is-active' : '' ?>">Precios</a></li>
        </ul>

        <div class="db-nav-cta">
            <a href="<?= base_url('login') ?>" class="db-login">Iniciar sesión</a>
            <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-white">Solicitar demo</a>
            <button class="db-burger" id="dbBurger" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="dbMobile">
                <span></span><span></span>
            </button>
        </div>
    </nav>

    <div class="db-mobile" id="dbMobile" hidden>
        <a href="<?= base_url('/') ?>">Inicio</a>
        <a href="<?= base_url('servicios') ?>">Servicios</a>
        <a href="<?= base_url('dsg') ?>">DSG</a>
        <a href="<?= base_url('precio') ?>">Precios</a>
        <a href="<?= base_url('login') ?>">Iniciar sesión</a>
        <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-lilac">Solicitar demo</a>
    </div>
</header>

<?= $slot ?>

<footer class="db-foot">
    <div class="db-wrap">
        <div class="db-foot-top">
            <div class="db-foot-brand">
                <img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo">
                <span class="name">DSG Peru</span>
                <span class="slogan">Software inteligente para negocios que crecen.</span>
            </div>

            <nav class="db-foot-nav">
                <a href="<?= base_url('servicios') ?>">Servicios</a>
                <a href="<?= base_url('precio') ?>">Precios</a>
                <a href="<?= base_url('/#contacto') ?>">Contacto</a>
            </nav>

            <div class="db-foot-social">
                <a href="https://www.facebook.com/dsgperu" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/dsgperu/" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <div class="db-foot-bottom">
            <p>© <?= date('Y') ?> DSG Peru Technology. Todos los derechos reservados.</p>
            <p><a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a></p>
        </div>
    </div>
</footer>

<script src="<?= base_url('js/contador.js') ?>"></script>
<script>
    (function () {
        var burger = document.getElementById('dbBurger');
        var panel = document.getElementById('dbMobile');
        if (!burger || !panel) return;

        function setOpen(open) {
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            burger.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
            panel.hidden = !open;
        }
        burger.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(panel.hidden);
        });
        panel.addEventListener('click', function (e) {
            if (e.target.closest('a')) setOpen(false);
        });
        document.addEventListener('click', function (e) {
            if (!panel.hidden && !panel.contains(e.target) && !burger.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) { setOpen(false); burger.focus(); }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900 && !panel.hidden) setOpen(false);
        });
    })();

    /* Ripple Material (estilo Google) en los botones */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.db-btn');
        if (!btn) return;
        var rect = btn.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height) * 2.4;
        var rip = document.createElement('span');
        rip.className = 'db-ripple';
        rip.style.width = rip.style.height = size + 'px';
        rip.style.left = (e.clientX - rect.left - size / 2) + 'px';
        rip.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(rip);
        rip.addEventListener('animationend', function () { rip.remove(); });
    });
</script>

</body>
</html>
