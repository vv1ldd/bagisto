@php
    $showCategories = false;
    $authUser = auth()->guard('customer')->user();
    $userInitial = $authUser ? strtoupper(substr($authUser->credits_alias ?: $authUser->username, 0, 1)) : null;
    $userName = $authUser ? ($authUser->credits_alias ?: $authUser->username) : null;

    /* Get initial cart count for server-side rendering */
    $cart = \Webkul\Checkout\Facades\Cart::getCart();
    $cartCount = $cart ? $cart->items->count() : 0;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between h-[64px]">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="group"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <div class="relative w-9 h-9">
                <div class="absolute inset-0 bg-[#7C45F5] rounded-[0.6rem] rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-lg shadow-[#7C45F5]/20"></div>
                <div class="absolute inset-0 bg-white rounded-[0.6rem] flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                    <span class="text-[17px] font-black text-zinc-900 tracking-tighter italic ml-[1px] mt-[1px]">M</span>
                </div>
            </div>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
    </div>

    {{-- MIDDLE: Toolbar (Filters & Search) --}}
    <div class="flex-grow flex justify-center px-4">
    </div>

    {{-- RIGHT: Server-side rendered nav — no Vue, no CLS --}}
    <div class="flex items-center gap-4 flex-shrink-0">
        <v-theme-switcher></v-theme-switcher>

        @auth('customer')
            {{-- Logged-in: Avatar + Alias --}}
            <div class="flex items-center gap-2.5">
                {{-- Avatar/Cart icon — Vue only adds the cart badge on top --}}
                <div class="relative min-w-[28px]">
                    <a href="{{ $cartCount > 0 ? route('shop.checkout.cart.index') : route('shop.customers.account.index') }}" 
                       class="relative group block" 
                       id="header-avatar-link">
                        <div class="flex h-8 w-8 items-center justify-center bg-[#7C45F5] text-white font-bold shadow-[0_0_15px_rgba(124,69,245,0.3)] leading-none ring-1 ring-white/10 transition-all group-hover:scale-105 group-hover:shadow-[0_0_20px_rgba(124,69,245,0.5)] active:scale-95 rounded-full">
                            @if ($cartCount > 0)
                                <span class="icon-cart text-base"></span>
                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20 rounded-full">
                                    {{ $cartCount }}
                                </span>
                            @else
                                <span class="text-[11px] uppercase">{{ $userInitial }}</span>
                            @endif
                        </div>
                    </a>

                    {{-- Tiny Vue component to handle dynamic updates and toggles --}}
                    <v-cart-badge
                        cart-url="{{ route('shop.checkout.cart.index') }}"
                        avatar-url="{{ route('shop.customers.account.index') }}"
                        :initial-count="{{ $cartCount }}"
                        user-initial="{{ $userInitial }}"
                    ></v-cart-badge>
                </div>

                <a href="{{ route('shop.customers.account.index') }}"
                    class="text-[13px] font-bold text-zinc-400 hover:text-white transition-colors truncate max-w-[140px] tracking-tight">
                    {{ '@' . $userName }}@if($authUser->is_investor) <span title="Инвестор" class="text-xs leading-none ml-1">💎</span>@endif
                </a>
            </div>
        @else
            {{-- Guest: Login button --}}
            <a href="{{ route('shop.customer.session.index') }}"
                class="flex items-center justify-center bg-[#7C45F5] px-6 py-2.5 text-[14px] font-bold text-white shadow-[0_0_20px_rgba(124,69,245,0.4)] transition-all hover:bg-[#8A5CF7] hover:shadow-[0_0_30px_rgba(124,69,245,0.6)] active:scale-[0.97] rounded-full">
                Войти / Регистрация
            </a>
        @endauth
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        /**
         * v-cart-badge: tiny Vue component that handles the cart/avatar toggle.
         * It uses a prop for the initial count to avoid flashes during mounting.
         */
        app.component('v-cart-badge', {
            template: `
                <a :href="cartCount > 0 ? cartUrl : avatarUrl" class="absolute inset-0 z-10 block">
                    <div class="flex h-8 w-8 items-center justify-center bg-[#7C45F5] text-white font-bold shadow-[0_0_15px_rgba(124,69,245,0.3)] leading-none ring-1 ring-white/10 transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(124,69,245,0.5)] active:scale-95 rounded-full">
                        <template v-if="cartCount > 0">
                            <span class="icon-cart text-base"></span>
                            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20 rounded-full">
                                @{{ cartCount }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="text-[11px] uppercase">@{{ userInitial }}</span>
                        </template>
                    </div>
                </a>
            `,

            props: {
                cartUrl: String,
                avatarUrl: String,
                initialCount: Number,
                userInitial: String,
            },

            data() {
                return { 
                    cartCount: this.initialCount 
                };
            },

            mounted() {
                // Fetch real count to be sure, although initial should be correct
                this.fetchCart();

                this.$emitter.on('update-mini-cart', cart => {
                    this.cartCount = cart ? (cart.items_count ?? cart.items?.length ?? 0) : 0;
                });
            },

            methods: {
                fetchCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(r => { 
                            this.cartCount = r.data?.data?.items_count ?? r.data?.data?.items?.length ?? 0; 
                        })
                        .catch(() => {});
                }
            }
        });

        app.component('v-theme-switcher', {
            template: `
                <div class="flex items-center gap-1 bg-black/5 dark:bg-white/5 backdrop-blur-md rounded-full p-1 border border-black/10 dark:border-white/10 transition-colors">
                    <button @click="setTheme('light')" :class="{'bg-white dark:bg-[#7C45F5] text-[#7C45F5] dark:text-white shadow-sm': currentMode === 'light', 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white': currentMode !== 'light'}" class="w-7 h-7 flex items-center justify-center rounded-full transition-all" title="Светлая">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>
                    <button @click="setTheme('dark')" :class="{'bg-zinc-800 dark:bg-[#7C45F5] text-white shadow-sm': currentMode === 'dark', 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white': currentMode !== 'dark'}" class="w-7 h-7 flex items-center justify-center rounded-full transition-all" title="Темная">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <button @click="setTheme('auto')" :class="{'bg-[#7C45F5] text-white shadow-sm': currentMode === 'auto', 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white': currentMode !== 'auto'}" class="w-7 h-7 flex items-center justify-center rounded-full transition-all" title="Автоматически (по времени)">
                        <span class="text-[9px] font-bold tracking-wider leading-none">AUTO</span>
                    </button>
                </div>
            `,
            data() {
                return {
                    currentMode: 'auto'
                }
            },
            mounted() {
                let localTheme = localStorage.getItem('theme');
                if (localTheme === 'dark' || localTheme === 'light') {
                    this.currentMode = localTheme;
                }
            },
            methods: {
                setTheme(theme) {
                    this.currentMode = theme;
                    if (theme === 'auto') {
                        localStorage.removeItem('theme');
                        var hour = new Date().getHours();
                        if (hour >= 18 || hour < 6) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    } else {
                        localStorage.setItem('theme', theme);
                        if (theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}