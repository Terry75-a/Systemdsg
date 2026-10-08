<main class="rg-content">

    <!-- ══════════ HERO (2 columnas + form) ══════════ -->
    <section class="rg-hero rg-wrap">
        <div class="rg-hero-grid">
            <div class="rg-hero-main">
                <div class="rg-badges">
                    <span class="rg-badge-chip"><i class="fa-solid fa-capsules"></i> Boticas y farmacias</span>
                    <span class="rg-badge-chip"><i class="fa-solid fa-utensils"></i> Restaurantes</span>
                    <span class="rg-badge-chip"><i class="fa-solid fa-shop"></i> Minimarkets</span>
                </div>

                <h1 class="rg-h1">
                    El sistema que tu
                    <span class="grad" data-rotate='["botica","farmacia","tienda","página web"]'>botica</span>
                    necesita
                </h1>
            </div>

            <div class="rg-hero-side">
                <p class="rg-hero-lead">
                    Punto de venta, kardex, caja y facturación electrónica para boticas y farmacias,
                    restaurantes, minimarkets y páginas web a medida. Implementación en menos de 24 horas.
                </p>

                <form class="rg-lead-form">
                    <input type="email" name="correo" placeholder="Tu correo de trabajo" required>
                    <button class="rg-btn rg-btn-dark" type="submit">
                        Solicitar demo <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <p class="rg-micro">Demo sin costo · Soporte 24/7 · Sin contrato de permanencia</p>
            </div>
        </div>

        <div class="rg-hero-stats">
            <div class="rg-stat">
                <b><span id="stas-number">0</span>+</b>
                <span>Empresas atendidas</span>
            </div>
            <div class="rg-stat">
                <b id="experience-years">0</b>
                <span>Años de experiencia</span>
            </div>
            <div class="rg-stat is-acc">
                <b><span id="uptime-guaranteed">0</span>%</b>
                <span>Uptime garantizado</span>
            </div>
            <div class="rg-logos">
                <span>VIFARMA</span>
                <span>CARPANETO</span>
                <span>HUELLITAS</span>
            </div>
        </div>
    </section>

    <!-- ══════════ SERVICIOS (carrusel de tarjetas) ══════════ -->
    <section class="rg-sec" id="servicios">
        <div class="rg-wrap">
            <div class="rg-rail-head">
                <div class="rg-head" style="margin-bottom:0;">
                    <span class="rg-tag">Servicios</span>
                    <h2 class="rg-h2">Sistemas que se adaptan a <span class="grad">cada giro</span></h2>
                    <p class="rg-sub">
                        Empezamos por boticas y farmacias —nuestro giro más fuerte— y llevamos el mismo nivel
                        de detalle a restaurantes, minimarkets, hoteles y páginas web hechas a medida.
                    </p>
                </div>
                <div class="rg-rail-nav">
                    <button class="rg-arrow" type="button" data-rail-target="rgSvcRail" data-rail-prev aria-label="Tarjetas anteriores"><i class="fa-solid fa-arrow-left"></i></button>
                    <button class="rg-arrow" type="button" data-rail-target="rgSvcRail" data-rail-next aria-label="Tarjetas siguientes"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="rg-rail" id="rgSvcRail">
                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#boticas">
                    <span class="art"><img src="<?= base_url('images/svc-photo-botica.jpg') ?>" alt="Sistema para boticas y farmacias" loading="lazy"></span>
                    <div class="body">
                        <h3>Boticas y farmacias</h3>
                        <span class="rg-tags">
                            <span>Lotes y vencidos</span><span>Kardex</span><span>SUNAT y DIGEMID</span>
                        </span>
                    </div>
                </a>

                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#restaurantes">
                    <span class="art"><img src="<?= base_url('images/svc-photo-restaurante.jpg') ?>" alt="Sistema para restaurantes" loading="lazy"></span>
                    <div class="body">
                        <h3>Restaurantes</h3>
                        <span class="rg-tags">
                            <span>Mesas y pedidos</span><span>Comandas a cocina</span><span>Delivery</span>
                        </span>
                    </div>
                </a>

                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#minimarkets">
                    <span class="art"><img src="<?= base_url('images/svc-photo-minimarket.jpg') ?>" alt="Sistema para minimarkets" loading="lazy"></span>
                    <div class="body">
                        <h3>Minimarkets</h3>
                        <span class="rg-tags">
                            <span>POS táctil</span><span>Código de barras</span><span>Inventario</span>
                        </span>
                    </div>
                </a>

                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#web">
                    <span class="art"><img src="<?= base_url('images/svc-photo-web.jpg') ?>" alt="Páginas web a medida" loading="lazy"></span>
                    <div class="body">
                        <h3>Páginas web a medida</h3>
                        <span class="rg-tags">
                            <span>Diseño propio</span><span>SEO local</span><span>WhatsApp</span>
                        </span>
                    </div>
                </a>

                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#hoteles">
                    <span class="art"><img src="<?= base_url('images/svc-photo-hoteles.jpg') ?>" alt="Sistema para hoteles y cafés" loading="lazy"></span>
                    <div class="body">
                        <h3>Hoteles y cafés</h3>
                        <span class="rg-tags">
                            <span>Reservas</span><span>Carta QR</span><span>Reportes</span>
                        </span>
                    </div>
                </a>

                <a class="rg-svc-card" href="<?= base_url('servicios') ?>#medida">
                    <span class="art"><img src="<?= base_url('images/svc-photo-medida.jpg') ?>" alt="Software a medida" loading="lazy"></span>
                    <div class="body">
                        <h3>Software a medida</h3>
                        <span class="rg-tags">
                            <span>Procesos propios</span><span>Integraciones</span><span>Migración</span>
                        </span>
                    </div>
                </a>
            </div>

            <div class="rg-actions" style="justify-content:center; margin-top:34px;">
                <a href="<?= base_url('servicios') ?>" class="rg-btn rg-btn-ghost">
                    Ver todos los sistemas <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════ PLANES (cuestionario + filas de plan) ══════════ -->
    <section class="rg-sec" id="planes">
        <div class="rg-wrap">
            <div class="rg-split">
                <div>
                    <div class="rg-head">
                        <span class="rg-tag">Planes</span>
                        <h2 class="rg-h2">El plan que se ajusta a <span class="grad">cómo creces</span></h2>
                        <p class="rg-sub">
                            Sube o baja de plan cuando quieras, sin penalidades. Las páginas web se cotizan aparte.
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
                        <a href="<?= base_url('precio') ?>" class="rg-btn rg-btn-dark">
                            Ver plan recomendado <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="rg-plan-rows">
                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name"><img src="<?= base_url('images/svc-photo-botica.jpg') ?>" alt=""> Esencial</span>
                            <span class="rg-plan-price">S/ 900 <small>/mes</small></span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Punto de venta, facturación electrónica y control de inventario para una botica o tienda que quiere ordenarse.</p>
                            <a href="<?= base_url('precio') ?>" class="rg-btn rg-btn-soft">Más detalles <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                    </article>

                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name"><img src="<?= base_url('images/svc-photo-minimarket.jpg') ?>" alt=""> Negocio</span>
                            <span class="rg-plan-price">S/ 990 <small>/mes</small></span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Módulo de tu sector, 2 sedes, usuarios ilimitados y soporte prioritario 24/7 para negocios que venden a diario.</p>
                            <a href="<?= base_url('precio') ?>" class="rg-btn rg-btn-soft">Más detalles <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                    </article>

                    <article class="rg-plan-row">
                        <div class="rg-plan-row-top">
                            <span class="rg-plan-name"><img src="<?= base_url('images/svc-photo-medida.jpg') ?>" alt=""> Corporativo</span>
                            <span class="rg-plan-price">A medida</span>
                        </div>
                        <div class="rg-plan-row-body">
                            <p>Sedes ilimitadas, módulos desarrollados a medida, integraciones y gerente de cuenta asignado.</p>
                            <a href="<?= base_url('precio') ?>" class="rg-btn rg-btn-soft">Más detalles <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════ POR QUÉ ELEGIRNOS (cuellos de botella) ══════════ -->
    <section class="rg-sec" id="por-que">
        <div class="rg-wrap">
            <div class="rg-split">
                <div>
                    <div class="rg-head">
                        <span class="rg-tag">Por qué elegirnos</span>
                        <h2 class="rg-h2">Resolvemos los cuellos de botella que <span class="grad">te frenan</span></h2>
                        <p class="rg-sub">
                            Los mismos problemas aparecen en cada negocio: productos vencidos, caja descuadrada y
                            reportes que llegan tarde. Los atacamos desde el primer día.
                        </p>
                    </div>

                    <div class="rg-quote-card">
                        <div class="who">
                            <img src="<?= base_url('images/vifarma.png') ?>" alt="Juan Alberto">
                            <div>
                                <h4>Juan Alberto</h4>
                                <p><b>Dueño de Vifarma</b> · Pucallpa</p>
                            </div>
                        </div>
                        <p>“Un sistema fácil e increíble. Nos da control total del negocio, simplifica todo y ahora tengo más tiempo para lo importante.”</p>
                    </div>
                </div>

                <div class="rg-dark-list">
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Cero vencidos: alertas por lote <b>semanas antes</b></div>
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Boletas y facturas <b>SUNAT y DIGEMID</b> al día</div>
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Kardex y stock <b>exacto</b> en cada venta</div>
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Caja cuadrada: <b>cero faltantes</b> al cierre</div>
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Soporte <b>24/7</b>, también en plena hora punta</div>
                    <div class="rg-dark-row"><span class="circ"><i class="fa-solid fa-arrow-right"></i></span> Sistema funcionando en <b>menos de 24 horas</b></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════ CÓMO TRABAJAMOS ══════════ -->
    <section class="rg-sec" id="proceso">
        <div class="rg-wrap">
            <div class="rg-head">
                <span class="rg-tag">Cómo trabajamos</span>
                <h2 class="rg-h2">De la llamada a tu sistema <span class="grad">en 3 pasos</span></h2>
                <p class="rg-sub">Sin reuniones eternas ni proyectos de meses: te escuchamos, lo instalamos y te acompañamos.</p>
            </div>

            <div class="rg-split">
                <div class="rg-steps">
                    <article class="rg-step">
                        <div class="rg-step-top">
                            <span class="rg-step-n">01</span>
                            <h3>Conversamos</h3>
                            <span class="chev"><i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                        <p>Escuchamos cómo opera tu negocio: cómo compras, cómo vendes y dónde se te pierde dinero en el día a día.</p>
                    </article>

                    <article class="rg-step">
                        <div class="rg-step-top">
                            <span class="rg-step-n">02</span>
                            <h3>Implementamos</h3>
                            <span class="chev"><i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                        <p>Instalamos, cargamos tus productos y capacitamos a tu equipo en menos de 24 horas. El primer día ya cobras con el sistema.</p>
                    </article>

                    <article class="rg-step">
                        <div class="rg-step-top">
                            <span class="rg-step-n">03</span>
                            <h3>Acompañamos</h3>
                            <span class="chev"><i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                        <p>Soporte 24/7 y actualizaciones constantes: si algo falla en pleno horario de ventas, respondemos igual.</p>
                        <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-dark">
                            Empezar hoy <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </article>
                </div>

                <div class="rg-step-visual">
                    <div class="glass">
                        <b>Ventas de hoy · S/ 4 820</b>
                        <span>Tu operación visible en tiempo real, desde el celular o la caja.</span>
                        <div class="bars" aria-hidden="true">
                            <i style="height:34%"></i><i style="height:58%"></i><i style="height:46%"></i>
                            <i style="height:82%"></i><i style="height:64%"></i><i style="height:96%"></i>
                            <i style="height:72%"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════ TESTIMONIOS ══════════ -->
    <section class="rg-sec" id="testimonios">
        <div class="rg-wrap">
            <div class="rg-head is-center">
                <div class="rg-ratings">
                    <span class="rg-tag"><i class="fa-solid fa-star" style="color:#f59e0b"></i> 4.9 en Google</span>
                    <span class="rg-tag"><i class="fa-solid fa-shield-halved"></i> +500 empresas</span>
                    <span class="rg-tag"><i class="fa-solid fa-headset"></i> Soporte 24/7</span>
                </div>
                <h2 class="rg-h2">Boticas y negocios que ya <span class="grad">operan con DSG</span></h2>
                <p class="rg-sub">Historias reales de dueños que ordenaron su negocio con nuestro software.</p>
            </div>

            <div class="rg-testi-grid">
                <article class="rg-testi-card">
                    <div class="who">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="Juan Alberto">
                        <div>
                            <h4>Juan Alberto</h4>
                            <p><b>Dueño de Vifarma</b></p>
                        </div>
                    </div>
                    <p>“Un sistema fácil e increíble. Nos da control total del negocio, simplifica todo y ahora tengo más tiempo para lo importante.”</p>
                </article>

                <article class="rg-testi-card">
                    <div class="who">
                        <img src="<?= base_url('images/farma.png') ?>" alt="Alessandro Macchi">
                        <div>
                            <h4>Alessandro Macchi</h4>
                            <p><b>Pizzería Carpaneto</b></p>
                        </div>
                    </div>
                    <p>“Controlo compras, ventas y stock desde cualquier lugar. La operación diaria es mucho más simple y no pierdo tiempo contando productos.”</p>
                </article>

                <article class="rg-testi-card">
                    <div class="who">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="Sara Marín">
                        <div>
                            <h4>Sara Marín</h4>
                            <p><b>Fundadora de Huellitas</b></p>
                        </div>
                    </div>
                    <p>“Optimizó nuestros procesos con un soporte excelente. Lo recomiendo totalmente por su innovación y cercanía.”</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ══════════ CONTROL DE CALIDAD (réplica 0.82%) ══════════ -->
    <section class="rg-sec rg-vet-sec" id="calidad">
        <div class="rg-wrap">
            <div class="rg-head is-center">
                <span class="rg-tag">DSG en cifras</span>
                <h2 class="rg-h2">Números que hablan <span class="grad">por nosotros</span></h2>
                <p class="rg-sub">El respaldo real detrás de cada sistema DSG.</p>
            </div>

            <div class="rg-vet-grid">
                <article class="rg-vet-card is-dark">
                    <div>
                        <span class="rg-vet-tag">Paso 1</span>
                        <h3>Negocios que operan con DSG</h3>
                        <div class="rg-vet-num">500+</div>
                        <p class="rg-vet-desc">Boticas, restaurantes, minimarkets y más en todo el Perú.</p>
                    </div>
                    <div class="rg-vet-faces" aria-hidden="true">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/farma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/botica.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/restaurant.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/market.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/pollos.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/minimark.png') ?>" alt="" loading="lazy">
                    </div>
                </article>

                <article class="rg-vet-card">
                    <div>
                        <span class="rg-vet-tag">Paso 2</span>
                        <h3>Tiempo en línea garantizado</h3>
                        <div class="rg-vet-num">99.9<small>%</small></div>
                        <p class="rg-vet-desc">Tu sistema disponible incluso en plena hora punta.</p>
                    </div>
                    <div class="rg-vet-faces" aria-hidden="true">
                        <img src="<?= base_url('images/farma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/restaurant.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/botica.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/pollos.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/market.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/minimark.png') ?>" alt="" loading="lazy">
                    </div>
                </article>

                <article class="rg-vet-card">
                    <div>
                        <span class="rg-vet-tag">Paso 3</span>
                        <h3>Calificación promedio en Google</h3>
                        <div class="rg-vet-num">4.9</div>
                        <p class="rg-vet-desc">La nota que nos dejan dueños que ordenaron su negocio.</p>
                    </div>
                    <div class="rg-vet-faces" aria-hidden="true">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/market.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/pollos.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/farma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/minimark.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/botica.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/restaurant.png') ?>" alt="" loading="lazy">
                    </div>
                </article>

                <article class="rg-vet-card">
                    <div>
                        <span class="rg-vet-tag">Paso 4</span>
                        <h3>Soporte que nunca duerme</h3>
                        <div class="rg-vet-num">24<small>/7</small></div>
                        <p class="rg-vet-desc">Ayuda real a toda hora, todos los días.</p>
                        <a href="<?= base_url('/#contacto') ?>" class="rg-btn rg-btn-dark rg-vet-cta">
                            Agendar demo <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="rg-vet-faces" aria-hidden="true">
                        <img src="<?= base_url('images/minimark.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/botica.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/pollos.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/market.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/restaurant.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="" loading="lazy">
                        <img src="<?= base_url('images/farma.png') ?>" alt="" loading="lazy">
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ══════════ CTA (panel con form) ══════════ -->
    <section class="rg-sec">
        <div class="rg-wrap">
            <div class="rg-cta-panel">
                <span class="rg-tag">Demo gratuita</span>
                <h2 class="rg-h2">Estás a un paso de <span class="grad">ordenar tu negocio</span></h2>
                <p class="rg-sub">
                    Déjanos tu correo y agendamos una demo de 30 minutos para mostrarte el sistema exacto para tu giro.
                </p>
                <form class="rg-lead-form">
                    <input type="email" name="correo" placeholder="Tu correo de trabajo" required>
                    <button class="rg-btn rg-btn-dark" type="submit">
                        Agendar demo <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- ══════════ CONTACTO ══════════ -->
    <section class="rg-sec" id="contacto">
        <div class="rg-wrap">
            <div class="rg-head">
                <span class="rg-tag">Contacto</span>
                <h2 class="rg-h2">Hablemos de <span class="grad">tu proyecto</span></h2>
                <p class="rg-sub">
                    Cuéntanos qué necesitas y te responderemos en menos de 24 horas con una propuesta clara, sin tecnicismos.
                </p>
            </div>

            <div class="rg-contact-grid">
                <div class="rg-info-col">
                    <div class="rg-info">
                        <div class="rg-info-item">
                            <div class="ico"><img src="<?= base_url('images/rg-info-mail.svg') ?>" alt="Escríbenos" loading="lazy"></div>
                            <div>
                                <span>Escríbenos</span>
                                <p>soporte@dsgperu.com</p>
                            </div>
                        </div>
                        <div class="rg-info-item">
                            <div class="ico"><img src="<?= base_url('images/rg-info-phone.svg') ?>" alt="Llámanos" loading="lazy"></div>
                            <div>
                                <span>Llámanos</span>
                                <p>+51 923 942 001</p>
                            </div>
                        </div>
                        <div class="rg-info-item">
                            <div class="ico"><img src="<?= base_url('images/rg-info-pin.svg') ?>" alt="Visítanos" loading="lazy"></div>
                            <div>
                                <span>Visítanos</span>
                                <p>Pucallpa, Perú</p>
                            </div>
                        </div>
                    </div>

                    <div class="rg-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3947.2940800138344!2d-74.55927542543665!3d-8.372705984411823!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91a3bd22a238dd3f%3A0xdb55cc6384f0f0e9!2sDSG%20Peru%20Technology!5e0!3m2!1ses-419!2spe!4v1773334830351!5m2!1ses-419!2spe"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="DSG Peru Technology en Google Maps"></iframe>
                    </div>
                </div>

                <div class="rg-form">
                    <h3>Solicita tu demo gratuita</h3>
                    <p class="f-sub">Completa el formulario y te contactaremos hoy mismo.</p>

                    <form action="<?= base_url('enviar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="rg-field">
                            <input type="text" name="nombre" placeholder="Nombre completo" required>
                        </div>
                        <div class="f-row">
                            <div class="rg-field">
                                <input type="email" name="correo" placeholder="Correo electrónico" required>
                            </div>
                            <div class="rg-field">
                                <input type="text" name="empresa" placeholder="Empresa">
                            </div>
                        </div>
                        <div class="rg-field">
                            <input type="tel" name="telefono" placeholder="Teléfono / WhatsApp">
                        </div>
                        <div class="rg-field">
                            <textarea name="mensaje" rows="4" placeholder="¿Qué necesitas? (botica, farmacia, restaurante, página web...)"></textarea>
                        </div>
                        <button type="submit" class="rg-btn rg-btn-dark">Enviar mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>
