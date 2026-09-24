<div class="card shadow-sm border-0 w-100 h-100 p-0 m-0">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="h4 mb-1">Menú del sistema</h2>
                <p class="text-muted mb-0">Vista saneada del sidebar administrativo según las rutas y pantallas hoy disponibles.</p>
            </div>
            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">Solo lectura</span>
        </div>

        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <h5 class="mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Alcance actual</h5>
            <p class="mb-0">Se retiraron acciones fantasmas: este módulo no reordena ni edita el menú todavía. Solo documenta la estructura visible que realmente existe.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="list-group shadow-sm">
                    <div class="list-group-item list-group-item-action active bg-dark border-dark">
                        <i class="fa-solid fa-layer-group me-2"></i>Estructura vigente del menú
                    </div>
                    <?php foreach (($menuItems ?? []) as $item): ?>
                        <?php if (($item['type'] ?? 'directo') === 'grupo'): ?>
                            <div class="list-group-item bg-light ps-4">
                                <i class="fa-solid <?= esc($item['icon']) ?> me-2"></i>
                                <strong><?= esc($item['label']) ?></strong>
                                <div class="ms-3 mt-2 d-flex flex-column gap-2">
                                    <?php foreach (($item['children'] ?? []) as $child): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border text-start"><?= esc($child) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="list-group-item">
                                <i class="fa-solid <?= esc($item['icon']) ?> me-3"></i><?= esc($item['label']) ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 bg-light h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-circle-info me-2"></i>Limitaciones documentadas</h5>
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
</div>
