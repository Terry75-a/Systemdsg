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

    var KEY = 'pwa_install_dismissed';
    var COOLDOWN = 14 * 24 * 60 * 60 * 1000;

    var deferred = null;
    var banner = null;
    var isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

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

    function showGenericHelpUI() {
        var body = banner && banner.querySelector('.pwa-install-body');
        if (!body) return;
        body.innerHTML =
            '<strong>Instalar app DSG</strong>' +
            '<span>Abre el menú del navegador <b>⋮</b> y elige <b>«Instalar aplicación»</b> o <b>«Agregar a pantalla de inicio»</b>.</span>' +
            '<div class="pwa-install-actions">' +
                '<button type="button" class="pwa-install-btn pwa-install-skip">Entendido</button>' +
            '</div>';
        body.querySelector('.pwa-install-skip').addEventListener('click', function () {
            hideBanner();
            saveDismiss(false);
        });
    }

    function showInstallPromptUI() {
        showBanner(
            '<strong>Instalar app DSG</strong>' +
            '<span>Acceso rápido desde tu pantalla de inicio.</span>' +
            '<div class="pwa-install-actions">' +
                '<button type="button" class="pwa-install-btn pwa-install-go">Instalar</button>' +
                '<button type="button" class="pwa-install-btn pwa-install-skip">Ahora no</button>' +
            '</div>'
        );
        if (!banner) return;
        banner.querySelector('.pwa-install-go').addEventListener('click', function () {
            var d = deferred;
            if (!d || typeof d.prompt !== 'function') {
                showGenericHelpUI();
                return;
            }
            try {
                d.prompt();
                hideBanner();
                d.userChoice.then(function (res) {
                    if (!res || res.outcome !== 'accepted') saveDismiss(false);
                    deferred = null;
                }).catch(function () {});
            } catch (err) {
                showGenericHelpUI();
            }
        });
        banner.querySelector('.pwa-install-skip').addEventListener('click', function () {
            hideBanner();
            saveDismiss(false);
        });
    }

    function showIOSHelpUI() {
        showBanner(
            '<strong>Instalar app DSG</strong>' +
            '<span>Toca <b>Compartir</b> <span class="pwa-install-ios-icon">&#x2191;</span> y luego <b>«Agregar a pantalla de inicio»</b>.</span>' +
            '<div class="pwa-install-actions">' +
                '<button type="button" class="pwa-install-btn pwa-install-skip">Entendido</button>' +
            '</div>'
        );
        if (!banner) return;
        banner.querySelector('.pwa-install-skip').addEventListener('click', function () {
            hideBanner();
            saveDismiss(false);
        });
    }

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferred = e;
        if (!isIOS && !isDismissed()) showInstallPromptUI();
    });

    window.addEventListener('appinstalled', function () {
        saveDismiss(true);
        hideBanner();
        deferred = null;
    });

    if (isIOS) {
        setTimeout(function () {
            if (!isStandalone && !banner && !isDismissed()) showIOSHelpUI();
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
</style>
