<?php $uri = service('uri'); 

      $is_dashboard = in_array($uri->getSegment(1), ['dashboard', 'clientes', 'personasadd', 'calendario', 'perfil', 'personas',  'tipo_plan', 'planesadd' , 'notificaciones','planes','usuarios', 'permisos', 'menu', 'configuracion', 'ReporteVentas', 'ReporteStock', 'ReporteUsuarios']);

?>

<?php if ($is_dashboard): ?>
    </div>
    </div>
    </div>

    <footer class="footer bg-white text-center py-3 border-top" style="margin-top: auto;">
        <p class="mb-0 text-muted">&copy; <?= date('Y') ?> DSG PERU TECHNOLOGY. Todos los derechos reservados. · <a href="<?= base_url('asisten-dsg') ?>" class="text-muted">Asistencia del personal</a></p>
    </footer>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/dist/simplebar.js') ?>"></script>
    <script src="js/menu.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/init.js"></script>
    <script src="js/home_dark.js"></script>
    <script src="js/dsg_carrusel/carrusel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= base_url('js/main.js') ?>"></script>
<?php else: ?>
    </div><!-- /#main-wrapper (.page-wrapper de header) -->
    <footer class="main-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <img src="<?= base_url('images/logo_3.1.png') ?>"  alt="DSG Logo" class="footer-logo">
                <span class="brand-name">DSG Peru</span>
                <p class="brand-slogan">Software inteligente para negocios que crecen.</p>
            </div>

            <nav class="footer-nav">
                <a href="<?= base_url('servicios') ?>">Servicios</a>
                <a href="<?= base_url('precio') ?>">Precios</a>
                <a href="<?= base_url('/#contacto') ?>">Contacto</a>
            </nav>

            <div class="footer-social">
                <a href="https://www.facebook.com/dsgperu" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/dsgperu/" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> DSG Peru Technology. Todos los derechos reservados. <a href="<?= base_url('asisten-dsg') ?>" class="footer-stealth">Asistencia del personal</a></p>
        </div>
    </div>
    

<script src="<?= base_url('js/contador.js') ?>"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


<script>
  var swiper = new Swiper(".testimonialSwiper", {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: true,
    autoplay: { delay: 5000 },
    pagination: { el: ".swiper-pagination", clickable: true },
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    breakpoints: {
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 }
    }
  });
</script>

<script>
  (function () {
    var shell = document.querySelector('.nav-shell');
    if (!shell) return;
    var toggle = document.getElementById('mobile-menu-toggle');
    var panel = document.getElementById('navMobile');
    var progress = document.getElementById('navProgress');

    function onScroll() {
      var y = window.scrollY || window.pageYOffset || 0;
      shell.classList.toggle('is-scrolled', y > 8);
      if (progress) {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        var p = h > 0 ? Math.min(1, Math.max(0, y / h)) : 0;
        progress.style.transform = 'scaleX(' + p + ')';
      }
    }

    function setOpen(open) {
      if (!toggle || !panel) return;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
      if (open) {
        panel.hidden = false;
        requestAnimationFrame(function () { panel.classList.add('open'); });
        document.body.style.overflow = 'hidden';
      } else {
        panel.classList.remove('open');
        document.body.style.overflow = '';
        window.setTimeout(function () {
          if (!panel.classList.contains('open')) panel.hidden = true;
        }, 180);
      }
    }

    function isOpen() {
      return panel && !panel.hidden && panel.classList.contains('open');
    }

    if (toggle && panel) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(!isOpen());
      });
      panel.addEventListener('click', function (e) {
        if (e.target.closest('a')) setOpen(false);
      });
      document.addEventListener('click', function (e) {
        if (isOpen() && !shell.contains(e.target)) setOpen(false);
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) {
          setOpen(false);
          toggle.focus();
        }
      });
      window.addEventListener('resize', function () {
        if (window.innerWidth > 940 && isOpen()) setOpen(false);
      });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  })();
</script>

</footer>
<?php endif; ?>
<!-- Contenedor único donde se van apilando todos los Toasts hacia abajo -->
<div id="toastContainer" style="position:fixed; right:16px; bottom:16px; z-index: 999999;"></div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL GLOBAL DE CONFIRMACIÓN (eliminar / acciones irreversibles)
     Uso JS:  confirmDsg('¿Eliminar este registro?').then(ok => { ... })
     ────────────────────────────────────────────────────────────── -->
<style>
.dsg-cfm-backdrop {
    position: fixed; inset: 0;
    background: rgba(32, 33, 36, .55);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 10800;
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
    opacity: 0; pointer-events: none;
    transition: opacity .2s cubic-bezier(.4, 0, .2, 1);
}
.dsg-cfm-backdrop.is-open { opacity: 1; pointer-events: auto; }
html.dark .dsg-cfm-backdrop { background: rgba(0, 0, 0, .65); }
.dsg-cfm-card {
    width: 100%; max-width: 400px;
    background: var(--sb-surface, #fff);
    color: var(--sb-on-surface, #161618);
    border: 1px solid var(--sb-outline-variant, #e4e4e7);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(32, 33, 36, .22);
    padding: 24px;
    display: flex; flex-direction: column; align-items: center; gap: 12px;
    text-align: center;
    transform: translateY(12px) scale(.97);
    transition: transform .2s cubic-bezier(.4, 0, .2, 1);
}
.dsg-cfm-backdrop.is-open .dsg-cfm-card { transform: none; }
html.dark .dsg-cfm-card {
    background: #1f1f1f; color: #e8eaed;
    border-color: #2c2c2c;
    box-shadow: 0 8px 32px rgba(0, 0, 0, .55);
}
.dsg-cfm-icon {
    display: grid; place-items: center;
    width: 48px; height: 48px; border-radius: 16px;
    background: color-mix(in srgb, #d93025 10%, transparent);
    color: #d93025;
    font-size: 20px;
}
html.dark .dsg-cfm-icon { color: #f28b82; background: color-mix(in srgb, #f28b82 14%, transparent); }
.dsg-cfm-title { margin: 0; font-size: 16px; font-weight: 600; letter-spacing: -.01em; }
.dsg-cfm-msg { margin: 0; font-size: 13.5px; line-height: 1.55; color: var(--sb-on-surface-variant, #52525b); }
html.dark .dsg-cfm-msg { color: #9aa0a6; }
.dsg-cfm-actions { display: flex; flex-direction: column; gap: 8px; width: 100%; margin-top: 8px; }
.dsg-cfm-btn {
    min-height: 44px; width: 100%;
    border: 0; border-radius: 999px;
    font-size: 14px; font-weight: 500; letter-spacing: .01em;
    cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    transition: background-color .15s cubic-bezier(.4, 0, .2, 1), color .15s cubic-bezier(.4, 0, .2, 1);
}
.dsg-cfm-ok { background: #d93025; color: #fff; }
.dsg-cfm-ok:hover { background: #c5221f; }
html.dark .dsg-cfm-ok { background: transparent; color: #f28b82; box-shadow: inset 0 0 0 1px #5f6368; }
html.dark .dsg-cfm-ok:hover { background: color-mix(in srgb, #f28b82 14%, transparent); }
.dsg-cfm-cancel { background: transparent; color: var(--sb-on-surface-variant, #52525b); }
.dsg-cfm-cancel:hover { background: var(--sb-surface-container-high, #ececee); color: var(--sb-on-surface, #161618); }
html.dark .dsg-cfm-cancel { color: #9aa0a6; }
html.dark .dsg-cfm-cancel:hover { background: #2c2c2c; color: #e8eaed; }
@media (min-width: 480px) {
    .dsg-cfm-actions { flex-direction: row-reverse; }
}
</style>
<div class="dsg-cfm-backdrop" id="dsgConfirmBackdrop" aria-hidden="true">
    <div class="dsg-cfm-card" role="alertdialog" aria-modal="true" aria-labelledby="dsgCfmTitle" aria-describedby="dsgCfmMsg">
        <span class="dsg-cfm-icon"><span class="material-symbols-outlined">delete</span></span>
        <h2 class="dsg-cfm-title" id="dsgCfmTitle">¿Eliminar?</h2>
        <p class="dsg-cfm-msg" id="dsgCfmMsg">Esta acción no se puede deshacer.</p>
        <div class="dsg-cfm-actions">
            <button type="button" class="dsg-cfm-btn dsg-cfm-ok" id="dsgCfmOk"><span class="material-symbols-outlined" style="font-size:18px;">check</span> <span id="dsgCfmOkLabel">Eliminar</span></button>
            <button type="button" class="dsg-cfm-btn dsg-cfm-cancel" id="dsgCfmCancel">Cancelar</button>
        </div>
    </div>
</div>
<script>
/* confirmDsg(message|options) → Promise<boolean>
   options: { title, message, okLabel, icon } */
window.confirmDsg = function (opts) {
    return new Promise(function (resolve) {
        var backdrop = document.getElementById('dsgConfirmBackdrop');
        var titleEl = document.getElementById('dsgCfmTitle');
        var msgEl = document.getElementById('dsgCfmMsg');
        var okBtn = document.getElementById('dsgCfmOk');
        var cancelBtn = document.getElementById('dsgCfmCancel');
        var okLabel = document.getElementById('dsgCfmOkLabel');
        var iconEl = backdrop.querySelector('.dsg-cfm-icon .material-symbols-outlined');

        if (typeof opts === 'string') opts = { message: opts };
        opts = opts || {};

        titleEl.textContent = opts.title || '¿Eliminar?';
        msgEl.textContent = opts.message || 'Esta acción no se puede deshacer.';
        okLabel.textContent = opts.okLabel || 'Eliminar';
        if (iconEl) iconEl.textContent = opts.icon || 'delete';

        var done = false;
        function finish(val) {
            if (done) return;
            done = true;
            backdrop.classList.remove('is-open');
            backdrop.setAttribute('aria-hidden', 'true');
            document.removeEventListener('keydown', onKey);
            setTimeout(function () { resolve(val); }, 180);
        }
        function onKey(e) { if (e.key === 'Escape') finish(false); }

        okBtn.onclick = function () { finish(true); };
        cancelBtn.onclick = function () { finish(false); };
        backdrop.onclick = function (e) { if (e.target === backdrop) finish(false); };
        document.addEventListener('keydown', onKey);

        backdrop.classList.add('is-open');
        backdrop.setAttribute('aria-hidden', 'false');
        cancelBtn.focus();
    });
};
/* Delegación global: cualquier <form data-confirm-delete="mensaje"> espera
   a confirmDsg antes de enviarse. Reemplaza a onsubmit="return confirm(…)" */
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    var msg = form.getAttribute('data-confirm-delete');
    if (msg === null) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    var opts = {};
    try { opts = JSON.parse(msg) || {}; } catch (err) { opts = { message: msg }; }
    if (typeof opts === 'string') opts = { message: opts };
    confirmDsg(opts).then(function (ok) {
        if (!ok) return;
        form.removeAttribute('data-confirm-delete');
        HTMLFormElement.prototype.submit.call(form);
    });
}, true);
</script>

<script>
/* ══════════════════════════════════════════════════════════════
   TOASTS — estilo Asisten DSG
   Tipo: success · warning · danger · info
   ══════════════════════════════════════════════════════════════ */
function showToast(titulo, mensaje, tipo) {
    // Compatibilidad: si llaman con la firma vieja (mensaje, tipo)
    if (mensaje === undefined || (typeof mensaje === 'string' && ['success','warning','danger','info'].indexOf(mensaje) !== -1 && tipo === undefined)) {
        tipo = mensaje || 'success';
        mensaje = titulo;
        titulo = '';
    }
    tipo = tipo || 'success';

    var container = document.getElementById('toastContainer');
    if (!container) return;

    var icons = {
        success: 'check_circle',
        warning: 'priority_high',
        danger:  'error',
        info:    'info'
    };
    var titles = {
        success: 'Listo',
        warning: 'Atención',
        danger:  'Alerta',
        info:    'Información'
    };

    var el = document.createElement('div');
    el.className = 'pdsg-toast pdsg-toast--' + tipo;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');

    var head = '<p class="pdsg-toast-title">' + (titulo || titles[tipo] || 'Aviso') + '</p>';
    var body = '<div class="pdsg-toast-body">' + head +
               (mensaje ? '<p class="pdsg-toast-msg">' + mensaje + '</p>' : '') + '</div>';

    el.innerHTML =
        '<span class="pdsg-toast-icon"><span class="material-symbols-outlined">' + (icons[tipo] || 'info') + '</span></span>' +
        body +
        '<button type="button" class="pdsg-toast-close" aria-label="Cerrar">' +
            '<span class="material-symbols-outlined">close</span>' +
        '</button>' +
        '<span class="pdsg-toast-progress"></span>';

    container.appendChild(el);

    // entrada suave
    requestAnimationFrame(function () {
        requestAnimationFrame(function () { el.classList.add('is-in'); });
    });

    var dur = 6000;
    var bar = el.querySelector('.pdsg-toast-progress');
    if (bar) bar.style.animationDuration = dur + 'ms';

    function cerrar() {
        if (el.dataset.cerrado) return;
        el.dataset.cerrado = '1';
        el.classList.remove('is-in');
        el.classList.add('is-out');
        setTimeout(function () { el.remove(); }, 320);
    }

    var timer = setTimeout(cerrar, dur);
    el.querySelector('.pdsg-toast-close').addEventListener('click', function () {
        clearTimeout(timer);
        cerrar();
    });
}
</script>
</body>
</html>
