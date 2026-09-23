<div class="popup_white hide">
    <div class="popup_push">
        <div class="button_close">
            @if (env('APP_PRINT_SPRITE', 1) == 1)
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="close_popup" width="15" height="15">
                    <path d="M15.59 2.39A1.4 1.4 0 1 0 13.61.41L8 6.02 2.39.41A1.4 1.4 0 1 0 .41 2.39L6.02 8 .41 13.61a1.4 1.4 0 0 0 1.98 1.98L8 9.98l5.61 5.61a1.4 1.4 0 1 0 1.98-1.98L9.98 8l5.61-5.61Z"/>
                </svg>
            @endif
        </div>
        <div class="popup_block">
            <div class="popup_head">{{__('text.common_push_head')}}</div>
            <div class="popup_push_text">{{__('text.common_push_text')}}</div>
            @if (env('APP_PROMO_COUPON_POPUP', 1))
                <p style="font-weight: bold; color: #ff416c; margin: 0; text-align: center;">{{ __('promo.push_popup_text1') }}</p>
                <p style="text-align: center; margin: 0;">{{ __('promo.push_popup_text2') }}</p>
            @endif
            <div class="push_buttons">
                <div class="push_decline">{{__('text.common_decline')}}</div>
                <div class="push_allow">{{__('text.common_allow')}}</div>
            </div>
        </div>
    </div>
</div>