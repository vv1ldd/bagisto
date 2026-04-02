@php
    $showCategories = false;
    $authUser = auth()->guard('customer')->user();
    $userInitial = $authUser ? strtoupper(substr($authUser->credits_alias ?: $authUser->username, 0, 1)) : null;
    $userName = $authUser ? ($authUser->credits_alias ?: $authUser->username) : null;

    /* Get initial cart count for server-side rendering */
    $cart = \Webkul\Checkout\Facades\Cart::getCart();
    $cartCount = $cart ? $cart->items->count() : 0;
@endphp

<div class="w-full h-full max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between">
    {{-- LEFT: Logo --}}
    <div class="flex items-center flex-shrink-0 h-full">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="group h-full flex items-center"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <div class="relative w-10 h-10 flex items-center justify-center bg-white border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] group-hover:translate-x-0.5 group-hover:translate-y-0.5 group-hover:shadow-none transition-all duration-200">
                <span class="text-[19px] font-black text-zinc-900 tracking-tighter italic ml-[1px] mt-[1px]">M</span>
            </div>
            <span class="ml-3 text-lg font-black uppercase tracking-tighter text-zinc-900 hidden md:block">
                Meanly<span class="text-[#7C45F5]">.</span>
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
    </div>

    {{-- MIDDLE: Search Bar (Redesigned) --}}
    <div class="flex-grow flex justify-center px-12 max-w-2xl">
        <div class="w-full relative group">
            <form action="{{ route('shop.search.index') }}" method="GET" class="flex items-center bg-white border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] group-focus-within:translate-x-0.5 group-focus-within:translate-y-0.5 group-focus-within:shadow-none transition-all duration-200 overflow-hidden">
                <input 
                    type="text" 
                    name="query" 
                    placeholder="Поиск игровых товаров..." 
                    class="w-full py-2.5 px-4 text-xs font-bold uppercase tracking-widest text-zinc-900 placeholder-zinc-400 focus:outline-none border-none ring-0"
                >
                <button type="submit" class="px-5 border-l-2 border-zinc-900 hover:bg-zinc-900 hover:text-white transition-colors h-10">
                    <span class="icon-search text-lg"></span>
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: Actions --}}
    <div class="flex items-center gap-6 flex-shrink-0">

        @auth('customer')
            <div class="flex items-center gap-4">
                {{-- Account/Cart Tile --}}
                <div class="relative">
                    <a href="{{ $cartCount > 0 ? route('shop.checkout.onepage.index') : route('shop.customers.account.index') }}" 
                       class="relative block h-10 w-10 bg-white border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all duration-200 group" 
                       id="header-avatar-link">
                        <div class="flex h-full w-full items-center justify-center font-black text-zinc-900 uppercase">
                            @if ($cartCount > 0)
                                <span class="icon-cart text-lg"></span>
                                <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center bg-[#7C45F5] text-[10px] font-black text-white border-2 border-zinc-900">
                                    {{ $cartCount }}
                                </span>
                            @else
                                <span class="text-xs">{{ $userInitial }}</span>
                            @endif
                        </div>
                    </a>

                    <v-cart-badge
                        cart-url="{{ route('shop.checkout.onepage.index') }}"
                        avatar-url="{{ route('shop.customers.account.index') }}"
                        :initial-count="{{ $cartCount }}"
                        user-initial="{{ $userInitial }}"
                    ></v-cart-badge>
                </div>

                <div class="flex flex-col">
                    <a href="{{ route('shop.customers.account.index') }}"
                        class="text-[12px] font-black uppercase tracking-widest text-zinc-900 hover:text-[#7C45F5] transition-colors truncate max-w-[140px]">
                        {{ '@' . $userName }}@if($authUser->is_investor)<span title="Инвестор" class="ml-1">💎</span>@endif
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('shop.customer.session.index') }}"
                class="bg-[#D6FF00] border-2 border-zinc-900 px-6 py-2.5 text-[12px] font-black uppercase tracking-widest text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all duration-200 whitespace-nowrap">
                Войти
            </a>
        @endauth
    </div>
</div>

@pushOnce('scripts')
    <script type="module">
        app.component('v-cart-badge', {
            template: `
                <a :href="cartCount > 0 ? cartUrl : avatarUrl" class="absolute inset-0 z-10 block opacity-0"></a>
            `,
            props: ['cartUrl', 'avatarUrl', 'initialCount', 'userInitial'],
            data() { return { cartCount: this.initialCount }; },
            mounted() {
                this.$emitter.on('update-mini-cart', cart => {
                    this.cartCount = cart ? (cart.items_count ?? cart.items?.length ?? 0) : 0;
                    // Update main UI via sync if needed, but here we let server-side do initial.
                });
            }
        });
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                </div>
            `,
            data() { return { currentMode: 'auto' }; },
            mounted() {
                let localTheme = localStorage.getItem('theme');
                if (localTheme) this.currentMode = localTheme;
            },
            methods: {
                setTheme(theme) {
                    this.currentMode = theme;
                    localStorage.setItem('theme', theme);
                    if (theme === 'dark') document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}