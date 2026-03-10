<x-shop::layouts.account :is-cardless="true" :has-header="false">
    <div class="flex items-center justify-center min-h-[85vh] py-10">
        <div class="w-full max-w-[700px] bg-white border border-zinc-100 shadow-xl relative pt-6 pb-4 px-8">
            <a href="{{ route('shop.customers.account.organizations.index') }}"
                class="absolute top-0 right-0 p-4 text-zinc-400 hover:text-red-500 transition-colors group">
                <span class="icon-cancel text-xl group-hover:scale-110 transition-transform"></span>
            </a>

            <div class="pt-2 pb-4 flex border-b border-zinc-50 mb-6">
                <h1 class="text-[18px] font-bold text-zinc-900 leading-tight">
                    Добавление организации
                </h1>
            </div>

            <form action="{{ route('shop.customers.account.organizations.store') }}" method="POST" id="org-form">
                @csrf

                <!-- SECTION 1: Organization Details -->
                <div class="text-left mb-6">
                    <h3
                        class="text-[12px] font-bold uppercase tracking-wider text-zinc-400 mb-4 flex items-center gap-2 border-b border-zinc-50 pb-2">
                        <span class="text-zinc-400 text-lg">📁</span>
                        Основные сведения
                    </h3>

                    <div class="space-y-4">
                        {{-- Row 1: Search --}}
                        <div class="relative">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="required uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    Поиск по названию или ИНН
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="name" id="org-name"
                                    value="{{ old('name') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-1 focus:!ring-[#7C45F5] text-[14px] text-zinc-900 font-medium"
                                    placeholder="Начните вводить данные для автозаполнения..." autocomplete="off" />

                                <x-shop::form.control-group.error control-name="name" />
                            </x-shop::form.control-group>

                            <div id="org-suggestions"
                                class="absolute z-[60] w-full mt-1 bg-white border border-zinc-200 shadow-2xl hidden max-h-72 overflow-y-auto ltr:left-0 rtl:right-0">
                            </div>
                        </div>

                        {{-- Row 2: INN | KPP --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="required uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    ИНН
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="inn" id="org-inn"
                                    value="{{ old('inn') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 focus:!border-[#7C45F5]"
                                    placeholder="10 или 12 цифр" />

                                <x-shop::form.control-group.error control-name="inn" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    КПП
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="kpp" id="org-kpp"
                                    value="{{ old('kpp') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 focus:!border-[#7C45F5]"
                                    placeholder="9 цифр" />

                                <x-shop::form.control-group.error control-name="kpp" />
                            </x-shop::form.control-group>
                        </div>

                        {{-- Row 3: OGRN | Address --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    ОГРН
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="ogrn" id="org-ogrn"
                                    value="{{ old('ogrn') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 focus:!border-[#7C45F5]"
                                    placeholder="ОГРН" />

                                <x-shop::form.control-group.error control-name="ogrn" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    Юридический адрес
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="address" id="org-address"
                                    value="{{ old('address') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 text-[14px] text-zinc-900 focus:!border-[#7C45F5] truncate"
                                    placeholder="Полный адрес" />

                                <x-shop::form.control-group.error control-name="address" />
                            </x-shop::form.control-group>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Bank Details -->
                <div class="text-left mb-6">
                    <h3
                        class="text-[12px] font-bold uppercase tracking-wider text-zinc-400 mb-4 flex items-center gap-2 border-b border-zinc-50 pb-2">
                        <span class="text-zinc-400 text-lg">🏦</span>
                        Банковские реквизиты
                    </h3>

                    <div class="space-y-4">
                        {{-- Row 1: BIC | Settlement Account --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <x-shop::form.control-group class="!mb-0">
                                    <x-shop::form.control-group.label
                                        class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                        БИК или Название Банка
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control type="text" name="bic" id="bank-bic"
                                        value="{{ old('bic') }}"
                                        class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 focus:!border-[#7C45F5]"
                                        placeholder="БИК или название" autocomplete="off" />

                                    <x-shop::form.control-group.error control-name="bic" />
                                </x-shop::form.control-group>

                                <div id="bank-suggestions"
                                    class="absolute z-[60] w-full mt-1 bg-white border border-zinc-200 shadow-2xl hidden max-h-72 overflow-y-auto ltr:left-0 rtl:right-0">
                                </div>
                            </div>

                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="required uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    Расчетный счет
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="settlement_account"
                                    id="bank-account" value="{{ old('settlement_account') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 focus:!border-[#7C45F5]"
                                    placeholder="20 цифр" />

                                <x-shop::form.control-group.error control-name="settlement_account" />
                            </x-shop::form.control-group>
                        </div>

                        {{-- Row 2: Bank name | Correspondent Account --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    Название Банка
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="bank_name" id="bank-name"
                                    value="{{ old('bank_name') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 text-[14px] text-zinc-900 bg-zinc-50 font-medium"
                                    placeholder="Подтянется по БИК" readonly tabindex="-1" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group class="!mb-0">
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-widest text-[10px] font-bold text-zinc-400">
                                    Корр. счет
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="correspondent_account"
                                    id="bank-corr" value="{{ old('correspondent_account') }}"
                                    class="!py-2.5 !px-4 !border-zinc-200 font-mono text-[14px] text-zinc-900 bg-zinc-50"
                                    placeholder="Подтянется по БИК" readonly tabindex="-1" />
                            </x-shop::form.control-group>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-50 mt-6 py-4 flex items-center justify-end gap-3">
                    <a href="{{ route('shop.customers.account.organizations.index') }}"
                        class="px-5 py-2.5 text-[13px] font-medium text-zinc-500 hover:text-zinc-800 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                        class="px-10 py-2.5 bg-[#7C45F5] hover:bg-[#6534d4] text-[14px] text-white font-bold transition-all active:scale-95 shadow-lg shadow-violet-200">
                        Сохранить организацию
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Formatting for numbers
                window.forceNumeric = function (e) {
                    // Allow modifier keys (Ctrl, Cmd, Alt, Shift)
                    if (e.ctrlKey || e.metaKey || e.altKey) return;

                    // Allow functional keys
                    const functionalKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Escape'];
                    if (functionalKeys.includes(e.key)) return;

                    // Block anything that isn't a digit
                    if (!/[\d]/.test(e.key)) {
                        e.preventDefault();
                    }
                };

                // Use event delegation for keydown (better than keypress for control keys)
                document.addEventListener('keydown', function (e) {
                    if (e.target && (e.target.id === 'org-inn' || e.target.id === 'org-kpp' || e.target.id === 'bank-account')) {
                        window.forceNumeric(e);
                    }
                });

                // Automatic sanitization for numeric fields (handles right-click paste)
                document.addEventListener('input', function (e) {
                    if (e.target && (e.target.id === 'org-inn' || e.target.id === 'org-kpp' || e.target.id === 'bank-account' || e.target.id === 'org-ogrn')) {
                        e.target.value = e.target.value.replace(/\D/g, '');
                    }
                });

                // Validation logic for creating organization
                document.body.addEventListener('submit', function (e) {
                    if (e.target && e.target.id === 'org-form') {
                        const bicInput = document.getElementById('bank-bic');
                        const accInput = document.getElementById('bank-account');
                        const bic = bicInput ? bicInput.value.replace(/\D/g, '') : '';
                        const account = accInput ? accInput.value.replace(/\D/g, '') : '';

                        if (bic || account) {
                            if (!window.isValidBankAccount(bic, account)) {
                                alert('Расчетный счет не соответствует БИК банка (неверный контрольный ключ)');
                                e.preventDefault();
                                return false;
                            }
                        }
                    }
                });

                // DaData Organization Autocomplete
                let orgTimeout = null;

                function handleOrgInput(e) {
                    // Prevent autocomplete from triggering on programmatic 'input' events (after selection)
                    if (e && !e.isTrusted) return;

                    clearTimeout(orgTimeout);
                    const query = e.target.value;
                    const orgSuggestionsBox = document.getElementById('org-suggestions');

                    if (query.length < 3) {
                        if (orgSuggestionsBox) orgSuggestionsBox.classList.add('hidden');
                        return;
                    }

                    orgTimeout = setTimeout(async () => {
                        try {
                            const response = await fetch(`{{ route('shop.customers.account.organizations.suggest') }}?query=${encodeURIComponent(query)}`);
                            const data = await response.json();

                            if (orgSuggestionsBox) {
                                orgSuggestionsBox.innerHTML = '';

                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-2.5 hover:bg-emerald-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        const itemName = item.name || '';
                                        const inn = item.inn || '';
                                        const kpp = item.kpp ? ` КПП: ${item.kpp}` : '';
                                        const address = item.address || '';
                                        const ogrn = item.ogrn || '';

                                        div.innerHTML = `
                                                                                <div class="font-bold text-zinc-900 text-[13px]">${itemName}</div>
                                                                                <div class="text-[11px] text-zinc-500 font-mono mt-1">ИНН: ${inn}${kpp}</div>
                                                                                <div class="text-[11px] text-zinc-400 mt-1 truncate">${address}</div>
                                                                            `;

                                        div.onclick = () => {
                                            const nameInput = document.getElementById('org-name');
                                            const innInput = document.getElementById('org-inn');
                                            const kppInput = document.getElementById('org-kpp');
                                            const addressInput = document.getElementById('org-address');
                                            const ogrnInput = document.getElementById('org-ogrn');

                                            if (nameInput) nameInput.value = itemName;
                                            if (innInput) innInput.value = inn;
                                            if (kppInput && item.kpp) kppInput.value = item.kpp;
                                            if (addressInput && address) addressInput.value = address;
                                            if (ogrnInput && ogrn) ogrnInput.value = ogrn;

                                            // Manually trigger input events for Vue v-model to update
                                            [nameInput, innInput, kppInput, addressInput, ogrnInput].forEach(input => {
                                                if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
                                            });

                                            orgSuggestionsBox.classList.add('hidden');
                                            orgSuggestionsBox.innerHTML = '';
                                        };

                                        orgSuggestionsBox.appendChild(div);
                                    });
                                    // Match width and position to active input
                                    const parentGroup = e.target.closest('.relative');
                                    if (parentGroup && !parentGroup.contains(orgSuggestionsBox)) {
                                        parentGroup.appendChild(orgSuggestionsBox);
                                    }
                                    orgSuggestionsBox.classList.remove('hidden');
                                } else {
                                    orgSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[12px]">Ничего не найдено</div>';
                                    orgSuggestionsBox.classList.remove('hidden');
                                }
                            }
                        } catch (err) {
                            console.error('Error fetching org suggestions', err);
                        }
                    }, 500);
                }

                // DaData Bank Autocomplete
                let bankTimeout = null;

                function handleBankInput(e) {
                    // Prevent autocomplete from triggering on programmatic 'input' events (after selection)
                    if (e && !e.isTrusted) return;

                    clearTimeout(bankTimeout);
                    const query = e.target.value;
                    const bankSuggestionsBox = document.getElementById('bank-suggestions');

                    if (query.length < 3) {
                        if (bankSuggestionsBox) bankSuggestionsBox.classList.add('hidden');
                        return;
                    }

                    bankTimeout = setTimeout(async () => {
                        try {
                            const response = await fetch(`{{ route('shop.customers.account.organizations.suggest_bank') }}?query=${encodeURIComponent(query)}`);
                            const data = await response.json();

                            if (bankSuggestionsBox) {
                                bankSuggestionsBox.innerHTML = '';

                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-2.5 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        div.innerHTML = `
                                                                                <div class="font-bold text-zinc-900 text-[13px]">${item.bank_name || item.name}</div>
                                                                                <div class="text-[11px] text-zinc-500 font-mono mt-1">БИК: ${item.bic} | Корр: ${item.correspondent_account}</div>
                                                                            `;

                                        div.onclick = () => {
                                            const bicInput = document.getElementById('bank-bic');
                                            const nameInput = document.getElementById('bank-name');
                                            const corrInput = document.getElementById('bank-corr');

                                            if (bicInput) bicInput.value = item.bic;
                                            if (nameInput) nameInput.value = item.bank_name || item.name;
                                            if (corrInput) corrInput.value = item.correspondent_account;

                                            [bicInput, nameInput, corrInput].forEach(input => {
                                                if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
                                            });

                                            bankSuggestionsBox.classList.add('hidden');
                                            bankSuggestionsBox.innerHTML = '';

                                            // Focus on settlement account next
                                            const bankAccountInput = document.getElementById('bank-account');
                                            if (bankAccountInput) {
                                                bankAccountInput.focus();
                                            }
                                        };

                                        bankSuggestionsBox.appendChild(div);
                                    });
                                    // Match width and position to active input
                                    const parentGroup = e.target.closest('.relative') || e.target.parentNode;
                                    if (parentGroup) {
                                        parentGroup.appendChild(bankSuggestionsBox);
                                    }
                                    bankSuggestionsBox.classList.remove('hidden');
                                } else {
                                    bankSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[12px]">Банк не найден</div>';
                                    bankSuggestionsBox.classList.remove('hidden');
                                }
                            }
                        } catch (err) {
                            console.error('Error fetching bank suggestions', err);
                        }
                    }, 500);
                }

                // Event delegation for input events (handling typing, pasting, and changing)
                const events = ['input', 'paste', 'change'];
                events.forEach(eventType => {
                    document.addEventListener(eventType, function (e) {
                        if (e.target) {
                            if (e.target.id === 'org-name' || e.target.id === 'org-inn') {
                                if (eventType === 'paste') {
                                    setTimeout(() => handleOrgInput(e), 0);
                                } else {
                                    handleOrgInput(e);
                                }
                            } else if (e.target.id === 'bank-bic') {
                                if (eventType === 'paste') {
                                    setTimeout(() => handleBankInput(e), 0);
                                } else {
                                    handleBankInput(e);
                                }
                            }
                        }
                    });
                });

                // Hide suggestions on outside click
                document.addEventListener('click', function (e) {
                    const orgSuggestionsBox = document.getElementById('org-suggestions');
                    const bicInput = document.getElementById('bank-bic');

                    if (orgSuggestionsBox && !orgSuggestionsBox.contains(e.target) &&
                        (!orgNameInput || !orgNameInput.contains(e.target)) &&
                        (!orgInnInput || !orgInnInput.contains(e.target))) {
                        orgSuggestionsBox.classList.add('hidden');
                    }

                    const bankSuggestionsBox = document.getElementById('bank-suggestions');
                    if (bankSuggestionsBox && !bankSuggestionsBox.contains(e.target) &&
                        (!bicInput || !bicInput.contains(e.target))) {
                        bankSuggestionsBox.classList.add('hidden');
                    }
                });
            });

            // Re-usable bank account validation function
            window.isValidBankAccount = function (bic, account) {
                bic = (bic || '').toString().replace(/\D/g, '');
                account = (account || '').toString().replace(/\D/g, '');

                if (bic.length !== 9 || account.length !== 20) return false;

                let bicPart;
                if (bic[6] === '0' && bic[7] === '0') {
                    bicPart = '0' + bic[4] + bic[5];
                } else {
                    bicPart = bic.substring(6, 9);
                }

                const combined = bicPart + account;
                const weights = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];

                let sum = 0;
                for (let i = 0; i < 23; i++) {
                    const digit = parseInt(combined[i]);
                    sum += (digit * weights[i]) % 10;
                }

                return sum % 10 === 0;
            };
        </script>
    @endpush
</x-shop::layouts.account>