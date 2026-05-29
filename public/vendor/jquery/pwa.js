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

function getPwaProtocolUrl() {
    return 'web+truepharm://open';
}

function getPwaStartUrl() {
    return '/';
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
        openButton.textContent = 'How to open App';
        return;
    }

    openButton.textContent = 'Open installed App';
}

function bindOpenAppButton() {
    if (!openButton) {
        return;
    }

    openButton.onclick = function () {
        clearOpenAppHint();

        if (isIOSDevice()) {
            showOpenAppHint('Open the app from the icon on your iPhone Home Screen. Safari cannot open the installed PWA directly from this button.');
            return;
        }

        if (isAndroidDevice()) {
            showOpenAppHint('Open the app from the icon on your Android Home Screen or app drawer. Chrome may not allow opening the installed PWA directly from this button.');
            return;
        }

        if (isDesktopDevice()) {
            window.location.href = getPwaProtocolUrl();

            setTimeout(function () {
                showOpenAppHint('If the app did not open, use the “Open in app” button in the browser address bar or open it from your desktop app shortcut.');
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

    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }

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

    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }

    clearOpenAppHint();
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

    updateOpenButtonText();
    bindOpenAppButton();

    if (isIOSDevice()) {
        showOpenAppHint('If the app is not installed yet, use Safari: Share → Add to Home Screen. If it is already installed, open it from the Home Screen icon.');
    } else if (isAndroidDevice()) {
        showOpenAppHint('If the app is already installed, open it from the Home Screen or app drawer. If not installed, use the browser menu → Add to Home screen.');
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

            window.location.href = getPwaStartUrl();
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