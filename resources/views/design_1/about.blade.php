
@extends($design . '.layouts.main')

@section('title', __('text.about_us_title'))

@section('content')
<div class="main">
    <!-- content -->
    <section class="page__text-block text-block">
        <div class="text-block__container">
            <h2 class="text-block__title title" id="scroll">{{__('text.about_us_title')}}</h2>
            <div class="text-block__body">
                {!!__('text.about_us_text')!!}
                <h3 class = "ship_">{{__('text.about_us_title1')}}</h3>
                <p>{{__('text.about_us_text1')}}</p>
                <h3 class = "ship_">{{__('text.about_us_title2')}}</h3>
                <ul>{{__('text.about_us_text2_1')}}</ul>
                    <li>{{__('text.about_us_text2_2')}}</li>
                    <li>{{__('text.about_us_text2_3')}}</li>
                    <p></p>
                <ul>{{__('text.about_us_text2_4')}}</ul>
                    <li>{{__('text.about_us_text2_5')}}</li>
                    <li>{{__('text.about_us_text2_6')}}</li>
                    <p></p>
                <h3 class = "ship_">{{__('text.about_us_title3')}}</h3>
                <p>{{__('text.about_us_text3_1')}}</p>
                <p>{{__('text.about_us_text3_2')}}</p>
                <ul>{{__('text.about_us_text3_3')}}</ul>
                    <li>{{__('text.about_us_text3_4')}}</li>
                    <li>{{__('text.about_us_text3_5')}}</li>
                    <p></p>
                <p>{{__('text.about_us_text3_6')}}</p>
                <h3 class = "ship_">{{__('text.about_us_title4')}}</h3>
                <p>{{__('text.about_us_text4')}}</p>
            </div>
        </div>
    </section>
    </div>
                        </div>
                    </div>
                </div>

@endsection