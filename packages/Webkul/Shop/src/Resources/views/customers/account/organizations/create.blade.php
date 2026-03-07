<x-shop::layouts.account>
    <div class="flex-auto ios-tile-relative ios-group max-w-[600px] mx-auto p-8 max-md:p-6">
        <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.organizations.index') }}'"
            class="ios-close-button">
            <span class="icon-cancel text-xl"></span>
        </a>

        <div class="mb-8">
            <h1 class="text-[22px] font-bold text-zinc-900 leading-tight">
                @lang('shop::app.customers.account.organizations.create.add-organization')
            </h1>
            <p class="text-[14px] text-zinc-500 mt-1">
                Добавьте информацию об организации для выставления счетов.
            </p>
        </div>

        <x-shop::form :action="route('shop.customers.account.organizations.store')">
            <div class="space-y-5">
                <!-- Organization Name -->
                <x-shop::form.control-group class="!mb-0">
                    <x-shop::form.control-group.label
                        class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                        @lang('shop::app.customers.account.organizations.create.name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="text" name="name" rules="required" :value="old('name')"
                        class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                        :label="trans('shop::app.customers.account.organizations.create.name')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.name')" />

                    <x-shop::form.control-group.error control-name="name" />
                </x-shop::form.control-group>

                <div class="grid grid-cols-2 gap-4">
                    <!-- INN -->
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.label
                            class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.create.inn')
                        </x-shop::form.control-group.label>

                        <div class="flex gap-2">
                            <x-shop::form.control-group.control type="text" name="inn" rules="required" :value="old('inn')"
                                id="inn-input"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all flex-1"
                                :label="trans('shop::app.customers.account.organizations.create.inn')"
                                :placeholder="trans('shop::app.customers.account.organizations.create.inn')" />
                            
                            <button type="button" id="lookup-inn-btn"
                                class="px-4 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold transition-all disabled:opacity-50">
                                Найти
                            </button>
                        </div>

                        <x-shop::form.control-group.error control-name="inn" />
                    </x-shop::form.control-group>

                    <!-- KPP -->
                    <x-shop::form.control-group class="!mb-0">
                        <x-shop::form.control-group.label
                            class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.create.kpp')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" name="kpp" :value="old('kpp')"
                            id="kpp-input"
                            class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                            :label="trans('shop::app.customers.account.organizations.create.kpp')"
                            :placeholder="trans('shop::app.customers.account.organizations.create.kpp')" />

                        <x-shop::form.control-group.error control-name="kpp" />
                    </x-shop::form.control-group>
                </div>

                <!-- Address -->
                <x-shop::form.control-group class="!mb-0">
                    <x-shop::form.control-group.label
                        class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                        @lang('shop::app.customers.account.organizations.create.address')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control type="textarea" name="address" :value="old('address')"
                        id="address-input"
                        class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all min-h-[80px]"
                        :label="trans('shop::app.customers.account.organizations.create.address')"
                        :placeholder="trans('shop::app.customers.account.organizations.create.address')" />

                    <x-shop::form.control-group.error control-name="address" />
                </x-shop::form.control-group>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const lookupBtn = document.getElementById('lookup-inn-btn');
                const innInput = document.getElementById('inn-input');
                const nameInput = document.querySelector('input[name="name"]');
                const kppInput = document.getElementById('kpp-input');
                const addressInput = document.getElementById('address-input');

                if (lookupBtn) {
                    lookupBtn.addEventListener('click', async function() {
                        const inn = innInput.value.trim();
                        if (!inn) return;

                        lookupBtn.disabled = true;
                        lookupBtn.innerText = '...';

                        try {
                            const response = await fetch(`{{ route('shop.customers.account.organizations.lookup_inn', ':inn') }}`.replace(':inn', inn));
                            const data = await response.json();

                            if (response.ok) {
                                if (data.name) nameInput.value = data.name;
                                if (data.kpp) kppInput.value = data.kpp;
                                if (data.address) addressInput.value = data.address;
                                
                                // Flash success visual cue
                                [nameInput, kppInput, addressInput].forEach(el => {
                                    el.style.backgroundColor = '#f0fff4';
                                    setTimeout(() => el.style.backgroundColor = '', 1000);
                                });
                            } else {
                                alert(data.message || 'Ошибка при поиске');
                            }
                        } catch (error) {
                            console.error('INN lookup error:', error);
                            alert('Не удалось выполнить поиск');
                        } finally {
                            lookupBtn.disabled = false;
                            lookupBtn.innerText = 'Найти';
                        }
                    });
                }
            });
        </script>
    @endpush

                <div class="pt-4 border-t border-zinc-100 mt-6 pt-6">
                    <h2 class="text-[16px] font-bold text-zinc-900 mb-4">Банковские реквизиты</h2>

                    <div class="space-y-4">
                        <!-- Bank Name -->
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.label
                                class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                @lang('shop::app.customers.account.organizations.create.bank_name')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" name="bank_name" :value="old('bank_name')"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                :label="trans('shop::app.customers.account.organizations.create.bank_name')"
                                :placeholder="trans('shop::app.customers.account.organizations.create.bank_name')" />

                            <x-shop::form.control-group.error control-name="bank_name" />
                        </x-shop::form.control-group>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- BIC -->
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                    @lang('shop::app.customers.account.organizations.create.bic')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="bic" :value="old('bic')"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                    :label="trans('shop::app.customers.account.organizations.create.bic')"
                                    :placeholder="trans('shop::app.customers.account.organizations.create.bic')" />

                                <x-shop::form.control-group.error control-name="bic" />
                            </x-shop::form.control-group>

                            <!-- Settlement Account -->
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                    @lang('shop::app.customers.account.organizations.create.settlement_account')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="settlement_account"
                                    :value="old('settlement_account')"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                    :label="trans('shop::app.customers.account.organizations.create.settlement_account')"
                                    :placeholder="trans('shop::app.customers.account.organizations.create.settlement_account')" />

                                <x-shop::form.control-group.error control-name="settlement_account" />
                            </x-shop::form.control-group>
                        </div>

                        <!-- Correspondent Account -->
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.label
                                class="!text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                @lang('shop::app.customers.account.organizations.create.correspondent_account')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" name="correspondent_account"
                                :value="old('correspondent_account')"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all"
                                :label="trans('shop::app.customers.account.organizations.create.correspondent_account')"
                                :placeholder="trans('shop::app.customers.account.organizations.create.correspondent_account')" />

                            <x-shop::form.control-group.error control-name="correspondent_account" />
                        </x-shop::form.control-group>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit"
                        class="w-full bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold py-4 px-8 rounded-none shadow-lg shadow-[#7C45F5]/20 transition-all active:scale-[0.98]">
                        @lang('shop::app.customers.account.organizations.create.save')
                    </button>
                </div>
            </div>
        </x-shop::form>
    </div>
</x-shop::layouts.account>