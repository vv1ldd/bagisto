<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        История начислений
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
                    /* Green */
                    font-weight: 600;
                }

                .amount-negative {
                    color: #ef4444;
                    /* Red */
                    font-weight: 600;
                }
            </style>
        @endpush

        <div class="pb-8 pt-2 ios-page">

            {{-- Balances Overview --}}
            <div class="ios-group-title">Ваш Баланс</div>
            <div
                class="ios-group mb-6 p-4 bg-gradient-to-br from-zinc-900 to-zinc-800 text-white rounded-2xl shadow-md border border-zinc-700">
                <div class="flex flex-col gap-2 relative z-10">
                    <div class="text-[13px] text-zinc-400 font-medium uppercase tracking-wider">Общая покупательная
                        способность</div>
                    <div class="text-3xl font-bold font-mono tracking-tight drop-shadow-sm">
                        {{ core()->formatPrice(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                    </div>

                    @if(auth()->guard('customer')->user()->balances->count() > 0)
                        <div class="mt-3 pt-3 border-t border-zinc-700/50 flex flex-col gap-2">
                            <div class="text-[11px] text-zinc-500 uppercase tracking-wider">Крипто-активы:</div>
                            @foreach(auth()->guard('customer')->user()->balances as $balance)
                                <div class="flex justify-between items-center text-[14px]">
                                    <span class="text-zinc-300 font-medium uppercase">{{ $balance->currency_code }}</span>
                                    <span
                                        class="font-mono text-zinc-100">{{ rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-2 text-[12px] text-zinc-400">Пополните баланс криптовалютой для совершения покупок.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recharge Section --}}
            <div class="ios-group-title">Пополнение баланса</div>
            <div class="ios-group mb-6 p-4">
                <p class="text-[14px] text-zinc-600 mb-4 leading-relaxed">
                    Чтобы пополнить Credits, отправьте любую сумму с вашего **верифицированного** кошелька на адрес
                    ниже.
                    Средства будут зачислены автоматически после подтверждения в сети.
                </p>

                <div class="flex flex-col gap-3">
                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                        <div class="text-[11px] text-zinc-400 uppercase font-bold mb-1">Bitcoin (BTC)</div>
                        <div class="text-[13px] font-mono text-zinc-800 break-all select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText)">
                            {{ config('crypto.verification_addresses.bitcoin') }}
                        </div>
                    </div>

                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                        <div class="text-[11px] text-zinc-400 uppercase font-bold mb-1">Ethereum (ETH / USDT ERC20)
                        </div>
                        <div class="text-[13px] font-mono text-zinc-800 break-all select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText)">
                            {{ config('crypto.verification_addresses.ethereum') }}
                        </div>
                    </div>

                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                        <div class="text-[11px] text-zinc-400 uppercase font-bold mb-1">TON (The Open Network)
                        </div>
                        <div class="text-[13px] font-mono text-zinc-800 break-all select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText)">
                            {{ config('crypto.verification_addresses.ton') }}
                        </div>
                    </div>

                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                        <div class="text-[11px] text-zinc-400 uppercase font-bold mb-1">USDT (сеть TON)
                        </div>
                        <div class="text-[13px] font-mono text-zinc-800 break-all select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText)">
                            {{ config('crypto.verification_addresses.usdt_ton') }}
                        </div>
                    </div>

                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                        <div class="text-[11px] text-zinc-400 uppercase font-bold mb-1">Dash (DASH)
                        </div>
                        <div class="text-[13px] font-mono text-zinc-800 break-all select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText)">
                            {{ config('crypto.verification_addresses.dash') }}
                        </div>
                    </div>
                </div>

                <p class="text-[12px] text-zinc-400 mt-4 italic">
                    * Убедитесь, что ваш адрес верифицирован в <a
                        href="{{ route('shop.customers.account.profile.edit') }}"
                        class="text-zinc-900 underline">профиле</a>.
                </p>
            </div>



            <div class="ios-group-title">История начислений</div>
            <div class="glass-card !bg-white/70 overflow-hidden rounded-2xl shadow-sm border border-zinc-100">
                @if ($transactions->count() > 0)
                    <div class="flex flex-col">
                        @foreach ($transactions as $transaction)
                            <div class="credit-row">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[15px] font-bold text-zinc-900 capitalize">
                                            @if($transaction->type === 'deposit')
                                                Пополнение
                                            @elseif($transaction->type === 'withdrawal')
                                                Списание
                                            @elseif($transaction->type === 'purchase')
                                                Оплата
                                            @elseif($transaction->type === 'refund')
                                                Возврат
                                            @elseif($transaction->type === 'transfer_debit')
                                                Перевод от вас
                                            @elseif($transaction->type === 'transfer_credit')
                                                Перевод вам
                                            @else
                                                {{ $transaction->type }}
                                            @endif
                                        </span>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-500 uppercase tracking-wider font-medium">
                                            {{ $transaction->status }}
                                        </span>
                                    </div>

                                    @if($transaction->notes)
                                        <div class="text-[13px] text-zinc-500">
                                            {{ $transaction->notes }}
                                        </div>
                                    @endif

                                    <div class="text-[12px] text-zinc-400">
                                        {{ $transaction->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div
                                        class="text-[16px] {{ (float) $transaction->amount > 0 ? 'amount-positive' : 'amount-negative' }}">
                                        {{ (float) $transaction->amount > 0 ? '+' : '' }}{{ core()->formatPrice($transaction->amount) }}
                                    </div>
                                    <div class="text-[11px] text-zinc-400 font-mono mt-1">
                                        ID: {{ $transaction->uuid ? substr($transaction->uuid, 0, 8) : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                        <div class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mb-4">
                            <span class="icon-sales text-3xl text-zinc-300"></span>
                        </div>
                        <p class="text-[15px] font-medium text-zinc-500">У вас пока нет транзакций</p>
                        <p class="text-[13px] mt-1 text-zinc-400">Все операции по вашему балансу будут отображаться здесь
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        </div>
</x-shop::layouts.account>