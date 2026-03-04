@pushOnce('scripts')
    <script type="text/x-template" id="v-direct-checkout-template">
                    <div class="mt-8 space-y-8">
                        <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
                            <span class="icon-spinner animate-spin text-5xl text-[#7C45F5] mb-4"></span>
                            <p class="text-zinc-500 font-medium">Подготовка оформления...</p>
                        </div>

                        <div v-else-if="cart" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <!-- Unified Checkout Block -->
                            <div class="bg-white/50 backdrop-blur-sm border border-zinc-100 rounded-3xl p-8 shadow-sm">
                                <h3 class="text-xl font-black text-zinc-900 uppercase tracking-tight mb-6">Данные и оплата</h3>

                                <!-- Address Selection -->
                                <div class="mb-10">
                                    @include('shop::checkout.onepage.address')
                                </div>

                                <!-- Payment Selection -->
                                <div v-if="paymentMethods" class="animate-in fade-in slide-in-from-top-4 duration-500">
                                    @include('shop::checkout.onepage.payment')
                                </div>

                                <!-- Footer Action -->
                                <div class="mt-10">
                                    <button
                                        @click="placeOrder"
                                        :disabled="isPlacingOrder || !paymentMethodSelected"
                                        class="w-full bg-[#7C45F5] text-white py-5 rounded-2xl font-black text-lg shadow-xl shadow-purple-200 hover:bg-[#6b35e4] transition-all disabled:opacity-50 flex items-center justify-center gap-3"
                                    >
                                        <span v-if="!isPlacingOrder">Оплатить @{{ cart.formatted_grand_total }}</span>
                                        <span v-else class="icon-spinner animate-spin text-2xl"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Success State -->
                            <div v-if="currentStep == 'success'" class="bg-white/50 backdrop-blur-sm border border-zinc-100 rounded-3xl p-10 shadow-sm text-center">
                                <div class="h-20 w-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span class="icon-done text-4xl"></span>
                                </div>
                                <h3 class="text-2xl font-bold text-zinc-900 mb-2">Заказ оформлен!</h3>
                                <p class="text-zinc-500 mb-8">Спасибо за покупку. Инструкции отправлены на ваш email.</p>
                            </div>
                        </div>
                    </div>
                </script>

    <script type="module">
        app.component('v-direct-checkout', {
            template: '#v-direct-checkout-template',
            props: ['productId'],
            data() {
                return {
                    isLoading: false,
                    cart: null,
                    currentStep: 'address',
                    isPlacingOrder: false,
                    paymentMethodSelected: false,
                    paymentMethods: null,
                    selectedPaymentMethod: null,
                }
            },
            mounted() {
                this.$emitter.on('after-payment-method-selected', () => {
                    this.paymentMethodSelected = true;
                });

                // Listen for address selection to refresh payment methods
                this.$emitter.on('after-address-save', (data) => {
                    this.paymentMethods = data;
                    if (this.cart) {
                        this.getCart(); // Refresh cart to get updated totals if any
                    }
                });
            },
            methods: {
                initCheckout() {
                    this.isLoading = true;
                    this.getCart();
                },
                getCart() {
                    this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                        .then(response => {
                            this.cart = response.data.data;
                            this.isLoading = false;

                            // If we have a billing address, fetch payment methods immediately
                            if (this.cart.billing_address) {
                                this.fetchPaymentMethods();
                            }
                        })
                        .catch(error => {
                            this.isLoading = false;
                        });
                },
                fetchPaymentMethods() {
                    this.$axios.get("{{ route('shop.checkout.onepage.payment_methods.index') }}")
                        .then(response => {
                            this.paymentMethods = response.data.paymentMethods;
                        });
                },
                placeOrder() {
                    this.isPlacingOrder = true;

                    // The actual order placement logic
                    const executeStore = () => {
                        this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                            .then(response => {
                                if (response.data.data.redirect) {
                                    window.location.href = response.data.data.redirect_url;
                                } else {
                                    this.currentStep = 'success';
                                    this.$emitter.emit('update-mini-cart', null);
                                }
                                this.isPlacingOrder = false;
                            })
                            .catch(error => {
                                this.isPlacingOrder = false;
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    };

                    executeStore();
                }
            }
        });
    </script>
@endPushOnce