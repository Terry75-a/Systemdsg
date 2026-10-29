<style>
    /* Estilos para igualar exactamente las cajas de tu interfaz actual */
    .config-card-outer {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eaedf1;
        padding: 24px;
    }
    .config-section-box {
        background: #ffffff;
        border: 1px solid #eef0f4;
        border-radius: 14px;
        padding: 20px;
        height: 100%;
    }
    .config-item-card {
        border: 1px solid #edf0f5;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        background: #fcfcfd;
        transition: border-color 0.2s;
    }
    .config-item-card:hover {
        border-color: #d0d7de;
    }
    .config-item-card:last-child {
        margin-bottom: 0;
    }
    .badge-soft-success {
        background-color: #e6f6f0;
        color: #0d8a5e;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
    }
</style>

<form id="formConfigAdmin" action="#" method="POST">
    <div class="config-card-outer shadow-sm">
        
        <!-- Cabecera igual a tu diseño pero con botón de guardado activo -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h5 class="fw-bold mb-1">Configuración administrativa</h5>
                <p class="text-muted small mb-0">Ajusta las reglas de alertas automáticas para planes de clientes y preferencias operativas del panel.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 fw-normal">Cambios pendientes</span>
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4 py-2 fw-medium">
                    Guardar configuración
                </button>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- COLUMNA IZQUIERDA: CONFIGURACIÓN DE ALERTAS -->
            <div class="col-lg-6">
                <div class="config-section-box">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span>🔔</span> Configuración de Alertas (Planes y Clientes)
                    </h6>

                    <!-- Item 1: Campana del sistema -->
                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div class="pe-3">
                            <div class="fw-bold small text-dark">Notificaciones en la campana superior</div>
                            <div class="text-muted small mt-1">Muestra avisos dentro del panel cuando un plan está próximo a vencer o hay eventos en el calendario.</div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input" type="checkbox" name="alerta_campana" checked>
                        </div>
                    </div>

                    <!-- Item 2: Días de anticipación -->
                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div class="pe-3">
                            <div class="fw-bold small text-dark">Anticipación por vencimiento de Plan</div>
                            <div class="text-muted small mt-1">¿Cuántos días antes del vencimiento el sistema debe generar la alerta del cliente?</div>
                        </div>
                        <div style="min-width: 120px;">
                            <select class="form-select form-select-sm fw-semibold" name="dias_anticipacion">
                                <option value="3">3 días antes</option>
                                <option value="5" selected>5 días antes</option>
                                <option value="7">7 días antes</option>
                                <option value="15">15 días antes</option>
                            </select>
                        </div>
                    </div>

                    <!-- Item 3: Alertas de clientes vencidos -->
                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div class="pe-3">
                            <div class="fw-bold small text-dark">Alerta de planes vencidos / impagos</div>
                            <div class="text-muted small mt-1">Mantener alerta activa en el panel mientras el cliente tenga un plan vencido sin renovar.</div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input" type="checkbox" name="alerta_vencidos" checked>
                        </div>
                    </div>

                    <!-- Item 4: Explicación clara del Correo Destino -->
                    <div class="config-item-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold small text-dark">Resumen de alertas al correo del Admin</div>
                                <div class="text-muted small">Envía un correo automático con la lista de clientes cuyos planes vencen en la semana.</div>
                            </div>
                            <div class="form-check form-switch fs-5 m-0">
                                <input class="form-check-input" type="checkbox" name="enviar_correo_alertas" id="switchCorreo" checked>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <label class="form-label text-muted" style="font-size: 0.78rem;">¿A qué correo enviamos los avisos?</label>
                            <input type="email" class="form-control form-control-sm" name="correo_receptor_alertas" value="gerson@dsgperu.com" placeholder="ejemplo@dsgperu.com">
                        </div>
                    </div>

                </div>
            </div>

            <!-- COLUMNA DERECHA: CONFIGURACIÓN DEL ADMIN Y ACCESOS -->
            <div class="col-lg-6">
                <div class="config-section-box">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span>⚙️</span> Ajustes del Administrador y Accesos
                    </h6>

                    <!-- Item 1: Tiempo de sesión -->
                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div class="pe-3">
                            <div class="fw-bold small text-dark">Cierre de sesión por inactividad</div>
                            <div class="text-muted small mt-1">Tiempo máximo sin usar el sistema antes de pedir contraseña nuevamente por seguridad.</div>
                        </div>
                        <div style="min-width: 125px;">
                            <select class="form-select form-select-sm fw-semibold" name="tiempo_sesion">
                                <option value="30">30 minutos</option>
                                <option value="60" selected>1 hora</option>
                                <option value="240">4 horas</option>
                            </select>
                        </div>
                    </div>

                    <!-- Item 2: Creación rápida o bloqueo -->
                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div class="pe-3">
                            <div class="fw-bold small text-dark">Bloquear acceso a usuarios inactivos</div>
                            <div class="text-muted small mt-1">Impide automáticamente el inicio de sesión a cuentas marcadas como bajas o suspendidas.</div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input" type="checkbox" name="bloqueo_inactivos" checked>
                        </div>
                    </div>

                    
                    <div class="mt-4 mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Accesos rápidos de administración</span>
                    </div>

                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small text-dark">Usuarios y Permisos</div>
                            <div class="text-muted small mt-1">Gestión CRUD operativa de cuentas de acceso y roles por módulo.</div>
                        </div>
                        <a href="/usuarios" class="badge-soft-success text-decoration-none">Ir a Usuarios →</a>
                    </div>

                    <div class="config-item-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small text-dark">Catálogo de Planes</div>
                            <div class="text-muted small mt-1">Configuración de precios, tipos de plan y vigencias para clientes.</div>
                        </div>
                        <a href="/planes" class="badge-soft-success text-decoration-none">Ir a Planes →</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</form>