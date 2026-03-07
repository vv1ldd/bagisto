<x-shop::layouts.account>
    <div class="flex-auto ios-tile-relative ios-group max-w-[600px] mx-auto p-8 max-md:p-6">
        <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.organizations.index') }}'"
            class="ios-close-button">
            <span class="icon-cancel text-xl"></span>
        </a>

        <div class="mb-8">
            <h1 class="text-[22px] font-bold text-zinc-900 leading-tight">
                @lang('shop::app.customers.account.organizations.edit.title')
            </h1>
            <p class="text-[14px] text-zinc-500 mt-1">
                Обновите информацию об организации.
            </p>
        </div>

        <x-shop::form method="PUT" :action="route('shop.customers.account.organizations.update', $organization->id)">
            <div class="space-y-5">
                <!-- Organization Name -->
                <x-shop::form.control-group class="!mb-0">
                    <x-shop::form.control-group.label
                        class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                        @lang('shop::app.customers.account.organizations.edit.name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="text" name="name" rules="required" :value="old('name') ?? $organization->name"
                        class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all font-bold"
                        :label="trans('shop::app.customers.account.organizations.edit.name')"
                        :placeholder="trans('shop::app.customers.account.organizations.edit.name')" />

                    <x-shop::form.control-group.error control-name="name" />
                </x-shop::form.control-group>

                <div class="grid grid-cols-2 gap-4">
                    <!-- INN -->
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.label
                            class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.edit.inn')
                        </x-shop::form.control-group.label>

                        <div class="flex gap-2">
                            <x-shop::form.control-group.control type="text" name="inn" rules="required" :value="old('inn') ?? $organization->inn"
                                id="inn-input"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all flex-1"
                                :label="trans('shop::app.customers.account.organizations.edit.inn')"
                                :placeholder="trans('shop::app.customers.account.organizations.edit.inn')" />
                            
                            <button type="button" id="lookup-inn-btn"
                                class="px-6 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold transition-all disabled:opacity-50">
                                Найти
                            </button>
                        </div>

                        <x-shop::form.control-group.error control-name="inn" />
                    </x-shop::form.control-group>

                    <!-- KPP -->
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.label
                            class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.edit.kpp')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" name="kpp" :value="old('kpp') ?? $organization->kpp"
                            id="kpp-input"
                            class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                            :label="trans('shop::app.customers.account.organizations.edit.kpp')"
                            :placeholder="trans('shop::app.customers.account.organizations.edit.kpp')" />

                        <x-shop::form.control-group.error control-name="kpp" />
                    </x-shop::form.control-group>
                </div>

                <!-- Address -->
                <x-shop::form.control-group class="!mb-0">
                    <x-shop::form.control-group.label
                        class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                        @lang('shop::app.customers.account.organizations.edit.address')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="textarea" name="address" :value="old('address') ?? $organization->address"
                        id="address-input"
                        class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all min-h-[80px]"
                        :label="trans('shop::app.customers.account.organizations.edit.address')"
                        :placeholder="trans('shop::app.customers.account.organizations.edit.address')" />

                    <x-shop::form.control-group.error control-name="address" />
                </x-shop::form.control-group>

                <div class="pt-4 border-t border-zinc-100 mt-6 pt-6">
                    <h2 class="text-[16px] font-bold text-zinc-900 mb-4">Банковские реквизиты</h2>

                    <div class="space-y-4">
                        <!-- Bank Name -->
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.label
                                class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                @lang('shop::app.customers.account.organizations.edit.bank_name')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" name="bank_name" :value="old('bank_name') ?? $organization->bank_name"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all font-medium"
                                :label="trans('shop::app.customers.account.organizations.edit.bank_name')"
                                :placeholder="trans('shop::app.customers.account.organizations.edit.bank_name')" />

                            <x-shop::form.control-group.error control-name="bank_name" />
                        </x-shop::form.control-group>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- BIC -->
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                    @lang('shop::app.customers.account.organizations.edit.bic')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="bic" :value="old('bic') ?? $organization->bic"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                    :label="trans('shop::app.customers.account.organizations.edit.bic')"
                                    :placeholder="trans('shop::app.customers.account.organizations.edit.bic')" />

                                <x-shop::form.control-group.error control-name="bic" />
                            </x-shop::form.control-group>

                            <!-- Settlement Account -->
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                    @lang('shop::app.customers.account.organizations.edit.settlement_account')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="settlement_account"
                                    :value="old('settlement_account') ?? $organization->settlement_account"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all font-medium"
                                    :label="trans('shop::app.customers.account.organizations.edit.settlement_account')"
                                    :placeholder="trans('shop::app.customers.account.organizations.edit.settlement_account')" />

                                <x-shop::form.control-group.error control-name="settlement_account" />
                            </x-shop::form.control-group>
                        </div>

                        <!-- Correspondent Account -->
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.label
                                class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                @lang('shop::app.customers.account.organizations.edit.correspondent_account')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" name="correspondent_account"
                                :value="old('correspondent_account') ?? $organization->correspondent_account"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                :label="trans('shop::app.customers.account.organizations.edit.correspondent_account')"
                                :placeholder="trans('shop::app.customers.account.organizations.edit.correspondent_account')" />

                            <x-shop::form.control-group.error control-name="correspondent_account" />
                        </x-shop::form.control-group>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit"
                        class="w-full bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold py-4 px-8 rounded-none shadow-lg shadow-[#7C45F5]/20 transition-all active:scale-[0.98]">
                        @lang('shop::app.customers.account.organizations.edit.update-btn')
                    </button>
                </div>
            </div>
        </x-shop::form>
    </div>
</x-shop::layouts.account>