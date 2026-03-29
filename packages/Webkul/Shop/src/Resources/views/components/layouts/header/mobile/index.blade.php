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

<div class="flex h-full items-center gap-4 px-4 shadow-none lg:hidden bg-white">
    <div class="flex items-center justify-between w-full h-full">
        <!-- Left Navigation -->
        <div class="flex items-center gap-x-5 h-full">
            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.before') !!}

            <a href="{{ route('shop.home.index') }}" class="group flex items-center h-full"
                aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
                <div class="relative w-9 h-9 flex items-center justify-center bg-white border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-200">
                    <span class="text-[17px] font-black text-zinc-900 tracking-tighter italic ml-[1px] mt-[1px]">M</span>
                </div>
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Right Navigation -->
        <div class="flex items-center gap-3">
            @if(session()->get('logged_in_via_tma'))
                <a href="{{ route('shop.customers.account.index') }}" 
                   class="flex h-9 items-center gap-1.5 bg-[#D6FF00] px-3 text-[10px] font-black text-black border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all uppercase tracking-tighter">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Меню
                </a>
            @endif

            <v-theme-switcher></v-theme-switcher>

            @auth('customer')
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        <a href="{{ $cartItemsCount > 0 ? route('shop.checkout.cart.index') : route('shop.customers.account.index') }}" 
                           class="relative block h-9 w-9 bg-white border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-200"
                           id="header-mobile-avatar-link">
                            <div class="flex h-full w-full items-center justify-center font-black text-zinc-900 uppercase">
                                @if ($cartItemsCount > 0)
                                    <span class="icon-cart text-lg"></span>
                                    <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center bg-[#7C45F5] text-[9px] font-black text-white border-2 border-zinc-900">
                                        {{ $cartItemsCount }}
                                    </span>
                                @else
                                    <span class="text-[11px] uppercase">{{ $userInitial }}</span>
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
                </div>
            @else
                <v-header-cart></v-header-cart>
                <a href="{{ route('shop.customer.session.index') }}"
                    class="bg-[#7C45F5] border-2 border-zinc-900 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-white shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all whitespace-nowrap">
                    Войти
                </a>
            @endauth
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>
h.after') !!}
</div>