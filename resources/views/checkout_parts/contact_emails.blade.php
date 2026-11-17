                        <div class="enter-info__row">
                            <div class="enter-info__input poopup">
                                <label for="email" class="enter-info__label">{{__('text.checkout_email')}}</label>
                                <span class="poopuptext" id="error_email">{{__('text.checkout_wrong_email')}}</span>
                                <input id="email" required autocomplete="off" type="email" name="email" class="input" value="{{ session('form.email', '') }}">
                            </div>
                            <div class="enter-info__input poopup ck-optional">
                                <span class="poopuptext" id="error_alt_email">{{__('text.checkout_wrong_email')}}</span>
                                <label for="alt_email" class="enter-info__label">{{__('text.checkout_email2')}}</label>
                                <input id="alt_email" autocomplete="off" type="email" name="alt_email" class="input" value="{{ session('form.alt_email', '') }}">
                            </div>
                        </div>
