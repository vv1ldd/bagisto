<x-shop::layouts :has-feature="false">
    <x-slot:title>Подтверждение оплаты СБП</x-slot:title>

    <div class="bg-[#F8FAF9] min-h-screen py-12" id="v-sbp-confirm">
        <v-sbp-confirm :order='@json($order)'></v-sbp-confirm>
    </div>

    @push('scripts')
        <script type="text/x-template" id="v-sbp-confirm-template">
            <div class="max-w-[700px] mx-auto px-4">
                <!-- Main Container -->
                <div class="bg-white border-[6px] border-zinc-900 p-8 md:p-14 shadow-[20px_20px_0px_0px_rgba(124,69,245,1)] relative overflow-hidden transition-all duration-500">
                    
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#7C45F5] translate-x-16 -translate-y-16 rotate-45 z-10"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-yellow-400 -translate-x-12 translate-y-12 rotate-12 z-10 border-4 border-zinc-900"></div>

                    <!-- Header -->
                    <div class="relative z-20 mb-12">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="bg-zinc-900 text-white px-3 py-1 font-black uppercase text-xs tracking-widest">SBP v2.0</span>
                            <div class="h-[2px] flex-grow bg-zinc-200"></div>
                        </div>
                        <h1 class="text-5xl font-black uppercase tracking-tighter text-zinc-900 leading-none">
                            Заказ #@{{ order.increment_id }}
                        </h1>
                    </div>

                    <!-- Amount Summary Container -->
                    <div class="relative z-20 grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                        <!-- Left: RUB -->
                        <div class="bg-[#F8FAF9] border-4 border-zinc-900 p-6 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                            <p class="font-black uppercase text-xs text-zinc-400 mb-2 tracking-widest">Сумма оплаты</p>
                            <p class="text-4xl font-black">@{{ parseFloat(order.grand_total).toLocaleString() }} <span class="text-xl italic">₽</span></p>
                        </div>
                        <!-- Right: MC Bonus -->
                        <div class="bg-zinc-900 border-4 border-zinc-900 p-6 shadow-[8px_8px_0px_0px_rgba(124,69,245,1)] text-white group overflow-hidden relative">
                            <div class="absolute inset-0 bg-[#7C45F5] translate-y-full group-hover:translate-y-0 transition-transform duration-300 z-0"></div>
                            <div class="relative z-10">
                                <p class="font-black uppercase text-xs text-zinc-500 mb-2 tracking-widest">Кэшбэк СБП (+5%)</p>
                                <p class="text-4xl font-black text-yellow-400">+@{{ (order.grand_total * 0.05).toFixed(2) }} <span class="text-xl font-normal opacity-50 italic text-white">MC</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Process Stages -->
                    <div class="relative z-20 space-y-4 mb-14">
                        <!-- Stage 1: RUB Receipt -->
                        <div :class="['flex items-center gap-6 p-5 border-4 transition-all duration-300', status.rub === 'success' ? 'bg-green-50 border-green-500 shadow-[6px_6px_0px_0px_rgba(34,197,94,1)]' : 'bg-white border-zinc-900']">
                            <div class="relative">
                                <div v-if="status.rub === 'success'" class="w-12 h-12 bg-green-500 border-4 border-zinc-900 flex items-center justify-center text-white font-black">✓</div>
                                <div v-else class="w-12 h-12 bg-zinc-100 border-4 border-zinc-900 flex items-center justify-center animate-pulse font-black italic">₽</div>
                            </div>
                            <div class="flex-grow">
                                <p class="font-black uppercase text-sm">Прием платежа через СБП</p>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-tighter" v-if="status.rub === 'success'">Платеж подтвержден банком</p>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-tighter" v-else>Ожидание сигнала от эквайринга...</p>
                            </div>
                        </div>

                        <!-- Stage 2: MC Minting (1:1) -->
                        <div :class="['flex items-center gap-6 p-5 border-4 transition-all duration-300', status.mint === 'success' ? 'bg-green-50 border-green-500 shadow-[6px_6px_0px_0px_rgba(34,197,94,1)]' : status.rub === 'success' ? 'bg-white border-zinc-900' : 'bg-zinc-50 border-zinc-200 opacity-50 text-zinc-400']">
                            <div class="relative">
                                <div v-if="status.mint === 'success'" class="w-12 h-12 bg-green-500 border-4 border-zinc-900 flex items-center justify-center text-white font-black">✓</div>
                                <div v-else-if="status.rub === 'success'" class="w-12 h-12 bg-[#7C45F5] border-4 border-zinc-900 flex items-center justify-center text-white animate-spin">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg>
                                </div>
                                <div v-else class="w-12 h-12 bg-zinc-100 border-4 border-zinc-300 flex items-center justify-center font-black italic">MC</div>
                            </div>
                            <div class="flex-grow">
                                <p class="font-black uppercase text-sm">Зачисление Meanly Coins (1:1)</p>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-tighter truncate max-w-[300px]" v-if="status.mint === 'success'">Tx @{{ tx.base }}</p>
                            </div>
                        </div>

                        <!-- Stage 3: Bonus Minting (5%) -->
                        <div :class="['flex items-center gap-6 p-5 border-4 transition-all duration-300', status.bonus === 'success' ? 'bg-yellow-50 border-yellow-500 shadow-[6px_6px_0px_0px_rgba(234,179,8,1)]' : status.mint === 'success' ? 'bg-white border-zinc-900' : 'bg-zinc-50 border-zinc-200 opacity-50 text-zinc-400']">
                            <div class="relative">
                                <div v-if="status.bonus === 'success'" class="w-12 h-12 bg-yellow-400 border-4 border-zinc-900 flex items-center justify-center text-zinc-900 font-black">✓</div>
                                <div v-else-if="status.mint === 'success'" class="w-12 h-12 bg-yellow-400 border-4 border-zinc-900 flex items-center justify-center text-zinc-900 animate-bounce font-black text-xl">+</div>
                                <div v-else class="w-12 h-12 bg-zinc-100 border-4 border-zinc-300 flex items-center justify-center font-black italic">%</div>
                            </div>
                            <div class="flex-grow">
                                <p class="font-black uppercase text-sm">Начисление кэшбэка (+5%)</p>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-tighter truncate max-w-[300px]" v-if="status.bonus === 'success'">Tx @{{ tx.bonus }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button Container -->
                    <div class="relative z-20">
                        <!-- Simulation Button (Testing) -->
                        <button 
                            v-if="status.rub === 'waiting'"
                            @click="triggerSimulation"
                            class="mb-6 w-full py-4 border-4 border-dashed border-zinc-300 text-zinc-400 font-black uppercase tracking-widest hover:border-zinc-900 hover:text-zinc-900 hover:bg-zinc-50 transition-all text-sm group"
                        >
                            <span class="inline-block group-hover:animate-bounce mr-2">👉</span> Учебная Симуляция Оплаты СБП
                        </button>

                        <!-- Final Passkey Button -->
                        <button 
                            @click="confirmWithPasskey"
                            :disabled="status.bonus !== 'success' || isFinishing"
                            :class="['w-full py-8 text-2xl font-black uppercase tracking-[0.2em] border-[6px] border-zinc-900 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] transition-all flex items-center justify-center gap-4 active:translate-x-1 active:translate-y-1 active:shadow-none', status.bonus === 'success' ? 'bg-[#7C45F5] text-white hover:bg-zinc-900' : 'bg-zinc-100 text-zinc-300 cursor-not-allowed opacity-50']"
                        >
                            <span v-if="!isFinishing">Подтвердить Passkey</span>
                            <svg v-else class="animate-spin h-8 w-8 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Security Badge -->
                    <div class="mt-10 flex items-center justify-center gap-4 relative z-20">
                        <svg class="w-5 h-5 text-zinc-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <p class="text-[10px] font-black italic uppercase text-zinc-400 tracking-[0.3em]">Blockchain Secure Signature Required</p>
                    </div>
                </div>

                <!-- Help Link -->
                <p class="text-center mt-12">
                    <a href="#" class="text-zinc-400 font-black uppercase text-[11px] tracking-widest hover:text-zinc-900 underline underline-offset-8">Центр поддержки пользователей Meanly</a>
                </p>
            </div>
        </script>

        <script type="module">
            app.component('v-sbp-confirm', {
                template: '#v-sbp-confirm-template',
                props: ['order'],
                data() {
                    return {
                        status: {
                            rub: 'waiting',   // waiting, success
                            mint: 'idle',      // idle, loading, success
                            bonus: 'idle'      // idle, loading, success
                        },
                        tx: {
                            base: null,
                            bonus: null
                        },
                        isFinishing: false
                    }
                },
                mounted() {
                    this.fetchStatus();
                },
                methods: {
                    async fetchStatus() {
                        try {
                            const response = await this.$axios.get(`/checkout/sbp/status/${this.order.id}`);
                            if (response.data.received) {
                                this.status.rub = 'success';
                                this.status.mint = 'success';
                                this.status.bonus = 'success';
                                this.tx.base = response.data.tx.base;
                                this.tx.bonus = response.data.tx.bonus;
                            }
                        } catch (e) {
                            console.error('Status fetch error:', e);
                        }
                    },

                    async triggerSimulation() {
                        this.status.rub = 'success';
                        
                        // Start Minting Process
                        setTimeout(async () => {
                            this.status.mint = 'loading';
                            try {
                                const response = await this.$axios.get(`/checkout/sbp/callback/${this.order.id}`);
                                if (response.data.success) {
                                    this.tx.base = response.data.tx1;
                                    this.tx.bonus = response.data.tx2;
                                    
                                    // Base Minted
                                    setTimeout(() => {
                                        this.status.mint = 'success';
                                        
                                        // Bonus Minting Kick-off
                                        setTimeout(() => {
                                            this.status.bonus = 'success';
                                            this.$emitter.emit('add-flash', { 
                                                type: 'success', 
                                                message: `Кэшбэк ${response.data.amounts.bonus} MC зачислен!` 
                                            });
                                        }, 1500);
                                    }, 2000);
                                }
                            } catch (e) {
                                this.status.rub = 'waiting';
                                this.status.mint = 'idle';
                                this.$emitter.emit('add-flash', { type: 'error', message: 'Сбой блокчейн-транзакции' });
                                console.error(e);
                            }
                        }, 1000);
                    },

                    async confirmWithPasskey() {
                        this.isFinishing = true;
                        try {
                            // 1. Get Passkey Signature Options
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

                            let credential;
                            try {
                                credential = await navigator.credentials.get({ publicKey: options });
                            } catch (err) {
                                console.warn('Browser/Hardware Biometric Error:', err);
                                this.isFinishing = false;
                                this.$emitter.emit('add-flash', { 
                                    type: 'error', 
                                    message: 'Биометрия сорвалась или была отменена. Проверьте настройки устройства.' 
                                });
                                return;
                            }

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

                            // 2. Finalize Order
                            try {
                                const finishRes = await this.$axios.post(`/checkout/sbp/finish/${this.order.id}`, {
                                    passkey_assertion: credentialJson
                                });

                                if (finishRes.data.success) {
                                    window.location.href = '{{ route('shop.checkout.onepage.success') }}';
                                }
                            } catch (serverErr) {
                                console.error('Server Verification Error:', serverErr);
                                this.isFinishing = false;
                                this.$emitter.emit('add-flash', { 
                                    type: 'error', 
                                    message: 'Ошибка верификации подписи на сервере. Попробуйте еще раз.' 
                                });
                            }
                        } catch (e) {
                            console.error('General Finalization error:', e);
                            this.isFinishing = false;
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Системная ошибка. Попробуйте позже.' });
                        }
                    }
                }
            });
        </script>
    @endpush
</x-shop::layouts>
