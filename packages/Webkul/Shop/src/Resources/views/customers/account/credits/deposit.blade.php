<x-shop::layouts.account :back-link="route('shop.customers.account.credits.index')" back-text="Назад к балансу">
    {{-- Page Title --}}
    <x-slot:title>
        Пополнение баланса
        </x-slot>

        <div class="max-w-lg mx-auto px-4 py-6">

            {{-- Page title --}}
            <div class="mb-6">
                <h1 class="text-[22px] font-bold text-zinc-900">Пополнение Credits</h1>
                <p class="text-[13px] text-zinc-400 mt-1">Выберите сеть и валюту для пополнения</p>
            </div>

            @php
                $groups = [
                    'ton' => [
                        'name' => 'TON Network',
                        'icon' => '💎',
                        'color' => '#0098EA',
                        'color2' => '#33BFFF',
                        'assets' => [
                            'ton' => [
                                'name' => 'TON (Native)',
                                'symbol' => 'TON',
                                'icon' => '💎',
                                'address' => config('crypto.verification_addresses.ton')
                            ],
                            'usdt_ton' => [
                                'name' => 'USDT (TON)',
                                'symbol' => 'USDT',
                                'icon' => '₮',
                                'address' => config('crypto.verification_addresses.usdt_ton')
                            ],
                        ]
                    ],
                    'bitcoin' => [
                        'name' => 'Bitcoin',
                        'icon' => '₿',
                        'color' => '#F7931A',
                        'color2' => '#FDB953',
                        'assets' => [
                            'bitcoin' => [
                                'name' => 'Bitcoin',
                                'symbol' => 'BTC',
                                'icon' => '₿',
                                'address' => config('crypto.verification_addresses.bitcoin')
                            ],
                        ]
                    ],
                    'ethereum' => [
                        'name' => 'Ethereum',
                        'icon' => 'Ξ',
                        'color' => '#627EEA',
                        'color2' => '#8FA4EF',
                        'assets' => [
                            'ethereum' => [
                                'name' => 'Ethereum / USDT ERC20',
                                'symbol' => 'ETH',
                                'icon' => 'Ξ',
                                'address' => config('crypto.verification_addresses.ethereum')
                            ],
                        ]
                    ],
                    'dash' => [
                        'name' => 'Dash',
                        'icon' => 'D',
                        'color' => '#1c75bc',
                        'color2' => '#4DA3E0',
                        'assets' => [
                            'dash' => [
                                'name' => 'Dash',
                                'symbol' => 'DASH',
                                'icon' => 'D',
                                'address' => config('crypto.verification_addresses.dash')
                            ],
                        ]
                    ],
                ];

                // Filter groups to only those where user has at least one verified address
                $verifiedNetworkKeys = $verifiedAddresses->pluck('network')->unique()->toArray();

                foreach ($groups as $groupKey => &$group) {
                    $group['assets'] = array_filter($group['assets'], function ($assetKey) use ($verifiedNetworkKeys) {
                        return in_array($assetKey, $verifiedNetworkKeys);
                    }, ARRAY_FILTER_USE_KEY);
                }

                $groups = array_filter($groups, function ($group) {
                    return !empty($group['assets']);
                });
            @endphp

            @if(empty($groups))
                {{-- No verified addresses --}}
                <div
                    class="bg-white rounded-2xl border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                    <div>
                        <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                        <p class="text-[13px] text-zinc-400 mt-1">Для пополнения необходимо сначала добавить и
                            верифицировать свой кошелёк.</p>
                    </div>
                    <a href="{{ route('shop.customers.account.crypto.index') }}#wallet-add-section"
                        style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                        class="text-white font-bold px-6 py-3 rounded-2xl text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                        + Добавить кошелёк
                    </a>
                </div>
            @else
                {{-- Step 1: Network Selection --}}
                <div id="step-networks">
                    <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Шаг 1: Выберите сеть</p>
                    <div class="flex flex-col gap-3">
                        @foreach($groups as $key => $group)
                            @php $assetsCount = count($group['assets']); @endphp
                            <button onclick="selectGroup('{{ $key }}', {{ $assetsCount === 1 ? 'true' : 'false' }})"
                                class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden flex items-center gap-4 px-4 py-4 hover:border-violet-200 transition-all active:scale-[0.98]">
                                <span
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[18px] font-bold shrink-0"
                                    style="background:linear-gradient(135deg,{{ $group['color'] }},{{ $group['color2'] }})">{{ $group['icon'] }}</span>
                                <div class="text-left flex-1">
                                    <div class="text-[15px] font-bold text-zinc-900">{{ $group['name'] }}</div>
                                    <div class="text-[12px] text-zinc-400">
                                        {{ implode(', ', array_column($group['assets'], 'symbol')) }}
                                    </div>
                                </div>
                                <span class="text-zinc-300">→</span>
                            </button>
                        @endforeach

                        {{-- Manage Wallets Button --}}
                        <a href="{{ route('shop.customers.account.crypto.index') }}#wallet-add-section"
                            class="bg-zinc-50/50 rounded-2xl border border-dashed border-zinc-200 flex items-center gap-4 px-4 py-4 hover:border-violet-300 hover:bg-zinc-50 transition-all active:scale-[0.98] mt-2">
                            <span
                                class="w-10 h-10 rounded-xl flex items-center justify-center bg-zinc-200/50 text-zinc-500 text-[20px] font-light shrink-0">+</span>
                            <div class="text-left flex-1">
                                <div class="text-[15px] font-bold text-zinc-900">Добавить кошелёк</div>
                                <div class="text-[12px] text-zinc-400">Управление и верификация адресов</div>
                            </div>
                            <span class="text-zinc-300 text-[18px]">→</span>
                        </a>
                    </div>
                </div>

                {{-- Step 2: Asset Selection (if multiple) --}}
                <div id="step-assets" class="hidden">
                    <div class="mb-4">
                        <button onclick="backToNetworks()"
                            class="text-[13px] text-zinc-400 font-medium hover:text-zinc-600 transition-colors">← Назад к
                            выбору сети</button>
                    </div>
                    <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Шаг 2: Выберите валюту</p>

                    @foreach($groups as $gKey => $group)
                        <div id="assets-group-{{ $gKey }}" class="hidden group-assets-list flex flex-col gap-3">
                            @foreach($group['assets'] as $aKey => $asset)
                                <button onclick="selectAsset('{{ $gKey }}', '{{ $aKey }}')"
                                    class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden flex items-center gap-4 px-4 py-4 hover:border-violet-200 transition-all active:scale-[0.98]">
                                    <span
                                        class="w-10 h-10 bg-zinc-100 rounded-xl flex items-center justify-center text-zinc-600 text-[18px] font-bold shrink-0">{{ $asset['icon'] }}</span>
                                    <div class="text-left flex-1">
                                        <div class="text-[15px] font-bold text-zinc-900">{{ $asset['name'] }}</div>
                                        <div class="text-[12px] text-zinc-400">{{ $asset['symbol'] }}</div>
                                    </div>
                                    <span class="text-zinc-300">→</span>
                                </button>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- Manage Wallets Button --}}
                    <a href="{{ route('shop.customers.account.credits.index') }}#wallet-add-section"
                        class="bg-zinc-50/50 rounded-2xl border border-dashed border-zinc-200 flex items-center gap-4 px-4 py-4 hover:border-violet-300 hover:bg-zinc-50 transition-all active:scale-[0.98] mt-2">
                        <span
                            class="w-10 h-10 rounded-xl flex items-center justify-center bg-zinc-200/50 text-zinc-500 text-[20px] font-light shrink-0">+</span>
                        <div class="text-left flex-1">
                            <div class="text-[15px] font-bold text-zinc-900">Добавить кошелёк</div>
                            <div class="text-[12px] text-zinc-400">Управление и верификация адресов</div>
                        </div>
                        <span class="text-zinc-300 text-[18px]">→</span>
                    </a>
                </div>

                {{-- Step 3: Final Deposit Details --}}
                <div id="step-details" class="hidden">
                    @foreach($groups as $gKey => $group)
                        @foreach($group['assets'] as $aKey => $asset)
                            <div id="details-asset-{{ $aKey }}" class="hidden asset-details-view">
                                {{-- Header --}}
                                <div class="flex items-center gap-3 mb-6">
                                    <span
                                        class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-[22px] font-bold shrink-0"
                                        style="background:linear-gradient(135deg,{{ $group['color'] }},{{ $group['color2'] }})">{{ $asset['icon'] }}</span>
                                    <div class="flex-1">
                                        <h2 class="text-[18px] font-bold text-zinc-900">{{ $asset['name'] }}</h2>
                                        <div class="flex gap-3 mt-1">
                                            <button onclick="backToSelection('{{ $gKey }}')"
                                                class="text-[13px] text-violet-500 font-medium hover:underline">
                                                ← Сменить валюту
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Instruction --}}
                                <div class="bg-amber-50 border border-amber-100 rounded-2xl px-4 py-4 mb-6">
                                    <p class="text-[13px] text-amber-800 leading-relaxed font-medium mb-2">
                                        ⚠️ ВАЖНО: Отправляйте средства ТОЛЬКО с ваших верифицированных кошельков.
                                    </p>
                                    <p class="text-[12px] text-amber-700 leading-relaxed">
                                        Для зачисления необходимо отправить <strong>точную сумму верификации</strong> (с дробным
                                        хвостом).
                                        Это является фактором верификации платежа.
                                    </p>
                                </div>

                                {{-- Cold Wallet Address --}}
                                <div class="bg-zinc-900 rounded-2xl p-5 shadow-inner mb-6">
                                    <div class="text-[11px] text-zinc-500 uppercase font-bold tracking-widest mb-2">Адрес для
                                        пополнения ({{ $asset['symbol'] }})</div>
                                    <div
                                        class="text-[15px] font-mono text-white break-words mb-4 select-all text-center leading-relaxed">
                                        {{ $asset['address'] }}
                                    </div>
                                    <button onclick="copyAddr('{{ $asset['address'] }}', this)"
                                        class="w-full bg-white/10 hover:bg-white/20 text-white text-[13px] font-bold py-3 rounded-xl transition-all active:scale-[0.98]">
                                        Копировать адрес
                                    </button>
                                </div>

                                {{-- User's Wallets Reminder --}}
                                @php
                                    $userWallets = $verifiedAddresses->where('network', $aKey);
                                @endphp

                                @if($userWallets->isNotEmpty())
                                    <div class="mt-8">
                                        <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Отправляйте
                                            средства только отсюда:</p>
                                        <div class="flex flex-col gap-2">
                                            @foreach($userWallets as $uw)
                                                <div
                                                    class="bg-zinc-50 rounded-xl px-3 py-2 border border-zinc-100 text-[12px] font-mono text-zinc-500 truncate">
                                                    {{ $uw->address }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif

        </div>

        @push('scripts')
            <script>
                function selectGroup(key, isSingle) {
                    document.getElementById('step-networks').classList.add('hidden');

                    if (isSingle) {
                        // Find the asset key for this group
                        const groupAssets = {!! json_encode(array_map(fn($g) => array_keys($g['assets']), $groups)) !!};
                        const assetKey = groupAssets[key][0];
                        showDetails(assetKey);
                    } else {
                        document.getElementById('step-assets').classList.remove('hidden');
                        // Hide all currency lists
                        document.querySelectorAll('.group-assets-list').forEach(el => el.classList.add('hidden'));
                        // Show only the selected group's currencies
                        document.getElementById('assets-group-' + key).classList.remove('hidden');
                    }
                }

                function selectAsset(groupKey, assetKey) {
                    document.getElementById('step-assets').classList.add('hidden');
                    showDetails(assetKey);
                }

                function showDetails(assetKey) {
                    document.getElementById('step-details').classList.remove('hidden');
                    // Hide all asset details
                    document.querySelectorAll('.asset-details-view').forEach(el => el.classList.add('hidden'));
                    // Show selected asset details
                    document.getElementById('details-asset-' + assetKey).classList.remove('hidden');
                }

                function backToNetworks() {
                    document.getElementById('step-assets').classList.add('hidden');
                    document.getElementById('step-networks').classList.remove('hidden');
                }

                function backToSelection(groupKey) {
                    document.getElementById('step-details').classList.add('hidden');

                    // If the group only had ONE asset, go back to networks. Otherwise, back to asset selection.
                    const groupElementsCount = document.querySelectorAll('#assets-group-' + groupKey + ' button').length;
                    if (groupElementsCount === 1) {
                        document.getElementById('step-networks').classList.remove('hidden');
                    } else {
                        document.getElementById('step-assets').classList.remove('hidden');
                        document.getElementById('assets-group-' + groupKey).classList.remove('hidden');
                    }
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