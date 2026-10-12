<div class="modal fade" id="modalVerPersona" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pdsg-modal-content">

      <div class="pdsg-modal-bar">
        <span class="pdsg-modal-avatar" id="ver_avatar">-</span>
        <div class="pdsg-modal-head-info">
          <h5 class="pdsg-modal-name" id="ver_nombre">-</h5>
          <span class="pdsg-modal-sub" id="ver_razon_social">-</span>
        </div>
        <span class="pdsg-chip pdsg-chip-on" id="pdsg-modal-chip"><i class="fa-solid fa-circle"></i> Activo</span>
        <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="pdsg-modal-scroll">
        <div class="pdsg-modal-body">

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-id-card"></i> Datos personales</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-user"></i> Apellido paterno</span>
                  <span class="pdsg-modal-field-value" id="ver_apellido_paterno">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-user"></i> Apellido materno</span>
                  <span class="pdsg-modal-field-value" id="ver_apellido_materno">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-hashtag"></i> DNI</span>
                  <span class="pdsg-modal-field-value pdsg-mono" id="ver_dni">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-hashtag"></i> RUC</span>
                  <span class="pdsg-modal-field-value pdsg-mono" id="ver_ruc">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-address-book"></i> Contacto</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                  <span class="pdsg-modal-field-value" id="ver_telefono">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Dirección</span>
                  <span class="pdsg-modal-field-value" id="ver_direccion">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group">
            <div class="pdsg-modal-card">
              <span class="pdsg-modal-group-title"><i class="fa-solid fa-map-location-dot"></i> Ubicación</span>
              <div class="pdsg-modal-grid">
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Departamento</span>
                  <span class="pdsg-modal-field-value" id="ver_departamento">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Provincia</span>
                  <span class="pdsg-modal-field-value" id="ver_provincia">-</span>
                </div>
                <div class="pdsg-modal-field">
                  <span class="pdsg-modal-field-label"><i class="fa-solid fa-location-dot"></i> Distrito</span>
                  <span class="pdsg-modal-field-value" id="ver_distrito">-</span>
                </div>
              </div>
            </div>
          </div>

          <div class="pdsg-modal-group pdsg-modal-group--table">
            <span class="pdsg-modal-group-title"><i class="fa-solid fa-shop"></i> Sucursales</span>
            <div class="table-responsive pdsg-modal-table">
              <table class="table table-sm table-hover align-middle mb-0" id="tablaSucursales">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th class="text-center">Estado</th>
                  </tr>
                </thead>
                <tbody id="ver_sucursales">
                  <tr id="sucursales_vacio">
                    <td colspan="5" class="text-center text-muted py-3">
                      <i class="bi bi-inbox me-1"></i> Sin sucursales registradas
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>