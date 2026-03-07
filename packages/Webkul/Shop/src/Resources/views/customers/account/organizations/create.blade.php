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
                    <x-shop::form.control-group class="!mb-4" id="step-1-search">
                        <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.create.inn')
                        </x-shop::form.control-group.label>

                        <div class="flex gap-2">
                            <x-shop::form.control-group.control type="text" name="inn" rules="required" :value="old('inn')"
                                id="inn-input"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all flex-1"
                                :label="trans('shop::app.customers.account.organizations.create.inn')"
                                placeholder="Введите ИНН..." />
                            
                            <button type="button" id="lookup-inn-btn"
                                class="px-6 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold transition-all disabled:opacity-50 text-[14px]">
                                Найти
                            </button>
                        </div>
                        <x-shop::form.control-group.error control-name="inn" />
                    </x-shop::form.control-group>

                    <!-- Extracted Organization Details (Readonly Constants) -->
                    <div id="step-1-details" class="hidden space-y-4 bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative">
                        <!-- Loading Overlay -->
                        <div id="step-1-overlay" class="hidden absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.name')
                                </label>
                                <input type="text" name="name" id="name-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-semibold text-zinc-900 focus:ring-0 cursor-default" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                    @lang('shop::app.customers.account.organizations.create.kpp')
                                </label>
                                <input type="text" name="kpp" id="kpp-input" readonly 
                                    class="w-full bg-transparent border-0 p-0 text-[15px] font-mono text-zinc-700 focus:ring-0 cursor-default" />
                            </div>
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
                    <div class="flex items-center justify-between mb-4" id="step-2-search">
                        <h2 class="text-[16px] font-bold text-zinc-900">Шаг 2: Банк организации</h2>
                        <span id="step-2-badge" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Заполнено</span>
                    </div>

                    <!-- BIC Search -->
                    <x-shop::form.control-group class="!mb-4" id="step-2-search">
                        <x-shop::form.control-group.label class="required !text-[13px] !font-semibold !text-zinc-500 !mb-1.5 uppercase tracking-wider">
                            @lang('shop::app.customers.account.organizations.create.bic')
                        </x-shop::form.control-group.label>

                        <div class="flex gap-2">
                            <x-shop::form.control-group.control type="text" name="bic" :value="old('bic')" id="bic-input"
                                class="!py-3 !px-4 !border-zinc-200 focus:!border-[#7C45F5] focus:!ring-0 transition-all flex-1"
                                :label="trans('shop::app.customers.account.organizations.create.bic')"
                                placeholder="Введите БИК банка..." />
                                
                            <button type="button" id="lookup-bic-btn"
                                class="px-6 bg-[#7C45F5] hover:bg-[#6534d4] text-white font-bold transition-all disabled:opacity-50 text-[14px]">
                                Найти
                            </button>
                        </div>
                        <x-shop::form.control-group.error control-name="bic" />
                    </x-shop::form.control-group>

                    <!-- Extracted Bank Details (Readonly Constants) -->
                    <div id="step-2-details" class="hidden space-y-4 bg-zinc-50/50 rounded-lg p-5 border border-zinc-100 relative">
                        <!-- Loading Overlay -->
                        <div id="step-2-overlay" class="hidden absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10"></div>

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
        setTimeout(() => {
            console.log('Wizard Initialization');

            // Steps Elements
            const s1 = document.getElementById('step-1');
            const s2 = document.getElementById('step-2');
            const s3 = document.getElementById('step-3');
            
            const submitBtn = document.getElementById('submit-btn');
            
            // Step 1: INN
            const innInput = document.getElementById('inn-input');
            const lookupInnBtn = document.getElementById('lookup-inn-btn');
            const step1Details = document.getElementById('step-1-details');
            const nameInput = document.getElementById('name-input');
            const kppInput = document.getElementById('kpp-input');
            const addressInput = document.getElementById('address-input');
            const confirmStep1Btn = document.getElementById('confirm-step-1-btn');
            const step1Overlay = document.getElementById('step-1-overlay');
            const step1Badge = document.getElementById('step-1-badge');
            
            // Step 2: BIC
            const bicInput = document.getElementById('bic-input');
            const lookupBicBtn = document.getElementById('lookup-bic-btn');
            const step2Details = document.getElementById('step-2-details');
            const bankNameInput = document.getElementById('bank-name-input');
            const corrAccountInput = document.getElementById('corr-account-input');
            const confirmStep2Btn = document.getElementById('confirm-step-2-btn');
            const step2Overlay = document.getElementById('step-2-overlay');
            const step2Badge = document.getElementById('step-2-badge');

            // Step 3: Account
            const settlementInput = document.getElementById('settlement-account-input');

            // Recreate buttons to drop old listeners safely
            if (lookupInnBtn) {
                const newB = lookupInnBtn.cloneNode(true);
                lookupInnBtn.parentNode.replaceChild(newB, lookupInnBtn);
                newB.onclick = async () => {
                    const inn = innInput.value.trim();
                    if (!inn) return alert('Введите ИНН');
                    
                    newB.disabled = true;
                    newB.innerText = '...';
                    
                    try {
                        const response = await fetch(`{{ route('shop.customers.account.organizations.lookup_inn', ':inn') }}`.replace(':inn', inn));
                        const data = await response.json();
                        
                        if (response.ok) {
                            nameInput.value = data.name || '';
                            kppInput.value = data.kpp || '';
                            addressInput.value = data.address || '';
                            step1Details.classList.remove('hidden');
                            
                            // Visual cue
                            [nameInput, kppInput, addressInput].forEach(el => {
                                el.style.backgroundColor = '#f0fff4';
                                setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                            });
                        } else {
                            alert(data.message || 'Организация не найдена');
                        }
                    } catch (e) {
                         alert('Ошибка сети при поиске ИНН');
                    } finally {
                        newB.disabled = false;
                        newB.innerText = 'Найти';
                    }
                };
            }

            if (confirmStep1Btn) {
                confirmStep1Btn.onclick = () => {
                    step1Overlay.classList.remove('hidden'); // Freeze step 1
                    confirmStep1Btn.classList.add('hidden');
                    step1Badge.classList.remove('hidden');
                    
                    s2.classList.remove('hidden');
                    document.getElementById('step-2-search').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    bicInput.focus();
                };
            }

            if (lookupBicBtn) {
                const newB = lookupBicBtn.cloneNode(true);
                lookupBicBtn.parentNode.replaceChild(newB, lookupBicBtn);
                newB.onclick = async () => {
                    const bic = bicInput.value.trim();
                    if (!bic) return alert('Введите БИК');
                    
                    newB.disabled = true;
                    newB.innerText = '...';
                    
                    try {
                        const response = await fetch(`{{ route('shop.customers.account.organizations.lookup_bic', ':bic') }}`.replace(':bic', bic));
                        const data = await response.json();
                        
                        if (response.ok) {
                            bankNameInput.value = data.bank_name || '';
                            corrAccountInput.value = data.correspondent_account || '';
                            step2Details.classList.remove('hidden');
                            
                            // Visual cue
                            [bankNameInput, corrAccountInput].forEach(el => {
                                el.style.backgroundColor = '#f0fff4';
                                setTimeout(() => el.style.backgroundColor = 'transparent', 1000);
                            });
                        } else {
                            alert(data.message || 'Банк не найден');
                        }
                    } catch (e) {
                         alert('Ошибка сети при поиске БИК');
                    } finally {
                        newB.disabled = false;
                        newB.innerText = 'Найти';
                    }
                };
            }

            if (confirmStep2Btn) {
                confirmStep2Btn.onclick = () => {
                    step2Overlay.classList.remove('hidden');
                    confirmStep2Btn.classList.add('hidden');
                    step2Badge.classList.remove('hidden');
                    
                    s3.classList.remove('hidden');
                    s3.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    settlementInput.focus();
                };
            }

            if (settlementInput) {
                settlementInput.addEventListener('input', (e) => {
                    // Only allow numbers
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 20);
                    submitBtn.disabled = e.target.value.length !== 20;
                });
            }

            // If there are validation errors on reload (meaning user hit submit but failed), unlock everything
            if ("{{ $errors->any() }}" === "1") {
                step1Details.classList.remove('hidden');
                s2.classList.remove('opacity-50', 'pointer-events-none');
                step2Details.classList.remove('hidden');
                s3.classList.remove('opacity-50', 'pointer-events-none');
            }

        }, 300);
    </script>
</x-shop::layouts.account>