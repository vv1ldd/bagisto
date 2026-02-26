<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.profile.edit.edit-profile')
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="profile.edit" />
        @endSection
        @endif



        <div class="flex-auto p-8 max-md:p-5 pt-4">


            {!! view_render_event('bagisto.shop.customers.account.profile.edit.before', ['customer' => $customer]) !!}

            <!-- Profile Edit Form -->
            <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data">
                {!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.before', ['customer' => $customer]) !!}


                {!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.image.after') !!}


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
                            overflow: hidden;
                            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                        }

                        .ios-row {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            padding: 16px 20px;
                            border-bottom: 1px solid #f4f4f5;
                            position: relative;
                        }

                        .ios-row:last-child {
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
                            /* override control-group space */
                            background: transparent !important;
                        }

                        .ios-input-wrapper input:not([name="phone"]),
                        .ios-input-wrapper select {
                            width: 100%;
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
                        }

                        /* Phone input needs padding for the flag */
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
                            font-size: 12px;
                            margin-top: 2px;
                        }

                        /* intl-tel-input overrides for iOS style */
                        .iti {
                            width: 100%;
                            justify-content: flex-end;
                        }

                        .iti__flag-container {
                            z-index: 5 !important;
                            cursor: pointer !important;
                        }

                        .iti--container {
                            z-index: 9999 !important;
                        }

                        .iti__country-list {
                            z-index: 9999 !important;
                        }

                        /* Hide browser date/time icons */
                        input::-webkit-calendar-picker-indicator,
                        input::-webkit-inner-spin-button,
                        input::-webkit-clear-button {
                            display: none !important;
                            appearance: none !important;
                            -webkit-appearance: none !important;
                        }

                        /* Special styling for Date of Birth pill */
                        v-date-picker,
                        v-datetime-picker,
                        #dob_input_edit,
                        #dob_input_edit input {
                            background: transparent !important;
                            border: none !important;
                            box-shadow: none !important;
                            padding: 0 !important;
                            font-size: 15px !important;
                            color: #18181b !important;
                            width: auto !important;
                            text-align: right !important;
                        }

                        .ios-arrow {
                            margin-left: 8px;
                            color: #d4d4d8;
                            font-size: 18px;
                            flex-shrink: 0;
                        }

                        .ios-input-wrapper select {
                            padding-right: 0 !important;
                            text-align: right;
                            text-align-last: right;
                        }

                        /* Newsletter switch */
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

                        /* iOS Toggle Switch Custom Styling */
                        .ios-toggle-container {
                            display: flex;
                            align-items: center;
                        }

                        .ios-switch {
                            position: relative;
                            display: inline-block;
                            width: 51px;
                            height: 31px;
                        }

                        .ios-switch input {
                            opacity: 0;
                            width: 0;
                            height: 0;
                        }

                        .ios-slider {
                            position: absolute;
                            cursor: pointer;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            background-color: #e9e9ea;
                            transition: .4s;
                            border-radius: 34px;
                        }

                        .ios-slider:before {
                            position: absolute;
                            content: "";
                            height: 27px;
                            width: 27px;
                            left: 2px;
                            bottom: 2px;
                            background-color: white;
                            transition: .4s;
                            border-radius: 50%;
                            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
                        }

                        .ios-switch input:checked+.ios-slider {
                            background-color: #34c759;
                        }

                        .ios-switch input:checked+.ios-slider:before {
                            transform: translateX(20px);
                        }

                        @media (max-width: 768px) {
                            .ios-settings-wrapper {
                                padding: 0 16px;
                            }

                            .ios-row {
                                padding: 14px 16px;
                            }

                            .ios-label {
                                font-size: 14px;
                            }

                            .ios-input-wrapper {
                                margin-left: 12px;
                            }

                            .ios-input-wrapper input,
                            .ios-input-wrapper select {
                                font-size: 14px !important;
                            }
                        }
                    </style>
                @endpush

                <div class="ios-settings-wrapper">
                    @if (session()->has('recovery_key'))
                        <div
                            class="mb-6 rounded-2xl bg-[#FFFBF0] border border-[#FBE3A1] p-6 flex flex-col md:flex-row items-start gap-4 shadow-sm">
                            <span class="icon-information text-3xl text-[#F5A623] md:mt-1"></span>
                            <div>
                                <h3 class="text-[#8C6D1F] text-lg font-bold mb-2">Сохраните резервный ключ!</h3>
                                <p class="text-[#8C6D1F]/80 text-[15px] mb-4 leading-relaxed">
                                    Это ваш супер-секретный ключ доступа. Если вы потеряете доступ к почте и устройствам,
                                    вы сможете использовать его вместе с вашими данными для восстановления аккаунта.
                                    <strong>Этот ключ показывается только один раз!</strong>
                                </p>
                                <div
                                    class="bg-[#FBE3A1]/30 border border-[#F5A623]/20 rounded-xl p-4 text-center text-[#8C6D1F] font-mono font-bold tracking-widest text-xl md:text-2xl select-all">
                                    {{ session('recovery_key') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Group 1: Contact Info -->
                    <div class="ios-group">
                        <!-- Username -->
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.username') <span
                                    class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="text" name="username" rules="required"
                                        :value="old('username') ?? $customer->username"
                                        placeholder="Имя пользователя"
                                        :label="trans('shop::app.customers.account.profile.edit.username')" />
                                    <x-shop::form.control-group.error control-name="username" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>

                        <!-- First Name -->
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.first-name') <span
                                    class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                                        :value="old('first_name') ?? (($customer->first_name === 'Пользователь' || $customer->first_name === '') ? null : $customer->first_name)"
                                        placeholder="Имя"
                                        :label="trans('shop::app.customers.account.profile.edit.first-name')" />
                                    <x-shop::form.control-group.error control-name="first_name" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.last-name') <span
                                    class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.control type="text" name="last_name" rules="required"
                                        :value="old('last_name') ?? $customer->last_name" placeholder="Фамилия"
                                        :label="trans('shop::app.customers.account.profile.edit.last-name')" />
                                    <x-shop::form.control-group.error control-name="last_name" />
                                </x-shop::form.control-group>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>

                        <!-- Email (read-only) -->
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.email') <span
                                    class="text-red-500">*</span></label>
                            <div class="ios-input-wrapper">
                                {{-- Hidden input so the value is still submitted --}}
                                <input type="hidden" name="email" value="{{ $customer->email }}">
                                <span class="text-[15px] text-zinc-400 text-right">{{ $customer->email }}</span>
                                <span class="ios-arrow icon-arrow-right"></span>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="ios-row">
                            <label class="ios-label">@lang('shop::app.customers.account.profile.edit.phone') <span
                                    class="text-red-500">*</span></label>
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
                    </div>

                    <!-- Group 2: Personal Details -->
                    <div class="ios-group">
                        <!-- Gender -->
                        @if (empty($customer->gender) || str_starts_with($customer->gender, '$2y$'))
                            <div class="ios-row">
                                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.gender') <span
                                        class="text-red-500">*</span></label>
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
                            <!-- DOB -->
                            <div class="ios-row">
                                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.dob') <span
                                        class="text-red-500">*</span></label>
                                <div class="ios-input-wrapper">
                                    <x-shop::form.control-group class="!mb-0">
                                        <x-shop::form.control-group.control type="date" name="date_of_birth"
                                            rules="required" :value="old('date_of_birth') ?? (str_starts_with($customer->date_of_birth, '$2y$') ? '' : $customer->date_of_birth)"
                                            id="dob_input_edit" allow-input="false" placeholder="Выберите дату"
                                            :label="trans('shop::app.customers.account.profile.edit.dob')" />
                                        <x-shop::form.control-group.error control-name="date_of_birth" />
                                    </x-shop::form.control-group>
                                    <span class="ios-arrow icon-arrow-right"></span>
                                </div>
                            </div>
                        @endif

                        @if (empty($customer->birth_city) || str_starts_with($customer->birth_city, '$2y$'))
                            <!-- Birth City -->
                            <div class="ios-row">
                                <label class="ios-label">@lang('shop::app.customers.account.profile.edit.birth-city') <span
                                        class="text-red-500">*</span></label>
                                <div class="ios-input-wrapper">
                                    <x-shop::form.control-group class="!mb-0">
                                        <x-shop::form.control-group.control type="text" name="birth_city" rules="required"
                                            :value="old('birth_city') ?? (str_starts_with($customer->birth_city, '$2y$') ? '' : $customer->birth_city)"
                                            placeholder="Например: Москва"
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
                        <label class="text-[15px] font-medium text-zinc-900 cursor-pointer select-none m-0"
                            for="is-subscribed">
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

                    <div class="flex justify-center">
                        <button type="submit"
                            class="primary-button inline-flex justify-center rounded-full px-12 py-3.5 text-center text-[15px] font-medium max-md:w-full">
                            @lang('shop::app.customers.account.profile.edit.save')
                        </button>
                    </div>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}

            </x-shop::form>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.after', ['customer' => $customer]) !!}

        </div>
</x-shop::layouts.account>