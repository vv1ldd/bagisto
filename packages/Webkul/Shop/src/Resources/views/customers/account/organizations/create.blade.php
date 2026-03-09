<x-shop::layouts.account :is-cardless="true" :show-back="false">
    <div
        class="flex-auto ios-tile-relative ios-group max-w-[800px] mx-auto p-8 max-md:p-6 !bg-transparent border-none !shadow-none">

        <!-- Brand Header Section -->
        <div class="flex items-center justify-between mb-12">
            <div class="text-[20px] font-black tracking-tighter text-[#7C45F5]">
                MEANLY
            </div>

            <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.organizations.index') }}'"
                class="w-8 h-8 bg-white border border-gray-100 flex items-center justify-center text-zinc-400 hover:text-[#7C45F5] hover:border-[#7C45F5] transition-all">
                <span class="icon-cancel text-xl"></span>
            </a>
        </div>


        <x-shop::form :action="route('shop.customers.account.organizations.store')">
            <div class="space-y-6" id="wizard-container">
                <!-- ================== STEP 1: ORGANIZATION DETAILS ================== -->
                <div id="step-1" class="transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 1: Данные организации</h2>
                        <div class="flex items-center gap-3">
                            <button type="button" id="magic-scan-btn"
                                class="hidden flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-[#7C45F5] to-[#A855F7] text-white text-[12px] font-bold rounded-none shadow-lg hover:shadow-xl transition-all active:scale-95 group">
                                <span class="group-hover:rotate-12 transition-transform">✨</span> Заполнить по
                                фото/скану
                            </button>
                            <span id="step-1-badge"
                                class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓
                                Заполнено</span>
                        </div>
                    </div>

                    <input type="file" id="magic-scan-input" class="hidden" accept=".pdf,.jpg,.jpeg,.png">

                    <!-- Step 1 Summary (Shown after confirmation) -->
                    <div id="step-1-summary"
                        class="hidden bg-white border border-zinc-200 rounded-none p-5 mb-8 shadow-sm transition-all duration-300">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div id="summary-org-name" class="text-[17px] font-black text-zinc-900 leading-tight">
                                </div>
                                <button type="button" id="edit-step-1-btn"
                                    class="shrink-0 text-[12px] font-bold text-[#7C45F5] hover:bg-[#7C45F5]/5 px-3 py-1.5 rounded transition-all">
                                    Изменить
                                </button>
                            </div>

                            <div id="summary-org-address" class="text-[13px] text-zinc-500 font-medium leading-relaxed max-w-[580px]">
                            </div>

                            <div class="flex flex-wrap items-center gap-x-8 gap-y-2 pt-3 border-t border-zinc-50">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">ИНН</span>
                                    <span id="summary-org-inn" class="text-[13px] font-mono text-zinc-700"></span>
                                </div>
                                <div id="summary-kpp-container" class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">КПП</span>
                                    <span id="summary-org-kpp" class="text-[13px] font-mono text-zinc-700"></span>
                                </div>
                                <div id="summary-ogrn-container" class="hidden flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest opacity-60">ОГРН</span>
                                    <span id="summary-org-ogrn" class="text-[13px] font-mono text-zinc-700"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="step-1-inputs">
                        <!-- INN Search -->
                        <x-shop::form.control-group class="!mb-4" id="step-1-input-container">
                            <x-shop::form.control-group.label
                                class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                                ИНН или название организации
                            </x-shop::form.control-group.label>

                            <div class="relative w-full overflow-visible flex gap-0 border border-zinc-200"
                                id="search-wrapper">
                                <div class="relative flex-grow">
                                    <v-field name="inn" rules="required"
                                        label="ИНН или название организации"
                                        v-slot="{ field }">
                                        <input type="text" id="inn-input" v-bind="field"
                                            class="!py-3.5 !px-4 !border-0 transition-all w-full text-gray-600"
                                            placeholder="Введите ИНН или название компании..." autocomplete="off" />
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

                            <x-shop::form.control-group.error control-name="inn" />
                        </x-shop::form.control-group>

                        <!-- Extracted Organization Details (Confirmation Card) -->
                        <div id="step-1-details"
                            class="hidden space-y-8 bg-white rounded-none p-8 border-2 border-[#7C45F5] relative transition-all duration-500 shadow-[0_20px_50px_rgba(124,69,245,0.1)]">
                            <div
                                class="absolute -top-4 left-6 bg-[#7C45F5] text-white px-4 py-1 text-[12px] font-black uppercase tracking-widest">
                                Проверка данных
                            </div>

                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-[18px] font-black text-zinc-900 tracking-tight">
                                    Это верная организация?
                                </h3>
                            </div>

                            <div class="space-y-6">
                                <div class="p-6 bg-zinc-50/30 !rounded-none border-b border-zinc-100 pb-8 transition-all duration-300" id="name-container">
                                    <label
                                        class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2 opacity-60">
                                        Название организации
                                    </label>
                                    <input type="text" name="name" id="name-input"
                                        class="w-full bg-transparent border-0 p-0 text-[24px] font-black text-zinc-900 focus:ring-0 transition-all placeholder:text-zinc-300 tracking-tight read-only:opacity-80 read-only:cursor-default"
                                        placeholder="Название организации" />
                                </div>

                                <div class="px-6 space-y-8 pb-4">
                                    <div id="address-container" class="transition-all duration-300">
                                        <label
                                            class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2 opacity-60">
                                            Юридический адрес
                                        </label>
                                        <input type="text" name="address" id="address-input"
                                            class="w-full bg-transparent border-0 p-0 text-[15px] font-medium text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 leading-relaxed read-only:opacity-80 read-only:cursor-default"
                                            placeholder="Юридический адрес" />
                                    </div>

                                    <div class="flex flex-wrap items-center gap-x-10 gap-y-4 pt-2 border-t border-zinc-50">
                                        <div id="kpp-container" class="flex items-center gap-3 transition-all duration-300">
                                            <span
                                                class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest opacity-40">КПП</span>
                                            <input type="text" name="kpp" id="kpp-input"
                                                class="w-[110px] bg-zinc-50 border border-zinc-100 px-3 py-1 text-[13px] font-mono text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 rounded-none read-only:bg-zinc-100/50 read-only:border-transparent read-only:cursor-default"
                                                placeholder="—" />
                                        </div>

                                        <div id="ogrn-container" class="flex items-center gap-3 transition-all duration-300">
                                            <span
                                                class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest opacity-40">ОГРН</span>
                                            <input type="text" name="ogrn" id="ogrn-input"
                                                class="w-[160px] bg-zinc-50 border border-zinc-100 px-3 py-1 text-[13px] font-mono text-zinc-600 focus:ring-0 transition-all placeholder:text-zinc-300 rounded-none read-only:bg-zinc-100/50 read-only:border-transparent read-only:cursor-default"
                                                placeholder="—" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 flex flex-col items-center">
                                <button type="button" id="confirm-step-1-btn"
                                    class="w-full bg-[#7C45F5] hover:bg-black text-white font-black py-5 px-8 rounded-none shadow-2xl transition-all active:scale-[0.97] flex items-center justify-center gap-4 text-[17px] uppercase tracking-wider group">
                                    Да, всё верно
                                    <span
                                        class="icon-arrow-right text-xl group-hover:translate-x-1 transition-transform"></span>
                                </button>
                                <p class="text-[12px] text-zinc-400 mt-4 font-medium">
                                    Проверьте данные, прежде чем перейти к реквизитам
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- ================== STEP 2: BANK DETAILS ================== -->
                <div id="step-2" class="hidden transition-all duration-300 pt-8 border-t border-zinc-100">
                    <div id="step-2-card"
                        class="space-y-6 bg-white rounded-none p-6 border-2 border-[#7C45F5] relative transition-all duration-500 shadow-[0_20px_50px_rgba(124,69,245,0.1)]">
                        <div
                            class="absolute -top-3.5 left-6 bg-[#7C45F5] text-white px-3 py-1 text-[11px] font-black uppercase tracking-widest">
                            Реквизиты платежа
                        </div>

                        <div class="flex items-center justify-between mb-1" id="step-2-header">
                            <h2 class="text-[17px] font-black text-zinc-900 tracking-tight">Шаг 2: Банковские реквизиты
                            </h2>
                            <span id="step-2-badge"
                                class="hidden bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">✓
                                Заполнено</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <!-- Left: Bank Search -->
                            <div class="relative">
                                <x-shop::form.control-group class="!mb-0" id="step-2-input-container">
                                    <x-shop::form.control-group.label
                                        class="required !text-[12px] !font-bold text-zinc-400 !mb-1.5 uppercase tracking-wider opacity-80">
                                        Название банка или БИК
                                    </x-shop::form.control-group.label>

                                    <div class="relative w-full overflow-visible">
                                        <x-shop::form.control-group.control type="text" name="bic" :value="old('bic')"
                                            id="bic-input"
                                            class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all w-full relative z-10 !rounded-none text-[15px]"
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

                            <!-- Right: Settlement Account -->
                            <div>
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.label
                                        class="required !text-[12px] !font-bold text-zinc-400 !mb-1.5 uppercase tracking-wider flex items-center gap-2 opacity-80">
                                        @lang('shop::app.customers.account.organizations.create.settlement_account')
                                        <span
                                            class="text-[9px] bg-zinc-50 px-1.5 py-0.5 border border-zinc-100 font-mono text-zinc-400">20</span>
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control type="text" name="settlement_account"
                                        rules="required|length:20" :value="old('settlement_account')"
                                        id="settlement-account-input"
                                        class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-2 focus:!ring-[#7C45F5]/20 transition-all text-[15px] font-mono tracking-[0.1em] placeholder:tracking-normal placeholder:font-sans !rounded-none"
                                        :label="trans('shop::app.customers.account.organizations.create.settlement_account')"
                                        placeholder="40702810..." />

                                    <x-shop::form.control-group.error control-name="settlement_account" />
                                    <div id="settlement-account-error"
                                        class="text-red-500 text-[11px] mt-1 hidden font-bold"></div>
                                </x-shop::form.control-group>
                            </div>
                        </div>

                        <!-- Extracted Bank Details (Premium Tag Style) -->
                        <div id="step-2-details"
                            class="hidden mt-6 space-y-4 bg-white rounded-none p-6 border border-zinc-100 relative shadow-sm">
                            <div class="space-y-4">
                                <div>
                                    <h3 id="display-bank-name"
                                        class="text-[17px] font-bold text-zinc-900 leading-tight"></h3>
                                    <input type="hidden" name="bank_name" id="bank-name-input" />
                                </div>

                                <div class="flex items-center gap-6 pt-4 border-t border-zinc-100">
                                    <div class="text-[12px] text-zinc-400 flex items-center gap-1.5">
                                        <span class="font-bold uppercase tracking-wider opacity-60">БИК</span>
                                        <span id="display-bic"
                                            class="font-mono text-zinc-600 bg-zinc-50 px-2 py-0.5 border border-zinc-100"></span>
                                    </div>
                                    <div class="text-[12px] text-zinc-400 flex items-center gap-1.5">
                                        <span class="font-bold uppercase tracking-wider opacity-60">КОРР. СЧЕТ</span>
                                        <span id="display-corr-account"
                                            class="font-mono text-zinc-600 bg-zinc-50 px-2 py-0.5 border border-zinc-100"></span>
                                        <input type="hidden" name="correspondent_account" id="corr-account-input" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" id="submit-btn" disabled
                                class="w-full bg-[#7C45F5] hover:bg-black disabled:bg-zinc-200 disabled:text-zinc-400 text-white font-black py-4 px-8 rounded-none shadow-2xl disabled:shadow-none transition-all active:scale-[0.97] flex items-center justify-center gap-4 text-[16px] uppercase tracking-wider group">
                                @lang('shop::app.customers.account.organizations.create.save')
                                <span
                                    class="icon-arrow-right text-xl group-hover:translate-x-1 transition-transform"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-shop::form>
    </div>

    <!-- Script moved INSIDE the component structure to ensure execution after dynamic load -->
    <script>
        if (typeof window.initOrganizationWizard === 'undefined') {
            window.isValidBankAccount = function (bic, account) {
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

            window.initOrganizationWizard = function () {
                console.log('Wizard Initialization with Document Listeners');

                let bankDebounceTimer;

                document.addEventListener('click', async function (e) {

                    // --- STEP 1: Confirm ---
                    const confirmStep1Btn = e.target.closest('#confirm-step-1-btn');
                    if (confirmStep1Btn) {
                        e.preventDefault();

                        const name = document.getElementById('name-input').value;
                        const inn = document.getElementById('inn-input').value;
                        const kpp = document.getElementById('kpp-input').value;
                        const ogrn = document.getElementById('ogrn-input').value;

                        const address = document.getElementById('address-input').value;

                        // Fill summary
                        if (document.getElementById('summary-org-name')) document.getElementById('summary-org-name').innerText = name;
                        if (document.getElementById('summary-org-inn')) document.getElementById('summary-org-inn').innerText = inn;
                        if (document.getElementById('summary-org-address')) document.getElementById('summary-org-address').innerText = address;

                        const summaryKppContainer = document.getElementById('summary-kpp-container');
                        if (kpp) {
                            document.getElementById('summary-org-kpp').innerText = kpp;
                            summaryKppContainer.classList.remove('hidden');
                        } else {
                            summaryKppContainer.classList.add('hidden');
                        }

                        const summaryOgrnContainer = document.getElementById('summary-ogrn-container');
                        if (ogrn) {
                            document.getElementById('summary-org-ogrn').innerText = ogrn;
                            summaryOgrnContainer.classList.remove('hidden');
                        } else {
                            summaryOgrnContainer.classList.add('hidden');
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
                            if (document.getElementById('bic-input')) document.getElementById('bic-input').focus();
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


                });

                // --- HELPER: Select Organization and Show Card ---
                window.selectOrganization = function (org) {
                    console.log('Selecting organization:', org.name);

                    const innInput = document.getElementById('inn-input');
                    const nameInput = document.getElementById('name-input');
                    const kppInput = document.getElementById('kpp-input');
                    const addressInput = document.getElementById('address-input');
                    const ogrnInput = document.getElementById('ogrn-input');
                    const suggestionsContainer = document.getElementById('org-suggestions');
                    const step1Details = document.getElementById('step-1-details');
                    const kppContainer = document.getElementById('kpp-container');

                    // Populate fields and set to Readonly (as per user request: identified by INN = no manual edit)
                    if (innInput) innInput.value = org.inn || '';
                    if (nameInput) {
                        nameInput.value = org.name || '';
                        nameInput.readOnly = true;
                    }
                    if (kppInput) {
                        kppInput.value = org.kpp || '';
                        kppInput.readOnly = true;
                    }
                    if (addressInput) {
                        addressInput.value = org.address || '';
                        addressInput.readOnly = true;
                    }
                    if (ogrnInput) {
                        ogrnInput.value = org.ogrn || '';
                        ogrnInput.readOnly = true;
                    }

                    // Show/hide KPP container
                    if (kppContainer) {
                        if (!org.kpp) kppContainer.classList.add('hidden');
                        else kppContainer.classList.remove('hidden');
                    }

                    // Hide suggestions
                    if (suggestionsContainer) suggestionsContainer.classList.add('hidden');

                    // Show confirmation card
                    if (step1Details) {
                        step1Details.classList.remove('hidden');
                        step1Details.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        // Focus the confirm button
                        const confirmBtn = document.getElementById('confirm-step-1-btn');
                        if (confirmBtn) setTimeout(() => confirmBtn.focus(), 200);
                    }
                };

                // Organization Lookup Function
                window.triggerOrgLookup = async (query, forceSelect = false) => {
                    const suggestionsContainer = document.getElementById('org-suggestions');
                    if (!query || query.length < 3) return;

                    try {
                        const relativePath = "{{ route('shop.customers.account.organizations.suggest_organization', [], false) }}";
                        const url = `${window.location.origin}${relativePath}?query=${encodeURIComponent(query)}`;

                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Network response was not ok');

                        const organizations = await response.json();

                        if (organizations && organizations.length > 0) {
                            // AUTO-SELECT: If forceSelect is true and we have exactly one result
                            if (forceSelect && organizations.length === 1) {
                                window.selectOrganization(organizations[0]);
                                return;
                            }

                            suggestionsContainer.innerHTML = organizations.map(org => {
                                const safeName = (org.name || '').replace(/"/g, '&quot;');
                                const safeAddress = (org.address || '').replace(/"/g, '&quot;');
                                const safeInn = (org.inn || '').replace(/"/g, '&quot;');
                                const safeKpp = (org.kpp || '').replace(/"/g, '&quot;');
                                const safeOgrn = (org.ogrn || '').replace(/"/g, '&quot;');

                                return `
                                <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0"
                                    data-name="${safeName}"
                                    data-inn="${safeInn}"
                                    data-kpp="${safeKpp}"
                                    data-ogrn="${safeOgrn}"
                                    data-address="${safeAddress}">
                                    <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${org.name || 'Неизвестная организация'}</div>
                                    <div class="text-[12px] text-zinc-500 font-mono">
                                        ИНН: ${org.inn || '-'} 
                                        ${org.kpp ? ` | КПП: ${org.kpp}` : ''}
                                        ${org.ogrn ? ` | ОГРН: ${org.ogrn}` : ''}
                                    </div>
                                    <div class="text-[11px] text-zinc-400 mt-1 truncate">${org.address || ''}</div>
                                </div>
                            `;}).join('');
                            suggestionsContainer.classList.remove('hidden');
                        } else {
                            suggestionsContainer.classList.add('hidden');
                        }
                    } catch (err) {
                        console.error('Ошибка при поиске организации:', err);
                    }
                };

                // Event delegation for search interactions
                document.addEventListener('click', function (e) {
                    // Search Button Click
                    const searchBtn = e.target.closest('#lookup-org-btn');
                    if (searchBtn) {
                        const innInput = document.getElementById('inn-input');
                        if (innInput) window.triggerOrgLookup(innInput.value.trim(), true);
                    }

                    // Item Selection from Suggestions
                    const orgItem = e.target.closest('div[data-inn]');
                    if (orgItem && document.getElementById('org-suggestions').contains(orgItem)) {
                        window.selectOrganization({
                            name: orgItem.dataset.name,
                            inn: orgItem.dataset.inn,
                            kpp: orgItem.dataset.kpp,
                            ogrn: orgItem.dataset.ogrn,
                            address: orgItem.dataset.address
                        });
                    }
                });

                document.addEventListener('keydown', function (e) {
                    // Enter Key in INN Input
                    if (e.target.id === 'inn-input' && e.key === 'Enter') {
                        e.preventDefault();
                        window.triggerOrgLookup(e.target.value.trim(), true);
                    }
                });

                document.addEventListener('input', function (e) {
                    // Organization Search Activation/Live Search
                    if (e.target.id === 'inn-input') {
                        const innInput = e.target;
                        const query = innInput.value.trim();
                        const lookupBtn = document.getElementById('lookup-org-btn');
                        const step1Details = document.getElementById('step-1-details');

                        if (lookupBtn) lookupBtn.disabled = query.length < 3;

                        // Hide details on edit
                        if (step1Details && !step1Details.classList.contains('hidden')) {
                            step1Details.classList.add('hidden');
                        }

                        if (typeof window.orgDebounceTimer !== 'undefined') clearTimeout(window.orgDebounceTimer);

                        if (query.length < 3) {
                            document.getElementById('org-suggestions').classList.add('hidden');
                            return;
                        }

                        window.orgDebounceTimer = setTimeout(() => {
                            window.triggerOrgLookup(query, false); // Live search doesn't force select
                        }, 500);
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

                                const relativePath = "{{ route('shop.customers.account.organizations.suggest_bank', [], false) }}";
                                const url = `${window.location.origin}${relativePath}?query=${encodeURIComponent(query)}`;
                                
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

                                    suggestionsContainer.innerHTML = banks.map(bank => {
                                        const safeName = (bank.bank_name || '').replace(/"/g, '&quot;');
                                        const safeBic = (bank.bic || '').replace(/"/g, '&quot;');
                                        const safeCorr = (bank.correspondent_account || '').replace(/"/g, '&quot;');

                                        return `
                                        <div class="px-4 py-3 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-0 flex justify-between items-start"
                                            data-name="${safeName}"
                                            data-bic="${safeBic}"
                                            data-corr="${safeCorr}">
                                            <div>
                                                <div class="font-bold text-zinc-900 text-[14px] leading-tight mb-1">${bank.bank_name || 'Неизвестный банк'} ${bank.isValidForAccount ? '<span class="ml-1 text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded">✓ Подходит к счету</span>' : ''}</div>
                                                <div class="text-[12px] text-zinc-500 font-mono">БИК: ${bank.bic || '-'} | Корр.счет: ${bank.correspondent_account || '-'}</div>
                                            </div>
                                        </div>
                                    `;}).join('');
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
                document.addEventListener('click', function (e) {
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
                            // Critical: Dispatch input event for Vue/VeeValidate and to trigger our own listeners
                            bicInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        if (document.getElementById('bank-name-input')) document.getElementById('bank-name-input').value = bankItem.dataset.name || '';
                        if (document.getElementById('display-bank-name')) document.getElementById('display-bank-name').innerText = bankItem.dataset.name || '';

                        if (document.getElementById('corr-account-input')) document.getElementById('corr-account-input').value = bankItem.dataset.corr || '';
                        if (document.getElementById('display-corr-account')) document.getElementById('display-corr-account').innerText = bankItem.dataset.corr || '';

                        if (document.getElementById('display-bic')) document.getElementById('display-bic').innerText = bankItem.dataset.bic || '';

                        bankSuggestions.classList.add('hidden');

                        const step2Details = document.getElementById('step-2-details');
                        if (step2Details) step2Details.classList.remove('hidden');

                        ['display-bank-name', 'display-bic', 'display-corr-account'].forEach(id => {
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