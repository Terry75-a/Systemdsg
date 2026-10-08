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

$sistemas = [
    ['#boticas', 'Boticas y farmacias', 'fa-capsules'],
    ['#restaurantes', 'Restaurantes', 'fa-utensils'],
    ['#minimarkets', 'Minimarkets', 'fa-shop'],
    ['#web', 'Páginas web', 'fa-window-maximize'],
    ['#hoteles', 'Hoteles', 'fa-bed'],
    ['#cafes', 'Cafés y pastelerías', 'fa-mug-saucer'],
    ['#medida', 'Software a medida', 'fa-pen-ruler'],
];

$sections = [
    ''          => [['#servicios', 'Servicios'], ['#planes', 'Planes'], ['#por-que', 'Por qué elegirnos'], ['#proceso', 'Cómo trabajamos'], ['#testimonios', 'Testimonios'], ['#contacto', 'Contacto']],
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
    <script>try{var __t=localStorage.getItem('rg-theme');if(!__t&&window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches){__t='dark'}if(__t==='dark'){document.documentElement.setAttribute('data-theme','dark')}}catch(__e){}</script>
    <link rel="stylesheet" href="<?= base_url('css/index/components/awesomic.css?v=' . filemtime(FCPATH . 'css/index/components/awesomic.css')) ?>">
</head>

<body class="rg-page">

<!-- ══ Navbar sticky (estilo awesomic: marca · menú centrado · acciones) ══ -->
<header class="rg-nav">
    <a href="<?= base_url('/') ?>" class="rg-brand" aria-label="DSG Perú · Inicio">
        <span class="rg-brand-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Perú"></span>
        <span>DSG Perú<small>TECHNOLOGY</small></span>
    </a>

    <nav class="rg-nav-links" aria-label="Navegación del sitio">
        <span class="rg-nav-item">
            <a href="<?= base_url('') ?>" class="rg-nav-link <?= $seg === '' ? 'is-active' : '' ?>">Inicio</a>
        </span>
        <span class="rg-nav-item">
            <a href="<?= base_url('servicios') ?>" class="rg-nav-link <?= $seg === 'servicios' ? 'is-active' : '' ?>">
                Servicios <i class="fa-solid fa-chevron-down"></i>
            </a>
            <span class="rg-dropdown">
                <?php foreach ($sistemas as [$href, $label, $icon]): ?>
                    <a href="<?= base_url('servicios') ?><?= $href ?>"><i class="fa-solid <?= $icon ?>"></i> <?= $label ?></a>
                <?php endforeach; ?>
            </span>
        </span>
        <span class="rg-nav-item">
            <a href="<?= base_url('dsg') ?>" class="rg-nav-link <?= $seg === 'dsg' ? 'is-active' : '' ?>">Empresa</a>
        </span>
        <span class="rg-nav-item">
            <a href="<?= base_url('precio') ?>" class="rg-nav-link <?= $seg === 'precio' ? 'is-active' : '' ?>">Precios</a>
        </span>
    </nav>

    <div class="rg-nav-actions">
        <a href="<?= base_url('login') ?>" class="rg-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a>
        <a href="<?= $contactUrl ?>" class="rg-btn rg-btn-white"><i class="fa-regular fa-calendar"></i> Agendar demo</a>
        <a href="tel:+51923942001" class="rg-btn rg-btn-dark rg-btn-pill"><i class="fa-solid fa-phone"></i> Hablar ahora</a>
        <button class="rg-theme-toggle" id="rgThemeToggle" type="button" aria-label="Activar modo oscuro" title="Modo oscuro">
            <i class="fa-solid fa-moon" aria-hidden="true"></i>
        </button>
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

<!-- ══ Footer (tarjeta "Get in touch" + columnas + wordmark) ══ -->
<footer class="rg-foot">
    <div class="rg-wrap">
        <div class="rg-foot-top">
            <div class="rg-foot-contact">
                <h4>Get in touch</h4>
                <div class="line"><i class="fa-solid fa-location-dot"></i> Pucallpa, Ucayali, Perú · atendemos todo el país</div>
                <div class="stack">
                    <a href="tel:+51923942001"><i class="fa-solid fa-phone"></i> +51 923 942 001</a>
                    <a href="mailto:soporte@dsgperu.com"><i class="fa-solid fa-envelope"></i> soporte@dsgperu.com</a>
                </div>
                <div class="row">
                    <div class="rg-foot-social">
                        <a href="https://www.facebook.com/dsgperu" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/dsgperu/" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.me/51923942001" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="rg-foot-cols">
                <div class="rg-foot-col">
                    <h5>Empresa</h5>
                    <a href="<?= base_url('dsg') ?>">Sobre DSG</a>
                    <a href="<?= base_url('dsg') ?>#trayectoria">Trayectoria</a>
                    <a href="<?= base_url('precio') ?>">Precios</a>
                    <a href="<?= $contactUrl ?>">Contacto</a>
                    <a href="<?= base_url('login') ?>">Iniciar sesión</a>
                </div>
                <div class="rg-foot-col">
                    <h5>Recursos</h5>
                    <a href="<?= base_url('asisten-dsg') ?>">Asistencia del personal</a>
                    <a href="<?= base_url('servicios') ?>#faq">Preguntas frecuentes</a>
                    <a href="<?= base_url('precio') ?>#faq">Preguntas sobre precios</a>
                    <a href="<?= $contactUrl ?>">Solicitar demo</a>
                </div>
                <div class="rg-foot-col">
                    <h5>Servicios</h5>
                    <a href="<?= base_url('servicios') ?>#boticas">Boticas y farmacias</a>
                    <a href="<?= base_url('servicios') ?>#restaurantes">Restaurantes</a>
                    <a href="<?= base_url('servicios') ?>#minimarkets">Minimarkets</a>
                    <a href="<?= base_url('servicios') ?>#web">Páginas web</a>
                    <a href="<?= base_url('servicios') ?>#medida">Software a medida</a>
                </div>
            </div>
        </div>

        <p class="rg-foot-note">
            <i class="fa-solid fa-sparkles"></i>
            Software para boticas, farmacias, restaurantes, minimarkets y páginas web a medida en el Perú.
        </p>

        <div class="rg-wordmark" aria-hidden="true">DSGPERU<sup>✳</sup></div>

        <div class="rg-foot-bottom">
            <span>&copy; <?= date('Y') ?> DSG Peru Technology. Todos los derechos reservados.</span>
            <span class="right">
                <a href="<?= base_url('asisten-dsg') ?>">Términos y políticas</a>
                <a href="<?= base_url('dsg') ?>">Empresa</a>
                <span class="credits">Fotos: Wikimedia Commons</span>
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

    /* ── Formularios pill del hero/CTA: llevan al formulario de contacto ── */
    document.querySelectorAll('form.rg-lead-form').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            e.preventDefault();
            var mail = f.querySelector('input[type="email"]');
            var target = document.getElementById('contacto');
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(function () {
                var dest = document.querySelector('#contacto .rg-form input[name="correo"]');
                if (dest) {
                    if (mail && mail.value) dest.value = mail.value;
                    dest.focus({ preventScroll: true });
                }
            }, 600);
        });
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

    /* ── Palabra rotativa del hero (estilo awesomic) ── */
    (function () {
        var el = document.querySelector('.grad[data-rotate]');
        if (!el) return;
        var words;
        try { words = JSON.parse(el.getAttribute('data-rotate')); } catch (e) { return; }
        if (!words || words.length < 2) return;
        var i = 0;
        setInterval(function () {
            i = (i + 1) % words.length;
            el.classList.add('is-out');
            setTimeout(function () { el.textContent = words[i]; el.classList.remove('is-out'); }, 340);
        }, 3400);
    })();

    /* ── Flechas del carrusel de tarjetas ── */
    document.querySelectorAll('[data-rail-target]').forEach(function (btn) {
        var rail = document.getElementById(btn.getAttribute('data-rail-target'));
        if (!rail) return;
        btn.addEventListener('click', function () {
            var card = rail.querySelector('.rg-svc-card');
            var step = card ? card.getBoundingClientRect().width + 16 : 340;
            rail.scrollBy({ left: btn.hasAttribute('data-rail-prev') ? -step : step, behavior: 'smooth' });
        });
    });

    /* ── Segmentados (cuestionario de planes) ── */
    document.querySelectorAll('.rg-seg').forEach(function (seg) {
        seg.addEventListener('click', function (e) {
            var b = e.target.closest('button');
            if (!b) return;
            seg.querySelectorAll('button').forEach(function (x) { x.classList.toggle('on', x === b); });
        });
    });

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

    /* ── Modo oscuro (negro puro, con persistencia) ── */
    (function () {
        var root = document.documentElement;
        var btn = document.getElementById('rgThemeToggle');
        function paint() {
            var dark = root.getAttribute('data-theme') === 'dark';
            if (!btn) return;
            var icon = btn.querySelector('i');
            if (icon) icon.className = dark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            btn.setAttribute('aria-label', dark ? 'Activar modo claro' : 'Activar modo oscuro');
            btn.setAttribute('title', dark ? 'Modo claro' : 'Modo oscuro');
        }
        if (btn) {
            btn.addEventListener('click', function () {
                var dark = root.getAttribute('data-theme') !== 'dark';
                if (dark) root.setAttribute('data-theme', 'dark');
                else root.removeAttribute('data-theme');
                try { localStorage.setItem('rg-theme', dark ? 'dark' : 'light'); } catch (e) {}
                paint();
            });
        }
        paint();
    })();

</script>

</body>
</html>
