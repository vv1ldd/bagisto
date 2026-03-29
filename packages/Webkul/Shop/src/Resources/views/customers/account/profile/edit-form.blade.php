{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.before', ['customer' => $customer]) !!}

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.image.after') !!}

<div class="mt-2 w-full max-w-[600px] mx-auto px-4">
    {{-- Main Profile Info --}}
    <div class="mb-4">
        <h3 class="ios-section-label !px-0">Основная информация</h3>
        
        <div class="nav-grid">
            {{-- Pseudonym --}}
            <div class="nav-tile !p-0 overflow-hidden items-center" :class="{'!border-red-500 shadow-[0_0_10px_rgba(239,68,68,0.2)]': usernameError, '!border-green-500 shadow-[0_0_10px_rgba(34,197,94,0.2)]': !usernameError && customer.username}">
                <div class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] text-white border-r border-black/10 shrink-0">
                    <span class="text-xl font-black">@</span>
                </div>
                <label class="nav-label pl-3 pr-2 py-3 flex-shrink-0">Никнейм</label>
                <div class="flex-grow flex items-center justify-end pr-4">
                    <x-shop::form.control-group class="!mb-0 flex-1">
                        <x-shop::form.control-group.control type="text" name="username" rules="required"
                            class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                            :value="old('username') ?? (str_starts_with($customer->username, 'user_') ? '' : $customer->username)" placeholder="nickname"
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            label="Псевдоним"
                            v-on:focus="clearDefaultUsername($event)"
                            v-on:input="debounceCheckUsername($event.target.value)" />
                    </x-shop::form.control-group>
                </div>
            </div>
            <div v-if="usernameError" class="px-4 py-1 text-[10px] text-red-500 font-bold uppercase tracking-wider">
                <i class="icon-toast-error mr-1"></i> @{{ usernameError }}
            </div>

            {{-- First Name --}}
            <div class="nav-tile !p-0 overflow-hidden items-center mt-1">
                <div class="w-12 h-12 flex items-center justify-center bg-[#00D1FF] text-white border-r border-black/10 shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <label class="nav-label pl-3 pr-2 py-3 flex-shrink-0">Имя</label>
                <div class="flex-grow flex items-center justify-end pr-4">
                    <x-shop::form.control-group class="!mb-0 flex-1">
                        <x-shop::form.control-group.control type="text" name="first_name"
                            class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                            :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                            placeholder="Введите имя" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                    </x-shop::form.control-group>
                </div>
            </div>

            {{-- Last Name --}}
            <div class="nav-tile !p-0 overflow-hidden items-center mt-1">
                <div class="w-12 h-12 flex items-center justify-center bg-[#00D1FF] text-white border-r border-black/10 shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <label class="nav-label pl-3 pr-2 py-3 flex-shrink-0">Фамилия</label>
                <div class="flex-grow flex items-center justify-end pr-4">
                    <x-shop::form.control-group class="!mb-0 flex-1">
                        <x-shop::form.control-group.control type="text" name="last_name"
                            class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                            :value="old('last_name') ?? $customer->last_name" placeholder="Введите фамилию"
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                    </x-shop::form.control-group>
                </div>
            </div>

            <!-- Email Hidden -->
            @if ($customer->email)
                <input type="hidden" name="email" value="{{ $customer->email }}">
            @endif

        </div>
    </div>

    {{-- Save Button --}}
    <div class="mt-6">
        <button type="submit"
            :disabled="!meta.valid || !!usernameError"
            class="w-full bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-[13px] transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
            @lang('shop::app.customers.account.profile.edit.save')
        </button>
    </div>
</div>
</div>

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}
