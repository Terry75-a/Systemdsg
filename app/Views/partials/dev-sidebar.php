<!-- DEV SIDEBAR -->
<aside class="dev-sidebar" id="devSidebar">
    <div class="dev-sidebar-head">
        <a href="<?= base_url('dios') ?>" class="dev-logo">
            <span class="dev-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG"></span>
            <span class="dev-logo-text"><strong>Dev Panel</strong><small>DEV-001</small></span>
        </a>
    </div>
    <nav class="dev-nav">
        <a href="<?= base_url('dios') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'dashboard' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="<?= base_url('dios/admins') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'admins' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            <span>Administradores</span>
        </a>
        <a href="<?= base_url('dios/codigos') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'codigos' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">vpn_key</span>
            <span>Códigos</span>
        </a>
        <a href="<?= base_url('dios/empleados') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'empleados' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">badge</span>
            <span>Empleados</span>
        </a>
        <a href="<?= base_url('dios/estructura') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'estructura' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">account_tree</span>
            <span>Estructura</span>
        </a>
        <a href="<?= base_url('dios/perfil') ?>" class="dev-nav-link <?= ($activePage ?? '') === 'perfil' ? 'is-active' : '' ?>">
            <span class="material-symbols-outlined">person</span>
            <span>Mi perfil</span>
        </a>
        <a href="<?= base_url('auth/logout') ?>" class="dev-nav-link">
            <span class="material-symbols-outlined">logout</span>
            <span>Cerrar sesión</span>
        </a>
        <div class="dark-toggle-wrap">
            <button class="dark-toggle" id="darkModeToggle" type="button">
                <span class="material-symbols-outlined" id="darkModeIcon">dark_mode</span>
                <span class="dark-toggle-label" id="darkModeLabel">Modo oscuro</span>
            </button>
            <div class="dark-toggle-track" id="darkToggleTrack">
                <div class="dark-toggle-thumb" id="darkToggleThumb"></div>
            </div>
        </div>
    </nav>
    <div class="dev-sidebar-footer">
        <a href="<?= base_url('dios/perfil') ?>" class="dev-sidebar-user">
            <span class="dev-sidebar-avatar">DV</span>
            <div>
                <span class="dev-sidebar-user-name">Superadmin</span>
                <span class="dev-sidebar-user-role">Desarrollador</span>
            </div>
        </a>
    </div>
</aside>

<!-- ═══════════════════════════════════════
     MOBILE NAVS
     ═══════════════════════════════════════ -->

<!-- Left pill nav -->
<nav class="dev-mobile-nav" id="mobileNav">
    <a href="<?= base_url('dios') ?>" class="<?= ($activePage ?? '') === 'dashboard' ? 'is-active' : '' ?>" title="Dashboard">
        <span class="material-symbols-outlined">dashboard</span>
    </a>
    <a href="<?= base_url('dios/admins') ?>" class="<?= ($activePage ?? '') === 'admins' ? 'is-active' : '' ?>" title="Admins">
        <span class="material-symbols-outlined">admin_panel_settings</span>
    </a>
    <a href="<?= base_url('dios/codigos') ?>" class="<?= ($activePage ?? '') === 'codigos' ? 'is-active' : '' ?>" title="Códigos">
        <span class="material-symbols-outlined">vpn_key</span>
    </a>
    <a href="<?= base_url('dios/empleados') ?>" class="<?= ($activePage ?? '') === 'empleados' ? 'is-active' : '' ?>" title="Empleados">
        <span class="material-symbols-outlined">badge</span>
    </a>
    <a href="<?= base_url('dios/estructura') ?>" class="<?= ($activePage ?? '') === 'estructura' ? 'is-active' : '' ?>" title="Estructura">
        <span class="material-symbols-outlined">account_tree</span>
    </a>
</nav>

<!-- Mobile dark mode btn — top right -->
<button class="dev-mobile-dark-btn" id="mobileDarkBtn" type="button" title="Modo oscuro">
    <span class="material-symbols-outlined" id="mobileDarkIcon">dark_mode</span>
</button>

<!-- Modal — mismo elemento que el btn -->
<div class="dev-mobile-overlay" id="mobileOverlay">
    <div class="dev-mobile-modal" id="mobileModal">
        <span class="material-symbols-outlined dev-modal-icon" id="modalIcon">more_vert</span>
        <div class="dev-mobile-modal-section" id="modalSection">
            <a href="<?= base_url('dios/perfil') ?>" class="dev-mobile-modal-link <?= ($activePage ?? '') === 'perfil' ? 'is-active' : '' ?>">
                <span class="material-symbols-outlined">person</span> Mi perfil
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
(function() {
    var modal = document.getElementById('mobileModal');
    var modalIcon = document.getElementById('modalIcon');
    var section = document.getElementById('modalSection');
    var links = modal.querySelectorAll('.dev-mobile-modal-link');
    var overlay = document.getElementById('mobileOverlay');
    var isOpen = false;

    function openMenu() {
        if (isOpen) return;
        isOpen = true;
        overlay.classList.add('is-open');

        gsap.set(links, { opacity: 0, y: 6 });

        var tl = gsap.timeline();

        tl.to(modal, {
            width: 220,
            height: 200,
            borderRadius: 18,
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#ffffff',
            duration: 0.35,
            ease: 'back.out(1.1)'
        });

        tl.to(modalIcon, {
            opacity: 0,
            scale: 0.5,
            duration: 0.15,
            ease: 'power2.in'
        }, 0);

        tl.to(section, {
            opacity: 1,
            duration: 0.01
        }, 0.15);

        tl.to(links, {
            opacity: 1,
            y: 0,
            duration: 0.2,
            stagger: 0.025,
            ease: 'power3.out'
        }, '-=0.15');
    }

    function closeMenu() {
        if (!isOpen) return;

        var tl = gsap.timeline({
            onComplete: function() {
                isOpen = false;
                overlay.classList.remove('is-open');
                gsap.set(modal, { height: 52 });
                gsap.set(section, { opacity: 0 });
            }
        });

        tl.to(links, {
            opacity: 0,
            y: 4,
            duration: 0.1,
            stagger: 0.008,
            ease: 'power2.in'
        });

        tl.to(modalIcon, {
            opacity: 1,
            scale: 1,
            duration: 0.15,
            ease: 'power2.out'
        }, '-=0.05');

        tl.to(modal, {
            width: 52,
            height: 52,
            borderRadius: '50%',
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#1b7a42',
            duration: 0.3,
            ease: 'power3.inOut'
        }, '-=0.1');
    }

    modal.addEventListener('click', function(e) {
        e.stopPropagation();
        if (!isOpen) {
            openMenu();
        }
    });

    overlay.addEventListener('click', function() {
        if (isOpen) closeMenu();
    });

    links.forEach(function(link) {
        link.addEventListener('click', function() {
            closeMenu();
        });
    });

    /* Dark mode */
    var darkToggle = document.getElementById('darkModeToggle');
    var darkIcon = document.getElementById('darkModeIcon');
    var darkLabel = document.getElementById('darkModeLabel');
    var darkTrack = document.getElementById('darkToggleTrack');
    var mobileDarkBtn = document.getElementById('mobileDarkBtn');
    var mobileDarkIcon = document.getElementById('mobileDarkIcon');
    var isDark = localStorage.getItem('devDarkMode') === 'true';

    function applyDark(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            if (darkIcon) darkIcon.textContent = 'light_mode';
            if (darkLabel) darkLabel.textContent = 'Modo claro';
            if (mobileDarkIcon) mobileDarkIcon.textContent = 'light_mode';
        } else {
            document.documentElement.classList.remove('dark');
            if (darkIcon) darkIcon.textContent = 'dark_mode';
            if (darkLabel) darkLabel.textContent = 'Modo oscuro';
            if (mobileDarkIcon) mobileDarkIcon.textContent = 'dark_mode';
        }
    }

    applyDark(isDark);

    function toggleDark() {
        isDark = !isDark;
        localStorage.setItem('devDarkMode', isDark);
        applyDark(isDark);
    }

    if (darkToggle) darkToggle.addEventListener('click', toggleDark);
    if (darkTrack) darkTrack.addEventListener('click', toggleDark);
    if (mobileDarkBtn) mobileDarkBtn.addEventListener('click', toggleDark);
})();
</script>
