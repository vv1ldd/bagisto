{!! view_render_event('bagisto.shop.categories.view.filters.before') !!}

@inject('toolbar', 'Webkul\Product\Helpers\Toolbar')

<!-- Horizontal Filter Bar — shown on all screen sizes (desktop + mobile) -->
<div>
    <v-filters @filter-applied="setFilters('filter', $event)" @filter-clear="clearFilters('filter', $event)"
        @toolbar-applied="setFilters('toolbar', $event)">
        <x-shop::shimmer.categories.filters />
    </v-filters>
</div>



{!! view_render_event('bagisto.shop.categories.view.filters.after') !!}

@pushOnce('scripts')

    {{-- ── v-filters template ──────────────────────────────────────── --}}
    <script type="text/x-template" id="v-filters-template">
                                                                                    <div>
                                                                                        {{-- Shimmer while filter options are loading --}}
                                                                                        <template v-if="isLoading">
                                                                                            <x-shop::shimmer.categories.filters />
                                                                                        </template>


                                                                                {{-- Horizontal filter bar --}}
                                                                                <template v-else>
                                                                                    <div class="sticky top-0 z-[100] flex items-center gap-4 overflow-x-auto border-b border-zinc-100 bg-white py-3 no-scrollbar px-4 md:px-0">
                                                                                                {{-- SEARCH: Pill-style search input --}}
                                                                                                <div class="flex-shrink-0 min-w-[180px] md:min-w-[240px]">
                                                                                                    <form action="{{ route('shop.search.index') }}" class="relative group">
                                                                                                        <span class="icon-search absolute left-4 top-1/2 -translate-y-1/2 text-xl text-zinc-400 group-hover:text-[#7C45F5] transition-colors"></span>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            name="query"
                                                                                                            value="{{ request('query') }}"
                                                                                                            placeholder="Поиск..."
                                                                                                            class="w-full rounded-full border border-zinc-300 bg-white py-2 pl-11 pr-4 text-sm font-medium text-zinc-700 transition-all hover:border-[#7C45F5] focus:border-[#7C45F5] focus:outline-none focus:ring-4 focus:ring-[#7C45F5]/10"
                                                                                                            minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                                                                                                            maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                                                                                                            required
                                                                                                        />
                                                                                                    </form>
                                                                                                </div>

                                                                                                {{-- LEFT: attribute filter pills --}}
                                                                                                <div class="flex items-center gap-2">

                                                                                                    <div
                                                                                                        class="relative"
                                                                                                        v-for="filter in filters.available"
                                                                                                        :key="filter.id"
                                                                                                    >
                                                                                                        {{-- Pill button --}}
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="flex items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-medium transition-colors"
                                                                                                            :class="isFilterApplied(filter)
                                                                                                                ? 'border-[#7C45F5] bg-[#7C45F5]/10 text-[#7C45F5]'
                                                                                                                : 'border-zinc-300 bg-white text-zinc-700 hover:border-[#7C45F5] hover:text-[#7C45F5]'"
                                                                                                            :data-filter-id="filter.id"
                                                                                                            @click.stop="toggleDropdown(filter.id)"
                                                                                                        >
                                                                                                            <span>@{{ filter.name }}</span>
                                                                                                            <span
                                                                                                                class="rounded-full bg-[#7C45F5] px-1.5 py-0.5 text-xs font-semibold text-white"
                                                                                                                v-if="isFilterApplied(filter)"
                                                                                                            >@{{ getAppliedCount(filter) }}</span>
                                                                                                            <span
                                                                                                                class="text-base"
                                                                                                                :class="activeDropdown === filter.id ? 'icon-arrow-up' : 'icon-arrow-down'"
                                                                                                            ></span>
                                                                                                        </button>

                                                                                                        {{-- Filter dropdown — Teleported to body so it's above all stacking contexts --}}
                                                                                                        <Teleport to="body">
                                                                                                            <div
                                                                                                                v-show="activeDropdown === filter.id"
                                                                                                                class="fixed min-w-[220px] rounded-xl border border-zinc-200 bg-white shadow-xl"
                                                                                                                style="z-index: 9999;"
                                                                                                                :style="getDropdownStyle(filter.id)"
                                                                                                                @click.stop
                                                                                                            >
                                                                                                                <v-filter-item
                                                                                                                    ref="filterItemComponent"
                                                                                                                    :key="filter.id"
                                                                                                                    :filter="filter"
                                                                                                                    :compact="true"
                                                                                                                    @values-applied="applyFilter(filter, $event)"
                                                                                                                />
                                                                                                            </div>
                                                                                                        </Teleport>
                                                                                                    </div>

                                                                                                    {{-- Clear all --}}
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        v-if="hasAppliedFilters"
                                                                                                        class="flex items-center gap-1 rounded-full border border-red-300 px-3 py-2 text-sm font-medium text-red-500 transition hover:bg-red-50"
                                                                                                        @click="clear()"
                                                                                                    >
                                                                                                        <span class="icon-cross text-sm"></span>
                                                                                                        @lang('shop::app.categories.filters.clear-all')
                                                                                                    </button>

                                                                                                </div>

                                                                                                {{-- RIGHT: sort + grid/list + all-filters --}}
                                                                                                <div class="flex items-center gap-3">

                                                                                                    {{-- Sort pills — desktop only --}}
                                                                                                    <div class="flex items-center gap-1.5 max-md:hidden">
                                                                                                        <button
                                                                                                            v-for="sort in sortOptions"
                                                                                                            :key="sort.value"
                                                                                                            type="button"
                                                                                                            class="flex items-center gap-1 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors whitespace-nowrap"
                                                                                                            :class="sort.value === currentSort
                                                                                                                ? 'border-[#7C45F5] bg-[#7C45F5]/10 text-[#7C45F5]'
                                                                                                                : 'border-zinc-200 bg-white text-zinc-500 hover:border-[#7C45F5] hover:text-[#7C45F5]'"
                                                                                                            @click="applySort(sort.value)"
                                                                                                        >
                                                                                                            @{{ sort.title }}
                                                                                                        </button>
                                                                                                    </div>

                                                                                                    {{-- Grid / List toggle - desktop only --}}
                                                                                                    <div class="flex items-center rounded-full border border-zinc-300 p-0.5 max-md:hidden">
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="rounded-full px-3 py-1.5 transition"
                                                                                                            :class="currentMode === 'list' ? 'bg-[#7C45F5]/10 text-[#7C45F5]' : 'text-zinc-400 hover:text-zinc-700'"
                                                                                                            @click="setMode('list')"
                                                                                                            title="@lang('shop::app.categories.toolbar.list')"
                                                                                                        >
                                                                                                            <span class="text-xl" :class="currentMode === 'list' ? 'icon-listing-fill' : 'icon-listing'"></span>
                                                                                                        </button>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="rounded-full px-3 py-1.5 transition"
                                                                                                            :class="currentMode === 'grid' ? 'bg-[#7C45F5]/10 text-[#7C45F5]' : 'text-zinc-400 hover:text-zinc-700'"
                                                                                                            @click="setMode('grid')"
                                                                                                            title="@lang('shop::app.categories.toolbar.grid')"
                                                                                                        >
                                                                                                            <span class="text-xl" :class="currentMode === 'grid' ? 'icon-grid-view-fill' : 'icon-grid-view'"></span>
                                                                                                        </button>
                                                                                                    </div>


                                                                                                </div>
                                                                                            </div>


                                                                                        </template>
                                                                                    </div>
                                                                                </script>

    {{-- ── v-filter-item template ───────────────────────────────────── --}}
    <script type="text/x-template" id="v-filter-item-template">
                                                                                    <div>
                                                                                        {{-- COMPACT mode: inside desktop dropdown --}}
                                                                                        <div v-if="compact">
                                                                                            {{-- Price range --}}
                                                                                            <div v-if="filter.type === 'price'" class="p-4">
                                                                                                <v-price-filter
                                                                                                    :key="refreshKey"
                                                                                                    :default-price-range="appliedValues"
                                                                                                    @set-price-range="applyValue($event)"
                                                                                                />
                                                                                            </div>

                                                                                            {{-- Checkbox / Boolean options --}}
                                                                                            <template v-else>
                                                                                                <div class="px-3 pt-3" v-if="filter.type !== 'boolean'">
                                                                                                    <div class="relative">
                                                                                                        <span class="icon-search pointer-events-none absolute left-3 top-3 text-xl text-zinc-400"></span>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            class="block w-full rounded-xl border border-zinc-200 py-2.5 pl-10 pr-3 text-sm"
                                                                                                            placeholder=""
                                                                                                            v-model="searchQuery"
                                                                                                            v-debounce:500="searchOptions"
                                                                                                        />
                                                                                                    </div>
                                                                                                </div>

                                                                                                <ul class="max-h-[260px] overflow-y-auto p-2">
                                                                                                    <li v-for="(option, optionIndex) in options" :key="`${filter.id}_${option.id}`">
                                                                                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 hover:bg-zinc-50">
                                                                                                            <input type="checkbox" class="hidden peer" :value="option.id" v-model="appliedValues" @change="applyValue" />
                                                                                                            <span class="icon-uncheck peer-checked:icon-check-box flex-shrink-0 text-2xl text-navyBlue peer-checked:text-navyBlue"></span>
                                                                                                            <span class="text-sm text-zinc-700">@{{ option.name }}</span>
                                                                                                        </label>
                                                                                                    </li>
                                                                                                    <li v-if="! options.length && ! isLoadingMore" class="px-3 py-4 text-center text-sm text-zinc-400">
                                                                                                        @lang('shop::app.categories.filters.search.no-options-available')
                                                                                                    </li>
                                                                                                </ul>

                                                                                                <div class="border-t border-zinc-100 px-3 py-2" v-if="meta && meta.current_page < meta.last_page">
                                                                                                    <button type="button" class="w-full rounded-lg py-2 text-sm text-[#7C45F5] hover:bg-[#7C45F5]/5"
                                                                                                        @click="loadMoreOptions" :disabled="isLoadingMore">
                                                                                                        <span v-if="isLoadingMore">@lang('shop::app.categories.filters.search.loading')</span>
                                                                                                        <span v-else>@lang('shop::app.categories.filters.search.load-more')</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </template>
                                                                                        </div>

                                                                                        <!-- ACCORDION mode: native Vue accordion to avoid Blade-component-in-x-template issues -->
                                                                                        <div v-if="!compact" class="border-b border-zinc-200 last:border-b-0">
                                                                                            <!-- Accordion header -->
                                                                                            <button
                                                                                                type="button"
                                                                                                class="flex w-full items-center justify-between px-0 py-3"
                                                                                                @click="accordionOpen = !accordionOpen"
                                                                                            >
                                                                                                <span class="text-base font-semibold">@{{ filter.name }}</span>
                                                                                                <span class="text-xl" :class="accordionOpen ? 'icon-arrow-up' : 'icon-arrow-down'"></span>
                                                                                            </button>

                                                                                            <!-- Accordion body -->
                                                                                            <div v-show="accordionOpen" class="pb-4">
                                                                                                <!-- Price range -->
                                                                                                <div v-if="filter.type === 'price'">
                                                                                                    <v-price-filter :key="refreshKey" :default-price-range="appliedValues" @set-price-range="applyValue($event)" />
                                                                                                </div>

                                                                                                <!-- Checkbox options -->
                                                                                                <template v-else>
                                                                                                    <!-- Search box -->
                                                                                                    <div class="relative mb-2" v-if="filter.type !== 'boolean'">
                                                                                                        <span class="icon-search pointer-events-none absolute left-3 top-3 text-xl text-zinc-400"></span>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            class="block w-full rounded-xl border border-zinc-200 py-3 pl-10 pr-3 text-sm"
                                                                                                            placeholder=""
                                                                                                            v-model="searchQuery"
                                                                                                            v-debounce:500="searchOptions"
                                                                                                        />
                                                                                                    </div>

                                                                                                    <ul class="max-h-[260px] overflow-y-auto">
                                                                                                        <li v-for="option in options" :key="`${filter.id}_${option.id}`">
                                                                                                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-zinc-50">
                                                                                                                <input type="checkbox" class="hidden peer" :value="option.id" v-model="appliedValues" @change="applyValue" />
                                                                                                                <span class="icon-uncheck peer-checked:icon-check-box flex-shrink-0 text-2xl text-navyBlue peer-checked:text-navyBlue"></span>
                                                                                                                <span class="text-sm text-zinc-700">@{{ option.name }}</span>
                                                                                                            </label>
                                                                                                        </li>
                                                                                                        <li v-if="!options.length && !isLoadingMore" class="py-4 text-center text-sm text-zinc-400">
                                                                                                            @lang('shop::app.categories.filters.search.no-options-available')
                                                                                                        </li>
                                                                                                    </ul>

                                                                                                    <div class="mt-2" v-if="meta && meta.current_page < meta.last_page">
                                                                                                        <button type="button" class="w-full rounded-lg py-2 text-sm text-[#7C45F5] hover:bg-[#7C45F5]/5"
                                                                                                            @click="loadMoreOptions" :disabled="isLoadingMore">
                                                                                                            <span v-if="isLoadingMore">@lang('shop::app.categories.filters.search.loading')</span>
                                                                                                            <span v-else>@lang('shop::app.categories.filters.search.load-more')</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </template>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </script>


    {{-- ── v-price-filter template (two inputs: от/до) ──────────────── --}}
    <script type="text/x-template" id="v-price-filter-template">
                                                            <div class="p-1">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="flex-1">
                                                                        <label class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-zinc-400">От</label>
                                                                        <input
                                                                            type="number"
                                                                            class="w-full rounded-xl border border-zinc-200 px-3 py-2 text-sm text-zinc-800 focus:border-[#7C45F5] focus:outline-none"
                                                                            :placeholder="minRange"
                                                                            v-model.number="localMin"
                                                                            :min="0"
                                                                            :max="localMax"
                                                                            @change="apply"
                                                                        />
                                                                    </div>
                                                                    <span class="mt-5 text-zinc-400">—</span>
                                                                    <div class="flex-1">
                                                                        <label class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-zinc-400">До</label>
                                                                        <input
                                                                            type="number"
                                                                            class="w-full rounded-xl border border-zinc-200 px-3 py-2 text-sm text-zinc-800 focus:border-[#7C45F5] focus:outline-none"
                                                                            :placeholder="allowedMaxPrice"
                                                                            v-model.number="localMax"
                                                                            :min="localMin"
                                                                            @change="apply"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    class="mt-3 w-full rounded-xl bg-[#7C45F5] py-2 text-sm font-semibold text-white transition hover:bg-[#6534d4]"
                                                                    @click="apply"
                                                                >Применить</button>
                                                            </div>
                                                        </script>

    <script type="module">
        window.app.component('v-filters', {
            template: '#v-filters-template',

            data() {
                return {
                    isLoading: true,
                    activeDropdown: null,
                    allFiltersOpen: false,
                    sortOpen: false,

                    // Sort — taken from PHP toolbar helper so it mirrors v-toolbar
                    sortOptions: @json($toolbar->getAvailableOrders())
                        .filter(s => !['name-asc', 'name-desc', 'created_at-asc'].includes(s.value))
                        .map(s => {
                            if (s.value === 'price-asc') s.title = 'По возрастанию цены';
                            if (s.value === 'price-desc') s.title = 'По убыванию цены';
                            if (s.value === 'created_at-desc') s.title = 'По новинкам';
                            return s;
                        }),
                    currentSort: '{{ $toolbar->getOrder($params ?? [])['value'] }}',

                    // Display mode
                    currentMode: '{{ $toolbar->getMode($params ?? []) }}',

                    filters: {
                        available: [],
                        applied: {},
                    },
                };
            },

            computed: {
                hasAppliedFilters() {
                    return Object.values(this.filters.applied).some(v => v && (Array.isArray(v) ? v.length > 0 : !!v));
                },

                sortLabel() {
                    const match = this.sortOptions.find(s => s.value === this.currentSort);
                    return match ? match.title : "@lang('shop::app.products.sort-by.title')";
                },

                sortDropdownStyle() {
                    if (!this.$refs.sortBtn) return {};
                    const r = this.$refs.sortBtn.getBoundingClientRect();
                    return { top: (r.bottom + 4) + 'px', right: (window.innerWidth - r.right) + 'px' };
                },
            },

            mounted() {
                this.getFilters();
                this.setFiltersFromUrl();

                // Emit initial toolbar state so parent v-category / v-search hydrates correctly
                this.emitToolbar();

                document.addEventListener('click', this.closeAll);
                window.addEventListener('scroll', this.closeAll, { passive: true });
            },

            unmounted() {
                document.removeEventListener('click', this.closeAll);
                window.removeEventListener('scroll', this.closeAll);
            },

            methods: {
                getDropdownStyle(filterId) {
                    // Position the fixed dropdown below its pill button
                    const btns = this.$el.querySelectorAll('[data-filter-id="' + filterId + '"]');
                    if (!btns.length) return {};
                    const r = btns[0].getBoundingClientRect();
                    return { top: (r.bottom + 4) + 'px', left: r.left + 'px' };
                },

                getFilters() {
                    this.$axios.get('{{ route("shop.api.categories.attributes") }}', {
                        params: { category_id: "{{ isset($category) ? $category->id : '' }}" }
                    })
                        .then(response => {
                            this.isLoading = false;
                            this.filters.available = response.data.data;
                        })
                        .catch(error => {
                            console.error("Failed to fetch filters:", error);
                            this.isLoading = false;
                        });
                },

                setFiltersFromUrl() {
                    let queryParams = new URLSearchParams(window.location.search);
                    queryParams.forEach((value, key) => {
                        if (key === 'sort') {
                            this.currentSort = value;
                        } else if (key === 'mode') {
                            this.currentMode = value;
                        } else if (!['limit'].includes(key)) {
                            this.filters.applied[key] = value.split(',');
                        }
                    });
                    this.$emit('filter-applied', this.filters.applied);
                },

                emitToolbar() {
                    this.$emit('toolbar-applied', {
                        default: {
                            sort: '{{ $toolbar->getOrder([])['value'] }}',
                            mode: '{{ $toolbar->getMode([]) }}',
                        },
                        applied: {
                            sort: this.currentSort !== '{{ $toolbar->getOrder([])['value'] }}' ? this.currentSort : undefined,
                            mode: this.currentMode !== '{{ $toolbar->getMode([]) }}' ? this.currentMode : undefined,
                        },
                    });
                },

                applyFilter(filter, values) {
                    if (values && values.length) {
                        this.filters.applied[filter.code] = values;
                    } else {
                        delete this.filters.applied[filter.code];
                    }
                    this.$emit('filter-applied', this.filters.applied);
                },

                clear() {
                    this.filters.applied = {};
                    // Reset child filter items
                    const items = this.$refs.filterItemComponent;
                    if (Array.isArray(items)) {
                        items.forEach(item => {
                            item.$data.appliedValues = item.filter.code === 'price' ? null : [];
                        });
                    }
                    this.$emit('filter-applied', this.filters.applied);
                },

                applySort(value) {
                    this.currentSort = value;
                    this.sortOpen = false;
                    this.updateUrl('sort', value);
                    this.emitToolbar();
                },

                setMode(mode) {
                    this.currentMode = mode;
                    this.updateUrl('mode', mode);
                    this.emitToolbar();
                },

                updateUrl(key, value) {
                    let params = new URLSearchParams(window.location.search);
                    params.set(key, value);
                    window.history.pushState({}, '', '?' + params.toString());
                },

                toggleDropdown(filterId) {
                    this.sortOpen = false;
                    this.activeDropdown = this.activeDropdown === filterId ? null : filterId;
                },

                closeAll() {
                    this.activeDropdown = null;
                    this.sortOpen = false;
                },

                isFilterApplied(filter) {
                    const applied = this.filters.applied[filter.code];
                    return applied && (Array.isArray(applied) ? applied.length > 0 : !!applied);
                },

                getAppliedCount(filter) {
                    const applied = this.filters.applied[filter.code];
                    if (!applied) return 0;
                    return Array.isArray(applied) ? applied.length : 1;
                },
            },
        });

        window.app.component('v-filter-item', {
            template: '#v-filter-item-template',

            props: {
                filter: { type: Object, required: true },
                compact: { type: Boolean, default: false },
            },

            data() {
                return {
                    options: [],
                    meta: null,
                    appliedValues: null,
                    currentPage: 1,
                    searchQuery: '',
                    isLoadingMore: true,
                    refreshKey: 0,
                    accordionOpen: true,
                };
            },

            created() {
                const parent = this.$parent?.$data ?? {};
                if (this.filter.code === 'price') {
                    this.appliedValues = parent.filters?.applied?.[this.filter.code]?.join(',') ?? null;
                } else {
                    this.appliedValues = parent.filters?.applied?.[this.filter.code] ?? [];
                }
            },

            mounted() {
                this.fetchFilterOptions();
            },

            watch: {
                appliedValues: {
                    handler(newVal, oldVal) {
                        if (this.filter.code === 'price' && newVal !== oldVal && !newVal) {
                            this.refreshKey++;
                        }
                    }
                }
            },

            methods: {
                applyValue($event) {
                    if (this.filter.code === 'price') {
                        this.appliedValues = $event;
                        this.$emit('values-applied', this.appliedValues);
                        return;
                    }
                    this.$emit('values-applied', this.appliedValues);
                },

                searchOptions() {
                    this.currentPage = 1;
                    this.fetchFilterOptions(true);
                },

                loadMoreOptions() {
                    this.currentPage++;
                    this.fetchFilterOptions(false);
                },

                fetchFilterOptions(replace = true) {
                    this.isLoadingMore = true;
                    const url = `{{ route("shop.api.categories.attribute_options", 'attribute_id') }}`.replace('attribute_id', this.filter.id);
                    this.$axios.get(url, { params: { page: this.currentPage, search: this.searchQuery } })
                        .then(response => {
                            this.isLoadingMore = false;
                            this.options = replace ? response.data.data : [...this.options, ...response.data.data];
                            this.meta = response.data.meta;
                        })
                        .catch(error => {
                            console.error("Failed to fetch filter options:", error);
                            this.isLoadingMore = false;
                        });
                },
            },
        });

        window.app.component('v-price-filter', {
            template: '#v-price-filter-template',
            props: ['defaultPriceRange'],

            data() {
                return {
                    allowedMaxPrice: '',
                    minRange: '',
                    localMin: '',
                    localMax: '',
                };
            },

            created() {
                if (this.defaultPriceRange) {
                    const parts = this.defaultPriceRange.split(',');
                    this.localMin = parts[0] ?? '';
                    this.localMax = parts[1] ?? '';
                }
            },

            mounted() {
                this.$axios.get('{{ route("shop.api.categories.max_price", isset($category) ? $category->id : "") }}')
                    .then(response => {
                        if (response.data.data.max_price) {
                            this.allowedMaxPrice = response.data.data.max_price;
                            this.minRange = '0';
                        }
                    })
                    .catch(error => {
                        console.error("Failed to fetch max price:", error);
                    });
            },

            methods: {
                apply() {
                    const min = this.localMin !== '' ? this.localMin : 0;
                    const max = this.localMax !== '' ? this.localMax : this.allowedMaxPrice;
                    this.$emit('set-price-range', [min, max].join(','));
                },
            },
        });
    </script>

@endPushOnce