{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.before', ['customer' => $customer]) !!}

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.image.after') !!}

<div class="mt-4 w-full max-w-[800px] mx-auto px-1">

    {{-- Main Profile Info --}}
    <div class="mb-10">
        <h3 class="ios-section-label !bg-transparent !border-0 !px-4">Профиль</h3>
        
        <div class="nav-grid">
            {{-- Pseudonym --}}
            <div class="nav-tile !p-0 overflow-hidden items-center">
                <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">Псевдоним <span class="text-red-500">*</span></label>
                <div class="flex-grow flex items-center justify-end pr-5 group">
                    <span class="text-zinc-400 mr-1 text-[15px] font-medium selection:bg-transparent">@</span>
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
                    <span class="nav-arrow !ml-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- First Name --}}
            <div class="nav-tile !p-0 overflow-hidden items-center">
                <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">@lang('shop::app.customers.account.profile.edit.first-name') <span class="text-red-500">*</span></label>
                <div class="flex-grow flex items-center justify-end pr-5">
                    <x-shop::form.control-group class="!mb-0 flex-1">
                        <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                            class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                            :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                            placeholder="Имя" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                    </x-shop::form.control-group>
                    <span class="nav-arrow !ml-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Last Name --}}
            <div class="nav-tile !p-0 overflow-hidden items-center">
                <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">@lang('shop::app.customers.account.profile.edit.last-name') <span class="text-red-500">*</span></label>
                <div class="flex-grow flex items-center justify-end pr-5">
                    <x-shop::form.control-group class="!mb-0 flex-1">
                        <x-shop::form.control-group.control type="text" name="last_name" rules="required"
                            class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                            :value="old('last_name') ?? $customer->last_name" placeholder="Фамилия"
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                    </x-shop::form.control-group>
                    <span class="nav-arrow !ml-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Email Hidden -->
            @if ($customer->email)
                <input type="hidden" name="email" value="{{ $customer->email }}">
            @endif

        </div>
    </div>

    {{-- Personal Details --}}
    @if ((empty($customer->gender) || str_starts_with($customer->gender, '$2y$')) || (empty($customer->date_of_birth) || str_starts_with($customer->date_of_birth, '$2y$')) || (empty($customer->birth_city) || str_starts_with($customer->birth_city, '$2y$')))
    <div class="mb-10">
        <h3 class="ios-section-label !bg-transparent !border-0 !px-4">Личные данные</h3>
        <div class="nav-grid">
            @if (empty($customer->gender) || str_starts_with($customer->gender, '$2y$'))
                <div class="nav-tile !p-0 overflow-hidden items-center">
                    <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">@lang('shop::app.customers.account.profile.edit.gender') <span class="text-red-500">*</span></label>
                    <div class="flex-grow flex items-center justify-end pr-5">
                        <x-shop::form.control-group class="!mb-0 flex-1">
                            <x-shop::form.control-group.control type="select" name="gender" rules="required"
                                class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full appearance-none cursor-pointer"
                                :value="old('gender') ?? (str_starts_with($customer->gender, '$2y$') ? '' : $customer->gender)"
                                :label="trans('shop::app.customers.account.profile.edit.gender')">
                                <option value="" disabled hidden>Выберите пол</option>
                                <option value="Male" {{ (($customer->gender ?? old('gender')) == 'Male') ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.male')</option>
                                <option value="Female" {{ (($customer->gender ?? old('gender')) == 'Female') ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.female')</option>
                                <option value="Other" {{ (($customer->gender ?? old('gender')) == 'Other') ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.other')</option>
                            </x-shop::form.control-group.control>
                        </x-shop::form.control-group>
                        <span class="nav-arrow !ml-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            @endif

            @if (empty($customer->date_of_birth) || str_starts_with($customer->date_of_birth, '$2y$'))
                <div class="nav-tile !p-0 overflow-hidden items-center">
                    <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">@lang('shop::app.customers.account.profile.edit.dob') <span class="text-red-500">*</span></label>
                    <div class="flex-grow flex items-center justify-end pr-5">
                        <x-shop::form.control-group class="!mb-0 flex-1">
                            <x-shop::form.control-group.control type="date" name="date_of_birth"
                                rules="required" class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full cursor-pointer"
                                :value="old('date_of_birth') ?? (str_starts_with($customer->date_of_birth, '$2y$') ? '' : $customer->date_of_birth)"
                                id="dob_input_edit" allow-input="false" placeholder="Выберите дату"
                                :label="trans('shop::app.customers.account.profile.edit.dob')" />
                        </x-shop::form.control-group>
                        <span class="nav-arrow !ml-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            @endif

            @if (empty($customer->birth_city) || str_starts_with($customer->birth_city, '$2y$'))
                <div class="nav-tile !p-0 overflow-hidden items-center">
                    <label class="nav-label pl-5 pr-2 py-4 flex-shrink-0 min-w-[140px]">@lang('shop::app.customers.account.profile.edit.birth-city') <span class="text-red-500">*</span></label>
                    <div class="flex-grow flex items-center justify-end pr-5">
                        <x-shop::form.control-group class="!mb-0 flex-1">
                            <x-shop::form.control-group.control type="text" name="birth_city" rules="required"
                                class="!border-0 !bg-transparent !p-0 !text-right !text-[15px] !text-zinc-500 !font-medium !shadow-none !ring-0 !h-auto w-full"
                                :value="old('birth_city') ?? (str_starts_with($customer->birth_city, '$2y$') ? '' : $customer->birth_city)"
                                placeholder="Например: Москва" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                data-lpignore="true" data-1p-ignore
                                :label="trans('shop::app.customers.account.profile.edit.birth-city')" />
                        </x-shop::form.control-group>
                        <span class="nav-arrow !ml-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Save Button --}}
    <div class="flex justify-center mt-10">
        <button type="submit"
            :disabled="!meta.valid || !!usernameError"
            class="w-full bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-5 rounded-3xl font-black uppercase tracking-widest text-[13px] transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
            @lang('shop::app.customers.account.profile.edit.save')
        </button>
    </div>
</div>

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}
