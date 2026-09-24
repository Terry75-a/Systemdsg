<main class="services-page">

    <!-- ═══════════ HERO ═══════════ -->
    <section class="services-hero">
        <div class="container">
            <div class="services-intro">
                <span class="badge-solutions"><span class="badge-dot"></span> Nuestras soluciones</span>
                <h1 class="main-title">Sistemas especializados para tu industria</h1>
                <p class="main-subtitle">
                    Desarrollamos software a medida diseñado específicamente para las necesidades únicas de cada sector.
                    Desde restaurantes hasta hoteles, tenemos la solución perfecta para tu negocio.
                </p>
                <div class="cta-buttons services-hero-ctas">
                    <a href="<?= base_url('/#contacto') ?>" class="btn-primary">Solicitar demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= base_url('precio') ?>" class="btn-cyan">Ver precios</a>
                </div>
                <div class="services-hero-stats">
                    <div><strong>6</strong><span>Sistemas especializados</span></div>
                    <div><strong>500+</strong><span>Empresas atendidas</span></div>
                    <div><strong>24h</strong><span>Implementación</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ ÍNDICE ═══════════ -->
    <nav class="services-index" aria-label="Índice de sistemas">
        <div class="container">
            <a href="#restaurantes"><i class="fa-solid fa-utensils"></i> Restaurantes</a>
            <a href="#boticas"><i class="fa-solid fa-capsules"></i> Boticas</a>
            <a href="#minimarkets"><i class="fa-solid fa-shop"></i> Minimarkets</a>
            <a href="#hoteles"><i class="fa-solid fa-bed"></i> Hoteles</a>
            <a href="#cafes"><i class="fa-solid fa-mug-saucer"></i> Cafés</a>
            <a href="#medida"><i class="fa-solid fa-pen-ruler"></i> A medida</a>
        </div>
    </nav>

    <!-- ═══════════ 01 RESTAURANTES ═══════════ -->
    <section class="sys-block" id="restaurantes">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">01</span>
                    <div class="sys-icon"><i class="fa-solid fa-utensils"></i></div>
                    <div class="sys-stat"><strong>2x</strong><span>más rápida la atención en mesa</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Restaurantes · Pollerías · Pizzerías</p>
                    <h2>Sistema para restaurantes</h2>
                    <p class="sys-desc">Del pedido a la cuenta sin papeles perdidos: tus mozos toman pedidos desde el celular, la cocina los ve al instante en pantalla y la caja factura en segundos. Menos errores, mesas que rotan más rápido y clientes que vuelven.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Gestión de mesas y pedidos en tiempo real</li>
                        <li><i class="fa-solid fa-check"></i> Pantalla de cocina (KDS) sin comandas en papel</li>
                        <li><i class="fa-solid fa-check"></i> Facturación electrónica SUNAT integrada</li>
                        <li><i class="fa-solid fa-check"></i> Integración con apps de delivery</li>
                        <li><i class="fa-solid fa-check"></i> Control de caja, turnos y propinas por mozo</li>
                        <li><i class="fa-solid fa-check"></i> Reportes de platos más vendidos y horarios pico</li>
                    </ul>
                    <p class="sys-ideal"><strong>Ideal para:</strong> pollerías, cevicherías, pizzerías, chifas y restobares.</p>
                    <a href="<?= base_url('/#contacto') ?>" class="link-more">Cotizar este sistema <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ 02 BOTICAS ═══════════ -->
    <section class="sys-block sys-alt" id="boticas">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">02</span>
                    <div class="sys-icon"><i class="fa-solid fa-capsules"></i></div>
                    <div class="sys-stat"><strong>0</strong><span>vencidos con alertas automáticas</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Boticas · Farmacias · Droguerías</p>
                    <h2>Sistema para boticas y farmacias</h2>
                    <p class="sys-desc">Cada lote y cada vencimiento bajo control: el sistema te avisa antes de que un producto caduque, gestiona recetas médicas y mantiene tu stock exacto para no perder ni una venta.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Control de lotes y fechas de vencimiento</li>
                        <li><i class="fa-solid fa-check"></i> Gestión de recetas médicas retenidas</li>
                        <li><i class="fa-solid fa-check"></i> Alertas de stock mínimo por producto</li>
                        <li><i class="fa-solid fa-check"></i> Integración con DIGEMID</li>
                        <li><i class="fa-solid fa-check"></i> Compras y cuentas por pagar a proveedores</li>
                        <li><i class="fa-solid fa-check"></i> Ventas con facturación electrónica</li>
                    </ul>
                    <p class="sys-ideal"><strong>Ideal para:</strong> boticas independientes, cadenas de farmacias y droguerías.</p>
                    <a href="<?= base_url('/#contacto') ?>" class="link-more">Cotizar este sistema <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ 03 MINIMARKETS ═══════════ -->
    <section class="sys-block" id="minimarkets">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">03</span>
                    <div class="sys-icon"><i class="fa-solid fa-shop"></i></div>
                    <div class="sys-stat"><strong>Seg</strong><span>cobro por producto con barcode</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Minimarkets · Bodegas · Licorerías</p>
                    <h2>Sistema para minimarkets</h2>
                    <p class="sys-desc">Colas cortas e inventario exacto: punto de venta táctil que cobra en segundos, lector de código de barras y control de existencias que se actualiza con cada venta.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Punto de venta táctil ultrarrápido</li>
                        <li><i class="fa-solid fa-check"></i> Lector de código de barras y balanza</li>
                        <li><i class="fa-solid fa-check"></i> Inventario en tiempo real por almacén</li>
                        <li><i class="fa-solid fa-check"></i> Gestión de proveedores y órdenes de compra</li>
                        <li><i class="fa-solid fa-check"></i> Promociones, combos y control de ofertas</li>
                        <li><i class="fa-solid fa-check"></i> Cierre de caja y control de faltantes</li>
                    </ul>
                    <p class="sys-ideal"><strong>Ideal para:</strong> minimarkets, bodegas, licorerías y tiendas de conveniencia.</p>
                    <a href="<?= base_url('/#contacto') ?>" class="link-more">Cotizar este sistema <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ 04 HOTELES ═══════════ -->
    <section class="sys-block sys-alt" id="hoteles">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">04</span>
                    <div class="sys-icon"><i class="fa-solid fa-bed"></i></div>
                    <div class="sys-stat"><strong>100%</strong><span>ocupación siempre visible</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Hoteles · Hostales · Alojamientos</p>
                    <h2>Sistema para hoteles</h2>
                    <p class="sys-desc">Recepción sin cuadernos: reservas, check-in y check-out en una sola pantalla, con el estado de cada habitación y los consumos del huésped cargados a su cuenta automáticamente.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Reservas y calendario de ocupación</li>
                        <li><i class="fa-solid fa-check"></i> Check-in / check-out en segundos</li>
                        <li><i class="fa-solid fa-check"></i> Estado de habitaciones (libre, ocupada, limpieza)</li>
                        <li><i class="fa-solid fa-check"></i> Consumos de restaurante y servicios a la cuenta</li>
                        <li><i class="fa-solid fa-check"></i> Tarifas por temporada y tipo de habitación</li>
                        <li><i class="fa-solid fa-check"></i> Reportes de ocupación e ingresos por periodo</li>
                    </ul>
                    <p class="sys-ideal"><strong>Ideal para:</strong> hoteles, hostales, hospedajes y departamentos por días.</p>
                    <a href="<?= base_url('/#contacto') ?>" class="link-more">Cotizar este sistema <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ 05 CAFÉS ═══════════ -->
    <section class="sys-block" id="cafes">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">05</span>
                    <div class="sys-icon"><i class="fa-solid fa-mug-saucer"></i></div>
                    <div class="sys-stat"><strong>QR</strong><span>carta digital sin imprimir</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Cafés · Pastelerías · Juguerías</p>
                    <h2>Sistema para cafés y pastelerías</h2>
                    <p class="sys-desc">Atención veloz en barra y mesa: comandas rápidas, carta digital con QR que se actualiza sola y control de costos por receta para que cada postre deje su margen.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Comandas rápidas para barra y mesa</li>
                        <li><i class="fa-solid fa-check"></i> Carta digital con código QR</li>
                        <li><i class="fa-solid fa-check"></i> Costos por receta y control de insumos</li>
                        <li><i class="fa-solid fa-check"></i> Pedidos para delivery y recojo</li>
                        <li><i class="fa-solid fa-check"></i> Caja rápida con medios de pago (Yape, Plin, tarjeta)</li>
                        <li><i class="fa-solid fa-check"></i> Reportes diarios de venta por producto</li>
                    </ul>
                    <p class="sys-ideal"><strong>Ideal para:</strong> cafeterías, pastelerías, juguerías y sangucherías.</p>
                    <a href="<?= base_url('/#contacto') ?>" class="link-more">Cotizar este sistema <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ 06 A MEDIDA ═══════════ -->
    <section class="sys-block sys-custom" id="medida">
        <div class="container">
            <div class="sys-grid">
                <div class="sys-side">
                    <span class="sys-num">06</span>
                    <div class="sys-icon"><i class="fa-solid fa-pen-ruler"></i></div>
                    <div class="sys-stat"><strong>Tú</strong><span>defines los módulos</span></div>
                </div>
                <div class="sys-main">
                    <p class="tagline">Distribuidoras · Ferreterías · Clínicas · Más</p>
                    <h2>Software a medida</h2>
                    <p class="sys-desc">¿Tu negocio no encaja en un sistema estándar? Lo construimos contigo: analizamos tus procesos y desarrollamos los módulos exactos que necesitas, ni uno de más.</p>
                    <ul class="sys-list">
                        <li><i class="fa-solid fa-check"></i> Levantamiento de procesos de tu empresa</li>
                        <li><i class="fa-solid fa-check"></i> Módulos personalizados (ventas, stock, personal, caja)</li>
                        <li><i class="fa-solid fa-check"></i> Integraciones con tus herramientas actuales</li>
                        <li><i class="fa-solid fa-check"></i> Capacitación y manuales para tu equipo</li>
                    </ul>
                    <div class="cta-buttons sys-cta">
                        <a href="<?= base_url('/#contacto') ?>" class="btn-primary">Conversemos de tu proyecto <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ TODO INCLUYE ═══════════ -->
    <section class="sys-included">
        <div class="container">
            <div class="features-header">
                <p class="tagline">Sin letra pequeña</p>
                <h2 class="section-title">Todo sistema incluye</h2>
            </div>
            <div class="included-grid">
                <div class="included-item"><i class="fa-solid fa-bolt"></i><div><strong>Implementación en 24h</strong><span>Instalado y funcionando al día siguiente.</span></div></div>
                <div class="included-item"><i class="fa-solid fa-graduation-cap"></i><div><strong>Capacitación incluida</strong><span>Tu equipo aprende a usarlo con nosotros.</span></div></div>
                <div class="included-item"><i class="fa-solid fa-headset"></i><div><strong>Soporte 24/7</strong><span>Ayuda real a toda hora, todos los días.</span></div></div>
                <div class="included-item"><i class="fa-solid fa-arrows-rotate"></i><div><strong>Actualizaciones</strong><span>Mejoras continuas sin costo adicional.</span></div></div>
                <div class="included-item"><i class="fa-solid fa-mobile-screen-button"></i><div><strong>Acceso móvil</strong><span>Supervisa tu negocio desde tu celular.</span></div></div>
                <div class="included-item"><i class="fa-solid fa-file-invoice"></i><div><strong>Facturación SUNAT</strong><span>Boletas y facturas electrónicas integradas.</span></div></div>
            </div>
        </div>
    </section>

    <!-- ═══════════ FAQ ═══════════ -->
    <section class="sys-faq">
        <div class="container">
            <div class="features-header">
                <p class="tagline">Dudas frecuentes</p>
                <h2 class="section-title">Preguntas frecuentes</h2>
            </div>
            <div class="faq-list">
                <details class="faq-item">
                    <summary>¿Cuánto demora tener mi sistema funcionando?</summary>
                    <p>Los sistemas por sector se implementan en menos de 24 horas, incluyendo configuración y capacitación básica. Un software a medida toma entre 2 y 6 semanas según los módulos.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Necesito comprar computadoras especiales?</summary>
                    <p>No. Nuestros sistemas funcionan en PCs, laptops, tablets y celulares comunes, con Windows o Android. Si necesitas equipos (impresoras, lectores, cajones), te asesoramos en la compra.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Capacitan a mi personal?</summary>
                    <p>Sí, toda implementación incluye capacitación para tu equipo y manuales de uso. Si entra personal nuevo, coordinamos refuerzos sin costo adicional el primer mes.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Funciona si tengo varias sedes?</summary>
                    <p>Sí. Los sistemas son multi-sede: cada local opera con su caja y stock, y tú ves todo consolidado desde tu celular o computadora.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Qué pasa si ya tengo otro sistema?</summary>
                    <p>Migramos tu información (productos, clientes, stock) sin que pares de vender. Coordinamos el cambio en horarios de bajo movimiento para no afectar tu operación.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="cta-band">
        <div class="container">
            <div class="cta-band-inner">
                <p class="tagline tagline-light">Demo gratuita</p>
                <h2 class="cta-band-title">¿Cuál sistema necesita tu negocio?</h2>
                <p class="cta-band-desc">Escríbenos hoy y te mostramos en 30 minutos el sistema exacto para tu sector.</p>
                <div class="cta-buttons cta-band-buttons">
                    <a href="<?= base_url('/#contacto') ?>" class="btn-light">Solicitar demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= base_url('precio') ?>" class="btn-ghost-light">Ver precios</a>
                </div>
            </div>
        </div>
    </section>

</main>
