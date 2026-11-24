{{--
    Coupon row under a package row of the price table: [−10%] Price with coupon  $108.
    Used right after the package row, inside the same @foreach:
      table designs: @include('shared.coupon_row', ['couponBase' => $item['price'], 'couponProductId' => $product['id'], 'couponColspan' => 2])
                     (the package cell and the button cell get rowspan="2", the <tr> gets class product--has-coupon)
      div designs:   @include('shared.coupon_row', ['couponBase' => ..., 'couponProductId' => ..., 'couponAs' => 'div'])
      css:           @include('shared.coupon_row', ['couponAs' => 'css']) — styles only (JS-rendered tables)
      cell:          a <td class="product__coupon-cell"> INSIDE the package <tr> (before the button cell). Hidden on desktop,
                     shown by the design's css on phones, where the package row is a grid and the coupon must sit above the button.
    Optional: 'couponRowClass' — extra class for the <tr> (default 'product', pass '' when the design styles .product).
--}}
@php
    $crCssOnly = ($couponAs ?? 'tr') === 'css'; // styles only, for rows rendered by JS (design_17 chat)
    $crDiscount = $crCssOnly ? 0 : \App\Services\CouponService::discount((float) $couponBase, $couponProductId ?? null);
@endphp
@if ($crDiscount > 0 || ($crCssOnly && \App\Services\CouponService::active()))
    @include('shared.coupon_tag', ['css' => true])
    @once
        <style>
            /* the package row and its coupon row read as one block: no line between them,
               and the coupon cell is not the table's left edge (the package cell spans both rows) */
            tr.product--has-coupon > td:not([rowspan]),
            tr.product--has-coupon > th:not([rowspan]) { border-bottom: 0 !important; padding-bottom: 4px; }
            /* flex-row tables (design_2/3/4/9): no row padding / min-height between a package and its coupon box
               (padding and min-height do nothing on real table rows, so table designs are unaffected) */
            tr.product--has-coupon { padding-bottom: 0 !important; min-height: 0 !important; }
            tr.product--coupon { padding-top: 0 !important; min-height: 0 !important; }
            tr.product--coupon > td { padding-top: 0; padding-right: 14px !important; border-top: 0 !important; border-left: 0 !important; border-right: 0 !important; vertical-align: middle;
                background: transparent !important; /* zebra rules (tr:nth-child(even) td) would paint only the coupon row */ }
            /* flex-row tables (design_2/3/4/9): the coupon cell lives inside the package row, which becomes a
               2-row grid — first cell (package) and the button cell span both rows, the coupon sits under the
               middle columns. Column widths are measured from the normal flex layout by the script below. */
            .product--coupon-grid:not(.coupon-measure) { display: grid !important; grid-template-columns: repeat(4, auto); justify-content: space-between; align-items: center; row-gap: 6px; }
            .product--coupon-grid:not(.coupon-measure) > th, .product--coupon-grid:not(.coupon-measure) > td { width: auto !important; min-width: 0; }
            .product--coupon-grid:not(.coupon-measure) > :first-child,
            .product--coupon-grid:not(.coupon-measure) > [class*="__btn"] { grid-row: 1 / span 2; }
            .product--coupon-grid > .product__coupon-cell { grid-column: 2 / -2; grid-row: 2; display: block; padding: 0; border: 0; margin-right: 10px; }
            .product--coupon-grid > .product__coupon-cell .coupon-row { margin: 0; }
            .product--coupon-grid.coupon-measure > .product__coupon-cell { display: none; }
            .coupon-row-wrap { display: block; padding: 0 10px 4px; box-sizing: border-box; }
            .coupon-row {
                display: flex; align-items: center; gap: 10px;
                margin: 0 0 6px; padding: 7px 12px; border-radius: 10px;
                background: rgba(46, 158, 79, .08);
                background: color-mix(in srgb, var(--coupon-color, #2e9e4f) 10%, transparent);
                font-size: 14px; line-height: 1.3; color: inherit; text-align: start;
            }
            .coupon-row__label { flex: 1 1 auto; min-width: 0; opacity: .75; font-style: normal; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .coupon-row__sum { flex: none; font-size: 16px; font-weight: 700; white-space: nowrap; }
            .coupon-row--narrow { justify-content: space-between; }
            .coupon-row--narrow .coupon-row__label { display: none; }
            .coupon-row .coupon-tag { flex: none; font-size: 12px; padding: 3px 7px 3px 6px; }
            /* phones: the tag already says "coupon −10%"; keep tag + price on one line */
            @media (max-width: 480px) {
                .coupon-row { justify-content: space-between; gap: 6px; padding: 6px 8px; }
                .coupon-row__label { display: none; }
                .coupon-row__sum { font-size: 15px; }
            }
        </style>
        <script>
            // Designs whose package rows are flex boxes (design_2/3/4/9) ignore rowspan/colspan:
            // place the coupon box exactly under the Per pill + Special price cells of the row above.
            (function () {
                function align() {
                    document.querySelectorAll('tr.product--coupon').forEach(function (tr) {
                        var prev = tr.previousElementSibling; while (prev && prev.tagName !== 'TR') prev = prev.previousElementSibling;
                        var td = tr.firstElementChild, box = tr.querySelector('.coupon-row');
                        if (!prev || !td || !box) return;
                        if (getComputedStyle(prev).display.indexOf('table') === 0) {
                            tr.style.display = td.style.display = box.style.marginLeft = box.style.width = '';
                            return;
                        }
                        var cells = Array.prototype.filter.call(prev.children, function (c) {
                            return /^T[DH]$/.test(c.tagName) && !c.hasAttribute('rowspan') && getComputedStyle(c).position !== 'absolute' && c.getBoundingClientRect().width > 0;
                        });
                        if (!cells.length) return;
                        tr.style.display = 'block'; td.style.display = 'block';
                        box.style.marginLeft = box.style.width = '';
                        var l = cells[0].getBoundingClientRect().left, r = cells[cells.length - 1].getBoundingClientRect().right;
                        var base = box.getBoundingClientRect().left - parseFloat(getComputedStyle(box).marginLeft || 0);
                        box.style.marginLeft = Math.max(0, l - base) + 'px';
                        box.style.width = Math.max(0, r - l) + 'px';
                        box.style.boxSizing = 'border-box';
                    });
                }
                // div designs (design_5/7/8): the box sits after the package row; align it under the
                // middle columns (per pill + price) while the row is one line, full width when stacked.
                function alignDiv() {
                    document.querySelectorAll('.coupon-row-wrap').forEach(function (wrap) {
                        var box = wrap.querySelector('.coupon-row'), prev = wrap.previousElementSibling;
                        while (prev && /^(STYLE|SCRIPT|LINK)$/.test(prev.tagName)) prev = prev.previousElementSibling;
                        if (!box || !prev) return;
                        box.style.marginLeft = box.style.width = '';
                        var kids = Array.prototype.filter.call(prev.children, function (c) {
                            return c.getBoundingClientRect().width > 0 && getComputedStyle(c).position !== 'absolute';
                        });
                        if (kids.length < 3) return;
                        var first = kids[0].getBoundingClientRect(), mid = kids[1].getBoundingClientRect(), last = kids[kids.length - 2].getBoundingClientRect();
                        if (mid.top >= first.bottom - 2) return; // stacked (phones): keep full width
                        var base = box.getBoundingClientRect().left;
                        box.style.boxSizing = 'border-box';
                        box.style.marginLeft = Math.max(0, mid.left - base) + 'px';
                        box.style.width = Math.max(0, last.right - mid.left) + 'px';
                    });
                }
                // too narrow for "With coupon" next to the tag and the price: show tag + price only
                function narrow() {
                    document.querySelectorAll('.coupon-row').forEach(function (box) {
                        box.classList.remove('coupon-row--narrow');
                        if (box.getBoundingClientRect().width < 250) box.classList.add('coupon-row--narrow');
                    });
                }
                // design_7/8 price rows (.table_row) with the coupon inside: the row is a 2-row grid (design css);
                // give the grid exactly the column widths the normal flex row has.
                function gridRows() {
                    document.querySelectorAll('.table_row > .coupon-row-wrap, .product--coupon-grid > .product__coupon-cell').forEach(function (wrap) {
                        var row = wrap.parentElement;
                        row.style.gridTemplateColumns = '';
                        if (!row.getBoundingClientRect().width) return; // hidden tab: measured when shown
                        row.classList.add('coupon-measure');
                        var cols = Array.prototype.filter.call(row.children, function (c) { return c !== wrap && !/^(STYLE|SCRIPT|LINK)$/.test(c.tagName); })
                            .map(function (c) { var st = getComputedStyle(c); // margin box: cells keep their own margins in the grid
                                return (c.getBoundingClientRect().width + (parseFloat(st.marginLeft) || 0) + (parseFloat(st.marginRight) || 0)).toFixed(2) + 'px'; });
                        row.classList.remove('coupon-measure');
                        row.style.gridTemplateColumns = cols.join(' ');
                    });
                }
                // the coupon row can be the table's last row: the design's "last row" bottom border / corner radius
                // then lands only under the coupon box. Give it to the package and button cells (rowspan) too.
                function lastRows() {
                    document.querySelectorAll('tbody > tr.product--coupon:last-child').forEach(function (tr) {
                        if (tr.getAttribute('data-coupon-last')) return;
                        var td = tr.firstElementChild, prev = tr.previousElementSibling;
                        while (prev && prev.tagName !== 'TR') prev = prev.previousElementSibling;
                        if (!td || !prev) return;
                        var st = getComputedStyle(td), spans = Array.prototype.filter.call(prev.children, function (c) { return c.hasAttribute('rowspan'); });
                        if (!spans.length) return;
                        tr.setAttribute('data-coupon-last', '1');
                        var border = st.borderBottomWidth + ' ' + st.borderBottomStyle + ' ' + st.borderBottomColor;
                        spans.forEach(function (c) { c.style.borderBottom = border; });
                        spans[0].style.borderBottomLeftRadius = st.borderBottomLeftRadius;
                        spans[spans.length - 1].style.borderBottomRightRadius = st.borderBottomRightRadius;
                        td.style.borderBottomLeftRadius = td.style.borderBottomRightRadius = '0';
                    });
                }
                // Put the final price ("Only $328") right above the coupon price ("$295"): shift the content of the
                // Special price cell right so the last price's right edge meets the coupon sum's right edge.
                function notMeta(c) { return !/^(STYLE|SCRIPT|LINK)$/.test(c.tagName); }
                function priceCellOf(box) {
                    var w = box.closest('tr.product--coupon, .product__coupon-cell, .coupon-row-wrap');
                    if (!w) return null;
                    var row, skip = null, cells;
                    if (w.tagName === 'TR' || (w.classList.contains('coupon-row-wrap') && !w.parentElement.matches('.item, .table_row'))) {
                        row = w.previousElementSibling;
                        while (row && !notMeta(row)) row = row.previousElementSibling;
                    } else { row = w.parentElement; skip = w; }
                    if (!row) return null;
                    cells = Array.prototype.filter.call(row.children, function (c) {
                        return c !== skip && notMeta(c) && c.getBoundingClientRect().width > 0 && getComputedStyle(c).position !== 'absolute';
                    });
                    if (w.tagName === 'TR') {
                        cells = cells.filter(function (c) { return !c.hasAttribute('rowspan'); });
                        return cells[cells.length - 1] || null;
                    }
                    return cells.length >= 3 ? cells[cells.length - 2] : null;
                }
                function lastTextRight(cell) {
                    var walker = document.createTreeWalker(cell, NodeFilter.SHOW_TEXT, null), node, last = null;
                    while ((node = walker.nextNode())) {
                        if (!node.nodeValue.trim() || (node.parentElement && node.parentElement.closest('.coupon-row'))) continue;
                        last = node;
                    }
                    if (!last) return null;
                    var r = document.createRange(); r.selectNodeContents(last);
                    var rect = r.getBoundingClientRect();
                    return rect.width ? rect.right : null;
                }
                // move the price content (not the full-width wrappers, they would stick out of the cell)
                function shiftInside(el, dx, depth) {
                    depth = depth || 0;
                    if (depth > 6) return; // safety: never recurse deeper than a price cell can be
                    var cw = el.getBoundingClientRect().width;
                    Array.prototype.slice.call(el.childNodes).forEach(function (n) {
                        if (n.nodeType === 3) {
                            if (!n.nodeValue.trim()) return;
                            var span = document.createElement('coupon-shift'); // bare text can't be moved: wrap it once (own tag: design rules like '.x span' must not style it)
                            span.className = 'coupon-shift-text';
                            n.parentNode.insertBefore(span, n); span.appendChild(n);
                            n = span;
                        }
                        if (n.nodeType === 1 && n.classList.contains('coupon-shift-text')) {
                            // our own wrapper: move it as is (a design rule may make every span a full-width block)
                            n.setAttribute('data-coupon-shift', '1'); n.style.position = 'relative'; n.style.left = dx + 'px';
                            return;
                        }
                        if (n.nodeType !== 1 || !notMeta(n) || n.classList.contains('coupon-row') || n.querySelector('.coupon-row')) return;
                        var st = getComputedStyle(n);
                        if (st.display === 'none' || st.position === 'absolute' || st.position === 'fixed') return;
                        var w = n.getBoundingClientRect().width;
                        var tr = lastTextRight(n);
                        // block boxes much wider than their text (full-width wrappers) would stick out: move what is inside them;
                        // small boxes with padding (badges like -79%) are moved whole
                        if (!/^inline/.test(st.display) && n.childNodes.length && (w >= cw - 2 || (tr !== null && n.getBoundingClientRect().right - tr > 16))) { shiftInside(n, dx, depth + 1); return; }
                        n.setAttribute('data-coupon-shift', '1');
                        n.style.position = 'relative';
                        n.style.left = dx + 'px';
                    });
                }
                function priceOverCoupon() {
                    document.querySelectorAll('[data-coupon-shift]').forEach(function (n) { n.style.left = ''; n.style.position = ''; n.removeAttribute('data-coupon-shift'); });
                    document.querySelectorAll('.coupon-row').forEach(function (box) {
                        var sum = box.querySelector('.coupon-row__sum'), cell = priceCellOf(box);
                        if (!sum || !cell || !box.getBoundingClientRect().width) return;
                        var right = lastTextRight(cell);
                        if (right === null) return;
                        var dx = Math.round(sum.getBoundingClientRect().right - right);
                        if (dx <= 0 || dx > 400) return; // only move right, and never across the layout
                        shiftInside(cell, dx);
                    });
                }
                var _align = align; align = function () { gridRows(); _align(); alignDiv(); narrow(); lastRows(); priceOverCoupon(); };
                // tables rendered later by JS (design_17 chat): re-run when coupon boxes appear
                if (window.MutationObserver) {
                    new MutationObserver(function (list) {
                        for (var i = 0; i < list.length; i++) for (var j = 0; j < list[i].addedNodes.length; j++) {
                            var n = list[i].addedNodes[j];
                            if (n.nodeType === 1 && (n.classList.contains('coupon-row') || n.querySelector('.coupon-row'))) { later(); return; }
                        }
                    }).observe(document.documentElement, { childList: true, subtree: true });
                }
                var t;
                function later() { clearTimeout(t); t = setTimeout(function () { align(); }, 100); }
                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { align(); }); else align();
                window.addEventListener('load', function () { align(); });
                window.addEventListener('resize', later);
                document.addEventListener('click', function () { setTimeout(function () { align(); }, 50); }); // dosage tabs etc.
            })();
        </script>
    @endonce
    @if ($crCssOnly)
        {{-- styles only --}}
    @elseif (($couponAs ?? 'tr') === 'cell')
        <td class="product__coupon-cell">
            <div class="coupon-row" title="{{ __('text.coupon_cart_note') }}">
                @include('shared.coupon_tag')
                <em class="coupon-row__label">{{ __('text.coupon_price_label') }}</em>
                <b class="coupon-row__sum">{{ \App\Models\Currency::convert($couponBase - $crDiscount, true) }}</b>
            </div>
        </td>
    @elseif (($couponAs ?? 'tr') === 'div')
        <div class="coupon-row-wrap">
            <div class="coupon-row" title="{{ __('text.coupon_cart_note') }}">
                @include('shared.coupon_tag')
                <em class="coupon-row__label">{{ __('text.coupon_price_label') }}</em>
                <b class="coupon-row__sum">{{ \App\Models\Currency::convert($couponBase - $crDiscount, true) }}</b>
            </div>
        </div>
    @else
        <tr class="{{ $couponRowClass ?? 'product' }} product--coupon">
            <td colspan="{{ $couponColspan ?? 2 }}">
                <div class="coupon-row" title="{{ __('text.coupon_cart_note') }}">
                    @include('shared.coupon_tag')
                    <em class="coupon-row__label">{{ __('text.coupon_price_label') }}</em>
                    <b class="coupon-row__sum">{{ \App\Models\Currency::convert($couponBase - $crDiscount, true) }}</b>
                </div>
            </td>
        </tr>
    @endif
@endif