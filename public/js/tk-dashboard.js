/* Dashboard admin (réplica de System-Trackin): números que suben,
   barras que se llenan y pestañas del recorrido. */
(function () {
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Barras: se llenan un instante después de pintar
  requestAnimationFrame(function () {
    requestAnimationFrame(function () { document.body.classList.add('tk-ready'); });
  });

  // AnimatedNumber: sube desde 0 la primera vez que se ve
  var fmt = function (n) { return Math.round(n).toLocaleString('en-US'); };
  var ease = function (t) { return 1 - Math.pow(1 - t, 4); };
  var animar = function (el) {
    var fin = Number(el.getAttribute('data-count')) || 0;
    if (reduce || !fin) { el.textContent = fmt(fin); return; }
    var t0 = performance.now();
    var paso = function (t) {
      var p = Math.min(1, (t - t0) / 900);
      el.textContent = fmt(fin * ease(p));
      if (p < 1) requestAnimationFrame(paso);
    };
    requestAnimationFrame(paso);
  };
  var nums = document.querySelectorAll('.tk-dash [data-count]');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { io.unobserve(e.target); animar(e.target); }
      });
    });
    nums.forEach(function (el) { el.textContent = '0'; io.observe(el); });
  }

  // Pestañas del recorrido (Día normal / Con incidencia)
  document.querySelectorAll('.tk-route-tabs [data-route]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var key = btn.getAttribute('data-route');
      document.querySelectorAll('.tk-route-tabs [data-route]').forEach(function (b) {
        b.classList.toggle('active', b === btn);
      });
      document.querySelectorAll('[data-route-panel]').forEach(function (p) {
        var on = p.getAttribute('data-route-panel') === key;
        p.hidden = !on;
        if (on) {
          // re-dispara la animación de las estaciones
          p.querySelectorAll('.tk-station').forEach(function (li) {
            li.style.animation = 'none'; void li.offsetWidth; li.style.animation = '';
          });
        }
      });
    });
  });
})();
