<?php
$uri = service('uri');
$seg = (string) $uri->getSegment(1);
$isAdmin = (int) session()->get('id_rol') === 1;
$programadorActivo = isset($this->session->userdata['programdor']) && (int) $this->session->userdata['programdor'] === 1;
$uname = session()->get('username') ?? 'Usuario';
$ini = strtoupper(substr(trim((string) $uname), 0, 2) ?: 'US');
$role = $isAdmin ? 'Administrador' : 'Usuario';

function pdsgSeg(string $c): bool { return $GLOBALS['__pdsg_seg'] === $c; }
function pdsgSegIn(array $c): bool { return in_array($GLOBALS['__pdsg_seg'], $c, true); }
function pdsgCls(bool $ok): string { return $ok ? ' is-active' : ''; }
$GLOBALS['__pdsg_seg'] = $seg;
?>

<aside class="dev-sidebar" id="devSidebar">
    <div class="dev-sidebar-head">
        <a href="<?= base_url('dashboard') ?>" class="dev-logo">
            <span class="dev-logo-mark"><img src="<?= base_url('images/logo_3.1.png') ?>" alt="DSG"></span>
            <span class="dev-logo-text"><strong>Admin Panel</strong><small>DSG Perú</small></span>
        </a>
    </div>

    <?php if ($programadorActivo): ?>
        <div class="dev-alert dev-alert-danger" style="margin:0 12px 10px;padding:10px 12px;">
            <span class="material-symbols-outlined">warning</span>
            <span>Modo Programador Activado</span>
        </div>
    <?php endif; ?>

    <nav class="dev-nav">
        <a href="<?= base_url('dashboard') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('dashboard')) ?>">
            <span class="material-symbols-outlined">home</span>
            <span>Inicio</span>
        </a>
        <a href="<?= base_url('personas') ?>" class="dev-nav-link<?= pdsgCls(pdsgSegIn(['personas', 'personasadd'])) ?>">
            <span class="material-symbols-outlined">groups</span>
            <span>Clientes</span>
        </a>

        <div class="dev-nav-section">Mantenedor</div>
        <a href="<?= base_url('tipo_plan') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('tipo_plan')) ?>">
            <span class="material-symbols-outlined">category</span>
            <span>Tipo Plan</span>
        </a>
        <a href="<?= base_url('planes') ?>" class="dev-nav-link<?= pdsgCls(pdsgSegIn(['planes', 'planesadd'])) ?>">
            <span class="material-symbols-outlined">sell</span>
            <span>Planes</span>
        </a>

        <a href="<?= base_url('calendario') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('calendario')) ?>">
            <span class="material-symbols-outlined">calendar_month</span>
            <span>Calendario</span>
        </a>
        <a href="<?= base_url('perfil') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('perfil')) ?>">
            <span class="material-symbols-outlined">person</span>
            <span>Mi Perfil</span>
        </a>

        <?php if ($isAdmin): ?>
            <div class="dev-nav-section" style="padding-top:24px;">Administración</div>
            <a href="<?= base_url('permisos') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('permisos')) ?>">
                <span class="material-symbols-outlined">admin_panel_settings</span>
                <span>Permisos</span>
            </a>
            <a href="<?= base_url('usuarios') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('usuarios')) ?>">
                <span class="material-symbols-outlined">manage_accounts</span>
                <span>Usuarios</span>
            </a>
            <a href="<?= base_url('menu') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('menu')) ?>">
                <span class="material-symbols-outlined">menu_book</span>
                <span>Menú</span>
            </a>
            <a href="<?= base_url('configuracion') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('configuracion')) ?>">
                <span class="material-symbols-outlined">settings</span>
                <span>Configuración</span>
            </a>
        <?php endif; ?>

        <div class="dev-nav-section" style="padding-top:24px;">General</div>
        <a href="<?= base_url('') ?>" class="dev-nav-link">
            <span class="material-symbols-outlined">language</span>
            <span>Sitio web</span>
        </a>
        <form method="post" action="<?= base_url('logout') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="dev-nav-link" style="width:100%;border:0;background:none;text-align:left;cursor:pointer;font-family:inherit;">
                <span class="material-symbols-outlined">logout</span>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </nav>

    <div class="dev-sidebar-footer">
        <a href="<?= base_url('perfil') ?>" class="dev-sidebar-user">
            <span class="dev-sidebar-avatar"><?= esc($ini) ?></span>
            <span style="min-width:0;">
                <span class="dev-sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($uname) ?></span>
                <span class="dev-sidebar-user-role"><?= esc($role) ?></span>
            </span>
        </a>
    </div>
</aside>

<!-- ═══════════════ MOBILE ═══════════════ -->

<!-- Barra pill inferior -->
<nav class="dev-mobile-nav" id="mobileNav">
    <a href="<?= base_url('dashboard') ?>" class="<?= pdsgSeg('dashboard') ? 'is-active' : '' ?>" title="Inicio">
        <span class="material-symbols-outlined">home</span>
    </a>
    <a href="<?= base_url('personas') ?>" class="<?= pdsgSegIn(['personas', 'personasadd']) ? 'is-active' : '' ?>" title="Clientes">
        <span class="material-symbols-outlined">groups</span>
    </a>
    <a href="<?= base_url('planes') ?>" class="<?= pdsgSegIn(['planes', 'planesadd']) ? 'is-active' : '' ?>" title="Planes">
        <span class="material-symbols-outlined">sell</span>
    </a>
    <a href="<?= base_url('calendario') ?>" class="<?= pdsgSeg('calendario') ? 'is-active' : '' ?>" title="Calendario">
        <span class="material-symbols-outlined">calendar_month</span>
    </a>
</nav>

<!-- Modal morfo — botón inferior derecho -->
<div class="dev-mobile-overlay" id="mobileOverlay">
    <div class="dev-mobile-modal" id="mobileModal">
        <span class="material-symbols-outlined dev-modal-icon" id="modalIcon">more_vert</span>
        <div class="dev-mobile-modal-section" id="modalSection">
            <a href="<?= base_url('perfil') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('perfil')) ?>">
                <span class="material-symbols-outlined">person</span> Mi Perfil
            </a>
            <a href="<?= base_url('tipo_plan') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('tipo_plan')) ?>">
                <span class="material-symbols-outlined">category</span> Tipo Plan
            </a>
            <?php if ($isAdmin): ?>
                <a href="<?= base_url('usuarios') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('usuarios')) ?>">
                    <span class="material-symbols-outlined">manage_accounts</span> Usuarios
                </a>
                <a href="<?= base_url('configuracion') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('configuracion')) ?>">
                    <span class="material-symbols-outlined">settings</span> Configuración
                </a>
            <?php endif; ?>
            <a href="<?= base_url('') ?>" class="dev-mobile-modal-link">
                <span class="material-symbols-outlined">language</span> Sitio web
            </a>
            <form method="post" action="<?= base_url('logout') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="dev-mobile-modal-link" style="width:100%;border:0;background:none;text-align:left;cursor:pointer;font-family:inherit;">
                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                </button>
            </form>
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
            height: 240,
            borderRadius: 18,
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--g-surface').trim() || '#ffffff',
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
                gsap.set(modal, { height: 56 });
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
            width: 56,
            height: 56,
            borderRadius: '50%',
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--g-primary').trim() || '#1b7a42',
            duration: 0.3,
            ease: 'power3.inOut'
        }, '-=0.1');
    }

    modal.addEventListener('click', function(e) {
        e.stopPropagation();
        if (!isOpen) { openMenu(); } else { closeMenu(); }
    });

    overlay.addEventListener('click', function() {
        if (isOpen) closeMenu();
    });

    links.forEach(function(link) {
        link.addEventListener('click', function() {
            closeMenu();
        });
    });

    /* Dark mode (compartido: desktop y móvil).
   El botón vive en topbar.php, que se renderiza DESPUÉS de este script,
   así que se escucha al documento (event delegation). */
    var isDark = localStorage.getItem('devDarkMode') === 'true';
    var desktopDark = document.getElementById('dsgDarkBtn');

    function applyDark(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            if (desktopDark) desktopDark.classList.add('is-on');
        } else {
            document.documentElement.classList.remove('dark');
            if (desktopDark) desktopDark.classList.remove('is-on');
        }
    }

    applyDark(isDark);

    function toggleDark() {
        isDark = !isDark;
        localStorage.setItem('devDarkMode', isDark);
        applyDark(isDark);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('#dsgDarkBtn')) {
            e.preventDefault();
            toggleDark();
        }
    });
})();
</script>