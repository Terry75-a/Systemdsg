<style>
    /* Estilos de jerarquía visual y alertas */
    .venc-card {
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        border-radius: 14px;
        background: var(--bs-body-bg, #ffffff);
    }
    .venc-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .venc-border-vencido { border-left: 6px solid #dc2626 !important; }
    .venc-border-hoy     { border-left: 6px solid #ea580c !important; }
    .venc-border-pronto  { border-left: 6px solid #f59e0b !important; }
    .venc-border-normal  { border-left: 6px solid #0284c7 !important; }

    /* Avatar de iniciales para la Persona */
    .avatar-persona {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* Etiquetas destacadas de Empresa y Sucursal */
    .entity-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .pill-empresa {
        background-color: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .pill-sucursal {
        background-color: rgba(234, 88, 12, 0.12);
        color: #c2410c;
        border: 1px solid rgba(234, 88, 12, 0.25);
    }

    /* Animación de pulso para vencimientos críticos (Hoy o Vencidos) */
    @keyframes pulseUrgent {
        0%   { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.45); }
        70%  { box-shadow: 0 0 0 8px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .badge-urgente {
        animation: pulseUrgent 2s infinite;
    }

    /* Botones de filtro */
    .filter-tab.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
    }
</style>

<!-- ENCABEZADO Y BOTÓN DE ENVÍO MANUAL -->
<div class="pdsg-page-head d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="pdsg-page-title mb-1">Notificaciones y Alertas de Vencimiento</h1>
        <p class="pdsg-page-sub mb-0">Control detallado por cliente, empresa y sucursal próxima a vencer</p>
    </div>
    <div>
        <button type="button" id="btnDispararAvisos" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm px-3 py-2">
            <span class="material-symbols-outlined" style="font-size: 20px;">send</span>
            <span>Enviar Recordatorios de Hoy (Correo / WhatsApp)</span>
        </button>
    </div>
</div>

<!-- TARJETAS KPI DINÁMICAS -->
<div class="row g-3 mb-4">
    <!-- Vencen Hoy -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card alert-card border-0 shadow-sm border-start border-danger border-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">¡Vencen Hoy!</span>
                    <h3 class="fw-bold my-1 text-danger"><?= esc($kpi['vencen_hoy'] ?? 0) ?></h3>
                    <small class="text-danger fw-medium">Atención inmediata</small>
                </div>
                <div class="alert-card__icon bg-danger-subtle text-danger">
                    <span class="material-symbols-outlined filled">notification_important</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Por Vencer -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card alert-card border-0 shadow-sm border-start border-warning border-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Próximos a Vencer</span>
                    <h3 class="fw-bold my-1 text-warning"><?= esc($kpi['por_vencer'] ?? 0) ?></h3>
                    <small class="text-muted">Dentro del rango de aviso</small>
                </div>
                <div class="alert-card__icon bg-warning-subtle text-warning">
                    <span class="material-symbols-outlined filled">schedule</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Planes Vencidos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card alert-card border-0 shadow-sm border-start border-secondary border-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Servicios Vencidos</span>
                    <h3 class="fw-bold my-1 text-dark"><?= esc($kpi['vencidos'] ?? 0) ?></h3>
                    <small class="text-muted">Pendientes de renovación o corte</small>
                </div>
                <div class="alert-card__icon bg-secondary-subtle text-secondary">
                    <span class="material-symbols-outlined filled">warning</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monto Total / Notificados Hoy -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card alert-card border-0 shadow-sm border-start border-primary border-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total por Cobrar</span>
                    <h3 class="fw-bold my-1 text-primary">S/ <?= number_format((float)($kpi['monto_total'] ?? 0), 2) ?></h3>
                    <small class="text-success fw-medium">✓ <?= esc($kpi['notificados'] ?? 0) ?> notificados hoy</small>
                </div>
                <div class="alert-card__icon bg-primary-subtle text-primary">
                    <span class="material-symbols-outlined filled">payments</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BARRA DE FILTROS Y BUSCADOR -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
        <div class="d-flex flex-wrap gap-2" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold filter-tab active" data-filter="todos">
                Todos (<?= count($registros ?? []) ?>)
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm fw-semibold filter-tab" data-filter="hoy">
                🔥 ¡Vencen Hoy! (<?= esc($kpi['vencen_hoy'] ?? 0) ?>)
            </button>
            <button type="button" class="btn btn-outline-warning btn-sm fw-semibold filter-tab" data-filter="por_vencer">
                ⏳ Por Vencer (<?= esc($kpi['por_vencer'] ?? 0) ?>)
            </button>
            <button type="button" class="btn btn-outline-dark btn-sm fw-semibold filter-tab" data-filter="vencidos">
                ⚠️ Vencidos (<?= esc($kpi['vencidos'] ?? 0) ?>)
            </button>
        </div>

        <div class="position-relative" style="min-width: 280px; flex: 1; max-width: 400px;">
            <input type="text" id="buscadorAlertas" class="form-control form-control-sm ps-4 py-2" 
                   placeholder="Buscar por persona, empresa, sucursal o plan...">
            <span class="material-symbols-outlined position-absolute text-muted" 
                  style="left: 8px; top: 50%; transform: translateY(-50%); font-size: 18px;">search</span>
        </div>
    </div>
</div>

<!-- LISTADO DE TARJETAS RESALTADAS (PERSONA -> EMPRESA -> SUCURSAL) -->
<?php if (empty($registros)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <span class="material-symbols-outlined text-success mb-2" style="font-size: 56px;">task_alt</span>
            <h5 class="fw-bold">¡Todo al día!</h5>
            <p class="text-muted mb-0">No hay personas ni sucursales con planes próximos a vencer en este momento.</p>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-3" id="contenedorAlertas">
        <?php foreach ($registros as $reg): 
            $dias = (int) $reg->dias_restantes;
            $esPlan = ((int) $reg->id_tipo_pago === 1);
            $nombreServicio = $esPlan
                ? ($reg->nombre_plan ?? 'Plan de Servicio') . (!empty($reg->tipo_plan_nombre) ? " ({$reg->tipo_plan_nombre})" : '')
                : ($reg->concepto ?: 'Servicio Adicional');

            // Clasificación visual según urgencia
            if ($dias < 0) {
                $categoriaFiltro = 'vencidos';
                $claseBorde      = 'venc-border-vencido';
                $badgeColor      = 'bg-danger text-white badge-urgente';
                $avatarColor     = 'bg-danger-subtle text-danger';
                $textoUrgencia   = 'VENCIDO HACE ' . abs($dias) . ' DÍA(S)';
            } elseif ($dias === 0) {
                $categoriaFiltro = 'hoy';
                $claseBorde      = 'venc-border-hoy';
                $badgeColor      = 'bg-danger text-white badge-urgente';
                $avatarColor     = 'bg-danger-subtle text-danger';
                $textoUrgencia   = '¡VENCE HOY!';
            } elseif ($dias <= 3) {
                $categoriaFiltro = 'por_vencer';
                $claseBorde      = 'venc-border-pronto';
                $badgeColor      = 'bg-warning text-dark';
                $avatarColor     = 'bg-warning-subtle text-warning-emphasis';
                $textoUrgencia   = ($dias === 1) ? 'VENCE MAÑANA (1 DÍA)' : "VENCE EN {$dias} DÍAS";
            } else {
                $categoriaFiltro = 'por_vencer';
                $claseBorde      = 'venc-border-normal';
                $badgeColor      = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                $avatarColor     = 'bg-primary-subtle text-primary';
                $textoUrgencia   = "VENCE EN {$dias} DÍAS";
            }

            // Iniciales de la Persona
            $partesNombre = explode(' ', trim($reg->nombre_cliente ?? 'Cliente'));
            $iniciales = strtoupper(substr($partesNombre[0] ?? 'C', 0, 1) . substr($partesNombre[1] ?? '', 0, 1));

            // Teléfono limpio para botón directo de WhatsApp
            $telLimpio = preg_replace('/\D/', '', (string)($reg->telefono_sucursal ?? ''));
            if (strlen($telLimpio) === 9 && str_starts_with($telLimpio, '9')) {
                $telLimpio = '51' . $telLimpio;
            }
            $fechaFormateada = date('d/m/Y', strtotime($reg->fecha_de_vencimiento));
            $montoFormateado = number_format((float)$reg->precio, 2);

            $msgWhatsApp = rawurlencode(
                "Hola *{$reg->nombre_cliente}*, te escribimos de *DSG Perú Technology*. " .
                "Te recordamos que el servicio *{$nombreServicio}* de la empresa *{$reg->razon_social}* " .
                "(Sucursal: *{$reg->nombre_sucursal}*) tiene como fecha de vencimiento el *{$fechaFormateada}* " .
                "por un monto de *S/ {$montoFormateado}*."
            );

            $notificadoHoy = (!empty($reg->ultimo_aviso) && $reg->ultimo_aviso === $hoy);
        ?>
            <div class="card border-0 shadow-sm venc-card <?= $claseBorde ?> item-alerta"
                 data-categoria="<?= $categoriaFiltro ?>"
                 data-search="<?= esc(strtolower($reg->nombre_cliente . ' ' . $reg->razon_social . ' ' . $reg->nombre_sucursal . ' ' . $nombreServicio)) ?>">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center gy-3">
                        
                        <!-- 1. IDENTIDAD: PERSONA, EMPRESA Y SUCURSAL -->
                        <div class="col-12 col-lg-5">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar-persona <?= $avatarColor ?>">
                                    <?= esc($iniciales) ?>
                                </div>
                                <div class="w-100">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            Responsable / Titular:
                                        </span>
                                        <?php if ($notificadoHoy): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                                ✓ Avisado hoy
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                                                Pendiente de aviso hoy
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Nombre de la Persona bien resaltado -->
                                    <h5 class="fw-bold mb-2 text-body" style="font-size: 1.1rem;">
                                        <?= esc($reg->nombre_cliente ?: 'Sin nombre asignado') ?>
                                    </h5>

                                    <!-- Chips resaltados de EMPRESA y SUCURSAL -->
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="entity-pill pill-empresa" title="Empresa / Razón Social">
                                            <span class="material-symbols-outlined" style="font-size: 17px;">domain</span>
                                            <span><strong>Empresa:</strong> <?= esc($reg->razon_social ?: 'Sin Razón Social') ?></span>
                                        </span>

                                        <span class="entity-pill pill-sucursal" title="Sucursal / Sede">
                                            <span class="material-symbols-outlined" style="font-size: 17px;">storefront</span>
                                            <span><strong>Sucursal:</strong> <?= esc($reg->nombre_sucursal) ?></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. SERVICIO / PLAN Y CONTACTO -->
                        <div class="col-12 col-sm-6 col-lg-3 border-start-lg ps-lg-4">
                            <span class="text-muted d-block small mb-1">
                                <?= $esPlan ? '📦 Plan Contratado' : '🛠️ Servicio / Concepto' ?>
                            </span>
                            <div class="fw-bold text-body mb-2">
                                <?= esc($nombreServicio) ?>
                            </div>
                            <div class="d-flex flex-column gap-1 small text-muted">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 15px;">call</span>
                                    <span><?= esc($reg->telefono_sucursal ?: 'Sin teléfono') ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-truncate" title="<?= esc($reg->correo_cliente ?? '') ?>">
                                    <span class="material-symbols-outlined" style="font-size: 15px;">mail</span>
                                    <span class="text-truncate"><?= esc($reg->correo_cliente ?: 'Sin correo') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. FECHA DE VENCIMIENTO, SEMÁFORO Y MONTO -->
                        <div class="col-12 col-sm-6 col-lg-2 text-sm-end text-lg-center">
                            <span class="badge <?= $badgeColor ?> px-3 py-2 rounded-pill fw-bold mb-2 d-inline-block" style="font-size: 0.78rem;">
                                ⏰ <?= esc($textoUrgencia) ?>
                            </span>
                            <div class="small text-muted">
                                Vence: <strong class="text-body"><?= $fechaFormateada ?></strong>
                            </div>
                            <div class="fs-5 fw-bold text-primary mt-1">
                                S/ <?= $montoFormateado ?>
                            </div>
                        </div>

                        <!-- 4. BOTONES DE ACCIÓN DIRECTA -->
                        <div class="col-12 col-lg-2 d-flex flex-lg-column justify-content-end gap-2">
                            <a href="<?= base_url('personasadd/' . $reg->id_persona) ?>" 
                               class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center gap-1 fw-semibold">
                                <span class="material-symbols-outlined" style="font-size: 17px;">open_in_new</span>
                                <span>Ver Cliente / Renovar</span>
                            </a>

                            <?php if (!empty($telLimpio)): ?>
                                <a href="https://wa.me/<?= esc($telLimpio) ?>?text=<?= $msgWhatsApp ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center gap-1 fw-semibold">
                                    <span class="material-symbols-outlined" style="font-size: 17px;">chat</span>
                                    <span>WhatsApp Directo</span>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botonesFiltro = document.querySelectorAll('.filter-tab');
    const inputBuscador = document.getElementById('buscadorAlertas');
    const tarjetas      = document.querySelectorAll('.item-alerta');
    let filtroActual    = 'todos';

    function filtrarTarjetas() {
        const texto = (inputBuscador ? inputBuscador.value.toLowerCase().trim() : '');

        tarjetas.forEach(card => {
            const cat = card.getAttribute('data-categoria');
            const info = card.getAttribute('data-search') || '';

            const coincideCategoria = (filtroActual === 'todos') || (cat === filtroActual);
            const coincideTexto     = info.includes(texto);

            card.style.display = (coincideCategoria && coincideTexto) ? '' : 'none';
        });
    }

    botonesFiltro.forEach(btn => {
        btn.addEventListener('click', function () {
            botonesFiltro.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtroActual = this.getAttribute('data-filter');
            filtrarTarjetas();
        });
    });

    if (inputBuscador) {
        inputBuscador.addEventListener('input', filtrarTarjetas);
    }

    // Disparar recordatorios manualmente (Correo + WhatsApp API)
    const btnDisparar = document.getElementById('btnDispararAvisos');
    if (btnDisparar) {
        btnDisparar.addEventListener('click', function () {
            const textoOriginal = btnDisparar.innerHTML;
            btnDisparar.disabled = true;
            btnDisparar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> <span>Enviando avisos...</span>';

            fetch('<?= base_url('notificaciones/enviar-correos') ?>')
                .then(res => res.json())
                .then(data => {
                    const env = data?.resultado?.enviados ?? 0;
                    alert(`Proceso completado. Se enviaron ${env} recordatorio(s) pendientes del día de hoy.`);
                    window.location.reload();
                })
                .catch(() => {
                    alert('Ocurrió un error al intentar procesar los recordatorios.');
                    btnDisparar.disabled = false;
                    btnDisparar.innerHTML = textoOriginal;
                });
        });
    }
});
</script>