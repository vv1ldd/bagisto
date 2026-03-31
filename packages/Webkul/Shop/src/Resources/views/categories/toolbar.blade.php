{!! view_render_event('bagisto.shop.categories.view.toolbar.before') !!}

<v-toolbar @filter-applied='setFilters("toolbar", $event)'></v-toolbar>

{!! view_render_event('bagisto.shop.categories.view.toolbar.after') !!}

@inject('toolbar' , 'Webkul\Product\Helpers\Toolbar')

@pushOnce('scripts')
    <script
        type="text/x-template"
        id='v-toolbar-template'
    >
        <div>
            <!-- Desktop Toolbar -->
            <div class="flex justify-between max-md:hidden">
                {!! view_render_event('bagisto.shop.categories.toolbar.filter.before') !!}

                <!-- Product Sorting Filters -->
                <x-shop::dropdown
                    class="z-[10]"
                    position="bottom-left"
                >
                    <x-slot:toggle>
                        <!-- Dropdown Toggler -->
                        <button class="group flex w-full max-w-[280px] cursor-pointer items-center justify-between gap-4 border-2 border-zinc-900 bg-white px-5 py-3 text-sm font-black uppercase tracking-widest transition-all hover:bg-zinc-50 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                            <span>
                                <span class="text-zinc-400 mr-2 text-[10px]">@lang('shop::app.products.sort-by.title'):</span>
                                @{{ sortLabel ?? "@lang('shop::app.products.sort-by.title')" }}
                            </span>

                            <span
                                class="icon-arrow-down text-xl transition-transform group-hover:rotate-180"
                                role="presentation"
                            ></span>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot name="menu" class="!p-0 !border-2 !border-zinc-900 !rounded-none !shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                        <x-shop::dropdown.menu.item
                            v-for="(sort, key) in filters.available.sort"
                            ::class="{'!bg-zinc-900 !text-white': sort.value == filters.applied.sort, 'hover:bg-zinc-100': sort.value != filters.applied.sort}"
                            class="!px-5 !py-3 !text-[11px] !font-black !uppercase !tracking-widest !border-b-2 !border-zinc-900 last:!border-b-0"
                            @click="apply('sort', sort.value)"
                        >
                            @{{ sort.title }}
                        </x-shop::dropdown.menu.item>
                    </x-slot>
                </x-shop::dropdown>

                {!! view_render_event('bagisto.shop.categories.toolbar.filter.after') !!}

                {!! view_render_event('bagisto.shop.categories.toolbar.pagination.before') !!}

                <!-- Product Pagination Limit -->
                <div class="flex items-center gap-10">
                    <!-- Product Pagination Limit -->
                    <x-shop::dropdown position="bottom-right">
                        <x-slot:toggle>
                            <!-- Dropdown Toggler -->
                            <button class="group flex w-full max-w-[140px] cursor-pointer items-center justify-between gap-4 border-2 border-zinc-900 bg-white px-5 py-3 text-sm font-black uppercase tracking-widest transition-all hover:bg-zinc-50 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                                <span>
                                    <span class="text-zinc-400 mr-2 text-[10px]">@lang('shop::app.categories.toolbar.show'):</span>
                                    @{{ filters.applied.limit ?? "@lang('shop::app.categories.toolbar.show')" }}
                                </span>

                                <span
                                    class="icon-arrow-down text-xl transition-transform group-hover:rotate-180"
                                    role="presentation"
                                ></span>
                            </button>
                        </x-slot>

                        <!-- Dropdown Content -->
                        <x-slot name="menu" class="!p-0 !border-2 !border-zinc-900 !rounded-none !shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                            <x-shop::dropdown.menu.item
                                v-for="(limit, key) in filters.available.limit"
                                ::class="{'!bg-zinc-900 !text-white': limit == filters.applied.limit, 'hover:bg-zinc-100': limit != filters.applied.limit}"
                                class="!px-5 !py-3 !text-[11px] !font-black !uppercase !tracking-widest !border-b-2 !border-zinc-900 last:!border-b-0"
                                @click="apply('limit', limit)"
                            >
                                @{{ limit }}
                            </x-shop::dropdown.menu.item>
                        </x-slot>
                    </x-shop::dropdown>

                    <!-- Listing Mode Switcher -->
                    <div class="flex items-center gap-2">
                        <span
                            class="group flex h-11 w-11 cursor-pointer items-center justify-center border-2 border-zinc-900 transition-all hover:bg-zinc-900 hover:text-white"
                            :class="filters.applied.mode === 'list' ? 'bg-zinc-900 text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]'"
                            role="button"
                            aria-label="@lang('shop::app.categories.toolbar.list')"
                            tabindex="0"
                            @click="changeMode('list')"
                        >
                            <span :class="(filters.applied.mode === 'list') ? 'icon-listing-fill' : 'icon-listing'" class="text-2xl"></span>
                        </span>

                        <span
                            class="group flex h-11 w-11 cursor-pointer items-center justify-center border-2 border-zinc-900 transition-all hover:bg-zinc-900 hover:text-white"
                            :class="filters.applied.mode === 'grid' || !filters.applied.mode ? 'bg-zinc-900 text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]'"
                            role="button"
                            aria-label="@lang('shop::app.categories.toolbar.grid')"
                            tabindex="0"
                            @click="changeMode('grid')"
                        >
                            <span :class="(filters.applied.mode === 'grid' || !filters.applied.mode) ? 'icon-grid-view-fill' : 'icon-grid-view'" class="text-2xl"></span>
                        </span>
                    </div>
                </div>

                {!! view_render_event('bagisto.shop.categories.toolbar.pagination.after') !!}
            </div>

            <!-- Mobile Toolbar -->
            <div class="md:hidden mt-4 overflow-x-auto no-scrollbar">
                <ul class="flex gap-2 pb-2">
                    <li
                        class="flex-none whitespace-nowrap border-2 border-zinc-900 px-4 py-2 text-[10px] font-black uppercase tracking-widest transition-all"
                        :class="sort.value == filters.applied.sort ? 'bg-zinc-900 text-white shadow-none' : 'bg-white text-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]'"
                        v-for="(sort, key) in filters.available.sort"
                        @click="apply('sort', sort.value)"
                    >
                        @{{ sort.title }}
                    </li>
                </ul>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-toolbar', {
            template: '#v-toolbar-template',

            data() {
                return {
                    filters: {
                        available: {
                            sort: @json($toolbar->getAvailableOrders()),

                            limit: @json($toolbar->getAvailableLimits()),

                            mode: @json($toolbar->getAvailableModes()),
                        },

                        default: {
                            sort: '{{ $toolbar->getOrder([])['value'] }}',

                            limit: '{{ $toolbar->getLimit([]) }}',

                            mode: '{{ $toolbar->getMode([]) }}',
                        },

                        applied: {
                            sort: '{{ $toolbar->getOrder($params ?? [])['value'] }}',

                            limit: '{{ $toolbar->getLimit($params ?? []) }}',

                            mode: '{{ $toolbar->getMode($params ?? []) }}',
                        }
                    }
                };
            },

            created() {
                let queryParams = new URLSearchParams(window.location.search);

                queryParams.forEach((value, filter) => {
                    if (['sort', 'limit', 'mode'].includes(filter)) {
                        this.filters.applied[filter] = value;
                    }
                });
            },

            mounted() {
                this.setFilters();
            },

            computed: {
                sortLabel() {
                    return this.filters.available.sort.find(sort => sort.value === this.filters.applied.sort).title;
                }
            },

            methods: {
                apply(type, value) {
                    this.filters.applied[type] = value;

                    this.setFilters();
                },

                changeMode(value = 'grid') {
                    this.filters.applied['mode'] = value;

                    this.setFilters();
                },

                setFilters() {
                    let filters = {};

                    for (let key in this.filters.applied) {
                        if (this.filters.applied[key] != this.filters.default[key]) {
                            filters[key] = this.filters.applied[key];
                        }
                    }

                    this.$emit('filter-applied', {
                        default: this.filters.default,
                        applied: filters,
                    });
                }
            },
        });
    </script>
@endPushOnce
