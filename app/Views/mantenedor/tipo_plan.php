<link rel="stylesheet" href="<?= base_url('css/dashboard/tipo-plan.css?v=' . filemtime(FCPATH . 'css/dashboard/tipo-plan.css')) ?>">
<main class="tp-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Tipos de plan</h1>
            <p class="pdsg-page-sub">Organiza las modalidades disponibles para tus planes de servicio.</p>
        </div>
        <button type="button" id="tpAdd" class="btn pdsg-add-btn tp-add">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Añadir
        </button>
    </div>

    <div id="tpNotice" class="alert alert-success" role="status" hidden></div>

    <!-- KPIs (StatGrid): se llenan con los datos de la tabla -->
    <section class="tk-stats tp-stats" aria-label="Resumen de tipos de plan">
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">category</span><div><p class="tk-stat-lbl">Tipos registrados</p><p class="tk-stat-val" id="tpStatTotal">—</p></div></div>
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">filter_list</span><div><p class="tk-stat-lbl">Mostrando</p><p class="tk-stat-val" id="tpStatShown">—</p></div></div>
        <div class="tk-stat"><span class="material-symbols-outlined tk-stat-ico">new_releases</span><div><p class="tk-stat-lbl">Último agregado</p><p class="tk-stat-val tp-stat-txt" id="tpStatLast">—</p></div></div>
    </section>
    <section class="pdsg-card pdsg-table-card" aria-label="Listado de tipos de plan">
        <div class="tp-toolbar">
            <div class="tp-category"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><span>Todos los tipos</span><span class="pdsg-tab-count" id="tpCount" aria-live="polite">—</span></div>
            <label class="tp-search" for="tpSearch"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="tpSearch" placeholder="Buscar tipo de plan…" aria-label="Buscar tipo de plan"></label>
        </div>
        <div class="pdsg-table-wrap">
            <table id="tablaplanes" class="pdsg-table" style="width:100%">
                <thead><tr>
                    <th scope="col" class="tp-id-column">Código</th>
                    <th scope="col">Tipo de plan</th>
                    <th scope="col" class="pdsg-th-end">Acciones</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</main>


<div class="modal fade" id="tpModal" tabindex="-1" aria-labelledby="tpModalTitle" aria-describedby="tpModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="tpForm" action="<?= base_url('tipo_plan/guardar') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_tipo_plan" id="tpId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                    <div><h2 id="tpModalTitle">Añadir tipo de plan</h2><p id="tpModalDescription">Define una modalidad para tus planes de servicio.</p></div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="tpFormError" class="alert alert-danger" role="alert" hidden></div>
                    <label for="tpName">Nombre del tipo de plan <span class="text-danger">*</span></label>
                    <input type="text" id="tpName" name="nombre_tipo" class="form-control" placeholder="Ej.: Mensual, trimestral o anual" maxlength="50" required aria-describedby="tpNameHelp">
                    <p id="tpNameHelp">Usa un nombre breve y fácil de identificar. Máximo 50 caracteres.</p>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="tpSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar tipo de plan</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var tablaplanes 

    $(document).ready(function() {
        
        tablaplanes = $('#tablaplanes').DataTable({
            autoWidth: false,
            lengthChange: false,
            dom: 'rt<"tp-table-footer"ip>',
            pageLength: 10,
            order: [[0, 'asc']],
            drawCallback: function () {
                var api = this.api();
                var info = api.page.info();
                document.getElementById('tpCount').textContent = info.recordsTotal;
                document.getElementById('tpStatTotal').textContent = info.recordsTotal;
                document.getElementById('tpStatShown').textContent = info.recordsDisplay;
                var ultimo = null;
                api.rows().data().each(function (r) { if (!ultimo || Number(r.id_tipo_plan) > Number(ultimo.id_tipo_plan)) ultimo = r; });
                document.getElementById('tpStatLast').textContent = ultimo ? ultimo.nombre_tipo : '—';
            },
            "ajax": {
                "url": "<?= base_url('tipo_plan/listar') ?>",
                "type": "GET",
                "dataSrc": "data"
            },
            "columns": [
                { "data": "id_tipo_plan", "className": "tp-code", "render": function (data, type) {
                    if (type !== 'display') return data;
                    return '<span class="tk-code-pill">TP-' + String(Number(data) || 0).padStart(2, '0') + '</span>';
                } },
                { "data": "nombre_tipo", "render": function (data, type) {
                    if (type !== 'display') return data;
                    var label = $('<span>').text(data || '').html();
                    return '<span class="tp-name"><span class="tp-type-icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>'
                        + '<span class="tp-name-txt"><b>' + label + '</b><small>Modalidad de plan</small></span></span>';
                } },
                { 
                    "data": null,
                    "className": "pdsg-td-end tp-actions-cell",
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, row) {
                        return `
                            <span class="pdsg-actions">
                            <button type="button" class="pdsg-action pdsg-action-edit tp-edit" title="Editar tipo de plan" aria-label="Editar tipo de plan">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="pdsg-action pdsg-action-danger" title="Eliminar tipo de plan" aria-label="Eliminar tipo de plan" onclick="eliminarTipoPlan(${Number(row.id_tipo_plan)})">
                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                            </button>
                            </span>
                        `;
                    }
                }
            ],
             "language": {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": '<div class="tp-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>Sin coincidencias</strong><span>Prueba con otro nombre o código.</span></div>',
    "emptyTable": '<div class="tp-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>Aún no hay tipos de plan</strong><span>Usa Añadir para registrar el primero.</span></div>',
    "info": "Mostrando _START_–_END_ de _TOTAL_ tipos",
    "infoEmpty": "0 tipos",
    "infoFiltered": "(de _MAX_ en total)",
    "search": "Buscar:",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": '<i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">Siguiente</span>',
        "previous": '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">Anterior</span>'
    }
}
        });
        $('#tpSearch').on('input', function () { tablaplanes.search(this.value).draw(); });
        if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            gsap.fromTo('.tp-page > *', { opacity: 0, y: 10 }, {
                opacity: 1, y: 0, duration: .35, stagger: .06, ease: 'power2.out', clearProps: 'opacity,transform'
            });
        }
    })

    function eliminarTipoPlan(id_tipo_plan) {
        confirmDsg({ message: '¿Estás seguro de que deseas eliminar este tipo de plan?' }).then(function (ok) {
            if (!ok) return;

            $.ajax({
                url: '<?= base_url('tipo_plan/eliminar/') ?>',
                type: 'POST',
                data: { id_tipo_plan: id_tipo_plan, ['<?= csrf_token() ?>']: document.querySelector('#tpForm input[name="<?= csrf_token() ?>"]').value },
                success: function(response) {
                    if (response.status === 'success') {

                        alert(response.message);
                        tablaplanes.ajax.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ocurrió un error al intentar eliminar el tipo de plan.');
                }
            });
        });
    }

    
</script>
<script src="<?= base_url('js/tipo-plan-editor.js?v=' . filemtime(FCPATH . 'js/tipo-plan-editor.js')) ?>"></script>
