/* ═══════════════════════════════════════
   DEV TABLE — Pagination Component
   ═══════════════════════════════════════ */

function DevTable(config) {
    this.data = config.data || [];
    this.columns = config.columns || [];
    this.rowsPerPage = config.rowsPerPage || 5;
    this.container = document.getElementById(config.containerId);
    this.currentPage = 1;
    this.totalPages = Math.ceil(this.data.length / this.rowsPerPage);

    this.render();
}

DevTable.prototype.getPageItems = function() {
    var start = (this.currentPage - 1) * this.rowsPerPage;
    return this.data.slice(start, start + this.rowsPerPage);
};

DevTable.prototype.render = function() {
    var items = this.getPageItems();
    var start = (this.currentPage - 1) * this.rowsPerPage + 1;
    var end = Math.min(this.currentPage * this.rowsPerPage, this.data.length);
    var self = this;

    var html = '';

    /* Scroll container */
    html += '<div class="dtable-scroll">';
    html += '<table class="dtable">';

    /* Header */
    html += '<thead><tr>';
    for (var i = 0; i < this.columns.length; i++) {
        html += '<th>' + this.columns[i].label + '</th>';
    }
    html += '</tr></thead>';

    /* Body */
    html += '<tbody>';
    if (items.length === 0) {
        html += '<tr><td colspan="' + this.columns.length + '">';
        html += '<div class="dtable-empty">';
        html += '<span class="material-symbols-outlined">inbox</span>';
        html += '<span>No hay datos</span>';
        html += '</div></td></tr>';
    } else {
        for (var j = 0; j < items.length; j++) {
            html += '<tr>';
            for (var k = 0; k < this.columns.length; k++) {
                var col = this.columns[k];
                var value = '';
                if (col.render) {
                    value = col.render(items[j], j);
                } else {
                    value = items[j][col.key] || '';
                }
                html += '<td>' + value + '</td>';
            }
            html += '</tr>';
        }
    }
    html += '</tbody>';
    html += '</table>';
    html += '</div>';

    /* Footer */
    html += '<div class="dtable-footer">';
    html += '<div class="dtable-summary">';
    html += start + ' a ' + end + ' de ' + this.data.length + ' resultados';
    html += '</div>';
    html += '<div class="dtable-pagination">';

    /* Previous */
    html += '<button class="dtable-page-nav" data-action="prev"' + (this.currentPage === 1 ? ' disabled' : '') + '>';
    html += '<span class="material-symbols-outlined">chevron_left</span> Anterior';
    html += '</button>';

    /* Page numbers */
    for (var p = 1; p <= this.totalPages; p++) {
        html += '<button class="dtable-page' + (p === this.currentPage ? ' is-active' : '') + '" data-page="' + p + '">' + p + '</button>';
    }

    /* Next */
    html += '<button class="dtable-page-nav" data-action="next"' + (this.currentPage === this.totalPages ? ' disabled' : '') + '>';
    html += 'Siguiente <span class="material-symbols-outlined">chevron_right</span>';
    html += '</button>';

    html += '</div>';
    html += '</div>';

    this.container.innerHTML = html;

    /* Bind events */
    this.bindEvents();
};

DevTable.prototype.bindEvents = function() {
    var self = this;
    var pages = this.container.querySelectorAll('.dtable-page');
    var prev = this.container.querySelector('[data-action="prev"]');
    var next = this.container.querySelector('[data-action="next"]');

    for (var i = 0; i < pages.length; i++) {
        pages[i].addEventListener('click', function() {
            self.goToPage(parseInt(this.getAttribute('data-page')));
        });
    }

    if (prev) {
        prev.addEventListener('click', function() {
            if (self.currentPage > 1) self.goToPage(self.currentPage - 1);
        });
    }

    if (next) {
        next.addEventListener('click', function() {
            if (self.currentPage < self.totalPages) self.goToPage(self.currentPage + 1);
        });
    }
};

DevTable.prototype.goToPage = function(page) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.render();
};
