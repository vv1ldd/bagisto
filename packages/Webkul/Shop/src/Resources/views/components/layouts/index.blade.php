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

        @stack('meta')

        <link
            rel="icon"
            sizes="16x16"
            href="{{ core()->getCurrentChannel()->favicon_url ?? '/favicon.ico' }}"
        />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

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
        <div class="fixed inset-0 bg-[#f8f8fa] -z-30"></div>
        <div class="fixed inset-0 bg-gradient-to-tr from-[#7C45F5]/15 via-white to-[#FF4D6D]/10 -z-20"></div>
        <div class="fixed inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(124,69,245,0.08)_0%,transparent_50%),radial-gradient(circle_at_80%_80%,rgba(255,77,109,0.08)_0%,transparent_50%)] -z-10 animate-pulse duration-[10s]"></div>

        <!-- Built With Bagisto -->
        <div id="app" class="flex flex-col min-h-screen overflow-x-hidden relative">
            <!-- Sidebar Navigation -->
            <x-shop::layouts.sidebar />

            <div id="content-push-wrapper" class="flex flex-col min-h-screen bg-white transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] will-change-transform shadow-[0_0_80px_rgba(0,0,0,0.02)] px-0 md:px-0">
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
                <main id="main" class="flex-grow">
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
            window.addEventListener("load", function (event) {
                app.mount("#app");
            });
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>

        <script type="text/x-template" id="v-shortcut-help-template">
            <div v-if="isVisible" 
                class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
                @click.self="isVisible = false">
                <div class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl transition-all animate-in fade-in zoom-in duration-200">
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
                                            <kbd class="min-w-[2.5rem] flex items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">⌘</kbd>
                                            <kbd class="min-w-[2.5rem] flex items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">K</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Помощь по клавишам</span>
                                        <kbd class="min-w-[2.5rem] flex items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-500 shadow-sm">?</kbd>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4 class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-400">Навигация</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Главная страница</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">H</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Личный профиль</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">P</kbd>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-zinc-600">Мои заказы</span>
                                        <div class="flex items-center gap-1.5 font-medium text-zinc-400">
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">G</kbd>
                                            <span class="text-xs">затем</span>
                                            <kbd class="min-w-[1.5rem] flex items-center justify-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 text-xs">O</kbd>
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

        <script>
          (function(d,t) {
            var BASE_URL="https://support.wildcloud.ru";
            var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=BASE_URL+"/packs/js/sdk.js";
            g.async = true;
            s.parentNode.insertBefore(g,s);
            g.onload=function(){
              window.chatwootSDK.run({
                websiteToken: 'CiwXQPuAVsbf6bPh5XEstDjP',
                baseUrl: BASE_URL
              })
            }
          })(document,"script");
        </script>
    </body>
</html>
