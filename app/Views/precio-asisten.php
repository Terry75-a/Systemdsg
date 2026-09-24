<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Precios de Asisten DSG - Control de asistencia del personal. Planes para negocios de todos los tamaños.">
    <title>Precios · Asisten DSG</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/index/components/asistencia.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/precio-asisten.css') ?>">
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
            <a href="<?= base_url('asisten-dsg') ?>" class="asisten-link-site"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Asisten DSG</a>
            <a href="<?= base_url('login-verde') ?>" class="asisten-link-login">Iniciar sesión</a>
            <a href="<?= base_url('/#contacto') ?>" class="btn-green btn-green-sm">Solicitar demo</a>
            <button class="asisten-toggle" id="asistenToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="asistenMobile">
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
    <div class="asisten-mobile" id="asistenMobile" hidden>
        <a href="<?= base_url('asisten-dsg') ?>"><span><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Asisten DSG</span><span>→</span></a>
        <a href="<?= base_url('login-verde') ?>" class="is-login"><span><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Iniciar sesión</span><span>→</span></a>
        <a href="<?= base_url('/#contacto') ?>" class="is-cta"><span>Solicitar demo</span><span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a>
    </div>
</header>

<div class="asistencia-page">

    <!-- ═══════════ HERO ═══════════ -->
    <section class="asistencia-hero">
        <div class="container">
            <div class="asistencia-section-head reveal">
                <span class="asistencia-tag">Precios</span>
                <h1 class="asistencia-h2">Planes simples, <span class="serif-accent">sin sorpresas.</span></h1>
                <p class="asistencia-sub">Elige el plan que mejor se adapte a tu negocio. Todos incluyen soporte y actualizaciones.</p>
            </div>
        </div>
    </section>

    <!-- ═══════════ PLANES ═══════════ -->
    <section class="precio-asisten-plans">
        <div class="container">
            <div class="precio-asisten-grid">

                <!-- PLAN BÁSICO -->
                <div class="precio-asisten-card reveal">
                    <div class="precio-asisten-head">
                        <span class="precio-asisten-tag">Básico</span>
                        <div class="precio-asisten-price">
                            <span class="precio-asisten-amount">S/ 150</span>
                            <span class="precio-asisten-period">/mes</span>
                        </div>
                        <p class="precio-asisten-desc">Para negocios pequeños con hasta 10 empleados.</p>
                    </div>
                    <ul class="precio-asisten-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Hasta 10 empleados</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Marcación con código QR</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Control de tardanzas</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Reporte mensual básico</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> 1 sede</li>
                        <li class="no"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Turnos rotativos</li>
                        <li class="no"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Exportación a planilla</li>
                    </ul>
                    <a href="<?= base_url('/#contacto') ?>" class="btn-precio-asisten">Solicitar demo</a>
                </div>

                <!-- PLAN NEGOCIO (DESTACADO) -->
                <div class="precio-asisten-card is-featured reveal" data-delay="1">
                    <div class="precio-asisten-badge">Más popular</div>
                    <div class="precio-asisten-head">
                        <span class="precio-asisten-tag">Negocio</span>
                        <div class="precio-asisten-price">
                            <span class="precio-asisten-amount">S/ 290</span>
                            <span class="precio-asisten-period">/mes</span>
                        </div>
                        <p class="precio-asisten-desc">Para negocios en crecimiento con hasta 30 empleados.</p>
                    </div>
                    <ul class="precio-asisten-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Hasta 30 empleados</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Marcación con código QR</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Control de tardanzas y faltas</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Horarios y turnos fijos</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Reportes para planilla</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> 2 sedes</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Alertas en tiempo real</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Soporte prioritario</li>
                    </ul>
                    <a href="<?= base_url('/#contacto') ?>" class="btn-precio-asisten is-primary">Solicitar demo</a>
                </div>

                <!-- PLAN CORPORATIVO -->
                <div class="precio-asisten-card reveal" data-delay="2">
                    <div class="precio-asisten-head">
                        <span class="precio-asisten-tag">Corporativo</span>
                        <div class="precio-asisten-price">
                            <span class="precio-asisten-amount">Cotizar</span>
                        </div>
                        <p class="precio-asisten-desc">Para empresas con múltiples sedes y personal variable.</p>
                    </div>
                    <ul class="precio-asisten-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Empleados ilimitados</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Marcación con código QR</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Turnos rotativos avanzados</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Sedes ilimitadas</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Exportación a planilla</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> API integración</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Soporte dedicado</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Capacitación incluida</li>
                    </ul>
                    <a href="<?= base_url('/#contacto') ?>" class="btn-precio-asisten">Solicitar cotización</a>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════ FAQ ═══════════ -->
    <section class="asistencia-steps">
        <div class="container">
            <div class="asistencia-section-head reveal">
                <span class="asistencia-tag">Preguntas frecuentes</span>
                <h2 class="asistencia-h2">¿Tienes dudas?</h2>
            </div>
            <div class="faq-asisten">
                <div class="faq-asisten-item reveal">
                    <button class="faq-asisten-q" type="button" aria-expanded="false">
                        <span>¿Puedo cambiar de plan después?</span>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                    <div class="faq-asisten-a">
                        <p>Sí, puedes upgrading o downgrade en cualquier momento. El cambio se refleja en tu siguiente ciclo de facturación.</p>
                    </div>
                </div>
                <div class="faq-asisten-item reveal" data-delay="1">
                    <button class="faq-asisten-q" type="button" aria-expanded="false">
                        <span>¿Hay contrato o permanencia mínima?</span>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                    <div class="faq-asisten-a">
                        <p>No, no hay contratos ni permanencia. Puedes cancelar cuando quieras sin penalidades.</p>
                    </div>
                </div>
                <div class="faq-asisten-item reveal" data-delay="2">
                    <button class="faq-asisten-q" type="button" aria-expanded="false">
                        <span>¿Qué incluye el soporte?</span>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                    <div class="faq-asisten-a">
                        <p>Todos los planes incluyen soporte por chat y correo. El plan Negocio y Corporativo incluyen soporte prioritario con respuesta en menos de 2 horas.</p>
                    </div>
                </div>
                <div class="faq-asisten-item reveal" data-delay="3">
                    <button class="faq-asisten-q" type="button" aria-expanded="false">
                        <span>¿Puedo probar gratis antes de contratar?</span>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                    <div class="faq-asisten-a">
                        <p>Sí, ofrecemos una demo gratuita de 15 días sin necesidad de tarjeta de crédito. Prueba todas las funciones antes de decidir.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="asistencia-cta">
        <div class="container">
            <div class="asistencia-cta-inner reveal">
                <span class="asistencia-cta-kicker">¿Listo para empezar?</span>
                <h2 class="asistencia-cta-title">Ordena la asistencia de tu equipo hoy</h2>
                <p class="asistencia-cta-desc">Pide una demo sin compromiso y descubre cómo Asisten DSG simplifica el control de personal de tu negocio.</p>
                <div class="asistencia-cta-btns">
                    <a href="<?= base_url('/#contacto') ?>" class="btn-cta-white">Solicitar demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= base_url('asisten-dsg') ?>" class="btn-cta-ghost">Volver a Asisten DSG</a>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- ═══════════ FOOTER PROPIO ═══════════ -->
<footer class="asisten-footer">
    <div class="container">
        <div class="asisten-footer-top">
            <a href="<?= base_url('asisten-dsg') ?>" class="asisten-logo">
                <span class="asisten-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo"></span>
                <span class="asisten-logo-text">Asisten DSG</span>
            </a>
            <nav class="asisten-footer-nav" aria-label="Enlaces de Asisten DSG">
                <a href="<?= base_url('asisten-dsg') ?>">Asisten DSG</a>
                <a href="<?= base_url('precio-asisten') ?>">Precios</a>
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

    /* FAQ accordion */
    var faqBtns = document.querySelectorAll('.faq-asisten-q');
    faqBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            var answer = btn.nextElementSibling;
            btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            if (expanded) {
                answer.style.maxHeight = null;
                btn.querySelector('i').className = 'fa-solid fa-plus';
            } else {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                btn.querySelector('i').className = 'fa-solid fa-minus';
            }
        });
    });

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
