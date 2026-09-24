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

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalVerPersona" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pdsg-modal-content">

      <div class="pdsg-modal-bar">
        <span class="pdsg-modal-avatar" id="ver_avatar">-</span>
        <div class="pdsg-modal-head-info">
          <h5 class="pdsg-modal-name" id="ver_nombre">-</h5>
          <span class="pdsg-modal-sub" id="ver_razon_social">-</span>
        </div>
        <span class="pdsg-chip pdsg-chip-on" id="pdsg-modal-chip"><i class="fa-solid fa-circle"></i> Activo</span>
        <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="pdsg-modal-scroll">
        <div class="pdsg-modal-body">

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-id-card"></i> Datos personales</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-user"></i> Apellido paterno</span>
                  <span class="pdsg-modal-field-value" id="ver_apellido_paterno">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-user"></i> Apellido materno</span>
                  <span class="pdsg-modal-field-value" id="ver_apellido_materno">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-hashtag"></i> DNI</span>
                  <span class="pdsg-modal-field-value pdsg-mono" id="ver_dni">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-hashtag"></i> RUC</span>
                  <span class="pdsg-modal-field-value pdsg-mono" id="ver_ruc">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-address-book"></i> Contacto</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                  <span class="pdsg-modal-field-value" id="ver_telefono">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Dirección</span>
                  <span class="pdsg-modal-field-value" id="ver_direccion">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-map-location-dot"></i> Ubicación</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Departamento</span>
                  <span class="pdsg-modal-field-value" id="ver_departamento">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Provincia</span>
                  <span class="pdsg-modal-field-value" id="ver_provincia">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Distrito</span>
                  <span class="pdsg-modal-field-value" id="ver_distrito">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group pdsg-modal-group--table">
            <span class="pdsg-modal-group-title"><i class="fa-solid fa-shop"></i> Sucursales</span>
            <div class="table-responsive pdsg-modal-table">
              <table class="table table-sm table-hover align-middle mb-0" id="tablaSucursales">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th class="text-center">Estado</th>
                  </tr>
                </thead>
                <tbody id="ver_sucursales">
                  <tr id="sucursales_vacio">
                    <td colspan="5" class="text-center text-muted py-3">
                      <i class="bi bi-inbox me-1"></i> Sin sucursales registradas
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Ver Sucursal -->
<div class="modal fade" id="modalVerSucursal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pdsg-modal-content">

      <div class="pdsg-modal-bar">
        <span class="pdsg-modal-avatar" id="suc_avatar">-</span>
        <div class="pdsg-modal-head-info">
          <h5 class="pdsg-modal-name" id="suc_nombre">-</h5>
          <span class="pdsg-modal-sub" id="suc_detalle_sub">-</span>
        </div>
        <span class="pdsg-chip pdsg-chip-on" id="suc-chip"><i class="fa-solid fa-circle"></i> Activo</span>
        <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="pdsg-modal-scroll">
        <div class="pdsg-modal-body">

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-shop"></i> Datos de la sucursal</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-hashtag"></i> Código cliente</span>
                  <span class="pdsg-modal-field-value pdsg-mono" id="suc_codigo">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-user-tie"></i> Representante</span>
                  <span class="pdsg-modal-field-value" id="suc_representante">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-tags"></i> Tipo</span>
                  <span class="pdsg-modal-field-value" id="suc_tipo">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                  <span class="pdsg-modal-field-value" id="suc_telefono">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-envelope"></i> Correo</span>
                  <span class="pdsg-modal-field-value" id="suc_correo">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Dirección</span>
                  <span class="pdsg-modal-field-value" id="suc_direccion">-</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-light">
    <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">
    <h5 class="modal-title" id="modalPlanLabel">Asignar Plan de Servicio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
      <div class="modal-body">
    
    <div class="alert alert-dark text-white align-items-center mb-3" role="alert">
        <div>
            Asignando plan a: <strong id="modalClienteNombre"></strong>
           <input type="hidden" id="idPersonaHidden" name="id_persona"> 
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="selectPlan" class="form-label">Seleccionar Plan disponible:</label>
            <select class="form-select" id="selectPlan">
                </select>
        </div>

        <div class="col-md-6">
            <label for="selectTipoPlan" class="form-label">Tipo de plan:</label>
            <select class="form-select" id="selectTipoPlan">
                </select>
        </div>

        

        <div class="col-md-6">
            <label for="fechaInicio" class="form-label fw-medium">Fecha de Inicio: <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="fechaInicio" value="2026-06-19" required>
          </div>

          <div class="col-md-6">
            <label for="fechaVencimiento" class="form-label fw-medium">Fecha de Vencimiento:</label>
            <input type="date" class="form-control bg-light" id="fechaVencimiento" readonly placeholder="Cálculo automático">
          </div>

        <div class="col-md-6">
            <label for="inputPrecio" class="form-label">Precio Cobrado (S/):</label>
            <input type="number" step="0.01" class="form-control" id="inputPrecio" placeholder="0.00">
        </div>

        <div class="col-md-6">
            <label for="selectEstadoPago" class="form-label fw-medium">Estado del Pago: <span class="text-danger">*</span></label>
            <select class="form-select" id="selectEstadoPago" required>
              <option value="Pagado" selected>Pagado</option>
              <option value="Pendiente">Pendiente</option>
              <option value="Cortesia">Cortesía / Demo</option>
            </select>
          </div>

        <div class="col-12">
            <label for="txtObservaciones" class="form-label">Observaciones (Opcional):</label>
            <textarea class="form-control" id="txtObservaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
        </div>
    </div>

    

</div>
      <div class="modal-footer">
        
        <button type="submit" class="btn btn-light px-4 py-2 fw-semibold border rounded-0" id="btnGuardarPlan">Asignar Plan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Registrar / Editar Persona (carga el formulario personasadd en un iframe) -->
<div class="modal fade" id="modalPersonaAdd" tabindex="-1" aria-label="Registrar o editar persona" aria-hidden="true">
    <div class="modal-dialog pdsg-add-dialog">
        <div class="modal-content pdsg-modal-content pdsg-add-modal-content">
            <div class="pdsg-modal-loading" id="modalPersonaAddLoading" hidden>
                <button type="button" class="pe-loading-close" data-bs-dismiss="modal" aria-label="Cerrar formulario"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <span class="pdsg-loading-ring" aria-hidden="true"></span>
                <p class="pdsg-loading-text">Preparando formulario…</p>
            </div>
            <iframe id="modalPersonaAddFrame" class="pdsg-add-frame" src="about:blank"
                    title="Formulario de persona" frameborder="0"></iframe>
        </div>
    </div>
</div>

<script>

  
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
    }
});

    var estadoActual = 1;
    var todosClientes = [];
    var paginaActual = 1;
    var POR_PAGINA = 5;

    // ── Cargar datos y pintar tabla estilo HeroUI ──
    function cargarClientes() {
        $.getJSON("<?= base_url('personas/listar') ?>", { estado: estadoActual })
            .done(function (resp) {
                todosClientes = resp.data || [];
                paginaActual = 1;
                pintarTabla();
            })
            .fail(function () {
                todosClientes = [];
                pintarTabla();
            });
    }

    // Contadores por pestaña
    function cargarContadores() {
        $.getJSON("<?= base_url('personas/listar') ?>", { estado: 1 })
            .done(function (r) { $('#countActivos').text((r.data || []).length); });
        $.getJSON("<?= base_url('personas/listar') ?>", { estado: 0 })
            .done(function (r) { $('#countInactivos').text((r.data || []).length); });
    }

    function inicialesCliente(nombre) {
        var n = (nombre || '').trim().split(/\s+/);
        return ((n[0] || '?').charAt(0) + (n[1] ? n[1].charAt(0) : '')).toUpperCase();
    }

    function chipEstado(activo) {
        if (activo) {
            return '<span class="pdsg-chip pdsg-chip-on"><i class="fa-solid fa-circle"></i> Activo</span>';
        }
        return '<span class="pdsg-chip pdsg-chip-off"><i class="fa-solid fa-circle"></i> Inactivo</span>';
    }

    function renderBotonVer(c) {
        return `
            <button type="button" class="pdsg-action pdsg-action-view" title="Ver detalle"
                data-bs-toggle="modal" data-bs-target="#modalVerPersona"
                data-id="${c.id_persona}" data-nombre="${c.nombre}"
                data-dni="${c.dni}" data-telefono="${c.telefono}" data-direccion="${c.direccion}">
                <i class="fa-solid fa-eye"></i><span>Ver</span>
            </button>`;
    }

    function ocultarMiles(v) { return String(v == null ? '' : v); }

    function pintarTabla() {
        var total = todosClientes.length;
        var totalPaginas = Math.max(1, Math.ceil(total / POR_PAGINA));
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        var inicio = (paginaActual - 1) * POR_PAGINA;
        var filas = todosClientes.slice(inicio, inicio + POR_PAGINA);

        var esMobile = window.innerWidth <= 768;

        var $empty = $('#pdsgEmpty');
        if (!filas.length) {
            $empty.removeAttr('hidden');
            $('#pdsgPagination').attr('hidden', true);
            $('#pdsgMlist').empty();
            $('#tablaClientes tbody').empty();
            return;
        }
        $empty.attr('hidden', true);

        if (esMobile) {
            var $list = $('#pdsgMlist').empty();
            $('#tablaClientes').hide();
            $list.show();
            $.each(filas, function (_, c) {
                var acciones = accionesCliente(c);
                $list.append(`
                    <div class="pdsg-mcard">
                        <div class="pdsg-mcard-top">
                            <span class="pdsg-avatar">${inicialesCliente(c.nombre)}</span>
                            <div class="pdsg-mcard-user">
                                <span class="pdsg-user-name">${ocultarMiles(c.nombre)}</span>
                                <span class="pdsg-mcard-chip">${chipEstado(estadoActual == 1)}</span>
                            </div>
                            <span class="pdsg-mcard-chevron"><i class="fa-solid fa-chevron-right"></i></span>
                        </div>
                        <div class="pdsg-mcard-details">
                            <div class="pdsg-mcard-detail">
                                <span class="pdsg-mcard-label"><i class="fa-solid fa-hashtag"></i> DNI</span>
                                <span class="pdsg-mcard-val pdsg-mono">${ocultarMiles(c.dni)}</span>
                            </div>
                            <div class="pdsg-mcard-detail">
                                <span class="pdsg-mcard-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                                <span class="pdsg-mcard-val">${ocultarMiles(c.telefono) || '—'}</span>
                            </div>
                            <div class="pdsg-mcard-detail">
                                <span class="pdsg-mcard-label"><i class="fa-solid fa-location-dot"></i> Dirección</span>
                                <span class="pdsg-mcard-val">${ocultarMiles(c.direccion) || '—'}</span>
                            </div>
                        </div>
                        <div class="pdsg-mcard-actions"><span class="pdsg-actions">${acciones}</span></div>
                    </div>
                `);
            });
        } else {
            var tbody = $('#tablaClientes tbody').empty();
            $('#pdsgMlist').hide();
            $('#tablaClientes').show();
            $.each(filas, function (_, c) {
                var acciones = accionesCliente(c);
                tbody.append(`
                <tr>
                    <td data-label="Cliente">
                        <span class="pdsg-user">
                            <span class="pdsg-avatar">${inicialesCliente(c.nombre)}</span>
                            <span class="pdsg-user-name">${ocultarMiles(c.nombre)}</span>
                        </span>
                    </td>
                    <td data-label="DNI" class="pdsg-mono">${ocultarMiles(c.dni)}</td>
                    <td data-label="Teléfono">${ocultarMiles(c.telefono)}</td>
                    <td data-label="Dirección">${ocultarMiles(c.direccion)}</td>
                    <td data-label="Estado" class="pdsg-td-center">${chipEstado(estadoActual == 1)}</td>
                    <td data-label="Acciones" class="pdsg-td-end"><span class="pdsg-actions">${acciones}</span></td>
                </tr>
            `);
            });
        }

        pintarPaginacion(total);
    }

    function accionesCliente(c) {
        if (estadoActual == 1) {
            return renderBotonVer(c)
                + `<a href="<?= base_url('personasadd/') ?>${c.id_persona}" class="pdsg-action pdsg-action-edit" title="Editar"><i class="fa-solid fa-pen-to-square"></i><span>Editar</span></a>`
                + `<button class="pdsg-action pdsg-action-danger" title="Eliminar" onclick="cambiarEstado(${c.id_persona}, 0, this)"><i class="fa-solid fa-trash"></i><span>Eliminar</span></button>`;
        }
        return `<button class="pdsg-action pdsg-action-on" title="Activar" onclick="cambiarEstado(${c.id_persona}, 1, this)"><i class="fa-solid fa-check"></i><span>Activar</span></button>`
            + `<button class="pdsg-action pdsg-action-danger" title="Borrar definitivamente" onclick="borrarPersona(${c.id_persona}, this)"><i class="fa-solid fa-ban"></i><span>Borrar</span></button>`;
    }

    function pintarPaginacion(total) {
        var $pag = $('#pdsgPagination');
        if (total === 0) { $pag.attr('hidden', true); return; }
        $pag.removeAttr('hidden');

        var totalPaginas = Math.max(1, Math.ceil(total / POR_PAGINA));
        var fin = Math.min(paginaActual * POR_PAGINA, total);
        var inicio = (paginaActual - 1) * POR_PAGINA + 1;

        $('#pdsgSummary').text(inicio + ' a ' + fin + ' de ' + total + ' clientes');

        var html = '';
        html += `<button type="button" class="pdsg-page-nav" ${paginaActual == 1 ? 'disabled' : ''} data-pagina="${paginaActual - 1}">
                    <i class="fa-solid fa-chevron-left"></i> Prev
                 </button>`;
        for (var p = 1; p <= totalPaginas; p++) {
            html += `<button type="button" class="pdsg-page ${p == paginaActual ? 'is-active' : ''}" data-pagina="${p}">${p}</button>`;
        }
        html += `<button type="button" class="pdsg-page-nav" ${paginaActual == totalPaginas ? 'disabled' : ''} data-pagina="${paginaActual + 1}">
                    Next <i class="fa-solid fa-chevron-right"></i>
                 </button>`;

        $('#pdsgPages').html(html);
    }

    // Cambiar página (delegación)
    $('#pdsgPages').on('click', '.pdsg-page, .pdsg-page-nav', function () {
        if ($(this).is(':disabled')) return;
        var p = parseInt($(this).data('pagina'), 10);
        var totalPaginas = Math.max(1, Math.ceil(todosClientes.length / POR_PAGINA));
        if (p < 1 || p > totalPaginas) return;
        paginaActual = p;
        pintarTabla();
    });

    $(document).ready(function () {

        // Iniciales de la tabla
        cargarContadores();
        cargarClientes();

        // Evento para cambiar entre pestañas Activos / Inactivos
        $('#pestanasEstado .pdsg-tab').on('click', function (e) {
            e.preventDefault();

            $('#pestanasEstado .pdsg-tab').removeClass('is-active');
            $(this).addClass('is-active');

            estadoActual = $(this).data('estado');

            if (estadoActual == 0) {
                $('#btnAnadir').hide();
            } else {
                $('#btnAnadir').show();
            }

            cargarClientes();
        });

       

        // Cargar los planes disponibles cada vez que se abra el modal
        $('#modalPlan').on('show.bs.modal', function () {
            cargarPlanesDisponibles();
            cargarTiposPlan();
             
            
            setTimeout(function() {
                mantenerPlanSeleccionado();
            }, 300);
        });

        $('#btnGuardarPlan').on('click', function() {
            asignarPlan();
        });
        

         
       $(document).on('click', '.btn-ver-plan', function() {
    var idPersona = $(this).data('id');
    // Guardamos el id de la persona en el input oculto del modal
    $('#idEmpresaModal').val(idPersona); 

    obtenerNombrePersona(idPersona);

});
    });

    // Esta función reemplaza a tu antigua función eliminar
    function cambiarEstado(idPersona, nuevoEstado, boton) {
        if (!idPersona) {
            alert("ID no válido.");
            return;
        }

        
        var textoConfirmacion = (nuevoEstado === 0) ? "¿Estás seguro de dar de baja a esta persona?" : "¿Estás seguro de activar de nuevo a esta persona?";

        if (confirm(textoConfirmacion)) {
            var fila = $(boton).closest('tr, .pdsg-mcard');

            $.ajax({
                // Mandamos la petición a tu controlador de eliminar
                url: "<?= base_url('personas/eliminar/') ?>" + idPersona,
                type: "POST",
                dataType: "json",
                // Enviamos el nuevo estado en el cuerpo del POST para que el backend lo reciba
                data: { estado: nuevoEstado }, 
                success: function(response) {
                    if (response.status === 'success') {
                        // Eliminamos la fila visible y recargamos
                        if (fila && fila.length) fila.remove();
                        cargarClientes();
                        cargarContadores();
                        alert(response.message || "Estado actualizado con éxito.");
                    } else {
                        alert("No se pudo cambiar el estado en la base de datos.");
                    }
                },
                error: function() {
                    alert("Error de comunicación con el servidor.");
                }
            });
        }
    }

    $('#modalVerPersona').on('show.bs.modal', function (event) {
    var boton = $(event.relatedTarget);
    var id = boton.data('id');
   
    // Datos básicos que ya tenías en la fila
    var nombre = boton.data('nombre') || '';
    $('#ver_nombre').text(nombre || '-');
    $('#ver_dni').text(boton.data('dni') || '-');
    $('#ver_telefono').text(boton.data('telefono') || '-');
    $('#ver_direccion').text(boton.data('direccion') || '-');

    // Avatar con iniciales + chip de estado
    var n = nombre.trim().split(/\s+/);
    var iniciales = ((n[0] || '?').charAt(0) + (n[1] ? n[1].charAt(0) : '')).toUpperCase();
    $('#ver_avatar').text(iniciales);
    var chip = $('#pdsg-modal-chip');
    if (estadoActual == 1) {
        chip.attr('class', 'pdsg-chip pdsg-chip-on')
            .html('<i class="fa-solid fa-circle"></i> Activo');
    } else {
        chip.attr('class', 'pdsg-chip pdsg-chip-off')
            .html('<i class="fa-solid fa-circle"></i> Inactivo');
    }

    $.ajax({
        url: "<?= base_url('personas/obtenerDetalle') ?>",
        type: "GET",
        data: { id: id },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                var p = response.data;
                $('#ver_apellido_paterno').text(p.apellido_paterno || '-');
                $('#ver_apellido_materno').text(p.apellido_materno || '-');
                $('#ver_ruc').text(p.ruc || '-');
                $('#ver_razon_social').text(p.razon_social || '-');
                $('#ver_departamento').text(p.departamento || '-');
                $('#ver_provincia').text(p.provincia || '-');
                $('#ver_distrito').text(p.distrito || '-');
                
                 var tbody = $('#ver_sucursales');
tbody.empty();

if (p.sucursales && p.sucursales.length > 0) {
    p.sucursales.forEach(function(sucursal, index) {
        var estadoBadge = (sucursal.estado == 1)
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';

        var fila = `
            <tr class="pdsg-suc-row" data-index="${index}"
                data-nombre="${sucursal.nombre_sucursal || ''}"
                data-codigo="${sucursal.codigo_cliente || ''}"
                data-representante="${sucursal.representante || ''}"
                data-tipo="${sucursal.tipo || ''}"
                data-telefono="${sucursal.telefono || ''}"
                data-correo="${sucursal.correo || ''}"
                data-direccion="${sucursal.direccion || ''}"
                data-estado="${sucursal.estado || 0}"
                title="Ver detalle de la sucursal">
                <td data-label="#"><span class="pdsg-suc-num">${index + 1}</span></td>
                <td data-label="Nombre" class="pdsg-suc-nombre">${sucursal.nombre_sucursal || '-'}</td>
                <td data-label="Dirección">${sucursal.direccion || '-'}</td>
                <td data-label="Teléfono">${sucursal.telefono || '-'}</td>
                <td data-label="Estado" class="text-center">${estadoBadge}</td>
            </tr>
        `;
        tbody.append(fila);
    });
} else {
    tbody.append(`
        <tr id="sucursales_vacio">
            <td colspan="5" class="text-center text-muted py-3">
                <i class="bi bi-inbox me-1"></i> Sin sucursales registradas
            </td>
        </tr>
    `);
}
            
                
            }
        },
        
        error: function() {
            console.error("Error al obtener el detalle de la persona.");
        }
    });
    
    
    
});





    function borrarPersona(idPersona, boton) {
        if (!idPersona) {
            alert("ID no válido.");
            return;
        }

        if (confirm("¿Estás seguro de eliminar permanentemente a esta persona? Esta acción no se puede deshacer.")) {
            var fila = $(boton).closest('tr, .pdsg-mcard');

            $.ajax({
                url: "<?= base_url('personas/borrar/') ?>" + idPersona,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        // Eliminamos la fila visible y recargamos
                        if (fila && fila.length) fila.remove();
                        cargarClientes();
                        cargarContadores();
                        alert(response.message || "Persona eliminada permanentemente.");
                    } else {
                        alert("No se pudo eliminar la persona en la base de datos.");
                    }
                },
                error: function() {
                    alert("Error de comunicación con el servidor.");
                }
            });
        }
    }

   function cargarDetallePersona(id) {
    $.ajax({
        url: "<?= base_url('personas/obtenerNombrepersona') ?>",
        type: "GET",
        dataType: "json",
        data: { idPersona: id }, // Coincide con $this->request->getGet('idPersona')
        beforeSend: function() {
            $('#ver_nombre').text("Cargando...");
            $('#ver_dni').text("Cargando...");
        },
        success: function(response) {
            if (response.status === 'success') {
                const persona = response.data;
                
                // Si tu Model devuelve array o entity, los campos llegan como propiedades en JSON
                $('#ver_nombre').text(persona.nombre || '-');
                $('#ver_dni').text(persona.dni || '-');
                $('#ver_telefono').text(persona.telefono || 'Sin teléfono');
                $('#ver_direccion').text(persona.direccion || 'Sin dirección');
            } else {
                alert(response.message || "Error al obtener la persona");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            $('#ver_nombre').text("Error");
        }
    });
}

    function cargarPlanesDisponibles() {
        $.ajax({
            url: "<?= base_url('personas/planesDisponibles') ?>",
            type: "GET",
            dataType: "json",
            success: function(planes) {
                var selectPlan = $('#selectPlan');
                
                selectPlan.empty();
                selectPlan.append('');
                planes.forEach(function(plan) {
                    selectPlan.append(`<option value="${plan.id_plan}">${plan.nombre_plan}</option>`);
                    
                });
                

            },
            error: function() {
                alert("Error al cargar los planes disponibles.");
            }
        });
    };
    function cargarTiposPlan() {
        $.ajax({
            url: "<?= base_url('personas/tipoplan') ?>",
            type: "GET",
            dataType: "json",
            success: function(tipos) {
                var selectTipoPlan = $('#selectTipoPlan');
                selectTipoPlan.empty();
                selectTipoPlan.append('');
                tipos.forEach(function(tipo){
                    selectTipoPlan.append(`<option value="${tipo.id_tipo_plan}">${tipo.nombre_tipo}</option>`);


                });
            },
            error: function() {
                alert("Error al cargar los tipos de plan disponibles.");
            }
        });
    };

    function asignarPlan() {
        var idPlan = $('#selectPlan').val();
        var idTipoPlan = $('#selectTipoPlan').val();
        var idPersona = $('#idEmpresaModal').val();
        

    var fechaInicio = $('#fechaInicio').val();
    var fechaVencimiento = $('#fechaVencimiento').val();
    var precioCobrado = $('#inputPrecio').val();
    var estadoPago = $('#selectEstadoPago').val();
    var observaciones = $('#txtObservaciones').val();

    if(!idPersona){
        alert("Error: No se pudo obtener el ID del registro.");
        return;
    }


        $.ajax({
            url: "<?= base_url('personas/Asignarplanes') ?>",
            type: "POST",
            dataType: "json",
            data: {
                id_persona: idPersona,
                id_plan: idPlan,
                id_tipo_plan: idTipoPlan,
                fecha_de_inicio: fechaInicio,
               fecha_de_vencimiento: fechaVencimiento,
               precio: precioCobrado,
               estado_pago: estadoPago,
                descripcion: observaciones
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message || "Plan asignado correctamente.");
                    $('#modalPlan').modal('hide'); 
                    tablaCliente.ajax.reload(); // Recargar la tabla después de asignar el plan
                } else {
                    alert(response.message || "Error al asignar el plan.");
                }
            },
            error: function() {
                alert("Error al asignar el plan.");
            }
        });
    }

    function mantenerPlanSeleccionado(idPersona = null) {
        var IdPlanSelect = $('#selectPlan');
        var IdTipoPlanSelect = $('#selectTipoPlan');

        var idPersona = $('#idEmpresaModal').val();

        if (!idPersona) {
            console.warn("No hay ID de persona para cargar el plan");
            return;
        }

        $.ajax({
            url: "<?= base_url('personas/mantenerPlanSeleccionado') ?>",
            type: "GET",
            dataType: "json",
            data: {
                idPersona: idPersona
            },
            success: function(response) {
                if (response.status === 'success') {
                    
                    IdPlanSelect.val(response.data.id_plan).trigger('change');
                    IdTipoPlanSelect.val(response.data.id_tipo_plan).trigger('change');
                    console.log("Plan actual cargado:", response.data);
                }else {
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", status, error);
                console.error("Respuesta del servidor:", xhr.responseText);
            }
        });
    }

    
    function registrarFechaInicio(){
        var fechaInicio = $('#fechaInicio').val();

            $.ajax({
                url: "<?= base_url('personas/registrarFechaInicio') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    fecha_inicio: fechaInicio
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message || "Fecha de inicio registrada correctamente.");
                    } else {
                        alert(response.message || "Error al registrar la fecha de inicio.");
                    }
                },
                error: function() {
                    alert("Error al registrar la fecha de inicio.");
                }
            });
    }
    
function calcularVencimiento() {
    var fechaInicioVal = $('#fechaInicio').val();
    var tipoPlan = $('#selectTipoPlan').val(); // 1 = Mensual, 2 = Trimestral, 3 = Semestral, 4 = Anual

    if (!fechaInicioVal || !tipoPlan) return;

    var fecha = new Date(fechaInicioVal + 'T00:00:00'); // Evita desfase de zona horaria

    // Sumamos tiempo dependiendo del id_tipo_plan (Basado en tu tabla tipo_plan)
    if (tipoPlan == "1") { // Mensual
        fecha.setMonth(fecha.getMonth() + 1);
    } else if (tipoPlan == "2") { // Trimestral
        fecha.setMonth(fecha.getMonth() + 3);
    } else if (tipoPlan == "3") { // Semestral
        fecha.setMonth(fecha.getMonth() + 6);
    } else if (tipoPlan == "4") { // Anual
        fecha.setFullYear(fecha.getFullYear() + 1);
    }

    // Formatear la fecha a YYYY-MM-DD para asignarla al input
    var yyyy = fecha.getFullYear();
    var mm = String(fecha.getMonth() + 1).padStart(2, '0');
    var dd = String(fecha.getDate()).padStart(2, '0');

    $('#fechaVencimiento').val(`${yyyy}-${mm}-${dd}`);
}

// Escuchar cambios en los inputs para recalcular en tiempo real
$(document).on('change', '#fechaInicio, #selectTipoPlan', function() {
    calcularVencimiento();
});


/* ═══════════════ ANIMACIONES GSAP ═══════════════ */
function pdsgAnimarTabla() {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 768;
    var items = esMobile
        ? gsap.utils.toArray('.pdsg-mcard')
        : gsap.utils.toArray('#tablaClientes tbody tr');

    if (!items.length) return;
    if (window.__pdsgTl) window.__pdsgTl.kill();

    window.__pdsgTl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    gsap.set(items, { opacity: 0, y: 14 });
    window.__pdsgTl.to(items, { opacity: 1, y: 0, duration: .45, stagger: .06 });
}

// Cerrar modal con slide out + fade
function pdsgModalCerrar() {
    var modal = $('#modalVerPersona');
    if (!modal.hasClass('show')) return;

    if (typeof gsap !== 'undefined') {
        var content = modal.find('.modal-content');
        if (window.__pdsgCloseTween) window.__pdsgCloseTween.kill();
        gsap.to(content, {
            y: 40, opacity: 0, scale: .98, duration: .22, ease: 'power2.in',
            overwrite: true,
            onComplete: function () {
                window.__pdsgCloseTween = null;
                bootstrap.Modal.getInstance(modal[0])?.hide();
            }
        });
    } else {
        bootstrap.Modal.getInstance(modal[0])?.hide();
    }
}

// Render del modal Ver con animación GSAP (entrada como "página")
$(document).on('show.bs.modal', '#modalVerPersona', function () {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 480;

    gsap.set('#modalVerPersona .pdsg-modal-bar', { opacity: 0, y: 12 });
    gsap.set('#modalVerPersona .pdsg-modal-scroll', { opacity: 0, y: 20 });
    gsap.set('#modalVerPersona .pdsg-modal-scroll .pdsg-modal-group', { opacity: 0, y: 12 });

    if (esMobile) {
        var vh = window.innerHeight;
        gsap.set('#modalVerPersona .pdsg-modal-content', { y: vh, autoAlpha: 1 });
        gsap.set('#modalVerPersona .pdsg-modal-close', { opacity: 0, scale: .5, rotate: -90 });
    } else {
        gsap.set('#modalVerPersona .pdsg-modal-content', { y: 40, autoAlpha: 0 });
    }
});

$(document).on('shown.bs.modal', '#modalVerPersona', function () {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 480;
    var vh = window.innerHeight;

    window.__pdsgModalTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    // Desktop / tablet: entra con fade + slide arriba; mobile: ya viene preparado con y=vh en show
    if (!esMobile) gsap.set('#modalVerPersona .pdsg-modal-content', { y: 40, autoAlpha: 0 });

    window.__pdsgModalTl
        .to('#modalVerPersona .pdsg-modal-content', { y: 0, autoAlpha: 1, duration: .45, ease: 'power3.out' })
        .fromTo('#modalVerPersona .pdsg-modal-close',
            { opacity: 0, scale: .5, rotate: -90 },
            { opacity: 1, scale: 1, rotate: 0, duration: .3 }, '-=.15')
        .to('#modalVerPersona .pdsg-modal-bar', { opacity: 1, y: 0, duration: .3 }, '-=.25')
        .to('#modalVerPersona .pdsg-modal-scroll', { opacity: 1, y: 0, duration: .25 }, '-=.2')
        .fromTo('#modalVerPersona .pdsg-modal-scroll .pdsg-modal-group',
            { opacity: 0, y: 12 },
            { opacity: 1, y: 0, duration: .3, stagger: .05 }, '-=.15');
});;

// Sustituir el cierre nativo por el animado
$(document).on('click', '#modalVerPersona .pdsg-modal-close', function (e) {
    e.preventDefault();
    pdsgModalCerrar();
});
$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalVerPersona').hasClass('show')) {
        pdsgModalCerrar();
    }
});
// Clic en la zona oscura fuera del modal
$(document).on('mousedown', '#modalVerPersona', function (e) {
    if (e.target === this || $(e.target).hasClass('modal-dialog')) {
        pdsgModalCerrar();
    }
});
// Si Bootstrap cierra sin pasar por nosotros, reseteamos
$('#modalVerPersona').on('hidden.bs.modal', function () {
    if (typeof gsap === 'undefined') return;
    gsap.set('#modalVerPersona .pdsg-modal-content', { clearProps: 'all', y: 0, autoAlpha: 1 });
    gsap.set('#modalVerPersona .pdsg-modal-bar', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerPersona .pdsg-modal-scroll', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerPersona .pdsg-modal-scroll .pdsg-modal-group', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerPersona .pdsg-modal-close', { clearProps: 'all', opacity: 1, scale: 1 });
});

// ============================================================
// Modal Ver Sucursal (desde la tabla de sucursales)
// ============================================================

// Clic en una fila de sucursal -> abre el detalle
$(document).on('click', '#ver_sucursales .pdsg-suc-row', function () {
    var $fila = $(this);
    var suc = {
        nombre:       $fila.data('nombre'),
        codigo:       $fila.data('codigo'),
        representante:$fila.data('representante'),
        tipo:         $fila.data('tipo'),
        telefono:     $fila.data('telefono'),
        correo:       $fila.data('correo'),
        direccion:    $fila.data('direccion'),
        estado:       Number($fila.data('estado'))
    };

    $('#suc_nombre').text(suc.nombre || '-');
    $('#suc_detalle_sub').text(suc.codigo ? 'Código: ' + suc.codigo : 'Sucursal');
    $('#suc_codigo').text(suc.codigo || '-');
    $('#suc_representante').text(suc.representante || '-');
    $('#suc_tipo').text(suc.tipo || '-');
    $('#suc_telefono').text(suc.telefono || '-');
    $('#suc_correo').text(suc.correo || '-');
    $('#suc_direccion').text(suc.direccion || '-');

    var n = (suc.nombre || 'S').trim().split(/\s+/);
    var iniciales = ((n[0] || 'S').charAt(0) + (n[1] ? n[1].charAt(0) : '')).toUpperCase();
    $('#suc_avatar').text(iniciales);

    var chip = $('#suc-chip');
    if (suc.estado == 1) {
        chip.attr('class', 'pdsg-chip pdsg-chip-on').html('<i class="fa-solid fa-circle"></i> Activo');
    } else {
        chip.attr('class', 'pdsg-chip pdsg-chip-off').html('<i class="fa-solid fa-circle"></i> Inactivo');
    }

    var modal = new bootstrap.Modal(document.getElementById('modalVerSucursal'));
    modal.show();
});

function pdsgModalSucursalCerrar() {
    var modal = $('#modalVerSucursal');
    if (!modal.hasClass('show')) return;

    if (typeof gsap !== 'undefined') {
        gsap.to(modal.find('.modal-content'), {
            y: 40, opacity: 0, scale: .98, duration: .22, ease: 'power2.in',
            overwrite: true,
            onComplete: function () {
                bootstrap.Modal.getInstance(modal[0])?.hide();
            }
        });
    } else {
        bootstrap.Modal.getInstance(modal[0])?.hide();
    }
}

// Animaciones GSAP de entrada/salida para el modal de sucursal
$(document).on('show.bs.modal', '#modalVerSucursal', function () {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 480;
    gsap.set('#modalVerSucursal .pdsg-modal-bar', { opacity: 0, y: 12 });
    gsap.set('#modalVerSucursal .pdsg-modal-scroll', { opacity: 0, y: 20 });
    gsap.set('#modalVerSucursal .pdsg-modal-scroll .pdsg-modal-group', { opacity: 0, y: 12 });

    if (esMobile) {
        gsap.set('#modalVerSucursal .pdsg-modal-content', { y: window.innerHeight, autoAlpha: 1 });
        gsap.set('#modalVerSucursal .pdsg-modal-close', { opacity: 0, scale: .5, rotate: -90 });
    } else {
        gsap.set('#modalVerSucursal .pdsg-modal-content', { scale: .88, autoAlpha: 0 });
    }
});

$(document).on('shown.bs.modal', '#modalVerSucursal', function () {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 480;

    window.__pdsgSucTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    if (esMobile) {
        window.__pdsgSucTl
            .to('#modalVerSucursal .pdsg-modal-content', { y: 0, autoAlpha: 1, duration: .5, ease: 'power4.out' })
            .to('#modalVerSucursal .pdsg-modal-bar', { opacity: 1, y: 0, duration: .3 }, '-=.3')
            .fromTo('#modalVerSucursal .pdsg-modal-close',
                { opacity: 0, scale: .5, rotate: -90 },
                { opacity: 1, scale: 1, rotate: 0, duration: .3 }, '-=.2')
            .to('#modalVerSucursal .pdsg-modal-scroll', { opacity: 1, y: 0, duration: .25 }, '-=.25')
            .fromTo('#modalVerSucursal .pdsg-modal-scroll .pdsg-modal-group',
                { opacity: 0, y: 12 },
                { opacity: 1, y: 0, duration: .3, stagger: .05 }, '-=.2');
    } else {
        gsap.set('#modalVerSucursal .pdsg-modal-content', { scale: .88, autoAlpha: 0 });
        window.__pdsgSucTl
            .to('#modalVerSucursal .pdsg-modal-content', { scale: 1, autoAlpha: 1, duration: .42, ease: 'back.out(1.5)' })
            .to('#modalVerSucursal .pdsg-modal-bar', { opacity: 1, y: 0, duration: .3 }, '-=.25')
            .fromTo('#modalVerSucursal .pdsg-modal-close',
                { opacity: 0, scale: .5, rotate: -90 },
                { opacity: 1, scale: 1, rotate: 0, duration: .3 }, '-=.2')
            .to('#modalVerSucursal .pdsg-modal-scroll', { opacity: 1, y: 0, duration: .25 }, '-=.2')
            .fromTo('#modalVerSucursal .pdsg-modal-scroll .pdsg-modal-group',
                { opacity: 0, y: 12 },
                { opacity: 1, y: 0, duration: .3, stagger: .05 }, '-=.15');
    }
});

$(document).on('click', '#modalVerSucursal .pdsg-modal-close', function (e) {
    e.preventDefault();
    pdsgModalSucursalCerrar();
});
$(document).on('mousedown', '#modalVerSucursal', function (e) {
    if (e.target === this || $(e.target).hasClass('modal-dialog')) {
        pdsgModalSucursalCerrar();
    }
});
$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalVerSucursal').hasClass('show')) {
        pdsgModalSucursalCerrar();
    }
});
$('#modalVerSucursal').on('hidden.bs.modal', function () {
    if (typeof gsap === 'undefined') return;
    gsap.set('#modalVerSucursal .pdsg-modal-content', { clearProps: 'all', scale: 1, y: 0, autoAlpha: 1 });
    gsap.set('#modalVerSucursal .pdsg-modal-bar', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerSucursal .pdsg-modal-scroll', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerSucursal .pdsg-modal-scroll .pdsg-modal-group', { clearProps: 'all', opacity: 1, y: 0 });
    gsap.set('#modalVerSucursal .pdsg-modal-close', { clearProps: 'all', opacity: 1, scale: 1 });
});

// Animar al pintar
$(document).ajaxComplete(function () { pdsgAnimarTabla(); });
$(window).on('resize', function () {
    if (typeof gsap === 'undefined') return;
    var esMobile = window.innerWidth <= 768;
    if (esMobile) { $('#tablaClientes').hide(); $('#pdsgMlist').show(); }
    else { $('#pdsgMlist').hide(); $('#tablaClientes').show(); }
});
pdsgAnimarTabla();

// ════════════════════════════════════════════════════
// MODAL: REGISTRAR / EDITAR PERSONA (== personasadd)
// iframe con precarga: se carga en segundo plano para
// que el clic abra el modal al instante.
// ════════════════════════════════════════════════════
var modalPersonaAdd = document.getElementById('modalPersonaAdd');
var modalPersonaAddFrame = document.getElementById('modalPersonaAddFrame');
var modalPersonaAddLoading = document.getElementById('modalPersonaAddLoading');
var pdsgAddTl = null;
var pdsgFrameUrl = null;   // URL que ya terminó de cargar en el iframe
var pdsgFrameCargando = null; // URL en curso de carga

function pdsgUrlModal(base) {
    var sep = base.indexOf('?') === -1 ? '?' : '&';
    return base + sep + 'modal=1';
}

// Carga (o recarga) el iframe en segundo plano. No toca el modal.
function pdsgCargarFrame(target) {
    if (!modalPersonaAddFrame) return;
    if (pdsgFrameCargando === target) return; // ya en curso
    pdsgFrameCargando = target;
    var visible = modalPersonaAdd.classList.contains('show');
    if (visible) modalPersonaAddLoading.hidden = false;
    modalPersonaAddFrame.onload = function () {
        if (pdsgFrameCargando !== target) return; // llegó una carga vieja
        pdsgFrameCargando = null;
        pdsgFrameUrl = target;
        modalPersonaAddLoading.hidden = true;
    };
    modalPersonaAddFrame.src = target;
}

function pdsgPrecargarAnadir() {
    pdsgCargarFrame(pdsgUrlModal('<?= base_url('personasadd') ?>'));
}

function abrirModalPersonaAdd(url) {
    if (!modalPersonaAddFrame || modalPersonaAdd.classList.contains('show')) return;
    var target = pdsgUrlModal(url || '<?= base_url('personasadd') ?>');
    pdsgResetAnimacionAdd();
    if (pdsgFrameUrl === target && !pdsgFrameCargando) {
        // Ya estaba precargado: abre al instante, sin spinner.
        modalPersonaAddLoading.hidden = true;
    } else {
        pdsgCargarFrame(target);
    }
    bootstrap.Modal.getOrCreateInstance(modalPersonaAdd).show();
}

function pdsgAnimarAdd() {
    pdsgResetAnimacionAdd();
    if (typeof gsap === 'undefined' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    pdsgAddTl = gsap.fromTo(modalPersonaAdd.querySelector('.pdsg-add-modal-content'),
        { scale: .98, opacity: 0, y: 12 },
        { scale: 1, opacity: 1, y: 0, duration: .28, ease: 'power2.out',
          clearProps: 'transform,opacity,visibility' });
}

function pdsgResetAnimacionAdd() {
    if (pdsgAddTl) { pdsgAddTl.kill(); pdsgAddTl = null; }
    if (typeof gsap !== 'undefined') {
        var content = modalPersonaAdd.querySelector('.pdsg-add-modal-content');
        gsap.killTweensOf(content);
        gsap.set(content, { clearProps: 'transform,opacity,visibility' });
    }
}

function pdsgCerrarAdd() {
    bootstrap.Modal.getInstance(modalPersonaAdd)?.hide();
}

modalPersonaAdd.addEventListener('shown.bs.modal', pdsgAnimarAdd);
modalPersonaAdd.addEventListener('hide.bs.modal', pdsgResetAnimacionAdd);

// Al cerrar: el iframe sigue vivo, pero se recarga en segundo plano
// para que la próxima apertura muestre un formulario limpio ya cargado.
modalPersonaAdd.addEventListener('hidden.bs.modal', function () {
    pdsgResetAnimacionAdd();
    modalPersonaAddLoading.hidden = true;
    if (pdsgFrameUrl) pdsgCargarFrame(pdsgFrameUrl);
});

// Precarga al cargar la página (en reposo) y al pasar el cursor por Añadir.
if ('requestIdleCallback' in window) {
    requestIdleCallback(pdsgPrecargarAnadir, { timeout: 2500 });
} else {
    setTimeout(pdsgPrecargarAnadir, 1200);
}
$(document).on('mouseenter focus', '#btnAnadir', pdsgPrecargarAnadir);

// Aceptar únicamente mensajes del formulario que pertenece a este modal.
window.addEventListener('message', function (e) {
    if (e.origin !== window.location.origin || e.source !== modalPersonaAddFrame.contentWindow) return;
    if (!e.data || typeof e.data !== 'object') return;
    if (e.data.tipo === 'pdsg:cerrarModalPersona') {
        pdsgCerrarAdd();
    } else if (e.data.tipo === 'pdsg:personaGuardada') {
        pdsgCerrarAdd();
        cargarClientes();
        cargarContadores();
        // Refrescar el formulario en segundo plano para la próxima apertura.
        if (pdsgFrameUrl) pdsgCargarFrame(pdsgFrameUrl);
    }
});

// Delegación: el botón Añadir del header redirige al modal
$(document).on('click', '#btnAnadir', function () {
    abrirModalPersonaAdd();
});

// Delegación: botones "Editar" de la tabla abren el mismo modal con los datos
$(document).on('click', '.pdsg-action-edit', function (e) {
    e.preventDefault();
    var href = $(this).attr('href') || '';
    var id = href.substring(href.lastIndexOf('/') + 1);
    if (!id) return;
    abrirModalPersonaAdd('<?= base_url('personasadd') ?>' + '/' + id);
});

</script>