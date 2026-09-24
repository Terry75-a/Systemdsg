<div class="card p-4 mx-auto shadow-sm border-0" style="max-width: 100%;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h2 class="h4 mb-1">Configuración administrativa</h2>
      <p class="text-muted mb-0">Landing interna para acceder solo a acciones hoy implementadas dentro del scope administrativo.</p>
    </div>
    <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">Sin guardado</span>
  </div>

  <div class="alert alert-info border-0 shadow-sm mb-4">
    <h5 class="mb-2"><i class="fa-solid fa-circle-info me-2"></i>Estado del módulo</h5>
    <p class="mb-0">Esta pantalla no persiste tema, idioma ni preferencias globales. Se mantiene como panel de referencia hasta que exista backend real para configuración.</p>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card border-0 bg-light h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Accesos realmente disponibles</h5>
          <div class="d-flex flex-column gap-3">
            <?php foreach (($modulosAdministracion ?? []) as $modulo): ?>
              <a href="<?= esc($modulo['ruta']) ?>" class="text-decoration-none text-reset">
                <div class="border rounded p-3 bg-white h-100">
                  <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                    <h6 class="mb-0"><?= esc($modulo['nombre']) ?></h6>
                    <span class="badge <?= ($modulo['estado'] === 'Disponible') ? 'bg-success-subtle text-success border' : 'bg-warning-subtle text-warning border' ?>">
                      <?= esc($modulo['estado']) ?>
                    </span>
                  </div>
                  <p class="text-muted mb-0"><?= esc($modulo['descripcion']) ?></p>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card border-0 bg-light h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><i class="fa-solid fa-clipboard-list me-2"></i>Limitaciones actuales</h5>
          <ul class="mb-0 ps-3">
            <?php foreach (($limitaciones ?? []) as $limitacion): ?>
              <li class="mb-2"><?= esc($limitacion) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
