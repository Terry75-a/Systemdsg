<?php
?>

<link rel="stylesheet" href="<?= base_url('css/dashboard/persona-editor.css?v=' . filemtime(FCPATH . 'css/dashboard/persona-editor.css')) ?>">
<div class="container-fluid px-2 pdsg-persona-page">

    <!-- Mensajes flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0 pdsg-add-wrap">
        <div class="card-body pdsg-add-body">

            <form action="<?= base_url('personas/guardar-todo') ?>" method="POST" id="formPersona">
                <?= csrf_field() ?>
                <!-- Contenedor para registrar los IDs de sucursales a eliminar en BD -->
<div id="sucursalesEliminadasContainer"></div>

                <input type="hidden" name="id_persona" value="<?= isset($persona) ? (is_array($persona) ? $persona['id_persona'] : $persona->id_persona) : '' ?>">

                <!-- Hero: título + CTA -->
                <div class="pdsg-add-head">
                    <span class="pdsg-add-avatar">
                        <i class="fa-solid <?= isset($persona) ? 'fa-user-pen' : 'fa-user-plus' ?>"></i>
                    </span>
                    <div class="pdsg-add-head-info">
                        <span class="pe-eyebrow">DIRECTORIO DE CLIENTES</span>
                        <h5 class="pdsg-add-title" id="pe-title">
                            <?= isset($persona) ? 'Editar Persona' : 'Registrar Nueva Persona' ?>
                        </h5>
                        <span class="pdsg-add-sub">Complete la información del cliente en un solo lugar.</span>
                    </div>
                    <a href="<?= base_url('personas') ?>" class="pdsg-add-close" title="Cancelar" aria-label="Cerrar formulario">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </div>

                <div class="pe-workspace">
                    <aside class="pe-sidebar" aria-label="Secciones del formulario">
                        <span class="pe-nav-label">INFORMACIÓN DEL CLIENTE</span>
                        <nav class="pe-nav">
                            <a href="#pe-personal" class="pe-nav-link" aria-current="location"><span class="pe-nav-number">01</span><span>Datos personales<small>Identidad y contacto</small></span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></a>
                            <a href="#pe-business" class="pe-nav-link"><span class="pe-nav-number">02</span><span>Empresa<small>Información opcional</small></span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></a>
                        </nav>
                        <div class="pe-sidebar-note"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><p>¿Por dónde empezar?<span>Ingrese el DNI y use la búsqueda para completar los datos disponibles.</span></p></div>
                    </aside>
                    <div class="pe-scroll" tabindex="0" aria-label="Datos del cliente">
                <!-- ══════════════════════════════════════════════ -->
                <!-- SECCIÓN: DATOS PERSONALES                      -->
                <!-- ══════════════════════════════════════════════ -->
                <section class="pdsg-modal-card pdsg-add-card" id="pe-personal" aria-labelledby="pe-personal-title">
                    <div class="pe-section-heading"><span class="pe-section-icon"><i class="fa-regular fa-user" aria-hidden="true"></i></span><div><span class="pe-eyebrow">01 / PERSONA</span><h2 class="pdsg-modal-group-title" id="pe-personal-title">Datos personales</h2></div></div>
                    <p class="pdsg-section-help">Identificación, contacto y domicilio del cliente.</p>

                    <div class="row g-3 pe-field-grid">
                        <div class="pe-lookup">
                            <label for="dni" class="pdsg-field-label">DNI <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text"
                                       id="dni"
                                       name="dni"
                                       class="form-control pdsg-input"
                                       maxlength="8"
                                       placeholder="Ej: 12345678"
                                       value="<?= old('dni', isset($persona) ? (is_array($persona) ? $persona['dni'] : $persona->dni) : '') ?>"
                                       required>
                                <button type="button"
                                        class="btn pdsg-btn-search"
                                        id="btnBuscarDni"
                                        title="Buscar por DNI" aria-label="Buscar por DNI">
                                    <span id="spinnerDni" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    <i class="fa-solid fa-magnifying-glass" id="iconoDni" aria-hidden="true"></i><span>Buscar</span>
                                </button>
                            </div>
                            <div id="alertaDni" class="mt-1" aria-live="polite"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="nombre" class="pdsg-field-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="nombre"
                                   name="nombre"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('nombre', isset($persona) ? (is_array($persona) ? $persona['nombre'] : $persona->nombre) : '') ?>"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="apellido_paterno" class="pdsg-field-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="apellido_paterno"
                                   name="apellido_paterno"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('apellido_paterno', isset($persona) ? (is_array($persona) ? $persona['apellido_paterno'] : $persona->apellido_paterno) : '') ?>"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="apellido_materno" class="pdsg-field-label">Apellido Materno</label>
                            <input type="text"
                                   id="apellido_materno"
                                   name="apellido_materno"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('apellido_materno', isset($persona) ? (is_array($persona) ? $persona['apellido_materno'] : $persona->apellido_materno) : '') ?>">
                        </div>

                        <div class="pe-field-divider">Contacto</div>
                        <div class="col-md-4">
                            <label for="telefono" class="pdsg-field-label">Teléfono</label>
                            <input type="text"
                                   id="telefono"
                                   name="telefono"
                                   class="form-control pdsg-input"
                                   value="<?= old('telefono', isset($persona) ? (is_array($persona) ? $persona['telefono'] : $persona->telefono) : '') ?>"
                                   maxlength="9"
                                   placeholder="Ej: 999888777">
                        </div>

                        <div class="pe-wide">
                            <label for="correo" class="pdsg-field-label">Correo</label>
                            <input type="email"
                                   id="correo"
                                   name="correo"
                                   class="form-control pdsg-input"
                                   value="<?= old('correo', isset($persona) ? (is_array($persona) ? $persona['correo'] : $persona->correo) : '') ?>"
                                   placeholder="correo@ejemplo.com">
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="pdsg-field-label">Fecha de Nacimiento</label>
                            <input type="date"
                                   id="fecha_nacimiento"
                                   value="<?= old('fecha_nacimiento', isset($persona) ? (is_array($persona) ? $persona['fecha_nacimiento'] : $persona->fecha_nacimiento) : '') ?>"
                                   name="fecha_nacimiento"
                                   class="form-control pdsg-input">
                        </div>

                        <div class="pe-field-divider">Domicilio personal</div>
                        <div class="pe-full">
                            <label for="direccion_rep" class="pdsg-field-label">Dirección Personal</label>
                            <input type="text"
                                   id="direccion_rep"
                                   name="direccion_rep"
                                   value="<?= old('direccion_rep', isset($persona) ? (is_array($persona) ? $persona['direccion'] : $persona->direccion) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Av. / Jr. / Calle...">
                        </div>

                        <div class="pe-location">
                            <label for="departamento_rep" class="pdsg-field-label">Departamento</label>
                            <select id="departamento_rep" name="departamento_rep" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach(isset($departamentos) ? $departamentos : [] as $dep): ?>
                                    <option value="<?= $dep->id ?>"
                                        <?= (isset($persona) && (is_array($persona) ? $persona['id_departamento'] : $persona->id_departamento) == $dep->id) ? 'selected' : '' ?>
                                        <?= (old('departamento_rep') == $dep->id) ? 'selected' : '' ?>>
                                        <?= esc($dep->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="pe-location">
                            <label for="provincia_rep" class="pdsg-field-label">Provincia</label>
                            <select id="provincia_rep" name="provincia_rep" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        <div class="pe-location">
                            <label for="distrito_rep" class="pdsg-field-label">Distrito</label>
                            <select id="distrito_rep" name="distrito_rep" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- ══════════════════════════════════════════════ -->
                <!-- SECCIÓN: DATOS EMPRESARIALES                   -->
                <!-- ══════════════════════════════════════════════ -->
                <section class="pdsg-modal-card pdsg-add-card" id="pe-business" aria-labelledby="pe-business-title">
                    <div class="pe-section-heading"><span class="pe-section-icon"><i class="fa-regular fa-building" aria-hidden="true"></i></span><div><span class="pe-eyebrow">02 / EMPRESA</span><h2 class="pdsg-modal-group-title" id="pe-business-title">Datos empresariales <small class="pdsg-opcional">Opcional</small></h2></div></div>
                    <p class="pdsg-section-help">Complete esta sección si el cliente tiene una empresa.</p>

                    <div class="row g-3 pe-field-grid">
                        <div class="pe-lookup">
                            <label for="ruc" class="pdsg-field-label">RUC</label>
                            <div class="input-group">
                                <input type="text"
                                       id="ruc"
                                       name="ruc"
                                       class="form-control pdsg-input"
                                       value="<?= old('ruc', isset($empresa) ? (is_array($empresa) ? $empresa['ruc'] : $empresa->ruc) : '') ?>"
                                       maxlength="11"
                                       placeholder="Ej: 20123456789">
                                <button type="button"
                                        class="btn pdsg-btn-search"
                                        id="btnBuscarRuc"
                                        title="Buscar por RUC" aria-label="Buscar por RUC">
                                    <span id="spinnerRuc" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    <i class="fa-solid fa-magnifying-glass" id="iconoRuc" aria-hidden="true"></i><span>Buscar</span>
                                </button>
                            </div>
                            <div id="alertaRuc" class="mt-1" aria-live="polite"></div>
                        </div>

                        <div class="pe-wide">
                            <label for="razon_social" class="pdsg-field-label">Razón Social</label>
                            <input type="text"
                                   id="razon_social"
                                   name="razon_social"
                                   value="<?= old('razon_social', isset($empresa) ? (is_array($empresa) ? $empresa['razon_social'] : $empresa->razon_social) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente">
                        </div>

                        <div class="pe-full">
                            <label for="correo_empresa" class="pdsg-field-label">Correo Empresarial</label>
                            <input type="email"
                                   id="correo_empresa"
                                   name="correo_empresa"
                                   value="<?= old('correo_empresa', isset($empresa) ? (is_array($empresa) ? $empresa['correo'] : $empresa->correo) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="empresa@correo.com">
                        </div>

                        <div class="pe-field-divider">Domicilio fiscal</div>
                        <div class="pe-full">
                            <label for="direccion" class="pdsg-field-label">Dirección Fiscal</label>
                            <input type="text"
                                   id="direccion"
                                   name="direccion"
                                   value="<?= old('direccion', isset($empresa) ? (is_array($empresa) ? $empresa['direccion'] : $empresa->direccion) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente">
                        </div>

                        <div class="pe-location">
                            <label for="departamento_repdos" class="pdsg-field-label">Departamento</label>
                            <select id="departamento_repdos" name="departamento_repdos" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach (isset($departamentos) ? $departamentos : [] as $dep): ?>
                                    <option value="<?= $dep->id ?>"
                                        <?= (isset($empresa) && (is_array($empresa) ? $empresa['id_departamento'] : $empresa->id_departamento) == $dep->id) ? 'selected' : '' ?>
                                        <?= (old('departamento_repdos') == $dep->id) ? 'selected' : '' ?>>
                                        <?= esc($dep->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="pe-location">
                            <label for="provincia_repdos" class="pdsg-field-label">Provincia</label>
                            <select id="provincia_repdos" name="provincia_repdos" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        <div class="pe-location">
                            <label for="distrito_repdos" class="pdsg-field-label">Distrito</label>
                            <select id="distrito_repdos" name="distrito_repdos" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- ══════════════════════════════════════════════ -->
                <!-- SECCIÓN: TABLA DE SUCURSALES                   -->
                <!-- ══════════════════════════════════════════════ -->
                <div id="contenedorSucursales" class="pdsg-add-card pdsg-modal-card" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <span class="pdsg-modal-group-title mb-0">
                            <i class="fa-solid fa-shop"></i> Sucursales
                        </span>
                        <button type="button" id="btnAbrirModalNuevaSucursal" class="pdsg-add-btn">
                            <i class="fa-solid fa-plus"></i> Agregar Sucursal
                        </button>
                    </div>

                    <div class="pdsg-suc-list" id="tbodySucursalesWrap">
                        <table class="table pdsg-suc-table mb-0" id="tablaSucursales">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySucursales">
                                <?php if (!empty($sucursales)): ?>
                                    <?php foreach ($sucursales as $suc): ?>
                                        <?php 
                                            $get = function ($key, $default = null) use ($suc) {
                                                if (is_array($suc)) {
                                                    return $suc[$key] ?? $default;
                                                }
                                                return $suc->{$key} ?? $default;
                                            };
                                            $idSuc  = $get('id_sucursal');
                                            $codCli = $get('codigo_cliente');
                                            $nomSuc = $get('nombre_sucursal');
                                            $repSuc = $get('representante') ?? $get('tipo') ?? 'sucursal';
                                            $depSuc = $get('id_departamento');
                                            $provSuc = $get('id_provincia');
                                            $distSuc = $get('id_distrito');
                                            $dirSuc = $get('direccion');
                                            $telSuc = $get('telefono');
                                            $corSuc = $get('correo');
                                            $estSuc = $get('estado', 1);
                                            $indice = 'existing_' . $idSuc; 
                                        ?>
                                        <tr data-indice="<?= $indice; ?>">
                                            <td>
                                                <span class="pdsg-suc-name txt-nombre"><i class="fa-solid fa-shop"></i> <?= esc($nomSuc); ?></span>
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][codigo_cliente_sucursal]" value="<?= esc($codCli); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][nombre_sucursal]" value="<?= esc($nomSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][representante_sucursal]" value="<?= esc($repSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][departamento_sucursal]" value="<?= esc($depSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][provincia_sucursal]" value="<?= esc($provSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][distrito_sucursal]" value="<?= esc($distSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][direccion]" value="<?= esc($dirSuc); ?>">
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][correo]" value="<?= esc($corSuc); ?>">
                                            </td>
                                            <td>
                                                <span class="txt-telefono"><?= esc($telSuc); ?></span>
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][telefono]" value="<?= esc($telSuc); ?>">
                                            </td>
                                            <td class="text-center">
                                                <span class="pdsg-chip <?= $estSuc == 1 ? 'pdsg-chip-on' : 'pdsg-chip-off' ?>">
                                                    <i class="fa-solid fa-circle"></i> <?= $estSuc == 1 ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                                <input type="hidden" name="sucursales[<?= $indice; ?>][estado]" value="<?= $estSuc; ?>">
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-edit btn-editar-sucursal" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-del btn-eliminar-sucursal" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-more btn-asignar-plan"
                                                    data-id-persona="<?= esc($idSuc); ?>"
                                                    data-nombre-persona="<?= esc($nomSuc); ?>"
                                                    title="Asignar Plan">
                                                    <i class="fa-solid fa-inbox"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr id="filaVacia">
                                        <td colspan="4" class="text-center text-muted py-3">No hay sucursales registradas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                    </div><!-- .pe-scroll -->
                </div><!-- .pe-workspace -->
                <!-- BOTONES PRINCIPALES -->
                <div class="d-flex gap-2 pdsg-add-actions">
                    <span class="pe-required-note"><span>*</span> Campos obligatorios</span>
                    <button type="submit" class="btn pdsg-btn-save px-4" id="btnGuardar">
                        <?php if (isset($persona)): ?>
                            <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Cambios
                        <?php else: ?>
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Persona
                        <?php endif; ?>
                    </button>
                    <a href="<?= base_url('personas') ?>" class="btn pdsg-btn-cancel px-4" data-cancelar-modal>
                        <i class="fa-solid fa-xmark me-1"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════ -->
<!-- MODAL ÚNICO SUCURSAL (CREAR / EDITAR)               -->
<!-- ════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalSucursal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="pdsg-modal-bar">
                <span class="pdsg-modal-avatar"><i class="fa-solid fa-shop" aria-hidden="true"></i></span>
                <div class="pdsg-modal-head-info">
                    <h5 class="pdsg-modal-name" id="modalSucursalTitulo">Registrar Sucursal</h5>
                    <span class="pdsg-modal-sub">Datos de la sucursal</span>
                </div>
                <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <div class="pdsg-modal-scroll">
                <div class="pdsg-modal-body">
                    <form id="formSucursal">
                        <input type="hidden" id="editandoIndice" value="">

                        <div class="pdsg-modal-group">
                            <span class="pdsg-modal-group-title">Datos de la sucursal</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre_sucursal">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" placeholder="Ej: Sucursal Lima Centro" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="codigo_cliente_sucursal">Código Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="codigo_cliente_sucursal" name="codigo_cliente_sucursal" placeholder="Ej: 0000070" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="representante_sucursal">Representante <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="representante_sucursal" name="representante_sucursal" placeholder="Ej: Juan Pérez" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono_sucursal">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono_sucursal" name="telefono_sucursal" maxlength="9" placeholder="Ej: 987654321">
                                </div>

                                <div class="col-md-6">
                                    <label for="correo_sucursal">Correo</label>
                                    <input type="email" class="form-control" id="correo_sucursal" name="correo_sucursal" placeholder="Ej: sucursal@correo.com">
                                </div>

                                <div class="col-md-6">
                                    <label for="direccion_sucursal">Dirección</label>
                                    <input type="text" class="form-control" id="direccion_sucursal" name="direccion_sucursal" placeholder="Ej: Av. Arequipa 1234">
                                </div>

                                <div class="col-md-4">
                                    <label for="departamento_sucursal">Departamento</label>
                                    <select class="form-select" id="departamento_sucursal" name="departamento_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach (isset($departamentos) ? $departamentos : [] as $dep): ?>
                                            <option value="<?= $dep->id ?>"><?= esc($dep->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="provincia_sucursal">Provincia</label>
                                    <select class="form-select" id="provincia_sucursal" name="provincia_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="distrito_sucursal">Distrito</label>
                                    <select class="form-select" id="distrito_sucursal" name="distrito_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="pdsg-modal-group">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="estado_label">Estado</label>
                                    <select class="form-select" id="estado_label" name="estado_label">
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pe-ms-footer">
                <button type="button" class="btn pe-ms-cancel" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
                </button>
                <button type="button" id="btnGuardarSucursalLista" class="btn pe-ms-save">
                    <i class="fa-solid fa-check" aria-hidden="true"></i> <span id="btnGuardarSucursalTexto">Agregar a la Lista</span>
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">
      <div class="pe-ms-head">
        <span class="pe-ms-icon"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
        <div class="pe-ms-head-info">
          <h2 id="modalPlanLabel">Asignar Plan de Servicio</h2>
          <p>Selecciona el plan y condiciones para este cliente.</p>
        </div>
        <button type="button" class="pe-ms-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
      </div>
      <div class="pe-ms-body-wrap">
        <div class="pe-ms-body">
          <div class="alert mb-3" role="alert">
            Asignando plan a: <strong id="modalClienteNombre"></strong>
            <input type="hidden" id="idPersonaHidden" name="id_persona">
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="selectPlan">Seleccionar Plan disponible:</label>
              <select class="form-select" id="selectPlan"></select>
            </div>

            <div class="col-md-6">
              <label for="selectTipoPlan">Tipo de plan:</label>
              <select class="form-select" id="selectTipoPlan"></select>
            </div>

            <div class="col-md-6">
              <label for="fechaInicio">Fecha de Inicio: <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="fechaInicio" value="2026-06-19" required>
            </div>

            <div class="col-md-6">
              <label for="fechaVencimiento">Fecha de Vencimiento:</label>
              <input type="date" class="form-control" id="fechaVencimiento" readonly placeholder="Cálculo automático">
            </div>

            <div class="col-md-6">
              <label for="inputPrecio">Precio Cobrado (S/):</label>
              <input type="number" step="0.01" class="form-control" id="inputPrecio" placeholder="0.00">
            </div>

            <div class="col-md-6">
              <label for="selectEstadoPago">Estado del Pago: <span class="text-danger">*</span></label>
              <select class="form-select" id="selectEstadoPago" required>
                <option value="Pagado" selected>Pagado</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Cortesia">Cortesía / Demo</option>
              </select>
            </div>

            <div class="col-12">
              <label for="txtObservaciones">Observaciones (Opcional):</label>
              <textarea class="form-control" id="txtObservaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="pe-ms-footer">
        <button type="button" class="btn pe-ms-cancel" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </button>
        <button type="submit" class="btn pe-ms-save" id="btnGuardarPlan">
          <i class="fa-solid fa-check" aria-hidden="true"></i> Asignar Plan
        </button>
      </div>
    </div>
  </div>
</div>
<style>
.body-wrapper {
    padding-top: 20px !important;
}
</style>

<script src="<?= base_url('js/persona-editor.js?v=' . filemtime(FCPATH . 'js/persona-editor.js')) ?>"></script>

<script>
// ── CONFIGURACIÓN DE URL Y TOKEN CSRF
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';
const BASE_URL = '<?= rtrim(base_url(), '/') . '/' ?>';

const PROVINCIA_EDITAR  = "<?= isset($persona) ? (is_array($persona) ? $persona['id_provincia'] : $persona->id_provincia) : '' ?>";
const DISTRITO_EDITAR   = "<?= isset($persona) ? (is_array($persona) ? $persona['id_distrito'] : $persona->id_distrito) : '' ?>";
const PROVINCIA_EDITARd = "<?= isset($empresa) ? (is_array($empresa) ? $empresa['id_provincia'] : $empresa->id_provincia) : '' ?>";
const DISTRITO_EDITARX  = "<?= isset($empresa) ? (is_array($empresa) ? $empresa['id_distrito'] : $empresa->id_distrito) : '' ?>";

// Obtener o inicializar Modal de Bootstrap con seguridad
function obtenerModalSucursal() {
    const el = document.getElementById('modalSucursal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(el);
    }
    return null;
}

function obtenerModalPlan() {
    const el = document.getElementById('modalPlan');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(el);
    }
    return null;
}

// ════════════════════════════════════════════════════
// BÚSQUEDA POR DNI
// ════════════════════════════════════════════════════
document.getElementById('btnBuscarDni').addEventListener('click', async function () {
    const dni = document.getElementById('dni').value.trim();
    const alerta = document.getElementById('alertaDni');
    alerta.innerHTML = '';

    if (!/^\d{8}$/.test(dni)) {
        alerta.innerHTML = `<small class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i> El DNI debe tener 8 dígitos.</small>`;
        return;
    }

    document.getElementById('spinnerDni').classList.remove('d-none');
    document.getElementById('iconoDni').classList.add('d-none');
    this.disabled = true;

    try {
        const formData = new FormData();
        formData.append('dni', dni);
        formData.append(csrfName, csrfHash);

        const response = await fetch(`${BASE_URL}personas/buscar-dni`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            document.getElementById('nombre').value           = data.nombres ?? '';
            document.getElementById('apellido_paterno').value = data.apellido_paterno ?? '';
            document.getElementById('apellido_materno').value = data.apellido_materno ?? '';
            alerta.innerHTML = `<small class="text-success"><i class="fa-solid fa-circle-check"></i> Datos encontrados.</small>`;
            document.getElementById('telefono').focus();
        } else {
            document.getElementById('nombre').value = '';
            document.getElementById('apellido_paterno').value = '';
            document.getElementById('apellido_materno').value = '';
            alerta.innerHTML = `<small class="text-danger"><i class="fa-solid fa-circle-xmark"></i> ${data.message ?? 'DNI no encontrado.'}</small>`;
        }
    } catch (error) {
        console.error('Error DNI:', error);
        alerta.innerHTML = `<small class="text-danger">Error de conexión.</small>`;
    } finally {
        document.getElementById('spinnerDni').classList.add('d-none');
        document.getElementById('iconoDni').classList.remove('d-none');
        this.disabled = false;
    }
});

document.getElementById('dni').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('btnBuscarDni').click();
    }
});

// ════════════════════════════════════════════════════
// BÚSQUEDA POR RUC
// ════════════════════════════════════════════════════
document.getElementById('btnBuscarRuc').addEventListener('click', async function () {
    const ruc = document.getElementById('ruc').value.trim();
    const alerta = document.getElementById('alertaRuc');
    const contenedorSucursales = document.getElementById('contenedorSucursales');
    alerta.innerHTML = '';

    if (!/^\d{11}$/.test(ruc)) {
        alerta.innerHTML = `<small class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i> El RUC debe tener 11 dígitos.</small>`;
        contenedorSucursales.style.display = 'none';
        return;
    }

    document.getElementById('spinnerRuc').classList.remove('d-none');
    document.getElementById('iconoRuc').classList.add('d-none');
    this.disabled = true;

    try {
        const formData = new FormData();
        formData.append('ruc', ruc);
        formData.append(csrfName, csrfHash);

        const response = await fetch(`${BASE_URL}personas/buscar-ruc`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            document.getElementById('razon_social').value = data.razon ?? '';
            document.getElementById('direccion').value    = data.direccion ?? '';
            alerta.innerHTML = `<small class="text-success"><i class="fa-solid fa-circle-check"></i> Empresa encontrada.</small>`;
            contenedorSucursales.style.display = 'block';
        } else {
            document.getElementById('razon_social').value = '';
            document.getElementById('direccion').value    = '';
            alerta.innerHTML = `<small class="text-danger"><i class="fa-solid fa-circle-xmark"></i> ${data.message ?? 'RUC no encontrado.'}</small>`;
            contenedorSucursales.style.display = 'none';
        }
    } catch (error) {
        console.error('Error RUC:', error);
        alerta.innerHTML = `<small class="text-danger">Error de conexión.</small>`;
    } finally {
        document.getElementById('spinnerRuc').classList.add('d-none');
        document.getElementById('iconoRuc').classList.remove('d-none');
        this.disabled = false;
    }
});

document.getElementById('ruc').addEventListener('input', function () {
    const rucVal = this.value.trim();
    const contenedor = document.getElementById('contenedorSucursales');
    if (/^\d{11}$/.test(rucVal)) {
        contenedor.style.display = 'block';
    } else {
        contenedor.style.display = 'none';
    }
});

// ════════════════════════════════════════════════════
// UBIGEOS: PERSONA Y EMPRESA
// ════════════════════════════════════════════════════
async function cargarProvincias(idDep, idProvSelected = null) {
    const selectProv = document.getElementById('provincia_rep');
    const selectDist = document.getElementById('distrito_rep');
    selectProv.innerHTML = '<option value="">-- Seleccionar --</option>';
    selectDist.innerHTML = '<option value="">-- Seleccionar --</option>';
    if (!idDep) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/provincias/${idDep}`);
        const data = await res.json();
        data.forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov.id;
            opt.textContent = prov.name;
            if (idProvSelected && prov.id == idProvSelected) opt.selected = true;
            selectProv.appendChild(opt);
        });
        if (selectProv.value) cargarDistritos(selectProv.value, DISTRITO_EDITAR);
    } catch (e) { console.error(e); }
}

async function cargarDistritos(idProv, idDistSelected = null) {
    const selectDist = document.getElementById('distrito_rep');
    selectDist.innerHTML = '<option value="">-- Seleccionar --</option>';
    if (!idProv) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/distritos/${idProv}`);
        const data = await res.json();
        data.forEach(dist => {
            const opt = document.createElement('option');
            opt.value = dist.id;
            opt.textContent = dist.name;
            if (idDistSelected && dist.id == idDistSelected) opt.selected = true;
            selectDist.appendChild(opt);
        });
    } catch (e) { console.error(e); }
}

document.getElementById('departamento_rep').addEventListener('change', function() {
    cargarProvincias(this.value);
});
document.getElementById('provincia_rep').addEventListener('change', function() {
    cargarDistritos(this.value);
});

async function cargarProvinciasEmpresa(idDep, idProvSelected = null) {
    const selectProv = document.getElementById('provincia_repdos');
    const selectDist = document.getElementById('distrito_repdos');
    selectProv.innerHTML = '<option value="">-- Seleccionar --</option>';
    selectDist.innerHTML = '<option value="">-- Seleccionar --</option>';
    if (!idDep) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/provincias/${idDep}`);
        const data = await res.json();
        data.forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov.id;
            opt.textContent = prov.name;
            if (idProvSelected && prov.id == idProvSelected) opt.selected = true;
            selectProv.appendChild(opt);
        });
        if (selectProv.value) cargarDistritosEmpresa(selectProv.value, DISTRITO_EDITARX);
    } catch (e) { console.error(e); }
}

async function cargarDistritosEmpresa(idProv, idDistSelected = null) {
    const selectDist = document.getElementById('distrito_repdos');
    selectDist.innerHTML = '<option value="">-- Seleccionar --</option>';
    if (!idProv) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/distritos/${idProv}`);
        const data = await res.json();
        data.forEach(dist => {
            const opt = document.createElement('option');
            opt.value = dist.id;
            opt.textContent = dist.name;
            if (idDistSelected && dist.id == idDistSelected) opt.selected = true;
            selectDist.appendChild(opt);
        });
    } catch (e) { console.error(e); }
}

document.getElementById('departamento_repdos').addEventListener('change', function () {
    cargarProvinciasEmpresa(this.value);
});
document.getElementById('provincia_repdos').addEventListener('change', function () {
    cargarDistritosEmpresa(this.value);
});

// ════════════════════════════════════════════════════
// UBIGEO: SUCURSAL
// ════════════════════════════════════════════════════
async function cargarProvinciasSucursal(idDep, idProvSelected = null, idDistSelected = null) {
    const selectProv = document.getElementById('provincia_sucursal');
    const selectDist = document.getElementById('distrito_sucursal');
    selectProv.innerHTML = '<option value="">--Seleccionar--</option>';
    selectDist.innerHTML = '<option value="">--Seleccionar--</option>';
    if (!idDep) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/provincias/${idDep}`);
        const data = await res.json();
        data.forEach(prov => {
            const opt = document.createElement('option');
            opt.value = prov.id;
            opt.textContent = prov.name;
            if (idProvSelected && String(prov.id) === String(idProvSelected)) opt.selected = true;
            selectProv.appendChild(opt);
        });
        if (selectProv.value) {
            await cargarDistritosSucursal(selectProv.value, idDistSelected);
        }
    } catch (e) { console.error('Error provincias sucursal:', e); }
}

async function cargarDistritosSucursal(idProv, idDistSelected = null) {
    const selectDist = document.getElementById('distrito_sucursal');
    selectDist.innerHTML = '<option value="">--Seleccionar--</option>';
    if (!idProv) return;

    try {
        const res = await fetch(`${BASE_URL}ubigeo/distritos/${idProv}`);
        const data = await res.json();
        data.forEach(dist => {
            const opt = document.createElement('option');
            opt.value = dist.id;
            opt.textContent = dist.name;
            if (idDistSelected && String(dist.id) === String(idDistSelected)) opt.selected = true;
            selectDist.appendChild(opt);
        });
    } catch (e) { console.error('Error distritos sucursal:', e); }
}

document.getElementById('departamento_sucursal').addEventListener('change', function () {
    cargarProvinciasSucursal(this.value);
});
document.getElementById('provincia_sucursal').addEventListener('change', function () {
    cargarDistritosSucursal(this.value);
});

// ════════════════════════════════════════════════════
// GESTIÓN DEL MODAL: CREAR, EDITAR, GUARDAR Y ELIMINAR
// ════════════════════════════════════════════════════

// 1. Abrir Modal en modo CREAR
document.getElementById('btnAbrirModalNuevaSucursal').addEventListener('click', function () {
    document.getElementById('formSucursal').reset();
    document.getElementById('editandoIndice').value = '';
    document.getElementById('modalSucursalTitulo').textContent = 'Registrar Sucursal';
    document.getElementById('btnGuardarSucursalTexto').textContent = 'Agregar a la Lista';
    document.getElementById('provincia_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
    document.getElementById('distrito_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';

    const modal = obtenerModalSucursal();
    if (modal) modal.show();
});


// 2. Abrir Modal en modo EDITAR
document.getElementById('tablaSucursales').addEventListener('click', async function (e) {
    const btnEditar = e.target.closest('.btn-editar-sucursal');
    if (!btnEditar) return;

    const fila = btnEditar.closest('tr');
    const indice = fila.getAttribute('data-indice');

    const getVal = (campo) => fila.querySelector(`input[name="sucursales[${indice}][${campo}]"]`)?.value || '';

    document.getElementById('modalSucursalTitulo').textContent = 'Editar Sucursal';
    document.getElementById('btnGuardarSucursalTexto').textContent = 'Guardar Cambios';
    document.getElementById('editandoIndice').value = indice;

    document.getElementById('nombre_sucursal').value         = getVal('nombre_sucursal');
    document.getElementById('codigo_cliente_sucursal').value = getVal('codigo_cliente_sucursal');
    document.getElementById('representante_sucursal').value  = getVal('representante_sucursal') || getVal('representante');
    document.getElementById('direccion_sucursal').value      = getVal('direccion');
    document.getElementById('telefono_sucursal').value       = getVal('telefono');
    document.getElementById('correo_sucursal').value         = getVal('correo');
    document.getElementById('estado_label').value            = getVal('estado') || '1';

    const depId  = getVal('departamento_sucursal');
    const provId = getVal('provincia_sucursal');
    const distId = getVal('distrito_sucursal');

    document.getElementById('departamento_sucursal').value = depId;
    if (depId) {
        await cargarProvinciasSucursal(depId, provId, distId);
    }

    const modal = obtenerModalSucursal();
    if (modal) modal.show();
});

// 3. Guardar / Modificar en la lista
document.getElementById('btnGuardarSucursalLista').addEventListener('click', function () {
    const nombre        = document.getElementById('nombre_sucursal').value.trim();
    const codCliente    = document.getElementById('codigo_cliente_sucursal').value.trim();
    const representante = document.getElementById('representante_sucursal').value.trim();
    const direccion     = document.getElementById('direccion_sucursal').value.trim();
    const telefono      = document.getElementById('telefono_sucursal').value.trim();
    const correo        = document.getElementById('correo_sucursal').value.trim();
    const dep           = document.getElementById('departamento_sucursal').value;
    const prov          = document.getElementById('provincia_sucursal').value;
    const dist          = document.getElementById('distrito_sucursal').value;
    const estadoVal     = document.getElementById('estado_label').value;
    const estadoTexto   = document.getElementById('estado_label').options[document.getElementById('estado_label').selectedIndex].text;

    if (!nombre || !codCliente || !representante) {
        alert('Por favor complete los campos obligatorios (*).');
        return;
    }

    const editandoIndice = document.getElementById('editandoIndice').value;
    const indice = editandoIndice || ('temp_' + Date.now());

    const contenidoFila = `
        <td>
            <span class="pdsg-suc-name txt-nombre"><i class="fa-solid fa-shop"></i> ${nombre}</span>
            <input type="hidden" name="sucursales[${indice}][codigo_cliente_sucursal]" value="${codCliente}">
            <input type="hidden" name="sucursales[${indice}][nombre_sucursal]" value="${nombre}">
            <input type="hidden" name="sucursales[${indice}][representante_sucursal]" value="${representante}">
            <input type="hidden" name="sucursales[${indice}][departamento_sucursal]" value="${dep}">
            <input type="hidden" name="sucursales[${indice}][provincia_sucursal]" value="${prov}">
            <input type="hidden" name="sucursales[${indice}][distrito_sucursal]" value="${dist}">
            <input type="hidden" name="sucursales[${indice}][direccion]" value="${direccion}">
            <input type="hidden" name="sucursales[${indice}][correo]" value="${correo}">
        </td>
        <td>
            <span class="txt-telefono">${telefono}</span>
            <input type="hidden" name="sucursales[${indice}][telefono]" value="${telefono}">
            <input type="hidden" name="sucursales[${indice}][estado]" value="${estadoVal}">
        </td>
        <td class="text-center">
            <span class="pdsg-chip ${estadoVal === '1' ? 'pdsg-chip-on' : 'pdsg-chip-off'}">
                <i class="fa-solid fa-circle"></i> ${estadoTexto}
            </span>
        </td>
        <td class="text-end">
            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-edit btn-editar-sucursal" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-del btn-eliminar-sucursal" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
            </button>
            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-more btn-asignar-plan"
                data-id-persona="${indice}"
                data-nombre-persona="${nombre}"
                title="Asignar Plan">
                <i class="fa-solid fa-inbox"></i>
            </button>
        </td>
    `;

    const tbody = document.getElementById('tbodySucursales');
    const filaVacia = document.getElementById('filaVacia');
    if (filaVacia) filaVacia.remove();

    if (editandoIndice) {
        const filaExistente = tbody.querySelector(`tr[data-indice="${editandoIndice}"]`);
        if (filaExistente) filaExistente.innerHTML = contenidoFila;
    } else {
        const nuevaFila = document.createElement('tr');
        nuevaFila.setAttribute('data-indice', indice);
        nuevaFila.innerHTML = contenidoFila;
        tbody.appendChild(nuevaFila);
    }

    const modal = obtenerModalSucursal();
    if (modal) modal.hide();
});

document
// 4. Eliminar Fila y registrar ID si ya existía en la Base de Datos
document.getElementById('tbodySucursales').addEventListener('click', function (e) {
    const btnEliminar = e.target.closest('.btn-eliminar-sucursal');
    if (!btnEliminar) return;

    const fila = btnEliminar.closest('tr');
    const indice = fila.getAttribute('data-indice');

    // Si el índice empieza con 'existing_', significa que ya existe en la BD
    if (indice && indice.startsWith('existing_')) {
        const idSucursal = indice.replace('existing_', '');
        
        const inputEliminado = document.createElement('input');
        inputEliminado.type = 'hidden';
        inputEliminado.name = 'sucursales_eliminadas[]';
        inputEliminado.value = idSucursal;
        
        document.getElementById('sucursalesEliminadasContainer').appendChild(inputEliminado);
    }

    // Remover la fila de la vista
    fila.remove();

    // Mostrar mensaje si la tabla queda vacía
    const tbody = document.getElementById('tbodySucursales');
    if (tbody.querySelectorAll('tr').length === 0) {
        tbody.innerHTML = `
            <tr id="filaVacia">
                <td colspan="4" class="text-center text-muted py-3">No hay sucursales registradas.</td>
            </tr>
        `;
    }
});

// 5. Limpiar formulario al cerrar
document.getElementById('modalSucursal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('editandoIndice').value = '';
    document.getElementById('formSucursal').reset();
    document.getElementById('provincia_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
    document.getElementById('distrito_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
});

// ════════════════════════════════════════════════════
// INICIALIZACIÓN
// ════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    const depPersona = document.getElementById('departamento_rep');
    if (depPersona && depPersona.value !== "") {
        cargarProvincias(depPersona.value, PROVINCIA_EDITAR);
    }

    const depEmpresa = document.getElementById('departamento_repdos');
    if (depEmpresa && depEmpresa.value !== "") {
        cargarProvinciasEmpresa(depEmpresa.value, PROVINCIA_EDITARd);
    }

    const razonSocialInicial = document.getElementById('razon_social').value.trim();
    const tieneSucursales = <?= !empty($sucursales) ? 'true' : 'false' ?>;
    if (razonSocialInicial !== "" || tieneSucursales) {
        document.getElementById('contenedorSucursales').style.display = 'block';
    }
});

// ════════════════════════════════════════════════════
// ABRIR MODAL PARA ASIGNAR PLAN DE SERVICIO
// ════════════════════════════════════════════════════
document.getElementById('tablaSucursales').addEventListener('click', function (e) {
    const btnAsignar = e.target.closest('.btn-asignar-plan');
    if (!btnAsignar) return;

    const fila = btnAsignar.closest('tr');
    const idPersona = btnAsignar.getAttribute('data-id-persona') || fila.getAttribute('data-indice');
    
    
    const razonSocial = document.getElementById('razon_social').value.trim();

    
    const nombreSucursal = fila.querySelector('.txt-nombre')?.textContent.trim() || '';

    // 3. Asignar al modal: Razón Social (o la sucursal si la razón social está vacía)
    document.getElementById('idPersonaHidden').value = idPersona;
    document.getElementById('modalClienteNombre').textContent = razonSocial !== '' ? razonSocial : nombreSucursal;

    // 4. Cargar planes y tipos de plan
    cargarPlanesYTipos();

    // 5. Mostrar modal
    const modalPlan = obtenerModalPlan();
    if (modalPlan) {
        modalPlan.show();
    }
});

// ════════════════════════════════════════════════════
// CALCULAR FECHA DE VENCIMIENTO SEGÚN TIPO DE PLAN
// ════════════════════════════════════════════════════
function calcularFechaVencimiento() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const idTipoPlan = document.getElementById('selectTipoPlan').value;
    const fechaVencimiento = document.getElementById('fechaVencimiento');
    
    if (!fechaInicio || !idTipoPlan) {
        fechaVencimiento.value = '';
        return;
    }
    
    const inicio = new Date(fechaInicio + 'T00:00:00');
    let mesesAAgregar = 0;
    
    // Mapear id_tipo_plan a meses
    switch (idTipoPlan) {
        case '1': // Mensual
            mesesAAgregar = 1;
            break;
        case '2': // Trimestral
            mesesAAgregar = 3;
            break;
        case '3': // Semestral
            mesesAAgregar = 6;
            break;
        case '4': // Anual
            mesesAAgregar = 12;
            break;
        default:
            fechaVencimiento.value = '';
            return;
    }
    
    inicio.setMonth(inicio.getMonth() + mesesAAgregar);
    
    // Formatear como YYYY-MM-DD
    const yyyy = inicio.getFullYear();
    const mm = String(inicio.getMonth() + 1).padStart(2, '0');
    const dd = String(inicio.getDate()).padStart(2, '0');
    fechaVencimiento.value = `${yyyy}-${mm}-${dd}`;
}

// Event listeners para recalcular automáticamente
document.addEventListener('DOMContentLoaded', function () {
    const fechaInicio = document.getElementById('fechaInicio');
    const selectTipoPlan = document.getElementById('selectTipoPlan');
    
    if (fechaInicio) {
        fechaInicio.addEventListener('change', calcularFechaVencimiento);
    }
    if (selectTipoPlan) {
        selectTipoPlan.addEventListener('change', calcularFechaVencimiento);
    }
});

// ════════════════════════════════════════════════════
// CARGAR PLANES Y TIPOS DE PLAN EN EL MODAL
// ════════════════════════════════════════════════════
async function cargarPlanesYTipos() {
    try {
        // Cargar planes
        const resPlanes = await fetch(`${BASE_URL}personas/planesDisponibles`);
        const planes = await resPlanes.json();
        const selectPlan = document.getElementById('selectPlan');
        selectPlan.innerHTML = '<option value="">-- Seleccionar --</option>';
        planes.forEach(plan => {
            const opt = document.createElement('option');
            opt.value = plan.id_plan;
            opt.textContent = plan.nombre_plan;
            selectPlan.appendChild(opt);
        });

        // Cargar tipos de plan
        const resTipos = await fetch(`${BASE_URL}personas/tipoplan`);
        const tipos = await resTipos.json();
        const selectTipo = document.getElementById('selectTipoPlan');
        selectTipo.innerHTML = '<option value="">-- Seleccionar --</option>';
        tipos.forEach(tipo => {
            const opt = document.createElement('option');
            opt.value = tipo.id_tipo_plan;
            opt.textContent = tipo.nombre_tipo;
            selectTipo.appendChild(opt);
        });
    } catch (error) {
        console.error('Error cargando planes:', error);
    }
}

// ════════════════════════════════════════════════════
// ANIMACIONES GSAP: MODAL SUCURSAL (CREAR / EDITAR)
// ════════════════════════════════════════════════════
(function () {
    const modal = document.getElementById('modalSucursal');
    if (!modal) return;

    if (typeof gsap !== 'undefined') {
        modal.addEventListener('show.bs.modal', function () {
            if (window.__pdsgSucFormTl) window.__pdsgSucFormTl.kill();
            var esMobile = window.innerWidth <= 480;
            gsap.set('#modalSucursal .pdsg-modal-bar', { opacity: 0, y: 12 });
            gsap.set('#modalSucursal .pdsg-modal-scroll', { opacity: 0, y: 20 });

            if (esMobile) {
                gsap.set('#modalSucursal .pdsg-modal-content', { y: window.innerHeight, autoAlpha: 1 });
                gsap.set('#modalSucursal .pdsg-modal-close', { opacity: 0, scale: .5, rotate: -90 });
            } else {
                gsap.set('#modalSucursal .pdsg-modal-content', { scale: .9, autoAlpha: 0 });
            }
        });

        modal.addEventListener('shown.bs.modal', function () {
            if (window.__pdsgSucFormTl) window.__pdsgSucFormTl.kill();
            var esMobile = window.innerWidth <= 480;
            window.__pdsgSucFormTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

            if (esMobile) {
                window.__pdsgSucFormTl
                    .to('#modalSucursal .pdsg-modal-content', { y: 0, autoAlpha: 1, duration: .5, ease: 'power4.out' })
                    .to('#modalSucursal .pdsg-modal-bar', { opacity: 1, y: 0, duration: .3 }, '-=.3')
                    .fromTo('#modalSucursal .pdsg-modal-close',
                        { opacity: 0, scale: .5, rotate: -90 },
                        { opacity: 1, scale: 1, rotate: 0, duration: .3 }, '-=.2')
                    .to('#modalSucursal .pdsg-modal-scroll', { opacity: 1, y: 0, duration: .25 }, '-=.25')
                    .fromTo('#modalSucursal .pdsg-modal-card',
                        { opacity: 0, y: 14 },
                        { opacity: 1, y: 0, duration: .3, stagger: .06 }, '-=.2');
            } else {
                gsap.set('#modalSucursal .pdsg-modal-content', { scale: .9, autoAlpha: 0 });
                window.__pdsgSucFormTl
                    .to('#modalSucursal .pdsg-modal-content', { scale: 1, autoAlpha: 1, duration: .42, ease: 'back.out(1.5)' })
                    .to('#modalSucursal .pdsg-modal-bar', { opacity: 1, y: 0, duration: .3 }, '-=.25')
                    .fromTo('#modalSucursal .pdsg-modal-close',
                        { opacity: 0, scale: .5, rotate: -90 },
                        { opacity: 1, scale: 1, rotate: 0, duration: .3 }, '-=.2')
                    .to('#modalSucursal .pdsg-modal-scroll', { opacity: 1, y: 0, duration: .25 }, '-=.2')
                    .fromTo('#modalSucursal .pdsg-modal-card',
                        { opacity: 0, y: 14 },
                        { opacity: 1, y: 0, duration: .3, stagger: .06 }, '-=.15');
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            gsap.set('#modalSucursal .pdsg-modal-content', { clearProps: 'all', scale: 1, y: 0, autoAlpha: 1 });
            gsap.set('#modalSucursal .pdsg-modal-bar', { clearProps: 'all', opacity: 1, y: 0 });
            gsap.set('#modalSucursal .pdsg-modal-scroll', { clearProps: 'all', opacity: 1, y: 0 });
            gsap.set('#modalSucursal .pdsg-modal-close', { clearProps: 'all', opacity: 1, scale: 1 });
        });

        // Cierre animado: interceptar el botón X y el fondo
        modal.querySelector('.pdsg-modal-close').addEventListener('click', function (e) {
            e.preventDefault();
            pdsgSucursalCerrarForm();
        });
        modal.addEventListener('mousedown', function (e) {
            if (e.target === modal || e.target.classList.contains('modal-dialog')) {
                pdsgSucursalCerrarForm();
            }
        });
    }
})();

function pdsgSucursalCerrarForm() {
    const modal = document.getElementById('modalSucursal');
    if (!modal || !modal.classList.contains('show')) return;
    if (typeof gsap !== 'undefined') {
        gsap.to('#modalSucursal .pdsg-modal-content', {
            y: 40, opacity: 0, scale: .98, duration: .22, ease: 'power2.in',
            overwrite: true,
            onComplete: function () {
                bootstrap.Modal.getInstance(modal)?.hide();
            }
        });
    } else {
        bootstrap.Modal.getInstance(modal)?.hide();
    }
}

// ════════════════════════════════════════════════════
// GUARDADO AJAX: cuando el formulario vive dentro del
// modal "Registrar / Editar Persona" (personas.php), se
// envía por fetch, se cierra el modal y se recarga la tabla.
// La detección: la vista corre dentro de un iframe (?modal=1).
// ════════════════════════════════════════════════════
(function () {
    const form = document.getElementById('formPersona');
    const dentroModal = (window.location.search.indexOf('modal=1') !== -1)
        || (typeof window.frameElement !== 'undefined' && window.frameElement !== null);
    if (!form) return;
    if (!dentroModal) return;   // página standalone: submit normal

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.reportValidity()) return;

        const btn = document.getElementById('btnGuardar');
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Guardando…';

        const csrfField = form.querySelector('input[name="<?= csrf_token() ?>"]');
        const csrfName = csrfField ? csrfField.getAttribute('name') : '<?= csrf_token() ?>';
        const csrfVal = csrfField ? csrfField.value : '';

        const data = new FormData(form);
        if (csrfField) data.set(csrfName, csrfVal);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfVal
            },
            body: data
        })
        .then(function (r) { return r.json(); })
        .then(function (resp) {
            if (resp && resp.success) {
                window.parent.postMessage({ tipo: 'pdsg:personaGuardada', message: resp.message || '' }, '*');
            } else {
                alert((resp && resp.message) ? resp.message : 'Ocurrió un error al guardar.');
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        })
        .catch(function () {
            alert('Ocurrió un error de conexión al guardar.');
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
    });

    // Botón "Cancelar" y la X del hero cierran el modal
    const btnCancelar = form.querySelector('[data-cancelar-modal]');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function (e) {
            e.preventDefault();
            window.parent.postMessage({ tipo: 'pdsg:cerrarModalPersona' }, '*');
        });
    }
    const xCerrar = document.querySelector('.pdsg-add-close');
    if (xCerrar) {
        xCerrar.addEventListener('click', function (e) {
            e.preventDefault();
            window.parent.postMessage({ tipo: 'pdsg:cerrarModalPersona' }, '*');
        });
    }
})();

</script>