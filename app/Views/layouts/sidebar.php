<?php
$uri = service('uri');
$seg = (string) $uri->getSegment(1);
$idRol = (int) session()->get('id_rol');
$isAdmin = ($idRol === 1);
$programadorActivo = (int) session()->get('programdor') === 1;
$uname = (string) (session()->get('username') ?? 'Usuario');
$ini = strtoupper(substr(trim($uname), 0, 2) ?: 'US');
$role = (string) (session()->get('rol_nombre') ?: ($isAdmin ? 'Administrador' : 'Usuario'));
$sbAvatarPath = trim((string) (session()->get('imagen') ?? ''));
if ($sbAvatarPath === '') {
    $sbIdPersona = (int) session()->get('id_persona');
    if ($sbIdPersona > 0) {
        $sbPersonaAvatar = (new \App\Models\PersonaModel())->select('imagen')->find($sbIdPersona);
        if (!empty($sbPersonaAvatar->imagen)) {
            $sbAvatarPath = trim((string) $sbPersonaAvatar->imagen);
            session()->set('imagen', $sbAvatarPath);
        }
    }
}

$sbAvatarUrl = '';
if ($sbAvatarPath !== '') {
    if (preg_match('#^https?://#i', $sbAvatarPath)) {
        $sbAvatarUrl = $sbAvatarPath;
    } else {
        $sbRel = ltrim($sbAvatarPath, '/');
        if (is_file(FCPATH . $sbRel)) {
            $sbAvatarUrl = base_url($sbRel) . '?v=' . filemtime(FCPATH . $sbRel);
        }
    }
}

$allowedLinks = [];
$allowedNames = [];

if (!$isAdmin && $idRol > 0) {
    $permisosRol = (new \App\Models\PermisosModel())->obtenerPermisosPorRol($idRol);
    foreach ($permisosRol as $perm) {
        if ((int) ($perm->read ?? 0) !== 1) {
            continue;
        }

        $link = trim(strtolower((string) ($perm->link ?? '')), '/');
        if ($link !== '') {
            $allowedLinks[$link] = true;
        }

        $nombre = mb_strtolower(trim((string) ($perm->menu_nombre ?? '')), 'UTF-8');
        if ($nombre !== '') {
            $allowedNames[$nombre] = true;
        }
    }
}

$pdsgPuedeVer = static function (array $links, array $names = []) use ($isAdmin, $allowedLinks, $allowedNames): bool {
    if ($isAdmin) {
        return true;
    }

    foreach ($links as $l) {
        if (isset($allowedLinks[trim(strtolower($l), '/')])) {
            return true;
        }
    }

    foreach ($names as $n) {
        if (isset($allowedNames[mb_strtolower(trim($n), 'UTF-8')])) {
            return true;
        }
    }

    return false;
};

$verInicio        = $pdsgPuedeVer(['dashboard', 'inicio'], ['inicio', 'dashboard']);
$verClientes      = $pdsgPuedeVer(['personas', 'clientes'], ['personas', 'clientes']);
$verTipoPlan      = $pdsgPuedeVer(['tipo_plan'], ['tipo plan', 'tipo de plan', 'tipo_plan']);
$verPlanes        = $pdsgPuedeVer(['planes'], ['planes', 'plan']);
$verCalendario    = $pdsgPuedeVer(['calendario'], ['calendario']);
$verPerfil        = $pdsgPuedeVer(['perfil'], ['mi perfil', 'perfil']);
$verPermisos      = $pdsgPuedeVer(['permisos'], ['permisos']);
$verUsuarios      = $pdsgPuedeVer(['usuarios'], ['usuarios']);
$verMenu          = $pdsgPuedeVer(['menu', 'menus'], ['menú', 'menu']);
$verConfiguracion = $pdsgPuedeVer(['configuracion'], ['configuración', 'configuracion']);

$mostrarGeneral    = in_array(true, [$verInicio, $verClientes], true);
$mostrarMantenedor = in_array(true, [$verTipoPlan, $verPlanes, $verCalendario, $verPerfil], true);
$mostrarAdmin      = in_array(true, [$verPermisos, $verUsuarios, $verMenu, $verConfiguracion], true);

if (!function_exists('pdsgSeg')) {
    function pdsgSeg(string $c): bool { return $GLOBALS['__pdsg_seg'] === $c; }
    function pdsgSegIn(array $c): bool { return in_array($GLOBALS['__pdsg_seg'], $c, true); }
    function pdsgCls(bool $ok): string { return $ok ? ' is-active' : ''; }
}
$GLOBALS['__pdsg_seg'] = $seg;
?>

<script>try{if(localStorage.getItem('devSidebarCollapsed')==='1')document.documentElement.classList.add('sb-collapsed')}catch(e){}</script>
<aside class="dev-sidebar" id="devSidebar">
    <div class="dev-sidebar-head">
        <a href="<?= base_url($verInicio ? 'dashboard' : '') ?>" class="dev-logo">
            <span class="dev-logo-mark"><img src="<?= base_url('images/dsg-logo.svg') ?>" alt="DSG"></span>
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
        <?php if ($mostrarGeneral): ?>
            <div class="dev-nav-section">General</div>
            <?php if ($verInicio): ?>
                <a href="<?= base_url('dashboard') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('dashboard')) ?>">
                    <span class="material-symbols-outlined">home</span>
                    <span>Inicio</span>
                </a>
            <?php endif; ?>
            <?php if ($verClientes): ?>
                <a href="<?= base_url('personas') ?>" class="dev-nav-link<?= pdsgCls(pdsgSegIn(['personas', 'personasadd'])) ?>">
                    <span class="material-symbols-outlined">groups</span>
                    <span>Clientes</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($mostrarMantenedor): ?>
            <div class="dev-nav-section">Mantenedor</div>
            <?php if ($verTipoPlan): ?>
                <a href="<?= base_url('tipo_plan') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('tipo_plan')) ?>">
                    <span class="material-symbols-outlined">category</span>
                    <span>Tipo Plan</span>
                </a>
            <?php endif; ?>
            <?php if ($verPlanes): ?>
                <a href="<?= base_url('planes') ?>" class="dev-nav-link<?= pdsgCls(pdsgSegIn(['planes', 'planesadd'])) ?>">
                    <span class="material-symbols-outlined">sell</span>
                    <span>Planes</span>
                </a>
            <?php endif; ?>
            <?php if ($verCalendario): ?>
                <a href="<?= base_url('calendario') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('calendario')) ?>">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <span>Calendario</span>
                </a>
            <?php endif; ?>
            <?php if ($verPerfil): ?>
                <a href="<?= base_url('perfil') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('perfil')) ?>">
                    <span class="material-symbols-outlined">person</span>
                    <span>Mi Perfil</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($mostrarAdmin): ?>
            <div class="dev-nav-section">Administración</div>
            <?php if ($verPermisos): ?>
                <a href="<?= base_url('permisos') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('permisos')) ?>">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <span>Permisos</span>
                </a>
            <?php endif; ?>
            <?php if ($verUsuarios): ?>
                <a href="<?= base_url('usuarios') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('usuarios')) ?>">
                    <span class="material-symbols-outlined">manage_accounts</span>
                    <span>Usuarios</span>
                </a>
            <?php endif; ?>
            <?php if ($verMenu): ?>
                <a href="<?= base_url('menu') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('menu')) ?>">
                    <span class="material-symbols-outlined">menu_book</span>
                    <span>Menú</span>
                </a>
            <?php endif; ?>
            <?php if ($verConfiguracion): ?>
                <a href="<?= base_url('configuracion') ?>" class="dev-nav-link<?= pdsgCls(pdsgSeg('configuracion')) ?>">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Configuración</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <div class="dev-sidebar-tools">
        <div class="dev-nav-section">Herramientas</div>
        <button type="button" class="dev-nav-link dev-tool" id="darkModeToggle" onclick="var b=document.getElementById('dsgDarkBtn');if(b)b.click();">
            <span class="material-symbols-outlined" id="darkModeIcon">dark_mode</span>
            <span id="darkModeLabel">Modo oscuro</span>
        </button>
        <button type="button" class="dev-nav-link dev-tool is-muted" id="sidebarCollapseBtn" data-icons="fixed" aria-label="Colapsar menú" aria-expanded="true">
            <span class="material-symbols-outlined">chevron_left</span>
            <span>Colapsar</span>
        </button>
        <div class="dev-nav-section dev-tools-sep">Cuenta</div>
        <?php if ($verPerfil): ?>
            <a href="<?= base_url('perfil') ?>" class="dev-sidebar-user">
                <span class="dev-sidebar-avatar">
                    <?php if ($sbAvatarUrl !== ''): ?>
                        <img src="<?= esc($sbAvatarUrl) ?>"
                             alt="<?= esc($uname) ?>"
                             class="dsg-user-avatar-img"
                             onerror="this.parentElement.textContent='<?= esc($ini, 'js') ?>';">
                    <?php else: ?>
                        <?= esc($ini) ?>
                    <?php endif; ?>
                </span>
                <span style="min-width:0;">
                    <span class="dev-sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($uname) ?></span>
                    <span class="dev-sidebar-user-role"><?= esc($role) ?></span>
                </span>
            </a>
        <?php else: ?>
            <div class="dev-sidebar-user">
                <span class="dev-sidebar-avatar">
                    <?php if ($sbAvatarUrl !== ''): ?>
                        <img src="<?= esc($sbAvatarUrl) ?>"
                             alt="<?= esc($uname) ?>"
                             class="dsg-user-avatar-img"
                             onerror="this.parentElement.textContent='<?= esc($ini, 'js') ?>';">
                    <?php else: ?>
                        <?= esc($ini) ?>
                    <?php endif; ?>
                </span>
                <span style="min-width:0;">
                    <span class="dev-sidebar-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($uname) ?></span>
                    <span class="dev-sidebar-user-role"><?= esc($role) ?></span>
                </span>
            </div>
        <?php endif; ?>
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
    </div>
</aside>

<!-- ═══════════════ MOBILE ═══════════════ -->

<nav class="dev-mobile-nav" id="mobileNav">
    <?php if ($verInicio): ?>
        <a href="<?= base_url('dashboard') ?>" class="<?= pdsgSeg('dashboard') ? 'is-active' : '' ?>" title="Inicio">
            <span class="material-symbols-outlined">home</span>
        </a>
    <?php endif; ?>
    <?php if ($verClientes): ?>
        <a href="<?= base_url('personas') ?>" class="<?= pdsgSegIn(['personas', 'personasadd']) ? 'is-active' : '' ?>" title="Clientes">
            <span class="material-symbols-outlined">groups</span>
        </a>
    <?php endif; ?>
    <?php if ($verPlanes): ?>
        <a href="<?= base_url('planes') ?>" class="<?= pdsgSegIn(['planes', 'planesadd']) ? 'is-active' : '' ?>" title="Planes">
            <span class="material-symbols-outlined">sell</span>
        </a>
    <?php endif; ?>
    <?php if ($verCalendario): ?>
        <a href="<?= base_url('calendario') ?>" class="<?= pdsgSeg('calendario') ? 'is-active' : '' ?>" title="Calendario">
            <span class="material-symbols-outlined">calendar_month</span>
        </a>
    <?php endif; ?>
</nav>

<div class="dev-mobile-overlay" id="mobileOverlay">
    <div class="dev-mobile-modal" id="mobileModal">
        <span class="material-symbols-outlined dev-modal-icon" id="modalIcon">more_vert</span>
        <div class="dev-mobile-modal-section" id="modalSection">
            <?php if ($verPerfil): ?>
                <a href="<?= base_url('perfil') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('perfil')) ?>">
                    <span class="material-symbols-outlined">person</span> Mi Perfil
                </a>
            <?php endif; ?>
            <?php if ($verTipoPlan): ?>
                <a href="<?= base_url('tipo_plan') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('tipo_plan')) ?>">
                    <span class="material-symbols-outlined">category</span> Tipo Plan
                </a>
            <?php endif; ?>
            <?php if ($verPermisos): ?>
                <a href="<?= base_url('permisos') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('permisos')) ?>">
                    <span class="material-symbols-outlined">admin_panel_settings</span> Permisos
                </a>
            <?php endif; ?>
            <?php if ($verUsuarios): ?>
                <a href="<?= base_url('usuarios') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('usuarios')) ?>">
                    <span class="material-symbols-outlined">manage_accounts</span> Usuarios
                </a>
            <?php endif; ?>
            <?php if ($verMenu): ?>
                <a href="<?= base_url('menu') ?>" class="dev-mobile-modal-link<?= pdsgCls(pdsgSeg('menu')) ?>">
                    <span class="material-symbols-outlined">menu_book</span> Menú
                </a>
            <?php endif; ?>
            <?php if ($verConfiguracion): ?>
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
        gsap.set(modal, { width: 224 });

        var contentH = 16;
        links.forEach(function(l) { contentH += l.offsetHeight; });

        gsap.set(modal, { width: 48, height: 48 });

        var tl = gsap.timeline();

        tl.to(modal, {
            width: 224,
            height: Math.min(contentH, window.innerHeight - 100),
            borderRadius: '24px',
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--sb-surface').trim() || '#ffffff',
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

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function() {
                if (!isOpen) return;
                var h = 16;
                links.forEach(function(l) { h += l.offsetHeight; });
                gsap.to(modal, {
                    height: Math.min(h, window.innerHeight - 100),
                    duration: 0.25,
                    ease: 'power2.out',
                    overwrite: 'auto'
                });
            });
        }
    }

    function closeMenu() {
        if (!isOpen) return;
        isOpen = false;

        var tl = gsap.timeline({
            onComplete: function() {
                overlay.classList.remove('is-open');
                gsap.set(modal, { clearProps: 'width,height,borderRadius,backgroundColor' });
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
            width: 48,
            height: 48,
            borderRadius: '50%',
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--sb-surface').trim() || '#ffffff',
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
        link.addEventListener('click', function(e) {
            e.stopPropagation();
            closeMenu();
        });
    });

    var isDark = localStorage.getItem('devDarkMode') === 'true';
    var desktopDark = document.getElementById('dsgDarkBtn');
    var darkIcon = document.getElementById('darkModeIcon');
    var darkLabel = document.getElementById('darkModeLabel');

    function applyDark(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            if (desktopDark) desktopDark.classList.add('is-on');
        } else {
            document.documentElement.classList.remove('dark');
            if (desktopDark) desktopDark.classList.remove('is-on');
        }
        if (darkIcon) darkIcon.textContent = dark ? 'light_mode' : 'dark_mode';
        if (darkLabel) darkLabel.textContent = dark ? 'Modo claro' : 'Modo oscuro';
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
<script src="<?= base_url('js/sidebar-collapse.js?v=' . filemtime(FCPATH . 'js/sidebar-collapse.js')) ?>"></script>