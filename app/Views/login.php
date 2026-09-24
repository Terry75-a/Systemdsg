<!-- ═══════════ LOGIN: formulario limpio ═══════════ -->
<div class="main-wrapper">
    <div class="left-side">
        <div class="login-box">

            <a href="<?= base_url('/') ?>" class="back-link" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Volver al sitio principal
            </a>

            <p class="login-eyebrow">Bienvenido de nuevo</p>
            <h2 class="title-modern">Inicia sesión</h2>
            <p class="login-sub">Accede al panel de control de tu negocio.</p>

            <?php
            $flashMsg = session()->getFlashdata('msg') ?? session()->getFlashdata('error');
            $flashTipo = session()->getFlashdata('tipo') ?? 'danger';
            $flashClass = ($flashTipo === 'warning') ? 'alerta-warning' : 'alerta-error';
            $flashIcon = ($flashTipo === 'warning') ? 'fa-triangle-exclamation' : 'fa-circle-exclamation';
            ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="<?= esc($flashClass) ?>" role="alert">
                    <i class="fa-solid <?= esc($flashIcon) ?>" aria-hidden="true"></i>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <form class="formularioxd" method="post" action="<?= site_url('login/auth') ?>" autocomplete="on">
                <?= csrf_field() ?>

                <div class="form-group-modern">
                    <label for="username">Usuario</label>
                    <div class="input-with-icon">
                        <input type="text" id="username" name="username" placeholder="example@email.com"
                               autocomplete="username" required>
                        <span class="input-icon"><i class="fa-regular fa-user" aria-hidden="true"></i></span>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label for="password">Contraseña</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="••••••••"
                               autocomplete="current-password" required>
                        <button type="button" id="togglePassword" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary-modern">
                    Entrar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>

                <div class="footer-links">
                    <span class="login-help">¿Problemas para entrar? Contacta a tu administrador.</span>
                </div>
            </form>

        </div>
    </div>

    <!-- ═══════════ LOGIN: panel de marca ═══════════ -->
    <div class="right-side">
        <div class="right-inner">
            <span class="right-eyebrow">DSG Perú Technology</span>
            <h3 class="right-title">Todo tu negocio, bajo control.</h3>
            <p class="right-desc">Ventas, inventario y reportes en un solo panel. Diseñado para restaurantes, boticas y minimarkets.</p>
            <ul class="right-points">
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Punto de venta rápido y facturación electrónica</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Inventario en tiempo real con alertas de stock</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Reportes claros para decidir mejor</li>
            </ul>
            <p class="right-quote">"Nos da control total del negocio y ahora tengo más tiempo para lo importante."</p>
            <p class="right-author">Juan Alberto — Vifarma, Perú</p>
        </div>
    </div>
</div>

<script src="<?= base_url('js/eyepassword.js?v=' . filemtime(FCPATH . 'js/eyepassword.js')) ?>"></script>

</div><!-- /.page-wrapper -->
</body>
</html>
