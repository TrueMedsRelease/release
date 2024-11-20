function logIn() {
    let password = $('#password_field').val();
    let captcha = $('#captcha_field').val();

    if (!password) {
        $('#password_error').show();
        $('#login_messages').hide();
    } else {
        $('#password_error').hide();
    }

    if (!captcha) {
        $('#captcha_error').show();
        $('#login_messages').hide();
    } else {
        $('#captcha_error').hide();

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
                    $('#captcha_error').text($('#captcha_invalid').val());
                    $('#captcha_error').show();
                    $('#login_messages').hide();
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {
                    $('#captcha_error').hide();
                }

                if (!$('#captcha_error').is(':visible') && !$('#password_error').is(':visible')) {
                    $.ajax({
                        url: '/admin/request_login',
                        type: 'POST',
                        cache: false,
                        dataType: 'json',
                        data: {
                            'password': password,
                        },
                        success: function (data) {
                            if (data['status'] == 'error') {
                                alert(data['text']);
                            } else {
                                window.location.href = data['url'];
                            }
                        }
                    });
                }
            }
        });
    }
}

function addToMain() {
    let selected_ids = $('#not_showed_on_main_page_products_field').val();

    $.ajax({
        url: '/admin/main_page/add_to_main',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#admin_main_page').html(data.html);
            }
        }
    });
}

function deleteFromMain() {
    let selected_ids = $('#products_on_main_field').val();

    $.ajax({
        url: '/admin/main_page/delete_from_main',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#admin_main_page').html(data.html);
            }
        }
    });
}

function productUpInSort() {
    let selected_ids = $('#products_on_main_field').val();

    $.ajax({
        url: '/admin/main_page/product_up_in_sort',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#admin_main_page').html(data.html);
            }
        }
    });
}

function productDownInSort() {
    let selected_ids = $('#products_on_main_field').val();

    $.ajax({
        url: '/admin/main_page/product_down_in_sort',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#admin_main_page').html(data.html);
            }
        }
    });
}

function saveUserProperties() {
    let new_password = $('#new_password_field').val();
    let repeat_password = $('#new_password_repeat_field').val();

    if (new_password == '') {
        $('#new_password_error').show();
    } else {
        $('#new_password_error').hide();
    }

    if (repeat_password == '') {
        $('#new_password_repeat_error').show();
    } else {
        $('#new_password_repeat_error').hide();
    }

    if (new_password != '' && repeat_password != '') {
        if (new_password != repeat_password) {
            $('#new_password_repeat_error').text($('#invalid_password_repeat').val());
            $('#new_password_repeat_error').show();
        } else {
            $('#new_password_repeat_error').hide();
        }
    }

    if (!$('#new_password_error').is(':visible') && !$('#new_password_repeat_error').is(':visible')) {
        $.ajax({
            url: '/admin/main_properties/save_user_properties',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'new_password': new_password
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    alert('Password saved');
                    location.href = data.url;
                }
            }
        });
    }
}

function saveTemplate() {
    let selected_template = $('input[name="template_name_field"]:checked').val();

    $.ajax({
        url: '/admin/main_properties/save_template',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_template': selected_template
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                alert('Template saved');
                $('#properties_content').html(data.html);
            }
        }
    });
}

function loadPageProperties() {
    let page = $('#pages_field').val();
    let language_id = $('#language_field').val();

    if (page != null && language_id != null) {
        $.ajax({
            url: '/admin/main_properties/load_page_properties',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'page': page,
                'language_id': language_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#properties_content').html(data.html);
                    $('#pages_field option[value="' + page + '"]').attr('selected', "selected");
                    $('#language_field option[value=' + language_id + ']').attr('selected', "selected");
                }
            }
        });
    }
    // else {
    //     alert("Check one page and language");
    // }
}

function savePageProperties() {
    let page = $('#pages_field').val();
    let language_id = $('#language_field').val();
    let title = $('#title_field').val();
    let keyword = $.trim($('#keywords_field').val());
    let description = $.trim($('#description_field').val());

    if (page != null && language_id != null) {
        if (title != '' && keyword != '' && description != '') {
            $.ajax({
                url: '/admin/main_properties/save_page_properties',
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {
                    'page': page,
                    'language_id': language_id,
                    'title': title,
                    'keyword': keyword,
                    'description': description
                },
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status == 'error') {
                        alert(data.text);
                    } else {
                        alert('Page properties saved');
                        location.href = data.url;
                    }
                }
            });
        } else {
            alert("Empty field");
        }
    } else {
        alert("Check one page and language");
    }
}

function addProductToShowed() {
    let selected_ids = $('#not_showed_products_field').val();

    $.ajax({
        url: '/admin/available_products/add_to_showed',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#available_product').html(data.html);
            }
        }
    });
}

function deleteProductFromShowed() {
    let selected_ids = $('#showed_products_field').val();

    $.ajax({
        url: '/admin/available_products/delete_from_showed',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'selected_products': selected_ids
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                $('#available_product').html(data.html);
            }
        }
    });
}

function loadProductPackaging(product_id) {
    if (product_id) {
        $.ajax({
            url: '/admin/available_packagings/load_packaging_info',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#available_packagings').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function addPackagngInShowed() {
    let selected_ids = $('#not_showed_packagings_field').val();
    let product_id = $('#all_products_field').val();

    if (product_id) {
        $.ajax({
            url: '/admin/available_packagings/add_pack_to_showed',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'selected_packaging': selected_ids,
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#available_packagings').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function deletePackagngFromShowed() {
    let selected_ids = $('#showed_packagings_field').val();
    let product_id = $('#all_products_field').val();

    if (product_id) {
        $.ajax({
            url: '/admin/available_packagings/delete_pack_from_showed',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'selected_packaging': selected_ids,
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#available_packagings').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function packagingUpInSort() {
    let selected_ids = $('#showed_packagings_field').val();
    let product_id = $('#all_products_field').val();

    if (product_id) {
        $.ajax({
            url: '/admin/available_packagings/packaging_up_in_sort',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'selected_packaging': selected_ids,
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#available_packagings').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function packagingDownInSort() {
    let selected_ids = $('#showed_packagings_field').val();
    let product_id = $('#all_products_field').val();

    if (product_id) {
        $.ajax({
            url: '/admin/available_packagings/packaging_down_in_sort',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'selected_packaging': selected_ids,
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#available_packagings').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function loadProductInfo(product_id) {
    if (product_id) {
        $.ajax({
            url: '/admin/products/load_product_info',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#product_prop_content').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function saveProductInfo() {
    let data = $('#products_form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    $.ajax({
        url: '/admin/products/save_product_info',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'product_form_data': data
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}

function loadProductURL(product_id) {
    if (product_id) {
        $.ajax({
            url: '/admin/seo/load_product_url',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'product_id': product_id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#properties_content').html(data.html);

                    $('#all_products_field option[value=' + product_id + ']').attr('selected', "selected");
                    let position = $('#all_products_field option[value=' + product_id + ']').offset().top;
                    $('#all_products_field').scrollTop(position - 500);
                }
            }
        });
    }
}

function saveProductURL() {
    let data = $('#products_form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    $.ajax({
        url: '/admin/seo/save_product_url',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'product_form_data': data
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}

function saveLanguagesInfo() {
    let data = $('#languages_form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    $.ajax({
        url: '/admin/save_languages_info',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'languages_form_data': data
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}

function saveCurrenciesInfo() {
    let data = $('#currencies_form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    $.ajax({
        url: '/admin/save_currencies_info',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'currencies_form_data': data
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}

function loadPixelData(page) {
    if (page) {
        $.ajax({
            url: '/admin/seo/load_pixel',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'page': page
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    $('#pixel_text').text(data.text);
                }
            }
        });
    }
}

function SavePixelData() {
    let selected_page = $('input[name="pixel_name_field"]:checked').val();
    // let pixel_text = $('#pixel_text').val();
    let pixel_text = $('#pixel_form').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    if (selected_page) {
        $.ajax({
            url: '/admin/seo/save_pixel',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'selected_page': selected_page,
                'pixel_text': pixel_text
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    location.href = data.url;
                }
            }
        });
    }
}

function saveGiftCardInfo() {
    let card_info = $('input[name="gift_card_info"]:checked').val();

    if (card_info) {
        $.ajax({
            url: '/admin/available_products/gift_card_info',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {
                'card_info': card_info,
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'error') {
                    alert(data.text);
                } else {
                    location.href = data.url;
                }
            }
        });
    }
}

function saveCheckoutInfo() {
    let default_shipping = $('input[name="default_shipping"]:checked').val();
    // let default_insur = $('input[name="default_insur"]:checked').val();
    // let default_secret = $('input[name="default_secret"]:checked').val();
    // let paypal_setting = $('input[name="paypal_setting"]:checked').val();
    $.ajax({
        url: '/admin/checkout/save_checkout_info',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'default_shipping': default_shipping,
            // 'default_insur': default_insur,
            // 'default_secret': default_secret,
            // 'paypal_setting': paypal_setting,
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}

function saveSubscribePopupInfo() {
    let popup_status = $('input[name="subsc_popup_info"]:checked').val();

    $.ajax({
        url: '/admin/main_page/save_subscribe_info',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'popup_status': popup_status,
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data.status == 'error') {
                alert(data.text);
            } else {
                location.href = data.url;
            }
        }
    });
}