<div class="modal mf-modal" id="modalPlan" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content mf-content" id="formAsignarPlan" method="POST">

      <!-- ID para identificar la sucursal o empresa -->
      <input type="hidden" id="idSucursalModal" name="id_sucursal" value="">
      <input type="hidden" id="idEmpresaModal" name="id_empresa" value="">

      <!-- Cabecera -->
      <div class="mf-head">
        <span class="mf-icon"><span class="material-symbols-outlined">workspace_premium</span></span>
        <div class="mf-head-info">
          <h2 id="modalPlanLabel">Asignar Plan de Servicio</h2>
          <p>Selecciona el plan y condiciones para este cliente.</p>
        </div>
        <button type="button" class="mf-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="mf-scroll">
        <div class="mf-body">

          <!-- A quién se asigna -->
          <div class="mf-target" role="status">
            <span class="mf-target-ic"><span class="material-symbols-outlined">storefront</span></span>
            <div class="mf-target-info">
              <span>Asignando plan a</span>
              <strong id="modalClienteNombre"></strong>
            </div>
          </div>

          <!-- Plan -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">inventory_2</span> Plan</h3>
            <div class="mf-grid">
              <div class="mf-f">
                <label for="selectPlan">Plan disponible <span class="mf-req">*</span></label>
                <select class="form-select" id="selectPlan" name="id_plan" required></select>
              </div>
              <div class="mf-f">
                <label for="selectTipoPlan">Tipo de plan <span class="mf-req">*</span></label>
                <select class="form-select" id="selectTipoPlan" name="id_tipo_plan" required></select>
              </div>
            </div>
          </section>

          <!-- Vigencia -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">event</span> Vigencia</h3>
            <div class="mf-grid">
              <div class="mf-f">
                <label for="fechaInicio">Fecha de inicio <span class="mf-req">*</span></label>
                <!-- name="fecha_de_inicio" igual a la BD -->
                <input type="date" class="form-control" id="fechaInicio" name="fecha_de_inicio" required>
              </div>
              <div class="mf-f" id="grupoVencimiento">
                <label for="fechaVencimiento">Fecha de vencimiento <span class="mf-hint">Automática</span></label>
                <!-- name="fecha_de_vencimiento" igual a la BD -->
                <input type="date" class="form-control" id="fechaVencimiento" name="fecha_de_vencimiento" readonly placeholder="Cálculo automático">
              </div>

              <div class="mf-f mf-span d-none" id="bloqueFinMensual">
                <label class="mf-lbl">Finalización del plan mensual</label>
                <div class="mf-options">
                  <label class="mf-option" for="finSinFecha">
                    <input class="form-check-input" type="radio" name="tipo_fin" id="finSinFecha" value="SIN_FIN" checked onchange="actualizarCamposFin()">
                    <span class="mf-option-txt"><b>Sin fecha de fin</b><small>Se renueva cada mes</small></span>
                  </label>
                  <label class="mf-option" for="finFecha">
                    <input class="form-check-input" type="radio" name="tipo_fin" id="finFecha" value="FECHA" onchange="actualizarCamposFin()">
                    <span class="mf-option-txt"><b>Finalizar el</b><small>Elige el último día</small></span>
                  </label>
                </div>
                <input type="date" class="form-control mf-fin-date" id="fechaFinMensual" name="fecha_fin" disabled aria-label="Fecha de finalización">
              </div>
            </div>
          </section>

          <!-- Cobro -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">payments</span> Cobro</h3>
            <div class="mf-grid">
              <div class="mf-f mf-span">
                <label for="inputPrecio">Precio cobrado <span class="mf-req">*</span></label>
                <div class="mf-input">
                  <span class="mf-affix">S/</span>
                  <input type="number" step="0.01" class="form-control" id="inputPrecio" name="precio" placeholder="0.00" required>
                </div>
              </div>
              <div class="mf-f mf-span">
                <label for="txtObservaciones">Observaciones <span class="mf-hint">Opcional</span></label>
                <textarea class="form-control" id="txtObservaciones" name="observaciones" rows="2" placeholder="Ej: Pago adelantado, descuento especial..."></textarea>
              </div>
            </div>
          </section>

        </div>
      </div>

      <div class="mf-foot">
        <button type="button" class="mf-btn mf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="mf-btn mf-btn-primary" id="btnGuardarPlan">
          <span class="material-symbols-outlined">check</span> Asignar Plan
        </button>
      </div>

    </form>
  </div>
</div>
