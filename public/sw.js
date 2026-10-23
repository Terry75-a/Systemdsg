/*
 * DSG PWA — Service Worker ligero.
 * - Navegaciones públicas: red primero, caché como respaldo (offline básico).
 *   Las páginas autenticadas NUNCA se cachean (evita servir HTML de otro usuario).
 * - Estáticos (css/js/img/fonts): caché con revalidación en segundo plano.
 * - APIs / SSE / JSON / push / evidencias: siempre red.
 */
const CACHE = 'dsg-pwa-v3';
const ESTATICOS = /\.(?:css|js|png|jpg|jpeg|gif|svg|webp|woff2?|ttf|ico)$/i;
const PUBLICAS = /^\/(login-verde|contacto-demo)?\/?$/;

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE).then((cache) =>
            cache.addAll(['./', './login-verde']).catch(() => undefined)
        )
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;

    // Datos vivos: notificaciones, estado, SSE, push, evidencias → solo red
    if (/\/(notificaciones|estado-actual|realtime|bio-session|push|evidencia|cron)\b/.test(url.pathname)) return;
    if ((req.headers.get('accept') || '').includes('text/event-stream')) return;

    // Navegaciones: sólo las públicas se cachean
    if (req.mode === 'navigate') {
        if (! PUBLICAS.test(url.pathname)) return;
        event.respondWith(
            fetch(req)
                .then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => undefined);
                    return res;
                })
                .catch(() =>
                    caches.match(req).then((hit) => hit || caches.match('./login-verde'))
                )
        );
        return;
    }

    // Estáticos: caché al instante + actualizar en segundo plano
    if (ESTATICOS.test(url.pathname)) {
        event.respondWith(
            caches.match(req).then((cached) => {
                const network = fetch(req)
                    .then((res) => {
                        if (res.ok) {
                            const copy = res.clone();
                            caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => undefined);
                        }
                        return res;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});

// ── Web Push ────────────────────────────────────────────────────
self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { body: event.data ? event.data.text() : '' };
    }
    const title = data.title || 'DSG';
    event.waitUntil(
        self.registration.showNotification(title, {
            body: data.body || '',
            icon: './images/pwa/icon-192.png',
            badge: './images/pwa/icon-192.png',
            tag: data.tag || 'dsg',
            data: { url: data.url || './mi-panel' }
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const destino = (event.notification.data && event.notification.data.url) || './mi-panel';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((lista) => {
            for (const c of lista) {
                if ('focus' in c) {
                    c.navigate(destino);
                    return c.focus();
                }
            }
            return clients.openWindow(destino);
        })
    );
});
