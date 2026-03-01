<x-shop::layouts.account>
    <x-slot:title>
        Баланс и история
        </x-slot>

        @push('styles')
            <style>
                .credit-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                }

                .credit-row:last-child {
                    border-bottom: none;
                }

                .amount-positive {
                    color: #10b981;
                    font-weight: 600;
                }

                .amount-negative {
                    color: #ef4444;
                    font-weight: 600;
                }
            </style>
        @endpush

        <div class="pb-8 pt-2 ios-page">
            {{-- Header with Deposit Link --}}
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="ios-group-title !mb-0">Ваш Баланс</div>
                <a href="{{ route('shop.customers.account.credits.deposit') }}"
                    class="text-[13px] font-bold text-violet-600 bg-violet-50 border border-violet-100 px-4 py-2 rounded-full active:scale-95 transition-all shadow-sm">
                    + Пополнить
                </a>
            </div>

            {{-- Total Balance Card --}}
            <div
                class="ios-group mb-8 p-6 bg-gradient-to-br from-zinc-900 to-zinc-800 text-white rounded-[24px] shadow-xl border border-zinc-700 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>

                <div class="flex flex-col gap-2 relative z-10">
                    <div class="text-[12px] text-zinc-400 font-bold uppercase tracking-[0.1em] opacity-80">Общая
                        покупательная способность</div>
                    <div class="text-3xl font-bold font-mono text-white tracking-tight">
                        {{ core()->formatPrice(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
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
                    @endphp

                    @if($balances->count() > 0)
                        <div class="mt-4 pt-4 border-t border-white/10 flex flex-col gap-3">
                            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Крипто-активы</div>
                            @foreach($balances as $balance)
                                @php
                                    $m = $netLabels[$balance->currency_code] ?? ['label' => strtoupper($balance->currency_code), 'symbol' => '?', 'color' => '#888'];
                                    $rate = $exchangeRateService->getRate($balance->currency_code);
                                    $fiat = $balance->amount * $rate;
                                    $amount = rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.');
                                @endphp
                                <div class="flex justify-between items-center group">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-8 h-8 rounded-xl flex items-center justify-center text-[14px] text-white font-bold transition-transform group-hover:scale-110 shadow-sm"
                                            style="background: {{ $m['color'] }}">
                                            {{ $m['symbol'] }}
                                        </span>
                                        <span class="text-[14px] text-zinc-200 font-bold">{{ $m['label'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[15px] font-bold font-mono text-white leading-none">{{ $amount }}</div>
                                        @if($fiat > 0)
                                            <div class="text-[11px] text-zinc-400 font-medium mt-1">≈
                                                {{ core()->formatPrice($fiat) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="mt-4 p-4 rounded-xl bg-white/5 border border-white/5 text-[12px] text-zinc-400 leading-relaxed italic">
                            У вас пока нет активных балансов. Пополните счет криптовалютой, чтобы совершать покупки и
                            расчеты.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Transactions History --}}
            <div class="ios-group-title flex items-center justify-between px-1">
                <span>История транзакций</span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $transactions->total() }}
                    всего</span>
            </div>
            <div class="bg-white overflow-hidden rounded-[24px] shadow-sm border border-zinc-100">
                @if ($transactions->count() > 0)
                    <div class="flex flex-col divide-y divide-zinc-50">
                        @foreach ($transactions as $transaction)
                            <div class="credit-row hover:bg-zinc-50/50">
                                <div class="flex flex-col gap-1.5 min-w-0 pr-4">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $typeLabels = [
                                                'deposit' => 'Пополнение',
                                                'withdrawal' => 'Списание',
                                                'purchase' => 'Оплата',
                                                'refund' => 'Возврат',
                                                'transfer_debit' => 'Перевод от вас',
                                                'transfer_credit' => 'Перевод вам',
                                            ];
                                            $typeLabel = $typeLabels[$transaction->type] ?? $transaction->type;
                                            $statusColors = [
                                                'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                'failed' => 'bg-red-50 text-red-600 border-red-100',
                                            ];
                                            $statusClass = $statusColors[$transaction->status] ?? 'bg-zinc-50 text-zinc-500 border-zinc-100';
                                        @endphp
                                        <span class="text-[15px] font-bold text-zinc-900 truncate">{{ $typeLabel }}</span>
                                        <span
                                            class="text-[9px] px-1.5 py-0.5 rounded-md border {{ $statusClass }} uppercase tracking-wider font-bold shrink-0">
                                            {{ $transaction->status }}
                                        </span>
                                    </div>

                                    @if($transaction->notes)
                                        <div class="text-[12px] text-zinc-500 leading-tight">
                                            {{ $transaction->notes }}
                                        </div>
                                    @endif

                                    <div class="text-[11px] text-zinc-400 font-medium">
                                        {{ $transaction->created_at->format('d.m.Y — H:i') }}
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <div
                                        class="text-[16px] font-bold font-mono {{ (float) $transaction->amount > 0 ? 'amount-positive' : 'amount-negative' }}">
                                        {{ (float) $transaction->amount > 0 ? '+' : '' }}{{ core()->formatPrice($transaction->amount) }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400 font-mono mt-0.5 uppercase tracking-tighter">
                                        #{{ $transaction->uuid ? substr($transaction->uuid, 0, 8) : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-zinc-400 px-10 text-center">
                        <div class="w-20 h-20 bg-zinc-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <span class="icon-sales text-4xl text-zinc-200"></span>
                        </div>
                        <p class="text-[17px] font-bold text-zinc-700">Транзакций не найдено</p>
                        <p class="text-[13px] mt-2 text-zinc-400 leading-relaxed">Все операции по вашему балансу будут
                            бережно храниться в этом разделе для вашего удобства</p>
                    </div>
                @endif
            </div>

            <div class="mt-8">
                {{ $transactions->links() }}
            </div>
        </div>
</x-shop::layouts.account>