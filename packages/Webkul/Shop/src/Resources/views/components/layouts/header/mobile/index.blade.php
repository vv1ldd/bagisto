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
                class="flex items-center bg-white px-3 h-[29px] rounded-2xl border border-zinc-200 shadow-sm transition active:scale-[0.98]">
                <span class="text-[13px] font-mono text-zinc-900 font-bold whitespace-nowrap">
                    @
                    {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                </span>
            </a>
            @endauth
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>