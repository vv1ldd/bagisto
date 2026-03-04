<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
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

        <div class="px-4 py-3 sm:px-8 sm:py-6 max-w-7xl mx-auto" v-cloak>
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('shop.home.index') }}" class="group flex items-center gap-2">
                    <span
                        class="text-2xl font-black tracking-tighter text-[#7C45F5] transition-transform group-hover:scale-105">MEANLY</span>
                </a>

                @guest('customer')
                    @include('shop::checkout.login')
                @else
                    <div
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-white/60 border border-white/80 backdrop-blur-md shadow-sm">
                        <div
                            class="h-7 w-7 rounded-full bg-[#7C45F5] text-white flex items-center justify-center text-[10px] font-black uppercase ring-2 ring-white">
                            {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                        </div>
                        <span class="text-xs font-black text-zinc-600 truncate max-w-[120px]">
                            @
                            {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}
                        </span>
                    </div>
                @endguest
            </div>

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
                                        <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[32px] p-6 shadow-sm mb-6 overflow-hidden">
                                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/20">
                                                <h3 class="text-lg font-black text-zinc-800 uppercase tracking-tight">Ваш заказ</h3>
                                                <div class="px-3 py-1 bg-[#7C45F5] rounded-full text-white text-[10px] font-black uppercase">
                                                    @{{ cart.items.length }} поз.
                                                </div>
                                            </div>

                                            <div class="max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                                                @include('shop::checkout.onepage.summary')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[32px] p-2 shadow-sm overflow-hidden">
                                        <!-- Address -->
                                        <div class="p-4 sm:p-6 border-b border-white/40" data-step="address">
                                            <template v-if="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                                                @include('shop::checkout.onepage.address')
                                            </template>
                                        </div>

                                        <!-- Shipping -->
                                        <div v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)" class="p-4 sm:p-6 border-b border-white/40" data-step="shipping">
                                            @include('shop::checkout.onepage.shipping')
                                        </div>

                                        <!-- Payment -->
                                        <div v-if="['payment', 'review'].includes(currentStep)" class="p-4 sm:p-6" data-step="payment">
                                            @include('shop::checkout.onepage.payment')
                                        </div>
                                    </div>
                                </div>

                                <!-- Sidebar Column (RIGHT) -->
                                <div class="hidden lg:block sticky top-8 space-y-4">
                                    <div class="bg-white/60 backdrop-blur-3xl border border-white/80 rounded-[32px] p-6 shadow-xl shadow-zinc-200/50">
                                        @include('shop::checkout.onepage.summary')

                                        <div class="mt-8 space-y-4" v-if="canPlaceOrder">
                                            <button
                                                type="button"
                                                class="w-full rounded-2xl bg-[#7C45F5] py-4 text-base font-black text-white shadow-[0_10px_20px_-5px_rgba(124,69,245,0.4)] transition-all hover:bg-[#6b35e4] hover:shadow-[0_15px_30px_-5px_rgba(124,69,245,0.5)] active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
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
                            currentStep: 'address',
                            shippingMethods: null,
                            paymentMethods: null,
                            selectedPaymentMethod: null,
                            canPlaceOrder: false,
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
                        _b64ToUint8Array(base64) {
                            if (!base64) return new Uint8Array(0);
                            var padding = '='.repeat((4 - base64.length % 4) % 4);
                            var b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                            var rawData = window.atob(b64);
                            var outputArray = new Uint8Array(rawData.length);
                            for (var i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                            return outputArray;
                        },
                        _bufToBase64URL(buffer) {
                            var binary = '';
                            var bytes = new Uint8Array(buffer);
                            for (var i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
                            return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                        },
                        async payWithPasskey() {
                            this.isPlacingOrder = true;
                            try {
                                const optionsResponse = await this.$axios.post('{{ route('passkeys.login-options') }}');
                                let options = optionsResponse.data;
                                options.challenge = this._b64ToUint8Array(options.challenge);
                                if (options.allowCredentials) {
                                    options.allowCredentials.forEach(cred => { cred.id = this._b64ToUint8Array(cred.id); });
                                }
                                const credential = await navigator.credentials.get({ publicKey: options });
                                if (!credential) throw new Error('Аутентификация отменена');
                                const payload = {
                                    start_authentication_response: JSON.stringify({
                                        id: credential.id,
                                        rawId: this._bufToBase64URL(credential.rawId),
                                        response: {
                                            clientDataJSON: this._bufToBase64URL(credential.response.clientDataJSON),
                                            authenticatorData: this._bufToBase64URL(credential.response.authenticatorData),
                                            signature: this._bufToBase64URL(credential.response.signature),
                                            userHandle: credential.response.userHandle ? this._bufToBase64URL(credential.response.userHandle) : null,
                                        },
                                        type: credential.type,
                                        clientExtensionResults: credential.getClientExtensionResults() || {},
                                    })
                                };
                                await this.$axios.post('{{ route('passkeys.login') }}', payload);
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