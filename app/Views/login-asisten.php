<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en tu panel de control DSG Perú. Ventas, inventario y reportes bajo control.">
    <title>Iniciar sesión · DSG Perú Technology</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/index/components/login-asisten.css?v=20260922?v=20260922') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>

<div class="login-page">

    <!-- ═══════════ LADO IZQUIERDO: FORMULARIO ═══════════ -->
    <div class="login-left">
        <div class="login-left-inner">

            <div class="reveal">
                <div class="login-brand">
                    <span class="login-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
                    <span class="login-brand-text">DSG Perú<small>TECHNOLOGY</small></span>
                </div>

                <div class="login-eyebrow"><span class="pulse"></span> Acceso autorizado</div>
                <h1 class="login-title">Bienvenido de <span class="serif-accent">vuelta.</span></h1>
                <p class="login-sub">Ingresa tus credenciales para acceder a tu panel de control.</p>

                <?php
                $flashMsg = session()->getFlashdata('msg') ?? session()->getFlashdata('error');
                $flashTipo = session()->getFlashdata('tipo') ?? 'danger';
                $flashClass = ($flashTipo === 'warning') ? 'alerta-warning' : 'alerta-error';
                $flashIcon = ($flashTipo === 'warning') ? 'fa-triangle-exclamation' : 'fa-circle-exclamation';
                ?>
                <?php if (!empty($flashMsg)): ?>
                    <div class="<?= esc($flashClass) ?>" role="alert">
                        <i class="fa-solid <?= esc($flashIcon) ?>" aria-hidden="true"></i>
                        <span><?= esc($flashMsg) ?></span>
                    </div>
                <?php endif; ?>

                <!-- TABS: Email / DNI / Codigo -->
                <div class="login-tabs">
                    <button type="button" class="login-tab is-active" data-tab="email">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i> Correo
                    </button>
                    <button type="button" class="login-tab" data-tab="dni">
                        <i class="fa-solid fa-id-card" aria-hidden="true"></i> DNI
                    </button>
                    <button type="button" class="login-tab" data-tab="code">
                        <i class="fa-solid fa-key" aria-hidden="true"></i> Codigo
                    </button>
                </div>

                <!-- FORM: Email -->
                <form class="login-form" id="formEmail" method="post" action="<?= site_url('auth/login') ?>" autocomplete="on">
                    <?= csrf_field() ?>
                    <input type="hidden" name="login_type" value="email">

                    <div class="form-field">
                        <label for="username">Correo electrónico</label>
                        <div class="field-wrap">
                            <i class="fa-regular fa-envelope field-icon" aria-hidden="true"></i>
                            <input type="text" id="username" name="username" placeholder="tu@colegio.edu.pe"
                                   autocomplete="username" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-lock field-icon" aria-hidden="true"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••"
                                   autocomplete="current-password" required>
                            <button type="button" id="togglePassword" class="field-eye" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Iniciar sesión <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <!-- FORM: DNI -->
                <form class="login-form" id="formDni" method="post" action="<?= site_url('auth/login') ?>" autocomplete="off" style="display:none;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="login_type" value="dni">

                    <div class="form-field">
                        <label for="dni">DNI del empleado</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-id-card field-icon" aria-hidden="true"></i>
                            <input type="text" id="dni" name="dni" placeholder="12345678"
                                   inputmode="numeric" pattern="[0-9]{8}" maxlength="8" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="password_dni">Contraseña</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-lock field-icon" aria-hidden="true"></i>
                            <input type="password" id="password_dni" name="password" placeholder="••••••••"
                                   autocomplete="off" required>
                            <button type="button" id="togglePasswordDni" class="field-eye" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Entrar como empleado <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <!-- FORM: Codigo -->
                <form class="login-form" id="formCode" method="post" action="<?= site_url('auth/login') ?>" autocomplete="off" style="display:none;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="login_type" value="code">

                    <div class="form-field">
                        <label for="login_code">Codigo de acceso</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-key field-icon" aria-hidden="true"></i>
                            <input type="text" id="login_code" name="code" placeholder="Ej. C89E30"
                                   autocomplete="off" maxlength="6" style="text-transform:uppercase;" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="dni_code">DNI del empleado</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-id-card field-icon" aria-hidden="true"></i>
                            <input type="text" id="dni_code" name="dni" placeholder="12345678"
                                   inputmode="numeric" pattern="[0-9]{8}" maxlength="8" autocomplete="off" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Entrar con codigo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <div class="login-foot">
                    <a href="<?= base_url('registro') ?>" class="login-foot-link">¿No tienes cuenta? Crear una</a>
                    <a href="<?= base_url('asisten-dsg') ?>" class="login-foot-link">← Volver al sitio principal</a>
                    <span class="login-foot-help">¿Problemas para entrar? Contacta a tu administrador.</span>
                </div>
            </div>

        </div>
    </div>

    <!-- ═══════════ LADO DERECHO: PANEL DE MARCA ═══════════ -->
    <div class="login-right">
        <div class="login-right-inner reveal">
            <span class="login-right-tag">DSG Perú Technology</span>
            <h2 class="login-right-title">Todo tu negocio, <span class="serif-accent-white">bajo control.</span></h2>
            <p class="login-right-desc">Ventas, inventario y reportes en un solo panel. Diseñado para restaurantes, boticas y minimarkets del Perú.</p>

            <ul class="login-right-list">
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Punto de venta rápido y facturación electrónica</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Inventario en tiempo real con alertas de stock</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Reportes claros para decidir mejor</li>
            </ul>

            <div class="login-right-quote">
                <blockquote>"Nos da control total del negocio y ahora tengo más tiempo para lo importante."</blockquote>
                <footer><strong>Juan Alberto</strong> · Vifarma, Perú</footer>
            </div>
        </div>

        <p class="login-watermark" aria-hidden="true">DSG</p>
    </div>

</div>

<script>
(function () {
    /* — tabs — */
    var tabs = document.querySelectorAll('.login-tab');
    var formEmail = document.getElementById('formEmail');
    var formDni = document.getElementById('formDni');
    var formCode = document.getElementById('formCode');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            var target = tab.getAttribute('data-tab');
            formEmail.style.display = 'none';
            formDni.style.display = 'none';
            formCode.style.display = 'none';
            if (target === 'dni') {
                formDni.style.display = 'block';
            } else if (target === 'code') {
                formCode.style.display = 'block';
            } else {
                formEmail.style.display = 'block';
            }
        });
    });

    /* — toggle password email — */
    var eye = document.getElementById('togglePassword');
    var pass = document.getElementById('password');
    if (eye && pass) {
        eye.addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            eye.innerHTML = '<i class="fa-solid fa-eye' + (show ? '-slash' : '') + '" aria-hidden="true"></i>';
        });
    }

    /* — toggle password dni — */
    var eyeDni = document.getElementById('togglePasswordDni');
    var passDni = document.getElementById('password_dni');
    if (eyeDni && passDni) {
        eyeDni.addEventListener('click', function () {
            var show = passDni.type === 'password';
            passDni.type = show ? 'text' : 'password';
            eyeDni.innerHTML = '<i class="fa-solid fa-eye' + (show ? '-slash' : '') + '" aria-hidden="true"></i>';
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
