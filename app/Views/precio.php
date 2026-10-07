<main class="db-main">

    <!-- ═══════════ HERO ═══════════ -->
    <section class="db-hero db-page-hero db-wrap">
        <span class="db-tag">Precios claros, sin letra pequeña</span>

        <h1 class="db-h1">Un plan para cada <span class="grad">tamaño de negocio</span></h1>

        <p class="db-sub">
            Elige el plan que acompaña tu operación hoy. Puedes subir o bajar de plan cuando quieras.
        </p>

        <div class="db-hero-actions">
            <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-lilac">
                Hablar con ventas <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="<?= base_url('servicios') ?>" class="db-btn db-btn-ghost">Ver sistemas</a>
        </div>
    </section>

    <!-- ═══════════ PLANES ═══════════ -->
    <section class="db-sec">
        <div class="db-wrap">
            <div class="db-plans">

                <!-- PLAN ESENCIAL -->
                <article class="db-plan">
                    <h3>Esencial</h3>
                    <p class="for">Para empezar a ordenar tu negocio.</p>
                    <p class="db-price"><b>S/ 900</b>/mes</p>
                    <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-ghost">Cotizar Esencial</a>
                    <ul class="db-plan-list">
                        <li class="yes"><i class="fa-solid fa-check"></i> Punto de venta completo</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Facturación electrónica SUNAT</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> 1 sede · 2 usuarios</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Inventario básico</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Reporte diario de ventas</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Capacitación inicial</li>
                        <li class="no"><i class="fa-solid fa-minus"></i> Módulos por sector (KDS, lotes, reservas)</li>
                        <li class="no"><i class="fa-solid fa-minus"></i> Multi-sede y accesos por rol</li>
                    </ul>
                </article>

                <!-- PLAN NEGOCIO -->
                <article class="db-plan db-plan-feat">
                    <span class="db-badge">Más popular</span>
                    <h3>Negocio</h3>
                    <p class="for">Para operaciones que venden todos los días.</p>
                    <p class="db-price"><b>S/ 990</b>/mes</p>
                    <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-dark">
                        Cotizar Negocio <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <ul class="db-plan-list">
                        <li class="yes"><i class="fa-solid fa-check"></i> Todo lo del plan Esencial</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Módulo de tu sector (cocina, lotes, reservas…)</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> 2 sedes · usuarios ilimitados</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Inventario avanzado + alertas de stock</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Reportes e informes completos</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Integración con delivery</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Soporte prioritario 24/7</li>
                        <li class="no"><i class="fa-solid fa-minus"></i> Desarrollos a medida</li>
                    </ul>
                </article>

                <!-- PLAN CORPORATIVO -->
                <article class="db-plan">
                    <h3>Corporativo</h3>
                    <p class="for">Para cadenas y operaciones a medida.</p>
                    <p class="db-price"><b>Cotizar</b></p>
                    <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-ghost">Hablar con ventas</a>
                    <ul class="db-plan-list">
                        <li class="yes"><i class="fa-solid fa-check"></i> Todo lo del plan Negocio</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Sedes y usuarios ilimitados</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Módulos desarrollados a medida</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Integraciones con tus sistemas actuales</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Migración de tu información histórica</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> Gerente de cuenta asignado</li>
                        <li class="yes"><i class="fa-solid fa-check"></i> SLA y capacitaciones programadas</li>
                    </ul>
                </article>
            </div>

            <p class="db-note">Precios referenciales en soles. El precio final depende de tu sector, sedes y módulos. Sin costos ocultos.</p>

            <h3 class="db-faq-title">Preguntas sobre precios</h3>
            <div class="db-faq">
                <details class="db-faq-item">
                    <summary>¿El pago es mensual o pago único?</summary>
                    <p>Los planes son de suscripción mensual e incluyen soporte, actualizaciones y facturación electrónica. También ofrecemos pago anual con 2 meses de descuento.</p>
                </details>
                <details class="db-faq-item">
                    <summary>¿Necesito comprar equipos?</summary>
                    <p>No necesariamente: el sistema funciona en tus computadoras y celulares actuales. Si necesitas impresoras, lectores o cajones de dinero, te asesoramos en la compra al mejor precio.</p>
                </details>
                <details class="db-faq-item">
                    <summary>¿Puedo cambiar de plan después?</summary>
                    <p>Sí, cuando quieras. Si tu negocio crece, subes de plan conservando toda tu información. Si necesitas ajustar, también puedes bajar sin penalidades.</p>
                </details>
                <details class="db-faq-item">
                    <summary>¿Hay contrato de permanencia?</summary>
                    <p>No exigimos permanencia mínima en los planes Esencial y Negocio. En Corporativo se acuerda según el alcance del proyecto.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="db-sec db-cta-band">
        <div class="db-wrap">
            <div class="db-cta-box">
                <div class="db-head" style="margin-bottom:0;">
                    <span class="db-tag">Demo gratuita</span>
                    <h2 class="db-h2">¿No sabes qué plan <span class="grad">te conviene?</span></h2>
                    <p class="db-sub">Conversemos 15 minutos y te recomendamos el plan exacto para tu negocio, sin compromiso.</p>
                </div>
                <div class="db-hero-actions">
                    <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-lilac">
                        Solicitar demo <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="<?= base_url('servicios') ?>" class="db-btn db-btn-ghost">Ver sistemas</a>
                </div>
            </div>
        </div>
    </section>

</main>
