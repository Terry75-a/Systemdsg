<div class="pdsg-page-head">
    <div>
        <h1 class="pdsg-page-title">Tabla de Clientes</h1>
        <p class="pdsg-page-sub">Visualiza el padrón comercial y el estado de cada cliente.</p>
    </div>
    <button type="button" id="btnAnadir" class="btn pdsg-add-btn">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Añadir
    </button>
</div>

<div class="pdsg-card pdsg-table-card">
          
        
    <div class="pdsg-tabs" id="pestanasEstado">
        <button type="button" class="pdsg-tab is-active" data-estado="1">
            <span class="pdsg-tab-label">Activos</span>
            <span class="pdsg-tab-count" id="countActivos">-</span>
        </button>
        <button type="button" class="pdsg-tab" data-estado="0">
            <span class="pdsg-tab-label">Inactivos</span>
            <span class="pdsg-tab-count" id="countInactivos">-</span>
        </button>

        <div class="pdsg-search-wrap" style="min-width: 260px; max-width: 360px; width: 100%;">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" id="buscarCliente" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, DNI, teléfono..." autocomplete="off">
            </div>
        </div>
    </div>

    <div class="pdsg-table-wrap">
        <table id="tablaClientes" class="pdsg-table" style="width:100%">
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
        <div class="pdsg-empty" id="pdsgEmpty" hidden>
            <span class="material-symbols-outlined">group_off</span>
            <p>No se encontraron clientes en este estado.</p>
        </div>
    </div>

    <div class="pdsg-pagination" id="pdsgPagination" hidden>
        <span class="pdsg-summary" id="pdsgSummary"></span>
        <div class="pdsg-pages" id="pdsgPages"></div>
    </div>

</div>

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
    urlPlanes: '<?= base_url('personas/planesDisponibles') ?>',
    urlTipoPlan: '<?= base_url('personas/tipoplan') ?>',
    urlAsignarPlanes: '<?= base_url('personas/Asignarplanes') ?>',
    urlMantenerPlan: '<?= base_url('personas/mantenerPlanSeleccionado') ?>',
    urlPersonasAdd: '<?= base_url('personasadd') ?>'
};
</script>

<!-- Script externo de la lista -->
<script src="<?= base_url('js/personas-lista.js?v=' . filemtime(FCPATH . 'js/personas-lista.js')) ?>"></script>