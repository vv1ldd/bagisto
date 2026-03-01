@extends('shop::layouts.account')

@section('page_title')
    Пополнение баланса
@endsection

@section('account-content')
    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- Back --}}
        <a href="{{ route('shop.customers.account.credits.index') }}"
            class="inline-flex items-center gap-2 text-[13px] text-zinc-400 font-semibold mb-5 hover:text-zinc-700 transition-colors">
            ← Назад к балансу
        </a>

        {{-- Page title --}}
        <div class="mb-6">
            <h1 class="text-[22px] font-bold text-zinc-900">Пополнение Credits</h1>
            <p class="text-[13px] text-zinc-400 mt-1">Отправьте крипто с верифицированного кошелька</p>
        </div>

        @if($verifiedAddresses->isEmpty())
            {{-- No verified addresses --}}
            <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                <div>
                    <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                    <p class="text-[13px] text-zinc-400 mt-1">Добавьте и верифицируйте адрес,<br>чтобы принимать депозиты</p>
                </div>
                <a href="{{ route('shop.customers.account.credits.index') }}#wallet-add-section"
                    style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                    class="text-white font-bold px-6 py-3 rounded-2xl text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                    + Добавить кошелёк
                </a>
            </div>
        @else
            {{-- Instruction --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3 mb-5">
                <p class="text-[13px] text-amber-700 leading-relaxed">
                    ⚠️ Отправьте <strong>точную сумму верификации</strong> с вашего верифицированного кошелька на адрес ниже.
                    Точность суммы — фактор верификации. Средства зачислятся автоматически после подтверждения в сети.
                </p>
            </div>

            {{-- Verified addresses --}}
            @php
                $meta = [
                    'bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#FDB953'],
                    'ethereum' => ['Ethereum / USDT ERC20', 'ETH', 'Ξ', '#627EEA', '#8FA4EF'],
                    'ton' => ['TON', 'TON', '💎', '#0098EA', '#33BFFF'],
                    'usdt_ton' => ['USDT (сеть TON)', 'USDT', '₮', '#26A17B', '#4DBFA0'],
                    'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0'],
                ];
            @endphp

            <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Верифицированные адреса</p>

            <div class="flex flex-col gap-3">
                @foreach($verifiedAddresses as $va)
                    @php $vm = $meta[$va->network] ?? [strtoupper($va->network), strtoupper($va->network), '?', '#888', '#aaa']; @endphp
                    <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden">
                        {{-- Network header --}}
                        <div class="flex items-center gap-3 px-4 py-3"
                            style="background:linear-gradient(135deg,{{ $vm[3] }}18,{{ $vm[4] }}12)">
                            <span
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-[16px] font-bold shrink-0"
                                style="background:linear-gradient(135deg,{{ $vm[3] }},{{ $vm[4] }})">{{ $vm[2] }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-bold text-zinc-900">{{ $vm[0] }}</div>
                                <div class="text-[11px] font-mono text-zinc-400 truncate">{{ $va->address }}</div>
                            </div>
                            <button onclick="copyAddr('{{ $va->address }}', this)"
                                class="shrink-0 text-[11px] font-bold text-violet-600 bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl active:scale-95 transition-all">
                                Копировать
                            </button>
                        </div>
                        {{-- Balance --}}
                        <div class="flex items-center justify-between px-4 py-2 bg-zinc-50/60 border-t border-zinc-100">
                            <span class="text-[11px] text-zinc-400 uppercase tracking-wide">Баланс</span>
                            <span class="text-[13px] font-bold font-mono text-zinc-800">
                                {{ rtrim(rtrim(number_format($va->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                {{ $vm[1] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Add more --}}
            <a href="{{ route('shop.customers.account.credits.index') }}#wallet-add-section"
                class="block text-center text-[13px] text-violet-500 font-semibold mt-4 py-2 active:opacity-60 transition-all">
                + Добавить ещё кошелёк
            </a>
        @endif
    </div>

    @push('scripts')
        <script>
            function copyAddr(text, btn) {
                navigator.clipboard.writeText(text).then(() => {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<span class="text-emerald-500">✓ Скопировано</span>';
                    setTimeout(() => btn.innerHTML = orig, 2000);
                });
            }
        </script>
    @endpush
@endsection