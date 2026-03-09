<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('admin::app.settings.billing-entities.create.title')
        </x-slot>

        <!-- Create form -->
        <x-admin::form :action="route('admin.settings.billing_entities.store')">
            <div class="flex justify-between items-center">
                <p class="text-[20px] text-gray-800 dark:text-white font-bold">
                    @lang('admin::app.settings.billing-entities.create.title')
                </p>

                <div class="flex gap-x-[10px] items-center">
                    <!-- Cancel Button -->
                    <a href="{{ route('admin.settings.billing_entities.index') }}"
                        class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white">
                        @lang('admin::app.settings.billing-entities.create.back-btn')
                    </a>

                    <!-- Save Button -->
                    <button type="submit" class="primary-button">
                        @lang('admin::app.settings.billing-entities.create.save-btn')
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
                            @lang('admin::app.settings.billing-entities.create.general')
                        </p>

                        <!-- Name -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.billing-entities.create.name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="name" rules="required"
                                :label="trans('admin::app.settings.billing-entities.create.name')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.name')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="name"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- INN -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.inn')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="inn"
                                :label="trans('admin::app.settings.billing-entities.create.inn')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.inn')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="inn"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- KPP -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.kpp')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="kpp"
                                :label="trans('admin::app.settings.billing-entities.create.kpp')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.kpp')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="kpp"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- Address -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.address')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="textarea" name="address"
                                :label="trans('admin::app.settings.billing-entities.create.address')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.address')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="address"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>
                    </div>

                    {{-- People Details --}}
                    <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                        <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                            @lang('admin::app.settings.billing-entities.create.people')
                        </p>

                        <!-- Director Name -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.director-name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="director_name"
                                :label="trans('admin::app.settings.billing-entities.create.director-name')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.director-name')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="director_name"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- Accountant Name -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.accountant-name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="accountant_name"
                                :label="trans('admin::app.settings.billing-entities.create.accountant-name')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.accountant-name')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="accountant_name"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Right sub-component -->
                <div class="flex flex-col gap-[8px] w-[360px] max-w-full">
                    {{-- Banking Details --}}
                    <div class="p-[16px] bg-white dark:bg-gray-900 rounded-[4px] box-shadow">
                        <p class="text-[16px] text-gray-800 dark:text-white font-semibold mb-[16px]">
                            @lang('admin::app.settings.billing-entities.create.banking')
                        </p>

                        <!-- Bank Name -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.bank-name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="bank_name"
                                :label="trans('admin::app.settings.billing-entities.create.bank-name')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.bank-name')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="bank_name"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- BIC -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.bic')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="bic"
                                :label="trans('admin::app.settings.billing-entities.create.bic')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.bic')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="bic"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- Settlement Account -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.settlement-account')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="settlement_account"
                                :label="trans('admin::app.settings.billing-entities.create.settlement-account')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.settlement-account')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="settlement_account"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <!-- Correspondent Account -->
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.billing-entities.create.correspondent-account')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="correspondent_account"
                                :label="trans('admin::app.settings.billing-entities.create.correspondent-account')"
                                :placeholder="trans('admin::app.settings.billing-entities.create.correspondent-account')">
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="correspondent_account"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>
        </x-admin::form>
</x-admin::layouts>