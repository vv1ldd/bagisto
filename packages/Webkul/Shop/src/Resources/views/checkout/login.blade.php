<!-- Checkout Login Vue JS Component -->
<v-checkout-login>
    <div class="flex items-center">
        <span
            class="cursor-pointer rounded-full border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-6 py-2 text-sm font-bold text-[#7C45F5] transition-all hover:bg-[#7C45F5]/10 active:scale-95">
            @lang('shop::app.checkout.login.title')
        </span>
    </div>
</v-checkout-login>

@pushOnce('scripts')
    {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}

    <script type="text/x-template" id="v-checkout-login-template">
            <div>
                <div class="flex items-center">
                    <span
                        class="cursor-pointer rounded-full border border-[#7C45F5]/20 bg-[#7C45F5]/5 px-6 py-2 text-sm font-bold text-[#7C45F5] transition-all hover:bg-[#7C45F5]/10 active:scale-95"
                        role="button"
                        @click="$refs.loginModel.open()"
                    >
                        @lang('shop::app.checkout.login.title')
                    </span>
                </div>

                <!-- Login Form -->
                <x-shop::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    {!! view_render_event('bagisto.shop.checkout.login.before') !!}

                    <!-- Login form -->
                    <form @submit="handleSubmit($event, login)">
                        {!! view_render_event('bagisto.shop.checkout.login.form_controls.before') !!}

                        <!-- Login modal -->
                        <x-shop::modal ref="loginModel">
                            <!-- Modal Header -->
                            <x-slot:header class="max-md:p-5">
                                <h2 class="text-xl font-bold text-zinc-800">
                                    @lang('shop::app.checkout.login.title')
                                </h2>
                            </x-slot>

                            <!-- Modal Content -->
                            <x-slot:content class="!px-6 py-4">
                                <!-- Email -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required text-xs font-bold text-zinc-400 uppercase tracking-wider">
                                        @lang('shop::app.checkout.login.email')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="email"
                                        class="rounded-xl border border-zinc-200 px-6 py-4 focus:border-[#7C45F5] focus:ring-[#7C45F5]"
                                        name="email"
                                        rules="required|email"
                                        :label="trans('shop::app.checkout.login.email')"
                                        placeholder="email@example.com"
                                        :aria-label="trans('shop::app.checkout.login.email')"
                                        aria-required="true"
                                    />

                                    <x-shop::form.control-group.error class="mt-2 text-xs font-bold text-red-500" control-name="email" />
                                </x-shop::form.control-group>

                                <!-- Password -->
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.label class="required text-xs font-bold text-zinc-400 uppercase tracking-wider">
                                        @lang('shop::app.checkout.login.password')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="password"
                                        class="rounded-xl border border-zinc-200 px-6 py-4 focus:border-[#7C45F5] focus:ring-[#7C45F5]"
                                        id="password"
                                        name="password"
                                        rules="required|min:6"
                                        :label="trans('shop::app.checkout.login.password')"
                                        :placeholder="trans('shop::app.checkout.login.password')"
                                        :aria-label="trans('shop::app.checkout.login.password')"
                                        aria-required="true"
                                    />

                                    <x-shop::form.control-group.error class="mt-2 text-xs font-bold text-red-500" control-name="password" />
                                </x-shop::form.control-group>

                                <!-- Captcha -->
                                @if (core()->getConfigData('customer.captcha.credentials.status'))
                                    <x-shop::form.control-group class="mt-5">
                                        {!! \Webkul\Customer\Facades\Captcha::render() !!}

                                        <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                                    </x-shop::form.control-group>
                                @endif
                            </x-slot>

                            <!-- Modal Footer -->
                            <x-slot:footer class="!px-6 pb-8">
                                <div class="flex flex-wrap items-center gap-4">
                                    <x-shop::button
                                        class="primary-button w-full rounded-full bg-[#7C45F5] py-4 text-base font-bold text-white shadow-lg transition-all hover:bg-[#6b35e4] hover:shadow-xl active:scale-95 disabled:opacity-50"
                                        :title="trans('shop::app.checkout.login.title')"
                                        ::loading="isStoring"
                                        ::disabled="isStoring"
                                    />
                                </div>
                            </x-slot>
                        </x-shop::modal>

                        {!! view_render_event('bagisto.shop.checkout.login.form_controls.after') !!}
                    </form>
                </x-shop::form>

                {!! view_render_event('bagisto.shop.checkout.login.after') !!}
            </div>
        </script>

    <script type="module">
        app.component('v-checkout-login', {
            template: '#v-checkout-login-template',

            data() {
                return {
                    isStoring: false,
                }
            },

            methods: {
                login(params, {
                    resetForm,
                    setErrors
                }) {
                    this.isStoring = true;

                    const captchaResponse = document.querySelector('[name="g-recaptcha-response"]')?.value

                    params['g-recaptcha-response'] = captchaResponse;

                    this.$axios.post("{{ route('shop.api.customers.session.create') }}", params)
                        .then((response) => {
                            this.isStoring = false;

                            window.location.reload();
                        })
                        .catch((error) => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);

                                return;
                            }

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response.data.message
                            });
                        });
                },
            }
        })
    </script>
@endPushOnce