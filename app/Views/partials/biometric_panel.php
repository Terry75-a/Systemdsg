<?php
    $bHuella = (int) ($bioHuella ?? 0);
    $bRostro = (int) ($bioRostro ?? 0);
    $bRostroPath = (string) ($bioRostroPath ?? '');
    $bCredId = (string) ($bioHuellaCredId ?? '');
    $bCompleto = $bHuella > 0 && $bRostro > 0;

    // Contexto de marcado: aprovecha la pagina para saber si se marca salida
    // y cual es la hora de salida esperada (para detectar salida anticipada)
    $bioMarkExit      = (bool) (($bioMark['exit'] ?? false) === true);
    $bioMarkHoraSalida = (string) ($bioMark['hora_salida'] ?? '');
?>
<style>
.bio-card {
    background: var(--g-surface, #1b2430); border: 1px solid var(--g-border, #26323f);
    border-radius: var(--g-radius, 16px); padding: 18px; margin-bottom: 18px;
}
.bio-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.bio-head .material-symbols-outlined { color: var(--g-accent, #4f8cff); font-size: 22px; }
.bio-head h3 { margin: 0; font-size: 15px; font-weight: 700; font-family: var(--g-font-display, inherit); }
.bio-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.bio-item {
    border: 1px dashed var(--g-border, #26323f); border-radius: 14px; padding: 14px;
    display: flex; flex-direction: column; gap: 8px;
}
.bio-item-ok { border-style: solid; border-color: rgba(46,204,113,.35); background: rgba(46,204,113,.06); }
.bio-item-label { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; }
.bio-item-label .material-symbols-outlined { font-size: 17px; }
.bio-ok { color: var(--g-success, #2ecc71); }
.bio-no { color: var(--g-error, #ef4444); }
.bio-item-hint { font-size: 11.5px; color: var(--g-text-secondary, #a9b6c8); line-height: 1.45; }
.bio-thumb { width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(46,204,113,.35); }
.bio-note { font-size: 11.5px; color: var(--g-text-secondary, #a9b6c8); margin-top: 12px; display: flex; gap: 6px; align-items: center; }
@media (max-width: 640px) { .bio-grid { grid-template-columns: 1fr; } }

/* Modal propio del partial (evita depender de clases de la pagina) */
.bio-modal {
    position: fixed; inset: 0; z-index: 990; background: rgba(8,12,18,.72);
    display: flex; align-items: center; justify-content: center; padding: 20px;
    opacity: 0; visibility: hidden; transition: opacity .18s, visibility .18s;
}
.bio-modal.c-open { opacity: 1; visibility: visible; }
.bio-dialog {
    background: var(--g-surface, #1b2430); border: 1px solid var(--g-border, #26323f);
    border-radius: 18px; width: 100%; max-width: 430px; overflow: hidden;
    box-shadow: 0 24px 70px rgba(0,0,0,.5);
}
.bio-dialog-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--g-border, #26323f); }
.bio-dialog-head h3 { margin: 0; font-size: 15px; font-weight: 700; display: flex; gap: 8px; align-items: center; }
.bio-dialog-head h3 .material-symbols-outlined { color: var(--g-accent, #4f8cff); }
.bio-x { background: none; border: none; cursor: pointer; color: var(--g-text-secondary, #a9b6c8); font-size: 20px; line-height: 1; padding: 4px; }
.bio-dialog-body { padding: 18px; }
.bio-dialog-foot { display: flex; gap: 10px; justify-content: flex-end; padding: 14px 18px; border-top: 1px solid var(--g-border, #26323f); }
.bio-cam-wrap { position: relative; border-radius: 14px; overflow: hidden; background: #0b0f14; aspect-ratio: 4/3; margin-bottom: 12px; }
.bio-cam-wrap video { width: 100%; height: 100%; object-fit: cover; display: block; }
.bio-cam-err { padding: 26px; text-align: center; color: var(--g-text-secondary, #a9b6c8); font-size: 12.5px; }
.bio-shot { position: absolute; left: 0; right: 0; bottom: 10px; display: flex; justify-content: center; gap: 8px; }
.bio-prev { border-radius: 14px; max-height: 190px; width: 100%; object-fit: cover; }
.bio-step { display: flex; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 12px; }
.bio-dot { width: 26px; height: 26px; border-radius: 50%; background: var(--g-border, #26323f); color: var(--g-text-secondary, #a9b6c8); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
.bio-dot.done { background: var(--g-success, #2ecc71); color: #fff; }
.bio-state-msg { font-size: 12.5px; border-radius: 10px; padding: 10px 12px; margin-top: 12px; display: none; }
.bio-state-msg.show { display: block; }
.bio-msg-ok { background: rgba(46,204,113,.1); color: var(--g-success, #2ecc71); }
.bio-msg-err { background: rgba(239,68,68,.1); color: var(--g-error, #ef4444); }
</style>

<div class="bio-card">
    <div class="bio-head">
        <span class="material-symbols-outlined">fingerprint</span>
        <h3>Biometria obligatoria</h3>
        <?php if ($bCompleto): ?><span class="dev-badge dev-badge-green">Completa</span>
        <?php elseif ($bHuella || $bRostro): ?><span class="dev-badge dev-badge-amber">Parcial</span>
        <?php else: ?><span class="dev-badge dev-badge-red">Pendiente</span><?php endif; ?>
    </div>
    <div class="bio-grid">
        <div class="bio-item <?= $bHuella ? 'bio-item-ok' : '' ?>">
            <div class="bio-item-label">
                <span class="material-symbols-outlined <?= $bHuella ? 'bio-ok' : 'bio-no' ?>"><?= $bHuella ? 'check_circle' : 'radio_button_unchecked' ?></span>
                <span><?= $bHuella ? 'Huella registrada' : 'Registrar huella' ?></span>
            </div>
            <?php if ($bHuella): ?>
                <div class="bio-item-hint"><?= $bHuella === 2 ? 'Registrada (simulacion de prueba).' : 'Usara tu lector biometrico (celular/PC).' ?></div>
                <button type="button" class="dev-btn dev-btn-outline dev-btn-sm" onclick="bioEnrollHuella(1)"><span class="material-symbols-outlined">refresh</span> Re-registrar</button>
            <?php else: ?>
                <div class="bio-item-hint">Tu celular o PC pedira tu huella (o rostro con Face ID).</div>
                <button type="button" class="dev-btn dev-btn-sm" onclick="bioEnrollHuella()"><span class="material-symbols-outlined">fingerprint</span> Registrar huella</button>
                <button type="button" class="dev-btn dev-btn-outline dev-btn-sm" onclick="bioSimHuella()">Probar sin lector (simulacion)</button>
            <?php endif; ?>
        </div>
        <div class="bio-item <?= $bRostro ? 'bio-item-ok' : '' ?>">
            <div class="bio-item-label">
                <span class="material-symbols-outlined <?= $bRostro ? 'bio-ok' : 'bio-no' ?>"><?= $bRostro ? 'check_circle' : 'radio_button_unchecked' ?></span>
                <span>Rostro (facial)</span>
            </div>
            <?php if ($bRostro): ?>
                <?php if ($bRostroPath): ?><img src="<?= esc($bRostroPath) ?>" alt="rostro" class="bio-thumb"><?php endif; ?>
                <button type="button" class="dev-btn dev-btn-outline dev-btn-sm" onclick="bioEnrollRostro()"><span class="material-symbols-outlined">refresh</span> Actualizar foto</button>
            <?php else: ?>
                <div class="bio-item-hint">Activa la camara, pon el rostro frente a ella y captura la foto.</div>
                <button type="button" class="dev-btn dev-btn-sm" onclick="bioEnrollRostro()"><span class="material-symbols-outlined">photo_camera</span> Capturar rostro</button>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!$bCompleto): ?>
        <div class="bio-note"><span class="material-symbols-outlined" style="font-size:16px;">lock</span> Registra tu huella o tu rostro; al marcar se te pedirá cualquiera de los dos.</div>
    <?php endif; ?>
</div>

<!-- Modal: capturar rostro (camara) -->
<div class="bio-modal" id="bioRostroModal">
    <div class="bio-dialog">
        <div class="bio-dialog-head">
            <h3><span class="material-symbols-outlined">portrait</span> Capturar rostro</h3>
            <button class="bio-x" onclick="bioCloseRostro()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="bio-dialog-body">
            <div class="bio-cam-wrap" id="bioCamWrap">
                <video id="bioCam" autoplay playsinline muted></video>
                <div class="bio-cam-err" id="bioCamErr" style="display:none;">
                    No se pudo abrir la camara.<br>Usa el boton "Subir foto" como alternativa.
                </div>
                <div class="bio-shot" id="bioShot">
                    <button type="button" class="dev-btn dev-btn-sm" onclick="bioTakeShot()"><span class="material-symbols-outlined">camera</span> Capturar</button>
                </div>
            </div>
            <img class="bio-prev" id="bioShotPrev" style="display:none;" alt="captura">
            <div class="bio-state-msg" id="bioRostroMsg"></div>
            <div style="margin-top:12px; display:flex; gap:10px; align-items:center;">
                <button type="button" class="dev-btn dev-btn-outline dev-btn-sm" onclick="bioUploadRostro()"><span class="material-symbols-outlined">upload_file</span> Subir foto</button>
                <input type="file" id="bioRostroFile" accept="image/*" style="display:none;">
            </div>
        </div>
        <div class="bio-dialog-foot">
            <button type="button" class="dev-btn dev-btn-outline" onclick="bioCloseRostro()">Cancelar</button>
            <button type="button" class="dev-btn dev-btn-primary" id="bioRostroSave" disabled onclick="bioSaveRostro()">
                <span class="material-symbols-outlined">check</span> Guardar rostro
            </button>
        </div>
    </div>
</div>

<!-- Modal: verificar biometria antes de marcar -->
<div class="bio-modal" id="bioCheckModal">
    <div class="bio-dialog">
        <div class="bio-dialog-head">
            <h3><span class="material-symbols-outlined">verified_user</span> Verificar identidad</h3>
            <button class="bio-x" onclick="bioCloseCheck()"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="bio-dialog-body">
            <div style="font-size:12.5px; color:var(--g-text-secondary,#a9b6c8); margin-bottom:12px;">
                Antes de marcar te pedimos tu <strong>huella</strong> o, si este equipo no tiene lector, verificamos tu <strong>rostro</strong>.
            </div>
            <div id="bioCheckMethod" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                <button type="button" class="dev-btn dev-btn-sm" data-method="huella" id="bioMethodHuella" style="display:none;"><span class="material-symbols-outlined">fingerprint</span> Verificar con huella</button>
                <button type="button" class="dev-btn dev-btn-sm" data-method="facial" id="bioMethodFacial" style="display:none;"><span class="material-symbols-outlined">portrait</span> Verificar con rostro</button>
            </div>
            <div class="bio-step" id="bioCheckHuellaBox">
                <span class="bio-dot" id="bioHuellaDot">1</span>
                <span>Coloca tu dedo en el lector <span style="display:inline-flex;gap:6px;margin-left:6px;">
                    <button type="button" class="dev-btn dev-btn-sm dev-btn-primary" id="bioCheckHuellaBtn" onclick="bioVerifyHuella()"><span class="material-symbols-outlined">fingerprint</span> Verificar</button>
                </span></span>
            </div>
            <div id="bioCheckFacialBox">
                <div class="bio-cam-wrap" id="bioCheckCamWrap">
                    <video id="bioCheckCam" autoplay playsinline muted></video>
                    <div class="bio-cam-err" id="bioCheckCamErr" style="display:none;">
                        No se pudo abrir la camara.<br>Sin camara no puedes verificar tu rostro.
                    </div>
                    <div class="bio-shot">
                        <button type="button" class="dev-btn dev-btn-sm" onclick="bioCheckTakeShot()"><span class="material-symbols-outlined" style="font-size:17px;">camera</span> Fotografiar rostro ahora</button>
                    </div>
                </div>
                <img class="bio-prev" id="bioCheckPrev" style="display:none;" alt="evidencia">
            </div>
            <button type="button" class="dev-btn dev-btn-outline dev-btn-sm" id="bioSwitchBtn" onclick="bioSwitchMethod()" style="display:none;margin-top:6px;">
                <span class="material-symbols-outlined">swap_horiz</span> <span id="bioSwitchText"></span>
            </button>

            <div id="bioCheckEarly" style="display:none; margin-top:14px;">
                <div style="background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.32); border-radius:12px; padding:11px 13px; margin-bottom:9px; display:flex; gap:10px; align-items:flex-start;">
                    <span class="material-symbols-outlined" style="color:var(--g-warning,#f59e0b); font-size:19px; flex-shrink:0;">logout</span>
                    <div style="font-size:12.5px; color:var(--g-text-secondary,#a9b6c8); line-height:1.5;">
                        Estás saliendo <strong style="color:var(--g-text,#e5edf7);">antes de tu hora de salida</strong>
                        (<span id="bioCheckHoraSalida" style="font-family:monospace; font-weight:700;"></span>).
                        Escribe el motivo; quedará en <strong style="color:var(--g-text,#e5edf7);">revisión</strong> para tu administrador.
                    </div>
                </div>
                <textarea id="bioCheckJust"
                    placeholder="Ej: Tengo examen, permiso por salud, diligencia personal..."
                    style="width:100%; box-sizing:border-box; border:1px solid var(--g-border,#26323f); border-radius:12px; padding:11px 13px; font-size:13.5px; font-family:var(--g-font,inherit); color:var(--g-text,#e5edf7); background:var(--g-surface,#1b2430); resize:vertical; min-height:72px;"></textarea>
            </div>

            <div class="bio-state-msg" id="bioCheckMsg"></div>
        </div>
        <div class="bio-dialog-foot">
            <button type="button" class="dev-btn dev-btn-outline" onclick="bioCloseCheck()">Cancelar</button>
            <button type="button" class="dev-btn dev-btn-primary" id="bioCheckConfirm" disabled onclick="bioSubmitCheck()">
                <span class="material-symbols-outlined">verified_user</span> Confirmar y marcar
            </button>
        </div>
    </div>
    <form id="bioCheckForm" method="post" action="<?= site_url('mi-panel/registrar') ?>" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="bio_ok" value="1">
        <input type="hidden" name="bio_metodo" id="bioCheckMetodo" value="">
        <input type="hidden" name="evidencia" id="bioCheckEvi" value="">
        <input type="hidden" name="justificacion" id="bioCheckJustVal" value="">
    </form>
</div>

<script>
(function () {
    window.__bioState = {
        humino_huellaOk: false,
        humino_selfie: '',
        stream: null,
        checkStream: null,
        pendingEvidencia: ''
    };
    function $(id) { return document.getElementById(id); }
    function csrfToken() {
        var el = document.querySelector('input[name=csrf_test_name]');
        return el ? el.value : '<?= session('csrf_hash') ?>';
    }
    function fetchFreshCsrf() {
        return fetch('<?= site_url('mi-panel/bio-session') ?>', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) { return (d && d.csrfHash) ? d.csrfHash : csrfToken(); })
            .catch(function () { return csrfToken(); });
    }
    function b64ToU8(b64) {
        var s = atob(b64.replace(/-/g, '+').replace(/_/g, '/'));
        var u = new Uint8Array(s.length);
        for (var i = 0; i < s.length; i++) u[i] = s.charCodeAt(i);
        return u;
    }
    function u8ToB64(arr) {
        var s = '';
        new Uint8Array(arr).forEach(function (b) { s += String.fromCharCode(b); });
        return btoa(s).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }
    function msg(id, text, ok) {
        var el = $(id);
        el.textContent = text;
        el.className = 'bio-state-msg show ' + (ok ? 'bio-msg-ok' : 'bio-msg-err');
    }
    function fetchPost(url, body) {
        return fetchFreshCsrf().then(function (tok) {
            body.set('csrf_test_name', tok);
            return fetch(url, { method: 'POST', body: body, credentials: 'same-origin' }).then(function (r) { return r.json(); });
        });
    }
    function stopStream() {
        if (window.__bioState.stream) { window.__bioState.stream.getTracks().forEach(function (t) { t.stop(); }); window.__bioState.stream = null; }
        if (window.__bioState.checkStream) { window.__bioState.checkStream.getTracks().forEach(function (t) { t.stop(); }); window.__bioState.checkStream = null; }
    }
    function startCam(video, errEl) {
        errEl.style.display = 'none';
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            errEl.style.display = 'block';
            return Promise.reject(new Error('no getUserMedia'));
        }
        return navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 720 } }, audio: false })
            .then(function (stream) {
                video.srcObject = stream;
                if (video.id === 'bioCheckCam') window.__bioState.checkStream = stream;
                else window.__bioState.stream = stream;
            })
            .catch(function () {
                errEl.style.display = 'block';
                throw new Error('cam');
            });
    }
    function snap(video, imgEl, wrap, saveBtn) {
        var v = video.videoWidth > 0 ? video : null;
        if (!v) { msg('bioRostroMsg', 'Primero captura el video (camara activa y permitida).', false); return; }
        var c = document.createElement('canvas');
        c.width = v.videoWidth; c.height = v.videoHeight;
        c.getContext('2d').drawImage(v, 0, 0, c.width, c.height);
        var url = c.toDataURL('image/jpeg', 0.82);
        imgEl.src = url; imgEl.style.display = 'block';
        if (wrap) wrap.style.display = 'none';
        if (saveBtn) saveBtn.disabled = false;
        window.__bioState.pendingEvidencia = url;
    }

    // ── Registro de huella (WebAuthn) ──────────────────────────
    window.bioEnrollHuella = function (again) {
        msg('bioRostroMsg', '', true);
        fetch('<?= site_url('mi-panel/bio-session') ?>', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) { alert(d.msj || 'Error'); return; }
                if (!window.PublicKeyCredential || !window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
                    if (!again) bioSimHuella();
                    else alert('Tu navegador no expone un lector biometrico. Usa la opcion "Probar sin lector".');
                    return;
                }
                window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().then(function (avail) {
                    if (!avail) {
                        if (!again) bioSimHuella();
                        else alert('No hay autenticador biometrico (lector de huella/Face ID) disponible. Usa "Probar sin lector".');
                        return;
                    }
                    navigator.credentials.create({
                        publicKey: {
                            challenge: b64ToU8(d.challenge),
                            rp: { id: location.hostname, name: 'DSG Peru' },
                            user: { id: new Uint8Array(16), name: d.userEmail || d.userName, displayName: d.userName },
                            pubKeyCredParams: [{ type: 'public-key', alg: -7 }, { type: 'public-key', alg: -257 }],
                            authenticatorSelection: { authenticatorAttachment: 'platform', userVerification: 'required', residentKey: 'required' },
                            timeout: 60000
                        }
                    }).then(function (cred) {
                        var body = new FormData();
                        body.set('credential_id', u8ToB64(cred.rawId));
                        body.set('mode', 'realkey');
                        fetchPost('<?= site_url('mi-panel/guardar-huella') ?>', body).then(function (res) {
                            alert(res.msj || 'Huella registrada');
                            if (res.ok) location.reload();
                        });
                    }).catch(function () {
                        msg('bioRostroMsg', 'No se pudo registrar la huella. Prueba la opcion "Probar sin lector".', false);
                    });
                });
            });
    };
    window.bioSimHuella = function () {
        var body = new FormData();
        body.set('credential_id', 'sim-' + Math.random().toString(36).slice(2));
        body.set('mode', 'sim');
        fetchPost('<?= site_url('mi-panel/guardar-huella') ?>', body).then(function (res) {
            alert(res.msj || 'Huella registrada');
            if (res.ok) location.reload();
        });
    };

    // ── Registro de rostro ─────────────────────────────────────
    window.bioEnrollRostro = function () {
        $('bioRostroModal').classList.add('c-open');
        $('bioShotPrev').style.display = 'none';
        $('bioCamWrap').style.display = '';
        $('bioRostroSave').disabled = true;
        window.__bioState.pendingEvidencia = '';
        startCam($('bioCam'), $('bioCamErr')).catch(function () {});
    };
    window.bioTakeShot = function () {
        snap($('bioCam'), $('bioShotPrev'), $('bioCamWrap'), $('bioRostroSave'));
    };
    window.bioUploadRostro = function () { $('bioRostroFile').click(); };
    $('bioRostroFile').addEventListener('change', function () {
        var f = this.files && this.files[0];
        if (!f) return;
        var rd = new FileReader();
        rd.onload = function () {
            window.__bioState.pendingEvidencia = rd.result;
            $('bioShotPrev').src = rd.result; $('bioShotPrev').style.display = 'block';
            $('bioCamWrap').style.display = 'none';
            $('bioRostroSave').disabled = false;
        };
        rd.readAsDataURL(f);
    });
    window.bioSaveRostro = function () {
        var url = window.__bioState.pendingEvidencia;
        if (!url) { msg('bioRostroMsg', 'Captura o sube una foto primero.', false); return; }
        var body = new FormData();
        body.set('imagen', url);
        fetchPost('<?= site_url('mi-panel/guardar-rostro') ?>', body).then(function (res) {
            msg('bioRostroMsg', res.msj || 'Rostro guardado', !!res.ok);
            if (res.ok) setTimeout(function () { location.reload(); }, 900);
        }).catch(function () {
            msg('bioRostroMsg', 'Error de red al guardar.', false);
        });
    };
    window.bioCloseRostro = function () {
        $('bioRostroModal').classList.remove('c-open');
        if (window.__bioState.stream) { window.__bioState.stream.getTracks().forEach(function (t) { t.stop(); }); window.__bioState.stream = null; }
    };

    // ── Verificacion al marcar: huella O rostro ────────────────
    var B = window.__bioState;

    window.openBioCheck = function () {
        var tH = <?= $bHuella ?> > 0;
        var tR = <?= $bRostro ?> > 0;
        if (!tH && !tR) {
            alert('Registra tu huella o tu rostro en la seccion Biometria antes de marcar asistencia.');
            return;
        }
        B.humino_huellaOk = false;
        B.facialOk = false;
        B.selfie = '';
        B.method = tH ? 'huella' : 'facial';
        $('bioCheckModal').classList.add('c-open');
        $('bioCheckMsg').className = 'bio-state-msg';
        $('bioHuellaDot').textContent = '1';
        $('bioHuellaDot').classList.remove('done');
        $('bioCheckConfirm').disabled = true;
        $('bioCheckHuellaBtn').disabled = false;

        // Salida anticipada: si se marca salida antes de la hora prevista, pedir motivo
        B.earlyExit = setupEarlyExit();
        var box = $('bioCheckEarly');
        if (B.earlyExit) {
            $('bioCheckHoraSalida').textContent = '<?= esc($bioMarkHoraSalida) ?>';
            $('bioCheckJust').value = '';
            $('bioCheckJustVal').value = '';
            box.style.display = '';
        } else {
            $('bioCheckJust').value = '';
            $('bioCheckJustVal').value = '';
            box.style.display = 'none';
        }

        detectMethod(tH, tR).then(function (m) {
            m = (m === 'huella' && !tH) || (m === 'facial' && !tR) ? (tH ? 'huella' : 'facial') : m;
            showMethod(m);
        });
    };

    function setupEarlyExit() {
        if (<?= $bioMarkExit ? '1' : '0' ?> !== 1) return false;
        var h = '<?= esc($bioMarkHoraSalida) ?>';
        if (!/^\d{2}:\d{2}$/.test(h)) return false;
        var p = h.split(':');
        var salMin = parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
        var n = new Date();
        return (n.getHours() * 60 + n.getMinutes()) < salMin;
    }

    function canFinger() {
        if (!window.PublicKeyCredential || !window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
            return Promise.resolve(false);
        }
        return window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
    }
    function detectMethod(tH, tR) {
        if (!tH || !tR) return Promise.resolve(tH ? 'huella' : 'facial');
        return canFinger().then(function (yes) {
            var real = '<?= esc($bCredId) ?>'.indexOf('sim-') !== 0;
            return yes && real ? 'huella' : 'facial';
        });
    }
    function showMethod(m) {
        B.method = m;
        var isH = m === 'huella';
        $('bioCheckHuellaBox').style.display = isH ? 'flex' : 'none';
        $('bioCheckFacialBox').style.display = isH ? 'none' : '';
        $('bioMethodHuella').style.display = (<?= $bHuella ?> > 0 && !isH) ? 'flex' : 'none';
        $('bioMethodFacial').style.display = (<?= $bRostro ?> > 0 && isH) ? 'flex' : 'none';
        $('bioCheckMetodo').value = m;
        if (isH) {
            $('bioCheckCamWrap').style.display = 'none';
            if (B.checkStream) { B.checkStream.getTracks().forEach(function (t) { t.stop(); }); B.checkStream = null; }
            $('bioSwitchText').textContent = 'No tengo lector de huella · usar rostro';
            $('bioSwitchBtn').style.display = <?= $bRostro ?> > 0 ? 'flex' : 'none';
        } else {
            $('bioSwitchText').textContent = 'Tengo lector de huella · usar huella';
            $('bioSwitchBtn').style.display = <?= $bHuella ?> > 0 ? 'flex' : 'none';
            $('bioCheckCamWrap').style.display = '';
            $('bioCheckPrev').style.display = 'none';
            if (B.checkStream) { B.checkStream.getTracks().forEach(function (t) { t.stop(); }); B.checkStream = null; }
            startCam($('bioCheckCam'), $('bioCheckCamErr')).catch(function () {
                $('bioCheckMsg').className = 'bio-state-msg show bio-msg-err';
                $('bioCheckMsg').textContent = 'No se pudo abrir la camara. Intentelo de nuevo.';
            });
        }
    }
    window.bioSwitchMethod = function () {
        $('bioCheckMsg').className = 'bio-state-msg';
        showMethod(B.method === 'huella' ? 'facial' : 'huella');
    };
    document.getElementById('bioMethodHuella').addEventListener('click', function () { showMethod('huella'); });
    document.getElementById('bioMethodFacial').addEventListener('click', function () { showMethod('facial'); });

    window.bioVerifyHuella = function () {
        $('bioCheckHuellaBtn').disabled = true;
        var credId = '<?= esc($bCredId) ?>';
        if (!window.PublicKeyCredential || credId === '' || credId.indexOf('sim-') === 0) {
            return bioHuellaDone();
        }
        fetch('<?= site_url('mi-panel/bio-session') ?>', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) { $('bioCheckHuellaBtn').disabled = false; msg('bioCheckMsg', 'Sesion invalida.', false); return; }
                navigator.credentials.get({
                    publicKey: {
                        challenge: b64ToU8(d.challenge),
                        allowCredentials: [{ type: 'public-key', id: b64ToU8(credId) }],
                        userVerification: 'required',
                        timeout: 60000
                    }
                }).then(function () { bioHuellaDone(); }, function () {
                    $('bioCheckHuellaBtn').disabled = false;
                    msg('bioCheckMsg', 'Huella no reconocida. Reintenta o usa "verificar con rostro".', false);
                });
            }).catch(function () {
                $('bioCheckHuellaBtn').disabled = false;
                msg('bioCheckMsg', 'No se pudo verificar la huella. Usa "verificar con rostro".', false);
            });
    };
    function bioHuellaDone() {
        $('bioHuellaDot').textContent = '✓';
        $('bioHuellaDot').classList.add('done');
        $('bioCheckHuellaBtn').disabled = true;
        B.humino_huellaOk = true;
        msg('bioCheckMsg', 'Huella verificada ✓', true);
        bioTryEnable();
    }

    // Validador facial (face-api.js, CDN)
    var F_MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
    var F_READY = false;
    var F_LOADING = null;
    function faceReady() {
        if (window.faceapi && F_READY) return Promise.resolve(true);
        if (F_LOADING) return F_LOADING;
        F_LOADING = new Promise(function (res, rej) {
            function loadModels() {
                Promise.all([
                    window.faceapi.nets.tinyFaceDetector.loadFromUri(F_MODEL_URL),
                    window.faceapi.nets.faceLandmark68Net.loadFromUri(F_MODEL_URL),
                    window.faceapi.nets.faceRecognitionNet.loadFromUri(F_MODEL_URL)
                ]).then(function () { F_READY = true; F_LOADING = null; res(true); }, function () { F_LOADING = null; rej(new Error('models')); });
            }
            if (window.faceapi) { loadModels(); return; }
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js';
            s.onload = loadModels;
            s.onerror = function () { F_LOADING = null; rej(new Error('cdn')); };
            document.head.appendChild(s);
        });
        return F_LOADING;
    }
    function verificarFacial(selfieUrl) {
        var refUrl = '<?= base_url('uploads/' . $bRostroPath) ?>';
        return faceReady().then(function () {
            return Promise.all([faceapi.fetchImage(refUrl), faceapi.fetchImage(selfieUrl)]);
        }).then(function (ims) {
            var opt = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.45 });
            return Promise.all([
                faceapi.detectSingleFace(ims[0], opt).withFaceLandmarks().withFaceDescriptor(),
                faceapi.detectSingleFace(ims[1], opt).withFaceLandmarks().withFaceDescriptor()
            ]);
        }).then(function (rs) {
            if (!rs[0]) return { ok: false, msg: 'No se pudo leer tu foto de referencia. Re-registra tu rostro.' };
            if (!rs[1]) return { ok: false, msg: 'No se detecto ningun rostro en la foto. Reintenta mirando a la camara.' };
            var dist = faceapi.euclideanDistance(rs[0].descriptor, rs[1].descriptor);
            if (dist < 0.55) return { ok: true, msg: 'Rostro verificado ✓' };
            return { ok: false, msg: 'El rostro no coincide con el registrado (d=' + dist.toFixed(2) + '). Reintenta.' };
        }).catch(function () {
            return { ok: false, msg: 'No se pudo cargar el validador facial (revisa tu conexion).' };
        });
    }

    window.bioCheckTakeShot = function () {
        snap($('bioCheckCam'), $('bioCheckPrev'), $('bioCheckCamWrap'), null);
        B.selfie = B.pendingEvidencia || '';
        if (!B.selfie) { msg('bioCheckMsg', 'Activa la camara y vuelve a fotografiar tu rostro.', false); return; }
        msg('bioCheckMsg', 'Comparando tu rostro con el registrado...', true);
        $('bioCheckConfirm').disabled = true;
        verificarFacial(B.selfie).then(function (res) {
            if (res.ok) {
                B.facialOk = true;
                msg('bioCheckMsg', res.msg, true);
            } else {
                B.facialOk = false;
                msg('bioCheckMsg', res.msg, false);
            }
            bioTryEnable();
        });
    };
    function bioTryEnable() {
        $('bioCheckConfirm').disabled = !(B.humino_huellaOk || B.facialOk);
    }
    window.bioSubmitCheck = function () {
        if (!(B.humino_huellaOk || B.facialOk)) {
            msg('bioCheckMsg', 'Verifica tu huella o tu rostro para continuar.', false);
            return;
        }
        if (B.earlyExit) {
            var just = $('bioCheckJust').value.trim();
            if (just === '') {
                msg('bioCheckMsg', 'Escribe el motivo de tu salida anticipada para registrar la salida.', false);
                return;
            }
            $('bioCheckJustVal').value = just;
        }
        $('bioCheckEvi').value = B.selfie;
        $('bioCheckMetodo').value = B.method;
        fetchFreshCsrf().then(function (tok) {
            var inp = $('bioCheckForm').querySelector('input[name=csrf_test_name]');
            if (inp) inp.value = tok;
            $('bioCheckForm').submit();
        });
    };
    window.bioCloseCheck = function () {
        $('bioCheckModal').classList.remove('c-open');
        if (window.__bioState.checkStream) { window.__bioState.checkStream.getTracks().forEach(function (t) { t.stop(); }); window.__bioState.checkStream = null; }
    };

    // Mantener fresco el token CSRF en otros formularios de la pagina
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (f.tagName !== 'FORM' || f.id === 'bioCheckForm') return;
        if (f.__bioFresh) return;
        e.preventDefault();
        f.__bioFresh = true;
        fetchFreshCsrf().then(function (tok) {
            var i = f.querySelector('input[name=csrf_test_name]');
            if (i) i.value = tok;
            f.submit();
        });
    });
})();
</script>