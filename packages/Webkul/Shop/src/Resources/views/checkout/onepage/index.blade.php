<x-shop::layouts :has-feature="false">
    <x-slot:title>@lang('shop::app.checkout.onepage.index.checkout')</x-slot>

    <style>
        [v-cloak] { display: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #18181b; border-radius: 10px; }
    </style>

    <div class="bg-[#F8FAF9] min-h-screen py-4 md:py-8" v-cloak>
        <div class="max-w-7xl mx-auto px-4">
            <v-checkout>
                <x-shop::shimmer.checkout.onepage />
            </v-checkout>
        </div>
    </div>

    @push('scripts')
        <script type="text/x-template" id="v-checkout-template">
            <div>
                <template v-if="! cart">
                    <x-shop::shimmer.checkout.onepage />
                </template>

                <template v-else>
                    <!-- Compact Progress Breadcrumb -->
                    <div class="flex flex-wrap items-center gap-3 mb-6 md:mb-8">
                        <div 
                            class="flex items-center gap-2 px-5 py-2 border-[3px] border-zinc-900 transition-all duration-300"
                            :class="['address', 'shipping', 'payment', 'review'].includes(currentStep) ? 'bg-[#7C45F5] text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-400 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]'"
                        >
                            <span class="text-[11px] font-black uppercase tracking-[0.15em]">01. @lang('shop::app.checkout.onepage.address.title')</span>
                        </div>

                        <div class="hidden md:block w-8 h-0.5 bg-zinc-900 opacity-20"></div>

                        <div 
                            class="flex items-center gap-2 px-5 py-2 border-[3px] border-zinc-900 transition-all duration-300"
                            :class="['shipping', 'payment', 'review'].includes(currentStep) ? 'bg-[#7C45F5] text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-400 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]'"
                        >
                            <span class="text-[11px] font-black uppercase tracking-[0.15em]">02. @lang('shop::app.checkout.onepage.shipping.shipping-method')</span>
                        </div>

                        <div class="hidden md:block w-8 h-0.5 bg-zinc-900 opacity-20"></div>

                        <div 
                            class="flex items-center gap-2 px-5 py-2 border-[3px] border-zinc-900 transition-all duration-300"
                            :class="['payment', 'review'].includes(currentStep) ? 'bg-[#7C45F5] text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-400 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]'"
                        >
                            <span class="text-[11px] font-black uppercase tracking-[0.15em]">03. @lang('shop::app.checkout.onepage.payment.payment-method')</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8 items-start">
                        <!-- Left Column: Steps -->
                        <div class="space-y-6" id="steps-container">
                            <div class="bg-white border-4 border-zinc-900 p-6 md:p-10 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                                <!-- Step: Address/Profile -->
                                <div data-step="address" v-show="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                                    @include('shop::checkout.onepage.address')
                                </div>

                                <!-- Step: Shipping -->
                                <div v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)" data-step="shipping" class="mt-8 pt-8 border-t-4 border-zinc-50">
                                    @include('shop::checkout.onepage.shipping')
                                </div>

                                <!-- Step: Payment -->
                                <div v-if="['payment', 'review'].includes(currentStep)" data-step="payment" class="mt-8 pt-8 border-t-4 border-zinc-50">
                                    @include('shop::checkout.onepage.payment')
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Summary -->
                        <div class="sticky top-6">
                            <div class="bg-white border-4 border-zinc-900 p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]">
                                @include('shop::checkout.onepage.summary')

                                <!-- Place Order Button -->
                                <div class="mt-6 pt-6 border-t-4 border-zinc-900" v-if="canPlaceOrder">
                                    <button
                                        type="button"
                                        class="w-full bg-[#7C45F5] border-4 border-zinc-900 py-5 text-[16px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:bg-[#8A5CF7] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center gap-3 disabled:opacity-50"
                                        :disabled="isPlacingOrder"
                                        @click="placeOrder"
                                    >
                                        <span v-if="!isPlacingOrder">
                                            @lang('shop::app.checkout.onepage.summary.place-order')
                                        </span>
                                        <svg v-else class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </button>

                                    <p class="text-center text-[10px] font-black text-zinc-400 mt-4 uppercase tracking-[0.1em] leading-relaxed">
                                        Оформляя заказ, вы соглашаетесь с <br/> 
                                        <a href="#" class="underline hover:text-zinc-900 transition-colors">правилами сервиса</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
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
                        currentStep: 'address',
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
                    },
                    stepProcessed(data) {
                        if (this.currentStep == 'shipping') {
                            this.shippingMethods = data;
                        } else if (this.currentStep == 'payment') {
                            this.paymentMethods = data;
                        } else if (this.currentStep == 'review') {
                            this.cart = data;
                        }
                        setTimeout(() => this.getCart(), 50);
                    },
                    async placeOrder() {
                        this.isPlacingOrder = true;
                        let payload = {};

                        // Intercept if Meanly Wallet (credits) is selected
                        if (this.selectedPaymentMethod === 'credits') {
                            try {
                                const optionsRes = await this.$axios.post('{{ route('passkeys.login-options') }}');
                                const options = optionsRes.data;

                                if (options.challenge) {
                                    options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
                                }
                                if (options.allowCredentials) {
                                    options.allowCredentials = options.allowCredentials.map(c => ({
                                        ...c,
                                        id: Uint8Array.from(atob(c.id.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0))
                                    }));
                                }

                                const credential = await navigator.credentials.get({ publicKey: options });

                                const credentialJson = {
                                    id: credential.id,
                                    rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                    type: credential.type,
                                    response: {
                                        authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                        clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                        signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                        userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '') : null
                                    }
                                };
                                
                                // Attach passkey proof to order payload
                                payload = { passkey_assertion: credentialJson };

                            } catch (e) {
                                console.error('Passkey authentication error:', e);
                                this.isPlacingOrder = false;
                                this.$emitter.emit('add-flash', { type: 'error', message: 'Биометрическое подтверждение обязательно для оплаты кошельком.' });
                                return;
                            }
                        }

                        this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}', payload)
                            .then(response => {
                                window.location.href = response.data.data.redirect ? response.data.data.redirect_url : '{{ route('shop.checkout.onepage.success') }}';
                            })
                            .catch(error => {
                                this.isPlacingOrder = false;
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Ошибка оформления заказа' });
                            });
                    }
                },
            });
        </script>
    @endpush
</x-shop::layouts>