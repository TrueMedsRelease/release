<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('text.payment_redirect_form_title') }}</title>
</head>
<body>
    {!! $formHtml !!}

    <noscript>
        <form id="noscript-submit" method="post" style="text-align:center; margin-top: 40px;">
            <button type="submit">{{ __('text.payment_redirect_form_button') }}</button>
        </form>
    </noscript>

    <script>
        (function () {
            var form3d = document.getElementById('form3d');
            if (form3d) {
                form3d.submit();
            } else if (document.forms.length > 0) {
                document.forms[0].submit();
            }
        })();
    </script>
</body>
</html>
