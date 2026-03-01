<x-shop::layouts.account>
    <x-slot:title>
        Meanly Pay
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
            {{-- Header (Titles removed as per request) --}}
            <div class="mb-4"></div>

            {{-- Total Balance Card (Premium Light Theme) --}}
            <div class="ios-group mb-8 p-6 bg-white rounded-[24px] shadow-md relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-400/5 rounded-full blur-3xl"></div>

                <div class="flex flex-col gap-2 relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="text-[12px] text-zinc-500 font-bold uppercase tracking-[0.1em] opacity-80">Общая
                            покупательная способность</div>
                        <div
                            class="text-[12px] font-mono text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full border border-violet-100 font-bold">
                            @ {{ auth()->guard('customer')->user()->username }}
                        </div>
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

                    <div class="text-4xl font-bold font-mono text-zinc-900 tracking-tight mt-1">
                        {{ core()->formatPrice($user->getTotalFiatBalance()) }}
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
                        <div class="mt-4 text-[13px] text-zinc-400 italic">
                            Нет активных балансов
                        </div>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('shop.customers.account.credits.deposit') }}"
                            class="inline-flex items-center justify-center text-[14px] font-bold text-white bg-zinc-900 px-6 py-3 rounded-2xl active:scale-95 transition-all shadow-lg shadow-zinc-100">
                            + Пополнить
                        </a>
                    </div>

                </div>
            </div>

            {{-- Transactions History Navigation --}}
            <div class="mt-8">
                <a href="{{ route('shop.customers.account.credits.transactions') }}"
                    class="ios-group flex items-center justify-between p-5 bg-white rounded-[24px] shadow-sm hover:shadow-md active:scale-[0.98] transition-all group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400 group-hover:bg-violet-50 group-hover:text-violet-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-[15px] font-bold text-zinc-900 leading-tight">История транзакций</div>
                            <div class="text-[11px] text-zinc-400 font-medium">Посмотреть все движения по счету</div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-zinc-300 group-hover:text-violet-400 transition-all transform group-hover:translate-x-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
</x-shop::layouts.account>