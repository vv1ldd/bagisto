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
        <div>
            <div class="flex items-center gap-x-5 max-md:gap-x-4">


                <div>
                    <x-shop::dropdown
                        position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                        <x-slot:toggle>
                            <img src="{{ auth()->guard('customer')->user()?->image_url ?? bagisto_asset('images/user-placeholder.png') }}"
                                class="h-8 w-8 cursor-pointer rounded-full shadow-sm" alt="User Profile">
                            </x-slot>

                            <!-- Guest Dropdown -->
                            @guest('customer')
                                <x-slot:content class="min-w-[300px]">
                                    <div class="p-5 pb-0">
                                        <div class="grid gap-2.5">
                                            <p class="text-xl font-dmserif text-zinc-800">
                                                @lang('shop::app.components.layouts.header.mobile.welcome-guest')
                                            </p>

                                            <p class="text-sm text-zinc-500 leading-relaxed">
                                                @lang('shop::app.components.layouts.header.mobile.dropdown-text')
                                            </p>
                                        </div>
                                    </div>

                                    <p class="w-full mt-3 border border-zinc-100"></p>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.customers_action.before') !!}

                                    <div class="flex flex-col gap-3 p-5">
                                        {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.sign_in_button.before') !!}

                                        <a href="{{ route('shop.customer.session.create') }}"
                                            class="flex w-full items-center justify-center rounded-2xl bg-[#7C45F5] px-8 py-4 text-center font-medium text-white shadow-lg shadow-[#7C45F5]/20 transition-all hover:bg-[#6534d4] active:scale-[0.98]">
                                            @lang('shop::app.components.layouts.header.mobile.sign-in')
                                        </a>

                                        <a href="{{ route('shop.customers.register.index') }}"
                                            class="flex w-full items-center justify-center rounded-2xl border-2 border-[#7C45F5] bg-white px-8 py-3.5 text-center font-medium text-[#7C45F5] transition-all hover:bg-[#7C45F5]/5 active:scale-[0.98]">
                                            @lang('shop::app.components.layouts.header.mobile.sign-up')
                                        </a>

                                        {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.sign_in_button.after') !!}
                                    </div>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.customers_action.after') !!}
                                </x-slot:content>
                            @endguest

                            <!-- Customers Dropdown -->
                            @auth('customer')
                                <x-slot:content class="!p-0 min-w-[300px] overflow-hidden">
                                    <div class="h-0.5 w-full"
                                        style="background: linear-gradient(90deg, #A855F7 0%, #3B82F6 100%);"></div>

                                    <div class="p-5 pb-2">
                                        <a href="{{ route('shop.customers.account.profile.edit') }}"
                                            class="mb-4 flex items-center gap-4 rounded-2xl border border-white/40 bg-zinc-50/50 p-4 transition-all active:scale-[0.98] shadow-sm">
                                            <div class="flex-grow">
                                                <h2 class="text-base font-bold text-zinc-900 leading-tight">
                                                    {{ auth()->guard('customer')->user()->first_name }}
                                                    {{ auth()->guard('customer')->user()->last_name }}
                                                </h2>
                                                <p class="text-zinc-500 text-xs mt-0.5 break-all">
                                                    {{ auth()->guard('customer')->user()->email }}
                                                </p>
                                            </div>
                                            <span class="icon-arrow-right text-lg text-zinc-300 rtl:icon-arrow-left"></span>
                                        </a>

                                        <div class="space-y-1">
                                            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.before') !!}

                                            <a class="flex items-center gap-3 px-4 py-2.5 text-base font-medium text-zinc-700 transition active:bg-zinc-100 rounded-xl"
                                                href="{{ route('shop.customers.account.index') }}">
                                                <span class="icon-profile text-xl text-zinc-400"></span>
                                                @lang('shop::app.components.layouts.header.mobile.profile')
                                            </a>

                                            <a class="flex items-center gap-3 px-4 py-2.5 text-base font-medium text-zinc-700 transition active:bg-zinc-100 rounded-xl"
                                                href="{{ route('shop.customers.account.orders.index') }}">
                                                <span class="icon-orders text-xl text-zinc-400"></span>
                                                @lang('shop::app.components.layouts.header.mobile.orders')
                                            </a>

                                            @if ($showWishlist)
                                                <a class="flex items-center gap-3 px-4 py-2.5 text-base font-medium text-zinc-700 transition active:bg-zinc-100 rounded-xl"
                                                    href="{{ route('shop.customers.account.wishlist.index') }}">
                                                    <span class="icon-heart text-xl text-zinc-400"></span>
                                                    @lang('shop::app.components.layouts.header.mobile.wishlist')
                                                </a>
                                            @endif

                                            <a class="flex items-center gap-3 px-4 py-2.5 text-base font-medium text-zinc-700 transition active:bg-zinc-100 rounded-xl"
                                                href="{{ route('shop.checkout.cart.index') }}">
                                                <span class="icon-cart text-xl text-zinc-400"></span>
                                                @lang('shop::app.components.layouts.header.mobile.cart')
                                            </a>

                                            <div class="my-2 border-t border-zinc-100"></div>

                                            <x-shop::form method="DELETE"
                                                action="{{ route('shop.customer.session.destroy') }}" id="customerLogout" />

                                            <a class="flex items-center gap-3 px-4 py-2.5 text-base font-medium text-red-500 transition active:bg-red-50 rounded-xl"
                                                href="{{ route('shop.customer.session.destroy') }}"
                                                onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                                <span
                                                    class="icon-arrow-right text-xl text-red-300 rtl:icon-arrow-left"></span>
                                                @lang('shop::app.components.layouts.header.mobile.logout')
                                            </a>

                                            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.after') !!}
                                        </div>
                                    </div>
                                </x-slot:content>
                            @endauth
                    </x-shop::dropdown>
                </div>
            </div>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>