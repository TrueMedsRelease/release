self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => caches.delete(cacheName))
            );
        })
    );
});

self.addEventListener('activate', (event) => {
    self.registration.unregister().then(() => {
        console.log('Service Worker unregistered and caches cleared.');
    });
});