/* ══════════════════════════════════════════════════════════════
   COLAPSAR / EXPANDIR SIDEBAR
   ──────────────────────────────────────────────────────────────
   Botón en el borde derecho del menú lateral. El estado se guarda
   en localStorage (devSidebarCollapsed) y la clase .sb-collapsed
   se pinta antes de cargar este archivo con un script inline en
   cada sidebar, así no parpadea al cambiar de página.
   ══════════════════════════════════════════════════════════════ */
(function () {
    var KEY   = 'devSidebarCollapsed';
    var root  = document.documentElement;
    var btn   = document.getElementById('sidebarCollapseBtn');
    var links = Array.prototype.slice.call(document.querySelectorAll('.dev-nav-link'));
    var user  = document.querySelector('.dev-sidebar-user');

    /* Al colapsar, cada enlace guarda su texto como tooltip nativo */
    function setTitles(collapsed) {
        links.concat(user ? [user] : []).forEach(function (el) {
            if (collapsed) {
                if (!el.getAttribute('data-label')) {
                    var s = el.querySelector('span:not(.material-symbols-outlined):not(.dev-nav-badge)');
                    el.setAttribute('data-label', (s ? s.textContent : el.textContent).trim());
                }
                el.setAttribute('title', el.getAttribute('data-label'));
            } else {
                el.removeAttribute('title');
            }
        });
    }

    function apply(collapsed) {
        root.classList.toggle('sb-collapsed', collapsed);

        if (btn) {
            /* Botón estilo Trackin («Colapsar» con flecha): la flecha gira por CSS */
            var ico = btn.querySelector('.material-symbols-outlined');
            if (ico && btn.getAttribute('data-icons') !== 'fixed') ico.textContent = collapsed ? 'menu' : 'menu_open';
            btn.title = collapsed ? 'Expandir menú' : 'Colapsar menú';
            btn.setAttribute('aria-label', btn.title);
            btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }

        setTitles(collapsed);

        try { localStorage.setItem(KEY, collapsed ? '1' : '0'); } catch (e) {}

        /* Muchos gráficos y tablas calculan su ancho al redimensionar */
        window.dispatchEvent(new Event('resize'));
    }

    if (btn) {
        btn.addEventListener('click', function () {
            apply(!root.classList.contains('sb-collapsed'));
        });
    }

    /* Estado inicial: la clase ya la puso el script inline del sidebar */
    apply(root.classList.contains('sb-collapsed'));
})();
