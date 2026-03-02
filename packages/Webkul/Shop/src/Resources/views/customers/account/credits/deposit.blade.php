<x-shop::layouts.account :has-header="false">
    {{-- Page Title Slot (Meta) --}}
    <x-slot:title>
        Пополнение баланса
        </x-slot>

        <div class="max-w-lg mx-auto px-4 py-12">
            {{-- Top Navigation & Title (Truly Outside the Tile) --}}
            <div class="flex items-center gap-4 mb-8">
                <a id="page-back-link" href="{{ route('shop.customers.account.credits.index') }}"
                    class="w-10 h-10 rounded-full bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 id="page-title" class="text-[22px] font-bold text-zinc-900 leading-none">Пополнение баланса</h1>
            </div>
            <div class="bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden p-6 md:p-8">

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
                        class="bg-white rounded-[32px] border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
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
                    <div id="step-selection"
                        class="bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden p-6 md:p-8">
                        <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Откуда будете
                            пополнять?
                        </p>
                        <div class="flex flex-col gap-3">
                            @foreach($verifiedAddresses as $address)
                                @php
                                    $nm = [
                                        'bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623', 'BTC'],
                                        'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF', 'ETH'],
                                        'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF', 'TON'],
                                        'usdt_ton' => ['USDT (TON)', 'USDT', '₮', '#26A17B', '#4DBFA0', 'TON'],
                                        'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0', 'DASH']
                                    ];
                                    $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc', strtoupper($address->network)];

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
                                    $exp = ['bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/', 'ethereum' => 'https://etherscan.io/address/', 'ton' => 'https://tonviewer.com/', 'usdt_ton' => 'https://tonviewer.com/', 'dash' => 'https://insight.dash.org/insight/address/'];
                                    $expLink = ($exp[$address->network] ?? '#') . $address->address;
                                @endphp

                                <div onclick="selectAsset('{{ $address->network }}', '{{ $address->id }}')"
                                    class="group relative bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden hover:shadow-md hover:border-violet-100 transition-all p-3 cursor-pointer text-left">
                                    <div class="flex items-center gap-4">
                                        {{-- Left: Icon --}}
                                        <div class="relative shrink-0">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[16px] font-bold shadow-sm"
                                                style="background:linear-gradient(135deg,{{ $m[3] }},{{ $m[4] }})">
                                                {{ $m[2] }}
                                            </div>
                                        </div>

                                        {{-- Middle: Info Stack --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 group/alias">
                                                @if($address->isVerified())
                                                    <div class="text-[#0095f6] shrink-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                                            fill="currentColor">
                                                            <path
                                                                d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41 1.41-8.48 8.48z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <span class="text-[14px] font-bold text-zinc-900 truncate">
                                                    {{ $fullAlias }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 mt-0.5">
                                                <code
                                                    class="text-[11px] font-mono text-zinc-400 truncate select-all">{{ $m[5] }} &gt; {{ $m[1] }} &gt; {{ $address->address }}</code>
                                                <div class="flex items-center gap-1.5 shrink-0"
                                                    onclick="event.stopPropagation()">
                                                    <button onclick="copyAddr('{{ $address->address }}', this)"
                                                        class="text-zinc-300 hover:text-violet-500 transition-colors"
                                                        title="Копировать адрес">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path
                                                                d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                                            <path
                                                                d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5a2 2 0 012-2h6a2 2 0 00-2-2H5z" />
                                                        </svg>
                                                    </button>
                                                    <a href="{{ $expLink }}" target="_blank"
                                                        class="text-zinc-300 hover:text-violet-500 transition-colors"
                                                        title="Открыть в эксплорере">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path
                                                                d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                            <path
                                                                d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3 mt-1.5">
                                                <div class="flex items-baseline gap-1.5 leading-none">
                                                    <span class="text-[14px] font-bold font-mono text-zinc-900">
                                                        {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">{{ $m[1] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-zinc-300">→</span>
                                    </div>
                                </div>
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
                    <div id="step-details"
                        class="hidden bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden p-6 md:p-8">
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
                                    $exp = ['bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/', 'ethereum' => 'https://etherscan.io/address/', 'ton' => 'https://tonviewer.com/', 'usdt_ton' => 'https://tonviewer.com/', 'dash' => 'https://insight.dash.org/insight/address/'];
                                    $expLink = ($exp[$address->network] ?? '#') . $address->address;
                                @endphp

                                {{-- Source Section (Visual Flow) --}}
                                <div class="mb-4">
                                    <p class="text-[11px] text-zinc-400 uppercase font-bold tracking-wider mb-3">Отправляйте
                                        средства только с этого кошелька:</p>

                                    <div
                                        class="group relative bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden p-3 text-left">
                                        <div class="flex items-center gap-4">
                                            {{-- Left: Icon --}}
                                            <div class="relative shrink-0">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[16px] font-bold shadow-sm"
                                                    style="background:linear-gradient(135deg,{{ $asset['color'] }},{{ $asset['color2'] }})">
                                                    {{ $asset['icon'] }}
                                                </div>
                                            </div>

                                            {{-- Middle: Info Stack --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 group/alias">
                                                    @if($address->isVerified())
                                                        <div class="text-[#0095f6] shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                                viewBox="0 0 24 24" fill="currentColor">
                                                                <path
                                                                    d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41 1.41-8.48 8.48z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <span class="text-[14px] font-bold text-zinc-900 truncate">
                                                        {{ $fullAlias }}
                                                    </span>
                                                </div>

                                                @php
                                                    $exp = ['bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/', 'ethereum' => 'https://etherscan.io/address/', 'ton' => 'https://tonviewer.com/', 'usdt_ton' => 'https://tonviewer.com/', 'dash' => 'https://insight.dash.org/insight/address/'];
                                                    $expLink = ($exp[$address->network] ?? '#') . $address->address;
                                                @endphp
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <code
                                                        class="text-[11px] font-mono text-zinc-400 truncate select-all">{{ $asset['symbol'] }} &gt; {{ $address->address }}</code>
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <button onclick="copyAddr('{{ $address->address }}', this)"
                                                            class="text-zinc-300 hover:text-violet-500 transition-colors"
                                                            title="Копировать адрес">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                                                <path
                                                                    d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5a2 2 0 012-2h6a2 2 0 00-2-2H5z" />
                                                            </svg>
                                                        </button>
                                                        <a href="{{ $expLink }}" target="_blank"
                                                            class="text-zinc-300 hover:text-violet-500 transition-colors"
                                                            title="Открыть в эксплорере">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                                <path
                                                                    d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3 mt-1.5">
                                                    <div class="flex items-baseline gap-1.5 leading-none">
                                                        <span class="text-[14px] font-bold font-mono text-zinc-900">
                                                            {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                                        </span>
                                                        <span
                                                            class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">{{ $asset['symbol'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
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
                                <div
                                    class="bg-zinc-900 rounded-3xl p-6 shadow-xl mb-6 ring-1 ring-white/10 flex flex-col items-center">
                                    <div class="text-[11px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Адрес для
                                        пополнения ({{ $asset['symbol'] }})</div>

                                    {{-- QR Code --}}
                                    <div class="bg-white p-3 rounded-2xl mb-5 shadow-inner">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($asset['address']) }}"
                                            alt="QR Code" class="w-[150px] h-[150px] block">
                                    </div>

                                    <div
                                        class="text-[14px] font-mono text-white break-all mb-5 select-all text-center leading-relaxed font-medium px-2">
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
                    const initialTitle = "Пополнение баланса";
                    const initialBackUrl = "{{ route('shop.customers.account.credits.index') }}";

                    function selectAsset(assetKey, walletId) {
                        document.getElementById('step-selection').classList.add('hidden');
                        document.getElementById('step-details').classList.remove('hidden');

                        // Update Top Header
                        document.getElementById('page-title').innerText = "Детали пополнения";
                        const backBtn = document.getElementById('page-back-link');
                        backBtn.href = "javascript:void(0)";
                        backBtn.onclick = function (e) { e.preventDefault(); backToSelection(); };

                        // Hide all wallet details
                        document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                        // Show selected wallet details
                        const target = document.getElementById('details-wallet-' + walletId);
                        if (target) target.classList.remove('hidden');
                    }

                    function backToSelection() {
                        document.getElementById('step-details').classList.add('hidden');
                        document.getElementById('step-selection').classList.remove('hidden');

                        // Restore Top Header
                        document.getElementById('page-title').innerText = initialTitle;
                        const backBtn = document.getElementById('page-back-link');
                        backBtn.href = initialBackUrl;
                        backBtn.onclick = null;
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