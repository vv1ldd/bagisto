<x-shop::layouts.account :is-cardless="true" :show-back="false">
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

        <style>
            /* Ultimate Rectangular Enforcement */
            #wizard-root, #wizard-root *, 
            #wizard-root input, #wizard-root button, #wizard-root select,
            #wizard-root .ios-tile, #wizard-root [class*="rounded-"] {
                border-radius: 0 !important;
            }
            
            #inn-input, #inn-input:focus, .v-field input, .v-field input:focus {
                border-radius: 0 !important;
                outline: none !important;
                box-shadow: none !important;
            }

            #search-wrapper {
                border-radius: 0 !important;
                overflow: hidden;
            }
            
            /* Remove framework-applied focus rings that might look rounded */
            #wizard-root input:focus {
                --tw-ring-offset-shadow: 0 0 #0000 !important;
                --tw-ring-shadow: 0 0 #0000 !important;
                outline: 2px solid #7C45F5 !important;
                outline-offset: -2px !important;
            }
        </style>

        <x-shop::form :action="route('shop.customers.account.organizations.store')" id="wizard-root">
            <div class="space-y-6" id="wizard-container">
                <!-- ================== STEP 1: ORGANIZATION DETAILS ================== -->
                <div id="step-1" class="transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 1: Данные организации</h2>
                        <div class="flex items-center gap-3">
                            <button type="button" id="magic-scan-btn" 
                                class="hidden flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-[#7C45F5] to-[#A855F7] text-white text-[12px] font-bold rounded-none shadow-lg hover:shadow-xl transition-all active:scale-95 group">
                                <span class="group-hover:rotate-12 transition-transform">✨</span> Заполнить по фото/скану
                            </button>
                            <span id="step-1-badge" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Заполнено</span>
                        </div>
                    </div>

                    <input type="file" id="magic-scan-input" class="hidden" accept=".pdf,.jpg,.jpeg,.png">

                    <!-- Step 1 Summary (Shown after confirmation) -->
                    <div id="step-1-summary" class="hidden bg-white border border-zinc-200 rounded-none p-5 mb-6 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <div id="summary-org-name" class="text-[18px] font-bold text-zinc-900 leading-tight"></div>
                                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-zinc-100">
                                    <div class="text-[12px] text-zinc-400">
                                        <span class="font-bold uppercase tracking-wider mr-1">ИНН:</span>
                                        <span id="summary-org-inn" class="font-mono text-zinc-600"></span>
                                    </div>
                                    <div id="summary-kpp-container" class="text-[12px] text-zinc-400">
                                        <span class="font-bold uppercase tracking-wider mr-1">КПП:</span>
                                        <span id="summary-org-kpp" class="font-mono text-zinc-600"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="edit-step-1-btn" class="text-[12px] font-bold text-[#7C45F5] hover:underline px-3 py-1 bg-[#7C45F5]/5 rounded-none transition-all">
                                Изменить
                            </button>
                        </div>
                    </div>

                    <div id="step-1-inputs">
                        <!-- INN Search -->
                        <x-shop::form.control-group class="!mb-4" id="step-1-input-container">
                            <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                @lang('shop::app.customers.account.organizations.create.inn')
                            </x-shop::form.control-group.label>

                            <div class="relative w-full overflow-visible flex gap-0 border border-zinc-200" id="search-wrapper">
                                <div class="relative flex-grow">
                                    <v-field
                                        name="inn"
                                        rules="required"
                                        label="{{ trans('shop::app.customers.account.organizations.create.inn') }}"
                                        v-slot="{ field }"
                                    >
                                        <input
                                            type="text"
                                            id="inn-input"
                                            v-bind="field"
                                            class="!py-3.5 !px-4 !border-0 transition-all w-full text-gray-600"
                                            placeholder="Введите ИНН или название..." 
                                            autocomplete="off" />
                                    </v-field>
                                </div>
                                
                                <button type="button" id="lookup-org-btn" disabled
                                    class="bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-100 disabled:text-zinc-400 text-white font-bold px-8 !py-3.5 transition-all whitespace-nowrap text-[14px]">
                                    Найти
                                </button>

                                <div id="org-suggestions" 
                                    style="max-height: 320px !important; overflow-y: auto !important;"
                                    class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-none shadow-2xl hidden">
                                    <!-- Suggestions will be injected here via JS -->
                                </div>
                            </div>
                            
                            <div class="mt-3 text-right">
                                <button type="button" id="manual-entry-btn" class="text-[12px] text-zinc-500 hover:text-[#7C45F5] transition-colors underline underline-offset-2">
                                    Ввести данные вручную
                                </button>
                            </div>
                            <x-shop::form.control-group.error control-name="inn" />
                        </x-shop::form.control-group>

                        <!-- Extracted Organization Details (Confirmation Card) -->
                        <div id="step-1-details" class="hidden space-y-6 bg-[#7C45F5]/5 rounded-none p-6 border border-[#7C45F5]/10 relative transition-all duration-500">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-[#7C45F5] text-white p-1.5 rounded-none">
                                    <span class="icon-done text-xs"></span>
                                </div>
                                <h3 class="text-[15px] font-bold text-zinc-900">
                                    Это верная организация?
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 gap-5">
                                <div class="ios-tile p-4 bg-white/60 !rounded-none">
                                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2">
                                        @lang('shop::app.customers.account.organizations.create.name')
                                    </label>
                                    <input type="text" name="name" id="name-input"  
                                        class="w-full bg-transparent border-0 p-0 text-[17px] font-bold text-zinc-900 focus:ring-0 transition-all placeholder:text-zinc-300"
                                        placeholder="Название организации" />
                                </div>

                                <div id="kpp-container" class="ios-tile p-4 bg-white/60 !rounded-none">
                                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2">
                                        @lang('shop::app.customers.account.organizations.create.kpp')
                                    </label>
                                    <input type="text" name="kpp" id="kpp-input"  
                                        class="w-full bg-transparent border-0 p-0 text-[16px] font-mono text-zinc-700 focus:ring-0 transition-all placeholder:text-zinc-300"
                                        placeholder="КПП (если есть)" />
                                </div>

                                <div class="ios-tile p-4 bg-white/60 !rounded-none">
                                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2">
                                        @lang('shop::app.customers.account.organizations.create.address')
                                    </label>
                                    <input type="text" name="address" id="address-input"  
                                        class="w-full bg-transparent border-0 p-0 text-[14px] text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300"
                                        placeholder="Юридический адрес" />
                                </div>
                            </div>

                            <div class="pt-4 flex flex-col items-center">
                                <button type="button" id="confirm-step-1-btn"
                                    class="w-full bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold py-4 px-8 rounded-none shadow-xl shadow-[#7C45F5]/20 transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[16px]">
                                    Подтвердить и продолжить
                                    <span class="icon-arrow-right text-lg"></span>
                                </button>
                                <p class="text-[12px] text-zinc-400 mt-4 font-medium">
                                    Проверьте данные, прежде чем перейти к реквизитам
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- ================== STEP 2: BANK DETAILS ================== -->
                <div id="step-2" class="hidden transition-all duration-300 pt-6 border-t border-zinc-100">
                    <div class="flex items-center justify-between mb-4" id="step-2-header">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 2: Банковские реквизиты</h2>
                        <span id="step-2-badge" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Заполнено</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Settlement Account (Primary focus) -->
                        <div>
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider flex items-center gap-2">
                                    @lang('shop::app.customers.account.organizations.create.settlement_account')
                                    <span class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-none text-zinc-500">20 цифр</span>
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="settlement_account" rules="required|length:20"
                                    :value="old('settlement_account')"
                                    id="settlement-account-input"
                                    class="!py-4 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 focus:text-lg focus:font-mono transition-all text-[16px] font-mono tracking-widest placeholder:tracking-normal placeholder:font-sans !rounded-none"
                                    :label="trans('shop::app.customers.account.organizations.create.settlement_account')"
                                    placeholder="40702810..." />

                                <x-shop::form.control-group.error control-name="settlement_account" />
                                <div id="settlement-account-error" class="text-red-500 text-[12px] mt-1 hidden font-bold"></div>
                            </x-shop::form.control-group>
                        </div>

                        <!-- Right: Bank Search -->
                        <div class="relative">
                            <x-shop::form.control-group class="!mb-0" id="step-2-input-container">
                                <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                    Название банка или БИК
                                </x-shop::form.control-group.label>

                                <div class="relative w-full overflow-visible">
                                    <x-shop::form.control-group.control type="text" name="bic" :value="old('bic')" id="bic-input"
                                        class="!py-4 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all w-full relative z-10 !rounded-none"
                                        placeholder="Начните вводить..." autocomplete="off" />
                                        
                                    <div id="bank-suggestions" 
                                        style="max-height: 320px !important; overflow-y: auto !important;"
                                        class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-none shadow-2xl hidden">
                                        <!-- Suggestions will be injected here via JS -->
                                    </div>
                                </div>
                                <x-shop::form.control-group.error control-name="bic" />
                            </x-shop::form.control-group>
                        </div>
                    </div>

                    <!-- Extracted Bank Details (Readonly Constants) -->
                    <div id="step-2-details" class="hidden mt-6 space-y-4 bg-zinc-50/50 rounded-none p-5 border border-zinc-100 relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.bank_name')
                                </label>
                                <input type="text" name="bank_name" id="bank-name-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-semibold text-zinc-900 focus:ring-0 cursor-default" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.correspondent_account')
                                </label>
                                <input type="text" name="correspondent_account" id="corr-account-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" id="submit-btn" disabled
                            class="w-full bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-200 disabled:text-zinc-400 text-white font-bold py-4 px-8 rounded-none shadow-lg shadow-[#7C45F5]/20 disabled:shadow-none transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            @lang('shop::app.customers.account.organizations.create.save')
                        </button>
                    </div>
                </div>
            </div>
        </x-shop::form>
    </div>

    <!-- Script moved INSIDE the component structure to ensure execution after dynamic load -->
    <script>
        if (typeof window.initOrganizationWizard === 'undefined') {
            window.isValidBankAccount = function(bic, account) {
                if (!bic || !account || account.length !== 20 || bic.length !== 9) return false;
                
                // CBR Algorithm: last 3 digits of BIC + 20 digits of Account
                const combined = bic.substring(6, 9) + account;
                const weights = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];
                
                let sum = 0;
                for (let i = 0; i < 23; i++) {
                    sum += (parseInt(combined[i]) * weights[i]) % 10;
                }
                
                return sum % 10 === 0;
            };

            window.initOrganizationWizard = function() {           console.log('Wizard Initialization with Document Listeners');

            document.addEventListener('click', async function(e) {
                
            // --- STEP 1: Confirm ---
            const confirmStep1Btn = e.target.closest('#confirm-step-1-btn');
            if (confirmStep1Btn) {
                    e.preventDefault();
                    
                    const name = document.getElementById('name-input').value;
                    const inn = document.getElementById('inn-input').value;
                    const kpp = document.getElementById('kpp-input').value;
                    
                    // Fill summary
                    document.getElementById('summary-org-name').innerText = name;
                    document.getElementById('summary-org-inn').innerText = inn;
                    
                    const summaryKppContainer = document.getElementById('summary-kpp-container');
                    if (kpp) {
                        document.getElementById('summary-org-kpp').innerText = kpp;
                        summaryKppContainer.classList.remove('hidden');
                    } else {
                        summaryKppContainer.classList.add('hidden');
                    }
                    
                    // Toggle visibility
                    document.getElementById('step-1-inputs').classList.add('hidden');
                    document.getElementById('step-1-summary').classList.remove('hidden');
                    document.getElementById('step-1-badge').classList.remove('hidden');
                    
                    const s2 = document.getElementById('step-2');
                    if (s2) {
                        s2.classList.remove('hidden');
                        if (document.getElementById('step-2-header')) {
                            document.getElementById('step-2-header').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        if (document.getElementById('bank-query')) document.getElementById('bank-query').focus();
                    }
                    return;
                }

                // --- STEP 1: Edit ---
                const editStep1Btn = e.target.closest('#edit-step-1-btn');
                if (editStep1Btn) {
                    e.preventDefault();
                    document.getElementById('step-1-inputs').classList.remove('hidden');
                    document.getElementById('step-1-summary').classList.add('hidden');
                    document.getElementById('step-1-badge').classList.add('hidden');
                    
                    // Hide subsequent steps
                    ['step-2', 'step-3', 'step-4'].forEach(id => {
                        const step = document.getElementById(id);
                        if (step) step.classList.add('hidden');
                    });
                    
                    document.getElementById('inn-input').focus();
                    return;
                }

                // --- STEP 1: Manual Entry ---
                const manualEntryBtn = e.target.closest('#manual-entry-btn');
                if (manualEntryBtn) {
                    e.preventDefault();
                    
                    const step1Details = document.getElementById('step-1-details');
                    if (step1Details) {
                        step1Details.classList.remove('hidden');
                        step1Details.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    const kppContainer = document.getElementById('kpp-container');
                    if (kppContainer) kppContainer.classList.remove('hidden');
                    
                    const nameInput = document.getElementById('name-input');
                    if (nameInput) nameInput.focus();
                    
                    return;
                }

            });
            
            // Organization Lookup Function
            window.triggerOrgLookup = async (query) => {
                const suggestionsContainer = document.getElementById('org-suggestions');
                if (!query || query.length < 3) return;

                try {
                    const url = `{{ route('shop.customers.account.organizations.suggest_organization') }}?query=${encodeURIComponent(query)}`;
                    const response = await fetch(url);
                    const organizations = await response.json();
                    
                    if (response.ok && organizations && organizations.length > 0) {
                        suggestionsContainer.innerHTML = organizations.map(org => `
                            <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0"
                                data-name="${org.name || ''}"
                                data-inn="${org.inn || ''}"
                                data-kpp="${org.kpp || ''}"
                                data-address="${org.address || ''}">
                                <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${org.name || 'Неизвестная организация'}</div>
                                <div class="text-[12px] text-zinc-500 font-mono">
                                    ИНН: ${org.inn || '-'} 
                                    ${org.kpp ? ` | КПП: ${org.kpp}` : ''}
                                </div>
                                <div class="text-[11px] text-zinc-400 mt-1 truncate">${org.address || ''}</div>
                            </div>
                        `).join('');
                        suggestionsContainer.classList.remove('hidden');
                    } else {
                        suggestionsContainer.classList.add('hidden');
                    }
                } catch (err) {
                    console.error('Ошибка при поиске организации', err);
                }
            };

            // Search Button Click
            document.addEventListener('click', function(e) {
                if (e.target.id === 'lookup-org-btn') {
                    const innInput = document.getElementById('inn-input');
                    if (innInput) {
                        window.triggerOrgLookup(innInput.value.trim());
                    }
                }
            });

            // Event delegation for input fields
            let bankDebounceTimer;
            
            document.addEventListener('input', function(e) {
                // Organization Search Activation
                if (e.target.id === 'inn-input') {
                    const lookupBtn = document.getElementById('lookup-org-btn');
                    if (lookupBtn) {
                        lookupBtn.disabled = e.target.value.trim().length < 3;
                    }
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.target.id === 'inn-input' && e.key === 'Enter') {
                    e.preventDefault();
                    window.triggerOrgLookup(e.target.value.trim());
                }
            });

            document.addEventListener('input', function(e) {
                // Organization Live Search (Keep it but it can be redundant now)
                if (e.target.id === 'inn-input') {
                    const innInput = e.target;
                    const suggestionsContainer = document.getElementById('org-suggestions');
                    
                    if (typeof window.orgDebounceTimer !== 'undefined') clearTimeout(window.orgDebounceTimer);
                    const query = innInput.value.trim();
                    
                    if (query.length < 3) {
                        suggestionsContainer.classList.add('hidden');
                        suggestionsContainer.innerHTML = '';
                        return;
                    }
                    
                    window.orgDebounceTimer = setTimeout(() => {
                        window.triggerOrgLookup(query);
                    }, 600); // Slightly longer debounce for live search
                }

                // Bank Autocomplete Logic
                if (e.target.id === 'bic-input') {
                    const bicInput = e.target;
                    const suggestionsContainer = document.getElementById('bank-suggestions');
                    
                    clearTimeout(bankDebounceTimer);
                    const query = bicInput.value.trim();
                    
                    if (query.length < 2) {
                        suggestionsContainer.classList.add('hidden');
                        suggestionsContainer.innerHTML = '';
                        return;
                    }
                    
                    bankDebounceTimer = setTimeout(async () => {
                        try {
                            const accountInput = document.getElementById('settlement-account-input');
                            const account = accountInput ? accountInput.value.trim() : '';
                            
                            const url = `{{ route('shop.customers.account.organizations.suggest_bank') }}?query=${encodeURIComponent(query)}`;
                            const response = await fetch(url);
                            let banks = await response.json();
                            
                            if (response.ok && banks && banks.length > 0) {
                                // Prioritize banks that match the current account checksum
                                if (account.length === 20) {
                                    banks = banks.map(bank => ({
                                        ...bank,
                                        isValidForAccount: window.isValidBankAccount(bank.bic, account)
                                    })).sort((a, b) => b.isValidForAccount - a.isValidForAccount);
                                }

                                suggestionsContainer.innerHTML = banks.map(bank => `
                                    <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0 flex justify-between items-start"
                                        data-name="${bank.bank_name || ''}"
                                        data-bic="${bank.bic || ''}"
                                        data-corr="${bank.correspondent_account || ''}">
                                        <div>
                                            <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${bank.bank_name || 'Неизвестный банк'} ${bank.isValidForAccount ? '<span class="ml-1 text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded">✓ Подходит к счету</span>' : ''}</div>
                                            <div class="text-[12px] text-zinc-500 font-mono">БИК: ${bank.bic || '-'} | Корр.счет: ${bank.correspondent_account || '-'}</div>
                                        </div>
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
                }
                if (e.target.id === 'settlement-account-input') {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 20);
                    
                    const account = e.target.value;
                    const bicInput = document.getElementById('bic-input');
                    const bic = bicInput ? bicInput.value.trim() : '';
                    const submitBtn = document.getElementById('submit-btn');
                    const errorMsg = document.getElementById('settlement-account-error');
                    
                    if (account.length === 20 && bic.length === 9) {
                        const isValid = window.isValidBankAccount(bic, account);
                        
                        if (isValid) {
                            e.target.classList.remove('!border-red-500', '!ring-red-500');
                            e.target.classList.add('!border-green-500');
                            if (errorMsg) errorMsg.classList.add('hidden');
                            if (submitBtn) submitBtn.disabled = false;
                        } else {
                            e.target.classList.remove('!border-green-500');
                            e.target.classList.add('!border-red-500', '!ring-red-500');
                            if (errorMsg) {
                                errorMsg.innerText = 'Неверный контрольный ключ счета для данного БИК';
                                errorMsg.classList.remove('hidden');
                            }
                            if (submitBtn) submitBtn.disabled = true;
                        }
                    } else if (account.length === 20 && bic.length === 0) {
                        // Account entered first, just generic success if 20 digits, 
                        // checksum will be verified once BIC is selected
                        e.target.classList.add('!border-green-500');
                        if (errorMsg) errorMsg.classList.add('hidden');
                        if (submitBtn) submitBtn.disabled = true; // Still need BIC
                    } else {
                        e.target.classList.remove('!border-green-500', '!border-red-500', '!ring-red-500');
                        if (errorMsg) errorMsg.classList.add('hidden');
                        if (submitBtn) submitBtn.disabled = true;
                    }
                }
            });

        // Handle clicking a suggestion or clicking outside
        document.addEventListener('click', function(e) {
            const orgSuggestions = document.getElementById('org-suggestions');
            const bankSuggestions = document.getElementById('bank-suggestions');
            const innInput = document.getElementById('inn-input');
            const bicInput = document.getElementById('bic-input');
            
            // --- Organization Suggestion Selection ---
            const orgItem = e.target.closest('div[data-inn]');
            if (orgItem && orgSuggestions && orgSuggestions.contains(orgItem)) {
                if (innInput) innInput.value = orgItem.dataset.inn;
                if (document.getElementById('name-input')) document.getElementById('name-input').value = orgItem.dataset.name || '';
                if (document.getElementById('kpp-input')) document.getElementById('kpp-input').value = orgItem.dataset.kpp || '';
                if (document.getElementById('address-input')) document.getElementById('address-input').value = orgItem.dataset.address || '';
                
                const kppContainer = document.getElementById('kpp-container');
                if (kppContainer) {
                    if (!orgItem.dataset.kpp) kppContainer.classList.add('hidden');
                    else kppContainer.classList.remove('hidden');
                }
                
                orgSuggestions.classList.add('hidden');
                
                const step1Details = document.getElementById('step-1-details');
                if (step1Details) {
                    step1Details.classList.remove('hidden');
                    // Focus the confirm button immediately for the user
                    const confirmBtn = document.getElementById('confirm-step-1-btn');
                    if (confirmBtn) {
                        setTimeout(() => confirmBtn.focus(), 100);
                    }
                    
                    // Smooth scroll to the confirmation card
                    step1Details.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                return;
            }

            // --- Bank Suggestion Selection ---
            const bankItem = e.target.closest('div[data-bic]');
            if (bankItem && bankSuggestions && bankSuggestions.contains(bankItem)) {
                if (bicInput) {
                    bicInput.value = bankItem.dataset.bic;
                }
                
                if (document.getElementById('bank-name-input')) document.getElementById('bank-name-input').value = bankItem.dataset.name || '';
                if (document.getElementById('corr-account-input')) document.getElementById('corr-account-input').value = bankItem.dataset.corr || '';
                
                bankSuggestions.classList.add('hidden');
                
                const step2Details = document.getElementById('step-2-details');
                if (step2Details) step2Details.classList.remove('hidden');
                
                ['bank-name-input', 'corr-account-input'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.backgroundColor = '#f0fff4';
                        setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                    }
                });

                // Re-validate settlement account if it exists
                const accountInput = document.getElementById('settlement-account-input');
                if (accountInput && accountInput.value.length === 20) {
                    accountInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                return;
            }
            
            // Clicking outside
            if (orgSuggestions && !orgSuggestions.classList.contains('hidden')) {
                if (innInput && !innInput.contains(e.target) && !orgSuggestions.contains(e.target)) {
                    orgSuggestions.classList.add('hidden');
                }
            }
            if (bankSuggestions && !bankSuggestions.classList.contains('hidden')) {
                if (bicInput && !bicInput.contains(e.target) && !bankSuggestions.contains(e.target)) {
                    bankSuggestions.classList.add('hidden');
                }
            }
        });
        }
    }

    // Initialize the wizard
    window.initOrganizationWizard();

    // Run validation error visual unlocking
        setTimeout(() => {
            if ("{{ $errors->any() }}" === "1") {
                const step1Details = document.getElementById('step-1-details');
                if (step1Details) step1Details.classList.remove('hidden');
                
                const s2 = document.getElementById('step-2');
                if (s2) s2.classList.remove('hidden');
                
                const step2Details = document.getElementById('step-2-details');
                if (step2Details) step2Details.classList.remove('hidden');
            }
        }, 100);
    </script>
</x-shop::layouts.account>