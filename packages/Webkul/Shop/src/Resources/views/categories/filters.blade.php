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
                                                                                                                     :class="{
                                                                                                                         'sticky top-4 z-[100] mx-4 mb-4 rounded-3xl border border-[#E8E4FF] bg-[#F8F7FF]/80 py-3 shadow-lg shadow-[#7C45F5]/5 backdrop-blur-xl': !isTeleportTargetAvailable,
                                                                                                                         'w-full border-t border-zinc-100 bg-[#F8F7FF]/60 py-2.5': isTeleportTargetAvailable
                                                                                                                     }"
                                                                                                                     class="no-scrollbar overflow-x-auto transition-all"
                                                                                                                 >
                                                                                                                 <div class="max-w-7xl mx-auto flex items-center gap-4 px-6">
                                                                                                                        {{-- SEARCH: Pill-style search input --}}
                                                                                                                        <div class="flex-shrink-0 min-w-[180px] md:min-w-[240px]">
                                                                                                                            <form action="{{ route('shop.search.index') }}" class="relative group">
                                                                                                                                <span class="icon-search absolute left-4 top-1/2 -translate-y-1/2 text-xl text-zinc-400 group-hover:text-[#7C45F5] transition-colors"></span>
                                                                                                                                 <input
                                                                                                                                     type="text"
                                                                                                                                     name="query"
                                                                                                                                     value="{{ request('query') }}"
                                                                                                                                     placeholder="Поиск..."
                                                                                                                                     class="w-full rounded-2xl border border-white bg-white/50 py-2.5 pl-11 pr-4 text-sm font-medium text-zinc-700 transition-all hover:border-[#7C45F5] focus:border-[#7C45F5] focus:outline-none focus:ring-4 focus:ring-[#7C45F5]/10 shadow-sm"
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
                                                                                                                                     class="flex items-center gap-1.5 rounded-2xl border px-4 py-2.5 text-sm font-semibold transition-all active:scale-[0.98]"
                                                                                                                                     :class="isFilterApplied(filter)
                                                                                                                                         ? 'border-[#7C45F5] bg-[#7C45F5] text-white shadow-md shadow-[#7C45F5]/20'
                                                                                                                                         : 'border-white bg-white text-zinc-600 hover:border-[#7C45F5]/30 hover:bg-white/80 shadow-sm'"
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
                                                                                                                                 class="flex items-center gap-1 rounded-2xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-red-500 transition-all hover:bg-red-50 active:scale-[0.98] shadow-sm"
                                                                                                                                 @click="clear()"
                                                                                                                             >
                                                                                                                                <span class="icon-cross text-sm"></span>
                                                                                                                                @lang('shop::app.categories.filters.clear-all')
                                                                                                                            </button>

                                                                                                                        </div>

                                                                                                                        {{-- RIGHT: sort + grid/list + all-filters --}}
                                                                                                                        <div class="flex items-center gap-3 ml-auto">

                                                                                                                            {{-- Sort pills — desktop only --}}
                                                                                                                            <div class="flex items-center gap-1.5 max-md:hidden">
                                                                                                                                 <button
                                                                                                                                     v-for="sort in sortOptions"
                                                                                                                                     :key="sort.value"
                                                                                                                                     type="button"
                                                                                                                                     class="flex items-center gap-1 rounded-2xl border px-4 py-2.5 text-sm font-semibold transition-all active:scale-[0.98] whitespace-nowrap"
                                                                                                                                     :class="sort.value === currentSort
                                                                                                                                         ? 'border-[#7C45F5] bg-[#7C45F5] text-white shadow-md shadow-[#7C45F5]/20'
                                                                                                                                         : 'border-white bg-white text-zinc-500 hover:border-[#7C45F5]/30 hover:bg-white/80 shadow-sm'"
                                                                                                                                     @click="applySort(sort.value)"
                                                                                                                                 >
                                                                                                                                    @{{ sort.title }}
                                                                                                                                </button>
                                                                                                                            </div>

                                                                                                                            {{-- Grid / List toggle - desktop only --}}
                                                                                                                             <div class="flex items-center rounded-2xl border border-[#E8E4FF] bg-white p-1 max-md:hidden shadow-sm">
                                                                                                                                 <button
                                                                                                                                     type="button"
                                                                                                                                     class="rounded-xl px-4 py-2 transition-all active:scale-[0.95]"
                                                                                                                                     :class="currentMode === 'list' ? 'bg-[#7C45F5] text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600'"
                                                                                                                                     @click="setMode('list')"
                                                                                                                                     title="@lang('shop::app.categories.toolbar.list')"
                                                                                                                                 >
                                                                                                                                     <span class="text-xl" :class="currentMode === 'list' ? 'icon-listing-fill' : 'icon-listing'"></span>
                                                                                                                                 </button>
                                                                                                                                 <button
                                                                                                                                     type="button"
                                                                                                                                     class="rounded-xl px-4 py-2 transition-all active:scale-[0.95]"
                                                                                                                                     :class="currentMode === 'grid' ? 'bg-[#7C45F5] text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600'"
                                                                                                                                     @click="setMode('grid')"
                                                                                                                                     title="@lang('shop::app.categories.toolbar.grid')"
                                                                                                                                 >
                                                                                                                                     <span class="text-xl" :class="currentMode === 'grid' ? 'icon-grid-view-fill' : 'icon-grid-view'"></span>
                                                                                                                                 </button>
                                                                                                                             </div>


                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                    </div>


                                                                                                                  </Teleport>
                                                                                                                </template>
