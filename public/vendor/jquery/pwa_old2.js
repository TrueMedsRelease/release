window.addEventListener('load', function () {
    function getPWADisplayMode() {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;

        if (document.referrer.startsWith('android-app://')) {
            return 'twa';
        }

        if (navigator.standalone || isStandalone) {
            return 'standalone';
        }

        return 'browser';
    }

    const mode = getPWADisplayMode();

    document.cookie = 'theme=' + encodeURIComponent(mode) + '; path=/';

    const statTemp = document.getElementById('stattemp');
    if (!statTemp || !statTemp.value) {
        return;
    }

    const params = statTemp.value + '&theme=' + encodeURIComponent(mode);

    if (window.jQuery && typeof $.ajax === 'function') {
        $.ajax({
            url: routePWAInfo,
            type: 'POST',
            data: {
                params: params
            },
            dataType: 'json'
        });
    } else {
        const formData = new FormData();
        formData.append('params', params);

        fetch(routePWAInfo, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        }).catch(() => {});
    }
});