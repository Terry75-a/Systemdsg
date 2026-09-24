<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Asisten DSG: control de asistencia del personal. Marcación con QR, tardanzas, reportes para planilla y multi-sede.">
    <title>Asisten DSG · Control de asistencia del personal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/index/components/asistencia.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body class="asisten-body">

<!-- ═══════════ NAV PROPIO ═══════════ -->
<header class="asisten-nav-shell" id="asistenShell">
    <nav class="asisten-nav" aria-label="Navegación de Asisten DSG">
        <a href="<?= base_url('asisten-dsg') ?>" class="asisten-logo" aria-label="Asisten DSG - Inicio">
            <span class="asisten-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
            <span class="asisten-logo-text">Asisten DSG<small>CONTROL DE PERSONAL</small></span>
        </a>
        <div class="asisten-nav-actions">
            <a href="<?= base_url('login-verde') ?>" class="asisten-link-login">Iniciar sesión</a>
            <a href="<?= base_url('/#contacto') ?>" class="btn-green btn-green-sm">Solicitar demo</a>
            <button class="asisten-toggle" id="asistenToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="asistenMobile">
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
    <div class="asisten-mobile" id="asistenMobile" hidden>
        <a href="<?= base_url('login-verde') ?>" class="is-login"><span><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Iniciar sesión</span><span>→</span></a>
        <a href="<?= base_url('/#contacto') ?>" class="is-cta"><span>Solicitar demo</span><span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a>
    </div>
</header>

<div class="asistencia-page">

    <!-- ═══════════ HERO ═══════════ -->
    <section class="asistencia-hero">
        <div class="container">
            <div class="asistencia-hero-grid">
                <div class="reveal in">
                    <div class="asistencia-eyebrow"><span class="pulse"></span> Asisten DSG · Control de personal</div>
                    <h1 class="asistencia-title">La asistencia de tu equipo, <span class="serif-accent">sin planillas a mano.</span></h1>
                    <p class="asistencia-desc">Registra ingresos, salidas, tardanzas y faltas en segundos. Reportes listos para tu planilla y control total desde tu celular.</p>
                    <div class="asistencia-ctas">
                        <a href="<?= base_url('/#contacto') ?>" class="btn-green">Solicitar demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        <a href="<?= base_url('precio-asisten') ?>" class="btn-white-green">Ver precios</a>
                    </div>
                    <div class="hero-proof">
                        <div class="avatar-cluster" aria-hidden="true">
                            <span class="av-1">JA</span><span class="av-2">AM</span><span class="av-3">SM</span><span class="av-4">+</span>
                        </div>
                        <p><strong>Más de 500 empresas</strong> ya ordenan su personal con DSG</p>
                    </div>
                </div>

                <div class="hero-visual reveal in" data-delay="1">
                    <div class="mock" aria-label="Demostración del panel de asistencias">
                        <div class="mock-bar">
                            <i></i><i></i><i></i>
                            <span class="mock-live"><span class="dot"></span> En vivo</span>
                            <span class="mock-clock" id="mockClock">--:--:--</span>
                        </div>
                        <div class="mock-body">
                            <div class="mock-title-row">
                                <h3>Hoy · Turno mañana</h3>
                                <span>Sede Pucallpa</span>
                            </div>
                            <div class="mock-rows" id="mockRows">
                                <div class="mock-row">
                                    <span class="mock-avatar ma-1">RC</span>
                                    <div class="mock-who"><strong>Rosa Campos</strong><small>Caja · Ingreso</small></div>
                                    <span class="mock-time">08:00</span>
                                    <span class="mock-status st-ok">A tiempo</span>
                                </div>
                                <div class="mock-row">
                                    <span class="mock-avatar ma-2">LM</span>
                                    <div class="mock-who"><strong>Luis Mora</strong><small>Almacén · Ingreso</small></div>
                                    <span class="mock-time">08:04</span>
                                    <span class="mock-status st-ok">A tiempo</span>
                                </div>
                                <div class="mock-row">
                                    <span class="mock-avatar ma-3">DP</span>
                                    <div class="mock-who"><strong>Dina Paredes</strong><small>Ventas · Ingreso</small></div>
                                    <span class="mock-time">08:17</span>
                                    <span class="mock-status st-late">Tarde</span>
                                </div>
                            </div>
                        </div>
                        <div class="mock-foot">
                            <div class="mock-meter"><span id="mockMeter"></span></div>
                            <div class="mock-foot-row">
                                <p><strong id="mockCount">38</strong>/42 presentes hoy</p>
                                <button type="button" class="mock-mark" id="mockMark">Marcar asistencia</button>
                            </div>
                        </div>
                    </div>
                    <span class="chip chip-1"><i class="fa-solid fa-bolt"></i> Marcación en 5 seg</span>
                    <span class="chip chip-2"><i class="fa-solid fa-check"></i> +12 puntuales hoy</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ MARQUEE ═══════════ -->
    <div class="marquee" aria-hidden="true">
        <div class="marquee-track">
            <span>Código QR · Turnos rotativos · Tardanzas · Reporte de planilla · Multi-sede · Alertas en vivo ·&nbsp;</span>
            <span>Código QR · Turnos rotativos · Tardanzas · Reporte de planilla · Multi-sede · Alertas en vivo ·&nbsp;</span>
        </div>
    </div>

    <!-- ═══════════ BENTO FEATURES ═══════════ -->
    <section class="asistencia-features">
        <div class="container">
            <div class="asistencia-section-head reveal">
                <span class="asistencia-tag">Funciones</span>
                <h2 class="asistencia-h2">Todo tu personal, <span class="serif-accent">en una sola vista.</span></h2>
                <p class="asistencia-sub">Marcar, supervisar y justificar la asistencia de todo tu equipo sin hojas sueltas ni chats perdidos.</p>
            </div>
            <div class="bento">
                <div class="bento-card span-4 reveal">
                    <div class="asistencia-icon"><i class="fa-solid fa-qrcode"></i></div>
                    <h3>Marcación con QR</h3>
                    <p>Tu personal marca ingreso y salida escaneando un código. Sin filas, sin equipos costosos, sin excusas.</p>
                    <div class="qr-box" aria-hidden="true">
                        <svg width="104" height="104" viewBox="0 0 25 25" fill="#10231A">
                            <rect x="1" y="1" width="7" height="7" fill="none" stroke="#10231A" stroke-width="1.6"/><rect x="3" y="3" width="3" height="3"/>
                            <rect x="17" y="1" width="7" height="7" fill="none" stroke="#10231A" stroke-width="1.6"/><rect x="19" y="3" width="3" height="3"/>
                            <rect x="1" y="17" width="7" height="7" fill="none" stroke="#10231A" stroke-width="1.6"/><rect x="3" y="19" width="3" height="3"/>
                            <rect x="10" y="2" width="2" height="2"/><rect x="13" y="4" width="2" height="2"/><rect x="10" y="7" width="2" height="2"/>
                            <rect x="2" y="10" width="2" height="2"/><rect x="5" y="12" width="2" height="2"/><rect x="8" y="10" width="2" height="2"/>
                            <rect x="11" y="11" width="3" height="3"/><rect x="15" y="10" width="2" height="2"/><rect x="18" y="12" width="2" height="2"/>
                            <rect x="21" y="10" width="2" height="2"/><rect x="10" y="15" width="2" height="2"/><rect x="14" y="14" width="2" height="2"/>
                            <rect x="17" y="16" width="2" height="2"/><rect x="21" y="15" width="2" height="2"/><rect x="12" y="18" width="2" height="2"/>
                            <rect x="15" y="20" width="2" height="2"/><rect x="19" y="19" width="2" height="2"/><rect x="22" y="21" width="2" height="2"/>
                            <rect x="10" y="22" width="2" height="2"/><rect x="5" y="15" width="2" height="2"/><rect x="7" y="21" width="2" height="2"/>
                        </svg>
                    </div>
                </div>
                <div class="bento-card span-2 reveal" data-delay="1">
                    <div class="asistencia-icon"><i class="fa-solid fa-clock"></i></div>
                    <h3>Tardanzas y faltas</h3>
                    <p>Llegadas tarde y ausencias detectadas al instante.</p>
                    <div class="mini-rows" aria-hidden="true">
                        <div><span>08:17 · Dina P.</span><span class="mock-status st-late">Tarde</span></div>
                        <div><span>Sin marca · J. Ríos</span><span class="mock-status st-late">Falta</span></div>
                    </div>
                </div>
                <div class="bento-card span-2 reveal">
                    <div class="asistencia-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <h3>Horarios y turnos</h3>
                    <p>Fijos o rotativos, por área o sede.</p>
                    <div class="mini-shifts" aria-hidden="true">
                        <div>Mañana <span class="shift-bar"><span style="width:82%"></span></span> <small>8–2</small></div>
                        <div>Tarde <span class="shift-bar"><span style="width:55%"></span></span> <small>2–8</small></div>
                    </div>
                </div>
                <div class="bento-card span-4 reveal" data-delay="1">
                    <div class="asistencia-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <h3>Reportes listos para planilla</h3>
                    <p>Horas, extras y descuentos exportados en un clic. Tu contador te lo va a agradecer.</p>
                    <div class="mini-bars" aria-hidden="true">
                        <i></i><i class="hot"></i><i></i><i class="hot"></i><i></i><i class="hot"></i><i></i>
                    </div>
                </div>
                <div class="bento-card span-3 reveal">
                    <div class="asistencia-icon"><i class="fa-solid fa-bell"></i></div>
                    <h3>Alertas en tiempo real</h3>
                    <p>Avisos cuando alguien falta o llega tarde, sin esperar a fin de mes.</p>
                </div>
                <div class="bento-card span-3 reveal" data-delay="1">
                    <div class="asistencia-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    <h3>Multi-sede y móvil</h3>
                    <p>Varias sucursales bajo control desde tu celular, con permisos por supervisor.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ PASOS ═══════════ -->
    <section class="asistencia-steps">
        <div class="container">
            <div class="asistencia-section-head reveal">
                <span class="asistencia-tag">Cómo funciona</span>
                <h2 class="asistencia-h2">Empieza en 3 pasos</h2>
            </div>
            <div class="asistencia-steps-grid">
                <div class="asistencia-step reveal">
                    <span class="asistencia-ghost">01</span>
                    <h3>Registra tu personal</h3>
                    <p>Carga trabajadores, horarios y sedes con ayuda de nuestro equipo, en minutos.</p>
                </div>
                <div class="asistencia-step reveal" data-delay="1">
                    <span class="asistencia-ghost">02</span>
                    <h3>Marca todos los días</h3>
                    <p>Cada trabajador registra ingreso y salida con QR desde cualquier dispositivo.</p>
                </div>
                <div class="asistencia-step reveal" data-delay="2">
                    <span class="asistencia-ghost">03</span>
                    <h3>Revisa y paga exacto</h3>
                    <p>Reportes automáticos para una planilla sin errores ni reclamos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ FRASE ═══════════ -->
    <section class="asistencia-quote">
        <div class="container">
            <blockquote class="reveal">
                <p>"Pasamos del cuaderno a tener todo el personal controlado desde el celular. La planilla sale sola."</p>
                <footer><strong>Juan Alberto</strong> · Vifarma, Perú</footer>
            </blockquote>
        </div>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="asistencia-cta">
        <div class="container">
            <div class="asistencia-cta-inner reveal">
                <span class="asistencia-cta-kicker">Demo gratuita · 30 minutos</span>
                <h2 class="asistencia-cta-title">Olvídate del cuaderno de asistencia</h2>
                <p class="asistencia-cta-desc">Pide una demostración sin compromiso y ve cómo Asisten DSG ordena tu personal desde el primer día.</p>
                <div class="asistencia-cta-btns">
                    <a href="<?= base_url('/#contacto') ?>" class="btn-cta-white">Solicitar demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= base_url('/') ?>" class="btn-cta-ghost">Volver al sitio principal</a>
                </div>
            </div>
        </div>
    </section>

</div><!-- /.asistencia-page -->

<!-- ═══════════ FOOTER PROPIO ═══════════ -->
<footer class="asisten-footer">
    <div class="container">
        <div class="asisten-footer-top">
            <a href="<?= base_url('asisten-dsg') ?>" class="asisten-logo">
                <span class="asisten-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
                <span class="asisten-logo-text">Asisten DSG</span>
            </a>
            <nav class="asisten-footer-nav" aria-label="Enlaces de Asisten DSG">
                <a href="<?= base_url('/') ?>">Sitio principal</a>
                <a href="<?= base_url('servicios') ?>">Servicios</a>
                <a href="<?= base_url('/#contacto') ?>">Contacto</a>
            </nav>
        </div>
    </div>
    <p class="asisten-word" aria-hidden="true">ASISTEN</p>
    <div class="asisten-footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> DSG Perú Technology · Asisten DSG. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script>
(function () {
    /* — menú móvil + sombra al scroll — */
    var shell = document.getElementById('asistenShell');
    var toggle = document.getElementById('asistenToggle');
    var panel = document.getElementById('asistenMobile');
    function onScroll() {
        if (shell) shell.classList.toggle('scrolled', (window.scrollY || 0) > 8);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    function setOpen(open) {
        if (!toggle || !panel) return;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        if (open) {
            panel.hidden = false;
            requestAnimationFrame(function () { panel.classList.add('open'); });
        } else {
            panel.classList.remove('open');
            window.setTimeout(function () {
                if (!panel.classList.contains('open')) panel.hidden = true;
            }, 180);
        }
    }
    function isOpen() { return panel && !panel.hidden && panel.classList.contains('open'); }
    if (toggle && panel) {
        toggle.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!isOpen()); });
        panel.addEventListener('click', function (e) { if (e.target.closest('a')) setOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen()) { setOpen(false); toggle.focus(); }
        });
        window.addEventListener('resize', function () { if (window.innerWidth > 760 && isOpen()) setOpen(false); });
    }

    /* — reloj en vivo del mockup — */
    var clock = document.getElementById('mockClock');
    function tick() {
        if (!clock) return;
        var d = new Date();
        var p = function (n) { return (n < 10 ? '0' : '') + n; };
        clock.textContent = p(d.getHours()) + ':' + p(d.getMinutes()) + ':' + p(d.getSeconds());
    }
    tick();
    window.setInterval(tick, 1000);

    /* — barra de presentes + demo de marcación — */
    var meter = document.getElementById('mockMeter');
    var count = document.getElementById('mockCount');
    var rows = document.getElementById('mockRows');
    var mark = document.getElementById('mockMark');
    var total = 42, present = 38;
    function paint() {
        if (meter) meter.style.width = Math.min(100, (present / total) * 100) + '%';
        if (count) count.textContent = present;
    }
    window.setTimeout(paint, 350);
    if (mark && mark.addEventListener) {
        mark.addEventListener('click', function () {
            if (!rows) return;
            var d = new Date();
            var p = function (n) { return (n < 10 ? '0' : '') + n; };
            var row = document.createElement('div');
            row.className = 'mock-row is-fresh';
            row.innerHTML = '<span class="mock-avatar ma-4">TÚ</span>' +
                '<div class="mock-who"><strong>Demostración</strong><small>Marcación manual</small></div>' +
                '<span class="mock-time">' + p(d.getHours()) + ':' + p(d.getMinutes()) + '</span>' +
                '<span class="mock-status st-ok">A tiempo</span>';
            rows.insertBefore(row, rows.firstChild);
            while (rows.children.length > 5) rows.removeChild(rows.lastChild);
            if (present < total) { present++; paint(); }
        });
    }

    /* — reveal on scroll — */
    var els = document.querySelectorAll('.reveal:not(.in)');
    if ('IntersectionObserver' in window && els.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.12 });
        els.forEach(function (el) { io.observe(el); });
    } else {
        els.forEach(function (el) { el.classList.add('in'); });
    }
})();
</script>

</body>
</html>
