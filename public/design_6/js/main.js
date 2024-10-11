$( function() {
    const announce = document.querySelector(".announce__item");
    const tempurl = window.location.href;
    let pathname = window.location.pathname;
    if (pathname != '/') {
        if (!tempurl.includes('affiliate') && !tempurl.includes('contact_us')) {
            setTimeout((() => {
                announce.classList.add("active");
                announce.parentNode.style = 'z-index: 300;';
            }), 8000);

            setTimeout((() => {
                announce.classList.remove("active");
                announce.parentNode.style = 'z-index: 0;';
            }), 12000);

        }
    }

    $('.more').on('click', function (e){
        e.preventDefault();
        let block = $(this).parents('.product__image-block_links').find('.product__image_links');
        block.toggleClass('active');
        if($(this).text() == 'view all') {
            $(this).html('hide');
            block.css({'display': 'flex', 'flex-wrap': 'wrap'});
        } else {
            $(this).html('view all');
            block.css({'display': '-webkit-box'});
        }
    });

} );

function undisabled(page) {
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let captcha = document.getElementById("captcha").value;
    let validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    if (name == '' || email == '' || !email.match(validRegex) || captcha == '') {
        if (page == 'contact_us') {
            document.getElementById('message_send_button').setAttribute('disabled', 'disabled');
        } else {
            document.getElementById('affiliate_send_button').setAttribute('disabled', 'disabled');
        }
    } else {
        if (page == 'contact_us') {
            document.getElementById('message_send_button').style.backgroundColor = 'var(--green)';
            document.getElementById('message_send_button').removeAttribute('disabled');
        } else {
            document.getElementById('affiliate_send_button').style.backgroundColor = 'var(--green)';
            document.getElementById('affiliate_send_button').removeAttribute('disabled');
        }
    }
}


$(document).on('change', '[data-quantity-value]', function () {
    let value = parseInt($(this).val());
    let id = parseInt($(this).attr('id'));
    let value_ship = $("input[name='delivery']:checked").val();
    let value_bonus = $("input[name='bonus']:checked").attr('id');
    $.ajax({
        url: "/app/ajax_cart.php",
        type: 'POST',
        data: {'num': value, 'id': id, 'bonus_id': value_bonus, 'shipping_code': value_ship},
        dataType: 'html',
        success : function(data) {
            $(".basket").html(data);
        }
    });
});

$(document).on('click', '#message_send_button', function () {
    const name = document.getElementById("name").value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById("subject").value;
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    $.ajax({
        url: '/request_contact_us',
        type: "POST",
        cache: false,
        data: { 'name' : name,
        'email' : email,
        'subject' : subject,
        'message' : message,
        'captcha' : captcha,
        'submit' : submit },
        dataType: "json",
        success: function(data) { //Данные отправлены успешно
            if (data['status'] == 'error') {
                alert(data['text']);
                $('#captcha_image').attr('src', data['new_captcha']);
            } else {
                $('.page-text__top-row').hide();
                $('.page-text__inner').hide();
                $('.message_sended').removeClass('hidden');
                $('.message_sended').addClass('active');

                setTimeout((() => {
                    location.href = '/' + location.search;
                }), 2000);
            }
        }
     });
});

$(document).on('click', '#affiliate_send_button', function () {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const jabber = document.getElementById("jabber").value;
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    $.ajax({
        url:     '/request_affiliate',
        type:     "POST",
        cache: false,
        data: { 'name' : name,
        'email' : email,
        'jabber' : jabber,
        'message' : message,
        'captcha' : captcha,
        'submit' : submit },
        dataType: "json",
        success: function(data) { //Данные отправлены успешно
            if (data['status'] == 'error') {
                alert(data['text']);
                $('#captcha_image').attr('src', data['new_captcha']);
            } else {
                $('.page-text__top-row').hide();
                $('.page-text__inner').hide();
                $('.message_sended').removeClass('hidden');
                $('.message_sended').addClass('active');

                setTimeout((() => {
                    location.href = '/' + location.search;
                }), 2000);
            }
        }
     });
});

$(document).on('click', '.search_button', function () {
    location.href='/app/search.php?search=' + $('[name="search"]').val();
});

$(document).on('click', '.gift_checkbox', function () {
    if ($(this).hasClass('get-gift')) {
        $(this).removeClass('get-gift');
        $('.gift_bottom_block').hide();
    } else {
        $(this).addClass('get-gift');
        $('.gift_bottom_block').css('display', 'flex');
    }
});

$(document).on('click', '.select_header_gift', function () {
    $(this).parent().toggleClass('is-active');
});

$(document).on('click', '.select_item_gift', function () {
    $('.select_current_gift').text($(this).text());
    $('.select_current_gift').attr('curr_packaging_id', $(this).attr('packaging_id'));
    $(this).parent().parent().removeClass('is-active');
});