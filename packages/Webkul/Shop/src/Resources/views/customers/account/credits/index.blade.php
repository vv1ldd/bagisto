<x-shop::layouts.account :has-header="false">
    <x-slot:title>
        Meanly Wallet
    </x-slot>

        <div class="max-w-lg mx-auto px-4 py-12">
            {{-- Top Navigation & Title (Truly Outside the Tile) --}}
            <div class="flex items-center gap-4 mb-8">
                <a id="page-back-link" href="{{ route('shop.customers.account.index') }}"
                    class="w-10 h-10 rounded-full bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <button id="step-back-btn" onclick="handleStepBack()" style="display: none;"
                    class="w-10 h-10 rounded-full bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
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
                class="ios-group p-6 bg-white rounded-[32px] shadow-md relative overflow-hidden active:scale-[0.99] transition-transform">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-400/5 rounded-full blur-3xl"></div>
                <div class="flex flex-col gap-2 relative z-10">
                    {{-- Row 1: label + badges --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="text-[12px] text-zinc-500 font-bold uppercase tracking-[0.1em] opacity-80">Общая<br>покупательная способность</div>
                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                            <div class="text-[12px] font-mono text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full border border-violet-100 font-bold whitespace-nowrap">
                                @ {{ $user->username }}
                            </div>
                            @if($user->is_investor)
                                <div class="text-[11px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200 tracking-wide whitespace-nowrap">
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
                                class="w-10 h-10 rounded-2xl bg-zinc-50 flex items-center justify-center text-zinc-400 group-hover:bg-violet-50 group-hover:text-violet-600 transition-all border border-zinc-100 group-hover:border-violet-100 shadow-sm text-[20px]">
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
                                    <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $m['color'] }}"></span>
                                    <span class="text-zinc-900 font-bold font-mono">{{ $amount }} {{ $m['label'] }}</span>
                                    <span class="text-zinc-400 opacity-60">≈</span>
                                    <span>{{ core()->formatPrice($fiat) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 text-[13px] text-zinc-400 italic">Нет активных балансов</div>
                    @endif

                    <div class="mt-8">
                        @if($user->is_investor)
                            <button onclick="goToDeposit()"
                                class="inline-flex items-center justify-center text-[14px] font-bold text-white bg-zinc-900 px-6 py-3 rounded-2xl active:scale-95 transition-all shadow-lg shadow-zinc-100">
                                + Пополнить
                            </button>
                        @else
                            <div class="flex flex-col gap-1.5">
                                <button disabled
                                    class="inline-flex items-center justify-center text-[14px] font-bold text-zinc-400 bg-zinc-100 px-6 py-3 rounded-2xl cursor-not-allowed opacity-60">
                                    + Пополнить
                                </button>
                                <p class="text-[11px] text-zinc-400 text-center">Методы пополнения пока недоступны</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Step 2: Transactions --}}
            <div id="step-transactions"
                class="hidden bg-white overflow-hidden rounded-[32px] border border-zinc-100 shadow-sm">
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
                                            class="text-[9px] px-1.5 py-0.5 rounded-md border {{ $statusClass }} uppercase tracking-wider font-bold shrink-0">{{ $transaction->status }}</span>
                                    </div>
                                    @if($transaction->notes)
                                        <div class="text-[12px] text-zinc-500 leading-tight">{{ $transaction->notes }}</div>
                                    @endif
                                    <div class="text-[11px] text-zinc-400 font-medium">
                                        {{ $transaction->created_at->format('d.m.Y — H:i') }}</div>
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
                        <div
                            class="w-20 h-20 bg-zinc-50 rounded-full flex items-center justify-center mb-6 shadow-inner text-3xl">
                            📭</div>
                        <p class="text-[17px] font-bold text-zinc-700">Транзакций не найдено</p>
                    </div>
                @endif
            </div>

            {{-- Step: Empty --}}
            <div id="step-empty"
                class="hidden bg-white rounded-[32px] border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                <div>
                    <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                    <p class="text-[13px] text-zinc-400 mt-1">Для пополнения необходимо сначала добавить и
                        верифицировать свой кошелёк.</p>
                </div>
                <button onclick="goToManagement()" style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                    class="text-white font-bold px-6 py-3 rounded-2xl text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                    + Добавить кошелёк
                </button>
            </div>

            {{-- Step: Management (Combined Deposit & Management) --}}
            <div id="step-management" class="hidden space-y-4">
                <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3 px-2">Выберите кошелек для пополнения
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
                        if (isset($nmData['token'])) $parts[] = $nmData['token'];
                        if ($address->alias) $parts[] = $address->alias;
                        $fullAlias = implode('.', $parts);

                        $explorerUrl = $m[6] . $address->address;
                        $dAmt = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');
                        
                        // Style attributes based on coin
                        $coinColor = $m[3];
                    @endphp
                    
                    <div class="bg-white rounded-3xl shadow-sm hover:shadow-md transition-all group/card relative flex items-center">
                        {{-- Clickable Area for Deposit --}}
                        <button type="button" onclick="selectAsset('{{ $address->network }}', '{{ $address->id }}')" class="flex-1 flex gap-4 p-6 min-w-0 text-left cursor-pointer items-center">
                            {{-- Icon Column --}}
                            <div class="relative shrink-0">
                                <div class="w-[52px] h-[52px] rounded-[16px] flex items-center justify-center text-white text-[22px] font-bold shadow-sm" style="background: {{ $coinColor }}">
                                    {{ $m[2] }}
                                </div>
                            </div>

                            {{-- Main Content Column --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-center gap-1.5">
                                {{-- Header: Verified Icon + Alias --}}
                                <div class="flex items-center gap-1.5 min-w-0">
                                    @if($address->isVerified())
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-black shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41 1.41-8.48 8.48z" />
                                        </svg>
                                    @endif
                                    <span class="text-[18px] font-bold text-black truncate tracking-tight">{{ $fullAlias }}</span>
                                </div>

                                {{-- Network Breadcrumbs + Address (Premium View) --}}
                                <div class="flex items-center gap-2 text-[12px] font-bold text-zinc-400 uppercase tracking-tight">
                                    <span class="shrink-0">{{ $m[5] }}</span>
                                    <span class="shrink-0 text-zinc-200">›</span>
                                    <span class="shrink-0">{{ $m[1] }}</span>
                                    <span class="shrink-0 text-zinc-200">›</span>
                                    <div class="flex items-center gap-1.5 min-w-0 pr-2">
                                        <code class="font-mono text-[12px] text-zinc-400 truncate tracking-tighter opacity-70">{{ $address->address }}</code>
                                    </div>
                                </div>

                                {{-- Balance + Sync Status --}}
                                <div class="flex items-center gap-3 mt-0.5">
                                    <span class="text-[16px] font-bold font-mono text-black">
                                        {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                        <span class="text-[12px] text-zinc-400 font-bold uppercase ml-1">{{ $m[1] }}</span>
                                    </span>
                                    <div class="flex items-center gap-1 text-[12px] font-bold text-zinc-400 opacity-60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
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
                            <div class="shrink-0 pl-2 pr-4 text-zinc-300 group-hover/card:text-violet-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Action Column (Delete) --}}
                        <div class="shrink-0 flex items-center pr-8 pl-2 border-l border-zinc-50 ml-2">
                            <form id="delete-wallet-form-{{ $address->id }}" action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmWalletDeletion('{{ $address->id }}', '{{ $address->alias ?: $address->address }}')" 
                                    class="w-[42px] h-[42px] rounded-[16px] flex items-center justify-center bg-zinc-50 text-zinc-400 transition-all hover:bg-red-50 hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <button onclick="goToAddWallet()" class="w-full py-[22px] mt-4 border border-dashed border-zinc-200 bg-transparent text-zinc-400 font-bold hover:text-zinc-600 transition-all flex items-center justify-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-zinc-100 flex items-center justify-center text-[18px] text-zinc-400">+</span>
                    <span class="text-[15px]">Добавить новый кошелек</span>
                </button>
            </div>

            {{-- Step: Deposit Details --}}
            <div id="step-details" class="hidden">
                @foreach($allAddresses as $address)
                    @php
                        $nm = ['bitcoin' => ['Bitcoin', 'BTC'], 'ethereum' => ['Ethereum', 'ETH'], 'ton' => ['TON', 'TON'], 'usdt_ton' => ['USDT (TON)', 'USDT'], 'dash' => ['Dash', 'DASH']];
                        $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc'];
                    @endphp
                    <div id="details-wallet-{{ $address->id }}" class="wallet-details-view hidden">
                        <div class="bg-white rounded-[32px] shadow-sm overflow-hidden p-6 md:p-8 flex flex-col items-center">
                            
                            {{-- QR Code Section --}}
                            <div class="relative inline-block mt-4 mb-2">
                                <div class="border border-zinc-100 rounded-[32px] p-6 pb-8 bg-white shadow-sm inline-block">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($address->address) }}"
                                        alt="QR Code" class="w-56 h-56 mx-auto" />
                                </div>
                                {{-- Floated Label --}}
                                <div class="absolute -bottom-3 left-0 right-0 flex justify-center">
                                    <div class="bg-white px-4 py-1.5 text-[11px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                        Адрес пополнения ({{ $m[0] }})
                                    </div>
                                </div>
                            </div>

                            {{-- Address Copy Section --}}
                            <div class="w-full max-w-sm mt-8 bg-zinc-50 rounded-[28px] p-6 text-center cursor-pointer active:scale-95 transition-all group"
                                onclick="copyAddr('{{ $address->address }}', this.querySelector('.copy-txt'))">
                                <code class="font-mono text-[14px] text-zinc-800 break-all block leading-relaxed mb-6">
                                    {{ $address->address }}
                                </code>
                                <div class="flex items-center justify-center gap-2 text-black font-black text-[11px] uppercase tracking-wider">
                                    <span class="copy-txt">Скопировать</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Verification Warning --}}
                            <div class="w-full max-w-sm mt-8 p-5 bg-violet-50/50 rounded-2xl flex gap-3 text-left">
                                <span class="text-lg">⚠️</span>
                                <p class="text-[12px] text-violet-700 leading-snug">
                                    <b>Внимание:</b> Переводите средства исключительно из верифицированного кошелька, чтобы система смогла автоматически зачислить платеж.
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

        {{-- Step: Add Wallet --}}
        <div id="step-add-wallet" class="hidden bg-white rounded-[32px] border border-zinc-100 shadow-sm p-6 md:p-8">
            <x-shop::form :action="route('shop.customers.account.crypto.store')">
                <input type="hidden" name="network" id="wallet-net-input" value="">
                
                <div class="space-y-6">
                    {{-- 1. Asset/Coin Selection --}}
                    <div>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">1. Монета</p>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach([
                                'btc' => ['₿', 'BTC', '#F7931A'], 
                                'eth' => ['Ξ', 'ETH', '#627EEA'], 
                                'usdt' => ['₮', 'USDT', '#26A17B'],
                                'ton' => ['◎', 'TON', '#0098EA'], 
                                'dash' => ['D', 'DASH', '#1c75bc']
                            ] as $coin => $m)
                                <button type="button" id="coin-{{ $coin }}" onclick="selCoin('{{ $coin }}')" class="flex flex-col items-center justify-center p-3 rounded-[16px] border border-zinc-100 transition-all group bg-white hover:bg-zinc-50 relative overflow-hidden">
                                    <div id="coin-icon-{{ $coin }}" class="w-8 h-8 rounded-[12px] flex items-center justify-center text-white text-[15px] font-bold mb-1.5 shadow-sm transition-all" style="background: {{ $m[2] }}">
                                        {{ $m[0] }}
                                    </div>
                                    <span id="coin-label-{{ $coin }}" class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter transition-all">{{ $m[1] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Network Selection (Dynamic) --}}
                    <div id="wallet-network-section" class="hidden">
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">2. Сеть</p>
                        <div id="network-options-container" class="grid grid-cols-2 gap-2">
                            {{-- Populated by JS --}}
                        </div>
                    </div>

                    {{-- 3. Address Input --}}
                    <div id="wallet-addr-section" class="hidden space-y-6">
                        <div>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">3. Адрес</p>
                            <input type="text" name="address" id="wallet-addr-input" placeholder="Вставьте адрес…" oninput="onWalletInput(this.value)" class="w-full rounded-2xl border-zinc-100 bg-zinc-50 font-mono py-4 pl-5 focus:bg-white transition-all shadow-inner placeholder-zinc-300 text-zinc-800" />
                        </div>
                        
                        <div class="pt-2"> {{-- Added padding top to separate button from input --}}
                            <button type="submit" id="wallet-add-btn" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);opacity:0.4;cursor:not-allowed;" class="w-full text-white font-bold py-4 rounded-2xl shadow-lg transition-all">+ Добавить кошелек</button>
                        </div>
                    </div>
                </div>
            </x-shop::form>
        </div>
        </div>

        @push('scripts')
            <script>
                let currentStep = 'dashboard';
                const initialTitle = "Meanly Wallet";

                function switchStep(newStep) {
                    ['step-dashboard', 'step-transactions', 'step-details', 'step-management', 'step-add-wallet', 'step-empty'].forEach(id => {
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
                        if (currentStep === 'details') titleEl.innerText = "Детали пополнения";
                        if (currentStep === 'management') titleEl.innerText = "Кошельки для пополнения";
                        if (currentStep === 'add-wallet') titleEl.innerText = "Новый кошелек";
                    }
                }

                function handleStepBack() {
                    if (currentStep === 'transactions') switchStep('dashboard');
                    else if (currentStep === 'empty') switchStep('dashboard');
                    else if (currentStep === 'details') switchStep('management');
                    else if (currentStep === 'management') switchStep('dashboard');
                    else if (currentStep === 'add-wallet') switchStep('management');
                }

                function goToDeposit() { switchStep(@json($allAddresses->isEmpty() ? 'empty' : 'management')); }
                function goToManagement() { switchStep('management'); }
                function goToAddWallet() { switchStep('add-wallet'); }
                function selectAsset(assetKey, walletId) {
                    // Check if wallet is verified, ideally this should be handled better, but simple check for now
                    const form = document.getElementById('delete-wallet-form-' + walletId);
                    if(form && !form.closest('div.bg-white').querySelector('svg[viewBox="0 0 24 24"]')) {
                        alert('Сначала верифицируйте кошелек для пополнения.');
                        return;
                    }
                    switchStep('details');
                    document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                    const target = document.getElementById('details-wallet-' + walletId);
                    if (target) target.classList.remove('hidden');
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
                    btn.className = 'network-btn flex items-center justify-center p-3 rounded-[16px] border border-zinc-100 bg-white transition-all font-bold text-[13px] text-zinc-900';
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

                    @if(session('show_verify_id'))
                        @php $target = $allAddresses->firstWhere('id', session('show_verify_id')); @endphp
                        @if($target)
                            goToManagement();
                            setTimeout(() => showVerifyModal('{{ $target->id }}', '{{ $target->network }}', '{{ rtrim(rtrim(number_format($target->verification_amount ?? 0, 8, '.', ''), '0'), '.') }}', '{{ $target->address }}'), 500);
                        @endif
                    @endif
                });
            </script>
        @endpush
</x-shop::layouts.account>