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
                                        <Teleport to="#header-toolbar-teleport-target" :disabled="!isTeleportTargetAvailable">
                                            <div 
                                                :class="[
                                                    !isTeleportTargetAvailable 
                                                        ? 'sticky top-4 z-[100] mx-4 mb-4 rounded-3xl border border-[#E8E4FF] bg-[#F8F7FF]/80 py-3 shadow-lg shadow-[#7C45F5]/5 backdrop-blur-xl no-scrollbar overflow-x-auto' 
                                                        : 'w-full py-1'
                                                ]"
                                                class="transition-all"
                                            >
                                        <div 
                                            class="max-w-7xl mx-auto flex items-center flex-nowrap transition-all"
                                            :class="isTeleportTargetAvailable ? 'gap-1 px-0' : 'gap-4 px-6'"
                                        >
                                            {{-- SEARCH: Pill-style search input --}}
                                            <div 
                                                class="flex-shrink-0 transition-all"
                                                :class="[
                                                    isTeleportTargetAvailable ? 'w-[150px] max-md:hidden' : 'min-w-[180px] md:min-w-[240px]',
                                                ]"
                                            >
                                                <form action="{{ route('shop.search.index') }}" class="relative group">
                                                    <span class="absolute top-1/2 -translate-y-1/2 text-zinc-400 group-hover:text-[#7C45F5] transition-colors"
                                                        :class="isTeleportTargetAvailable ? 'icon-search left-2 text-base' : 'icon-search left-3 text-lg'"
                                                    ></span>
                                                    <input
                                                        type="text"
                                                        name="query"
                                                        value="{{ request('query') }}"
                                                        placeholder="Поиск..."
                                                        class="w-full rounded-xl border border-white bg-white/50 text-zinc-700 transition-all hover:border-[#7C45F5] focus:border-[#7C45F5] focus:outline-none shadow-sm"
                                                        :class="isTeleportTargetAvailable ? 'py-1 pl-9 pr-2 text-[13px]' : 'py-1.5 pl-9 pr-4 text-sm font-medium'"
                                                        minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                                                        maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                                                        required
                                                    />
                                                </form>
                                            </div>

                                            {{-- LEFT: attribute filter pills --}}
                                            <div 
                                                class="flex items-center flex-nowrap"
                                                :class="isTeleportTargetAvailable ? 'gap-1' : 'gap-1.5'"
                                            >
                                                <div
                                                    class="relative"
                                                    v-for="filter in filters.available"
                                                    :key="filter.id"
                                                    v-if="!isTeleportTargetAvailable || !isMobile || filter.code === 'brand'"
                                                >
                                                    {{-- Pill button --}}
                                                    <button
                                                        type="button"
                                                        class="flex items-center gap-1.5 rounded-xl border font-semibold transition-all active:scale-[0.98] whitespace-nowrap"
                                                        :class="[
                                                            isFilterApplied(filter)
                                                                ? 'border-[#7C45F5] bg-[#7C45F5] text-white shadow-md shadow-[#7C45F5]/20'
                                                                : 'border-white bg-white text-zinc-600 hover:border-[#7C45F5]/30 hover:bg-white/80 shadow-sm',
                                                            isTeleportTargetAvailable ? 'px-2 py-1 text-[13px]' : 'px-4 py-2.5 text-sm'
                                                        ]"
                                                        :data-filter-id="filter.id"
                                                        @click.stop="toggleDropdown(filter.id)"
                                                    >
                                                        <span>@{{ filter.name }}</span>
                                                        <span
                                                            class="rounded-full bg-[#7C45F5] px-1 py-0.5 text-[10px] font-bold text-white shadow-sm"
                                                            v-if="isFilterApplied(filter)"
                                                        >@{{ getAppliedCount(filter) }}</span>
                                                        <span
                                                            :class="[
                                                                activeDropdown === filter.id ? 'icon-arrow-up' : 'icon-arrow-down',
                                                                isTeleportTargetAvailable ? 'text-xs' : 'text-base'
                                                            ]"
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

                                                        <button
                                                            type="button"
                                                            class="flex items-center gap-1 border transition-all active:scale-[0.98] shadow-sm whitespace-nowrap"
                                                            :class="[
                                                                hasAppliedFilters 
                                                                    ? 'border-red-200 bg-white text-red-500 hover:bg-red-50' 
                                                                    : 'border-zinc-100 bg-white/30 text-zinc-300 pointer-events-none',
                                                                isTeleportTargetAvailable ? 'rounded-xl px-2 py-1 text-[13px] font-bold' : 'rounded-2xl px-4 py-2.5 text-sm font-bold',
                                                                isTeleportTargetAvailable && isMobile ? 'hidden' : ''
                                                            ]"
                                                            @click="clear()"
                                                        >
                                                            <span class="icon-cross" :class="isTeleportTargetAvailable ? 'text-[10px]' : 'text-sm'"></span>
                                                            @lang('shop::app.categories.filters.clear-all')
                                                        </button>
                                                    </div>

                                                    {{-- RIGHT: sort + grid/list + all-filters --}}
                                                    <div 
                                                        class="flex items-center flex-nowrap ml-auto transition-all"
                                                        :class="isTeleportTargetAvailable ? 'gap-1' : 'gap-2'"
                                                    >
                                                        {{-- Sort pills — desktop only --}}
                                                        <div 
                                                            class="flex items-center flex-nowrap max-md:hidden"
                                                            :class="isTeleportTargetAvailable ? 'gap-1' : 'gap-1'"
                                                        >
                                                            <button
                                                                v-for="sort in sortOptions"
                                                                :key="sort.value"
                                                                type="button"
                                                                class="flex items-center gap-1 rounded-xl border font-semibold transition-all active:scale-[0.98] whitespace-nowrap"
                                                                :class="[
                                                                    sort.value === currentSort
                                                                        ? 'border-[#7C45F5] bg-[#7C45F5] text-white shadow-md shadow-[#7C45F5]/20'
                                                                        : 'border-white bg-white text-zinc-500 hover:border-[#7C45F5]/30 hover:bg-white/80 shadow-sm',
                                                                    isTeleportTargetAvailable ? 'px-2 py-1 text-[13px]' : 'px-3 py-1.5 text-xs'
                                                                ]"
                                                                @click="applySort(sort.value)"
                                                            >
                                                                @{{ sort.title }}
                                                            </button>
                                                        </div>

                                                        {{-- Grid / List toggle - desktop only --}}
                                                        <div 
                                                            class="flex items-center rounded-xl border border-[#E8E4FF] bg-white p-0.5 max-md:hidden shadow-sm flex-nowrap"
                                                            :class="isTeleportTargetAvailable ? 'scale-[0.85]' : ''"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="rounded-lg transition-all active:scale-[0.95]"
                                                                :class="[
                                                                    currentMode === 'list' ? 'bg-[#7C45F5] text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600',
                                                                    isTeleportTargetAvailable ? 'px-2 py-1' : 'px-3 py-1.5'
                                                                ]"
                                                                @click="setMode('list')"
                                                                title="@lang('shop::app.categories.toolbar.list')"
                                                            >
                                                                <span :class="[currentMode === 'list' ? 'icon-listing-fill' : 'icon-listing', isTeleportTargetAvailable ? 'text-base' : 'text-lg']"></span>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="rounded-lg transition-all active:scale-[0.95]"
                                                                :class="[
                                                                    currentMode === 'grid' ? 'bg-[#7C45F5] text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600',
                                                                    isTeleportTargetAvailable ? 'px-2 py-1' : 'px-3 py-1.5'
                                                                ]"
                                                                @click="setMode('grid')"
                                                                title="@lang('shop::app.categories.toolbar.grid')"
                                                            >
                                                                <span :class="[currentMode === 'grid' ? 'icon-grid-view-fill' : 'icon-grid-view', isTeleportTargetAvailable ? 'text-base' : 'text-lg']"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </Teleport>
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
                    isTeleportTargetAvailable: false,
                    isMobile: window.innerWidth < 768,

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
                this.isTeleportTargetAvailable = !!document.getElementById('header-toolbar-teleport-target');

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
                    this.isMobile = window.innerWidth < 768;
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