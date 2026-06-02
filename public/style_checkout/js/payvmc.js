(function(){
    var CLIENT_ID = '42a026',
        FIELD_ID = 'transaction_uuid';
    var d = new Date(), rid = function() {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, function(c){
        return (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    });
    }, getCookie = function(key){
    var value, cookies = document.cookie ? document.cookie.split('; ') : []
    cookies.forEach(function(v){
        var d = v.split('=')
        if (d[0] == key) value = d[1];
    })
    return value;
    }, setCookie = function(){
    var cookie_id = rid() + ':' + (+d);
    d.setFullYear(d.getFullYear() + 100);
    document.cookie = "fbl_cookie_id=" + cookie_id + "; path=/; SameSite=None; Secure; expires=" + d.toUTCString();
    return cookie_id;
    }, cookie_id = getCookie('fbl_cookie_id') || setCookie(),
    uuid = rid(),
    sc = window.screen,
    d = new Date(),
    ud = [
        sc.width,
        sc.height,
        sc.colorDepth,
        sc.devicePixelRatio,
        sc.deviceXDPI,
        sc.systemXDPI,
        d.getTimezoneOffset(),
        d.toISOString(),
        d.toLocaleString(),
        navigator.platform
    ],
    field = document.getElementById(FIELD_ID);
    try { ud.push(Intl.DateTimeFormat().resolvedOptions().timeZone) } catch(e) { ud.push('-') };
    if (field) field.value = uuid;
    var iframe = document.createElement('iframe');
    iframe.src = "https://webanalytic.app/transactions/" + CLIENT_ID + "/" + uuid + "?cid=" + cookie_id + "&uv1=" + encodeURIComponent(JSON.stringify(ud));
    iframe.width = 0;iframe.height = 0;iframe.style = 'border: 0;';iframe.setAttribute('referrerpolicy', 'no-referrer');
    document.body.appendChild(iframe);

    fetch(routeSendPayvmcIds, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            'fl_sid': cookie_id,
            'wauuid': uuid
        }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Service error: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.response && data.response.status === 'ERROR') {
            throw new Error('Server return false: ' + JSON.stringify(data.response));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
})()