/* global Chart, coreui */

/**
 * --------------------------------------------------------------------------
 * CoreUI Boostrap Admin Template main.js
 * Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
 * --------------------------------------------------------------------------
 */

// Comprobamos si Chart y coreui existen antes de configurar nada
if (typeof Chart !== 'undefined' && typeof coreui !== 'undefined') {
  // Disable the on-canvas tooltip
  Chart.defaults.pointHitDetectionRadius = 1;
  Chart.defaults.plugins.tooltip.enabled = false;
  Chart.defaults.plugins.tooltip.mode = 'index';
  Chart.defaults.plugins.tooltip.position = 'nearest';
  
  // Estas líneas fallan en CoreUI 5+ porque la estructura cambió
  // Las comentamos o adaptamos si es necesario
  if (coreui.ChartJS && coreui.ChartJS.customTooltips) {
    Chart.defaults.plugins.tooltip.external = coreui.ChartJS.customTooltips;
  }
  
  if (coreui.Utils && coreui.Utils.getStyle) {
    Chart.defaults.defaultFontColor = coreui.Utils.getStyle('--cui-body-color');
  }

  document.documentElement.addEventListener('ColorSchemeChange', () => {
    if (typeof cardChart1 !== 'undefined') cardChart1.update();
    if (typeof cardChart2 !== 'undefined') cardChart2.update();
    if (typeof mainChart !== 'undefined') mainChart.update();
  });

  const random = (min, max) => Math.floor(Math.random() * (max - min + 1) + min);

  // Solo inicializamos los gráficos si los elementos existen en el DOM
  const chart1Elem = document.getElementById('card-chart1');
  if (chart1Elem) {
    const cardChart1 = new Chart(chart1Elem, {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
          label: 'My First dataset',
          backgroundColor: 'transparent',
          borderColor: 'rgba(255,255,255,.55)',
          pointBackgroundColor: (coreui.Utils ? coreui.Utils.getStyle('--cui-primary') : '#5856d6'),
          data: [65, 59, 84, 84, 51, 55, 40]
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        maintainAspectRatio: false,
        scales: {
          x: { border: { display: false }, grid: { display: false, drawBorder: false }, ticks: { display: false } },
          y: { min: 30, max: 89, display: false, grid: { display: false }, ticks: { display: false } }
        },
        elements: { line: { borderWidth: 1, tension: 0.4 }, point: { radius: 4, hitRadius: 10, hoverRadius: 4 } }
      }
    });
  }

  const chart2Elem = document.getElementById('card-chart2');
  if (chart2Elem) {
    const cardChart2 = new Chart(chart2Elem, {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
          label: 'My First dataset',
          backgroundColor: 'transparent',
          borderColor: 'rgba(255,255,255,.55)',
          pointBackgroundColor: (coreui.Utils ? coreui.Utils.getStyle('--cui-info') : '#39f'),
          data: [1, 18, 9, 17, 34, 22, 11]
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        maintainAspectRatio: false,
        scales: {
          x: { border: { display: false }, grid: { display: false, drawBorder: false }, ticks: { display: false } },
          y: { min: -9, max: 39, display: false, grid: { display: false }, ticks: { display: false } }
        },
        elements: { line: { borderWidth: 1 }, point: { radius: 4, hitRadius: 10, hoverRadius: 4 } }
      }
    });
  }

  const mainChartElem = document.getElementById('main-chart');
  if (mainChartElem) {
    const mainChart = new Chart(mainChartElem, {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
          label: 'My First dataset',
          backgroundColor: (coreui.Utils ? `rgba(${coreui.Utils.getStyle('--cui-info-rgb')}, .1)` : 'rgba(57, 153, 255, .1)'),
          borderColor: (coreui.Utils ? coreui.Utils.getStyle('--cui-info') : '#39f'),
          pointHoverBackgroundColor: '#fff',
          borderWidth: 2,
          data: [random(50, 200), random(50, 200), random(50, 200), random(50, 200), random(50, 200), random(50, 200), random(50, 200)],
          fill: true
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true }
        }
      }
    });
  }
}
