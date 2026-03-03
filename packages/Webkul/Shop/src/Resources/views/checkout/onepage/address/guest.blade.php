<!-- Guest Checkout Vue Component -->
<v-checkout-address-guest :cart="cart" @processing="stepForward" @processed="stepProcessed"></v-checkout-address-guest>

{!! view_render_event('bagisto.shop.checkout.onepage.address.guest.after') !!}

@include('shop::checkout.onepage.address.form')

@pushOnce('scripts')
    <script type="text/x-template" id="v-checkout-address-guest-template">
        <div class="transition-all duration-500">
            <!-- State: Selection -->
            <div v-if="state == 'selection'" class="p-8 text-center bg-white/40 backdrop-blur-md rounded-2xl border border-white/20 shadow-sm">
                <h2 class="text-2xl font-medium text-navyBlue mb-8">@lang('shop::app.checkout.onepage.address.selection-title')</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Login -->
                    <a href="{{ route('shop.customer.session.index') }}?return_to=checkout" 
                       class="p-6 rounded-2xl border border-zinc-200 hover:border-navyBlue hover:bg-navyBlue/5 transition-all group">
                        <span class="icon-profile text-4xl text-zinc-400 group-hover:text-navyBlue mb-4 block"></span>
                        <p class="font-medium text-navyBlue">@lang('shop::app.checkout.onepage.address.login')</p>
                    </a>

                    <!-- Register -->
                    <a href="{{ route('shop.customer.register.index') }}?return_to=checkout" 
                       class="p-6 rounded-2xl border border-zinc-200 hover:border-navyBlue hover:bg-navyBlue/5 transition-all group">
                        <span class="icon-plus text-4xl text-zinc-400 group-hover:text-navyBlue mb-4 block"></span>
                        <p class="font-medium text-navyBlue">@lang('shop::app.checkout.onepage.address.register')</p>
                    </a>

                    <!-- Guest -->
                    <div @click="state = 'email'" 
                         class="p-6 rounded-2xl border border-zinc-200 hover:border-purple-600 hover:bg-purple-50 cursor-pointer transition-all group">
                        <span class="icon-cart text-4xl text-zinc-400 group-hover:text-purple-600 mb-4 block"></span>
                        <p class="font-medium text-navyBlue group-hover:text-purple-700">@lang('shop::app.checkout.onepage.address.guest-checkout')</p>
                    </div>
                </div>
            </div>

            <!-- State: Email Input -->
            <div v-if="state == 'email'" class="max-w-md mx-auto p-8 bg-white/40 backdrop-blur-md rounded-2xl border border-white/20 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <span class="icon-arrow-left text-2xl cursor-pointer text-zinc-400 hover:text-navyBlue" @click="state = 'selection'"></span>
                    <h2 class="text-xl font-medium text-navyBlue">@lang('shop::app.checkout.onepage.address.enter-email')</h2>
                </div>

                <div class="mb-6 text-zinc-500 text-sm">
                    На указанный адрес будет отправлен одноразовый код для подтверждения.
                </div>

                <x-shop::form.control-group>
                    <x-shop::form.control-group.control
                        type="email"
                        name="email"
                        v-model="email"
                        class="!rounded-xl border-zinc-200 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="example@mail.com"
                        rules="required|email"
                    />
                </x-shop::form.control-group>

                <div class="mt-8">
                    <x-shop::button
                        class="primary-button w-full rounded-xl py-3 shadow-lg hover:shadow-xl transition-all"
                        ::disabled="!email || isSendingOtp"
                        ::loading="isSendingOtp"
                        @click="sendOtp"
                        title="{{ trans('shop::app.checkout.onepage.address.send-otp') }}"
                    />
                </div>
            </div>

            <!-- State: OTP Verification -->
            <div v-if="state == 'otp'" class="max-w-md mx-auto p-8 bg-white/40 backdrop-blur-md rounded-2xl border border-white/20 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <span class="icon-arrow-left text-2xl cursor-pointer text-zinc-400 hover:text-navyBlue" @click="state = 'email'"></span>
                    <h2 class="text-xl font-medium text-navyBlue">@lang('shop::app.checkout.onepage.address.verify-otp')</h2>
                </div>

                <div class="mb-6 p-4 rounded-xl bg-purple-50 border border-purple-100 text-purple-700 text-sm">
                    <p class="font-medium mb-1">@lang('shop::app.checkout.onepage.address.otp-sent')</p>
                    <p class="opacity-80">@{{ email }}</p>
                </div>

                <x-shop::form.control-group>
                    <x-shop::form.control-group.control
                        type="text"
                        name="otp"
                        v-model="otp"
                        class="!rounded-xl border-zinc-200 focus:border-purple-500 focus:ring-purple-500 text-center text-2xl tracking-[0.5em] font-bold"
                        maxlength="6"
                        placeholder="000000"
                    />
                </x-shop::form.control-group>
                
                <p v-if="otpError" class="mt-2 text-sm text-red-500 text-center">@{{ otpError }}</p>

                <div class="mt-8">
                    <x-shop::button
                        class="primary-button w-full rounded-xl py-3 shadow-lg hover:shadow-xl transition-all"
                        ::disabled="otp.length < 6 || isVerifyingOtp"
                        ::loading="isVerifyingOtp"
                        @click="verifyOtp"
                        title="{{ trans('shop::app.checkout.onepage.address.confirm') }}"
                    />
                </div>
                
                <p class="mt-6 text-center text-sm text-zinc-400">
                    Не получили код? 
                    <button @click="sendOtp" class="text-purple-600 font-medium hover:underline" :disabled="isSendingOtp">Отправить еще раз</button>
                </p>
            </div>

            <!-- State: Address Form -->
            <div v-if="state == 'address'">
                <x-shop::form v-slot="{ meta, errors, handleSubmit }" as="div">
                    <form @submit="handleSubmit($event, addAddress)">
                        <div class="mb-4">
                            <!-- Billing Address Header -->
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-medium text-navyBlue">
                                    @lang('shop::app.checkout.onepage.address.billing-address')
                                </h2>
                                <p class="text-sm text-zinc-500">@{{ email }}</p>
                            </div>

                            <!-- Billing Address Form -->
                            <v-checkout-address-form control-name="billing"
                                :address="{ email: email, ... (cart.billing_address || {}) }"></v-checkout-address-form>

                            <!-- Use for Shipping Checkbox -->
                            <x-shop::form.control-group class="!mb-0 flex items-center gap-2.5 mt-4" v-if="cart.have_stockable_items">
                                <x-shop::form.control-group.control type="checkbox" name="billing.use_for_shipping"
                                    id="use_for_shipping" for="use_for_shipping" value="1"
                                    @change="useBillingAddressForShipping = ! useBillingAddressForShipping"
                                    ::checked="!! useBillingAddressForShipping" />

                                <label
                                    class="cursor-pointer select-none text-base text-zinc-500"
                                    for="use_for_shipping">
                                    @lang('shop::app.checkout.onepage.address.same-as-billing')
                                </label>
                            </x-shop::form.control-group>
                        </div>

                        <!-- Guest Shipping Address -->
                        <template v-if="cart.have_stockable_items">
                            <div class="mt-8" v-if="! useBillingAddressForShipping">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-xl font-medium text-navyBlue">
                                        @lang('shop::app.checkout.onepage.address.shipping-address')
                                    </h2>
                                </div>

                                <v-checkout-address-form control-name="shipping"
                                    :address="cart.shipping_address || undefined"></v-checkout-address-form>
                            </div>
                        </template>

                        <!-- Proceed Button -->
                        <div class="mt-8 flex justify-end">
                            <x-shop::button
                                class="primary-button rounded-2xl px-12 py-4 text-lg shadow-lg hover:shadow-xl transition-all"
                                :title="trans('shop::app.checkout.onepage.address.proceed')" 
                                ::loading="isStoring"
                                ::disabled="isStoring" />
                        </div>
                    </form>
                </x-shop::form>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-checkout-address-guest', {
            template: '#v-checkout-address-guest-template',

            props: ['cart'],

            emits: ['processing', 'processed'],

            data() {
                return {
                    state: 'selection',

                    email: '',

                    otp: '',

                    isSendingOtp: false,

                    isVerifyingOtp: false,

                    otpError: '',

                    useBillingAddressForShipping: true,

                    isStoring: false,
                }
            },

            created() {
                if (this.cart.billing_address) {
                    this.useBillingAddressForShipping = this.cart.billing_address.use_for_shipping;
                    
                    // If already has address and verified (backend session handles actual verification), 
                    // we could jump to address state. 
                    // But for simplicity of this flow demo, we start at selection if no email in cart.
                    if (this.cart.billing_address.email) {
                        this.email = this.cart.billing_address.email;
                        this.state = 'address';
                    }
                }
            },

            methods: {
                sendOtp() {
                    this.isSendingOtp = true;
                    this.otpError = '';

                    this.$axios.post('{{ route('shop.api.checkout.onepage.send_otp') }}', {
                        email: this.email
                    })
                    .then(response => {
                        this.isSendingOtp = false;
                        this.state = 'otp';
                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                    })
                    .catch(error => {
                        this.isSendingOtp = false;
                        this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message || 'Ошибка при отправке кода' });
                    });
                },

                verifyOtp() {
                    this.isVerifyingOtp = true;
                    this.otpError = '';

                    this.$axios.post('{{ route('shop.api.checkout.onepage.verify_otp') }}', {
                        email: this.email,
                        otp: this.otp
                    })
                    .then(response => {
                        this.isVerifyingOtp = false;
                        this.state = 'address';
                        this.$emitter.emit('add-flash', { type: 'success', message: 'Email подтвержден' });
                    })
                    .catch(error => {
                        this.isVerifyingOtp = false;
                        this.otpError = error.response.data.message || 'Неверный код';
                    });
                },

                addAddress(params, { setErrors }) {
                    this.isStoring = true;

                    params['billing']['use_for_shipping'] = this.useBillingAddressForShipping;
                    params['billing']['email'] = this.email;

                    this.moveToNextStep();

                    this.$axios.post('{{ route('shop.checkout.onepage.addresses.store') }}', params)
                        .then((response) => {
                            this.isStoring = false;

                            if (response.data.data.redirect_url) {
                                window.location.href = response.data.data.redirect_url;
                            } else {
                                if (this.cart.have_stockable_items) {
                                    this.$emit('processed', response.data.data.shippingMethods);
                                } else {
                                    this.$emit('processed', response.data.data.payment_methods);
                                }
                            }
                        })
                        .catch(error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            }
                        });
                },

                moveToNextStep() {
                    if (this.cart.have_stockable_items) {
                        this.$emit('processing', 'shipping');
                    } else {
                        this.$emit('processing', 'payment');
                    }
                }
            }
        });
    </script>
@endPushOnce