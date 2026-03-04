@pushOnce('scripts')
    <script type="text/x-template" id="v-direct-checkout-template">
                <div class="mt-8 space-y-8">
                    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
                        <span class="icon-spinner animate-spin text-5xl text-[#7C45F5] mb-4"></span>
                        <p class="text-zinc-500 font-medium">Подготовка оформления...</p>
                    </div>

                    <div v-else-if="cart" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <!-- Step 1: Address/Email -->
                        <div v-if="currentStep == 'address'" class="bg-white/50 backdrop-blur-sm border border-zinc-100 rounded-3xl p-8 shadow-sm">
                            <h3 class="text-xl font-black text-zinc-900 uppercase tracking-tight mb-6">1. Данные покупателя</h3>
                            @include('shop::checkout.onepage.address')
                        </div>

                        <!-- Step 2: Payment -->
                        <div v-if="currentStep == 'payment'" class="bg-white/50 backdrop-blur-sm border border-zinc-100 rounded-3xl p-8 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-black text-zinc-900 uppercase tracking-tight">2. Выбор оплаты</h3>
                                <button @click="currentStep = 'address'" class="text-sm font-bold text-[#7C45F5] hover:underline">Изменить данные</button>
                            </div>
                            @include('shop::checkout.onepage.payment')
                        </div>

                        <!-- Success State -->
                        <div v-if="currentStep == 'success'" class="bg-white/50 backdrop-blur-sm border border-zinc-100 rounded-3xl p-10 shadow-sm text-center">
                            <div class="h-20 w-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="icon-done text-4xl"></span>
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-900 mb-2">Заказ оформлен!</h3>
                            <p class="text-zinc-500 mb-8">Спасибо за покупку. Инструкции отправлены на ваш email.</p>
                        </div>

                        <!-- Footer Action -->
                        <div v-if="currentStep == 'payment'" class="mt-10">
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
                }
            },
            mounted() {
                this.$emitter.on('after-payment-method-selected', () => {
                    this.paymentMethodSelected = true;
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

                            if (this.cart.billing_address) {
                                this.currentStep = 'payment';
                            } else {
                                this.currentStep = 'address';
                            }
                        })
                        .catch(error => {
                            this.isLoading = false;
                        });
                },
                stepProcessed() {
                    this.getCart();
                },
                placeOrder() {
                    this.isPlacingOrder = true;
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
                }
            }
        });
    </script>
@endPushOnce