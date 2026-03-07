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
                            Название организации (алиас, необязательно)
                        </label>
                        <input type="text" name="alias" value="{{ old('alias') ?? $organization->alias }}" placeholder="Например: Мое ИП, Основная ООО..."
                            class="w-full bg-white border border-zinc-200 rounded px-3 py-2 text-[14px] text-zinc-900 focus:border-[#7C45F5] focus:ring-1 focus:ring-[#7C45F5]/30 transition-all" />
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

                <!-- ================== BLOCK 2: SETTLEMENT ACCOUNTS ================== -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[18px] font-bold text-zinc-900 tracking-tight">Расчетные счета</h2>
                    </div>

                    @if($organization->settlementAccounts->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($organization->settlementAccounts as $account)
                                <div
                                    class="bg-white rounded-xl p-5 border shadow-sm border-zinc-200 relative group transition-all hover:border-[#7C45F5]/30">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                @if($account->alias)
                                                    <div class="text-[16px] font-bold text-zinc-900 leading-tight">
                                                        {{ $account->alias }}
                                                    </div>
                                                @else
                                                    <div class="text-[16px] font-mono tracking-wider font-bold text-zinc-900 leading-tight">
                                                        {{ $account->settlement_account }}
                                                    </div>
                                                @endif

                                                <!-- Edit Alias Button -->
                                                <button type="button" onclick="toggleAliasEdit({{ $account->id }})" class="text-zinc-400 hover:text-[#7C45F5] transition-colors p-1" title="Изменить название">
                                                    <span class="icon-edit text-sm"></span>
                                                </button>
                                            </div>

                                            @if($account->alias)
                                                <div class="text-[13px] font-mono text-zinc-500 mb-0.5">
                                                    {{ $account->settlement_account }}
                                                </div>
                                            @endif

                                            <div class="text-[13px] font-medium text-zinc-500">
                                                {{ $account->bank_name }}
                                            </div>

                                            <!-- Hidden Alias Edit Form -->
                                            <form method="POST" id="edit-alias-form-{{ $account->id }}" action="{{ route('shop.customers.account.organizations.settlement_accounts.update_alias', ['organizationId' => $organization->id, 'accountId' => $account->id]) }}" class="hidden mt-3 max-w-sm">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex items-center gap-2">
                                                    <input type="text" name="alias" value="{{ $account->alias }}" placeholder="Например: Зарплатный" class="w-full text-sm border-zinc-200 rounded focus:border-[#7C45F5] focus:ring focus:ring-[#7C45F5]/20 px-3 py-1.5" />
                                                    <button type="submit" class="bg-[#7C45F5] hover:bg-[#6534d4] text-white text-xs font-bold px-3 py-1.5 rounded transition-colors whitespace-nowrap">
                                                        Сохранить
                                                    </button>
                                                    <button type="button" onclick="toggleAliasEdit({{ $account->id }})" class="text-zinc-400 hover:text-zinc-600 px-2 py-1.5 transition-colors">
                                                        Отмена
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            @if($account->is_default)
                                                <span
                                                    class="bg-[#7C45F5]/10 text-[#7C45F5] text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Основной</span>
                                            @endif

                                            <!-- Delete Button Form -->
                                            <form method="POST"
                                                action="{{ route('shop.customers.account.organizations.settlement_accounts.destroy', ['organizationId' => $organization->id, 'accountId' => $account->id]) }}"
                                                class="inline-block"
                                                onsubmit="return confirm('Вы уверены, что хотите удалить этот расчетный счет?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-100"
                                                    title="Удалить счет">
                                                    <span class="icon-bin text-sm"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-zinc-100">
                                        <div>
                                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">БИК
                                            </div>
                                            <div class="text-[13px] font-mono text-zinc-700">{{ $account->bic }}</div>
                                        </div>
                                        @if($account->correspondent_account)
                                            <div>
                                                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Корр.
                                                    счет</div>
                                                <div class="text-[13px] font-mono text-zinc-700">
                                                    {{ $account->correspondent_account }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-blue-50 text-blue-800 p-4 rounded-lg mb-6 text-sm border border-blue-100">
                            У этой организации еще нет привязанных расчетных счетов. Пожалуйста, добавьте хотя бы один.
                        </div>
                    @endif

                    <!-- ================== ADD NEW ACCOUNT FORM ================== -->
                    <div class="bg-zinc-50 rounded-xl p-5 border border-zinc-200 mt-6" id="add-account-section">
                        <h3 class="text-[15px] font-bold text-zinc-900 mb-4 flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-full bg-[#7C45F5]/10 text-[#7C45F5] flex items-center justify-center text-sm font-bold">+</span>
                            Добавить новый счет
                        </h3>

                        <form method="POST"
                            action="{{ route('shop.customers.account.organizations.settlement_accounts.store', $organization->id) }}"
                            id="add-account-form">
                            @csrf

                            <div class="space-y-4">
                                <x-shop::form.control-group class="!mb-4" id="step-2-input-container" style="position: relative;">
                                    <x-shop::form.control-group.label
                                        class="required !text-[12px] !font-bold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                        Название банка или БИК
                                    </x-shop::form.control-group.label>

                                    <div class="relative w-full overflow-visible">
                                        <x-shop::form.control-group.control type="text" name="bic" id="bic"
                                            :value="old('bic')" autocomplete="off"
                                            class="!w-full !px-4 !py-3 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 font-mono tracking-wider transition-all placeholder:font-sans relative z-10"
                                            placeholder="Начните вводить название..." />

                                        <div id="bank-suggestions" class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-lg shadow-2xl hidden max-h-[250px] overflow-y-auto">
                                            <!-- Suggestions -->
                                        </div>
                                    </div>

                                    <x-shop::form.control-group.error control-name="bic" />
                                    <div id="bic-error" class="hidden text-red-500 text-xs mt-1 font-medium"></div>
                                </x-shop::form.control-group>

                                <div id="bank-details-container"
                                    class="hidden space-y-4 pt-4 border-t border-zinc-200 border-dashed">
                                    <x-shop::form.control-group>
                                        <x-shop::form.control-group.label
                                            class="!text-[12px] !font-bold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                            Наименование банка
                                        </x-shop::form.control-group.label>
                                        <x-shop::form.control-group.control type="text" name="bank_name" id="bank_name"
                                            rules="required" :value="old('bank_name')" readonly
                                            class="!bg-zinc-100 !text-zinc-600 focus:!ring-0 cursor-default" />
                                        <x-shop::form.control-group.error control-name="bank_name" />
                                    </x-shop::form.control-group>

                                    <x-shop::form.control-group>
                                        <x-shop::form.control-group.label
                                            class="!text-[12px] !font-bold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                            Корреспондентский счет
                                        </x-shop::form.control-group.label>
                                        <x-shop::form.control-group.control type="text" name="correspondent_account"
                                            id="correspondent_account" :value="old('correspondent_account')" readonly
                                            class="!bg-zinc-100 !text-zinc-600 font-mono focus:!ring-0 cursor-default" />
                                        <x-shop::form.control-group.error control-name="correspondent_account" />
                                    </x-shop::form.control-group>

                                    <x-shop::form.control-group>
                                        <x-shop::form.control-group.label
                                            class="!text-[12px] !font-bold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                            Название счета (необязательно)
                                        </x-shop::form.control-group.label>
                                        <x-shop::form.control-group.control type="text" name="alias" id="alias" :value="old('alias')"
                                            class="!py-3 !px-4 !border-zinc-300 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all bg-white"
                                            placeholder="Например: Основной Сбербанк, Зарплатный..." />
                                        <x-shop::form.control-group.error control-name="alias" />
                                    </x-shop::form.control-group>

                                    <x-shop::form.control-group class="!mb-6">
                                        <x-shop::form.control-group.label
                                            class="required !text-[12px] !font-bold !text-zinc-500 !mb-1.5 uppercase tracking-wider flex items-center justify-between">
                                            <span>Расчетный счет</span>
                                            <span
                                                class="text-[10px] bg-zinc-200 px-2 py-0.5 rounded-full text-zinc-600">20
                                                цифр</span>
                                        </x-shop::form.control-group.label>

                                        <x-shop::form.control-group.control type="text" name="settlement_account"
                                            id="settlement_account" rules="required|length:20"
                                            :value="old('settlement_account')"
                                            class="!py-3 !px-4 !border-zinc-300 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 font-mono tracking-widest text-[15px] transition-all bg-white"
                                            placeholder="00000000000000000000" />
                                        <x-shop::form.control-group.error control-name="settlement_account" />
                                    </x-shop::form.control-group>

                                    <button type="submit" id="submit-account-btn" disabled
                                        class="w-full bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-300 disabled:text-zinc-500 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                        Сохранить счет
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-shop::form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bicInput = document.getElementById('bic');
            const bicError = document.getElementById('bic-error');
            const bankDetailsContainer = document.getElementById('bank-details-container');
            const bankNameInput = document.getElementById('bank_name');
            const corrAccountInput = document.getElementById('correspondent_account');
            const settlementAccountInput = document.getElementById('settlement_account');
            const submitAccountBtn = document.getElementById('submit-account-btn');

            // Set up Bank Autocomplete
            let bankDebounceTimer;
            if (bicInput) {
                // Return old value if present
                if (bicInput.value.length === 9) {
                    if (bankNameInput && bankNameInput.value) {
                        bankDetailsContainer.classList.remove('hidden');
                    }
                }

                bicInput.addEventListener('input', (e) => {
                    const suggestionsContainer = document.getElementById('bank-suggestions');
                    clearTimeout(bankDebounceTimer);
                    const query = e.target.value.trim();

                    if (query.length < 2) {
                        suggestionsContainer.classList.add('hidden');
                        suggestionsContainer.innerHTML = '';
                        // Bank details hide logic
                        if (query.length !== 9) {
                            bankDetailsContainer.classList.add('hidden');
                            bankNameInput.value = '';
                            corrAccountInput.value = '';
                        }
                        return;
                    }

                    bankDebounceTimer = setTimeout(async () => {
                        try {
                            const url = `{{ route('shop.customers.account.organizations.suggest_bank') }}?query=${encodeURIComponent(query)}`;
                            const response = await fetch(url);
                            const banks = await response.json();

                            if (response.ok && banks && banks.length > 0) {
                                suggestionsContainer.innerHTML = banks.map(bank => `
                                    <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0"
                                        data-name="${bank.bank_name || ''}"
                                        data-bic="${bank.bic || ''}"
                                        data-corr="${bank.correspondent_account || ''}">
                                        <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${bank.bank_name || 'Неизвестный банк'}</div>
                                        <div class="text-[12px] text-zinc-500 font-mono">БИК: ${bank.bic || '-'} | Корр.счет: ${bank.correspondent_account || '-'}</div>
                                    </div>
                                `).join('');
                                suggestionsContainer.classList.remove('hidden');
                            } else {
                                suggestionsContainer.classList.add('hidden');
                            }
                        } catch (err) {
                            console.error('Ошибка при поиске банка', err);
                        }
                    }, 400);
                });

                document.addEventListener('click', function(e) {
                    const suggestionsContainer = document.getElementById('bank-suggestions');
                    
                    if (!suggestionsContainer) return;

                    const item = e.target.closest('div[data-bic]');
                    if (item && suggestionsContainer.contains(item)) {
                        bicInput.value = item.dataset.bic;
                        bankNameInput.value = item.dataset.name || '';
                        corrAccountInput.value = item.dataset.corr || '';

                        suggestionsContainer.classList.add('hidden');
                        bankDetailsContainer.classList.remove('hidden');

                        ['bank_name', 'correspondent_account'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) {
                                el.style.backgroundColor = '#f0fff4';
                                setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                            }
                        });
                        
                        // Focus on settlement account if empty
                        if (!settlementAccountInput.value) {
                            setTimeout(() => settlementAccountInput.focus(), 100);
                        }
                        return;
                    }
                    
                    if (!bicInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                        suggestionsContainer.classList.add('hidden');
                    }
                });
            }
        });

        // Helper to toggle Alias Edit form
        function toggleAliasEdit(accountId) {
            const form = document.getElementById(`edit-alias-form-${accountId}`);
            if (form) {
                form.classList.toggle('hidden');
                form.classList.toggle('block');
            }
        }
    </script>
</x-shop::layouts.account>