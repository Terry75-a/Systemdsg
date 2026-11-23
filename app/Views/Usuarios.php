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

    <div id="usNotice" role="status" hidden></div>

    <section class="pdsg-card pdsg-table-card" aria-label="Listado de usuarios">
        <div class="tk2-toolbar us-toolbar">
            <div class="tk2-chips">
                <div class="us-category"><span class="material-symbols-outlined" aria-hidden="true">group</span><span>Todos los usuarios</span><span class="pdsg-tab-count" id="usCount" aria-live="polite">0</span></div>
            </div>
            <label class="tk2-search us-search" for="usSearch">
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                <input type="search" id="usSearch" placeholder="Buscar usuario, nombre, email…" aria-label="Buscar usuario">
            </label>
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
                    <div id="usFormError" hidden></div>

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
                                    <option value="<?= (int) $rol->id_rol ?>"><?= esc($rol->nombre) ?></option>
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
                    <div id="usEditError" hidden></div>

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
                            <small class="form-text text-muted">Déjalo vacío si no quieres cambiarla.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="usEditRol">Rol <span class="text-danger">*</span></label>
                            <select name="id_rol" id="usEditRol" class="form-control" required>
                                <option value="">-- Seleccionar rol --</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= (int) $rol->id_rol ?>"><?= esc($rol->nombre) ?></option>
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
                    <div id="usPermError" hidden></div>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

<script>
const BASE_URL = '<?= base_url() ?>';
const CSRF_TOKEN = '<?= csrf_token() ?>';
const CSRF_HEADER = '<?= csrf_header() ?>';
let CSRF_HASH = '<?= csrf_hash() ?>';
const MENUS = <?= json_encode($menusPayload, JSON_UNESCAPED_UNICODE) ?>;

let tablaUsuarios;

window.escapeHtml = function (value) {
    const map = {
        '&': '&' + 'amp;',
        '<': '&' + 'lt;',
        '>': '&' + 'gt;',
        '"': '&' + 'quot;',
        "'": '&' + '#039;'
    };
    return String(value ?? '').replace(/[&<>"']/g, function (c) {
        return map[c];
    });
};

window.buildAlert = function (type, content) {
    return '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
        + content
        + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>'
        + '</div>';
};

window.renderErrors = function (errors, fallbackMessage) {
    const items = Object.values(errors || {}).filter(Boolean);
    if (!items.length) {
        return escapeHtml(fallbackMessage || 'Ocurrió un error inesperado.');
    }
    return '<ul class="mb-0 ps-3">' + items.map(function (item) {
        return '<li>' + escapeHtml(item) + '</li>';
    }).join('') + '</ul>';
};

window.setAlert = function (containerId, type, html) {
    const container = document.getElementById(containerId);
    if (!container) return;
    if (html) {
        container.innerHTML = buildAlert(type, html);
        container.hidden = false;
        container.style.display = '';
        const modalBody = container.closest('.tp-modal-body');
        if (modalBody) {
            modalBody.scrollTop = 0;
        }
    } else {
        container.innerHTML = '';
        container.hidden = true;
    }
};

window.clearAlert = function (containerId) {
    setAlert(containerId, '', '');
};

window.cleanupBackdrops = function () {
    if (!document.querySelector('.modal.show')) {
        document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }
};

window.openModal = function (modalId) {
    const el = document.getElementById(modalId);
    if (!el || !window.bootstrap || !bootstrap.Modal) return;
    bootstrap.Modal.getOrCreateInstance(el).show();
};

window.closeModal = function (modalId) {
    const el = document.getElementById(modalId);
    if (!el) {
        cleanupBackdrops();
        return;
    }

    const instance = (window.bootstrap && bootstrap.Modal)
        ? bootstrap.Modal.getInstance(el)
        : null;

    if (instance) {
        instance.hide();
    } else {
        const closeBtn = el.querySelector('[data-bs-dismiss="modal"]');
        if (closeBtn) {
            closeBtn.click();
        }
    }

    setTimeout(function () {
        if (el.classList.contains('show')) {
            const stuck = (window.bootstrap && bootstrap.Modal)
                ? bootstrap.Modal.getInstance(el)
                : null;
            if (stuck) {
                stuck.dispose();
            }
            el.classList.remove('show');
            el.setAttribute('aria-hidden', 'true');
            el.removeAttribute('aria-modal');
            el.style.display = 'none';
        }
        cleanupBackdrops();
    }, 350);
};

window.syncCsrfToken = function (res, data) {
    const nextHash = (data && (data.csrfHash || data.csrf_hash))
        || (res && res.headers && res.headers.get(CSRF_HEADER));
    if (!nextHash) return;
    CSRF_HASH = nextHash;
    document.querySelectorAll('input[name="' + CSRF_TOKEN + '"]').forEach(function (input) {
        input.value = nextHash;
    });
};

window.parseJsonResponse = async function (res) {
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        return { success: false, message: 'Respuesta inválida del servidor (HTTP ' + res.status + ').' };
    }
};

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

window.renderPermsBody = function (permisos) {
    const tbody = document.getElementById('usPermBody');
    const permMap = new Map((permisos || []).map(function (p) {
        return [Number(p.menu_id), p];
    }));
    if (!MENUS.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No hay menús configurados.</td></tr>';
        return;
    }
    tbody.innerHTML = MENUS.map(function (menu) {
        const p = permMap.get(Number(menu.id)) || {};
        return '<tr data-menu-id="' + Number(menu.id) + '">'
            + '<td>' + escapeHtml(menu.nombre) + '</td>'
            + '<td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="read" ' + (Number(p.read) === 1 ? 'checked' : '') + '></td>'
            + '<td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="insert" ' + (Number(p.insert) === 1 ? 'checked' : '') + '></td>'
            + '<td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="update" ' + (Number(p.update) === 1 ? 'checked' : '') + '></td>'
            + '<td class="text-center"><input class="form-check-input perm-check" type="checkbox" data-action="delete" ' + (Number(p.delete) === 1 ? 'checked' : '') + '></td>'
            + '</tr>';
    }).join('');
};

jQuery(document).ready(function () {
    // Mover modales al body evita conflictos de z-index y backdrop bloqueado
    ['usModal', 'usEditModal', 'usPermModal'].forEach(function (modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            document.body.appendChild(el);
            el.addEventListener('hidden.bs.modal', cleanupBackdrops);
        }
    });

    tablaUsuarios = jQuery('#tablaUsuarios').DataTable({
        autoWidth: false,
        lengthChange: false,
        dom: 'rt<"us-table-footer"ip>',
        pageLength: 10,
        order: [[0, 'desc']],
        drawCallback: function () {
            const api = this.api();
            jQuery('#usCount').text(api.page.info().recordsTotal);
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
                    if (type !== 'display') return row.username || '';
                    const ini = (row.username || '?').trim().substring(0, 2).toUpperCase();
                    return '<div class="us-user-wrap">'
                        + '<span class="pdsg-avatar">' + escapeHtml(ini) + '</span>'
                        + '<div>'
                        + '<div class="us-user-name">' + escapeHtml(row.username) + '</div>'
                        + '<div class="us-user-id">ID #' + Number(row.id_usuario) + '</div>'
                        + '</div>'
                        + '</div>';
                }
            },
            {
                data: null,
                className: 'us-person-cell',
                render: function (data, type, row) {
                    const full = [row.nombre, row.apellido_paterno, row.apellido_materno].filter(Boolean).join(' ');
                    if (type !== 'display') return full + ' ' + (row.dni || '');
                    const dni = row.dni ? 'DNI: ' + row.dni : '—';
                    return '<div class="us-person-name">' + escapeHtml(full || 'Sin nombre registrado') + '</div>'
                        + '<div class="us-person-dni">' + escapeHtml(dni) + '</div>';
                }
            },
            {
                data: null,
                className: 'us-contact-cell',
                render: function (data, type, row) {
                    if (type !== 'display') return row.correo || '';
                    return '<div class="us-contact-email">' + escapeHtml(row.correo ?? '—') + '</div>';
                }
            },
            {
                data: 'rol_nombre',
                className: 'us-rol-cell',
                render: function (data, type, row) {
                    const rol = escapeHtml(row.rol_nombre || 'Sin rol');
                    if (type !== 'display') return row.rol_nombre || '';
                    return '<span class="us-role-tag">' + rol + '</span>';
                }
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
                    const id = Number(row.id_usuario);
                    const est = Number(row.estado);
                    const isActive = est === 1;
                    const toggleClass = isActive ? 'pdsg-action-warning' : 'pdsg-action-success';
                    const toggleTitle = isActive ? 'Desactivar usuario' : 'Activar usuario';
                    const toggleIcon = isActive ? 'fa-user-slash' : 'fa-user-check';

                    return '<span class="pdsg-actions">'
                        + '<button type="button" class="pdsg-action pdsg-action-edit" onclick="openEditUsuario(' + id + ')" title="Editar usuario" aria-label="Editar usuario">'
                        + '<i class="fa-solid fa-pen" aria-hidden="true"></i>'
                        + '</button>'
                        + '<button type="button" class="pdsg-action pdsg-action-info" onclick="openPermisosUsuario(' + id + ')" title="Permisos del rol" aria-label="Permisos del rol">'
                        + '<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>'
                        + '</button>'
                        + '<button type="button" class="pdsg-action ' + toggleClass + '" onclick="toggleEstadoUsuario(' + id + ', ' + est + ')" title="' + toggleTitle + '" aria-label="' + toggleTitle + '">'
                        + '<i class="fa-solid ' + toggleIcon + '" aria-hidden="true"></i>'
                        + '</button>'
                        + '</span>';
                }
            }
        ],
        language: {
            processing: 'Procesando...',
            zeroRecords: '<div class="tp-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>Sin coincidencias</strong><span>Prueba con otro término.</span></div>',
            emptyTable: '<div class="tp-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>No hay usuarios registrados</strong><span>Usa “Nuevo Usuario” para crear el primero.</span></div>',
            info: '_START_–_END_ de _TOTAL_ usuarios',
            infoEmpty: '0 usuarios',
            infoFiltered: '(de _MAX_ en total)',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior'
            }
        }
    });

    // --- Buscador de la tabla ---
    jQuery('#usSearch').on('input', function () {
        tablaUsuarios.search(this.value).draw();
    });

    // --- Nuevo usuario modal ---
    jQuery('#usModal').on('show.bs.modal', function () {
        jQuery('#usForm')[0].reset();
        jQuery('#usId').val('');
        clearAlert('usFormError');
        jQuery('#usPassHelp').text('Mínimo 6 caracteres.').removeClass('text-danger');
        jQuery('#usPassword').prop('required', true);
    });

    // --- Guardar nuevo ---
    jQuery('#usForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usFormError');
        const btn = document.getElementById('usSave');
        btn.disabled = true;
        const formData = new FormData(this);
        try {
            const res = await fetch(BASE_URL + 'usuarios/registrar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await parseJsonResponse(res);
            syncCsrfToken(res, data);
            if (!res.ok || !data.success) {
                console.warn('Error usuarios/registrar:', res.status, data);
                setAlert('usFormError', 'danger', renderErrors(data.errors, data.message));
                return;
            }
            closeModal('usModal');
            setAlert('usNotice', 'success', escapeHtml(data.message || 'Usuario registrado correctamente.'));
            tablaUsuarios.ajax.reload(null, false);
        } catch (err) {
            setAlert('usFormError', 'danger', 'Error de conexión.');
        } finally {
            btn.disabled = false;
        }
    });

    // --- Editar usuario ---
    jQuery('#usEditForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usEditError');
        const btn = document.getElementById('usEditSave');
        btn.disabled = true;
        const id = jQuery('#usEditId').val();
        const formData = new FormData(this);
        try {
            const res = await fetch(BASE_URL + 'usuarios/actualizar/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await parseJsonResponse(res);
            syncCsrfToken(res, data);
            if (!res.ok || !data.success) {
                console.warn('Error usuarios/actualizar:', res.status, data);
                setAlert('usEditError', 'danger', renderErrors(data.errors, data.message));
                return;
            }
            closeModal('usEditModal');
            setAlert('usNotice', 'success', escapeHtml(data.message || 'Usuario actualizado correctamente.'));
            tablaUsuarios.ajax.reload(null, false);
        } catch (err) {
            setAlert('usEditError', 'danger', 'Error de conexión.');
        } finally {
            btn.disabled = false;
        }
    });

    // --- Permisos del rol ---
    jQuery('#usPermForm').on('submit', async function (e) {
        e.preventDefault();
        clearAlert('usPermError');
        const btn = document.getElementById('usPermSave');
        btn.disabled = true;
        const id = jQuery('#usPermIdUsuario').val();
        const payload = Array.from(document.querySelectorAll('#usPermBody tr[data-menu-id]')).map(function (row) {
            return {
                menu_id: Number(row.dataset.menuId),
                read: row.querySelector('[data-action="read"]').checked ? 1 : 0,
                insert: row.querySelector('[data-action="insert"]').checked ? 1 : 0,
                update: row.querySelector('[data-action="update"]').checked ? 1 : 0,
                delete: row.querySelector('[data-action="delete"]').checked ? 1 : 0
            };
        });
        try {
            const headers = {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            headers[CSRF_HEADER] = CSRF_HASH;
            headers[CSRF_TOKEN] = CSRF_HASH;

            const res = await fetch(BASE_URL + 'usuarios/permisos/' + id, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ permisos: payload })
            });
            const data = await parseJsonResponse(res);
            syncCsrfToken(res, data);
            if (!res.ok || !data.success) {
                console.warn('Error usuarios/permisos:', res.status, data);
                setAlert('usPermError', 'danger', escapeHtml(data.message || 'No se pudieron guardar.'));
                return;
            }
            closeModal('usPermModal');
            setAlert('usNotice', 'success', escapeHtml(data.message || 'Permisos actualizados correctamente.'));
        } catch (err) {
            setAlert('usPermError', 'danger', 'Error de conexión.');
        } finally {
            btn.disabled = false;
        }
    });

    // --- Acciones inline ---
    window.openEditUsuario = function (id) {
        closeModal('usModal');
        clearAlert('usEditError');
        fetch(BASE_URL + 'usuarios/obtener/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async function (res) {
                return { res: res, data: await parseJsonResponse(res) };
            })
            .then(function (result) {
                if (!result.res.ok || !result.data.success) {
                    setAlert('usEditError', 'danger', escapeHtml((result.data && result.data.message) || 'No se pudo cargar.'));
                    openModal('usEditModal');
                    return;
                }
                const u = result.data.data;
                jQuery('#usEditId').val(u.id_usuario);
                jQuery('#usEditPassword').val('');
                fillUserForm('usEdit', u);
                openModal('usEditModal');
            })
            .catch(function () {
                setAlert('usEditError', 'danger', 'Error de conexión.');
                openModal('usEditModal');
            });
    };

    window.openPermisosUsuario = function (id) {
        closeModal('usModal');
        closeModal('usEditModal');
        clearAlert('usPermError');
        fetch(BASE_URL + 'usuarios/obtener/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async function (res) {
                return { res: res, data: await parseJsonResponse(res) };
            })
            .then(function (result) {
                if (!result.res.ok || !result.data.success) {
                    setAlert('usPermError', 'danger', escapeHtml((result.data && result.data.message) || 'No se pudieron cargar.'));
                    openModal('usPermModal');
                    return;
                }
                const u = result.data.data;
                jQuery('#usPermIdUsuario').val(u.id_usuario);
                jQuery('#usPermUserLabel').text(u.username + ' · ' + (fullName(u) || 'Sin nombre'));
                jQuery('#usPermRolLabel').text(u.rol_nombre || 'Sin rol');
                renderPermsBody(u.permisos || []);
                openModal('usPermModal');
            })
            .catch(function () {
                setAlert('usPermError', 'danger', 'Error de conexión.');
                openModal('usPermModal');
            });
    };

    window.toggleEstadoUsuario = function (id, estado) {
        const accion = Number(estado) === 1 ? 'desactivar' : 'activar';
        if (!confirm('¿Quieres ' + accion + ' este usuario?')) return;
        const formData = new FormData();
        formData.append(CSRF_TOKEN, CSRF_HASH);
        const headers = { 'X-Requested-With': 'XMLHttpRequest' };
        headers[CSRF_HEADER] = CSRF_HASH;

        fetch(BASE_URL + 'usuarios/estado/' + id, {
            method: 'POST',
            headers: headers,
            body: formData
        })
            .then(async function (res) {
                return { res: res, data: await parseJsonResponse(res) };
            })
            .then(function (result) {
                syncCsrfToken(result.res, result.data);
                if (!result.res.ok || !result.data.success) {
                    setAlert('usNotice', 'danger', escapeHtml((result.data && result.data.message) || 'No se pudo actualizar.'));
                } else {
                    setAlert('usNotice', 'success', escapeHtml((result.data && result.data.message) || 'Estado actualizado correctamente.'));
                    tablaUsuarios.ajax.reload(null, false);
                }
            })
            .catch(function () {
                setAlert('usNotice', 'danger', 'Error de conexión.');
            });
    };

    // Animación de entrada
    if (typeof gsap !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        gsap.fromTo('.us-page > *', { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.35, stagger: 0.06, ease: 'power2.out', clearProps: 'opacity,transform' });
    }
});
</script>