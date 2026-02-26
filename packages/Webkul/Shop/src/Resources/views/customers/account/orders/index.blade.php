<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.orders.title')
        </x-slot>

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
                    padding: 14px 20px;
                    border-bottom: 1px solid #f4f4f5;
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

        <div class="pb-8 pt-2 max-md:pb-5">
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
                            <div v-for="i in 5" :key="i" class="h-14 bg-zinc-100 rounded-xl animate-pulse">
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <template v-if="available.records.length === 0">
                            <div class="flex flex-col items-center justify-center py-16 text-zinc-400">
                                <span class="icon-orders text-5xl mb-3 text-zinc-300"></span>
                                <p class="text-[15px] font-medium text-zinc-500">
                                    @lang('shop::app.customers.account.orders.title')</p>
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