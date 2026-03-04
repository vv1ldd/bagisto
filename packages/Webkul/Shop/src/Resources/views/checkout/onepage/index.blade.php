<!-- SEO Meta Content -->
@push('meta')
<meta name="description" content="@lang('shop::app.checkout.onepage.index.checkout')" />

<meta name="keywords" content="@lang('shop::app.checkout.onepage.index.checkout')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.onepage.index.checkout')
        </x-slot>

        <style>
            body {
                background: linear-gradient(135deg, #fdf4ff 0%, #ffffff 50%, #f5f3ff 100%);
                min-height: 100vh;
            }
        </style>

        {!! view_render_event('bagisto.shop.checkout.onepage.header.before') !!}

        <!-- Page Header -->
        <div class="flex-wrap">
            <div class="flex w-full items-center justify-between px-8 py-4 max-sm:px-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2" aria-label="Meanly">
                        <span class="text-[28px] font-black tracking-tighter text-[#7C45F5]">MEANLY</span>
                    </a>
                </div>

                <!-- Page Title Pill -->
                <div class="absolute left-1/2 -translate-x-1/2 max-md:hidden">
                    <div class="rounded-full border border-white/60 bg-white/40 px-5 py-1.5 backdrop-blur-md shadow-sm">
                        <h1 class="text-sm font-semibold text-zinc-800">
                            @lang('shop::app.checkout.onepage.index.checkout')
                        </h1>
                    </div>
                </div>

                <!-- User/Login -->
                <div class="flex items-center gap-4">
                    @guest('customer')
                        @include('shop::checkout.login')
                    @else
                        <div
                            class="flex items-center gap-3 rounded-full bg-white/40 p-1 pr-4 backdrop-blur-md border border-white/60">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#7C45F5] text-white font-bold text-xs uppercase">
                                {{ substr(auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-zinc-700">@
                                {{ auth()->guard('customer')->user()->credits_alias ?: auth()->guard('customer')->user()->username }}</span>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.checkout.onepage.header.after') !!}

        <!-- Page Content -->
        <div class="container mt-4 px-8 max-sm:px-4 mx-auto max-w-7xl">
            <!-- Checkout Vue Component -->
            <v-checkout>
                <!-- Shimmer Effect -->
                <x-shop::shimmer.checkout.onepage />
            </v-checkout>
        </div>

        @pushOnce('scripts')
            <script type="text/x-template" id="v-checkout-template">
                                    <template v-if="! cart">
                                        <!-- Shimmer Effect -->
                                        <x-shop::shimmer.checkout.onepage />
                                    </template>

                                    <template v-else>
                                        <div class="grid grid-cols-[1fr_auto] gap-8 max-lg:grid-cols-[1fr] max-md:gap-5">
                                            <!-- Included Checkout Summary Blade File For Mobile view -->
                                            <div class="hidden max-md:block">
                                                @include('shop::checkout.onepage.summary')
                                            </div>

                                            <div
                                                class="overflow-y-auto max-md:grid max-md:gap-4"
                                                id="steps-container"
                                            >
                                                <!-- Included Addresses Blade File -->
                                                <template v-if="['address', 'shipping', 'payment', 'review'].includes(currentStep)">
                                                    @include('shop::checkout.onepage.address')
                                                </template>

                                                <!-- Included Shipping Methods Blade File -->
                                                <template v-if="cart.have_stockable_items && ['shipping', 'payment', 'review'].includes(currentStep)">
                                                    @include('shop::checkout.onepage.shipping')
                                                </template>

                                                <!-- Included Payment Methods Blade File -->
                                                <template v-if="['payment', 'review'].includes(currentStep)">
                                                    @include('shop::checkout.onepage.payment')
                                                </template>
                                            </div>

                                            <!-- Included Checkout Summary Blade File For Desktop view -->
                                            <div class="sticky top-8 block h-max w-[442px] max-w-full max-lg:w-auto max-lg:max-w-[442px] ltr:pl-8 max-lg:ltr:pl-0 rtl:pr-8 max-lg:rtl:pr-0">
                                                <div class="block max-md:hidden bg-white/40 backdrop-blur-3xl border border-white/40 rounded-2xl p-6 shadow-sm">
                                                    @include('shop::checkout.onepage.summary')
                                                </div>

                                                <div
                                                    class="flex flex-col gap-4 mt-8"
                                                    v-if="canPlaceOrder"
                                                >
                                                    <template v-if="cart.payment_method == 'paypal_smart_button'">
                                                        {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button.before') !!}

                                                        <!-- Paypal Smart Button Vue Component -->
                                                        <v-paypal-smart-button></v-paypal-smart-button>

                                                        {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button.after') !!}
                                                    </template>

                                                    <template v-else>
                                                        <button
                                                            type="button"
                                                            class="primary-button flex w-full items-center justify-center rounded-full bg-[#7C45F5] py-5 text-lg font-black text-white shadow-[0_10px_20px_-5px_rgba(124,69,245,0.4)] transition-all hover:bg-[#6b35e4] hover:shadow-[0_15px_30px_-5px_rgba(124,69,245,0.5)] active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none"
                                                            :disabled="isPlacingOrder"
                                                            @click="placeOrder"
                                                        >
                                                            <span v-if="!isPlacingOrder">@lang('shop::app.checkout.onepage.summary.place-order')</span>
                                                            <span v-else class="flex items-center gap-2">
                                                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Обработка...
                                                            </span>
                                                        </button>

                                                        <p class="text-center text-[10px] font-medium text-zinc-400">
                                                            Нажимая на кнопку, вы соглашаетесь с <a href="#" class="underline hover:text-[#7C45F5]">условиями обслуживания</a>
                                                        </p>
                                                    </template>
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

                                    this.scrollToCurrentStep();
                                })
                                .catch(error => { });
                        },

                        stepForward(step) {
                            this.currentStep = step;

                            if (step == 'review') {
                                this.canPlaceOrder = true;

                                return;
                            }

                            this.canPlaceOrder = false;

                            if (this.currentStep == 'shipping') {
                                this.shippingMethods = null;
                            } else if (this.currentStep == 'payment') {
                                this.paymentMethods = null;
                            }
                        },

                        stepProcessed(data) {
                            if (this.currentStep == 'shipping') {
                                this.shippingMethods = data;
                            } else if (this.currentStep == 'payment') {
                                this.paymentMethods = data;
                            }

                            this.getCart();
                        },

                        scrollToCurrentStep() {
                            let container = document.getElementById('steps-container');

                            if (!container) {
                                return;
                            }

                            container.scrollIntoView({
                                behavior: 'smooth',
                                block: 'end'
                            });
                        },

                        placeOrder() {
                            this.isPlacingOrder = true;

                            this.$axios.post('{{ route('shop.checkout.onepage.orders.store') }}')
                                .then(response => {
                                    if (response.data.data.redirect) {
                                        window.location.href = response.data.data.redirect_url;
                                    } else {
                                        window.location.href = '{{ route('shop.checkout.onepage.success') }}';
                                    }

                                    this.isPlacingOrder = false;
                                })
                                .catch(error => {
                                    this.isPlacingOrder = false

                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                        }
                    },
                });
            </script>
        @endPushOnce
</x-shop::layouts>