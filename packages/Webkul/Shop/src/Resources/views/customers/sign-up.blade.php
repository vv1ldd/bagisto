@push('meta')
<meta name="description" content="@lang('shop::app.customers.signup-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.signup-form.page-title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

    <div id="registration-wizard" class="flex flex-col items-center">
        <!-- Back Button -->
        <div class="flex flex-col items-center mb-8">
            <a href="{{ route('shop.customer.session.index') }}" 
                class="group flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-[#7C45F5] transition-colors">
                <span class="icon-arrow-left text-base transition-transform group-hover:-translate-x-1"></span>
                @lang('shop::app.customers.login-form.back-to-login-options')
            </a>
        </div>

        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-zinc-900 mb-2">Создать аккаунт</h1>
            <p class="text-sm text-zinc-500 max-w-[320px] mx-auto leading-relaxed">
                Используйте Passkey для мгновенного и безопасного входа без пароля.
            </p>
        </div>




        <!-- Nickname Input -->
        <div class="w-full max-w-[340px] mx-auto mb-6">
            <div class="bg-white border border-gray-100 shadow-sm overflow-hidden rounded-md">
                <div class="flex items-center justify-between py-3 px-4 min-h-[52px] relative focus-within:bg-gray-50 transition-colors">
                    <label for="nickname-input" class="text-[15px] font-medium text-zinc-900 whitespace-nowrap flex-shrink-0">Псевдоним</label>
                    <div class="flex-grow ml-4 flex justify-end items-center relative">
                        <span class="text-zinc-400 mr-0.5 text-[15px] select-none flex-shrink-0 font-medium">@</span>
                        <input type="text" id="nickname-input" required
                            class="w-full text-right outline-none bg-transparent text-zinc-500 font-medium focus:text-[#7C45F5] focus:placeholder-transparent pb-0.5 text-[15px]"
                            placeholder="nickname" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            pattern="^[a-zA-Z0-9_\-\.]{3,30}$">
                        <!-- Status Icon -->
                        <div id="nickname-icon" class="ml-2 w-4 h-4 flex-shrink-0 flex items-center justify-center opacity-0 transition-opacity text-sm"></div>
                    </div>
                </div>
            </div>
            <p id="nickname-status" class="text-xs mt-2 text-center font-medium min-h-[16px] transition-opacity duration-200 opacity-0"></p>
        </div>

        <!-- Primary Action Button -->
        <button type="button" id="start-registration-btn" onclick="handlePasskeyRegistration(event)" disabled
            class="flex w-full items-center justify-center gap-3 !rounded-none bg-[#7C45F5] px-8 py-5 text-center font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-xl shadow-[#7C45F5]/30 uppercase tracking-[0.2em] text-sm active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5] disabled:active:scale-100 disabled:shadow-none">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <circle cx="12" cy="11" r="3"></circle>
                <path d="M12 14v1"></path>
            </svg>
            Создать аккаунт
        </button>

        <p class="mt-8 text-center text-[11px] text-zinc-400 max-w-[280px] leading-relaxed italic">
            Нажимая «Создать аккаунт», вы подтверждаете согласие с условиями использования сервиса.
        </p>
    </div>

    {!! view_render_event('bagisto.shop.customers.signup.after') !!}

    @push('scripts')
        <script>
            let checkTimeout;
            let isUsernameValid = false;
            let isChecking = false;

            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'nickname-input') {
                    handleNicknameInput(e.target);
                }
            });

            function handleNicknameInput(inputEl) {
                clearTimeout(checkTimeout);
                
                const nicknameIcon = document.getElementById('nickname-icon');
                const nicknameStatus = document.getElementById('nickname-status');
                
                // reset visual
                nicknameIcon.className = 'ml-2 w-4 h-4 flex-shrink-0 flex items-center justify-center opacity-0 transition-opacity text-sm';
                nicknameIcon.innerHTML = '';
                nicknameStatus.className = 'text-xs mt-2 text-center font-medium min-h-[16px] transition-opacity duration-200 opacity-0';
                
                isUsernameValid = false;
                updateButtonState();

                const val = inputEl.value.trim();
                if (!val) return;

                if (!/^[a-zA-Z0-9_\-\.]{3,30}$/.test(val)) {
                    showStatus('❌', 'Некорректный формат (разрешены буквы, цифры, минус, точка, подчеркивание, длина 3-30)', 'error');
                    return;
                }

                // Show loading spinner
                showStatus('<div class="w-3.5 h-3.5 border-2 border-[#7C45F5] border-r-transparent rounded-full animate-spin"></div>', '', 'checking');
                isChecking = true;
                updateButtonState();

                checkTimeout = setTimeout(async () => {
                    try {
                        const formData = new FormData();
                        formData.append('username', val);
                        formData.append('_token', '{{ csrf_token() }}');

                        const res = await fetch('{{ route('shop.customers.register.check_username') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();
                        
                        isChecking = false;
                        
                        if (data.available) {
                            showStatus('✅', data.message, 'success');
                            isUsernameValid = true;
                        } else {
                            showStatus('❌', data.message, 'error');
                            isUsernameValid = false;
                        }
                    } catch (e) {
                        isChecking = false;
                        showStatus('❌', 'Ошибка проверки', 'error');
                    }
                    updateButtonState();
                }, 500); // 500ms debounce
            }

            function showStatus(iconHTML, text, type) {
                const nicknameIcon = document.getElementById('nickname-icon');
                const nicknameStatus = document.getElementById('nickname-status');

                if (nicknameIcon) {
                    nicknameIcon.innerHTML = iconHTML;
                    nicknameIcon.className = 'ml-2 w-4 h-4 flex-shrink-0 flex items-center justify-center opacity-100 transition-opacity text-sm';
                }
                
                if (nicknameStatus) {
                    if (text) {
                        nicknameStatus.textContent = text;
                        nicknameStatus.className = `text-xs mt-2 text-center font-medium min-h-[16px] transition-opacity duration-200 opacity-100 ${type === 'success' ? 'text-emerald-500' : 'text-red-500'}`;
                    } else {
                        nicknameStatus.className = `text-xs mt-2 text-center font-medium min-h-[16px] transition-opacity duration-200 opacity-0`;
                    }
                }
            }

            function updateButtonState() {
                const registerBtn = document.getElementById('start-registration-btn');
                if (registerBtn) {
                    registerBtn.disabled = !isUsernameValid || isChecking;
                }
            }

            /**
             * Main execution logic
             */
            async function handlePasskeyRegistration(e) {
                e.preventDefault();
                
                if (!isUsernameValid) return;

                const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                const btn = document.getElementById('start-registration-btn');
                const originalText = btn.innerHTML;
                
                const nickname = document.getElementById('nickname-input').value.trim();

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey. Пожалуйста, используйте современный браузер.');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<span class="animate-pulse">Подготовка...</span>';

                try {
                    // Step 1: Create placeholder customer and get options
                    console.log('[Passkey] Preparing registration...');
                    const prepareResponse = await fetch('{{ route('shop.customers.register.passkey.prepare') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username: nickname })
                    });

                    if (!prepareResponse.ok) {
                        const errorData = await prepareResponse.json();
                        let errorMessage = 'Ошибка инициализации регистрации.';
                        if (errorData.errors && errorData.errors.username) {
                            errorMessage = errorData.errors.username[0];
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                        showStatus('❌', errorMessage, 'error');
                        throw new Error(errorMessage);
                    }

                    const rawOptions = await prepareResponse.json();
                    const optionsJSON = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                    // Robust base64url conversion for Safari
                    const toBase64Url = (str) => {
                        if (!str || typeof str !== 'string') return str;
                        return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    };

                    if (optionsJSON.challenge) optionsJSON.challenge = toBase64Url(optionsJSON.challenge);
                    if (optionsJSON.user && optionsJSON.user.id) optionsJSON.user.id = toBase64Url(optionsJSON.user.id);
                    if (optionsJSON.excludeCredentials) {
                        optionsJSON.excludeCredentials.forEach(cred => {
                            if (cred.id) cred.id = toBase64Url(cred.id);
                        });
                    }

                    console.log('[Passkey] RP ID:', optionsJSON.rp ? optionsJSON.rp.id : 'N/A');
                    console.log('[Passkey] User:', JSON.stringify(optionsJSON.user));
                    console.log('[Passkey] Raw options from server:', JSON.stringify(optionsJSON));

                    btn.innerHTML = '<span class="animate-pulse">Создайте ключ...</span>';

                    // Pass server options directly (v8+ format)
                    const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);
                    console.log('[Passkey] Credential created:', attResp);
                    
                    btn.innerHTML = '<span class="animate-pulse">Сохранение...</span>';

                    // Step 4: Send credential back to server to finalize
                    const storeResponse = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(attResp)
                    });

                    if (!storeResponse.ok) {
                        const storeError = await storeResponse.json();
                        throw new Error(storeError.message || 'Ошибка сохранения ключа доступа.');
                    }

                    console.log('[Passkey] Registration success! Redirecting...');
                    btn.innerHTML = 'Готово!';
                    
                    window.location.href = '{{ route('shop.customers.account.onboarding.security') }}';

                } catch (err) {
                    console.error('[Passkey] Error:', err);
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                        const rpId = (typeof optionsJSON !== 'undefined' && optionsJSON.rp) ? optionsJSON.rp.id : 'N/A';
                        alert('Ошибка: ' + err.message + '\n\nRP ID: ' + rpId);
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }
        </script>
    @endpush
</x-shop::layouts.split-screen>