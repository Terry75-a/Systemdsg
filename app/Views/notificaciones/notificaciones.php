<!-- ═══ Notificaciones · diseño System-Trackin (solo presentación; la lógica es la misma) ═══ -->
<div class="tk2-page ntf-page">

    <div class="tk2-title">
        <div>
            <h2>Avisos de vencimiento</h2>
            <p>Planes y servicios de cada cliente, empresa y sucursal que vencen pronto o ya vencieron.</p>
        </div>
        <div class="tk2-title-actions">
            <button type="button" id="btnDispararAvisos" class="tk2-btn tk2-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">send</span>
                <span>Enviar recordatorios de hoy</span>
            </button>
        </div>
    </div>

    <!-- Métricas -->
    <div class="tk2-stats ntf-stats" aria-label="Resumen de vencimientos">
        <div class="tk2-stat ntf-stat-hoy">
            <span class="material-symbols-outlined">notification_important</span>
            <p class="tk2-stat-label">Vencen hoy</p>
            <p class="tk2-stat-value"><?= esc($kpi['vencen_hoy'] ?? 0) ?></p>
            <span class="ntf-stat-sub">Atención inmediata</span>
        </div>
        <div class="tk2-stat ntf-stat-pronto">
            <span class="material-symbols-outlined">schedule</span>
            <p class="tk2-stat-label">Próximos a vencer</p>
            <p class="tk2-stat-value"><?= esc($kpi['por_vencer'] ?? 0) ?></p>
            <span class="ntf-stat-sub">Dentro del rango de aviso</span>
        </div>
        <div class="tk2-stat ntf-stat-vencido">
            <span class="material-symbols-outlined">event_busy</span>
            <p class="tk2-stat-label">Vencidos</p>
            <p class="tk2-stat-value"><?= esc($kpi['vencidos'] ?? 0) ?></p>
            <span class="ntf-stat-sub">Por renovar o cortar</span>
        </div>
        <div class="tk2-stat ntf-stat-monto">
            <span class="material-symbols-outlined">payments</span>
            <p class="tk2-stat-label">Total por cobrar</p>
            <p class="tk2-stat-value">S/ <?= number_format((float)($kpi['monto_total'] ?? 0), 2) ?></p>
            <span class="ntf-stat-sub"><?= esc($kpi['notificados'] ?? 0) ?> avisados hoy</span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="tk2-toolbar">
        <div class="tk2-chips" role="group" aria-label="Filtrar avisos">
            <button type="button" class="tk2-chip filter-tab active" data-filter="todos">
                Todos <span class="pdsg-tab-count"><?= count($registros ?? []) ?></span>
            </button>
            <button type="button" class="tk2-chip filter-tab ntf-chip-hoy" data-filter="hoy">
                <span class="ntf-dot"></span> Vencen hoy <span class="pdsg-tab-count"><?= esc($kpi['vencen_hoy'] ?? 0) ?></span>
            </button>
            <button type="button" class="tk2-chip filter-tab ntf-chip-pronto" data-filter="por_vencer">
                <span class="ntf-dot"></span> Por vencer <span class="pdsg-tab-count"><?= esc($kpi['por_vencer'] ?? 0) ?></span>
            </button>
            <button type="button" class="tk2-chip filter-tab ntf-chip-vencido" data-filter="vencidos">
                <span class="ntf-dot"></span> Vencidos <span class="pdsg-tab-count"><?= esc($kpi['vencidos'] ?? 0) ?></span>
            </button>
        </div>
        <label class="tk2-search">
            <span class="material-symbols-outlined" aria-hidden="true">search</span>
            <input type="text" id="buscadorAlertas" placeholder="Buscar persona, empresa, sucursal o plan…" autocomplete="off">
        </label>
    </div>

    <!-- Lista -->
    <?php if (empty($registros)): ?>
        <div class="tk2-card ntf-empty">
            <span class="ntf-empty-icon"><span class="material-symbols-outlined">task_alt</span></span>
            <h3>Todo al día</h3>
            <p>No hay personas ni sucursales con planes próximos a vencer en este momento.</p>
        </div>
    <?php else: ?>
        <div class="tk2-card ntf-list" id="contenedorAlertas">
            <?php foreach ($registros as $reg):
                $dias = (int) $reg->dias_restantes;
                $esPlan = ((int) $reg->id_tipo_pago === 1);
                $nombreServicio = $esPlan
                    ? ($reg->nombre_plan ?? 'Plan de Servicio') . (!empty($reg->tipo_plan_nombre) ? " ({$reg->tipo_plan_nombre})" : '')
                    : ($reg->concepto ?: 'Servicio Adicional');

                // Clasificación según urgencia (misma regla de antes)
                if ($dias < 0) {
                    $categoriaFiltro = 'vencidos';
                    $nivel           = 'vencido';
                    $textoUrgencia   = 'Vencido hace ' . abs($dias) . ' día(s)';
                } elseif ($dias === 0) {
                    $categoriaFiltro = 'hoy';
                    $nivel           = 'hoy';
                    $textoUrgencia   = '¡Vence hoy!';
                } elseif ($dias <= 3) {
                    $categoriaFiltro = 'por_vencer';
                    $nivel           = 'pronto';
                    $textoUrgencia   = ($dias === 1) ? 'Vence mañana' : "Vence en {$dias} días";
                } else {
                    $categoriaFiltro = 'por_vencer';
                    $nivel           = 'normal';
                    $textoUrgencia   = "Vence en {$dias} días";
                }

                $partesNombre = explode(' ', trim($reg->nombre_cliente ?? 'Cliente'));
                $iniciales = strtoupper(substr($partesNombre[0] ?? 'C', 0, 1) . substr($partesNombre[1] ?? '', 0, 1));

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
                <article class="ntf-item ntf-<?= $nivel ?> item-alerta"
                     data-categoria="<?= $categoriaFiltro ?>"
                     data-search="<?= esc(strtolower($reg->nombre_cliente . ' ' . $reg->razon_social . ' ' . $reg->nombre_sucursal . ' ' . $nombreServicio)) ?>">

                    <div class="ntf-who">
                        <span class="ntf-avatar"><?= esc($iniciales) ?></span>
                        <div class="ntf-who-main">
                            <div class="ntf-name-row">
                                <b class="ntf-name"><?= esc($reg->nombre_cliente ?: 'Sin nombre asignado') ?></b>
                                <?php if ($notificadoHoy): ?>
                                    <span class="ntf-flag is-ok"><span class="material-symbols-outlined">done</span> Avisado hoy</span>
                                <?php else: ?>
                                    <span class="ntf-flag">Sin aviso hoy</span>
                                <?php endif; ?>
                            </div>
                            <div class="ntf-tags">
                                <span class="ntf-tag" title="Empresa / Razón social"><span class="material-symbols-outlined">domain</span><?= esc($reg->razon_social ?: 'Sin razón social') ?></span>
                                <span class="ntf-tag" title="Sucursal"><span class="material-symbols-outlined">storefront</span><?= esc($reg->nombre_sucursal) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="ntf-service">
                        <span class="ntf-label"><?= $esPlan ? 'Plan contratado' : 'Servicio / concepto' ?></span>
                        <b><?= esc($nombreServicio) ?></b>
                        <span class="ntf-contact"><span class="material-symbols-outlined">call</span><?= esc($reg->telefono_sucursal ?: 'Sin teléfono') ?></span>
                        <span class="ntf-contact" title="<?= esc($reg->correo_cliente ?? '') ?>"><span class="material-symbols-outlined">mail</span><span class="ntf-trunc"><?= esc($reg->correo_cliente ?: 'Sin correo') ?></span></span>
                    </div>

                    <div class="ntf-due">
                        <span class="ntf-pill"><span class="ntf-dot"></span><?= esc($textoUrgencia) ?></span>
                        <span class="ntf-date">Vence el <b><?= $fechaFormateada ?></b></span>
                        <b class="ntf-amount">S/ <?= $montoFormateado ?></b>
                    </div>

                    <div class="ntf-actions">
                        <a href="<?= base_url('personasadd/' . $reg->id_persona) ?>" class="tk2-btn tk2-btn-primary ntf-btn">
                            <span class="material-symbols-outlined">open_in_new</span><span>Ver / renovar</span>
                        </a>
                        <?php if (!empty($telLimpio)): ?>
                            <a href="https://wa.me/<?= esc($telLimpio) ?>?text=<?= $msgWhatsApp ?>" target="_blank" rel="noopener" class="tk2-btn tk2-btn-ghost ntf-btn ntf-wa">
                                <span class="material-symbols-outlined">chat</span><span>WhatsApp</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

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
            botonesFiltro.forEach(b => b.classList.remove('active', 'is-active'));
            this.classList.add('active', 'is-active');
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
