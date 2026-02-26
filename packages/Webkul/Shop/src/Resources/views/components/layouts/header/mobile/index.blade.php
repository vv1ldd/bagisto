<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $showCompare = (bool) core()->getConfigData('catalog.products.settings.compare_option');

    $showWishlist = (bool) core()->getConfigData('customer.settings.wishlist.wishlist_option');
@endphp

<div class="flex flex-wrap gap-4 px-4 pt-4 pb-3 shadow-sm lg:hidden">
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


                <!-- For Large screens -->
                <div class="max-md:hidden">
                    <x-shop::dropdown
                        position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                        <x-slot:toggle>
                            <img src="{{ auth()->guard('customer')->user()?->image_url ?? bagisto_asset('images/user-placeholder.png') }}"
                                class="h-8 w-8 cursor-pointer rounded-full shadow-sm" alt="User Profile">
                            </x-slot>

                            <!-- Guest Dropdown -->
                            @guest('customer')
                                <x-slot:content>
                                    <div class="grid gap-2.5">
                                        <p class="text-xl font-dmserif">
                                            @lang('shop::app.components.layouts.header.mobile.welcome-guest')
                                        </p>

                                        <p class="text-sm">
                                            @lang('shop::app.components.layouts.header.mobile.dropdown-text')
                                        </p>
                                    </div>

                                    <p class="w-full mt-3 border border-zinc-200"></p>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.customers_action.before') !!}

                                    <div class="flex flex-col gap-3 mt-6">
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
                                    </x-slot>
                            @endguest

                                <!-- Customers Dropdown -->
                                @auth('customer')
                                    <x-slot:content class="!p-0">
                                        <!-- Neon Accent Indicator -->
                                        <div class="h-0.5 w-full"
                                            style="background: linear-gradient(90deg, #A855F7 0%, #3B82F6 100%);"></div>

                                        <div class="grid gap-2.5 p-5 pb-0">
                                            <p class="text-xl font-dmserif" v-pre>
                                                {{ auth()->guard('customer')->user()->first_name }}
                                            </p>

                                            <p class="text-sm">
                                                @lang('shop::app.components.layouts.header.mobile.dropdown-text')
                                            </p>
                                        </div>

                                        <p class="w-full mt-3 border border-zinc-200"></p>

                                        <div class="mt-2.5 grid gap-1 pb-2.5">
                                            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.before') !!}

                                            <a class="px-5 py-2 text-base cursor-pointer"
                                                href="{{ route('shop.customers.account.index') }}">
                                                @lang('shop::app.components.layouts.header.mobile.profile')
                                            </a>

                                            <a class="px-5 py-2 text-base cursor-pointer"
                                                href="{{ route('shop.customers.account.orders.index') }}">
                                                @lang('shop::app.components.layouts.header.mobile.orders')
                                            </a>

                                            @if ($showWishlist)
                                                <a class="px-5 py-2 text-base cursor-pointer"
                                                    href="{{ route('shop.customers.account.wishlist.index') }}">
                                                    @lang('shop::app.components.layouts.header.mobile.wishlist')
                                                </a>
                                            @endif

                                            <!--Customers logout-->
                                            @auth('customer')
                                                <x-shop::form method="DELETE"
                                                    action="{{ route('shop.customer.session.destroy') }}" id="customerLogout" />

                                                <a class="px-5 py-2 text-base cursor-pointer"
                                                    href="{{ route('shop.customer.session.destroy') }}"
                                                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                                    @lang('shop::app.components.layouts.header.mobile.logout')
                                                </a>
                                            @endauth

                                            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.after') !!}
                                        </div>
                                        </x-slot>
                                @endauth
                    </x-shop::dropdown>
                </div>

                <!-- For Medium and small screen -->
                <div class="md:hidden">
                    @guest('customer')
                        <a href="{{ route('shop.customer.session.create') }}"
                            aria-label="@lang('shop::app.components.layouts.header.mobile.account')">
                            <img src="{{ bagisto_asset('images/user-placeholder.png') }}"
                                class="h-8 w-8 cursor-pointer rounded-full shadow-sm" alt="User Profile">
                        </a>
                    @endguest

                    <!-- Customers Dropdown -->
                    @auth('customer')
                        <a href="{{ route('shop.customers.account.index') }}"
                            aria-label="@lang('shop::app.components.layouts.header.mobile.account')">
                            <img src="{{ auth()->guard('customer')->user()?->image_url ?? bagisto_asset('images/user-placeholder.png') }}"
                                class="h-8 w-8 cursor-pointer rounded-full shadow-sm" alt="User Profile">
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}
</div>