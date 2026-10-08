<main class="rg-content">

    <!-- ═══════════ HERO ═══════════ -->
    <section class="rg-hero rg-wrap">
        <div class="rg-hero-grid">
            <div class="rg-hero-main">
                <div class="rg-badges">
                    <span class="rg-badge-chip"><i class="fa-solid fa-receipt"></i> Sin letra pequeña</span>
                    <span class="rg-badge-chip"><i class="fa-solid fa-rotate"></i> Cambia de plan cuando quieras</span>
                    <span class="rg-badge-chip"><i class="fa-solid fa-headset"></i> Soporte 24/7 incluido</span>
                </div>

                <h1 class="rg-h1">Un plan para cada <span class="grad">tamaño de negocio</span></h1>
            </div>

            <div class="rg-hero-side">
                <p class="rg-hero-lead">
                    Elige el plan que acompaña tu botica, farmacia o negocio hoy. Puedes subir o bajar de plan
                    cuando quieras, y las páginas web se cotizan aparte.
                </p>

                <form class="rg-lead-form">
                    <input type="email" name="correo" placeholder="Tu correo de trabajo" required>
                    <button class="rg-btn rg-btn-dark" type="submit">
                        Hablar con ventas <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <p class="rg-micro">Precios en soles · Sin costos ocultos · Sin contrato de permanencia</p>
            </div>
        </div>

        <div class="rg-hero-stats">
            <div class="rg-stat"><b>S/ 900</b><span>Plan Esencial al mes</span></div>
            <div class="rg-stat is-acc"><b>S/ 990</b><span>Plan Negocio al mes</span></div>
            <div class="rg-stat"><b>24h</b><span>Puesta en marcha</span></div>
            <div class="rg-logos">
                <span>VIFARMA</span>
                <span>CARPANETO</span>
                <span>HUELLITAS</span>
            </div>
        </div>
    </section>

    <!-- ═══════════ PLANES (cuestionario + filas) ═══════════ -->
    <section class="rg-sec" id="planes">
        <div class="rg-wrap">
            <div class="rg-split">
                <div>
                    <div class="rg-head">
                        <span class="rg-tag">Precios claros</span>
                        <h2 class="rg-h2">Encuentra el plan que <span class="grad">te corresponde</span></h2>
                        <p class="rg-sub">
                            Responde dos preguntas y mira cuál encaja con tu operación. El precio final depende de
                            tu sector, sedes y módulos.
                        </p>
                    </div>

                    <div class="rg-quiz">
                        <div class="rg-quiz-block">
                            <span>¿Qué tipo de negocio tienes?</span>
                            <div class="rg-seg">
                                <button class="on" type="button"><span class="radio"></span> Botica</button>
                                <button type="button"><span class="radio"></span> Restaurante</button>
                                <button type="button"><span class="radio"></span> Minimarket</button>
                            </div>
                        </div>
                        <div class="rg-quiz-block">
                            <span>¿Cuántas sedes operan hoy?</span>
                            <div class="rg-seg">
                                <button class="on" type="button"><span class="radio"></span> 1 sede</button>
                                <button type="button"><span class="radio"></span> 2 sedes</button>
                                <button type="button"><span class="radio"></span> 3 o más</button>
                            </div>
                        </div>
                        <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-dark">
                            Recomendarme un plan <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="rg-plan-rows">
                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name">💊 Esencial</span>
                            <span class="rg-plan-price">S/ 900 <small>/mes</small></span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Para una botica o tienda que quiere ordenarse: caja, inventario y facturación sin complicaciones.</p>
                            <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-soft">Cotizar <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                        <ul class="rg-plan-list">
                            <li class="yes"><i class="fa-solid fa-check"></i> Punto de venta completo</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Facturación electrónica SUNAT</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> 1 sede · 2 usuarios</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Inventario con control de stock</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Reporte diario de ventas</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Capacitación inicial</li>
                            <li class="no"><i class="fa-solid fa-minus"></i> Módulos por sector (lotes, KDS, reservas)</li>
                            <li class="no"><i class="fa-solid fa-minus"></i> Multi-sede y accesos por rol</li>
                        </ul>
                    </article>

                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name">🏪 Negocio</span>
                            <span class="rg-plan-price">S/ 990 <small>/mes</small></span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Para boticas y negocios que venden todos los días, con módulo de tu sector y soporte prioritario.</p>
                            <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-dark">Cotizar <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <ul class="rg-plan-list">
                            <li class="yes"><i class="fa-solid fa-check"></i> Todo lo del plan Esencial</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Módulo de tu sector (lotes, cocina, reservas…)</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> 2 sedes · usuarios ilimitados</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Inventario avanzado + alertas de stock</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Reportes e informes completos</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Integración con delivery</li>
                            <li class="yes"><i class="fa-solid fa-check"></i> Soporte prioritario 24/7</li>
                            <li class="no"><i class="fa-solid fa-minus"></i> Desarrollos a medida</li>
                        </ul>
                    </article>

                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name">🏢 Corporativo</span>
                            <span class="rg-plan-price">A medida</span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Para cadenas y operaciones que necesitan módulos propios, integraciones y acompañamiento dedicado.</p>
                            <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-soft">Hablar con ventas <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                        <ul class="rg-plan-list">
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
            </div>

            <p class="rg-note">Precios referenciales en soles. El precio final depende de tu sector, sedes y módulos. Sin costos ocultos.</p>
        </div>
    </section>

    <!-- ═══════════ FAQ (2 columnas) ═══════════ -->
    <section class="rg-sec" id="faq">
        <div class="rg-wrap">
            <div class="rg-faq-split">
                <aside class="rg-faq-aside">
                    <h2 class="rg-h2">Preguntas</h2>
                    <p>Lo que más nos preguntan antes de contratar. Si falta la tuya, escríbenos y te respondemos hoy mismo.</p>
                    <div class="rg-faq-aside-btns">
                        <a href="<?= base_url('/#contacto') ?>" aria-label="Escríbenos"><i class="fa-regular fa-envelope"></i></a>
                        <a href="tel:+51923942001" aria-label="Llámanos"><i class="fa-solid fa-phone"></i></a>
                        <a href="https://wa.me/51923942001" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="<?= base_url('servicios') ?>#faq" aria-label="Más preguntas"><i class="fa-regular fa-circle-question"></i></a>
                        <a href="<?= base_url('precio') ?>#planes" aria-label="Volver a planes"><i class="fa-solid fa-arrow-up"></i></a>
                    </div>
                </aside>

                <div class="rg-faq">
                    <details class="rg-faq-item">
                        <summary>¿El pago es mensual o pago único? <span class="chev"><i class="fa-solid fa-chevron-down"></i></span></summary>
                        <p>Los planes son de suscripción mensual e incluyen soporte, actualizaciones y facturación electrónica. También ofrecemos pago anual con 2 meses de descuento.</p>
                    </details>
                    <details class="rg-faq-item">
                        <summary>¿Necesito comprar equipos? <span class="chev"><i class="fa-solid fa-chevron-down"></i></span></summary>
                        <p>No necesariamente: el sistema funciona en tus computadoras y celulares actuales. Si necesitas impresoras, lectores o cajones de dinero, te asesoramos en la compra al mejor precio.</p>
                    </details>
                    <details class="rg-faq-item">
                        <summary>¿Puedo cambiar de plan después? <span class="chev"><i class="fa-solid fa-chevron-down"></i></span></summary>
                        <p>Sí, cuando quieras. Si tu negocio crece, subes de plan conservando toda tu información. Si necesitas ajustar, también puedes bajar sin penalidades.</p>
                    </details>
                    <details class="rg-faq-item">
                        <summary>¿Hay contrato de permanencia? <span class="chev"><i class="fa-solid fa-chevron-down"></i></span></summary>
                        <p>No exigimos permanencia mínima en los planes Esencial y Negocio. En Corporativo se acuerda según el alcance del proyecto.</p>
                    </details>
                    <details class="rg-faq-item">
                        <summary>¿Hacen páginas web por separado? <span class="chev"><i class="fa-solid fa-chevron-down"></i></span></summary>
                        <p>Sí. Landing, catálogo o tienda con tu propio diseño se cotizan aparte según las páginas y los módulos que necesites, e incluyen dominio, hosting y conexión con WhatsApp.</p>
                    </details>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="rg-sec">
        <div class="rg-wrap">
            <div class="rg-cta-panel">
                <span class="rg-tag">Demo gratuita</span>
                <h2 class="rg-h2">¿No sabes qué plan <span class="grad">te conviene?</span></h2>
                <p class="rg-sub">Conversemos 15 minutos y te recomendamos el plan exacto para tu botica o negocio, sin compromiso.</p>
                <form class="rg-lead-form">
                    <input type="email" name="correo" placeholder="Tu correo de trabajo" required>
                    <button class="rg-btn rg-btn-dark" type="submit">
                        Solicitar demo <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

</main>
