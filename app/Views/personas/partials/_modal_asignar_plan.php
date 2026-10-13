<div class="modal fade" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pdsg-modal-content">
      <div class="pdsg-modal-bar">
    <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">
    <span class="pdsg-modal-avatar"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
    <div class="pdsg-modal-head-info">
      <h5 class="pdsg-modal-name" id="modalPlanLabel">Asignar Plan de Servicio</h5>
      <span class="pdsg-modal-sub">Selecciona el plan y el periodo</span>
    </div>
    <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
</div>
      <div class="modal-body">
        <input type="hidden" name="id_tipo_pago" value="1">
    
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

          <<div class="col-md-6" id="grupoVencimiento">

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