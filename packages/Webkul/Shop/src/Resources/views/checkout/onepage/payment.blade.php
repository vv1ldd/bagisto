{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods :methods="paymentMethods" @processing="stepForward" @processed="stepProcessed">
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-payment-methods-template">
            <div class="mb-7 max-md:last:!mb-0">
                <template v-if="! methods">
                    <!-- Payment Method shimmer Effect -->
                    <x-shop::shimmer.checkout.onepage.payment-method />
                </template>

                <template v-else>
                    {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.before') !!}

                    <!-- Accordion Blade Component -->
                    <x-shop::accordion class="overflow-hidden !border-b-0 max-md:rounded-lg max-md:!border-none max-md:!bg-gray-100">
                        <!-- Accordion Blade Component Header -->
                        <x-slot:header class="px-0 py-4 max-md:p-3 max-md:text-sm max-md:font-medium max-sm:p-2">

                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-medium max-md:text-base">
                                    @lang('shop::app.checkout.onepage.payment.payment-method')
                                </h2>
                            </div>
                        </x-slot>

                        <!-- Accordion Blade Component Content -->
                        <x-slot:content class="mt-8 !p-0 max-md:mt-0 max-md:rounded-t-none max-md:border max-md:border-t-0 max-md:!p-4">
                            <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                                <div 
                                    class="relative cursor-pointer max-md:max-w-full max-md:flex-auto"
                                    v-for="(payment, index) in methods"
                                >
                                    {!! view_render_event('bagisto.shop.checkout.payment-method.before') !!}

                                    <input 
                                        type="radio" 
                                        name="payment[method]" 
                                        :value="payment.payment"
                                        :id="payment.method"
                                        class="peer hidden"
                                        @change="store(payment)"
                                    >

                                    <label 
                                        :for="payment.method" 
                                        class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                    >
                                    </label>

                                    <label 
                                        :for="payment.method" 
                                        class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                    >
                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.image.before') !!}

                                        <img
                                            class="max-h-11 max-w-14"
                                            :src="payment.image"
                                            width="55"
                                            height="55"
                                            :alt="payment.method_title"
                                            :title="payment.method_title"
                                        />

                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.image.after') !!}

                                        <div>
                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.title.before') !!}

                                            <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                                @{{ payment.method_title }}
                                            </p>

                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.title.after') !!}

                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.description.before') !!}

                                            <p class="mt-2.5 text-xs font-medium text-zinc-500 max-md:mt-1 max-sm:mt-0">
                                                @{{ payment.description }}
                                            </p> 

                                            {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.description.after') !!}

                                        </div>
                                    </label>

                                    {!! view_render_event('bagisto.shop.checkout.payment-method.after') !!}

                                    <!-- Todo implement the additionalDetails -->
                                    {{-- \Webkul\Payment\Payment::getAdditionalDetails($payment['method'] --}}
                                    <!-- Crypto Selection for Credits -->
                                    <div v-if="payment.method === 'credits' && $parent.selectedPaymentMethod === 'credits'" class="w-full mt-3 px-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Выберите криптовалюту для списания</label>
                                        <select 
                                            name="payment[crypto_coin]" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"
                                            @change="selectCrypto($event.target.value)"
                                        >
                                            <option value="">-- Выберите монету --</option>
                                            @if(auth()->guard('customer')->check() && auth()->guard('customer')->user()->balances->count() > 0)
                                                @foreach(auth()->guard('customer')->user()->balances as $balance)
                                                    <option value="{{ $balance->currency_code }}">
                                                        Баланс {{ strtoupper($balance->currency_code) }} (в наличии: {{ rtrim(rtrim(number_format($balance->amount, 8, '.', ''), '0'), '.') }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Окончательная сумма списания будет рассчитана по живому курсу в момент оформления заказа.</p>
                                    </div>

                                </div>
                            </div>
                        </x-slot>
                    </x-shop::accordion>

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