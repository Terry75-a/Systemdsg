(function ($) {
    'use strict';

    const cfg = window.PDSG_LISTA_CFG || {};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': cfg.csrfHash || ''
        }
    });

    var estadoActual = 1;
    var todosClientes = [];
    var paginaActual = 1;
    var POR_PAGINA = 5;

    // ── Cargar datos y pintar tabla ──
    function cargarClientes() {
        $.getJSON(cfg.urlListar, { estado: estadoActual })
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

    function cargarContadores() {
        $.getJSON(cfg.urlListar, { estado: 1 })
            .done(function (r) { $('#countActivos').text((r.data || []).length); });
        $.getJSON(cfg.urlListar, { estado: 0 })
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

    function escHtml(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    function inicialesSucursal(nombre) {
        var n = (nombre || '').trim().split(/\s+/);
        return ((n[0] || 'S').charAt(0) + (n[1] ? n[1].charAt(0) : '')).toUpperCase();
    }

    /**
     * Tarjeta de sucursal (reemplaza a la fila de tabla).
     * - En PC: tarjeta ancha con todos los datos a la vista.
     * - En móvil: solo lo esencial; al tocarla abre el modal con el detalle.
     */
    function tarjetaSucursal(s, index) {
        var chip = (s.estado == 1)
            ? '<span class="pdsg-chip pdsg-chip-on"><i class="fa-solid fa-circle"></i> Activo</span>'
            : '<span class="pdsg-chip pdsg-chip-off"><i class="fa-solid fa-circle"></i> Inactivo</span>';

        var campos = [
            ['fa-solid fa-phone', 'Teléfono', s.telefono, ''],
            ['fa-solid fa-envelope', 'Correo', s.correo, ' pdsg-suc-field--extra'],
            ['fa-solid fa-user-tie', 'Representante', s.representante, ' pdsg-suc-field--extra'],
            ['fa-solid fa-tags', 'Tipo', s.tipo, ' pdsg-suc-field--extra'],
            ['fa-solid fa-hashtag', 'Código cliente', s.codigo_cliente, ' pdsg-suc-field--extra pdsg-suc-field--mono']
        ];

        var htmlCampos = campos.map(function (c) {
            return `
                <div class="pdsg-suc-field${c[3]}">
                    <span class="pdsg-suc-field-label"><i class="${c[0]}"></i> ${c[1]}</span>
                    <span class="pdsg-suc-field-value">${escHtml(c[2]) || '—'}</span>
                </div>`;
        }).join('');

        return `
            <article class="pdsg-suc-card pdsg-suc-row" role="button" tabindex="0"
                data-index="${index}"
                data-nombre="${escHtml(s.nombre_sucursal || '')}"
                data-codigo="${escHtml(s.codigo_cliente || '')}"
                data-representante="${escHtml(s.representante || '')}"
                data-tipo="${escHtml(s.tipo || '')}"
                data-telefono="${escHtml(s.telefono || '')}"
                data-correo="${escHtml(s.correo || '')}"
                data-direccion="${escHtml(s.direccion || '')}"
                data-estado="${escHtml(s.estado || 0)}"
                aria-label="Ver detalle de la sucursal ${escHtml(s.nombre_sucursal || '')}"
                title="Ver detalle de la sucursal">
                <div class="pdsg-suc-top">
                    <span class="pdsg-suc-avatar">${inicialesSucursal(s.nombre_sucursal)}</span>
                    <div class="pdsg-suc-id">
                        <span class="pdsg-suc-nombre">${escHtml(s.nombre_sucursal) || '-'}</span>
                        <span class="pdsg-suc-dir"><i class="fa-solid fa-location-dot"></i><span class="pdsg-suc-dir-txt">${escHtml(s.direccion) || '—'}</span></span>
                    </div>
                    ${chip}
                    <span class="pdsg-suc-chevron"><i class="fa-solid fa-chevron-right"></i></span>
                </div>
                <div class="pdsg-suc-fields">${htmlCampos}</div>
                <div class="pdsg-suc-foot"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver detalle completo</div>
            </article>
        `;
    }

    function pintarTabla() {
        var total = todosClientes.length;
        var totalPaginas = Math.max(1, Math.ceil(total / POR_PAGINA));
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        var inicio = (paginaActual - 1) * POR_PAGINA;
        var filas = todosClientes.slice(inicio, inicio + POR_PAGINA);

        var esMobile = window.innerWidth <= 768;
        var $empty =$('#pdsgEmpty');

        if (!filas.length) {
            $empty.removeAttr('hidden');$('#pdsgPagination').attr('hidden', true);
            $('#pdsgMlist').empty();
            $('#tablaClientes tbody').empty();
            return;
        }
        $empty.attr('hidden', true);

        if (esMobile) {
            var $list =$('#pdsgMlist').empty();
            $('#tablaClientes').hide();
            $list.show();$.each(filas, function (_, c) {
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
                + `<a href="${cfg.urlPersonasAdd}/${c.id_persona}" class="pdsg-action pdsg-action-edit" title="Editar"><i class="fa-solid fa-pen-to-square"></i><span>Editar</span></a>`
                + `<button class="pdsg-action pdsg-action-danger" title="Eliminar" onclick="cambiarEstado(${c.id_persona}, 0, this)"><i class="fa-solid fa-trash"></i><span>Eliminar</span></button>`;
        }
        return `<button class="pdsg-action pdsg-action-on" title="Activar" onclick="cambiarEstado(${c.id_persona}, 1, this)"><i class="fa-solid fa-check"></i><span>Activar</span></button>`
            + `<button class="pdsg-action pdsg-action-danger" title="Borrar definitivamente" onclick="borrarPersona(${c.id_persona}, this)"><i class="fa-solid fa-ban"></i><span>Borrar</span></button>`;
    }

    function pintarPaginacion(total) {
        var $pag =$('#pdsgPagination');
        if (total === 0) { $pag.attr('hidden', true); return; }$pag.removeAttr('hidden');

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

    $('#pdsgPages').on('click', '.pdsg-page, .pdsg-page-nav', function () {
        if ($(this).is(':disabled')) return;
        var p = parseInt($(this).data('pagina'), 10);
        var totalPaginas = Math.max(1, Math.ceil(todosClientes.length / POR_PAGINA));
        if (p < 1 || p > totalPaginas) return;
        paginaActual = p;
        pintarTabla();
    });

    $(document).ready(function () {
        cargarContadores();
        cargarClientes();

        if (typeof showToast === 'function') {
            showToast('Clientes nuevos', 'success');
        }

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

        $('#modalPlan').on('show.bs.modal', function () {
            cargarPlanesDisponibles();
            cargarTiposPlan();
            setTimeout(function () {
                mantenerPlanSeleccionado();
            }, 300);
        });

        $('#btnGuardarPlan').on('click', function () {
            asignarPlan();
        });

        $(document).on('click', '.btn-ver-plan', function () {
            var idPersona = $(this).data('id');$('#idEmpresaModal').val(idPersona);
            if (typeof obtenerNombrePersona === 'function') {
                obtenerNombrePersona(idPersona);
            }
        });
    });

    window.cambiarEstado = function (idPersona, nuevoEstado, boton) {
        if (!idPersona) {
            alert("ID no válido.");
            return;
        }

        var textoConfirmacion = (nuevoEstado === 0) ? "¿Estás seguro de dar de baja a esta persona?" : "¿Estás seguro de activar de nuevo a esta persona?";

        confirmDsg({ message: textoConfirmacion, okLabel: (nuevoEstado === 0 ? 'Dar de baja' : 'Activar'), icon: (nuevoEstado === 0 ? 'person_off' : 'person_check') }).then(function (ok) {
            if (!ok) return;
            var fila = $(boton).closest('tr, .pdsg-mcard');

            var csrfName = window.PDSG_LISTA_CFG?.csrfName || 'csrf_test_name';
        var csrfHash = window.PDSG_LISTA_CFG?.csrfHash || '';

        var payload = { 
            estado: nuevoEstado 
        };
        // Incluir el token CSRF en la data
        if (csrfHash) {
            payload[csrfName] = csrfHash;
        }

            $.ajax({
                url: cfg.urlEliminar + '/' + idPersona,
                type: "POST",
                dataType: "json",
                headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
                data: payload,
                success: function (response) {
                // Actualizar el token CSRF si el backend envía uno nuevo
                if (response.csrfHash) {
                    window.PDSG_LISTA_CFG.csrfHash = response.csrfHash;
                }

                if (response.status === 'success') {
                    if (fila && fila.length) fila.remove();
                    cargarClientes();
                    cargarContadores();
                    alert(response.message || "Estado actualizado con éxito.");
                } else {
                    alert(response.message || "No se pudo cambiar el estado en la base de datos.");
                }
            },
                error: function (xhr) {
                if (xhr.status === 403) {
                    alert("Error 403: Sesión o token CSRF expirado. Recarga la página.");
                } else {
                    alert("Error de comunicación con el servidor.");
                }
            }
            });
        });
    };

    $('#modalVerPersona').on('show.bs.modal', function (event) {
        var boton = $(event.relatedTarget);
        var id = boton.data('id');

        var nombre = boton.data('nombre') || '';
        $('#ver_nombre').text(nombre || '-');
        $('#ver_dni').text(boton.data('dni') || '-');
        $('#ver_telefono').text(boton.data('telefono') || '-');
        $('#ver_direccion').text(boton.data('direccion') || '-');

        var n = nombre.trim().split(/\s+/);
        var iniciales = ((n[0] || '?').charAt(0) + (n[1] ? n[1].charAt(0) : '')).toUpperCase();
        $('#ver_avatar').text(iniciales);
        var chip = $('#pdsg-modal-chip');
        if (estadoActual == 1) {
            chip.attr('class', 'pdsg-chip pdsg-chip-on').html('<i class="fa-solid fa-circle"></i> Activo');
        } else {
            chip.attr('class', 'pdsg-chip pdsg-chip-off').html('<i class="fa-solid fa-circle"></i> Inactivo');
        }

        $.ajax({
            url: cfg.urlDetalle,
            type: "GET",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    var p = response.data;
                    $('#ver_apellido_paterno').text(p.apellido_paterno || '-');
                    $('#ver_apellido_materno').text(p.apellido_materno || '-');
                    $('#ver_ruc').text(p.ruc || '-');
                    $('#ver_razon_social').text(p.razon_social || '-');
                    $('#ver_departamento').text(p.departamento || '-');
                    $('#ver_provincia').text(p.provincia || '-');
                    $('#ver_distrito').text(p.distrito || '-');

                    var cont = $('#ver_sucursales');
                    cont.empty();

                    var listaSucursales = p.sucursales || [];
                    var contadorSuc = $('#ver_suc_count');
                    if (listaSucursales.length) {
                        contadorSuc.text(listaSucursales.length).removeAttr('hidden');
                    } else {
                        contadorSuc.attr('hidden', true);
                    }

                    if (listaSucursales.length) {
                        listaSucursales.forEach(function (sucursal, index) {
                            cont.append(tarjetaSucursal(sucursal, index));
                        });
                    } else {
                        cont.append(`
                            <div class="pdsg-suc-empty" id="sucursales_vacio">
                                <i class="bi bi-inbox"></i>
                                <span>Sin sucursales registradas</span>
                            </div>
                        `);
                    }
                }
            },
            error: function () {
                console.error("Error al obtener el detalle de la persona.");
            }
        });
    });

    window.borrarPersona = function (idPersona, boton) {
        if (!idPersona) {
            alert("ID no válido.");
            return;
        }

        confirmDsg({
            title: '¿Eliminar permanentemente?',
            message: '¿Estás seguro de eliminar permanentemente a esta persona? Esta acción no se puede deshacer.'
        }).then(function (ok) {
            if (!ok) return;
            var fila = $(boton).closest('tr, .pdsg-mcard');

            $.ajax({
                url: cfg.urlBorrar + '/' + idPersona,
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        if (fila && fila.length) fila.remove();
                        cargarClientes();
                        cargarContadores();
                        alert(response.message || "Persona eliminada permanentemente.");
                    } else {
                        alert("No se pudo eliminar la persona en la base de datos.");
                    }
                },
                error: function () {
                    alert("Error de comunicación con el servidor.");
                }
            });
        });
    };

    function cargarPlanesDisponibles() {
        $.ajax({
            url: cfg.urlPlanes,
            type: "GET",
            dataType: "json",
            success: function (planes) {
                var selectPlan = $('#selectPlan');
                selectPlan.empty();
                planes.forEach(function (plan) {
                    selectPlan.append(`<option value="${plan.id_plan}">${plan.nombre_plan}</option>`);
                });
            },
            error: function () {
                alert("Error al cargar los planes disponibles.");
            }
        });
    }

    function cargarTiposPlan() {
        $.ajax({
            url: cfg.urlTipoPlan,
            type: "GET",
            dataType: "json",
            success: function (tipos) {
                var selectTipoPlan = $('#selectTipoPlan');
                selectTipoPlan.empty();
                tipos.forEach(function (tipo) {
                    selectTipoPlan.append(`<option value="${tipo.id_tipo_plan}">${tipo.nombre_tipo}</option>`);
                });
            },
            error: function () {
                alert("Error al cargar los tipos de plan disponibles.");
            }
        });
    }

    function asignarPlan() {
        var idPlan = $('#selectPlan').val();
        var idTipoPlan = $('#selectTipoPlan').val();
        var idPersona = $('#idEmpresaModal').val();
        var fechaInicio = $('#fechaInicio').val();
        var fechaVencimiento = $('#fechaVencimiento').val();
        var precioCobrado = $('#inputPrecio').val();
        var estadoPago = $('#selectEstadoPago').val();
        var observaciones = $('#txtObservaciones').val();

        if (!idPersona) {
            alert("Error: No se pudo obtener el ID del registro.");
            return;
        }

        $.ajax({
            url: cfg.urlAsignarPlanes,
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
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message || "Plan asignado correctamente.");
                    $('#modalPlan').modal('hide');
                    cargarClientes();
                } else {
                    alert(response.message || "Error al asignar el plan.");
                }
            },
            error: function () {
                alert("Error al asignar el plan.");
            }
        });
    }

    function mantenerPlanSeleccionado() {
        var IdPlanSelect = $('#selectPlan');
        var IdTipoPlanSelect = $('#selectTipoPlan');
        var idPersona = $('#idEmpresaModal').val();

        if (!idPersona) return;

        $.ajax({
            url: cfg.urlMantenerPlan,
            type: "GET",
            dataType: "json",
            data: { idPersona: idPersona },
            success: function (response) {
                if (response.status === 'success') {
                    IdPlanSelect.val(response.data.id_plan).trigger('change');
                    IdTipoPlanSelect.val(response.data.id_tipo_plan).trigger('change');
                }
            }
        });
    }

    function calcularVencimiento() {
        var fechaInicioVal = $('#fechaInicio').val();
        var tipoPlan = $('#selectTipoPlan').val();
        if (!fechaInicioVal || !tipoPlan) return;

        var fecha = new Date(fechaInicioVal + 'T00:00:00');
        if (tipoPlan == "1") {
            fecha.setMonth(fecha.getMonth() + 1);
        } else if (tipoPlan == "2") {
            fecha.setMonth(fecha.getMonth() + 3);
        } else if (tipoPlan == "3") {
            fecha.setMonth(fecha.getMonth() + 6);
        } else if (tipoPlan == "4") {
            fecha.setFullYear(fecha.getFullYear() + 1);
        }

        var yyyy = fecha.getFullYear();
        var mm = String(fecha.getMonth() + 1).padStart(2, '0');
        var dd = String(fecha.getDate()).padStart(2, '0');
        $('#fechaVencimiento').val(`${yyyy}-${mm}-${dd}`);
    }

    $(document).on('change', '#fechaInicio, #selectTipoPlan', calcularVencimiento);

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
        window.__pdsgModalTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

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
    });

    $(document).on('click', '#modalVerPersona .pdsg-modal-close', function (e) {
        e.preventDefault();
        pdsgModalCerrar();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#modalVerPersona').hasClass('show')) {
            pdsgModalCerrar();
        }
    });

    $(document).on('mousedown', '#modalVerPersona', function (e) {
        if (e.target === this || $(e.target).hasClass('modal-dialog')) {
            pdsgModalCerrar();
        }
    });

    $('#modalVerPersona').on('hidden.bs.modal', function () {
        if (typeof gsap === 'undefined') return;
        gsap.set('#modalVerPersona .pdsg-modal-content', { clearProps: 'all', y: 0, autoAlpha: 1 });
        gsap.set('#modalVerPersona .pdsg-modal-bar', { clearProps: 'all', opacity: 1, y: 0 });
        gsap.set('#modalVerPersona .pdsg-modal-scroll', { clearProps: 'all', opacity: 1, y: 0 });
        gsap.set('#modalVerPersona .pdsg-modal-scroll .pdsg-modal-group', { clearProps: 'all', opacity: 1, y: 0 });
        gsap.set('#modalVerPersona .pdsg-modal-close', { clearProps: 'all', opacity: 1, scale: 1 });
    });

    // ── Modal Ver Sucursal ──
    $(document).on('keydown', '#ver_sucursales .pdsg-suc-row', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $(document).on('click', '#ver_sucursales .pdsg-suc-row', function () {
        var $fila =$(this);
        var suc = {
            nombre: $fila.data('nombre'),
            codigo: $fila.data('codigo'),
            representante: $fila.data('representante'),
            tipo: $fila.data('tipo'),
            telefono: $fila.data('telefono'),
            correo: $fila.data('correo'),
            direccion: $fila.data('direccion'),
            estado: Number($fila.data('estado'))
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

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#modalVerSucursal').hasClass('show')) {
            pdsgModalSucursalCerrar();
        }
    });

    $(document).on('mousedown', '#modalVerSucursal', function (e) {
        if (e.target === this || $(e.target).hasClass('modal-dialog')) {
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

    $(document).ajaxComplete(function () { pdsgAnimarTabla(); });$(window).on('resize', function () {
        if (typeof gsap === 'undefined') return;
        var esMobile = window.innerWidth <= 768;
        if (esMobile) { $('#tablaClientes').hide(); $('#pdsgMlist').show(); }
        else { $('#pdsgMlist').hide(); $('#tablaClientes').show(); }
    });
    pdsgAnimarTabla();

    // ── Modal con Iframe: Personasadd ──
    var modalPersonaAdd = document.getElementById('modalPersonaAdd');
    var modalPersonaAddFrame = document.getElementById('modalPersonaAddFrame');
    var modalPersonaAddLoading = document.getElementById('modalPersonaAddLoading');
    var pdsgAddTl = null;
    var pdsgFrameUrl = null;
    var pdsgFrameCargando = null;

    function pdsgUrlModal(base) {
        var sep = base.indexOf('?') === -1 ? '?' : '&';
        return base + sep + 'modal=1';
    }

    function pdsgCargarFrame(target) {
        if (!modalPersonaAddFrame) return;
        if (pdsgFrameCargando === target) return;
        pdsgFrameCargando = target;
        var visible = modalPersonaAdd.classList.contains('show');
        if (visible && modalPersonaAddLoading) modalPersonaAddLoading.hidden = false;
        modalPersonaAddFrame.onload = function () {
            if (pdsgFrameCargando !== target) return;
            pdsgFrameCargando = null;
            pdsgFrameUrl = target;
            if (modalPersonaAddLoading) modalPersonaAddLoading.hidden = true;
        };
        modalPersonaAddFrame.src = target;
    }

    function pdsgPrecargarAnadir() {
        pdsgCargarFrame(pdsgUrlModal(cfg.urlPersonasAdd));
    }

    function abrirModalPersonaAdd(url) {
        if (!modalPersonaAddFrame || modalPersonaAdd.classList.contains('show')) return;
        var target = pdsgUrlModal(url || cfg.urlPersonasAdd);
        pdsgResetAnimacionAdd();
        if (pdsgFrameUrl === target && !pdsgFrameCargando) {
            if (modalPersonaAddLoading) modalPersonaAddLoading.hidden = true;
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
    // 1. Quitar el foco del elemento activo (iframe o botón dentro del modal)
    if (document.activeElement && typeof document.activeElement.blur === 'function') {
        document.activeElement.blur();
    }
    
    // 2. Devolver el foco al botón principal de la página
    const btnAnadir = document.getElementById('btnAnadir');
    if (btnAnadir) btnAnadir.focus();

    // 3. Cerrar el modal con Bootstrap
    bootstrap.Modal.getInstance(modalPersonaAdd)?.hide();
}

    if (modalPersonaAdd) {
    modalPersonaAdd.addEventListener('hide.bs.modal', function () {
        // Asegurar que el foco no permanezca dentro antes de aplicar aria-hidden
        if (document.activeElement && modalPersonaAdd.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
        modalPersonaAdd.addEventListener('shown.bs.modal', pdsgAnimarAdd);
    modalPersonaAdd.addEventListener('hide.bs.modal', pdsgResetAnimacionAdd);
        modalPersonaAdd.addEventListener('hidden.bs.modal', function () {
            pdsgResetAnimacionAdd();
            if (modalPersonaAddLoading) modalPersonaAddLoading.hidden = true;
            if (pdsgFrameUrl) pdsgCargarFrame(pdsgFrameUrl);
        });
    }

    if ('requestIdleCallback' in window) {
        requestIdleCallback(pdsgPrecargarAnadir, { timeout: 2500 });
    } else {
        setTimeout(pdsgPrecargarAnadir, 1200);
    }
    $(document).on('mouseenter focus', '#btnAnadir', pdsgPrecargarAnadir);

    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin) return;
        if (modalPersonaAddFrame && e.source !== modalPersonaAddFrame.contentWindow) return;
        if (!e.data || typeof e.data !== 'object') return;
        if (e.data.tipo === 'pdsg:cerrarModalPersona') {
            pdsgCerrarAdd();
        } else if (e.data.tipo === 'pdsg:personaGuardada') {
            pdsgCerrarAdd();
            cargarClientes();
            cargarContadores();
            if (pdsgFrameUrl) pdsgCargarFrame(pdsgFrameUrl);
        }
    });

    $(document).on('click', '#btnAnadir', function () {
        abrirModalPersonaAdd();
    });

    $(document).on('click', '.pdsg-action-edit', function (e) {
        e.preventDefault();
        var href = $(this).attr('href') || '';
        var id = href.substring(href.lastIndexOf('/') + 1);
        if (!id) return;
        abrirModalPersonaAdd(cfg.urlPersonasAdd + '/' + id);
    });

})(jQuery);