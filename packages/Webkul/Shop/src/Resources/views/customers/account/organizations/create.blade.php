<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.organizations.create.title')
        </x-slot>

        <div class="flex-auto p-8 max-md:p-5 pt-4">
            <x-shop::form :action="route('shop.customers.account.organizations.store')">
                <!-- Organization Name -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.organizations.create.name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="text" name="name" rules="required" :value="old('name')"
                        :label="trans('shop::app.customers.account.organizations.create.name')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.name')" />

                    <x-shop::form.control-group.error control-name="name" />
                </x-shop::form.control-group>

                <!-- INN -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.account.organizations.create.inn')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="text" name="inn" rules="required" :value="old('inn')"
                        :label="trans('shop::app.customers.account.organizations.create.inn')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.inn')" />

                    <x-shop::form.control-group.error control-name="inn" />
                </x-shop::form.control-group>

                <!-- KPP -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label>
                        @lang('shop::app.customers.account.organizations.create.kpp')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="text" name="kpp" :value="old('kpp')"
                        :label="trans('shop::app.customers.account.organizations.create.kpp')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.kpp')" />

                    <x-shop::form.control-group.error control-name="kpp" />
                </x-shop::form.control-group>

                <!-- Address -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label>
                        @lang('shop::app.customers.account.organizations.create.address')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="textarea" name="address" :value="old('address')"
                        :label="trans('shop::app.customers.account.organizations.create.address')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.address')" />

                    <x-shop::form.control-group.error control-name="address" />
                </x-shop::form.control-group>

                <button type="submit"
                    class="primary-button m-0 block  px-11 py-3 text-center text-base max-md:w-full max-md:max-w-full max-md: max-md:py-2 max-sm:py-1.5">
                    @lang('shop::app.customers.account.organizations.create.save')
                </button>
            </x-shop::form>
        </div>
</x-shop::layouts.account>