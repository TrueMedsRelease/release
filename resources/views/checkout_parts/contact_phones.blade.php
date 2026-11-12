                        <div class="enter-info__row">
                            <div class="enter-info__line poopup">
                                <span class="poopuptext" id="error_phone">{{__('text.checkout_wrong_phone')}}</span>
                                <div class="enter-info__country phone_code">
                                    <select name="phone_code" class="form" id="phone_code_select"
                                        data-pseudo-label="{{__('text.checkout_phone')}}" data-scroll="300" data-class-modif="code"
                                        data-search-placeholder="{{ __('text.checkout_code_search') }}" data-search-empty="{{ __('text.checkout_code_not_found') }}">
                                        @foreach ($phone_codes as $item)
                                            <option id=""
                                            @if (empty(session('form')))
                                                 @selected($item['iso'] == session('location.country', ''))
                                            @else
                                                @selected($item['iso'] == session('form.phone_code', ''))
                                            @endif
                                                data-name="{{ $item['nicename'] }}" data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}" value="{{ $item['iso'] }}">
                                                +{{ $item['phonecode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input required autocomplete="off" type="number" id="phone" name="phone"
                                        value="{{ session('form.phone', '') }}"
                                        placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                            </div>
                            <div class="enter-info__line poopup ck-optional">
                                <span class="poopuptext" id="error_alt_phone">{{__('text.checkout_wrong_phone')}}</span>
                                <div class="enter-info__country alt_phone_code">
                                    <select name="alt_phone_code" class="form" id="phone_code_select"
                                        data-pseudo-label="{{__('text.checkout_alt_phone')}}" data-scroll="300" data-class-modif="code"
                                        data-search-placeholder="{{ __('text.checkout_code_search') }}" data-search-empty="{{ __('text.checkout_code_not_found') }}">
                                        @foreach ($phone_codes as $item)
                                            <option id=""
                                            @if (empty(session('form')))
                                                @selected($item['iso'] == session('location.country', ''))
                                            @else
                                                @selected($item['iso'] == session('form.alt_phone_code', ''))
                                            @endif
                                                data-name="{{ $item['nicename'] }}" data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
                                                value="{{ $item['iso'] }}">
                                                +{{ $item['phonecode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input autocomplete="off" type="number" id="alt_phone" name="alt_phone"
                                        value="{{ session('form.alt_phone', '') }}"
                                        placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                            </div>
                        </div>
