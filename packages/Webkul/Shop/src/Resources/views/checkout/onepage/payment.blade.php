{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods :methods="paymentMethods" :selected-payment-method="selectedPaymentMethod" @processing="stepForward"
    @processed="stepProcessed" @change-payment-method="selectedPaymentMethod = $event">
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-payment-methods-template">
        <div class="animate-in fade-in duration-500">
            <template v-if="! methods">
                <x-shop::shimmer.checkout.onepage.payment-method />
            </template>

            <template v-else>
                <h2 class="text-2xl font-black uppercase tracking-[0.2em] mb-10 text-zinc-900 border-b-4 border-zinc-900 pb-4 inline-block">
                    @lang('shop::app.checkout.onepage.payment.payment-method')
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <template v-for="payment in methods">
                        <div
                            class="relative border-4 border-zinc-900 transition-all duration-300 cursor-pointer p-8 overflow-hidden min-h-[140px] flex flex-col justify-center"
                            :class="[selectedPaymentMethod == payment.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-1 translate-y-1' : 'bg-white text-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)]']"
                            @click="handleSelection(payment)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-6">
                                    <div 
                                        class="flex h-16 w-16 items-center justify-center border-4"
                                        :class="[selectedPaymentMethod == payment.method ? 'border-white bg-white' : 'border-zinc-900 bg-zinc-50']"
                                    >
                                        <img :src="payment.image" class="max-h-full max-w-full p-2 grayscale" />
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-lg font-black uppercase tracking-widest truncate">@{{ payment.method_title }}</p>
                                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1" v-if="payment.method !== 'credits'">Pay with @{{ payment.method }}</p>
                                    </div>
                                </div>

                                <div class="flex h-7 w-7 border-4 transition-all" :class="[selectedPaymentMethod == payment.method ? 'border-white bg-white' : 'border-zinc-900 bg-white shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]']">
                                    <div v-if="selectedPaymentMethod == payment.method" class="m-auto h-3 w-3 bg-[#7C45F5]"></div>
                                </div>
                            </div>

                            <!-- Special Styling for Wallet (Credits) -->
                            <div v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" class="mt-6 pt-6 border-t-4 border-white/20 animate-in slide-in-from-top-2 duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Доступно на балансе</span>
                                    @if(auth()->guard('customer')->check())
                                        <span class="text-2xl font-black tabular-nums">
                                            {{ core()->currency(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="px-4 py-2 bg-white/10 border-2 border-white/20 text-[9px] font-black uppercase tracking-widest leading-loose">
                                    Списание произойдёт автоматически после подтверждения заказа
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
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
                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", { payment: selectedMethod })
                        .then(response => {
                            this.$emit('processed', response.data.cart);
                        }).catch(() => this.$emit('processing', 'payment'));
                }
            }
        });
    </script>
@endPushOnce