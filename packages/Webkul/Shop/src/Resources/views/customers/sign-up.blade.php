@push('meta')
<meta name="description" content="@lang('shop::app.customers.signup-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.signup-form.page-title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

    <div id="registration-wizard" class="flex flex-col items-center w-full max-w-[440px] mx-auto py-6 animate-in fade-in slide-in-from-bottom-5 duration-700">
        <!-- Back Button (Brutalist) -->
        <div class="w-full mb-10">
            <a href="{{ route('shop.customer.session.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-50 border-2 border-zinc-900 text-[10px] font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-900 hover:text-white transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:shadow-none active:translate-x-1 active:translate-y-1 group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path d="M19 12H5m7-7l-7 7 7 7"/>
                </svg>
                Назад
            </a>
        </div>

        <div class="mb-12 text-left w-full">
            <h1 class="text-4xl font-black text-zinc-900 mb-4 uppercase tracking-tighter leading-none">Создать<br>Аккаунт</h1>
            <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider leading-relaxed">
                Безопасность <span class="text-[#7C45F5]">нового уровня</span> с технологией Passkey.
            </p>
        </div>

        <!-- Nickname Input (Premium Brutalist) -->
        <div class="w-full mb-10 group">
            <div class="relative">
                <!-- Decorative background -->
                <div class="absolute inset-0 bg-zinc-100 border-2 border-zinc-900 translate-x-1 translate-y-1"></div>
                
                <div class="relative bg-white border-2 border-zinc-900 group-focus-within:border-[#7C45F5] transition-colors overflow-hidden">
                    <div class="flex items-center justify-between py-4 px-6 min-h-[64px]">
                        <label for="nickname-input" class="text-xs font-black text-zinc-400 uppercase tracking-widest flex-shrink-0 group-focus-within:text-[#7C45F5] transition-colors">Никнейм</label>
                        <div class="flex-grow ml-4 flex justify-end items-center relative">
                            <span class="text-zinc-900 mr-1 text-xl select-none flex-shrink-0 font-black">@</span>
                            <input type="text" id="nickname-input" required
                                class="w-full text-right outline-none bg-transparent text-zinc-900 font-black focus:placeholder-transparent pb-0.5 text-xl tracking-tight placeholder:text-zinc-200"
                                placeholder="твой_ник" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                pattern="^[a-zA-Z0-9_\-\.]{3,30}$">
                            <!-- Status Icon -->
                            <div id="nickname-icon" class="ml-3 w-5 h-5 flex-shrink-0 flex items-center justify-center opacity-0 transition-opacity text-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
            <p id="nickname-status" class="text-[10px] mt-4 text-left font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-0 px-2"></p>
        </div>

        <!-- Primary Action Button (Brutalist style) -->
        <button type="button" id="start-registration-btn" onclick="handlePasskeyRegistration(event)" disabled
            class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-sm overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-x-0 disabled:hover:translate-y-0 disabled:hover:shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] mb-8">
            <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <circle cx="12" cy="11" r="3"></circle>
            </svg>
            Создать через Passkey
        </button>

        <p class="text-center text-[10px] text-zinc-400 font-bold uppercase tracking-[0.15em] leading-relaxed max-w-[320px]">
            Нажимая кнопку, вы подтверждаете согласие с <a href="#" class="text-zinc-600 underline underline-offset-2 decoration-2 decoration-[#7C45F5]/30 hover:text-[#7C45F5]">правилами сервиса</a>.
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
                    nicknameIcon.className = 'ml-3 w-5 h-5 flex-shrink-0 flex items-center justify-center opacity-100 transition-opacity text-sm';
                }
                
                if (nicknameStatus) {
                    if (text) {
                        nicknameStatus.textContent = text;
                        nicknameStatus.className = `text-[10px] mt-4 text-left font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-100 px-2 ${type === 'success' ? 'text-emerald-500' : 'text-[#FF4D6D]'}`;
                    } else {
                        nicknameStatus.className = `text-[10px] mt-4 text-left font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-0 px-2`;
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