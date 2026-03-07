@push('styles')
    <style>
        .ios-settings-wrapper {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .ios-group {
            background-color: #fff;
            border: 1px solid #f3f4f6;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .ios-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            min-height: 52px;
            border-bottom: 1px solid #f3f4f6;
            position: relative;
            transition: background-color 0.2s ease;
        }

        .ios-row:first-child {
        }

        .ios-row:last-child {
            border-bottom: none;
        }

        .ios-row:active {
            background-color: #f9fafb;
        }

        .ios-label {
            font-size: 16px;
            font-weight: 500;
            color: #18181b;
            margin: 0;
            white-space: nowrap;
            flex-shrink: 0;
            background: transparent !important;
        }

        .ios-input-wrapper {
            flex-grow: 1;
            margin-left: 16px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: transparent !important;
        }

        .ios-input-wrapper>div {
            width: auto;
            margin-bottom: 0 !important;
            background: transparent !important;
        }

        .ios-input-wrapper input:not([name="phone"]),
        .ios-input-wrapper select {
            width: 100% !important;
            height: auto !important;
            line-height: normal !important;
            text-align: right !important;
            text-align-last: right !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 0 2px 0 !important;
            margin: 0 !important;
            color: #71717a !important;
            font-size: 15px !important;
            appearance: none;
            outline: none !important;
            border-radius: 0 !important;
        }
        
        .ios-input-wrapper input.pseudonym-input {
            width: auto !important;
            text-align: left !important;
            flex-grow: 0 !important;
        }

        .ios-input-wrapper input:focus::placeholder,
        .ios-input-wrapper input:focus::-webkit-input-placeholder { color: transparent !important; }

        ::selection {
            background-color: rgba(124, 69, 245, 0.2) !important;
            color: inherit !important;
        }

        ::-moz-selection {
            background-color: rgba(124, 69, 245, 0.2) !important;
            color: inherit !important;
        }


        input::-webkit-calendar-picker-indicator,
        input::-webkit-inner-spin-button,
        input::-webkit-clear-button {
            display: none !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }

        /* Hide browser contact/credential autofill icon */
        input::-webkit-contacts-auto-fill-button,
        input::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            display: none !important;
            pointer-events: none;
            height: 0;
            width: 0;
            margin: 0;
        }

        .ios-input-wrapper input[type="date"],
        .ios-input-wrapper input[type="date"]:focus,
        .ios-input-wrapper input[type="date"]:hover {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 24px !important;
            line-height: 24px !important;
            font-size: 15px !important;
            color: #71717a !important;
            text-align: right !important;
            cursor: pointer !important;
        }

        .ios-input-wrapper span.relative { background: transparent !important; }
        .ios-input-wrapper .flatpickr-input {
            -webkit-appearance: none !important;
            appearance: none !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 24px !important;
            line-height: 24px !important;
            font-size: 15px !important;
            color: #71717a !important;
            text-align: right !important;
            cursor: pointer !important;
        }

        .ios-arrow { margin-left: 8px; color: #d4d4d8; font-size: 18px; flex-shrink: 0; }
        .ios-input-wrapper select { padding-right: 0 !important; text-align: right; text-align-last: right; }

        .ios-switch-row {
            background: #fff;
            border: 1px solid #e4e4e7;
            padding: 14px 20px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .ios-toggle-container { display: flex; align-items: center; }
        .ios-switch { position: relative; display: inline-block; width: 51px; height: 31px; }
        .ios-switch input { opacity: 0; width: 0; height: 0; }
        .ios-slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #e9e9ea; transition: .4s;
        }
        .ios-slider:before {
            position: absolute; content: "";
            height: 27px; width: 27px; left: 2px; bottom: 2px;
            background-color: white; transition: .4s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }
        .ios-switch input:checked+.ios-slider { background-color: #34c759; }
        .ios-switch input:checked+.ios-slider:before { transform: translateX(20px); }

        @media (max-width: 768px) {
            .ios-settings-wrapper { padding: 0 12px; }
            .ios-group { margin-bottom: 12px; }
            .ios-row { padding: 8px 14px; min-height: 44px; }
            .ios-label { font-size: 14px; }
            .ios-input-wrapper { margin-left: 10px; }
            .ios-input-wrapper input, .ios-input-wrapper select { font-size: 14px !important; }
            .ios-switch-row { padding: 10px 14px; margin-bottom: 16px; }
        }
    </style>
@endpush

@if (isset($isCompleteRegistration) && $isCompleteRegistration)
    <input type="hidden" name="is_complete_registration" value="1">
@endif

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.before', ['customer' => $customer]) !!}

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.image.after') !!}

<div class="ios-settings-wrapper mx-auto w-full">

    @if (isset($isCompleteRegistration) && $isCompleteRegistration)
        {{-- Registration flow --}}
        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-zinc-900 mb-1">Расскажите о себе</h1>
            <p class="text-sm text-zinc-500 max-w-[400px] mx-auto leading-normal">
                Укажите настоящие имя и фамилию — они понадобятся для безопасного входа по Magic Link и восстановления доступа.
            </p>
        </div>

        <div class="w-full mx-auto relative">
            <div class="ios-group w-full !mb-2 !overflow-hidden">
                    {{-- Fields for registration mode --}}
                    <div class="ios-row !flex-col !items-start !h-auto !py-3">
                        <div class="flex items-center justify-between w-full">
                            <label class="ios-label">Псевдоним <span class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper !justify-end">
                                <span class="text-zinc-400 mr-0.5 text-[15px] select-none">@</span>
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="text" name="username" rules="required"
                                        class="pseudonym-input"
                                        :value="old('username') ?? (str_starts_with($customer->username, 'user_') ? '' : $customer->username)" placeholder="nickname"
                                        autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        data-lpignore="true" data-1p-ignore
                                        label="Псевдоним" 
                                        v-on:focus="clearDefaultUsername($event)"
                                        v-on:input="debounceCheckUsername($event.target.value)" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>
                    </div>
                    <div class="ios-row">
                        <label class="ios-label">@lang('shop::app.customers.account.profile.edit.first-name') <span class="text-red-500">*</span></label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                                    :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                                    placeholder="Имя" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    data-lpignore="true" data-1p-ignore
                                    :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                            </x-shop::form.control-group>
                            <span class="ios-arrow icon-arrow-right"></span>
                        </div>
                    </div>
                    <div class="ios-row">
                        <label class="ios-label">@lang('shop::app.customers.account.profile.edit.last-name') <span class="text-red-500">*</span></label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control type="text" name="last_name" rules="required"
                                    :value="old('last_name') ?? $customer->last_name" placeholder="Фамилия"
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    data-lpignore="true" data-1p-ignore
                                    :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                            </x-shop::form.control-group>
                            <span class="ios-arrow icon-arrow-right"></span>
                        </div>
                    </div>
                    <div class="ios-row">
                        <label class="ios-label">@lang('shop::app.customers.account.profile.edit.email') <span class="text-red-500">*</span></label>
                        <div class="ios-input-wrapper">
                            <input type="hidden" name="email" value="{{ $customer->email }}">
                            <span class="text-[15px] text-zinc-400 text-right">{{ $customer->email }}</span>
                            <span class="ios-arrow icon-arrow-right"></span>
                        </div>
                    </div>

                    @if (empty($customer->gender) || str_starts_with($customer->gender, '$2y$'))
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.gender') <span class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper relative">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="select" name="gender" rules="required"
                                        :value="old('gender') ?? (str_starts_with($customer->gender, '$2y$') ? '' : $customer->gender)"
                                        :label="trans('shop::app.customers.account.profile.edit.gender')">
                                        <option value="" disabled hidden>Выберите пол</option>
                                        <option value="Male" {{ (($customer->gender ?? old('gender')) == 'Male') ? 'selected' : '' }}>
                                            @lang('shop::app.customers.account.profile.edit.male')
                                        </option>
                                        <option value="Female" {{ (($customer->gender ?? old('gender')) == 'Female') ? 'selected' : '' }}>
                                            @lang('shop::app.customers.account.profile.edit.female')
                                        </option>
                                        <option value="Other" {{ (($customer->gender ?? old('gender')) == 'Other') ? 'selected' : '' }}>
                                            @lang('shop::app.customers.account.profile.edit.other')
                                        </option>
                                    </x-shop::form.control-group.control>
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>
                    @endif

                    @if (empty($customer->date_of_birth) || str_starts_with($customer->date_of_birth, '$2y$'))
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.dob') <span class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="date" name="date_of_birth"
                                        rules="required" :value="old('date_of_birth') ?? (str_starts_with($customer->date_of_birth, '$2y$') ? '' : $customer->date_of_birth)"
                                        id="dob_input_edit" allow-input="false" placeholder="Выберите дату"
                                        :label="trans('shop::app.customers.account.profile.edit.dob')" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right pointer-events-none"></span>
                            </div>
                        </div>
                    @endif

                    @if (empty($customer->birth_city) || str_starts_with($customer->birth_city, '$2y$'))
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.birth-city') <span class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="text" name="birth_city" rules="required"
                                        :value="old('birth_city') ?? (str_starts_with($customer->birth_city, '$2y$') ? '' : $customer->birth_city)"
                                        placeholder="Например: Москва" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        data-lpignore="true" data-1p-ignore
                                        :label="trans('shop::app.customers.account.profile.edit.birth-city')" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit"
                        :disabled="!meta.valid || !!usernameError"
                        class="flex w-full items-center justify-center gap-3  bg-[#7C45F5] px-8 py-3 text-center text-[15px] font-bold text-white shadow-lg shadow-[#7C45F5]/20 transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5] disabled:active:scale-100 rounded-none">
                        @lang('shop::app.customers.account.profile.edit.save')
                    </button>
                </div>
            </div>

    @else
        {{-- Normal profile edit mode --}}
        <!-- Group 1: Contact Info -->
        <div class="ios-group relative">
            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                class="absolute !top-5 !right-5 z-20 w-8 h-8 bg-white border border-gray-100 flex items-center justify-center text-zinc-400 active:scale-95 transition-all hover:text-[#7C45F5] hover:border-gray-200"
                style="right: 20px !important; left: auto !important;">
                <span class="icon-cancel text-xl"></span>
            </a>

            <div class="px-5 pt-6 pb-2">
                <h1 class="text-[20px] font-bold text-zinc-900 leading-tight">Профиль</h1>
            </div>

            <div class="ios-row !flex-col !items-start !h-auto !py-3">
                <div class="flex items-center justify-between w-full">
                    <label class="ios-label">Псевдоним <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper !justify-end">
                        <span class="text-zinc-400 mr-0.5 text-[15px] select-none">@</span>
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="text" name="username" rules="required"
                                class="pseudonym-input"
                                :value="old('username') ?? (str_starts_with($customer->username, 'user_') ? '' : $customer->username)" placeholder="nickname"
                                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                data-lpignore="true" data-1p-ignore
                                label="Псевдоним"
                                v-on:focus="clearDefaultUsername($event)"
                                v-on:input="debounceCheckUsername($event.target.value)" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>
            </div>
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.first-name') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                            :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                            placeholder="Имя" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                    </x-shop::form.control-group>
                    <span class="ios-arrow icon-arrow-right"></span>
                </div>
            </div>
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.last-name') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.control type="text" name="last_name" rules="required"
                            :value="old('last_name') ?? $customer->last_name" placeholder="Фамилия"
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore
                            :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                    </x-shop::form.control-group>
                    <span class="ios-arrow icon-arrow-right"></span>
                </div>
            </div>
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.email') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <input type="hidden" name="email" value="{{ $customer->email }}">
                    <span class="text-[15px] text-zinc-400 text-right">{{ $customer->email }}</span>
                    <span class="ios-arrow icon-arrow-right"></span>
                </div>
            </div>

            <div class="ios-row cursor-pointer" onclick="window.location.href='{{ route('shop.customers.account.addresses.index') }}'">
                <label class="ios-label cursor-pointer text-zinc-900">@lang('shop::app.layouts.address')</label>
                <div class="ios-input-wrapper justify-end">
                    <span class="text-[15px] text-zinc-400 text-right truncate max-w-[200px]">
                        {{ $customer->default_address ? ($customer->default_address->address . ', ' . $customer->default_address->city) : 'Настроить' }}
                    </span>
                    <span class="ios-arrow icon-arrow-right"></span>
                </div>
            </div>
        </div>

        <!-- Group 2: Personal Details -->
        <div class="ios-group">
            @if (empty($customer->gender) || str_starts_with($customer->gender, '$2y$'))
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.edit.gender') <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper relative">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="select" name="gender" rules="required"
                                :value="old('gender') ?? (str_starts_with($customer->gender, '$2y$') ? '' : $customer->gender)"
                                :label="trans('shop::app.customers.account.profile.edit.gender')">
                                <option value="" disabled hidden>Выберите пол</option>
                                <option value="Male" {{ (($customer->gender ?? old('gender')) == 'Male') ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.male')
                                </option>
                                <option value="Female" {{ (($customer->gender ?? old('gender')) == 'Female') ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.female')
                                </option>
                                <option value="Other" {{ (($customer->gender ?? old('gender')) == 'Other') ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.other')
                                </option>
                            </x-shop::form.control-group.control>
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>
            @endif

            @if (empty($customer->date_of_birth) || str_starts_with($customer->date_of_birth, '$2y$'))
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.edit.dob') <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="date" name="date_of_birth"
                                rules="required" :value="old('date_of_birth') ?? (str_starts_with($customer->date_of_birth, '$2y$') ? '' : $customer->date_of_birth)"
                                id="dob_input_edit" allow-input="false" placeholder="Выберите дату"
                                :label="trans('shop::app.customers.account.profile.edit.dob')" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right pointer-events-none"></span>
                    </div>
                </div>
            @endif

            @if (empty($customer->birth_city) || str_starts_with($customer->birth_city, '$2y$'))
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.edit.birth-city') <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="text" name="birth_city" rules="required"
                                :value="old('birth_city') ?? (str_starts_with($customer->birth_city, '$2y$') ? '' : $customer->birth_city)"
                                placeholder="Например: Москва" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                data-lpignore="true" data-1p-ignore
                                :label="trans('shop::app.customers.account.profile.edit.birth-city')" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>
            @endif

        </div>

        <!-- Newsletter Toggle -->
        <div class="ios-switch-row">
            <label class="text-[15px] font-medium text-zinc-900 cursor-pointer select-none m-0" for="is-subscribed">
                Подписаться на уведомления
            </label>
            <div class="ios-toggle-container">
                <label class="ios-switch">
                    <input type="checkbox" name="subscribed_to_news_letter" id="is-subscribed"
                        @checked($customer->subscribed_to_news_letter)>
                    <span class="ios-slider"></span>
                </label>
            </div>
        </div>

        <div class="flex justify-center mt-6">
            <button type="submit"
                :disabled="!meta.valid || !!usernameError"
                class="flex w-full items-center justify-center gap-3 bg-[#7C45F5] px-8 py-3 text-center text-[15px] font-bold text-white shadow-lg shadow-[#7C45F5]/20 transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5] disabled:active:scale-100 rounded-none">
                @lang('shop::app.customers.account.profile.edit.save')
            </button>
        </div>


    @endif

</div>

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}
