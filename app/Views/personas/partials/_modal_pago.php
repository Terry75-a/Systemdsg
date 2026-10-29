<div class="modal fade mf-modal" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content mf-content" id="formPago" onsubmit="return false;">

      <!-- CAMPOS OCULTOS NECESARIOS -->
      <input type="hidden" id="pagoIdSucursal" name="id_sucursal" value="">
      <input type="hidden" id="pagoIdEmpresa" name="id_empresa" value="">
      <input type="hidden" name="id_tipo_pago" value="2">

      <!-- Cabecera -->
      <div class="mf-head">
        <span class="mf-icon"><span class="material-symbols-outlined">receipt_long</span></span>
        <div class="mf-head-info">
          <h2 id="modalPagoLabel">Otros Pagos</h2>
          <p>Registra el concepto, vencimiento y monto del pago.</p>
        </div>
        <button type="button" class="mf-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="mf-scroll">
        <div class="mf-body">

          <!-- Pago -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">payments</span> Pago</h3>
            <div class="mf-grid">
              <div class="mf-f mf-span">
                <label for="pagoConcepto">Concepto <span class="mf-req">*</span></label>
                <input type="text" class="form-control" id="pagoConcepto" name="concepto" placeholder="Ej. Soporte técnico, Mantenimiento..." required>
              </div>
              <div class="mf-f">
                <label for="pagoMonto">Monto <span class="mf-req">*</span></label>
                <div class="mf-input">
                  <span class="mf-affix">S/</span>
                  <input type="number" step="0.01" min="0" class="form-control" id="pagoMonto" name="monto" placeholder="50.00" required>
                </div>
              </div>
              <div class="mf-f">
                <label for="pagoEstado">Estado</label>
                <select class="form-select" id="pagoEstado" name="estado">
                  <option value="1" selected>ACTIVO</option>
                  <option value="0">INACTIVO</option>
                </select>
              </div>
            </div>
          </section>

          <!-- Vencimiento y aviso -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">notifications</span> Vencimiento y aviso</h3>
            <div class="mf-grid">
              <div class="mf-f">
                <label for="pagoFechaVenc">Fecha de vencimiento</label>
                <input type="date" class="form-control" id="pagoFechaVenc" name="fecha_vencimiento">
              </div>
              <div class="mf-f">
                <label for="pagoDiasAviso">Días de aviso</label>
                <div class="mf-input mf-input-end">
                  <input type="number" min="0" class="form-control" id="pagoDiasAviso" name="dias_aviso" placeholder="10">
                  <span class="mf-affix">días</span>
                </div>
              </div>
              <div class="mf-f mf-span">
                <label for="pagoCorreo">Correo de notificación</label>
                <div class="mf-input">
                  <span class="mf-affix"><span class="material-symbols-outlined">mail</span></span>
                  <input type="email" class="form-control" id="pagoCorreo" name="correo_notificacion" placeholder="correo@ejemplo.com">
                </div>
              </div>
              <div class="mf-f mf-span">
                <label for="pagoObservaciones">Observaciones <span class="mf-hint">Opcional</span></label>
                <textarea class="form-control" id="pagoObservaciones" name="observaciones" rows="2" placeholder="Detalles u observaciones adicionales..."></textarea>
              </div>
            </div>
          </section>

            <div class="col-md-6">
              <label for="pagoMonto">Monto:<span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" class="form-control" id="pagoMonto" name="monto" placeholder="Ej. 50.00" required>
            </div>

            <div class="col-md-6">
              <label for="pagoFechaVenc">Fecha de vencimiento:</label>
              <input type="date" class="form-control" id="pagoFechaVenc" name="fecha_vencimiento">
            </div>

            <div class="col-md-6">
              <label for="pagoEstado">Estado:</label>
              <select class="form-select" id="pagoEstado" name="estado">
                <option value="1" selected>ACTIVO</option>
                <option value="0">INACTIVO</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="planDiasAviso">Días de aviso (Repeticiones antes de vencer):</label>
              <input type="number" min="1" max="60" class="form-control" id="planDiasAviso" name="num_repeticiones" value="5" placeholder="Ej: 5">
            </div>

            <div class="col-12">
              <label for="pagoCorreo">Correo de notificación:</label>
              <input type="email" class="form-control" id="pagoCorreo" name="correo_notificacion" placeholder="correo@ejemplo.com">
            </div>

            <div class="col-12">
  <label for="pagoObservaciones">Observaciones:</label>
  <textarea class="form-control" id="pagoObservaciones" name="observaciones" rows="2" placeholder="Detalles u observaciones adicionales..."></textarea>
</div>

          </div>
        </div>
      </div>

      <div class="mf-foot">
        <button type="button" class="mf-btn mf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="mf-btn mf-btn-primary" id="btnGuardarPago">
          <span class="material-symbols-outlined">save</span> Guardar
        </button>
      </div>

    </form>
  </div>
</div>
