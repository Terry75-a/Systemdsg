<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accede al panel de control de tu negocio. DSG Peru Technology.">
    <title>Iniciar sesión · DSG Perú Technology</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/login/loginindex.css?v=' . filemtime(FCPATH . 'css/login/loginindex.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>
<!-- ═══════════ LOGIN · pantalla completa ═══════════ -->
<div class="lg-page">
    <div class="lg-card">

        <div class="lg-form-col">
            <div class="lg-form-inner">
                <a href="<?= base_url('/') ?>" class="lg-back">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver al sitio principal
                </a>

                <span class="lg-tag">Panel de clientes</span>
                <h1 class="lg-h1">Bienvenido de <span class="grad">nuevo</span></h1>
                <p class="lg-sub">Accede al panel de control de tu negocio.</p>

                <?php
                $flashMsg = session()->getFlashdata('msg') ?? session()->getFlashdata('error');
                $flashTipo = session()->getFlashdata('tipo') ?? 'danger';
                $flashClass = ($flashTipo === 'warning') ? 'lg-alert-warning' : 'lg-alert-error';
                $flashIcon = ($flashTipo === 'warning') ? 'fa-triangle-exclamation' : 'fa-circle-exclamation';
                ?>
                <?php if (!empty($flashMsg)): ?>
                    <div class="<?= esc($flashClass) ?>" role="alert">
                        <i class="fa-solid <?= esc($flashIcon) ?>" aria-hidden="true"></i>
                        <span><?= esc($flashMsg) ?></span>
                    </div>
                <?php endif; ?>

                <form class="lg-form" method="post" action="<?= site_url('login/auth') ?>" autocomplete="on">
                    <?= csrf_field() ?>

                    <div class="lg-field">
                        <label for="username">Usuario</label>
                        <div class="lg-input">
                            <input type="text" id="username" name="username" placeholder="example@email.com"
                                   autocomplete="username" required>
                            <span class="lg-trailing"><i class="fa-regular fa-user" aria-hidden="true"></i></span>
                        </div>
                    </div>

                    <div class="lg-field">
                        <label for="password">Contraseña</label>
                        <div class="lg-input">
                            <input type="password" id="password" name="password" placeholder="••••••••"
                                   autocomplete="current-password" required>
                            <button type="button" id="togglePassword" class="lg-trailing lg-eye" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="lg-submit">
                        Entrar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <p class="lg-help">¿Problemas para entrar? Contacta a tu administrador.</p>

                <footer class="lg-foot">&copy; <?= date('Y') ?> DSG Peru Technology · <a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a></footer>
            </div>
        </div>

        <div class="lg-side">
            <img class="lg-side-art" src="<?= base_url('images/svc-photo-medida.jpg') ?>" alt="Panel de control DSG" loading="lazy">
            <div class="lg-side-card">
                <div class="lg-side-stars" aria-hidden="true">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p>"Nos da control total del negocio y ahora tengo más tiempo para lo importante."</p>
                <div class="lg-side-who">
                    <img src="<?= base_url('images/vifarma.png') ?>" alt="Vifarma">
                    <div><b>Juan Alberto</b><span>Vifarma · Pucallpa</span></div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="<?= base_url('js/eyepassword.js?v=' . filemtime(FCPATH . 'js/eyepassword.js')) ?>"></script>

</body>
</html>
