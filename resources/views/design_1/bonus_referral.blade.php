@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="main">
    <section class="page__text-block text-block">
        <div class="text-block__container">
            <h2 class="text-block__title title" id="scroll">{{ __('text.bonus_ref_menu') }}</h2>
            <div class="text-block__body">
                <h3 class = "ship_">{{ __('text.bonus_page_bonus') }}</h3>
                <br>
                <p>{{ __('text.bonus_page_text1') }}</p>
                <p style="line-height: 1.5">
                    {{ __('text.bonus_page_text2') }}
                </p>
                <ul style="line-height: 1.5">
                    {{ __('text.bonus_page_text3') }}
                    <li>1) {{ __('text.bonus_page_text4') }}</li>
                    <li>2) {{ __('text.bonus_page_text5') }}</li>
                    <li>3) {{ __('text.bonus_page_text6') }}</li>
                </ul>
                <br>
                <p style="line-height: 1.8">
                    {!! __('text.bonus_page_text7') !!}
                </p>
                <p style="line-height: 1.8">
                    {!! __('text.bonus_page_text8') !!}
                </p>
                <p><b>{{ __('text.bonus_page_text9') }}</b></p>

                <h3 class = "ship_">{{ __('text.bonus_page_referral') }}</h3>
                <br>
                <p style="line-height: 1.5">
                    {!! __('text.bonus_page_text10') !!}
                </p>
                <p style="line-height: 1.5">
                    {{ __('text.bonus_page_text11') }}
                </p>
            </div>
        </div>
    </section>
</div>
</div>
</div>
</div>

@endsection