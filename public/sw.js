// self.addEventListener('install', (event) => {
//     event.waitUntil(
//         caches.keys().then((cacheNames) => {
//             return Promise.all(
//                 cacheNames.map((cacheName) => caches.delete(cacheName))
//             );
//         })
//     );
// });

// self.addEventListener('activate', (event) => {
//     self.registration.unregister().then(() => {
//         console.log('Service Worker unregistered and caches cleared.');
//     });
// });

let PUSH_SAVE_URL = '/push/save_push';

const SW_VERSION = '2026-05-27-3';
const CACHE_PREFIX = 'shop-pwa';

const PAGE_CACHE = `${CACHE_PREFIX}-pages-${SW_VERSION}`;
const STATIC_CACHE = `${CACHE_PREFIX}-static-${SW_VERSION}`;
const IMAGE_CACHE = `${CACHE_PREFIX}-images-${SW_VERSION}`;
const FONT_CACHE = `${CACHE_PREFIX}-fonts-${SW_VERSION}`;

const KEEP_CACHES = [PAGE_CACHE, STATIC_CACHE, IMAGE_CACHE, FONT_CACHE];

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keys = await caches.keys();

        await Promise.all(
            keys.map((key) => {
                const isOldWorkbox = key.startsWith('workbox-precache');
                const shouldDelete = isOldWorkbox || !KEEP_CACHES.includes(key);

                if (shouldDelete) {
                    return caches.delete(key);
                }

                return Promise.resolve();
            })
        );

        await self.clients.claim();
    })());
});

self.addEventListener('message', (event) => {
    if (!event.data) return;

    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
        return;
    }

    if (event.data.type === 'SET_PUSH_SAVE_URL' && event.data.url) {
        PUSH_SAVE_URL = event.data.url;
    }
});

importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.5.4/workbox-sw.js');

if (self.workbox) {
    workbox.setConfig({ debug: false });

    const NEVER_CACHE_PATTERNS = [
        /^\/admin(?:\/|$)/,

        /^\/cart(?:\/|$)/,
        /^\/cart_content(?:\/|$)/,

        /^\/checkout(?:\/|$)/,
        /^\/checkout_content(?:\/|$)/,
        /^\/complete(?:\/|$)/,
        /^\/redirect(?:\/|$)/,

        /^\/lang=.*$/i,
        /^\/curr=.*$/i,
        /^\/design=.*$/i,
        /^\/[^/]+\/lang=.*$/i,
        /^\/[^/]+\/curr=.*$/i,
        /^\/[^/]+\/design=.*$/i,

        /^\/search(?:\/|$)/,
        /^\/search_autocomplete(?:\/|$)/,
        /^\/app\/search\.php(?:\/|$)/,

        /^\/pwa\/pwa_info(?:\/|$)/,
        /^\/push\/save_push(?:\/|$)/,
        /^\/request_call(?:\/|$)/,
        /^\/request_subscribe(?:\/|$)/,
        /^\/request_contact_us(?:\/|$)/,
        /^\/request_affiliate(?:\/|$)/,
        /^\/request_login(?:\/|$)/,
        /^\/verify_profile(?:\/|$)/,
        /^\/check_code(?:\/|$)/,

        /^\/crypto_info(?:\/|$)/,
        /^\/validate_for_crypt(?:\/|$)/,
        /^\/data_for_crypt(?:\/|$)/,
        /^\/local_payment_info(?:\/|$)/,
        /^\/data_for_local_payment(?:\/|$)/,
        /^\/local_payment(?:\/|$)/,
        /^\/validate_for_google(?:\/|$)/,
        /^\/validate_for_sepa(?:\/|$)/,
        /^\/send_google(?:\/|$)/,
        /^\/log_google(?:\/|$)/,
        /^\/paypal(?:\/|$)/,
        /^\/sepa(?:\/|$)/,
        /^\/check_payment(?:\/|$)/,
        /^\/send_checkout_phone_email(?:\/|$)/,
        /^\/zelle_data(?:\/|$)/,
        /^\/zelle(?:\/|$)/,
        /^\/send_payvmc_ids(?:\/|$)/,
        /^\/bonus_card_process(?:\/|$)/,
        /^\/forget_bonuses(?:\/|$)/,
        /^\/gift_card_process(?:\/|$)/
    ];

    workbox.routing.registerRoute(
        ({ request }) => request.method !== 'GET',
        new workbox.strategies.NetworkOnly()
    );

    workbox.routing.registerRoute(
        ({ url, request }) => {
            if (request.method !== 'GET') return false;
            return NEVER_CACHE_PATTERNS.some((re) => re.test(url.pathname));
        },
        new workbox.strategies.NetworkOnly()
    );

    workbox.routing.registerRoute(
        ({ request, url }) => {
            return request.mode === 'navigate' &&
                !NEVER_CACHE_PATTERNS.some((re) => re.test(url.pathname));
        },
        new workbox.strategies.NetworkFirst({
            cacheName: PAGE_CACHE,
            networkTimeoutSeconds: 4,
            plugins: [
                new workbox.cacheableResponse.CacheableResponsePlugin({
                    statuses: [200],
                }),
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 100,
                    maxAgeSeconds: 12 * 60 * 60,
                    purgeOnQuotaError: true,
                }),
            ],
        })
    );

    workbox.routing.registerRoute(
        ({ request, url }) =>
            request.destination === 'script' ||
            request.destination === 'style' ||
            request.destination === 'worker' ||
            /\.(?:js|css|ico)$/i.test(url.pathname),
        new workbox.strategies.StaleWhileRevalidate({
            cacheName: STATIC_CACHE,
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 150,
                    maxAgeSeconds: 7 * 24 * 60 * 60,
                    purgeOnQuotaError: true,
                }),
            ],
        })
    );

    workbox.routing.registerRoute(
        ({ request, url }) =>
            url.pathname.startsWith('/set_images/') ||
            request.destination === 'image' ||
            /\.(?:png|webp|svg|jpg|jpeg|gif|avif)$/i.test(url.pathname),
        new workbox.strategies.CacheFirst({
            cacheName: IMAGE_CACHE,
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 1000,
                    maxAgeSeconds: 7 * 24 * 60 * 60,
                    purgeOnQuotaError: true,
                }),
            ],
        })
    );

    workbox.routing.registerRoute(
        ({ request, url }) =>
            request.destination === 'font' ||
            /\.(?:woff|woff2|ttf|otf)$/i.test(url.pathname),
        new workbox.strategies.CacheFirst({
            cacheName: FONT_CACHE,
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 50,
                    maxAgeSeconds: 30 * 24 * 60 * 60,
                    purgeOnQuotaError: true,
                }),
            ],
        })
    );
}

self.addEventListener('pushsubscriptionchange', (event) => {
    event.waitUntil(Promise.resolve());
});

self.addEventListener('push', (event) => {
    let notification = {};

    try {
        notification = event.data ? event.data.json() : {};
    } catch (e) {
        notification = {
            title: 'Notification',
            body: event.data ? event.data.text() : '',
        };
    }

    const title = notification.title || 'Notification';

    event.waitUntil(
        self.registration.showNotification(title, {
            body: notification.body || '',
            icon: notification.img || '/android-chrome-192x192.png',
            data: {
                notifURL: notification.url || '/',
            },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = new URL(
        event.notification?.data?.notifURL || '/',
        self.location.origin
    ).href;

    event.waitUntil((async () => {
        const clientsList = await clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        });

        for (const client of clientsList) {
            try {
                if (client.url === targetUrl) {
                    return await client.focus();
                }

                const navigatedClient = await client.navigate(targetUrl);

                if (navigatedClient) {
                    return await navigatedClient.focus();
                }
            } catch (e) {
                // если конкретный client уже исчез — пробуем дальше
            }
        }

        return clients.openWindow(targetUrl);
    })());
});