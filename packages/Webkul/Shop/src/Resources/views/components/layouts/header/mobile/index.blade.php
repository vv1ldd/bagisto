<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $showCompare = (bool) core()->getConfigData('catalog.products.settings.compare_option');

    $showWishlist = (bool) core()->getConfigData('customer.settings.wishlist.wishlist_option');
@endphp

<div class="flex flex-wrap gap-4 px-4 pt-4 pb-3 shadow-none lg:hidden glass-header border-none !bg-transparent">
    <div class="flex items-center justify-between w-full">
        <!-- Left Navigation -->
        <div class="flex items-center gap-x-5">
            {{-- Hamburger Menu Removed - Replaced with Floating Edge Trigger --}}

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.before') !!}

            <a href="{{ route('shop.home.index') }}" class="max-h-[30px]"
                aria-label="@lang('shop::app.components.layouts.header.mobile.bagisto')">
                <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}" width="131" height="29">
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Right Navigation -->
        <div class="flex items-center">
            @guest('customer')
                <a href="{{ route('shop.customer.session.create') }}"
                    class="flex items-center justify-center rounded-[20px] bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-5 py-2 text-[14px] font-bold text-white shadow-lg shadow-purple-500/20 transition-all active:scale-[0.97]">
                    Войти
                </a>
            @else
            <a href="{{ route('shop.customers.account.index') }}"
                class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white/80 p-1 pr-3 shadow-sm transition active:opacity-70 active:scale-[0.98] glass-card !border-white/50 text-[#7C45F5]">
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-[#7C45F5] to-[#FF4D6D] flex items-center justify-center text-white shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex flex-col justify-center">
                    <span class="text-[11px] font-bold leading-none">
                        {{ core()->formatPrice(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                    </span>
                </div>
            </a>
            @endauth
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>