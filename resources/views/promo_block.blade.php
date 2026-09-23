@if (env('APP_PROMO_COUPON_POPUP', 1))
    @php
        $promoChannels = collect(config('promo.channels'))->filter(fn($c) => !empty($c['enabled']));
        $gpwIcons = [
            'email'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
            'push'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
            'telegram'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.04 15.31l-.37 5.27c.53 0 .76-.23 1.05-.51l2.55-2.44 5.28 3.87c.97.53 1.66.25 1.91-.9l3.47-16.29c.31-1.43-.52-2.08-1.47-1.72L1.05 10.45c-1.42.56-1.4 1.35-.24 1.71l5.22 1.63L18.1 6.2c.57-.38 1.09-.17.66.21L9.04 15.31z"/></svg>',
            'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.297-.497.1-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
            'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
            'discord'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.79 19.79 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.865-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.058a.082.082 0 00.031.056 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028 14.09 14.09 0 001.226-1.994.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
        ];
    @endphp

    <div id="global-promo-widget">
        <div id="gpw-trigger" onclick="GPW.openPopup()" title="{{ __('promo.title') }}">
            <span>%</span>
        </div>

        <div id="gpw-popup" class="gpw-hidden">
            <div id="gpw-popup-content">
                <span id="gpw-close" onclick="GPW.closePopup()">&times;</span>
                <h3>{{ __('promo.title') }}</h3>
                <p>{{ __('promo.subtitle') }}</p>

                <div id="gpw-tabs">
                    @foreach ($promoChannels as $key => $ch)
                        <button type="button" class="gpw-tab @if($loop->first) gpw-tab-active @endif"
                                data-channel="{{ $key }}" title="{{ $ch['title'] }}"
                                onclick="GPW.switchChannel('{{ $key }}')">
                            {!! $gpwIcons[$key] ?? '' !!} {{ $ch['title'] }}
                        </button>
                    @endforeach
                </div>

                @foreach ($promoChannels as $key => $ch)
                    <div class="gpw-panel @if(!$loop->first) gpw-hidden @endif" id="gpw-panel-{{ $key }}">
                        @if (!empty($ch['input_placeholder']))
                            <input type="{{ $ch['input_type'] ?? 'text' }}" id="gpw-field-{{ $key }}" placeholder="{{ __('promo.' . $ch['input_placeholder']) }}">
                        @endif
                        <button type="button" id="gpw-request-{{ $key }}" onclick="GPW.requestCode('{{ $key }}')">{{ __('promo.' . $ch['button_text'], ['channel' => $ch['title']]) }}</button>
                        <p id="gpw-subscribed-{{ $key }}" style="display:none; color:#28a745; font-weight:bold; margin:0 0 10px 0;"></p>
                        {{-- @if (!empty($ch['hint']))
                            <p style="font-size:13px; color:#666; margin:10px 0 0 0;">{{ __('promo.' . $ch['hint']) }}</p>
                        @endif --}}
                    </div>
                @endforeach

                {{-- <div id="gpw-activate-block">
                    <input type="text" id="gpw-code" placeholder="{{ __('promo.enter_code') }}" maxlength="6" style="text-transform:uppercase;">
                    <button type="button" class="gpw-btn-success" onclick="GPW.verifyCode()">{{ __('promo.activate_btn') }}</button>
                </div> --}}

                <div id="gpw-message"></div>
            </div>
        </div>
    </div>

    <style>
        #global-promo-widget * { box-sizing: border-box; font-family: Arial, sans-serif; }
        #gpw-trigger {
            position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
            background: linear-gradient(135deg, #ff416c, #ff4b2b); color: #fff;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: bold; cursor: pointer; z-index: 99998;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;
        }
        #gpw-trigger:hover { transform: scale(1.1); }
        #gpw-popup {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); z-index: 99999; display: flex;
            align-items: center; justify-content: center;
        }
        .gpw-hidden { display: none !important; }
        #gpw-popup-content {
            background: #fff; padding: 25px; border-radius: 12px; width: 90%; max-width: 420px;
            position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        #gpw-close { position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; color: #999; }
        #gpw-popup-content h3 { margin: 0 0 8px 0; color: #333; text-align: center; }
        #gpw-popup-content p { margin: 0 0 15px 0; color: #666; text-align: center; font-size: 14px; }

        /* Табы-переключатель */
        #gpw-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin: 0 0 15px 0;
        }
        #gpw-popup-content .gpw-tab {
            min-width: 0;
            display: flex; align-items: center; justify-content: center; gap: 5px;
            padding: 9px 4px; background: #fff; border: 2px solid #e5e5e5;
            border-radius: 8px; color: #777; font-size: 12px; font-weight: bold;
            cursor: pointer; transition: all 0.15s;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        #gpw-popup-content .gpw-tab:hover { border-color: #bbb; }
        #gpw-popup-content .gpw-tab.gpw-tab-active { border-color: #007bff; color: #007bff; background: #f0f7ff; }
        #gpw-popup-content .gpw-tab svg { width: 16px; height: 16px; flex: none; }

        @media (max-width: 420px) {
            #gpw-tabs { grid-template-columns: repeat(2, 1fr); }
        }

        #gpw-popup-content input {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;
            font-size: 14px; margin-bottom: 10px;
        }
        #gpw-popup-content button:not(.gpw-tab) {
            width: 100%; padding: 12px; background: #007bff; color: #fff; border: none;
            border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; transition: opacity 0.2s;
        }
        #gpw-popup-content button:not(.gpw-tab):hover { opacity: 0.9; }
        #gpw-popup-content button:not(.gpw-tab):disabled { background: #ccc; cursor: not-allowed; }
        #gpw-popup-content .gpw-btn-success { background: #28a745; }
        #gpw-activate-block { margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; }
        #gpw-message { margin-top: 12px; font-weight: bold; text-align: center; color: #d9534f; }
        #gpw-message.success { color: #28a745; }

        #gpw-popup-content.gpw-busy button { pointer-events: none; }

        #gpw-popup-content button.gpw-loading {
            position: relative;
            color: transparent !important;
        }
        #gpw-popup-content button.gpw-loading::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 18px; height: 18px;
            margin: -9px 0 0 -9px;
            border: 3px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: gpw-spin 0.7s linear infinite;
        }
        @keyframes gpw-spin { to { transform: rotate(360deg); } }

        #gpw-popup-content button:not(.gpw-tab):disabled {
            background: #28a745 !important;
            color: #fff;
            opacity: 1;
            cursor: default;
        }
        #gpw-toast {
            position: fixed; top: 20px; left: 50%;
            transform: translateX(-50%) translateY(-90px);
            background: #28a745; color: #fff; padding: 12px 22px; border-radius: 8px;
            font: bold 14px Arial, sans-serif; z-index: 100000;
            box-shadow: 0 4px 15px rgba(0,0,0,.3);
            transition: transform .35s ease; pointer-events: none;
        }
        #gpw-toast.gpw-toast-show { transform: translateX(-50%) translateY(0); }
    </style>

    <script>
        window.PromoRoutes = {
            send: '{{ route("promo.send", ["channel" => "__CHANNEL__"]) }}',
            verify: '{{ route("promo.verify", ["channel" => "__CHANNEL__"]) }}'
        };

        window.PromoConfig = {
            promoCouponEnabled: {{ config('promo.promo_coupon_enabled') ? 'true' : 'false' }}
        };

        function promoUrl(kind, channel) {
            const tpl = (window.PromoRoutes && window.PromoRoutes[kind]) || '';
            if (!tpl) return '/promo/' + kind + '/' + channel;
            return tpl.replace('__CHANNEL__', channel);
        }

        const GPW_LANG = @json(__('promo'));
    </script>

    <script>
        function getPromoCookie(name) {
            const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([$?*|{}[\]\\/])/g, '\\$1') + '=([^;]*)'));
            return m ? decodeURIComponent(m[1]) : '';
        }

        const GPW = {
            currentChannel: document.querySelector('#gpw-tabs .gpw-tab-active')?.dataset.channel || 'email',
            botUrls: @json(collect(config('promo.channels'))->mapWithKeys(fn($c, $k) => [$k => $c['bot_url'] ?? ''])),
            sentIdentifiers: {},
            _busy: false,
            _busyBtn: null,

            channelHooks: {
                email: {
                    getIdentifier: () => (document.getElementById('gpw-field-email')?.value || '').trim().toLowerCase(),
                    onSuccess: () => {
                        const inp = document.getElementById('gpw-field-email');
                        const email = (inp?.value || '').trim().toLowerCase();
                        if (!email) return;
                        GPW.sentIdentifiers.email = email;
                        if (inp) { inp.readOnly = true; inp.style.background = '#f3f3f3'; }
                        let link = document.getElementById('gpw-unlock-email');
                        if (!link && inp) {
                            link = document.createElement('p');
                            link.id = 'gpw-unlock-email';
                            link.style.cssText = 'margin:0 0 10px 0;';
                            link.innerHTML = '<a href="#" onclick="GPW.unlockIdentifier(\'email\'); return false;" style="color:#007bff;font-size:13px;text-decoration:underline;">'+ GPW_LANG.change_email +'</a>';
                            inp.insertAdjacentElement('afterend', link);
                        }
                        if (link) link.style.display = 'block';
                    },
                },
                push: {
                    getIdentifier: () => getPromoCookie('user_push'),
                    beforeRequest: async () => {
                        let up = getPromoCookie('user_push');
                        if (up) return up;
                        if (typeof window.allowPushSubscribeFromClick === 'function') window.allowPushSubscribeFromClick();
                        if (typeof window.enableNotif !== 'function') return null;
                        const res = await window.enableNotif();
                        if (!res || !res.subscription) return null;
                        await new Promise(r => setTimeout(r, 500));
                        return getPromoCookie('user_push');
                    },
                    onPanelShow: () => {
                        const up = getPromoCookie('user_push');
                        const btn = document.getElementById('gpw-request-push');
                        const msg = document.getElementById('gpw-subscribed-push');
                        if (btn) btn.style.display = up ? 'none' : 'block';
                        if (msg) {
                            msg.style.display = up ? 'block' : 'none';
                            msg.innerHTML = up ? GPW_LANG.push_done + '<br><span style="font-weight:normal;font-size:13px;color:#666;">' + GPW_LANG.push_done_hint + '</span>' : '';
                        }
                    },
                },
            },

            getHook: function(channel) {
                if (this.channelHooks[channel]) return this.channelHooks[channel];
                const url = this.botUrls[channel] || '';
                return {
                    noBot: !url,
                    getIdentifier: () => '',
                    onSuccess: (data) => {
                        if (url && data.token) {
                            window.open(url + (url.indexOf('?') >= 0 ? '&' : '?') + 'start=' + encodeURIComponent(data.token), '_blank');
                        }
                    },
                };
            },

            openPopup: function() {
                document.getElementById('gpw-popup').classList.remove('gpw-hidden');
                this.refreshPanel();
            },
            closePopup: function() {
                document.getElementById('gpw-popup').classList.add('gpw-hidden');
                this.showMsg('');
            },
            showMsg: function(text, ok = false) {
                const el = document.getElementById('gpw-message');
                el.textContent = text || '';
                el.className = ok ? 'success' : '';
            },
            toast: function(text) {
                let t = document.getElementById('gpw-toast');
                if (!t) { t = document.createElement('div'); t.id = 'gpw-toast'; document.body.appendChild(t); }
                t.textContent = text;
                t.classList.add('gpw-toast-show');
                setTimeout(() => t.classList.remove('gpw-toast-show'), 2500);
            },
            setBusy: function(busy, btn) {
                this._busy = busy;
                const content = document.getElementById('gpw-popup-content');
                if (!content) return;
                if (busy) {
                    content.classList.add('gpw-busy');
                    if (btn) btn.classList.add('gpw-loading');
                    this._busyBtn = btn || null;
                } else {
                    content.classList.remove('gpw-busy');
                    if (this._busyBtn) { this._busyBtn.classList.remove('gpw-loading'); this._busyBtn = null; }
                }
            },
            switchChannel: function(channel) {
                this.currentChannel = channel;
                document.querySelectorAll('#gpw-tabs .gpw-tab').forEach(b => b.classList.toggle('gpw-tab-active', b.dataset.channel === channel));
                document.querySelectorAll('#gpw-popup-content .gpw-panel').forEach(p => p.classList.toggle('gpw-hidden', p.id !== 'gpw-panel-' + channel));
                this.showMsg('');
                this.refreshPanel();
            },
            refreshPanel: function() {
                const hook = this.getHook(this.currentChannel);
                if (hook.onPanelShow) hook.onPanelShow();
            },
            unlockIdentifier: function(channel) {
                delete this.sentIdentifiers[channel];
                const inp = document.getElementById('gpw-field-' + channel);
                if (inp) { inp.readOnly = false; inp.style.background = ''; inp.focus(); }
                const link = document.getElementById('gpw-unlock-' + channel);
                if (link) link.style.display = 'none';
                document.getElementById('gpw-code').value = '';
                this.showMsg('');
            },

            requestCode: async function(channel) {
                if (this._busy) return;
                const hook = this.getHook(channel);
                if (hook.noBot) return this.showMsg(GPW_LANG.bot_off);
                const btn = document.getElementById('gpw-request-' + channel);
                this.setBusy(true, btn);
                try {
                    let identifier = hook.getIdentifier ? hook.getIdentifier() : '';
                    if (hook.beforeRequest) {
                        this.showMsg(GPW_LANG.preparing);
                        identifier = await hook.beforeRequest();
                        if (identifier === null) { this.showMsg(GPW_LANG.sub_failed); return; }
                    }
                    const r = await fetch(promoUrl('send', channel), {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({identifier: identifier})
                    });
                    const data = await r.json();
                    this.showMsg(data.message, data.status === 'success');
                    if (data.status === 'success') {
                        if (identifier) this.sentIdentifiers[channel] = identifier;
                        if (btn) { btn.disabled = true; btn.textContent = GPW_LANG.sent; }
                        if (hook.onSuccess) hook.onSuccess(data);
                    }
                    this.refreshPanel();
                } catch (e) {
                    this.showMsg(GPW_LANG.conn_error);
                } finally {
                    this.setBusy(false);
                }
            },

            verifyCode: function() {
                if (this._busy) return;
                const channel = this.currentChannel;
                const code = document.getElementById('gpw-code').value.trim();
                if (!code) return this.showMsg(GPW_LANG.enter_code);
                const hook = this.getHook(channel);
                const payload = {code: code};

                const ident = this.sentIdentifiers[channel] || (hook.getIdentifier ? hook.getIdentifier() : '');
                if (ident) payload.identifier = ident;
                const btn = document.querySelector('#gpw-activate-block .gpw-btn-success');
                this.setBusy(true, btn);
                fetch(promoUrl('verify', channel), {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify(payload)
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') {
                        this.showMsg(data.message, true);
                        if (btn) { btn.disabled = true; btn.textContent = GPW_LANG.activating; }
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.setBusy(false);
                        this.showMsg(data.message || GPW_LANG.invalid_code);
                    }
                }).catch(() => { this.setBusy(false); this.showMsg(GPW_LANG.verify_error); });
            },

            autoVerifyFromPush: function(code) {
                fetch(promoUrl('verify', 'push'), {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({code: code})
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') {
                        this.toast(GPW_LANG.applied);
                        setTimeout(() => location.reload(), 1200);
                    } else if (data.reason === 'window') {
                        this.toast(GPW_LANG.already_applied);
                    } else {
                        this.openPopup();
                        this.switchChannel('push');
                        document.getElementById('gpw-code').value = code;
                        this.showMsg(data.message || GPW_LANG.invalid_code);
                    }
                }).catch(() => {});
            },

            checkUrlParams: function() {
                const params = new URLSearchParams(window.location.search);
                if (params.get('open_promo_widget') !== '1') return;
                const type = params.get('type');
                const code = params.get('code');
                window.history.replaceState({}, document.title, window.location.pathname);
                if (type === 'push' && code) { this.autoVerifyFromPush(code); return; }
                this.openPopup();
                if (type && document.getElementById('gpw-panel-' + type)) this.switchChannel(type);
                if (code) document.getElementById('gpw-code').value = code;
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            GPW.checkUrlParams();
            GPW.refreshPanel();
        });
    </script>
@endif