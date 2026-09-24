<link rel="stylesheet" href="<?= base_url('css/dashboard/tipo-plan.css?v=' . filemtime(FCPATH . 'css/dashboard/tipo-plan.css')) ?>">
<main class="tp-page pl-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Gestión de Planes</h1>
            <p class="pdsg-page-sub">Administra los planes de servicio para tus clientes</p>
        </div>
        <button type="button" id="plAdd" class="btn pdsg-add-btn tp-add">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Nuevo Plan
        </button>
    </div>

    <div id="plNotice" class="alert alert-success" role="status" hidden></div>
    <section class="pdsg-card pdsg-table-card" aria-label="Listado de planes">
        <div class="tp-toolbar">
            <div class="tp-category"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><span>Todos los planes</span><span class="pdsg-tab-count" id="plCount" aria-live="polite">—</span></div>
            <label class="tp-search" for="plSearch"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="plSearch" placeholder="Buscar plan…" aria-label="Buscar plan"></label>
        </div>
        <div class="pdsg-table-wrap">
            <table id="tablplanes" class="pdsg-table" style="width:100%">
                <thead><tr>
                    <th scope="col" class="tp-id-column">Código</th>
                    <th scope="col">Plan</th>
                    <th scope="col">Precio</th>
                    <th scope="col" class="pdsg-th-end">Acciones</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</main>


<div class="modal fade" id="plModal" tabindex="-1" aria-labelledby="plModalTitle" aria-describedby="plModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="plForm" action="<?= base_url('planes/guardar') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_plan" id="plId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                    <div><h2 id="plModalTitle">Nuevo Plan</h2><p id="plModalDescription">Define un plan de servicio para tus clientes.</p></div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="plFormError" class="alert alert-danger" role="alert" hidden></div>

                    <label for="plName">Nombre del plan <span class="text-danger">*</span></label>
                    <input type="text" id="plName" name="nombre_plan" class="form-control" placeholder="Ej.: Plan Básico, Premium…" maxlength="100" required aria-describedby="plNameHelp">
                    <p id="plNameHelp">Usa un nombre claro y breve. Máximo 100 caracteres.</p>

                    <label for="plDesc" class="mt-3">Descripción</label>
                    <textarea id="plDesc" name="descripcion" class="form-control" rows="2" placeholder="Describe brevemente qué incluye este plan…"></textarea>

                    <label for="plPrecio" class="mt-3">Precio (S/) <span class="text-danger">*</span></label>
                    <input type="number" id="plPrecio" name="precio" class="form-control" min="0" step="0.01" placeholder="0.00" required>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label for="plInicio" class="mt-3">Fecha de inicio</label>
                            <input type="date" id="plInicio" name="fecha_de_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="plVenc" class="mt-3">Fecha de vencimiento</label>
                            <input type="date" id="plVenc" name="fecha_de_vencimiento" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="plSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar plan</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var tablplanes;

    $(document).ready(function () {
        tablplanes = $('#tablplanes').DataTable({
            autoWidth: false,
            lengthChange: false,
            dom: 'rt<"tp-table-footer"ip>',
            pageLength: 10,
            order: [[0, 'asc']],
            drawCallback: function () {
                var api = this.api();
                document.getElementById('plCount').textContent = api.page.info().recordsTotal;
            },
            ajax: {
                url: "<?= base_url('planes/listar') ?>",
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id_plan', className: 'tp-code' },
                { data: 'nombre_plan', render: function (data, type) {
                    if (type !== 'display') return data;
                    var label = $('<span>').text(data || '').html();
                    return '<span class="tp-name"><span class="tp-type-icon"><i class="fa-regular fa-credit-card" aria-hidden="true"></i></span><span>' + label + '</span></span>';
                }},
                { data: 'precio', render: function (data, type) {
                    if (type !== 'display') return data;
                    var n = parseFloat(data) || 0;
                    return 'S/ ' + n.toFixed(2);
                }},
                {
                    data: null,
                    className: 'pdsg-td-end tp-actions-cell',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <span class="pdsg-actions">
                            <button type="button" class="pdsg-action pdsg-action-edit pl-edit" title="Editar plan" aria-label="Editar plan">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="pdsg-action pdsg-action-danger" title="Eliminar plan" aria-label="Eliminar plan" onclick="eliminarPlan(${Number(row.id_plan)})">
                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                            </button>
                            </span>
                        `;
                    }
                }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: '<div class="tp-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>Sin coincidencias</strong><span>Prueba con otro nombre o código.</span></div>',
                emptyTable: '<div class="tp-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>Aún no hay planes</strong><span>Usa Nuevo Plan para registrar el primero.</span></div>',
                info: "_START_–_END_ de _TOTAL_ planes",
                infoEmpty: "0 planes",
                infoFiltered: "(de _MAX_ en total)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
        $('#plSearch').on('input', function () { tablplanes.search(this.value).draw(); });
        if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            gsap.fromTo('.pl-page > *', { opacity: 0, y: 10 }, {
                opacity: 1, y: 0, duration: .35, stagger: .06, ease: 'power2.out', clearProps: 'opacity,transform'
            });
        }
    });

    function eliminarPlan(id_plan) {
        if (confirm('¿Estás seguro de que deseas eliminar este plan?')) {
            $.ajax({
                url: '<?= base_url('planes/eliminar') ?>',
                type: 'POST',
                data: { id_plan: id_plan, ['<?= csrf_token() ?>']: document.querySelector('#plForm input[name="<?= csrf_token() ?>"]').value },
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        tablplanes.ajax.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Ocurrió un error al intentar eliminar el plan.');
                }
            });
        }
    }
</script>
<script src="<?= base_url('js/plane-editor.js?v=' . filemtime(FCPATH . 'js/plane-editor.js')) ?>"></script>
