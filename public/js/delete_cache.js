const DEBUG = true;

// Меняем версию файла, когда меняем service worker
const serviceWorkerVer = "/sw.js?v=1";

var url     = window.location.origin,
    fullUrl = url + serviceWorkerVer;
var flag    = false;

+function installServiceWorker() {
    if ("serviceWorker" in navigator) {
        window.addEventListener('load', () => {
            removeServiceWorker(navigator.serviceWorker);

            removeCache('workbox-precache-v2-' + url + '/');
            removeCache('workbox-precache-' + url + '/');
            removeCache('workbox-precache-' + url + '/-temp');
            removeCache('workbox-precache');
            removeCache('images_cache');
            removeCache('descriptions_cache');
            removeCache('design_cache');
            removeCache('app_cache');
            removeCache('assets');
            removeCache('main');
            removeCache('other');
            removeCache('static-resources');
            removeCache('image_cache');
            removeCache('product_images');
        });
    }
}();


// Удаления service worker
function removeServiceWorker(navigatorServiceWorker) {
    navigatorServiceWorker.getRegistrations()
        .then(function (registrations) {
                for (var registration of registrations) {
                    registration.unregister();
                    // DEBUG && console.log("[SW] Предыдущая версия service worker была успешно удалена");
                    // DEBUG && console.log("[SW] ! Для установки новой версии перезагрузите страницу");
                }
            }
        );
}

// Очистка кэша
function removeCache(cache) {
    caches.delete(cache).then(function (boolean) {
        // DEBUG && console.log("[SW] Кэш " + cache + " очищен");
    });
}