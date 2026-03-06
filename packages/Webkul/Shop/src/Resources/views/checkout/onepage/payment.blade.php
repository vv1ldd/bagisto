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
                                class="relative p-2.5  border transition-all duration-300 group cursor-pointer flex flex-col bg-white"
                                :class="[selectedPaymentMethod == payment.method ? 'border-[#7C45F5] ring-1 ring-[#7C45F5] shadow-lg shadow-[#7C45F5]/5' : 'border-zinc-100 hover:border-zinc-200 shadow-sm']"
                                @click="handleSelection(payment)"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8  bg-zinc-50 border border-zinc-100 p-1 flex items-center justify-center shrink-0">
                                            <img :src="payment.image" class="max-h-full max-w-full object-contain" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-bold truncate transition-colors" :class="[selectedPaymentMethod == payment.method ? 'text-[#7C45F5]' : 'text-zinc-800']">
                                                @{{ payment.method_title }}
                                            </p>
                                        </div>
                                    </div>

                                    <div 
                                        class="h-4 w-4  border flex items-center justify-center transition-all bg-zinc-50"
                                        :class="[selectedPaymentMethod == payment.method ? 'border-[#7C45F5] bg-[#7C45F5]' : 'border-zinc-300']"
                                    >
                                        <div v-if="selectedPaymentMethod == payment.method" class="h-1.5 w-1.5  bg-white"></div>
                                    </div>
                                </div>

                                <!-- Meanly Wallet: show total balance, no coin selection needed -->
                                <div v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" class="mt-3 p-2.5  bg-[#7C45F5]/5 border border-[#7C45F5]/10 animate-in fade-in slide-in-from-top-1 duration-200">
                                    <div class="flex items-center justify-between px-1">
                                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-wider">Баланс</span>
                                        @if(auth()->guard('customer')->check())
                                            <span class="text-[13px] font-black text-zinc-800">
                                                {{ core()->currency(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 px-1 text-[10px] text-zinc-400">Спишем автоматически с вашего крипто-кошелька</p>
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
            methods: {
                handleSelection(payment) {
                    this.$emit('change-payment-method', payment.method);
                    this.store(payment);
                },
                store(selectedMethod) {
                    this.$emit('processing', 'review');
                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                        payment: selectedMethod
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