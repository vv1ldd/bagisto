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

        <x-shop::form :action="route('shop.customers.account.organizations.store')">
            <div class="space-y-6" id="wizard-container">
                <!-- ================== STEP 1: ORGANIZATION DETAILS ================== -->
                <div id="step-1" class="transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 1: Данные организации</h2>
                        <span id="step-1-badge" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Заполнено</span>
                    </div>

                    <!-- INN Search -->
                    <x-shop::form.control-group class="!mb-4" id="step-1-input-container">
                        <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.create.inn')
                        </x-shop::form.control-group.label>

                        <div class="flex items-stretch gap-2">
                            <div class="flex-1">
                                <x-shop::form.control-group.control type="text" name="inn" rules="required" :value="old('inn')"
                                    id="inn-input"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all w-full !rounded-lg"
                                    :label="trans('shop::app.customers.account.organizations.create.inn')"
                                    placeholder="Введите ИНН..." />
                            </div>
                            
                            <button type="button" id="lookup-inn-btn"
                                class="px-6 py-3 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold transition-all disabled:opacity-50 text-[14px] rounded-lg whitespace-nowrap flex items-center justify-center">
                                Найти
                            </button>
                        </div>
                        <x-shop::form.control-group.error control-name="inn" />
                    </x-shop::form.control-group>

                    <!-- Extracted Organization Details (Readonly Constants) -->
                    <div id="step-1-details" class="hidden space-y-4 bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative">

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.name')
                                </label>
                                <input type="text" name="name" id="name-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-semibold text-zinc-900 focus:ring-0 cursor-default" />
                            </div>

                            <div id="kpp-container">
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.kpp')
                                </label>
                                <input type="text" name="kpp" id="kpp-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                Название организации (алиас, необязательно)
                            </label>
                            <input type="text" name="alias" id="alias-input" placeholder="Например: Мое ИП, Основная ООО..."
                                class="w-full bg-white border border-zinc-200 rounded px-3 py-2 text-[14px] text-zinc-900 focus:border-[#7C45F5] focus:ring-1 focus:ring-[#7C45F5]/30 transition-all" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                @lang('shop::app.customers.account.organizations.create.address')
                            </label>
                            <input type="text" name="address" id="address-input" readonly 
                                class="w-full bg-transparent border-0 p-0 text-[14px] text-zinc-600 focus:ring-0 cursor-default" />
                        </div>

                        <div class="pt-2 flex flex-col md:flex-row gap-3 items-center justify-between border-t border-zinc-200/60 mt-4">
                            <span class="text-[12px] text-zinc-500">
                                Проверьте данные. Если всё верно, нажмите «Подтвердить».
                            </span>
                            <button type="button" id="confirm-step-1-btn"
                                class="px-6 py-2 border-2 border-[#7C45F5] text-[#7C45F5] hover:bg-[#7C45F5] hover:text-white font-bold transition-all text-[13px] w-full md:w-auto">
                                Подтвердить и продолжить
                            </button>
                        </div>
                    </div>
                </div>


                <!-- ================== STEP 2: BANK DETAILS ================== -->
                <div id="step-2" class="hidden transition-all duration-300 pt-6 border-t border-zinc-100">
                    <div class="flex items-center justify-between mb-4" id="step-2-header">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 2: Банк организации</h2>
                        <span id="step-2-badge" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Заполнено</span>
                    </div>

                    <!-- Bank Search Autocomplete -->
                    <x-shop::form.control-group class="!mb-4" id="step-2-input-container">
                        <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            Название банка или БИК
                        </x-shop::form.control-group.label>

                        <div class="relative w-full overflow-visible">
                            <x-shop::form.control-group.control type="text" name="bic" :value="old('bic')" id="bic-input"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all w-full relative z-10"
                                placeholder="Начните вводить название..." autocomplete="off" />
                                
                            <div id="bank-suggestions" 
                                style="max-height: 320px !important; overflow-y: auto !important;"
                                class="absolute z-[9999] top-full left-0 w-full mt-1 bg-white border border-zinc-200 rounded-lg shadow-2xl hidden">
                                <!-- Suggestions will be injected here via JS -->
                            </div>
                        </div>
                        <x-shop::form.control-group.error control-name="bic" />
                    </x-shop::form.control-group>

                    <!-- Extracted Bank Details (Readonly Constants) -->
                    <div id="step-2-details" class="hidden space-y-4 bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative">

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

                        <div class="pt-2 flex flex-col md:flex-row gap-3 items-center justify-between border-t border-zinc-200/60 mt-4">
                            <span class="text-[12px] text-zinc-500">
                                Банк определен. Нажмите для ввода расч. счета.
                            </span>
                            <button type="button" id="confirm-step-2-btn"
                                class="px-6 py-2 border-2 border-[#7C45F5] text-[#7C45F5] hover:bg-[#7C45F5] hover:text-white font-bold transition-all text-[13px] w-full md:w-auto">
                                Подтвердить и продолжить
                            </button>
                        </div>
                    </div>
                </div>


                <!-- ================== STEP 3: SETTLEMENT ACCOUNT ================== -->
                <div id="step-3" class="hidden transition-all duration-300 pt-6 border-t border-zinc-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 3: Расчетный счет</h2>
                    </div>

                    <x-shop::form.control-group class="!mb-6">
                        <x-shop::form.control-group.label
                            class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider flex items-center gap-2">
                            @lang('shop::app.customers.account.organizations.create.settlement_account')
                            <span class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded text-zinc-500">20 цифр</span>
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" name="settlement_account" rules="required|length:20"
                            :value="old('settlement_account')"
                            id="settlement-account-input"
                            class="!py-4 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 focus:text-lg focus:font-mono transition-all text-[16px] font-mono tracking-widest placeholder:tracking-normal placeholder:font-sans"
                            :label="trans('shop::app.customers.account.organizations.create.settlement_account')"
                            placeholder="40702810..." />

                        <x-shop::form.control-group.error control-name="settlement_account" />
                    </x-shop::form.control-group>

                    <button type="submit" id="submit-btn" disabled
                        class="w-full bg-[#7C45F5] hover:bg-[#6534d4] disabled:bg-zinc-200 disabled:text-zinc-400 text-white font-bold py-4 px-8 rounded-none shadow-lg shadow-[#7C45F5]/20 disabled:shadow-none transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        @lang('shop::app.customers.account.organizations.create.save')
                    </button>
                </div>
            </div>
        </x-shop::form>
    </div>

    <!-- Script moved INSIDE the component structure to ensure execution after dynamic load -->
    <script>
        if (!window.wizardListening) {
            window.wizardListening = true;
            console.log('Wizard Initialization with Document Listeners');

            document.addEventListener('click', async function(e) {
                
                // --- STEP 1: INN Lookup ---
                const lookupInnBtn = e.target.closest('#lookup-inn-btn');
                if (lookupInnBtn) {
                    e.preventDefault();
                    if (lookupInnBtn.disabled) return;
                    
                    const innInput = document.getElementById('inn-input');
                    const inn = innInput ? innInput.value.trim() : '';
                    if (!inn) return alert('Введите ИНН');
                    
                    lookupInnBtn.disabled = true;
                    const originalText = lookupInnBtn.innerText;
                    lookupInnBtn.innerText = '...';
                    
                    try {
                        const url = `{{ route('shop.customers.account.organizations.lookup_inn', ':inn') }}`.replace(':inn', inn);
                        const response = await fetch(url);
                        const data = await response.json();
                        
                        // We must re-query the elements in case DOM updated during fetch
                        if (response.ok) {
                            if (document.getElementById('name-input')) document.getElementById('name-input').value = data.name || '';
                            if (document.getElementById('kpp-input')) document.getElementById('kpp-input').value = data.kpp || '';
                            if (document.getElementById('address-input')) document.getElementById('address-input').value = data.address || '';
                            
                            const kppContainer = document.getElementById('kpp-container');
                            if (kppContainer) {
                                if (!data.kpp) kppContainer.classList.add('hidden');
                                else kppContainer.classList.remove('hidden');
                            }
                            
                            const step1Details = document.getElementById('step-1-details');
                            if (step1Details) step1Details.classList.remove('hidden');
                            
                            // Visual cue
                            ['name-input', 'kpp-input', 'address-input'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    el.style.backgroundColor = '#f0fff4';
                                    setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                                }
                            });
                        } else {
                            alert(data.message || 'Организация не найдена');
                        }
                    } catch (err) {
                        alert('Ошибка сети при поиске ИНН');
                    } finally {
                        // Reselect button as it might have been lost
                        const btn = document.getElementById('lookup-inn-btn');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerText = originalText;
                        }
                    }
                    return;
                }

                // --- STEP 1: Confirm ---
                const confirmStep1Btn = e.target.closest('#confirm-step-1-btn');
                if (confirmStep1Btn) {
                    e.preventDefault();
                    if (document.getElementById('step-1-details')) document.getElementById('step-1-details').classList.add('hidden');
                    if (document.getElementById('step-1-input-container')) document.getElementById('step-1-input-container').classList.add('opacity-50', 'pointer-events-none');
                    if (document.getElementById('lookup-inn-btn')) document.getElementById('lookup-inn-btn').classList.add('hidden');
                    
                    if (document.getElementById('step-1-badge')) document.getElementById('step-1-badge').classList.remove('hidden');
                    
                    const s2 = document.getElementById('step-2');
                    if (s2) {
                        s2.classList.remove('hidden');
                        if (document.getElementById('step-2-header')) {
                            document.getElementById('step-2-header').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        if (document.getElementById('bic-input')) document.getElementById('bic-input').focus();
                    }
                    return;
                }

                // --- STEP 2: Bank Autocomplete removed from click listener as it is now an input listener ---

                // --- STEP 2: Confirm ---
                const confirmStep2Btn = e.target.closest('#confirm-step-2-btn');
                if (confirmStep2Btn) {
                    e.preventDefault();
                    if (document.getElementById('step-2-details')) document.getElementById('step-2-details').classList.add('hidden');
                    if (document.getElementById('step-2-input-container')) document.getElementById('step-2-input-container').classList.add('opacity-50', 'pointer-events-none');
                    
                    if (document.getElementById('step-2-badge')) document.getElementById('step-2-badge').classList.remove('hidden');
                    
                    const s3 = document.getElementById('step-3');
                    if (s3) {
                        s3.classList.remove('hidden');
                        s3.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (document.getElementById('settlement-account-input')) document.getElementById('settlement-account-input').focus();
                    }
                    return;
                }
            });
            
            // Event delegation for input fields
            let bankDebounceTimer;
            
            document.addEventListener('input', function(e) {
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
                }
                if (e.target.id === 'settlement-account-input') {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 20);
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = e.target.value.length !== 20;
                    }
                }
            });

            // Handle clicking a suggestion or clicking outside
            document.addEventListener('click', function(e) {
                const suggestionsContainer = document.getElementById('bank-suggestions');
                const bicInput = document.getElementById('bic-input');
                
                // Clicking a dropdown item
                const item = e.target.closest('div[data-bic]');
                if (item && suggestionsContainer && suggestionsContainer.contains(item)) {
                    if (bicInput) {
                        bicInput.value = item.dataset.bic;
                    }
                    
                    if (document.getElementById('bank-name-input')) document.getElementById('bank-name-input').value = item.dataset.name || '';
                    if (document.getElementById('corr-account-input')) document.getElementById('corr-account-input').value = item.dataset.corr || '';
                    
                    suggestionsContainer.classList.add('hidden');
                    
                    const step2Details = document.getElementById('step-2-details');
                    if (step2Details) step2Details.classList.remove('hidden');
                    
                    ['bank-name-input', 'corr-account-input'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.style.backgroundColor = '#f0fff4';
                            setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                        }
                    });
                    return;
                }
                
                // Clicking outside
                if (suggestionsContainer && !suggestionsContainer.classList.contains('hidden')) {
                    if (bicInput && !bicInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                        suggestionsContainer.classList.add('hidden');
                    }
                }
            });
        }

        // Run validation error visual unlocking
        setTimeout(() => {
            if ("{{ $errors->any() }}" === "1") {
                const step1Details = document.getElementById('step-1-details');
                if (step1Details) step1Details.classList.remove('hidden');
                
                const s2 = document.getElementById('step-2');
                if (s2) s2.classList.remove('hidden');
                
                const step2Details = document.getElementById('step-2-details');
                if (step2Details) step2Details.classList.remove('hidden');
                
                const s3 = document.getElementById('step-3');
                if (s3) s3.classList.remove('hidden');
            }
        }, 100);
    </script>
</x-shop::layouts.account>