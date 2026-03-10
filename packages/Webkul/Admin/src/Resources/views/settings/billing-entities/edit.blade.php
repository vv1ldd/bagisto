<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('admin::app.settings.billing-entities.edit.title')
    </x-slot>

    <!-- Edit form -->
    <x-admin::form
        :action="route('admin.settings.billing_entities.update', $billingEntity->id)"
        enctype="multipart/form-data"
        method="PUT"
    >
        <div class="flex justify-between items-center">
            <p class="text-[20px] text-gray-800 dark:text-white font-bold">
                @lang('admin::app.settings.billing-entities.edit.title')
            </p>

            <div class="flex gap-x-[10px] items-center">
                <!-- Cancel Button -->
                <a
                    href="{{ route('admin.settings.billing_entities.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white"
                >
                    @lang('admin::app.settings.billing-entities.edit.back-btn')
                </a>

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.settings.billing-entities.edit.save-btn')
                </button>
            </div>
        </div>

        <!-- body content -->
        <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                {{-- General Details --}}
                <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                        @lang('admin::app.settings.billing-entities.edit.general')
                    </p>

                    <!-- Name -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.settings.billing-entities.edit.name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="name"
                            rules="required"
                            :value="old('name') ?: $billingEntity->name"
                            :label="trans('admin::app.settings.billing-entities.edit.name')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.name')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="name"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <!-- INN -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.inn')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="inn"
                            :value="old('inn') ?: $billingEntity->inn"
                            :label="trans('admin::app.settings.billing-entities.edit.inn')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.inn')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="inn"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <!-- KPP -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.kpp')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="kpp"
                            :value="old('kpp') ?: $billingEntity->kpp"
                            :label="trans('admin::app.settings.billing-entities.edit.kpp')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.kpp')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="kpp"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <!-- Address -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.address')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            name="address"
                            :value="old('address') ?: $billingEntity->address"
                            :label="trans('admin::app.settings.billing-entities.edit.address')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.address')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="address"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <!-- Tax Regime -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.tax-regime')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control type="select" name="tax_regime"
                            :value="old('tax_regime') ?: $billingEntity->tax_regime"
                            :label="trans('admin::app.settings.billing-entities.edit.tax-regime')">
                            <option value="osno" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'osno' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.osno')</option>
                            <option value="usn-6" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'usn-6' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.usn-6')</option>
                            <option value="usn-15" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'usn-15' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.usn-15')</option>
                            <option value="usn-vat-5" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'usn-vat-5' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.usn-vat-5')</option>
                            <option value="usn-vat-7" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'usn-vat-7' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.usn-vat-7')</option>
                            <option value="aupsn" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'aupsn' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.aupsn')</option>
                            <option value="psn" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'psn' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.psn')</option>
                            <option value="npd" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'npd' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.npd')</option>
                            <option value="eshn" {{ (old('tax_regime') ?: $billingEntity->tax_regime) == 'eshn' ? 'selected' : '' }}>@lang('admin::app.settings.billing-entities.edit.tax-regime-options.eshn')</option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error
                            control-name="tax_regime"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>
                </div>

                {{-- People Details --}}
                <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                        @lang('admin::app.settings.billing-entities.edit.people')
                    </p>

                    <!-- Director Name -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.director-name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="director_name"
                            :value="old('director_name') ?: $billingEntity->director_name"
                            :label="trans('admin::app.settings.billing-entities.edit.director-name')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.director-name')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="director_name"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                     <!-- Accountant Name -->
                     <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.accountant-name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="accountant_name"
                            :value="old('accountant_name') ?: $billingEntity->accountant_name"
                            :label="trans('admin::app.settings.billing-entities.edit.accountant-name')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.accountant-name')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="accountant_name"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>
                </div>
            </div>

            <!-- Right sub-component -->
            <div class="flex flex-col gap-[8px] w-[360px] max-w-full">
                {{-- Banking Details --}}
                <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                        @lang('admin::app.settings.billing-entities.edit.banking')
                    </p>

                     <!-- Bank Name -->
                     <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.bank-name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="bank_name"
                            :value="old('bank_name') ?: $billingEntity->bank_name"
                            :label="trans('admin::app.settings.billing-entities.edit.bank-name')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.bank-name')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="bank_name"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <!-- BIC -->
                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.bic')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="bic"
                            :value="old('bic') ?: $billingEntity->bic"
                            :label="trans('admin::app.settings.billing-entities.edit.bic')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.bic')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="bic"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                     <!-- Settlement Account -->
                     <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.settlement-account')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="settlement_account"
                            :value="old('settlement_account') ?: $billingEntity->settlement_account"
                            :label="trans('admin::app.settings.billing-entities.edit.settlement-account')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.settlement-account')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="settlement_account"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                     <!-- Correspondent Account -->
                     <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.settings.billing-entities.edit.correspondent-account')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="correspondent_account"
                            :value="old('correspondent_account') ?: $billingEntity->correspondent_account"
                            :label="trans('admin::app.settings.billing-entities.edit.correspondent-account')"
                            :placeholder="trans('admin::app.settings.billing-entities.edit.correspondent-account')"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="correspondent_account"></x-admin::form.control-group.error>
                    </x-admin::form.control-group>
                </div>

                {{-- Seal --}}
                <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                    <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                        @lang('admin::app.settings.billing-entities.edit.seal')
                    </p>

                    <x-admin::media.images
                        name="seal"
                        :uploaded-images="$billingEntity->seal ? [['id' => 'seal', 'url' => \Illuminate\Support\Facades\Storage::url($billingEntity->seal)]] : []"
                        width="220px"
                    />

                    <p class="text-[12px] text-gray-600 dark:text-gray-300 mt-[10px]">
                        @lang('admin::app.settings.billing-entities.edit.seal-info')
                    </p>
                </div>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
