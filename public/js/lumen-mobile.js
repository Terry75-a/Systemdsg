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
