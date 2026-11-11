<link rel="stylesheet" href="<?= base_url('css/dashboard/configuracion.css?v=' . filemtime(FCPATH . 'css/dashboard/configuracion.css')) ?>">
<main class="tp-page cfg-page">
    <form id="formConfigAdmin" action="#" method="POST">
        <div class="pdsg-page-head">
            <div>
                <h1 class="pdsg-page-title">Configuración administrativa</h1>
                <p class="pdsg-page-sub">Ajusta las reglas de alertas automáticas para planes de clientes y preferencias operativas del panel.</p>
            </div>
            <div class="cfg-head-actions">
                <span class="cfg-pending"><span class="cfg-pending-dot" aria-hidden="true"></span>Cambios pendientes</span>
                <button type="submit" class="tk2-btn tk2-btn-primary cfg-save">
                    <span class="material-symbols-outlined" aria-hidden="true">save</span>
                    <span>Guardar configuración</span>
                </button>
            </div>
        </div>

        <div class="cfg-grid">
            <!-- Alertas de planes y clientes -->
            <section class="tk2-card cfg-section" aria-labelledby="cfgAlertasTitle">
                <header class="cfg-section-head">
                    <span class="cfg-icon" aria-hidden="true"><span class="material-symbols-outlined">notifications_active</span></span>
                    <div>
                        <h2 id="cfgAlertasTitle">Alertas de planes y clientes</h2>
                        <p>Avisos automáticos de vencimientos y renovaciones dentro del panel.</p>
                    </div>
                </header>

                <div class="cfg-list">
                    <div class="cfg-item">
                        <div class="cfg-item-copy">
                            <p class="cfg-item-title" id="cfgCampanaTitle">Notificaciones en la campana superior</p>
                            <p class="cfg-item-desc">Muestra avisos dentro del panel cuando un plan está próximo a vencer o hay eventos en el calendario.</p>
                        </div>
                        <label class="cfg-switch">
                            <input type="checkbox" name="alerta_campana" checked aria-labelledby="cfgCampanaTitle">
                            <span class="cfg-switch-track" aria-hidden="true"></span>
                        </label>
                    </div>

                    <div class="cfg-item cfg-item-select">
                        <div class="cfg-item-copy">
                            <p class="cfg-item-title" id="cfgDiasTitle">Anticipación por vencimiento de plan</p>
                            <p class="cfg-item-desc">¿Cuántos días antes del vencimiento el sistema debe generar la alerta del cliente?</p>
                        </div>
                        <div class="cfg-select">
                            <select class="form-select" name="dias_anticipacion" aria-labelledby="cfgDiasTitle">
                                <option value="3">3 días antes</option>
                                <option value="5" selected>5 días antes</option>
                                <option value="7">7 días antes</option>
                                <option value="15">15 días antes</option>
                            </select>
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                        </div>
                    </div>

                    <div class="cfg-item">
                        <div class="cfg-item-copy">
                            <p class="cfg-item-title" id="cfgVencidosTitle">Alerta de planes vencidos / impagos</p>
                            <p class="cfg-item-desc">Mantener alerta activa en el panel mientras el cliente tenga un plan vencido sin renovar.</p>
                        </div>
                        <label class="cfg-switch">
                            <input type="checkbox" name="alerta_vencidos" checked aria-labelledby="cfgVencidosTitle">
                            <span class="cfg-switch-track" aria-hidden="true"></span>
                        </label>
                    </div>

                    <div class="cfg-item cfg-item-stack">
                        <div class="cfg-item-row">
                            <div class="cfg-item-copy">
                                <p class="cfg-item-title" id="cfgCorreoTitle">Resumen de alertas al correo del Admin</p>
                                <p class="cfg-item-desc">Envía un correo automático con la lista de clientes cuyos planes vencen en la semana.</p>
                            </div>
                            <label class="cfg-switch">
                                <input type="checkbox" name="enviar_correo_alertas" id="switchCorreo" checked aria-labelledby="cfgCorreoTitle">
                                <span class="cfg-switch-track" aria-hidden="true"></span>
                            </label>
                        </div>
                        <div class="cfg-field">
                            <label class="cfg-field-label" for="cfgCorreo">¿A qué correo enviamos los avisos?</label>
                            <input type="email" id="cfgCorreo" class="form-control cfg-input" name="correo_receptor_alertas" value="gerson@dsgperu.com" placeholder="ejemplo@dsgperu.com">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Ajustes del administrador y accesos -->
            <section class="tk2-card cfg-section" aria-labelledby="cfgAdminTitle">
                <header class="cfg-section-head">
                    <span class="cfg-icon" aria-hidden="true"><span class="material-symbols-outlined">admin_panel_settings</span></span>
                    <div>
                        <h2 id="cfgAdminTitle">Ajustes del administrador y accesos</h2>
                        <p>Seguridad de la sesión y atajos a las áreas de administración.</p>
                    </div>
                </header>

                <div class="cfg-list">
                    <div class="cfg-item cfg-item-select">
                        <div class="cfg-item-copy">
                            <p class="cfg-item-title" id="cfgSesionTitle">Cierre de sesión por inactividad</p>
                            <p class="cfg-item-desc">Tiempo máximo sin usar el sistema antes de pedir contraseña nuevamente por seguridad.</p>
                        </div>
                        <div class="cfg-select">
                            <select class="form-select" name="tiempo_sesion" aria-labelledby="cfgSesionTitle">
                                <option value="30">30 minutos</option>
                                <option value="60" selected>1 hora</option>
                                <option value="240">4 horas</option>
                            </select>
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                        </div>
                    </div>

                    <div class="cfg-item">
                        <div class="cfg-item-copy">
                            <p class="cfg-item-title" id="cfgInactivosTitle">Bloquear acceso a usuarios inactivos</p>
                            <p class="cfg-item-desc">Impide automáticamente el inicio de sesión a cuentas marcadas como bajas o suspendidas.</p>
                        </div>
                        <label class="cfg-switch">
                            <input type="checkbox" name="bloqueo_inactivos" checked aria-labelledby="cfgInactivosTitle">
                            <span class="cfg-switch-track" aria-hidden="true"></span>
                        </label>
                    </div>
                </div>

                <h3 class="cfg-subhead">Accesos rápidos de administración</h3>
                <div class="cfg-links">
                    <a href="<?= base_url('usuarios') ?>" class="cfg-link">
                        <span class="cfg-link-icon" aria-hidden="true"><span class="material-symbols-outlined">manage_accounts</span></span>
                        <span class="cfg-link-copy">
                            <b>Usuarios y Permisos</b>
                            <small>Gestión CRUD operativa de cuentas de acceso y roles por módulo.</small>
                        </span>
                        <span class="material-symbols-outlined cfg-link-arrow" aria-hidden="true">arrow_forward</span>
                    </a>
                    <a href="<?= base_url('planes') ?>" class="cfg-link">
                        <span class="cfg-link-icon" aria-hidden="true"><span class="material-symbols-outlined">sell</span></span>
                        <span class="cfg-link-copy">
                            <b>Catálogo de Planes</b>
                            <small>Configuración de precios, tipos de plan y vigencias para clientes.</small>
                        </span>
                        <span class="material-symbols-outlined cfg-link-arrow" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
            </section>
        </div>
    </form>
</main>
