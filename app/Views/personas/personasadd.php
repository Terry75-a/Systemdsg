<?php
?>

<link rel="stylesheet" href="<?= base_url('css/dashboard/persona-editor.css?v=' . filemtime(FCPATH . 'css/dashboard/persona-editor.css')) ?>">
<link rel="stylesheet" href="<?= base_url('css/dashboard/tk-persona.css?v=' . filemtime(FCPATH . 'css/dashboard/tk-persona.css')) ?>">
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

    <div class="pf-shell pdsg-add-wrap">
            <form action="<?= base_url('personas/guardar-todo') ?>" method="POST" id="formPersona" class="pf-form">
                <?= csrf_field() ?>
                <!-- Contenedor para registrar los IDs de sucursales a eliminar en BD -->
<div id="sucursalesEliminadasContainer"></div>

                <input type="hidden" name="id_persona" value="<?= isset($persona) ? (is_array($persona) ? $persona['id_persona'] : $persona->id_persona) : '' ?>">


                <!-- ═══ Barra superior: volver · título · pasos · guardar ═══ -->
                <header class="pf-top">
                    <a href="<?= base_url('personas') ?>" class="pf-back pdsg-add-close" title="Cerrar" aria-label="Cerrar formulario">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div class="pf-top-title">
                        <span class="pf-crumb">Clientes <i class="fa-solid fa-chevron-right"></i> <?= isset($persona) ? 'Editar' : 'Nuevo' ?></span>
                        <h1 id="pe-title"><?= isset($persona) ? 'Editar cliente' : 'Nuevo cliente' ?></h1>
                    </div>
                    <nav class="pf-steps" aria-label="Secciones del formulario">
                        <a href="#pf-identidad" class="is-on"><b>1</b>Persona</a>
                        <a href="#pf-empresa"><b>2</b>Empresa</a>
                    </nav>
                    <button type="submit" class="btn pdsg-btn-save pf-save" id="btnGuardar">
                        <?php if (isset($persona)): ?>
                            <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Cambios
                        <?php else: ?>
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Persona
                        <?php endif; ?>
                    </button>
                </header>

                <div class="pf-body">
                    <!-- ═══ Inicio: búsqueda por DNI + ficha en vivo ═══ -->
                    <section class="pf-hero">
                        <div class="pf-hero-main">
                            <span class="pf-kicker"><i class="fa-solid fa-bolt"></i> Empieza aquí</span>
                            <h2>Busca al cliente por su DNI</h2>
                            <p>Los nombres y apellidos se completan solos con los datos de RENIEC.</p>
                        <div class="pe-lookup pf-lookup">
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

                        </div>
                        <aside class="pf-card" aria-live="polite">
                            <span class="pf-card-avatar" id="pfAv">?</span>
                            <div class="pf-card-info">
                                <b id="pfName">Sin nombre aún</b>
                                <span id="pfDoc">DNI —</span>
                            </div>
                            <ul class="pf-card-meta">
                                <li><i class="fa-solid fa-phone"></i><span id="pfTel">—</span></li>
                                <li><i class="fa-regular fa-envelope"></i><span id="pfMail">—</span></li>
                                <li><i class="fa-regular fa-building"></i><span id="pfBiz">Sin empresa</span></li>
                            </ul>
                            <div class="pf-card-bar"><i id="pfBar"></i></div>
                            <span class="pf-card-pct" id="pfPct">0 % completo</span>
                        </aside>
                    </section>

                    <!-- ═══ Persona ═══ -->
                    <section class="pf-sec" id="pf-identidad">
                        <div class="pf-sec-side"><span class="pf-num">01</span><h3>Identidad</h3><p>Nombre completo tal como figura en el DNI.</p></div>
                        <div class="pf-sec-card"><div class="pf-grid">
                        <div class="pf-f">
                            <label for="nombre" class="pdsg-field-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="nombre"
                                   name="nombre"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('nombre', isset($persona) ? (is_array($persona) ? $persona['nombre'] : $persona->nombre) : '') ?>"
                                   required>
                        </div>
                        <div class="pf-f">
                            <label for="apellido_paterno" class="pdsg-field-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="apellido_paterno"
                                   name="apellido_paterno"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('apellido_paterno', isset($persona) ? (is_array($persona) ? $persona['apellido_paterno'] : $persona->apellido_paterno) : '') ?>"
                                   required>
                        </div>
                        <div class="pf-f">
                            <label for="apellido_materno" class="pdsg-field-label">Apellido Materno</label>
                            <input type="text"
                                   id="apellido_materno"
                                   name="apellido_materno"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente"
                                   value="<?= old('apellido_materno', isset($persona) ? (is_array($persona) ? $persona['apellido_materno'] : $persona->apellido_materno) : '') ?>">
                        </div>
                        </div></div>
                    </section>
                    <section class="pf-sec" id="pe-personal">
                        <div class="pf-sec-side"><span class="pf-num">02</span><h3>Contacto</h3><p>Cómo comunicarte con el cliente.</p></div>
                        <div class="pf-sec-card"><div class="pf-grid">
                        <div class="pf-f">
                            <label for="telefono" class="pdsg-field-label">Teléfono</label>
                            <input type="text"
                                   id="telefono"
                                   name="telefono"
                                   class="form-control pdsg-input"
                                   value="<?= old('telefono', isset($persona) ? (is_array($persona) ? $persona['telefono'] : $persona->telefono) : '') ?>"
                                   maxlength="9"
                                   placeholder="Ej: 999888777">
                        </div>
                        <div class="pf-f">
                            <label for="correo" class="pdsg-field-label">Correo</label>
                            <input type="email"
                                   id="correo"
                                   name="correo"
                                   class="form-control pdsg-input"
                                   value="<?= old('correo', isset($persona) ? (is_array($persona) ? $persona['correo'] : $persona->correo) : '') ?>"
                                   placeholder="correo@ejemplo.com">
                        </div>
                        <div class="pf-f">
                            <label for="fecha_nacimiento" class="pdsg-field-label">Fecha de Nacimiento</label>
                            <input type="date"
                                   id="fecha_nacimiento"
                                   value="<?= old('fecha_nacimiento', isset($persona) ? (is_array($persona) ? $persona['fecha_nacimiento'] : $persona->fecha_nacimiento) : '') ?>"
                                   name="fecha_nacimiento"
                                   class="form-control pdsg-input">
                        </div>
                        </div></div>
                    </section>
                    <section class="pf-sec">
                        <div class="pf-sec-side"><span class="pf-num">03</span><h3>Domicilio</h3><p>Dirección personal del cliente.</p></div>
                        <div class="pf-sec-card"><div class="pf-grid">
                        <div class="pf-f pf-full">
                            <label for="direccion_rep" class="pdsg-field-label">Dirección Personal</label>
                            <input type="text"
                                   id="direccion_rep"
                                   name="direccion_rep"
                                   value="<?= old('direccion_rep', isset($persona) ? (is_array($persona) ? $persona['direccion'] : $persona->direccion) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Av. / Jr. / Calle...">
                        </div>
                        <div class="pf-f">
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
                        <div class="pf-f">
                            <label for="provincia_rep" class="pdsg-field-label">Provincia</label>
                            <select id="provincia_rep" name="provincia_rep" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        <div class="pf-f">
                            <label for="distrito_rep" class="pdsg-field-label">Distrito</label>
                            <select id="distrito_rep" name="distrito_rep" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        </div></div>
                    </section>

                    <!-- ═══ Empresa ═══ -->
                    <section class="pf-sec" id="pf-empresa">
                        <div class="pf-sec-side"><span class="pf-num">04</span><h3>Empresa <small>Opcional</small></h3><p>Solo si el cliente factura con RUC. La razón social y la dirección fiscal se completan solas.</p></div>
                        <div class="pf-sec-card" id="pe-business"><div class="pf-grid">
                        <div class="pe-lookup pf-f pf-full">
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

                        <div class="pf-f">
                            <label for="razon_social" class="pdsg-field-label">Razón Social</label>
                            <input type="text"
                                   id="razon_social"
                                   name="razon_social"
                                   value="<?= old('razon_social', isset($empresa) ? (is_array($empresa) ? $empresa['razon_social'] : $empresa->razon_social) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente">
                        </div>
                        <div class="pf-f pf-full">
                            <label for="correo_empresa" class="pdsg-field-label">Correo Empresarial</label>
                            <input type="email"
                                   id="correo_empresa"
                                   name="correo_empresa"
                                   value="<?= old('correo_empresa', isset($empresa) ? (is_array($empresa) ? $empresa['correo'] : $empresa->correo) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="empresa@correo.com">
                        </div>
                            <div class="pf-sub pf-full">Domicilio fiscal</div>
                        <div class="pf-f pf-full">
                            <label for="direccion" class="pdsg-field-label">Dirección Fiscal</label>
                            <input type="text"
                                   id="direccion"
                                   name="direccion"
                                   value="<?= old('direccion', isset($empresa) ? (is_array($empresa) ? $empresa['direccion'] : $empresa->direccion) : '') ?>"
                                   class="form-control pdsg-input"
                                   placeholder="Se llena automáticamente">
                        </div>
                        <div class="pf-f">
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
                        <div class="pf-f">
                            <label for="provincia_repdos" class="pdsg-field-label">Provincia</label>
                            <select id="provincia_repdos" name="provincia_repdos" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        <div class="pf-f">
                            <label for="distrito_repdos" class="pdsg-field-label">Distrito</label>
                            <select id="distrito_repdos" name="distrito_repdos" class="form-select pdsg-input">
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        </div></div>
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

                                                 <!-- BOTÓN CONDICIONAL SEGÚN ESTADO (DESACTIVAR O ACTIVAR) -->
                                              <?php if ($estSuc == 1): ?>
                                           <button type="button" class="pdsg-suc-btn pdsg-suc-btn-del btn-toggle-estado-sucursal" title="Desactivar">
                                                <i class="fa-solid fa-trash"></i>
                                                   </button>
                                           <?php else: ?>
                                           <button type="button" class="pdsg-suc-btn pdsg-suc-btn-on btn-toggle-estado-sucursal" title="Activar" style="color: #10b981; background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.25);">
                                                <i class="fa-solid fa-check"></i>
                                                       </button>
                                              <?php endif; ?>

                                            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-more btn-asignar-plan"
                                              data-id-persona="<?= esc($idSuc); ?>"
                                            data-nombre-persona="<?= esc($nomSuc); ?>"
                                               title="Asignar Plan">
                                               <i class="fa-solid fa-inbox"></i>
                                                 </button>
                                            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-payment btn-asignar-tipo-pagos" title="tipopago">
                                               <i class="fa-solid fa-money-bill"></i>
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

                </div>

                <footer class="pf-foot">
                    <span class="pe-required-note"><span>*</span> Campos obligatorios</span>
                    <span class="pf-foot-hint"><i class="fa-regular fa-keyboard"></i> Puedes guardar en cualquier momento desde la barra superior</span>
                </footer>
            </form>
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

<script>
/* Ficha en vivo y pasos: solo muestra lo que se escribe, no cambia el envío */
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var campos = ['dni', 'nombre', 'apellido_paterno', 'telefono', 'correo', 'direccion_rep', 'departamento_rep'];
    function v(id) { var e = $(id); return e ? String(e.value || '').trim() : ''; }
    function pintar() {
        var nom = [v('nombre'), v('apellido_paterno'), v('apellido_materno')].filter(Boolean).join(' ');
        $('pfName').textContent = nom || 'Sin nombre aún';
        $('pfAv').textContent = nom ? nom.split(/\s+/).slice(0, 2).map(function (p) { return p[0]; }).join('').toUpperCase() : '?';
        $('pfDoc').textContent = 'DNI ' + (v('dni') || '—');
        $('pfTel').textContent = v('telefono') || '—';
        $('pfMail').textContent = v('correo') || '—';
        $('pfBiz').textContent = v('razon_social') || (v('ruc') ? 'RUC ' + v('ruc') : 'Sin empresa');
        var ok = campos.filter(function (id) { return v(id) !== ''; }).length;
        var pct = Math.round(ok / campos.length * 100);
        $('pfBar').style.width = pct + '%';
        $('pfPct').textContent = pct + ' % completo';
    }
    var form = $('formPersona');
    if (form) { form.addEventListener('input', pintar); form.addEventListener('change', pintar); }
    setInterval(pintar, 800); /* también refleja lo que completa la búsqueda por DNI/RUC */
    pintar();

    /* pasos de la barra superior: marca la sección visible */
    var pasos = document.querySelectorAll('.pf-steps a');
    var secciones = [$('pf-identidad'), $('pf-empresa')];
    var cont = document.querySelector('.pf-body');
    function marcar() {
        var idx = 0;
        secciones.forEach(function (sec, i) { if (sec && sec.getBoundingClientRect().top < window.innerHeight * 0.45) idx = i; });
        pasos.forEach(function (a, i) { a.classList.toggle('is-on', i === idx); });
    }
    (cont || window).addEventListener('scroll', marcar, { passive: true });
    window.addEventListener('scroll', marcar, { passive: true });
    pasos.forEach(function (a) {
        a.addEventListener('click', function (e) {
            var t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
})();
</script>

<!-- Carga del JavaScript externo modular -->
<script src="<?= base_url('js/persona-editor.js?v=' . filemtime(FCPATH . 'js/persona-editor.js')) ?>"></script>

