<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Registra tu cuenta de empleado en DSG Perú con tu código de invitación.">
    <title>Registro de empleado · DSG Perú Technology</title>

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

                <div class="login-eyebrow"><span class="pulse"></span> Registro de empleado</div>
                <h1 class="login-title">Activa tu <span class="serif-accent">cuenta.</span></h1>
                <p class="login-sub">Ingresa el código que te dio tu desarrollador y crea tu contraseña.</p>

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

                <form class="login-form" method="post" action="<?= site_url('dios/register-employee') ?>" autocomplete="off">
                    <?= csrf_field() ?>

                    <div class="form-field">
                        <label for="code">Código de invitación</label>
                        <div class="field-wrap">
                            <i class="fa-solid fa-ticket field-icon" aria-hidden="true"></i>
                            <input type="text" id="code" name="code" placeholder="Ej: A3F8K2"
                                   maxlength="6" style="text-transform:uppercase; letter-spacing:0.12em; font-weight:700;"
                                   autocomplete="off" required>
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
                        Activar cuenta <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>

                    <div class="login-foot">
                        <a href="<?= base_url('login-verde') ?>" class="login-foot-link">¿Ya tienes cuenta? Iniciar sesión</a>
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
            <h2 class="login-right-title">Tu cuenta de <span class="serif-accent-white">empleado.</span></h2>
            <p class="login-right-desc">Con tu código de invitación puedes crear tu cuenta y acceder al sistema de control de asistencia.</p>

            <ul class="login-right-list">
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Marca tu asistencia con código QR</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Consulta tus horarios y turnos</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Revisa tu historial de asistencia</li>
            </ul>

            <div class="login-right-quote">
                <blockquote>" Activar mi cuenta fue rápido. Solo necesité el código que me dio mi jefe."</blockquote>
                <footer><strong>Rosa Campos</strong> · Empleada, Perú</footer>
            </div>
        </div>

        <p class="login-watermark" aria-hidden="true">DSG</p>
    </div>

</div>

<script>
(function () {
    var eye = document.getElementById('togglePassword');
    var pass = document.getElementById('password');
    if (eye && pass) {
        eye.addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            eye.innerHTML = '<i class="fa-solid fa-eye' + (show ? '-slash' : '') + '" aria-hidden="true"></i>';
        });
    }

    // Auto-uppercase code
    var codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function () { codeInput.value = codeInput.value.toUpperCase(); });
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
