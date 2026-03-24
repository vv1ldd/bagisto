<x-admin::layouts>
    <x-slot:title>
        Web3 Горячий Кошелек
    </x-slot>

    <!-- Header Section -->
    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
                Web3 управление
            </p>

            <p class="!leading-normal text-gray-600 dark:text-gray-300">
                Управление горячим кошельком, балансом газа и смарт-контрактами
            </p>
        </div>
    </div>

    <!-- Body Component -->
    <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
        
        <!-- Hot Wallet Widget -->
        @php
            $hotWalletAddress = env('ADMIN_ETH_PUBLIC_ADDRESS', env('ADMIN_ETH_PRIVATE_KEY') ? '0xB1ABfEab7E90B8565F715871f8a0fF1B9FD9F9AA' : null);
            $contractAddress  = env('MINT_CONTRACT_ADDRESS', null);
            $alchemyUrl       = env('ALCHEMY_RPC_URL', null);
        @endphp

        <div class="flex w-full max-w-[500px] flex-col gap-2">
            <div class="bg-white rounded box-shadow dark:bg-gray-900 overflow-hidden">
                {{-- Header --}}
                <div class="px-5 py-6 border-b dark:border-gray-800" style="background: linear-gradient(135deg, #1a0050 0%, #7C45F5 100%);">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-2xl">⚡</div>
                        <div>
                            <p class="text-xs font-bold text-white/60 uppercase tracking-widest">Meanly Admin Hot Wallet</p>
                            <p class="text-lg font-black text-white mt-1">Arbitrum One / Mainnet</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Address --}}
                    <div>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Адрес кошелька (Admin)</p>
                        @if($hotWalletAddress)
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3">
                                <code class="text-sm font-mono text-[#7C45F5] truncate flex-1" title="{{ $hotWalletAddress }}">{{ $hotWalletAddress }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $hotWalletAddress }}'); this.textContent='Скопировано ✓'" class="text-xs font-bold text-gray-400 hover:text-[#7C45F5] transition-colors shrink-0">Скопировать</button>
                            </div>
                            <a href="https://arbiscan.io/address/{{ $hotWalletAddress }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs text-[#7C45F5] hover:underline mt-2 font-bold">
                                <span>Посмотреть на Arbiscan →</span>
                            </a>
                        @else
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 rounded-lg px-5 py-4">
                                <p class="text-xs font-bold text-amber-700">⚠ Добавьте адрес в .env:</p>
                                <code class="text-[11px] text-amber-600">ADMIN_ETH_PUBLIC_ADDRESS=0x...</code>
                            </div>
                        @endif
                    </div>

                    {{-- Balance Refresh Widget (AJAX via Alchemy) --}}
                    <div>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">ETH Баланс (Газ)</p>
                        <div id="hw-balance-block" class="bg-gray-50 dark:bg-gray-800 rounded-lg px-5 py-4 flex items-center justify-between">
                            <span id="hw-balance" class="text-3xl font-black text-[#1a0050] dark:text-white">—</span>
                            <button onclick="loadHotWalletBalance()" class="text-xs font-bold bg-[#7C45F5]/10 text-[#7C45F5] px-3 py-1.5 rounded-lg hover:bg-[#7C45F5]/20 transition-colors">Обновить</button>
                        </div>
                    </div>

                    {{-- Contract Address --}}
                    <div>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Смарт-Контракт NFT</p>
                        @if($contractAddress)
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3">
                                <code class="text-sm font-mono text-green-600 truncate flex-1" title="{{ $contractAddress }}">{{ $contractAddress }}</code>
                            </div>
                            <a href="https://arbiscan.io/address/{{ $contractAddress }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs text-green-600 hover:underline mt-2 font-bold">
                                <span>Посмотреть контракт на Arbiscan →</span>
                            </a>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 rounded-lg px-5 py-4">
                                <p class="text-xs font-bold text-red-600">✗ Не задан</p>
                                <code class="text-[11px] text-red-500">MINT_CONTRACT_ADDRESS= в .env</code>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <!-- Send Transaction Widget -->
        <div class="flex flex-1 flex-col gap-2">
            <div class="bg-white rounded box-shadow dark:bg-gray-900 overflow-hidden h-full flex flex-col">
                <div class="px-5 py-4 border-b dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <span class="icon-payment text-xl"></span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white leading-tight">Отправить Газ (ETH)</p>
                            <p class="text-xs text-gray-500">Защищено с помощью Passkey 🛡️</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col gap-5">
                    <!-- Form -->
                    <form id="sendTxForm" onsubmit="handleSendTransaction(event)" class="flex flex-col gap-4">
                        <!-- Address -->
                        <div>
                            <label for="tx_address" class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-1.5 label-required">Адрес получателя</label>
                            <input 
                                type="text" 
                                id="tx_address" 
                                name="address" 
                                required 
                                pattern="^0x[a-fA-F0-9]{40}$"
                                placeholder="0x..." 
                                class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 text-sm text-gray-600 transition-all focus:border-blue-600 focus:ring-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 font-mono"
                            >
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="tx_amount" class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-1.5 label-required">Сумма (ETH)</label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    id="tx_amount" 
                                    name="amount" 
                                    required 
                                    min="0.000001" 
                                    step="0.000001"
                                    placeholder="0.01" 
                                    class="w-full rounded-lg border bg-gray-50 pl-4 pr-12 py-2.5 text-sm text-gray-600 transition-all focus:border-blue-600 focus:ring-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">ETH</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-auto pt-2">
                            <button 
                                type="submit" 
                                id="sendTxBtn"
                                class="w-full flex items-center justify-center gap-2 cursor-pointer rounded-lg border border-blue-700 bg-blue-600 px-4 py-3 font-semibold text-white transition-all hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span class="icon-payment text-lg gap-2"></span>
                                <span>Подтвердить через Passkey</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <!-- SimpleWebAuthn Browser Script -->
    <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

    <script>
        async function handleSendTransaction(e) {
            e.preventDefault();

            const form = document.getElementById('sendTxForm');
            const button = document.getElementById('sendTxBtn');
            const address = document.getElementById('tx_address').value;
            const amount = document.getElementById('tx_amount').value;
            const originalBtnContent = button.innerHTML;
            const SimpleWebAuthn = window.SimpleWebAuthnBrowser;

            if (!SimpleWebAuthn) {
                app.config.globalProperties.$emitter.emit('add-flash', { type: 'error', message: 'WebAuthn library missing.' });
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="icon-settings animate-spin text-lg"></span><span>Подготовка...</span>';

            try {
                // 1. Get Passkey Options Challenge from server
                const optRes = await fetch('{{ route('admin.passkey.login_options') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });

                if (!optRes.ok) throw new Error('Failed to get authentication options.');

                const rawOptions = await optRes.json();
                const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                // 2. Trigger OS Passkey prompt (FaceID/TouchID/etc)
                button.innerHTML = '<span class="icon-settings animate-spin text-lg"></span><span>Ожидание пользователя...</span>';
                const asseResp = await SimpleWebAuthn.startAuthentication(options);

                // 3. Send securely to new Web3 endpoint
                button.innerHTML = '<span class="icon-settings animate-spin text-lg"></span><span>Отправка транзакции...</span>';
                
                const txRes = await fetch('{{ route('admin.web3.transaction.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        address: address,
                        amount: amount,
                        start_authentication_response: JSON.stringify(asseResp)
                    })
                });

                const result = await txRes.json();

                if (txRes.ok) {
                    app.config.globalProperties.$emitter.emit('add-flash', { type: 'success', message: result.message });
                    form.reset();
                    if (result.explorer_url) {
                        window.open(result.explorer_url, '_blank');
                    }
                    if (typeof loadHotWalletBalance === 'function') {
                        setTimeout(loadHotWalletBalance, 2000); // refresh balance slightly later
                    }
                } else {
                    throw new Error(result.message || 'Transaction failed.');
                }
            } catch (err) {
                console.error('[Web3 Tx]', err);
                if (err.name !== 'AbortError' && err.name !== 'NotAllowedError' && !err.message.includes('cancelled')) {
                    app.config.globalProperties.$emitter.emit('add-flash', { type: 'error', message: err.message });
                } else {
                    app.config.globalProperties.$emitter.emit('add-flash', { type: 'info', message: 'Авторизация отменена.' });
                }
            } finally {
                button.disabled = false;
                button.innerHTML = originalBtnContent;
            }
        }
    </script>
    @endpush

    @if($hotWalletAddress && $alchemyUrl)
    @push('scripts')
    <script>
        async function loadHotWalletBalance() {
            const el = document.getElementById('hw-balance');
            el.textContent = 'Загрузка...';
            try {
                const res = await fetch('{{ $alchemyUrl }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        jsonrpc: '2.0', method: 'eth_getBalance',
                        params: ['{{ $hotWalletAddress }}', 'latest'], id: 1
                    })
                });
                const data = await res.json();
                const wei = parseInt(data.result, 16);
                const eth = (wei / 1e18).toFixed(6);
                el.textContent = eth + ' ETH';
                el.className = 'text-3xl font-black ' + (wei < 1e15 ? 'text-red-500' : 'text-[#1a0050] dark:text-white');
            } catch(e) {
                el.textContent = 'Ошибка';
            }
        }
        // Load on page init
        document.addEventListener('DOMContentLoaded', loadHotWalletBalance);
    </script>
    @endpush
    @endif
</x-admin::layouts>
