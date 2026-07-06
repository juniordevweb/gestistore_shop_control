const CACHE_VERSION = 'gestistore-v3';

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

// PWA installable : le SW existe, mais les pages passent toujours par le réseau.
self.addEventListener('fetch', () => {});
