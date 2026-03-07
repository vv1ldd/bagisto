<x-shop::layouts.split-screen>
    <x-slot:title>
        Подтверждение личности
        </x-slot>

        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-zinc-900 mb-1">Подтвердите вашу личность</h1>
            <p class="text-sm text-zinc-500">Для завершения входа введите ваши данные</p>
        </div>

        @error('verification')
            <div class="mb-4 bg-red-50 p-3 border border-red-100 text-sm text-red-600 flex items-center gap-3 rounded-xl">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                {{ $message }}
            </div>
        @enderror

        <x-shop::form :action="route('shop.customer.login.verify_identity.post')">
            @push('styles')
                <style>
                    .ios-group {
                        background-color: #fff;
                        border: 1px solid #e4e4e7;
                        border-radius: 16px;
                        margin-bottom: 12px;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                    }

                    .ios-row {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 12px 20px;
                        border-bottom: 1px solid #f4f4f5;
                        position: relative;
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
                        position: relative;
                        padding-right: 20px;
                    }

                    .ios-input-wrapper>div {
                        width: auto;
                        margin-bottom: 0 !important;
                        background: transparent !important;
                        display: flex;
                        justify-content: flex-end;
                    }

                    .ios-input-wrapper input,
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

                    .ios-input-wrapper input:focus::placeholder {
                        color: transparent;
                    }

                    /* Hide browser date/time icons */
                    input::-webkit-calendar-picker-indicator,
                    input::-webkit-inner-spin-button,
                    input::-webkit-clear-button {
                        display: none !important;
                        appearance: none !important;
                        -webkit-appearance: none !important;
                    }

                    /* Flatpickr overrides */
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

                    .ios-arrow {
                        position: absolute;
                        right: 0;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #d4d4d8;
                        font-size: 16px;
                        margin-left: 0 !important;
                    }

                    @media (max-width: 768px) {
                        .ios-row {
                            padding: 10px 16px;
                        }

                        .ios-label {
                            font-size: 14px;
                        }

                        .ios-input-wrapper {
                            margin-left: 12px;
                            padding-right: 20px;
                        }

                        .ios-input-wrapper input,
                        .ios-input-wrapper select {
                            font-size: 14px !important;
                        }
                    }
                </style>
            @endpush

            <div class="ios-group">
                <!-- First Name -->
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.index.first-name')
                        <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="text" name="first_name" rules="required"
                                :value="old('first_name')" placeholder="Имя"
                                :label="trans('shop::app.customers.account.profile.index.first-name')" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>

                <!-- Last Name -->
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.index.last-name')
                        <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="text" name="last_name" rules="required"
                                :value="old('last_name')" placeholder="Фамилия"
                                :label="trans('shop::app.customers.account.profile.index.last-name')" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>

                <!-- Gender -->
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.index.gender')
                        <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="select" name="gender" rules="required"
                                :label="trans('shop::app.customers.account.profile.index.gender')">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>
                                    Выберите...</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.male')
                                </option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.female')
                                </option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>
                                    @lang('shop::app.customers.account.profile.edit.other')
                                </option>
                            </x-shop::form.control-group.control>
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>

                <!-- DOB -->
                <div class="ios-row">
                    <label class="ios-label">@lang('shop::app.customers.account.profile.index.dob')
                        <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="date" name="date_of_birth" rules="required"
                                :value="old('date_of_birth')" id="dob_input" allow-input="false"
                                placeholder="Выберите дату"
                                :label="trans('shop::app.customers.account.profile.index.dob')" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right pointer-events-none"></span>
                    </div>
                </div>

                <!-- Birth City -->
                <div class="ios-row">
                    <label class="ios-label">Город рождения <span class="text-red-500">*</span></label>
                    <div class="ios-input-wrapper">
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control type="text" name="birth_city" rules="required"
                                :value="old('birth_city')" placeholder="Например: Москва" label="Город рождения" />
                        </x-shop::form.control-group>
                        <span class="ios-arrow icon-arrow-right"></span>
                    </div>
                </div>
            </div>

            <!-- Centralized Validation Errors -->
            <div v-if="Object.keys(errors).length" class="mb-4">
                <div class="text-[12px] text-red-500 font-medium bg-red-50/50 p-2 rounded-lg border border-red-100/50">
                    <p v-for="error in errors" class="mb-0.5 last:mb-0">
                        • @{{ error }}
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <button
                    class="w-full !rounded-[20px] bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20"
                    type="submit">
                    Подтвердить и войти
                </button>
            </div>
        </x-shop::form>


        <div class="mt-8 text-center">
            <a href="{{ route('shop.customer.session.index') }}"
                class="text-sm font-medium text-zinc-400 hover:text-[#7C45F5]">
                Отмена и возврат к способам входа
            </a>
        </div>
</x-shop::layouts.split-screen>