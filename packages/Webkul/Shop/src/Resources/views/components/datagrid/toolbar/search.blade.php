<v-datagrid-search
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @search="search"
>
    {{ $slot }}
</v-datagrid-search>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-search-template"
    >
        <slot
            name="search"
            :available="available"
            :applied="applied"
            :search="search"
            :get-searched-values="getSearchedValues"
        >
            <template v-if="isLoading">
                <x-shop::shimmer.datagrid.toolbar.search />
            </template>

            <template v-else>
                <div class="flex w-full items-center gap-x-1">
                    <!-- Search Panel -->
                    <div class="flex max-w-[445px] items-center max-md:w-full max-md:max-w-[250px]">
                        <div class="relative w-full group">
                            <input
                                type="text"
                                name="search"
                                :value="getSearchedValues('all')"
                                class="w-full border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-[14px] text-zinc-600 transition-all hover:bg-white hover:border-zinc-300 focus:bg-white focus:border-[#7C45F5] focus:ring-2 focus:ring-[#7C45F5]/10 rounded-2xl outline-none"
                                placeholder="@lang('shop::app.components.datagrid.toolbar.search.title')"
                                autocomplete="off"
                                @keyup.enter="search"
                            >

                            <div class="icon-search pointer-events-none absolute top-3 flex items-center text-xl text-zinc-400 group-focus-within:text-[#7C45F5] transition-colors ltr:right-4 rtl:left-4">
                            </div>
                        </div>
                    </div>

                    <!-- Information Panel -->
                    <div class="max-md:hidden ltr:pl-4 rtl:pr-4">
                        <p class="text-[13px] font-medium text-zinc-400 tracking-wide uppercase">
                            @{{ "@lang('shop::app.components.datagrid.toolbar.results')".replace(':total', available.meta.total) }}
                        </p>
                    </div>
                </div>
            </template>
        </slot>
    </script>

    <script type="module">
        app.component('v-datagrid-search', {
            template: '#v-datagrid-search-template',

            props: ['isLoading', 'available', 'applied'],

            emits: ['search'],

            data() {
                return {
                    filters: {
                        columns: [],
                    },
                };
            },

            mounted() {
                this.filters.columns = this.applied.filters.columns.filter((column) => column.index === 'all');
            },

            methods: {
                /**
                 * Perform a search operation based on the input value.
                 *
                 * @param {Event} $event
                 * @returns {void}
                 */
                search($event) {
                    let requestedValue = $event.target.value;

                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    if (! requestedValue) {
                        appliedColumn.value = [];

                        this.$emit('search', this.filters);

                        return;
                    }

                    if (appliedColumn) {
                        appliedColumn.value = [requestedValue];
                    } else {
                        this.filters.columns.push({
                            index: 'all',
                            value: [requestedValue]
                        });
                    }

                    this.$emit('search', this.filters);
                },

                /**
                 * Get the searched values for a specific column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getSearchedValues(columnIndex) {
                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    return appliedColumn?.value ?? [];
                },
            },
        });
    </script>
@endPushOnce
