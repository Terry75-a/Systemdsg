
<main class="demo-page">
    <div class="demo-container">
        <div class="demo-info">
            <h1>Contacta a nuestro equipo de ventas</h1>
            <p>Estamos felices de responder tus preguntas y ayudarte a conocer DSG PERÚ.</p>
            
            <ul class="demo-benefits">
                <li><i class="fa-solid fa-check"></i> Agenda una demostración en vivo</li>
                <li><i class="fa-solid fa-check"></i> Obtén información detallada de precios</li>
                <li><i class="fa-solid fa-check"></i> Explora casos de uso para tu equipo</li>
            </ul>

            <div class="help-box">
                <i class="fa-regular fa-circle-question"></i>
                <p>Para problemas técnicos o preguntas de productos, por favor visita nuestro <a href="#">Centro de Ayuda</a>.</p>
            </div>
        </div>

        <div class="demo-form-wrapper">
            <form action="<?= base_url('enviar-demo') ?>" method="POST" class="slack-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" name="first_name" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Apellido *</label>
                        <input type="text" name="last_name" placeholder="Tu apellido" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Correo de trabajo *</label>
                        <input type="email" name="email" placeholder="nombre@empresa.com" required>
                    </div>
                    <div class="form-group">
                        <label>Cargo *</label>
                        <select name="role" required>
                            <option value="">Por favor selecciona uno</option>
                            <option value="owner">Dueño / Gerente</option>
                            <option value="admin">Administrador</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>País / Región *</label>
                    <select name="country" required>
                        <option value="Peru">Perú</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Mexico">México</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Empresa *</label>
                        <input type="text" name="company" placeholder="Nombre de tu negocio" required>
                    </div>
                    <div class="form-group">
                        <label>Tamaño de la empresa *</label>
                        <select name="size" required>
                            <option value="">Selecciona una opción</option>
                            <option value="1-10">1-10 empleados</option>
                            <option value="11-50">11-50 empleados</option>
                            <option value="51+">Más de 50 empleados</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">

                

                <div class="form-group">
                    <label>Número de teléfono *</label>
                    <input type="tel" name="phone" placeholder="+51 000 000 000" required>
                </div>
                <div class="form-group">
                    <label>¿Cómo puedo ayudarle*</label>
                    <select name="size" required>
                        <option value="">Por favor, seleccione una opción.</option>
                        <option value="">Quiero evaluar Slack para mi organización</option>
                        <option value="">Quiero entender qué plan  es adecuado para mí</option>
                        <option value="">Tengo una pregunta sobre el servicio</option>
                    </select>

                </div>

                </div>
                  <div class="form-group">
                     <label>¿Algo más?</label>
                     <input type="text" name="message" placeholder="Cuéntanos un poco más sobre tu negocio o tus necesidades específicas."></i>

                  </div>

                <button type="submit" class="btn-submit-demo">Enviar solicitud</button>
            </form>
        </div>
    </div>
</main>
