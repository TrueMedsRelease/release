// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('/sw.js').then((registration) => {
//         console.log('Temporary Service Worker registered:', registration);
//     });
// }

const DEBUG = true;
const SW_VERSION = '2026-04-07-8';
const SW_URL = `/sw.js?v=${SW_VERSION}`;

let refreshing = false;
let installPromptEvent = null;
let periodicSyncRegistered = false;

(function initServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        DEBUG && console.log('[SW] serviceWorker is not supported');
        return;
    }

    window.addEventListener('load', async () => {
        try {
            const reg = await navigator.serviceWorker.register(SW_URL, { scope: '/' });

            DEBUG && console.log('[SW] registered:', reg.scope);

            if (navigator.serviceWorker.controller) {
                DEBUG && console.log('[SW] current controller:', navigator.serviceWorker.controller.scriptURL);
                DEBUG && console.log('[SW] expected url:', new URL(SW_URL, window.location.origin).href);
            }

            if (reg.waiting) {
                DEBUG && console.log('[SW] waiting worker found, activating');
                reg.waiting.postMessage({ type: 'SKIP_WAITING' });
            }

            reg.addEventListener('updatefound', () => {
                const newWorker = reg.installing;
                if (!newWorker) return;

                DEBUG && console.log('[SW] update found');

                newWorker.addEventListener('statechange', () => {
                    DEBUG && console.log('[SW] installing worker state:', newWorker.state);

                    if (
                        newWorker.state === 'installed' &&
                        navigator.serviceWorker.controller
                    ) {
                        DEBUG && console.log('[SW] new worker installed, activate immediately');
                        newWorker.postMessage({ type: 'SKIP_WAITING' });
                    }
                });
            });

            const hadControllerOnLoad = !!navigator.serviceWorker.controller;

            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!hadControllerOnLoad) {
                    DEBUG && console.log('[SW] first install, skip reload');
                    return;
                }

                if (refreshing) return;
                refreshing = true;

                DEBUG && console.log('[SW] controller changed after update, reload page');
                window.location.reload();
            });

            setInterval(() => {
                reg.update().catch((err) => {
                    DEBUG && console.error('[SW] update error:', err);
                });
            }, 60 * 1000);

        } catch (err) {
            DEBUG && console.error('[SW] registration failed:', err);
        }
    });
})();

function base64UrlToUint8Array(base64String) {
    const normalizedInput = String(base64String || '').trim();
    const padding = '='.repeat((4 - (normalizedInput.length % 4)) % 4);
    const base64 = (normalizedInput + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

function getApplicationServerKey() {
    const vapidInput = document.getElementById('vapid_pub');

    if (!vapidInput || !vapidInput.value) {
        throw new Error('vapid_pub not found or empty');
    }

    const rawValue = vapidInput.value.trim();

    try {
        const directKey = base64UrlToUint8Array(rawValue);
        if (directKey.length === 65) {
            DEBUG && console.log('[PUSH] VAPID key parsed as direct base64url key');
            return directKey;
        }
    } catch (e) {
        DEBUG && console.log('[PUSH] direct VAPID parse failed');
    }

    try {
        const unwrapped = atob(rawValue).trim();
        const decodedKey = base64UrlToUint8Array(unwrapped);
        if (decodedKey.length === 65) {
            DEBUG && console.log('[PUSH] VAPID key parsed as wrapped base64 key');
            return decodedKey;
        }
    } catch (e) {
        DEBUG && console.log('[PUSH] wrapped VAPID parse failed');
    }

    throw new Error('Invalid VAPID public key format');
}

function formatDateTime(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

async function enableNotif() {
    try {
        const vapidInput = document.getElementById('vapid_pub');
        if (!vapidInput || !vapidInput.value) {
            DEBUG && console.log('[PUSH] vapid_pub not found or empty');
            return null;
        }

        if (!('Notification' in window)) {
            DEBUG && console.log('[PUSH] Notification API unsupported');
            return null;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            DEBUG && console.log('[PUSH] permission denied');
            return null;
        }

        const reg = await navigator.serviceWorker.ready;

        let subscription = await reg.pushManager.getSubscription();

        if (!subscription) {
            subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: getApplicationServerKey()
            });
        }

        const now = new Date();
        const timeZone = -now.getTimezoneOffset() / 60;
        const shopUrl = location.host;
        const pushInfo = JSON.stringify(subscription);
        const info = JSON.parse(pushInfo);

        const cookieDate = new Date();
        cookieDate.setDate(cookieDate.getDate() + 900);

        document.cookie =
            'user_push=' + encodeURIComponent(info.keys.auth) +
            '; path=/; expires=' + cookieDate.toUTCString();

        const langSession = document.getElementById('lang_session');
        const langSelected = document.querySelector('#lang_select option:checked');
        const currSelected = document.querySelector('#curr_select option:checked');

        const lang =
            (langSession && langSession.value) ||
            (langSelected && (langSelected.getAttribute('data-code') || '').trim()) ||
            '';

        const curr =
            (currSelected && (currSelected.textContent || '').trim()) ||
            '';

        const payload = {
            shop_url: shopUrl,
            lang: lang,
            curr: curr,
            push_info: pushInfo,
            date: formatDateTime(now),
            time_zone: timeZone,
            customer_id: 0,
        };

        if (window.jQuery && typeof window.jQuery.ajax === 'function') {
            await new Promise((resolve, reject) => {
                window.jQuery.ajax({
                    url: routeSavePush,
                    type: 'POST',
                    data: payload,
                    dataType: 'json',
                    success: function (res) {
                        if (res && res.status === 'error') {
                            DEBUG && console.error('[PUSH] save_push returned error:', res.text);
                        }
                        resolve(res);
                    },
                    error: function (xhr) {
                        DEBUG && console.error('[PUSH] save_push ajax error:', xhr);
                        reject(xhr);
                    }
                });
            });
        } else {
            const formData = new FormData();
            Object.keys(payload).forEach((key) => {
                formData.append(key, payload[key]);
            });

            const response = await fetch(routeSavePush, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`save_push failed with status ${response.status}`);
            }
        }

        DEBUG && console.log('[PUSH] subscribed successfully');

        return subscription;
    } catch (err) {
        DEBUG && console.error('[PUSH] enableNotif error:', err);
        return null;
    }
}

async function registerPeriodicSync() {
    const reg = await navigator.serviceWorker.ready;

    if (!reg.periodicSync) {
        DEBUG && console.log('[SW] periodicSync unsupported');
        return;
    }

    await reg.periodicSync.register('get-daily-news', {
        minInterval: 24 * 60 * 60 * 1000
    });
}

async function periodicReg() {
    try {
        if (!('permissions' in navigator)) return;

        const status = await navigator.permissions.query({
            name: 'periodic-background-sync'
        });

        if (status.state === 'granted' && !periodicSyncRegistered) {
            periodicSyncRegistered = true;
            await registerPeriodicSync();
            DEBUG && console.log('[SW] periodic-background-sync registered');
        }
    } catch (err) {
        DEBUG && console.log('[SW] periodic sync unsupported or denied', err);
    }
}

async function installed() {
    let pwa = false;

    try {
        if ('getInstalledRelatedApps' in navigator) {
            const relatedApps = await navigator.getInstalledRelatedApps();

            if (relatedApps && relatedApps.length) {
                for (const app of relatedApps) {
                    document.cookie =
                        'app_platform=' + encodeURIComponent(app.platform || '') +
                        '; path=/';

                    const stat = document.getElementById('stat');
                    if (stat && stat.src) {
                        stat.src += `&app_platform=${encodeURIComponent(app.platform || '')}&app_url=${encodeURIComponent(app.url || '')}`;
                    }

                    pwa = true;
                }
            }
        }
    } catch (err) {
        DEBUG && console.log('[PWA] installed() error', err);
    }

    return pwa;
}

function beforeInstall() {
    const btnInstall = document.querySelector('#install__button');
    const installBlock = document.querySelector('#install');

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        installPromptEvent = event;
        DEBUG && console.log('[PWA] beforeinstallprompt fired');
    });

    if (btnInstall) {
        btnInstall.addEventListener('click', async () => {
            if (!installPromptEvent) {
                DEBUG && console.log('[PWA] installPromptEvent is empty');
                return;
            }

            installPromptEvent.prompt();

            const choice = await installPromptEvent.userChoice;

            if (choice.outcome === 'accepted') {
                DEBUG && console.log('[PWA] user accepted install');
                if (installBlock) {
                    installBlock.style.display = 'none';
                }
            } else {
                DEBUG && console.log('[PWA] user dismissed install');
            }

            installPromptEvent = null;
        });
    }

    window.addEventListener('appinstalled', () => {
        installPromptEvent = null;
        document.cookie = 'pwa_installed=true; path=/; max-age=31536000';
        DEBUG && console.log('[PWA] app installed');
    });
}

async function main() {
    await periodicReg();

    const pwaInstalled = await installed();
    if (!pwaInstalled) {
        beforeInstall();
    }
}

main();