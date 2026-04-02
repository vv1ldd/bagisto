{!! view_render_event('bagisto.shop.categories.view.filters.before') !!}

@inject('toolbar', 'Webkul\Product\Helpers\Toolbar')

{{-- Component templates and scripts below --}}

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
                                                                                                            {{-- HORIZONTAL TOOLBAR --}}
                                                                                                            <div 
                                                                                                                class="w-full px-0 py-0 flex items-center justify-center h-10"
                                                                                                            >
                                                                                                                <div 
                                                                                                                    class="flex items-center flex-nowrap h-full w-full gap-2 items-center justify-center"
                                                                                                                >
                                                                                                                    {{-- SEARCH: Box-style search input --}}
                                                                                                                    <div 
                                                                                                                        class="flex-shrink-0 transition-all flex items-center h-full max-w-[200px]"
                                                                                                                    >
                                                                                                                        <form action="{{ route('shop.search.index') }}" class="relative group w-full flex items-center h-full">
                                                                                                                            <div class="relative w-full h-full">
                                                                                                                                <span class="absolute top-1/2 -translate-y-1/2 text-zinc-400 group-hover:text-[#7C45F5] transition-colors icon-search left-3 text-base"></span>
                                                                                                                                <input
                                                                                                                                    type="text"
                                                                                                                                    name="query"
                                                                                                                                    ref="searchInput"
                                                                                                                                    value="{{ request('query') }}"
                                                                                                                                    placeholder="Поиск..."
                                                                                                                                    class="block w-full h-full !rounded-none border border-zinc-200 bg-white font-medium text-zinc-700 transition-all focus:border-[#7C45F5] focus:outline-none focus:ring-0 pl-11 pr-2 text-[13px]"
                                                                                                                                    minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                                                                                                                                    maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                                                                                                                                    required
                                                                                                                                />
                                                                                                                            </div>
                                                                                                                        </form>
                                                                                                                    </div>

                                                                                                                    {{-- CENTER/RIGHT: Filters & Sort --}}
                                                                                                                    <div class="flex items-center gap-1.5 h-full">
                                                                                                                        {{-- Attributes --}}
                                                                                                                        <div
                                                                                                                            class="relative h-full"
                                                                                                                            v-for="filter in filters.available"
                                                                                                                            :key="filter.id"
                                                                                                                        >
                                                                                                                            <button
                                                                                                                                type="button"
                                                                                                                                class="flex items-center gap-1.5 border !rounded-none font-semibold transition-all active:scale-[0.98] whitespace-nowrap h-full box-shadow-sm"
                                                                                                                                :class="[
                                                                                                                                    isFilterApplied(filter)
                                                                                                                                        ? 'border-[#7C45F5] bg-[#7C45F5] text-white'
                                                                                                                                        : 'border-zinc-200 bg-white text-zinc-600 hover:border-[#7C45F5]/30 hover:bg-white',
                                                                                                                                    'px-3 text-[12px]'
                                                                                                                                ]"
                                                                                                                                :data-filter-id="filter.id"
                                                                                                                                @click.stop="toggleDropdown(filter.id)"
                                                                                                                            >
                                                                                                                                <span>@{{ filter.name }}</span>
                                                                                                                                <span
                                                                                                                                    class=" bg-white px-1 py-0.5 text-[10px] font-bold text-[#7C45F5] shadow-xs"
                                                                                                                                    v-if="isFilterApplied(filter)"
                                                                                                                                >@{{ getAppliedCount(filter) }}</span>
                                                                                                                                <span
                                                                                                                                    :class="[
                                                                                                                                        activeDropdown === filter.id ? 'icon-arrow-up' : 'icon-arrow-down',
                                                                                                                                        'text-[10px]'
                                                                                                                                    ]"
                                                                                                                                ></span>
                                                                                                                            </button>
                                                                                                                                <div
                                                                                                                                    v-show="activeDropdown === filter.id"
                                                                                                                                    class="fixed min-w-[220px] border border-zinc-200 bg-white shadow-xl"
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
                                                                                                                        </div>

                                                                                                                        {{-- Sort Options --}}
                                                                                                                        <div class="flex items-center gap-1.5 h-full ml-1 overflow-x-auto no-scrollbar">
                                                                                                                            <button
                                                                                                                                v-for="sort in sortOptions"
                                                                                                                                :key="sort.value"
                                                                                                                                type="button"
                                                                                                                                class="flex items-center gap-1 border !rounded-none font-semibold transition-all active:scale-[0.98] whitespace-nowrap h-full box-shadow-sm flex-shrink-0"
                                                                                                                                :class="[
                                                                                                                                    sort.value === currentSort
                                                                                                                                        ? 'border-[#7C45F5] bg-[#7C45F5] text-white'
                                                                                                                                        : 'border-zinc-200 bg-white text-zinc-500 hover:border-[#7C45F5]/30 hover:bg-white',
                                                                                                                                    'px-2.5 text-[12px]'
                                                                                                                                ]"
                                                                                                                                @click="applySort(sort.value)"
                                                                                                                            >
                                                                                                                                @{{ sort.title }}
                                                                                                                            </button>
                                                                                                                        </div>

                                                                                                                        {{-- Clear All: Red cross (Matching height h-10) --}}
                                                                                                                        <button
                                                                                                                            type="button"
                                                                                                                            class="flex h-10 w-10 items-center justify-center border !rounded-none transition-all active:scale-[0.95] ml-1"
                                                                                                                            title="@lang('shop::app.categories.filters.clear-all')"
                                                                                                                            :class="[
                                                                                                                                hasAppliedFilters 
                                                                                                                                    ? 'bg-red-500 text-white border-red-500 hover:bg-red-600 cursor-pointer opacity-100' 
                                                                                                                                    : 'border-zinc-100 bg-white text-zinc-200 pointer-events-none opacity-0',
                                                                                                                            ]"
                                                                                                                            @click="clear()"
                                                                                                                        >
                                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
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
                                                                                                                                                                                                                                            class="block w-full !rounded-none border border-zinc-200 py-2.5 pl-10 pr-3 text-sm"
                                                                                                                                                                                                                                            placeholder=""
                                                                                                                                                                                                                                            v-model="searchQuery"
                                                                                                                                                                                                                                            v-debounce:500="searchOptions"
                                                                                                                                                                                                                                        />
                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                <ul class="max-h-[260px] overflow-y-auto p-2">
                                                                                                                                                                                                                                    <li v-for="(option, optionIndex) in options" :key="`${filter.id}_${option.id}`">
                                                                                                                                                                                                                                        <label class="flex cursor-pointer items-center gap-2  px-3 py-2 hover:bg-zinc-50">
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
                                                                                                                                                                                                                                    <button type="button" class="w-full  py-2 text-sm text-[#7C45F5] hover:bg-[#7C45F5]/5"
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
                                                                                                                                                                                                                                            class="block w-full !rounded-none border border-zinc-200 py-3 pl-10 pr-3 text-sm"
                                                                                                                                                                                                                                            placeholder=""
                                                                                                                                                                                                                                            v-model="searchQuery"
                                                                                                                                                                                                                                            v-debounce:500="searchOptions"
                                                                                                                                                                                                                                        />
                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                    <ul class="max-h-[260px] overflow-y-auto">
                                                                                                                                                                                                                                        <li v-for="option in options" :key="`${filter.id}_${option.id}`">
                                                                                                                                                                                                                                            <label class="flex cursor-pointer items-center gap-3  px-2 py-2 hover:bg-zinc-50">
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
                                                                                                                                                                                                                                        <button type="button" class="w-full  py-2 text-sm text-[#7C45F5] hover:bg-[#7C45F5]/5"
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
                                                                                                                                                                                                            class="w-full  border border-zinc-200 px-3 py-2 text-sm text-zinc-800 focus:border-[#7C45F5] focus:outline-none"
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
                                                                                                                                                                                                            class="w-full  border border-zinc-200 px-3 py-2 text-sm text-zinc-800 focus:border-[#7C45F5] focus:outline-none"
                                                                                                                                                                                                            :placeholder="allowedMaxPrice"
                                                                                                                                                                                                            v-model.number="localMax"
                                                                                                                                                                                                            :min="localMin"
                                                                                                                                                                                                            @change="apply"
                                                                                                                                                                                                        />
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <button
                                                                                                                                                                                                    type="button"
                                                                                                                                                                                                    class="mt-3 w-full  bg-[#7C45F5] py-2 text-sm font-semibold text-white transition hover:bg-[#6534d4]"
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
                    isMounted: false,
                    isMobile: window.innerWidth < 1024,
                    isSearchExpanded: false,

                    // Sort — taken from PHP toolbar helper so it mirrors v-toolbar
                    sortOptions: @json($toolbar->getAvailableOrders())
                        .filter(s => !['name-asc', 'name-desc', 'created_at-asc'].includes(s.value))
                        .map(s => {
                            if (s.value === 'price-asc') s.title = 'Цена: ниже ↓';
                            if (s.value === 'price-desc') s.title = 'Цена: выше ↑';
                            if (s.value === 'created_at-desc') s.title = 'По новинкам';
                            return s;
                        }),
                    currentSort: '{{ $toolbar->getOrder($params ?? [])['value'] }}' || 'created_at-desc',

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
                    const hasFilters = Object.values(this.filters.applied).some(v => v && (Array.isArray(v) ? v.length > 0 : !!v));
                    return hasFilters || this.currentSort !== 'created_at-desc';
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
                this.isMounted = true;

                this.getFilters();
                this.setFiltersFromUrl();

                // Emit initial toolbar state so parent v-category / v-search hydrates correctly
                this.emitToolbar();

                document.addEventListener('click', this.closeAll);
                window.addEventListener('scroll', this.closeAll, { passive: true });
                window.addEventListener('resize', this.handleResize);
            },

            unmounted() {
                document.removeEventListener('click', this.closeAll);
                window.removeEventListener('scroll', this.closeAll);
                window.removeEventListener('resize', this.handleResize);
            },

            methods: {
                handleResize() {
                    this.isMobile = window.innerWidth < 1024;
                },

                getDropdownStyle(filterId) {
                    // Position the fixed dropdown below its pill button
                    // Note: use document.querySelector because this.$el might be the Teleport target container
                    const btns = document.querySelectorAll('[data-filter-id="' + filterId + '"]');
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

                    this.$emitter.emit('header-toolbar-applied', {
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
                    this.$emitter.emit('header-filters-applied', this.filters.applied);
                },

                clear() {
                    this.filters.applied = {};

                    // Reset sorting to newest
                    this.currentSort = 'created_at-desc';
                    this.updateUrl('sort', 'created_at-desc');

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

                handleSearchBlur(e) {
                    if (this.isMobile && !e.target.value) {
                        this.isSearchExpanded = false;
                    }
                },
            },

            watch: {
                isSearchExpanded(val) {
                    if (val) {
                        this.$nextTick(() => {
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.focus();
                            }
                        });
                    }
                }
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