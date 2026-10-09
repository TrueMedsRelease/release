/**
 * /admin/cabinet page — logic for this page only.
 * The shop domain is never typed in: the server takes it from the
 * current request host (CabinetService::shopDomain() → $request->getHost()).
 */
(function ($) {
    'use strict';

    function copyText(text, $btn) {
        var done = function () {
            var original = $btn.text();
            $btn.text('Copied');
            setTimeout(function () { $btn.text(original); }, 1200);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () {
                fallbackCopy($btn);
                done();
            });
        } else {
            fallbackCopy($btn);
            done();
        }
    }

    function fallbackCopy($btn) {
        var $input = $('#' + $btn.data('copy-target'));
        if (!$input.length) return;
        $input.trigger('select');
        try {
            document.execCommand('copy');
        } catch (e) {
                /* clipboard API unavailable — value already selected for Ctrl+C */
        }
    }

    function init() {
        var $form = $('#cabinet_properties');
        if (!$form.length) return;

        var saveUrl = $form.data('save-url');
        var testUrl = $form.data('test-url');
        var $saveBtn = $('#cab_save_btn');
        var $testBtn = $('#cab_test_btn');
        var $result = $('#cabinet_test_result');
        var saveHtml = $saveBtn.html();

        function setResult(text, kind) {
            $result.text(text || '');
            $result.removeClass('cab-admin__result--success cab-admin__result--warning cab-admin__result--error');
            if (kind) {
                $result.addClass('cab-admin__result--' + kind);
            }
        }

        function setPending(pending) {
            $saveBtn.prop('disabled', pending);
            $testBtn.prop('disabled', pending);
            if (pending) {
                $saveBtn.text('Saving...');
            } else {
                $saveBtn.html(saveHtml);
            }
        }

        function formData() {
            var displayPages = {};
            ['orders', 'bonus', 'referral', 'contact', 'chat'].forEach(function (page) {
                displayPages[page] = $('#cab_page_' + page).is(':checked') ? '1' : '0';
            });
            return {
                cabinet_enabled: $('input[name="cabinet_enabled"]:checked').val(),
                cabinet_secret: $('#cabinet_secret').val(),
                'display[pages][orders]': displayPages.orders,
                'display[pages][bonus]': displayPages.bonus,
                'display[pages][referral]': displayPages.referral,
                'display[pages][contact]': displayPages.contact,
                'display[pages][chat]': displayPages.chat,
                'display[orders_filters]': $('#cab_orders_filters').is(':checked') ? '1' : '0',
                'display[orders_help]': $('#cab_orders_help').is(':checked') ? '1' : '0',
                'display[orders_shop_button]': $('#cab_shop_button').val(),
                'display[shop_domain_override]': $('#cab_shop_override').val(),
                'display[billing_url_override]': $('#cab_billing_override').val(),
                'display[checkout_base_override]': $('#cab_checkout_override').val(),
                'display[billing_button]': $('#cab_billing_button').is(':checked') ? '1' : '0'
            };
        }

        function save() {
            setPending(true);
            setResult('Saving...', '');
            $.ajax({
                url: saveUrl,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: formData()
            }).done(function (data) {
                if (data && data.status === 'error') {
                    setResult(data.text || 'Save failed.', 'error');
                    setPending(false);
                } else if (data && data.url) {
                    location.href = data.url;
                } else {
                    setResult('Unexpected response.', 'error');
                    setPending(false);
                }
            }).fail(function () {
                setResult('Save request failed.', 'error');
                setPending(false);
            });
        }

        function test() {
            setPending(true);
            setResult('Checking...', '');
            $.ajax({
                url: testUrl,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: formData()
            }).done(function (data) {
                var kind = data.status === 'success' ? 'success' : (data.status === 'warning' ? 'warning' : 'error');
                setResult((data && (data.text || data.status)) || 'No response.', kind);
                if (data && (data.status === 'success' || data.status === 'warning')) {
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    setPending(false);
                }
            }).fail(function () {
                setResult('Request failed.', 'error');
                setPending(false);
            });
        }

        // Enter in form fields saves instead of leaving via the empty action.
        $form.on('submit', function (e) {
            e.preventDefault();
            save();
        });
        $testBtn.on('click', test);
        // Display settings live outside the form: their Save button
        // reuses the same save (formData reads all fields by ID).
        $('#cab_display_save_btn').on('click', function () {
            $form.trigger('submit');
        });

        // Show/hide the secret.
        $('#cab_secret_toggle').on('click', function () {
            var $secret = $('#cabinet_secret');
            var show = $secret.attr('type') === 'password';
            $secret.attr('type', show ? 'text' : 'password');
            $(this).text(show ? 'Hide' : 'Show');
        });

        // Copy domain/callback from the guide.
        $('[data-copy-target]').on('click', function () {
            var $btn = $(this);
            var $input = $('#' + $btn.data('copy-target'));
            if ($input.length) {
                copyText($input.val(), $btn);
            }
        });
    }

    $(init);
})(jQuery);
