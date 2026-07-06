<script>
(function () {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', function () {
        navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
            .then(function (registration) {
                console.log('[PWA] Service Worker enregistre:', registration.scope);

                registration.addEventListener('updatefound', function () {
                    var worker = registration.installing;
                    if (!worker) {
                        return;
                    }
                    worker.addEventListener('statechange', function () {
                        if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                            worker.postMessage({ type: 'SKIP_WAITING' });
                        }
                    });
                });
            })
            .catch(function (error) {
                console.error('[PWA] Erreur Service Worker:', error);
            });

        var refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', function () {
            if (refreshing) {
                return;
            }
            refreshing = true;
            window.location.reload();
        });
    });
})();
</script>
