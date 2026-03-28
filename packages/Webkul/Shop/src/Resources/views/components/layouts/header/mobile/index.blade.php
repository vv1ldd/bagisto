<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $showCompare = (bool) core()->getConfigData('catalog.products.settings.compare_option');

    $showWishlist = (bool) core()->getConfigData('customer.settings.wishlist.wishlist_option');

    /* Get initial cart count for server-side rendering */
    $cart = \Webkul\Checkout\Facades\Cart::getCart();
    $cartItemsCount = $cart ? $cart->items->count() : 0;

    $authUser = auth()->guard('customer')->user();
    $userInitial = $authUser ? strtoupper(substr($authUser->credits_alias ?: $authUser->username, 0, 1)) : null;
@endphp

<div class="flex h-[64px] items-center gap-4 px-8 max-sm:px-4 shadow-none lg:hidden !bg-transparent">
    <div class="flex items-center justify-between w-full">
        <!-- Left Navigation -->
        <div class="flex items-center gap-x-5">
            {{-- Hamburger Menu Removed - Replaced with Floating Edge Trigger --}}

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.before') !!}

            <a href="{{ route('shop.home.index') }}" class="group"
                aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
                <div class="relative w-9 h-9">
                    <div class="absolute inset-0 bg-[#7C45F5] rounded-[0.6rem] rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-lg shadow-[#7C45F5]/20"></div>
                    <div class="absolute inset-0 bg-white rounded-[0.6rem] flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                        <span class="text-[17px] font-black text-zinc-900 tracking-tighter italic ml-[1px] mt-[1px]">M</span>
                    </div>
                </div>
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Right Navigation -->
        <div class="flex items-center gap-2">
            @if(session()->get('logged_in_via_tma'))
                <a href="{{ route('shop.customers.account.index') }}" 
                   class="flex h-8 items-center gap-1.5 bg-[#D6FF00] px-3 text-[11px] font-black text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all uppercase tracking-tighter rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Меню
                </a>
            @endif

            <v-theme-switcher></v-theme-switcher>

            @auth('customer')
                <div class="flex items-center gap-2">
                    <div class="relative shrink-0 min-w-[28px]">
                        <a href="{{ $cartItemsCount > 0 ? route('shop.checkout.cart.index') : route('shop.customers.account.index') }}" 
                           class="block relative group"
                           id="header-mobile-avatar-link">
                            <div class="flex h-8 w-8 items-center justify-center bg-[#7C45F5] text-white font-bold text-[11px] shadow-[0_0_15px_rgba(124,69,245,0.3)] leading-none ring-1 ring-white/10 transition-all group-hover:scale-105 active:scale-95 rounded-full">
                                @if ($cartItemsCount > 0)
                                    <span class="icon-cart text-base"></span>
                                    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center bg-white text-[9px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20 rounded-full">
                                        {{ $cartItemsCount }}
                                    </span>
                                @else
                                    <span class="uppercase">{{ $userInitial }}</span>
                                @endif
                            </div>
                        </a>
                        <v-cart-badge
                            cart-url="{{ route('shop.checkout.cart.index') }}"
                            avatar-url="{{ route('shop.customers.account.index') }}"
                            :initial-count="{{ $cartItemsCount }}"
                            user-initial="{{ $userInitial }}"
                        ></v-cart-badge>
                    </div>
                    <a href="{{ route('shop.customers.account.index') }}" class="text-[13px] font-bold text-zinc-600 dark:text-zinc-400 hover:text-[#7C45F5] dark:hover:text-white transition-colors flex items-center gap-1 whitespace-nowrap tracking-tight">
                        @
                        {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                        @if(auth()->guard('customer')->user()->is_investor)
                            <span title="Инвестор" class="text-[13px] leading-none ml-0.5">💎</span>
                        @endif
                    </a>
                </div>
            @else
                <v-header-cart></v-header-cart>
                <a href="{{ route('shop.customer.session.index') }}"
                    class="flex items-center justify-center ml-2 bg-[#7C45F5] px-4 py-2 text-[13px] font-bold text-white shadow-[0_0_15px_rgba(124,69,245,0.4)] transition-all hover:bg-[#8A5CF7] active:scale-[0.97] rounded-full whitespace-nowrap">
                    Войти
                </a>
            @endauth

        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>