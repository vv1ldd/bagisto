{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods :methods="paymentMethods" @processing="stepForward" @processed="stepProcessed">
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-payment-methods-template">
                    <div class="mb-12 max-md:mb-8">
                        <template v-if="! methods">
                            <!-- Payment Method shimmer Effect -->
                            <x-shop::shimmer.checkout.onepage.payment-method />
                        </template>

                        <template v-else>
                            {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.before') !!}

                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-xl font-bold text-zinc-800">
                                    @lang('shop::app.checkout.onepage.payment.payment-method')
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div 
                                    v-for="(payment, index) in methods"
                                    :key="payment.method"
                                    class="relative p-6 rounded-2xl border transition-all duration-300 group cursor-pointer overflow-hidden flex flex-col"
                                    :class="[$parent.selectedPaymentMethod == payment.method ? 'border-[#7C45F5] bg-white ring-1 ring-[#7C45F5] shadow-lg' : 'border-white/60 bg-white/40 backdrop-blur-3xl hover:border-white/80 shadow-sm']"
                                    @click="store(payment)"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-br from-transparent to-[#7C45F5]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                    <input 
                                        type="radio" 
                                        name="payment[method]" 
                                        :value="payment.payment"
                                        :id="payment.method"
                                        class="peer hidden"
                                        :checked="$parent.selectedPaymentMethod == payment.method"
                                    >

                                    <div class="relative flex items-center justify-between w-full">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm border border-zinc-100 p-2">
                                                <img
                                                    class="max-h-full max-w-full object-contain"
                                                    :src="payment.image"
                                                    :alt="payment.method_title"
                                                />
                                            </div>

                                            <div class="min-w-0">
                                                <p class="font-bold transition-colors duration-300" :class="[$parent.selectedPaymentMethod == payment.method ? 'text-[#7C45F5]' : 'text-zinc-800']">
                                                    @{{ payment.method_title }}
                                                </p>
                                                <p class="text-xs font-medium text-zinc-500 mt-0.5">@{{ payment.description }}</p>
                                            </div>
                                        </div>

                                        <div 
                                            class="flex h-6 w-6 items-center justify-center rounded-full border-2 transition-all duration-300"
                                            :class="[$parent.selectedPaymentMethod == payment.method ? 'border-[#7C45F5] bg-[#7C45F5] scale-110 shadow-md' : 'border-zinc-300 group-hover:border-zinc-400']"
                                        >
                                            <div v-if="$parent.selectedPaymentMethod == payment.method" class="h-2.5 w-2.5 rounded-full bg-white"></div>
                                        </div>
                                    </div>

                                    <!-- Crypto Selection for Credits -->
                                    <div v-if="payment.method === 'credits' && $parent.selectedPaymentMethod === 'credits'" class="relative mt-6 w-full animate-[fadeIn_0.3s_ease-out]">
                                        <label class="block text-xs font-bold text-[#7C45F5] uppercase tracking-wider mb-2">Выберите монету для оплаты</label>

                                        <div class="relative">
                                            <select 
                                                name="payment[crypto_coin]" 
                                                class="block w-full appearance-none rounded-xl border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-4 py-3 text-sm font-medium text-zinc-800 focus:border-[#7C45F5] focus:outline-none focus:ring-1 focus:ring-[#7C45F5] transition-all"
                                                @change="selectCrypto($event.target.value)"
                                                @click.stop
                                            >
                                                <option value="">-- Выберите монету --</option>
                                                @if(auth()->guard('customer')->check() && auth()->guard('customer')->user()->balances->count() > 0)
                                                    @foreach(auth()->guard('customer')->user()->balances as $balance)
                                                        <option value="{{ $balance->currency_code }}">
                                                            {{ strtoupper($balance->currency_code) }} ({{ rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.') }} доступно)
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#7C45F5]">
                                                <span class="icon-arrow-down text-lg"></span>
                                            </div>
                                        </div>

                                        <p class="mt-2 text-[10px] font-medium text-zinc-400 leading-tight italic">
                                            * Окончательная сумма будет рассчитана по актуальному курсу при подтверждении заказа.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.after') !!}
                        </template>
                    </div>

                            {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.after') !!}
                        </template>
                    </div>
                </script>

    <script type="module">
        app.component('v-payment-methods', {
            template: '#v-payment-methods-template',

            props: {
                methods: {
                    type: Object,
                    required: true,
                    default: () => null,
                },
            },

            emits: ['processing', 'processed'],

            data() {
                return {
                    selectedCrypto: null
                }
            },

            methods: {
                selectCrypto(coin) {
                    this.selectedCrypto = coin;
                    // Auto-trigger store if crypto is selected so it updates the cart/session
                    if (this.selectedCrypto && this.$parent.selectedPaymentMethod === 'credits') {
                        const creditMethod = this.methods.find(m => m.method === 'credits');
                        if (creditMethod) {
                            this.store(creditMethod);
                        }
                    }
                },

                store(selectedMethod) {
                    if (selectedMethod.method === 'credits' && !this.selectedCrypto) {
                        // Don't proceed to processing/review yet if they clicked Credits but haven't chosen a coin
                        this.$parent.selectedPaymentMethod = selectedMethod.method;
                        return;
                    }

                    this.$emit('processing', 'review');

                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                        payment: Object.assign({}, selectedMethod, { crypto_coin: this.selectedCrypto })
                    })
                        .then(response => {
                            this.$emit('processed', response.data.cart);

                            // Used in mobile view. 
                            if (window.innerWidth <= 768) {
                                window.scrollTo({
                                    top: document.body.scrollHeight,
                                    behavior: 'smooth'
                                });
                            }
                        })
                        .catch(error => {
                            this.$emit('processing', 'payment');

                            if (error.response.data.redirect_url) {
                                window.location.href = error.response.data.redirect_url;
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce