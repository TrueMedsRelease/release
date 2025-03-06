@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div class="text-page mb50">
	<h2 class="title-page">{{__('text.testimonials_title')}}</h2>
	<p>{!! __('text.testimonials_text') !!}</p>
	<div class="feedback-list">
		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_1')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_1')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_2')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_2')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_3')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_3')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_4')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_4')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_5')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_5')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_6')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_6')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_7')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_7')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_8')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_8')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_9')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_9')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_10')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_10')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_11')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_11')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_12')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_12')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_13')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_13')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_14')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_14')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_15')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_15')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_16')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_16')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_17')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_17')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_18')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_18')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_19')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_19')}}</p>
			</div>
		</div>

		<div class="item">
			<div class="head">
				<span class="name">{!!__('text.testimonials_author_t_20')!!}</span>
				<div class="stars">
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
					<span class="active"></span>
				</div>
			</div>
			<div class="text">
				<p>{{__('text.testimonials_t_20')}}</p>
			</div>
		</div>
	</div>
</div>

<div class="sale-banners">
    <div class="happy-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
            <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
        </span>
    </div>
    <div class="wow-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner2_text1')}} <br> {!!__('text.common_banner2_text2')!!}</span>
            <span class="text">{{__('text.common_banner2_text3')}} <br> {{__('text.common_banner2_text4')}}</span>
        </span>
    </div>
</div>

@endsection