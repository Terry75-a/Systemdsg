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
<div id="toastContainer" style="position:fixed; right:20px; bottom:20px; z-index: 999999;"></div>

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
