<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de control DSG Perú - Gestiona tu negocio desde un solo lugar.">
    <title>Panel de control · DSG Perú Technology</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dashboard-verde.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside class="dash-sidebar" id="dashSidebar">
    <div class="dash-sidebar-head">
        <a href="<?= base_url('/') ?>" class="dash-logo">
            <span class="dash-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG"></span>
            <span class="dash-logo-text">DSG Perú<small>TECHNOLOGY</small></span>
        </a>
        <button class="dash-sidebar-close" id="sidebarClose" aria-label="Cerrar menú">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="dash-nav" aria-label="Navegación del panel">
        <a href="<?= base_url('dashboard-verde') ?>" class="dash-nav-link is-active">
            <i class="fa-solid fa-house" aria-hidden="true"></i>
            <span>Inicio</span>
        </a>
        <a href="#" class="dash-nav-link">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
            <span>Clientes</span>
        </a>
        <a href="#" class="dash-nav-link">
            <i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i>
            <span>Inventario</span>
        </a>
        <a href="#" class="dash-nav-link">
            <i class="fa-solid fa-cash-register" aria-hidden="true"></i>
            <span>Ventas</span>
        </a>
        <a href="#" class="dash-nav-link">
            <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
            <span>Reportes</span>
        </a>

        <div class="dash-nav-divider"></div>

        <a href="#" class="dash-nav-link">
            <i class="fa-solid fa-gear" aria-hidden="true"></i>
            <span>Configuración</span>
        </a>
        <a href="<?= base_url('auth/logout') ?>" class="dash-nav-link dash-nav-logout">
            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
            <span>Cerrar sesión</span>
        </a>
    </nav>
</aside>

<!-- ═══════════ CONTENIDO PRINCIPAL ═══════════ -->
<div class="dash-main">

    <!-- TOPBAR -->
    <header class="dash-topbar">
        <button class="dash-menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <div class="dash-topbar-title">
            <h1>Panel de control</h1>
            <p>Bienvenido, <?= esc(session()->get('user_name') ?: 'Usuario') ?></p>
        </div>

        <div class="dash-topbar-right">
            <div class="dash-avatar">
                <span><?= strtoupper(substr(session()->get('user_name') ?: 'U', 0, 2)) ?></span>
            </div>
        </div>
    </header>

    <!-- CONTENIDO -->
    <div class="dash-content">

        <!-- WELCOME CARD -->
        <section class="dash-welcome reveal">
            <div class="dash-welcome-text">
                <div class="dash-welcome-eyebrow"><span class="pulse"></span> Resumen del día</div>
                <h2 class="dash-welcome-title">Hola, <?= esc(session()->get('user_name') ?: 'Usuario') ?> <span class="serif-accent">👋</span></h2>
                <p class="dash-welcome-desc">Aquí tienes un resumen de la actividad de tu negocio. Los datos se actualizarán en tiempo real.</p>
            </div>
            <div class="dash-welcome-meta">
                <span class="dash-welcome-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?= date('d/m/Y') ?></span>
                <span class="dash-welcome-role"><i class="fa-solid fa-shield" aria-hidden="true"></i> <?= esc(session()->get('user_role') ?: 'Usuario') ?></span>
            </div>
        </section>

        <!-- STATS GRID -->
        <section class="dash-stats reveal" data-delay="1">
            <div class="dash-stat-card">
                <div class="dash-stat-icon si-blue"><i class="fa-solid fa-users" aria-hidden="true"></i></div>
                <div class="dash-stat-info">
                    <span class="dash-stat-value" data-count="127">0</span>
                    <span class="dash-stat-label">Clientes activos</span>
                </div>
                <span class="dash-stat-badge is-up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> 12%</span>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon si-green"><i class="fa-solid fa-cash-register" aria-hidden="true"></i></div>
                <div class="dash-stat-info">
                    <span class="dash-stat-value">S/ 8,420</span>
                    <span class="dash-stat-label">Ventas este mes</span>
                </div>
                <span class="dash-stat-badge is-up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> 8%</span>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon si-amber"><i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i></div>
                <div class="dash-stat-info">
                    <span class="dash-stat-value" data-count="342">0</span>
                    <span class="dash-stat-label">Productos en stock</span>
                </div>
                <span class="dash-stat-badge is-down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> 3%</span>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon si-purple"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i></div>
                <div class="dash-stat-info">
                    <span class="dash-stat-value" data-count="56">0</span>
                    <span class="dash-stat-label">Facturas este mes</span>
                </div>
                <span class="dash-stat-badge is-up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> 24%</span>
            </div>
        </section>

        <!-- GRID 2 COL: ACTIVIDAD + ACCESOS RÁPIDOS -->
        <section class="dash-grid-2">

            <!-- ACTIVIDAD RECIENTE -->
            <div class="dash-card reveal">
                <div class="dash-card-head">
                    <h3>Actividad reciente</h3>
                    <a href="#" class="dash-card-link">Ver todo →</a>
                </div>
                <div class="dash-activity">
                    <div class="dash-activity-item">
                        <div class="dash-activity-dot dot-green"></div>
                        <div class="dash-activity-info">
                            <p><strong>Nueva venta registrada</strong> — Venta #1089 por S/ 245.00</p>
                            <span>Hace 12 minutos</span>
                        </div>
                    </div>
                    <div class="dash-activity-item">
                        <div class="dash-activity-dot dot-blue"></div>
                        <div class="dash-activity-info">
                            <p><strong>Cliente registrado</strong> — María Fernanda López DNI 45123678</p>
                            <span>Hace 34 minutos</span>
                        </div>
                    </div>
                    <div class="dash-activity-item">
                        <div class="dash-activity-dot dot-amber"></div>
                        <div class="dash-activity-info">
                            <p><strong>Stock bajo</strong> — Paracetamol 500mg (quedan 12 unidades)</p>
                            <span>Hace 1 hora</span>
                        </div>
                    </div>
                    <div class="dash-activity-item">
                        <div class="dash-activity-dot dot-green"></div>
                        <div class="dash-activity-info">
                            <p><strong>Venta pagada</strong> — Factura #1087 — S/ 890.00</p>
                            <span>Hace 2 horas</span>
                        </div>
                    </div>
                    <div class="dash-activity-item">
                        <div class="dash-activity-dot dot-purple"></div>
                        <div class="dash-activity-info">
                            <p><strong>Reporte generado</strong> — Reporte mensual de ventas enviado</p>
                            <span>Hace 3 horas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCESOS RÁPIDOS -->
            <div class="dash-card reveal" data-delay="1">
                <div class="dash-card-head">
                    <h3>Accesos rápidos</h3>
                </div>
                <div class="dash-shortcuts">
                    <a href="#" class="dash-shortcut">
                        <div class="dash-shortcut-icon sc-green"><i class="fa-solid fa-plus" aria-hidden="true"></i></div>
                        <span>Nueva venta</span>
                    </a>
                    <a href="#" class="dash-shortcut">
                        <div class="dash-shortcut-icon sc-blue"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></div>
                        <span>Nuevo cliente</span>
                    </a>
                    <a href="#" class="dash-shortcut">
                        <div class="dash-shortcut-icon sc-amber"><i class="fa-solid fa-box-open" aria-hidden="true"></i></div>
                        <span>Agregar producto</span>
                    </a>
                    <a href="#" class="dash-shortcut">
                        <div class="dash-shortcut-icon sc-purple"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></div>
                        <span>Ver reportes</span>
                    </a>
                </div>

                <!-- GRAFICO FICTICIO -->
                <div class="dash-chart">
                    <div class="dash-chart-head">
                        <span>Ventas de la semana</span>
                        <span class="dash-chart-legend"><span class="dot-leg green"></span> Esta semana</span>
                    </div>
                    <div class="dash-bars">
                        <div class="dash-bar-col"><div class="dash-bar" style="height:45%"><span>L</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar" style="height:62%"><span>M</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar" style="height:38%"><span>Mi</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar is-hot" style="height:85%"><span>J</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar" style="height:71%"><span>V</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar" style="height:54%"><span>S</span></div></div>
                        <div class="dash-bar-col"><div class="dash-bar" style="height:28%"><span>D</span></div></div>
                    </div>
                </div>
            </div>

        </section>

    </div><!-- /.dash-content -->

</div><!-- /.dash-main -->

<script>
(function () {
    /* — sidebar toggle — */
    var sidebar = document.getElementById('dashSidebar');
    var toggle = document.getElementById('menuToggle');
    var close = document.getElementById('sidebarClose');
    function openSidebar() { sidebar.classList.add('open'); }
    function closeSidebar() { sidebar.classList.remove('open'); }
    if (toggle) toggle.addEventListener('click', openSidebar);
    if (close) close.addEventListener('click', closeSidebar);

    /* — counter animation — */
    var counters = document.querySelectorAll('.dash-stat-value[data-count]');
    function animateCount(el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        var current = 0;
        var step = Math.max(1, Math.floor(target / 40));
        var timer = setInterval(function () {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current.toLocaleString('es-PE');
        }, 25);
    }
    if ('IntersectionObserver' in window && counters.length) {
        var cio = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { animateCount(en.target); cio.unobserve(en.target); }
            });
        }, { threshold: 0.3 });
        counters.forEach(function (c) { cio.observe(c); });
    } else {
        counters.forEach(function (c) { animateCount(c); });
    }

    /* — reveal on scroll — */
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
