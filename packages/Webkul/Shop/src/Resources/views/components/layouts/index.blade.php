@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()?->direction ?? 'ltr' }}"
>
    <head>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <!-- Global Theme Switcher (Anti-FOUC) -->
        <script>
            (function() {
                try {
                    var localTheme = localStorage.getItem('theme');
                    var themeToApply = 'light';
                    
                    if (localTheme === 'dark' || localTheme === 'light') {
                        themeToApply = localTheme;
                    } else {
                        var hour = new Date().getHours();
                        if (hour >= 18 || hour < 6) {
                            themeToApply = 'dark';
                        }
                    }
                    
                    if (themeToApply === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (e) {}
            })();
        </script>
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
            content="{{ json_encode(core()->getCurrentCurrency()) }}"
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
        <script src="https://telegram.org/js/telegram-web-app.js"></script>

        <script>
            /**
             * Telegram Mini App (TMA) Detection & Environment Setup
             */
            (function() {
                const tg = window.Telegram?.WebApp;
                if (tg && tg.initData) {
                    document.documentElement.classList.add('tma-mode');
                    
                    // Sync Telegram theme colors to CSS variables
                    const theme = tg.themeParams;
                    if (theme.bg_color) document.documentElement.style.setProperty('--tma-bg', theme.bg_color);
                    if (theme.text_color) document.documentElement.style.setProperty('--tma-text', theme.text_color);
                    if (theme.hint_color) document.documentElement.style.setProperty('--tma-hint', theme.hint_color);
                    if (theme.link_color) document.documentElement.style.setProperty('--tma-link', theme.link_color);
                    if (theme.button_color) document.documentElement.style.setProperty('--tma-button', theme.button_color);
                    if (theme.button_text_color) document.documentElement.style.setProperty('--tma-button-text', theme.button_text_color);

                    window.isTMA = true;
                } else {
                    window.isTMA = false;
                }
            })();
        </script>

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
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        />

        @stack('styles')

        <style>
            html {
                scrollbar-gutter: stable;
            }

            /* Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: rgba(124, 69, 245, 0.25);
                border-radius: 9999px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: rgba(124, 69, 245, 0.5);
            }

            /* For Firefox */
            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(124, 69, 245, 0.25) transparent;
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

        <!-- Background Layer -->
        <div class="fixed inset-0 -z-30 bg-[#F0EFFF] dark:bg-[#100c24] transition-colors duration-500"></div>

        <!-- Built With Bagisto -->
        <div id="app" class="flex flex-col min-h-screen overflow-x-hidden relative text-zinc-900 dark:text-white transition-colors duration-500">
            
            <div id="main-content-wrapper" class="flex flex-col min-h-screen bg-transparent">
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
            document.addEventListener("DOMContentLoaded", async function (event) {
                // If in TMA and not logged in, trigger transparent login
                if (window.isTMA && !document.querySelector('meta[name="customer-id"]')) {
                    console.log('TMA: Initializing auto-login...');
                    try {
                        const tg = window.Telegram.WebApp;
                        const response = await fetch('{{ route('shop.tma.login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ initData: tg.initData })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            if (result.success) {
                                if (result.is_new_registration && result.redirect_url) {
                                    window.location.href = result.redirect_url;
                                } else {
                                    // Reload to apply session and show authenticated state
                                    window.location.reload();
                                }
                                return;
                            }
                        }
                    } catch (e) {
                        console.error('TMA Auto-login failed:', e);
                    }
                }

                app.mount("#app");
            });

            // If we are in load event and app isn't mounted yet, mount it just in case
            window.addEventListener("load", () => {
                if (document.getElementById('app') && !document.getElementById('app').__vue_app__) {
                     try { app.mount("#app"); } catch(e) {}
                }
            });

            // ── Global Passkey Wallet Helper ──────────────────────────────
            function _b64ToUint8Array(base64) {
                if (!base64) return new Uint8Array(0);
                const b64 = base64.replace(/-/g, '+').replace(/_/g, '/');
                const pad = b64.length % 4;
                const padded = pad ? b64 + '===='.slice(pad) : b64;
                const rawData = window.atob(padded);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
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
                
                const SWAB = window.SimpleWebAuthnBrowser;
                if (!SWAB) {
                    alert('Библиотека аутентификации не загружена. Попробуйте обновить страницу.');
                    return;
                }

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер или соединение (требуется HTTPS) не поддерживают Passkey.');
                    return;
                }

                if (el) el.classList.add('opacity-50', 'pointer-events-none');
                
                try {
                    var response = await fetch('{{ route('passkeys.login-options') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    if (!response.ok) throw new Error('Ошибка связи с сервером');
                    var rawOptions = await response.json();
                    var options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;
                    
                    // Robust base64url conversion for Safari
                    const toBase64Url = (str) => {
                        if (!str || typeof str !== 'string') return str;
                        return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    };

                    if (options.challenge) options.challenge = toBase64Url(options.challenge);
                    if (options.allowCredentials) {
                        options.allowCredentials.forEach(cred => {
                            if (cred.id) cred.id = toBase64Url(cred.id);
                        });
                    }

                    var asseResp = await SWAB.startAuthentication(options);

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
                    if (err.name !== 'NotAllowedError') {
                        console.error('Passkey Auth Error:', err);
                        alert(err.message === 'The string did not match the expected pattern' 
                            ? 'Ошибка формата данных в Safari. Пожалуйста, попробуйте еще раз или используйте другой браузер.' 
                            : err.message);
                    }
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
            $chatwootEnabled = filter_var(core()->getConfigData('general.content.chatwoot.enabled'), FILTER_VALIDATE_BOOLEAN);
            $chatwootToken = core()->getConfigData('general.content.chatwoot.website_token');
            $chatwootBaseUrl = core()->getConfigData('general.content.chatwoot.base_url') ?? 'https://support.wildcloud.ru';

            // Unified visibility logic: hide on calls, registration, account, and auth-related pages
            $showChatwoot = $chatwootEnabled && $chatwootToken && ! (
                request()->routeIs('shop.call.index') || 
                request()->routeIs('shop.customer.account*') || 
                request()->routeIs('shop.customers.account*') || 
                request()->routeIs('shop.customer.session.index') ||
                request()->routeIs('shop.customers.register*') ||
                request()->routeIs('shop.customers.forgot_password*') ||
                request()->routeIs('shop.customers.reset_password*')
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
