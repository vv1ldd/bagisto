<x-shop::layouts.account :has-header="false">
    {{-- Page Title Slot (Meta) --}}
    <x-slot:title>
        Пополнение баланса
        </x-slot>

        <div class="max-w-lg mx-auto px-4 py-12">
            {{-- Top Navigation & Title (Truly Outside the Tile) --}}
            <div class="flex items-center gap-4 mb-8">
                {{-- Initial state: Real Link to Balance --}}
                <a id="page-back-link" href="{{ route('shop.customers.account.credits.index') }}"
                    class="w-10 h-10 rounded-full bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                {{-- Unified Step-back button --}}
                <button id="step-back-btn" onclick="handleStepBack()" style="display: none;"
                    class="w-10 h-10 rounded-full bg-white border border-zinc-100 flex items-center justify-center text-zinc-400 hover:text-violet-500 hover:border-violet-100 transition-all shadow-sm group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h1 id="page-title" class="text-[22px] font-bold text-zinc-900 leading-none">Пополнение баланса</h1>
            </div>

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

                <div id="step-empty"
                    class="{{ $verifiedAddresses->isEmpty() ? '' : 'hidden' }} bg-white rounded-[32px] border border-zinc-100 shadow-sm p-8 flex flex-col items-center text-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-violet-50 flex items-center justify-center text-3xl">🔐</div>
                    <div>
                        <p class="text-[16px] font-bold text-zinc-800">Нет верифицированных кошельков</p>
                        <p class="text-[13px] text-zinc-400 mt-1">Для пополнения необходимо сначала добавить и
                            верифицировать свой кошелёк.</p>
                    </div>
                    <button onclick="goToManagement()"
                        style="background:linear-gradient(135deg,#7c3aed,#4f46e5)"
                        class="text-white font-bold px-6 py-3 rounded-2xl text-[15px] shadow-lg shadow-violet-200 active:scale-95 transition-all">
                        + Добавить кошелёк
                    </button>
                </div>

                {{-- Wallet Selection (Flat List) --}}
                <div id="step-selection"
                    class="{{ $verifiedAddresses->isEmpty() ? 'hidden' : '' }} bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden p-6 md:p-8">
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
                                            <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
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

                        {{-- Manage Wallets Button (Instant Transition) --}}
                        <button onclick="goToManagement()"
                            class="w-full bg-zinc-50/50 rounded-2xl border border-dashed border-zinc-200 flex items-center gap-4 px-4 py-4 hover:border-violet-300 hover:bg-zinc-50 transition-all active:scale-[0.98] mt-2 group/manage">
                            <span
                                class="w-10 h-10 rounded-xl flex items-center justify-center bg-zinc-200/50 text-zinc-500 text-[20px] font-light shrink-0 group-hover/manage:bg-violet-100 group-hover/manage:text-violet-600 transition-colors">+</span>
                            <div class="text-left flex-1">
                                <div class="text-[15px] font-bold text-zinc-900">Управление кошельками</div>
                                <div class="text-[12px] text-zinc-400">Добавить или верифицировать новый</div>
                            </div>
                            <span
                                class="text-zinc-300 text-[18px] group-hover/manage:text-violet-400 group-hover/manage:translate-x-1 transition-all">→</span>
                        </button>
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

                {{-- Step 3: Wallet Management (List All) --}}
                <div id="step-management" class="hidden space-y-3">
                    @if($allAddresses->isEmpty())
                        <div
                            class="bg-white rounded-[32px] border border-zinc-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mb-4 text-3xl">👛
                            </div>
                            <p class="text-zinc-500 font-bold text-[16px]">У вас пока нет кошельков</p>
                            <p class="text-zinc-400 text-[13px] mt-1">Добавьте свой первый адрес для пополнения</p>
                        </div>
                    @else
                        @foreach($allAddresses as $address)
                            @php
                                $nm = ['bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623', 'BTC'], 'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF', 'ETH'], 'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF', 'TON'], 'usdt_ton' => ['USDT (TON)', 'USDT', '₮', '#26A17B', '#4DBFA0', 'TON'], 'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0', 'DASH']];
                                $m = $nm[$address->network] ?? ['Unknown', '?', '?', '#aaa', '#ccc', strtoupper($address->network)];
                                $dAmt = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');
                                $exp = ['bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/', 'ethereum' => 'https://etherscan.io/address/', 'ton' => 'https://tonviewer.com/', 'usdt_ton' => 'https://tonviewer.com/', 'dash' => 'https://insight.dash.org/insight/address/'];
                                $expLink = ($exp[$address->network] ?? '#') . $address->address;

                                $username = auth()->guard('customer')->user()->username;
                                $netMap = ['ton' => ['chain' => 'ton'], 'usdt_ton' => ['chain' => 'ton', 'token' => 'usdt'], 'bitcoin' => ['chain' => 'btc'], 'ethereum' => ['chain' => 'erc20', 'token' => 'usdt'], 'dash' => ['chain' => 'dash']];
                                $nmData = $netMap[$address->network] ?? ['chain' => $address->network];

                                $parts = ["@{$username}"];
                                $parts[] = $nmData['chain'] ?? $address->network;
                                if (isset($nmData['token']))
                                    $parts[] = $nmData['token'];
                                if ($address->alias)
                                    $parts[] = $address->alias;
                                $fullAlias = implode('.', $parts);
                            @endphp
                            <div
                                class="bg-white rounded-[24px] border border-zinc-100 shadow-sm p-4 hover:border-violet-100 transition-all flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[16px] font-bold shadow-sm shrink-0"
                                    style="background:linear-gradient(135deg,{{ $m[3] }},{{ $m[4] }})">{{ $m[2] }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        @if($address->isVerified())
                                            <div class="text-[#0095f6] shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                    viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M22.5 12.5c0-1.58-.88-2.95-2.18-3.65.15-.44.23-.91.23-1.4 0-2.48-2.02-4.5-4.5-4.5-.49 0-.96.08-1.4.22C13.95 1.88 12.58 1 11 1s-2.95.88-3.65 2.17c-.44-.14-.91-.22-1.4-.22-2.48 0-4.5 2.02-4.5 4.5 0 .49.08.96.22 1.4C.38 9.55-.5 10.92-.5 12.5s.88 2.95 2.17 3.65c-.14.44-.22.91-.22 1.4 0 2.48 2.02 4.5 4.5 4.5.49 0 .96-.08 1.4-.22 1.1 2.09 3.26 3.5 5.75 3.5 2.49 0 4.65-1.41 5.75-3.5.44.14.91.22 1.4.22 2.48 0 4.5-2.02 4.5-4.5 0-.49-.08-.96-.22-1.4 1.3-1.2 2.18-2.57 2.18-4.15zm-12.23 4.81L6.04 13l1.41-1.41 2.82 2.82 7.07-7.07 1.41-1.41-8.48 8.48z" />
                                        </svg></div>@endif
                                        <span class="text-[14px] font-bold text-zinc-900 truncate">{{ $fullAlias }}</span>
                                        <button
                                            onclick="const newAlias = prompt('Название кошелька', '{{ $address->alias ?? '' }}'); if (newAlias !== null) { document.getElementById('update-alias-form-{{ $address->id }}').querySelector('input[name=alias]').value = newAlias; document.getElementById('update-alias-form-{{ $address->id }}').submit(); }"
                                            class="text-zinc-300 hover:text-violet-500 transition-colors"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg></button>
                                        <form id="update-alias-form-{{ $address->id }}"
                                            action="{{ route('shop.customers.account.crypto.update_alias', $address->id) }}"
                                            method="POST" class="hidden">@csrf<input type="hidden" name="alias" value=""></form>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <code
                                            class="text-[10px] font-mono text-zinc-400 truncate select-all">{{ $address->address }}</code>
                                        <button onclick="copyAddr('{{ $address->address }}', this)"
                                            class="text-zinc-300 hover:text-violet-500 transition-colors"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                                <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5a2 2 0 012-2h6a2 2 0 00-2-2H5z" />
                                            </svg></button>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <span
                                            class="text-[13px] font-bold font-mono text-zinc-900">{{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                            <span class="text-[10px] text-zinc-400">{{ $m[1] }}</span></span>
                                        <a href="{{ route('shop.customers.account.crypto.sync', $address->id) }}"
                                            class="text-zinc-300 hover:text-emerald-500 transition-all"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                                <path d="M3 3v5h5" />
                                                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                                <path d="M16 16h5v5" />
                                            </svg></a>
                                        @if(!$address->isVerified()) <button
                                            onclick="showVerifyModal('{{ $address->id }}','{{ $address->network }}','{{ $dAmt }}','{{ $address->address }}')"
                                        class="text-[10px] text-emerald-600 font-bold hover:underline">Верифицировать</button>@endif
                                    </div>
                                </div>
                                    <form id="delete-wallet-form-{{ $address->id }}" action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST" class="shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmWalletDeletion('{{ $address->id }}', '{{ $address->alias ?: $address->address }}')" class="w-9 h-9 rounded-xl flex items-center justify-center bg-zinc-50 hover:bg-red-50 text-red-400 border border-zinc-100 hover:border-red-100 transition-all active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                            </div>
                        @endforeach
                    <button type="button" onclick="goToAddWallet()"
                        class="w-full py-4 mt-2 rounded-[24px] border border-dashed border-zinc-200 bg-zinc-50/10 text-zinc-400 hover:text-violet-500 hover:border-violet-200 hover:bg-violet-50/30 transition-all font-bold flex items-center justify-center gap-2 group active:scale-[0.98]">
                        + Добавить новый кошелек
                    </button>
                </div>

                {{-- Step 4: Add New Wallet (Form) --}}
                <div id="step-add-wallet"
                    class="hidden bg-white rounded-[32px] border border-zinc-100 shadow-sm p-6 md:p-8 animate-in fade-in zoom-in-98 duration-300">
                    <x-shop::form :action="route('shop.customers.account.crypto.store')">
                        <input type="hidden" name="network" id="wallet-net-input" value="">
                        <div class="space-y-6">
                            <div>
                                <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">1. Выберите
                                    сеть</p>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['bitcoin' => ['₿', 'BTC', '#F7931A'], 'ethereum' => ['Ξ', 'ETH', '#627EEA'], 'ton' => ['◎', 'TON', '#0098EA'], 'dash' => ['D', 'DASH', '#1c75bc']] as $net => $m)
                                        <button type="button" id="wnet-{{ $net }}" onclick="selNet('{{ $net }}')"
                                            class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border-2 border-zinc-50 transition-all duration-200 hover:bg-zinc-50 active:scale-95 group">
                                            <span
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[16px] font-bold mb-1 shadow-sm transition-transform group-hover:scale-110"
                                                style="background:linear-gradient(135deg, {{ $m[2] }}, {{ $m[2] }}dd)">{{ $m[0] }}</span>
                                            <span
                                                class="text-[10px] font-bold text-zinc-400 group-hover:text-zinc-600 transition-colors uppercase tracking-tight">{{ $m[1] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div id="wallet-addr-section"
                                class="hidden space-y-6 animate-in fade-in slide-in-from-top-2 duration-300">
                                <div id="ton-asset-selector"
                                    class="hidden p-3 bg-zinc-50/50 rounded-2xl border border-zinc-100">
                                    <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3 px-1">2.
                                        Актив для верификации</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach(['ton' => 'TON Coin', 'usdt_ton' => 'USDT (TON)'] as $asset => $label)
                                            <button type="button" onclick="selTonAsset('{{ $asset }}')"
                                                data-asset="{{ $asset }}"
                                                class="ton-asset-btn flex items-center justify-center gap-2 py-3 rounded-xl border border-zinc-200 bg-white transition-all active:scale-95">
                                                <span class="text-[13px] font-bold text-zinc-600">{{ $label }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">3. Адрес
                                        кошелька</p>
                                    <div class="relative group">
                                        <input type="text" name="address" id="wallet-addr-input"
                                            placeholder="Вставьте ваш адрес…" oninput="onWalletInput(this.value)"
                                            class="w-full rounded-2xl border-zinc-100 bg-zinc-50/50 text-[14px] font-mono py-4 pl-5 pr-14 placeholder-zinc-400 focus:outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-50 focus:bg-white transition-all shadow-inner" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none">
                                            <div id="wallet-val-ok" class="hidden text-emerald-500 font-bold text-[18px]">✓
                                            </div>
                                            <div id="wallet-val-err" class="hidden text-red-500 font-bold text-[18px]">✗
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="wallet-add-btn"
                                    style="background:linear-gradient(135deg,#7c3aed,#4f46e5);opacity:0.4;cursor:not-allowed;"
                                    class="w-full text-white font-bold py-4 rounded-2xl text-[15px] shadow-lg shadow-violet-100 active:scale-[0.98] transition-all">
                                    + Добавить этот кошелёк
                                </button>
                            </div>
                        </div>
                    </x-shop::form>
                </div>
            @endif

            @push('scripts')
                <script>
                    const initialTitle = "Пополнение баланса";
                    let currentStep = @json($verifiedAddresses->isEmpty() ? 'empty' : 'selection');

                    // ── Clipboard & Navigation ────────────────────────────────────────────────────
                    function copyAddr(text, btn) {
                        navigator.clipboard.writeText(text).then(() => {
                            const orig = btn.innerHTML;
                            btn.innerHTML = '✓ Скопировано';
                            setTimeout(() => btn.innerHTML = orig, 2000);
                        });
                    }

                    function updateHeader() {
                        const titleEl = document.getElementById('page-title');
                        const backLink = document.getElementById('page-back-link');
                        const backBtn = document.getElementById('step-back-btn');

                        if (currentStep === 'selection' || currentStep === 'empty') {
                            titleEl.innerText = initialTitle;
                            backLink.style.display = 'flex';
                            backBtn.style.display = 'none';
                        } else {
                            backLink.style.display = 'none';
                            backBtn.style.display = 'flex';
                            if (currentStep === 'details') titleEl.innerText = "Детали пополнения";
                            if (currentStep === 'management') titleEl.innerText = "Мои кошельки";
                            if (currentStep === 'add-wallet') titleEl.innerText = "Новый кошелек";
                        }
                    }

                    function switchStep(newStep) {
                        ['step-selection', 'step-details', 'step-management', 'step-add-wallet', 'step-empty'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.classList.add('hidden');
                        });
                        const target = document.getElementById('step-' + newStep);
                        if (target) target.classList.remove('hidden');
                        currentStep = newStep;
                        updateHeader();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }

                    function handleStepBack() {
                        if (currentStep === 'details') switchStep('selection');
                        else if (currentStep === 'management') switchStep(@json($verifiedAddresses->isEmpty() ? 'empty' : 'selection'));
                        else if (currentStep === 'add-wallet') switchStep('management');
                    }

                    // ── Integration Functions ────────────────────────────────────────────────────
                    function selectAsset(assetKey, walletId) {
                        switchStep('details');
                        document.querySelectorAll('.wallet-details-view').forEach(el => el.classList.add('hidden'));
                        const target = document.getElementById('details-wallet-' + walletId);
                        if (target) target.classList.remove('hidden');
                    }

                    function goToManagement() { switchStep('management'); }
                    function goToAddWallet() { switchStep('add-wallet'); }

                    // ── Validation Logic (Ported from crypto/index) ────────────────────────────────
                    const _SHA256 = (() => { const K = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2]; function h(msg) { let H = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]; msg = Array.from(msg); const l = msg.length * 8; msg.push(0x80); while ((msg.length % 64) !== 56) msg.push(0); for (let i = 7; i >= 0; i--)msg.push((l / (2 ** (i * 8))) & 0xFF); for (let c = 0; c < msg.length; c += 64) { const W = []; for (let i = 0; i < 16; i++)W[i] = (msg[c + i * 4] << 24) | (msg[c + i * 4 + 1] << 16) | (msg[c + i * 4 + 2] << 8) | msg[c + i * 4 + 3]; for (let i = 16; i < 64; i++) { const s0 = ((W[i - 15] >>> 7) | (W[i - 15] << 25)) ^ ((W[i - 15] >>> 18) | (W[i - 15] << 14)) ^ (W[i - 15] >>> 3); const s1 = ((W[i - 2] >>> 17) | (W[i - 2] << 15)) ^ ((W[i - 2] >>> 19) | (W[i - 2] << 13)) ^ (W[i - 2] >>> 10); W[i] = (W[i - 16] + s0 + W[i - 7] + s1) >>> 0; } let [a, b, d, e, f, g, hh, ii] = [...H, H[6], H[7]]; for (let j = 0; j < 64; j++) { const S1 = ((f >>> 6) | (f << 26)) ^ ((f >>> 11) | (f << 21)) ^ ((f >>> 25) | (f << 7)); const ch = (f & g) ^ (~f & hh); const t1 = (ii + S1 + ch + K[j] + W[j]) >>> 0; const S0 = ((a >>> 2) | (a << 30)) ^ ((a >>> 13) | (a << 19)) ^ ((a >>> 22) | (a << 10)); const maj = (a & b) ^ (a & d) ^ (b & d); const t2 = (S0 + maj) >>> 0; ii = hh; hh = g; g = f; f = (e + t1) >>> 0; e = d; d = b; b = a; a = (t1 + t2) >>> 0; } H = [H[0] + a, H[1] + b, H[2] + d, H[3] + e, H[4] + f, H[5] + g, H[6] + hh, H[7] + ii].map(v => v >>> 0); } return H.map(v => v.toString(16).padStart(8, '0')).join(''); } return { hash: h }; })();
                    function _b58d(s) { const A = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'; let n = 0n; for (const c of s) { const i = A.indexOf(c); if (i < 0) return null; n = n * 58n + BigInt(i); } let h = n.toString(16); if (h.length % 2) h = '0' + h; const b = h.match(/../g).map(x => parseInt(x, 16)); return [...Array(s.match(/^1*/)[0].length).fill(0), ...b]; }
                    function _b58chk(a) { const b = _b58d(a); if (!b || b.length < 5) return false; const p = b.slice(0, -4), cs = b.slice(-4), h1 = _SHA256.hash(p), h2 = _SHA256.hash(h1.match(/../g).map(x => parseInt(x, 16))); return h2.slice(0, 8) === cs.map(x => x.toString(16).padStart(2, '0')).join(''); }
                    function _crc16(d) { let c = 0; for (const b of d) { c ^= (b << 8); for (let i = 0; i < 8; i++)c = (c & 0x8000) ? ((c << 1) ^ 0x1021) : (c << 1); } return c & 0xFFFF; }
                    const ADDR_NETS = {
                        bitcoin: { validate: a => { if (/^bc1[a-z0-9]{6,87}$/.test(a)) return true; if (!/^[13][1-9A-HJ-NP-Za-km-z]{25,34}$/.test(a)) return false; return _b58chk(a); }, bg: '#FFF7ED', color: '#F7931A' },
                        ethereum: { validate: a => /^0x[0-9a-fA-F]{40}$/.test(a), bg: '#EEF2FF', color: '#627EEA' },
                        ton: { validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); let bin; try { bin = atob(b64); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); }, bg: '#E0F5FF', color: '#0098EA' },
                        dash: { validate: a => { if (!/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(a)) return false; return _b58chk(a); }, bg: '#EFF6FF', color: '#1c75bc' }
                    };

                    let _selNet = null;
                    function selNet(net) {
                        _selNet = net;
                        if (net === 'ton') {
                            document.getElementById('ton-asset-selector').classList.remove('hidden');
                            document.getElementById('wallet-net-input').value = 'ton';
                            selTonAsset('ton');
                        } else {
                            document.getElementById('ton-asset-selector').classList.add('hidden');
                            document.getElementById('wallet-net-input').value = net;
                        }
                        Object.keys(ADDR_NETS).forEach(k => {
                            const b = document.getElementById('wnet-' + k);
                            if (k === net) { b.style.background = ADDR_NETS[k].bg; b.style.borderColor = ADDR_NETS[k].color; }
                            else { b.style.background = ''; b.style.borderColor = '#e4e4e7'; }
                        });
                        document.getElementById('wallet-addr-section').classList.remove('hidden');
                        document.getElementById('wallet-addr-input').focus();
                        onWalletInput('');
                    }

                    function selTonAsset(asset) {
                        document.getElementById('wallet-net-input').value = asset;
                        document.querySelectorAll('.ton-asset-btn').forEach(btn => {
                            const isActive = btn.dataset.asset === asset;
                            btn.style.borderColor = isActive ? '#0098EA' : '#e4e4e7';
                            btn.style.background = isActive ? '#E0F5FF' : '#fff';
                        });
                    }

                    function onWalletInput(val) {
                        const ok = document.getElementById('wallet-val-ok'), err = document.getElementById('wallet-val-err'), btn = document.getElementById('wallet-add-btn');
                        val = val.trim();
                        if (!val || !_selNet) { ok.classList.add('hidden'); err.classList.add('hidden'); btn.disabled = true; btn.style.opacity = '0.4'; return; }
                        const v = ADDR_NETS[_selNet].validate(val);
                        ok.classList.toggle('hidden', !v); err.classList.toggle('hidden', v);
                        btn.disabled = !v; btn.style.opacity = v ? '1' : '0.4'; btn.style.cursor = v ? 'pointer' : 'not-allowed';
                    }

                    function confirmWalletDeletion(id, expected) {
                        const input = prompt(`Введите "${expected}" для удаления:`);
                        if (input === expected) document.getElementById(`delete-wallet-form-${id}`).submit();
                    }

                    // Deep link & Auto-Verification logic
                    document.addEventListener('DOMContentLoaded', () => {
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.get('step') === 'management') goToManagement();

                        @if(session('show_verify_id'))
                            @php
                                $target = $allAddresses->firstWhere('id', session('show_verify_id'));
                            @endphp
                            @if($target)
                                goToManagement();
                                setTimeout(() => {
                                    showVerifyModal(
                                        '{{ $target->id }}',
                                        '{{ $target->network }}',
                                        '{{ rtrim(rtrim(number_format($target->verification_amount ?? 0, 8, '.', ''), '0'), '.') }}',
                                        '{{ $target->address }}'
                                    );
                                }, 500);
                            @endif
                        @endif
                    });
                </script>
            @endpush
</x-shop::layouts.account>