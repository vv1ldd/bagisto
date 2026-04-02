<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Подтверждение SBP
    </x-slot>

    <v-sbp-confirm 
        :order="{{ json_encode($order) }}"
        :is-test-mode="{{ $is_test_mode ? 'true' : 'false' }}"
    ></v-sbp-confirm>

    @pushOnce('scripts')
    <script type="text/x-template" id="v-sbp-confirm-template">
        <div class="flex min-h-screen flex-col items-center justify-center bg-[#fdf4ff] px-4 py-12 relative overflow-hidden">
            <!-- Background Decorations -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute top-10 left-10 w-64 h-64 border-8 border-black rotate-12"></div>
                <div class="absolute bottom-10 right-10 w-80 h-80 border-8 border-[#7C45F5] -rotate-6"></div>
            </div>

            <div class="mb-12 relative z-10">
                <span class="text-4xl font-black tracking-tighter text-[#7C45F5] drop-shadow-[4px_4px_0px_rgba(0,0,0,1)] uppercase italic">
                    {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
                </span>
            </div>

            <div class="w-full max-w-[450px] bg-white border-[6px] border-black p-8 shadow-[16px_16px_0px_0px_rgba(0,0,0,1)] relative z-10">
                <div class="mb-8 flex items-center justify-between border-b-4 border-black pb-4">
                    <h2 class="text-3xl font-black uppercase italic tracking-tighter">Оплата SBP</h2>
                    <span class="bg-black text-white px-3 py-1 font-bold text-sm tracking-widest italic">#@{{ order.increment_id }}</span>
                </div>

                <div class="space-y-8">
                    <!-- Unified Status Section -->
                    <div v-if="!isReady" class="bg-zinc-100 border-4 border-black p-6 relative overflow-hidden group">
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="animate-spin rounded-full h-10 w-10 border-4 border-black border-t-transparent transition-colors" :class="{ 'border-[#7C45F5]': paymentReceived }"></div>
                            <div>
                                <p class="text-xs font-black uppercase text-zinc-400">Статус</p>
                                <p v-if="mintingError" class="text-xl font-black uppercase tracking-tight italic text-red-600">Ошибка минтинга! ❌</p>
                                <p v-else-if="isMinting" class="text-xl font-black uppercase tracking-tight italic text-[#7C45F5] animate-pulse">Минтинг монет... ⛓️</p>
                                <p v-else-if="paymentReceived && !isReady" class="text-xl font-black uppercase tracking-tight italic text-[#7C45F5]">Подтверждение сети... ⏳</p>
                                <p v-else class="text-xl font-black uppercase tracking-tight italic">Ожидание оплаты...</p>
                            </div>
                        </div>
                        <div v-if="mintingError" class="mt-4">
                            <button @click="performMintBase" class="w-full py-2 bg-red-100 border-2 border-red-600 text-red-600 font-bold uppercase text-[10px] hover:bg-red-200 transition-colors">
                                Повторить попытку минтинга
                            </button>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-[#7C45F5] transition-all duration-700" :style="{ width: paymentReceived ? '70%' : '20%' }"></div>
                    </div>

                    <!-- Ready Status -->
                    <div v-else class="bg-green-400 border-4 border-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] -rotate-1 transition-transform">
                        <div class="flex items-center gap-4">
                            <div class="bg-white border-2 border-black p-2">
                                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase text-zinc-800">Готово к подтверждению</p>
                                <p class="text-xl font-black uppercase italic tracking-tight">Платеж получен!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Passkey Action -->
                    <div class="pt-4">
                        <button 
                            @click="confirmWithPasskey"
                            :disabled="!isReady || isFinishing"
                            class="group relative w-full overflow-hidden border-4 border-black bg-[#7C45F5] px-8 py-5 text-2xl font-black uppercase italic tracking-widest text-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-black hover:text-white active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale"
                        >
                            <span v-if="isFinishing" class="flex items-center justify-center gap-3">
                                <div class="animate-spin rounded-full h-6 w-6 border-2 border-white border-t-transparent"></div>
                                Финализация...
                            </span>
                            <span v-else>Подтвердить 🤳</span>
                        </button>
                    </div>

                    <p class="text-center text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                        Безопасная транзакция через Web3 Passkey
                    </p>
                </div>

                <!-- Debug Overlay (Mini) -->
                <div class="mt-4 flex justify-center gap-4 border-t border-zinc-100 pt-2 text-[8px] font-black uppercase tracking-tighter text-zinc-300">
                    <span>Paid: @{{ paymentReceived ? 'YES' : 'NO' }}</span>
                    <span>Ready: @{{ isReady ? 'YES' : 'NO' }}</span>
                </div>

                <!-- Simulation Tool (QR Code) -->
                <div v-if="isTestMode && !paymentReceived" class="mt-8 pt-6 border-t-2 border-dashed border-zinc-200 text-center">
                    <p class="text-[10px] font-bold uppercase text-zinc-400 mb-4 tracking-widest">Dev Simulation / Scan to Pay</p>
                    
                    <div class="inline-block p-4 bg-white border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-4">
                        <img :src="qrImageUrl" alt="Scan to simulate" class="w-[160px] h-[160px]">
                    </div>

                    <p class="text-[11px] text-zinc-500 mb-4 px-4 leading-relaxed font-bold italic">
                        Отсканируйте код телефоном,<br>
                        чтобы имитировать сигнал банка.
                    </p>

                    <button 
                        @click="simulatePayment"
                        class="w-full py-2 bg-[#F6E05E] border-2 border-black font-black text-[10px] uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[1px] active:translate-y-[1px] transition-all hover:bg-[#ecc94b]"
                        :disabled="isSimulating"
                    >
                        <span v-if="!isSimulating">[ FORCE SIMULATION ]</span>
                        <span v-else>PROCESSING...</span>
                    </button>
                </div>
            </div>

            <!-- Debug Hidden -->
            <div class="mt-8 text-[10px] text-zinc-400 font-mono opacity-20 hover:opacity-100 transition-opacity">
                TX: @{{ txBase || 'pending...' }}
            </div>
        </div>
    </script>

    @php
        $additional = $order->payment->additional ?? [];
        if (is_string($additional)) {
            $additional = json_decode($additional, true) ?? [];
        }
    @endphp

    <script type="module">
        app.component('v-sbp-confirm', {
            template: '#v-sbp-confirm-template',
            data() {
                return {
                    paymentReceived: @json($additional['sbp_payment_received'] ?? false),
                    isReady: @json($additional['is_ready_for_passkey'] ?? false),
                    isFinishing: false,
                    isSimulating: false,
                    isMinting: false,
                    mintingError: false,
                    txBase: @json($additional['mint_tx_base'] ?? null),
                    pollInterval: null,
                    csrfToken: '{{ csrf_token() }}',
                    callbackUrl: '{{ route('shop.checkout.sbp.callback', $order->id) }}',
                    qrImageUrl: `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent('{{ route('shop.checkout.sbp.callback', $order->id) }}')}`,
                    passkeyOptions: @json($passkeyOptions ?? null)
                }
            },
            props: ['order', 'isTestMode'],
            mounted() {
                console.log('SBP Component Mounted. Order:', this.order.id);
                this.fetchStatus();
                this.pollInterval = setInterval(this.fetchStatus, 3000);
            },
            beforeUnmount() {
                clearInterval(this.pollInterval);
            },
            methods: {
                async simulatePayment() {
                    console.log('simulatePayment called');
                    if (this.isSimulating) return;
                    this.isSimulating = true;
                    
                    try {
                        console.log('Requesting callback simulation...');
                        await axios.get(`${window.location.origin}/checkout/sbp/callback/${this.order.id}`);
                        console.log('Callback simulation successful');
                        await this.fetchStatus();
                    } catch (err) {
                        console.error('Simulation error:', err);
                    } finally {
                        this.isSimulating = false;
                    }
                },
                async performMintBase() {
                    if (this.isMinting) return;
                    console.log('Starting Minting 1:1 body...');
                    this.isMinting = true;
                    this.mintingError = false;

                    try {
                        const response = await axios.post(`${window.location.origin}/checkout/sbp/mint-base/${this.order.id}`, {}, {
                            headers: { 'X-CSRF-TOKEN': this.csrfToken }
                        });
                        console.log('Minting response:', response.data);
                        if (response.data.success) {
                            this.txBase = response.data.tx;
                            // Wait for 5 seconds to allow blockchain confirmation before next status fetch
                            setTimeout(() => {
                                this.fetchStatus();
                            }, 5000);
                        } else {
                            throw new Error(response.data.message || 'Minting failed');
                        }
                    } catch (err) {
                        console.error('Minting error:', err);
                        this.mintingError = true;
                        
                        // Show error to user via flash if possible
                        if (window.addFlash) {
                            window.addFlash({ type: 'error', message: `Ошибка минтинга: ${err.message}` });
                        }
                    } finally {
                        this.isMinting = false;
                    }
                },
                async fetchStatus() {
                    try {
                        const response = await axios.get(`${window.location.origin}/checkout/sbp/status/${this.order.id}`);
                        if (response.data.success) {
                            this.paymentReceived = response.data.received;
                            this.isReady = response.data.is_ready;
                            this.txBase = response.data.tx_base;
                            
                            console.log('Status update:', {
                                paid: this.paymentReceived,
                                ready: this.isReady,
                                tx: this.txBase
                            });

                            if (this.paymentReceived && !this.isReady && !this.isMinting && !this.txBase) {
                                this.performMintBase();
                            }

                            if (this.isReady) {
                                console.log('Ready for Passkey. Stopping poll.');
                                clearInterval(this.pollInterval);
                            }
                        }
                    } catch (err) {
                        console.error('Polling error:', err);
                    }
                },
                async confirmWithPasskey() {
                    if (this.isFinishing) return;
                    this.isFinishing = true;

                    try {
                        if (!this.passkeyOptions) {
                            throw new Error('Опции Passkey не загружены. Обновите страницу.');
                        }

                        // Helper to convert base64 (Spatie format) to Uint8Array for navigator.credentials
                        const base64ToUint8Array = (base64) => {
                            if (!base64) return new Uint8Array(0).buffer;
                            const binaryString = window.atob(base64.replace(/-/g, '+').replace(/_/g, '/'));
                            const len = binaryString.length;
                            const bytes = new Uint8Array(len);
                            for (let i = 0; i < len; i++) {
                                bytes[i] = binaryString.charCodeAt(i);
                            }
                            return bytes.buffer;
                        };

                        console.log('Passkey: Raw options from server:', this.passkeyOptions);
                        
                        // Spatie's loginOptions usually returns the options directly at the root,
                        // while some registrations wrap them in a 'publicKey' property.
                        const rawOptions = this.passkeyOptions.publicKey ? this.passkeyOptions.publicKey : this.passkeyOptions;

                        // Prepare WebAuthn options for navigator.credentials.get()
                        const options = {
                            publicKey: {
                                ...rawOptions,
                                challenge: base64ToUint8Array(rawOptions.challenge),
                                allowCredentials: (rawOptions.allowCredentials || []).map(cred => ({
                                    ...cred,
                                    id: base64ToUint8Array(cred.id)
                                }))
                            }
                        };

                        console.log('Requesting Passkey signature...', options);
                        const assertion = await navigator.credentials.get(options);
                        
                        // 2. Send the full assertion to server for Web3 relay
                        const finishResponse = await axios.post(`${window.location.origin}/checkout/sbp/finish/${this.order.id}`, {
                            passkey_assertion: {
                                id: assertion.id,
                                rawId: btoa(String.fromCharCode.apply(null, new Uint8Array(assertion.rawId))),
                                response: {
                                    authenticatorData: btoa(String.fromCharCode.apply(null, new Uint8Array(assertion.response.authenticatorData))),
                                    clientDataJSON: btoa(String.fromCharCode.apply(null, new Uint8Array(assertion.response.clientDataJSON))),
                                    signature: btoa(String.fromCharCode.apply(null, new Uint8Array(assertion.response.signature))),
                                    userHandle: assertion.response.userHandle ? btoa(String.fromCharCode.apply(null, new Uint8Array(assertion.response.userHandle))) : null
                                },
                                type: assertion.type
                            }
                        }, {
                            headers: { 'X-CSRF-TOKEN': this.csrfToken }
                        });

                        if (finishResponse.data.success) {
                            window.location.href = `${window.location.origin}/checkout/onepage/success`;
                        } else {
                            throw new Error(finishResponse.data.message || 'Verification failed');
                        }
                    } catch (err) {
                        console.error('Passkey Error:', err);
                        this.isFinishing = false;
                        
                        let displayMsg = err.message || 'Верификация не удалась';
                        if (err.name === 'NotAllowedError') displayMsg = 'Операция отменена пользователем.';
                        
                        if (window.addFlash) {
                            window.addFlash({ type: 'error', message: `Ошибка: ${displayMsg}` });
                        } else {
                             alert(`Ошибка: ${displayMsg}`);
                        }
                    }
                }
            }
        });
    </script>
    @endpushOnce
</x-shop::layouts>
