<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Crea tu cuenta en DSG Perú y accede a tu panel de control.">
    <title>Crear cuenta · DSG Perú Technology</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/index/components/login-asisten.css?v=20260922?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>

<!-- ═══════════ NAV PROPIO ═══════════ -->
<header class="asisten-nav-shell" id="asistenShell">
    <nav class="asisten-nav" aria-label="Navegación de DSG Perú">
        <a href="<?= base_url('asisten-dsg') ?>" class="asisten-logo" aria-label="DSG Perú - Inicio">
            <span class="asisten-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
            <span class="asisten-logo-text">DSG Perú<small>TECHNOLOGY</small></span>
        </a>
        <div class="asisten-nav-actions">
            <a href="<?= base_url('login-verde') ?>" class="asisten-link-site">Iniciar sesión</a>
            <a href="<?= base_url('/#contacto') ?>" class="btn-green btn-green-sm">Solicitar demo</a>
            <button class="asisten-toggle" id="asistenToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="asistenMobile">
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
    <div class="asisten-mobile" id="asistenMobile" hidden>
        <a href="<?= base_url('login-verde') ?>"><span>Iniciar sesión</span><span>→</span></a>
        <a href="<?= base_url('/#contacto') ?>" class="is-cta"><span>Solicitar demo</span><span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a>
    </div>
</header>

<div class="login-page">

    <!-- ═══════════ LADO IZQUIERDO: FORMULARIO ═══════════ -->
    <div class="login-left">
        <div class="login-left-inner">

            <div class="login-card reveal">
                <div class="login-card-head">
                    <div class="login-brand">
                        <span class="login-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
                        <span class="login-brand-text">DSG Perú<small>TECHNOLOGY</small></span>
                    </div>
                    <a href="<?= base_url('asisten-dsg') ?>" class="login-back" aria-label="Volver al inicio">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="login-eyebrow"><span class="pulse"></span> Nuevo usuario</div>
                <h1 class="login-title">Crea tu <span class="serif-accent">cuenta.</span></h1>
                <p class="login-sub">Regístrate para acceder a tu panel de control y gestionar tu negocio.</p>

                <?php
                $flashMsg = session()->getFlashdata('msg');
                $flashTipo = session()->getFlashdata('tipo') ?? 'danger';
                $flashClass = ($flashTipo === 'warning') ? 'alerta-warning' : (($flashTipo === 'success') ? 'alerta-success' : 'alerta-error');
                $flashIcon = ($flashTipo === 'warning') ? 'fa-triangle-exclamation' : (($flashTipo === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation');
                ?>
                <?php if (!empty($flashMsg)): ?>
                    <div class="<?= esc($flashClass) ?>" role="alert">
                        <i class="fa-solid <?= esc($flashIcon) ?>" aria-hidden="true"></i>
                        <span><?= esc($flashMsg) ?></span>
                    </div>
                <?php endif; ?>

                <form class="login-form" method="post" action="<?= site_url('auth/register') ?>" autocomplete="on">
                    <?= csrf_field() ?>

                    <div class="form-field">
                        <label for="name">Nombre completo</label>
                        <div class="field-wrap">
                            <i class="fa-regular fa-user field-icon" aria-hidden="true"></i>
                            <input type="text" id="name" name="name" placeholder="Juan Pérez"
                                   autocomplete="name" required value="<?= esc(old('name')) ?>">
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="email">Correo electrónico</label>
                        <div class="field-wrap">
                            <i class="fa-regular fa-envelope field-icon" aria-hidden="true"></i>
                            <input type="email" id="email" name="email" placeholder="tu@colegio.edu.pe"
                                   autocomplete="email" required value="<?= esc(old('email')) ?>">
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-lock field-icon" aria-hidden="true"></i>
                            <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres"
                                   autocomplete="new-password" required minlength="6">
                            <button type="button" id="togglePassword" class="field-eye" aria-label="Mostrar contraseña">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="password_confirm">Confirmar contraseña</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-lock field-icon" aria-hidden="true"></i>
                            <input type="password" id="password_confirm" name="password_confirm" placeholder="Repite tu contraseña"
                                   autocomplete="new-password" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Crear cuenta <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>

                    <div class="login-foot">
                        <a href="<?= base_url('login-verde') ?>" class="login-foot-link">¿Ya tienes cuenta? Inicia sesión</a>
                        <a href="<?= base_url('asisten-dsg') ?>" class="login-foot-link">← Volver al sitio principal</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ═══════════ LADO DERECHO: PANEL DE MARCA ═══════════ -->
    <div class="login-right">
        <div class="login-right-inner reveal">
            <span class="login-right-tag">DSG Perú Technology</span>
            <h2 class="login-right-title">Empieza a controlar <span class="serif-accent-white">tu negocio hoy.</span></h2>
            <p class="login-right-desc">Regístrate gratis y accede a todas las herramientas que necesitas para manejar ventas, inventario y reportes.</p>

            <ul class="login-right-list">
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Registro rápido en menos de 1 minuto</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Panel de control completo y en tiempo real</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Soporte técnico incluido</li>
            </ul>

            <div class="login-right-quote">
                <blockquote>"Crear mi cuenta fue lo más fácil. En 5 minutos ya estaba usando el sistema."</blockquote>
                <footer><strong>María Fernanda</strong> · Vifarma, Perú</footer>
            </div>
        </div>

        <p class="login-watermark" aria-hidden="true">DSG</p>
    </div>

</div>

<script>
(function () {
    var shell = document.getElementById('asistenShell');
    var toggle = document.getElementById('asistenToggle');
    var panel = document.getElementById('asistenMobile');
    function onScroll() { if (shell) shell.classList.toggle('scrolled', (window.scrollY || 0) > 8); }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    function setOpen(open) {
        if (!toggle || !panel) return;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) { panel.hidden = false; requestAnimationFrame(function () { panel.classList.add('open'); }); }
        else { panel.classList.remove('open'); setTimeout(function () { if (!panel.classList.contains('open')) panel.hidden = true; }, 180); }
    }
    function isOpen() { return panel && !panel.hidden && panel.classList.contains('open'); }
    if (toggle && panel) {
        toggle.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!isOpen()); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && isOpen()) setOpen(false); });
    }

    var eye = document.getElementById('togglePassword');
    var pass = document.getElementById('password');
    if (eye && pass) {
        eye.addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            eye.innerHTML = '<i class="fa-solid fa-eye' + (show ? '-slash' : '') + '" aria-hidden="true"></i>';
        });
    }

    var els = document.querySelectorAll('.reveal:not(.in)');
    if ('IntersectionObserver' in window && els.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
        }, { threshold: 0.12 });
        els.forEach(function (el) { io.observe(el); });
    } else { els.forEach(function (el) { el.classList.add('in'); }); }
})();
</script>

</body>
</html>
