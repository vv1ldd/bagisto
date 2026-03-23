<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.dashboard.index.title')
    </x-slot>

    <!-- User Details Section -->
    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white" v-pre>
                @lang('admin::app.dashboard.index.user-name', ['user_name' => auth()->guard('admin')->user()->name])
            </p>

            <p class="!leading-normal text-gray-600 dark:text-gray-300">
                @lang('admin::app.dashboard.index.user-info')
            </p>
        </div>

        <!-- Actions -->
        <v-dashboard-filters>
            <!-- Shimmer -->
            <div class="flex gap-1.5">
                <div class="shimmer h-[39px] w-[132px] rounded-md"></div>
                <div class="shimmer h-[39px] w-[140px] rounded-md"></div>
                <div class="shimmer h-[39px] w-[140px] rounded-md"></div>
            </div>
        </v-dashboard-filters>
    </div>

    <!-- Body Component -->
    <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
        <!-- Left Section -->
        <div class="flex flex-col flex-1 gap-8 max-xl:flex-auto">
            {!! view_render_event('bagisto.admin.dashboard.overall_details.before') !!}

            <!-- Overall Details -->
            <div class="flex flex-col gap-2">
                <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                    @lang('admin::app.dashboard.index.overall-details')
                </p>

                <!-- Over All Details Section -->
                @include('admin::dashboard.over-all-details')
            </div>

            {!! view_render_event('bagisto.admin.dashboard.overall_details.after') !!}

            {!! view_render_event('bagisto.admin.dashboard.todays_details.before') !!}

            <!-- Todays Details -->
            <div class="flex flex-col gap-2">
                <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                    @lang('admin::app.dashboard.index.today-details')
                </p>

                <!-- Todays Details Section -->
                @include('admin::dashboard.todays-details')
            </div>

            {!! view_render_event('bagisto.admin.dashboard.todays_details.after') !!}

            {!! view_render_event('bagisto.admin.dashboard.stock_threshold.before') !!}

            <!-- Stock Threshold -->
            <div class="flex flex-col gap-2">
                <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                    @lang('admin::app.dashboard.index.stock-threshold')
                </p>

                <!-- Products List -->  
                @include('admin::dashboard.stock-threshold-products')
            </div>
            
            {!! view_render_event('bagisto.admin.dashboard.stock_threshold.after') !!}
        </div>

        <!-- Right Section -->
        <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">

            <!-- Hot Wallet Widget -->
            @php
                $hotWalletAddress = env('ADMIN_ETH_PUBLIC_ADDRESS', env('ADMIN_ETH_PRIVATE_KEY') ? '0xB1ABfEab7E90B8565F715871f8a0fF1B9FD9F9AA' : null);
                $contractAddress  = env('MINT_CONTRACT_ADDRESS', null);
                $alchemyUrl       = env('ALCHEMY_RPC_URL', null);
            @endphp

            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                🔐 Web3 Горячий Кошелек
            </p>

            <div class="bg-white rounded box-shadow dark:bg-gray-900 overflow-hidden">
                {{-- Header --}}
                <div class="px-5 py-4 border-b dark:border-gray-800" style="background: linear-gradient(135deg, #1a0050 0%, #7C45F5 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-xl">⚡</div>
                        <div>
                            <p class="text-[11px] font-bold text-white/60 uppercase tracking-widest">Meanly Admin Hot Wallet</p>
                            <p class="text-[13px] font-black text-white mt-0.5">Arbitrum One / Mainnet</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Address --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Адрес кошелька (Admin)</p>
                        @if($hotWalletAddress)
                            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2">
                                <code class="text-[11px] font-mono text-[#7C45F5] truncate flex-1" title="{{ $hotWalletAddress }}">{{ $hotWalletAddress }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $hotWalletAddress }}'); this.textContent='✓'" class="text-[10px] font-bold text-gray-400 hover:text-[#7C45F5] transition-colors shrink-0">Сопировать</button>
                            </div>
                            <a href="https://arbiscan.io/address/{{ $hotWalletAddress }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[10px] text-[#7C45F5] hover:underline mt-1.5 font-bold">
                                <span>Посмотреть на Arbiscan →</span>
                            </a>
                        @else
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 rounded-lg px-4 py-3">
                                <p class="text-[11px] font-bold text-amber-700">⚠ Добавьте адрес в .env:</p>
                                <code class="text-[10px] text-amber-600">ADMIN_ETH_PUBLIC_ADDRESS=0x...</code>
                            </div>
                        @endif
                    </div>

                    {{-- Balance Refresh Widget (AJAX via Alchemy) --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">ETH Баланс (Газ)</p>
                        <div id="hw-balance-block" class="bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3 flex items-center justify-between">
                            <span id="hw-balance" class="text-[22px] font-black text-[#1a0050] dark:text-white">—</span>
                            <button onclick="loadHotWalletBalance()" class="text-[11px] font-bold text-[#7C45F5] hover:underline">Обновить</button>
                        </div>
                    </div>

                    {{-- Contract Address --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Смарт-Контракт NFT</p>
                        @if($contractAddress)
                            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-2">
                                <code class="text-[11px] font-mono text-green-600 truncate flex-1" title="{{ $contractAddress }}">{{ $contractAddress }}</code>
                            </div>
                            <a href="https://arbiscan.io/address/{{ $contractAddress }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[10px] text-green-600 hover:underline mt-1.5 font-bold">
                                <span>Посмотреть контракт на Arbiscan →</span>
                            </a>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 rounded-lg px-4 py-3">
                                <p class="text-[11px] font-bold text-red-600">✗ Не задан</p>
                                <code class="text-[10px] text-red-500">MINT_CONTRACT_ADDRESS= в .env</code>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            @if($hotWalletAddress && $alchemyUrl)
            @push('scripts')
            <script>
                async function loadHotWalletBalance() {
                    const el = document.getElementById('hw-balance');
                    el.textContent = '...';
                    try {
                        const res = await fetch('{{ $alchemyUrl }}', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                jsonrpc: '2.0', method: 'eth_getBalance',
                                params: ['{{ $hotWalletAddress }}', 'latest'], id: 1
                            })
                        });
                        const data = await res.json();
                        const wei = parseInt(data.result, 16);
                        const eth = (wei / 1e18).toFixed(6);
                        el.textContent = eth + ' ETH';
                        el.className = 'text-[22px] font-black ' + (wei < 1e15 ? 'text-red-500' : 'text-[#1a0050] dark:text-white');
                    } catch(e) {
                        el.textContent = 'Ошибка';
                    }
                }
                // Load on page init
                document.addEventListener('DOMContentLoaded', loadHotWalletBalance);
            </script>
            @endpush
            @endif

            <!-- Store Stats -->
            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                @lang('admin::app.dashboard.index.store-stats')
            </p>

            {!! view_render_event('bagisto.admin.dashboard.store_stats.before') !!}

            <!-- Store Stats -->
            <div class="bg-white rounded box-shadow dark:bg-gray-900">
                <!-- Total Sales Details -->
                @include('admin::dashboard.total-sales')

                <!-- Total Visitors Details -->
                @include('admin::dashboard.total-visitors')

                <!-- Top Selling Products -->
                @include('admin::dashboard.top-selling-products')

                <!-- Top Customers -->
                @include('admin::dashboard.top-customers')
            </div>

            {!! view_render_event('bagisto.admin.dashboard.store_stats.after') !!}
        </div>
    </div>
    
    @pushOnce('scripts')
        <script
            type="module"
            src="{{ bagisto_asset('js/chart.js') }}"
        >
        </script>

        <script
            type="text/x-template"
            id="v-dashboard-filters-template"
        >
            <div class="flex gap-1.5">
                <template v-if="channels.length > 2">
                    <x-admin::dropdown position="bottom-right">
                        <x-slot:toggle>
                            <button
                                type="button"
                                class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center text-sm leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            >
                                @{{ channels.find(channel => channel.code == filters.channel).name }}
                                
                                <span class="text-2xl icon-sort-down"></span>
                            </button>
                        </x-slot>

                        <x-slot:menu class="!p-0 shadow-[0_5px_20px_rgba(0,0,0,0.15)] dark:border-gray-800">
                            <x-admin::dropdown.menu.item
                                v-for="channel in channels"
                                ::class="{'bg-gray-100 dark:bg-gray-950': channel.code == filters.channel}"
                                @click="filters.channel = channel.code"
                            >
                                @{{ channel.name }}
                            </x-admin::dropdown.menu.item>
                        </x-slot>
                    </x-admin::dropdown>
                </template>

                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="filters.start"
                        placeholder="@lang('admin::app.dashboard.index.start-date')"
                    />
                </x-admin::flat-picker.date>

                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="filters.end"
                        placeholder="@lang('admin::app.dashboard.index.end-date')"
                    />
                </x-admin::flat-picker.date>
            </div>
        </script>

        <script type="module">
            app.component('v-dashboard-filters', {
                template: '#v-dashboard-filters-template',

                data() {
                    return {
                        channels: [
                            {
                                name: "@lang('admin::app.dashboard.index.all-channels')",
                                code: ''
                            },
                            ...@json(core()->getAllChannels()),
                        ],
                        
                        filters: {
                            channel: '',

                            start: "{{ $startDate->format('Y-m-d') }}",
                            
                            end: "{{ $endDate->format('Y-m-d') }}",
                        }
                    }
                },

                watch: {
                    filters: {
                        handler() {
                            this.$emitter.emit('reporting-filter-updated', this.filters);
                        },

                        deep: true
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
