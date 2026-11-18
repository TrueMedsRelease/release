{{--
    Плашка «Добавьте ещё $X для бесплатной доставки».
    Подключается в ajax/cart_content каждого дизайна через @include('shared.free_ship_progress').
    Ожидает в области видимости: $shipping, $product_total_check, $Currency.

    Отключение: APP_FREE_SHIP_PROGRESS=0 в .env (по умолчанию включена).

    Режимы:
      • сумма < 200 — одна полоса до порога выбранной доставки (Regular 200 / Express 300);
      • сумма ≥ 200 и доступны обе доставки — полоса из двух половин: левая — бесплатная
        Regular (достигнута, на стыке отметка ✓), правая — путь до бесплатной Express.

    Внешний вид дизайн задаёт CSS-переменными на .free-ship-progress в своём style.css:
      --fsp-bg, --fsp-border, --fsp-radius, --fsp-padding, --fsp-margin, --fsp-text,
      --fsp-font-size, --fsp-accent (цвет суммы и процента), --fsp-bar (заливка полосы),
      --fsp-track (фон полосы), --fsp-height (толщина полосы), --fsp-marker-text (галочка на стыке).

    При перерисовке корзины полосы и процент плавно анимируются от прошлых значений к новым.
--}}
@php
    $fsp_thresholds = ['regular' => 200, 'ems' => 300];
    $fsp_name = session('cart_option.shipping', env('APP_DEFAULT_SHIPPING'));
    $fsp_staged = !empty($shipping['regular']) && !empty($shipping['ems']) && $product_total_check >= $fsp_thresholds['regular'];
    if ($fsp_staged) {
        $fsp_name = 'ems';
    }

    $fsp_threshold = env('APP_FREE_SHIP_PROGRESS', 1) ? ($fsp_thresholds[$fsp_name] ?? null) : null;
@endphp
@if ($fsp_threshold && !empty($shipping[$fsp_name]))
    @php
        $fsp_left = max(0, $fsp_threshold - $product_total_check);
        $fsp_percent = min(100, max(0, (int) floor($product_total_check / $fsp_threshold * 100)));
        $fsp_label = __('text.cart_free_' . ($fsp_name == 'ems' ? 'express' : 'regular'));
        // Заполнение второй половины: путь от 200 до 300.
        $fsp_stage2 = $fsp_staged
            ? min(100, (int) floor(($product_total_check - $fsp_thresholds['regular']) / ($fsp_thresholds['ems'] - $fsp_thresholds['regular']) * 100))
            : 0;
    @endphp
    <div class="free-ship-progress @if ($fsp_staged) is-staged @endif @if ($fsp_left == 0) is-done @endif" data-fsp>
        <div class="free-ship-progress__head">
            <span class="free-ship-progress__text">
                @if ($fsp_left > 0)
                    {!! __('text.cart_free_left', ['amount' => '<b>' . e($Currency::convert($fsp_left, false, true)) . '</b>', 'delivery' => e($fsp_label)]) !!}
                @else
                    &#10003; {{ $fsp_label }}
                @endif
            </span>
            @unless ($fsp_staged)
                <span class="free-ship-progress__percent" data-fsp-percent="{{ $fsp_percent }}">{{ $fsp_percent }}%</span>
            @endunless
        </div>

        @if ($fsp_staged)
            <div class="free-ship-progress__stages">
                <div class="free-ship-progress__track">
                    <div class="free-ship-progress__bar" data-fsp-bar="a" style="width: 100%"></div>
                </div>
                <span class="free-ship-progress__marker" aria-hidden="true">&#10003;</span>
                <div class="free-ship-progress__track">
                    <div class="free-ship-progress__bar" data-fsp-bar="b" style="width: {{ $fsp_stage2 }}%"></div>
                </div>
            </div>
            <div class="free-ship-progress__labels">
                <span class="free-ship-progress__label is-reached">&#10003; {{ __('text.cart_free_regular') }}</span>
                <span class="free-ship-progress__label @if ($fsp_left == 0) is-reached @endif">@if ($fsp_left == 0)&#10003; @endif{{ __('text.cart_free_express') }}</span>
            </div>
        @else
            <div class="free-ship-progress__track">
                <div class="free-ship-progress__bar" data-fsp-bar="a" style="width: {{ $fsp_percent }}%"></div>
            </div>
        @endif
    </div>
    <style>
        .free-ship-progress{box-sizing:border-box;margin:var(--fsp-margin,0 0 16px);padding:var(--fsp-padding,14px 18px 16px);border:var(--fsp-border,1px solid #e3e3e3);border-radius:var(--fsp-radius,10px);background:var(--fsp-bg,#fff);color:var(--fsp-text,inherit);font-size:var(--fsp-font-size,14px);line-height:1.35;text-align:left}
        .free-ship-progress__head{display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:10px;font-weight:500}
        .free-ship-progress__text b,.free-ship-progress__percent,.free-ship-progress.is-done .free-ship-progress__text{color:var(--fsp-accent,#0eba63);font-weight:600}
        .free-ship-progress__percent{flex-shrink:0;font-variant-numeric:tabular-nums}
        .free-ship-progress__track{height:var(--fsp-height,8px);border-radius:999px;overflow:hidden;background:var(--fsp-track,#e9ecef)}
        .free-ship-progress__bar{height:100%;border-radius:inherit;background:var(--fsp-bar,var(--fsp-accent,#28d17c));transition:width .8s cubic-bezier(.22,.61,.36,1)}
        .free-ship-progress__stages{display:flex;align-items:center;gap:4px}
        .free-ship-progress__stages .free-ship-progress__track{flex:1 1 0}
        .free-ship-progress__marker{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:var(--fsp-bar,var(--fsp-accent,#28d17c));color:var(--fsp-marker-text,#fff);font-size:11px;font-weight:700;line-height:1}
        .free-ship-progress__labels{display:flex;margin-top:6px;font-size:.85em;opacity:.8}
        .free-ship-progress__label{flex:1 1 0;text-align:right;padding-left:8px}
        .free-ship-progress__label:first-child{padding-right:13px}
        .free-ship-progress__label.is-reached{color:var(--fsp-accent,#0eba63);font-weight:600;opacity:1}
        @media (prefers-reduced-motion: reduce){.free-ship-progress__bar{transition:none}}
    </style>
    <script>
        (function () {
            var els = document.querySelectorAll('[data-fsp]:not([data-fsp-ready])');
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var prev = window.__fspPrev || {};
            window.__fspPrev = prev;
            for (var i = 0; i < els.length; i++) setup(els[i]);

            function setup(el) {
                el.setAttribute('data-fsp-ready', '');

                // Полосы: «a» — одиночная полоса или левая половина, «b» — правая половина.
                // Если половины сейчас нет, её прошлое значение сбрасываем в 0.
                var seen = {};
                var bars = el.querySelectorAll('[data-fsp-bar]');
                for (var j = 0; j < bars.length; j++) {
                    var key = bars[j].getAttribute('data-fsp-bar');
                    seen[key] = true;
                    animateBar(bars[j], key);
                }
                if (!seen.b) prev.b = 0;

                var label = el.querySelector('[data-fsp-percent]');
                if (label) animateNumber(label);
            }

            function animateBar(bar, key) {
                var target = parseFloat(bar.style.width) || 0;
                var from = typeof prev[key] === 'number' ? prev[key] : 0;
                prev[key] = target;
                if (reduce || from === target) return;
                bar.style.transition = 'none';
                bar.style.width = from + '%';
                bar.offsetWidth;
                bar.style.transition = '';
                requestAnimationFrame(function () { bar.style.width = target + '%'; });
            }

            function animateNumber(label) {
                var target = +label.getAttribute('data-fsp-percent') || 0;
                var from = typeof prev.pct === 'number' ? prev.pct : 0;
                prev.pct = target;
                if (reduce || from === target) return;
                var start = null, dur = 800;
                label.textContent = from + '%';
                requestAnimationFrame(function step(ts) {
                    if (start === null) start = ts;
                    var t = Math.min(1, (ts - start) / dur);
                    label.textContent = Math.round(from + (target - from) * (1 - Math.pow(1 - t, 3))) + '%';
                    if (t < 1) requestAnimationFrame(step);
                });
            }
        })();
    </script>
@endif
