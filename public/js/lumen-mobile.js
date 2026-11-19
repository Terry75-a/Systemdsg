/* ══════════════════════════════════════════════════════════════
   LUMEN · tablas en celular
   Pone a cada celda la etiqueta de su columna (data-label) y marca la
   tabla con .lm-ct. En pantallas ≤ 768 px, lumen.css convierte cada
   fila en una tarjeta: "Fecha 18/11 · Entrada 08:52 · Estado Puntual".
   Funciona con encabezados de varias filas (colspan) y con filas que se
   agregan después (tiempo real, paginación de DevTable, filtros).
   ══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var SALTAR = '.tk-pivot, .lm-keep';

  /** Etiqueta de cada columna, uniendo los encabezados apilados. */
  function etiquetas(table) {
    var thead = table.tHead;
    if (!thead) return null;
    var grid = [];
    Array.prototype.forEach.call(thead.rows, function (tr, r) {
      grid[r] = grid[r] || [];
      var c = 0;
      Array.prototype.forEach.call(tr.cells, function (cell) {
        while (grid[r][c] !== undefined) c++;
        var txt = (cell.textContent || '').replace(/\s+/g, ' ').trim();
        var cs = cell.colSpan || 1, rs = cell.rowSpan || 1;
        for (var i = 0; i < rs; i++) {
          grid[r + i] = grid[r + i] || [];
          for (var j = 0; j < cs; j++) grid[r + i][c + j] = txt;
        }
        c += cs;
      });
    });
    var cols = Math.max.apply(null, grid.map(function (row) { return row.length; }));
    var out = [];
    for (var k = 0; k < cols; k++) {
      var partes = [];
      grid.forEach(function (row) {
        var t = row[k];
        if (t && partes.indexOf(t) === -1) partes.push(t);
      });
      out.push(partes.join(' · '));
    }
    return out;
  }

  function etiquetarFila(tr, labels) {
    if (tr.parentNode && tr.parentNode.tagName === 'THEAD') return;
    var c = 0;
    Array.prototype.forEach.call(tr.cells, function (td) {
      if (td.tagName !== 'TD') { c += td.colSpan || 1; return; }
      var span = td.colSpan || 1;
      if (span > 1 && span >= labels.length - 1) {
        td.classList.add('lm-ct-full');     // fila vacía / mensaje
      } else if (!td.hasAttribute('data-label')) {
        td.setAttribute('data-label', labels[c] || '');
      }
      // Celdas sólo con botones = barra de acciones de la tarjeta
      if (td.querySelector('button, a.dev-btn-icon, .dev-btn-icon, .tk-actions, .hu-actions, .personal-actions')
          && !(td.textContent || '').replace(/\s+/g, '').length) {
        td.classList.add('lm-ct-actions');
      } else if (td.querySelector('.tk-actions, .hu-actions, .personal-actions, .dev-table-actions')) {
        td.classList.add('lm-ct-actions');
      }
      c += span;
    });
  }

  function preparar(table) {
    if (table.matches(SALTAR) || table.dataset.lmCt === '1') return;
    var labels = etiquetas(table);
    if (!labels || !labels.length) return;
    table.dataset.lmCt = '1';
    table.classList.add('lm-ct');
    if (table.parentElement) table.parentElement.classList.add('lm-ct-wrap');
    Array.prototype.forEach.call(table.tBodies, function (tb) {
      Array.prototype.forEach.call(tb.rows, function (tr) { etiquetarFila(tr, labels); });
      // Filas nuevas (tiempo real, paginación, filtros)
      new MutationObserver(function (muts) {
        muts.forEach(function (m) {
          Array.prototype.forEach.call(m.addedNodes, function (n) {
            if (n.nodeType === 1 && n.tagName === 'TR') etiquetarFila(n, labels);
          });
        });
      }).observe(tb, { childList: true });
    });
  }

  function escanear(root) {
    (root || document).querySelectorAll('.dev-main table').forEach(preparar);
  }

  function iniciar() {
    escanear();
    // Tablas creadas después (DevTable re-renderiza su contenedor completo)
    var main = document.querySelector('.dev-main');
    if (main) {
      new MutationObserver(function (muts) {
        // Sólo si llegó una tabla nueva (el reloj y otros textos no cuentan)
        for (var i = 0; i < muts.length; i++) {
          var nuevos = muts[i].addedNodes;
          for (var j = 0; j < nuevos.length; j++) {
            var n = nuevos[j];
            if (n.nodeType === 1 && (n.tagName === 'TABLE' || (n.querySelector && n.querySelector('table')))) {
              escanear(main); return;
            }
          }
        }
      }).observe(main, { childList: true, subtree: true });
    }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
  else iniciar();
})();

/* ══════════════════════════════════════════════════════════════
   LUMEN · avisos como notificaciones laterales (toasts)
   Los avisos de la página (.dev-alert / .alert, mensajes flash) pasan
   a una pila fija arriba a la derecha (en celular, arriba a lo ancho),
   con botón para cerrar y cierre automático. window.lmToast(tipo, msg)
   permite lanzar uno desde cualquier script.
   ══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  function pila() {
    var p = document.getElementById('lm-toasts');
    if (!p) {
      p = document.createElement('div');
      p.id = 'lm-toasts';
      p.setAttribute('aria-live', 'polite');
      document.body.appendChild(p);
    }
    return p;
  }

  function tipoDe(el) {
    var c = el.className || '';
    if (/(danger|error)/.test(c)) return 'error';
    if (/warning/.test(c)) return 'warning';
    return 'success';
  }

  function cerrar(t) {
    if (!t || t.dataset.cerrando) return;
    t.dataset.cerrando = '1';
    t.classList.remove('is-in');
    setTimeout(function () { t.remove(); }, 160);
  }

  function mostrar(el) {
    var tipo = tipoDe(el);
    el.classList.add('lm-toast');
    el.setAttribute('role', tipo === 'success' ? 'status' : 'alert');
    el.style.cssText = '';
    if (!el.querySelector('.lm-toast-x')) {
      var x = document.createElement('button');
      x.type = 'button';
      x.className = 'lm-toast-x';
      x.setAttribute('aria-label', 'Cerrar aviso');
      x.innerHTML = '<span class="material-symbols-outlined">close</span>';
      x.addEventListener('click', function () { cerrar(el); });
      el.appendChild(x);
    }
    pila().appendChild(el);
    requestAnimationFrame(function () { el.classList.add('is-in'); });
    // Éxito se va antes; errores y avisos quedan más tiempo. Se pausa con el mouse.
    var ms = tipo === 'success' ? 4500 : 8000, timer;
    function programar() { timer = setTimeout(function () { cerrar(el); }, ms); }
    el.addEventListener('mouseenter', function () { clearTimeout(timer); });
    el.addEventListener('mouseleave', programar);
    programar();
  }

  window.lmToast = function (tipo, msg) {
    var ico = { success: 'check_circle', warning: 'warning', error: 'error' }[tipo] || 'info';
    var d = document.createElement('div');
    d.className = 'dev-alert dev-alert-' + (tipo === 'error' ? 'danger' : tipo);
    d.innerHTML = '<span class="material-symbols-outlined filled">' + ico + '</span><span></span>';
    d.lastChild.textContent = msg;
    mostrar(d);
  };

  function iniciar() {
    var main = document.querySelector('.dev-main');
    if (!main) return;
    // Sólo los avisos de la página, no los que viven dentro de ventanas o formularios
    main.querySelectorAll('.dev-alert, .alert').forEach(function (el) {
      if (el.closest('.dev-modal, .dev-modal-overlay, form, #lm-toasts')) return;
      if (!(el.textContent || '').trim()) return;
      mostrar(el);
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
  else iniciar();
})();

/* ══════════════════════════════════════════════════════════════
   LUMEN · nombres para botones de solo ícono
   Un botón que sólo muestra un ícono (cerrar, editar, eliminar…) no
   tiene texto para el lector de pantalla. Se le pone aria-label según
   su ícono, sin tocar los que ya tienen aria-label o title.
   ══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';
  var NOMBRES = {
    close: 'Cerrar', edit: 'Editar', delete: 'Eliminar', delete_outline: 'Eliminar',
    visibility: 'Ver detalle', more_vert: 'Más opciones', more_horiz: 'Más opciones',
    content_copy: 'Copiar', download: 'Descargar', refresh: 'Actualizar', search: 'Buscar',
    group: 'Integrantes', person_add: 'Agregar persona', fingerprint: 'Biometría',
    key: 'Nueva contraseña', lock_reset: 'Nueva contraseña', check: 'Aprobar', block: 'Rechazar',
    menu: 'Abrir menú', arrow_back: 'Volver', chevron_left: 'Anterior', chevron_right: 'Siguiente',
    notifications: 'Notificaciones', logout: 'Cerrar sesión', dark_mode: 'Modo oscuro', light_mode: 'Modo claro'
  };
  function nombrar(root) {
    (root || document).querySelectorAll('button, a.dev-btn-icon, [role="button"]').forEach(function (b) {
      if (b.hasAttribute('aria-label') || b.hasAttribute('title')) return;
      var ico = b.querySelector('.material-symbols-outlined, .material-icons, .material-symbols-rounded');
      if (!ico) return;
      var texto = (b.textContent || '').replace(ico.textContent || '', '').replace(/\s+/g, '');
      if (texto.length) return; // ya tiene texto visible
      var n = NOMBRES[(ico.textContent || '').trim()];
      if (n) { b.setAttribute('aria-label', n); ico.setAttribute('aria-hidden', 'true'); }
    });
  }
  function iniciar() {
    nombrar();
    var main = document.querySelector('.dev-main') || document.body;
    new MutationObserver(function (muts) {
      for (var i = 0; i < muts.length; i++) for (var j = 0; j < muts[i].addedNodes.length; j++) if (muts[i].addedNodes[j].nodeType === 1) { nombrar(main); return; }
    }).observe(main, { childList: true, subtree: true });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
  else iniciar();
})();

/* Escape cierra la ventana abierta en cualquier pantalla (si la vista no lo hace ya) */
document.addEventListener('keydown', function (e) {
  if (e.key !== 'Escape' || e.defaultPrevented) return;
  var abiertas = Array.prototype.filter.call(document.querySelectorAll('.dev-modal-overlay'), function (o) {
    if (/(^|\s)(active|show|open|is-open)(\s|$)/.test(o.className)) return true;
    var c = getComputedStyle(o);
    return c.display !== 'none' && c.visibility !== 'hidden' && c.opacity !== '0';
  });
  var top = abiertas[abiertas.length - 1];
  if (!top) return;
  var x = top.querySelector('.dev-modal-close, .pd-close, [aria-label="Cerrar"], [data-mc], [data-mcnotif]');
  if (x) x.click();
});

/* Al abrir una ventana el foco entra en ella; al cerrarla vuelve al botón
   que la abrió (teclado y lector de pantalla no se quedan detrás). */
(function () {
  'use strict';
  var origen = new WeakMap();
  function abierta(o) { return /(^|\s)(active|show|open|is-open)(\s|$)/.test(o.className); }
  function vigilar(o) {
    if (o.dataset.lmFoco) return;
    o.dataset.lmFoco = '1';
    if (!o.hasAttribute('role')) { var m = o.querySelector('.dev-modal') || o; m.setAttribute('role', 'dialog'); m.setAttribute('aria-modal', 'true'); }
    var antes = abierta(o);
    new MutationObserver(function () {
      var ahora = abierta(o);
      if (ahora === antes) return;
      antes = ahora;
      if (ahora) {
        origen.set(o, document.activeElement);
        // Se reintenta mientras la ventana termina de hacerse visible (transición)
        var n = 0;
        (function intentar() {
          if (!abierta(o) || o.contains(document.activeElement)) return;
          var f = o.querySelector('input:not([type=hidden]):not([disabled]), select, textarea, button:not([disabled]), [href], [tabindex]:not([tabindex="-1"])');
          if (f) f.focus({ preventScroll: true });
          if (!o.contains(document.activeElement) && ++n < 10) setTimeout(intentar, 60);
        })();
      } else {
        var v = origen.get(o);
        if (v && v.focus && document.contains(v)) v.focus({ preventScroll: true });
      }
    }).observe(o, { attributes: true, attributeFilter: ['class'] });
  }
  function iniciar() {
    document.querySelectorAll('.dev-modal-overlay').forEach(vigilar);
    new MutationObserver(function () { document.querySelectorAll('.dev-modal-overlay:not([data-lm-foco])').forEach(vigilar); })
      .observe(document.body, { childList: true });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
  else iniciar();
})();

/* Entradas en limpio: las animaciones de entrada (tarjetas, filas, menú) terminan en
   filter: blur(0) / translateY(0) y, al quedar fijas (fill-mode both), convierten a la
   tarjeta en el marco de lo que tenga position: fixed adentro (el menú ⋮ de Personal
   salía desplazado o recortado). Al terminar se suelta la animación: el elemento ya
   está en su estado final, así que no cambia nada a la vista. */
(function () {
  'use strict';
  var ENTRADAS = /^(tk-item-in|np-fade-up|np-fade-down|np-nav-in|np-row-in|np-alert-in|edu-fade-up)$/;
  document.addEventListener('animationend', function (e) {
    if (e.target === e.currentTarget || !ENTRADAS.test(e.animationName)) return;
    e.target.style.animation = 'none';
  }, true);
  // Respaldo (pestañas en segundo plano no siempre emiten animationend):
  // al cargar y antes de cada toque se sueltan las entradas ya terminadas.
  function soltar() {
    if (!document.getAnimations) return;
    document.getAnimations().forEach(function (a) {
      var t = a.effect && a.effect.target;
      if (t && t.style && a.playState === 'finished' && ENTRADAS.test(a.animationName || '')) t.style.animation = 'none';
    });
  }
  window.addEventListener('load', function () { setTimeout(soltar, 1500); });
  document.addEventListener('pointerdown', soltar, true);
  document.addEventListener('keydown', soltar, true);
})();
