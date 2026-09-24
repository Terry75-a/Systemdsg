<main class="container-fluid px-4 py-3">
    

    <div class="mb-3">
        <h1 class="h3 mb-1">Registrar un nuevo tipo de plan</h1>
        <p class="text-muted mb-0">Completa los datos para crear una nueva categoría de plan</p>
    </div>

    <div class="row justify-content-start">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="<?=  base_url('tipo_plan/guardar') ?>" method="POST" id="formPlan" novalidate >
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="nombre_tipo" class="form-label fw-semibold">
                                Nombre del Tipo de Plan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre_tipo" name="nombre_tipo" required maxlength="50">
                            <div class="invalid-feedback">Este campo es obligatorio.</div>
                          
                            
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="btnRegistrar">
                                <i class="fas fa-check me-1"></i> Registrar
                            </button>
                            <a href="tipo_plan" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


