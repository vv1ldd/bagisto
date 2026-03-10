<x-shop::layouts.account :has-header="false">
    <x-slot:title>
        Meanly Wallet
        </x-slot>

        <div class="max-w-lg mx-auto px-4 py-12">
            {{-- Top Navigation & Title (Truly Outside the Tile) --}}
            <div class="flex items-center gap-4 mb-8">
                <a id="page-back-link" href="{{ route('shop.customers.account.index') }}"
                    class="w-10 h-10  bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <button id="step-back-btn" onclick="handleStepBack()" style="display: none;"
                    class="w-10 h-10  bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h1 id="page-title" class="text-[22px] font-bold text-zinc-900 leading-none">Meanly Wallet</h1>
            </div>

            @php
                $user = auth()->guard('customer')->user();
                $balances = $user->balances;
                $exchangeRateService = app(\Webkul\Customer\Services\ExchangeRateService::class);
                $netLabels = [
                    'ton' => ['label' => 'TON', 'symbol' => '◎', 'color' => '#0098EA'],
                    'usdt_ton' => ['label' => 'USDT', 'symbol' => '₮', 'color' => '#26A17B'],
                    'bitcoin' => ['label' => 'BTC', 'symbol' => '₿', 'color' => '#F7931A'],
                    'ethereum' => ['label' => 'ETH', 'symbol' => 'Ξ', 'color' => '#627EEA'],
                    'dash' => ['label' => 'DASH', 'symbol' => 'D', 'color' => '#1c75bc'],
                ];

                $allAssets = [
                    'ton' => ['name' => 'TON (Native)', 'symbol' => 'TON', 'icon' => '💎', 'network' => 'TON Network', 'color' => '#0098EA', 'color2' => '#33BFFF', 'address' => config('crypto.verification_addresses.ton')],
                    'usdt_ton' => ['name' => 'USDT (TON)', 'symbol' => 'USDT', 'icon' => '₮', 'network' => 'TON Network', 'color' => '#0098EA', 'color2' => '#33BFFF', 'address' => config('crypto.verification_addresses.usdt_ton')],
                    'bitcoin' => ['name' => 'Bitcoin', 'symbol' => 'BTC', 'icon' => '₿', 'network' => 'Bitcoin', 'color' => '#F7931A', 'color2' => '#FDB953', 'address' => config('crypto.verification_addresses.bitcoin')],
                    'ethereum' => ['name' => 'Ethereum / USDT ERC20', 'symbol' => 'ETH', 'icon' => 'Ξ', 'network' => 'Ethereum', 'color' => '#627EEA', 'color2' => '#8FA4EF', 'address' => config('crypto.verification_addresses.ethereum')],
                    'dash' => ['name' => 'Dash', 'symbol' => 'DASH', 'icon' => 'D', 'network' => 'Dash', 'color' => '#1c75bc', 'color2' => '#4DA3E0', 'address' => config('crypto.verification_addresses.dash')],
                ];
            @endphp

            {{-- Step 1: Dashboard --}}
            <div id="step-dashboard"
                class="ios-group p-6 bg-white  shadow-md relative overflow-hidden active:scale-[0.99] transition-transform">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-400/5  blur-3xl"></div>
                <div class="flex flex-col gap-2 relative z-10">
                    {{-- Row 1: label + badges --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="text-[12px] text-zinc-500 font-bold uppercase tracking-[0.1em] opacity-80">
                            @if($user->is_investor)
                                Общая<br>покупательная способность
                            @else
                                Баланс
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                            <div
                                class="text-[12px] font-mono text-violet-600 bg-violet-50 px-2.5 py-1  border border-violet-100 font-bold whitespace-nowrap">
                                @ {{ $user->username }}
                            </div>
                            @if($user->is_investor)
                                <div
                                    class="text-[11px] font-black text-amber-600 bg-amber-50 px-2.5 py-1  border border-amber-200 tracking-wide whitespace-nowrap">
                                    💎 Инвестор
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: balance + история --}}
                    <div class="flex items-center justify-between mt-1">
                        <div class="text-4xl font-bold font-mono text-zinc-900 tracking-tight">
                            {{ core()->formatPrice($user->getTotalFiatBalance()) }}
                        </div>
                        <button onclick="switchStep('transactions')" class="flex flex-col items-center gap-1 group">
                            <div
                                class="w-10 h-10  bg-zinc-50 flex items-center justify-center text-zinc-400 group-hover:bg-violet-50 group-hover:text-violet-600 transition-all border border-zinc-100 group-hover:border-violet-100 shadow-sm text-[20px]">
                                📜</div>
                            <span
                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest group-hover:text-violet-500 transition-colors">история</span>
                        </button>
                    </div>

                    @if($balances->count() > 0)
                        <div class="mt-4 flex flex-col gap-2.5">
                            @foreach($balances as $balance)
                                @php
                                    $m = $netLabels[$balance->currency_code] ?? ['label' => strtoupper($balance->currency_code), 'symbol' => '?', 'color' => '#888'];
                                    $rate = $exchangeRateService->getRate($balance->currency_code);
                                    $fiat = $balance->amount * $rate;
                                    $amount = rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.');
                                @endphp
                                <div class="flex items-center gap-2 text-[14px] font-medium text-zinc-500">
                                    <span class="w-2 h-2  shrink-0" style="background: {{ $m['color'] }}"></span>
                                    <span class="text-zinc-900 font-bold font-mono">{{ $amount }} {{ $m['label'] }}</span>
                                    <span class="text-zinc-400 opacity-60">≈</span>
                                    <span>{{ core()->formatPrice($fiat) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8 flex gap-3 max-sm:flex-col">
                        <div class="flex-1">
                            @if($user->is_investor)
                                <button onclick="goToDeposit()"
                                    class="w-full inline-flex items-center justify-center text-[14px] font-bold text-white bg-zinc-900 px-6 py-3  active:scale-95 transition-all shadow-lg shadow-zinc-100">
                                    + Пополнить
                                </button>
                            @else
                                <div class="flex flex-col gap-1.5 h-full">
                                    <button disabled
                                        class="w-full h-full inline-flex items-center justify-center text-[14px] font-bold text-zinc-400 bg-zinc-100 px-6 py-3  cursor-not-allowed opacity-60">
                                        + Пополнить
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!auth()->guard('customer')->user()->is_investor)
                        <p class="text-[11px] text-zinc-400 text-center mt-2">Методы пополнения пока недоступны</p>
                    @endif

                    {{-- Cashback Info (only for non-investors) --}}
                    @if(!auth()->guard('customer')->user()->is_investor)
                        <div class="mt-6 p-4 bg-emerald-50  border border-emerald-100 flex gap-3">
                            <div class="text-xl">💸</div>
                            <div class="text-[12px] text-emerald-800 leading-snug">
                                Мы начисляем кэшбек за покупки на баланс вашего кошелька.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Step 2: Transactions --}}
            <div id="step-transactions" class="hidden bg-white overflow-hidden  border border-zinc-100 shadow-sm">
                @if ($transactions->count() > 0)
                    <div class="flex flex-col divide-y divide-zinc-50">
                        @foreach ($transactions as $transaction)
                            <div class="p-5 hover:bg-zinc-50/50 flex items-center justify-between">
                                <div class="flex flex-col gap-1.5 min-w-0 pr-4">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $typeLabels = ['deposit' => 'Пополнение', 'withdrawal' => 'Списание', 'purchase' => 'Оплата', 'refund' => 'Возврат', 'transfer_debit' => 'Перевод от вас', 'transfer_credit' => 'Перевод вам', 'cashback' => '💸 Кэшбек'];
                                            $typeLabel = $typeLabels[$transaction->type] ?? $transaction->type;
                                            $statusColors = ['completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'pending' => 'bg-amber-50 text-amber-600 border-amber-100', 'failed' => 'bg-red-50 text-red-600 border-red-100'];
                                            $statusClass = $statusColors[$transaction->status] ?? 'bg-zinc-50 text-zinc-500 border-zinc-100';
                                        @endphp
                                        <span class="text-[15px] font-bold text-zinc-900 truncate">{{ $typeLabel }}</span>
                                        <span
                                            class="text-[9px] px-1.5 py-0.5  border {{ $statusClass }} uppercase tracking-wider font-bold shrink-0">{{ $transaction->status }}</span>
                                    </div>
                                    @if($transaction->notes)
                                        <div class="text-[12px] text-zinc-500 leading-tight">{{ $transaction->notes }}</div>
                                    @endif
                                    <div class="text-[11px] text-zinc-400 font-medium">
                                        {{ $transaction->created_at->format('d.m.Y — H:i') }}
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    @php
                                        $debitTypes = ['purchase', 'withdrawal', 'transfer_debit'];
                                        $isDebit = in_array($transaction->type, $debitTypes);
                                        $sign = $isDebit ? '-' : '+';
                                        $colorClass = $isDebit ? 'text-red-500' : 'text-emerald-500';
                                    @endphp
                                    <div class="text-[16px] font-bold font-mono {{ $colorClass }}">
                                        {{ $sign }}{{ core()->formatPrice($transaction->amount) }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400 font-mono mt-0.5 uppercase tracking-tighter">
                                        #{{ $transaction->uuid ? substr($transaction->uuid, 0, 8) : 'N/A' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-6 border-t border-zinc-50">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-zinc-400 px-10 text-center">
                        <div class="w-20 h-20 bg-zinc-50  flex items-center justify-center mb-6 shadow-inner text-3xl">
                            📭</div>
                        <p class="text-[17px] font-bold text-zinc-700">Транзакций не найдено</p>
                    </div>
                @endif
            </div>

            {{-- Step: Deposit Type Selection --}}
            <div id="step-deposit-type" class="hidden ios-group p-6 bg-white shadow-md">
                <p class="text-[12px] text-zinc-400 uppercase font-bold tracking-wider mb-4 px-2 text-center">Способ
                    пополнения</p>
                <div class="grid grid-cols-1 gap-4">
                    {{-- Crypto Option --}}
                    <button onclick="goToCryptoManagement()"
                        class="flex items-center gap-4 p-5 bg-white border border-zinc-100 shadow-sm hover:shadow-md hover:border-violet-200 transition-all text-left group active:scale-[0.98]">
                        <div
                            class="w-12 h-12 bg-violet-50 flex items-center justify-center text-violet-600 text-2xl shrink-0 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                            🪙
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-[16px] font-bold text-zinc-900 group-hover:text-violet-700 transition-colors">
                                Криптовалюта</h3>
                            <p class="text-[12px] text-zinc-500 mt-0.5 leading-snug">Пополнение через USDT, TON, BTC или
                                ETH</p>
                        </div>
                        <div class="text-zinc-300 group-hover:text-violet-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>

                    {{-- B2B Bank Transfer Option --}}
                    <button onclick="goToB2BManagement()"
                        class="flex items-center gap-4 p-5 bg-white border border-zinc-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all text-left group active:scale-[0.98]">
                        <div
                            class="w-12 h-12 bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            🏦
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-[16px] font-bold text-zinc-900 group-hover:text-emerald-700 transition-colors">
                                Банковский перевод</h3>
                            <p class="text-[12px] text-zinc-500 mt-0.5 leading-snug">Безналичная оплата от юридического
                                лица (B2B)</p>
                        </div>
                        <div class="text-zinc-300 group-hover:text-emerald-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>

                    {{-- B2C Bank Transfer Option --}}
                    <button onclick="goToB2CManagement()"
                        class="flex items-center gap-4 p-5 bg-white border border-zinc-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-left group active:scale-[0.98]">
                        <div
                            class="w-12 h-12 bg-blue-50 flex items-center justify-center text-blue-600 text-2xl shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            👤
                        </div>
                        <div class="flex-1">
                            <h3 class="text-[16px] font-bold text-zinc-900 group-hover:text-blue-700 transition-colors">
                                Перевод от физ. лица</h3>
                            <p class="text-[12px] text-zinc-500 mt-0.5 leading-snug">Оплата по реквизитам через банк</p>
                        </div>
                        <div class="text-zinc-300 group-hover:text-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Step: Empty (Crypto) --}}
            <div id="step-empty"
                class="hidden bg-white  border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16  bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                <div>
                    <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                    <p class="text-[13px] text-zinc-400 mt-1">Для пополнения необходимо сначала добавить и
                        верифицировать свой кошелёк.</p>
                </div>
                <button onclick="goToAddWallet()" style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                    class="text-white font-bold px-6 py-3  text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                    + Добавить кошелёк
                </button>
            </div>

            {{-- Step: Management (Combined Deposit & Management) --}}
            <div id="step-management" class="hidden space-y-4">
                <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3 px-2">Выберите кошелек для
                    пополнения
                </p>
                @foreach($allAddresses as $address)
                    @php
                        $nm = [
                            'bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623', 'BTC', 'https://mempool.space/address/'],
                            'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF', 'ETH', 'https://etherscan.io/address/'],
                            'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF', 'TON', 'https://tonviewer.com/'],
                            'usdt_ton' => ['TON', 'USDT', '₮', '#26A17B', '#4DBFA0', 'TON', 'https://tonviewer.com/'],
                            'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0', 'DASH', 'https://blockchair.com/dash/address/']
                        ];
                        $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc', strtoupper($address->network), '#'];

                        $netMap = [
                            'ton' => ['chain' => 'ton'],
                            'usdt_ton' => ['chain' => 'ton', 'token' => 'usdt'],
                            'bitcoin' => ['chain' => 'btc'],
                            'ethereum' => ['chain' => 'erc20', 'token' => 'usdt'],
                            'dash' => ['chain' => 'dash']
                        ];
                        $nmData = $netMap[$address->network] ?? ['chain' => $address->network];

                        $parts = ["@" . $user->username, $nmData['chain'] ?? $address->network];
                        if (isset($nmData['token']))
                            $parts[] = $nmData['token'];
                        if ($address->alias)
                            $parts[] = $address->alias;
                        $fullAlias = implode('.', $parts);

                        $explorerUrl = $m[6] . $address->address;
                        $dAmt = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');

                        // Style attributes based on coin
                        $coinColor = $m[3];
                    @endphp

                    <div class="bg-white  shadow-sm hover:shadow-md transition-all group/card relative flex items-center">
                        {{-- Clickable Area for Deposit --}}
                        <button type="button" onclick="selectAsset('{{ $address->network }}', '{{ $address->id }}')"
                            class="flex-1 flex gap-4 p-6 min-w-0 text-left cursor-pointer items-center">
                            {{-- Icon Column --}}
                            <div class="relative shrink-0">
                                <div class="w-[52px] h-[52px]  flex items-center justify-center text-white text-[22px] font-bold shadow-sm"
                                    style="background: {{ $coinColor }}">
                                    {{ $m[2] }}
                                </div>
                            </div>

                            {{-- Main Content Column --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-center gap-1.5">
                                {{-- Header: Verified Icon + Alias --}}
                                <div class="flex items-center gap-1.5 min-w-0">
                                    @if($address->isVerified())
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-black shrink-0"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41 1.41-8.48 8.48z" />
                                        </svg>
                                    @endif
                                    <span
                                        class="text-[18px] font-bold text-black truncate tracking-tight">{{ $fullAlias }}</span>
                                </div>

                                {{-- Network Breadcrumbs + Address (Premium View) --}}
                                <div
                                    class="flex items-center gap-2 text-[12px] font-bold text-zinc-400 uppercase tracking-tight">
                                    <span class="shrink-0">{{ $m[5] }}</span>
                                    <span class="shrink-0 text-zinc-200">›</span>
                                    <span class="shrink-0">{{ $m[1] }}</span>
                                    <span class="shrink-0 text-zinc-200">›</span>
                                    <div class="flex items-center gap-1.5 min-w-0 pr-2">
                                        <code
                                            class="font-mono text-[12px] text-zinc-400 truncate tracking-tighter opacity-70">{{ $address->address }}</code>
                                    </div>
                                </div>

                                {{-- Balance + Sync Status --}}
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="text-[16px] font-bold font-mono text-black">
                                        {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                        <span class="text-[12px] text-zinc-400 font-bold uppercase ml-1">{{ $m[1] }}</span>
                                    </span>
                                    <div class="flex items-center gap-1 text-[12px] font-bold text-zinc-400 opacity-60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        @if($address->updated_at)
                                            {{ $address->updated_at->diffForHumans() }}
                                        @else
                                            не синхронизирован
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Selection Indicator --}}
                            <div
                                class="shrink-0 pl-2 pr-4 text-zinc-300 group-hover/card:text-violet-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Action Column (Delete) --}}
                        <div class="shrink-0 flex items-center pr-8 pl-2 border-l border-zinc-50 ml-2">
                            <form id="delete-wallet-form-{{ $address->id }}"
                                action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST"
                                class="inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                    onclick="confirmWalletDeletion('{{ $address->id }}', '{{ $address->alias ?: $address->address }}')"
                                    class="w-[42px] h-[42px]  flex items-center justify-center bg-zinc-50 text-zinc-400 transition-all hover:bg-red-50 hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <button onclick="goToAddWallet()"
                    class="w-full py-[22px] mt-4 border border-dashed border-zinc-200 bg-transparent text-zinc-400 font-bold hover:text-zinc-600 transition-all flex items-center justify-center gap-3">
                    <span
                        class="w-7 h-7  bg-zinc-100 flex items-center justify-center text-[18px] text-zinc-400">+</span>
                    <span class="text-[15px]">Добавить новый кошелек</span>
                </button>
            </div>

            {{-- Step: B2B Management --}}
            <div id="step-b2b-management" class="hidden space-y-4">
                <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3 px-2">Выберите
                    организацию-плательщика</p>

                @forelse($organizations as $org)
                    <div
                        class="bg-white shadow-sm border border-zinc-100 hover:shadow-md hover:border-emerald-200 transition-all group/card relative flex items-center">
                        <button type="button" onclick="selectTopupOrg('{{ $org->id }}', '{{ $org->name }}')"
                            class="flex-1 flex gap-4 p-5 min-w-0 text-left cursor-pointer items-center">
                            {{-- Icon Column --}}
                            <div class="relative shrink-0">
                                <div
                                    class="w-12 h-12 bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl font-bold">
                                    🏢
                                </div>
                            </div>

                            {{-- Main Content Column --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-center gap-1.5">
                                <div
                                    class="text-[16px] font-bold text-zinc-900 truncate pr-2 group-hover/card:text-emerald-700 transition-colors">
                                    {{ $org->name }}
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-[11px] text-zinc-500 font-mono tracking-wide">
                                        ИНН: {{ $org->inn }}
                                    </div>
                                    @if($org->is_verified)
                                        <div
                                            class="text-[9px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded uppercase font-bold tracking-widest">
                                            ✓ Проверено
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Selection Indicator --}}
                            <div
                                class="shrink-0 pl-2 pr-4 text-zinc-300 group-hover/card:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Action Column (Delete) --}}
                        <div class="shrink-0 flex items-center pr-6 pl-2 border-l border-zinc-50 ml-2">
                            <form action="{{ route('shop.customers.account.organizations.delete', $org->id) }}"
                                method="POST" class="inline" onsubmit="return confirm('Удалить организацию?');">
                                @method('DELETE')
                                @csrf
                                <button type="submit"
                                    class="w-[38px] h-[38px] flex items-center justify-center bg-zinc-50 text-zinc-400 transition-all hover:bg-red-50 hover:text-red-500">
                                    <span class="icon-bin text-lg"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-16 text-zinc-400 bg-white border border-dashed border-zinc-200">
                        <div class="w-16 h-16 bg-zinc-50 flex items-center justify-center mb-4 shadow-inner text-2xl">
                            📋
                        </div>
                        <p class="text-[15px] font-bold text-zinc-600 mb-1">Организаций пока нет</p>
                        <p class="text-[12px] text-zinc-400 max-w-[200px] text-center">Добавьте хотя бы одну для пополнения
                            счета переводом</p>
                    </div>
                @endforelse

                <button type="button" onclick="goToAddOrganization()"
                    class="w-full py-5 mt-4 border border-zinc-200 bg-white shadow-sm text-emerald-600 font-bold hover:bg-emerald-50 hover:border-emerald-200 transition-all flex items-center justify-center gap-3">
                    <span class="text-[15px]">+ Добавить организацию</span>
                </button>
            </div>

            {{-- Step: Top-up Details --}}
            <div id="step-topup-details" class="hidden space-y-6">
                <div class="ios-group p-6 bg-white shadow-md">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">
                            🏦</div>
                        <div>
                            <h3 class="text-[16px] font-bold text-zinc-900" id="selected-org-name">Название организации
                            </h3>
                            <p class="text-[12px] text-zinc-400">Пополнение баланса через банковский перевод</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label
                                class="required !text-[12px] !font-bold text-zinc-400 uppercase tracking-widest">
                                @lang('shop::app.customers.account.topup.amount')
                            </x-shop::form.control-group.label>
                            <x-shop::form.control-group.control type="text" name="amount" id="topup-amount"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-emerald-500 focus:!ring-2 focus:!ring-emerald-500/20 transition-all text-[18px] font-bold"
                                placeholder="0.00" />
                        </x-shop::form.control-group>

                        <div id="topup-success-msg"
                            class="hidden p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-[14px]">
                            <p class="font-bold">@lang('shop::app.customers.account.topup.success')</p>
                            <p class="mt-1">@lang('shop::app.customers.account.topup.pending-message')</p>
                            <a id="topup-invoice-link" href="#" target="_blank"
                                class="inline-block mt-3 px-4 py-2 bg-emerald-600 text-white font-bold text-[13px] hover:bg-emerald-700 transition-colors">
                                ⬇️ @lang('shop::app.customers.account.topup.download-invoice')
                            </a>
                        </div>

                        <button type="button" id="generate-topup-btn" onclick="generateTopupInvoice()"
                            class="w-full bg-zinc-900 hover:bg-emerald-600 text-white font-bold py-4 px-8 shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3 text-[16px] uppercase tracking-wider">
                            <span id="btn-text">@lang('shop::app.customers.account.topup.generate-invoice')</span>
                            <div id="btn-loader"
                                class="hidden w-5 h-5 border-2 border-white border-t-transparent animate-spin"></div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step: B2C Details --}}
            <div id="step-b2c-details" class="hidden space-y-4">
                <div
                    class="bg-white shadow-sm border border-zinc-100 p-6 md:p-8 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-blue-50 flex items-center justify-center mb-4 text-3xl">🏦</div>
                    <h3 class="text-[18px] font-bold text-zinc-900 mb-2">Реквизиты для перевода</h3>
                    <p class="text-[14px] text-zinc-500 mb-6 max-w-[300px]">
                        Для пополнения баланса переведите средства по следующим реквизитам. Обязательно укажите ваш ID в
                        назначении платежа.
                    </p>

                    <div class="w-full bg-zinc-50 border border-zinc-100 p-5 text-left space-y-3">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">Получатель
                                (Наименование)</span>
                            <span class="text-[14px] font-mono text-zinc-900 mt-0.5">ИП АТАНИЯЗОВА НОВБАХАР
                                ДУРДЫКУЛЫЕВНА</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">ИНН</span>
                            <span class="text-[14px] font-mono text-zinc-900 mt-0.5">500315995400</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">Расчетный
                                счет</span>
                            <span
                                class="text-[14px] font-mono text-zinc-900 mt-0.5 flex justify-between items-center group">
                                <span>40802810800000109919</span>
                                <button type="button" onclick="copyAddr('40802810800000109919', this)"
                                    class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100">Копировать</button>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">БИК Банка</span>
                            <span
                                class="text-[14px] font-mono text-zinc-900 mt-0.5 flex justify-between items-center group">
                                <span>044525974</span>
                                <button type="button" onclick="copyAddr('044525974', this)"
                                    class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100">Копировать</button>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">Банк</span>
                            <span class="text-[14px] font-mono text-zinc-900 mt-0.5">АО «ТБанк»</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-400 font-bold uppercase tracking-wider">Назначение
                                платежа</span>
                            <span
                                class="text-[14px] font-mono text-zinc-900 mt-0.5 flex justify-between items-start md:items-center group">
                                <span class="pr-2">Оплата за цифровые услуги. Пользователь
                                    #{{ auth()->guard('customer')->id() }}. Без НДС.</span>
                                <button type="button"
                                    onclick="copyAddr('Оплата за цифровые услуги. Пользователь #{{ auth()->guard('customer')->id() }}. Без НДС.', this)"
                                    class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 hover:bg-blue-100 rounded transition md:opacity-0 md:group-hover:opacity-100 shrink-0">Копировать</button>
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 w-full p-4 bg-orange-50 border border-orange-100 text-left">
                        <p class="text-[12px] text-orange-800 font-medium">
                            <span class="font-bold">Важно:</span> Средства будут зачислены на ваш баланс после
                            поступления на расчетный счет (Обычно в течение 1 рабочего дня).
                            Пожалуйста, сохраняйте квитанцию об оплате для подтверждения (отправьте её в поддержку).
                        </p>
                    </div>
                </div>
            </div>

            <div id="step-details" class="hidden">
                @foreach($allAddresses as $address)
                    @php
                        $nm = ['bitcoin' => ['Bitcoin', 'BTC'], 'ethereum' => ['Ethereum', 'ETH'], 'ton' => ['TON', 'TON'], 'usdt_ton' => ['USDT (TON)', 'USDT'], 'dash' => ['Dash', 'DASH']];
                        $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc'];
                    @endphp
                    <div id="details-wallet-{{ $address->id }}" class="wallet-details-view hidden">
                        <div class="bg-white  shadow-sm overflow-hidden p-6 md:p-8 flex flex-col items-center">

                            {{-- QR Code Section --}}
                            <div class="relative inline-block mt-4 mb-2">
                                <div class="border border-zinc-100  p-6 pb-8 bg-white shadow-sm inline-block">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($address->address) }}"
                                        alt="QR Code" class="w-56 h-56 mx-auto" />
                                </div>
                                {{-- Floated Label --}}
                                <div class="absolute -bottom-3 left-0 right-0 flex justify-center">
                                    <div
                                        class="bg-white px-4 py-1.5 text-[11px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                        Адрес пополнения ({{ $m[0] }})
                                    </div>
                                </div>
                            </div>

                            {{-- Address Copy Section --}}
                            <div class="w-full max-w-sm mt-8 bg-zinc-50  p-6 text-center cursor-pointer active:scale-95 transition-all group"
                                onclick="copyAddr('{{ $address->address }}', this.querySelector('.copy-txt'))">
                                <code class="font-mono text-[14px] text-zinc-800 break-all block leading-relaxed mb-6">
                                                                                                            {{ $address->address }}
                                                                                                        </code>
                                <div
                                    class="flex items-center justify-center gap-2 text-black font-black text-[11px] uppercase tracking-wider">
                                    <span class="copy-txt">Скопировать</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Verification Warning --}}
                            <div class="w-full max-w-sm mt-8 p-5 bg-violet-50/50  flex gap-3 text-left">
                                <span class="text-lg">⚠️</span>
                                <p class="text-[12px] text-violet-700 leading-snug">
                                    <b>Внимание:</b> Переводите средства исключительно из верифицированного кошелька, чтобы
                                    система смогла автоматически зачислить платеж.
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Step: Add Organization --}}
            <div id="step-add-organization" class="hidden bg-white border border-zinc-100 shadow-sm p-6 md:p-8">
                <x-shop::form :action="route('shop.customers.account.organizations.store')">
                    <div class="space-y-6" id="wizard-container">
                        <!-- ================== STEP 1: ORGANIZATION DETAILS ================== -->
                        <div id="step-1" class="transition-all duration-300">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <h2 class="text-[16px] font-bold text-zinc-900">Шаг 1: Данные организации</h2>
                                <div class="flex items-center gap-3">
                                    <span id="step-1-badge"
                                        class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓
                                        Заполнено</span>
                                </div>
                            </div>

                            <!-- Step 1 Summary (Shown after confirmation) -->
                            <div id="step-1-summary"
                                class="hidden bg-white border border-zinc-200 rounded-none p-5 mb-8 shadow-sm transition-all duration-300">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-4">
                                        <div id="summary-org-name"
                                            class="text-[17px] font-black text-zinc-900 leading-tight"></div>
                                        <button type="button" id="edit-step-1-btn"
                                            class="shrink-0 text-[12px] font-bold text-[#7C45F5] hover:bg-[#7C45F5]/5 px-3 py-1.5 rounded transition-all">Изменить</button>
                                    </div>
                                    <div id="summary-org-address"
                                        class="text-[13px] text-zinc-500 font-medium leading-relaxed max-w-[580px]">
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-x-8 gap-y-2 pt-3 border-t border-zinc-50">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">ИНН</span>
                                            <span id="summary-org-inn"
                                                class="text-[13px] font-mono text-zinc-700"></span>
                                        </div>
                                        <div id="summary-kpp-container" class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">КПП</span>
                                            <span id="summary-org-kpp"
                                                class="text-[13px] font-mono text-zinc-700"></span>
                                        </div>
                                        <div id="summary-ogrn-container" class="hidden flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">ОГРН</span>
                                            <span id="summary-org-ogrn"
                                                class="text-[13px] font-mono text-zinc-700"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="step-1-inputs">
                                <x-shop::form.control-group class="!mb-4" id="step-1-input-container">
                                    <x-shop::form.control-group.label
                                        class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                        ИНН или название организации
                                    </x-shop::form.control-group.label>

                                    <div class="relative w-full overflow-visible flex gap-0 border border-zinc-200"
                                        id="search-wrapper">
                                        <div class="relative flex-grow">
                                            <v-field name="inn" rules="required" label="ИНН или название организации"
                                                v-slot="{ field }">
                                                <input type="text" id="inn-input" v-bind="field" @input="
                                                        const query = $event.target.value.trim();
                                                        const lookupBtn = document.getElementById('lookup-org-btn');
                                                        const step1Details = document.getElementById('step-1-details');
                                                        if (lookupBtn) lookupBtn.disabled = query.length < 3;
                                                        if (step1Details && !step1Details.classList.contains('hidden')) step1Details.classList.add('hidden');
                                                        if (typeof window.orgDebounceTimer !== 'undefined') clearTimeout(window.orgDebounceTimer);
                                                        if (query.length < 3) { document.getElementById('org-suggestions').classList.add('hidden'); return; }
                                                        window.orgDebounceTimer = setTimeout(() => { window.triggerOrgLookup(query, false); }, 500);
                                                    "
                                                    @keydown.enter.prevent="window.triggerOrgLookup($event.target.value.trim(), true)"
                                                    class="!py-3.5 !px-4 !border-0 transition-all w-full text-zinc-600 rounded-none focus:ring-0 focus:outline-none"
                                                    placeholder="Введите ИНН или название компании..."
                                                    autocomplete="off" />
                                            </v-field>
                                        </div>

                                        <button type="button" id="lookup-org-btn" disabled
                                            class="bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-100 disabled:text-zinc-400 text-white font-bold px-8 !py-3.5 transition-all text-[14px] rounded-none border-none border-l border-zinc-200">
                                            Найти
                                        </button>

                                        <div id="org-suggestions"
                                            style="max-height: 320px !important; overflow-y: auto !important;"
                                            class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-none shadow-2xl hidden">
                                        </div>
                                    </div>
                                    <x-shop::form.control-group.error control-name="inn" />
                                </x-shop::form.control-group>

                                <!-- Confirmation Card -->
                                <div id="step-1-details"
                                    class="hidden space-y-8 bg-white rounded-none p-8 border-2 border-[#7C45F5] relative transition-all duration-500 shadow-[0_20px_50px_rgba(124,69,245,0.1)]">
                                    <div
                                        class="absolute -top-4 left-6 bg-[#7C45F5] text-white px-4 py-1 text-[12px] font-black uppercase tracking-widest">
                                        Проверка данных</div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-[18px] font-black text-zinc-900 tracking-tight">Это верная
                                            организация?</h3>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="p-6 bg-zinc-50/30 !rounded-none border-b border-zinc-100 pb-8 transition-all duration-300"
                                            id="name-container">
                                            <label
                                                class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2 opacity-60">Название
                                                организации</label>
                                            <input type="text" name="name" id="name-input"
                                                class="w-full bg-transparent border-0 p-0 text-[24px] font-black text-zinc-900 focus:ring-0 transition-all placeholder:text-zinc-300 tracking-tight read-only:opacity-80 read-only:cursor-default"
                                                placeholder="Название организации" />
                                        </div>
                                        <div class="px-6 space-y-8 pb-4">
                                            <div id="address-container" class="transition-all duration-300">
                                                <label
                                                    class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2 opacity-60">Юридический
                                                    адрес</label>
                                                <input type="text" name="address" id="address-input"
                                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-medium text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 leading-relaxed read-only:opacity-80 read-only:cursor-default"
                                                    placeholder="Юридический адрес" />
                                            </div>
                                            <div
                                                class="flex flex-wrap items-center gap-x-10 gap-y-4 pt-2 border-t border-zinc-50">
                                                <div id="kpp-container"
                                                    class="flex items-center gap-3 transition-all duration-300">
                                                    <span
                                                        class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest opacity-40">КПП</span>
                                                    <input type="text" name="kpp" id="kpp-input"
                                                        class="w-[110px] bg-zinc-50 border border-zinc-100 px-3 py-1 text-[13px] font-mono text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 rounded-none read-only:bg-zinc-100/50 read-only:border-transparent read-only:cursor-default"
                                                        placeholder="—" />
                                                </div>
                                                <div id="ogrn-container"
                                                    class="flex items-center gap-3 transition-all duration-300">
                                                    <span
                                                        class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest opacity-40">ОГРН</span>
                                                    <input type="text" name="ogrn" id="ogrn-input"
                                                        class="w-[160px] bg-zinc-50 border border-zinc-100 px-3 py-1 text-[13px] font-mono text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 rounded-none read-only:bg-zinc-100/50 read-only:border-transparent read-only:cursor-default"
                                                        placeholder="—" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-4 flex flex-col items-center">
                                        <button type="button" id="confirm-step-1-btn"
                                            class="w-full bg-[#7C45F5] hover:bg-black text-white font-black py-5 px-8 rounded-none shadow-2xl transition-all active:scale-[0.97] flex items-center justify-center gap-4 text-[17px] uppercase tracking-wider group">
                                            Да, всё верно
                                            <span
                                                class="icon-arrow-right text-xl group-hover:translate-x-1 transition-transform"></span>
                                        </button>
                                        <p class="text-[12px] text-zinc-400 mt-4 font-medium">Проверьте данные, прежде
                                            чем перейти к реквизитам</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================== STEP 2: BANK DETAILS ================== -->
                        <div id="step-2" class="hidden transition-all duration-300 pt-8 border-t border-zinc-100">
                            <div id="step-2-card"
                                class="space-y-6 bg-white rounded-none p-6 border-2 border-[#7C45F5] relative transition-all duration-500 shadow-[0_20px_50px_rgba(124,69,245,0.1)]">
                                <div
                                    class="absolute -top-3.5 left-6 bg-[#7C45F5] text-white px-3 py-1 text-[11px] font-black uppercase tracking-widest">
                                    Реквизиты платежа</div>
                                <div class="flex items-center justify-between mb-1" id="step-2-header">
                                    <h2 class="text-[17px] font-black text-zinc-900 tracking-tight">Шаг 2: Банковские
                                        реквизиты</h2>
                                    <span id="step-2-badge"
                                        class="hidden bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">✓
                                        Заполнено</span>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    <!-- Left: Bank Search -->
                                    <div class="relative">
                                        <x-shop::form.control-group class="!mb-0" id="step-2-input-container">
                                            <x-shop::form.control-group.label
                                                class="required !text-[12px] !font-bold text-zinc-400 !mb-1.5 uppercase tracking-wider opacity-80">Название
                                                банка или БИК</x-shop::form.control-group.label>
                                            <div class="relative w-full overflow-visible">
                                                <x-shop::form.control-group.control type="text" name="bic"
                                                    :value="old('bic')" id="bic-input"
                                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all w-full relative z-10 !rounded-none text-[15px] focus:outline-none"
                                                    placeholder="Начните вводить..." autocomplete="off" />
                                                <div id="bank-suggestions"
                                                    style="max-height: 320px !important; overflow-y: auto !important;"
                                                    class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-none shadow-2xl hidden">
                                                </div>
                                            </div>
                                            <x-shop::form.control-group.error control-name="bic" />
                                        </x-shop::form.control-group>
                                    </div>

                                    <!-- Right: Settlement Account -->
                                    <div>
                                        <x-shop::form.control-group class="!mb-0">
                                            <x-shop::form.control-group.label
                                                class="required !text-[12px] !font-bold text-zinc-400 !mb-1.5 uppercase tracking-wider flex items-center gap-2 opacity-80">
                                                @lang('shop::app.customers.account.organizations.create.settlement_account')
                                                <span
                                                    class="text-[9px] bg-zinc-50 px-1.5 py-0.5 border border-zinc-100 font-mono text-zinc-400">20</span>
                                            </x-shop::form.control-group.label>
                                            <x-shop::form.control-group.control type="text" name="settlement_account"
                                                rules="required|length:20" :value="old('settlement_account')"
                                                id="settlement-account-input"
                                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all text-[15px] font-mono tracking-[0.1em] placeholder:tracking-normal placeholder:font-sans !rounded-none focus:outline-none"
                                                :label="trans('shop::app.customers.account.organizations.create.settlement_account')"
                                                placeholder="40702810..." />
                                            <x-shop::form.control-group.error control-name="settlement_account" />
                                            <div id="settlement-account-error"
                                                class="text-red-500 text-[11px] mt-1 hidden font-bold"></div>
                                        </x-shop::form.control-group>
                                    </div>
                                </div>

                                <!-- Extracted Bank Details -->
                                <div id="step-2-details"
                                    class="hidden mt-6 space-y-4 bg-white rounded-none p-6 border border-zinc-100 relative shadow-sm">
                                    <div class="space-y-4">
                                        <div>
                                            <h3 id="display-bank-name"
                                                class="text-[17px] font-bold text-zinc-900 leading-tight"></h3>
                                            <input type="hidden" name="bank_name" id="bank-name-input" />
                                        </div>
                                        <div class="flex items-center gap-6 pt-4 border-t border-zinc-100">
                                            <div class="text-[12px] text-zinc-400 flex items-center gap-1.5">
                                                <span class="font-bold uppercase tracking-wider opacity-60">БИК</span>
                                                <span id="display-bic"
                                                    class="font-mono text-zinc-600 bg-zinc-50 px-2 py-0.5 border border-zinc-100"></span>
                                            </div>
                                            <div class="text-[12px] text-zinc-400 flex items-center gap-1.5">
                                                <span class="font-bold uppercase tracking-wider opacity-60">КОРР.
                                                    СЧЕТ</span>
                                                <span id="display-corr-account"
                                                    class="font-mono text-zinc-600 bg-zinc-50 px-2 py-0.5 border border-zinc-100"></span>
                                                <input type="hidden" name="correspondent_account"
                                                    id="corr-account-input" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 flex gap-4">
                                    <button type="button" onclick="switchStep('b2b-management')"
                                        class="flex-1 bg-white border border-zinc-200 text-zinc-600 font-black py-4 px-8 rounded-none hover:bg-zinc-50 transition-all active:scale-[0.97] flex items-center justify-center gap-4 text-[16px] uppercase tracking-wider">
                                        Отмена
                                    </button>
                                    <button type="submit" id="org-submit-btn" disabled
                                        class="flex-[2] bg-[#7C45F5] hover:bg-black disabled:bg-zinc-200 disabled:text-zinc-400 text-white font-black py-4 px-8 rounded-none shadow-2xl disabled:shadow-none transition-all active:scale-[0.97] flex items-center justify-center gap-4 text-[16px] uppercase tracking-wider group">
                                        @lang('shop::app.customers.account.organizations.create.save')
                                        <span
                                            class="icon-arrow-right text-xl group-hover:translate-x-1 transition-transform"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </x-shop::form>
            </div>
        </div>

        @push('scripts')
            <script>
                             let currentStep = 'dashbo            ard';
                            const initialTitle = "Meanly Wallet";

                            function switchStep(newStep) {
                                ['step-dashboard', 'step-transactions', 'step-details', 'step-management', 'step-add-wallet', 'step-empty', 'step-deposit-type', 'step-b2b-management', 'step-add-organization', 'step-b2c-details', 'step-topup-details'].forEach(id => {
                                    const el = document.getElementById(id);
                                    if (el) el.classList.add('hidden');
                                });
                                const target = document.getElementById('step-' + newStep);
                                if (target) target.classList.remove('hidden');
                                currentStep = newStep;
                                updateHeader();
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }

                            function updateHeader() {
                                const titleEl = document.getElementById('page-title');
                                const backLink = document.getElementById('page-back-link');
                                const backBtn = document.getElementById('step-back-btn');

                                if (currentStep === 'dashboard') {
                                    titleEl.innerText = initialTitle;
                                    backLink.style.display = 'flex';
                                    backBtn.style.display = 'none';
                                } else {
                                    backLink.style.display = 'none';
                                    backBtn.style.display = 'flex';
                                    if (currentStep === 'transactions') titleEl.innerText = "История";
                                    if (currentStep === 'empty') titleEl.innerText = "Кошельки";
                                    if (currentStep === 'deposit-type') titleEl.innerText = "Пополнить баланс";
                                    if (currentStep === 'details') titleEl.innerText = "Детали пополнения";
                                    if (currentStep === 'management') titleEl.innerText = "Кошельки для пополнения";
                                    if (currentStep === 'b2b-management') titleEl.innerText = "Мои организации";
                                    if (currentStep === 'add-wallet') titleEl.innerText = "Новый кошелек";
                                    if (currentStep === 'add-organization') titleEl.innerText = "Добавить организацию";
                                    if (currentStep === 'b2c-details') titleEl.innerText = "Реквизиты для оплаты";
                                    if (currentStep === 'topup-details') titleEl.innerText = "Оформление счета";
                                }
                            }

                            function handleStepBack() {
                                if (currentStep === 'transactions') switchStep('dashboard');
                                else if (currentStep === 'deposit-type') switchStep('dashboard');
                                else if (currentStep === 'empty') switchStep('deposit-type');
                                else if (currentStep === 'details') switchStep('management');
                                else if (currentStep === 'management') switchStep('deposit-type');
                                else if (currentStep === 'b2b-management') switchStep('deposit-type');
                                else if (currentStep === 'b2c-details') switchStep('deposit-type');
                                else if (currentStep === 'add-wallet') switchStep('management');
                                else if (currentStep === 'add-organization') switchStep('b2b-management');
                                else if (currentStep === 'topup-details') switchStep('b2b-management');
                            }

                            function goToDeposit() { switchStep('deposit-type'); }
                            function goToCryptoManagement() { switchStep(@json($allAddresses->isEmpty() ? 'empty' : 'management')); }
                            function goToB2BManagement() { switchStep('b2b-management'); }
                            function goToB2CManagement() { switchStep('b2c-details'); }
                            function goToManagement() { switchStep('management'); }
                            function goToAddWallet() { switchStep('add-wallet'); }
                            function goToAddOrganization() { switchStep('add-organization'); }
                            function selectAsset(assetKey, walletId) {
                                // Check if wallet is verified, ideally this should be handled better, but simple check for now
                                const form = document.getElementById('delete-wallet-form-' + walletId);
                                if (form && !form.closest('div.bg-white').querySelector('svg[viewBox="0 0 24 24"]')) {
                                    alert('Сначала верифицируйте кошелек для пополнения.');
                                    return;
                                }
                                switchStep('details');
                                document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                                const target = document.getElementById('details-wallet-' + walletId);
                                if (target) target.classList.remove('hidden');
                            }

                            let _selectedTopupOrgId = null;

                            function selectTopupOrg(id, name) {
                                _selectedTopupOrgId = id;
                                document.getElementById('selected-org-name').innerText = name;
                                switchStep('topup-details');
                            }

                            async function generateTopupInvoice() {
                                const amount = document.getElementById('topup-amount').value;
                                const btn = document.getElementById('generate-topup-btn');
                                const btnText = document.getElementById('btn-text');
                                const btnLoader = document.getElementById('btn-loader');
                                const successMsg = document.getElementById('topup-success-msg');
                                const invoiceLink = document.getElementById('topup-invoice-link');

                                if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                                    alert('Пожалуйста, введите корректную сумму');
                                    return;
                                }

                                btn.disabled = true;
                                btnText.classList.add('hidden');
                                btnLoader.classList.remove('hidden');

                                try {
                                    const response = await fetch("{{ route('shop.customers.account.credits.topup.store') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            amount: amount,
                                            organization_id: _selectedTopupOrgId
                                        })
                                    });

                                    const result = await response.json();

                                    if (result.success) {
                                        successMsg.classList.remove('hidden');
                                        invoiceLink.href = "{{ route('shop.customers.account.credits.topup.print', ['id' => ':id']) }}".replace(':id', result.transaction_id);
                                        btn.classList.add('hidden');
                                    } else {
                                        alert(result.message || 'Произошла ошибка при создании счета');
                                        btn.disabled = false;
                                        btnText.classList.remove('hidden');
                                        btnLoader.classList.add('hidden');
                                    }
                                } catch (error) {
                                    console.error('Topup Error:', error);
                                    alert('Произошла системная ошибка. Пожалуйста, попробуйте позже.');
                                    btn.disabled = false;
                                    btnText.classList.remove('hidden');
                                    btnLoader.classList.add('hidden');
                                }
                            }

                            function copyAddr(text, btn) {
                                navigator.clipboard.writeText(text).then(() => {
                                    const orig = btn.innerText; btn.innerText = '✓ ADDR OK';
                                    setTimeout(() => btn.innerText = orig, 2000);
                                });
                            }

                            // --- FULL VALIDATION LOGIC ---
                            const _SHA256 = (() => { const K = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2]; function h(msg) { let H = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]; msg = Array.from(msg); const l = msg.length * 8; msg.push(0x80); while ((msg.length % 64) !== 56) msg.push(0); for (let i = 7; i >= 0; i--)msg.push((l / (2 ** (i * 8))) & 0xFF); for (let c = 0; c < msg.length; c += 64) { const W = []; for (let i = 0; i < 16; i++)W[i] = (msg[c + i * 4] << 24) | (msg[c + i * 4 + 1] << 16) | (msg[c + i * 4 + 2] << 8) | msg[c + i * 4 + 3]; for (let i = 16; i < 64; i++) { const s0 = ((W[i - 15] >>> 7) | (W[i - 15] << 25)) ^ ((W[i - 15] >>> 18) | (W[i - 15] << 14)) ^ (W[i - 15] >>> 3); const s1 = ((W[i - 2] >>> 17) | (W[i - 2] << 15)) ^ ((W[i - 2] >>> 19) | (W[i - 2] << 13)) ^ (W[i - 2] >>> 10); W[i] = (W[i - 16] + s0 + W[i - 7] + s1) >>> 0; } let [a, b, d, e, f, g, hh, ii] = [...H, H[6], H[7]]; for (let j = 0; j < 64; j++) { const S1 = ((f >>> 6) | (f << 26)) ^ ((f >>> 11) | (f << 21)) ^ ((f >>> 25) | (f << 7)); const ch = (f & g) ^ (~f & hh); const t1 = (ii + S1 + ch + K[j] + W[j]) >>> 0; const S0 = ((a >>> 2) | (a << 30)) ^ ((a >>> 13) | (a << 19)) ^ ((a >>> 22) | (a << 10)); const maj = (a & b) ^ (a & d) ^ (b & d); const t2 = (S0 + maj) >>> 0; ii = hh; hh = g; g = f; f = (e + t1) >>> 0; e = d; d = b; b = a; a = (t1 + t2) >>> 0; } H = [H[0] + a, H[1] + b, H[2] + d, H[3] + e, H[4] + f, H[5] + g, H[6] + hh, H[7] + ii].map(v => v >>> 0); } return H.map(v => v.toString(16).padStart(8, '0')).join(''); } return { hash: h }; })();
                            function _b58d(s) { const A = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'; let n = 0n; for (const c of s) { const i = A.indexOf(c); if (i < 0) return null; n = n * 58n + BigInt(i); } let h = n.toString(16); if (h.length % 2) h = '0' + h; const b = h.match(/../g).map(x => parseInt(x, 16)); return [...Array(s.match(/^1*/)[0].length).fill(0), ...b]; }
                            function _b58chk(a) { const b = _b58d(a); if (!b || b.length < 5) return false; const p = b.slice(0, -4), cs = b.slice(-4), h1 = _SHA256.hash(p), h2 = _SHA256.hash(h1.match(/../g).map(x => parseInt(x, 16))); return h2.slice(0, 8) === cs.map(x => x.toString(16).padStart(2, '0')).join(''); }
                            function _crc16(d) { let c = 0; for (const b of d) { c ^= (b << 8); for (let i = 0; i < 8; i++)c = (c & 0x8000) ? ((c << 1) ^ 0x1021) : (c << 1); } return c & 0xFFFF; }

                            const ADDR_NETS = {
                                bitcoin: { validate: a => { if (/^bc1[a-z0-9]{6,87}$/.test(a)) return true; if (!/^[13][1-9A-HJ-NP-Za-km-z]{25,34}$/.test(a)) return false; return _b58chk(a); }, bg: '#FFF7ED', color: '#F7931A' },
                                ethereum: { validate: a => /^0x[0-9a-fA-F]{40}$/.test(a), bg: '#EEF2FF', color: '#627EEA' },
                                ton: { validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); let bin; try { bin = atob(b64); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); }, bg: '#E0F5FF', color: '#0098EA' },
                                usdt_ton: { validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); let bin; try { bin = atob(b64); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); }, bg: '#E6F6F1', color: '#26A17B' },
                                dash: { validate: a => { if (!/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(a)) return false; return _b58chk(a); }, bg: '#EFF6FF', color: '#1c75bc' }
                            };

                            const COIN_NETWORKS = {
                                'btc': [{ id: 'bitcoin', label: 'Bitcoin (BTC)' }],
                                'eth': [{ id: 'ethereum', label: 'Ethereum (ERC20)' }],
                                'usdt': [{ id: 'usdt_ton', label: 'TON Network' }],
                                'ton': [{ id: 'ton', label: 'TON Network' }],
                                'dash': [{ id: 'dash', label: 'Dash' }]
                            };

                            const COIN_COLORS = {
                                'btc': '#F7931A',
                                'eth': '#627EEA',
                                'usdt': '#26A17B',
                                'ton': '#0098EA',
                                'dash': '#1c75bc'
                            };

                            let _selCoin = null;
                            let _selNet = null;

                            function selCoin(coin) {
                                _selCoin = coin;

                                // Update Coin Buttons
                                document.querySelectorAll('button[id^="coin-"]').forEach(b => {
                                    const id = b.id.replace('coin-', '');
                                    const isSelected = id === coin;
                                    const color = COIN_COLORS[id];

                                    if (isSelected) {
                                        b.style.borderColor = color;
                                        b.style.borderWidth = '1.5px'; // slightly thicker border
                                        b.classList.remove('bg-white');
                                        b.classList.add('bg-zinc-50/30'); // slight tint

                                        document.getElementById('coin-label-' + id).style.color = color;
                                    } else {
                                        b.style.borderColor = '#f4f4f5'; // zinc-100
                                        b.style.borderWidth = '1px';
                                        b.classList.add('bg-white');
                                        b.classList.remove('bg-zinc-50/30');

                                        document.getElementById('coin-label-' + id).style.color = '#a1a1aa'; // zinc-400
                                    }
                                });

                                const nets = COIN_NETWORKS[coin];
                                const container = document.getElementById('network-options-container');
                                container.innerHTML = '';

                                nets.forEach(net => {
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'network-btn flex items-center justify-center p-3  border border-zinc-100 bg-white transition-all font-bold text-[13px] text-zinc-900';
                                    btn.innerText = net.label;
                                    btn.onclick = () => selActualNet(net.id, btn, coin);
                                    container.appendChild(btn);
                                });

                                document.getElementById('wallet-network-section').classList.remove('hidden');
                                document.getElementById('wallet-addr-section').classList.add('hidden');
                                document.getElementById('wallet-net-input').value = '';
                                _selNet = null;
                                onWalletInput('');

                                if (nets.length === 1) {
                                    container.firstChild.click();
                                }
                            }

                            function selActualNet(netId, btnEl, coin) {
                                _selNet = netId;
                                document.getElementById('wallet-net-input').value = netId;

                                document.querySelectorAll('.network-btn').forEach(b => {
                                    b.style.borderColor = '#f4f4f5'; // zinc-100
                                    b.style.borderWidth = '1px';
                                    b.classList.remove('bg-[#E6F6F1]'); // ensure we remove any specific bg class if we add them later
                                    b.style.background = '#ffffff';
                                });

                                // Specifically map the green background for USDT as shown in screenshot
                                const color = COIN_COLORS[coin];
                                btnEl.style.borderColor = color;
                                btnEl.style.borderWidth = '1.5px';

                                // If it's the green usdt color, match the screenshot's green tint background
                                if (color === '#26A17B') {
                                    btnEl.style.background = '#E6F6F1';
                                } else if (color === '#0098EA') {
                                    btnEl.style.background = '#E0F5FF';
                                } else if (color === '#F7931A') {
                                    btnEl.style.background = '#FFF7ED';
                                } else if (color === '#627EEA') {
                                    btnEl.style.background = '#EEF2FF';
                                } else {
                                    btnEl.style.background = '#f8fafc'; // light gray fallback
                                }

                                document.getElementById('wallet-addr-section').classList.remove('hidden');
                                onWalletInput(document.getElementById('wallet-addr-input').value);
                            }

                            function onWalletInput(val) {
                                const v = _selNet ? ADDR_NETS[_selNet].validate(val.trim()) : false;
                                const btn = document.getElementById('wallet-add-btn');
                                btn.disabled = !v;
                                btn.style.opacity = v ? '1' : '0.4';
                                btn.style.cursor = v ? 'pointer' : 'not-allowed';
                            }
                            function confirmWalletDeletion(id, expected) { if (prompt(`Введите "${expected}" для удаления:`) === expected) document.getElementById(`delete-wallet-form-${id}`).submit(); }

                            document.addEventListener('DOMContentLoaded', () => {
                                const urlParams = new URLSearchParams(window.location.search);
                                const step = urlParams.get('step');
                                if (step === 'transactions') switchStep('transactions');
                                else if (step === 'deposit') goToDeposit();
                                else if (step === 'management') goToManagement();
                                else if (step === 'b2b-management') goToB2BManagement();

                                @if($errors->any() && old('inn'))
                                    switchStep('add-organization');
                                    // If name is already present due to old input, fast forward to step 2 visually
                                    @if(old('name'))
                                        setTimeout(() => {
                                            document.getElementById('step-1-inputs').classList.add('hidden');
                                            document.getElementById('step-1-summary').classList.remove('hidden');
                                            document.getElementById('step-1-badge').classList.remove('hidden');
                                            document.getElementById('step-2').classList.remove('hidden');

                                            if (document.getElementById('summary-org-name')) document.getElementById('summary-org-name').innerText = "{{ old('name') }}";
                                            if (document.getElementById('summary-org-inn')) document.getElementById('summary-org-inn').innerText = "{{ old('inn') }}";
                                            if (document.getElementById('summary-org-address')) document.getElementById('summary-org-address').innerText = "{{ old('address') }}";
                                            if (document.getElementById('summary-org-kpp') && "{{ old('kpp') }}") {
                                                document.getElementById('summary-org-kpp').innerText = "{{ old('kpp') }}";
                                                document.getElementById('summary-kpp-container').classList.remove('hidden');
                                            }
                                            if (document.getElementById('summary-org-ogrn') && "{{ old('ogrn') }}") {
                                                document.getElementById('summary-org-ogrn').innerText = "{{ old('ogrn') }}";
                                                document.getElementById('summary-ogrn-container').classList.remove('hidden');
                                            }

                                            // Also show the bank details display if we had selected one
                                            @if(old('bic') && old('bank_name'))
                                                document.getElementById('display-bank-name').innerText = "{{ old('bank_name') }}";
                                                document.getElementById('display-corr-account').innerText = "{{ old('correspondent_account') }}";
                                                document.getElementById('display-bic').innerText = "{{ old('bic') }}";
                                                document.getElementById('step-2-details').classList.remove('hidden');
                                            @endif
                                                                                                            }, 100);
                                    @endif
                                @endif

                                @if(session('show_verify_id'))
                                    @php $target = $allAddresses->firstWhere('id', session('show_verify_id')); @endphp
                                    @if($target)
                                        goToManagement();
                                        setTimeout(() => showVerifyModal('{{ $target->id }}', '{{ $target->network }}', '{{ rtrim(rtrim(number_format($target->verification_amount ?? 0, 8, '.', ''), '0'), '.') }}', '{{ $target->address }}'), 500);
                                    @endif
                                @endif

                                                                                    });

                            // --- ORGANIZATION WIZARD SCRIPTS ---
                            window.isValidBankAccount = function (bic, account) {
                                bic = (bic || '').toString().replace(/\D/g, '');
                                account = (account || '').toString().replace(/\D/g, '');

                                if (bic.length !== 9 || account.length !== 20) return false;

                                let bicPart;
                                if (bic[6] === '0' && bic[7] === '0') {
                                    bicPart = '0' + bic[4] + bic[5];
                                } else {
                                    bicPart = bic.substring(6, 9);
                                }

                                const combined = bicPart + account;
                                const weights = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];

                                let sum = 0;
                                for (let i = 0; i < 23; i++) {
                                    const digit = parseInt(combined[i]);
                                    sum += (digit * weights[i]) % 10;
                                }

                                return sum % 10 === 0;
                            };

                            window.initOrganizationWizard = function () {
                                let bankDebounceTimer;

                                document.addEventListener('click', async function (e) {
                                    const confirmStep1Btn = e.target.closest('#confirm-step-1-btn');
                                    if (confirmStep1Btn) {
                                        const inn = document.getElementById('inn-input').value;

                                        // Check if this INN is already in the list of existing organizations
                                        const existingOrgs = Array.from(document.querySelectorAll('#step-b2b-management div.text-\\[11px\\]'));
                                        const isDuplicate = existingOrgs.some(el => el.textContent.includes('ИНН: ' + inn));

                                        if (isDuplicate) {
                                            alert('Эта организация (ИНН ' + inn + ') уже добавлена в ваш профиль.');
                                            return;
                                        }

                                        const name = document.getElementById('name-input').value;
                                        const kpp = document.getElementById('kpp-input').value;
                                        const ogrn = document.getElementById('ogrn-input').value;
                                        const address = document.getElementById('address-input').value;

                                        if (document.getElementById('summary-org-name')) document.getElementById('summary-org-name').innerText = name;
                                        if (document.getElementById('summary-org-inn')) document.getElementById('summary-org-inn').innerText = inn;
                                        if (document.getElementById('summary-org-address')) document.getElementById('summary-org-address').innerText = address;

                                        const summaryKppContainer = document.getElementById('summary-kpp-container');
                                        if (kpp) {
                                            document.getElementById('summary-org-kpp').innerText = kpp;
                                            summaryKppContainer.classList.remove('hidden');
                                        } else {
                                            summaryKppContainer.classList.add('hidden');
                                        }

                                        const summaryOgrnContainer = document.getElementById('summary-ogrn-container');
                                        if (ogrn) {
                                            document.getElementById('summary-org-ogrn').innerText = ogrn;
                                            summaryOgrnContainer.classList.remove('hidden');
                                        } else {
                                            summaryOgrnContainer.classList.add('hidden');
                                        }

                                        document.getElementById('step-1-inputs').classList.add('hidden');
                                        document.getElementById('step-1-summary').classList.remove('hidden');
                                        document.getElementById('step-1-badge').classList.remove('hidden');

                                        const s2 = document.getElementById('step-2');
                                        if (s2) {
                                            s2.classList.remove('hidden');
                                            if (document.getElementById('step-2-header')) document.getElementById('step-2-header').scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            if (document.getElementById('bic-input')) document.getElementById('bic-input').focus();
                                        }
                                        return;
                                    }

                                    const editStep1Btn = e.target.closest('#edit-step-1-btn');
                                    if (editStep1Btn) {
                                        e.preventDefault();
                                        document.getElementById('step-1-inputs').classList.remove('hidden');
                                        document.getElementById('step-1-summary').classList.add('hidden');
                                        document.getElementById('step-1-badge').classList.add('hidden');

                                        ['step-2'].forEach(id => {
                                            const step = document.getElementById(id);
                                            if (step) step.classList.add('hidden');
                                        });
                                        document.getElementById('inn-input').focus();
                                        return;
                                    }
                                });

                                window.selectOrganization = function (org) {
                                    const innInput = document.getElementById('inn-input');
                                    const nameInput = document.getElementById('name-input');
                                    const kppInput = document.getElementById('kpp-input');
                                    const addressInput = document.getElementById('address-input');
                                    const ogrnInput = document.getElementById('ogrn-input');
                                    const suggestionsContainer = document.getElementById('org-suggestions');
                                    const step1Details = document.getElementById('step-1-details');
                                    const kppContainer = document.getElementById('kpp-container');

                                    if (innInput) {
                                        innInput.value = org.inn || '';
                                        innInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                    if (nameInput) { nameInput.value = org.name || ''; nameInput.readOnly = true; }
                                    if (kppInput) { kppInput.value = org.kpp || ''; kppInput.readOnly = true; }
                                    if (addressInput) { addressInput.value = org.address || ''; addressInput.readOnly = true; }
                                    if (ogrnInput) { ogrnInput.value = org.ogrn || ''; ogrnInput.readOnly = true; }

                                    if (kppContainer) {
                                        if (!org.kpp) kppContainer.classList.add('hidden');
                                        else kppContainer.classList.remove('hidden');
                                    }

                                    if (suggestionsContainer) suggestionsContainer.classList.add('hidden');

                                    if (step1Details) {
                                        step1Details.classList.remove('hidden');
                                        step1Details.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        const confirmBtn = document.getElementById('confirm-step-1-btn');
                                        if (confirmBtn) setTimeout(() => confirmBtn.focus(), 200);
                                    }
                                };

                                window.triggerOrgLookup = async (query, forceSelect = false) => {
                                    const suggestionsContainer = document.getElementById('org-suggestions');
                                    if (!query || query.length < 3) return;

                                    try {
                                        const relativePath = "{{ route('shop.customers.account.organizations.suggest_organization', [], false) }}";
                                        const url = `${window.location.origin}${relativePath}?query=${encodeURIComponent(query)}`;
                                        const response = await fetch(url);
                                        if (!response.ok) throw new Error('Network response failure');
                                        const organizations = await response.json();

                                        if (organizations && organizations.length > 0) {
                                            if (forceSelect && organizations.length === 1) {
                                                window.selectOrganization(organizations[0]);
                                                return;
                                            }

                                            suggestionsContainer.innerHTML = organizations.map(org => {
                                                const safeName = (org.name || '').replace(/"/g, '&quot;');
                                                const safeAddress = (org.address || '').replace(/"/g, '&quot;');
                                                const safeInn = (org.inn || '').replace(/"/g, '&quot;');
                                                const safeKpp = (org.kpp || '').replace(/"/g, '&quot;');
                                                const safeOgrn = (org.ogrn || '').replace(/"/g, '&quot;');

                                                return `
                                                                                                        <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0"
                                                                                                            data-name="${safeName}" data-inn="${safeInn}" data-kpp="${safeKpp}" data-ogrn="${safeOgrn}" data-address="${safeAddress}">
                                                                                                            <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${org.name || 'Неизвестная организация'}</div>
                                                                                                            <div class="text-[12px] text-zinc-500 font-mono">
                                                                                                                ИНН: ${org.inn || '-'} ${org.kpp ? ` | КПП: ${org.kpp}` : ''} ${org.ogrn ? ` | ОГРН: ${org.ogrn}` : ''}
                                                                                                            </div>
                                                                                                            <div class="text-[11px] text-zinc-400 mt-1 truncate">${org.address || ''}</div>
                                                                                                        </div>`;
                                            }).join('');
                                            suggestionsContainer.classList.remove('hidden');
                                        } else {
                                            suggestionsContainer.classList.add('hidden');
                                        }
                                    } catch (err) {
                                        console.error('Organization Lookup Error:', err);
                                    }
                                };

                                document.addEventListener('click', function (e) {
                                    const searchBtn = e.target.closest('#lookup-org-btn');
                                    if (searchBtn) {
                                        const innInput = document.getElementById('inn-input');
                                        if (innInput) window.triggerOrgLookup(innInput.value.trim(), true);
                                    }

                                    const orgItem = e.target.closest('div[data-inn]');
                                    if (orgItem && document.getElementById('org-suggestions') && document.getElementById('org-suggestions').contains(orgItem)) {
                                        window.selectOrganization({
                                            name: orgItem.dataset.name,
                                            inn: orgItem.dataset.inn,
                                            kpp: orgItem.dataset.kpp,
                                            ogrn: orgItem.dataset.ogrn,
                                            address: orgItem.dataset.address
                                        });
                                    }
                                });

                                document.addEventListener('keydown', function (e) {
                                    if (e.target.id === 'inn-input' && e.key === 'Enter') {
                                        e.preventDefault();
                                        window.triggerOrgLookup(e.target.value.trim(), true);
                                    }
                                });

                                document.addEventListener('input', function (e) {
                                    if (e.target.id === 'inn-input') {
                                        const query = e.target.value.trim();
                                        const lookupBtn = document.getElementById('lookup-org-btn');
                                        const step1Details = document.getElementById('step-1-details');

                                        if (lookupBtn) lookupBtn.disabled = query.length < 3;
                                        if (step1Details && !step1Details.classList.contains('hidden')) step1Details.classList.add('hidden');

                                        if (typeof window.orgDebounceTimer !== 'undefined') clearTimeout(window.orgDebounceTimer);
                                        if (query.length < 3) { document.getElementById('org-suggestions').classList.add('hidden'); return; }

                                        window.orgDebounceTimer = setTimeout(() => { window.triggerOrgLookup(query, false); }, 500);
                                    }

                                    if (e.target.id === 'bic-input') {
                                        const bicInput = e.target;
                                        const suggestionsContainer = document.getElementById('bank-suggestions');

                                        clearTimeout(bankDebounceTimer);
                                        const query = bicInput.value.trim();

                                        if (query.length < 2) {
                                            suggestionsContainer.classList.add('hidden');
                                            suggestionsContainer.innerHTML = '';
                                            return;
                                        }

                                        bankDebounceTimer = setTimeout(async () => {
                                            try {
                                                const accountInput = document.getElementById('settlement-account-input');
                                                const account = accountInput ? accountInput.value.trim() : '';

                                                const relativePath = "{{ route('shop.customers.account.organizations.suggest_bank', [], false) }}";
                                                const url = `${window.location.origin}${relativePath}?query=${encodeURIComponent(query)}`;
                                                const response = await fetch(url);
                                                let banks = await response.json();

                                                if (response.ok && banks && banks.length > 0) {
                                                    if (account.length === 20) {
                                                        banks = banks.map(bank => ({ ...bank, isValidForAccount: window.isValidBankAccount(bank.bic, account) })).sort((a, b) => b.isValidForAccount - a.isValidForAccount);
                                                    }

                                                    suggestionsContainer.innerHTML = banks.map(bank => {
                                                        const safeName = (bank.bank_name || '').replace(/"/g, '&quot;');
                                                        const safeBic = (bank.bic || '').replace(/"/g, '&quot;');
                                                        const safeCorr = (bank.correspondent_account || '').replace(/"/g, '&quot;');
                                                        return `
                                                                                                                <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0 flex justify-between items-start"
                                                                                                                    data-name="${safeName}" data-bic="${safeBic}" data-corr="${safeCorr}">
                                                                                                                    <div>
                                                                                                                        <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${bank.bank_name || 'Неизвестный банк'} ${bank.isValidForAccount ? '<span class="ml-1 text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded">✓ Подходит</span>' : ''}</div>
                                                                                                                        <div class="text-[12px] text-zinc-500 font-mono">БИК: ${bank.bic || '-'} | Корр.счет: ${bank.correspondent_account || '-'}</div>
                                                                                                                    </div>
                                                                                                                </div>`;
                                                    }).join('');
                                                    suggestionsContainer.classList.remove('hidden');
                                                } else {
                                                    suggestionsContainer.classList.add('hidden');
                                                }
                                            } catch (err) { console.error('Bank Lookup Error', err); }
                                        }, 400);
                                    }

                                    if (e.target.id === 'settlement-account-input') {
                                        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 20);
                                        const account = e.target.value;
                                        const bicInput = document.getElementById('bic-input');
                                        const bic = bicInput ? bicInput.value.trim() : '';
                                        const submitBtn = document.getElementById('org-submit-btn');
                                        const errorMsg = document.getElementById('settlement-account-error');

                                        if (account.length === 20 && bic.length === 9) {
                                            const isValid = window.isValidBankAccount(bic, account);
                                            if (isValid) {
                                                e.target.classList.remove('!border-red-500', '!ring-red-500');
                                                e.target.classList.add('!border-green-500');
                                                if (errorMsg) errorMsg.classList.add('hidden');
                                                if (submitBtn) submitBtn.disabled = false;
                                            } else {
                                                e.target.classList.remove('!border-green-500');
                                                e.target.classList.add('!border-red-500', '!ring-red-500');
                                                if (errorMsg) { errorMsg.innerText = 'Неверный контрольный ключ'; errorMsg.classList.remove('hidden'); }
                                                if (submitBtn) submitBtn.disabled = true;
                                            }
                                        } else if (account.length === 20 && bic.length === 0) {
                                            e.target.classList.add('!border-green-500');
                                            if (errorMsg) errorMsg.classList.add('hidden');
                                            if (submitBtn) submitBtn.disabled = true;
                                        } else {
                                            e.target.classList.remove('!border-green-500', '!border-red-500', '!ring-red-500');
                                            if (errorMsg) errorMsg.classList.add('hidden');
                                            if (submitBtn) submitBtn.disabled = true;
                                        }
                                    }
                                });

                                document.addEventListener('click', function (e) {
                                    const orgSuggestions = document.getElementById('org-suggestions');
                                    const bankSuggestions = document.getElementById('bank-suggestions');
                                    const innInput = document.getElementById('inn-input');
                                    const bicInput = document.getElementById('bic-input');

                                    const orgItem = e.target.closest('div[data-inn]');
                                    if (orgItem && orgSuggestions && orgSuggestions.contains(orgItem)) {
                                        if (innInput) innInput.value = orgItem.dataset.inn;
                                        if (document.getElementById('name-input')) document.getElementById('name-input').value = orgItem.dataset.name || '';
                                        if (document.getElementById('kpp-input')) document.getElementById('kpp-input').value = orgItem.dataset.kpp || '';
                                        if (document.getElementById('address-input')) document.getElementById('address-input').value = orgItem.dataset.address || '';

                                        const kppContainer = document.getElementById('kpp-container');
                                        if (kppContainer) {
                                            if (!orgItem.dataset.kpp) kppContainer.classList.add('hidden');
                                            else kppContainer.classList.remove('hidden');
                                        }

                                        orgSuggestions.classList.add('hidden');
                                        const step1Details = document.getElementById('step-1-details');
                                        if (step1Details) {
                                            step1Details.classList.remove('hidden');
                                            const confirmBtn = document.getElementById('confirm-step-1-btn');
                                            if (confirmBtn) setTimeout(() => confirmBtn.focus(), 100);
                                            step1Details.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }
                                        return;
                                    }

                                    const bankItem = e.target.closest('div[data-bic]');
                                    if (bankItem && bankSuggestions && bankSuggestions.contains(bankItem)) {
                                        if (bicInput) {
                                            bicInput.value = bankItem.dataset.bic;
                                            bicInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        }
                                        if (document.getElementById('bank-name-input')) document.getElementById('bank-name-input').value = bankItem.dataset.name || '';
                                        if (document.getElementById('display-bank-name')) document.getElementById('display-bank-name').innerText = bankItem.dataset.name || '';
                                        if (document.getElementById('corr-account-input')) document.getElementById('corr-account-input').value = bankItem.dataset.corr || '';
                                        if (document.getElementById('display-corr-account')) document.getElementById('display-corr-account').innerText = bankItem.dataset.corr || '';
                                        if (document.getElementById('display-bic')) document.getElementById('display-bic').innerText = bankItem.dataset.bic || '';

                                        bankSuggestions.classList.add('hidden');
                                        const step2Details = document.getElementById('step-2-details');
                                        if (step2Details) step2Details.classList.remove('hidden');

                                        ['display-bank-name', 'display-bic', 'display-corr-account'].forEach(id => {
                                            const el = document.getElementById(id);
                                            if (el) { el.style.backgroundColor = '#f0fff4'; setTimeout(() => el.style.backgroundColor = 'transparent', 1000); }
                                        });

                                        const accountInput = document.getElementById('settlement-account-input');
                                        if (accountInput && accountInput.value.length === 20) accountInput.dispatchEvent(new Event('input', { bubbles: true }));
                                        return;
                                    }

                                    if (orgSuggestions && !orgSuggestions.classList.contains('hidden') && innInput && !innInput.contains(e.target) && !orgSuggestions.contains(e.target)) orgSuggestions.classList.add('hidden');
                                    if (bankSuggestions && !bankSuggestions.classList.contains('hidden') && bicInput && !bicInput.contains(e.target) && !bankSuggestions.contains(e.target)) bankSuggestions.classList.add('hidden');
                                });
                            };
                            window.initOrganizationWizard();
                        </script>
        @endpush

</x-shop::layouts.account>