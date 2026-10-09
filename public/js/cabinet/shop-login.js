/**
 * Живой логин кабинета в отдельных login-layout дизайнов (без сборки).
 * Три отдельные страницы (как шаги customerportal Login.vue):
 *  email (только почта) -> captcha (только пазл) -> code (OTP из письма).
 * ID-привязки свои (shop-*),デザイン-независим: работает во всех 13 дизайнах.
 * Серверные эндпоинты те же, что у embedded.js (captcha/proxy/embedded).
 */
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function isEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function queryEmail() {
        try {
            return (new URLSearchParams(window.location.search).get('email') || '').trim();
        } catch (e) {
            return '';
        }
    }

    /* ================= шаг 1: email ================= */

    function initEmailStep() {
        var input = document.getElementById('shop-email');
        var btn = document.getElementById('shop-continue');
        var err = document.getElementById('shop-email-error');
        if (!input || !btn || !window.ShopLoginConfig) return;
        var cfg = window.ShopLoginConfig;

        function showError(text) {
            if (!err) return;
            err.textContent = text;
            err.hidden = false;
        }

        function go() {
            var value = input.value.trim();
            if (!isEmail(value)) {
                showError(cfg.tInvalidEmail || 'Wrong Email!');
                input.focus();
                return;
            }
            window.location.href = cfg.captchaUrl + '?email=' + encodeURIComponent(value);
        }

        btn.addEventListener('click', go);
        input.addEventListener('input', function () { if (err) err.hidden = true; });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                go();
            }
        });
        input.focus();
    }

    /* ================= шаг 2: пазл-капча ================= */

    function initCaptchaStep() {
        var box = document.getElementById('shop-captcha');
        if (!box || !window.ShopLoginConfig) return;
        var cfg = window.ShopLoginConfig;

        var canvas = document.getElementById('shop-captcha-canvas');
        var bg = document.getElementById('shop-captcha-bg');
        var piece = document.getElementById('shop-captcha-piece');
        var verifyBtn = document.getElementById('shop-captcha-verify');
        var refreshBtn = document.getElementById('shop-captcha-refresh');
        var statusEl = document.getElementById('shop-captcha-status');
        var form = document.getElementById('shop-captcha-form');
        var emailHidden = document.getElementById('shop-captcha-email');
        var emailText = document.getElementById('shop-captcha-email-text');
        if (!canvas || !form) return;

        var email = queryEmail();
        if (!isEmail(email)) {
            if (statusEl) {
                statusEl.textContent = cfg.tInvalidEmail || 'Wrong Email!';
                statusEl.className = 'cablogin-status cablogin-status--error';
            }
            if (verifyBtn) verifyBtn.disabled = true;
            return;
        }
        if (emailHidden) emailHidden.value = email;
        if (emailText) emailText.textContent = email;

        var state = {
            token: '', puzzleSize: 60, canvasWidth: 360, canvasHeight: 200,
            currentX: 10, currentY: 0, dragging: false,
            startMouseX: 0, startMouseY: 0, startPuzzleX: 0, startPuzzleY: 0,
            mouseEvents: [], startTime: 0, endTime: 0,
            loading: false, submitted: false,
        };

        function setStatus(text, kind) {
            if (!statusEl) return;
            statusEl.textContent = text || '';
            statusEl.className = 'cablogin-status' + (kind ? ' cablogin-status--' + kind : '');
        }

        function paintPiece() {
            piece.style.width = (state.puzzleSize / state.canvasWidth * 100) + '%';
            piece.style.aspectRatio = '1 / 1';
            piece.style.left = (state.currentX / state.canvasWidth * 100) + '%';
            piece.style.top = (state.currentY / state.canvasHeight * 100) + '%';
        }

        function clamp(v, min, max) {
            return Math.max(min, Math.min(v, max));
        }

        async function fetchCaptcha() {
            state.loading = true;
            if (verifyBtn) verifyBtn.disabled = true;
            setStatus('');
            try {
                var res = await fetch(cfg.captchaImageUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                var data = await res.json();
                state.token = data.token;
                state.puzzleSize = data.puzzleSize;
                state.canvasWidth = data.width;
                state.canvasHeight = data.height;
                canvas.style.aspectRatio = data.width + ' / ' + data.height;
                bg.src = data.backgroundImage;
                bg.style.display = '';
                piece.style.backgroundImage = 'url(' + data.puzzleImage + ')';
                piece.style.backgroundSize = 'cover';
                piece.style.display = '';
                state.currentX = 10;
                state.currentY = data.height - data.puzzleSize - 10;
                state.mouseEvents = [];
                state.startTime = 0;
                state.endTime = 0;
                paintPiece();
            } catch (e) {
                setStatus(cfg.tLoadFailed || 'Failed to load captcha. Try again.', 'error');
            } finally {
                state.loading = false;
                if (verifyBtn) verifyBtn.disabled = false;
            }
        }

        function dragStart(x, y) {
            state.dragging = true;
            state.startMouseX = x;
            state.startMouseY = y;
            state.startPuzzleX = state.currentX;
            state.startPuzzleY = state.currentY;
            state.startTime = Date.now();
            state.mouseEvents = [{ x: state.currentX, y: state.currentY, time: 0 }];
        }

        function dragMove(x, y) {
            if (!state.dragging) return;
            var rect = canvas.getBoundingClientRect();
            var s = rect.width > 0 && state.canvasWidth ? rect.width / state.canvasWidth : 1;
            state.currentX = clamp(state.startPuzzleX + (x - state.startMouseX) / s, 0, state.canvasWidth - state.puzzleSize);
            state.currentY = clamp(state.startPuzzleY + (y - state.startMouseY) / s, 0, state.canvasHeight - state.puzzleSize);
            paintPiece();
            state.mouseEvents.push({ x: state.currentX, y: state.currentY, time: Date.now() - state.startTime });
        }

        function dragEnd() {
            if (!state.dragging) return;
            state.dragging = false;
            state.endTime = Date.now();
        }

        piece.addEventListener('mousedown', function (e) { e.preventDefault(); dragStart(e.clientX, e.clientY); });
        document.addEventListener('mousemove', function (e) { dragMove(e.clientX, e.clientY); });
        document.addEventListener('mouseup', dragEnd);
        piece.addEventListener('touchstart', function (e) { e.preventDefault(); var t = e.touches[0]; dragStart(t.clientX, t.clientY); }, { passive: false });
        document.addEventListener('touchmove', function (e) {
            if (!state.dragging) return;
            e.preventDefault();
            var t = e.touches[0];
            dragMove(t.clientX, t.clientY);
        }, { passive: false });
        document.addEventListener('touchend', dragEnd);

        if (refreshBtn) refreshBtn.addEventListener('click', fetchCaptcha);

        if (verifyBtn) verifyBtn.addEventListener('click', async function () {
            if (state.loading || state.submitted || !state.token) return;
            state.loading = true;
            verifyBtn.disabled = true;
            setStatus(cfg.tChecking || 'Checking...', 'checking');
            try {
                var res = await fetch(cfg.captchaVerifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        token: state.token,
                        x: state.currentX,
                        y: state.currentY,
                        mouseEvents: state.mouseEvents,
                        totalTime: state.endTime ? state.endTime - state.startTime : Date.now() - state.startTime,
                    }),
                });
                var result = await res.json();
                if (result && result.success) {
                    state.submitted = true;
                    setStatus(cfg.tSuccess || 'Success!', 'success');
                    setTimeout(function () { form.submit(); }, 400);
                } else {
                    setStatus((result && result.reason) || cfg.tFailed || 'Verification failed', 'error');
                    setTimeout(fetchCaptcha, 1500);
                }
            } catch (e) {
                setStatus(cfg.tError || 'Verification error', 'error');
            } finally {
                state.loading = false;
                if (!state.submitted && verifyBtn) verifyBtn.disabled = false;
            }
        });

        fetchCaptcha();
    }

    /* ================= шаг 3: OTP ================= */

    function initOtpStep() {
        var wrap = document.getElementById('shop-otp');
        if (!wrap) return;
        var inputs = Array.prototype.slice.call(wrap.querySelectorAll('.cablogin-otp__cell'));
        var hidden = document.getElementById('shop-code-hidden');
        var form = document.getElementById('shop-code-form');
        if (!inputs.length || !hidden || !form) return;
        var submitting = false;

        function value() {
            return inputs.map(function (el) { return (el.value || '').replace(/\D/g, ''); }).join('');
        }
        function sync() {
            hidden.value = value();
            if (hidden.value.length === 6 && !submitting) {
                submitting = true;
                form.submit();
            }
        }
        function focusIndex(i) {
            i = Math.max(0, Math.min(inputs.length - 1, i));
            inputs[i].focus();
            inputs[i].select();
        }
        inputs.forEach(function (el, index) {
            el.addEventListener('input', function () {
                var digits = (el.value || '').replace(/\D/g, '');
                if (digits.length > 1) {
                    var chars = digits.split('');
                    var cursor = index;
                    for (var k = 0; k < chars.length && cursor < inputs.length; k++, cursor++) {
                        inputs[cursor].value = chars[k];
                    }
                    sync();
                    focusIndex(cursor);
                    return;
                }
                el.value = digits;
                sync();
                if (digits !== '') focusIndex(index + 1);
            });
            el.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace') {
                    e.preventDefault();
                    if (el.value) {
                        el.value = '';
                        sync();
                    } else if (index > 0) {
                        inputs[index - 1].value = '';
                        sync();
                        focusIndex(index - 1);
                    }
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    focusIndex(index - 1);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    focusIndex(index + 1);
                }
            });
            el.addEventListener('paste', function (e) {
                e.preventDefault();
                var text = ((e.clipboardData && e.clipboardData.getData('text')) || '').replace(/\D/g, '');
                if (!text) return;
                var chars = text.split('');
                var cursor = index;
                for (var k = 0; k < chars.length && cursor < inputs.length; k++, cursor++) {
                    inputs[cursor].value = chars[k];
                }
                sync();
                focusIndex(cursor);
            });
            el.addEventListener('focus', function () { el.select(); });
        });
        focusIndex(0);
    }

    /* ================= resend + fingerprint ================= */

    function initResend() {
        var btn = document.getElementById('shop-resend');
        var label = document.getElementById('shop-resend-label');
        if (!btn || !label || !window.ShopCodeConfig) return;
        var cfg = window.ShopCodeConfig;
        var secs = 180;
        btn.disabled = true;
        (function tick() {
            if (secs <= 0) {
                label.textContent = cfg.resend;
                btn.disabled = false;
                return;
            }
            label.textContent = (cfg.resendIn || 'Resend in {s}s').replace('{s}', String(secs));
            secs -= 1;
            setTimeout(tick, 1000);
        })();
        btn.addEventListener('click', function () {
            if (btn.disabled) return;
            window.location.href = cfg.emailUrl;
        });
    }

    function loadShopScript(src, timeoutMs) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            var done = false;
            var timer = setTimeout(function () {
                if (!done) { done = true; reject(new Error('device lib load timeout')); }
            }, timeoutMs || 5000);
            s.onload = function () { if (!done) { done = true; clearTimeout(timer); resolve(); } };
            s.onerror = function () { if (!done) { done = true; clearTimeout(timer); reject(new Error('device lib load failed')); } };
            s.src = src;
            document.head.appendChild(s);
        });
    }

    // Fingerprint привязывается к токену кабинета на шагах
    // embedded.start/verify (hidden input), а дальше каждая ручка
    // /account/api/* шлёт его же в X-FP (account.js). Несовпадение
    // (пусто на логине, реальный после) => 401 fingerprint_required
    // на первом же запросе заказов => снос токенов => возврат на логин.
    // Поэтому: считаем FP заранее, заполняем оба hidden input и не даём
    // формам уйти раньше (включая программный form.submit() OTP-автосабмита).
    var shopFpValue = '';
    var shopFpReady = null;
    var SHOP_FP_KEY = 'cabinet_fp';

    function readStoredShopFp() {
        try {
            var v = window.localStorage ? window.localStorage.getItem(SHOP_FP_KEY) : '';
            return v ? String(v) : '';
        } catch (e) {
            return '';
        }
    }

    function fillShopFp(fp) {
        if (!fp) return;
        shopFpValue = String(fp);
        try {
            if (window.localStorage) window.localStorage.setItem(SHOP_FP_KEY, shopFpValue);
        } catch (e) {}
        ['shop-fp', 'shop-code-fp'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el && !el.value) el.value = shopFpValue;
        });
    }

    function ensureShopFp() {
        if (shopFpValue) return Promise.resolve(shopFpValue);
        // DeviceLib нестабилен между страницами (viewport, links/scripts,
        // voices): переиспользуем значение, посчитанное ранее, чтобы кабинет
        // видел один и тот же FP на логине и в /account/api/*.
        var stored = readStoredShopFp();
        if (stored) {
            fillShopFp(stored);
            return Promise.resolve(shopFpValue);
        }
        if (shopFpReady) return shopFpReady;
        shopFpReady = (async function () {
            try {
                if (typeof window.DeviceLib === 'undefined' && typeof window.FingerprintLib === 'undefined') {
                    try {
                        await loadShopScript('/js/cabinet/device-lib.js', 5000);
                    } catch (e) { /* ниже вернём '' */ }
                }
                var Ctor = window.DeviceLib || window.FingerprintLib;
                if (!Ctor) return '';
                var r = await new Ctor().get();
                var fp = (r && r.hash && (r.hash.v4 || String(r.hash.numeric || ''))) || '';
                if (fp) fillShopFp(String(fp));
                return shopFpValue || '';
            } catch (e) {
                return shopFpValue || '';
            }
        })();
        return shopFpReady;
    }

    function guardShopForm(form) {
        if (!form || form.__shopFpGuarded) return;
        form.__shopFpGuarded = true;
        var nativeSubmit = form.submit.bind(form);
        var submitting = false;
        function submitWithFp() {
            if (submitting) return;
            if (shopFpValue) {
                fillShopFp(shopFpValue);
                submitting = true;
                nativeSubmit();
                return;
            }
            var timeout = new Promise(function (resolve) {
                setTimeout(function () { resolve('timeout'); }, 4000);
            });
            Promise.race([ensureShopFp(), timeout]).then(function () {
                fillShopFp(shopFpValue);
                submitting = true;
                nativeSubmit();
            });
        }
        form.addEventListener('submit', function (e) {
            if (submitting) return;
            var fpEl = document.getElementById('shop-fp') || document.getElementById('shop-code-fp');
            if ((fpEl && fpEl.value) || shopFpValue) return;
            e.preventDefault();
            submitWithFp();
        });
        form.submit = submitWithFp;
    }

    function initFingerprint() {
        ensureShopFp().catch(function () {});
        guardShopForm(document.getElementById('shop-captcha-form'));
        guardShopForm(document.getElementById('shop-code-form'));
    }

    function boot() {
        initEmailStep();
        initCaptchaStep();
        initOtpStep();
        initResend();
        initFingerprint();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
