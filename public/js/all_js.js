function up(pack_id) {
    $.ajax({
        url: '/cart/up',
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
        url: '/cart/down',
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
        url: '/cart/remove',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'pack_id':pack_id},
        success: function (data) {
            if(data == '')
            {
                window.location.replace("/cart");
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
        url: '/cart/upgrade',
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

function add_pack(pack_id) {
    $.ajax({
        url: '/cart/add_pack',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {'pack_id': pack_id},
        success: function (data) {
            data = JSON.parse(data);
            console.log(data.text);
            location.href = data.url_redirect;
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
        url: '/cart/change-shipping',
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
        url: '/cart/change-bonus',
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
    $.ajax({
        url: '/cart/up',
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
            url: '/check_code',
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
                        url: '/request_login',
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