/**
 * E2EE-транспорт кабинета: ECDH X25519 + HKDF-SHA512 + XSalsa20-Poly1305
 * (secretbox) поверх прокси шопа. Шоп видит Bearer/endpoint/размер, но не тела.
 *
 * Зависимость: tweetnacl (public/js/cabinet/nacl.min.js, window.nacl),
 * грузится раньше этого файла. Протокол — customerportal
 * docs/api-integration.md §5.4.
 *
 * Ключ сообщения K = HKDF(Z || K_code), где Z — ECDH эфемерного ключа
 * вкладки и долгоживущего pk портала, а K_code выведен из кода из письма
 * (setCode после verify) и тем же образом считается порталом в момент
 * verify. Так каждое сообщение привязано к церемонии входа: чужой конверт
 * в чужой сессии не откроется. Сессии без кода (админ-IP, старые) идут
 * только на Z (kb=0). K_code хранится в localStorage (пассивному шопу
 * недоступен — он видит лишь свой JS, а не хранилище браузера) и стирается
 * через forgetAll() при выходе везде.
 *
 * Ключи живут в памяти вкладки (сессионная eph-пара). Нет pk (портал без
 * E2EE) — прозрачный fallback на plaintext с warn в консоль.
 */
(function (global) {
    'use strict';

    var KEYS_URL = '/account/api/e2ee/keys';
    var STORE_KEY = 'e2ee_kc';
    var _keys = null;   // {kid, pk:Uint8Array}
    var _keysPromise = null;
    var _session = null; // {esk, epk}
    var _kc = undefined; // Uint8Array|null (undefined = не читали storage)

    function b64uEncode(bytes) {
        var bin = '';
        for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
        return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    function b64uDecode(s) {
        s = String(s || '');
        if (!/^[A-Za-z0-9\-_]+$/.test(s)) return null;
        var b64 = s.replace(/-/g, '+').replace(/_/g, '/');
        while (b64.length % 4) b64 += '=';
        try {
            var bin = atob(b64);
            var out = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
            return out;
        } catch (e) {
            return null;
        }
    }

    function utf8Encode(s) {
        if (typeof TextEncoder !== 'undefined') return new TextEncoder().encode(s);
        var utf8 = unescape(encodeURIComponent(s));
        var out = new Uint8Array(utf8.length);
        for (var i = 0; i < utf8.length; i++) out[i] = utf8.charCodeAt(i);
        return out;
    }

    function utf8Decode(bytes) {
        if (typeof TextDecoder !== 'undefined') return new TextDecoder().decode(bytes);
        var bin = '';
        for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
        return decodeURIComponent(escape(bin));
    }

    function concat(a, b) {
        var out = new Uint8Array(a.length + b.length);
        out.set(a, 0);
        out.set(b, a.length);
        return out;
    }

    function nacl() {
        var w = (typeof window !== 'undefined') ? window : (typeof global !== 'undefined' ? global : {});
        return w.nacl || null;
    }

    /* HKDF-SHA512 (RFC 5869) поверх tweetnacl.hash, 1-в-1 с PHP-версией. */

    function hmacSha512(keyBytes, dataBytes) {
        var lib = nacl();
        var blockSize = 128;
        var key = keyBytes.length > blockSize ? lib.hash(keyBytes) : keyBytes;
        var k = new Uint8Array(blockSize);
        k.set(key);
        var ipad = new Uint8Array(blockSize);
        var opad = new Uint8Array(blockSize);
        for (var i = 0; i < blockSize; i++) {
            ipad[i] = k[i] ^ 0x36;
            opad[i] = k[i] ^ 0x5c;
        }
        return lib.hash(concat(opad, lib.hash(concat(ipad, dataBytes))));
    }

    function hkdf(ikm, saltStr, infoStr, len) {
        var salt = saltStr ? utf8Encode(saltStr) : new Uint8Array(64);
        var prk = hmacSha512(salt, ikm);
        var t = hmacSha512(prk, concat(utf8Encode(infoStr), new Uint8Array([1])));
        return t.slice(0, len);
    }

    function fetchKeys() {
        if (_keys) return Promise.resolve(_keys);
        if (_keysPromise) return _keysPromise;
        _keysPromise = fetch(KEYS_URL, { credentials: 'same-origin' })
            .then(function (res) { return res.json().catch(function () { return {}; }); })
            .then(function (data) {
                _keysPromise = null;
                var lib = nacl();
                if (!lib || !data || !data.kid || !data.pk) return null;
                var pk = b64uDecode(data.pk);
                if (!pk || pk.length !== 32) return null;
                _keys = { kid: String(data.kid), pk: pk };
                return _keys;
            })
            .catch(function () {
                _keysPromise = null;
                return null;
            });
        return _keysPromise;
    }

    function forgetKeys() {
        _keys = null;
        _keysPromise = null;
    }

    function session() {
        var lib = nacl();
        if (!lib) return null;
        if (!_session) {
            var kp = lib.box.keyPair();
            _session = { esk: kp.secretKey, epk: kp.publicKey };
        }
        return _session;
    }

    function readStoredKc() {
        try {
            var raw = null;
            if (typeof localStorage !== 'undefined') raw = localStorage.getItem(STORE_KEY);
            if (!raw) return null;
            var kc = b64uDecode(raw);
            return (kc && kc.length === 32) ? kc : null;
        } catch (e) {
            return null;
        }
    }

    function getKc() {
        if (_kc !== undefined) return _kc;
        _kc = readStoredKc();
        return _kc;
    }

    function hasCodeKey() {
        return !!getKc();
    }

    /**
     * Запомнить K_code из кода из письма (вызывать при submit кода,
     * пока код ещё в руках — после verify его уже нигде нет).
     */
    function setCode(code, email) {
        try {
            var lib = nacl();
            if (!lib) return false;
            var kc = hkdf(
                utf8Encode(String(code || '')),
                'e2ee-code-v1',
                'code:' + String(email || '').trim().toLowerCase(),
                32
            );
            _kc = kc;
            try {
                if (typeof localStorage !== 'undefined') localStorage.setItem(STORE_KEY, b64uEncode(kc));
            } catch (e) {}
            return true;
        } catch (e) {
            return false;
        }
    }

    function forgetAll() {
        _session = null;
        _kc = null;
        forgetKeys();
        try {
            if (typeof localStorage !== 'undefined') localStorage.removeItem(STORE_KEY);
        } catch (e) {}
    }

    function messageKey(shared, kc, dir, kid) {
        var ikm = kc ? concat(shared, kc) : shared;
        return hkdf(ikm, 'e2ee-msg-v1', dir + ':' + kid, 32);
    }

    // Shop-путь /account/api/<x> -> путь портала /api/v1/<x> для привязки.
    function portalPath(shopPath) {
        var p = String(shopPath || '').split('?')[0];
        if (p.indexOf('/account/api/') === 0) return '/api/v1/' + p.slice('/account/api/'.length);
        return p;
    }

    /**
     * Шифрование тела запроса. Возвращает envelope либо null
     * (E2EE недоступен — слать plaintext).
     */
    function sealRequest(shopPath, method, body, keys) {
        var lib = nacl();
        var sess = session();
        if (!lib || !sess || !keys) return null;
        try {
            var kc = getKc();
            var path = portalPath(shopPath);
            var m = String(method || 'GET').toUpperCase();
            var shared = lib.scalarMult(sess.esk, keys.pk);
            var msgKey = messageKey(shared, kc, 'req', keys.kid);
            var nonce = lib.randomBytes(24);
            var plain = utf8Encode(JSON.stringify({
                ts: Math.floor(Date.now() / 1000),
                method: m,
                path: path,
                body: body,
            }));
            var ct = lib.secretbox(plain, nonce, msgKey);
            if (!ct) return null;
            return {
                envelope: {
                    v: 2,
                    kid: keys.kid,
                    epk: b64uEncode(sess.epk),
                    nonce: b64uEncode(nonce),
                    ct: b64uEncode(ct),
                    kb: kc ? 1 : 0,
                },
            };
        } catch (e) {
            return null;
        }
    }

    /**
     * Заголовки для GET-запроса (шифруется только ответ).
     * Возвращает {epk} для последующего openResponse либо null.
     */
    function sealGet() {
        var sess = session();
        if (!sess) return null;
        return { epk: sess.epk, esk: sess.esk };
    }

    /**
     * Расшифровка ответа-конверта. Бросает Error при неудаче.
     */
    function openResponse(data, esk) {
        var lib = nacl();
        var sess = session();
        if (!lib || !sess || !data || data.v !== 2 || !data.ct || !data.nonce || !data.kid) {
            throw new Error('E2EE: bad envelope');
        }
        var useEsk = esk || sess.esk;
        var ct = b64uDecode(data.ct);
        var nonce = b64uDecode(data.nonce);
        if (!ct || !nonce || nonce.length !== 24) {
            throw new Error('E2EE: bad envelope');
        }
        // Ответ зашифрован на тот же shared, что запрос (портал знает наш epk).
        // Перебираем базисы как портал: сначала K_code, потом чистый ECDH —
        // коды ошибок одинаковые, оракула нет.
        var keys = _keys;
        var candidates = [];
        var kc = getKc();
        if (keys) {
            // shared для ответа выводится порталом из нашего session-epk.
            candidates.push({ kid: data.kid, kc: kc });
            candidates.push({ kid: data.kid, kc: null });
        }
        var lastErr = null;
        for (var i = 0; i < candidates.length; i++) {
            try {
                var c = candidates[i];
                // shared ответа = ECDH(esk_сессии, pk_портала) — тот же Z, что у запроса.
                var z = lib.scalarMult(useEsk, keys.pk);
                var msgKey = messageKey(z, c.kc, 'resp', c.kid);
                var plain = lib.secretbox.open(ct, nonce, msgKey);
                if (plain) {
                    try {
                        return JSON.parse(utf8Decode(plain));
                    } catch (e) {
                        throw new Error('E2EE: bad payload');
                    }
                }
            } catch (e) {
                lastErr = e;
            }
        }
        throw lastErr || new Error('E2EE: open failed');
    }

    /**
     * Разобрать ответ прокси: конверт -> расшифровать (нужен esk от sealGet),
     * обычный JSON -> как есть. Ошибки (не-2xx вызывающая сторона уже
     * обработала) возвращаются как есть — машинные коды читаемы.
     */
    function unwrapResponse(data, esk) {
        if (data && typeof data === 'object' && data.v === 2 && typeof data.ct === 'string') {
            return openResponse(data, esk);
        }
        return data;
    }

    global.E2EE = {
        ensureKeys: fetchKeys,
        forgetKeys: forgetKeys,
        forgetAll: forgetAll,
        setCode: setCode,
        hasCodeKey: hasCodeKey,
        sealRequest: sealRequest,
        sealGet: sealGet,
        openResponse: openResponse,
        unwrapResponse: unwrapResponse,
        b64uEncode: b64uEncode,
        b64uDecode: b64uDecode,
    };
})(typeof window !== 'undefined' ? window : this);
