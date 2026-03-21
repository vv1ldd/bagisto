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

            <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2"
                aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
                <span
                    class="text-2xl font-black tracking-tighter text-[#7C45F5]">{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}</span>
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Right Navigation -->
        <div class="flex items-center">
            @auth('customer')
                <div class="flex items-center gap-2">
                    <div class="relative shrink-0 min-w-[28px]">
                        <a href="{{ $cartItemsCount > 0 ? route('shop.checkout.cart.index') : route('shop.customers.account.index') }}" 
                           class="block relative group"
                           id="header-mobile-avatar-link">
                            <div class="flex h-7 w-7 items-center justify-center bg-[#7C45F5] text-white font-bold text-[10px] shadow-sm leading-none ring-2 ring-white transition-all group-hover:scale-105 active:scale-95">
                                @if ($cartItemsCount > 0)
                                    <span class="icon-cart text-base"></span>
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center bg-white text-[8px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20">
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
                    <a href="{{ route('shop.customers.account.index') }}" class="text-[13px] font-medium text-zinc-700 flex items-center gap-1 whitespace-nowrap">
                        @
                        {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                        @if(auth()->guard('customer')->user()->is_investor)
                            <span title="Инвестор" class="text-[13px] leading-none">💎</span>
                        @endif
                    </a>
                </div>
            @else
                <v-header-cart></v-header-cart>
                <a href="{{ route('shop.customer.session.index') }}"
                    class="flex items-center justify-center ml-2 bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-4 py-2 text-[13px] font-bold text-white shadow-md shadow-purple-500/20 transition-all hover:shadow-purple-500/40 active:scale-[0.97] whitespace-nowrap">
                    Войти
                </a>
            @endauth

        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>