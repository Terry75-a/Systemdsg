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
                    <a href="<?= base_url('personas') ?>" class="pdsg-add-close text-decoration-none" title="Cancelar" aria-label="Cerrar formulario">
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
                                                   <i class="fa-solid fa-pen-to-square"></i> <span>Editar</span>
                                                    
                                                    
                                                </button>
                                                <button type="button" class="pdsg-action pdsg-action-danger btn-eliminar-sucursal" title="Eliminar"><i class="fa-solid fa-trash"></i><span>Eliminar</span></button>
                                                <!--<button  class="pdsg-suc-btn pdsg-suc-btn-del btn-eliminar-sucursal" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i><span>Eliminar</span>
                                                    
                                                </button> !-->
                                                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-more btn-asignar-plan"
                                                    data-id-persona="<?= esc($idSuc); ?>"
                                                    data-nombre-persona="<?= esc($nomSuc); ?>"
                                                    title="Asignar Plan">
                                                    <i class="fa-solid fa-inbox"></i> <span>Plan</span>
                                                </button>
                                                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-payment btn-asignar-tipo-pagos" title="tipopago">
                                                      <i class="fa-solid fa-money-bill"></i> <span>Pagos</span>
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
                    
                </div>

            </form>
        </div>
    </div>
</div>
<?= view('personas/partials/_modal_sucursal') ?>
<?= view('personas/partials/_modal_plan') ?>
<?= view('personas/partials/_modal_pago') ?>


<script>
window.PDSG_CFG = {
    csrfName: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>',
    baseUrl: '<?= rtrim(base_url(), '/') . '/' ?>',
    provinciaEditar: '<?= isset($persona) ? (is_array($persona) ? $persona['id_provincia'] : $persona->id_provincia) : '' ?>',
    distritoEditar: '<?= isset($persona) ? (is_array($persona) ? $persona['id_distrito'] : $persona->id_distrito) : '' ?>',
    provinciaEmpresa: '<?= isset($empresa) ? (is_array($empresa) ? $empresa['id_provincia'] : $empresa->id_provincia) : '' ?>',
    distritoEmpresa: '<?= isset($empresa) ? (is_array($empresa) ? $empresa['id_distrito'] : $empresa->id_distrito) : '' ?>',
    tieneSucursales: <?= !empty($sucursales) ? 'true' : 'false' ?>
};
</script>

<!-- Carga del JavaScript externo modular -->
<script src="<?= base_url('js/persona-editor.js?v=' . filemtime(FCPATH . 'js/persona-editor.js')) ?>"></script>

