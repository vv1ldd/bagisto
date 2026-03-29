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

                        <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight mb-1">
                            PAYMENT METHOD
                        </h2>
                        <p class="text-sm text-zinc-400 mb-5">Please select a payment method</p>

                        <div class="grid grid-cols-1 gap-6 w-full max-w-[450px]">
                            <div 
                                v-for="(payment, index) in methods"
                                :key="payment.method"
                                class="relative border-2 border-zinc-900 transition-all duration-200 cursor-pointer flex flex-col bg-white p-4"
                                :class="[selectedPaymentMethod == payment.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-1 translate-y-1' : 'text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5']"
                                @click="handleSelection(payment)"
                            >
                                <div class="flex items-center justify-between h-full px-2">
                                    <div class="flex items-center gap-4">
                                        <div 
                                            class="h-10 w-10 border-2 border-zinc-900 p-1 flex items-center justify-center shrink-0"
                                            :class="[selectedPaymentMethod == payment.method ? 'bg-white' : 'bg-zinc-50']"
                                        >
                                            <img :src="payment.image" class="max-h-full max-w-full object-contain" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[14px] font-black uppercase tracking-widest truncate">
                                                @{{ payment.method_title }}
                                            </p>
                                        </div>
                                    </div>

                                    <div 
                                        class="h-5 w-5 border-2 border-zinc-900 flex items-center justify-center transition-all"
                                        :class="[selectedPaymentMethod == payment.method ? 'bg-white' : 'bg-white']"
                                    >
                                        <div v-if="selectedPaymentMethod == payment.method" class="h-2.5 w-2.5 bg-[#7C45F5]"></div>
                                    </div>
                                </div>

                                <!-- Meanly Wallet Detail -->
                                <div 
                                    v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" 
                                    class="mt-4 pt-4 border-t-2 border-white/20 animate-in fade-in slide-in-from-top-1 duration-200"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Баланс</span>
                                        @if(auth()->guard('customer')->check())
                                            <span class="text-lg font-black tabular-nums">
                                                {{ core()->currency(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[9px] font-black uppercase tracking-widest opacity-60">Спишем автоматически с вашего крипто-кошелька</p>
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