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
                                <x-slot:content class="!p-0 min-w-[280px] overflow-hidden">
                                    <div class="h-0.5 w-full"
                                        style="background: linear-gradient(90deg, #A855F7 0%, #3B82F6 100%);"></div>

                                    <div class="p-6">
                                        <div class="flex flex-col gap-3 text-left">
                                            <p
                                                class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.25em] ml-1 opacity-70 uppercase">
                                                Личный кабинет
                                            </p>

                                            <a href="{{ route('shop.customer.session.create') }}"
                                                class="flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-6 py-4 text-center text-[15px] font-bold text-white shadow-xl shadow-purple-500/25 transition-all active:scale-[0.98]">
                                                Войти / Регистрация
                                            </a>
                                        </div>
                                    </div>
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