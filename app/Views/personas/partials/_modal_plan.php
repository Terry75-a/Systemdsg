<div class="modal fade mf-modal" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content mf-content" id="formAsignarPlan" onsubmit="return false;">

      <!-- CAMPOS OCULTOS NECESARIOS PARA CREAR Y EDITAR -->
      <input type="hidden" id="planIdPago" name="id_pago" value="">
      <input type="hidden" id="idSucursalModal" name="id_sucursal" value="">
      <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">
      <input type="hidden" id="idPersonaHidden" name="id_persona" value="">
      <input type="hidden" name="id_tipo_pago" value="1">

      <!-- Cabecera -->
      <div class="mf-head">
        <span class="mf-icon"><span class="material-symbols-outlined">workspace_premium</span></span>
        <div class="mf-head-info">
          <h2 id="modalPlanLabel">Asignar Plan de Servicio</h2>
          <p>Sucursal: <strong id="modalClienteNombre">—</strong></p>
        </div>
        <button type="button" class="mf-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="mf-scroll">
        <div class="mf-body">

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

          <!-- Datos del Plan -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">layers</span> Datos del Plan</h3>
            <div class="mf-grid">
              <div class="mf-f">
                <label for="selectPlan">Plan disponible <span class="mf-req">*</span></label>
                <select class="form-select" id="selectPlan" name="id_plan" required></select>
              </div>

              <div class="mf-f">
                <label for="selectTipoPlan">Tipo de plan <span class="mf-req">*</span></label>
                <select class="form-select" id="selectTipoPlan" name="id_tipo_plan" required></select>
              </div>

              <div class="mf-f">
                <label for="inputPrecio">Precio Cobrado <span class="mf-req">*</span></label>
                <div class="mf-input">
                  <span class="mf-affix">S/</span>
                  <input type="number" step="0.01" min="0" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required>
                </div>
              </div>

              <div class="mf-f">
                <label for="selectEstadoPago">Estado</label>
                <select class="form-select" id="selectEstadoPago" name="estado">
                  <option value="1" selected>ACTIVO</option>
                  <option value="0">INACTIVO</option>
                </select>
              </div>
            </div>
          </section>

          <!-- Vigencia y aviso -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">event_available</span> Vigencia y aviso</h3>
            <div class="mf-grid">
              <div class="mf-f">
                <label for="fechaInicio">Fecha de inicio <span class="mf-req">*</span></label>
                <input type="date" class="form-control" id="fechaInicio" name="fecha_de_inicio" value="<?= date('Y-m-d') ?>" required>
              </div>

              <div class="mf-f" id="grupoVencimiento">
                <label for="fechaVencimiento">Fecha de vencimiento</label>
                <input type="date" class="form-control bg-light" id="fechaVencimiento" name="fecha_de_vencimiento" readonly placeholder="Cálculo automático">
              </div>

              <!-- Bloque para plan MENSUAL -->
              <div class="mf-f mf-span d-none" id="bloqueFinMensual">
                <label class="d-block mb-1">Finalización del plan mensual</label>
                <div class="d-flex flex-wrap gap-3 align-items-center p-2 border rounded bg-light">
                  <div class="form-check mb-0">
                    <input class="form-check-input" type="radio" name="tipo_fin" id="finSinFin" value="SIN_FIN" checked>
                    <label class="form-check-label small" for="finSinFin">Sin fecha de fin</label>
                  </div>
                  <div class="form-check d-flex align-items-center gap-1 mb-0">
                    <input class="form-check-input" type="radio" name="tipo_fin" id="finRepeticiones" value="REPETICIONES">
                    <label class="form-check-label small" for="finRepeticiones">Días aviso:</label>
                    <input type="number" min="1" class="form-control form-control-sm" id="numRepeticiones" name="num_repeticiones" style="width: 75px;" placeholder="5" disabled>
                  </div>
                  <div class="form-check d-flex align-items-center gap-1 mb-0">
                    <input class="form-check-input" type="radio" name="tipo_fin" id="finFecha" value="FECHA">
                    <label class="form-check-label small" for="finFecha">Hasta:</label>
                    <input type="date" class="form-control form-control-sm" id="fechaFinMensual" name="fecha_fin" style="width: 140px;" disabled>
                  </div>
                </div>
              </div>

              <div class="mf-f mf-span">
                <label for="planCorreo">Correo de notificación <span class="mf-hint">Opcional</span></label>
                <div class="mf-input">
                  <span class="mf-affix"><span class="material-symbols-outlined">mail</span></span>
                  <input type="email" class="form-control" id="planCorreo" name="correo_notificacion" placeholder="cliente@ejemplo.com">
                </div>
              </div>

              <div class="mf-f mf-span">
                <label for="txtObservaciones">Observaciones <span class="mf-hint">Opcional</span></label>
                <textarea class="form-control" id="txtObservaciones" name="observaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
              </div>
            </div>
          </section>

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
          <section class="mf-sec">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h3 class="mf-sec-title mb-0">
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
      </div>

      <div class="mf-foot">
        <button type="button" class="mf-btn mf-btn-ghost" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="mf-btn mf-btn-primary" id="btnGuardarPlan">
          <span class="material-symbols-outlined">save</span> <span id="txtBtnGuardarPlan">Asignar Plan</span>
        </button>
      </div>

    </form>
  </div>
</div>

<style>
#modalPlan .modal-dialog {
    max-width: 650px !important;
    width: calc(100% - 1.5rem);
}
</style>