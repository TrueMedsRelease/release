{{--
    Плашка «Добавьте ещё $X для бесплатной доставки» для выбранного способа доставки.
    Подключается в ajax/cart_content каждого дизайна через @include('shared.free_ship_progress').
    Ожидает в области видимости: $shipping, $product_total_check, $Currency.

    Внешний вид дизайн задаёт CSS-переменными на .free-ship-progress в своём style.css:
      --fsp-bg, --fsp-border, --fsp-radius, --fsp-padding, --fsp-margin, --fsp-text,
      --fsp-font-size, --fsp-accent (цвет суммы и процента), --fsp-bar (заливка полосы),
      --fsp-track (фон полосы), --fsp-height (толщина полосы).

    При перерисовке корзины полоса и процент плавно анимируются от прошлого значения к новому.
--}}
@php
    $fsp_name = session('cart_option.shipping', env('APP_DEFAULT_SHIPPING'));
    $fsp_threshold = ['ems' => 300, 'regular' => 200][$fsp_name] ?? null;
@endphp
@if ($fsp_threshold && !empty($shipping[$fsp_name]))
    @php
        $fsp_left = max(0, $fsp_threshold - $product_total_check);
        $fsp_percent = min(100, max(0, (int) floor($product_total_check / $fsp_threshold * 100)));
        $fsp_label = __('text.cart_free_' . ($fsp_name == 'ems' ? 'express' : 'regular'));
    @endphp
    <div class="free-ship-progress @if ($fsp_left == 0) is-done @endif" data-fsp="{{ $fsp_percent }}">
        <div class="free-ship-progress__head">
            <span class="free-ship-progress__text">
                @if ($fsp_left > 0)
                    {!! __('text.cart_free_left', ['amount' => '<b>' . e($Currency::convert($fsp_left, false, true)) . '</b>', 'delivery' => e($fsp_label)]) !!}
                @else
                    &#10003; {{ $fsp_label }}
                @endif
            </span>
            <span class="free-ship-progress__percent" data-fsp-percent>{{ $fsp_percent }}%</span>
        </div>
        <div class="free-ship-progress__track">
            <div class="free-ship-progress__bar" style="width: {{ $fsp_percent }}%"></div>
        </div>
    </div>
    <style>
        .free-ship-progress{box-sizing:border-box;margin:var(--fsp-margin,0 0 16px);padding:var(--fsp-padding,14px 18px 16px);border:var(--fsp-border,1px solid #e3e3e3);border-radius:var(--fsp-radius,10px);background:var(--fsp-bg,#fff);color:var(--fsp-text,inherit);font-size:var(--fsp-font-size,14px);line-height:1.35;text-align:left}
        .free-ship-progress__head{display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:10px;font-weight:500}
        .free-ship-progress__text b,.free-ship-progress__percent,.free-ship-progress.is-done .free-ship-progress__text{color:var(--fsp-accent,#0eba63);font-weight:600}
        .free-ship-progress__percent{flex-shrink:0;font-variant-numeric:tabular-nums}
        .free-ship-progress__track{height:var(--fsp-height,8px);border-radius:999px;overflow:hidden;background:var(--fsp-track,#e9ecef)}
        .free-ship-progress__bar{height:100%;border-radius:inherit;background:var(--fsp-bar,var(--fsp-accent,#28d17c));transition:width .8s cubic-bezier(.22,.61,.36,1)}
        @media (prefers-reduced-motion: reduce){.free-ship-progress__bar{transition:none}}
    </style>
    <script>
        (function () {
            var els = document.querySelectorAll('[data-fsp]:not([data-fsp-ready])');
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            for (var i = 0; i < els.length; i++) animate(els[i]);

            function animate(el) {
                el.setAttribute('data-fsp-ready', '');
                var target = +el.getAttribute('data-fsp') || 0;
                var from = typeof window.__fspPrev === 'number' ? window.__fspPrev : 0;
                window.__fspPrev = target;
                var bar = el.querySelector('.free-ship-progress__bar');
                var label = el.querySelector('[data-fsp-percent]');
                if (reduce || from === target || !bar) return;

                // Полоса: ставим прошлую ширину без анимации, затем включаем transition к новой.
                bar.style.transition = 'none';
                bar.style.width = from + '%';
                bar.offsetWidth;
                bar.style.transition = '';
                requestAnimationFrame(function () { bar.style.width = target + '%'; });

                // Процент досчитывается синхронно с полосой.
                if (!label) return;
                var start = null, dur = 800;
                label.textContent = from + '%';
                requestAnimationFrame(function step(ts) {
                    if (start === null) start = ts;
                    var t = Math.min(1, (ts - start) / dur);
                    var eased = 1 - Math.pow(1 - t, 3);
                    label.textContent = Math.round(from + (target - from) * eased) + '%';
                    if (t < 1) requestAnimationFrame(step);
                });
            }
        })();
    </script>
@endif
