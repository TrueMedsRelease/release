function refreshCartSidebar() {
    if (typeof window.CartAside !== 'undefined' && typeof window.CartAside.refresh === 'function') {
        window.CartAside.refresh();
    }
}

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
            refreshCartSidebar();
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
            refreshCartSidebar();
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
                refreshCartSidebar();
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
            refreshCartSidebar();
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
            refreshCartSidebar();
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
            refreshCartSidebar();
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

function addCardNew() {
    let value_card = $('select[name=gift_card_select]').val();;

    $.ajax({
        url: routeCartUp,
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':value_card},
        success: function (data) {
            data = JSON.parse(data);
            $('#shopping_cart').html(data.html);
            refreshCartSidebar();
        }
    });
}

function enterProfile() {
    // Встроенный кабинет выключен: бэкенд не дёргаем, только валидируем
    // email и уводим покупателя на портал: true-help.com/login?email=...
    let email = ($('[name="form[email]"]').val() || '').trim();
    let validRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;

    if (!email) {
        $('#email_error .input').text(email_invalid_text);
        $('#email_error').show();
        return;
    }

    if (!email.match(validRegex)) {
        $('#email_error .input').text(email_invalid_text);
        $('#email_error').show();
        return;
    }

    $('#email_error').hide();
    $('#preloader').show();

    let portalBase = (typeof portalLoginUrl !== 'undefined' && portalLoginUrl)
        ? portalLoginUrl
        : 'https://true-help.com/login';
    window.location.href = portalBase + '?email=' + encodeURIComponent(email);
}

function verifyProfile() {
    let email = $('[name="form[email]"]').val();
    let verify_code = $('[name="verify_code"]').val();

    if (!verify_code) {
        $('#verify_code_error').show();
    } else {
        $.ajax({
            url: routeVerifyProfile,
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: {
                'email': email,
                'verify_code': verify_code,
            },
            success: function (data) {
                if (!$('#verify_code_error').is(':visible')) {
                    if (data['status'] == 'error') {
                        alert(data['text']);
                    } else {
                        $('#preloader').show();
                        window.location.href = data['url'];
                    }
                }
            }
        });
    }
}

function isPwaMode() {
    return window.matchMedia('(display-mode: standalone)').matches ||
        window.matchMedia('(display-mode: fullscreen)').matches ||
        window.navigator.standalone === true;
}

function setPwaModeCookie() {
    const isPwa = isPwaMode() ? '1' : '0';

    document.cookie =
        'is_pwa=' + isPwa +
        '; path=/' +
        '; max-age=31536000' +
        '; SameSite=Lax';
}

setPwaModeCookie();

window.matchMedia('(display-mode: standalone)').addEventListener?.('change', setPwaModeCookie);
window.matchMedia('(display-mode: fullscreen)').addEventListener?.('change', setPwaModeCookie);

$('#close_verify_block').click(function() {
    $('.popup_verify_profile').hide();
})

if (design > 0) {
    const responsiveImages = {
        checkup: {
            biggest: pathImageCheckupBiggest,
            big: pathImageCheckupBig,
            middle: pathImageCheckupMiddle,
            small: pathImageCheckupSmall,
        },
        installStore: {
            biggest: pathImageDownloadStoreBiggest,
            big: pathImageDownloadStoreBig,
            middle: pathImageDownloadStoreMiddle,
            small: pathImageDownloadStoreSmall,
        },
    };

    function getBannerSize() {
        const width = document.documentElement.clientWidth;

        if (width > 1920) return 'biggest';
        if (width > 768) return 'big';
        if (width > 390) return 'middle';
        return 'small';
    }

    function updateResponsiveBanners() {
        const size = getBannerSize();

        const $headerBanner = $('.christmas[data-banner]');
        const headerBannerSrc = $headerBanner.attr('data-src-' + size);
        if (headerBannerSrc) {
            $headerBanner.find('img').attr('src', headerBannerSrc);
        }

        if (design != 3) {
            $('.checkup img').attr('src', responsiveImages.checkup[size]);
        }

        $('.install_store img').attr('src', responsiveImages.installStore[size]);
    }

    let lastBannerSize = null;
    function onBannerResize() {
        const size = getBannerSize();
        if (size !== lastBannerSize) {
            lastBannerSize = size;
            updateResponsiveBanners();
        }
    }

    onBannerResize();
    window.addEventListener('resize', onBannerResize);
}

(function initCallbackPhoneSanitizer() {
    const selector = '.callback-form input[type="tel"], .callback-form .intl-phone';

    function sanitizeCallbackPhone(input) {
        const originalValue = String(input.value || '');
        let sanitizedValue = originalValue.replace(/[^0-9+()\s-]/g, '');

        if (sanitizedValue.charAt(0) === '+') {
            sanitizedValue = '+' + sanitizedValue.slice(1).replace(/\+/g, '');
        } else {
            sanitizedValue = sanitizedValue.replace(/\+/g, '');
        }

        if (sanitizedValue !== originalValue) {
            input.value = sanitizedValue;
        }
    }

    document.addEventListener('beforeinput', function (event) {
        const input = event.target;
        if (!input || !input.matches || !input.matches(selector)) return;

        if (
            event.inputType === 'insertText' &&
            event.data &&
            /[^0-9+()\s-]/.test(event.data)
        ) {
            event.preventDefault();
        }
    });

    document.addEventListener('input', function (event) {
        const input = event.target;
        if (!input || !input.matches || !input.matches(selector)) return;
        sanitizeCallbackPhone(input);
    });
})();
