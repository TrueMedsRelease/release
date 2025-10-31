function up(pack_id) {
    $.ajax({
        url: routeCartUp,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':pack_id},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

function down(pack_id) {
    $.ajax({
        url: routeCartDown,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':pack_id},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

function remove(pack_id) {
    $.ajax({
        url: routeCartRemove,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':pack_id},
        success: function (data) {
            if(data == '')
            {
                window.location.replace(routeCart);
            }
            else
            {
                data = JSON.parse(data);
                $('#shopping_cart').html(data.html);
            }
        }
    });
}

function upgrade(pack_id) {
    $.ajax({
        url: routeCartUpgrade,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':pack_id},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength)
}

function change_shipping(shipping_name, shipping_price)
{
    $.ajax({
        url: routeCartShipping,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'shipping_name':shipping_name, 'shipping_price':shipping_price},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

function change_bonus(bonus_id, bonus_price)
{
    $.ajax({
        url: routeCartBonus,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'bonus_id':bonus_id, 'bonus_price':bonus_price},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

// $(document).on('click', '.visible.gift', function () {
//     if ($(this).hasClass('get-gift')) {
//         $(this).removeClass('get-gift');
//         $('.gift_bottom_block').hide();
//     } else {
//         $(this).addClass('get-gift');
//         $('.gift_bottom_block').css('display', 'flex');
//     }
// });

// $(document).on('click', '.select_item_gift', function () {
//     $('.select_current_gift').text($(this).text());
//     $('.select_current_gift').attr('curr_packaging_id', $(this).attr('packaging_id'));
//     $(this).parent().parent().removeClass('is-active');
// });

// $(document).on('click', '.select_header_gift', function () {
//     $(this).parent().toggleClass('is-active');
// });

function addCard() {
    let value_card = $('.select_current_gift').attr('curr_packaging_id');
    if (!value_card) {
        value_card = $('#select_item_gift').val();
    }
    $.ajax({
        url: routeCartUp,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':value_card},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
        }
    });
}

function enterProfile() {
    let email = $('[name="form[email]"]').val();
    let captcha = $('[name="form[code]"]').val();
    let validRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;

    if (!email) {
        $('#email_error').show();
    } else if (!email.match(validRegex)) {
        $('#email_error .input').text(email_invalid_text);
        $('#email_error').show();
    } else {
        $('#email_error').hide();
    }

    if (!captcha) {
        $('#captcha_error').show();
    } else {
        $.ajax({
            url: routeCheckCode,
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: {
                'captcha': captcha,
            },
            success: function (data) {
                if (data['result'] == false) {
                    $('#captcha_image_log').attr('src', data['new_captcha']);
                    $('#captcha_error .input').text(code_invalid_text);
                    $('#captcha_error').show();
                } else {
                    $('#captcha_error').hide();
                }

                if (!$('#captcha_error').is(':visible') && !$('#email_error').is(':visible')) {
                    $.ajax({
                        url: routeRequestLogin,
                        type: 'POST',
                        cache: false,
                        dataType: 'json',
                        data: {
                            'email': email,
                            'captcha': captcha,
                        },
                        success: function (data) {
                            if (data['status'] == 'error') {
                                alert(data['text']);
                            } else {
                                $('#preloader').show();
                                window.location.href = data['url'];
                            }
                        }
                    });
                }
            }
        });
    }
}


(function (w, d) {
    "use strict";

    if (window._affStatsSdkLoaded) {
        return;
    }
    window._affStatsSdkLoaded = true;

    function validateConfig() {
        const defaults = {
            endpoint: "http://localhost:8173/statistics/v1/collect/visit",
            visitTimeoutMin: 120,
            debug: true,
            // storeTheme: "default"
        };

        const config = { ...defaults };

        // Validate endpoint URL
        try {
            new URL(config.endpoint);
        } catch {
            console.warn('[AffSDK] Invalid endpoint, using default');
            config.endpoint = defaults.endpoint;
        }

        // Validate timeout
        if (typeof config.visitTimeoutMin !== 'number' ||
            !isFinite(config.visitTimeoutMin) ||
            config.visitTimeoutMin <= 0) {
            config.visitTimeoutMin = defaults.visitTimeoutMin;
        }

        return config;
    }

    const cfg = validateConfig();
    const ENDPOINT = cfg.endpoint;
    const VISIT_TIMEOUT_MIN = cfg.visitTimeoutMin;
    const DEFAULT_COOKIE_LIFETIME_MINUTES = 365 * 24 * 60;
    const SDK_VERSION = "aff-collector-js/0.1.0";
    const SCHEMA_VERSION = "aff_pageview/1.0.0";

    function withErrorHandling(fn, context) {
        return function(...args) {
            try {
                return fn.apply(this, args);
            } catch (error) {
                if (cfg.debug && w.console) {
                    console.error(`[AffSDK] Error in ${context}:`, error);
                }
                return null;
            }
        };
    }

    function setCookie(name, value, minutes = 120) {
        try {
            let cookie = `${name}=${encodeURIComponent(value)}`;

            if (minutes) {
                const date = new Date();
                date.setTime(date.getTime() + (minutes * 60 * 1000));
                cookie += `; expires=${date.toUTCString()}`;
            }

            cookie += `; path=/`;
            cookie += `; SameSite=Lax`;

            if (location.protocol === 'https:') {
                cookie += '; Secure';
            }

            document.cookie = cookie;
            return true;
        } catch (error) {
            return false;
        }
    }

    function getCookie(name) {
        try {
            const nameEq = name + "=";
            const ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEq) === 0) {
                    return decodeURIComponent(c.substring(nameEq.length, c.length));
                }
            }
            return undefined;
        } catch (error) {
            return undefined;
        }
    }

    function getParamWithCookieFallback(qs, paramName, cookieName, defaultValue = null) {
        // 1. Check query string first
        const qsValue = qs.get(paramName);
        if (qsValue !== null && qsValue !== undefined && qsValue !== '') {
            // 2. If found, update cookie and return value
            setCookie(cookieName, qsValue, DEFAULT_COOKIE_LIFETIME_MINUTES);
            return safeStr(qsValue);
        }

        // 3. If not in query string, check cookie
        const cookieValue = getCookie(cookieName);
        if (cookieValue !== undefined) {
            return safeStr(cookieValue);
        }

        // 4. Return default value
        return defaultValue;
    }

    function nowISO() {
        try {
            return new Date().toISOString();
        } catch (_) {
            return null;
        }
    }

    function safeStr(v) {
        return (v === undefined || v === null) ? null : String(v);
    }

    function uuid() {
        try {
            if (w.crypto && crypto.randomUUID) {
                return crypto.randomUUID();
            }
        } catch (_) {}

        try {
            if (w.crypto && crypto.getRandomValues) {
                const buffer = new Uint8Array(16);
                crypto.getRandomValues(buffer);

                buffer[6] = (buffer[6] & 0x0f) | 0x40;
                buffer[8] = (buffer[8] & 0x3f) | 0x80;

                const hex = Array.from(buffer, byte =>
                    byte.toString(16).padStart(2, '0')
                ).join('');

                return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
            }
        } catch (_) {}

        // Final fallback with timestamp
        const timestamp = Date.now().toString(36);
        const random = Math.random().toString(36).substring(2, 10);
        return `fallback-${timestamp}-${random}`;
    }

    function parseQS() {
        try {
            return new URLSearchParams(w.location.search);
        } catch (_) {
            const p = new URLSearchParams();
            const q = (w.location.search || '').replace(/^\?/, '');
            if (!q) return p;
            const parts = q.split('&');
            for (let i = 0; i < parts.length; i++) {
                const s = parts[i];
                if (!s) continue;
                const k = s.split('=');
                try {
                    p.append(decodeURIComponent(k[0] || ''), decodeURIComponent(k[1] || ''));
                } catch (__) {
                }
            }
            return p;
        }
    }

    function getNetworkClickId(qs) {
        const keys = ["gclid", "fbclid", "msclkid", "ttclid", "yclid"];
        for (let i = 0; i < keys.length; i++) {
            const v = getParamWithCookieFallback(qs, keys[i], "tm_network_click_id", null);
            if (v) return v;
        }
        return null;
    }

    function extractIndividualSubs(qs) {
        const subs = {};
        for (let i = 1; i <= 10; i++) {
            const subKey = `sub${i}`;
            subs[subKey] = getParamWithCookieFallback(qs, subKey, `js_stat_${subKey}`, null);
        }
        return subs;
    }

    function currentAttributionSignature(qs) {
        const parts = [
            getParamWithCookieFallback(qs, "aff", "js_stat_aff_id", ""),
            getParamWithCookieFallback(qs, "click_id", "js_stat_click_id", ""),
            getParamWithCookieFallback(qs, "saff", "js_stat_saff", ""),
            getParamWithCookieFallback(qs, "utm_source", "js_stat_utm_source", ""),
            getParamWithCookieFallback(qs, "utm_medium", "js_stat_utm_medium", ""),
            getParamWithCookieFallback(qs, "utm_campaign", "js_stat_utm_campaign", ""),
            getParamWithCookieFallback(qs, "utm_term", "js_stat_utm_term", ""),
            getParamWithCookieFallback(qs, "utm_content", "js_stat_utm_content", ""),
        ];
        return parts.join("|");
    }

    // Get or set initial referrer from cookie
    function getInitialReferrer() {
        // Try to get from cookie first
        const cookieReferrer = getCookie("tm_initial_referrer");
        if (cookieReferrer) {
            return cookieReferrer;
        }

        // If no cookie, use current referrer and store it
        const currentReferrer = d.referrer || "no referrer";
        setCookie("tm_initial_referrer", currentReferrer, DEFAULT_COOKIE_LIFETIME_MINUTES);

        return currentReferrer;
    }

    function computeSessionId() {
        const cookieSessionId = getCookie("tm_session_id");
        if (cookieSessionId) {
            return cookieSessionId;
        }

        const newSessionId = uuid();
        setCookie("tm_session_id", newSessionId, VISIT_TIMEOUT_MIN);
        return newSessionId;
    }

    function computeVisitID(qs) {
        const currentSignature = currentAttributionSignature(qs);

        const visitDataCookie = getCookie("tm_visit_data");
        let existingVisitId = null;
        let existingSignature = null;

        if (visitDataCookie) {
            try {
                const data = JSON.parse(visitDataCookie);
                existingVisitId = data.visit_id;
                existingSignature = data.signature;
            } catch (_) {
                // Invalid JSON, treat as no existing data
            }
        }

        let visitId;
        let isUniq;

        // If we have existing visit data and signature matches, reuse it
        if (existingVisitId && existingSignature === currentSignature) {
            visitId = existingVisitId;
            isUniq = false;
        } else {
            // Signature changed or no existing data - generate new visit_id
            visitId = uuid();
            isUniq = true;

            // Store new visit data in cookie (120 minutes)
            const visitData = {
                visit_id: visitId,
                signature: currentSignature
            };
            setCookie("tm_visit_data", JSON.stringify(visitData), VISIT_TIMEOUT_MIN);
        }

        return { visit_id: visitId, is_uniq: isUniq };
    }

    const buildPayload = withErrorHandling(function() {
        const qs = parseQS();

        const { visit_id, is_uniq } = computeVisitID(qs);

        const individualSubs = extractIndividualSubs(qs);

        const payload = {
            schema_version: SCHEMA_VERSION,
            sdk_version: SDK_VERSION,
            event_id: uuid(),
            event_type: "pageview",
            client_ts: nowISO(),
            visit_id: visit_id,
            session_id: computeSessionId(),
            is_uniq: is_uniq,

            landing_url: w.location.href,
            referrer_url: getInitialReferrer(),

            aff_id: getParamWithCookieFallback(qs, "aff", "js_stat_aff_id", 0),

            click_id: getParamWithCookieFallback(qs, "click_id", "js_stat_click_id",
                getParamWithCookieFallback(qs, "saff", "js_stat_saff", null)),

            utm_source: getParamWithCookieFallback(qs, "utm_source", "js_stat_utm_source"),
            utm_medium: getParamWithCookieFallback(qs, "utm_medium", "js_stat_utm_medium"),
            utm_campaign: getParamWithCookieFallback(qs, "utm_campaign", "js_stat_utm_campaign"),
            utm_term: getParamWithCookieFallback(qs, "utm_term", "js_stat_utm_term"),
            utm_content: getParamWithCookieFallback(qs, "utm_content", "js_stat_utm_content"),

            network_click_id: safeStr(getNetworkClickId(qs)),

            ...individualSubs,

            store_theme: getParamWithCookieFallback(qs, "design", "js_stat_design_id", cfg.storeTheme || "unknown"),

            keyword: getParamWithCookieFallback(qs, "keyword", "js_stat_keyword",
                getParamWithCookieFallback(qs, "q", "js_stat_q", null)),

            language: (navigator.language || null),
            tz_offset_min: -new Date().getTimezoneOffset(),
            screen_w: (w.screen && screen.width) || null,
            screen_h: (w.screen && screen.height) || null,
            dpr: w.devicePixelRatio || 1,
        };

        if (cfg.debug && w.console && console.debug) {
            try {
                console.debug("[AffSDK] payload", payload);
            } catch (_) {
            }
        }
        return payload;
    }, 'buildPayload');

    const send = withErrorHandling(function(payload) {
        const body = JSON.stringify(payload);

        if (window.fetch) {
            fetch(ENDPOINT, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: body,
                keepalive: true,
                credentials: "omit"
            })
                .then(response => {
                    if (cfg.debug) {
                        console.debug('[AffSDK] Fetch response status:', response.status);
                    }
                })
                .catch(function () {});
            return 'fetch';
        }

        try {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", ENDPOINT, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.timeout = 5000;
            xhr.send(body);
            return 'xhr';
        } catch (_) {
            return 'none';
        }
    }, 'send');

    let _isAutoSent = false;

    function autoFire() {
        if (_isAutoSent) return;
        _isAutoSent = true;

        const startTime = performance.now();

        try {
            const payload = buildPayload();
            if (!payload) {
                if (cfg.debug) console.warn('[AffSDK] Failed to build payload');
                return;
            }

            const transport = send(payload);
            const duration = performance.now() - startTime;

            if (cfg.debug && w.console) {
                console.debug(`[AffSDK] Sent in ${duration.toFixed(2)}ms via: ${transport}`);
            }
        } catch (e) {
            if (cfg.debug && w.console) {
                console.warn("[AffSDK] autoFire failed:", e);
            }
        }
    }

    function initialize() {
        if (d.readyState === "loading") {
            d.addEventListener("DOMContentLoaded", autoFire, { once: true });
        } else {
            setTimeout(autoFire, 0);
        }

        d.addEventListener("visibilitychange", function () {
            if (d.visibilityState === "hidden") autoFire();
        }, { once: true });

        w.addEventListener("beforeunload", autoFire, { once: true });
    }

    initialize();

})(window, document);