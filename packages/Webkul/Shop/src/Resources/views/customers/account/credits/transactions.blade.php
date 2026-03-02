<x-shop::layouts.account :back-link="url()->previous(route('shop.customers.account.credits.index'))">
    <x-slot:title>
        История транзакций
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
            {{-- Transactions List --}}
            <div class="bg-white overflow-hidden rounded-[24px] shadow-md">
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-zinc-200" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-[17px] font-bold text-zinc-700">Транзакций не найдено</p>
                        <p class="text-[13px] mt-2 text-zinc-400 leading-relaxed">Все операции по вашему балансу будут
                            бережно храниться в этом разделе для вашего удобства</p>
                    </div>
                @endif
            </div>

            <div class="mt-8 px-2">
                {{ $transactions->links() }}
            </div>
        </div>
</x-shop::layouts.account>