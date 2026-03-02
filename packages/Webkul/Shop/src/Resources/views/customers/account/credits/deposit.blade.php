<x-shop::layouts.account :back-link="route('shop.customers.account.credits.index')" back-text="Назад к балансу">
    {{-- Page Title --}}
    <x-slot:title>
        Пополнение баланса
        </x-slot>

        <div class="max-w-lg mx-auto px-4 py-6">

            {{-- Page title --}}

            @php
                $allAssets = [
                    'ton' => [
                        'name' => 'TON (Native)',
                        'symbol' => 'TON',
                        'icon' => '💎',
                        'network' => 'TON Network',
                        'color' => '#0098EA',
                        'color2' => '#33BFFF',
                        'address' => config('crypto.verification_addresses.ton')
                    ],
                    'usdt_ton' => [
                        'name' => 'USDT (TON)',
                        'symbol' => 'USDT',
                        'icon' => '₮',
                        'network' => 'TON Network',
                        'color' => '#0098EA',
                        'color2' => '#33BFFF',
                        'address' => config('crypto.verification_addresses.usdt_ton')
                    ],
                    'bitcoin' => [
                        'name' => 'Bitcoin',
                        'symbol' => 'BTC',
                        'icon' => '₿',
                        'network' => 'Bitcoin',
                        'color' => '#F7931A',
                        'color2' => '#FDB953',
                        'address' => config('crypto.verification_addresses.bitcoin')
                    ],
                    'ethereum' => [
                        'name' => 'Ethereum / USDT ERC20',
                        'symbol' => 'ETH',
                        'icon' => 'Ξ',
                        'network' => 'Ethereum',
                        'color' => '#627EEA',
                        'color2' => '#8FA4EF',
                        'address' => config('crypto.verification_addresses.ethereum')
                    ],
                    'dash' => [
                        'name' => 'Dash',
                        'symbol' => 'DASH',
                        'icon' => 'D',
                        'network' => 'Dash',
                        'color' => '#1c75bc',
                        'color2' => '#4DA3E0',
                        'address' => config('crypto.verification_addresses.dash')
                    ],
                ];

                // Filter to only verified assets
                $verifiedNetworkKeys = $verifiedAddresses->pluck('network')->unique()->toArray();
                $verifiedAssets = array_filter($allAssets, function ($key) use ($verifiedNetworkKeys) {
                    return in_array($key, $verifiedNetworkKeys);
                }, ARRAY_FILTER_USE_KEY);
            @endphp

            @if($verifiedAddresses->isEmpty())
                {{-- No verified addresses --}}
                <div
                    class="bg-white rounded-2xl border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                    <div>
                        <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                        <p class="text-[13px] text-zinc-400 mt-1">Для пополнения необходимо сначала добавить и
                            верифицировать свой кошелёк.</p>
                    </div>
                    <a href="{{ route('shop.customers.account.crypto.index') }}"
                        style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                        class="text-white font-bold px-6 py-3 rounded-2xl text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                        + Добавить кошелёк
                    </a>
                </div>
            @else
                {{-- Wallet Selection (Flat List) --}}
                <div id="step-selection">
                    <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Откуда будете пополнять?
                    </p>
                    <div class="flex flex-col gap-3">
                        @foreach($verifiedAddresses as $address)
                            @php
                                $nm = [
                                    'bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623'],
                                    'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF'],
                                    'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF'],
                                    'usdt_ton' => ['USDT (TON)', 'USDT', '₮', '#26A17B', '#4DBFA0'],
                                    'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0']
                                ];
                                $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc'];

                                $username = auth()->guard('customer')->user()->username;
                                $netMap = [
                                    'ton' => ['chain' => 'ton'],
                                    'usdt_ton' => ['chain' => 'ton', 'token' => 'usdt'],
                                    'bitcoin' => ['chain' => 'btc'],
                                    'ethereum' => ['chain' => 'erc20', 'token' => 'usdt'],
                                    'dash' => ['chain' => 'dash']
                                ];
                                $nmData = $netMap[$address->network] ?? ['chain' => $address->network];

                                $parts = ["@{$username}"];
                                $parts[] = $nmData['chain'] ?? $address->network;
                                if (isset($nmData['token']))
                                    $parts[] = $nmData['token'];
                                if ($address->alias)
                                    $parts[] = $address->alias;

                                $fullAlias = implode('.', $parts);
                            @endphp

                            <button onclick="selectAsset('{{ $address->network }}', '{{ $address->id }}')"
                                class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden flex items-center gap-4 px-4 py-4 hover:border-violet-200 transition-all active:scale-[0.98] text-left">
                                <span
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[18px] font-bold shrink-0"
                                    style="background:linear-gradient(135deg,{{ $m[3] }},{{ $m[4] }})">{{ $m[2] }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[14px] font-bold text-zinc-900 truncate">{{ $fullAlias }}</div>
                                    <div class="flex items-center gap-1.5 leading-none mt-0.5">
                                        <span class="text-[12px] font-bold font-mono text-zinc-500">
                                            {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                        </span>
                                        <span
                                            class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $m[1] }}</span>
                                    </div>
                                </div>
                                <span class="text-zinc-300">→</span>
                            </button>
                        @endforeach

                        {{-- Manage Wallets Button --}}
                        <a href="{{ route('shop.customers.account.crypto.index') }}"
                            class="bg-zinc-50/50 rounded-2xl border border-dashed border-zinc-200 flex items-center gap-4 px-4 py-4 hover:border-violet-300 hover:bg-zinc-50 transition-all active:scale-[0.98] mt-2">
                            <span
                                class="w-10 h-10 rounded-xl flex items-center justify-center bg-zinc-200/50 text-zinc-500 text-[20px] font-light shrink-0">+</span>
                            <div class="text-left flex-1">
                                <div class="text-[15px] font-bold text-zinc-900">Управление кошельками</div>
                                <div class="text-[12px] text-zinc-400">Добавить или верифицировать новый</div>
                            </div>
                            <span class="text-zinc-300 text-[18px]">→</span>
                        </a>
                    </div>
                </div>

                {{-- Step 2: Final Deposit Details --}}
                <div id="step-details" class="hidden">
                    @foreach($verifiedAddresses as $address)
                        <div id="details-wallet-{{ $address->id }}" class="hidden wallet-details-view">
                            @php
                                $asset = $allAssets[$address->network] ?? null;
                                if (!$asset)
                                    continue;

                                $username = auth()->guard('customer')->user()->username;
                                $netMap = [
                                    'ton' => ['chain' => 'ton'],
                                    'usdt_ton' => ['chain' => 'ton', 'token' => 'usdt'],
                                    'bitcoin' => ['chain' => 'btc'],
                                    'ethereum' => ['chain' => 'erc20', 'token' => 'usdt'],
                                    'dash' => ['chain' => 'dash']
                                ];
                                $nmData = $netMap[$address->network] ?? ['chain' => $address->network];

                                $parts = ["@{$username}"];
                                $parts[] = $nmData['chain'] ?? $address->network;
                                if (isset($nmData['token']))
                                    $parts[] = $nmData['token'];
                                if ($address->alias)
                                    $parts[] = $address->alias;

                                $fullAlias = implode('.', $parts);
                            @endphp

                            {{-- Header --}}
                            <div class="flex items-center gap-3 mb-6">
                                <span
                                    class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-[22px] font-bold shrink-0"
                                    style="background:linear-gradient(135deg,{{ $asset['color'] }},{{ $asset['color2'] }})">{{ $asset['icon'] }}</span>
                                <div class="flex-1">
                                    <h2 class="text-[18px] font-bold text-zinc-900">{{ $asset['name'] }}</h2>
                                    <div class="flex gap-3 mt-1">
                                        <button onclick="backToSelection()"
                                            class="text-[13px] text-violet-500 font-medium hover:underline">
                                            ← Выбрать другой кошелек
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Source Section (Visual Flow) --}}
                            <div class="mb-4">
                                <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Отправляйте
                                    средства только с этого кошелька:</p>
                                <div class="bg-zinc-50 rounded-2xl p-4 border border-zinc-100 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-zinc-400 shadow-sm border border-zinc-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path d="M21 18H3V6h18v12zM5 8v8h14V8H5zm2 2h10v2H7v-2zm0 4h7v2H7v-2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[14px] font-bold text-zinc-900 truncate">{{ $fullAlias }}</div>
                                        <div class="text-[11px] font-mono text-zinc-400 truncate">{{ $address->address }}</div>
                                    </div>
                                    <div class="text-emerald-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Visual Connector --}}
                            <div class="flex flex-col items-center my-4 opacity-30">
                                <div class="w-px h-6 border-l border-dashed border-zinc-400"></div>
                                <div class="text-zinc-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7-7-7m14-8l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Cold Wallet Address (Destination) --}}
                            <div class="bg-zinc-900 rounded-2xl p-5 shadow-xl mb-6 ring-1 ring-white/10">
                                <div class="text-[11px] text-zinc-500 uppercase font-bold tracking-widest mb-2">Адрес для
                                    пополнения ({{ $asset['symbol'] }})</div>
                                <div
                                    class="text-[15px] font-mono text-white break-words mb-4 select-all text-center leading-relaxed font-medium">
                                    {{ $asset['address'] }}
                                </div>
                                <button onclick="copyAddr('{{ $asset['address'] }}', this)"
                                    class="w-full bg-white/10 hover:bg-white/20 text-white text-[13px] font-bold py-3.5 rounded-xl transition-all active:scale-[0.98] border border-white/5">
                                    Копировать адрес
                                </button>
                            </div>

                            {{-- Instruction (Warning at bottom) --}}
                            <div class="bg-violet-50 border border-violet-100 rounded-2xl px-4 py-4 mt-8">
                                <div class="flex gap-3">
                                    <div class="shrink-0 text-violet-500 text-[20px]">⚠️</div>
                                    <div>
                                        <p class="text-[13px] text-violet-800 leading-relaxed font-bold mb-1">
                                            Средства зачисляются только с верифицированного кошелька.
                                        </p>
                                        <p class="text-[12px] text-violet-700 leading-relaxed">
                                            Средства будут зачислены на ваш баланс автоматически в течение нескольких минут
                                            после подтверждения сетью.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        @push('scripts')
            <script>
                function selectAsset(assetKey, walletId) {
                    document.getElementById('step-selection').classList.add('hidden');
                    document.getElementById('step-details').classList.remove('hidden');
                    // Hide all wallet details
                    document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                    // Show selected wallet details
                    const target = document.getElementById('details-wallet-' + walletId);
                    if (target) target.classList.remove('hidden');
                }

                function backToSelection() {
                    document.getElementById('step-details').classList.add('hidden');
                    document.getElementById('step-selection').classList.remove('hidden');
                }

                function copyAddr(text, btn) {
                    navigator.clipboard.writeText(text).then(() => {
                        const orig = btn.innerHTML;
                        btn.innerHTML = '✓ Скопировано';
                        setTimeout(() => btn.innerHTML = orig, 2000);
                    });
                }
            </script>
        @endpush
</x-shop::layouts.account>