<!-- ═══ Clientes · réplica de la página «clients» de Trackin (solo diseño) ═══ -->
<div class="tk2-page">

    <div class="tk2-title">
        <div>
            <h2>Tus clientes</h2>
            <p>Padrón comercial de DSG: datos de cada cliente, su empresa, sucursales, planes y pagos.</p>
        </div>
        <div class="tk2-title-actions">
            <button type="button" id="btnAnadir" class="tk2-btn tk2-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">person_add</span> Añadir cliente
            </button>
        </div>
    </div>

    <!-- Métricas (se llenan con los mismos contadores de las pestañas) -->
    <div class="tk2-stats" aria-label="Resumen de clientes">
        <div class="tk2-stat">
            <span class="material-symbols-outlined">groups</span>
            <p class="tk2-stat-label">Clientes registrados</p>
            <p class="tk2-stat-value" id="tk2StatTotal">–</p>
        </div>
        <div class="tk2-stat">
            <span class="material-symbols-outlined">verified</span>
            <p class="tk2-stat-label">Activos</p>
            <p class="tk2-stat-value" id="tk2StatOn">–</p>
        </div>
        <div class="tk2-stat">
            <span class="material-symbols-outlined">person_off</span>
            <p class="tk2-stat-label">Inactivos</p>
            <p class="tk2-stat-value" id="tk2StatOff">–</p>
        </div>
    </div>

    <!-- Filtros: chips + buscador -->
    <div class="tk2-toolbar">
        <div class="tk2-chips pdsg-tabs" id="pestanasEstado" role="tablist">
            <button type="button" class="pdsg-tab tk2-chip is-active" data-estado="1" role="tab">
                <span class="pdsg-tab-label">Activos</span>
                <span class="pdsg-tab-count" id="countActivos">-</span>
            </button>
            <button type="button" class="pdsg-tab tk2-chip" data-estado="0" role="tab">
                <span class="pdsg-tab-label">Inactivos</span>
                <span class="pdsg-tab-count" id="countInactivos">-</span>
            </button>
        </div>
        <label class="tk2-search">
            <span class="material-symbols-outlined" aria-hidden="true">search</span>
            <input type="text" id="buscarCliente" placeholder="Buscar por nombre, DNI o teléfono…" autocomplete="off">
            <button type="button" class="tk2-search-clear" id="tk2BuscarLimpiar" aria-label="Borrar búsqueda" hidden>
                <span class="material-symbols-outlined">close</span>
            </button>
        </label>
    </div>

    <!-- Tabla (escritorio) y tarjetas (celular): las llena personas-lista.js -->
    <div class="tk2-card pdsg-table-card">
        <div class="pdsg-table-wrap">
            <table id="tablaClientes" class="pdsg-table tk2-table" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">Cliente</th>
                        <th scope="col">DNI</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Dirección</th>
                        <th scope="col" class="pdsg-th-center">Estado</th>
                        <th scope="col" class="pdsg-th-end">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="pdsg-mlist" id="pdsgMlist"></div>
            <div class="pdsg-empty tk2-empty" id="pdsgEmpty" hidden>
                <span class="material-symbols-outlined">group_off</span>
                <p>No se encontraron clientes en este estado.</p>
            </div>
        </div>

        <div class="pdsg-pagination tk2-pagination" id="pdsgPagination" hidden>
            <span class="pdsg-summary" id="pdsgSummary"></span>
            <div class="pdsg-pages" id="pdsgPages"></div>
        </div>
    </div>
</div>

<script>
/* Solo presentación: copia los contadores de las pestañas a las métricas y maneja la «x» del buscador */
(function () {
    var on = document.getElementById('countActivos'), off = document.getElementById('countInactivos');
    var n = function (el) { var v = parseInt(el && el.textContent, 10); return isNaN(v) ? null : v; };
    function sync() {
        var a = n(on), b = n(off);
        document.getElementById('tk2StatOn').textContent = a === null ? '–' : a;
        document.getElementById('tk2StatOff').textContent = b === null ? '–' : b;
        document.getElementById('tk2StatTotal').textContent = (a === null && b === null) ? '–' : (a || 0) + (b || 0);
    }
    [on, off].forEach(function (el) { if (el) new MutationObserver(sync).observe(el, { childList: true, characterData: true, subtree: true }); });
    sync();
    var q = document.getElementById('buscarCliente'), x = document.getElementById('tk2BuscarLimpiar');
    if (q && x) {
        var ver = function () { x.hidden = !q.value; };
        q.addEventListener('input', ver);
        x.addEventListener('click', function () { q.value = ''; q.dispatchEvent(new Event('input', { bubbles: true })); if (window.jQuery) jQuery(q).trigger('input'); ver(); q.focus(); });
    }
})();
</script>

<!-- Vistas parciales de modales -->
<?= view('personas/partials/_modal_ver_persona') ?>
<?= view('personas/partials/_modal_ver_sucursal') ?>
<?= view('personas/partials/_modal_asignar_plan') ?>
<?= view('personas/partials/_modal_persona_frame') ?>

<!-- Configuración de URLs y Tokens para JS externo -->
<script>
window.PDSG_LISTA_CFG = {
    csrfHash: '<?= csrf_hash() ?>',
    urlListar: '<?= base_url('personas/listar') ?>',
    urlEliminar: '<?= base_url('personas/eliminar') ?>',
    urlBorrar: '<?= base_url('personas/borrar') ?>',
    urlDetalle: '<?= base_url('personas/obtenerDetalle') ?>',
    urlPagosSucursal: '<?= base_url('personas/pagosSucursal') ?>',
    urlPlanes: '<?= base_url('personas/planesDisponibles') ?>',
    urlTipoPlan: '<?= base_url('personas/tipoplan') ?>',
    urlAsignarPlanes: '<?= base_url('personas/Asignarplanes') ?>',
    urlMantenerPlan: '<?= base_url('personas/mantenerPlanSeleccionado') ?>',
    urlPersonasAdd: '<?= base_url('personasadd') ?>'
};
</script>

<!-- Script externo de la lista -->
<script src="<?= base_url('js/personas-lista.js?v=' . filemtime(FCPATH . 'js/personas-lista.js')) ?>"></script>