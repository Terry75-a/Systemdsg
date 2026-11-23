<div class="modal fade" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content pdsg-modal-content" id="formAsignarPlan" onsubmit="return false;">

      <!-- CAMPOS OCULTOS NECESARIOS PARA CREAR Y EDITAR -->
      <input type="hidden" id="planIdPago" name="id_pago" value="">
      <input type="hidden" id="idSucursalModal" name="id_sucursal" value="">
      <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">
      <input type="hidden" id="idPersonaHidden" name="id_persona" value="">
      <input type="hidden" name="id_tipo_pago" value="1">

      <!-- Cabecera -->
      <div class="pdsg-modal-bar">
        <span class="pdsg-modal-avatar"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
        <div class="pdsg-modal-head-info">
          <h5 class="pdsg-modal-name" id="modalPlanLabel">Asignar Plan de Servicio</h5>
          <span class="pdsg-modal-sub">Selecciona el plan, periodo y gestiona los planes registrados</span>
        </div>
        <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body">

        <!-- Banner de sucursal / cliente -->
        <div class="alert alert-dark text-white align-items-center mb-3 py-2" role="alert">
          <div>
            Asignando plan a: <strong id="modalClienteNombre">—</strong>
          </div>
        </div>

        <!-- Banner de modo edición (oculto por defecto) -->
        <div id="planBannerEdicion" class="pdsg-edit-banner d-none">
          <div class="d-flex align-items-center gap-2">
            <span class="material-symbols-outlined">edit_note</span>
            <span>Editando plan: <strong id="planEditandoNombre"></strong></span>
          </div>
          <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="btnCancelarEdicionPlan">
            Cancelar edición
          </button>
        </div>

        <!-- Formulario de Plan -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="selectPlan" class="form-label">Seleccionar Plan disponible: <span class="text-danger">*</span></label>
            <select class="form-select" id="selectPlan" name="id_plan" required></select>
          </div>

          <div class="col-md-6">
            <label for="selectTipoPlan" class="form-label">Tipo de plan: <span class="text-danger">*</span></label>
            <select class="form-select" id="selectTipoPlan" name="id_tipo_plan" required></select>
          </div>

          <div class="col-md-6">
            <label for="fechaInicio" class="form-label fw-medium">Fecha de Inicio: <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="col-md-6" id="grupoVencimiento">
            <label for="fechaVencimiento" class="form-label fw-medium">Fecha de Vencimiento:</label>
            <input type="date" class="form-control bg-light" id="fechaVencimiento" name="fecha_vencimiento" readonly placeholder="Cálculo automático">
          </div>

          <div class="col-md-6">
            <label for="inputPrecio" class="form-label">Precio Cobrado (S/): <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required>
          </div>

          <div class="col-md-6">
            <label for="selectEstadoPago" class="form-label fw-medium">Estado del Plan: <span class="text-danger">*</span></label>
            <select class="form-select" id="selectEstadoPago" name="estado" required>
              <option value="1" selected>ACTIVO</option>
              <option value="0">INACTIVO</option>
            </select>
          </div>

          <div class="col-12">
            <label for="txtObservaciones" class="form-label">Observaciones (Opcional):</label>
            <textarea class="form-control" id="txtObservaciones" name="observaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
          </div>
        </div>

        <!-- Tarjeta de Vista Rápida (Se abre al pulsar el ojo "Ver") -->
        <div id="planDetalleCard" class="pdsg-pago-view-card d-none">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold d-flex align-items-center gap-1" style="font-size: 0.85rem; color: #334155;">
              <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span> Detalle del Plan Asignado
            </span>
            <button type="button" class="btn-close" style="font-size: 0.7rem;" id="btnCerrarDetallePlan" aria-label="Cerrar detalle"></button>
          </div>
          <div class="pdsg-pago-view-grid" id="planDetalleContenido"></div>
        </div>

        <!-- GRILLA DE PLANES REGISTRADOS -->
        <section class="mf-sec mt-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="mf-sec-title mb-0" style="font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
              <span class="material-symbols-outlined">table_rows</span> Planes Registrados
              <span class="pdsg-chip pdsg-chip-on ms-2" id="contadorPlanes">0</span>
            </h3>
          </div>

          <div class="pdsg-grid-pagos-wrap">
            <table class="table pdsg-grid-pagos mb-0">
              <thead>
                <tr>
                  <th>Plan / Periodo</th>
                  <th>Monto</th>
                  <th>Vencimiento</th>
                  <th class="text-center">Estado</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody id="tbodyPlanesPagos">
                <tr>
                  <td colspan="5" class="text-center text-muted py-3 small">Cargando registros...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary px-3 py-2" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold" id="btnGuardarPlan">
          <i class="fa-solid fa-floppy-disk me-1"></i> <span id="txtBtnGuardarPlan">Asignar Plan</span>
        </button>
      </div>

    </form>
  </div>
</div>

<style>
#modalPlan .modal-dialog {
    max-width: 680px !important;
    width: calc(100% - 1.5rem);
}
</style>
<style>
/* 1. Ampliar ligeramente el modal para que quepan las 5 columnas cómodamente */
#modalPago .modal-dialog {
    max-width: 650px !important;
    width: calc(100% - 1.5rem);
}

/* 2. Contenedor de la grilla sin desborde horizontal */
.pdsg-grid-pagos-wrap {
    max-height: 230px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 10px;
    background: #ffffff;
}

.pdsg-grid-pagos {
    width: 100%;
    table-layout: fixed; /* Fuerza a que todo entre en el 100% del ancho */
    font-size: 0.82rem;
    vertical-align: middle;
    margin-bottom: 0;
}

/* Distribución exacta de columnas */
.pdsg-grid-pagos th:nth-child(1), .pdsg-grid-pagos td:nth-child(1) { width: 34%; } /* Concepto */
.pdsg-grid-pagos th:nth-child(2), .pdsg-grid-pagos td:nth-child(2) { width: 16%; } /* Monto */
.pdsg-grid-pagos th:nth-child(3), .pdsg-grid-pagos td:nth-child(3) { width: 17%; } /* Vencimiento */
.pdsg-grid-pagos th:nth-child(4), .pdsg-grid-pagos td:nth-child(4) { width: 13%; } /* Estado */
.pdsg-grid-pagos th:nth-child(5), .pdsg-grid-pagos td:nth-child(5) { width: 20%; } /* Acciones */

.pdsg-grid-pagos thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f8fafc;
    color: #64748b;
    font-weight: 600;
    font-size: 0.70rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    padding: 8px 8px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    white-space: nowrap;
}

.pdsg-grid-pagos tbody td {
    padding: 8px 8px;
    color: #1e293b;
    border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    word-wrap: break-word;
}

/* Truncar textos largos como el correo o concepto para que no empujen la tabla */
.pdsg-cell-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

/* Botones de acción compactos y alineados en fila */
.pdsg-pago-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.pdsg-pago-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 7px;
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    transition: all 0.15s ease;
    cursor: pointer;
}
.pdsg-pago-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.pdsg-pago-btn--view {
    color: #0284c7;
    background: rgba(2, 132, 199, 0.08);
    border-color: rgba(2, 132, 199, 0.25);
}
.pdsg-pago-btn--edit {
    color: #d97706;
    background: rgba(217, 119, 6, 0.08);
    border-color: rgba(217, 119, 6, 0.25);
}
.pdsg-pago-btn--del {
    color: #dc2626;
    background: rgba(220, 38, 38, 0.08);
    border-color: rgba(220, 38, 38, 0.25);
}

.pdsg-grid-pagos tbody tr.is-editing {
    background-color: rgba(16, 185, 129, 0.08);
}

.pdsg-edit-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #065f46;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 12px;
    font-size: 0.84rem;
}

.pdsg-pago-view-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
}
.pdsg-pago-view-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 16px;
    font-size: 0.82rem;
}
.pdsg-pago-view-grid .full {
    grid-column: 1 / -1;
}
.pdsg-pago-view-grid small {
    display: block;
    color: #64748b;
    font-size: 0.72rem;
}
</style>