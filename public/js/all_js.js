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

    window.AffStatsConfig = {
        endpoint: "http://localhost:8173/statistics/v1",
        visitTimeoutMin: 120,
        debug: true,
    }

    const cfg = w.AffStatsConfig || {};
    const ENDPOINT = cfg.endpoint || "/collect/visit";
    const VISIT_TIMEOUT_MIN = (typeof cfg.visitTimeoutMin === "number" && isFinite(cfg.visitTimeoutMin)) ? cfg.visitTimeoutMin : 120;
    const SDK_VERSION = "aff-collector-js/0.1.0";
    const SCHEMA_VERSION = "aff_pageview/0.1.0";

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

    function hasStorage(type) {
        try {
            const s = w[type];
            if (!s) return false;
            const k = "__tm_t";
            s.setItem(k, "1");
            s.removeItem(k);
            return true;
        } catch (_) {
            return false;
        }
    }

    function uuid() {
        try {
            if (w.crypto && crypto.randomUUID) return crypto.randomUUID();
        } catch (_) {
        }
        try {
            if (w.crypto && crypto.getRandomValues) {
                const a = crypto.getRandomValues(new Uint8Array(16));
                a[6] = (a[6] & 0x0f) | 0x40;
                a[8] = (a[8] & 0x3f) | 0x80;
                const h = Array.prototype.map.call(a, function (b) {
                    return ('0' + b.toString(16)).slice(-2);
                }).join('');
                return h.slice(0, 8) + '-' + h.slice(8, 12) + '-' + h.slice(12, 16) + '-' + h.slice(16, 20) + '-' + h.slice(20);
            }
        } catch (_) {
        }
        // very last-resort fallback
        const t = Date.now().toString(16);
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = (Math.random() * 16) | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        }) + t.slice(0, 4);
    }

    function getOrSet(store, key, gen) {
        try {
            const v = store.getItem(key);
            if (v) return v;
            const n = gen();
            store.setItem(key, n);
            return n;
        } catch (_) {
            return gen();
        }
    }

    function parseQS() {
        try {
            return new URLSearchParams(w.location.search);
        } catch (_) {
            const p = new URLSearchParams(); // polyfill-ish usage
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
            const v = qs.get(keys[i]);
            if (v) return v;
        }
        return null;
    }

    function extractSubs(qs) {
        const obj = {};
        qs.forEach(function (v, k) {
            if (!v) return;
            if (/^sub[1-9]\d*$/.test(k) || /^sub_/.test(k)) obj[k] = v;
        });
        return obj;
    }

    function currentAttributionSignature(qs) {
        const parts = [
            qs.get("utm_source") || "",
            qs.get("utm_medium") || "",
            qs.get("utm_campaign") || "",
            qs.get("utm_term") || "",
            qs.get("utm_content") || "",
            getNetworkClickId(qs) || "",
            qs.get("aff") || "",
            (qs.get("click_id") || qs.get("saff") || "")
        ];
        return parts.join("|");
    }

    function computeVisitId(qs) {
        const VISIT_KEY = "tm_visit_id";
        const TOUCH_KEY = "tm_visit_touch_sig";
        const SEEN_KEY = "tm_visit_last_seen_ms";
        const lsOK = hasStorage('localStorage');
        const now = Date.now();
        const curSig = currentAttributionSignature(qs);
        let oldSig = null, lastSeen = 0, vId = null;
        if (lsOK) {
            try {
                vId = localStorage.getItem(VISIT_KEY);
                oldSig = localStorage.getItem(TOUCH_KEY);
                lastSeen = parseInt(localStorage.getItem(SEEN_KEY) || "0", 10);
            } catch (_) {
            }
        }
        const timeoutMs = VISIT_TIMEOUT_MIN * 60 * 1000;
        const needNew = !vId || (oldSig !== curSig && curSig) || (lastSeen && (now - lastSeen > timeoutMs));
        if (needNew) vId = uuid();
        if (lsOK) {
            try {
                localStorage.setItem(VISIT_KEY, vId);
                localStorage.setItem(TOUCH_KEY, curSig);
                localStorage.setItem(SEEN_KEY, String(now));
            } catch (_) {
            }
        }
        return vId;
    }

    function computeSessionId() {
        const key = "tm_session_id";
        const ssOK = hasStorage('sessionStorage');
        if (ssOK) return getOrSet(sessionStorage, key, uuid);
        if (!w.__tm_sid) w.__tm_sid = uuid(); // in-memory fallback
        return w.__tm_sid;
    }

    function detectStoreTheme(qs) {
        let designId = safeStr(qs.get("design"))
        if (designId) return designId;

        designId = getCookie("js_stat_design_id")
        if (designId) return designId;

        if (cfg.storeTheme !== undefined && cfg.storeTheme !== null) return String(cfg.storeTheme);
        return null;
    }

    function detectAffId(qs) {
        let affId = safeStr(qs.get("aff"))
        if (affId) return affId;

        affId = getCookie("js_stat_aff_id")
        if (affId) return affId;

        return 0;
    }

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function buildPayload() {
        const qs = parseQS();
        const payload = {
            schema_version: SCHEMA_VERSION,
            sdk_version: SDK_VERSION,
            event_id: uuid(),
            event_type: "pageview",
            client_ts: nowISO(),
            visit_id: computeVisitId(qs),
            session_id: computeSessionId(),

            landing_url: w.location.href,
            referrer_url: d.referrer || null,

            aff_id: detectAffId(qs),
            click_id: safeStr(qs.get("click_id") || qs.get("saff")),

            utm_source: safeStr(qs.get("utm_source")),
            utm_medium: safeStr(qs.get("utm_medium")),
            utm_campaign: safeStr(qs.get("utm_campaign")),
            utm_term: safeStr(qs.get("utm_term")),
            utm_content: safeStr(qs.get("utm_content")),
            network_click_id: safeStr(getNetworkClickId(qs)),

            sub_params: extractSubs(qs),
            store_theme: detectStoreTheme(qs),
            keyword: safeStr(qs.get("keyword") || qs.get("q")),

            language: (navigator.language || null),
            tz_offset_min: -new Date().getTimezoneOffset(),
            screen_w: (w.screen && screen.width) || null,
            screen_h: (w.screen && screen.height) || null,
            dpr: w.devicePixelRatio || 1,
            ua_raw: navigator.userAgent
        };

        if (cfg.debug && w.console && console.debug) {
            try {
                console.debug("[AffSDK] payload", payload);
            } catch (_) {
            }
        }
        return payload;
    }

    function send(payload) {
        const body = JSON.stringify(payload);
        if (navigator.sendBeacon) {
            try {
                const ok = navigator.sendBeacon(ENDPOINT, new Blob([body], {type: "application/json"}));
                if (ok) return "beacon";
            } catch (_) {
            }
        }

        try {
            fetch(ENDPOINT, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: body,
                keepalive: true,
                credentials: "omit",
                cache: "no-store"
            }).catch(function () {
            });
            return "fetch";
        } catch (_) {
        }

        try {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", ENDPOINT, false);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(body);
            return "xhr";
        } catch (_) {
            return "none";
        }
    }

    // -------- fire once on load --------
    let _isAutoSent = false;

    function autoFire() {
        if (_isAutoSent) return;
        _isAutoSent = true;
        try {
            const p = buildPayload();
            const t = send(p);
            if (cfg.debug && w.console) {
                try {
                    console.debug("[AffSDK] sent via:", t);
                } catch (_) {
                }
            }
        } catch (e) {
            if (cfg.debug && w.console) {
                try {
                    console.warn("[AffSDK] failed:", e);
                } catch (_) {
                }
            }
        }
    }

    if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", autoFire, {once: true});
    else setTimeout(autoFire, 0);

    d.addEventListener("visibilitychange", function () {
        if (d.visibilityState === "hidden") autoFire();
    }, {once: true});
})(window, document);