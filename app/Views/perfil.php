<link rel="stylesheet" href="<?= base_url('css/dashboard/perfil.css?v=' . filemtime(FCPATH . 'css/dashboard/perfil.css')) ?>">

<?php
$p = is_array($perfil) ? (object) $perfil : $perfil;
$inis = strtoupper(substr(trim((string) ($p->nombre ?? 'U')), 0, 1) . substr(trim((string) ($p->apellido_paterno ?? '')), 0, 1));
if ($inis === '' || $inis === 'U') { $inis = strtoupper(substr(trim((string) (session()->get('username') ?? 'US')), 0, 2)); }
$rol  = $p->rol_nombre ?? session()->get('rol_nombre') ?? '—';
$tel  = trim((string) ($p->telefono ?? '')) !== '' ? $p->telefono : 'No registrado';
$mail = trim((string) ($p->correo ?? '')) !== '' ? $p->correo : 'No registrado';
$miembro = !empty($p->created_at) ? date('d/m/Y', strtotime($p->created_at)) : '—';
$nombre = trim(($p->nombre ?? '') . ' ' . ($p->apellido_paterno ?? '')) ?: (session()->get('username') ?? 'Usuario');
$activo = ((int) ($p->estado ?? 1)) === 1;
?>

<main class="pf-page">
    <div class="pdsg-page-head">
        <div>
            <h1 class="pdsg-page-title">Mi Perfil</h1>
            <p class="pdsg-page-sub">Revisa tus datos personales y actualiza tu nombre o contraseña.</p>
        </div>
        <button type="button" class="btn pdsg-add-btn" onclick="document.getElementById('Byimagen').click()">
            <i class="fa-solid fa-camera" aria-hidden="true"></i> Cambiar foto
        </button>
        <input type="file" id="Byimagen" accept="image/*" hidden onchange="Most(event);mostrarFotoPerfil()">
    </div>

    <?php $flashMsg = session()->getFlashdata('msg'); ?>
    <?php if (! empty($flashMsg)): ?>
        <?php
        $tipoFlash = session()->getFlashdata('tipo') ?? 'success';
        $flashIcon = $tipoFlash === 'success' ? 'check_circle' : ($tipoFlash === 'danger' ? 'error' : 'warning');
        ?>
        <div class="dev-alert dev-alert-<?= esc($tipoFlash) ?>" role="status">
            <span class="material-symbols-outlined filled"><?= $flashIcon ?></span>
            <span><?= esc($flashMsg) ?></span>
        </div>
    <?php endif; ?>

    <div class="pf-grid">
        <!-- ══ RESUMEN ══ -->
        <section class="dev-card" aria-label="Información personal">
            <div class="dev-card-head">
                <h2><span class="material-symbols-outlined">badge</span> Información personal</h2>
                <span class="pf-status<?= $activo ? '' : ' is-off' ?>">
                    <span class="material-symbols-outlined filled">circle</span>
                    <?= $activo ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>

            <div class="pf-id">
                <div class="pf-avatar-wrap">
                    <img id="fotoPerfil" class="pf-avatar" src="<?= base_url('images/avatar-placeholder.svg') ?>" alt="Foto de perfil"
                         onerror="this.style.display='none';document.getElementById('pfInitials').style.display='flex';">
                    <div class="pf-avatar-initials" id="pfInitials" style="display:none;"><?= esc($inis) ?></div>
                    <button type="button" class="pf-avatar-btn" title="Cambiar foto" onclick="document.getElementById('Byimagen').click()">
                        <i class="fa-solid fa-camera" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="pf-id-text">
                    <p class="pf-name"><?= esc($nombre) ?></p>
                    <div class="pf-badges">
                        <span class="pf-badge"><span class="material-symbols-outlined">shield_person</span> <?= esc(ucfirst($rol)) ?></span>
                        <span class="pf-badge"><span class="material-symbols-outlined">alternate_email</span> <?= esc($p->username ?? '—') ?></span>
                    </div>
                </div>
            </div>

            <div class="pdsg-mcard-details pf-details">
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-user"></i> Usuario</span>
                    <span class="pdsg-mcard-val pdsg-mono"><?= esc($p->username ?? '—') ?></span>
                </div>
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-id-card"></i> DNI</span>
                    <span class="pdsg-mcard-val pdsg-mono"><?= esc($p->dni ?: '—') ?></span>
                </div>
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-envelope"></i> Correo</span>
                    <span class="pdsg-mcard-val<?= $mail === 'No registrado' ? ' is-muted' : '' ?>"><?= esc($mail) ?></span>
                </div>
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                    <span class="pdsg-mcard-val<?= $tel === 'No registrado' ? ' is-muted' : '' ?>"><?= esc($tel) ?></span>
                </div>
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-shield-halved"></i> Rol</span>
                    <span class="pdsg-mcard-val"><?= esc(ucfirst($rol)) ?></span>
                </div>
                <div class="pdsg-mcard-detail">
                    <span class="pdsg-mcard-label"><i class="fa-solid fa-calendar-days"></i> Miembro desde</span>
                    <span class="pdsg-mcard-val"><?= esc($miembro) ?></span>
                </div>
            </div>
        </section>

        <!-- ══ EDITAR ══ -->
        <section class="dev-card" aria-label="Editar perfil">
            <div class="dev-card-head">
                <h2><span class="material-symbols-outlined">edit_square</span> Editar perfil</h2>
            </div>

            <form class="pf-form" action="<?= base_url('perfil/actualizar') ?>" method="post" autocomplete="off">
                <?= csrf_field() ?>

                <div class="dev-field">
                    <label for="pfNombre">Nombre <span class="pf-req">*</span></label>
                    <input type="text" id="pfNombre" name="nombre"
                           value="<?= esc((string) old('nombre', (string) ($p->nombre ?? ''))) ?>" required minlength="2" maxlength="100"
                           placeholder="Tu nombre">
                </div>

                <div class="dev-field">
                    <label for="password">Contraseña</label>
                    <div class="pf-input-wrap">
                        <input type="password" id="password" name="password"
                               placeholder="••••••••••" minlength="6" autocomplete="new-password">
                        <button type="button" class="pf-toggle-pw" onclick="togglePasswordVisibility()"
                                aria-label="Mostrar u ocultar contraseña">
                            <span class="material-symbols-outlined" id="togglePasswordIcon">visibility_off</span>
                        </button>
                    </div>
                    <p class="pf-hint">
                        <span class="material-symbols-outlined">info</span>
                        Déjalo vacío si no deseas cambiarla (mínimo 6 caracteres).
                    </p>
                </div>

                <div class="dev-form-actions pf-actions">
                    <button type="reset" class="dev-btn dev-btn-outline">
                        <span class="material-symbols-outlined">restart_alt</span> Restablecer
                    </button>
                    <button type="submit" class="dev-btn dev-btn-primary" id="pfSubmit">
                        <span class="material-symbols-outlined">save</span> Guardar cambios
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<script src="<?= base_url('js/subirimage.js') ?>"></script>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.textContent = 'visibility';
        } else {
            passwordInput.type = 'password';
            icon.textContent = 'visibility_off';
        }
    }

    document.querySelector('.pf-form')?.addEventListener('submit', function (e) {
        const btn = document.getElementById('pfSubmit');
        if (btn.disabled) { e.preventDefault(); return; }
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined">progress_activity</span> Guardando…';
    });

    function mostrarFotoPerfil() {
        const img = document.getElementById('fotoPerfil');
        const ini = document.getElementById('pfInitials');
        if (!img || !ini) return;
        if (localStorage.getItem('fotoUsuario')) {
            img.style.display = 'block';
            ini.style.display = 'none';
        } else {
            img.style.display = 'none';
            ini.style.display = 'flex';
        }
    }
    window.addEventListener('DOMContentLoaded', mostrarFotoPerfil);
</script>
