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

                        <div class="grid grid-cols-1 gap-4 w-full max-w-[450px]">
                            <div 
                                v-for="(payment, index) in methods"
                                :key="payment.method"
                                class="relative border transition-all duration-300 group cursor-pointer flex flex-col justify-start bg-white"
                                style="height: 56px;"
                                :class="[selectedPaymentMethod == payment.method ? 'border-[#7C45F5] ring-1 ring-[#7C45F5] shadow-lg shadow-[#7C45F5]/5' : 'border-zinc-100 hover:border-zinc-200 shadow-sm']"
                                @click="handleSelection(payment)"
                            >
                                <div class="flex items-center justify-between h-full px-2.5">
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

                                <!-- Meanly Wallet: show total balance — absolutely positioned BELOW the tile, does NOT expand it -->
                                <div 
                                    v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" 
                                    class="absolute left-0 right-0 top-full z-10 p-2.5 bg-white border border-[#7C45F5]/20 border-t-0 animate-in fade-in slide-in-from-top-1 duration-200"
                                >
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