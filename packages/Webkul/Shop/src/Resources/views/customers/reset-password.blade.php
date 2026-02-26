@push('meta')
<meta name="description" content="@lang('shop::app.customers.reset-password.title')" />
<meta name="keywords" content="@lang('shop::app.customers.reset-password.title')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        @lang('shop::app.customers.reset-password.title')
        </x-slot>

        <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
            <!-- Left Side: Form -->
            <div
                class="flex w-full flex-col min-h-screen px-8 pt-12 pb-6 md:px-10 md:pt-16 md:pb-10 lg:px-20 lg:pt-20 lg:pb-20 md:w-1/2">
                <!-- Header/Logo -->
                <div class="mb-12 flex items-center justify-between">
                    <a href="{{ route('shop.home.index') }}"
                        aria-label="@lang('shop::app.customers.reset-password.bagisto')">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                            alt="{{ config('app.name') }}" width="120" class="h-auto">
                    </a>

                    <a href="{{ route('shop.customer.session.index') }}"
                        class="rounded-full border border-zinc-200 px-6 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50">
                        @lang('shop::app.customers.reset-password.back-link-title')
                    </a>
                </div>

                <div class="m-auto w-full max-w-[400px]">

                    {!! view_render_event('bagisto.shop.customers.reset_password.before') !!}

                    <div class="mt-4">
                        <x-shop::form :action="route('shop.customers.reset_password.store')">
                            <x-shop::form.control-group.control type="hidden" name="token" :value="$token" />

                            {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.before') !!}

                            <!-- Email -->
                            <x-shop::form.control-group class="mb-6">
                                <x-shop::form.control-group.label
                                    class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                    @lang('shop::app.customers.reset-password.email')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="email"
                                    class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                    name="email" rules="required|email" :value="old('email')"
                                    :label="trans('shop::app.customers.reset-password.email')"
                                    placeholder="email@example.com" />

                                <x-shop::form.control-group.error control-name="email" />
                            </x-shop::form.control-group>

                            <!-- Password -->
                            <x-shop::form.control-group class="mb-6">
                                <x-shop::form.control-group.label
                                    class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                    @lang('shop::app.customers.reset-password.password')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="password"
                                    class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                    name="password" rules="required|min:6" value=""
                                    :label="trans('shop::app.customers.reset-password.password')"
                                    :placeholder="trans('shop::app.customers.reset-password.password')"
                                    ref="password" />

                                <x-shop::form.control-group.error control-name="password" />
                            </x-shop::form.control-group>

                            <!-- Confirm Password -->
                            <x-shop::form.control-group class="mb-8">
                                <x-shop::form.control-group.label
                                    class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                    @lang('shop::app.customers.reset-password.confirm-password')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="password"
                                    class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                    name="password_confirmation" rules="confirmed:@password" value=""
                                    :label="trans('shop::app.customers.reset-password.confirm-password')"
                                    :placeholder="trans('shop::app.customers.reset-password.confirm-password')" />

                                <x-shop::form.control-group.error control-name="password_confirmation" />
                            </x-shop::form.control-group>

                            <div class="mt-8">
                                <button
                                    class="w-full rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20"
                                    type="submit">
                                    @lang('shop::app.customers.reset-password.submit-btn-title')
                                </button>
                            </div>

                            {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.after') !!}
                        </x-shop::form>
                    </div>

                    {!! view_render_event('bagisto.shop.customers.reset_password.after') !!}
                </div>

                <!-- Footer -->
                <div class="mt-auto pt-10 text-center text-xs text-zinc-400">
                    @lang('shop::app.customers.reset-password.footer', ['current_year' => date('Y')])
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
</x-shop::layouts>