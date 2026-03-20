@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >
        <meta 
            name="generator" 
            content="Bagisto"
        >

        @if (auth()->guard('customer')->check())
            <meta name="customer-id" content="{{ auth()->guard('customer')->user()->id }}">
        @endif

        @stack('meta')

        <link
            rel="icon"
            sizes="16x16"
            href="{{ core()->getCurrentChannel()->favicon_url ?? (core()->getCurrentChannel()->logo_url ?? '/favicon.ico') }}"
        />

        <script>
            window.Laravel = {
                reverbAppKey: '{{ config('broadcasting.connections.reverb.key') }}',
                reverbHost: '{{ config('broadcasting.connections.reverb.frontend.host') && config('broadcasting.connections.reverb.frontend.host') !== '127.0.0.1' ? config('broadcasting.connections.reverb.frontend.host') : '' }}' || window.location.hostname,
                reverbPort: '{{ config('broadcasting.connections.reverb.frontend.port') }}',
                reverbScheme: '{{ config('broadcasting.connections.reverb.frontend.scheme') }}',
                pusherAppKey: '{{ config('broadcasting.connections.pusher.key') }}',
                pusherHost: '{{ config('broadcasting.connections.pusher.options.host') }}',
                pusherPort: '{{ config('broadcasting.connections.pusher.options.port') }}',
                pusherScheme: '{{ config('broadcasting.connections.pusher.options.scheme') }}',
                pusherCluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                turnUrl: '{{ config('services.turn.url') }}',
                turnUsername: '{{ config('services.turn.username') }}',
                turnPassword: '{{ config('services.turn.password') }}',
            };
        </script>

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])
        <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

        <link
            rel="preconnect"
            href="https://fonts.googleapis.com"
            crossorigin
        />

        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin
        />

        <link
            rel="preload" as="style"
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap"
        />

        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap"
        />

        @stack('styles')

        <style>
            html {
                scrollbar-gutter: stable;
            }

            /* Force sharp corners globally (Meanly brutalist style) */
            *, *::before, *::after {
                border-radius: 0 !important;
                -webkit-border-radius: 0 !important;
                -moz-border-radius: 0 !important;
            }
            
            /* Target all input types and states */
            input, textarea, select, button, .v-field, .v-field__overlay, .v-field__outline, .form-control {
                border-radius: 0 !important;
            }

            .ios-tile-relative {
                position: relative !important;
            }

            .ios-group {
                background-color: #fff !important;
                border: 1px solid #f3f4f6 !important;
                margin-bottom: 20px !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04) !important;
            }

            .ios-close-button {
                position: absolute !important;
                top: 20px !important;
                right: 20px !important;
                left: auto !important;
                z-index: 20 !important;
                width: 32px !important;
                height: 32px !important;
                background-color: #ef4444 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: #ffffff !important;
                transition: all 0.2s ease !important;
                box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2) !important;
            }

            .ios-close-button:hover {
                background-color: #dc2626 !important;
                transform: scale(1.05) !important;
            }

            .ios-close-button:active {
                transform: scale(0.95) !important;
            }

            .ios-back-button {
                position: absolute !important;
                top: 20px !important;
                left: 20px !important;
                right: auto !important;
                z-index: 20 !important;
                width: 32px !important;
                height: 32px !important;
                background-color: #fff !important;
                border: 1px solid #f4f4f5 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: #a1a1aa !important;
                transition: all 0.2s ease !important;
            }

            .ios-back-button:hover {
                color: #7C45F5 !important;
                border-color: #e4e4e7 !important;
            }

            .ios-back-button:active {
                transform: scale(0.95) !important;
            }

            /* Unified Red Cross Style for Modals/Drawers */
            .ios-red-cross {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 32px !important;
                height: 32px !important;
                background-color: #ef4444 !important;
                color: #ffffff !important;
                transition: all 0.2s ease !important;
                cursor: pointer !important;
                font-size: 18px !important;
            }

            .ios-red-cross:hover {
                background-color: #dc2626 !important;
                transform: scale(1.05) !important;
            }
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif

        {!! view_render_event('bagisto.shop.layout.head.after') !!}

    </head>

    <body>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        <!-- Premium Background Layer - Refined Mesh Gradient -->
        <!-- Solid Background Layer (Matches Sidebar for Seamless integration) -->
        <div class="fixed inset-0 -z-30" style="background-color: #F0EFFF;"></div>

        <!-- Built With Bagisto -->
        <div id="app" class="flex flex-col min-h-screen overflow-x-hidden relative">
            
            <div id="main-content-wrapper" class="flex flex-col min-h-screen bg-transparent px-0 md:px-0">
                <!-- Flash Message Blade Component -->
                <x-shop::flash-group />

                <!-- Confirm Modal Blade Component -->
                <x-shop::modal.confirm />

                <!-- Page Header Blade Component -->
                @if ($hasHeader)
                    <x-shop::layouts.header />
                @endif

                @if(
                    core()->getConfigData('general.gdpr.settings.enabled')
                    && core()->getConfigData('general.gdpr.cookie.enabled')
                )
                    <x-shop::layouts.cookie />
                @endif

                {!! view_render_event('bagisto.shop.layout.content.before') !!}

                <!-- Page Content Blade Component -->
                <main id="main" class="flex-grow flex flex-col">
                    {{ $slot }}
                </main>

                {!! view_render_event('bagisto.shop.layout.content.after') !!}


                <!-- Page Services Blade Component -->
                @if ($hasFeature)
                    <x-shop::layouts.services />
                @endif

                <!-- Page Footer Blade Component -->
                @if ($hasFooter)
                    <x-shop::layouts.footer />
                @endif
            </div>

            <v-shortcut-help></v-shortcut-help>
            <v-call-overlay></v-call-overlay>
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        @stack('scripts')

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}
        <script>
            /**
             * Load event, the purpose of using the event is to mount the application
             * after all of our `Vue` components which is present in blade file have
             * been registered in the app. No matter what `app.mount()` should be
             * called in the last.
             */
            window.addEventListener("load", function (event) {
                app.mount("#app");
            });

            // ── Global Passkey Wallet Helper ──────────────────────────────
            function _b64ToUint8Array(base64) {
                if (!base64) return new Uint8Array(0);
                var padding = '='.repeat((4 - base64.length % 4) % 4);
                var b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                var rawData = window.atob(b64);
                var outputArray = new Uint8Array(rawData.length);
                for (var i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            function _bufToBase64URL(buffer) {
                var binary = '';
                var bytes = new Uint8Array(buffer);
                for (var i = 0; i < bytes.byteLength; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            }

            window.handleMeanlyWalletPasskey = async function (el, redirectUrl = null) {
                if (el && el.classList.contains('opacity-50')) return;
                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер или соединение (требуется HTTPS) не поддерживают Passkey.');
                    return;
                }
                if (el) el.classList.add('opacity-50', 'pointer-events-none');
                
                try {
                    const { startAuthentication } = SimpleWebAuthnBrowser;
                    var response = await fetch('{{ route('passkeys.login-options') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    if (!response.ok) throw new Error('Ошибка связи с сервером');
                    var options = await response.json();
                    
                    var asseResp = await startAuthentication(options);

                    var loginResponse = await fetch('{{ route('passkeys.login') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ start_authentication_response: JSON.stringify(asseResp), remember: false })
                    });

                    if (loginResponse.ok) {
                        window.location.href = redirectUrl || '{{ route('shop.customers.account.credits.index') }}';
                    } else {
                        var result = await loginResponse.json();
                        throw new Error(result.message || 'Ошибка проверки Passkey');
                    }
                } catch (err) {
                    if (err.name !== 'NotAllowedError') alert(err.message);
                } finally {
                    if (el) el.classList.remove('opacity-50', 'pointer-events-none');
                }
            }
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>

        <script type="text/x-template" id="v-shortcut-help-template">
            <div v-if="isVisible" 
                class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
                @click.self="isVisible = false">
                <div class="w-full max-w-lg overflow-hidden  bg-white shadow-2xl transition-all animate-in fade-in zoom-in duration-200">
                    <div class="flex items-center justify-between border-b border-zinc-100 p-6">
                        <h3 class="text-xl font-dmserif text-zinc-800">Горячие клавиши</h3>
                        <button @click="isVisible = false" class="text-2xl icon-cross text-zinc-400 hover:text-zinc-600"></button>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid gap-6">
                            <section>
                                <h4 class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-400">Глобальные</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Поиск по каталогу</span>
                                        <div class="flex gap-1">
                                            <kbd class="min-w-[2.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">⌘</kbd>
                                            <kbd class="min-w-[2.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">K</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Помощь по клавишам</span>
                                        <kbd class="min-w-[2.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">?</kbd>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4 class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-400">Навигация</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Главная страница</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">H</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Личный профиль</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">P</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Мои заказы</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center  border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">O</kbd>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="bg-zinc-50 p-4 text-center">
                        <p class="text-xs text-zinc-400">Нажмите <kbd class="px-1 font-semibold">ESC</kbd> чтобы закрыть</p>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-shortcut-help', {
                template: '#v-shortcut-help-template',
                data() {
                    return {
                        isVisible: false,
                        lastKeyPressed: null,
                        lastKeyTime: 0,
                    }
                },
                mounted() {
                    window.addEventListener('keydown', this.handleKeydown);
                },
                beforeUnmount() {
                    window.removeEventListener('keydown', this.handleKeydown);
                },
                methods: {
                    handleKeydown(e) {
                        // Ignore if typing in an input
                        const activeElement = document.activeElement;
                        const isInput = activeElement.tagName === 'INPUT' || 
                                        activeElement.tagName === 'TEXTAREA' || 
                                        activeElement.isContentEditable;
                        
                        if (isInput && e.key !== 'Escape') {
                            // Special case: Cmd+K while in input? Usually we don't want to break native behavior
                            // But if it's Cmd+K, we might want it to focus search anyway? Let's stay safe:
                            if (! ( (e.metaKey || e.ctrlKey) && e.key === 'k' )) {
                                return;
                            }
                        }

                        // Help (?)
                        if (e.key === '?' && !isInput) {
                            this.isVisible = !this.isVisible;
                            return;
                        }

                        // Escape to close
                        if (e.key === 'Escape') {
                            this.isVisible = false;
                        }

                        // Cmd+K or Ctrl+K for Search
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            const searchInput = document.querySelector('input[name="query"]');
                            if (searchInput) {
                                searchInput.focus();
                                // If we're not on category page, maybe we should redirect?
                                // But usually Cmd+K focuses a search modal. Here search is on catalog.
                                // If it's not present, let's redirect to catalog?
                            } else {
                                window.location.href = "{{ route('shop.search.index') }}";
                            }
                        }

                        // Navigation sequence G + ...
                        if (!isInput) {
                            const now = Date.now();
                            const key = e.key.toLowerCase();

                            if (this.lastKeyPressed === 'g' && (now - this.lastKeyTime < 1000)) {
                                if (key === 'h') window.location.href = "{{ route('shop.home.index') }}";
                                if (key === 'p') window.location.href = "{{ route('shop.customers.account.index') }}";
                                if (key === 'o') window.location.href = "{{ route('shop.customers.account.orders.index') }}";
                                this.lastKeyPressed = null;
                            } else {
                                this.lastKeyPressed = key;
                                this.lastKeyTime = now;
                            }
                        }
                    }
                }
            });
        </script>

        @php
            $chatwootEnabled = core()->getConfigData('general.content.chatwoot.enabled');
            $chatwootToken = core()->getConfigData('general.content.chatwoot.website_token');
            $chatwootBaseUrl = core()->getConfigData('general.content.chatwoot.base_url') ?? 'https://support.wildcloud.ru';

            // Unified visibility logic: hide on calls and customer account pages
            $showChatwoot = $chatwootEnabled && $chatwootToken && ! (
                request()->routeIs('shop.call.index') || 
                request()->routeIs('shop.customer.account*') || 
                request()->routeIs('shop.customer.session.index')
            );
        @endphp

        @if ($showChatwoot)
            <script>
              (function(d,t) {
                var BASE_URL="{{ $chatwootBaseUrl }}";
                var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
                g.src=BASE_URL+"/packs/js/sdk.js";
                g.async = true;
                s.parentNode.insertBefore(g,s);
                g.onload=function(){
                  window.chatwootSDK.run({
                    websiteToken: '{{ $chatwootToken }}',
                    baseUrl: BASE_URL
                  })
                }
              })(document,"script");
            </script>
        @endif
        {{-- Global Verification Modal --}}
        <div id="verify-modal"
            class="hidden fixed inset-0 z-[9999] items-center justify-center p-4 bg-black/60 backdrop-blur-xl">
            <div class="bg-white  w-full max-w-[400px] overflow-hidden shadow-2xl border border-white/20">
                <div class="p-8 text-center" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                    <div class="w-16 h-16 bg-white/20  flex items-center justify-center mx-auto mb-4 border border-white/20">
                        <span class="icon-shield text-white text-3xl"></span>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">Верификация</h3>
                    <p class="text-violet-100/90 text-[14px]">Докажите владение кошельком</p>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-violet-50/50 border border-violet-100 p-4 ">
                        <p class="text-[12px] text-violet-600 font-bold mb-1 uppercase tracking-widest">Сумма подтверждения</p>
                        <p id="verify-amount" class="text-2xl font-bold text-zinc-900 font-mono leading-none">—</p>
                    </div>

                    <div class="bg-zinc-50 border border-zinc-100 p-4 ">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-[12px] text-zinc-400 font-bold mb-1 uppercase tracking-widest">Куда отправить</p>
                                <p id="verify-dest" class="text-[11px] font-mono font-bold text-zinc-700 break-all leading-tight">—</p>
                            </div>
                            <button id="verify-dest-copy"
                                class="shrink-0 text-[11px] text-violet-600 font-bold bg-white border border-zinc-200 px-3 py-2  active:scale-95 transition-all shadow-sm">Копировать</button>
                        </div>
                    </div>

                    <div class="text-[13px] text-zinc-500 space-y-2 leading-relaxed bg-zinc-50/50  p-4 border border-zinc-100">
                        <div class="flex gap-2">
                            <span class="font-bold text-violet-600">1.</span>
                            <p>Отправьте <b class="text-zinc-900">ровно указанную</b> сумму со своего кошелька.</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-bold text-violet-600">2.</span>
                            <p>После отправки нажмите кнопку «Проверить» ниже.</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-8 flex flex-col gap-3">
                    <a id="verify-link" href="#"
                        class="w-full text-center text-white font-bold py-4  shadow-lg active:scale-[0.98] transition-all"
                        style="background: linear-gradient(to right, #7c3aed, #4f46e5); box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);">
                        Проверить транзакцию
                    </a>
                    <button onclick="closeVerifyModal()"
                        class="w-full text-zinc-400 hover:text-zinc-600 font-bold py-2 text-[14px] transition-colors">Сделаю позже</button>
                </div>
            </div>
        </div>
        {{-- <v-messenger></v-messenger> --}}
    </body>
</html>
