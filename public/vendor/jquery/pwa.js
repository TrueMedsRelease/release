let deferredInstallPrompt = null;

const installButton = document.getElementById('pwa-install-button');
const installBlock = document.getElementById('pwa-install-block');
const installedBlock = document.getElementById('pwa-installed-block');
const openButton = document.getElementById('pwa-open-button');
const statusBlock = document.getElementById('pwa-install-status');

function getCookieValue(name) {
    const matches = document.cookie.match(
        new RegExp('(?:^|; )' + name.replace(/([$?*|{}\[\]\\\/+^])/g, '\\$1') + '=([^;]*)')
    );

    return matches ? decodeURIComponent(matches[1]) : '';
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

function showOpenAppHint(text) {
    if (statusBlock) {
        statusBlock.style.display = 'block';
        statusBlock.textContent = text;
    }
}

function bindOpenAppButton() {
    if (!openButton) {
        return;
    }

    // openButton.onclick = function () {
    //     window.location.href = 'web+truepharm://open';
    // };
    openButton.onclick = function () {
        if (isIOSDevice()) {
            showOpenAppHint('Open the app from the icon on your Home Screen.');
            return;
        }

        if (isAndroidDevice()) {
            showOpenAppHint('Open the app from the icon on your Home Screen or app drawer.');
            return;
        }

        window.location.href = 'web+truepharm://open';
        return;

        showOpenAppHint('Open the app from your desktop app shortcut.');
    };
}

function showInstalledState() {
    if (installBlock) {
        installBlock.style.display = 'none';
    }

    if (installedBlock) {
        installedBlock.style.display = 'block';
    }

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

    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }
}

function showUnavailableState() {
    if (installBlock) {
        installBlock.style.display = 'none';
    }

    if (installButton) {
        installButton.disabled = true;
    }

    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }

    if (installedBlock) {
        installedBlock.style.display = 'block';
    }

    bindOpenAppButton();
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
    } catch (e) {
        result.user_push = getCookieValue('user_push') || '';
        return result;
    }
}

async function sendPwaInstallAccepted() {
    if (typeof routePwaInstallEvent === 'undefined' || !routePwaInstallEvent) {
        return;
    }

    const formData = new FormData();

    const csrf = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrf ? csrf.getAttribute('content') : '';

    if (csrfToken) {
        formData.append('_token', csrfToken);
    }

    const pushData = await getCurrentPushDataForPwaInstall();

    formData.append('event', 'native_install_accepted');
    formData.append('shop_url', location.host);
    formData.append('page_url', location.href);

    formData.append('user_push', pushData.user_push);
    formData.append('push_info', pushData.push_info);

    await fetch(routePwaInstallEvent, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: csrfToken ? {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        } : {}
    });
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();

    deferredInstallPrompt = event;

    showInstallState();
});

window.addEventListener('appinstalled', () => {
    document.cookie = 'pwa_installed=true; path=/; max-age=31536000';

    deferredInstallPrompt = null;

    showInstalledState();
});

if (installButton) {
    installButton.addEventListener('click', async () => {
        if (!deferredInstallPrompt) {
            showUnavailableState();
            return;
        }

        deferredInstallPrompt.prompt();

        const choice = await deferredInstallPrompt.userChoice;

        if (choice.outcome === 'accepted') {
            await sendPwaInstallAccepted();

            window.location.href = '/';
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