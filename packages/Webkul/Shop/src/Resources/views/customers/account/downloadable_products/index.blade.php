<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.downloadable-products.name')
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="downloadable-products" />
        @endSection
        @endif



        @push('styles')
            <style>
                .download-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 14px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                    text-decoration: none;
                }

                .download-row:last-child {
                    border-bottom: none;
                }

                .download-row:active {
                    background-color: #f4f4f5;
                }
            </style>
        @endpush

        <div class="flex-auto pt-2">
            <x-shop::datagrid :src="route('shop.customers.account.downloadable_products.index')">
                <!-- Hide default header -->
                <template #header="{ isLoading, available, applied, sort, performAction }">
                    <div class="hidden"></div>
                </template>

                <!-- Unified iOS-style body -->
                <template #body="{ isLoading, available, applied, sort, performAction }">
                    <template v-if="isLoading">
                        <div class="px-5 space-y-3 py-4">
                            <div v-for="i in 5" :key="i" class="h-14 bg-zinc-100 rounded-xl animate-pulse"></div>
                        </div>
                    </template>

                    <template v-else>
                        <template v-if="available.records.length === 0">
                            <div class="flex flex-col items-center justify-center py-16 text-zinc-400">
                                <span class="icon-download text-5xl mb-3 text-zinc-300"></span>
                                <p class="text-[15px] font-medium text-zinc-500">
                                    @lang('shop::app.customers.account.downloadable-products.name')</p>
                                <p class="text-[13px] mt-1 text-zinc-400">
                                    @lang('shop::app.customers.account.downloadable-products.records-found')</p>
                            </div>
                        </template>

                        <div class="flex flex-col">
                            <template v-for="record in available.records" :key="record.id">
                                <a href="javascript:void(0);" class="download-row group">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="font-semibold text-[15px] text-zinc-900">
                                                #@{{ record.increment_id }}
                                            </span>
                                            <span v-html="record.status" class="text-xs"></span>
                                        </div>
                                        <p class="text-[14px] text-zinc-600 font-medium" v-html="record.product_name">
                                        </p>
                                        <p class="text-[12px] text-zinc-400 mt-1">
                                            @{{ record.created_at }} • @lang('Remaining Downloads'): @{{
                                            record.remaining_downloads }}
                                        </p>
                                    </div>

                                    <!-- Right side: Chevron -->
                                    <div class="flex items-center shrink-0 ml-4">
                                        <span
                                            class="icon-arrow-right text-zinc-300 text-xl group-hover:text-[#007AFF] transition"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>
            </x-shop::datagrid>
        </div>
</x-shop::layouts.account>