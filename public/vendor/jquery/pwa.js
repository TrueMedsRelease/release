let deferredInstallPrompt = null;
let pwaInstallEventSent = false;

const installButton = document.getElementById('pwa-install-button');
const installBlock = document.getElementById('pwa-install-block');
const installedBlock = document.getElementById('pwa-installed-block');
const openButton = document.getElementById('pwa-open-button');
const statusBlock = document.getElementById('pwa-install-status');
const instructionBlock = document.getElementById('pwa-instruction-block');
const instructionTitle = document.getElementById('pwa-instruction-title');
const instructionSubtitle = document.getElementById('pwa-instruction-subtitle');
const instructionSteps = document.getElementById('pwa-instruction-steps');
const instructionNote = document.getElementById('pwa-instruction-note');

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

function getBrowserName() {
    const ua = navigator.userAgent.toLowerCase();

    if (ua.includes('opr/') || ua.includes('opera')) {
        return 'opera';
    }

    if (ua.includes('firefox') || ua.includes('fxios')) {
        return 'firefox';
    }

    if (ua.includes('crios')) {
        return 'chrome_ios';
    }

    if (ua.includes('safari') && !ua.includes('chrome') && !ua.includes('crios') && !ua.includes('fxios') && !ua.includes('opr/')) {
        return 'safari';
    }

    if (ua.includes('chrome') || ua.includes('chromium') || ua.includes('edg/')) {
        return 'chromium';
    }

    return 'unknown';
}

function isMacOSDevice() {
    return /macintosh|mac os x/i.test(navigator.userAgent) && !isIOSDevice();
}

function getInstallInstructionText() {
    const browser = getBrowserName();

    if (browser === 'safari' && isMacOSDevice()) {
        return pwaText(
            'pwa_instruction_safari_macos',
            'Installation through the button is not available in Safari on macOS.\n\nTo add the app:\n1. Open this page in Safari.\n2. Click the Share button in the toolbar.\n3. Choose “Add to Dock”.\n4. Click “Add”.\n\nAfter that, open the app from the Dock or Spotlight.'
        );
    }

    if (browser === 'safari' && isIOSDevice()) {
        return pwaText(
            'pwa_instruction_safari_ios',
            'Installation through the button is not available in Safari on iPhone or iPad.\n\nTo add the app:\n1. Open this page in Safari.\n2. Tap the Share button.\n3. Scroll down and tap “Add to Home Screen”.\n4. Enable “Open as Web App” if this option is shown.\n5. Tap “Add”.\n\nAfter that, open the app from the icon on your Home Screen.'
        );
    }

    if (browser === 'chrome_ios') {
        return pwaText(
            'pwa_instruction_chrome_ios',
            'Installation through the button is not available in Chrome on iPhone or iPad.\n\nTo add the app:\n1. Open this page in Chrome.\n2. Tap the Share button near the address bar.\n3. Tap “Add to Home Screen”.\n4. Confirm the app name.\n5. Tap “Add”.\n\nAfter that, open the app from the icon on your Home Screen.'
        );
    }

    if (browser === 'firefox') {
        if (isAndroidDevice()) {
            return pwaText(
                'pwa_instruction_firefox_android',
                'Installation through the button is not available in Firefox.\n\nTo add the site shortcut:\n1. Open this page in Firefox.\n2. Tap the menu button.\n3. Tap “Add to Home Screen”.\n4. Tap “Add”.\n\nAfter that, open the site from the icon on your Home Screen.'
            );
        }

        if (isIOSDevice()) {
            return pwaText(
                'pwa_instruction_firefox_ios',
                'Installation through the button is not available in Firefox on iPhone or iPad.\n\nTo add the site shortcut:\n1. Open this page in Firefox.\n2. Tap the Share button.\n3. Tap “Add to Home Screen”.\n4. Tap “Add”.\n\nAfter that, open the site from the icon on your Home Screen.'
            );
        }

        return pwaText(
            'pwa_instruction_firefox_desktop',
            'Installation through the button is not available in Firefox on desktop.\n\nYou can bookmark this site or open it in Chrome, Edge, or Safari on macOS to install it as an app.'
        );
    }

    if (browser === 'opera') {
        if (isAndroidDevice()) {
            return pwaText(
                'pwa_instruction_opera_android',
                'Installation through the button is not available in Opera.\n\nTo add the site shortcut:\n1. Open this page in Opera.\n2. Tap the menu button.\n3. Choose “Add to” or “Add to Home screen”.\n4. Confirm adding the shortcut.\n\nAfter that, open the site from the icon on your Home Screen.'
            );
        }

        if (isIOSDevice()) {
            return pwaText(
                'pwa_instruction_opera_ios',
                'Installation through the button is not available in Opera on iPhone or iPad.\n\nIf “Add to Home Screen” is available in the Share menu, use it. If it is not shown, open this page in Safari and use Share → Add to Home Screen.'
            );
        }

        return pwaText(
            'pwa_instruction_opera_desktop',
            'Installation through the button is not available in Opera on desktop.\n\nUse Chrome, Edge, or Safari on macOS if you want app-style installation.'
        );
    }

    return pwaText(
        'pwa_instruction_default',
        'Installation through the button is unavailable in this browser. Use Chrome or Edge, or add this site to your Home Screen from the browser menu.'
    );
}

function showInstructionBlock(title, text) {
    if (instructionBlock) {
        instructionBlock.style.display = 'block';
    }

    if (instructionTitle) {
        instructionTitle.textContent = title;
    }

    if (instructionText) {
        instructionText.textContent = text;
    }
}

function hideInstructionBlock() {
    if (instructionBlock) {
        instructionBlock.style.display = 'none';
    }

    if (instructionTitle) {
        instructionTitle.textContent = '';
    }

    if (instructionText) {
        instructionText.textContent = '';
    }
}

function escapeHtml(text) {
    return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function hideInstructionBlock() {
    if (instructionBlock) instructionBlock.style.display = 'none';
    if (instructionTitle) instructionTitle.textContent = '';
    if (instructionSubtitle) instructionSubtitle.textContent = '';
    if (instructionSteps) instructionSteps.innerHTML = '';
    if (instructionNote) instructionNote.textContent = '';
}

function renderInstructionSteps(steps) {
    if (!instructionSteps) return;

    instructionSteps.innerHTML = (steps || []).map((step) => {
        return `
            <div class="pwa-instruction-step">
                <div class="pwa-instruction-step-icon">${escapeHtml(step.icon || '•')}</div>
                <div class="pwa-instruction-step-text">${step.text}</div>
            </div>
        `;
    }).join('');
}

function showInstructionCard(data) {
    if (!instructionBlock) return;

    instructionBlock.style.display = 'block';

    if (instructionTitle) {
        instructionTitle.textContent = data.title || '';
    }

    if (instructionSubtitle) {
        instructionSubtitle.textContent = data.subtitle || '';
    }

    renderInstructionSteps(data.steps || []);

    if (instructionNote) {
        instructionNote.textContent = data.note || '';
    }
}

function getInstallInstructionData() {
    const browser = getBrowserName();

    if (browser === 'safari' && isMacOSDevice()) {
        return {
            title: 'Add the app in Safari',
            subtitle: 'Installation through the button is not available in Safari on macOS.',
            steps: [
                { icon: '1', text: 'Open this page in <strong>Safari</strong>.' },
                { icon: '2', text: 'Click the <strong>Share</strong> button in the toolbar.' },
                { icon: '3', text: 'Choose <strong>Add to Dock</strong>.' },
                { icon: '4', text: 'Click <strong>Add</strong>.' }
            ],
            note: 'After that, launch the app from the Dock or Spotlight.'
        };
    }

    if (browser === 'safari' && isIOSDevice()) {
        return {
            title: 'Add the app on iPhone / iPad',
            subtitle: 'Installation through the button is not available in Safari on iPhone or iPad.',
            steps: [
                { icon: '1', text: 'Open this page in <strong>Safari</strong>.' },
                { icon: '2', text: 'Tap the <strong>Share</strong> button.' },
                { icon: '3', text: 'Select <strong>Add to Home Screen</strong>.' },
                { icon: '4', text: 'If available, enable <strong>Open as Web App</strong>.' },
                { icon: '5', text: 'Tap <strong>Add</strong>.' }
            ],
            note: 'After that, open the app from the Home Screen icon.'
        };
    }

    if (browser === 'chrome_ios') {
        return {
            title: 'Add the app in Chrome for iPhone',
            subtitle: 'The install button is not available in Chrome on iPhone or iPad.',
            steps: [
                { icon: '1', text: 'Open this page in <strong>Chrome</strong>.' },
                { icon: '2', text: 'Tap the <strong>Share</strong> button near the address bar.' },
                { icon: '3', text: 'Choose <strong>Add to Home Screen</strong>.' },
                { icon: '4', text: 'Confirm the app name and tap <strong>Add</strong>.' }
            ],
            note: 'Then open the app from the Home Screen icon.'
        };
    }

    if (browser === 'firefox') {
        if (isAndroidDevice()) {
            return {
                title: 'Add shortcut in Firefox',
                subtitle: 'Firefox does not support the install button here.',
                steps: [
                    { icon: '1', text: 'Open this page in <strong>Firefox</strong>.' },
                    { icon: '2', text: 'Open the <strong>browser menu</strong>.' },
                    { icon: '3', text: 'Tap <strong>Add to Home Screen</strong>.' },
                    { icon: '4', text: 'Confirm adding the shortcut.' }
                ],
                note: 'After that, open the shortcut from your Home Screen.'
            };
        }

        if (isIOSDevice()) {
            return {
                title: 'Add shortcut in Firefox',
                subtitle: 'Firefox on iPhone/iPad does not support the install button.',
                steps: [
                    { icon: '1', text: 'Open this page in <strong>Firefox</strong>.' },
                    { icon: '2', text: 'Tap the <strong>Share</strong> button.' },
                    { icon: '3', text: 'Choose <strong>Add to Home Screen</strong>.' },
                    { icon: '4', text: 'Tap <strong>Add</strong>.' }
                ],
                note: 'Then launch it from the Home Screen.'
            };
        }

        return {
            title: 'Install is not available in Firefox',
            subtitle: 'Firefox desktop does not support this install button.',
            steps: [
                { icon: '★', text: 'You can <strong>bookmark</strong> this site.' },
                { icon: '↗', text: 'Or open it in <strong>Chrome</strong>, <strong>Edge</strong>, or <strong>Safari on macOS</strong>.' }
            ],
            note: 'Use another supported browser if you want app-style installation.'
        };
    }

    if (browser === 'opera') {
        if (isAndroidDevice()) {
            return {
                title: 'Add shortcut in Opera',
                subtitle: 'Opera does not support the install button here.',
                steps: [
                    { icon: '1', text: 'Open this page in <strong>Opera</strong>.' },
                    { icon: '2', text: 'Open the <strong>browser menu</strong>.' },
                    { icon: '3', text: 'Choose <strong>Add to Home screen</strong>.' },
                    { icon: '4', text: 'Confirm adding the shortcut.' }
                ],
                note: 'After that, open the shortcut from your Home Screen.'
            };
        }

        if (isIOSDevice()) {
            return {
                title: 'Add shortcut in Opera',
                subtitle: 'Opera on iPhone/iPad does not support the install button here.',
                steps: [
                    { icon: '1', text: 'Try the <strong>Share</strong> menu in Opera.' },
                    { icon: '2', text: 'If <strong>Add to Home Screen</strong> is available, use it.' },
                    { icon: '3', text: 'If not, open this page in <strong>Safari</strong>.' },
                    { icon: '4', text: 'Then use <strong>Share → Add to Home Screen</strong>.' }
                ],
                note: 'After that, open it from the Home Screen.'
            };
        }

        return {
            title: 'Install is not available in Opera',
            subtitle: 'Opera desktop does not support this install button.',
            steps: [
                { icon: '↗', text: 'Open this page in <strong>Chrome</strong>, <strong>Edge</strong>, or <strong>Safari on macOS</strong>.' },
                { icon: '★', text: 'Or save the page as a regular bookmark.' }
            ],
            note: 'Use a supported browser if you want app-style installation.'
        };
    }

    return {
        title: 'Manual installation',
        subtitle: 'The install button is unavailable in this browser.',
        steps: [
            { icon: '↗', text: 'Try opening this page in <strong>Chrome</strong> or <strong>Edge</strong>.' },
            { icon: '★', text: 'Or add the site from your browser menu if that option is available.' }
        ],
        note: 'Supported installation methods depend on browser and device.'
    };
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
    hideInstructionBlock();

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

    hideInstructionBlock();

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
    if (installedBlock) {
        installedBlock.style.display = 'none';
    }

    if (installBlock) {
        installBlock.style.display = 'block';
    }

    if (installButton) {
        installButton.disabled = true;
    }

    if (statusBlock) {
        statusBlock.style.display = 'none';
        statusBlock.textContent = '';
    }

    showInstructionCard(getInstallInstructionData());
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

    if (isPwaMode()) {
        showInstalledState();
        return;
    }

    const ua = navigator.userAgent.toLowerCase();
    const isChromeLike =
        ua.includes('chrome') ||
        ua.includes('chromium') ||
        ua.includes('edg/');

    const isOpera =
        ua.includes('opr/') ||
        ua.includes('opera');

    const isFirefox =
        ua.includes('firefox');

    const installedByCookie = getCookieValue('pwa_installed') === 'true';

    if (installedByCookie && isChromeLike && !isOpera && !isFirefox) {
        showInstalledState();
        return;
    }

    setTimeout(() => {
        if (!deferredInstallPrompt) {
            showUnavailableState();
        }
    }, 1500);
});