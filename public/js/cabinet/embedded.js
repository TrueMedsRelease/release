/**
 * Встроенный логин — клиентская логика без сборки.
 * Повторяет поведение кабинета (customerportal Login.vue + SmartCaptcha.vue):
 * шаг 1 — только email с кнопкой, шаг 2 — только пазл-капча
 * (без поля email), после решения форма уходит сама;
 * OTP из 6 ячеек с автосабмитом, обратный отсчёт повторной отправки.
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

    /* ================= шаги email -> captcha (Login.vue 1:1) ================= */

    function initCaptcha() {
        var box = document.getElementById('cab-captcha');
        if (!box || !window.CabEmbeddedConfig) return;

        var canvas = document.getElementById('cab-captcha-canvas');
        var bg = document.getElementById('cab-captcha-bg');
        var piece = document.getElementById('cab-captcha-piece');
        var verifyBtn = document.getElementById('cab-captcha-verify');
        var refreshBtn = document.getElementById('cab-captcha-refresh');
        var statusEl = document.getElementById('cab-captcha-status');
        var form = document.getElementById('cab-embedded-form');
        var emailRow = document.getElementById('cab-email-row');
        var emailInput = document.getElementById('cab-email');
        var emailError = document.getElementById('cab-email-error');
        var stepEmail = document.getElementById('cab-step-email');
        var stepCaptcha = document.getElementById('cab-step-captcha');
        var continueBtn = document.getElementById('cab-continue');
        var changeEmailBtn = document.getElementById('cab-change-email');
        if (!canvas || !bg || !piece || !verifyBtn || !refreshBtn || !statusEl || !form) return;

        var cfg = window.CabEmbeddedConfig;
        var state = {
            token: '',
            puzzleSize: 60,
            canvasWidth: 360,
            canvasHeight: 200,
            currentX: 10,
            currentY: 0,
            dragging: false,
            startMouseX: 0,
            startMouseY: 0,
            startPuzzleX: 0,
            startPuzzleY: 0,
            mouseEvents: [],
            startTime: 0,
            endTime: 0,
            loading: false,
            loaded: false,
            submitted: false,
        };

        function setStatus(text, type, loading) {
            statusEl.textContent = text || '';
            statusEl.className = 'status-indicator' + (type ? ' ' + type : '');
            if (loading) {
                var spin = document.createElement('span');
                spin.className = 'loading';
                statusEl.prepend(spin);
            }
        }

        function liveScale() {
            if (!canvas || !state.canvasWidth) return 1;
            var w = canvas.getBoundingClientRect().width;
            return w > 0 ? w / state.canvasWidth : 1;
        }

        function paintPiece() {
            var wPct = (state.puzzleSize / state.canvasWidth * 100);
            piece.style.width = wPct + '%';
            piece.style.aspectRatio = '1 / 1';
            piece.style.left = (state.currentX / state.canvasWidth * 100) + '%';
            piece.style.top = (state.currentY / state.canvasHeight * 100) + '%';
        }

        async function fetchCaptcha() {
            state.loading = true;
            verifyBtn.disabled = true;
            setStatus('', '');
            piece.classList.remove('is-success');
            state.mouseEvents = [];
            try {
                var res = await fetch(cfg.captchaUrl, {
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
                piece.style.borderRadius = '50%';
                piece.style.display = '';
                state.currentX = 10;
                state.currentY = data.height - data.puzzleSize - 10;
                state.startTime = 0;
                state.endTime = 0;
                state.loaded = true;
                paintPiece();
            } catch (e) {
                setStatus(cfg.tLoadFailed || 'Failed to load captcha. Try again.', 'error');
            } finally {
                state.loading = false;
                verifyBtn.disabled = false;
            }
        }

        function showEmail() {
            if (stepCaptcha) stepCaptcha.hidden = true;
            if (stepEmail) stepEmail.hidden = false;
            if (emailInput) emailInput.focus();
        }

        function showCaptcha() {
            if (stepEmail) stepEmail.hidden = true;
            if (stepCaptcha) stepCaptcha.hidden = false;
            // Капчу тянем лениво — только когда она реально понадобилась,
            // как SmartCaptcha в кабинете (монтируется на шаге капчи).
            if (!state.loaded && !state.loading) fetchCaptcha();
        }

        function hideEmailError() {
            if (emailError) emailError.hidden = true;
        }

        function showEmailError(text) {
            if (!emailError) return;
            emailError.textContent = text;
            emailError.hidden = false;
        }

        function goCaptcha() {
            var value = emailInput ? emailInput.value.trim() : '';
            if (!isEmail(value)) {
                showEmailError(cfg.tInvalidEmail || 'Wrong Email!');
                if (emailInput) emailInput.focus();
                return;
            }
            hideEmailError();
            if (emailInput) emailInput.value = value;
            showCaptcha();
        }

        if (continueBtn) {
            continueBtn.addEventListener('click', goCaptcha);
        }
        if (emailInput) {
            emailInput.addEventListener('input', hideEmailError);
            // Кнопка шага — type=button, поэтому Enter обрабатываем вручную.
            emailInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goCaptcha();
                }
            });
            emailInput.focus();
        }
        if (changeEmailBtn) {
            // Как changeEmail в кабинете: возврат на шаг email.
            changeEmailBtn.addEventListener('click', showEmail);
        }

        function clamp(v, min, max) {
            return Math.max(min, Math.min(v, max));
        }

        function dragStart(clientX, clientY) {
            state.dragging = true;
            state.startMouseX = clientX;
            state.startMouseY = clientY;
            state.startPuzzleX = state.currentX;
            state.startPuzzleY = state.currentY;
            state.startTime = Date.now();
            state.mouseEvents = [{ x: state.currentX, y: state.currentY, time: 0 }];
        }

        function dragMove(clientX, clientY) {
            if (!state.dragging) return;
            var s = liveScale();
            var x = clamp(state.startPuzzleX + (clientX - state.startMouseX) / s, 0, state.canvasWidth - state.puzzleSize);
            var y = clamp(state.startPuzzleY + (clientY - state.startMouseY) / s, 0, state.canvasHeight - state.puzzleSize);
            state.currentX = x;
            state.currentY = y;
            paintPiece();
            state.mouseEvents.push({ x: x, y: y, time: Date.now() - state.startTime });
        }

        function dragEnd() {
            if (!state.dragging) return;
            state.dragging = false;
            state.endTime = Date.now();
        }

        piece.addEventListener('mousedown', function (e) {
            e.preventDefault();
            dragStart(e.clientX, e.clientY);
        });
        document.addEventListener('mousemove', function (e) { dragMove(e.clientX, e.clientY); });
        document.addEventListener('mouseup', dragEnd);
        piece.addEventListener('touchstart', function (e) {
            e.preventDefault();
            var t = e.touches[0];
            dragStart(t.clientX, t.clientY);
        }, { passive: false });
        document.addEventListener('touchmove', function (e) {
            if (!state.dragging) return;
            e.preventDefault();
            var t = e.touches[0];
            dragMove(t.clientX, t.clientY);
        }, { passive: false });
        document.addEventListener('touchend', dragEnd);

        refreshBtn.addEventListener('click', fetchCaptcha);

        verifyBtn.addEventListener('click', async function () {
            if (state.loading || state.submitted || !state.token) return;
            state.loading = true;
            verifyBtn.disabled = true;
            setStatus(cfg.tChecking || 'Checking...', '', true);
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
                    // Как onCaptchaVerified в кабинете: решили пазл — форма уходит сама.
                    state.submitted = true;
                    setStatus(cfg.tSuccess || 'Success!', 'success');
                    piece.classList.add('is-success');
                    setTimeout(function () { form.submit(); }, 400);
                } else {
                    setStatus((result && result.reason) || cfg.tFailed || 'Verification failed', 'error');
                    setTimeout(fetchCaptcha, 1500);
                }
            } catch (e) {
                setStatus(cfg.tError || 'Verification error', 'error');
            } finally {
                state.loading = false;
                if (!state.submitted) verifyBtn.disabled = false;
            }
        });

        // Подсветка ошибки email как в UiInput.
        if (emailRow) {
            var syncRow = function () {
                emailRow.classList.toggle('active', !!(emailInput && emailInput.value));
            };
            if (emailInput) {
                emailInput.addEventListener('input', syncRow);
                syncRow();
            }
        }
    }

    /* ================= OTP из 6 ячеек (OtpInput 1:1) ================= */

    function initOtp() {
        var wrap = document.getElementById('cab-otp');
        if (!wrap) return;
        var inputs = Array.prototype.slice.call(wrap.querySelectorAll('.otp-input__cell'));
        var hidden = document.getElementById('cab-code-hidden');
        var form = document.getElementById('cab-code-form');
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
                    // Вставка нескольких цифр — разложить по ячейкам.
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

    /* ================= повторная отправка (обратный отсчёт) ================= */

    function initResend() {
        var btn = document.getElementById('cab-resend');
        var label = document.getElementById('cab-resend-label');
        if (!btn || !label || !window.CabCodeConfig) return;
        var cfg = window.CabCodeConfig;
        var secs = 180;
        btn.disabled = true;

        function tick() {
            if (secs <= 0) {
                label.textContent = cfg.resend;
                btn.disabled = false;
                return;
            }
            label.textContent = cfg.resendIn.replace('{s}', String(secs)).replace(':s', String(secs));
            secs -= 1;
            setTimeout(tick, 1000);
        }
        tick();

        // Повторная отправка требует свежего пазла — возвращаем на шаг email.
        btn.addEventListener('click', function () {
            if (btn.disabled) return;
            window.location.href = cfg.emailUrl;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initCaptcha();
            initOtp();
            initResend();
        });
    } else {
        initCaptcha();
        initOtp();
        initResend();
    }
})();
