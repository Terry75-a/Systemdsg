<div class="modal fade" id="modalSucursal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="pdsg-modal-bar">
                <span class="pdsg-modal-avatar"><i class="fa-solid fa-shop" aria-hidden="true"></i></span>
                <div class="pdsg-modal-head-info">
                    <h5 class="pdsg-modal-name" id="modalSucursalTitulo">Registrar Sucursal</h5>
                    <span class="pdsg-modal-sub">Datos de la sucursal</span>
                </div>
                <button type="button" class="pdsg-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <div class="pdsg-modal-scroll">
                <div class="pdsg-modal-body">
                    <form id="formSucursal">
                        <input type="hidden" id="editandoIndice" value="">

                        <div class="pdsg-modal-group">
                            <span class="pdsg-modal-group-title">Datos de la sucursal</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre_sucursal">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" placeholder="Ej: Sucursal Lima Centro" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="codigo_cliente_sucursal">Código Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="codigo_cliente_sucursal" name="codigo_cliente_sucursal" placeholder="Ej: 0000070" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="representante_sucursal">Representante <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="representante_sucursal" name="representante_sucursal" placeholder="Ej: Juan Pérez" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono_sucursal">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono_sucursal" name="telefono_sucursal" maxlength="9" placeholder="Ej: 987654321">
                                </div>

                                <div class="col-md-6">
                                    <label for="correo_sucursal">Correo</label>
                                    <input type="email" class="form-control" id="correo_sucursal" name="correo_sucursal" placeholder="Ej: sucursal@correo.com">
                                </div>

                                <div class="col-md-6">
                                    <label for="direccion_sucursal">Dirección</label>
                                    <input type="text" class="form-control" id="direccion_sucursal" name="direccion_sucursal" placeholder="Ej: Av. Arequipa 1234">
                                </div>

                                <div class="col-md-4">
                                    <label for="departamento_sucursal">Departamento</label>
                                    <select class="form-select" id="departamento_sucursal" name="departamento_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach (isset($departamentos) ? $departamentos : [] as $dep): ?>
                                            <option value="<?= $dep->id ?>"><?= esc($dep->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="provincia_sucursal">Provincia</label>
                                    <select class="form-select" id="provincia_sucursal" name="provincia_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="distrito_sucursal">Distrito</label>
                                    <select class="form-select" id="distrito_sucursal" name="distrito_sucursal">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="pdsg-modal-group">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="estado_label">Estado</label>
                                    <select class="form-select" id="estado_label" name="estado_label">
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pe-ms-footer">
                <button type="button" class="btn pe-ms-cancel" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
                </button>
                <button type="button" id="btnGuardarSucursalLista" class="btn pe-ms-save">
                    <i class="fa-solid fa-check" aria-hidden="true"></i> <span id="btnGuardarSucursalTexto">Agregar a la Lista</span>
                </button>
            </div>

        </div>
    </div>
</div>