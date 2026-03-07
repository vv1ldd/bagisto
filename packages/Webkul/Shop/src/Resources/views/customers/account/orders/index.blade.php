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
                    padding: 16px 24px;
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

        <div class="flex-auto pb-8 relative bg-white border border-gray-100">
            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="absolute !top-5 !right-5 z-20 w-8 h-8 bg-white border border-gray-100 flex items-center justify-center text-zinc-400 active:scale-95 transition-all hover:text-[#7C45F5] hover:border-gray-200"
                style="right: 20px !important; left: auto !important;">
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

                <!-- Unified iOS-style body -->
                <template #body="{ isLoading, available, applied, sort, performAction }">
                    <template v-if="isLoading">
                        <div class="px-5 space-y-3 py-4">
                            <div v-for="i in 5" :key="i" class="h-14 bg-zinc-100  animate-pulse">
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

                        <template v-for="record in available.records" :key="record.id">
                            <a :href="record.actions[0]?.url" class="order-row group">
                                <!-- Left: Order info -->
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-[15px] text-zinc-900">
                                            #@{{ record.id }}
                                        </span>
                                        <span v-html="record.status" class="text-xs"></span>
                                    </div>
                                    <p class="text-[13px] text-zinc-400 mt-0.5">@{{ record.created_at }}</p>

                                    <div class="mt-2 flex gap-3">
                                        <span v-if="record.reviews_count >= record.items_count"
                                            class="text-xs text-blue-600 font-medium hover:underline">
                                            @lang('shop::app.customers.account.orders.view.information.view-review')
                                        </span>
                                        <span v-else class="text-xs text-blue-600 font-medium hover:underline">
                                            @lang('shop::app.customers.account.orders.view.information.write-review')
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Total + chevron -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[15px] font-medium text-zinc-700">@{{ record.grand_total }}</span>
                                    <span
                                        class="icon-arrow-right text-zinc-300 text-xl group-hover:text-[#007AFF] transition"></span>
                                </div>
                            </a>
                        </template>
                    </template>
                </template>
            </x-shop::datagrid>

            {!! view_render_event('bagisto.shop.customers.account.orders.list.after') !!}
        </div>
</x-shop::layouts.account>