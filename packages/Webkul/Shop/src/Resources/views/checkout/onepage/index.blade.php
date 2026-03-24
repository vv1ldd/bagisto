<x-shop::layouts :has-feature="false" :has-footer="false">
    <x-slot:title>@lang('shop::app.checkout.onepage.index.checkout')</x-slot>

    <style>
        body {
            background: linear-gradient(135deg, #fdf4ff 0%, #ffffff 50%, #f5f3ff 100%);
            min-height: 100vh;
        }

        [v-cloak] {
            display: none;
        }
    </style>

    <div class="px-4 py-3 md:py-8 max-w-7xl mx-auto" v-cloak>
        <v-checkout>
            <x-shop::shimmer.checkout.onepage />
        </v-checkout>
    </div>

        @pushOnce('scripts')
            <script type="text/x-template" id="v-checkout-template">
                                            <template v-if="! cart">
                                                <x-shop::shimmer.checkout.onepage />
                                            </template>

                                            <template v-else>
                                                <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
                                                    <!-- Steps Column (LEFT) -->
                                                    <div class="space-y-6" id="steps-container">
                                                        <!-- Mobile Order Summary -->
                                                        <div class="lg:hidden">
                                                            <div class="bg-white/40 backdrop-blur-3xl border border-white/60  p-6 shadow-sm mb-6 overflow-hidden">
                                                                <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/20">
                                                                    <h3 class="text-lg font-black text-zinc-800 uppercase tracking-tight">Ваш заказ</h3>
                                                                    <div class="px-3 py-1 bg-[#7C45F5]  text-white text-[10px] font-black uppercase">
                                                                        @{{ cart.items.length }} поз.
                                                                    </div>
                                                                </div>

                                                                <div class="max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                                                                    @include('shop::checkout.onepage.summary')
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="bg-white p-6 sm:p-8 shadow-sm overflow-hidden flex flex-col gap-10">
                                                            <!-- Address -->
                                                            <div data-step="address">
                                                                <template v-if="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                                                                    @include('shop::checkout.onepage.address')
                                                                </template>
                                                            </div>

                                                            <!-- Shipping -->
                                                            <div v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)" data-step="shipping">
                                                                @include('shop::checkout.onepage.shipping')
                                                            </div>

                                                            <!-- Payment -->
                                                            <div v-if="['payment', 'review'].includes(currentStep)" data-step="payment">
                                                                @include('shop::checkout.onepage.payment')
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Sidebar Column (RIGHT) -->
                                                    <div class="hidden lg:block sticky top-8 space-y-4">
                                                        <div class="bg-white p-6 sm:p-8 shadow-sm">
                                                            @include('shop::checkout.onepage.summary')

                                                            <div class="mt-8 space-y-4" v-if="canPlaceOrder">
                                                                <button
                                                                    type="button"
                                                                    class="w-full  bg-[#7C45F5] py-4 text-base font-black text-white shadow-[0_10px_20px_-5px_rgba(124,69,245,0.4)] transition-all hover:bg-[#6b35e4] hover:shadow-[0_15px_30px_-5px_rgba(124,69,245,0.5)] active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                                                                    :disabled="isPlacingOrder"
                                                                    @click="placeOrder"
                                                                >
                                                                    <span v-if="!isPlacingOrder">
                                                                        <template v-if="cart.payment_method == 'credits'">Оплатить с Wallet</template>
                                                                        <template v-else>@lang('shop::app.checkout.onepage.summary.place-order')</template>
                                                                    </span>
                                                                    <svg v-else class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                    </svg>
                                                                </button>

                                                                <p class="text-center text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-relaxed">
                                                                    Нажимая "Оплатить", вы принимаете <br/> 
                                                                    <a href="#" class="underline hover:text-[#7C45F5] transition-colors">условиями обслуживания</a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </script>

            <script type="module">
                app.component('v-checkout', {
                    template: '#v-checkout-template',
                    data() {
                        return {
                            cart: null,

                            displayTax: {
                                prices: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_prices') }}",

                                subtotal: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_subtotal') }}",

                                shipping: "{{ core()->getConfigData('sales.taxes.shopping_cart.display_shipping_amount') }}",
                            },

                            isPlacingOrder: false,
                            currentStep: 'payment',
                            shippingMethods: null,
                            paymentMethods: null,
                            selectedPaymentMethod: null,
                            canPlaceOrder: false,
                        }
                    },
                    watch: {
                        selectedPaymentMethod(val) {
                            if (val) this.canPlaceOrder = true;
                        }
                    },
                    mounted() {
                        this.getCart();
                    },
                    methods: {
                        getCart() {
                            this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                                .then(response => {
                                    this.cart = response.data.data;
                                    if (this.cart.payment_method) {
                                        this.selectedPaymentMethod = this.cart.payment_method;
                                    }
                                    // For digital carts: load payment methods directly, no address step.
                                    if (this.cart && !this.cart.have_stockable_items && !this.paymentMethods) {
                                        this.$axios.get("{{ route('shop.checkout.onepage.payment_methods.index') }}")
                                            .then(r => {
                                                this.paymentMethods = r.data.payment_methods || r.data;
                                                if (this.selectedPaymentMethod) this.canPlaceOrder = true;
                                            });
                                    }
                                })
                                .catch(error => { });
                        },
                        stepForward(step) {
                            this.currentStep = step;
                            this.canPlaceOrder = (step == 'review');
                            if (this.currentStep == 'shipping') this.shippingMethods = null;
                            else if (this.currentStep == 'payment') this.paymentMethods = null;
                            this.scrollToCurrentStep();
                        },
                        stepProcessed(data) {
                            if (this.currentStep == 'shipping') {
                                this.shippingMethods = data;
                            } else if (this.currentStep == 'payment') {
                                this.paymentMethods = data;
                            } else if (this.currentStep == 'review') {
                                this.cart = data;
                            }

                            /* 
                             * Force a small delay to ensure sub-components have finished 
                             * their state updates before we refresh the global cart state.
                             */
                            setTimeout(() => this.getCart(), 50);
                        },
                        scrollToCurrentStep() {
                            setTimeout(() => {
                                let container = document.getElementById('steps-container');
                                if (!container) return;
                                let activeStep = container.querySelector(`[data-step="${this.currentStep}"]`);
                                if (activeStep) {
                                    activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            }, 100);
                        },
                        // Removed manual base64 helpers as we now use SimpleWebAuthnBrowser
                        async payWithPasskey() {
                            this.isPlacingOrder = true;
                            try {
                                const { startAuthentication } = window.SimpleWebAuthnBrowser;
                                const optionsResponse = await this.$axios.post('{{ route('passkeys.login-options') }}');
                                const options = optionsResponse.data;
                                
                                const asseResp = await startAuthentication(options);

                                await this.$axios.post('{{ route('passkeys.login') }}', {
                                    start_authentication_response: JSON.stringify(asseResp),
                                    remember: false
                                });
                                this.executePlaceOrder();
                            } catch (error) {
                                this.isPlacingOrder = false;
                                if (error.name !== 'NotAllowedError') {
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Ошибка безопасности' });
                                }
                            }
                        },
                        placeOrder() {
                            if (this.cart.payment_method == 'credits') {
                                this.payWithPasskey();
                                return;
                            }
                            this.executePlaceOrder();
                        },
                        executePlaceOrder() {
                            this.isPlacingOrder = true;
                            this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                                .then(response => {
                                    window.location.href = response.data.data.redirect ? response.data.data.redirect_url : '{{ route('shop.checkout.onepage.success') }}';
                                })
                                .catch(error => {
                                    this.isPlacingOrder = false;
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                        }
                    },
                });
            </script>
        @endPushOnce
</x-shop::layouts>