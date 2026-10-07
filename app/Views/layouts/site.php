<?php
$seg = strtolower(service('uri')->getSegment(1) ?? '');

$meta = [
    ''          => ['title' => 'Inicio',   'tag' => 'Sistemas para boticas, farmacias y más.'],
    'servicios' => ['title' => 'Servicios', 'tag' => 'Boticas, restaurantes, minimarkets y web.'],
    'dsg'       => ['title' => 'DSG',       'tag' => 'Transformamos la manera de hacer negocios en el Perú.'],
    'precio'    => ['title' => 'Precios',   'tag' => 'Un plan para cada tamaño de negocio.'],
];
if (!isset($meta[$seg])) {
    $seg = '';
}
$meta = $meta[$seg];

$pages = [
    ''          => 'Inicio',
    'servicios' => 'Servicios',
    'dsg'       => 'DSG',
    'precio'    => 'Precios',
];

$sections = [
    ''          => [['#servicios', 'Servicios'], ['#proceso', 'Proceso'], ['#por-que', 'Por qué elegirnos'], ['#testimonios', 'Testimonios'], ['#contacto', 'Contacto']],
    'servicios' => [['#boticas', 'Boticas y farmacias'], ['#restaurantes', 'Restaurantes'], ['#minimarkets', 'Minimarkets'], ['#web', 'Páginas web'], ['#hoteles', 'Hoteles'], ['#cafes', 'Cafés'], ['#medida', 'A medida']],
    'dsg'       => [['#trayectoria', 'Trayectoria'], ['#proposito', 'Propósito'], ['#valores', 'Valores']],
    'precio'    => [['#planes', 'Planes'], ['#faq', 'Preguntas']],
];
$sections = $sections[$seg];
$contactUrl = $seg === '' ? '#contacto' : base_url('/#contacto');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistemas de punto de venta, kardex y facturación para boticas y farmacias, restaurantes, minimarkets y páginas web personalizadas. Software a medida en Perú.">
    <title><?= esc($titulo ?? ($meta['title'] . ' · DSG Peru Technology')) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/awesomic.css?v=' . filemtime(FCPATH . 'css/index/components/awesomic.css')) ?>">
</head>

<body class="rg-page">

<!-- ══ Navbar sticky (awesomic) ══ -->
<header class="rg-nav">
    <a href="<?= base_url('/') ?>" class="rg-brand" aria-label="DSG Perú · Inicio">
        <span class="rg-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Perú"></span>
        <span>DSG Perú<small>TECHNOLOGY</small></span>
    </a>

    <nav class="rg-nav-menu" aria-label="Navegación del sitio">
        <?php foreach ($pages as $key => $label): ?>
            <a href="<?= base_url($key) ?>" class="rg-nav-link <?= $seg === $key ? 'is-active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="rg-nav-actions">
        <a href="<?= base_url('login') ?>" class="rg-btn rg-btn-ghost">Iniciar sesión</a>
        <a href="<?= $contactUrl ?>" class="rg-btn rg-btn-dark">Solicitar demo</a>
        <button class="rg-burger" id="rgBurger" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="rgDrawer">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- ══ Secciones de la página ══ -->
<nav class="rg-pills" aria-label="Secciones">
    <?php foreach ($sections as $i => [$href, $label]): ?>
        <a href="<?= $href ?>" class="rg-pill <?= $i === 0 ? 'is-active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
    <span class="rg-pills-end">
        <a href="<?= $contactUrl ?>" class="rg-pill">Contacto <i class="fa-solid fa-arrow-right"></i></a>
    </span>
</nav>

<?= $slot ?>

<!-- ══ Footer ══ -->
<footer class="rg-foot">
    <div class="rg-wrap">
        <div class="rg-foot-grid">
            <div class="rg-foot-brand">
                <a href="<?= base_url('/') ?>" class="rg-brand">
                    <span class="rg-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Perú"></span>
                    <span>DSG Perú<small>TECHNOLOGY</small></span>
                </a>
                <p>Software para boticas, farmacias, restaurantes, minimarkets y páginas web a medida en el Perú.</p>
                <div class="rg-foot-social">
                    <a href="https://www.facebook.com/dsgperu" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/dsgperu/" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <div>
                <h5>Productos</h5>
                <nav class="rg-foot-col" aria-label="Productos">
                    <a href="<?= base_url('servicios') ?>#boticas">Boticas y farmacias</a>
                    <a href="<?= base_url('servicios') ?>#restaurantes">Restaurantes</a>
                    <a href="<?= base_url('servicios') ?>#minimarkets">Minimarkets</a>
                    <a href="<?= base_url('servicios') ?>#web">Páginas web</a>
                </nav>
            </div>

            <div>
                <h5>Empresa</h5>
                <nav class="rg-foot-col" aria-label="Empresa">
                    <a href="<?= base_url('dsg') ?>">Sobre DSG</a>
                    <a href="<?= base_url('precio') ?>">Precios</a>
                    <a href="<?= $contactUrl ?>">Contacto</a>
                    <a href="<?= base_url('login') ?>">Iniciar sesión</a>
                    <a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a>
                </nav>
            </div>

            <div>
                <h5>Get in touch</h5>
                <div class="rg-foot-col">
                    <a href="mailto:soporte@dsgperu.com">soporte@dsgperu.com</a>
                    <a href="tel:+51923942001">+51 923 942 001</a>
                    <span>Pucallpa, Perú</span>
                </div>
            </div>
        </div>

        <div class="rg-foot-bottom">
            <span>&copy; <?= date('Y') ?> DSG Peru Technology. Todos los derechos reservados.</span>
            <span class="right">
                <a href="<?= base_url('asisten-dsg') ?>">Términos y políticas</a>
                <a href="<?= base_url('dsg') ?>">Empresa</a>
            </span>
        </div>
    </div>
</footer>

<!-- ══ Menú móvil ══ -->
<div class="rg-scrim" id="rgScrim" hidden></div>
<div class="rg-drawer" id="rgDrawer" hidden>
    <div class="rg-drawer-head">
        <a href="<?= base_url('/') ?>" class="rg-brand">
            <span class="rg-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Perú"></span>
            <span>DSG Perú<small>TECHNOLOGY</small></span>
        </a>
        <button class="rg-drawer-close" id="rgClose" type="button" aria-label="Cerrar menú">
            <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
    </div>

    <nav class="rg-nav-group" aria-label="Navegación del sitio">
        <span class="rg-nav-label">Navegación</span>
        <?php foreach ($pages as $key => $label): ?>
            <a href="<?= base_url($key) ?>" class="rg-nav-link <?= $seg === $key ? 'is-active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </nav>

    <nav class="rg-nav-group" aria-label="Secciones de esta página">
        <span class="rg-nav-label">En esta página</span>
        <?php foreach ($sections as $i => [$href, $label]): ?>
            <a href="<?= $href ?>" class="rg-nav-link is-sub"><?= $label ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="rg-drawer-cta">
        <p>Software para boticas, farmacias, restaurantes, minimarkets y páginas web a medida.</p>
        <div class="rg-drawer-actions">
            <a href="<?= $contactUrl ?>" class="rg-btn rg-btn-dark">Solicitar demo</a>
            <a href="<?= base_url('login') ?>" class="rg-btn rg-btn-ghost">Iniciar sesión</a>
        </div>
    </div>
</div>

<?php if ($seg === ''): ?>
<script src="<?= base_url('js/contador.js') ?>"></script>
<?php endif; ?>
<script>
    (function () {
        var burger = document.getElementById('rgBurger');
        var drawer = document.getElementById('rgDrawer');
        var scrim = document.getElementById('rgScrim');
        var closeBtn = document.getElementById('rgClose');
        if (!burger || !drawer || !scrim) return;

        function setOpen(open) {
            if (open) {
                drawer.hidden = false;
                scrim.hidden = false;
                requestAnimationFrame(function () {
                    drawer.classList.add('is-open');
                    scrim.classList.add('is-open');
                });
            } else {
                drawer.classList.remove('is-open');
                scrim.classList.remove('is-open');
                setTimeout(function () {
                    if (!drawer.classList.contains('is-open')) { drawer.hidden = true; scrim.hidden = true; }
                }, 240);
            }
            document.body.style.overflow = open ? 'hidden' : '';
            document.documentElement.style.overflow = open ? 'hidden' : '';
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            burger.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        }
        burger.addEventListener('click', function (e) { e.stopPropagation(); setOpen(drawer.hidden); });
        if (closeBtn) closeBtn.addEventListener('click', function () { setOpen(false); });
        scrim.addEventListener('click', function () { setOpen(false); });
        drawer.addEventListener('click', function (e) { if (e.target.closest('a')) setOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !drawer.hidden) { setOpen(false); burger.focus(); }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900 && !drawer.hidden) setOpen(false);
        });
    })();

    /* Ripple Material (estilo Google) en los botones */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.rg-btn');
        if (!btn) return;
        var rect = btn.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height) * 2.4;
        var rip = document.createElement('span');
        rip.className = 'rg-ripple';
        rip.style.width = rip.style.height = size + 'px';
        rip.style.left = (e.clientX - rect.left - size / 2) + 'px';
        rip.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(rip);
        rip.addEventListener('animationend', function () { rip.remove(); });
    });

    /* ── Scrollspy: píldora activa según la sección visible ── */
    (function () {
        var pills = [].slice.call(document.querySelectorAll('.rg-pills .rg-pill'))
            .filter(function (p) { return !p.closest('.rg-pills-end'); });
        var targets = pills.map(function (p) {
            var h = p.getAttribute('href');
            return h && h.charAt(0) === '#' ? document.querySelector(h) : null;
        });
        function update() {
            var best = 0;
            for (var i = 0; i < targets.length; i++) {
                if (!targets[i]) continue;
                if (targets[i].getBoundingClientRect().top <= 130) best = i;
            }
            pills.forEach(function (p, i) { p.classList.toggle('is-active', i === best); });
        }
        window.addEventListener('scroll', function () { requestAnimationFrame(update); }, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        window.addEventListener('hashchange', function () { setTimeout(update, 120); });
        update();
    })();

    /* ── Sonido de clic (Web Audio, sin archivos) ── */
    (function () {
        var ctx = null;
        function ensureCtx() {
            if (ctx) return ctx;
            var C = window.AudioContext || window.webkitAudioContext;
            if (!C) return null;
            try { ctx = new C(); } catch (e) { return null; }
            return ctx;
        }
        function wake() {
            var c = ensureCtx();
            if (c && c.state === 'suspended') {
                try { var p = c.resume(); if (p && p.catch) p.catch(function () {}); } catch (e) {}
            }
        }
        /* crea y despierta el contexto en el primer gesto del usuario */
        ['pointerdown', 'keydown'].forEach(function (t) {
            document.addEventListener(t, wake, { passive: true });
        });

        function schedule(c) {
            var t = c.currentTime;
            var osc = c.createOscillator();
            var gain = c.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(1320, t);
            osc.frequency.exponentialRampToValueAtTime(760, t + 0.045);
            gain.gain.setValueAtTime(0.0001, t);
            gain.gain.exponentialRampToValueAtTime(0.07, t + 0.004);
            gain.gain.exponentialRampToValueAtTime(0.0001, t + 0.075);
            osc.connect(gain);
            gain.connect(c.destination);
            osc.start(t);
            osc.stop(t + 0.1);
        }
        function tick() {
            if (localStorage.getItem('rgSound') === 'off') return;
            var c = ensureCtx();
            if (!c) return;
            /* programa siempre: si el contexto está suspendido suena al reanudarse */
            schedule(c);
            if (c.state !== 'running') {
                try { var p = c.resume(); if (p && p.catch) p.catch(function () {}); } catch (e) {}
            }
        }
        /* en fase capture: suena aunque el destino cancele la burbuja (p. ej. el burger) */
        document.addEventListener('click', function (e) {
            if (e.target && e.target.closest && e.target.closest('a, button, [role="button"], .rg-pill, .rg-nav-link, .rg-brand, label, summary')) tick();
        }, { capture: true, passive: true });
    })();

</script>

</body>
</html>
