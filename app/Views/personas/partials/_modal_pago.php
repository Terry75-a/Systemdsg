<div class="modal fade mf-modal" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content mf-content" id="formPago" onsubmit="return false;">

      <!-- CAMPOS OCULTOS NECESARIOS -->
      <input type="hidden" id="pagoIdPago" name="id_pago" value="">
      <input type="hidden" id="pagoIdSucursal" name="id_sucursal" value="">
      <input type="hidden" id="pagoIdEmpresa" name="id_empresa" value="">
      <input type="hidden" name="id_tipo_pago" value="2">

      <!-- Cabecera -->
      <div class="mf-head">
        <span class="mf-icon"><span class="material-symbols-outlined">receipt_long</span></span>
        <div class="mf-head-info">
          <h2 id="modalPagoLabel">Otros Pagos</h2>
          <p id="modalPagoSubtitulo">Registra o edita el concepto, vencimiento y monto del pago.</p>
        </div>
        <button type="button" class="mf-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="mf-scroll">
        <div class="mf-body">

          <!-- Banner de modo edición (oculto por defecto) -->
          <div id="pagoBannerEdicion" class="pdsg-edit-banner d-none">
            <div class="d-flex align-items-center gap-2">
              <span class="material-symbols-outlined">edit_note</span>
              <span>Editando pago: <strong id="pagoEditandoNombre"></strong></span>
            </div>
            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="btnCancelarEdicionPago">
              Cancelar edición
            </button>
          </div>

          <!-- Pago -->
          <section class="mf-sec">
            <h3 class="mf-sec-title"><span class="material-symbols-outlined">payments</span> Datos del Pago</h3>
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

          <!-- Tarjeta de Vista Rápida (Se abre al pulsar el ojo "Ver") -->
          <div id="pagoDetalleCard" class="pdsg-pago-view-card d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold d-flex align-items-center gap-1" style="font-size: 0.85rem; color: #334155;">
                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span> Detalle del Pago
              </span>
              <button type="button" class="btn-close" style="font-size: 0.7rem;" id="btnCerrarDetallePago" aria-label="Cerrar detalle"></button>
            </div>
            <div class="pdsg-pago-view-grid" id="pagoDetalleContenido"></div>
          </div>

          <!-- GRILLA DE PAGOS REGISTRADOS -->
          <section class="mf-sec">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h3 class="mf-sec-title mb-0">
                <span class="material-symbols-outlined">table_rows</span> Pagos Registrados
                <span class="pdsg-chip pdsg-chip-on ms-2" id="contadorOtrosPagos">0</span>
              </h3>
            </div>

            <div class="pdsg-grid-pagos-wrap">
              <table class="table pdsg-grid-pagos mb-0">
                <thead>
                  <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Vencimiento</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tbodyOtrosPagos">
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
        <button type="submit" class="mf-btn mf-btn-primary" id="btnGuardarPago">
          <span class="material-symbols-outlined">save</span> <span id="txtBtnGuardarPago">Guardar Pago</span>
        </button>
      </div>

    </form>
  </div>
</div>

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