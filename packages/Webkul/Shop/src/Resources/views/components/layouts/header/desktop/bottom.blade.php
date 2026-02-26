{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

<div
    class="flex min-h-[64px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8">
    <!--
        This section will provide categories for the first, second, and third levels. If
        additional levels are required, users can customize them according to their needs.
    -->
    <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5">
        {{-- Hamburger Menu Removed - Replaced with Floating Edge Trigger --}}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}"
            aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.bagisto')">
            <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}" width="131"
                height="29" alt="{{ config('app.name') }}">
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

        <v-desktop-category>
            <div class="flex items-center gap-5">
                <span class="w-20 h-6 rounded shimmer" role="presentation"></span>

                <span class="w-20 h-6 rounded shimmer" role="presentation"></span>

                <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
            </div>
        </v-desktop-category>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
    </div>

    <!-- Right Nagivation Section -->
    <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}

        <!-- Right Navigation Links -->
        <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">



            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

            <!-- user profile -->
            <x-shop::dropdown
                position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <div class="flex items-center">
                        <img src="{{ auth()->guard('customer')->user()?->image_url ?? bagisto_asset('images/user-placeholder.png') }}"
                            class="h-9 w-9 cursor-pointer rounded-full shadow-sm hover:ring-2 hover:ring-[#7C45F5]/20 transition-all active:scale-95"
                            alt="User Profile" tabindex="0" role="button">
                    </div>
                    </x-slot>

                    <!-- Guest Dropdown -->
                    @guest('customer')
                        <x-slot:content class="!p-0 min-w-[320px] overflow-hidden">
                            <!-- Neon Accent Indicator -->
                            <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #A855F7 0%, #3B82F6 100%);">
                            </div>

                            <div class="p-6">
                                <div class="mb-5">
                                    <p class="text-2xl font-dmserif text-zinc-800">
                                        @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                                    </p>

                                    <p class="mt-2 text-sm text-zinc-500 leading-relaxed">
                                        @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3">
                                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.before') !!}

                                    <a href="{{ route('shop.customer.session.create') }}"
                                        class="flex items-center justify-center rounded-2xl bg-[#7C45F5] px-8 py-3.5 text-base font-medium text-white shadow-lg shadow-[#7C45F5]/20 transition-all hover:bg-[#6534d4] active:scale-[0.98]">
                                        @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                                    </a>

                                    <a href="{{ route('shop.customers.register.index') }}"
                                        class="flex items-center justify-center rounded-2xl border-2 border-[#7C45F5] px-8 py-3 text-base font-medium text-[#7C45F5] transition-all hover:bg-[#7C45F5]/5 active:scale-[0.98]">
                                        @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                                    </a>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.after') !!}
                                </div>
                            </div>
                        </x-slot:content>
                    @endguest

                    <!-- Customers Dropdown -->
                    @auth('customer')
                        <x-slot:content class="!p-0 min-w-[300px] overflow-hidden">
                            <!-- Neon Accent Indicator -->
                            <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #A855F7 0%, #3B82F6 100%);">
                            </div>

                            <div class="p-5">
                                <div class="mb-4">
                                    <p class="text-xl font-dmserif text-zinc-800" v-pre>
                                        @lang('shop::app.components.layouts.header.desktop.bottom.welcome'),
                                        {{ auth()->guard('customer')->user()->first_name }}
                                    </p>

                                    <p class="mt-1 text-sm text-zinc-500">
                                        @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}

                                    <a class="flex items-center gap-4 rounded-xl px-4 py-2.5 text-base font-medium text-zinc-700 transition hover:bg-zinc-100"
                                        href="{{ route('shop.customers.account.index') }}">
                                        <div class="shrink-0">
                                            <img src="{{ auth()->guard('customer')->user()?->image_url ?? bagisto_asset('images/user-placeholder.png') }}"
                                                class="h-7 w-7 rounded-full shadow-sm">
                                        </div>
                                        @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                                    </a>

                                    <a class="flex items-center gap-4 rounded-xl px-4 py-2.5 text-base font-medium text-zinc-700 transition hover:bg-zinc-100"
                                        href="{{ route('shop.customers.account.orders.index') }}">
                                        <span class="icon-orders text-xl text-zinc-400"></span>
                                        @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                                    </a>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <a class="flex items-center gap-4 rounded-xl px-4 py-2.5 text-base font-medium text-zinc-700 transition hover:bg-zinc-100"
                                            href="{{ route('shop.customers.account.wishlist.index') }}">
                                            <span class="icon-heart text-xl text-zinc-400"></span>
                                            @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                                        </a>
                                    @endif

                                    <div class="my-2 border-t border-zinc-100"></div>

                                    <x-shop::form method="DELETE" action="{{ route('shop.customer.session.destroy') }}"
                                        id="customerLogout" />

                                    <a class="flex items-center gap-4 rounded-xl px-4 py-2.5 text-base font-medium text-red-500 transition hover:bg-red-50"
                                        href="{{ route('shop.customer.session.destroy') }}"
                                        onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                        <span class="icon-arrow-right text-xl text-red-300 rtl:icon-arrow-left"></span>
                                        @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                                    </a>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                                </div>
                            </div>
                        </x-slot:content>
                    @endauth
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-desktop-category-template">
                                                            <!-- Loading State -->
    <div class="flex items-center gap-5" v-if="isLoading">
        <span class="w-20 h-6 rounded shimmer" role="presentation"></span>

        <span class="w-20 h-6 rounded shimmer" role="presentation"></span>

        <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
    </div>

    <!-- Default category layout -->
    <div class="flex items-center"
        v-else-if="'{{ core()->getConfigData('general.design.categories.category_view') }}' !== 'sidebar'">
        <div class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
            v-for="category in categories">
            <span>
                <a :href="category.url" class="inline-block px-5 uppercase">
                    @{{ category.name }}
                </a>
            </span>

            <div class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                v-if="category.children && category.children.length">
                <div class="flex justify-between gap-x-[70px]">
                    <div class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                        v-for="pairCategoryChildren in pairCategoryChildren(category)">
                        <template v-for="secondLevelCategory in pairCategoryChildren">
                            <p class="font-medium text-navyBlue">
                                <a :href="secondLevelCategory.url">
                                    @{{ secondLevelCategory.name }}
                                </a>
                            </p>

                            <ul class="grid grid-cols-[1fr] gap-3"
                                v-if="secondLevelCategory.children && secondLevelCategory.children.length">
                                <li class="text-sm font-medium text-zinc-500"
                                    v-for="thirdLevelCategory in secondLevelCategory.children">
                                    <a :href="thirdLevelCategory.url">
                                        @{{ thirdLevelCategory.name }}
                                    </a>
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar category layout -->
    <div v-else>
        <!-- Categories Navigation -->
        <div class="flex items-center">
            <!-- "All" button for opening the category drawer -->
            <div class="flex h-[77px] cursor-pointer items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                @click="toggleCategoryDrawer">
                <span class="flex items-center gap-1 px-5 uppercase">
                    <span class="text-xl icon-hamburger"></span>

                    @lang('shop::app.components.layouts.header.desktop.bottom.all')
                </span>
            </div>

            <!-- Show only first 4 categories in main navigation -->
            <div class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                v-for="category in categories.slice(0, 4)">
                <span>
                    <a :href="category.url" class="inline-block px-5 uppercase">
                        @{{ category.name }}
                    </a>
                </span>

                <!-- Dropdown for each category -->
                <div class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                    v-if="category.children && category.children.length">
                    <div class="flex justify-between gap-x-[70px]">
                        <div class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                            v-for="pairCategoryChildren in pairCategoryChildren(category)">
                            <template v-for="secondLevelCategory in pairCategoryChildren">
                                <p class="font-medium text-navyBlue">
                                    <a :href="secondLevelCategory.url">
                                        @{{ secondLevelCategory.name }}
                                    </a>
                                </p>

                                <ul class="grid grid-cols-[1fr] gap-3"
                                    v-if="secondLevelCategory.children && secondLevelCategory.children.length">
                                    <li class="text-sm font-medium text-zinc-500"
                                        v-for="thirdLevelCategory in secondLevelCategory.children">
                                        <a :href="thirdLevelCategory.url">
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagisto Drawer Integration -->
        <x-shop::drawer position="left" width="400px" ::is-active="isDrawerActive" @toggle="onDrawerToggle"
            @close="onDrawerClose">
            <x-slot:toggle></x-slot>

                <x-slot:header class="border-b border-gray-200">
                    <div class="flex items-center justify-between w-full">
                        <p class="text-xl font-medium">
                            @lang('shop::app.components.layouts.header.desktop.bottom.categories')
                        </p>
                    </div>
                    </x-slot>

                    <x-slot:content class="!px-0">
                        <!-- Wrapper with transition effects -->
                        <div class="relative h-full overflow-hidden">
                            <!-- Sliding container -->
                            <div class="flex h-full transition-transform duration-300" :class="{
                                                                                    'ltr:translate-x-0 rtl:translate-x-0': currentViewLevel !== 'third',
                                                                                    'ltr:-translate-x-full rtl:translate-x-full': currentViewLevel === 'third'
                                                                                }">
                                <!-- First level view -->
                                <div class="h-[calc(100vh-74px)] w-full flex-shrink-0 overflow-auto">
                                    <div class="py-4">
                                        <div v-for="category in categories" :key="category.id"
                                            :class="{'mb-2': category.children && category.children.length}">
                                            <div
                                                class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100">
                                                <a :href="category.url" class="text-base font-medium text-black">
                                                    @{{ category.name }}
                                                </a>
                                            </div>

                                            <!-- Second Level Categories -->
                                            <div v-if="category.children && category.children.length">
                                                <div v-for="secondLevelCategory in category.children"
                                                    :key="secondLevelCategory.id">
                                                    <div class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100"
                                                        @click="showThirdLevel(secondLevelCategory, category, $event)">
                                                        <a :href="secondLevelCategory.url" class="text-sm font-normal">
                                                            @{{ secondLevelCategory.name }}
                                                        </a>

                                                        <span
                                                            v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                                            class="icon-arrow-right rtl:icon-arrow-left"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Third level view -->
                                <div class="flex-shrink-0 w-full h-full" v-if="currentViewLevel === 'third'">
                                    <div class="px-6 py-4 border-b border-gray-200">
                                        <button @click="goBackToMainView"
                                            class="flex items-center justify-center gap-2 focus:outline-none"
                                            aria-label="Go back">
                                            <span class="text-lg icon-arrow-left rtl:icon-arrow-right"></span>

                                            <p class="text-base font-medium text-black">
                                                @lang('shop::app.components.layouts.header.desktop.bottom.back-button')
                                            </p>
                                        </button>
                                    </div>

                                    <!-- Third Level Content -->
                                    <div class="py-4">
                                        <div v-for="thirdLevelCategory in currentSecondLevelCategory?.children"
                                            :key="thirdLevelCategory.id" class="mb-2">
                                            <a :href="thirdLevelCategory.url"
                                                class="block px-6 py-2 text-sm transition-colors duration-200 hover:bg-gray-100">
                                                @{{ thirdLevelCategory.name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </x-slot>
        </x-shop::drawer>
    </div>
    </script>

    <script type="module">
        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',

            data() {
                return {
                    isLoading: true,
                    categories: [],
                    isDrawerActive: false,
                    currentViewLevel: 'main',
                    currentSecondLevelCategory: null,
                    currentParentCategory: null
                }
            },

            mounted() {
                this.initCategories();
            },

            methods: {
                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);
                            this.isLoading = false;

                            return;
                        }

                    } catch (e) { }

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree', ['show_in_header' => 1]) }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                pairCategoryChildren(category) {
                    if (!category.children) return [];

                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) {
                            result.push(array.slice(index, index + 2));
                        }
                        return result;
                    }, []);
                },

                toggleCategoryDrawer() {
                    this.isDrawerActive = !this.isDrawerActive;
                    if (this.isDrawerActive) {
                        this.currentViewLevel = 'main';
                    }
                },

                onDrawerToggle(event) {
                    this.isDrawerActive = event.isActive;
                },

                onDrawerClose(event) {
                    this.isDrawerActive = false;
                },

                showThirdLevel(secondLevelCategory, parentCategory, event) {
                    if (secondLevelCategory.children && secondLevelCategory.children.length) {
                        this.currentSecondLevelCategory = secondLevelCategory;
                        this.currentParentCategory = parentCategory;
                        this.currentViewLevel = 'third';

                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    }
                },

                goBackToMainView() {
                    this.currentViewLevel = 'main';
                }
            },
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}