<?php
$searchTitle = $suggestion ?? $query;
$title = $searchTitle ? trans('shop::app.search.title', ['query' => $searchTitle]) : trans('shop::app.search.results');
$searchInstead = $suggestion ? $query : null;
?>
<!-- SEO Meta Content -->
@push('meta')
<meta name="description" content="{{ $title }}" />

<meta name="keywords" content="{{ $title }}" />
@endPush

<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{ $title }}
        </x-slot>

        <div class="container px-[60px] max-lg:px-8 max-sm:px-0">
            @if (request()->has('image-search'))
                @include('shop::search.images.results')
            @endif

            {{-- Removed the on-page heading "Search results" / "Результаты поиска" --}}

            @if ($searchInstead)
                <form action="{{ route('shop.search.index', ['suggest' => false]) }}"
                    class="flex max-w-[445px] items-center" role="search">
                    <input type="text" name="query" class="hidden" value="{{ $searchInstead }}">

                    <input type="text" name="suggest" class="hidden" value="0">

                    <p class="mt-1 text-sm text-gray-600" v-pre>
                        {{ trans('shop::app.search.suggest') }}

                        <button type="submit" class="text-[#7C45F5] hover:text-[#6c39e0] hover:underline"
                            aria-label="{{ trans('shop::app.components.layouts.header.desktop.bottom.submit') }}">
                            {{ $searchInstead }}
                        </button>
                    </p>
                </form>
            @endif
        </div>

        <!-- Product Listing -->
        <v-search>
            <x-shop::shimmer.categories.view />
        </v-search>

        @pushOnce('scripts')
            <script type="text/x-template" id="v-search-template">
                                                            <div class="container px-[60px] max-lg:px-8 max-sm:px-0">
                                                        <div class="md:mt-10">
                                                            <template v-if="products.length || isLoading">
                                                                @include('shop::categories.toolbar')
                                                            </template>

                                                            <!-- Product List Card Container -->
                                                            <div
                                                                class="mt-8 grid grid-cols-1 gap-6"
                                                                v-if="filters.toolbar.applied.mode === 'list' || (!filters.toolbar.applied.mode && filters.toolbar.default.mode === 'list')"
                                                            >
                                                                <!-- Product Card Shimmer Effect -->
                                                                <template v-if="isLoading">
                                                                    <x-shop::shimmer.products.cards.list count="12" />
                                                                </template>

                                                                <!-- Product Card Listing -->
                                                                <template v-else>
                                                                    <template v-if="products.length">
                                                                        <x-shop::products.card
                                                                            ::mode="'list'"
                                                                            v-for="product in products"
                                                                        />
                                                                    </template>

                                                                    <!-- Empty Products Container -->
                                                                    <template v-else>
                                                                        <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                                                            <div class="mb-4 flex h-24 w-24 items-center justify-center  bg-zinc-100 text-zinc-300">
                                                                                <span class="icon-cart text-[54px]"></span>
                                                                            </div>

                                                                            <p
                                                                                class="text-xl max-sm:text-sm"
                                                                                role="heading"
                                                                            >
                                                                                @lang('shop::app.categories.view.empty')
                                                                            </p>
                                                                        </div>
                                                                    </template>
                                                                </template>
                                                            </div>

                                                            <!-- Product Grid Card Container -->
                                                            <div v-else>
                                                                <!-- Product Card Shimmer Effect -->
                                                                <template v-if="isLoading">
                                                                    <div class="mt-8 grid grid-cols-5 gap-4 max-1060:grid-cols-3 max-md:grid-cols-2 max-md:gap-x-3 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-y-4">
                                                                        <x-shop::shimmer.products.cards.grid count="12" />
                                                                    </div>
                                                                </template>

                                                                <!-- Product Card Listing -->
                                                                <template v-else>
                                                                    <template v-if="products.length">
                                                                        <div class="mt-8 grid grid-cols-5 gap-4 max-1060:grid-cols-3 max-md:grid-cols-2 max-md:mt-5 max-md:justify-items-center max-md:gap-x-3 max-md:gap-y-4">
                                                                            <x-shop::products.card
                                                                                ::mode="'grid'"
                                                                                v-for="product in products"
                                                                                :navigation-link="route('shop.search.index')"
                                                                            />
                                                                        </div>
                                                                    </template>

                                                                    <!-- Empty Products Container -->
                                                                    <template v-else>
                                                                        <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                                                            <div class="mb-4 flex h-24 w-24 items-center justify-center  bg-zinc-100 text-zinc-300">
                                                                                <span class="icon-cart text-[54px]"></span>
                                                                            </div>

                                                                            <p
                                                                                class="text-xl max-sm:text-sm"
                                                                                role="heading"
                                                                            >
                                                                                @lang('shop::app.categories.view.empty')
                                                                            </p>
                                                                        </div>
                                                                    </template>
                                                                </template>
                                                            </div>

                                                            <!-- Infinite Scroll Sentinel -->
                                                            <div id="infinite-scroll-sentinel" ref="infiniteScrollSentinel" class="flex justify-center py-10" v-if="links.next">
                                                                <div class="mt-8 grid grid-cols-5 gap-4 max-1060:grid-cols-3 max-md:grid-cols-2 max-md:gap-x-3 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-y-4 w-full" v-if="loader">
                                                                    <x-shop::shimmer.products.cards.grid count="5" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </script>

            <script type="module">
                app.component('v-search', {
                    template: '#v-search-template',

                    data() {
                        return {
                            isMobile: window.innerWidth <= 767,

                            isLoading: true,

                            isDrawerActive: {
                                toolbar: false,

                                filter: false,
                            },

                            filters: {
                                toolbar: {
                                    default: {},

                                    applied: {},
                                },

                                filter: {},
                            },

                            products: [],

                            links: {},

                            loader: false,
                        }
                    },

                    computed: {
                        queryParams() {
                            let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar.applied);

                            return this.removeJsonEmptyValues(queryParams);
                        },

                        queryString() {
                            return this.jsonToQueryString(this.queryParams);
                        },
                    },

                    watch: {
                        queryParams() {
                            this.getProducts();
                        },

                        queryString() {
                            window.history.pushState({}, '', '?' + this.queryString);
                        },
                    },

                    mounted() {
                        this.getProducts();

                        this.$emitter.on('header-filters-applied', filters => {
                            this.setFilters('filter', filters);
                        });

                        this.$emitter.on('header-toolbar-applied', toolbar => {
                            this.setFilters('toolbar', toolbar);
                        });

                        this.initInfiniteScroll();
                    },

                    beforeUnmount() {
                        if (this.observer) {
                            this.observer.disconnect();
                        }
                    },

                    methods: {
                        setFilters(type, filters) {
                            this.filters[type] = filters;
                        },

                        clearFilters(type, filters) {
                            this.filters[type] = {};
                        },

                        getProducts() {
                            this.isDrawerActive = {
                                toolbar: false,
                                filter: false,
                            };

                            this.isLoading = true;

                            this.$axios.get(("{{ route('shop.api.products.index') }}"), {
                                params: this.queryParams
                            })
                                .then(response => {
                                    this.isLoading = false;

                                    this.products = response.data.data;

                                    this.links = response.data.links;
                                })
                                .catch(error => {
                                    console.error("Failed to fetch search results:", error);

                                    this.isLoading = false;
                                });
                        },

                        initInfiniteScroll() {
                            this.$nextTick(() => {
                                const sentinel = this.$refs.infiniteScrollSentinel;
                                if (!sentinel) return;

                                this.observer = new IntersectionObserver((entries) => {
                                    if (entries[0].isIntersecting && this.links.next && !this.loader) {
                                        this.loadMoreProducts();
                                    }
                                }, {
                                    rootMargin: '200px',
                                });

                                this.observer.observe(sentinel);
                            });
                        },

                        loadMoreProducts() {
                            if (this.loader || !this.links.next) {
                                return;
                            }

                            this.loader = true;

                            this.$axios.get(this.links.next)
                                .then(response => {
                                    this.loader = false;

                                    this.products = [...this.products, ...response.data.data];

                                    this.links = response.data.links;
                                })
                                .catch(error => {
                                    this.loader = false;
                                    console.error("Failed to load more search results:", error);
                                });
                        },

                        removeJsonEmptyValues(params) {
                            Object.keys(params).forEach(function (key) {
                                if ((!params[key] && params[key] !== undefined)) {
                                    delete params[key];
                                }

                                if (Array.isArray(params[key])) {
                                    params[key] = params[key].join(',');
                                }
                            });

                            return params;
                        },

                        jsonToQueryString(params) {
                            let parameters = new URLSearchParams();

                            for (const key in params) {
                                parameters.append(key, params[key]);
                            }

                            return parameters.toString();
                        }
                    },
                });
            </script>
        @endPushOnce
</x-shop::layouts>