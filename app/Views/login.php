<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accede al panel de control de tu negocio. DSG Peru Technology.">
    <title>Ingresar · DSG Perú Technology</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/login/tk-login.css?v=' . filemtime(FCPATH . 'css/login/tk-login.css')) ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>
<!-- ═══════════ LOGIN · réplica del login de System-Trackin ═══════════ -->
<main class="tkl">
    <section class="tkl-col">
        <form class="tkl-form" id="tklForm" method="post" action="<?= site_url('login/auth') ?>" autocomplete="on">
            <?= csrf_field() ?>

            <!-- Marca + título -->
            <div class="tkl-head">
                <span class="tkl-brand">
                    <img src="<?= base_url('images/logo_circular.png') ?>" alt="DSG Perú">
                    <span class="tkl-brand-txt">
                        <b>DSG Perú</b>
                        Panel de clientes
                    </span>
                </span>
                <h1>Ingresar</h1>
            </div>

            <p class="tkl-lead">Ingresa tu usuario y contraseña<br>para administrar tu negocio</p>

            <?php
            $flashMsg  = session()->getFlashdata('msg') ?? session()->getFlashdata('error');
            $flashTipo = session()->getFlashdata('tipo') ?? 'danger';
            ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="tkl-alert <?= $flashTipo === 'warning' ? 'is-warn' : '' ?>" role="alert">
                    <i class="fa-solid <?= $flashTipo === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation' ?>" aria-hidden="true"></i>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <!-- Campos -->
            <div class="tkl-fields">
                <input class="tkl-input" type="text" id="username" name="username" placeholder="Usuario"
                       aria-label="Usuario" autocomplete="username" required>
                <div class="tkl-pass">
                    <input class="tkl-input" type="password" id="password" name="password" placeholder="Contraseña"
                           aria-label="Contraseña" autocomplete="current-password" required>
                    <button type="button" id="togglePassword" class="tkl-eye" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="tkl-divider"><span></span>Restaurantes · Boticas · Minimarkets<span></span></div>

            <button type="submit" class="tkl-submit" id="tklSubmit" aria-label="Ingresar">
                <span class="tkl-submit-label">Ingresar</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                <span class="tkl-spin" aria-hidden="true"></span>
            </button>

            <p class="tkl-foot">
                <a href="<?= base_url('/') ?>">Volver al sitio principal</a>
                <span>·</span>
                <a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a>
            </p>
        </form>
    </section>

    <!-- Panel derecho: escena tecnológica (rejilla 3D, anillos y paneles de datos) -->
    <section class="tkl-side" aria-hidden="true">
        <div class="tkl-tech">
            <div class="tkl-floor"></div>
            <div class="tkl-glow"></div>
            <div class="tkl-scan"></div>

            <!-- núcleo: logo con anillos que giran -->
            <div class="tkl-core">
                <svg class="tkl-rings" viewBox="0 0 400 400">
                    <circle cx="200" cy="200" r="190" class="r r1"/>
                    <circle cx="200" cy="200" r="150" class="r r2"/>
                    <circle cx="200" cy="200" r="110" class="r r3"/>
                    <g class="orb o1"><circle cx="200" cy="10" r="5"/></g>
                    <g class="orb o2"><circle cx="200" cy="50" r="4"/></g>
                    <g class="orb o3"><circle cx="200" cy="90" r="3"/></g>
                </svg>
                <span class="tkl-core-logo"><img src="<?= base_url('images/logo_circular.png') ?>" alt=""></span>
            </div>

            <!-- paneles flotantes -->
            <div class="tkl-card c1">
                <span class="tkl-card-lbl"><i class="fa-solid fa-cash-register"></i> Ventas de hoy</span>
                <b>S/ 2,480.50</b>
                <span class="tkl-up"><i class="fa-solid fa-arrow-trend-up"></i> +12.4 %</span>
            </div>
            <div class="tkl-card c2">
                <span class="tkl-card-lbl"><i class="fa-solid fa-chart-column"></i> Últimos 7 días</span>
                <div class="tkl-bars"><i style="--h:42%"></i><i style="--h:65%"></i><i style="--h:48%"></i><i style="--h:80%"></i><i style="--h:58%"></i><i style="--h:92%"></i><i style="--h:74%"></i></div>
            </div>
            <div class="tkl-card c3">
                <span class="tkl-card-lbl"><i class="fa-solid fa-file-invoice"></i> Facturación electrónica</span>
                <span class="tkl-ok"><i class="fa-solid fa-circle-check"></i> Enviada a SUNAT</span>
            </div>
            <div class="tkl-card c4">
                <span class="tkl-card-lbl"><i class="fa-solid fa-boxes-stacked"></i> Inventario</span>
                <b>1,284 <small>productos</small></b>
                <span class="tkl-warn"><i class="fa-solid fa-triangle-exclamation"></i> 3 con stock bajo</span>
            </div>

            <div class="tkl-caption">
                <p class="tkl-caption-cities">PUNTO DE VENTA · INVENTARIO · FACTURACIÓN</p>
                <p class="tkl-caption-line">Todo tu negocio, bajo control.</p>
            </div>
        </div>
    </section>
</main>

<script src="<?= base_url('js/eyepassword.js?v=' . filemtime(FCPATH . 'js/eyepassword.js')) ?>"></script>
<script>
/* Al enviar: el botón muestra un círculo de carga y no se puede pulsar dos veces */
(function () {
    var form = document.getElementById('tklForm');
    var btn = document.getElementById('tklSubmit');
    if (!form || !btn) return;
    form.addEventListener('submit', function () {
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');
        setTimeout(function () { btn.disabled = true; }, 0);
    });
})();
</script>
</body>
</html>
