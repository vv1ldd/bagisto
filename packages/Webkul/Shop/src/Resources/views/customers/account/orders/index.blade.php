<x-shop::layouts.account :show-back="false">
    <!-- Page Title -->
    <x-slot:title></x-slot>

        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="orders" />
        @endSection
        @endif

        @push('styles')
            <style>
                .order-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 20px;
                    border-bottom: 1px solid #f3f4f6;
                    text-decoration: none;
                    transition: background-color 0.15s;
                }

                .order-row:last-child {
                    border-bottom: none;
                }

                .order-row:active {
                    background-color: #f4f4f5;
                }
            </style>
        @endpush

        <div class="flex-auto pb-8 ios-tile-relative bg-white border border-gray-100">
            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="ios-close-button">
                <span class="icon-cancel text-xl"></span>
            </a>

            <div class="px-5 pt-6 pb-2">
                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">
                    @lang('shop::app.customers.account.orders.title')
                </h1>
            </div>
            {!! view_render_event('bagisto.shop.customers.account.orders.list.before') !!}

            <x-shop::datagrid :src="route('shop.customers.account.orders.index')">
                <!-- Hide default header on all screens -->
                <template #header="{ isLoading, available, applied, sort, performAction }">
                    <div class="hidden"></div>
                </template>

                <!-- Simplify Toolbar: Keep only search -->
                <template #search="{ available, applied, search, getSearchedValues }">
                    <div class="flex items-center w-full bg-white border border-zinc-200">
                        <span class="icon-search text-[20px] text-zinc-400 ml-4"></span>
                        
                        <input 
                            type="text" 
                            class="w-full px-4 py-3 text-[14px] text-zinc-700 bg-transparent outline-none placeholder:text-zinc-400"
                            :value="getSearchedValues()"
                            placeholder="@lang('shop::app.components.datagrid.toolbar.search-title')"
                            @input="search($event.target.value)"
                        >
                    </div>
                </template>

                <template #pagination>
                    <div class="hidden"></div>
                </template>

                <template #filter>
                    <div class="hidden"></div>
                </template>

                <!-- Unified iOS-style body -->
                <template #body="{ isLoading, available, applied, sort, performAction }">
                    <template v-if="isLoading">
                        <div class="px-5 space-y-2 py-4">
                            <div v-for="i in 5" :key="i" class="h-12 bg-zinc-100/60 animate-pulse rounded-lg">
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <template v-if="available.records.length === 0">
                            <div class="flex flex-col items-center justify-center py-16 text-zinc-400">
                                <span class="icon-orders text-5xl mb-3 text-zinc-300"></span>
                                <p class="text-[15px] font-medium text-zinc-500">
                                    @lang('shop::app.customers.account.orders.title')
                                </p>
                                <p class="text-[13px] mt-1">@lang('shop::app.customers.account.orders.no-order')</p>
                            </div>
                        </template>

                        <div class="divide-y divide-zinc-100">
                            <template v-for="record in available.records" :key="record.id">
                                <a :href="record.actions[0]?.url" class="order-row group">
                                    <!-- Left: Order info -->
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-[14px] text-zinc-900">
                                                #@{{ record.id }}
                                            </span>
                                            <span v-html="record.status" class="text-[10px] uppercase font-bold tracking-wider"></span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <p class="text-[12px] text-zinc-400 font-medium">@{{ record.created_at }}</p>
                                            <span class="text-zinc-200 text-xs">•</span>
                                            <span v-if="record.reviews_count >= record.items_count"
                                                class="text-[12px] text-blue-500 font-medium hover:text-blue-600 transition-colors">
                                                @lang('shop::app.customers.account.orders.view.information.view-review')
                                            </span>
                                            <span v-else class="text-[12px] text-[#7C45F5] font-semibold hover:text-violet-700 transition-colors">
                                                @lang('shop::app.customers.account.orders.view.information.write-review')
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Right: Total + chevron -->
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-[14px] font-bold text-zinc-800">@{{ record.grand_total }}</span>
                                        <span
                                            class="icon-arrow-right text-zinc-300 text-lg group-hover:text-[#7C45F5] group-hover:translate-x-0.5 transition-all"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>
            </x-shop::datagrid>

            {!! view_render_event('bagisto.shop.customers.account.orders.list.after') !!}
        </div>
</x-shop::layouts.account>