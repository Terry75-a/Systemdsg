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

            <!-- Enter también envía; el botón visible es la barra «desliza para ingresar» -->
            <button type="submit" hidden></button>
            <div class="tkl-slide" id="tklSlide">
                <span class="tkl-slide-fill" id="tklFill" aria-hidden="true"></span>
                <span class="tkl-slide-hint" id="tklHint" aria-hidden="true"><i>›</i><i>›</i><i>›</i></span>
                <button type="button" class="tkl-slide-btn" id="tklHandle" aria-label="Ingresar">
                    <span class="tkl-slide-label">Ingresar</span>
                    <span class="tkl-spin" aria-hidden="true"></span>
                </button>
            </div>

            <p class="tkl-foot">
                <a href="<?= base_url('/') ?>">Volver al sitio principal</a>
                <span>·</span>
                <a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a>
            </p>
        </form>
    </section>

    <!-- Panel derecho: cuadrícula + ruta animada -->
    <section class="tkl-side" aria-hidden="true">
        <div class="tkl-map">
            <svg viewBox="100 60 1400 780" preserveAspectRatio="xMidYMid meet">
                <?php
                $routes = [
                    ['r1', 'M220 730 C 380 700, 480 600, 640 520', 5],
                    ['r2', 'M640 520 C 760 470, 800 340, 900 250', 4],
                    ['r3', 'M640 520 C 900 560, 1180 420, 1330 170', 7],
                ];
                $nodes = [['LIMA', 220, 730], ['HUÁNUCO', 640, 520], ['PUCALLPA', 900, 250], ['IQUITOS', 1330, 170]];
                ?>
                <?php foreach ($routes as [$id, $d, $dur]): ?>
                    <path id="<?= $id ?>" d="<?= $d ?>" fill="none" stroke="#fff" stroke-opacity="0.25" stroke-width="1.5" stroke-dasharray="4 10">
                        <animate attributeName="stroke-dashoffset" from="0" to="-56" dur="2.5s" repeatCount="indefinite" />
                    </path>
                    <?php foreach ([0, 0.5] as $o): ?>
                        <circle r="4" fill="#bedf3c">
                            <animateMotion dur="<?= $dur ?>s" begin="<?= -$o * $dur ?>s" repeatCount="indefinite"><mpath href="#<?= $id ?>" /></animateMotion>
                        </circle>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php foreach ($nodes as $i => [$label, $x, $y]): ?>
                    <circle cx="<?= $x ?>" cy="<?= $y ?>" r="6" fill="#09ae04" />
                    <circle cx="<?= $x ?>" cy="<?= $y ?>" r="6" fill="none" stroke="#09ae04">
                        <animate attributeName="r" values="6;30" dur="3s" begin="<?= $i * 0.7 ?>s" repeatCount="indefinite" />
                        <animate attributeName="opacity" values="0.7;0" dur="3s" begin="<?= $i * 0.7 ?>s" repeatCount="indefinite" />
                    </circle>
                    <text x="<?= $x + 18 ?>" y="<?= $y + 5 ?>" fill="#fff" fill-opacity="0.55" font-size="18" letter-spacing="4" font-family="monospace"><?= $label ?></text>
                <?php endforeach; ?>
            </svg>
            <div class="tkl-caption">
                <p class="tkl-caption-cities">LIMA · HUÁNUCO · PUCALLPA · IQUITOS</p>
                <p class="tkl-caption-line">Todo tu negocio, bajo control.</p>
            </div>
        </div>
    </section>
</main>

<script src="<?= base_url('js/eyepassword.js?v=' . filemtime(FCPATH . 'js/eyepassword.js')) ?>"></script>
<script>
/* Barra «desliza para ingresar» (réplica de SlideToSubmit de Trackin):
   arrastra el botón; si pasa del 80 % se envía el formulario.
   Clic, Enter o espacio sobre el botón también envían. */
(function () {
    var form = document.getElementById('tklForm');
    var slide = document.getElementById('tklSlide');
    var handle = document.getElementById('tklHandle');
    var fill = document.getElementById('tklFill');
    var hint = document.getElementById('tklHint');
    if (!form || !slide || !handle) return;

    var HANDLE = 120, PAD = 8, x = 0, startX = 0, dragging = false, moved = false, sent = false;
    var max = function () { return slide.clientWidth - HANDLE - PAD * 2; };

    function paint(anim) {
        var m = Math.max(max(), 1);
        handle.style.transition = fill.style.transition = anim ? 'transform 420ms cubic-bezier(.34,1.25,.64,1), width 420ms cubic-bezier(.34,1.25,.64,1), opacity 420ms' : 'none';
        handle.style.transform = 'translateX(' + x + 'px)';
        fill.style.width = (x + HANDLE) + 'px';
        fill.style.opacity = (0.04 + 0.18 * (x / m)).toFixed(3);
        hint.style.opacity = Math.max(0, 1 - x / (m * 0.45)).toFixed(3);
    }
    function submit() {
        if (sent) return;
        if (!form.reportValidity()) { x = 0; paint(true); slide.classList.add('is-shake'); setTimeout(function () { slide.classList.remove('is-shake'); }, 500); return; }
        sent = true;
        x = max(); paint(true);
        slide.classList.add('is-loading');
        form.submit();
    }

    handle.addEventListener('pointerdown', function (e) {
        if (sent) return;
        dragging = true; moved = false; startX = e.clientX - x;
        handle.setPointerCapture(e.pointerId);
    });
    handle.addEventListener('pointermove', function (e) {
        if (!dragging) return;
        var nx = Math.min(Math.max(e.clientX - startX, 0), max());
        if (Math.abs(nx - x) > 2) moved = true;
        x = nx; paint(false);
    });
    function end() {
        if (!dragging) return;
        dragging = false;
        if (!moved) { submit(); return; }
        if (x > max() * 0.8) submit(); else { x = 0; paint(true); }
    }
    handle.addEventListener('pointerup', end);
    handle.addEventListener('pointercancel', end);
    handle.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); submit(); }
    });
    form.addEventListener('submit', function () { sent = true; slide.classList.add('is-loading'); });
    window.addEventListener('resize', function () { if (!sent) { x = 0; paint(false); } });
    paint(false);
})();
</script>
</body>
</html>
