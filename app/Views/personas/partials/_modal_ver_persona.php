<div class="modal fade pv-modal" id="modalVerPersona" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pdsg-modal-content pv-content">

      <!-- Cabecera -->
      <div class="pdsg-modal-bar pv-hero">
        <span class="pdsg-modal-avatar pv-avatar" id="ver_avatar">-</span>
        <div class="pdsg-modal-head-info pv-who">
          <h5 class="pdsg-modal-name pv-name" id="ver_nombre">-</h5>
          <span class="pdsg-modal-sub pv-biz"><span class="material-symbols-outlined">apartment</span> <span id="ver_razon_social">-</span></span>
        </div>
        <span class="pdsg-chip pdsg-chip-on pv-state" id="pdsg-modal-chip"><span class="ms-dot"></span> Activo</span>
        <button type="button" class="pdsg-modal-close pv-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="pdsg-modal-scroll pv-scroll">
        <div class="pdsg-modal-body pv-body">

          <!-- Franja de datos clave (StatGrid) -->
          <div class="pdsg-modal-group pv-group">
            <div class="pv-stats">
              <div class="pv-stat"><span class="pv-stat-lbl"><span class="material-symbols-outlined">badge</span> DNI</span><span class="pv-stat-val pv-mono" id="ver_dni">-</span></div>
              <div class="pv-stat"><span class="pv-stat-lbl"><span class="material-symbols-outlined">receipt_long</span> RUC</span><span class="pv-stat-val pv-mono" id="ver_ruc">-</span></div>
              <div class="pv-stat"><span class="pv-stat-lbl"><span class="material-symbols-outlined">call</span> Teléfono</span><span class="pv-stat-val pv-mono" id="ver_telefono">-</span></div>
            </div>
          </div>

          <!-- Datos en filas (Row de Trackin) -->
          <div class="pdsg-modal-group pv-group">
            <div class="pv-card">
              <div class="pv-card-head"><h6><span class="material-symbols-outlined">person</span> Datos personales</h6><span>Identidad y domicilio</span></div>
              <div class="pv-rows">
                <div class="pv-row"><span><span class="material-symbols-outlined">person</span> Apellido paterno</span><b id="ver_apellido_paterno">-</b></div>
                <div class="pv-row"><span><span class="material-symbols-outlined">person</span> Apellido materno</span><b id="ver_apellido_materno">-</b></div>
                <div class="pv-row"><span><span class="material-symbols-outlined">home</span> Dirección</span><b id="ver_direccion">-</b></div>
                <div class="pv-row pv-row-loc">
                  <span><span class="material-symbols-outlined">location_on</span> Ubicación</span>
                  <b class="pv-loc">
                    <span id="ver_departamento">-</span><span class="material-symbols-outlined">chevron_right</span>
                    <span id="ver_provincia">-</span><span class="material-symbols-outlined">chevron_right</span>
                    <span id="ver_distrito">-</span>
                  </b>
                </div>
              </div>
            </div>
          </div>

          <!-- Sucursales (las tarjetas las arma personas-lista.js) -->
          <div class="pdsg-modal-group pdsg-modal-group--suc pv-group">
            <div class="pv-suc-head">
              <h6><span class="material-symbols-outlined">storefront</span> Sucursales <span class="pdsg-suc-count pv-count" id="ver_suc_count" hidden>0</span></h6>
              <span>Toca una sucursal para ver su detalle</span>
            </div>
            <div class="pdsg-suc-grid pv-suc-grid" id="ver_sucursales">
              <div class="pdsg-suc-empty" id="sucursales_vacio">
                <span class="material-symbols-outlined">inbox</span>
                <span>Sin sucursales registradas</span>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
