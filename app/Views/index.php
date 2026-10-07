<main class="db-main">

    <!-- ══════════ HERO ══════════ -->
    <section class="db-hero db-wrap">
        <span class="db-tag">Software a medida · Perú</span>

        <h1 class="db-h1" style="margin-top:26px;">
            Software que hace <span class="grad">crecer tu negocio</span>
        </h1>

        <p class="db-sub">
            En DSG Perú desarrollamos sistemas a medida para restaurantes, boticas y minimarkets.
            Automatiza tus ventas, controla tu inventario y toma decisiones con datos reales.
        </p>

        <div class="db-hero-actions">
            <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-lilac">
                Solicitar demo <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="<?= base_url('servicios') ?>" class="db-btn db-btn-ghost">
                Ver servicios <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <p class="db-micro">Sin tarjeta · Respuesta en menos de 24 horas</p>

        <div class="db-tiles">
            <div class="db-tile">
                <span class="t-label">Empresas atendidas</span>
                <div>
                    <div class="t-num"><span id="stas-number">0</span>+</div>
                    <div class="t-desc">Negocios que ya operan con DSG</div>
                </div>
            </div>
            <div class="db-tile">
                <span class="t-label">Años de experiencia</span>
                <div>
                    <div class="t-num" id="experience-years">0</div>
                    <div class="t-desc">Implementando software en el Perú</div>
                </div>
            </div>
            <div class="db-tile">
                <span class="t-label">Uptime garantizado</span>
                <div>
                    <div class="t-num"><span id="uptime-guaranteed">0</span>%</div>
                    <div class="t-desc">Tu operación nunca se detiene</div>
                </div>
            </div>
        </div>

        <div class="db-sectors">
            <span class="db-chip"><i class="fa-solid fa-utensils"></i> Restaurantes</span>
            <span class="db-chip"><i class="fa-solid fa-capsules"></i> Boticas y farmacias</span>
            <span class="db-chip"><i class="fa-solid fa-shop"></i> Minimarkets</span>
            <span class="db-chip"><i class="fa-solid fa-ellipsis"></i> Y más</span>
        </div>
    </section>

    <!-- ══════════ SERVICIOS ══════════ -->
    <section class="db-sec" id="servicios">
        <div class="db-wrap">
            <div class="db-head">
                <span class="db-tag">Servicios</span>
                <h2 class="db-h2">Un sistema para cada <span class="grad">tipo de negocio</span></h2>
                <p class="db-sub">
                    No vendemos software genérico: cada solución está diseñada para los procesos críticos de tu sector.
                </p>
            </div>

            <div class="db-grid-3">
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-utensils"></i></div>
                    <h3>Para restaurantes</h3>
                    <p class="db-card-desc">
                        Atiende más mesas con menos errores: pedidos directos a cocina, cuentas claras y facturación en segundos.
                    </p>
                    <ul class="db-feat">
                        <li><i class="fa-solid fa-check"></i> Gestión de mesas y pedidos</li>
                        <li><i class="fa-solid fa-check"></i> Control de cocina (KDS)</li>
                        <li><i class="fa-solid fa-check"></i> Facturación electrónica</li>
                        <li><i class="fa-solid fa-check"></i> Integración con delivery</li>
                    </ul>
                </article>

                <article class="db-card db-card-lilac">
                    <div class="db-tile-ico"><i class="fa-solid fa-capsules"></i></div>
                    <h3>Para boticas y farmacias</h3>
                    <p class="db-card-desc">
                        Cero vencidos y cero quiebres de stock: controla lotes, recetas y alertas automáticas todos los días.
                    </p>
                    <ul class="db-feat">
                        <li><i class="fa-solid fa-check"></i> Control de lotes y vencimientos</li>
                        <li><i class="fa-solid fa-check"></i> Gestión de recetas médicas</li>
                        <li><i class="fa-solid fa-check"></i> Alertas de stock mínimo</li>
                        <li><i class="fa-solid fa-check"></i> Integración con DIGEMID</li>
                    </ul>
                </article>

                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-shop"></i></div>
                    <h3>Para minimarkets</h3>
                    <p class="db-card-desc">
                        Colas más cortas e inventario exacto: punto de venta táctil y control de proveedores en tiempo real.
                    </p>
                    <ul class="db-feat">
                        <li><i class="fa-solid fa-check"></i> Punto de venta táctil</li>
                        <li><i class="fa-solid fa-check"></i> Lector de código de barras</li>
                        <li><i class="fa-solid fa-check"></i> Control de inventario</li>
                        <li><i class="fa-solid fa-check"></i> Gestión de proveedores</li>
                    </ul>
                </article>
            </div>

            <div class="db-hero-actions" style="margin-top:36px;">
                <a href="<?= base_url('servicios') ?>" class="db-btn db-btn-ghost">
                    Explorar todos los servicios <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════ PROCESO ══════════ -->
    <section class="db-sec" id="proceso">
        <div class="db-wrap">
            <div class="db-head">
                <span class="db-tag">Cómo trabajamos</span>
                <h2 class="db-h2">De la llamada a tu sistema <span class="grad">en 3 pasos</span></h2>
            </div>

            <div class="db-grid-3">
                <article class="db-card db-step">
                    <span class="num">01</span>
                    <h3>Conversamos</h3>
                    <p class="db-card-desc">Escuchamos cómo opera tu negocio y detectamos qué procesos te hacen perder tiempo y dinero.</p>
                </article>
                <article class="db-card db-card-lilac db-step">
                    <span class="num">02</span>
                    <h3>Implementamos</h3>
                    <p class="db-card-desc">Instalamos y configuramos tu sistema en menos de 24 horas, con capacitación incluida para tu equipo.</p>
                </article>
                <article class="db-card db-step">
                    <span class="num">03</span>
                    <h3>Acompañamos</h3>
                    <p class="db-card-desc">Soporte 24/7 y actualizaciones constantes para que tu operación nunca se detenga.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ══════════ POR QUÉ ELEGIRNOS ══════════ -->
    <section class="db-sec" id="por-que">
        <div class="db-wrap">
            <div class="db-head">
                <span class="db-tag">Por qué elegirnos</span>
                <h2 class="db-h2">Hecho para operar <span class="grad">sin fricción</span></h2>
            </div>

            <div class="db-grid-bento">
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Seguridad garantizada</h3>
                    <p class="db-card-desc">Tus datos protegidos con encriptación de nivel bancario y copias de seguridad automáticas.</p>
                </article>
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Implementación rápida</h3>
                    <p class="db-card-desc">Tu sistema funcionando en menos de 24 horas, con capacitación completa incluida.</p>
                </article>
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-headset"></i></div>
                    <h3>Soporte 24/7</h3>
                    <p class="db-card-desc">Un equipo técnico real, disponible a toda hora para resolver cualquier inconveniente.</p>
                </article>
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-arrows-rotate"></i></div>
                    <h3>Actualizaciones constantes</h3>
                    <p class="db-card-desc">Mejoras continuas de rendimiento y compatibilidad, sin costo adicional.</p>
                </article>
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    <h3>Acceso móvil</h3>
                    <p class="db-card-desc">Supervisa ventas, stock y reportes desde tu celular, estés donde estés.</p>
                </article>
                <article class="db-card">
                    <div class="db-tile-ico"><i class="fa-solid fa-handshake"></i></div>
                    <h3>Atención personalizada</h3>
                    <p class="db-card-desc">Un asesor asignado a tu cuenta, con seguimiento continuo de tus necesidades.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ══════════ TESTIMONIOS ══════════ -->
    <section class="db-sec" id="testimonios">
        <div class="db-wrap">
            <div class="db-head">
                <span class="db-tag">Testimonios</span>
                <h2 class="db-h2">Negocios que ya <span class="grad">operan con DSG</span></h2>
            </div>

            <div class="db-grid-3">
                <article class="db-card db-card-lilac">
                    <div class="db-card-top">
                        <div class="db-quote-ico"><i class="fa-solid fa-quote-left"></i></div>
                        <span class="db-flag"><img src="https://flagcdn.com/w40/pe.png" alt="Perú"> Perú</span>
                    </div>
                    <p class="db-quote">
                        “Un sistema fácil e increíble. Nos da control total del negocio, simplifica todo y ahora tengo más tiempo para lo importante.”
                    </p>
                    <div class="db-person">
                        <img src="<?= base_url('images/vifarma.png') ?>" alt="Juan Alberto">
                        <div>
                            <h4>Juan Alberto</h4>
                            <p>Dueño de Vifarma</p>
                        </div>
                    </div>
                </article>

                <article class="db-card">
                    <div class="db-card-top">
                        <div class="db-quote-ico"><i class="fa-solid fa-quote-left"></i></div>
                        <span class="db-flag"><img src="https://flagcdn.com/w40/co.png" alt="Colombia"> Colombia</span>
                    </div>
                    <p class="db-quote">
                        “Ha sido clave para el crecimiento de mi restaurante. Controlo todo desde cualquier lugar y la operación diaria es mucho más simple.”
                    </p>
                    <div class="db-person">
                        <img src="<?= base_url('images/farma.png') ?>" alt="Alessandro Macchi">
                        <div>
                            <h4>Alessandro Macchi</h4>
                            <p>Pizzería Carpaneto</p>
                        </div>
                    </div>
                </article>

                <article class="db-card">
                    <div class="db-card-top">
                        <div class="db-quote-ico"><i class="fa-solid fa-quote-left"></i></div>
                        <span class="db-flag"><img src="https://flagcdn.com/w40/gt.png" alt="Guatemala"> Guatemala</span>
                    </div>
                    <p class="db-quote">
                        “Optimizó nuestros procesos con un soporte excelente. Lo recomiendo totalmente por su innovación y cercanía.”
                    </p>
                    <div class="db-person">
                        <img src="<?= base_url('images/huellitas.png') ?>" alt="Sara Marín">
                        <div>
                            <h4>Sara Marín</h4>
                            <p>Fundadora de Huellitas</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ══════════ CTA FINAL ══════════ -->
    <section class="db-sec db-cta">
        <div class="db-wrap">
            <div class="db-cta-box">
                <span class="db-tag">Demo gratuita</span>
                <h2 class="db-h2" style="margin-top:22px;">
                    ¿Listo para ordenar y <span class="grad">hacer crecer tu negocio?</span>
                </h2>
                <p class="db-sub">
                    Agenda una demostración sin compromiso y descubre en 30 minutos cuánto tiempo y dinero puedes ahorrar.
                </p>
                <div class="db-hero-actions">
                    <a href="<?= base_url('/#contacto') ?>" class="db-btn db-btn-lilac">
                        Solicitar demo <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="<?= base_url('precio') ?>" class="db-btn db-btn-ghost">Ver precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════ CONTACTO ══════════ -->
    <section class="db-sec" id="contacto">
        <div class="db-wrap">
            <div class="db-head">
                <span class="db-tag">Contacto</span>
                <h2 class="db-h2">Hablemos de <span class="grad">tu proyecto</span></h2>
                <p class="db-sub">
                    Cuéntanos qué necesitas y te responderemos en menos de 24 horas con una propuesta clara, sin tecnicismos.
                </p>
            </div>

            <div class="db-contact-grid">
                <div class="db-info-col">
                    <div class="db-info">
                        <div class="db-info-item">
                            <div class="ico"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <span>Escríbenos</span>
                                <p>soporte@dsgperu.com</p>
                            </div>
                        </div>
                        <div class="db-info-item">
                            <div class="ico"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <span>Llámanos</span>
                                <p>+51 923 942 001</p>
                            </div>
                        </div>
                        <div class="db-info-item">
                            <div class="ico"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <span>Visítanos</span>
                                <p>Pucallpa, Perú</p>
                            </div>
                        </div>
                    </div>

                    <div class="db-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3947.2940800138344!2d-74.55927542543665!3d-8.372705984411823!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91a3bd22a238dd3f%3A0xdb55cc6384f0f0e9!2sDSG%20Peru%20Technology!5e0!3m2!1ses-419!2spe!4v1773334830351!5m2!1ses-419!2spe"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="DSG Peru Technology en Google Maps"></iframe>
                    </div>
                </div>

                <div class="db-form">
                    <h3>Solicita tu demo gratuita</h3>
                    <p class="f-sub">Completa el formulario y te contactaremos hoy mismo.</p>

                    <form action="<?= base_url('enviar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="db-field">
                            <input type="text" name="nombre" placeholder="Nombre completo" required>
                        </div>
                        <div class="f-row">
                            <div class="db-field">
                                <input type="email" name="correo" placeholder="Correo electrónico" required>
                            </div>
                            <div class="db-field">
                                <input type="text" name="empresa" placeholder="Empresa">
                            </div>
                        </div>
                        <div class="db-field">
                            <input type="tel" name="telefono" placeholder="Teléfono / WhatsApp">
                        </div>
                        <div class="db-field">
                            <textarea name="mensaje" rows="4" placeholder="¿Qué sistema necesitas? (restaurante, botica, minimarket...)"></textarea>
                        </div>
                        <button type="submit" class="db-btn db-btn-lilac">Enviar mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>
