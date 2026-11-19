<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil · Dev Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/logo_circular.png') ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-panel.css?v=' . filemtime(FCPATH . 'css/index/components/dev-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/dev-dark.css?v=' . filemtime(FCPATH . 'css/index/components/dev-dark.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/educonecta.css?v=' . filemtime(FCPATH . 'css/index/components/educonecta.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/next-panel.css?v=' . filemtime(FCPATH . 'css/index/components/next-panel.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/trackin.css?v=' . filemtime(FCPATH . 'css/index/components/trackin.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/lumen.css?v=' . filemtime(FCPATH . 'css/index/components/lumen.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('css/index/components/aibl.css?v=' . filemtime(FCPATH . 'css/index/components/aibl.css')) ?>">
    <script src="<?= base_url('js/lumen-mobile.js?v=' . filemtime(FCPATH . 'js/lumen-mobile.js')) ?>" defer></script>
    <style>
        /* ── Perfil del desarrollador (tema AIBL: verde DSG + negro) ── */
        .pf { display: grid; gap: 16px; width: 100%; }
        .pf-card { background: var(--lm-card, #fff); border-radius: 24px; box-shadow: var(--lm-shadow); padding: 22px 24px; }
        .pf-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .pf-head h3 { margin: 0; display: inline-flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 700; letter-spacing: -.015em; color: var(--lm-ink); }
        .pf-head h3::before { content: ""; width: 12px; height: 12px; border-radius: 4px; background: var(--ab-lime, #1b7a42); }
        .pf-head p { margin: 4px 0 0 22px; font-size: 12.5px; color: var(--lm-muted); }

        /* Cabecera: tarjeta negra con el avatar verde */
        .pf-hero { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 22px;
                   background: #141414; color: #fff; border-radius: 28px; padding: 26px 28px; position: relative; overflow: hidden; }
        .pf-hero::after { content: ""; position: absolute; right: -60px; top: -60px; width: 240px; height: 240px; border-radius: 50%;
                          background: radial-gradient(circle, color-mix(in srgb, var(--ab-lime, #1b7a42) 55%, transparent) 0%, transparent 70%); }
        .pf-avatar { width: 92px; height: 92px; border-radius: 50%; background: var(--ab-lime, #1b7a42); color: #fff;
                     display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; letter-spacing: -.02em;
                     box-shadow: 0 0 0 6px rgba(255,255,255,.08); }
        .pf-hello { font-size: 13px; color: rgba(255,255,255,.62); font-weight: 500; }
        .pf-name { font-size: 30px; font-weight: 800; letter-spacing: -.035em; line-height: 1.1; margin: 2px 0 10px; }
        .pf-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .pf-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;
                   background: rgba(255,255,255,.09); color: rgba(255,255,255,.88); }
        .pf-chip .material-symbols-outlined { font-size: 16px; }
        .pf-chip.is-green { background: var(--ab-lime, #1b7a42); color: #fff; }
        .pf-hero-side { position: relative; z-index: 1; text-align: right; }
        .pf-hero-side small { display: block; font-size: 11.5px; color: rgba(255,255,255,.55); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
        .pf-hero-side strong { display: block; font-size: 15px; margin-top: 4px; font-variant-numeric: tabular-nums; }
        .pf-hero > * { position: relative; z-index: 1; }

        /* Indicadores */
        .pf-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .pf-stat { background: var(--lm-card, #fff); border-radius: 22px; padding: 16px 18px; box-shadow: var(--lm-shadow); position: relative; min-height: 98px; }
        .pf-stat .material-symbols-outlined { position: absolute; top: 14px; right: 14px; width: 32px; height: 32px; border-radius: 50%;
                     display: inline-flex; align-items: center; justify-content: center; font-size: 17px; background: var(--lm-canvas); color: var(--lm-ink); }
        .pf-stat p { margin: 0; padding-right: 40px; font-size: 12px; font-weight: 600; color: var(--lm-muted); }
        .pf-stat b { display: block; margin-top: 8px; font-size: 28px; font-weight: 800; letter-spacing: -.04em; color: var(--lm-ink); }
        .pf-stat.is-green { background: var(--ab-lime, #1b7a42); }
        .pf-stat.is-green p { color: rgba(255,255,255,.8); }
        .pf-stat.is-green b { color: #fff; }
        .pf-stat.is-green .material-symbols-outlined { background: rgba(255,255,255,.18); color: #fff; }

        .pf-grid { display: grid; grid-template-columns: 1.25fr 1fr; gap: 16px; align-items: stretch; }
        .pf-grid > .pf-card { display: flex; flex-direction: column; }
        .pf-grid > .pf-card > .pf-form { flex: 1; display: flex; flex-direction: column; gap: 14px; }
        .pf-grid > .pf-card > .pf-form > .pf-actions { margin-top: auto; padding-top: 6px; }

        /* Formularios */
        .pf-form { display: grid; gap: 14px; }
        .pf-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .pf-field label { display: block; font-size: 12px; font-weight: 700; color: var(--lm-ink-2); margin-bottom: 6px; }
        .pf-input { position: relative; }
        .pf-input .material-symbols-outlined { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 19px; color: var(--lm-faint); pointer-events: none; }
        .pf-input input { width: 100%; height: 46px; padding: 0 44px 0 44px; border-radius: 14px; border: 1px solid var(--lm-line-2);
                          background: var(--lm-canvas); color: var(--lm-ink); font: inherit; font-size: 14px; outline: none; transition: border-color .15s, box-shadow .15s, background .15s; }
        .pf-input input:focus { background: var(--lm-card); border-color: var(--ab-lime, #1b7a42); box-shadow: 0 0 0 4px color-mix(in srgb, var(--ab-lime, #1b7a42) 18%, transparent); }
        .pf-input input[readonly] { background: transparent; border-style: dashed; color: var(--lm-muted); }
        .pf-input .pf-eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; border: 0; border-radius: 50%;
                            background: transparent; color: var(--lm-faint); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
        .pf-input .pf-eye:hover { background: var(--lm-line); color: var(--lm-ink); }
        .pf-input .pf-eye .material-symbols-outlined { position: static; transform: none; font-size: 18px; }
        .pf-hint { font-size: 11.5px; color: var(--lm-faint); margin-top: 5px; }
        .pf-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; flex-wrap: wrap; }
        .pf-btn { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 18px; border-radius: 999px; border: 1px solid var(--lm-line-2);
                  background: var(--lm-card); color: var(--lm-ink); font: inherit; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: background .15s, opacity .15s; }
        .pf-btn .material-symbols-outlined { font-size: 18px; }
        a.pf-btn, a.pf-btn:hover { text-decoration: none; }
        .pf-btn:hover { background: var(--lm-canvas); }
        .pf-btn-primary { background: #141414; color: #fff; border-color: #141414; }
        .pf-btn-primary .material-symbols-outlined { color: #7fd3a1; }
        .pf-btn-primary:hover { background: #2e2e2e; }
        .pf-btn-primary:disabled { opacity: .45; cursor: not-allowed; }
        html.dark .pf-btn-primary { background: var(--ab-lime, #1b7a42); border-color: var(--ab-lime, #1b7a42); }
        html.dark .pf-btn-primary .material-symbols-outlined { color: #fff; }
        .pf-dirty { display: none; align-items: center; gap: 6px; margin-right: auto; font-size: 12px; font-weight: 600; color: #a16207; }
        .pf-dirty.on { display: inline-flex; }
        .pf-dirty .material-symbols-outlined { font-size: 16px; }

        /* Fuerza de la contraseña */
        .pf-meter { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-top: 8px; }
        .pf-meter i { height: 6px; border-radius: 999px; background: var(--lm-line); transition: background .2s; }
        .pf-meter[data-l="1"] i:nth-child(-n+1) { background: #d64545; }
        .pf-meter[data-l="2"] i:nth-child(-n+2) { background: #e2a72e; }
        .pf-meter[data-l="3"] i:nth-child(-n+3) { background: #3a9d5f; }
        .pf-meter[data-l="4"] i:nth-child(-n+4) { background: var(--ab-lime, #1b7a42); }
        .pf-meter-txt { font-size: 11.5px; color: var(--lm-muted); margin-top: 5px; font-weight: 600; }
        .pf-match { font-size: 11.5px; margin-top: 5px; font-weight: 600; }
        .pf-match.ok { color: #1b7a42; } .pf-match.bad { color: #d64545; }

        /* Actividad */
        .pf-act { display: grid; }
        .pf-act-row { display: grid; grid-template-columns: 36px 1fr auto; align-items: center; gap: 12px; padding: 11px 0; border-top: 1px solid var(--lm-line); }
        .pf-act-row:first-child { border-top: 0; }
        .pf-act-ico { width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: var(--lm-canvas); }
        .pf-act-ico .material-symbols-outlined { font-size: 18px; }
        .pf-act-ico.in { background: color-mix(in srgb, var(--ab-lime, #1b7a42) 14%, transparent); color: var(--ab-lime, #1b7a42); }
        .pf-act-row b { font-size: 13.5px; font-weight: 700; color: var(--lm-ink); display: block; }
        .pf-act-row small { font-size: 12px; color: var(--lm-muted); }
        .pf-act-row time { font-size: 12px; color: var(--lm-muted); font-variant-numeric: tabular-nums; text-align: right; }
        .pf-empty { font-size: 13px; color: var(--lm-muted); padding: 10px 0; }

        /* Aviso (flash) */
        .pf-alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 16px; font-size: 13.5px; font-weight: 600; }
        .pf-alert.success { background: #e6f4ea; color: #15633a; }
        .pf-alert.warning { background: #fef7e0; color: #8a5a00; }
        .pf-alert.danger  { background: #fce8e6; color: #b3261e; }
        html.dark .pf-alert.success { background: #0d2818; color: #7fd3a1; }
        html.dark .pf-alert.warning { background: #3a2c10; color: #f3c26b; }
        html.dark .pf-alert.danger  { background: #3a1a1a; color: #ff8f8f; }

        @media (max-width: 1100px) { .pf-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .pf-hero { grid-template-columns: 1fr; text-align: center; justify-items: center; }
            .pf-chips { justify-content: center; } .pf-hero-side { text-align: center; }
            .pf-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .pf-row2 { grid-template-columns: 1fr; }
            .pf-name { font-size: 24px; }
        }
    </style>
</head>
<body>
<?php
    $old      = fn ($k, $def) => old($k) !== null ? old($k) : $def;
    $devName  = (string) ($user['name'] ?? 'Superadmin');
    $parts    = preg_split('/\s+/u', trim($devName));
    $devIni   = mb_strtoupper(mb_substr($parts[0] ?? 'D', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : mb_substr($parts[0] ?? 'V', 1, 1)));
    $fmt      = static fn ($d) => $d ? date('d/m/Y H:i', strtotime((string) $d)) : '—';
    $sesiones = $sesiones ?? [];
    $stats    = $stats ?? ['admins' => 0, 'personal' => 0, 'logins30' => 0];
?>
<div class="dev-layout">
    <?php $activePage = 'perfil'; ?>
    <?= view('partials/dev-sidebar', ['activePage' => $activePage]) ?>
    <div class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-row">
                <div>
                    <h1>Mi perfil</h1>
                    <p>Edita los datos de tu cuenta de desarrollador y tu contraseña de acceso.</p>
                </div>
            </div>
        </header>
        <div class="dev-content">
          <div class="pf">
            <?php $flashMsg = session()->getFlashdata('msg'); $flashTipo = session()->getFlashdata('tipo') ?? 'success'; ?>
            <?php if (!empty($flashMsg)): ?>
                <div class="pf-alert <?= esc($flashTipo) ?>" role="status">
                    <span class="material-symbols-outlined"><?= $flashTipo === 'success' ? 'check_circle' : 'error' ?></span>
                    <span><?= esc($flashMsg) ?></span>
                </div>
            <?php endif; ?>

            <!-- Cabecera -->
            <section class="pf-hero">
                <div class="pf-avatar" id="pfAvatar"><?= esc($devIni) ?></div>
                <div>
                    <div class="pf-hello">Cuenta de desarrollador</div>
                    <div class="pf-name" id="pfName"><?= esc($devName) ?></div>
                    <div class="pf-chips">
                        <span class="pf-chip is-green"><span class="material-symbols-outlined">shield_person</span> Superadmin · Dev</span>
                        <span class="pf-chip"><span class="material-symbols-outlined">tag</span> <?= esc($user['admin_code'] ?? 'DEV-001') ?></span>
                        <span class="pf-chip"><span class="material-symbols-outlined">mail</span> <?= esc($user['email'] ?: 'Sin correo') ?></span>
                        <span class="pf-chip"><span class="material-symbols-outlined">verified_user</span> Activo</span>
                    </div>
                </div>
                <div class="pf-hero-side">
                    <small>Último acceso</small>
                    <strong><?= esc($fmt($user['last_login'] ?? null)) ?></strong>
                    <small style="margin-top:12px;">Miembro desde</small>
                    <strong><?= esc($fmt($user['created'] ?? null)) ?></strong>
                </div>
            </section>

            <!-- Indicadores -->
            <section class="pf-stats">
                <div class="pf-stat is-green"><span class="material-symbols-outlined">admin_panel_settings</span><p>Acceso</p><b>Total</b></div>
                <div class="pf-stat"><span class="material-symbols-outlined">apartment</span><p>Administradores</p><b><?= (int) $stats['admins'] ?></b></div>
                <div class="pf-stat"><span class="material-symbols-outlined">groups</span><p>Personal registrado</p><b><?= (int) $stats['personal'] ?></b></div>
                <div class="pf-stat"><span class="material-symbols-outlined">login</span><p>Inicios de sesión (30 días)</p><b><?= (int) $stats['logins30'] ?></b></div>
            </section>

            <div class="pf-grid">
                <!-- Datos de la cuenta (editables) -->
                <section class="pf-card">
                    <div class="pf-head">
                        <div>
                            <h3>Datos de la cuenta</h3>
                            <p>Cambia lo que necesites y pulsa «Guardar cambios».</p>
                        </div>
                    </div>
                    <form method="post" action="<?= site_url('dios/update-profile') ?>" class="pf-form" id="pfForm" autocomplete="off">
                        <?= csrf_field() ?>
                        <div class="pf-field">
                            <label for="pf_name">Nombre visible</label>
                            <div class="pf-input"><span class="material-symbols-outlined">badge</span>
                                <input type="text" id="pf_name" name="name" value="<?= esc($old('name', $devName)) ?>" required minlength="3" maxlength="80" data-orig="<?= esc($devName) ?>">
                            </div>
                            <div class="pf-hint">Se muestra en el menú, la consola y el registro de sesiones.</div>
                        </div>
                        <div class="pf-row2">
                            <div class="pf-field">
                                <label for="pf_email">Correo electrónico</label>
                                <div class="pf-input"><span class="material-symbols-outlined">mail</span>
                                    <input type="email" id="pf_email" name="email" value="<?= esc($old('email', $user['email'] ?? '')) ?>" required maxlength="120" data-orig="<?= esc($user['email'] ?? '') ?>">
                                </div>
                                <div class="pf-hint">Con este correo inicias sesión.</div>
                            </div>
                            <div class="pf-field">
                                <label for="pf_dni">DNI</label>
                                <div class="pf-input"><span class="material-symbols-outlined">id_card</span>
                                    <input type="text" id="pf_dni" name="dni" value="<?= esc($old('dni', $user['dni'] ?? '')) ?>" required inputmode="numeric" pattern="\d{8}" maxlength="8" data-orig="<?= esc($user['dni'] ?? '') ?>">
                                </div>
                                <div class="pf-hint">8 dígitos. También sirve para iniciar sesión.</div>
                            </div>
                        </div>
                        <div class="pf-row2">
                            <div class="pf-field">
                                <label>Rol</label>
                                <div class="pf-input"><span class="material-symbols-outlined">shield</span><input type="text" value="Desarrollador (Superadmin)" readonly tabindex="-1"></div>
                            </div>
                            <div class="pf-field">
                                <label>Código</label>
                                <div class="pf-input"><span class="material-symbols-outlined">tag</span><input type="text" value="<?= esc($user['admin_code'] ?? 'DEV-001') ?>" readonly tabindex="-1"></div>
                            </div>
                        </div>
                        <div class="pf-actions">
                            <span class="pf-dirty" id="pfDirty"><span class="material-symbols-outlined">edit_note</span> Cambios sin guardar</span>
                            <button type="button" class="pf-btn" id="pfReset"><span class="material-symbols-outlined">undo</span> Deshacer</button>
                            <button type="submit" class="pf-btn pf-btn-primary" id="pfSave" disabled><span class="material-symbols-outlined">save</span> Guardar cambios</button>
                        </div>
                    </form>
                </section>

                <!-- Contraseña -->
                <section class="pf-card">
                    <div class="pf-head">
                        <div>
                            <h3>Contraseña</h3>
                            <p>Ingresa la actual y repite la nueva (mínimo 6 caracteres).</p>
                        </div>
                    </div>
                    <form method="post" action="<?= site_url('dios/update-password') ?>" class="pf-form" id="pwForm" autocomplete="off">
                        <?= csrf_field() ?>
                        <div class="pf-field">
                            <label for="current_password">Contraseña actual</label>
                            <div class="pf-input"><span class="material-symbols-outlined">lock</span>
                                <input type="password" id="current_password" name="current_password" placeholder="Tu contraseña actual" required autocomplete="current-password">
                                <button type="button" class="pf-eye" data-eye="current_password" aria-label="Mostrar contraseña"><span class="material-symbols-outlined">visibility</span></button>
                            </div>
                        </div>
                        <div class="pf-field">
                            <label for="new_password">Nueva contraseña</label>
                            <div class="pf-input"><span class="material-symbols-outlined">key</span>
                                <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres" required minlength="6" autocomplete="new-password">
                                <button type="button" class="pf-eye" data-eye="new_password" aria-label="Mostrar contraseña"><span class="material-symbols-outlined">visibility</span></button>
                            </div>
                            <div class="pf-meter" id="pfMeter" data-l="0"><i></i><i></i><i></i><i></i></div>
                            <div class="pf-meter-txt" id="pfMeterTxt">Escribe una contraseña nueva</div>
                        </div>
                        <div class="pf-field">
                            <label for="confirm_password">Confirmar contraseña</label>
                            <div class="pf-input"><span class="material-symbols-outlined">key</span>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la nueva contraseña" required minlength="6" autocomplete="new-password">
                                <button type="button" class="pf-eye" data-eye="confirm_password" aria-label="Mostrar contraseña"><span class="material-symbols-outlined">visibility</span></button>
                            </div>
                            <div class="pf-match" id="pfMatch"></div>
                        </div>
                        <div class="pf-actions">
                            <button type="button" class="pf-btn" onclick="sugerirPasswordPerfil()"><span class="material-symbols-outlined">casino</span> Sugerir</button>
                            <button type="submit" class="pf-btn pf-btn-primary"><span class="material-symbols-outlined">lock_reset</span> Cambiar contraseña</button>
                        </div>
                    </form>
                </section>
            </div>

            <!-- Actividad real (registro de sesiones) -->
            <section class="pf-card">
                <div class="pf-head">
                    <div>
                        <h3>Actividad reciente</h3>
                        <p>Tus últimos ingresos y salidas del sistema.</p>
                    </div>
                    <a class="pf-btn" href="<?= base_url('dios/sesiones') ?>"><span class="material-symbols-outlined">history</span> Ver todas</a>
                </div>
                <div class="pf-act">
                    <?php if (!$sesiones): ?>
                        <div class="pf-empty">Todavía no hay sesiones registradas.</div>
                    <?php endif; ?>
                    <?php foreach ($sesiones as $se): $in = ($se['evento'] ?? '') === 'login'; ?>
                        <div class="pf-act-row">
                            <span class="pf-act-ico <?= $in ? 'in' : '' ?>"><span class="material-symbols-outlined"><?= $in ? 'login' : 'logout' ?></span></span>
                            <div><b><?= $in ? 'Inicio de sesión' : 'Cierre de sesión' ?></b><small>IP <?= esc($se['ip'] ?: '—') ?></small></div>
                            <time><?= esc($fmt($se['creado'] ?? null)) ?></time>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
          </div>
        </div>
    </div>
</div>
<script>
function sugerirPasswordPerfil() {
    var up = 'ABCDEFGHJKLMNPQRSTUVWXYZ', lo = 'abcdefghjkmnpqrstuvwxyz', di = '23456789', sp = '@#$%';
    var all = up + lo + di + sp;
    function ri(n) { var a = new Uint32Array(1); crypto.getRandomValues(a); return a[0] % n; }
    var pw = up[ri(up.length)] + up[ri(up.length)] + lo[ri(lo.length)] + lo[ri(lo.length)] + di[ri(di.length)] + di[ri(di.length)] + sp[ri(sp.length)];
    while (pw.length < 14) pw += all[ri(all.length)];
    ['new_password', 'confirm_password'].forEach(function (id) { var el = document.getElementById(id); el.value = pw; el.type = 'text'; el.dispatchEvent(new Event('input')); });
}
(function () {
    // Datos de la cuenta: habilita «Guardar» sólo si algo cambió; vista previa del nombre e iniciales
    var form = document.getElementById('pfForm');
    var inputs = form.querySelectorAll('input[data-orig]');
    var save = document.getElementById('pfSave'), dirty = document.getElementById('pfDirty');
    var nameEl = document.getElementById('pf_name'), dni = document.getElementById('pf_dni');
    function ini(n) { var p = n.trim().split(/\s+/); if (!p[0]) return '··'; return (p[0][0] + (p.length > 1 ? p[p.length - 1][0] : (p[0][1] || ''))).toUpperCase(); }
    function check() {
        var changed = Array.prototype.some.call(inputs, function (i) { return i.value.trim() !== i.dataset.orig; });
        save.disabled = !changed; dirty.classList.toggle('on', changed);
        document.getElementById('pfName').textContent = nameEl.value.trim() || nameEl.dataset.orig;
        document.getElementById('pfAvatar').textContent = ini(nameEl.value || nameEl.dataset.orig);
    }
    dni.addEventListener('input', function () { dni.value = dni.value.replace(/\D/g, '').slice(0, 8); });
    inputs.forEach(function (i) { i.addEventListener('input', check); });
    document.getElementById('pfReset').addEventListener('click', function () { inputs.forEach(function (i) { i.value = i.dataset.orig; }); check(); });
    check();

    // Contraseña: mostrar/ocultar, fuerza y coincidencia
    document.querySelectorAll('[data-eye]').forEach(function (b) {
        b.addEventListener('click', function () {
            var el = document.getElementById(b.dataset.eye), show = el.type === 'password';
            el.type = show ? 'text' : 'password';
            b.querySelector('.material-symbols-outlined').textContent = show ? 'visibility_off' : 'visibility';
        });
    });
    var np = document.getElementById('new_password'), cp = document.getElementById('confirm_password');
    var meter = document.getElementById('pfMeter'), mtxt = document.getElementById('pfMeterTxt'), match = document.getElementById('pfMatch');
    function strength() {
        var v = np.value, s = 0;
        if (v.length >= 6) s++; if (v.length >= 10) s++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++; if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) s++;
        if (!v) s = 0; else if (v.length < 6) s = 1;
        meter.dataset.l = Math.min(s, 4);
        mtxt.textContent = !v ? 'Escribe una contraseña nueva' : ['Muy débil', 'Débil', 'Aceptable', 'Buena', 'Fuerte'][Math.min(s, 4)];
        if (!cp.value) { match.textContent = ''; match.className = 'pf-match'; }
        else if (cp.value === v) { match.textContent = 'Las contraseñas coinciden'; match.className = 'pf-match ok'; }
        else { match.textContent = 'Las contraseñas no coinciden'; match.className = 'pf-match bad'; }
    }
    np.addEventListener('input', strength); cp.addEventListener('input', strength);
    document.getElementById('pwForm').addEventListener('submit', function (e) { if (np.value !== cp.value) { e.preventDefault(); strength(); cp.focus(); } });
})();
</script>
</body>
</html>
