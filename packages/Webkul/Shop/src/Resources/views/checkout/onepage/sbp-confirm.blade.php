<x-shop::layouts :has-feature="false">
    <x-slot:title>Подтверждение оплаты СБП</x-slot>

    <div class="bg-[#F8FAF9] min-h-screen py-12" id="v-sbp-confirm">
        <v-sbp-confirm :order='@json($order)'></v-sbp-confirm>
    </div>

    @push('scripts')
        <script type="text/x-template" id="v-sbp-confirm-template">
            <div class="max-w-[600px] mx-auto px-4">
                <!-- Main Card -->
                <div class="bg-white border-4 border-zinc-900 p-8 md:p-12 shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] relative overflow-hidden">
                    
                    <!-- Top Ribbon -->
                    <div class="absolute top-0 left-0 w-full h-2 bg-[#7C45F5]"></div>

                    <!-- Header -->
                    <div class="mb-10 text-center">
                        <h1 class="text-3xl font-black uppercase tracking-tighter text-zinc-900 mb-2">
                            Заказ #@{{ order.increment_id }}
                        </h1>
                        <p class="text-zinc-500 font-bold uppercase tracking-widest text-xs">Ожидание подтверждения СБП</p>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-zinc-50 border-4 border-zinc-900 p-6 mb-10 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b-2 border-zinc-200">
                            <span class="font-black uppercase text-sm text-zinc-400">Сумма к оплате</span>
                            <span class="text-2xl font-black">@{{ parseFloat(order.grand_total).toFixed(2) }} ₽</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-black uppercase text-sm text-zinc-400">Начислим бонусов</span>
                            <span class="text-xl font-black text-[#7C45F5]">+@{{ (order.grand_total * 0.05).toFixed(2) }} MC</span>
                        </div>
                    </div>

                    <!-- States -->
                    <div class="space-y-8">
                        <!-- State 1: Waiting for Payment -->
                        <div v-if="state === 'waiting'" class="text-center py-10">
                            <div class="flex justify-center mb-8">
                                <div class="relative w-24 h-24">
                                    <div class="absolute inset-0 border-8 border-zinc-200 rounded-full"></div>
                                    <div class="absolute inset-0 border-8 border-[#7C45F5] rounded-full border-t-transparent animate-spin"></div>
                                </div>
                            </div>
                            <p class="text-lg font-black uppercase mb-4">Ожидаем сигнал от вашего банка...</p>
                            
                            <!-- Simulation Button (Testing only) -->
                            <button 
                                @click="simulateSuccess"
                                class="mt-4 px-6 py-2 border-2 border-dashed border-zinc-400 text-zinc-400 font-bold hover:border-zinc-900 hover:text-zinc-900 transition-all text-xs uppercase"
                            >
                                Симулировать успех оплаты (Тест)
                            </button>
                        </div>

                        <!-- State 2: Success & Minting -->
                        <div v-if="state === 'minting'" class="text-center py-10">
                            <div class="flex justify-center mb-8 gap-4">
                                <div class="w-16 h-16 bg-[#22C55E] border-4 border-zinc-900 flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="animate-bounce w-16 h-16 bg-white border-4 border-zinc-900 flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                    <span class="text-2xl font-black">MC</span>
                                </div>
                            </div>
                            <p class="text-xl font-black uppercase mb-2">Оплата получена!</p>
                            <p class="text-zinc-500 font-bold uppercase text-sm tracking-widest">Происходит зачисление монет в блокчейн...</p>
                        </div>

                        <!-- State 3: Ready to Finish -->
                        <div v-if="state === 'ready'" class="space-y-6">
                             <div class="bg-green-50 border-4 border-[#22C55E] p-4 flex items-center gap-4 shadow-[4px_4px_0px_0px_rgba(34,197,94,0.2)]">
                                <span class="font-black text-green-700">✓ Монеты зачислены на ваш баланс</span>
                             </div>

                             <button 
                                @click="finishWithPasskey"
                                :disabled="isFinishing"
                                class="w-full bg-[#7C45F5] border-4 border-zinc-900 py-6 text-xl font-black uppercase tracking-widest text-white shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all flex items-center justify-center gap-4 disabled:opacity-50"
                            >
                                <span v-if="!isFinishing">Подтвердить заказ через Passkey</span>
                                <svg v-else class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                             </button>

                             <p class="text-center text-[11px] font-bold text-zinc-400 uppercase leading-relaxed tracking-wider">
                                Подписание транзакции списания MC <br/> подтверждает завершение сделки
                             </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="mt-8 flex justify-center gap-6">
                    <a href="#" class="text-zinc-400 font-black uppercase text-[10px] tracking-widest hover:text-zinc-900 transition-colors">Помощь</a>
                    <a href="#" class="text-zinc-400 font-black uppercase text-[10px] tracking-widest hover:text-zinc-900 transition-colors">Правила СБП</a>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-sbp-confirm', {
                template: '#v-sbp-confirm-template',
                props: ['order'],
                data() {
                    return {
                        state: 'waiting', // waiting, minting, ready
                        isFinishing: false,
                        pollInterval: null
                    }
                },
                mounted() {
                    // Check initial state
                    if (this.order.additional && this.order.additional.sbp_payment_received) {
                        this.state = 'ready';
                    } else {
                        this.startPolling();
                    }
                },
                methods: {
                    startPolling() {
                        this.pollInterval = setInterval(async () => {
                            // Poll order status (simplified: just fetch order again via repository/controller)
                            // For this demo, we'll rely on the simulation button.
                        }, 5000);
                    },
                    
                    async simulateSuccess() {
                        this.state = 'minting';
                        try {
                            const response = await this.$axios.get(`/checkout/sbp/callback/${this.order.id}`);
                            if (response.data.success) {
                                setTimeout(() => {
                                    this.state = 'ready';
                                }, 2000);
                            }
                        } catch (e) {
                            this.state = 'waiting';
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Ошибка при симуляции оплаты' });
                        }
                    },

                    async finishWithPasskey() {
                        this.isFinishing = true;
                        try {
                            // WebAuthn Login Options
                            const optionsRes = await this.$axios.post('{{ route('passkeys.login-options') }}');
                            const options = optionsRes.data;

                            if (options.challenge) {
                                options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
                            }
                            if (options.allowCredentials) {
                                options.allowCredentials = options.allowCredentials.map(c => ({
                                    ...c,
                                    id: Uint8Array.from(atob(c.id.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0))
                                }));
                            }

                            const credential = await navigator.credentials.get({ publicKey: options });

                            const credentialJson = {
                                id: credential.id,
                                rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                type: credential.type,
                                response: {
                                    authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                    clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                    signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                    userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '') : null
                                }
                            };

                            // Send finalization request
                            const finishRes = await this.$axios.post(`/checkout/sbp/finish/${this.order.id}`, {
                                passkey_assertion: credentialJson
                            });

                            if (finishRes.data.success) {
                                window.location.href = '{{ route('shop.checkout.onepage.success') }}';
                            }
                        } catch (e) {
                            console.error('Finalization error:', e);
                            this.isFinishing = false;
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Не удалось завершить заказ. Попробуйте еще раз.' });
                        }
                    }
                }
            });
        </script>
    @endpush
</x-shop::layouts>
