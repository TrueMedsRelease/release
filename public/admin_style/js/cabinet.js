/**
 * Страница /admin/cabinet — логика только этой страницы.
 * Домен шопа нигде не вводится: сервер сам берёт его из хоста
 * текущего запроса (CabinetService::shopDomain() → $request->getHost()).
 */
(function ($) {
    'use strict';

    function setResult($el, text, kind) {
        $el.text(text || '');
        $el.removeClass('cab-admin__result--success cab-admin__result--warning cab-admin__result--error');
        if (kind) {
            $el.addClass('cab-admin__result--' + kind);
        }
    }

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
            /* clipboard API недоступен — значение уже выделено для Ctrl+C */
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

        function setPending(pending) {
            $saveBtn.prop('disabled', pending);
            $testBtn.prop('disabled', pending);
        }

        function formData() {
            return {
                cabinet_enabled: $('input[name="cabinet_enabled"]:checked').val(),
                cabinet_url: $('#cabinet_url').val(),
                cabinet_secret: $('#cabinet_secret').val()
            };
        }

        function save() {
            setPending(true);
            setResult($result, 'Saving...', '');
            $.ajax({
                url: saveUrl,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: formData()
            }).done(function (data) {
                if (data && data.status === 'error') {
                    setResult($result, data.text || 'Save failed.', 'error');
                    setPending(false);
                } else if (data && data.url) {
                    location.href = data.url;
                } else {
                    setResult($result, 'Unexpected response.', 'error');
                    setPending(false);
                }
            }).fail(function () {
                setResult($result, 'Save request failed.', 'error');
                setPending(false);
            });
        }

        function test() {
            setPending(true);
            setResult($result, 'Checking...', '');
            $.ajax({
                url: testUrl,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: formData()
            }).done(function (data) {
                var kind = data.status === 'success' ? 'success' : (data.status === 'warning' ? 'warning' : 'error');
                setResult($result, (data && (data.text || data.status)) || 'No response.', kind);
                if (data && (data.status === 'success' || data.status === 'warning')) {
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    setPending(false);
                }
            }).fail(function () {
                setResult($result, 'Request failed.', 'error');
                setPending(false);
            });
        }

        // Enter в полях формы — сохранить, а не уйти на пустой action.
        $form.on('submit', function (e) {
            e.preventDefault();
            save();
        });
        $testBtn.on('click', test);

        // Показать/скрыть секрет.
        $('#cab_secret_toggle').on('click', function () {
            var $secret = $('#cabinet_secret');
            var show = $secret.attr('type') === 'password';
            $secret.attr('type', show ? 'text' : 'password');
            $(this).text(show ? 'Hide' : 'Show');
        });

        // Копирование домена/callback из инструкции.
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
