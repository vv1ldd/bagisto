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
                                                        class="relative p-4 rounded-2xl border transition-all duration-300 group cursor-pointer overflow-hidden flex flex-col"
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
                                                            <div class="flex items-center gap-3">
                                                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm border border-zinc-100 p-1.5">
                                                                    <img
                                                                        class="max-h-full max-w-full object-contain"
                                                                        :src="payment.image"
                                                                        :alt="payment.method_title"
                                                                    />
                                                                </div>

                                                                <div class="min-w-0">
                                                                    <p class="text-sm font-bold transition-colors duration-300" :class="[$parent.selectedPaymentMethod == payment.method ? 'text-[#7C45F5]' : 'text-zinc-800']">
                                                                        @{{ payment.method_title }}
                                                                    </p>
                                                                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">@{{ payment.description }}</p>
                                                                </div>
                                                            </div>

                                                            <div 
                                                                class="flex h-6 w-6 items-center justify-center rounded-full border-2 transition-all duration-300"
                                                                :class="[$parent.selectedPaymentMethod == payment.method ? 'border-[#7C45F5] bg-[#7C45F5] scale-110 shadow-md' : 'border-zinc-300 group-hover:border-zinc-400']"
                                                            >
                                                                <div v-if="$parent.selectedPaymentMethod == payment.method" class="h-2.5 w-2.5 rounded-full bg-white"></div>
                                                            </div>
                                                        </div>

                                                        <!-- Meanly Wallet UI -->
                                                        <div v-if="payment.method === 'credits' && $parent.selectedPaymentMethod === 'credits'" class="relative mt-6 w-full animate-[fadeIn_0.3s_ease-out] p-5 rounded-2xl bg-gradient-to-br from-[#7C45F5]/10 to-[#7C45F5]/5 border border-[#7C45F5]/20 shadow-inner">
                                                            <div class="mb-5 flex items-center justify-between">
                                                                <label class="block text-[10px] font-black text-[#7C45F5] uppercase tracking-[0.2em]">Meanly Wallet</label>
                                                                @if(auth()->guard('customer')->check())
                                                                    <div class="flex flex-col items-end">
                                                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mb-0.5">Доступный баланс</span>
                                                                        <span class="text-sm font-black text-zinc-800">
                                                                            {{ core()->currency(auth()->guard('customer')->user()->getTotalFiatBalance()) }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="relative group/select">
                                                                <div class="absolute -top-2 left-3 px-1.5 bg-white text-[9px] font-black text-zinc-400 uppercase tracking-widest z-10">Выберите монету</div>
                                                                <select 
                                                                    name="payment[crypto_coin]" 
                                                                    class="block w-full appearance-none rounded-xl border-2 border-white bg-white/80 backdrop-blur-md px-4 py-3.5 text-sm font-bold text-zinc-800 shadow-sm focus:border-[#7C45F5] focus:outline-none focus:ring-4 focus:ring-[#7C45F5]/10 transition-all cursor-pointer"
                                                                    @change="selectCrypto($event.target.value)"
                                                                    @click.stop
                                                                >
                                                                    <option value="">-- Выберите валюту --</option>
                                                                    @if(auth()->guard('customer')->check() && auth()->guard('customer')->user()->balances->count() > 0)
                                                                        @foreach(auth()->guard('customer')->user()->balances as $balance)
                                                                            <option value="{{ $balance->currency_code }}">
                                                                                {{ strtoupper($balance->currency_code) }} ({{ rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.') }})
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#7C45F5] group-hover/select:scale-110 transition-transform">
                                                                    <span class="icon-arrow-down text-lg"></span>
                                                                </div>
                                                            </div>

                                                            <div class="mt-5 p-3 rounded-xl bg-white/50 border border-white/80 flex items-start gap-3">
                                                                <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#7C45F5]/10 text-[#7C45F5]">
                                                                    <span class="icon-information text-[10px]"></span>
                                                                </div>
                                                                <p class="text-[10px] font-semibold text-zinc-600 leading-normal">
                                                                    Оплата будет подтверждена через <span class="text-[#7C45F5] font-black">Passkey</span>. Сумма в криптовалюте фиксируется в момент оплаты.
                                                                </p>
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
                        // Don't proceed to processing/review yet if they clicked Wallet but haven't chosen a coin
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