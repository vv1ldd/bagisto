<v-quick-checkout ref="quickCheckout" :product-id="{{ $product->id }}"></v-quick-checkout>

@pushOnce('scripts')
    <script type="text/x-template" id="v-quick-checkout-template">
            <x-shop::modal ref="quickCheckoutModal">
                <x-slot:header class="!p-8 !pb-4">
                    <div class="flex items-center justify-between w-full">
                        <h2 class="text-2xl font-black text-zinc-900 uppercase tracking-tight">Быстрое оформление</h2>
                    </div>
                </x-slot>

                <x-slot:content class="!p-8 !pt-4">
                    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
                        <span class="icon-spinner animate-spin text-5xl text-[#7C45F5] mb-4"></span>
                        <p class="text-zinc-500 font-medium">Подготовка заказа...</p>
                    </div>

                    <div v-else-if="cart" class="grid grid-cols-1 md:grid-cols-[1fr_320px] gap-8">
                        <!-- Steps -->
                        <div class="space-y-6">
                            <!-- Step 1: Address/Email -->
                            <div v-if="currentStep == 'address'">
                                @include('shop::checkout.onepage.address')
                            </div>

                            <!-- Step 2: Payment -->
                            <div v-if="currentStep == 'payment'">
                                @include('shop::checkout.onepage.payment')
                            </div>

                            <!-- Step 3: Success State -->
                            <div v-if="currentStep == 'success'" class="text-center py-10">
                                <div class="h-20 w-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span class="icon-done text-4xl"></span>
                                </div>
                                <h3 class="text-2xl font-bold text-zinc-900 mb-2">Заказ оформлен!</h3>
                                <p class="text-zinc-500 mb-8">Спасибо за покупку. Мы отправили детали на ваш email.</p>
                                <x-shop::button
                                    class="primary-button rounded-xl px-8 py-3"
                                    title="Закрыть"
                                    @click="$refs.quickCheckoutModal.toggle()"
                                />
                            </div>
                        </div>

                        <!-- mini summary -->
                        <div class="bg-zinc-50 rounded-2xl p-6 border border-zinc-100 flex flex-col h-fit">
                            <h4 class="text-sm font-black text-zinc-400 uppercase tracking-widest mb-4">Ваш заказ</h4>

                            <div class="space-y-4 mb-6">
                                <div v-for="item in cart.items" :key="item.id" class="flex gap-3">
                                    <img :src="item.base_image.small_image_url" class="h-12 w-12 rounded-lg object-cover border border-zinc-200" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-zinc-800 truncate">@{{ item.name }}</p>
                                        <p class="text-[10px] text-zinc-500">@{{ item.quantity }} x @{{ item.formatted_price }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-zinc-200 pt-4 space-y-2">
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-500">Сумма</span>
                                    <span class="font-bold text-zinc-800">@{{ cart.formatted_sub_total }}</span>
                                </div>
                                <div v-if="cart.discount_amount > 0" class="flex justify-between text-xs">
                                    <span class="text-zinc-500">Скидка</span>
                                    <span class="font-bold text-green-600">- @{{ cart.formatted_discount_amount }}</span>
                                </div>
                                <div class="flex justify-between text-base pt-2 border-t border-zinc-100 mt-2">
                                    <span class="font-black text-zinc-900 uppercase">Итого</span>
                                    <span class="font-black text-[#7C45F5]">@{{ cart.formatted_grand_total }}</span>
                                </div>
                            </div>

                            <div class="mt-8" v-if="canPlaceOrder">
                                <button
                                    @click="placeOrder"
                                    :disabled="isPlacingOrder"
                                    class="w-full bg-[#7C45F5] text-white py-4 rounded-xl font-black shadow-lg shadow-purple-200 hover:bg-[#6b35e4] transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                                >
                                    <span v-if="!isPlacingOrder">Оплатить</span>
                                    <span v-else class="icon-spinner animate-spin text-lg"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </x-slot>

                <x-slot:footer class="hidden"></x-slot>
            </x-shop::modal>
        </script>

    <script type="module">
        app.component('v-quick-checkout', {
            template: '#v-quick-checkout-template',
            props: ['productId'],
            data() {
                return {
                    isLoading: false,
                    cart: null,
                    currentStep: 'address',
                    isPlacingOrder: false,
                    canPlaceOrder: false,
                    shippingMethods: null,
                    paymentMethods: null,
                }
            },
            methods: {
                open() {
                    this.$refs.quickCheckoutModal.toggle();
                    this.getCart();
                },
                getCart() {
                    this.isLoading = true;
                    this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                        .then(response => {
                            this.cart = response.data.data;
                            this.isLoading = false;

                            // Determine initial step
                            if (!this.cart.billing_address) {
                                this.currentStep = 'address';
                            } else if (!this.cart.payment_method) {
                                this.currentStep = 'payment';
                            } else {
                                this.currentStep = 'payment'; // Default to payment if address exists
                                this.canPlaceOrder = true;
                            }
                        })
                        .catch(error => {
                            this.isLoading = false;
                        });
                },
                stepForward(step) {
                    this.currentStep = step;
                    this.canPlaceOrder = (step == 'review' || step == 'payment');
                },
                stepProcessed(data) {
                    if (this.currentStep == 'address') {
                        this.currentStep = 'payment';
                    }
                    setTimeout(() => this.getCart(), 50);
                },
                placeOrder() {
                    this.isPlacingOrder = true;
                    this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                        .then(response => {
                            if (response.data.data.redirect) {
                                window.location.href = response.data.data.redirect_url;
                            } else {
                                this.currentStep = 'success';
                                this.canPlaceOrder = false;
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