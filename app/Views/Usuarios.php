<?php
$menusPayload = array_map(static fn($menu) => [
    'id' => (int) $menu->id,
    'nombre' => $menu->nombre,
], $menus ?? []);
?>
<link rel="stylesheet" href="<?= base_url('css/dashboard/usuarios.css?v=' . filemtime(FCPATH . 'css/dashboard/usuarios.css')) ?>">
<main class="tp-page us-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Gestión de Usuarios</h1>
            <p class="pdsg-page-sub">Alta, edición, estado y permisos asociados al rol.</p>
        </div>
        <button type="button" id="usAdd" class="btn pdsg-add-btn us-add" data-bs-toggle="modal" data-bs-target="#usModal">
            <i class="fa-solid fa-user-plus" aria-hidden="true"></i> Nuevo Usuario
        </button>
    </div>

    <div id="usNotice" class="alert alert-success" role="status" hidden></div>

    <section class="pdsg-card pdsg-table-card" aria-label="Listado de usuarios">
        <div class="us-toolbar">
            <div class="us-category"><i class="fa-solid fa-users" aria-hidden="true"></i><span>Todos los usuarios</span><span class="pdsg-tab-count" id="usCount" aria-live="polite">0</span></div>
            <label class="us-search" for="usSearch"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="usSearch" placeholder="Buscar usuario, nombre, email…" aria-label="Buscar usuario"></label>
        </div>
        <div class="pdsg-table-wrap">
            <table id="tablaUsuarios" class="pdsg-table" style="width:100%">
                <thead><tr>
                    <th scope="col" class="us-id-column">#</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Persona</th>
                    <th scope="col">Contacto</th>
                    <th scope="col">Rol</th>
                    <th scope="col" class="us-status-column">Estado</th>
                    <th scope="col" class="pdsg-th-end">Acciones</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</main>

<!-- Modal: Nuevo Usuario -->
<div class="modal fade" id="usModal" tabindex="-1" aria-labelledby="usModalTitle" aria-describedby="usModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="usForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" id="usId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                    <div><h2 id="usModalTitle">Nuevo Usuario</h2><p id="usModalDescription">Registra una persona y su acceso al sistema.</p></div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="usFormError" class="alert alert-danger" role="alert" hidden></div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="usNombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="usNombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usApellidoPaterno">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" id="usApellidoPaterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usApellidoMaterno">Apellido materno</label>
                            <input type="text" id="usApellidoMaterno" name="apellido_materno" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="usDni">DNI <span class="text-danger">*</span></label>
                            <input type="text" id="usDni" name="dni" class="form-control" maxlength="15" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usCorreo">Correo <span class="text-danger">*</span></label>
                            <input type="email" id="usCorreo" name="correo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usTelefono">Teléfono</label>
                            <input type="text" id="usTelefono" name="telefono" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label for="usUsername">Usuario <span class="text-danger">*</span></label>
                            <input type="text" id="usUsername" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usPassword">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" id="usPassword" name="password" class="form-control" required autocomplete="new-password">
                            <small id="usPassHelp" class="form-text text-muted">Mínimo 6 caracteres.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="usRol">Rol <span class="text-danger">*</span></label>
                            <select name="id_rol" id="usRol" class="form-control" required>
                                <option value="">-- Seleccionar rol --</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol->id_rol ?>"><?= esc($rol->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="usSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar</span></button>
                </div>
            </form>
        </div>
    </div>

<!-- Modal: Editar Usuario -->
<div class="modal fade" id="usEditModal" tabindex="-1" aria-labelledby="usEditModalTitle" aria-describedby="usEditModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="usEditForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" id="usEditId">
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-user-pen" aria-hidden="true"></i></span>
                    <div><h2 id="usEditModalTitle">Editar Usuario</h2><p id="usEditModalDescription">Modifica los datos personales y de acceso.</p></div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="usEditError" class="alert alert-danger" role="alert" hidden></div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="usEditNombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="usEditNombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditApellidoPaterno">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" id="usEditApellidoPaterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditApellidoMaterno">Apellido materno</label>
                            <input type="text" id="usEditApellidoMaterno" name="apellido_materno" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="usEditDni">DNI <span class="text-danger">*</span></label>
                            <input type="text" id="usEditDni" name="dni" class="form-control" maxlength="15" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditCorreo">Correo <span class="text-danger">*</span></label>
                            <input type="email" id="usEditCorreo" name="correo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditTelefono">Teléfono</label>
                            <input type="text" id="usEditTelefono" name="telefono" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label for="usEditUsername">Usuario <span class="text-danger">*</span></label>
                            <input type="text" id="usEditUsername" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditPassword">Nueva contraseña</label>
                            <input type="password" id="usEditPassword" name="password" class="form-control" autocomplete="new-password">
                            <small class="form-text text-muted">Dejalo vacío si no querés cambiarla.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditRol">Rol <span class="text-danger">*</span></label>
                            <select name="id_rol" id="usEditRol" class="form-control" required>
                                <option value="">-- Seleccionar rol --</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol->id_rol ?>"><?= esc($rol->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="usEditSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Permisos del Rol -->
<div class="modal fade" id="usPermModal" tabindex="-1" aria-labelledby="usPermModalTitle" aria-describedby="usPermModalDescription" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="usPermForm" novalidate>
                <div class="tp-modal-head">
                    <span class="tp-modal-icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                    <div>
                        <h2 id="usPermModalTitle">Permisos del Rol</h2>
                        <p id="usPermModalDescription">Usuario: <strong id="usPermUserLabel">—</strong> · Rol afectado: <strong id="usPermRolLabel">—</strong></p>
                    </div>
                    <button type="button" class="tp-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>
                <div class="tp-modal-body">
                    <div id="usPermError" class="alert alert-danger" role="alert" hidden></div>
                    <div class="alert alert-warning small" role="status">
                        <strong>Ojo:</strong> los permisos se guardan por <strong>rol</strong>, no por usuario individual. Cualquier cambio impacta a todos los usuarios que compartan ese rol.
                    </div>
                    <input type="hidden" id="usPermIdUsuario">
                    <div class="table-responsive">
                        <table class="pdsg-table" style="width:100%">
                            <thead><tr>
                                <th scope="col">Módulo</th>
                                <th scope="col" class="us-perm-column">Ver</th>
                                <th scope="col" class="us-perm-column">Crear</th>
                                <th scope="col" class="us-perm-column">Editar</th>
                                <th scope="col" class="us-perm-column">Eliminar</th>
                            </tr></thead>
                            <tbody id="usPermBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tp-modal-footer">
                    <button type="button" class="btn tp-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn tp-modal-save" id="usPermSave"><i class="fa-solid fa-check" aria-hidden="true"></i> <span>Guardar</span></button>
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
const MENUS = <?= json_encode($menusPayload, JSON_UNESCAPED_UNICODE) ?>;

let tablaUsuarios;

$(document).ready(function () {
    tablaUsuarios = $('#tablaUsuarios').DataTable({
        autoWidth: false,
        lengthChange: false,
        dom: 'rt<"us-table-footer"ip>',
        pageLength: 10,
        order: [[0, 'desc']],
        drawCallback: function () {
            const api = this.api();
            $('#usCount').text(api.page.info().recordsTotal);
        },
        ajax: {
            url: BASE_URL + 'usuarios/listar',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_usuario', className: 'us-code' },
            {
                data: null,
                className: 'us-user-cell',
                render: function (data, type, row) {
                    if (type !== 'display') return row.username;
                    return `
                        <div class="us-user-name">${escapeHtml(row.username)}</div>
                        <div class="us-user-id">ID #${row.id_usuario}</div>
                    `;
                }
            },
            {
                data: null,
                className: 'us-person-cell',
                render: function (data, type, row) {
                    if (type !== 'display') return '';
                    const full = [row.nombre, row.apellido_paterno, row.apellido_materno].filter(Boolean).join(' ');
                    const dni = row.dni ? 'DNI: ' + row.dni : '—';
                    return `
                        <div class="us-person-name">${escapeHtml(full || 'Sin nombre registrado')}</div>
                        <div class="us-person-dni">${escapeHtml(dni)}</div>
                    `;
                }
            },
            {
                data: null,
                className: 'us-contact-cell',
                render: function (data, type, row) {
                    if (type !== 'display') return '';
                    return '<div class="us-contact-email">' + escapeHtml(row.correo ?? '—') + '</div>';
                }
            },
            {
                data: 'rol_nombre',
                className: 'us-rol-cell'
            },
            {
                data: 'estado',
                className: 'us-status-cell',
                render: function (data) {
                    const on = Number(data) === 1;
                    return '<span class="us-badge ' + (on ? 'us-badge-on' : 'us-badge-off') + '">' + (on ? 'Activo' : 'Inactivo') + '</span>';
                }
            },
            {
                data: null,
                className: 'pdsg-td-end us-actions-cell',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <span class="pdsg-actions">
                            <button type="button" class="pdsg-action pdsg-action-edit" onclick="openEditUsuario(${row.id_usuario})" title="Editar usuario" aria-label="Editar usuario">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="pdsg-action pdsg-action-info" onclick="openPermisosUsuario(${row.id_usuario})" title="Permisos del rol" aria-label="Permisos del rol">
                                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="pdsg-action ${Number(row.estado) === 1 ? 'pdsg-action-warning' : 'pdsg-action-success'}" onclick="toggleEstadoUsuario(${row.id_usuario}, ${row.estado})" title="${Number(row.estado) === 1 ? 'Desactivar' : 'Activar'} usuario" aria-label="${Number(row.estado) === 1 ? 'Desactivar' : 'Activar'} usuario">
                                <i class="fa-solid ${Number(row.estado) === 1 ? 'fa-user-slash' : 'fa-user-check'}" aria-hidden="true"></i>
                            </button>
                        </span>
                    `;
                }
            }
        ],
        language: {
            processing: "Procesando...",
            zeroRecords: '<div class="tp-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>Sin coincidencias</strong><span>Prueba con otro término.</span></div>',
            emptyTable: '<div class="tp-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>No hay usuarios registrados</strong><span>Usa “Nuevo Usuario” para crear el primero.</span></div>',
            info: "_START_–_END_ de _TOTAL_ usuarios",
            infoEmpty: "0 usuarios",
            infoFiltered: "(de _MAX_ en total)",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });

    // --- Helpers (global scope para onclick inline) ---
    window.escapeHtml = function (value) {
        return String(value ?? '')
            .replace(/&/g, '&')
            .replace(/</g, '<')
            .replace(/>/g, '>')
            .replace(/"/g, '"')
            .replace(/'/g, '&#039;');
    };
    window.buildAlert = function (type, content) {
        return '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' + content + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    };
    window.renderErrors = function (errors = {}) {
        const items = Object.values(errors).filter(Boolean);
        if (!items.length) return 'Ocurrió un error inesperado.';
        return '<ul class="mb-0 ps-3">' + items.map(item => '<li>' + escapeHtml(item) + '</li>').join('') + '</ul>';
    };
    window.setAlert = function (containerId, type, html) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = html ? buildAlert(type, html) : '';
    };
    window.clearAlert = function (containerId) { setAlert(containerId, '', ''); };
    window.fullName = function (u) {
        return [u.nombre, u.apellido_paterno, u.apellido_materno].filter(Boolean).join(' ').trim();
    };
    window.fillUserForm = function (prefix, usuario) {
        document.getElementById(prefix + 'Nombre').value = usuario.nombre ?? '';
        document.getElementById(prefix + 'ApellidoPaterno').value = usuario.apellido_paterno ?? '';
        document.getElementById(prefix + 'ApellidoMaterno').value = usuario.apellido_materno ?? '';
        document.getElementById(prefix + 'Dni').value = usuario.dni ?? '';
        document.getElementById(prefix + 'Correo').value = usuario.correo ?? '';
        document.getElementById(prefix + 'Telefono').value = usuario.telefono ?? '';
        document.getElementById(prefix + 'Username').value = usuario.username ?? '';
        document.getElementById(prefix + 'Rol').value = usuario.id_rol ?? '';
    };
    window.renderPermsBody = function (permisos = []) {
        const tbody = document.getElementById('usPermBody');
        const permMap = new Map((permisos || []).map(p => [Number(p.menu_id), p]));
        if (!MENUS.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No hay menús configurados.</td></tr>';
            return;
        }
        tbody.innerHTML = MENUS.map(menu => {
            const p = permMap.get(Number(menu.id)) ?? {};
            return `
                <tr data-menu-id="${menu.id}">
                    <td>${escapeHtml(menu.nombre)}</td>
                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="read" ${Number(p.read) === 1 ? 'checked' : ''}></td>
                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="insert" ${Number(p.insert) === 1 ? 'checked' : ''}></td>
                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="update" ${Number(p.update) === 1 ? 'checked' : ''}></td>
                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="delete" ${Number(p.delete) === 1 ? 'checked' : ''}></td>
                </tr>
            `;
        }).join('');
    }

    // --- Nuevo usuario modal ---
    $('#usModal').on('show.bs.modal', function () {
        $('#usForm')[0].reset();
        $('#usId').val('');
        $('#usFormError').hide();
        $('#usPassHelp').text('Mínimo 6 caracteres.').removeClass('text-danger');
        $('#usPassword').prop('required', true);
    });

    // --- Guardar nuevo ---
    $('#usForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usFormError');
        const formData = new FormData(this);
        try {
            const res = await fetch(BASE_URL + 'usuarios/registrar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (!data.success) { setAlert('usFormError', 'danger', renderErrors(data.errors ?? { general: data.message })); return; }
            bootstrap.Modal.getInstance(document.getElementById('usModal')).hide();
            tablaUsuarios.ajax.reload();
        } catch (err) { setAlert('usFormError', 'danger', 'Error de conexión.'); }
    });

    // --- Editar usuario ---
    // (Ahora se bindea en bindActionButtons / draw.dt)

    $('#usEditForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usEditError');
        const id = $('#usEditId').val();
        const formData = new FormData(this);
        try {
            const res = await fetch(BASE_URL + 'usuarios/actualizar/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (!data.success) { setAlert('usEditError', 'danger', renderErrors(data.errors ?? { general: data.message })); return; }
            bootstrap.Modal.getInstance(document.getElementById('usEditModal')).hide();
            tablaUsuarios.ajax.reload();
        } catch (err) { setAlert('usEditError', 'danger', 'Error de conexión.'); }
    });

    // --- Permisos del rol ---
    // (Ahora se bindea en bindActionButtons / draw.dt)

    $('#usPermForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usPermError');
        const id = $('#usPermIdUsuario').val();
        const payload = Array.from(document.querySelectorAll('#usPermBody tr[data-menu-id]')).map(row => ({
            menu_id: Number(row.dataset.menuId),
            read: row.querySelector('[data-action="read"]').checked ? 1 : 0,
            insert: row.querySelector('[data-action="insert"]').checked ? 1 : 0,
            update: row.querySelector('[data-action="update"]').checked ? 1 : 0,
            delete: row.querySelector('[data-action="delete"]').checked ? 1 : 0,
        }));
        try {
            const res = await fetch(BASE_URL + 'usuarios/permisos/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [CSRF_TOKEN]: CSRF_HASH
                },
                body: JSON.stringify({ permisos: payload })
            });
            const data = await res.json();
            if (!data.success) { setAlert('usPermError', 'danger', escapeHtml(data.message ?? 'No se pudieron guardar.')); return; }
            bootstrap.Modal.getInstance(document.getElementById('usPermModal')).hide();
        } catch (err) { setAlert('usPermError', 'danger', 'Error de conexión.'); }
    });

    // --- Toggle estado ---
    // (Ahora se bindea via onclick inline)

    // Funciones globales para onclick inline (evitan problemas de delegación con DataTables)
    window.openEditUsuario = function (id) {
        // Ensure create modal is closed
        var createModalEl = document.getElementById('usModal');
        if (createModalEl.classList.contains('show')) {
            var inst = bootstrap.Modal.getInstance(createModalEl);
            if (inst) inst.hide();
        }
        clearAlert('usEditError');
        fetch(BASE_URL + 'usuarios/obtener/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) { setAlert('usEditError', 'danger', escapeHtml(data.message ?? 'No se pudo cargar.')); new bootstrap.Modal(document.getElementById('usEditModal')).show(); return; }
                const u = data.data;
                $('#usEditId').val(u.id_usuario);
                $('#usEditPassword').val('');
                fillUserForm('usEdit', u);
                new bootstrap.Modal(document.getElementById('usEditModal')).show();
            })
            .catch(() => setAlert('usEditError', 'danger', 'Error de conexión.'));
    };

    window.openPermisosUsuario = function (id) {
        // Ensure other modals are closed
        ['usModal', 'usEditModal'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el?.classList.contains('show')) {
                var inst = bootstrap.Modal.getInstance(el);
                if (inst) inst.hide();
            }
        });
        clearAlert('usPermError');
        fetch(BASE_URL + 'usuarios/obtener/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) { setAlert('usPermError', 'danger', escapeHtml(data.message ?? 'No se pudieron cargar.')); new bootstrap.Modal(document.getElementById('usPermModal')).show(); return; }
                const u = data.data;
                $('#usPermIdUsuario').val(u.id_usuario);
                $('#usPermUserLabel').text(u.username + ' · ' + (fullName(u) || 'Sin nombre'));
                $('#usPermRolLabel').text(u.rol_nombre ?? 'Sin rol');
                renderPermsBody(u.permisos ?? []);
                new bootstrap.Modal(document.getElementById('usPermModal')).show();
            })
            .catch(() => setAlert('usPermError', 'danger', 'Error de conexión.'));
    };

    window.toggleEstadoUsuario = function (id, estado) {
        const accion = estado === 1 ? 'desactivar' : 'activar';
        if (!confirm('¿Querés ' + accion + ' este usuario?')) return;
        const formData = new FormData();
        formData.append(CSRF_TOKEN, CSRF_HASH);
        fetch(BASE_URL + 'usuarios/estado/' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(r => r.json()).then(data => {
            if (!data.success) alert(data.message ?? 'No se pudo actualizar.');
            else tablaUsuarios.ajax.reload();
        }).catch(() => alert('Error de conexión.'));
    };

    // Animación de entrada
    if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        gsap.fromTo('.us-page > *', { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: .35, stagger: .06, ease: 'power2.out', clearProps: 'opacity,transform' });
    }
});
</script>