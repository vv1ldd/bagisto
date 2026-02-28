<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        История транзакций
        </x-slot>

        @section('breadcrumbs')
        <x-shop::breadcrumbs name="transactions" />
        @endSection

        @push('styles')
            <style>
                .transaction-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 20px;
                    border-bottom: 1px solid #f4f4f5;
                    transition: background-color 0.15s;
                }

                .transaction-row:last-child {
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

        <div class="pb-8 pt-2">
            <div class="glass-card !bg-white/70 overflow-hidden rounded-2xl shadow-sm border border-zinc-100">
                @if ($transactions->count() > 0)
                    <div class="flex flex-col">
                        @foreach ($transactions as $transaction)
                            <div class="transaction-row">
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
                                        class="text-[16px] {{ $transaction->amount > 0 ? 'amount-positive' : 'amount-negative' }}">
                                        {{ $transaction->amount > 0 ? '+' : '' }}{{ core()->formatPrice($transaction->amount) }}
                                    </div>
                                    <div class="text-[11px] text-zinc-400 font-mono mt-1">
                                        ID: {{ substr($transaction->uuid, 0, 8) }}
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