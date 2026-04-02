<!-- Guest Checkout Vue Component -->
<v-checkout-address-guest :cart="cart" @processing="stepForward" @processed="stepProcessed"></v-checkout-address-guest>

{!! view_render_event('bagisto.shop.checkout.onepage.address.guest.after') !!}

@include('shop::checkout.onepage.address.form')

@pushOnce('scripts')
    <script type="text/x-template" id="v-checkout-address-guest-template">
        <div class="transition-all duration-500">
            <!-- State: Selection -->
            <div v-if="state == 'selection'" class="p-10 text-center bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                <h2 class="text-3xl font-black uppercase tracking-[0.2em] mb-12 text-zinc-900">@lang('shop::app.checkout.onepage.address.selection-title')</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Login -->
                    <a href="{{ route('shop.customer.session.index') }}?return_to=checkout" 
                       class="p-8 bg-white border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all group flex flex-col items-center">
                        <span class="icon-profile text-5xl text-zinc-900 mb-6 block"></span>
                        <p class="font-black uppercase tracking-widest text-zinc-900">@lang('shop::app.checkout.onepage.address.login')</p>
                    </a>

                    <!-- Register -->
                    <a href="{{ route('shop.customers.register.index') }}?return_to=checkout" 
                       class="p-8 bg-white border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all group flex flex-col items-center">
                        <span class="icon-plus text-5xl text-zinc-900 mb-6 block"></span>
                        <p class="font-black uppercase tracking-widest text-zinc-900">@lang('shop::app.checkout.onepage.address.register')</p>
                    </a>

                    <!-- Guest -->
                    <div @click="state = 'email'" 
                         class="p-8 bg-[#7C45F5] border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 cursor-pointer transition-all group flex flex-col items-center">
                        <span class="icon-cart text-5xl text-white mb-6 block"></span>
                        <p class="font-black uppercase tracking-widest text-white">@lang('shop::app.checkout.onepage.address.guest-checkout')</p>
                    </div>
                </div>
            </div>

            <!-- State: Email Input -->
            <div v-if="state == 'email'" class="max-w-2xl mx-auto p-10 bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                <div class="flex items-center gap-6 mb-10 pb-6 border-b-4 border-zinc-900">
                    <span class="icon-arrow-left text-3xl cursor-pointer text-zinc-900 hover:text-[#7C45F5]" @click="state = 'selection'"></span>
                    <h2 class="text-2xl font-black uppercase tracking-[0.15em] text-zinc-900">@lang('shop::app.checkout.onepage.address.enter-email')</h2>
                </div>

                <div class="mb-10 text-zinc-500 text-[11px] font-black uppercase tracking-widest leading-relaxed">
                    На указанный адрес будет отправлен одноразовый код для подтверждения.
                </div>

                <x-shop::form.control-group>
                    <x-shop::form.control-group.control
                        type="email"
                        name="email"
                        v-model="email"
                        class="!border-4 !border-zinc-900 !rounded-none !py-6 !px-6 !text-lg !font-bold focus:!ring-[#7C45F5]/20"
                        placeholder="example@mail.com"
                        rules="required|email"
                    />
                </x-shop::form.control-group>

                <div class="mt-12">
                    <button
                        class="bg-[#7C45F5] border-4 border-zinc-900 w-full py-6 text-[14px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                        :disabled="!email || isSendingOtp"
                        @click="sendOtp"
                    >
                        <span v-if="!isSendingOtp">@lang('shop::app.checkout.onepage.address.send-otp')</span>
                        <span v-else class="animate-pulse">@lang('shop::app.checkout.onepage.address.send-otp')...</span>
                    </button>
                </div>
            </div>

            <!-- State: OTP Verification -->
            <div v-if="state == 'otp'" class="max-w-2xl mx-auto p-10 bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]">
                <div class="flex items-center gap-6 mb-10 pb-6 border-b-4 border-zinc-900">
                    <span class="icon-arrow-left text-3xl cursor-pointer text-zinc-900 hover:text-[#7C45F5]" @click="state = 'email'"></span>
                    <h2 class="text-2xl font-black uppercase tracking-[0.15em] text-zinc-900">@lang('shop::app.checkout.onepage.address.verify-otp')</h2>
                </div>

                <div class="mb-10 p-6 bg-[#7C45F5] border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] text-white">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-1">@lang('shop::app.checkout.onepage.address.otp-sent')</p>
                    <p class="text-lg font-black tabular-nums">@{{ email }}</p>
                </div>

                <x-shop::form.control-group>
                    <x-shop::form.control-group.control
                        type="text"
                        name="otp"
                        v-model="otp"
                        class="!border-4 !border-zinc-900 !rounded-none !py-8 !px-6 !text-3xl !font-black !text-center !tracking-[0.8em]"
                        maxlength="6"
                        placeholder="000000"
                    />
                </x-shop::form.control-group>
                
                <p v-if="otpError" class="mt-4 text-xs font-black uppercase tracking-widest text-red-600 text-center">@{{ otpError }}</p>

                <div class="mt-12">
                    <button
                        class="bg-[#7C45F5] border-4 border-zinc-900 w-full py-6 text-[14px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                        :disabled="otp.length < 6 || isVerifyingOtp"
                        @click="verifyOtp"
                    >
                        <span v-if="!isVerifyingOtp">@lang('shop::app.checkout.onepage.address.confirm')</span>
                        <span v-else class="animate-pulse">@lang('shop::app.checkout.onepage.address.confirm')...</span>
                    </button>
                </div>
                
                <p class="mt-8 text-center text-[10px] font-black uppercase tracking-widest text-zinc-400">
                    Не получили код? 
                    <button @click="sendOtp" class="text-[#7C45F5] hover:underline" :disabled="isSendingOtp">Отправить еще раз</button>
                </p>
            </div>

            <!-- State: Address Form -->
            <div v-if="state == 'address'">
                <x-shop::form v-slot="{ meta, errors, handleSubmit }" as="div">
                    <form @submit="handleSubmit($event, addAddress)">
                        <div class="mb-8 overflow-hidden">
                            <!-- Billing Address Header -->
                            <div class="flex items-center justify-between mb-8 pb-4 border-b-2 border-zinc-100">
                                <h2 class="text-2xl font-black uppercase tracking-[0.15em] text-zinc-900">
                                    @lang('shop::app.checkout.onepage.address.billing-address')
                                </h2>
                                <p class="text-[12px] font-black text-[#7C45F5] tabular-nums">@{{ email }}</p>
                            </div>

                            <!-- Billing Address Form -->
                            <v-checkout-address-form control-name="billing"
                                :address="{ email: email, ... (cart.billing_address || {}) }"></v-checkout-address-form>

                            <!-- Use for Shipping Checkbox -->
                            <div class="mt-8 p-6 bg-zinc-50 border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] flex items-center gap-4 cursor-pointer select-none"
                                @click="useBillingAddressForShipping = ! useBillingAddressForShipping"
                                v-if="cart.have_stockable_items">
                                
                                <div class="flex h-8 w-8 items-center justify-center border-4 border-zinc-900 transition-all shrink-0"
                                    :class="[useBillingAddressForShipping ? 'bg-[#7C45F5]' : 'bg-white']">
                                    <span v-if="useBillingAddressForShipping" class="icon-checkmark text-white font-black text-lg"></span>
                                </div>

                                <label class="cursor-pointer text-[12px] font-black uppercase tracking-widest text-zinc-900">
                                    @lang('shop::app.checkout.onepage.address.same-as-billing')
                                </label>
                            </div>
                        </div>

                        <!-- Guest Shipping Address -->
                        <template v-if="cart.have_stockable_items">
                            <div class="mt-14" v-if="! useBillingAddressForShipping">
                                <div class="flex items-center justify-between mb-8 pb-4 border-b-2 border-zinc-100">
                                    <h2 class="text-2xl font-black uppercase tracking-[0.15em] text-zinc-900">
                                        @lang('shop::app.checkout.onepage.address.shipping-address')
                                    </h2>
                                </div>

                                <v-checkout-address-form control-name="shipping"
                                    :address="cart.shipping_address || undefined"></v-checkout-address-form>
                            </div>
                        </template>

                        <!-- Proceed Button -->
                        <div class="mt-14 pt-10 border-t-4 border-zinc-900 flex justify-end">
                            <button
                                class="bg-zinc-900 border-4 border-zinc-900 px-16 py-6 text-[16px] font-black uppercase tracking-widest text-white shadow-[8px_8px_0px_0px_rgba(124,69,245,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                                :disabled="isStoring"
                                type="submit"
                            >
                                <span v-if="!isStoring">@lang('shop::app.checkout.onepage.address.proceed')</span>
                                <span v-else class="animate-spin h-6 w-6 border-4 border-white border-t-transparent rounded-none mx-auto"></span>
                            </button>
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

                    this.$axios.post('{{ route('shop.checkout.onepage.send_otp') }}', {
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

                    this.$axios.post('{{ route('shop.checkout.onepage.verify_otp') }}', {
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