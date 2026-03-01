@push('scripts')
    <script>
        // ─── Network auto-detection ────────────────────────────────────────────────
        const NETWORKS = {
            bitcoin:  { label: 'Bitcoin',     sub: 'BTC',          symbol: '₿', color: '#F7931A' },
            ethereum: { label: 'Ethereum',    sub: 'ETH / ERC20',  symbol: 'Ξ', color: '#627EEA' },
            ton:      { label: 'TON + USDT',  sub: 'TON сеть',     symbol: '◎', color: '#0098EA' },
            dash:     { label: 'Dash',        sub: 'DASH',         symbol: 'D', color: '#1c75bc' },
        };

        function detectNetwork(addr) {
            addr = addr.trim();
            if (!addr) return null;
            // Bitcoin: legacy (1/3) or bech32 (bc1)
            if (/^(1|3)[1-9A-HJ-NP-Za-km-z]{25,34}$/.test(addr) || /^bc1[a-z0-9]{6,87}$/.test(addr)) return 'bitcoin';
            // Ethereum: 0x + 40 hex chars
            if (/^0x[0-9a-fA-F]{40}$/.test(addr)) return 'ethereum';
            // TON: UQ/EQ friendly format or raw 0:hex — covers TON + USDT_TON
            if (/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(addr) || /^0:[0-9a-fA-F]{64}$/.test(addr)) return 'ton';
            // Dash: starts with X, 34 chars
            if (/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(addr)) return 'dash';
            return null;
        }

        function detectNetworkFromAddress(addr) {
            const badge    = document.getElementById('network-badge');
            const unknown  = document.getElementById('network-unknown');
            const netInput = document.getElementById('detected-network');
            const addBtn   = document.getElementById('add-btn');

            const network = detectNetwork(addr);

            badge.classList.add('hidden');
            unknown.classList.add('hidden');
            netInput.value = '';
            addBtn.disabled = true;

            if (!addr.trim()) return;

            if (network) {
                const meta = NETWORKS[network];
                document.getElementById('network-badge-icon').textContent  = meta.symbol;
                document.getElementById('network-badge-icon').style.background = meta.color;
                document.getElementById('network-badge-label').textContent = meta.label;
                document.getElementById('network-badge-sub').textContent   = meta.sub;
                badge.classList.remove('hidden');
                netInput.value = network;
                addBtn.disabled = false;
            } else {
                unknown.classList.remove('hidden');
            }
        }

            navigator.clipboard.writeText(text).then(() => {
                const original = btnEl.innerHTML;
                btnEl.innerHTML = '<span class="text-emerald-400 text-[11px] font-bold">✓ Скопировано</span>';
                setTimeout(() => btnEl.innerHTML = original, 2000);
            });
        }

        function showVerifyModal(id, network, amount, address) {
            document.getElementById('verify-modal').classList.remove('hidden');
            document.getElementById('verify-modal').classList.add('flex');
            const currencySymbols = {
                bitcoin: 'BTC', ethereum: 'ETH', ton: 'TON', usdt_ton: 'USDT', dash: 'DASH'
            };
            document.getElementById('verify-amount').innerText = amount + ' ' + (currencySymbols[network] || '');
            document.getElementById('verify-id').value = id;

            const destAddresses = {
                bitcoin: '{{ config('crypto.verification_addresses.bitcoin') }}',
                ethereum: '{{ config('crypto.verification_addresses.ethereum') }}',
                ton: '{{ config('crypto.verification_addresses.ton') }}',
                usdt_ton: '{{ config('crypto.verification_addresses.usdt_ton') }}',
                dash: '{{ config('crypto.verification_addresses.dash') }}'
            };
            const destAddress = destAddresses[network];
            document.getElementById('verify-dest-address').innerText = destAddress;
            document.getElementById('verify-dest-address-copy').onclick = () => {
                navigator.clipboard.writeText(destAddress);
                const btn = document.getElementById('verify-dest-address-copy');
                btn.innerText = '✓';
                setTimeout(() => btn.innerText = 'Копировать', 2000);
            };
            const verifyLink = "{{ route('shop.customers.account.crypto.verify', ':id') }}".replace(':id', id);
            document.getElementById('check-verify-btn').href = verifyLink;
        }

        function closeVerifyModal() {
            document.getElementById('verify-modal').classList.add('hidden');
            document.getElementById('verify-modal').classList.remove('flex');
        }
    </script>
@endpush

<x-shop::layouts.account>
    {{-- Verify Modal --}}
    <div id="verify-modal"
        class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-black/60 backdrop-blur-md">
        <div class="bg-white rounded-[28px] w-full max-w-[400px] overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-6 text-center">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <span class="icon-shield text-white text-2xl"></span>
                </div>
                <h3 class="text-xl font-bold text-white">Верификация</h3>
                <p class="text-violet-200 text-sm mt-1">Докажите владение кошельком</p>
            </div>

            <div class="p-6 space-y-3">
                <div class="bg-violet-50 border border-violet-100 p-4 rounded-2xl">
                    <p class="text-[12px] text-violet-500 font-medium mb-1 uppercase tracking-wide">Сумма платежа</p>
                    <p id="verify-amount" class="text-xl font-bold text-violet-900 font-mono">—</p>
                </div>

                <div class="bg-zinc-50 border border-zinc-100 p-4 rounded-2xl">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-[12px] text-zinc-400 font-medium mb-1 uppercase tracking-wide">Отправить на
                                адрес</p>
                            <p id="verify-dest-address"
                                class="text-[12px] font-mono font-bold text-zinc-700 break-all leading-relaxed">—</p>
                        </div>
                        <button id="verify-dest-address-copy"
                            class="shrink-0 text-[11px] text-violet-600 font-bold bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl mt-1 active:scale-95 transition-all">
                            Копировать
                        </button>
                    </div>
                </div>

                <div
                    class="text-[13px] text-zinc-500 space-y-1.5 leading-relaxed bg-zinc-50 rounded-2xl p-4 border border-zinc-100">
                    <p>1. Отправьте <b class="text-zinc-700">точно указанную</b> сумму на наш адрес.</p>
                    <p>2. Это докажет контроль над вашим кошельком.</p>
                    <p>3. Нажмите «Проверить» после отправки.</p>
                </div>
            </div>

            <div class="px-6 pb-6 flex flex-col gap-2">
                <a id="check-verify-btn" href="#"
                    class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold py-4 rounded-2xl active:scale-[0.98] transition-all text-center shadow-lg shadow-violet-200">
                    Проверить верификацию
                </a>
                <button onclick="closeVerifyModal()"
                    class="w-full text-zinc-400 font-semibold py-3 active:opacity-50 transition-all text-[14px]">
                    Позже
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" id="verify-id" value="">

    <x-slot:title>Крипто Адреса</x-slot>

        @push('styles')
            <style>
                .net-card {
                    background: white;
                    border-radius: 20px;
                    border: 1.5px solid #f0f0f3;
                    overflow: hidden;
                    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
                    margin-bottom: 12px;
                    transition: box-shadow 0.2s;
                }

                .net-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    font-size: 11px;
                    font-weight: 700;
                    padding: 3px 10px;
                    border-radius: 999px;
                }

                .verified-badge {
                    background: #ecfdf5;
                    color: #059669;
                    border: 1px solid #a7f3d0;
                }

                .unverified-badge {
                    background: #f4f4f5;
                    color: #71717a;
                    border: 1px solid #e4e4e7;
                }
            </style>
        @endpush

        <div class="flex-auto pb-10 pt-2 ios-page">

            {{-- Add Address Form --}}
            <div class="ios-group-title">Добавить адрес</div>
            <div class="rounded-[20px] border border-zinc-100 bg-white mb-6 overflow-hidden shadow-sm">
                <x-shop::form :action="route('shop.customers.account.crypto.store')">
                    <div class="p-5 flex flex-col gap-4">

                        {{-- Hidden network input populated by JS --}}
                        <input type="hidden" name="network" id="detected-network" value="">

                        {{-- Address input with auto-detect --}}
                        <div>
                            <label class="block text-[12px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">
                                Адрес кошелька
                            </label>
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control
                                    type="text" name="address" id="address-input" rules="required"
                                    placeholder="Вставьте адрес кошелька…" :label="'Адрес'"
                                    class="!rounded-xl !border-zinc-200 !text-[13px] font-mono !py-3 !px-4 focus:!border-violet-400 focus:!ring-2 focus:!ring-violet-100"
                                    oninput="detectNetworkFromAddress(this.value)" />
                                <x-shop::form.control-group.error control-name="address" />
                            </x-shop::form.control-group>

                            {{-- Detected network badge --}}
                            <div id="network-badge" class="hidden mt-3 flex items-center gap-2">
                                <span id="network-badge-icon"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[14px] font-bold"></span>
                                <div>
                                    <div id="network-badge-label" class="text-[14px] font-bold text-zinc-800"></div>
                                    <div id="network-badge-sub" class="text-[11px] text-zinc-400"></div>
                                </div>
                                <span id="network-badge-ok"
                                    class="ml-auto text-[11px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full">
                                    ✓ Обнаружена
                                </span>
                            </div>
                            <div id="network-unknown" class="hidden mt-3 text-[13px] text-red-400 font-medium">
                                ✗ Не удалось определить сеть. Проверьте адрес.
                            </div>
                        </div>

                        <button type="submit" id="add-btn" disabled
                            class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-md shadow-violet-200 active:scale-[0.98] transition-all text-[15px] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                            + Добавить адрес
                        </button>
                    </div>
                </x-shop::form>
            </div>

            {{-- Addresses List --}}
            <div class="ios-group-title">Ваши адреса</div>

            @if ($addresses->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mb-4 border border-zinc-100">
                        <span class="text-3xl">🔐</span>
                    </div>
                    <p class="text-zinc-500 font-semibold text-[15px]">Нет привязанных адресов</p>
                    <p class="text-zinc-400 text-[13px] mt-1">Добавьте кошелёк выше, чтобы получать депозиты</p>
                </div>
            @else
                @foreach ($addresses as $address)
                    @php
                        $networkMeta = [
                            'bitcoin' => ['label' => 'Bitcoin', 'ticker' => 'BTC', 'symbol' => '₿', 'from' => '#F7931A', 'to' => '#F5A623', 'text' => '#7A4100'],
                            'ethereum' => ['label' => 'Ethereum', 'ticker' => 'ETH', 'symbol' => 'Ξ', 'from' => '#627EEA', 'to' => '#8A9FEF', 'text' => '#1E3A8A'],
                            'ton' => ['label' => 'TON', 'ticker' => 'TON', 'symbol' => '◎', 'from' => '#0098EA', 'to' => '#33BFFF', 'text' => '#0c4a6e'],
                            'usdt_ton' => ['label' => 'USDT (TON)', 'ticker' => 'USDT', 'symbol' => '₮', 'from' => '#26A17B', 'to' => '#4DBFA0', 'text' => '#064E3B'],
                            'dash' => ['label' => 'Dash', 'ticker' => 'DASH', 'symbol' => 'D', 'from' => '#1c75bc', 'to' => '#4DA3E0', 'text' => '#1e3a5f'],
                        ];
                        $meta = $networkMeta[$address->network] ?? ['label' => strtoupper($address->network), 'ticker' => '?', 'symbol' => '?', 'from' => '#aaa', 'to' => '#ccc', 'text' => '#333'];
                        $displayAmount = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');
                        $explorerLinks = [
                            'bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/' . $address->address,
                            'ethereum' => 'https://etherscan.io/address/' . $address->address,
                            'ton' => 'https://tonviewer.com/' . $address->address,
                            'usdt_ton' => 'https://tonviewer.com/' . $address->address,
                            'dash' => 'https://insight.dash.org/insight/address/' . $address->address,
                        ];
                        $explorerLink = $explorerLinks[$address->network] ?? '#';
                    @endphp

                    <div class="net-card">
                        {{-- Card header gradient --}}
                        <div class="flex items-center justify-between px-5 py-3"
                            style="background: linear-gradient(135deg, {{ $meta['from'] }}18, {{ $meta['to'] }}12);">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-[17px]"
                                    style="background: linear-gradient(135deg, {{ $meta['from'] }}, {{ $meta['to'] }});">
                                    {{ $meta['symbol'] }}
                                </div>
                                <div>
                                    <div class="text-[14px] font-bold text-zinc-900">{{ $meta['label'] }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $meta['ticker'] }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($address->isVerified())
                                    <span class="net-badge verified-badge">
                                        <span class="icon-checkmark text-[9px]"></span> Верифицирован
                                    </span>
                                @else
                                    <span class="net-badge unverified-badge">Не верифицирован</span>
                                @endif
                            </div>
                        </div>

                        {{-- Address row --}}
                        <div class="px-5 py-3 border-t border-zinc-50 flex items-center justify-between gap-3">
                            <span class="text-[12px] font-mono text-zinc-500 truncate flex-1">{{ $address->address }}</span>
                            <button onclick="copyToClipboard('{{ $address->address }}', this)"
                                class="shrink-0 text-[11px] text-violet-600 font-bold bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl active:scale-95 transition-all">
                                <span>Копировать</span>
                            </button>
                        </div>

                        {{-- Balance row --}}
                        <div class="px-5 py-3 border-t border-zinc-50 flex items-end justify-between">
                            <div>
                                <div class="text-[11px] text-zinc-400 uppercase tracking-wider mb-0.5">Баланс</div>
                                <div class="text-[22px] font-bold font-mono text-zinc-900 leading-none">
                                    {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                    <span class="text-[13px] text-zinc-400 font-semibold">{{ $meta['ticker'] }}</span>
                                </div>
                            </div>
                            @if($address->last_sync_at)
                                <div class="text-[11px] text-zinc-300">{{ $address->last_sync_at->diffForHumans() }}</div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="px-5 py-3 border-t border-zinc-100 flex items-center gap-5 bg-zinc-50/50">
                            <a href="{{ route('shop.customers.account.crypto.sync', $address->id) }}"
                                class="text-[13px] text-violet-600 font-semibold active:opacity-50 transition-all">
                                ↻ Обновить
                            </a>
                            <a href="{{ $explorerLink }}" target="_blank"
                                class="text-[13px] text-zinc-400 font-semibold active:opacity-50 transition-all">
                                ↗ Эксплорер
                            </a>
                            <button
                                onclick="showVerifyModal('{{ $address->id }}', '{{ $address->network }}', '{{ $displayAmount }}', '{{ $address->address }}')"
                                class="text-[13px] text-emerald-600 font-semibold active:opacity-50 transition-all">
                                ✓ Верифицировать
                            </button>
                            <form action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST"
                                class="ml-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Удалить этот адрес?')"
                                    class="text-[13px] text-red-400 font-semibold active:opacity-50 transition-all">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <p class="px-4 py-4 text-[12px] text-zinc-300 text-center leading-tight">
                Балансы синхронизируются автоматически. Депозиты появляются в течение нескольких минут.
            </p>
        </div>
</x-shop::layouts.account>