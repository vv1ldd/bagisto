@push('meta')
<meta name="description" content="@lang('shop::app.customers.signup-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.signup-form.page-title')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
        </x-slot>

        <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
            <!-- Left Side: Form -->
            <div
                class="flex w-full flex-col min-h-screen px-8 pt-12 pb-6 md:px-10 md:pt-16 md:pb-10 lg:px-20 lg:pt-20 lg:pb-20 md:w-1/2 overflow-y-auto">
                <!-- Header/Logo -->
                <div class="mb-8 flex items-center justify-between">
                    <a href="{{ route('shop.home.index') }}"
                        aria-label="@lang('shop::app.customers.signup-form.bagisto')">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                            alt="{{ config('app.name') }}" width="120" class="h-auto">
                    </a>
                </div>

                <!-- Form Area -->
                <div class="flex flex-grow flex-col justify-center py-10">
                    <div class="mx-auto w-full max-w-[400px]">

                        {!! view_render_event('bagisto.shop.customers.signup.before') !!}

                        @if (session('status') === 'verification-sent')
                            <div
                                class="mb-8 flex h-14 w-14 items-center justify-center rounded-full bg-[#7C45F5]/10 mx-auto">
                                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#7C45F5"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>

                            <div class="mb-8 text-center">
                                <h1 class="font-dmserif text-4xl font-normal text-zinc-800 md:text-5xl">
                                    Подтвердите почту
                                </h1>

                                <p class="mt-4 text-lg text-zinc-500 text-center">
                                    Мы отправили письмо на <br>
                                    <span class="font-semibold text-zinc-800">{{ session('email') }}</span>
                                </p>
                            </div>

                            <div class="mt-12 p-6 rounded-2xl bg-[#7C45F5]/5 border border-[#7C45F5]/10">
                                <p class="text-zinc-600 leading-relaxed">
                                    Мы отправили вам письмо со специальной ссылкой. <br><br>
                                    Перейдите по ней, чтобы подтвердить свой аккаунт и продолжить настройку профиля. Код
                                    вводить не нужно — всё произойдет автоматически.
                                </p>
                            </div>

                            <p class="mt-8 text-center text-sm text-zinc-500">
                                Не пришло письмо?
                                <a href="{{ route('shop.customers.resend.verification_email', session('email')) }}"
                                    class="font-semibold text-[#7C45F5] hover:text-[#6534d4]">
                                    Отправить снова
                                </a>
                            </p>
                        @else
                            <x-shop::form :action="route('shop.customers.register.store')">
                                {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                                <!-- Email -->
                                <x-shop::form.control-group class="mb-4">
                                    <x-shop::form.control-group.label
                                        class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                        @lang('shop::app.customers.signup-form.email')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control type="email"
                                        class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                        name="email" rules="required|email" :value="old('email')"
                                        :label="trans('shop::app.customers.signup-form.email')"
                                        placeholder="email@example.com" />

                                    <x-shop::form.control-group.error control-name="email" />
                                </x-shop::form.control-group>

                                <!-- Options: Newsletter & GDPR -->
                                <div class="flex flex-col gap-3 mb-8">

                                    @if(core()->getConfigData('general.gdpr.settings.enabled') && core()->getConfigData('general.gdpr.agreement.enabled'))
                                        <div class="flex select-none items-center gap-2">
                                            <x-shop::form.control-group.control type="checkbox" name="agreement" id="agreement"
                                                value="1" rules="required" class="hidden peer" />
                                            <label
                                                class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue"
                                                for="agreement"></label>
                                            <label class="cursor-pointer text-sm text-zinc-500" for="agreement">
                                                {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                                                @if (core()->getConfigData('general.gdpr.agreement.agreement_content'))
                                                    <span class="underline cursor-pointer ml-1" @click="$refs.termsModal.open()">
                                                        @lang('shop::app.customers.signup-form.click-here')
                                                    </span>
                                                @endif
                                            </label>
                                        </div>
                                        <x-shop::form.control-group.error control-name="agreement" />
                                    @endif
                                </div>

                                <!-- Captcha -->
                                @if (core()->getConfigData('customer.captcha.credentials.status'))
                                    <div class="mb-8">
                                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                        <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                                    </div>
                                @endif

                                <div class="mt-8 text-center text-[13px] text-zinc-500 leading-relaxed">
                                    @lang('shop::app.customers.signup-form.agreement')
                                </div>

                                <div class="mt-6">
                                    <button
                                        class="w-full rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20"
                                        type="submit">
                                        @lang('shop::app.customers.signup-form.button-title')
                                    </button>

                                    {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}
                                </div>
                            </x-shop::form>

                            {!! view_render_event('bagisto.shop.customers.signup.after') !!}

                            <p class="mt-8 text-center text-sm text-zinc-500">
                                @lang('shop::app.customers.signup-form.account-exists')
                                <a class="font-bold text-zinc-800 hover:underline"
                                    href="{{ route('shop.customer.session.index') }}">
                                    @lang('shop::app.customers.signup-form.sign-in-button')
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-auto pt-10 text-center text-xs text-zinc-400">
                    @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
                </div>
            </div>

            <!-- Right Side: Artistic Image -->
            @php
                $bgConfig = core()->getConfigData('customer.login_page.background_image');
                $bgImageUrl = $bgConfig ? Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2670&auto=format&fit=crop';
            @endphp
            <div class="hidden md:block md:w-1/2">
                <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                    style="background-image: url('{{ $bgImageUrl }}')">
                    <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                        <div class="max-w-md">
                            {{-- Optional caption --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        @endpush

        <!-- Terms & Conditions Modal -->
        <x-shop::modal ref="termsModal">
            <x-slot:toggle></x-slot>
                <x-slot:header class="!p-5">
                    <p class="text-xl font-bold">@lang('shop::app.customers.signup-form.terms-conditions')</p>
                    </x-slot>
                    <x-slot:content class="!p-5">
                        <div class="max-h-[500px] overflow-auto prose prose-sm max-w-none">
                            {!! core()->getConfigData('general.gdpr.agreement.agreement_content') !!}
                        </div>
                        </x-slot>
        </x-shop::modal>
</x-shop::layouts>