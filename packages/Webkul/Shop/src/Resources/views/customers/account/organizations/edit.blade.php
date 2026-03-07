<x-shop::layouts.account :is-cardless="true" :show-back="false">
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
            <div class="space-y-6">
                <!-- ================== BLOCK 1: ORGANIZATION DETAILS (Readonly) ================== -->
                <div class="bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative">
                    <div class="flex items-center justify-between mb-4 border-b border-zinc-200/60 pb-3">
                        <h2 class="text-[14px] font-bold text-zinc-900 uppercase tracking-wider">Данные организации</h2>
                        <span
                            class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                            <span class="icon-done text-[10px]"></span> Подтверждено ФНС
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.edit.name')
                            </label>
                            <input type="text" name="name" value="{{ old('name') ?? $organization->name }}" readonly
                                class="w-full bg-transparent border-0 p-0 text-[15px] font-semibold text-zinc-900 focus:ring-0 cursor-default" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.edit.inn')
                                @if($organization->kpp)
                                    / @lang('shop::app.customers.account.organizations.edit.kpp')
                                @endif
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="inn" value="{{ old('inn') ?? $organization->inn }}" readonly
                                    class="w-auto bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default flex-1" />
                                @if($organization->kpp)
                                    <span class="text-zinc-300">/</span>
                                    <input type="text" name="kpp" value="{{ old('kpp') ?? $organization->kpp }}" readonly
                                        class="w-auto bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default flex-1" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                            @lang('shop::app.customers.account.organizations.edit.address')
                        </label>
                        <input type="text" name="address" value="{{ old('address') ?? $organization->address }}"
                            readonly
                            class="w-full bg-transparent border-0 p-0 text-[14px] text-zinc-600 focus:ring-0 cursor-default" />
                    </div>
                </div>

                <!-- ================== BLOCK 2: BANK DETAILS (Readonly) ================== -->
                <div class="bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative mt-6">
                    <div class="flex items-center justify-between mb-4 border-b border-zinc-200/60 pb-3">
                        <h2 class="text-[14px] font-bold text-zinc-900 uppercase tracking-wider">Банк организации</h2>
                        <span
                            class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                            <span class="icon-done text-[10px]"></span> Подтверждено ЦБ РФ
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.edit.bank_name')
                            </label>
                            <input type="text" name="bank_name"
                                value="{{ old('bank_name') ?? $organization->bank_name }}" readonly
                                class="w-full bg-transparent border-0 p-0 text-[15px] font-semibold text-zinc-900 focus:ring-0 cursor-default" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.edit.bic')
                            </label>
                            <input type="text" name="bic" value="{{ old('bic') ?? $organization->bic }}" readonly
                                class="w-full bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.edit.correspondent_account')
                            </label>
                            <input type="text" name="correspondent_account"
                                value="{{ old('correspondent_account') ?? $organization->correspondent_account }}"
                                readonly
                                class="w-full bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default" />
                        </div>
                    </div>
                </div>

                <!-- ================== BLOCK 3: SETTLEMENT ACCOUNT (Editable) ================== -->
                <div class="pt-6 border-t border-zinc-100">
                    <x-shop::form.control-group class="!mb-6">
                        <x-shop::form.control-group.label
                            class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider flex items-center gap-2">
                            @lang('shop::app.customers.account.organizations.edit.settlement_account')
                            <span class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded text-zinc-500">20 цифр</span>
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" name="settlement_account"
                            rules="required|length:20" :value="old('settlement_account') ?? $organization->settlement_account" id="settlement-account-input"
                            class="!py-4 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 focus:text-lg focus:font-mono transition-all text-[16px] font-mono tracking-widest"
                            :label="trans('shop::app.customers.account.organizations.edit.settlement_account')" />

                        <x-shop::form.control-group.error control-name="settlement_account" />
                    </x-shop::form.control-group>

                    <button type="submit" id="submit-btn"
                        class="w-full bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-200 disabled:text-zinc-400 text-white font-bold py-4 px-8 rounded-none shadow-lg shadow-[#7C45F5]/20 disabled:shadow-none transition-all active:scale-[0.98]">
                        @lang('shop::app.customers.account.organizations.edit.update-btn')
                    </button>
                </div>
            </div>
        </x-shop::form>
    </div>

    <script>
        setTimeout(() => {
            const settlementInput = document.getElementById('settlement-account-input');
            const submitBtn = document.getElementById('submit-btn');

            if (settlementInput) {
                // Initial check
                submitBtn.disabled = settlementInput.value.replace(/\D/g, '').length !== 20;

                settlementInput.addEventListener('input', (e) => {
                    // Only allow numbers
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 20);
                    submitBtn.disabled = e.target.value.length !== 20;
                });
            }
        }, 300);
    </script>
    </div>
</x-shop::layouts.account>