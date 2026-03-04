{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods :methods="paymentMethods" :selected-payment-method="selectedPaymentMethod" @processing="stepForward"
    @processed="stepProcessed" @change-payment-method="selectedPaymentMethod = $event">
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-payment-methods-template">
            <div class="mb-6 overflow-hidden">
                <template v-if="! methods">
                    <x-shop::shimmer.checkout.onepage.payment-method />
                </template>

                <template v-else>
                    {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.before') !!}

                    <div class="flex items-center justify-between mb-3 px-1">
                        <h2 class="text-sm font-black text-zinc-500 uppercase tracking-widest">
                            Способ оплаты
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div 
                            v-for="(payment, index) in methods"
                            :key="payment.method"
                            class="relative p-2.5 rounded-2xl border transition-all duration-300 group cursor-pointer flex flex-col bg-white"
                            :class="[selectedPaymentMethod == payment.method ? 'border-[#7C45F5] ring-1 ring-[#7C45F5] shadow-lg shadow-[#7C45F5]/5' : 'border-zinc-100 hover:border-zinc-200 shadow-sm']"
                            @click="handleSelection(payment)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-xl bg-zinc-50 border border-zinc-100 p-1 flex items-center justify-center shrink-0">
                                        <img :src="payment.image" class="max-h-full max-w-full object-contain" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold truncate transition-colors" :class="[selectedPaymentMethod == payment.method ? 'text-[#7C45F5]' : 'text-zinc-800']">
                                            @{{ payment.method_title }}
                                        </p>
                                    </div>
                                </div>

                                <div 
                                    class="h-4 w-4 rounded-full border flex items-center justify-center transition-all bg-zinc-50"
                                    :class="[selectedPaymentMethod == payment.method ? 'border-[#7C45F5] bg-[#7C45F5]' : 'border-zinc-300']"
                                >
                                    <div v-if="selectedPaymentMethod == payment.method" class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>

                            <!-- Meanly Wallet Compact Expansion -->
                            <div v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" class="mt-3 p-2.5 rounded-xl bg-[#7C45F5]/5 border border-[#7C45F5]/10 space-y-2 animate-in fade-in slide-in-from-top-1 duration-200">
                                <div class="flex items-center justify-between px-1">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-wider">Баланс</span>
                                    @if(auth()->guard('customer')->check())
                                        <span class="text-[13px] font-black text-zinc-800">
                                            {{ core()->currency(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="relative">
                                    <select 
                                        name="payment[crypto_coin]" 
                                        class="block w-full appearance-none rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-xs font-bold text-zinc-800 focus:border-[#7C45F5] focus:outline-none transition-all cursor-pointer shadow-sm"
                                        @change="selectCrypto($event.target.value)"
                                        @click.stop
                                    >
                                        <option value="">-- Выберите монету --</option>
                                        @if(auth()->guard('customer')->check() && auth()->guard('customer')->user()->balances->count() > 0)
                                            @foreach(auth()->guard('customer')->user()->balances as $balance)
                                                <option value="{{ $balance->currency_code }}">
                                                    {{ strtoupper($balance->currency_code) }} ({{ rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.') }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#7C45F5]">
                                        <span class="icon-arrow-down text-sm"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.after') !!}
                </template>
            </div>
        </script>

    <script type="module">
        app.component('v-payment-methods', {
            template: '#v-payment-methods-template',
            props: ['methods', 'selectedPaymentMethod'],
            emits: ['processing', 'processed', 'change-payment-method'],
            data() {
                return {
                    selectedCrypto: null
                }
            },
            methods: {
                handleSelection(payment) {
                    this.$emit('change-payment-method', payment.method);
                    if (payment.method !== 'credits' || this.selectedCrypto) {
                        this.store(payment);
                    }
                },
                selectCrypto(coin) {
                    this.selectedCrypto = coin;
                    if (this.selectedCrypto && this.selectedPaymentMethod === 'credits') {
                        const creditMethod = this.methods.find(m => m.method === 'credits');
                        if (creditMethod) {
                            this.store(creditMethod);
                        }
                    }
                },
                store(selectedMethod) {
                    this.$emit('processing', 'review');
                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                        payment: Object.assign({}, selectedMethod, { crypto_coin: this.selectedCrypto })
                    })
                        .then(response => {
                            this.$emit('processed', response.data.cart);
                        })
                        .catch(error => {
                            this.$emit('processing', 'payment');
                        });
                },
            },
        });
    </script>
@endPushOnce