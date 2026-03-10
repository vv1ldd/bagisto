<x-shop::layouts.account :show-back="true">
    <div class="flex-auto pb-8 ios-tile-relative ios-group max-w-[800px] mx-auto">
        <a href="{{ route('shop.customers.account.organizations.index') }}" class="ios-close-button">
            <span class="icon-cancel text-xl"></span>
        </a>

        <div class="px-5 pt-7 pb-2 flex justify-between items-center">
            <h1 class="text-[22px] font-bold text-zinc-900 leading-tight">
                Добавление организации
            </h1>
        </div>

        <div class="p-5">
            <form action="{{ route('shop.customers.account.organizations.store') }}" method="POST" id="org-form">
                @csrf

                <!-- Basic Organization Details -->
                <div class="bg-white border text-left border-zinc-100 shadow-sm p-6 mb-6">
                    <h3 class="text-[15px] font-bold text-zinc-900 mb-6 flex items-center gap-2">
                        <span class="text-emerald-500 text-lg">🏢</span>
                        Реквизиты юридического лица
                    </h3>

                    <div class="space-y-4">
                        <div class="relative">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="required uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    Название организации / ИП
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="name" id="org-name"
                                    value="{{ old('name') }}"
                                    class="!py-3 !px-4 !border-zinc-200 focus:!border-emerald-500 focus:!ring-2 focus:!ring-emerald-500/20 transition-all font-medium text-zinc-900"
                                    placeholder="Введите название или ИНН для поиска" autocomplete="off" />

                                <x-shop::form.control-group.error control-name="name" />
                            </x-shop::form.control-group>

                            <!-- DaData Organization Suggestions Dropdown -->
                            <div id="org-suggestions"
                                class="absolute z-50 w-full mt-1 bg-white border border-zinc-200 shadow-lg rounded-md hidden max-h-60 overflow-y-auto">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="required uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    ИНН
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="inn" id="org-inn"
                                    value="{{ old('inn') }}"
                                    class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-emerald-500 transition-all"
                                    placeholder="ИНН" />

                                <x-shop::form.control-group.error control-name="inn" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    КПП (если применимо)
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="kpp" id="org-kpp"
                                    value="{{ old('kpp') }}"
                                    class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-emerald-500 transition-all"
                                    placeholder="КПП" />

                                <x-shop::form.control-group.error control-name="kpp" />
                            </x-shop::form.control-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    ОГРН / ОГРНИП
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="ogrn" id="org-ogrn"
                                    value="{{ old('ogrn') }}"
                                    class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-emerald-500 transition-all"
                                    placeholder="ОГРН" />

                                <x-shop::form.control-group.error control-name="ogrn" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    Юридический адрес
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="address" id="org-address"
                                    value="{{ old('address') }}"
                                    class="!py-3 !px-4 !border-zinc-200 text-zinc-900 focus:!border-emerald-500 transition-all"
                                    placeholder="Юридический адрес" />

                                <x-shop::form.control-group.error control-name="address" />
                            </x-shop::form.control-group>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="bg-white border text-left border-zinc-100 shadow-sm p-6 mb-8">
                    <h3 class="text-[15px] font-bold text-zinc-900 mb-6 flex items-center gap-2">
                        <span class="text-blue-500 text-lg">🏦</span>
                        Основной банковский счет
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label
                                        class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                        БИК Банка
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control type="text" name="bic" id="bank-bic"
                                        value="{{ old('bic') }}"
                                        class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-blue-500 transition-all"
                                        placeholder="Введите БИК для поиска" autocomplete="off" />

                                    <x-shop::form.control-group.error control-name="bic" />
                                </x-shop::form.control-group>

                                <!-- DaData Bank Suggestions Dropdown -->
                                <div id="bank-suggestions"
                                    class="absolute z-50 w-full mt-1 bg-white border border-zinc-200 shadow-lg rounded-md hidden max-h-60 overflow-y-auto">
                                    <!-- Suggestions will be populated here -->
                                </div>
                            </div>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    Расчетный счет
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="settlement_account"
                                    id="bank-account" value="{{ old('settlement_account') }}"
                                    class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-blue-500 transition-all"
                                    placeholder="20 цифр расчетного счета" />

                                <x-shop::form.control-group.error control-name="settlement_account" />
                            </x-shop::form.control-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    Название Банка
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="bank_name" id="bank-name"
                                    value="{{ old('bank_name') }}"
                                    class="!py-3 !px-4 !border-zinc-200 text-zinc-900 focus:!border-blue-500 transition-all bg-zinc-50"
                                    placeholder="Название банка" readonly />

                                <x-shop::form.control-group.error control-name="bank_name" />
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label
                                    class="uppercase tracking-wider text-[11px] font-bold text-zinc-500">
                                    Корреспондентский счет
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control type="text" name="correspondent_account"
                                    id="bank-corr" value="{{ old('correspondent_account') }}"
                                    class="!py-3 !px-4 !border-zinc-200 font-mono text-zinc-900 focus:!border-blue-500 transition-all bg-zinc-50"
                                    placeholder="Корр. счет" readonly />

                                <x-shop::form.control-group.error control-name="correspondent_account" />
                            </x-shop::form.control-group>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <a href="{{ route('shop.customers.account.organizations.index') }}"
                        class="px-6 py-3 border border-zinc-200 text-zinc-600 font-medium hover:bg-zinc-50 transition-colors">
                        Отмена
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold shadow-md hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
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
                    if (!/[\d]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Tab') {
                        e.preventDefault();
                    }
                };

                const innInput = document.getElementById('org-inn');
                const kppInput = document.getElementById('org-kpp');
                const bicInput = document.getElementById('bank-bic');
                const accInput = document.getElementById('bank-account');

                if (innInput) innInput.addEventListener('keypress', window.forceNumeric);
                if (kppInput) kppInput.addEventListener('keypress', window.forceNumeric);
                if (bicInput) bicInput.addEventListener('keypress', window.forceNumeric);
                if (accInput) accInput.addEventListener('keypress', window.forceNumeric);

                // Validation logic for creating organization
                const orgForm = document.getElementById('org-form');
                if (orgForm) {
                    orgForm.addEventListener('submit', function (e) {
                        const bic = document.getElementById('bank-bic').value.replace(/\D/g, '');
                        const account = document.getElementById('bank-account').value.replace(/\D/g, '');
                        const inn = document.getElementById('org-inn').value.replace(/\D/g, '');

                        if (inn.length !== 10 && inn.length !== 12) {
                            alert('ИНН должен состоять из 10 или 12 цифр');
                            e.preventDefault();
                            return false;
                        }

                        if (bic || account) {
                            if (!window.isValidBankAccount(bic, account)) {
                                alert('Расчетный счет не соответствует БИК банка (неверный контрольный ключ)');
                                e.preventDefault();
                                return false;
                            }
                        }
                    });
                }

                // DaData Organization Autocomplete
                let orgTimeout = null;
                const orgNameInput = document.getElementById('org-name');
                const orgSuggestionsBox = document.getElementById('org-suggestions');

                if (orgNameInput) {
                    orgNameInput.addEventListener('input', function () {
                        clearTimeout(orgTimeout);
                        const query = this.value;

                        if (query.length < 3) {
                            orgSuggestionsBox.classList.add('hidden');
                            return;
                        }

                        orgTimeout = setTimeout(async () => {
                            try {
                                const response = await fetch(`{{ route('shop.customers.account.organizations.suggest') }}?query=${encodeURIComponent(query)}`);
                                const data = await response.json();

                                orgSuggestionsBox.innerHTML = '';

                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-3 hover:bg-emerald-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        const itemName = item.data.name?.short_with_opf || item.value;
                                        const inn = item.data.inn;
                                        const kpp = item.data.kpp ? ` КПП: ${item.data.kpp}` : '';
                                        const address = item.data.address?.value || '';
                                        const ogrn = item.data.ogrn || '';

                                        div.innerHTML = `
                                                <div class="font-bold text-zinc-900 text-[14px]">${itemName}</div>
                                                <div class="text-[11px] text-zinc-500 font-mono mt-1">ИНН: ${inn}${kpp}</div>
                                                <div class="text-[12px] text-zinc-400 mt-1 truncate">${address}</div>
                                            `;

                                        div.onclick = () => {
                                            document.getElementById('org-name').value = itemName;
                                            document.getElementById('org-inn').value = inn;
                                            if (item.data.kpp) document.getElementById('org-kpp').value = item.data.kpp;
                                            if (item.data.address) document.getElementById('org-address').value = address;
                                            if (ogrn) document.getElementById('org-ogrn').value = ogrn;

                                            orgSuggestionsBox.classList.add('hidden');
                                        };

                                        orgSuggestionsBox.appendChild(div);
                                    });
                                    orgSuggestionsBox.classList.remove('hidden');
                                } else {
                                    orgSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[13px]">Ничего не найдено</div>';
                                    orgSuggestionsBox.classList.remove('hidden');
                                }
                            } catch (e) {
                                console.error('Error fetching org suggestions', e);
                            }
                        }, 500);
                    });

                    // Hide suggestions on outside click
                    document.addEventListener('click', function (e) {
                        if (orgNameInput && orgSuggestionsBox && !orgNameInput.contains(e.target) && !orgSuggestionsBox.contains(e.target)) {
                            orgSuggestionsBox.classList.add('hidden');
                        }
                    });
                }

                // DaData Bank Autocomplete
                let bankTimeout = null;
                const bankSuggestionsBox = document.getElementById('bank-suggestions');

                if (bicInput) {
                    bicInput.addEventListener('input', function () {
                        clearTimeout(bankTimeout);
                        const query = this.value.replace(/\D/g, '');

                        if (query.length < 3) {
                            bankSuggestionsBox.classList.add('hidden');
                            return;
                        }

                        bankTimeout = setTimeout(async () => {
                            try {
                                const response = await fetch(`{{ route('shop.customers.account.organizations.suggest_bank') }}?query=${encodeURIComponent(query)}`);
                                const data = await response.json();

                                bankSuggestionsBox.innerHTML = '';

                                if (data && data.length > 0) {
                                    data.forEach(item => {
                                        const div = document.createElement('div');
                                        div.className = 'p-3 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';

                                        div.innerHTML = `
                                                <div class="font-bold text-zinc-900 text-[14px]">${item.bank_name || item.name}</div>
                                                <div class="text-[11px] text-zinc-500 font-mono mt-1">БИК: ${item.bic} | Корр: ${item.correspondent_account}</div>
                                            `;

                                        div.onclick = () => {
                                            document.getElementById('bank-bic').value = item.bic;
                                            document.getElementById('bank-name').value = item.bank_name || item.name;
                                            document.getElementById('bank-corr').value = item.correspondent_account;

                                            bankSuggestionsBox.classList.add('hidden');

                                            // Focus on settlement account next
                                            if (document.getElementById('bank-account')) {
                                                document.getElementById('bank-account').focus();
                                            }
                                        };

                                        bankSuggestionsBox.appendChild(div);
                                    });
                                    bankSuggestionsBox.classList.remove('hidden');
                                } else {
                                    bankSuggestionsBox.innerHTML = '<div class="p-3 text-zinc-500 text-[13px]">Банк не найден</div>';
                                    bankSuggestionsBox.classList.remove('hidden');
                                }
                            } catch (e) {
                                console.error('Error fetching bank suggestions', e);
                            }
                        }, 500);
                    });

                    // Hide suggestions on outside click
                    document.addEventListener('click', function (e) {
                        if (bicInput && bankSuggestionsBox && !bicInput.contains(e.target) && !bankSuggestionsBox.contains(e.target)) {
                            bankSuggestionsBox.classList.add('hidden');
                        }
                    });
                }
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