// ── CONFIGURACIÓN DE URL Y TOKENS (Vienen desde window.PDSG_CFG)
const cfg = window.PDSG_CFG || {};
const csrfName = cfg.csrfName || '';
const csrfHash = cfg.csrfHash || '';
const BASE_URL = cfg.baseUrl || '';

const PROVINCIA_EDITAR  = cfg.provinciaEditar || '';
const DISTRITO_EDITAR   = cfg.distritoEditar || '';
const PROVINCIA_EDITARd = cfg.provinciaEmpresa || '';
const DISTRITO_EDITARX  = cfg.distritoEmpresa || '';

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

const tablaSuc = document.getElementById('tablaSucursales');

// ════════════════════════════════════════════════════
// BÚSQUEDA POR DNI
// ════════════════════════════════════════════════════
const btnBuscarDni = document.getElementById('btnBuscarDni');
if (btnBuscarDni) {
    btnBuscarDni.addEventListener('click', async function () {
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
}

const inputDni = document.getElementById('dni');
if (inputDni) {
    inputDni.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnBuscarDni').click();
        }
    });
}

// ════════════════════════════════════════════════════
// BÚSQUEDA POR RUC
// ════════════════════════════════════════════════════
const btnBuscarRuc = document.getElementById('btnBuscarRuc');
if (btnBuscarRuc) {
    btnBuscarRuc.addEventListener('click', async function () {
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
}

const inputRuc = document.getElementById('ruc');
if (inputRuc) {
    inputRuc.addEventListener('input', function () {
        const rucVal = this.value.trim();
        const contenedor = document.getElementById('contenedorSucursales');
        if (/^\d{11}$/.test(rucVal)) {
            contenedor.style.display = 'block';
        } else {
            contenedor.style.display = 'none';
        }
    });
}

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

const depRep = document.getElementById('departamento_rep');
if (depRep) depRep.addEventListener('change', function() { cargarProvincias(this.value); });

const provRep = document.getElementById('provincia_rep');
if (provRep) provRep.addEventListener('change', function() { cargarDistritos(this.value); });

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

const depRepDos = document.getElementById('departamento_repdos');
if (depRepDos) depRepDos.addEventListener('change', function () { cargarProvinciasEmpresa(this.value); });

const provRepDos = document.getElementById('provincia_repdos');
if (provRepDos) provRepDos.addEventListener('change', function () { cargarDistritosEmpresa(this.value); });

// ════════════════════════════════════════════════════
// UBIGEO: SUCURSAL
// ════════════════════════════════════════════════════
async function cargarProvinciasSucursal(idDep, idProvSelected = null, idDistSelected = null) {
    const selectProv = document.getElementById('provincia_sucursal');
    const selectDist = document.getElementById('distrito_sucursal');
    if (!selectProv || !selectDist) return;
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
    if (!selectDist) return;
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

const depSuc = document.getElementById('departamento_sucursal');
if (depSuc) depSuc.addEventListener('change', function () { cargarProvinciasSucursal(this.value); });

const provSuc = document.getElementById('provincia_sucursal');
if (provSuc) provSuc.addEventListener('change', function () { cargarDistritosSucursal(this.value); });

// ════════════════════════════════════════════════════
// GESTIÓN DEL MODAL: CREAR, EDITAR, GUARDAR Y ELIMINAR
// ════════════════════════════════════════════════════
const btnAbrirModalNuevaSuc = document.getElementById('btnAbrirModalNuevaSucursal');
if (btnAbrirModalNuevaSuc) {
    btnAbrirModalNuevaSuc.addEventListener('click', async function () {
        document.getElementById('formSucursal').reset();
        document.getElementById('editandoIndice').value = '';
        document.getElementById('modalSucursalTitulo').textContent = 'Registrar Sucursal';
        document.getElementById('btnGuardarSucursalTexto').textContent = 'Agregar a la Lista';
        document.getElementById('provincia_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
        document.getElementById('distrito_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';

        
        const filas = document.querySelectorAll('#tbodySucursales tr:not(#filaVacia)');
        const ultimaFila = filas.length ? filas[filas.length - 1] : null;

        if (ultimaFila) {
            const indice = ultimaFila.getAttribute('data-indice');
            const getVal = (campo) =>
                ultimaFila.querySelector(`input[name="sucursales[${indice}][${campo}]"]`)?.value || '';

            document.getElementById('nombre_sucursal').value        = getVal('nombre_sucursal');
            document.getElementById('representante_sucursal').value = getVal('representante_sucursal') || getVal('representante');
            document.getElementById('telefono_sucursal').value      = getVal('telefono');
            document.getElementById('correo_sucursal').value        = getVal('correo');
            document.getElementById('direccion_sucursal').value     = getVal('direccion');

            const depId  = getVal('departamento_sucursal');
            const provId = getVal('provincia_sucursal');
            const distId = getVal('distrito_sucursal');

            document.getElementById('departamento_sucursal').value = depId;
            if (depId) {
                await cargarProvinciasSucursal(depId, provId, distId);
            }
            // El código de cliente se deja vacío a propósito (debe ser distinto en cada sucursal)
        }

        const modal = obtenerModalSucursal();
        if (modal) modal.show();
    });
}

if (tablaSuc) {
    tablaSuc.addEventListener('click', async function (e) {
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
}

// Función auxiliar para renderizar el botón de Desactivar (tacho) o Activar (check)
function renderBtnEstadoSucursal(estado) {
    if (String(estado) === '1') {
        return `
            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-del btn-toggle-estado-sucursal" title="Desactivar">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
    } else {
        return `
            <button type="button" class="pdsg-suc-btn pdsg-suc-btn-on btn-toggle-estado-sucursal" title="Activar" style="color: #10b981; background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.25);">
                <i class="fa-solid fa-check"></i>
            </button>
        `;
    }
}

const btnGuardarSucLista = document.getElementById('btnGuardarSucursalLista');
if (btnGuardarSucLista) {
    btnGuardarSucLista.addEventListener('click', function () {
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
                <button type="button" class="pdsg-suc-btn pdsg-suc-btn-payment btn-asignar-tipo-pagos" title="tipopago">
                    <i class="fa-solid fa-money-bill"></i>
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
}

const tbodySuc = document.getElementById('tbodySucursales');
if (tbodySuc) {
    tbodySuc.addEventListener('click', function (e) {
        const btnToggle = e.target.closest('.btn-toggle-estado-sucursal, .btn-eliminar-sucursal');
        if (!btnToggle) return;

        // Evitar submit o propagación indebida
        e.preventDefault();
        e.stopPropagation();

        const fila = btnToggle.closest('tr');
        if (!fila) return;

        const indice = fila.getAttribute('data-indice');
        const inputEstado = fila.querySelector(`input[name="sucursales[${indice}][estado]"]`);
        const tdEstado = fila.querySelector('td.text-center');

        const estadoActual = inputEstado ? String(inputEstado.value) : '1';
        const esActivo = (estadoActual === '1');
        const nuevoEstado = esActivo ? '0' : '1';

        const mensajeConfirm = esActivo 
            ? '¿Desea desactivar esta sucursal?' 
            : '¿Desea activar nuevamente esta sucursal?';

        const ejecutarConfirm = window.confirmDsg 
            || (window.parent && window.parent.confirmDsg) 
            || function(opts) { return Promise.resolve(window.confirm(opts.message || '¿Confirmar?')); };

        ejecutarConfirm({ 
            message: mensajeConfirm,
            okLabel: esActivo ? 'Desactivar' : 'Activar',
            icon: esActivo ? 'power_settings_new' : 'check_circle'
        }).then(function (ok) {
            if (!ok) return;

            // 1. Actualizar el chip y el input oculto de estado
            if (tdEstado) {
                tdEstado.innerHTML = `
                    <span class="pdsg-chip ${nuevoEstado === '1' ? 'pdsg-chip-on' : 'pdsg-chip-off'}">
                        <i class="fa-solid fa-circle"></i> ${nuevoEstado === '1' ? 'Activo' : 'Inactivo'}
                    </span>
                    <input type="hidden" name="sucursales[${indice}][estado]" value="${nuevoEstado}">
                `;
            }

            // 2. Reemplazar el botón (tacho <-> check verde)
            btnToggle.outerHTML = renderBtnEstadoSucursal(nuevoEstado);
        });
    });
}

const modalSucEl = document.getElementById('modalSucursal');
if (modalSucEl) {
    modalSucEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('editandoIndice').value = '';
        document.getElementById('formSucursal').reset();
        document.getElementById('provincia_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
        document.getElementById('distrito_sucursal').innerHTML = '<option value="">--Seleccionar--</option>';
    });
}



// ════════════════════════════════════════════════════
// ASIGNAR PLAN DESDE SUCURSALES
// ════════════════════════════════════════════════════
if (tablaSuc) {
    tablaSuc.addEventListener('click', function (e) {
        const btnAsignar = e.target.closest('.btn-asignar-plan');
        if (!btnAsignar) return;

        const fila = btnAsignar.closest('tr');
        const indice = btnAsignar.getAttribute('data-id-persona') || fila.getAttribute('data-indice');
        const idSucursal = indice ? indice.replace('existing_', '') : '';
        const nombreSucursal = fila.querySelector('.txt-nombre')?.textContent.trim() || '';

        const inputSucursal = document.getElementById('idSucursalModal');
        if (inputSucursal) inputSucursal.value = idSucursal;

        const lblCliente = document.getElementById('modalClienteNombre');
        if (lblCliente) lblCliente.textContent = nombreSucursal;

        cargarPlanesYTipos();
        cargarPagosSucursal(idSucursal, 1, 'modalPlan')

        const modalPlan = obtenerModalPlan();
        if (modalPlan) modalPlan.show();
    });
}

// ════════════════════════════════════════════════════
// FUNCIONES DEL BLOQUE MENSUAL Y VENCIMIENTO
// ════════════════════════════════════════════════════
function esPlanMensual() {
    const sel = document.getElementById('selectTipoPlan');
    if (!sel || sel.selectedIndex < 0) return false;
    const opt = sel.options[sel.selectedIndex];
    return !!opt && opt.textContent.trim().toUpperCase().includes('MENSUAL');
}

function actualizarCamposFin() {
    const radioSeleccionado = document.querySelector('input[name="tipo_fin"]:checked');
    const valor = radioSeleccionado ? radioSeleccionado.value : 'SIN_FIN';

    const inputRep = document.getElementById('numRepeticiones');
    const inputFecha = document.getElementById('fechaFinMensual');

    if (inputRep) {
        inputRep.disabled = (valor !== 'REPETICIONES');
        if (valor === 'REPETICIONES') inputRep.focus();
    }
    if (inputFecha) {
        inputFecha.disabled = (valor !== 'FECHA');
        if (valor === 'FECHA') inputFecha.focus();
    }
}

function actualizarBloqueMensual() {
    const mensual = esPlanMensual();
    const bloqueFin = document.getElementById('bloqueFinMensual');
    const grupoVencimiento = document.getElementById('grupoVencimiento');

    if (bloqueFin) bloqueFin.classList.toggle('d-none', !mensual);
    if (grupoVencimiento) grupoVencimiento.classList.toggle('d-none', mensual);

    if (mensual) {
        bloqueFin.querySelectorAll('input[type="radio"]').forEach(r => r.disabled = false);
        actualizarCamposFin();
    } else if (bloqueFin) {
        bloqueFin.querySelectorAll('input').forEach(i => i.disabled = true);
    }
}

function calcularFechaVencimiento() {
    actualizarBloqueMensual();

    const inputInicio = document.getElementById('fechaInicio');
    const selectTipo = document.getElementById('selectTipoPlan');
    const inputVencimiento = document.getElementById('fechaVencimiento');
    if (!inputInicio || !selectTipo || !inputVencimiento) return;

    if (!inputInicio.value) {
        const hoy = new Date();
        inputInicio.value = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
    }

    if (esPlanMensual()) {
        inputVencimiento.value = '';
        return;
    }

    const selectedOption = selectTipo.options[selectTipo.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        inputVencimiento.value = '';
        return;
    }

    let mesesAAgregar = parseInt(selectedOption.dataset.meses, 10);
    if (isNaN(mesesAAgregar)) {
        const nombreTipo = selectedOption.textContent.trim().toUpperCase();
        if (nombreTipo.includes('TRIMESTRAL')) mesesAAgregar = 3;
        else if (nombreTipo.includes('SEMESTRAL')) mesesAAgregar = 6;
        else if (nombreTipo.includes('ANUAL')) mesesAAgregar = 12;
        else mesesAAgregar = 0;
    }

    if (mesesAAgregar === 0) {
        inputVencimiento.value = '';
        return;
    }

    const [year, month, day] = inputInicio.value.split('-').map(Number);
    const fecha = new Date(year, month - 1, day);
    const diaOriginal = fecha.getDate();

    fecha.setMonth(fecha.getMonth() + mesesAAgregar);

    if (fecha.getDate() !== diaOriginal) {
        fecha.setDate(0);
    }

    const yyyy = fecha.getFullYear();
    const mm = String(fecha.getMonth() + 1).padStart(2, '0');
    const dd = String(fecha.getDate()).padStart(2, '0');
    inputVencimiento.value = `${yyyy}-${mm}-${dd}`;
}

async function cargarPlanesYTipos() {
    try {
        const resPlanes = await fetch(`${BASE_URL}personas/planesDisponibles`);
        const planes = await resPlanes.json();
        const selectPlan = document.getElementById('selectPlan');
        if (selectPlan) {
            selectPlan.innerHTML = '<option value="">-- Seleccionar --</option>';
            planes.forEach(plan => {
                const opt = document.createElement('option');
                opt.value = plan.id_plan;
                opt.textContent = plan.nombre_plan;
                selectPlan.appendChild(opt);
            });
        }

        const resTipos = await fetch(`${BASE_URL}personas/tipoplan`);
        const tipos = await resTipos.json();
        const selectTipo = document.getElementById('selectTipoPlan');
        if (selectTipo) {
            selectTipo.innerHTML = '<option value="">-- Seleccionar --</option>';
            tipos.forEach(tipo => {
                const opt = document.createElement('option');
                opt.value = tipo.id_tipo_plan;
                opt.textContent = tipo.nombre_tipo;

                const nombre = tipo.nombre_tipo.toUpperCase();
                let meses = 0;
                if (nombre.includes('MENSUAL')) meses = 1;
                else if (nombre.includes('TRIMESTRAL')) meses = 3;
                else if (nombre.includes('SEMESTRAL')) meses = 6;
                else if (nombre.includes('ANUAL')) meses = 12;

                opt.dataset.meses = meses;
                selectTipo.appendChild(opt);
            });
        }
        actualizarBloqueMensual();
    } catch (error) {
        console.error('Error cargando planes y tipos:', error);
    }
}

// ════════════════════════════════════════════════════
// ASIGNAR PLAN DESDE SUCURSALES (MODAL PLAN + GRILLA CRUD)
// ════════════════════════════════════════════════════
let cachePlanesPagos = [];

function resetModoEdicionPlan() {
    const formPl = document.getElementById('formAsignarPlan');
    const idSucActual = document.getElementById('idSucursalModal')?.value || '';
    const nombreSucActual = document.getElementById('modalClienteNombre')?.textContent || '';

    if (formPl) formPl.reset();

    if (document.getElementById('idSucursalModal')) {
        document.getElementById('idSucursalModal').value = idSucActual;
    }
    if (document.getElementById('modalClienteNombre')) {
        document.getElementById('modalClienteNombre').textContent = nombreSucActual;
    }
    const inputIdPago = document.getElementById('planIdPago');
    if (inputIdPago) inputIdPago.value = '';

    const inputInicio = document.getElementById('fechaInicio');
    if (inputInicio && !inputInicio.value) {
        const hoy = new Date();
        inputInicio.value = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
    }

    document.getElementById('planBannerEdicion')?.classList.add('d-none');
    document.getElementById('planDetalleCard')?.classList.add('d-none');

    const txtBtn = document.getElementById('txtBtnGuardarPlan');
    if (txtBtn) txtBtn.textContent = 'Asignar Plan';

    document.querySelectorAll('#tbodyPlanesPagos tr').forEach(tr => tr.classList.remove('is-editing'));
    actualizarBloqueMensual();
}

if (tablaSuc) {
    tablaSuc.addEventListener('click', async function (e) {
        const btnAsignar = e.target.closest('.btn-asignar-plan');
        if (!btnAsignar) return;

        const fila = btnAsignar.closest('tr');
        const indice = fila.getAttribute('data-indice') || btnAsignar.getAttribute('data-id-persona') || '';
        const idSucursal = indice.replace('existing_', '');

        if (!idSucursal || indice.startsWith('temp_')) {
            alert('Primero debe guardar los cambios de la persona/sucursal antes de asignar planes.');
            return;
        }

        const nombreSucursal = fila.querySelector('.txt-nombre')?.textContent.trim() || '';

        const inputSucursal = document.getElementById('idSucursalModal');
        if (inputSucursal) inputSucursal.value = idSucursal;

        const lblCliente = document.getElementById('modalClienteNombre');
        if (lblCliente) lblCliente.textContent = nombreSucursal;

        await cargarPlanesYTipos();
        resetModoEdicionPlan();
        cargarPagosSucursal(idSucursal, 1, 'modalPlan');

        const modalPlan = obtenerModalPlan();
        if (modalPlan) modalPlan.show();
    });
}

const btnCancelarEdicionPlan = document.getElementById('btnCancelarEdicionPlan');
if (btnCancelarEdicionPlan) {
    btnCancelarEdicionPlan.addEventListener('click', resetModoEdicionPlan);
}

const btnCerrarDetallePlan = document.getElementById('btnCerrarDetallePlan');
if (btnCerrarDetallePlan) {
    btnCerrarDetallePlan.addEventListener('click', function () {
        document.getElementById('planDetalleCard')?.classList.add('d-none');
    });
}

// Envío del formulario de Planes (Crear y Actualizar)
const formPlan = document.getElementById('formAsignarPlan');
if (formPlan) {
    formPlan.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btnSubmit = document.getElementById('btnGuardarPlan');
        const textoOriginal = btnSubmit.innerHTML;
        const idSucursal = document.getElementById('idSucursalModal')?.value || '';

        if (esPlanMensual()) {
            const radioFin = document.querySelector('[name="tipo_fin"]:checked');
            const tipoFin = radioFin ? radioFin.value : 'SIN_FIN';
            if (tipoFin === 'FECHA') {
                const ini = document.getElementById('fechaInicio').value;
                const fin = document.getElementById('fechaFinMensual')?.value;
                if (!fin || fin <= ini) {
                    alert('La fecha de finalización debe ser posterior a la fecha de inicio.');
                    return;
                }
            }
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Guardando...';

        try {
            const formData = new FormData(this);
            formData.append(csrfName, csrfHash);

            const res = await fetch(`${BASE_URL}personas/guardarPlan`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                }
            });
            const data = await res.json();

            if (data.status === 'success' || data.success) {
                const esEdicion = !!document.getElementById('planIdPago')?.value;
                pdsgToastPlan('success', esEdicion ? 'Plan actualizado' : 'Plan asignado', data.message || '¡Plan registrado correctamente!');
                resetModoEdicionPlan();
                cargarPagosSucursal(idSucursal, 1, 'modalPlan');
            } else {
                pdsgToastPlan('error', 'No se pudo guardar', data.message || 'Error al guardar el plan');
            }
        } catch (err) {
            console.error(err);
            pdsgToastPlan('error', 'Error de conexión', 'No se pudo comunicar con el servidor.');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = textoOriginal;
            const txtBtn = document.getElementById('txtBtnGuardarPlan');
            if (txtBtn && !document.getElementById('planIdPago')?.value) {
                txtBtn.textContent = 'Asignar Plan';
            }
        }
    });
}

// Eventos de la Grilla de Planes: Ver, Editar y Eliminar
const tbodyPlanesPagos = document.getElementById('tbodyPlanesPagos');
if (tbodyPlanesPagos) {
    tbodyPlanesPagos.addEventListener('click', async function (e) {
        const btn = e.target.closest('button[data-accion]');
        if (!btn) return;

        const idPago = btn.getAttribute('data-id');
        const accion = btn.getAttribute('data-accion');
        const pago = cachePlanesPagos.find(p => String(p.id_pago) === String(idPago));
        if (!pago) return;

        // 1. VER DETALLE DEL PLAN
        if (accion === 'ver') {
            const card = document.getElementById('planDetalleCard');
            const cont = document.getElementById('planDetalleContenido');
            if (!card || !cont) return;

            cont.innerHTML = `
                <div><small>Plan</small><strong>${escH(pago.nombre_plan || '—')}</strong></div>
                <div><small>Tipo / Periodo</small><strong>${escH(pago.nombre_tipo || '—')}</strong></div>
                <div><small>Monto</small><strong>S/ ${Number(pago.precio || 0).toFixed(2)}</strong></div>
                <div><small>Estado</small><span>${String(pago.estado) === '1' ? 'Activo' : 'Inactivo'}</span></div>
                <div><small>Fecha de Inicio</small><span>${escH((pago.fecha_de_inicio || '—').slice(0, 10))}</span></div>
                <div><small>Fecha de Vencimiento</small><span>${escH(pago.fecha_de_vencimiento ? pago.fecha_de_vencimiento.slice(0, 10) : 'Sin vencimiento')}</span></div>
                <div class="full"><small>Observaciones</small><span>${escH(pago.observaciones || 'Sin observaciones')}</span></div>
            `;
            card.classList.remove('d-none');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // 2. CARGAR EN FORMULARIO PARA EDITAR PLAN
        if (accion === 'editar') {
            if (document.getElementById('planIdPago')) {
                document.getElementById('planIdPago').value = pago.id_pago;
            }
            if (document.getElementById('selectPlan')) {
                document.getElementById('selectPlan').value = pago.id_plan || '';
            }
            if (document.getElementById('selectTipoPlan')) {
                document.getElementById('selectTipoPlan').value = pago.id_tipo_plan || '';
            }
            if (document.getElementById('fechaInicio')) {
                document.getElementById('fechaInicio').value = (pago.fecha_de_inicio || '').slice(0, 10);
            }

            actualizarBloqueMensual();

            if (document.getElementById('fechaVencimiento')) {
                document.getElementById('fechaVencimiento').value = (pago.fecha_de_vencimiento || '').slice(0, 10);
            }
            if (document.getElementById('inputPrecio')) {
                document.getElementById('inputPrecio').value = Number(pago.precio || 0).toFixed(2);
            }
            if (document.getElementById('selectEstadoPago')) {
                document.getElementById('selectEstadoPago').value = String(pago.estado ?? '1');
            }
            if (document.getElementById('txtObservaciones')) {
                document.getElementById('txtObservaciones').value = pago.observaciones || '';
            }

            const nombreEtiqueta = `${pago.nombre_plan || 'Plan'} (${pago.nombre_tipo || ''})`.trim();
            if (document.getElementById('planEditandoNombre')) {
                document.getElementById('planEditandoNombre').textContent = nombreEtiqueta;
            }
            document.getElementById('planBannerEdicion')?.classList.remove('d-none');

            const txtBtn = document.getElementById('txtBtnGuardarPlan');
            if (txtBtn) txtBtn.textContent = 'Actualizar Plan';

            document.querySelectorAll('#tbodyPlanesPagos tr').forEach(tr => tr.classList.remove('is-editing'));
            btn.closest('tr')?.classList.add('is-editing');
            document.getElementById('selectPlan')?.focus();
        }

        // 3. ELIMINAR PLAN
        if (accion === 'eliminar') {
            const nombrePlan = pago.nombre_plan || 'este plan';
            if (!confirm(`¿Seguro que deseas eliminar el plan "${nombrePlan}"?`)) return;

            try {
                const fd = new FormData();
                fd.append('id_pago', pago.id_pago);
                fd.append(csrfName, csrfHash);

                const res = await fetch(`${BASE_URL}personas/eliminarPago`, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfHash
                    }
                });
                const data = await res.json();
                if (data.status === 'success' || data.success) {
                    pdsgToastPlan('success', 'Plan eliminado', data.message || 'El plan fue eliminado.');
                    resetModoEdicionPlan();
                    const idSuc = document.getElementById('idSucursalModal')?.value || pago.id_sucursal;
                    cargarPagosSucursal(idSuc, 1, 'modalPlan');
                } else {
                    pdsgToastPlan('error', 'Error', data.message || 'No se pudo eliminar el plan.');
                }
            } catch (err) {
                console.error(err);
                pdsgToastPlan('error', 'Error', 'Error de conexión al eliminar.');
            }
        }
    });
}
// ════════════════════════════════════════════════════
// GESTIÓN DE OTROS PAGOS (MODAL PAGO + GRILLA CRUD)
// ════════════════════════════════════════════════════
let cacheOtrosPagos = [];

function resetModoEdicionPago() {
    const formP = document.getElementById('formPago');
    const idSucActual = document.getElementById('pagoIdSucursal')?.value || '';
    if (formP) formP.reset();
    if (document.getElementById('pagoIdSucursal')) {
        document.getElementById('pagoIdSucursal').value = idSucActual;
    }
    const inputIdPago = document.getElementById('pagoIdPago');
    if (inputIdPago) inputIdPago.value = '';

    document.getElementById('pagoBannerEdicion')?.classList.add('d-none');
    document.getElementById('pagoDetalleCard')?.classList.add('d-none');

    const txtBtn = document.getElementById('txtBtnGuardarPago');
    if (txtBtn) txtBtn.textContent = 'Guardar Pago';

    document.querySelectorAll('#tbodyOtrosPagos tr').forEach(tr => tr.classList.remove('is-editing'));
}

if (tablaSuc) {
    tablaSuc.addEventListener('click', function (e) {
        const btnPago = e.target.closest('.btn-asignar-tipo-pagos');
        if (!btnPago) return;

        const fila = btnPago.closest('tr');
        const indice = fila.getAttribute('data-indice');
        const idSucursal = indice ? indice.replace('existing_', '') : '';

        if (!idSucursal || indice.startsWith('temp_')) {
            alert('Primero debe guardar los cambios de la persona/sucursal antes de registrar pagos.');
            return;
        }

        document.getElementById('pagoIdSucursal').value = idSucursal;
        resetModoEdicionPago();
        cargarPagosSucursal(idSucursal, 2, 'modalPago');

        const modalPago = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPago'));
        modalPago.show();
    });
}

const btnCancelarEdicionPago = document.getElementById('btnCancelarEdicionPago');
if (btnCancelarEdicionPago) {
    btnCancelarEdicionPago.addEventListener('click', resetModoEdicionPago);
}

const btnCerrarDetallePago = document.getElementById('btnCerrarDetallePago');
if (btnCerrarDetallePago) {
    btnCerrarDetallePago.addEventListener('click', function () {
        document.getElementById('pagoDetalleCard')?.classList.add('d-none');
    });
}

// Envío del formulario de Otros Pagos (Crear y Actualizar)
const formPago = document.getElementById('formPago');
if (formPago) {
    formPago.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btnGuardarPago');
        const textoOriginal = btnSubmit.innerHTML;

        const concepto = document.getElementById('pagoConcepto').value.trim();
        const monto = document.getElementById('pagoMonto').value.trim();
        const idSucursal = document.getElementById('pagoIdSucursal').value;

        if (!concepto || !monto) {
            alert('Por favor complete el concepto y el monto.');
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Guardando...';

        try {
            const formData = new FormData(this);
            formData.append(csrfName, csrfHash);

            const res = await fetch(`${BASE_URL}personas/guardarOtrosPagos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                }
            });
            const data = await res.json();

            if (data.status === 'success' || data.success) {
                const esEdicion = !!document.getElementById('pagoIdPago')?.value;
                pdsgToastPlan('success', esEdicion ? 'Pago actualizado' : 'Pago registrado', data.message);
                resetModoEdicionPago();
                cargarPagosSucursal(idSucursal, 2, 'modalPago');
            } else {
                pdsgToastPlan('error', 'Error al guardar', data.message || 'No se pudo guardar el pago.');
            }
        } catch (err) {
            console.error(err);
            pdsgToastPlan('error', 'Error de conexión', 'No se pudo comunicar con el servidor.');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = textoOriginal;
            const txtBtn = document.getElementById('txtBtnGuardarPago');
            if (txtBtn && !document.getElementById('pagoIdPago')?.value) {
                txtBtn.textContent = 'Guardar Pago';
            }
        }
    });
}

// Eventos de la Grilla: Ver, Editar y Eliminar
const tbodyOtrosPagos = document.getElementById('tbodyOtrosPagos');
if (tbodyOtrosPagos) {
    tbodyOtrosPagos.addEventListener('click', async function (e) {
        const btn = e.target.closest('button[data-accion]');
        if (!btn) return;

        const idPago = btn.getAttribute('data-id');
        const accion = btn.getAttribute('data-accion');
        const pago = cacheOtrosPagos.find(p => String(p.id_pago) === String(idPago));
        if (!pago) return;

        // 1. VER DETALLE
        if (accion === 'ver') {
            const card = document.getElementById('pagoDetalleCard');
            const cont = document.getElementById('pagoDetalleContenido');
            if (!card || !cont) return;

            cont.innerHTML = `
                <div><small>Concepto</small><strong>${escH(pago.concepto || '—')}</strong></div>
                <div><small>Monto</small><strong>S/ ${Number(pago.precio || 0).toFixed(2)}</strong></div>
                <div><small>Fecha de Inicio</small><span>${escH(pago.fecha_de_inicio || '—')}</span></div>
                <div><small>Fecha de Vencimiento</small><span>${escH(pago.fecha_de_vencimiento || 'Sin vencimiento')}</span></div>
                <div><small>Días de Aviso</small><span>${pago.num_repeticiones ? pago.num_repeticiones + ' días antes' : '—'}</span></div>
                <div><small>Correo Notificación</small><span>${escH(pago.correo_notificacion || '—')}</span></div>
                <div class="full"><small>Observaciones</small><span>${escH(pago.observaciones || 'Sin observaciones')}</span></div>
            `;
            card.classList.remove('d-none');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // 2. CARGAR EN FORMULARIO PARA EDITAR
        if (accion === 'editar') {
            document.getElementById('pagoIdPago').value        = pago.id_pago;
            document.getElementById('pagoConcepto').value      = pago.concepto || '';
            document.getElementById('pagoMonto').value         = Number(pago.precio || 0).toFixed(2);
            document.getElementById('pagoEstado').value        = String(pago.estado ?? '1');
            document.getElementById('pagoFechaVenc').value     = (pago.fecha_de_vencimiento || '').slice(0, 10);
            document.getElementById('pagoDiasAviso').value     = pago.num_repeticiones ?? '';
            document.getElementById('pagoCorreo').value        = pago.correo_notificacion || '';
            document.getElementById('pagoObservaciones').value = pago.observaciones || '';

            document.getElementById('pagoEditandoNombre').textContent = pago.concepto || ('#' + pago.id_pago);
            document.getElementById('pagoBannerEdicion')?.classList.remove('d-none');

            const txtBtn = document.getElementById('txtBtnGuardarPago');
            if (txtBtn) txtBtn.textContent = 'Actualizar Pago';

            document.querySelectorAll('#tbodyOtrosPagos tr').forEach(tr => tr.classList.remove('is-editing'));
            btn.closest('tr')?.classList.add('is-editing');
            document.getElementById('pagoConcepto').focus();
        }

        // 3. ELIMINAR PAGO
        if (accion === 'eliminar') {
            if (!confirm(`¿Seguro que deseas eliminar el pago "${pago.concepto}"?`)) return;

            try {
                const fd = new FormData();
                fd.append('id_pago', pago.id_pago);
                fd.append(csrfName, csrfHash);

                const res = await fetch(`${BASE_URL}personas/eliminarPago`, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfHash
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    pdsgToastPlan('success', 'Eliminado', data.message);
                    resetModoEdicionPago();
                    cargarPagosSucursal(pago.id_sucursal, 2, 'modalPago');
                } else {
                    pdsgToastPlan('error', 'Error', data.message || 'No se pudo eliminar.');
                }
            } catch (err) {
                console.error(err);
                pdsgToastPlan('error', 'Error', 'Error de conexión al eliminar.');
            }
        }
    });
}

function pdsgToastPlan(tipo, titulo, mensaje) {
    var wrap = document.getElementById('pdsgPlanToastWrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'pdsgPlanToastWrap';
        document.body.appendChild(wrap);
    }

    var icon = tipo === 'success' ? 'check_circle' : 'error';
    var el = document.createElement('div');
    el.className = 'pdsg-plan-toast pdsg-plan-toast--' + tipo;
    el.setAttribute('role', 'alert');

    el.innerHTML =
        '<span class="pdsg-plan-toast-icon"><span class="material-symbols-outlined">' + icon + '</span></span>' +
        '<div class="pdsg-plan-toast-body">' +
            '<p class="pdsg-plan-toast-title">' + titulo + '</p>' +
            '<p class="pdsg-plan-toast-msg">' + mensaje + '</p>' +
        '</div>' +
        '<button type="button" class="pdsg-plan-toast-close" aria-label="Cerrar"><span class="material-symbols-outlined">close</span></button>' +
        '<span class="pdsg-plan-toast-bar"></span>';

    wrap.appendChild(el);
    requestAnimationFrame(function () {
        requestAnimationFrame(function () { el.classList.add('is-in'); });
    });

    var dur = 5000;
    var bar = el.querySelector('.pdsg-plan-toast-bar');
    if (bar) bar.style.animationDuration = dur + 'ms';

    function cerrar() {
        if (el.dataset.cerrado) return;
        el.dataset.cerrado = '1';
        el.classList.remove('is-in');
        el.classList.add('is-out');
        setTimeout(function () { el.remove(); }, 320);
    }
    var timer = setTimeout(cerrar, dur);
    el.querySelector('.pdsg-plan-toast-close').addEventListener('click', function () {
        clearTimeout(timer);
        cerrar();
    });
}

// ════════════════════════════════════════════════════
// GUARDADO AJAX IFRAME
// ════════════════════════════════════════════════════
(function () {
    const form = document.getElementById('formPersona');
    const dentroModal = (window.location.search.indexOf('modal=1') !== -1)
        || (typeof window.frameElement !== 'undefined' && window.frameElement !== null);
    if (!form || !dentroModal) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.reportValidity()) return;

        const btn = document.getElementById('btnGuardar');
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Guardando…';

        const data = new FormData(form);
        data.set(csrfName, csrfHash);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
            body: data
        })
        .then(r => r.json())
        .then(resp => {
            if (resp && resp.success) {
                window.parent.postMessage({ tipo: 'pdsg:personaGuardada', message: resp.message || '' }, '*');
            } else {
                alert((resp && resp.message) ? resp.message : 'Ocurrió un error al guardar.');
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        })
        .catch(() => {
            alert('Ocurrió un error de conexión al guardar.');
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
    });

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
// ════════════════════════════════════════════════════
// AUTOCOMPLETAR REPRESENTANTE
// ════════════════════════════════════════════════════
// ════════════════════════════════════════════════════
// AUTOCOMPLETAR REPRESENTANTE (PHP AUTOCOMPLETE)
// ════════════════════════════════════════════════════
(function () {
    const input = document.getElementById('representante_sucursal');
    const menu  = document.getElementById('listaRepresentantes');
    if (!input || !menu) return;

    let timer = null;
    let controller = null;
    let itemsActuales = [];
    let indiceActivo = -1;

    function resaltarCoincidencia(texto, busqueda) {
        const seguro = escH(texto);
        if (!busqueda) return seguro;
        const regex = new RegExp(`(${busqueda.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return seguro.replace(regex, '<mark>$1</mark>');
    }

    function cerrarMenu() {
        menu.classList.add('d-none');
        menu.innerHTML = '';
        itemsActuales = [];
        indiceActivo = -1;
    }

    function seleccionarItem(item) {
        if (!item) return;
        input.value = item.value || '';

        // Si el teléfono o correo de la sucursal están vacíos, los autocompleta
        const inputTel = document.getElementById('telefono_sucursal');
        const inputCor = document.getElementById('correo_sucursal');
        if (inputTel && !inputTel.value.trim() && item.telefono) {
            inputTel.value = item.telefono;
        }
        if (inputCor && !inputCor.value.trim() && item.correo) {
            inputCor.value = item.correo;
        }

        cerrarMenu();
    }

    function pintarMenu(lista, q) {
        itemsActuales = Array.isArray(lista) ? lista : [];
        indiceActivo = -1;

        if (!itemsActuales.length) {
            menu.innerHTML = '<div class="pdsg-ac-empty">Sin coincidencias registradas</div>';
            menu.classList.remove('d-none');
            return;
        }

        menu.innerHTML = itemsActuales.map((item, idx) => {
            const obj = typeof item === 'string'
                ? { label: item, value: item, origen: '' }
                : item;
            itemsActuales[idx] = obj;

            return `
                <div class="pdsg-ac-item" role="option" data-idx="${idx}">
                    <span>${resaltarCoincidencia(obj.label || obj.value, q)}</span>
                    ${obj.origen ? `<span class="pdsg-ac-badge">${escH(obj.origen)}</span>` : ''}
                </div>
            `;
        }).join('');

        menu.classList.remove('d-none');
    }

    async function consultarPhpAutocomplete(q) {
        if (controller) controller.abort();
        controller = new AbortController();

        try {
            const res = await fetch(
                `${BASE_URL}personas/buscarRepresentantes?term=${encodeURIComponent(q)}`,
                {
                    signal: controller.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }
            );
            const data = await res.json();
            pintarMenu(data, q);
        } catch (e) {
            if (e.name !== 'AbortError') console.error('Error en PHP Autocomplete:', e);
        }
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);

        if (q.length < 1) {
            cerrarMenu();
            return;
        }

        timer = setTimeout(() => consultarPhpAutocomplete(q), 220);
    });

    // Mostrar sugerencias iniciales al hacer foco si ya escribió o quiere elegir
    input.addEventListener('focus', function () {
        const q = this.value.trim();
        if (q.length >= 1) {
            consultarPhpAutocomplete(q);
        }
    });

    // Navegación con teclado (Flechas arriba/abajo, Enter, Escape)
    input.addEventListener('keydown', function (e) {
        if (menu.classList.contains('d-none') || !itemsActuales.length) return;

        const nodos = menu.querySelectorAll('.pdsg-ac-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            indiceActivo = (indiceActivo + 1) % nodos.length;
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            indiceActivo = (indiceActivo - 1 + nodos.length) % nodos.length;
        } else if (e.key === 'Enter' && indiceActivo >= 0) {
            e.preventDefault();
            seleccionarItem(itemsActuales[indiceActivo]);
            return;
        } else if (e.key === 'Escape') {
            cerrarMenu();
            return;
        } else {
            return;
        }

        nodos.forEach((n, i) => n.classList.toggle('is-active', i === indiceActivo));
        nodos[indiceActivo]?.scrollIntoView({ block: 'nearest' });
    });

    // Selección con clic
    menu.addEventListener('mousedown', function (e) {
        const itemEl = e.target.closest('.pdsg-ac-item');
        if (!itemEl) return;
        e.preventDefault();
        const idx = parseInt(itemEl.getAttribute('data-idx'), 10);
        seleccionarItem(itemsActuales[idx]);
    });

    // Cerrar al hacer clic fuera o al cerrar el modal
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !menu.contains(e.target)) {
            cerrarMenu();
        }
    });

    document.getElementById('modalSucursal')?.addEventListener('hidden.bs.modal', cerrarMenu);
})();
// ════════════════════════════════════════════════════
// LISTA DE PAGOS YA REGISTRADOS (Plan = tipo 1, Otros pagos = tipo 2)
// ════════════════════════════════════════════════════
function escH(v) {
    return String(v == null ? '' : v).replace(/[&<>"']/g, m =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
}

function obtenerContenedorPagos(modalId) {
    let cont = document.getElementById(modalId + 'Historial');
    if (cont) return cont;
    const body = document.querySelector(`#${modalId} .mf-body, #${modalId} .modal-body`);
    if (!body) return null;
    cont = document.createElement('section');
    cont.id = modalId + 'Historial';
    cont.className = 'mf-sec';
    body.insertBefore(cont, body.firstChild);
    return cont;
}

async function cargarPagosSucursal(idSucursal, tipoPago, modalId) {
    const esPlan = (tipoPago === 1);
    const tbody = document.getElementById(esPlan ? 'tbodyPlanesPagos' : 'tbodyOtrosPagos');
    const badge = document.getElementById(esPlan ? 'contadorPlanes' : 'contadorOtrosPagos');

    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3 small">Cargando registros…</td></tr>';

    try {
        const res = await fetch(
            `${BASE_URL}personas/pagosSucursal?id_sucursal=${idSucursal}&tipo=${tipoPago}`,
            { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
        );
        const pagos = await res.json();
        const lista = Array.isArray(pagos) ? pagos : [];

        if (esPlan) {
            cachePlanesPagos = lista;
        } else {
            cacheOtrosPagos = lista;
        }

        if (badge) badge.textContent = lista.length;

        if (!lista.length) {
            const msgVacio = esPlan
                ? 'No hay planes registrados para esta sucursal.'
                : 'No hay otros pagos registrados para esta sucursal.';
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3 small">${msgVacio}</td></tr>`;
            return;
        }

        tbody.innerHTML = lista.map(p => {
            const activo = String(p.estado) === '1';
            const venc = p.fecha_de_vencimiento ? escH(p.fecha_de_vencimiento.slice(0, 10)) : 'Sin fin';

            const colPrincipal = esPlan
                ? `<div class="fw-semibold pdsg-cell-truncate" title="${escH(p.nombre_plan || '—')}">${escH(p.nombre_plan || '—')}</div>
                   <div class="text-muted pdsg-cell-truncate" style="font-size:0.74rem;">${escH(p.nombre_tipo || '')}</div>`
                : `<div class="fw-semibold pdsg-cell-truncate" title="${escH(p.concepto || '—')}">${escH(p.concepto || '—')}</div>
                   ${p.correo_notificacion ? `<div class="text-muted pdsg-cell-truncate" style="font-size:0.74rem;">${escH(p.correo_notificacion)}</div>` : ''}`;

            return `
                <tr data-id-pago="${p.id_pago}">
                    <td>${colPrincipal}</td>
                    <td class="fw-semibold">S/ ${Number(p.precio || 0).toFixed(2)}</td>
                    <td>${venc}</td>
                    <td class="text-center">
                        <span class="pdsg-chip ${activo ? 'pdsg-chip-on' : 'pdsg-chip-off'}">
                            ${activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="pdsg-pago-actions">
                            <button type="button" class="pdsg-pago-btn pdsg-pago-btn--view" data-accion="ver" data-id="${p.id_pago}" title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button type="button" class="pdsg-pago-btn pdsg-pago-btn--edit" data-accion="editar" data-id="${p.id_pago}" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="pdsg-pago-btn pdsg-pago-btn--del" data-accion="eliminar" data-id="${p.id_pago}" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } catch (e) {
        console.error('Error cargando grilla de pagos/planes:', e);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3 small">No se pudo cargar la lista de registros.</td></tr>';
    }
}
// ════════════════════════════════════════════════════
// INICIALIZACIÓN GLOBAL
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

    const razonSocialInicial = document.getElementById('razon_social')?.value.trim() || '';
    if (razonSocialInicial !== "" || cfg.tieneSucursales) {
        const cont = document.getElementById('contenedorSucursales');
        if (cont) cont.style.display = 'block';
    }

    const fechaInicio = document.getElementById('fechaInicio');
    const selectTipoPlan = document.getElementById('selectTipoPlan');

    if (fechaInicio) fechaInicio.addEventListener('change', calcularFechaVencimiento);
    if (selectTipoPlan) selectTipoPlan.addEventListener('change', calcularFechaVencimiento);

    document.querySelectorAll('input[name="tipo_fin"]').forEach(radio => {
        radio.addEventListener('change', actualizarCamposFin);
    });

    cargarPlanesYTipos();
});
