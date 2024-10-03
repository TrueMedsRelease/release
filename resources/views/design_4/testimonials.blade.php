@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<h1 class="content__title title" id="scroll">{{__('text.testimonials_title')}}</h1>
<div class="reviews-page">
    <p class="reviews-page__descr">{!! __('text.testimonials_text') !!}</p>
    <div class="reviews-page__items">
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_1')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_1')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_2')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_2')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_3')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_3')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_4')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_4')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_5')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_5')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_6')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_6')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_7')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_7')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_8')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_8')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_9')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_9')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_10')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_10')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_11')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_11')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_12')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_12')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_13')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_13')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_14')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_14')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_15')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_15')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_16')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_16')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_17')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_17')}}</div>
    	</div>
    	<div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_18')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_18')}}</div>
    	</div>
        <div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_19')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_19')}}</div>
    	</div>
        <div class="review-item">
    		<div class="review-item__top">
    			<div class="review-item__name">{!!__('text.testimonials_author_t_20')!!}</div>
    			<div class="review-item__stars">
    				<svg width="98" height="18">
    					<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
    				</svg>
    			</div>
    		</div>
    		<div class="review-item__text">{{__('text.testimonials_t_20')}}</div>
    	</div>
    </div>
</div>

@endsection