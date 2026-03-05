@php
    $showCategories = true; // Always allow categories to show, toolbar will teleport next to them if available
@endphp

<div class="flex min-h-[88px] w-full items-center justify-between px-8 max-sm:px-4 mx-auto max-w-7xl gap-4">
    {{-- LEFT: Logo & Categories --}}
    <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5 flex-shrink-0">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2"
            aria-label="{{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}">
            <span class="text-2xl font-black tracking-tighter text-[#7C45F5] leading-none">
                {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

        @if ($showCategories)
            <v-desktop-category>
                <div class="flex items-center gap-5">
                    <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                    <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                    <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                </div>
            </v-desktop-category>
        @endif

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
    </div>

    {{-- CENTER: Toolbar Teleport Target --}}
    <div id="header-toolbar-teleport-target" class="flex-1 flex justify-center min-w-0 px-2 overflow-hidden"></div>

    {{-- RIGHT: Unified Navigation Section (Profile & Cart) --}}
    <div class="flex items-center flex-shrink-0">
        <v-header-nav></v-header-nav>
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
        <div class="group relative flex h-[64px] items-center" v-for="category in categories">
            <span>
                <a :href="category.url"
                    class="inline-block px-4 text-sm font-semibold uppercase tracking-wider text-zinc-600 transition-colors hover:text-[#7C45F5]">
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
                    <div class="flex flex-col gap-4 w-full">
                        <div class="flex items-center justify-between w-full">
                            <p class="text-xl font-medium">
                                @lang('shop::app.components.layouts.header.desktop.bottom.categories')
                            </p>
                        </div>

                        {{-- Sidebar Search --}}
                        <div class="relative px-6 pb-2">
                            <form action="{{ route('shop.search.index') }}" class="relative group">
                                <span
                                    class="icon-search absolute left-3 top-1/2 -translate-y-1/2 text-lg text-zinc-400 group-hover:text-[#7C45F5] transition-colors"></span>
                                <input type="text" name="query" value="{{ request('query') }}"
                                    placeholder="Поиск товаров..."
                                    class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-2.5 pl-10 pr-4 text-sm font-medium text-zinc-700 transition-all focus:border-[#7C45F5] focus:bg-white focus:outline-none shadow-sm"
                                    minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                                    maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                                    required />
                            </form>
                        </div>
                    </div>
                    </x-slot>

                    <x-slot:content class="!px-0">
                        <!-- Wrapper with transition effects -->
                        <div class="relative h-full overflow-hidden">
                            <!-- Sliding container -->
                            <div class="flex h-full transition-transform duration-300"
                                :class="{
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

        app.component('v-header-nav', {
            template: `
                    <div class="flex items-center gap-3">
                        @guest('customer')
                            <a href="{{ route('shop.customer.session.create') }}"
                                class="flex items-center justify-center rounded-[24px] bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-6 py-2.5 text-[14px] font-bold text-white shadow-lg shadow-purple-500/20 transition-all hover:shadow-purple-500/40 active:scale-[0.97]">
                                Войти / Регистрация
                            </a>
                        @else
                            <div class="flex items-center gap-3 rounded-full bg-white/40 p-1 pr-6 backdrop-blur-md border border-white/60 shadow-sm">
                                {{-- Unified Avatar/Cart Icon --}}
                                <a :href="'{{ route('shop.checkout.cart.index') }}'" class="relative group">
                                    <template v-if="cart && cart.items.length > 0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#7C45F5] text-white shadow-md transition-all group-hover:scale-110 active:scale-95">
                                            <span class="icon-cart text-xl"></span>
                                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-white text-[10px] font-black text-[#7C45F5] shadow-sm border border-[#7C45F5]/20">
                                                @{{ cart.items.length }}
                                            </span>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#7C45F5] text-white font-bold text-sm uppercase shadow-sm">
                                            {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                                        </div>
                                    </template>
                                </a>

                                <a href="{{ route('shop.customers.account.index') }}"
                                    class="text-[14px] font-semibold text-zinc-700 hover:text-[#7C45F5] transition-colors flex items-center gap-1">
                                    @
                                    {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                                    @if(auth()->guard('customer')->user()->is_investor)
                                        <span title="Инвестор" class="text-[14px] leading-none">💎</span>
                                    @endif
                                </a>
                            </div>
                        @endguest
                    </div>
                `,

            data() {
                return {
                    cart: null,
                }
            },

            mounted() {
                this.getCart();
                this.$emitter.on('update-mini-cart', (cart) => {
                    this.cart = cart;
                });
            },

            methods: {
                getCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(response => {
                            this.cart = response.data.data;
                        })
                        .catch(error => { });
                }
            }
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}