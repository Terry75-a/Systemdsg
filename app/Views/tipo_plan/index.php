<div class="container-fluid py-4">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="fw-bold mb-1">Gestión de Tipos de Plan</h3>
      <p class="text-muted mb-0">Administra las categorías o modalidades de tus planes.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipoPlan">
      <i class="bi bi-plus-lg me-1"></i> + Nuevo Tipo de Plan
    </button>
  </div>

  <!-- Card Principal -->
  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
      
      <!-- Barra de Búsqueda -->
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">🔍</span>
            <input type="text" id="filtroTipoPlan" class="form-control border-start-0 bg-light" placeholder="Buscar tipo de plan...">
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="tablaTipos">
          <thead class="table-light">
            <tr>
              <th scope="col" class="text-center" style="width: 50px;">#</th>
              <th scope="col">Nombre Tipo</th>
              <th scope="col">Descripción</th>
              <th scope="col" class="text-center" style="width: 120px;">Estado</th>
              <th scope="col" class="text-center" style="width: 150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-center fw-bold">01</td>
              <td class="fw-semibold">Residencial</td>
              <td class="text-muted">Planes de internet para hogares</td>
              <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Activo</span></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar">✏️</button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar">🗑️</button>
              </td>
            </tr>
            <tr>
              <td class="text-center fw-bold">02</td>
              <td class="fw-semibold">Corporativo</td>
              <td class="text-muted">Ancho de banda dedicado para empresas</td>
              <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Activo</span></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar">✏️</button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar">🗑️</button>
              </td>
            </tr>
            <tr>
              <td class="text-center fw-bold">03</td>
              <td class="fw-semibold">Dúo (Net + TV)</td>
              <td class="text-muted">Combo de fibra óptica más cable TV</td>
              <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Activo</span></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar">✏️</button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar">🗑️</button>
              </td>
            </tr>
            <tr>
              <td class="text-center fw-bold">04</td>
              <td class="fw-semibold">Pyme</td>
              <td class="text-muted">Planes especiales para negocios</td>
              <td class="text-center"><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">Inactivo</span></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary me-1" title="Editar">✏️</button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer / Paginación -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Mostrando 1 a 4 de 4 registros</small>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item disabled"><a class="page-link" href="#">Siguiente</a></li>
        </ul>
      </div>

    </div>
  </div>
</div>

<!-- Modal Registrar Nuevo Tipo de Plan -->
<div class="modal fade" id="modalTipoPlan" tabindex="-1" aria-labelledby="modalTipoPlanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalTipoPlanLabel">Registrar Nuevo Tipo de Plan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formTipoPlan">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nombre del Tipo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="Ej. Residencial, Corporativo..." required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" rows="3" placeholder="Breve detalle sobre este tipo de plan..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold d-block">Estado</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="estado" id="estadoActivo" value="1" checked>
              <label class="form-check-label" for="estadoActivo">Activo</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="estado" id="estadoInactivo" value="0">
              <label class="form-check-label" for="estadoInactivo">Inactivo</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Registro</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script para el buscador en tiempo real -->
<script>
  document.getElementById('filtroTipoPlan').addEventListener('keyup', function() {
    let valor = this.value.toLowerCase();
    let filas = document.querySelectorAll('#tablaTipos tbody tr');
    
    filas.forEach(fila => {
      let texto = fila.textContent.toLowerCase();
      fila.style.display = texto.includes(valor) ? '' : 'none';
    });
  });
</script>