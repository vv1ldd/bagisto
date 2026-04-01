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
        <div class="flex items-center gap-x-4 h-full">
            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.before') !!}

            <a href="{{ route('shop.home.index') }}" class="group flex items-center h-full"
                aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
                <div class="relative w-9 h-9 flex items-center justify-center bg-white border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-200">
                    <span class="text-[17px] font-black text-zinc-900 tracking-tighter italic ml-[1px] mt-[1px]">M</span>
                </div>
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Middle: Search -->
        <div class="flex-grow px-2 md:px-4 max-w-sm">
            <form action="{{ route('shop.search.index') }}" method="GET" class="flex items-center h-9 bg-white border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-200">
                <input 
                    type="text" 
                    name="query" 
                    placeholder="ПОИСК..." 
                    class="w-full h-full px-3 text-[10px] font-black uppercase tracking-widest text-zinc-900 placeholder-zinc-300 focus:outline-none border-none ring-0 bg-transparent"
                >
                <button type="submit" class="pr-3 flex items-center justify-center text-zinc-900 hover:text-[#7C45F5] transition-colors">
                    <span class="icon-search text-lg"></span>
                </button>
            </form>
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


            @auth('customer')
                <div class="flex flex-col items-end gap-1">
                    <div class="flex items-center gap-2">
                        {{-- QR Scanner Button --}}
                        <button @click="$emitter.emit('open-qr-scanner')" 
                           class="flex h-9 w-9 items-center justify-center bg-[#D6FF00] border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-200">
                            <svg class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </button>
                        <div class="relative shrink-0">
                            <a href="{{ $cartItemsCount > 0 ? route('shop.checkout.onepage.index') : route('shop.customers.account.index') }}" 
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
                                cart-url="{{ route('shop.checkout.onepage.index') }}"
                                avatar-url="{{ route('shop.customers.account.index') }}"
                                :initial-count="{{ $cartItemsCount }}"
                                user-initial="{{ $userInitial }}"
                            ></v-cart-badge>
                        </div>
                    </div>
                    <x-shop::live-balance :user="$authUser" class="text-[10px] h-3 mr-0.5" />
                </div>
            @else
                <v-header-cart></v-header-cart>
                <a href="{{ route('shop.customer.session.index') }}"
                    class="bg-[#7C45F5] border-2 border-zinc-900 px-4 h-9 flex items-center text-[11px] font-black uppercase tracking-widest text-white shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all whitespace-nowrap">
                    Войти
                </a>
            @endauth
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>