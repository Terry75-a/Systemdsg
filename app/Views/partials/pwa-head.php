<?php
/**
 * Cabecera PWA: manifest, iconos, theme-color (compatible iOS/Android/Chrome).
 * Incluye la captura temprana del instalador nativo (Chrome/Edge): si el evento
 * beforeinstallprompt llega antes que el resto de scripts, no se pierde.
 */
?>
<script>
window.__pwaInstallPrompt = null;
window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    window.__pwaInstallPrompt = e;
    window.dispatchEvent(new CustomEvent('pwa-install-available'));
});
window.addEventListener('appinstalled', function () {
    window.__pwaInstallPrompt = null;
});
</script>
<link rel="manifest" href="<?= base_url('manifest.json') ?>">
<meta name="theme-color" content="#1c5b40">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Asisten">
<link rel="apple-touch-icon" href="<?= base_url('images/asisten/apple-touch-icon.png') ?>">
<link rel="icon" href="<?= base_url('images/asisten/favicon.ico') ?>" sizes="48x48">
<link rel="icon" type="image/svg+xml" href="<?= base_url('images/asisten/favicon.svg') ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('images/pwa/icon-192.png') ?>">
