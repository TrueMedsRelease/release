@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <div class="main__content">
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.contact_us_sended')}}</p>
            </div>
            <h1 class="h1">{{__('text.affiliate_title')}}</h1>
            <form class="form affiliate-form form-panel">
                <fieldset class="form__fieldset">
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="affiliate-name" name="affiliate-name" required>
                        <label class="form__label label-text" for="affiliate-name">{{__('text.affiliate_name')}}</label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-email undefined" type="email" id="affiliate-email" name="affiliate-email" required>
                        <label class="form__label label-email" for="affiliate-email">{{__('text.affiliate_email')}}</label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="affiliate-messenger" name="affiliate-messenger">
                        <label class="form__label label-text" for="affiliate-messenger">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                    </div>
                </fieldset>
                <div class="form__field textarea-field">
                    <textarea class="form__text-input input-textarea" id="affiliate-message" name="affiliate-message" required></textarea>
                    <label class="form__label label-textarea" for="affiliate-message">{{__('text.affiliate_message')}}</label>
                </div>
                <div class="form__field custom-field">
                    <span class="captcha-label form__label">{{__('text.affiliate_code')}}</span>
                    <div class="captcha-wrapper">
                        <div class="captcha-img">
                            <img loading="lazy" src="{{ captcha_src() }}" id="captcha_image" style="border-radius: 1rem;">
                        </div>
                        <div class="form__field text-field">
                            <label class="form__label">
                                <input class="form__text-input" type="text" id="contact-captcha" required>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit button--secondary" type="button" onclick="sendAjaxAffiliate()">{{__('text.affiliate_send')}}</button>
                </div>
                <div class="form__field custom-field">
                    <div class="form__caption">
                        <p>{{__('text.affiliate_contact_message_2')}}</p>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <aside class="aside">
        <div class="accordion aside-nav" data-accordion>
            <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                        </svg>
                    </span>
                </summary>
                <div class="accordion-panel" data-accordion-panel>
                    <div class="accordion-content content">
                        <ul class="aside-nav__list">
                            @foreach ($bestsellers as $bestseller)
                                <li class="aside-nav__item">
                                    <a class="aside-nav__link" href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}
                                        <span class="aside-nav__price">{{ $Currency::convert($bestseller['price'], false, true) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </details>
            @foreach ($menu as $category)
                <details class="accordion-item @if($cur_category == $category['name']) is-open @endif" data-accordion-item @if($cur_category == $category['name']) open @endif>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                            </svg>
                        </span>
                    </summary>
                    <div class="accordion-panel" data-accordion-panel>
                        <div class="accordion-content content">
                            <ul class="aside-nav__list">
                                @foreach ($category['products'] as $item)
                                    <li class="aside-nav__item">
                                        <a class="aside-nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}
                                            <span class="aside-nav__price">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </aside>
</div>
@endsection

@section('rewies')
<div class="footer-testimonials">
    <div class="container">
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
</div>
@endsection