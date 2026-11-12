<div class="ck-switcher" data-ck-switcher>
    <span class="ck-switcher__title">Checkout skin</span>
    @foreach (\App\Services\CheckoutSkin::all() as $skinId => $skin)
        <a class="ck-switcher__item @if ($skinId === \App\Services\CheckoutSkin::id()) is_active @endif"
           href="{{ route('checkout.index') }}?checkout_skin={{ $skinId }}"
           title="{{ implode(', ', $skin['flags']) ?: 'original' }}">
            {{ $skinId }}. {{ $skin['name'] }}
        </a>
    @endforeach
</div>
