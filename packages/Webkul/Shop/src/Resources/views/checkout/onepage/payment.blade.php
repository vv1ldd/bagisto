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
                <h2 class="text-xl font-black uppercase tracking-[0.15em] mb-8 text-zinc-900 border-b-[3px] border-zinc-900 pb-3 inline-block">
                    @lang('shop::app.checkout.onepage.payment.payment-method')
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template v-for="payment in methods">
                        <div
                            class="relative border-[3px] border-zinc-900 transition-all duration-300 cursor-pointer p-6 overflow-hidden min-h-[110px] flex flex-col justify-center"
                            :class="[selectedPaymentMethod == payment.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                            @click="handleSelection(payment)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div 
                                        class="flex h-12 w-12 items-center justify-center border-[3px]"
                                        :class="[selectedPaymentMethod == payment.method ? 'border-white bg-white' : 'border-zinc-900 bg-zinc-50']"
                                    >
                                        <img :src="payment.image" class="max-h-full max-w-full p-1.5 grayscale" />
                                    </div>

                                    <div class="min-w-0 flex flex-col">
                                        <p class="text-[14px] font-black uppercase tracking-widest truncate">@{{ payment.method_title }}</p>
                                        <div v-if="payment.method === 'sbp'" class="mt-1">
                                            <span class="bg-zinc-900 text-white text-[8px] font-black px-1.5 py-0.5 uppercase tracking-tighter">Появится позже</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex h-5 w-5 border-[3px] transition-all" :class="[selectedPaymentMethod == payment.method ? 'border-white bg-white' : 'border-zinc-900 bg-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]']">
                                    <div v-if="selectedPaymentMethod == payment.method" class="m-auto h-2 w-2 bg-[#7C45F5]"></div>
                                </div>
                            </div>

                            <!-- Compact Wallet Detail -->
                            <div v-if="payment.method === 'credits' && selectedPaymentMethod === 'credits'" class="mt-4 pt-4 border-t-2 border-white/20 animate-in slide-in-from-top-2 duration-300">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-black uppercase tracking-widest opacity-80">Баланс</span>
                                    @if(auth()->guard('customer')->check())
                                        <div class="flex flex-col items-end">
                                            <span class="text-xl font-black tabular-nums">
                                                {{ number_format(auth()->guard('customer')->user()->balance, 2, '.', '') }} MC
                                            </span>
                                            <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest mt-0.5">
                                                1 MC = 1.00 ₽
                                            </span>
                                        </div>
                                    @endif
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