@push('meta')
<meta name="description" content="@lang('shop::app.customers.forgot-password.title')" />
<meta name="keywords" content="@lang('shop::app.customers.forgot-password.title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.forgot-password.title')
        </x-slot>

        <x-slot:header>
            <a href="{{ route('shop.customer.session.index') }}"
                class=" border border-zinc-200 px-6 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50">
                @lang('shop::app.customers.forgot-password.sign-in-button')
            </a>
        </x-slot:header>

        <div class="mt-2 text-center">
            <x-shop::form :action="route('shop.customers.forgot_password.store')" v-slot="{ meta }">
                {!! view_render_event('bagisto.shop.customers.forget_password_form_controls.before') !!}

                <!-- Email -->
                <x-shop::form.control-group class="mb-2">
                    <x-shop::form.control-group.label
                        class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                        @lang('shop::app.customers.login-form.email')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="email"
                        class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-[12px] focus:!ring-2 focus:!ring-zinc-500 w-full"
                        name="email" rules="required|email" value=""
                        :label="trans('shop::app.customers.login-form.email')" placeholder="email@example.com" />
                </x-shop::form.control-group>

                <!-- Captcha -->
                @if (core()->getConfigData('customer.captcha.credentials.status'))
                    <div class="mb-4">
                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                        <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                    </div>
                @endif

                <div class="mt-6">
                    <button
                        class="w-full !rounded-[20px] bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5]"
                        type="submit" :disabled="!meta.valid">
                        @lang('shop::app.customers.forgot-password.submit')
                    </button>
                </div>

                <p class="mt-8 text-center text-sm text-zinc-500">
                    @lang('shop::app.customers.forgot-password.back')
                    <a href="{{ route('shop.customer.session.index') }}"
                        class="font-semibold text-zinc-800 hover:underline">
                        @lang('shop::app.customers.forgot-password.sign-in-button')
                    </a>
                </p>

                {!! view_render_event('bagisto.shop.customers.forget_password_form_controls.after') !!}
            </x-shop::form>
        </div>

        {!! view_render_event('bagisto.shop.customers.forget_password.after') !!}

        @push('scripts')
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        @endpush
</x-shop::layouts.split-screen>