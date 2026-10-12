<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <form class="modal-content" id="formPago" onsubmit="return false;">

      <div class="pe-ms-head">
        <span class="pe-ms-icon"><i class="fa-solid fa-money-bill" aria-hidden="true"></i></span>
        <div class="pe-ms-head-info">
          <h2 id="modalPagoLabel">Otros Pagos</h2>
          <p>Registra el concepto, vencimiento y monto del pago.</p>
        </div>
        <button type="button" class="pe-ms-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
      </div>

      <div class="pe-ms-body-wrap">
        <div class="pe-ms-body">
          <div class="row g-3">

            <div class="col-12">
              <label for="pagoConcepto">Concepto:</label>
              <input type="text" class="form-control" id="pagoConcepto" name="concepto" placeholder="Ej. Licencia Anual">
            </div>

            <div class="col-md-6">
              <label for="pagoDiasAviso">Cantidad de días de aviso:</label>
              <input type="number" min="0" class="form-control" id="pagoDiasAviso" name="dias_aviso" placeholder="Ej. 10">
            </div>

            <div class="col-md-6">
              <label for="pagoMonto">Monto:</label>
              <input type="number" step="0.01" min="0" class="form-control" id="pagoMonto" name="monto" placeholder="Ej. 50.00">
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

            <div class="col-12">
              <label for="pagoCorreo">Correo de notificación:</label>
              <input type="email" class="form-control" id="pagoCorreo" name="correo_notificacion" placeholder="correo@ejemplo.com">
            </div>

          </div>
        </div>
      </div>

      <div class="pe-ms-footer" style="flex-shrink: 0;">
        <button type="button" class="btn pe-ms-cancel" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark" aria-hidden="true"></i> Salir
        </button>
        <button type="button" class="btn pe-ms-save" id="btnGuardarPago">
          <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Guardar
        </button>
      </div>

    </form>
  </div>
</div>
