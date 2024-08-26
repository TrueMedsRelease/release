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