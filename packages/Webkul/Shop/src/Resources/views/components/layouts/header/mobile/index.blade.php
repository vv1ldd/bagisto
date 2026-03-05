<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $showCompare = (bool) core()->getConfigData('catalog.products.settings.compare_option');

    $showWishlist = (bool) core()->getConfigData('customer.settings.wishlist.wishlist_option');
@endphp

<div class="flex h-[72px] items-center gap-4 px-8 max-sm:px-4 shadow-none lg:hidden !bg-transparent">
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
            <v-header-cart></v-header-cart>

            @auth('customer')
                <a href="{{ route('shop.customers.account.index') }}"
                    class="flex items-center gap-2 rounded-full bg-white/40 px-1 pr-3 py-1 backdrop-blur-md border border-white/60">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-[#7C45F5] text-white font-bold text-[10px] uppercase shrink-0">
                        {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                    </div>
                    <span class="text-[13px] font-medium text-zinc-700 flex items-center gap-1 whitespace-nowrap">
                        @
                        {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                        @if(auth()->guard('customer')->user()->is_investor)
                            <span title="Инвестор" class="text-[13px] leading-none">💎</span>
                        @endif
                    </span>
                </a>
            @else
                <a href="{{ route('shop.customer.session.index') }}"
                    class="flex items-center justify-center rounded-full bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-4 py-2 text-[13px] font-bold text-white shadow-md shadow-purple-500/20 transition-all hover:shadow-purple-500/40 active:scale-[0.97] whitespace-nowrap">
                    Войти
                </a>
            @endauth

        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>