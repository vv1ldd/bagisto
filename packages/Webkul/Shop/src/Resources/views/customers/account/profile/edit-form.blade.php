@push('styles')
    <style>
        .ios-settings-wrapper {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .ios-group {
            background-color: #fff;
            border: 1px solid #e4e4e7;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .ios-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            min-height: 48px;
            border-bottom: 1px solid #f4f4f5;
            position: relative;
            transition: padding 0.2s ease;
        }

        .ios-row:first-child {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .ios-row:last-child {
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            border-bottom: none;
        }

        .ios-label {
            font-size: 15px;
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
            height: 24px !important;
            line-height: 24px !important;
            text-align: right !important;
            text-align-last: right !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #71717a !important;
            font-size: 15px !important;
            appearance: none;
            outline: none !important;
        }

        .ios-input-wrapper input[name="phone"] {
            width: 100%;
            text-align: right !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            color: #71717a !important;
            font-size: 15px !important;
            appearance: none;
        }

        .ios-input-wrapper p.text-red-500 {
            background: transparent !important;
            display: block;
            text-align: right;
            font-size: 11px;
            margin-top: 4px;
            line-height: 1.2;
            font-weight: 500;
            color: #ef4444 !important;
        }

        .iti { width: 100%; justify-content: flex-end; }
        .iti__flag-container { z-index: 10 !important; cursor: pointer !important; }
        .iti--container { z-index: 99999 !important; }
        .iti__country-list {
            z-index: 99999 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            border: 1px solid #e4e4e7 !important;
            background-color: #fff !important;
        }
        .iti__search-input {
            margin-top: 5px !important;
            margin-bottom: 5px !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            width: calc(100% - 20px) !important;
            margin-left: 10px !important;
            margin-right: 10px !important;
            box-sizing: border-box !important;
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
            border-radius: 16px;
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
            background-color: #e9e9ea; transition: .4s; border-radius: 34px;
        }
        .ios-slider:before {
            position: absolute; content: "";
            height: 27px; width: 27px; left: 2px; bottom: 2px;
            background-color: white; transition: .4s; border-radius: 50%;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }
        .ios-switch input:checked+.ios-slider { background-color: #34c759; }
        .ios-switch input:checked+.ios-slider:before { transform: translateX(20px); }

        @media (max-width: 768px) {
            .ios-settings-wrapper { padding: 0 16px; }
            .ios-row { padding: 14px 16px; }
            .ios-label { font-size: 14px; }
            .ios-input-wrapper { margin-left: 12px; }
            .ios-input-wrapper input, .ios-input-wrapper select { font-size: 14px !important; }
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
        {{-- Registration flow: premium card wrapper --}}
        <div class="rounded-[2.5rem] bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-5 md:p-7 flex flex-col items-center relative overflow-hidden w-full shadow-[0_8px_32px_rgba(124,69,245,0.05)] border border-white">
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#3B82F6]/10 rounded-full blur-3xl"></div>

            <div class="w-full mx-auto z-10 relative">
                <h2 class="text-[22px] md:text-2xl font-bold text-zinc-900 mb-1 mt-0 text-center">Расскажите о себе</h2>
                <p class="text-[14px] text-zinc-600 mb-4 text-center mx-auto max-w-[320px]">
                    Заполните базовую информацию, чтобы мы могли персонализировать ваш профиль.
                </p>

                <div class="ios-group w-full !mb-4 !border-white/60 !bg-white/80 !backdrop-blur-xl !shadow-sm !rounded-[24px] overflow-hidden">
                    {{-- Fields for registration mode --}}
                    <div class="ios-row">
                        <label class="ios-label">@lang('shop::app.customers.account.profile.edit.username') <span class="text-red-500">*</span></label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control type="text" name="username" rules="required"
                                    :value="old('username') ?? $customer->username" placeholder="Имя пользователя"
                                    autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    :label="trans('shop::app.customers.account.profile.edit.username')" />
                                <x-shop::form.control-group.error control-name="username" />
                            </x-shop::form.control-group>
                            <span class="ios-arrow icon-arrow-right"></span>
                        </div>
                    </div>
                    <div class="ios-row">
                        <label class="ios-label">@lang('shop::app.customers.account.profile.edit.first-name') <span class="text-red-500">*</span></label>
                        <div class="ios-input-wrapper">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                                    :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                                    placeholder="Имя" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                                <x-shop::form.control-group.error control-name="first_name" />
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
                                    autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                                <x-shop::form.control-group.error control-name="last_name" />
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
                                    <x-shop::form.control-group.error control-name="gender" />
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
                                    <x-shop::form.control-group.error control-name="date_of_birth" />
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
                                        placeholder="Например: Москва" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        :label="trans('shop::app.customers.account.profile.edit.birth-city')" />
                                    <x-shop::form.control-group.error control-name="birth_city" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-center mt-4">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-[20px] bg-[#7C45F5] px-8 py-3 text-center text-[15px] font-bold text-white shadow-xl shadow-purple-500/25 transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 active:scale-[0.98]">
                        @lang('shop::app.customers.account.profile.edit.save')
                    </button>
                </div>
            </div>
        </div>

    @else
        {{-- Normal profile edit mode --}}
        <!-- Group 1: Contact Info -->
        <div class="ios-group">
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.username') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.control type="text" name="username" rules="required"
                            :value="old('username') ?? $customer->username" placeholder="Имя пользователя"
                            autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                            :label="trans('shop::app.customers.account.profile.edit.username')" />
                        <x-shop::form.control-group.error control-name="username" />
                    </x-shop::form.control-group>
                    <span class="ios-arrow icon-arrow-right"></span>
                </div>
            </div>
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.first-name') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                            :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                            placeholder="Имя" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                            :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                        <x-shop::form.control-group.error control-name="first_name" />
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
                            autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                            :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                        <x-shop::form.control-group.error control-name="last_name" />
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
            <div class="ios-row">
                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.phone') <span class="text-red-500">*</span></label>
                <div class="ios-input-wrapper">
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.control type="text" name="phone" rules="required|phone" v-phone
                            :value="old('phone') ?? $customer->phone" placeholder="Телефон"
                            :label="trans('shop::app.customers.account.profile.edit.phone')" />
                        <x-shop::form.control-group.error control-name="phone" />
                    </x-shop::form.control-group>
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
                            <x-shop::form.control-group.error control-name="gender" />
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
                            <x-shop::form.control-group.error control-name="date_of_birth" />
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
                                placeholder="Например: Москва" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                                :label="trans('shop::app.customers.account.profile.edit.birth-city')" />
                            <x-shop::form.control-group.error control-name="birth_city" />
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

        <div class="flex justify-center mt-10">
            <button type="submit"
                class="primary-button inline-flex justify-center rounded-full px-12 py-3.5 text-center text-[15px] font-medium max-md:w-full">
                @lang('shop::app.customers.account.profile.edit.save')
            </button>
        </div>
    @endif

</div>

{!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}
