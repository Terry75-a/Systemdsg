<div class="modal " id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabe" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <form class="modal-content" id="formAsignarPlan" method="POST">
      
      <!-- ID para identificar la sucursal o empresa -->
      <input type="hidden" id="idSucursalModal" name="id_sucursal" value="">
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

      <div class="pe-ms-body-wrap" style="overflow-y: auto; max-height: calc(85vh - 160px);">
        <div class="pe-ms-body">
          <div class="alert mb-3" role="alert">
            Asignando plan a: <strong id="modalClienteNombre"></strong>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="selectPlan">Seleccionar Plan disponible: <span class="text-danger">*</span></label>
              <select class="form-select" id="selectPlan" name="id_plan" required></select>
            </div>

            <div class="col-md-6">
              <label for="selectTipoPlan">Tipo de plan: <span class="text-danger">*</span></label>
              <select class="form-select" id="selectTipoPlan" name="id_tipo_plan" required></select>
            </div>

            <div class="col-md-6">
              <label for="fechaInicio">Fecha de Inicio: <span class="text-danger">*</span></label>
              <!-- name="fecha_de_inicio" igual a la BD -->
              <input type="date" class="form-control" id="fechaInicio" name="fecha_de_inicio" required>
            </div>

            <div class="col-md-6" id="grupoVencimiento">
              <label for="fechaVencimiento">Fecha de Vencimiento:</label>
              <!-- name="fecha_de_vencimiento" igual a la BD -->
              <input type="date" class="form-control" id="fechaVencimiento" name="fecha_de_vencimiento" readonly placeholder="Cálculo automático">
            </div>
              <div class="col-12 d-none" id="bloqueFinMensual">
  <label class="form-label fw-bold mb-2">Finalización del plan mensual:</label>

  <div class="form-check mb-2">
    <input class="form-check-input" type="radio" name="tipo_fin" id="finSinFecha" value="SIN_FIN" checked onchange="actualizarCamposFin()">
    <label class="form-check-label" for="finSinFecha" style="cursor: pointer;">Sin fecha de finalización</label>
  </div>

  

  <div class="form-check d-flex align-items-center gap-2">
    <input class="form-check-input mt-0" type="radio" name="tipo_fin" id="finFecha" value="FECHA" onchange="actualizarCamposFin()">
    <label class="form-check-label mb-0" for="finFecha" style="cursor: pointer;">Finalizar el</label>
    <input type="date" class="form-control" style="width: 190px; height: 35px; min-width: 190px;"
           id="fechaFinMensual" name="fecha_fin" disabled>
  </div>
</div>

            <div class="col-md-6">
              <label for="inputPrecio">Precio Cobrado (S/): <span class="text-danger">*</span></label>
              <input type="number" step="0.01" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required>
            </div>

            <div class="col-12">
              <label for="txtObservaciones">Observaciones (Opcional):</label>
              <textarea class="form-control" id="txtObservaciones" name="observaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="pe-ms-footer" style="flex-shrink: 0;">
        <button type="button" class="btn pe-ms-cancel" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </button>
        <button type="submit" class="btn pe-ms-save" id="btnGuardarPlan">
          <i class="fa-solid fa-check" aria-hidden="true"></i> Asignar Plan
        </button>
      </div>

    </form>
  </div>
</div>