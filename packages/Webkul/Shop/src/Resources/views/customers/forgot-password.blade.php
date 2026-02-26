@push('meta')
<meta name="description" content="@lang('shop::app.customers.forgot-password.title')" />
<meta name="keywords" content="@lang('shop::app.customers.forgot-password.title')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        @lang('shop::app.customers.forgot-password.title')
        </x-slot>

        <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
            <!-- Left Side: Form -->
            <div class="flex w-full flex-col min-h-screen p-10 md:w-1/2 lg:p-20">
                <!-- Header/Logo -->
                <div class="mb-12 flex items-center justify-between">
                    <a href="{{ route('shop.home.index') }}"
                        aria-label="@lang('shop::app.customers.forgot-password.bagisto')">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                            alt="{{ config('app.name') }}" width="120" class="h-auto">
                    </a>

                    <a href="{{ route('shop.customer.session.index') }}"
                        class="rounded-full border border-zinc-200 px-6 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50">
                        @lang('shop::app.customers.forgot-password.sign-in-button')
                    </a>
                </div>

                <div class="m-auto w-full max-w-[400px]">

                    {!! view_render_event('bagisto.shop.customers.forget_password.before') !!}

                    <div class="mt-4">
                        <x-shop::form :action="route('shop.customers.forgot_password.store')">
                            {!! view_render_event('bagisto.shop.customers.forget_password_form_controls.before') !!}

                            <!-- Email -->
                            <x-shop::form.control-group class="mb-4">
                                <x-shop::form.control-group.label
                                    class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                    @lang('shop::app.customers.login-form.email')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="email"
                                    class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                    name="email" rules="required|email" value=""
                                    :label="trans('shop::app.customers.login-form.email')"
                                    placeholder="email@example.com" />

                                <x-shop::form.control-group.error control-name="email" />
                            </x-shop::form.control-group>

                            <!-- Captcha -->
                            @if (core()->getConfigData('customer.captcha.credentials.status'))
                                <div class="mb-8">
                                    {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                    <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                                </div>
                            @endif

                            <div class="mt-8">
                                <button
                                    class="w-full rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20"
                                    type="submit">
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
                </div>

                <!-- Footer -->
                <div class="mt-auto pt-10 text-center text-xs text-zinc-400">
                    @lang('shop::app.customers.forgot-password.footer', ['current_year' => date('Y')])
                </div>
            </div>

            <!-- Right Side: Artistic Image -->
            @php
                $bgConfig = core()->getConfigData('customer.login_page.background_image');
                $bgImageUrl = $bgConfig ? Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2564&auto=format&fit=crop';
            @endphp
            <div class="hidden md:block md:w-1/2">
                <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                    style="background-image: url('{{ $bgImageUrl }}')">
                    <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        @endpush
</x-shop::layouts>