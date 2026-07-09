@extends($design . '.layouts.main')

@section('body_data_page', 'checkout')

@section('content')
<div class="main__content">
    <div class="main__heading">
        <h1 class="h1"></h1>
        <a class="button button--white button--return" href="{{ route('cart.index') }}">
            <span class="icon">
                <svg width="1em" height="1em" fill="currentColor">
                    <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#arrow-left") }}"></use>
                </svg>
            </span>
            {{ __('text.checkout_back') }}
        </a>
    </div>
    <div class="checkout_wrapper">

	</div>
    <script>
        const checkoutIntlTelUtilsScript = "{{ asset($design . '/vendor/intl-tel/js/utils.js') }}";

        $(document).ready(function() {
            $(".ploader").hide();

            $.ajax({
                method: 'GET',
                data: {},
                url: "{{ route('checkout.content') }}",
                dataType: 'html',
                success: function(data) {
                    data = JSON.parse(data);

                    $('.checkout_wrapper').html(data.html);

                    initCheckoutAjaxContent(document.querySelector('.checkout_wrapper'));

                    document.body.classList.add('loaded_hiding');

                    window.setTimeout(function () {
                        document.body.classList.add('loaded');
                        document.body.classList.remove('loaded_hiding');
                    }, 500);
                }
            });
        });

        function initCheckoutAjaxContent(root) {
            if (!root) {
                return;
            }

            initCheckoutCustomSelects(root);
            initCheckoutTabs(root);
            initCheckoutIntlTel(root);
            initCheckoutPaymentSwitcher(root);
            initCheckoutCopyButtons(root);
        }

        function initCheckoutCustomSelects(root) {
            if (typeof customSelect === 'undefined') {
                return;
            }

            const selects = root.querySelectorAll('select.select:not([data-checkout-select-ready])');

            selects.forEach(function(select, index) {
                if (select.customSelect) {
                    select.dataset.checkoutSelectReady = '1';
                    return;
                }

                const validChildren = Array.from(select.children).every(function(child) {
                    return child.tagName === 'OPTION' || child.tagName === 'OPTGROUP';
                });

                if (!validChildren) {
                    return;
                }

                select.dataset.checkoutSelectReady = '1';

                const uid = 'checkout-select-' + Date.now() + '-' + index + '-' + Math.random().toString(16).slice(2);

                select.classList.add(uid);

                customSelect('select.' + uid);

                select.classList.remove(uid);
            });
        }

        function initCheckoutTabs(root) {
            const tabsBlocks = root.querySelectorAll('[data-tabs]:not([data-checkout-tabs-ready])');

            tabsBlocks.forEach(function(tabsBlock) {
                tabsBlock.dataset.checkoutTabsReady = '1';

                const buttons = Array.from(tabsBlock.querySelectorAll('[data-tabs-button]'));
                const items = Array.from(tabsBlock.querySelectorAll('[data-tabs-item]'));

                function activateTab(index) {
                    buttons.forEach(function(button, buttonIndex) {
                        const isActive = buttonIndex === index;

                        button.classList.toggle('is-active', isActive);
                        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    items.forEach(function(item, itemIndex) {
                        const isActive = itemIndex === index;

                        item.classList.toggle('is-active', isActive);

                        const panel = item.querySelector('[data-tabs-panel]');

                        if (panel) {
                            panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                        }
                    });
                }

                buttons.forEach(function(button, index) {
                    button.addEventListener('click', function() {
                        activateTab(index);
                    });
                });

                const activeIndex = buttons.findIndex(function(button) {
                    return button.classList.contains('is-active');
                });

                activateTab(activeIndex >= 0 ? activeIndex : 0);
            });
        }

        function initCheckoutIntlTel(root) {
            if (typeof intlTelInput === 'undefined') {
                return;
            }

            const countryIsoInput = document.querySelector('#country_iso');
            const initialCountryInput = document.querySelector('#initial_country');

            let onlyCountries = [];

            try {
                onlyCountries = JSON.parse(countryIsoInput ? countryIsoInput.value : '[]');
            } catch (e) {
                onlyCountries = [];
            }

            const initialCountry = initialCountryInput ? initialCountryInput.value : 'us';

            const inputs = root.querySelectorAll('.intl-phone:not([data-checkout-intl-ready])');

            inputs.forEach(function(input) {
                input.dataset.checkoutIntlReady = '1';

                intlTelInput(input, {
                    utilsScript: checkoutIntlTelUtilsScript,
                    useFullscreenPopup: false,
                    showSelectedDialCode: true,
                    initialCountry: initialCountry,
                    onlyCountries: onlyCountries
                });
            });
        }

        function initCheckoutPaymentSwitcher(root) {
            const paymentSelect = root.querySelector('.payment-select');

            if (!paymentSelect || paymentSelect.dataset.checkoutPaymentReady === '1') {
                return;
            }

            paymentSelect.dataset.checkoutPaymentReady = '1';

            function switchPaymentMethod(hiddenSelector, shownSelector, submitText) {
                const parent = paymentSelect.closest('.payment-information');

                if (!parent) {
                    return;
                }

                parent.querySelectorAll(hiddenSelector).forEach(function(field) {
                    field.classList.add('hidden-field');

                    field.querySelectorAll('input, select, textarea').forEach(function(input) {
                        input.setAttribute('disabled', '');
                    });
                });

                parent.querySelectorAll(shownSelector).forEach(function(field) {
                    field.classList.remove('hidden-field');

                    field.querySelectorAll('input, select, textarea').forEach(function(input) {
                        input.removeAttribute('disabled');
                    });
                });

                const submitButtonText = root.querySelector('.submit-button .button-text');

                if (submitButtonText) {
                    submitButtonText.textContent = submitText;
                }
            }

            function updatePaymentMethod() {
                if (paymentSelect.value === 'crypto') {
                    switchPaymentMethod(
                        '.payment-information__card-field',
                        '.payment-information__crypto-field',
                        'I have paid'
                    );
                }

                if (paymentSelect.value === 'card') {
                    switchPaymentMethod(
                        '.payment-information__crypto-field',
                        '.payment-information__card-field',
                        'Place the order'
                    );
                }
            }

            paymentSelect.addEventListener('change', updatePaymentMethod);

            updatePaymentMethod();
        }

        function initCheckoutCopyButtons(root) {
            const copyButtons = root.querySelectorAll('.copy-button:not([data-checkout-copy-ready])');

            copyButtons.forEach(function(button) {
                button.dataset.checkoutCopyReady = '1';

                button.addEventListener('click', function() {
                    const field = button.closest('.copy-field');

                    if (!field) {
                        return;
                    }

                    const textElement = field.querySelector('.copy-text');

                    if (!textElement) {
                        return;
                    }

                    const text = textElement.textContent.trim();

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text);

                        const buttonText = button.querySelector('.button-text');

                        if (buttonText) {
                            buttonText.classList.add('is-visible');

                            setTimeout(function() {
                                buttonText.classList.remove('is-visible');
                            }, 1500);
                        }
                    }
                });
            });
        }
    </script>
</div>
@endsection