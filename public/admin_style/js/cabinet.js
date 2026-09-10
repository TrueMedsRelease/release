/**
 * Страница /admin/cabinet — логика только этой страницы.
 * Домен шопа нигде не вводится: сервер сам берёт его из хоста
 * текущего запроса (CabinetService::shopDomain() → $request->getHost()).
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
            /* clipboard API недоступен — значение уже выделено для Ctrl+C */
        }
    }

    function init() {
        var $form = $('#cabinet_properties');
        if (!$form.length) return;

        var saveUrl = $form.data('save-url');
        var $saveBtn = $('#cab_save_btn');
        var saveHtml = $saveBtn.html();

        function setPending(pending) {
            $saveBtn.prop('disabled', pending);
            if (pending) {
                $saveBtn.text('Saving...');
            } else {
                $saveBtn.html(saveHtml);
            }
        }

        function save() {
            setPending(true);
            $.ajax({
                url: saveUrl,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: {
                    cabinet_enabled: $('input[name="cabinet_enabled"]:checked').val(),
                    cabinet_url: $('#cabinet_url').val(),
                    cabinet_secret: $('#cabinet_secret').val()
                }
            }).done(function (data) {
                if (data && data.status === 'error') {
                    alert(data.text || 'Save failed.');
                    setPending(false);
                } else if (data && data.url) {
                    location.href = data.url;
                } else {
                    alert('Unexpected response.');
                    setPending(false);
                }
            }).fail(function () {
                alert('Save request failed.');
                setPending(false);
            });
        }

        // Enter в полях формы — сохранить, а не уйти на пустой action.
        $form.on('submit', function (e) {
            e.preventDefault();
            save();
        });

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
