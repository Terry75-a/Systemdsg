<?php
$flashMsg = session()->getFlashdata('msg');
$flashTipo = session()->getFlashdata('tipo') ?? 'success';
$departamentos = $departamentos ?? [];
$flashRole = in_array($flashTipo, ['danger', 'warning'], true) ? 'alert' : 'status';

$oldEmpresa = [
    'ruc' => old('ruc'),
    'razon_social' => old('razon_social'),
    'correo_empresa' => old('correo_empresa'),
    'telefono_empresa' => old('telefono_empresa'),
    'direccion' => old('direccion'),
    'departamento' => old('departamento'),
    'provincia' => old('provincia'),
    'distrito' => old('distrito'),
];

$empresaHasOldInput = array_filter(
    $oldEmpresa,
    static fn ($value): bool => trim((string) $value) !== ''
) !== [];

$representanteTabClass = $empresaHasOldInput ? '' : 'active';
$empresaTabClass = $empresaHasOldInput ? 'active' : '';
$representantePaneClass = $empresaHasOldInput ? '' : 'show active';
$empresaPaneClass = $empresaHasOldInput ? 'show active' : '';
?>

<div class="container-fluid px-2">
    <?php if ($flashMsg): ?>
        <div class="alert alert-<?= esc($flashTipo) ?> alert-dismissible fade show" role="<?= esc($flashRole) ?>" aria-live="polite">
            <?= esc($flashMsg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar mensaje"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
                <div>
                    <h3 class="fw-bold mb-1">Agregar Nuevo Cliente</h3>
                    <p class="text-muted mb-0">Completá primero los datos del representante y, si corresponde, asociá una empresa existente o nueva.</p>
                </div>
                <span class="badge text-bg-light border text-uppercase">Alta guiada</span>
            </div>
        </div>
        <div class="card-body p-4">
            <div id="lookup-feedback" class="visually-hidden" aria-live="polite"></div>

            <form method="POST" action="<?= base_url('clientes/registrar') ?>" novalidate>
                <?= csrf_field() ?>

                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $representanteTabClass ?> fw-bold" id="rep-tab" data-bs-toggle="tab"
                                data-bs-target="#rep" type="button" role="tab" aria-controls="rep" aria-selected="<?= $empresaHasOldInput ? 'false' : 'true' ?>">
                            <i class="fa-solid fa-user-tie me-2"></i>Representante Legal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $empresaTabClass ?> fw-bold" id="empresa-tab" data-bs-toggle="tab"
                                data-bs-target="#empresa" type="button" role="tab" aria-controls="empresa" aria-selected="<?= $empresaHasOldInput ? 'true' : 'false' ?>">
                            <i class="fa-solid fa-building me-2"></i>Empresa
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade <?= $representantePaneClass ?>" id="rep" role="tabpanel" aria-labelledby="rep-tab">
                        <div class="rounded-3 border bg-light-subtle px-3 py-2 mb-4 small text-muted">
                            Los campos marcados con <span class="text-danger fw-semibold">*</span> son obligatorios.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="dni">DNI <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" maxlength="8" inputmode="numeric" id="dni" name="dni" value="<?= esc(old('dni')) ?>" placeholder="Ej: 12345678" aria-describedby="dni-help" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btnBuscarDni" aria-label="Buscar datos por DNI">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="form-text" id="dni-help">Ingresá 8 dígitos y usá la búsqueda para autocompletar los nombres.</div>
                            </div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="nombre">Nombre <span class="text-danger">*</span></label><input type="text" class="form-control" id="nombre" name="nombre" value="<?= esc(old('nombre')) ?>" autocomplete="given-name" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="apellido_paterno">Apellido paterno <span class="text-danger">*</span></label><input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?= esc(old('apellido_paterno')) ?>" autocomplete="family-name" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="apellido_materno">Apellido materno</label><input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?= esc(old('apellido_materno')) ?>" autocomplete="additional-name"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="telefono">Teléfono</label><input type="text" class="form-control" maxlength="20" id="telefono" name="telefono" value="<?= esc(old('telefono')) ?>" placeholder="Ej: 999888777" autocomplete="tel"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="correo">Correo <span class="text-danger">*</span></label><input type="email" class="form-control" id="correo" name="correo" value="<?= esc(old('correo')) ?>" placeholder="correo@ejemplo.com" autocomplete="email" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="fecha_nacimiento">Fecha de nacimiento</label><input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= esc(old('fecha_nacimiento')) ?>"></div>
                            <div class="col-md-12"><label class="form-label fw-semibold" for="direccion_rep">Dirección Personal</label><input type="text" class="form-control" id="direccion_rep" name="direccion_rep" value="<?= esc(old('direccion_rep')) ?>" autocomplete="street-address"></div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="departamento_rep">Departamento</label>
                                <select name="departamento_rep" id="departamento_rep" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($departamentos as $dep): ?>
                                        <option value="<?= esc($dep->id) ?>" <?= old('departamento_rep') == $dep->id ? 'selected' : '' ?>><?= esc($dep->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label fw-semibold" for="provincia_rep">Provincia</label><select name="provincia_rep" id="provincia_rep" class="form-select"><option value="">Seleccionar</option></select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold" for="distrito_rep">Distrito</label><select name="distrito_rep" id="distrito_rep" class="form-select"><option value="">Seleccionar</option></select></div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="<?= base_url('clientes') ?>" class="btn btn-outline-secondary px-4"><i class="fa fa-arrow-left"></i> Volver</a>
                            <button type="button" class="btn btn-primary px-4" onclick="document.getElementById('empresa-tab').click()">Siguiente</button>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $empresaPaneClass ?>" id="empresa" role="tabpanel" aria-labelledby="empresa-tab">
                        <div class="alert alert-light border mb-4" role="note">
                            <div class="fw-semibold mb-1">Datos empresariales opcionales</div>
                            <ul class="mb-0 ps-3 small text-muted">
                                <li>Si completás cualquier dato de empresa, el RUC pasa a ser obligatorio.</li>
                                <li>Si el RUC ya existe, se reutiliza la empresa registrada y no se crea una nueva.</li>
                                <li>La razón social solo es obligatoria cuando el RUC todavía no existe.</li>
                            </ul>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label fw-semibold" for="ruc">RUC</label><div class="input-group"><input type="text" class="form-control" maxlength="11" inputmode="numeric" id="ruc" name="ruc" value="<?= esc(old('ruc')) ?>" placeholder="Ej: 20123456789" aria-describedby="ruc-help"><button type="button" class="btn btn-outline-secondary" id="btnBuscarRuc" aria-label="Buscar datos por RUC"><i class="fa fa-search" aria-hidden="true"></i></button></div><div class="form-text" id="ruc-help">Usalo para buscar o vincular una empresa existente.</div></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="razon_social">Razón social</label><input type="text" class="form-control" id="razon_social" name="razon_social" value="<?= esc(old('razon_social')) ?>" placeholder="Solo para empresa nueva" autocomplete="organization"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="correo_empresa">Correo empresarial</label><input type="email" class="form-control" id="correo_empresa" name="correo_empresa" value="<?= esc(old('correo_empresa')) ?>" autocomplete="email"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold" for="telefono_empresa">Teléfono</label><input type="text" class="form-control" maxlength="20" id="telefono_empresa" name="telefono_empresa" value="<?= esc(old('telefono_empresa')) ?>" autocomplete="tel"></div>
                            <div class="col-md-12"><label class="form-label fw-semibold" for="direccion">Dirección Fiscal</label><input type="text" class="form-control" id="direccion" name="direccion" value="<?= esc(old('direccion')) ?>" autocomplete="street-address"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold" for="departamento">Departamento</label><select name="departamento" id="departamento" class="form-select"><option value="">Seleccionar</option><?php foreach ($departamentos as $dep): ?><option value="<?= esc($dep->id) ?>" <?= old('departamento') == $dep->id ? 'selected' : '' ?>><?= esc($dep->name) ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold" for="provincia">Provincia</label><select name="provincia" id="provincia" class="form-select"><option value="">Seleccionar</option></select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold" for="distrito">Distrito</label><select name="distrito" id="distrito" class="form-select"><option value="">Seleccionar</option></select></div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="document.getElementById('rep-tab').click()"><i class="fa fa-arrow-left me-2"></i>Anterior</button>
                            <button type="submit" class="btn btn-success px-5"><i class="fa fa-plus me-2"></i>Agregar Cliente</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const feedback = document.getElementById('lookup-feedback');

    function announce(message) {
        if (feedback) {
            feedback.textContent = message;
        }
    }

    function resetSelect(select) { select.innerHTML = '<option value="">Seleccionar</option>'; }
    function populateSelect(select, data, selectedValue = '') {
        resetSelect(select);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            if (String(item.id) === String(selectedValue)) option.selected = true;
            select.appendChild(option);
        });
    }
    async function fetchJson(url) { const response = await fetch(url); return response.json(); }

    function setupUbigeo(deptId, provId, distId, selectedDept = '', selectedProv = '', selectedDist = '') {
        const deptSelect = document.getElementById(deptId);
        const provSelect = document.getElementById(provId);
        const distSelect = document.getElementById(distId);

        const loadDistricts = async (provinceId, preselectedDistrict = '') => {
            resetSelect(distSelect);
            if (!provinceId) return;
            const districts = await fetchJson("<?= base_url('ubigeo/distritos') ?>/" + provinceId);
            populateSelect(distSelect, districts, preselectedDistrict);
        };

        const loadProvinces = async (departmentId, preselectedProvince = '', preselectedDistrict = '') => {
            resetSelect(provSelect);
            resetSelect(distSelect);
            if (!departmentId) return;
            const provinces = await fetchJson("<?= base_url('ubigeo/provincias') ?>/" + departmentId);
            populateSelect(provSelect, provinces, preselectedProvince);
            if (preselectedProvince) await loadDistricts(preselectedProvince, preselectedDistrict);
        };

        deptSelect.addEventListener("change", function () { loadProvinces(this.value); });
        provSelect.addEventListener("change", function () { loadDistricts(this.value); });
        if (selectedDept) {
            deptSelect.value = selectedDept;
            loadProvinces(selectedDept, selectedProv, selectedDist);
        }
    }

    setupUbigeo("departamento", "provincia", "distrito", "<?= esc((string) old('departamento')) ?>", "<?= esc((string) old('provincia')) ?>", "<?= esc((string) old('distrito')) ?>");
    setupUbigeo("departamento_rep", "provincia_rep", "distrito_rep", "<?= esc((string) old('departamento_rep')) ?>", "<?= esc((string) old('provincia_rep')) ?>", "<?= esc((string) old('distrito_rep')) ?>");

    const btnBuscarDni = document.getElementById('btnBuscarDni');
    btnBuscarDni.addEventListener('click', function () {
        const dni = document.getElementById('dni').value.trim();
        if (dni.length !== 8) { alert("DNI debe tener 8 dígitos"); announce('El DNI debe tener 8 dígitos.'); return; }
        btnBuscarDni.disabled = true;
        announce('Buscando datos del DNI.');
        fetch('<?= base_url("clientes/buscar-dni") ?>', { method: 'POST', headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: "dni=" + encodeURIComponent(dni) })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nombre').value = data.nombres ?? '';
                    document.getElementById('apellido_materno').value = data.apellido_materno ?? '';
                    document.getElementById('apellido_paterno').value = data.apellido_paterno ?? '';
                    announce('Datos del DNI cargados correctamente.');
                } else {
                    announce(data.message || 'No se encontraron datos para el DNI.');
                    alert(data.message || "No encontrado");
                }
            })
            .catch(() => {
                announce('Ocurrió un error al buscar el DNI.');
                alert('No se pudo consultar el DNI en este momento.');
            })
            .finally(() => btnBuscarDni.disabled = false);
    });

    const btnBuscarRuc = document.getElementById('btnBuscarRuc');
    btnBuscarRuc.addEventListener('click', function () {
        const ruc = document.getElementById('ruc').value.trim();
        if (ruc.length !== 11) { alert("RUC debe tener 11 dígitos"); announce('El RUC debe tener 11 dígitos.'); return; }
        btnBuscarRuc.disabled = true;
        announce('Buscando datos del RUC.');
        fetch('<?= base_url("clientes/buscar-ruc") ?>', { method: 'POST', headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: "ruc=" + encodeURIComponent(ruc) })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('razon_social').value = data.razon ?? '';
                    document.getElementById('direccion').value = data.direccion ?? '';
                    announce('Datos del RUC cargados correctamente.');
                } else {
                    announce(data.message || 'No se encontraron datos para el RUC.');
                    alert(data.message || "No encontrado");
                }
            })
            .catch(() => {
                announce('Ocurrió un error al buscar el RUC.');
                alert('No se pudo consultar el RUC en este momento.');
            })
            .finally(() => btnBuscarRuc.disabled = false);
    });
});
</script>
