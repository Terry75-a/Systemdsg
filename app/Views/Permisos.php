<link rel="stylesheet" href="<?= base_url('css/dashboard/permisos.css?v=' . filemtime(FCPATH . 'css/dashboard/permisos.css')) ?>">
<main class="tp-page pe-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Configuración de Permisos</h1>
            <p class="pdsg-page-sub">Define qué acciones puede realizar cada rol en cada módulo del sistema.</p>
        </div>
        <button type="button" id="peAdd" class="btn pdsg-add-btn pe-add" data-bs-toggle="modal" data-bs-target="#peModal">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Asignar / Actualizar Permiso
        </button>
    </div>

    <div id="peNotice" class="alert alert-success" role="status" hidden></div>

    <section class="pdsg-card pdsg-table-card" aria-label="Listado de permisos">
        <div class="pe-toolbar">
            <div class="pe-category"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><span>Todos los permisos</span><span class="pdsg-tab-count" id="peCount" aria-live="polite">0</span></div>
            <label class="pe-search" for="peSearch"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="peSearch" placeholder="Buscar por rol, módulo…" aria-label="Buscar permiso"></label>
        </div>
        <div class="pdsg-table-wrap">
            <table id="tablaPermisos" class="pdsg-table" style="width:100%">
                <thead><tr>
                    <th scope="col" class="pe-id-column">#</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Módulo</th>
                    <th scope="col" class="pe-perm-column">Ver</th>
                    <th scope="col" class="pe-perm-column">Crear</th>
                    <th scope="col" class="pe-perm-column">Editar</th>
                    <th scope="col" class="pe-perm-column">Eliminar</th>
                    <th scope="col" class="pdsg-th-end">Acciones</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</main>

<!-- Modal: Asignar / Actualizar Permiso -->
<div class="modal fade" id="peModal" tabindex="-1" aria-labelledby="peModalTitle" aria-describedby="peModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="peForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id_permiso" id="peId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="peModalTitle">Asignar / Actualizar Permiso</h2>
                        <p id="peModalDescription">Si ya existe un permiso para este rol y módulo, se actualizará el registro existente.</p>
                    </div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="peFormError" class="alert alert-danger" role="alert" hidden></div>

                    <label for="peRol">Rol <span class="text-danger">*</span></label>
                    <select name="id_rol" id="peRol" class="form-control" required>
                        <option value="">-- Selecciona un rol --</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r->id_rol ?>"><?= esc($r->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="peMenu" class="mt-3">Módulo <span class="text-danger">*</span></label>
                    <select name="menu_id" id="peMenu" class="form-control" required>
                        <option value="">-- Selecciona un módulo --</option>
                        <?php foreach ($menus as $m): ?>
                            <option value="<?= $m->id ?>"><?= esc($m->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label fw-semibold mt-3">Acciones permitidas</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="read" id="peRead" value="1">
                                <label class="form-check-label" for="peRead">Ver</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="insert" id="peInsert" value="1">
                                <label class="form-check-label" for="peInsert">Crear</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="update" id="peUpdate" value="1">
                                <label class="form-check-label" for="peUpdate">Editar</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="delete" id="peDelete" value="1">
                                <label class="form-check-label" for="peDelete">Eliminar</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="peSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar permiso</span></button>
                </div>
            </form>
        </div>
    </div>

<!-- Modal: Editar Permiso -->
<div class="modal fade" id="peEditModal" tabindex="-1" aria-labelledby="peEditModalTitle" aria-describedby="peEditModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="peEditForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id_permiso" id="peEditId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="peEditModalTitle">Editar Permiso</h2>
                        <p id="peEditModalDescription">Rol: <strong id="peEditRol">—</strong> · Módulo: <strong id="peEditMenu">—</strong></p>
                    </div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="peEditError" class="alert alert-danger" role="alert" hidden></div>

                    <label class="form-label fw-semibold">Acciones permitidas</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="read" id="peEditRead" value="1">
                                <label class="form-check-label" for="peEditRead">Ver</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="insert" id="peEditInsert" value="1">
                                <label class="form-check-label" for="peEditInsert">Crear</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="update" id="peEditUpdate" value="1">
                                <label class="form-check-label" for="peEditUpdate">Editar</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="delete" id="peEditDelete" value="1">
                                <label class="form-check-label" for="peEditDelete">Eliminar</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="peEditSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Actualizar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

<script>
const BASE_URL = '<?= base_url() ?>';
const CSRF_TOKEN = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

let tablaPermisos;

$(document).ready(function () {
    tablaPermisos = $('#tablaPermisos').DataTable({
        autoWidth: false,
        lengthChange: false,
        dom: 'rt<"pe-table-footer"ip>',
        pageLength: 10,
        order: [[0, 'asc']],
        drawCallback: function () {
            const api = this.api();
            $('#peCount').text(api.page.info().recordsTotal);
        },
        ajax: {
            url: BASE_URL + 'permisos/listar',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_permiso', className: 'pe-code' },
            { data: 'rol_nombre', className: 'pe-rol' },
            { data: 'menu_nombre', className: 'pe-menu' },
            {
                data: 'read',
                className: 'pe-perm-cell',
                render: function (data) {
                    const v = Number(data) === 1;
                    return '<span class="pe-badge ' + (v ? 'pe-badge-on' : 'pe-badge-off') + '">' + (v ? '<i class="fa-solid fa-check" aria-hidden="true"></i>' : '<i class="fa-solid fa-xmark" aria-hidden="true"></i>') + '</span>';
                }
            },
            {
                data: 'insert',
                className: 'pe-perm-cell',
                render: function (data) {
                    const v = Number(data) === 1;
                    return '<span class="pe-badge ' + (v ? 'pe-badge-on' : 'pe-badge-off') + '">' + (v ? '<i class="fa-solid fa-check" aria-hidden="true"></i>' : '<i class="fa-solid fa-xmark" aria-hidden="true"></i>') + '</span>';
                }
            },
            {
                data: 'update',
                className: 'pe-perm-cell',
                render: function (data) {
                    const v = Number(data) === 1;
                    return '<span class="pe-badge ' + (v ? 'pe-badge-on' : 'pe-badge-off') + '">' + (v ? '<i class="fa-solid fa-check" aria-hidden="true"></i>' : '<i class="fa-solid fa-xmark" aria-hidden="true"></i>') + '</span>';
                }
            },
            {
                data: 'delete',
                className: 'pe-perm-cell',
                render: function (data) {
                    const v = Number(data) === 1;
                    return '<span class="pe-badge ' + (v ? 'pe-badge-on' : 'pe-badge-off') + '">' + (v ? '<i class="fa-solid fa-check" aria-hidden="true"></i>' : '<i class="fa-solid fa-xmark" aria-hidden="true"></i>') + '</span>';
                }
            },
            {
                data: null,
                className: 'pdsg-td-end pe-actions-cell',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <span class="pdsg-actions">
                            <button type="button" class="pdsg-action pdsg-action-edit pe-edit" data-id="${row.id_permiso}" title="Editar" aria-label="Editar permiso">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="pdsg-action pdsg-action-danger pe-delete" data-id="${row.id_permiso}" title="Eliminar" aria-label="Eliminar permiso">
                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                            </button>
                        </span>
                    `;
                }
            }
        ],
        language: {
            processing: "Procesando...",
            zeroRecords: '<div class="tp-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>Sin coincidencias</strong><span>Prueba con otro término.</span></div>',
            emptyTable: '<div class="tp-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>No hay permisos configurados</strong><span>Usa “Asignar / Actualizar Permiso” para crear el primero.</span></div>',
            info: "_START_–_END_ de _TOTAL_ permisos",
            infoEmpty: "0 permisos",
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

    $('#peSearch').on('input', function () { tablaPermisos.search(this.value).draw(); });

    // Helper: limpia backdrops huérfanos al cerrar un modal
    function cleanBackdrops() {
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    // Limpieza al cerrar (hidden.bs.modal)
    $('#peModal, #peEditModal').on('hidden.bs.modal', cleanBackdrops);

    // Abrir modal crear (limpia formulario)
    $('#peModal').on('show.bs.modal', function () {
        $('#peForm')[0].reset();
        $('#peId').val('');
        $('#peFormError').hide();
    });

    // Abrir modal editar
    $('#peEditModal').on('show.bs.modal', function () {
        // No limpiar backdrops aquí - Bootstrap los crea después de este evento
    });

    // Guardar nuevo / actualizar
    $('#peForm').on('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = BASE_URL + 'permisos/guardar';
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (!data.success) { $('#peFormError').text(data.message || 'Error al guardar.').show(); return; }
            bootstrap.Modal.getInstance(document.getElementById('peModal')).hide();
            cleanBackdrops();
            tablaPermisos.ajax.reload();
        } catch (err) { $('#peFormError').text('Error de conexión.').show(); }
    });

    // Abrir modal editar
    $(document).on('click', '.pe-edit', function () {
        const id = $(this).data('id');
        fetch(BASE_URL + 'permisos/obtener/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(res => {
                if (!res.success) { alert(res.message || 'No se pudo cargar.'); return; }
                const p = res.permiso;
                $('#peEditId').val(p.id_permiso);
                $('#peEditRol').text(p.rol_nombre ?? '—');
                $('#peEditMenu').text(p.menu_nombre ?? '—');
                $('#peEditRead').prop('checked', Number(p.read) === 1);
                $('#peEditInsert').prop('checked', Number(p.insert) === 1);
                $('#peEditUpdate').prop('checked', Number(p.update) === 1);
                $('#peEditDelete').prop('checked', Number(p.delete) === 1);
                $('#peEditError').hide();
                new bootstrap.Modal(document.getElementById('peEditModal')).show();
            })
            .catch(() => alert('Error al cargar el permiso.'));
    });

    // Guardar edición
    $('#peEditForm').on('submit', async function (e) {
        e.preventDefault();
        const id = $('#peEditId').val();
        const formData = new FormData(this);
        const url = BASE_URL + 'permisos/editar/' + id;
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (!data.success) { $('#peEditError').text(data.message || 'Error al actualizar.').show(); return; }
            bootstrap.Modal.getInstance(document.getElementById('peEditModal')).hide();
            cleanBackdrops();
            tablaPermisos.ajax.reload();
        } catch (err) { $('#peEditError').text('Error de conexión.').show(); }
    });

    // Eliminar
    $(document).on('click', '.pe-delete', function () {
        if (!confirm('¿Eliminar este permiso?')) return;
        const id = $(this).data('id');
        fetch(BASE_URL + 'permisos/eliminar/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ [CSRF_TOKEN]: CSRF_HASH }).toString()
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) { alert(res.message || 'Error al eliminar.'); return; }
                tablaPermisos.ajax.reload();
            })
            .catch(() => alert('Error de conexión.'));
    });

    // Animación de entrada
    if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        gsap.fromTo('.pe-page > *', { opacity: 0, y: 10 }, {
            opacity: 1, y: 0, duration: .35, stagger: .06, ease: 'power2.out', clearProps: 'opacity,transform'
        });
    }

    // Limpieza explícita al clicar Cancelar / Cerrar (data-bs-dismiss no siempre dispara hidden.bs.modal en test)
    $('#peModal .tp-modal-cancel, #peModal .tp-modal-close, #peEditModal .tp-modal-cancel, #peEditModal .tp-modal-close').on('click', function() {
        // hidden.bs.modal se disparará automáticamente, pero por si acaso:
        setTimeout(cleanBackdrops, 200);
    });
});
</script>