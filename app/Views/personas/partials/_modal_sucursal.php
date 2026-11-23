<div class="modal fade mf-modal" id="modalSucursal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content mf-content">

            <!-- Cabecera -->
            <div class="mf-head">
                <span class="mf-icon"><span class="material-symbols-outlined">storefront</span></span>
                <div class="mf-head-info">
                    <h2 id="modalSucursalTitulo">Registrar Sucursal</h2>
                    <p>Datos de la sucursal</p>
                </div>
                <button type="button" class="mf-close" data-bs-dismiss="modal" aria-label="Cerrar">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="mf-scroll">
                <div class="mf-body">
                    <form id="formSucursal">
                        <input type="hidden" id="editandoIndice" value="">

                        <!-- Identificación -->
                        <section class="mf-sec">
                            <h3 class="mf-sec-title"><span class="material-symbols-outlined">badge</span> Identificación</h3>
                            <div class="mf-grid">
                                <div class="mf-f">
                                    <label for="nombre_sucursal">Nombre <span class="mf-req">*</span></label>
                                    <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" placeholder="Ej: Sucursal Lima Centro" required>
                                </div>
                                <div class="mf-f">
                                    <label for="codigo_cliente_sucursal">Código cliente <span class="mf-req">*</span></label>
                                    <input type="text" class="form-control" id="codigo_cliente_sucursal" name="codigo_cliente_sucursal" placeholder="Ej: 0000070" required>
                                </div>
                                <div class="mf-f pdsg-ac-wrap">
                                    <label for="representante_sucursal">Representante <span class="mf-req">*</span></label>
                                    <div class="position-relative">
                                        <input type="text"
                                               class="form-control"
                                               id="representante_sucursal"
                                               name="representante_sucursal"
                                               placeholder="Escribe para buscar representante..."
                                               autocomplete="off"
                                               required>
                                        <div id="listaRepresentantes" class="pdsg-ac-menu d-none" role="listbox"></div>
                                    </div>
                                </div>
                                <div class="mf-f">
                                    <label for="estado_label">Estado</label>
                                    <select class="form-select" id="estado_label" name="estado_label">
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <!-- Contacto -->
                        <section class="mf-sec">
                            <h3 class="mf-sec-title"><span class="material-symbols-outlined">call</span> Contacto</h3>
                            <div class="mf-grid">
                                <div class="mf-f">
                                    <label for="telefono_sucursal">Teléfono</label>
                                    <div class="mf-input">
                                        <span class="mf-affix"><span class="material-symbols-outlined">call</span></span>
                                        <input type="text" class="form-control" id="telefono_sucursal" name="telefono_sucursal" maxlength="9" placeholder="987654321">
                                    </div>
                                </div>
                                <div class="mf-f">
                                    <label for="correo_sucursal">Correo</label>
                                    <div class="mf-input">
                                        <span class="mf-affix"><span class="material-symbols-outlined">mail</span></span>
                                        <input type="email" class="form-control" id="correo_sucursal" name="correo_sucursal" placeholder="sucursal@correo.com">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Ubicación -->
                        <section class="mf-sec">
                            <h3 class="mf-sec-title"><span class="material-symbols-outlined">location_on</span> Ubicación</h3>
                            <div class="mf-grid mf-grid-3">
                                <div class="mf-f mf-span">
                                    <label for="direccion_sucursal">Dirección</label>
                                    <input type="text" class="form-control" id="direccion_sucursal" name="direccion_sucursal" placeholder="Ej: Av. Arequipa 1234">
                                </div>
                                <div class="mf-f">
                                    <label for="departamento_sucursal">Departamento</label>
                                    <select class="form-select" id="departamento_sucursal" name="departamento_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach (isset($departamentos) ? $departamentos : [] as $dep): ?>
                                            <option value="<?= $dep->id ?>"><?= esc($dep->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mf-f">
                                    <label for="provincia_sucursal">Provincia</label>
                                    <select class="form-select" id="provincia_sucursal" name="provincia_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                                <div class="mf-f">
                                    <label for="distrito_sucursal">Distrito</label>
                                    <select class="form-select" id="distrito_sucursal" name="distrito_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            </div>

            <div class="mf-foot">
                <button type="button" class="mf-btn mf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGuardarSucursalLista" class="mf-btn mf-btn-primary">
                    <span class="material-symbols-outlined">check</span> <span id="btnGuardarSucursalTexto">Agregar a la Lista</span>
                </button>
            </div>

        </div>
    </div>
</div>
<style>
.pdsg-ac-wrap {
    position: relative;
}
.pdsg-ac-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1065;
    max-height: 210px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    padding: 4px;
}
.pdsg-ac-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 7px 10px;
    border-radius: 7px;
    font-size: 0.83rem;
    color: #1e293b;
    cursor: pointer;
    transition: background 0.12s ease;
}
.pdsg-ac-item:hover,
.pdsg-ac-item.is-active {
    background: #f1f5f9;
    color: #0f172a;
}
.pdsg-ac-item mark {
    background: rgba(16, 185, 129, 0.2);
    color: #065f46;
    font-weight: 600;
    padding: 0 2px;
    border-radius: 3px;
}
.pdsg-ac-badge {
    font-size: 0.68rem;
    padding: 2px 6px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #475569;
    white-space: nowrap;
}
.pdsg-ac-empty {
    padding: 8px 10px;
    font-size: 0.8rem;
    color: #64748b;
    text-align: center;
}
</style>