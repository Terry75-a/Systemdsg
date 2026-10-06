<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Asisten DSG: control de asistencia del personal. Marcación con QR, tardanzas, reportes para planilla y multi-sede.">
    <title>Asisten DSG · Control de asistencia del personal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/index/components/asisten-landing.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body class="mm-landing">

<!-- ═══════════ NAV FLOTANTE ═══════════ -->
<header>
    <nav class="mm-nav" id="mmNav" aria-label="Navegación de Asisten DSG">
        <div class="mm-nav-inner">
            <a href="<?= base_url('asisten-dsg') ?>" class="mm-nav-logo">
                <img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo">
                <span>Asisten DSG</span>
            </a>
            <div class="mm-nav-links">
                <a href="#caracteristicas" class="mm-nav-btn">Características</a>
                <a href="#precios" class="mm-nav-btn">Precios</a>
                <a href="#faq" class="mm-nav-btn">FAQ</a>
            </div>
            <div class="mm-nav-actions">
                <a href="<?= base_url('login-verde') ?>" class="mm-nav-btn">Iniciar sesión</a>
                <a href="#contacto" class="mm-nav-btn is-outline">Solicitar demo</a>
                <button class="mm-nav-toggle" id="mmToggle" type="button" aria-label="Abrir menú" aria-expanded="false">
                    <span class="material-symbols-rounded">menu</span>
                </button>
            </div>
        </div>
        <div class="mm-nav-mobile" id="mmMobile" hidden>
            <a href="#caracteristicas"><span>Características</span><span class="material-symbols-rounded">trending_up</span></a>
            <a href="#precios"><span>Precios</span><span class="material-symbols-rounded">shoppingmode</span></a>
            <a href="#faq"><span>Preguntas frecuentes</span><span class="material-symbols-rounded">help</span></a>
            <a href="<?= base_url('login-verde') ?>"><span>Iniciar sesión</span><span class="material-symbols-rounded">login</span></a>
            <a href="#contacto"><span>Solicitar demo</span><span class="material-symbols-rounded">event_available</span></a>
        </div>
    </nav>
</header>

<main>

    <!-- ═══════════ HERO ═══════════ -->
    <section class="mm-hero">
        <div class="mm-hero-copy reveal in">
            <a href="#contacto" class="cta-button"><span class="tag">GRATIS</span> Reserva una demo <span class="arrow">→</span></a>
            <h1 class="main-title">La asistencia de tu equipo, sin planillas a mano.</h1>
            <p class="mm-hero-desc">Registra ingresos, salidas, tardanzas y faltas en segundos. Reportes listos para tu planilla y control total desde tu celular.</p>
            <div class="mm-hero-ctas">
                <a href="#contacto" class="mm-btn mm-btn-filled">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                <a href="<?= base_url('login-verde') ?>" class="mm-btn mm-btn-tonal">Iniciar sesión</a>
            </div>
            <div class="mm-hero-checks">
                <span><span class="material-symbols-rounded">check</span> Sin instalación</span>
                <span><span class="material-symbols-rounded">check</span> Cancela cuando quieras</span>
                <span><span class="material-symbols-rounded">check</span> Soporte en Perú</span>
            </div>
        </div>

        <div class="mm-preview reveal in" data-delay="1">
            <div class="mm-preview-frame">
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
                <span class="chip chip-1"><span class="material-symbols-rounded">bolt</span> Marcación en 5 seg</span>
                <span class="chip chip-2"><span class="material-symbols-rounded">check_circle</span> +12 puntuales hoy</span>
            </div>
        </div>
    </section>

    <!-- ═══════════ VIDEO DEMO ═══════════ -->
    <section class="mm-section mm-video-section" id="demo-video">
        <div class="mm-section-head reveal">
            <span class="title-text">Demo en vivo</span>
            <h2 class="main-title">Míralo funcionando</h2>
            <p class="mm-section-sub">15 segundos para ver cómo tu equipo marca, cómo ves tardanzas en tiempo real y cómo se exporta la planilla.</p>
        </div>
        <div id="mm-video-container" class="mm-video-container reveal" data-delay="1">
            <div class="mm-video-thumb" data-role="thumbnail">
                <img src="<?= base_url('images/asisten-demo-poster.jpg') ?>" alt="Vista previa de Asisten DSG en acción" loading="lazy">
                <button type="button" class="mm-video-play" id="mmPlayVideo" aria-label="Reproducir video de demostración">
                    <span class="material-symbols-rounded ms-filled">play_arrow</span>
                </button>
            </div>
            <video id="mmMainVideo" class="mm-video-el" controls loop muted playsinline preload="none" poster="<?= base_url('images/asisten-demo-poster.jpg') ?>">
                <source src="<?= base_url('videos/asisten-demo.mp4') ?>" type="video/mp4">
                Tu navegador no admite video HTML5.
            </video>
        </div>
    </section>

    <!-- ═══════════ VALORES ═══════════ -->
    <section class="valores-panel reveal">
        <div class="mm-section-head">
            <h2 class="main-title">Lo que nos define</h2>
        </div>
        <div class="valores-grid">
            <div class="grid-card">
                <div class="title-with-icon">
                    <span class="material-symbols-rounded">rocket_launch</span>
                    <h3 class="title-medium">Simple</h3>
                </div>
                <p>Una interfaz clara para marcar, supervisar y exportar la asistencia de tu equipo, sin capacitaciones eternas.</p>
            </div>
            <div class="grid-card">
                <div class="title-with-icon">
                    <span class="material-symbols-rounded">security</span>
                    <h3 class="title-medium">Seguro</h3>
                </div>
                <p>Los datos de tu personal viajan cifrados y solo los ve el equipo que tú autorizas.</p>
            </div>
            <div class="grid-card">
                <div class="title-with-icon">
                    <span class="material-symbols-rounded">task_alt</span>
                    <h3 class="title-medium">Preciso</h3>
                </div>
                <p>Tardanzas, horas extras y descuentos calculados solos para pagar siempre exacto.</p>
            </div>
        </div>
    </section>

    <!-- ═══════════ CARACTERÍSTICAS (TABS) ═══════════ -->
    <section class="mm-section" id="caracteristicas">
        <div class="mm-section-head reveal">
            <span class="title-text">Características</span>
            <h2 class="main-title">Todo lo que necesitas</h2>
            <p class="mm-section-sub">Marcar, supervisar y justificar la asistencia de todo tu equipo sin hojas sueltas ni chats perdidos.</p>
        </div>

        <div class="tab-wrapper reveal" data-delay="1">
            <div class="menu-pill-container" role="tablist">
                <button class="tab-button-pill active" data-tab="0" type="button"><span class="material-symbols-rounded">qr_code_2</span>Marcación QR</button>
                <button class="tab-button-pill" data-tab="1" type="button"><span class="material-symbols-rounded">schedule</span>Tardanzas</button>
                <button class="tab-button-pill" data-tab="2" type="button"><span class="material-symbols-rounded">event</span>Horarios</button>
                <button class="tab-button-pill" data-tab="3" type="button"><span class="material-symbols-rounded">description</span>Reportes</button>
                <button class="tab-button-pill" data-tab="4" type="button"><span class="material-symbols-rounded">storefront</span>Multi-sede</button>
            </div>

            <!-- tab 0 · QR -->
            <div class="tab-content active" data-panel="0">
                <div class="feature-content-container">
                    <div class="feature-content-left">
                        <div class="tab-visual">
                            <div class="qr-box">
                                <svg width="120" height="120" viewBox="0 0 25 25" fill="currentColor">
                                    <rect x="1" y="1" width="7" height="7" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="3" width="3" height="3"/>
                                    <rect x="17" y="1" width="7" height="7" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="19" y="3" width="3" height="3"/>
                                    <rect x="1" y="17" width="7" height="7" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="19" width="3" height="3"/>
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
                    </div>
                    <div class="feature-content-right">
                        <h3>Marcación con QR</h3>
                        <p>Tu personal marca ingreso y salida escaneando un código. Sin filas, sin equipos costosos, sin excusas.</p>
                        <ul class="feature-list">
                            <li><span class="material-symbols-rounded">qr_code_2</span> Ingreso y salida con un escaneo</li>
                            <li><span class="material-symbols-rounded">smartphone</span> Desde cualquier celular</li>
                            <li><span class="material-symbols-rounded">download_for_offline</span> Sin apps ni equipos extra</li>
                        </ul>
                        <a href="#contacto" class="mm-btn mm-btn-tonal" style="align-self:flex-start">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                    </div>
                </div>
            </div>

            <!-- tab 1 · Tardanzas -->
            <div class="tab-content" data-panel="1">
                <div class="feature-content-container">
                    <div class="feature-content-left">
                        <div class="tab-visual mini-rows">
                            <div><span>08:17 · Dina P.</span><span class="mock-status st-late">Tarde</span></div>
                            <div><span>Sin marca · J. Ríos</span><span class="mock-status st-late">Falta</span></div>
                            <div><span>08:00 · Rosa C.</span><span class="mock-status st-ok">A tiempo</span></div>
                        </div>
                    </div>
                    <div class="feature-content-right">
                        <h3>Tardanzas y faltas</h3>
                        <p>Llegadas tarde y ausencias detectadas al instante, sin esperar a fin de mes para enterarte.</p>
                        <ul class="feature-list">
                            <li><span class="material-symbols-rounded">schedule</span> Detección automática de retrasos</li>
                            <li><span class="material-symbols-rounded">notifications_active</span> Alertas en tiempo real</li>
                            <li><span class="material-symbols-rounded">edit_document</span> Justificaciones desde el móvil</li>
                        </ul>
                        <a href="#contacto" class="mm-btn mm-btn-tonal" style="align-self:flex-start">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                    </div>
                </div>
            </div>

            <!-- tab 2 · Horarios -->
            <div class="tab-content" data-panel="2">
                <div class="feature-content-container">
                    <div class="feature-content-left">
                        <div class="tab-visual mini-shifts">
                            <div>Mañana <span class="shift-bar"><span style="width:82%"></span></span> <small>8–2</small></div>
                            <div>Tarde <span class="shift-bar"><span style="width:55%"></span></span> <small>2–8</small></div>
                            <div>Noche <span class="shift-bar"><span style="width:35%"></span></span> <small>8–2</small></div>
                        </div>
                    </div>
                    <div class="feature-content-right">
                        <h3>Horarios y turnos</h3>
                        <p>Define turnos fijos o rotativos por área o sede, y consulta el calendario de cada trabajador.</p>
                        <ul class="feature-list">
                            <li><span class="material-symbols-rounded">event_repeat</span> Turnos fijos o rotativos</li>
                            <li><span class="material-symbols-rounded">apartment</span> Por área o sede</li>
                            <li><span class="material-symbols-rounded">calendar_month</span> Calendario por persona</li>
                        </ul>
                        <a href="#contacto" class="mm-btn mm-btn-tonal" style="align-self:flex-start">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                    </div>
                </div>
            </div>

            <!-- tab 3 · Reportes -->
            <div class="tab-content" data-panel="3">
                <div class="feature-content-container">
                    <div class="feature-content-left">
                        <div class="tab-visual mini-bars">
                            <i></i><i class="hot"></i><i></i><i class="hot"></i><i></i><i class="hot"></i><i></i><i class="hot"></i>
                        </div>
                    </div>
                    <div class="feature-content-right">
                        <h3>Reportes para planilla</h3>
                        <p>Horas, extras y descuentos exportados en un clic. Tu contador te lo va a agradecer.</p>
                        <ul class="feature-list">
                            <li><span class="material-symbols-rounded">calculate</span> Horas y extras calculadas solas</li>
                            <li><span class="material-symbols-rounded">file_download</span> Exportación en un clic</li>
                            <li><span class="material-symbols-rounded">payments</span> Descuentos por falta y tardanza</li>
                        </ul>
                        <a href="#contacto" class="mm-btn mm-btn-tonal" style="align-self:flex-start">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                    </div>
                </div>
            </div>

            <!-- tab 4 · Multi-sede -->
            <div class="tab-content" data-panel="4">
                <div class="feature-content-container">
                    <div class="feature-content-left">
                        <div class="tab-visual mini-sedes">
                            <div><span class="material-symbols-rounded">storefront</span> Sede Pucallpa <span class="ms-live">En vivo</span></div>
                            <div><span class="material-symbols-rounded">storefront</span> Sede Lima <span class="ms-live">En vivo</span></div>
                            <div><span class="material-symbols-rounded">warehouse</span> Almacén central <span class="ms-live">En vivo</span></div>
                        </div>
                    </div>
                    <div class="feature-content-right">
                        <h3>Multi-sede y móvil</h3>
                        <p>Varias sucursales bajo control desde tu celular, con permisos por supervisor.</p>
                        <ul class="feature-list">
                            <li><span class="material-symbols-rounded">storefront</span> Varias sucursales a la vez</li>
                            <li><span class="material-symbols-rounded">badge</span> Permisos por supervisor</li>
                            <li><span class="material-symbols-rounded">visibility</span> Visión general en vivo</li>
                        </ul>
                        <a href="#contacto" class="mm-btn mm-btn-tonal" style="align-self:flex-start">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ MULTIPLATAFORMA ═══════════ -->
    <section class="mm-section">
        <div class="mm-section-head reveal">
            <span class="title-text">Multiplataforma</span>
            <h2 class="main-title">En la palma de tu mano</h2>
            <p class="mm-section-sub">Accede a tu control de asistencia desde cualquier dispositivo: teléfono, tablet u ordenador.</p>
        </div>
        <div class="mm-phone-mock reveal" data-delay="1">
            <img src="<?= base_url('images/asisten-iphone.webp') ?>" alt="Asisten DSG en un iPhone" loading="lazy" width="700" height="1513">
        </div>
        <div class="devices-grid reveal" data-delay="2">
            <div class="device-card">
                <span class="material-symbols-rounded">smartphone</span>
                <h3>Celular</h3>
                <p>Tu personal marca desde donde sea y tú revisas todo en movimiento.</p>
            </div>
            <div class="device-card">
                <span class="material-symbols-rounded">tablet_mac</span>
                <h3>Tablet</h3>
                <p>Ideal para recepción: supervisa ingresos y salidas en el lugar.</p>
            </div>
            <div class="device-card">
                <span class="material-symbols-rounded">computer</span>
                <h3>Computadora</h3>
                <p>Genera reportes completos y gestiona sedes, turnos y personal.</p>
            </div>
        </div>
    </section>

    <!-- ═══════════ TESTIMONIOS ═══════════ -->
    <section class="mm-section">
        <div class="mm-section-head reveal">
            <span class="title-text">Testimonios</span>
            <h2 class="main-title">Lo que dicen de Asisten DSG</h2>
        </div>
        <div class="testimonios-grid reveal" data-delay="1">
            <div class="testimonio-card">
                <span class="material-symbols-rounded testimonio-quote">format_quote</span>
                <p class="testimonio-text">Pasamos del cuaderno a tener todo el personal controlado desde el celular. La planilla sale sola.</p>
                <div class="testimonio-author">
                    <span class="testimonio-name">Juan Alberto</span>
                    <span class="testimonio-role">Vifarma, Perú</span>
                </div>
            </div>
            <div class="testimonio-card">
                <span class="material-symbols-rounded testimonio-quote">format_quote</span>
                <p class="testimonio-text">Marcamos con QR en dos sedes y las tardanzas ya no se discuten: todo queda registrado con hora y minuto.</p>
                <div class="testimonio-author">
                    <span class="testimonio-name">María Torres</span>
                    <span class="testimonio-role">Farmacia San Martín</span>
                </div>
            </div>
            <div class="testimonio-card">
                <span class="material-symbols-rounded testimonio-quote">format_quote</span>
                <p class="testimonio-text">El reporte de planilla sale listo cada mes: horas, extras y descuentos sin pelearme con el Excel.</p>
                <div class="testimonio-author">
                    <span class="testimonio-name">Carlos Ruiz</span>
                    <span class="testimonio-role">Grupo La Plaza</span>
                </div>
            </div>
            <div class="testimonio-card">
                <span class="material-symbols-rounded testimonio-quote">format_quote</span>
                <p class="testimonio-text">Empezamos con diez empleados y hoy controlamos tres sedes desde el celular, con permisos por supervisor.</p>
                <div class="testimonio-author">
                    <span class="testimonio-name">Lucía Fernández</span>
                    <span class="testimonio-role">Comercial Cusco</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ PRECIOS ═══════════ -->
    <section class="mm-section" id="precios">
        <div class="mm-section-head reveal">
            <span class="title-text">Precios</span>
            <h2 class="main-title">Precios para todos</h2>
            <p class="mm-section-sub">Elige el plan que mejor se adapte a tu negocio. Todos incluyen soporte y actualizaciones.</p>
        </div>
        <div class="pricing-grid reveal" data-delay="1">
            <div class="pricing-card">
                <span class="pricing-tag">Básico</span>
                <div class="pricing-price"><span class="pricing-amount">S/ 150</span><span class="pricing-period">/mes</span></div>
                <p class="pricing-desc">Para negocios pequeños con hasta 10 empleados.</p>
                <ul class="feature-list">
                    <li><span class="material-symbols-rounded">check</span> Hasta 10 empleados</li>
                    <li><span class="material-symbols-rounded">check</span> Marcación con código QR</li>
                    <li><span class="material-symbols-rounded">check</span> Control de tardanzas</li>
                    <li><span class="material-symbols-rounded">check</span> Reporte mensual básico</li>
                    <li><span class="material-symbols-rounded">check</span> 1 sede</li>
                </ul>
                <a href="#contacto" class="mm-btn mm-btn-tonal">Solicitar demo</a>
            </div>
            <div class="pricing-card">
                <span class="recommended-badge">Más popular</span>
                <span class="pricing-tag">Negocio</span>
                <div class="pricing-price"><span class="pricing-amount">S/ 290</span><span class="pricing-period">/mes</span></div>
                <p class="pricing-desc">Para negocios en crecimiento con hasta 30 empleados.</p>
                <ul class="feature-list">
                    <li><span class="material-symbols-rounded">check</span> Hasta 30 empleados</li>
                    <li><span class="material-symbols-rounded">check</span> Marcación con código QR</li>
                    <li><span class="material-symbols-rounded">check</span> Tardanzas y faltas</li>
                    <li><span class="material-symbols-rounded">check</span> Horarios y turnos fijos</li>
                    <li><span class="material-symbols-rounded">check</span> Reportes para planilla</li>
                    <li><span class="material-symbols-rounded">check</span> 2 sedes</li>
                    <li><span class="material-symbols-rounded">check</span> Alertas en tiempo real</li>
                    <li><span class="material-symbols-rounded">check</span> Soporte prioritario</li>
                </ul>
                <a href="#contacto" class="mm-btn mm-btn-filled">Solicitar demo</a>
            </div>
            <div class="pricing-card">
                <span class="pricing-tag">Corporativo</span>
                <div class="pricing-price"><span class="pricing-amount">Cotizar</span></div>
                <p class="pricing-desc">Para empresas con múltiples sedes y personal variable.</p>
                <ul class="feature-list">
                    <li><span class="material-symbols-rounded">check</span> Empleados ilimitados</li>
                    <li><span class="material-symbols-rounded">check</span> Marcación con código QR</li>
                    <li><span class="material-symbols-rounded">check</span> Turnos rotativos avanzados</li>
                    <li><span class="material-symbols-rounded">check</span> Sedes ilimitadas</li>
                    <li><span class="material-symbols-rounded">check</span> Exportación a planilla</li>
                    <li><span class="material-symbols-rounded">check</span> API de integración</li>
                    <li><span class="material-symbols-rounded">check</span> Soporte dedicado y capacitación</li>
                </ul>
                <a href="#contacto" class="mm-btn mm-btn-tonal">Solicitar cotización</a>
            </div>
        </div>
        <p class="pricing-note">¿Necesitas ver el detalle? <a href="<?= base_url('precio-asisten') ?>">Ver planes completos →</a></p>
    </section>

    <!-- ═══════════ FAQ ═══════════ -->
    <section class="mm-section" id="faq">
        <div class="mm-section-head reveal">
            <span class="title-text">Preguntas frecuentes</span>
            <h2 class="main-title">Resolvemos tus dudas</h2>
        </div>
        <div class="faq-list reveal" data-delay="1">
            <details class="faq-item">
                <summary>¿Cómo marca mi personal?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Cada trabajador escanea un código QR desde su celular para registrar ingreso y salida. No necesita instalar apps ni comprar equipos.</div>
            </details>
            <details class="faq-item">
                <summary>¿Sirve para varias sedes?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Sí. El plan Negocio incluye 2 sedes y el plan Corporativo sedes ilimitadas, cada una con sus propios horarios y supervisores.</div>
            </details>
            <details class="faq-item">
                <summary>¿Puedo exportar a mi planilla?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Sí. Los reportes incluyen horas trabajadas, extras, tardanzas y descuentos, exportables en un clic para tu proceso de planilla.</div>
            </details>
            <details class="faq-item">
                <summary>¿Puedo cambiar de plan después?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Sí, puedes subir o bajar de plan en cualquier momento. El cambio se refleja en tu siguiente ciclo de facturación.</div>
            </details>
            <details class="faq-item">
                <summary>¿Hay contrato o permanencia mínima?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">No, no hay contratos ni permanencia. Puedes cancelar cuando quieras sin penalidades.</div>
            </details>
            <details class="faq-item">
                <summary>¿Puedo probar gratis antes de contratar?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Sí, ofrecemos una demo gratuita de 15 días sin necesidad de tarjeta de crédito. Prueba todas las funciones antes de decidir.</div>
            </details>
            <details class="faq-item">
                <summary>¿Qué incluye el soporte?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Todos los planes incluyen soporte por chat y correo. Los planes Negocio y Corporativo incluyen soporte prioritario con respuesta en menos de 2 horas.</div>
            </details>
            <details class="faq-item">
                <summary>¿Mis datos están seguros?<span class="material-symbols-rounded faq-chevron">expand_more</span></summary>
                <div class="faq-answer">Los datos de tu personal viajan cifrados y solo los ven tú y los supervisores que tú autorices.</div>
            </details>
        </div>
    </section>

    <!-- ═══════════ CONTACTO / SOLICITAR DEMO ═══════════ -->
    <section class="mm-section" id="contacto">
        <div class="mm-section-head reveal">
            <span class="title-text">Contacto</span>
            <h2 class="main-title">Solicita tu demo gratuita</h2>
            <p class="mm-section-sub">Cuéntanos cuántas personas y sedes manejas y te mostramos Asisten DSG funcionando. Respondemos en menos de 24 horas.</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mm-form-flash is-success reveal"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mm-form-flash is-error reveal"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="mm-contact-grid reveal" data-delay="1">
            <div class="mm-contact-info content-box">
                <span class="title-text">Escríbenos</span>
                <a class="mm-contact-line" href="mailto:soporte@dsgperu.com">
                    <span class="mm-contact-ic"><span class="material-symbols-rounded">mail</span></span>
                    <span><small>Correo</small>soporte@dsgperu.com</span>
                </a>
                <a class="mm-contact-line" href="tel:+51923942001">
                    <span class="mm-contact-ic"><span class="material-symbols-rounded">call</span></span>
                    <span><small>Llámanos</small>+51 923 942 001</span>
                </a>
                <div class="mm-contact-line">
                    <span class="mm-contact-ic"><span class="material-symbols-rounded">location_on</span></span>
                    <span><small>Visítanos</small>Pucallpa, Perú</span>
                </div>
                <p class="mm-contact-note">También puedes solicitar una cotización para el plan Corporativo: respondemos con una propuesta a la medida de tus sedes.</p>
            </div>

            <div class="mm-contact-form content-box">
                <form action="<?= base_url('enviar') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="asunto" value="Solicitar demo · Asisten DSG">
                    <div class="mm-field">
                        <input type="text" name="nombre" placeholder="Nombre completo" required>
                    </div>
                    <div class="mm-field-row">
                        <div class="mm-field"><input type="email" name="correo" placeholder="Correo electrónico" required></div>
                        <div class="mm-field"><input type="text" name="empresa" placeholder="Empresa"></div>
                    </div>
                    <div class="mm-field">
                        <input type="tel" name="telefono" placeholder="Teléfono / WhatsApp">
                    </div>
                    <div class="mm-field">
                        <textarea name="mensaje" rows="4" placeholder="¿Cuántas personas y sedes quieres controlar?"></textarea>
                    </div>
                    <button type="submit" class="mm-btn mm-btn-filled mm-btn-submit">Solicitar demo <span class="material-symbols-rounded">arrow_forward</span></button>
                </form>
            </div>
        </div>
    </section>

</main>

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="mm-footer">
    <div class="mm-footer-main">
        <div class="mm-footer-brand">
            <a href="<?= base_url('asisten-dsg') ?>" class="mm-nav-logo">
                <img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG Logo">
                <span>Asisten DSG</span>
            </a>
            <p>Control de asistencia del personal con QR: marcación, tardanzas y reportes de planilla en un solo lugar.</p>
            <p class="mm-cookie"><span class="material-symbols-rounded">cookie</span> Usamos cookies técnicas para el funcionamiento de la web y cookies de preferencias si aceptas “Recordarme”.</p>
        </div>
        <div class="mm-footer-col">
            <h4>Contacto</h4>
            <a href="mailto:soporte@dsgperu.com"><span class="material-symbols-rounded">mail</span> soporte@dsgperu.com</a>
        </div>
        <div class="mm-footer-col">
            <h4>Acciones</h4>
            <a href="#contacto"><span class="material-symbols-rounded">forum</span> Contáctanos</a>
            <a href="<?= base_url('precio-asisten') ?>"><span class="material-symbols-rounded">receipt_long</span> Precios</a>
            <a href="<?= base_url('login-verde') ?>"><span class="material-symbols-rounded">login</span> Iniciar sesión</a>
            <a href="<?= base_url('/') ?>"><span class="material-symbols-rounded">language</span> Sitio principal</a>
        </div>
    </div>
    <div class="mm-footer-bottom">
        <p>&copy; <?= date('Y') ?> DSG Perú Technology · Asisten DSG. Todos los derechos reservados.</p>
    </div>
</footer>

<a class="mm-fab" href="#contacto" aria-label="Contáctanos">
    <span class="material-symbols-rounded">chat_bubble</span>
</a>

<script>
(function () {
    /* — nav: compacto al hacer scroll — */
    var nav = document.getElementById('mmNav');
    function onScroll() {
        if (!nav) return;
        if ((window.scrollY || 0) > 40) nav.setAttribute('nav-compacted', '');
        else nav.removeAttribute('nav-compacted');
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* — menú móvil — */
    var toggle = document.getElementById('mmToggle');
    var panel = document.getElementById('mmMobile');
    function setOpen(open) {
        if (!toggle || !panel) return;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.querySelector('.material-symbols-rounded').textContent = open ? 'close' : 'menu';
        panel.hidden = !open;
    }
    if (toggle && panel) {
        toggle.addEventListener('click', function () { setOpen(panel.hidden); });
        panel.addEventListener('click', function (e) { if (e.target.closest('a')) setOpen(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.addEventListener('resize', function () { if (window.innerWidth > 680) setOpen(false); });
    }

    /* — tabs de características — */
    var tabBtns = document.querySelectorAll('.tab-button-pill');
    var tabPanels = document.querySelectorAll('.tab-content');
    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-tab');
            tabBtns.forEach(function (b) { b.classList.toggle('active', b === btn); });
            tabPanels.forEach(function (p) { p.classList.toggle('active', p.getAttribute('data-panel') === id); });
        });
    });

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

    /* — video demo: miniatura con play — */
    var vidBox = document.getElementById('mm-video-container');
    var vidBtn = document.getElementById('mmPlayVideo');
    var vidEl = document.getElementById('mmMainVideo');
    if (vidBox && vidBtn && vidEl) {
        vidBtn.addEventListener('click', function () {
            vidBox.classList.add('is-playing');
            var p = vidEl.play();
            if (p && typeof p.catch === 'function') { p.catch(function () {}); }
            vidEl.focus();
        });
        vidEl.addEventListener('ended', function () {
            vidBox.classList.remove('is-playing');
            vidEl.pause();
            vidEl.currentTime = 0;
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
