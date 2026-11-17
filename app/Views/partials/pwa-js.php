<?php /** Registro del Service Worker + instalación PWA (banner propio tipo instalador). */ ?>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(function () {});
    });
}
</script>
<script>
(function () {
    var isStandalone =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.matchMedia('(display-mode: minimal-ui)').matches ||
        window.navigator.standalone === true;
    if (isStandalone) return;

    // La invitación a instalar la app pertenece SOLO al login de asistencias.
    // En el resto del sistema (admin, paneles, sitio) no se muestra.
    var esLoginAsistencias = /(^|\/)login-verde\/?$/.test(window.location.pathname);
    if (!esLoginAsistencias) return;

    // v2: el aviso anterior (solo instrucciones) no debe bloquear el instalador real.
    var KEY = 'pwa_install_dismissed_v2';
    var COOLDOWN = 14 * 24 * 60 * 60 * 1000;

    var deferred = window.__pwaInstallPrompt || null;
    var banner = null;
    var isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    // En iPhone, Chrome/Firefox/Edge no pueden agregar la app al inicio: solo Safari.
    var isIOSNoSafari = isIOS && /CriOS|FxiOS|EdgiOS|OPiOS|GSA/i.test(navigator.userAgent);

    function isDismissed() {
        var raw = null;
        try { raw = localStorage.getItem(KEY); } catch (e) {}
        if (raw === '1') return true;
        var at = parseInt(raw || '0', 10) || 0;
        return !!(at && (Date.now() - at) < COOLDOWN);
    }

    function saveDismiss(permanent) {
        try { localStorage.setItem(KEY, permanent ? '1' : String(Date.now())); } catch (e) {}
    }

    function hideBanner() {
        if (!banner) return;
        var b = banner;
        banner = null;
        b.classList.remove('is-visible');
        setTimeout(function () { if (b.parentNode) b.parentNode.removeChild(b); }, 350);
    }

    function showBanner(inner) {
        if (banner || isStandalone) return;
        banner = document.createElement('div');
        banner.className = 'pwa-install-banner';
        banner.innerHTML =
            '<div class="pwa-install-card">' +
                '<img class="pwa-install-icon" src="<?= base_url('images/pwa/icon-192.png') ?>" alt="DSG">' +
                '<div class="pwa-install-body">' + inner + '</div>' +
            '</div>';
        document.body.appendChild(banner);
        requestAnimationFrame(function () {
            if (banner) banner.classList.add('is-visible');
        });
    }

    // Pinta el contenido dentro del banner, creándolo si aún no existe.
    function paintBanner(inner) {
        if (!banner) {
            showBanner(inner);
            return;
        }
        var body = banner.querySelector('.pwa-install-body');
        if (body) body.innerHTML = inner;
    }

    // Ejecuta el instalador nativo del navegador (Chrome/Edge).
    function instalar() {
        var d = deferred || window.__pwaInstallPrompt;
        if (!d || typeof d.prompt !== 'function') return false;
        try {
            d.prompt();
            hideBanner();
            if (d.userChoice && d.userChoice.then) {
                d.userChoice.then(function (res) {
                    if (!res || res.outcome !== 'accepted') saveDismiss(false);
                    deferred = null;
                    window.__pwaInstallPrompt = null;
                }).catch(function () {});
            }
            return true;
        } catch (err) {
            return false;
        }
    }

    function mostrarInstalador() {
        deferred = deferred || window.__pwaInstallPrompt;
        if (!deferred || isDismissed() || banner) return;
        paintBanner(
            '<strong>Instalar Asisten DSG</strong>' +
            '<span>Tu asistencia, lista en la pantalla de inicio.</span>' +
            '<div class="pwa-install-actions">' +
                '<button type="button" class="pwa-install-btn pwa-install-go">Instalar</button>' +
                '<button type="button" class="pwa-install-btn pwa-install-skip">Ahora no</button>' +
            '</div>'
        );
        if (!banner) return;
        banner.querySelector('.pwa-install-go').addEventListener('click', instalar);
        banner.querySelector('.pwa-install-skip').addEventListener('click', function () {
            hideBanner();
            saveDismiss(false);
        });
    }

    // ── iPhone / iPad ─────────────────────────────────────────────
    // Apple no permite instalar desde un botón: el máximo acercamiento es
    // abrir la guía de «Compartir → Agregar a pantalla de inicio».
    function copiarEnlace(btn) {
        var enlace = window.location.href;
        var listo = function () {
            btn.textContent = 'Enlace copiado';
            setTimeout(function () { btn.textContent = 'Copiar enlace'; }, 2200);
        };
        var manual = function () {
            var ta = document.createElement('textarea');
            ta.value = enlace;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); listo(); } catch (e) {}
            document.body.removeChild(ta);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(enlace).then(listo).catch(manual);
        } else {
            manual();
        }
    }

    function cerrarGuiaIOS(overlay) {
        if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
        saveDismiss(false);
    }

    function abrirGuiaIOS() {
        hideBanner();
        var iconoCompartir =
            '<span class="pwa-ios-share" aria-hidden="true">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M12 16V4"/><path d="m8 8 4-4 4 4"/><path d="M5 12v7a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-7"/>' +
                '</svg>' +
            '</span>';
        var contenido = isIOSNoSafari
            ? '<p class="pwa-ios-lead">Para agregarla a tu pantalla de inicio en iPhone, abre esta página en <b>Safari</b>.</p>' +
              '<button type="button" class="pwa-install-btn pwa-install-go pwa-ios-copy-btn">Copiar enlace</button>'
            : '<p class="pwa-ios-lead">Se agrega a tu pantalla de inicio y abre como una app, sin App Store.</p>' +
              '<ol class="pwa-ios-steps">' +
                  '<li><span class="pwa-ios-num">1</span><div>Toca <b>Compartir</b> ' + iconoCompartir + ' en Safari.</div></li>' +
                  '<li><span class="pwa-ios-num">2</span><div>Elige <b>«Agregar a pantalla de inicio»</b>.</div></li>' +
                  '<li><span class="pwa-ios-num">3</span><div>Toca <b>«Agregar»</b> y listo.</div></li>' +
              '</ol>';
        var overlay = document.createElement('div');
        overlay.className = 'pwa-ios-guide';
        overlay.innerHTML =
            '<div class="pwa-ios-sheet" role="dialog" aria-modal="true" aria-labelledby="pwa-ios-title">' +
                '<button type="button" class="pwa-ios-close" aria-label="Cerrar">&times;</button>' +
                '<h2 id="pwa-ios-title">Instalar en iPhone</h2>' +
                contenido +
                '<button type="button" class="pwa-install-btn pwa-install-go pwa-ios-done">Entendido</button>' +
            '</div>';
        document.body.appendChild(overlay);
        overlay.querySelector('.pwa-ios-close').addEventListener('click', function () { cerrarGuiaIOS(overlay); });
        overlay.querySelector('.pwa-ios-done').addEventListener('click', function () { cerrarGuiaIOS(overlay); });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) cerrarGuiaIOS(overlay);
        });
        var copiar = overlay.querySelector('.pwa-ios-copy-btn');
        if (copiar) copiar.addEventListener('click', function () { copiarEnlace(copiar); });
        var cerrarEsc = function (e) {
            if (e.key === 'Escape') {
                cerrarGuiaIOS(overlay);
                document.removeEventListener('keydown', cerrarEsc);
            }
        };
        document.addEventListener('keydown', cerrarEsc);
        overlay.querySelector('.pwa-ios-close').focus();
    }

    function mostrarIOS() {
        if (!isIOS || isDismissed() || banner) return;
        paintBanner(
            '<strong>Instalar Asisten DSG</strong>' +
            '<span>En tu iPhone, directo a la pantalla de inicio.</span>' +
            '<div class="pwa-install-actions">' +
                '<button type="button" class="pwa-install-btn pwa-install-go">Cómo instalar</button>' +
                '<button type="button" class="pwa-install-btn pwa-install-skip">Ahora no</button>' +
            '</div>'
        );
        if (!banner) return;
        banner.querySelector('.pwa-install-go').addEventListener('click', abrirGuiaIOS);
        banner.querySelector('.pwa-install-skip').addEventListener('click', function () {
            hideBanner();
            saveDismiss(false);
        });
    }

    // El evento lo captura pwa-head (en el <head>) y nos avisa por aquí.
    window.addEventListener('pwa-install-available', function () {
        deferred = window.__pwaInstallPrompt || deferred;
        if (!isDismissed()) mostrarInstalador();
    });

    // Respaldo por si esta vista se usa sin pwa-head.
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        window.__pwaInstallPrompt = e;
        deferred = e;
        if (!isDismissed()) mostrarInstalador();
    });

    window.addEventListener('appinstalled', function () {
        saveDismiss(true);
        hideBanner();
        deferred = null;
        window.__pwaInstallPrompt = null;
    });

    // Si el instalador ya estaba disponible al cargar, mostramos el aviso.
    if (deferred) {
        setTimeout(mostrarInstalador, 1200);
    }

    // En iPhone/iPad no existe instalador nativo: mostramos la guía.
    if (isIOS) {
        setTimeout(function () {
            if (!banner && !isDismissed()) mostrarIOS();
        }, 2500);
    }
})();
</script>
<script>
/* Web Push: suscripción real (se llama desde el toggle de Notificaciones del perfil). */
window.dsgPush = (function () {
    function urlB64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = atob(base64);
        var outputArray = new Uint8Array(rawData.length);
        for (var i = 0; i < rawData.length; i++) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }

    function csrfToken() {
        var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
        return m ? decodeURIComponent(m[1]) : '';
    }

    return {
        soportado: function () {
            return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
        },
        permiso: function () {
            return this.soportado() ? Notification.permission : 'unsupported';
        },
        activar: async function () {
            if (!this.soportado()) return { ok: false, msj: 'Tu navegador no soporta notificaciones push' };
            var perm = await Notification.requestPermission();
            if (perm !== 'granted') return { ok: false, msj: 'Permiso de notificaciones denegado' };
            var reg, sub;
            try {
                reg = await navigator.serviceWorker.ready;
                sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    var r = await fetch('<?= base_url('push/clave') ?>', { credentials: 'same-origin' });
                    var d = await r.json();
                    if (!d.ok || !d.clave) return { ok: false, msj: 'Servidor de notificaciones no configurado' };
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlB64ToUint8Array(d.clave)
                    });
                }
            } catch (err) {
                return { ok: false, msj: 'El navegador no pudo registrarse para recibir avisos (' + (err && err.message ? err.message : 'error') + ')' };
            }
            var resp = await fetch('<?= base_url('push/suscribir') ?>', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body: JSON.stringify(sub.toJSON())
            });
            var j = await resp.json().catch(function () { return { ok: false }; });
            if (j.ok) { try { localStorage.setItem('empleado_push_enabled', 'true'); } catch (e) {} }
            return { ok: !!j.ok, msj: j.msj || '' };
        },
        desactivar: async function () {
            try {
                if (this.soportado()) {
                    var reg = await navigator.serviceWorker.ready;
                    var sub = await reg.pushManager.getSubscription();
                    if (sub) {
                        await fetch('<?= base_url('push/cancelar') ?>', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                            body: JSON.stringify({ endpoint: sub.endpoint })
                        });
                        await sub.unsubscribe();
                    }
                }
            } catch (e) {}
            try { localStorage.setItem('empleado_push_enabled', 'false'); } catch (e) {}
            return { ok: true };
        }
    };
})();
</script>
<style>
.pwa-install-banner {
  position: fixed;
  left: 12px;
  right: 12px;
  bottom: 12px;
  z-index: 400;
  transform: translateY(130%);
  transition: transform 350ms cubic-bezier(0.2, 0.9, 0.3, 1);
}
.pwa-install-banner.is-visible { transform: none; }
.pwa-install-card {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  background: #ffffff;
  color: #202124;
  border: 1px solid #e8eaed;
  border-radius: 16px;
  padding: 14px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.28);
  font-family: inherit;
}
.pwa-install-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  flex: 0 0 auto;
}
.pwa-install-body {
  flex: 1 1 140px;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.pwa-install-body strong { font-size: 14.5px; }
.pwa-install-body span { font-size: 12.5px; color: #5f6368; line-height: 1.4; }
.pwa-install-ios-icon { font-weight: 700; }
.pwa-install-actions {
  display: flex;
  gap: 8px;
  width: 100%;
}
.pwa-install-btn {
  flex: 1;
  border: 0;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
}
.pwa-install-go { background: #1b7a42; color: #ffffff; }
.pwa-install-go:active { background: #166535; }
.pwa-install-skip { background: #f1f3f4; color: #3c4043; }
html.dark .pwa-install-card {
  background: #1e1f20;
  color: #e8eaed;
  border-color: #333333;
}
html.dark .pwa-install-body span { color: #9aa0a6; }
html.dark .pwa-install-skip { background: #2a2b2d; color: #e8eaed; }
@media (min-width: 520px) {
  .pwa-install-banner { left: auto; width: 400px; }
}

/* Guía de instalación en iPhone/iPad */
.pwa-ios-guide {
  position: fixed;
  inset: 0;
  z-index: 500;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 12px;
  background: rgba(10, 25, 18, 0.55);
  -webkit-backdrop-filter: blur(4px);
  backdrop-filter: blur(4px);
}
.pwa-ios-sheet {
  position: relative;
  width: 100%;
  max-width: 430px;
  background: #ffffff;
  color: #202124;
  border-radius: 22px;
  padding: 22px 20px 18px;
  box-shadow: 0 18px 60px rgba(0, 0, 0, 0.35);
  font-family: inherit;
  animation: pwa-ios-in 280ms cubic-bezier(0.2, 0.9, 0.3, 1);
}
@keyframes pwa-ios-in {
  from { transform: translateY(24px); opacity: 0; }
  to   { transform: none; opacity: 1; }
}
.pwa-ios-sheet h2 {
  margin: 0 0 6px;
  font-size: 19px;
  line-height: 1.25;
  padding-right: 24px;
}
.pwa-ios-lead {
  margin: 0 0 14px;
  font-size: 13.5px;
  color: #5f6368;
  line-height: 1.45;
}
.pwa-ios-close {
  position: absolute;
  top: 8px;
  right: 10px;
  border: 0;
  background: transparent;
  color: #9aa0a6;
  font-size: 26px;
  line-height: 1;
  padding: 6px 8px;
  border-radius: 10px;
  cursor: pointer;
}
.pwa-ios-close:focus {
  outline: 2px solid rgba(27, 122, 66, 0.55);
  outline-offset: 1px;
}
.pwa-ios-steps {
  list-style: none;
  margin: 0 0 16px;
  padding: 0;
  display: grid;
  gap: 12px;
}
.pwa-ios-steps li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 13.5px;
  color: #3c4043;
  line-height: 1.45;
}
.pwa-ios-num {
  flex: 0 0 auto;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #e6f2ea;
  color: #1b7a42;
  font-size: 12.5px;
  font-weight: 700;
  display: grid;
  place-items: center;
}
.pwa-ios-share {
  display: inline-flex;
  width: 17px;
  height: 17px;
  vertical-align: -4px;
  color: #1b7a42;
}
.pwa-ios-share svg { width: 100%; height: 100%; }
.pwa-ios-done,
.pwa-ios-copy-btn { width: 100%; }
.pwa-ios-copy-btn { margin-bottom: 14px; }
html.dark .pwa-ios-sheet { background: #1e1f20; color: #e8eaed; }
html.dark .pwa-ios-lead,
html.dark .pwa-ios-steps li { color: #9aa0a6; }
html.dark .pwa-ios-num { background: #17352a; color: #7ee2a8; }
html.dark .pwa-ios-close { color: #9aa0a6; }
@media (min-width: 520px) {
  .pwa-ios-guide { align-items: center; }
}
</style>
