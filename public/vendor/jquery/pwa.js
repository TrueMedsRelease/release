let deferredInstallPrompt = null;
let pwaInstallEventSent = false;

const installButton = document.getElementById('pwa-install-button');
const installBlock = document.getElementById('pwa-install-block');
const installedBlock = document.getElementById('pwa-installed-block');
const openButton = document.getElementById('pwa-open-button');
const statusBlock = document.getElementById('pwa-install-status');

function pwaText(key, fallback) {
    if (
        window.PWA_I18N &&
        Object.prototype.hasOwnProperty.call(window.PWA_I18N, key) &&
        window.PWA_I18N[key]
    ) {
        return window.PWA_I18N[key];
    }

    return fallback || key;
}

function getCookieValue(name) {
    const matches = document.cookie.match(
        new RegExp('(?:^|; )' + name.replace(/([$?*|{}\[\]\\\/+^])/g, '\\$1') + '=([^;]*)')
    );

    return matches ? decodeURIComponent(matches[1]) : '';
}

function setCookie(name, value, maxAge) {
    document.cookie =
        name + '=' + encodeURIComponent(value) +
        '; path=/' +
        '; max-age=' + maxAge +
        '; SameSite=Lax';
}

function isPwaMode() {
    return window.matchMedia('(display-mode: standalone)').matches ||
        window.matchMedia('(display-mode: fullscreen)').matches ||
        window.navigator.standalone === true;
}

function isIOSDevice() {
    return /iphone|ipad|ipod/i.test(navigator.userAgent) ||
        (
            navigator.platform === 'MacIntel' &&
            navigator.maxTouchPoints > 1
        );
}

function isAndroidDevice() {
    return /android/i.test(navigator.userAgent);
}

function isDesktopDevice() {
    return !isIOSDevice() && !isAndroidDevice();
}

function getPwaProtocolUrl() {
    if (typeof pwaProtocolUrl !== 'undefined' && pwaProtocolUrl) {
        return pwaProtocolUrl;
    }

    return 'web+truepharm://open';
}

function getPwaStartUrl() {
    if (typeof pwaStartUrl !== 'undefined' && pwaStartUrl) {
        return pwaStartUrl;
    }

    return '/';
}

function getCsrfToken() {
    const csrf = document.querySelector('meta[name="csrf-token"]');
    return csrf ? csrf.getAttribute('content') : '';
}

function showStatus(text) {
    if (statusBlock) {
        statusBlock.style.display = 'block';
        statusBlock.textContent = text;
    }
}

function hideStatus() {
    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }
}

function getOrCreateOpenHintBlock() {
    if (!installedBlock) {
        return null;
    }

    let hintBlock = document.getElementById('pwa-open-hint');

    if (!hintBlock) {
        hintBlock = document.createElement('div');
        hintBlock.id = 'pwa-open-hint';
        hintBlock.style.marginTop = '12px';
        hintBlock.style.fontSize = '14px';
        hintBlock.style.lineHeight = '1.5';
        hintBlock.style.color = '#065f46';

        installedBlock.appendChild(hintBlock);
    }

    return hintBlock;
}

function showOpenAppHint(text) {
    const hintBlock = getOrCreateOpenHintBlock();

    if (hintBlock) {
        hintBlock.style.display = 'block';
        hintBlock.textContent = text;
    }
}

function clearOpenAppHint() {
    const hintBlock = document.getElementById('pwa-open-hint');

    if (hintBlock) {
        hintBlock.style.display = 'none';
        hintBlock.textContent = '';
    }
}

function updateOpenButtonText() {
    if (!openButton) {
        return;
    }

    if (isIOSDevice() || isAndroidDevice()) {
        openButton.textContent = pwaText('how_to_open_app', 'How to open App');
        return;
    }

    openButton.textContent = pwaText('open_installed_app', 'Open installed App');
}

function bindOpenAppButton() {
    if (!openButton) {
        return;
    }

    openButton.onclick = function () {
        clearOpenAppHint();

        if (isIOSDevice()) {
            showOpenAppHint(
                pwaText(
                    'ios_open_hint',
                    'Open the app from the icon on your iPhone Home Screen. Safari cannot open the installed PWA directly from this button.'
                )
            );
            return;
        }

        if (isAndroidDevice()) {
            showOpenAppHint(
                pwaText(
                    'android_open_hint',
                    'Open the app from the icon on your Android Home Screen or app drawer. Chrome may not allow opening the installed PWA directly from this button.'
                )
            );
            return;
        }

        if (isDesktopDevice()) {
            window.location.href = getPwaProtocolUrl();

            setTimeout(function () {
                showOpenAppHint(
                    pwaText(
                        'desktop_open_hint',
                        'If the app did not open, use the “Open in app” button in the browser address bar or open it from your desktop app shortcut.'
                    )
                );
            }, 1200);
        }
    };
}

function showInstalledState() {
    if (installBlock) {
        installBlock.style.display = 'none';
    }

    if (installButton) {
        installButton.disabled = true;
    }

    hideStatus();

    if (installedBlock) {
        installedBlock.style.display = 'block';
    }

    updateOpenButtonText();
    bindOpenAppButton();
}

function showInstallState() {
    if (installedBlock) {
        installedBlock.style.display = 'none';
    }

    if (installBlock) {
        installBlock.style.display = 'block';
    }

    if (installButton) {
        installButton.disabled = false;
    }

    hideStatus();
    clearOpenAppHint();
}

function showUnavailableState() {
    if (installBlock) {
        installBlock.style.display = 'none';
    }

    if (installButton) {
        installButton.disabled = true;
    }

    hideStatus();

    if (installedBlock) {
        installedBlock.style.display = 'block';
    }

    updateOpenButtonText();
    bindOpenAppButton();

    if (isIOSDevice()) {
        showOpenAppHint(
            pwaText(
                'ios_unavailable_hint',
                'If the app is not installed yet, use Safari: Share → Add to Home Screen. If it is already installed, open it from the Home Screen icon.'
            )
        );
    } else if (isAndroidDevice()) {
        showOpenAppHint(
            pwaText(
                'android_unavailable_hint',
                'If the app is already installed, open it from the Home Screen or app drawer. If not installed, use the browser menu → Add to Home screen.'
            )
        );
    }
}

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
            return directKey;
        }
    } catch (e) {}

    try {
        const unwrapped = atob(rawValue).trim();
        const decodedKey = base64UrlToUint8Array(unwrapped);
        if (decodedKey.length === 65) {
            return decodedKey;
        }
    } catch (e) {}

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

function getSelectedLang() {
    const langSession = document.getElementById('lang_session');
    const langSelected = document.querySelector('#lang_select option:checked');

    return (langSession && langSession.value) ||
        (langSelected && (langSelected.getAttribute('data-code') || '').trim()) ||
        getCookieValue('lang') ||
        document.documentElement.lang ||
        'en';
}

function getSelectedCurrency() {
    const currSession = document.getElementById('currency_session');
    const currSelected = document.querySelector('#curr_select option:checked');

    return (currSelected && (currSelected.textContent || '').trim()) ||
        getCookieValue('curr') ||
        'USD';
}

function getCustomerId() {
    const customerInput = document.getElementById('customer_id');

    if (customerInput && customerInput.value) {
        return customerInput.value;
    }

    return 0;
}

function getPushDataFromSubscription(subscription) {
    const result = {
        user_push: '',
        push_info: ''
    };

    if (!subscription) {
        result.user_push = getCookieValue('user_push') || '';
        return result;
    }

    const pushInfo = JSON.stringify(subscription);
    const parsed = JSON.parse(pushInfo);

    result.push_info = pushInfo;

    if (parsed && parsed.keys && parsed.keys.auth) {
        result.user_push = parsed.keys.auth;
    } else {
        result.user_push = getCookieValue('user_push') || '';
    }

    return result;
}

async function savePushSubscriptionToServices(subscription, source) {
    if (typeof routeSavePush === 'undefined' || !routeSavePush) {
        return {
            status: 'error',
            text: 'routeSavePush is not defined'
        };
    }

    const pushData = getPushDataFromSubscription(subscription);

    if (pushData.user_push) {
        setCookie('user_push', pushData.user_push, 900 * 24 * 60 * 60);
    }

    const now = new Date();
    const payload = {
        method: 'save',
        shop_url: location.host,
        lang: getSelectedLang(),
        curr: getSelectedCurrency(),
        push_info: pushData.push_info,
        date: formatDateTime(now),
        time_zone: -now.getTimezoneOffset() / 60,
        customer_id: getCustomerId(),
        pwa_mode: 1,
        source: source || 'pwa_install',
        debug_reason: 'pwa_install_create_push'
    };

    const csrfToken = getCsrfToken();
    const formData = new FormData();

    if (csrfToken) {
        formData.append('_token', csrfToken);
    }

    Object.keys(payload).forEach(function (key) {
        formData.append(key, payload[key]);
    });

    const response = await fetch(routeSavePush, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: csrfToken ? {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'X-PWA-Mode': '1'
        } : {
            'X-PWA-Mode': '1'
        }
    });

    return await response.json().catch(function () {
        return {
            status: 'error',
            text: 'Invalid JSON from save push route'
        };
    });
}

async function createPushSubscriptionAfterPwaInstall() {
    const result = {
        push_id: '',
        user_push: '',
        push_info: '',
        push_status: 'not_started',
        push_save_result: null
    };

    try {
        if (!('serviceWorker' in navigator)) {
            result.push_status = 'service_worker_unsupported';
            return result;
        }

        if (!('PushManager' in window)) {
            result.push_status = 'push_manager_unsupported';
            return result;
        }

        if (!('Notification' in window)) {
            result.push_status = 'notification_unsupported';
            return result;
        }

        let permission = Notification.permission;

        if (permission === 'default') {
            permission = await Notification.requestPermission();
        }

        if (permission !== 'granted') {
            result.push_status = 'notification_' + permission;
            return result;
        }

        const reg = await navigator.serviceWorker.ready;

        let subscription = await reg.pushManager.getSubscription();

        if (!subscription) {
            subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: getApplicationServerKey()
            });
        }

        const pushData = getPushDataFromSubscription(subscription);

        result.user_push = pushData.user_push;
        result.push_info = pushData.push_info;

        const saveResult = await savePushSubscriptionToServices(subscription, 'pwa_install_accepted');

        result.push_save_result = saveResult;
        result.push_status = saveResult && saveResult.status
            ? saveResult.status
            : 'saved';

        if (saveResult && saveResult.push_id) {
            result.push_id = saveResult.push_id;
        }

        return result;
    } catch (e) {
        result.push_status = 'error';
        result.push_error = e && e.message ? e.message : String(e);
        return result;
    }
}

async function getCurrentPushDataForPwaInstall() {
    const result = {
        user_push: '',
        push_info: ''
    };

    try {
        if (!('serviceWorker' in navigator)) {
            return result;
        }

        if (!('PushManager' in window)) {
            return result;
        }

        const reg = await navigator.serviceWorker.ready;
        const subscription = await reg.pushManager.getSubscription();

        return getPushDataFromSubscription(subscription);
    } catch (e) {
        result.user_push = getCookieValue('user_push') || '';
        return result;
    }
}

async function sendPwaInstallAccepted(pushResult) {
    if (pwaInstallEventSent) {
        return;
    }

    pwaInstallEventSent = true;

    if (typeof routePwaInstallEvent === 'undefined' || !routePwaInstallEvent) {
        return;
    }

    pushResult = pushResult || {};

    const fallbackPushData = await getCurrentPushDataForPwaInstall();

    const userPush = pushResult.user_push || fallbackPushData.user_push || '';
    const pushInfo = pushResult.push_info || fallbackPushData.push_info || '';
    const pushId = pushResult.push_id || '';

    const formData = new FormData();

    const csrfToken = getCsrfToken();

    if (csrfToken) {
        formData.append('_token', csrfToken);
    }

    formData.append('event', 'native_install_accepted');
    formData.append('shop_url', location.host);
    formData.append('page_url', location.href);

    formData.append('push_id', pushId);
    formData.append('user_push', userPush);
    formData.append('push_info', pushInfo);
    formData.append('push_status', pushResult.push_status || '');
    formData.append('push_error', pushResult.push_error || '');

    await fetch(routePwaInstallEvent, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: csrfToken ? {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'X-PWA-Mode': '1'
        } : {
            'X-PWA-Mode': '1'
        }
    });
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();

    deferredInstallPrompt = event;

    showInstallState();
});

window.addEventListener('appinstalled', async () => {
    setCookie('pwa_installed', 'true', 31536000);

    deferredInstallPrompt = null;

    showInstalledState();

    if (!pwaInstallEventSent) {
        let pushResult = {
            push_id: '',
            user_push: '',
            push_info: '',
            push_status: 'appinstalled_without_prompt'
        };

        if ('Notification' in window && Notification.permission === 'granted') {
            pushResult = await createPushSubscriptionAfterPwaInstall();
        }

        await sendPwaInstallAccepted(pushResult);
    }
});

if (installButton) {
    installButton.addEventListener('click', async () => {
        if (!deferredInstallPrompt) {
            showUnavailableState();
            return;
        }

        installButton.disabled = true;
        showStatus(pwaText('installing_app', 'Installing app...'));

        deferredInstallPrompt.prompt();

        const choice = await deferredInstallPrompt.userChoice;

        if (choice.outcome === 'accepted') {
            setCookie('pwa_installed', 'true', 31536000);

            showStatus(pwaText('creating_push', 'Creating push subscription...'));

            const pushResult = await createPushSubscriptionAfterPwaInstall();

            showStatus(pwaText('saving_install', 'Saving install information...'));

            await sendPwaInstallAccepted(pushResult);

            window.location.href = getPwaStartUrl();
        } else {
            installButton.disabled = false;
            showStatus(pwaText('install_cancelled', 'Installation was cancelled.'));
        }

        deferredInstallPrompt = null;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const installedByCookie = getCookieValue('pwa_installed') === 'true';

    if (isPwaMode()) {
        showInstalledState();
        return;
    }

    if (installedByCookie) {
        showInstalledState();
        return;
    }

    setTimeout(() => {
        if (!deferredInstallPrompt) {
            showUnavailableState();
        }
    }, 1500);
});