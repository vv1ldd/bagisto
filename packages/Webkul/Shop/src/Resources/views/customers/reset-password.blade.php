@push('meta')
<meta name="description" content="@lang('shop::app.customers.reset-password.title')" />
<meta name="keywords" content="@lang('shop::app.customers.reset-password.title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.reset-password.title')
        </x-slot>

        <x-slot:header>
            <a href="{{ route('shop.customer.session.index') }}"
                class=" border border-zinc-200 px-6 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-50">
                @lang('shop::app.customers.reset-password.back-link-title')
            </a>
        </x-slot:header>

        {!! view_render_event('bagisto.shop.customers.reset_password.before') !!}

        <div class="mt-2 text-center">
            <x-shop::form :action="route('shop.customers.reset_password.store')" v-slot="{ meta }">
                <x-shop::form.control-group.control type="hidden" name="token" :value="$token" />

                {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.before') !!}

                <!-- Email -->
                <x-shop::form.control-group class="mb-4">
                    <x-shop::form.control-group.label
                        class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                        @lang('shop::app.customers.reset-password.email')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="email"
                        class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-[12px] focus:!ring-2 focus:!ring-zinc-500 w-full"
                        name="email" rules="required|email" :value="old('email')"
                        :label="trans('shop::app.customers.reset-password.email')" placeholder="email@example.com" />

                    <div v-if="meta.touched && !meta.valid">
                        <x-shop::form.control-group.error control-name="email" />
                    </div>
                </x-shop::form.control-group>

                <!-- Password -->
                <x-shop::form.control-group class="mb-4">
                    <x-shop::form.control-group.label
                        class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                        @lang('shop::app.customers.reset-password.password')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="password"
                        class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-[12px] focus:!ring-2 focus:!ring-zinc-500 w-full"
                        name="password" rules="required|min:6" value=""
                        :label="trans('shop::app.customers.reset-password.password')"
                        :placeholder="trans('shop::app.customers.reset-password.password')" ref="password" />

                    <div v-if="meta.touched && !meta.valid">
                        <x-shop::form.control-group.error control-name="password" />
                    </div>
                </x-shop::form.control-group>

                <!-- Confirm Password -->
                <x-shop::form.control-group class="mb-6">
                    <x-shop::form.control-group.label
                        class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                        @lang('shop::app.customers.reset-password.confirm-password')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="password"
                        class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-[12px] focus:!ring-2 focus:!ring-zinc-500 w-full"
                        name="password_confirmation" rules="confirmed:@password" value=""
                        :label="trans('shop::app.customers.reset-password.confirm-password')"
                        :placeholder="trans('shop::app.customers.reset-password.confirm-password')" />

                    <div v-if="meta.touched && !meta.valid">
                        <x-shop::form.control-group.error control-name="password_confirmation" />
                    </div>
                </x-shop::form.control-group>

                <div class="mt-6">
                    <button
                        class="w-full !rounded-[20px] bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5]"
                        type="submit" :disabled="!meta.valid">
                        @lang('shop::app.customers.reset-password.submit-btn-title')
                    </button>
                </div>

                {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.after') !!}
            </x-shop::form>
        </div>

        {!! view_render_event('bagisto.shop.customers.reset_password.after') !!}
</x-shop::layouts.split-screen>