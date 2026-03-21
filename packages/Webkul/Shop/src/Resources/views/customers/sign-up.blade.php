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
                    </div>
                </div>
            </div>
            <p id="nickname-error" class="text-xs text-red-500 mt-2 hidden text-center font-medium"></p>
        </div>

        <!-- Primary Action Button -->
        <button type="button" id="start-registration-btn" onclick="handlePasskeyRegistration(event)"
            class="flex w-full items-center justify-center gap-3 !rounded-none bg-[#7C45F5] px-8 py-5 text-center font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-xl shadow-[#7C45F5]/30 uppercase tracking-[0.2em] text-sm active:scale-[0.98]">
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
            /**
             * Base64 to ArrayBuffer helper
             */
            function _b64ToUint8Array(base64) {
                if (!base64) return new Uint8Array(0);
                var padding = '='.repeat((4 - base64.length % 4) % 4);
                var b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                var rawData = window.atob(b64);
                var outputArray = new Uint8Array(rawData.length);
                for (var i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            /**
             * ArrayBuffer to Base64URL helper
             */
            function _bufToBase64URL(buffer) {
                var binary = '';
                var bytes = new Uint8Array(buffer);
                for (var i = 0; i < bytes.byteLength; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            }

            /**
             * Main execution logic
             */
            async function handlePasskeyRegistration(e) {
                e.preventDefault();
                
                const btn = document.getElementById('start-registration-btn');
                const originalText = btn.innerHTML;
                
                const nicknameInput = document.getElementById('nickname-input');
                const nicknameError = document.getElementById('nickname-error');
                const nickname = nicknameInput.value.trim();

                nicknameError.classList.add('hidden');

                if (!nickname) {
                    nicknameError.textContent = 'Укажите псевдоним';
                    nicknameError.classList.remove('hidden');
                    nicknameInput.focus();
                    return;
                }

                if (nickname.length < 3 || nickname.length > 30) {
                    nicknameError.textContent = 'Псевдоним должен содержать от 3 до 30 символов';
                    nicknameError.classList.remove('hidden');
                    nicknameInput.focus();
                    return;
                }

                if (!/^[a-zA-Z0-9_\-\.]+$/.test(nickname)) {
                    nicknameError.textContent = 'Псевдоним может содержать только латиницу, цифры, минус, подчеркивание и точку';
                    nicknameError.classList.remove('hidden');
                    nicknameInput.focus();
                    return;
                }

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey. Пожалуйста, обновите браузер или используйте современное устройство.');
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
                        
                        // Handle Laravel Validation format
                        let errorMessage = 'Ошибка инициализации регистрации.';
                        if (errorData.errors && errorData.errors.username) {
                            errorMessage = errorData.errors.username[0];
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }

                        nicknameError.textContent = errorMessage;
                        nicknameError.classList.remove('hidden');
                        throw new Error(errorMessage);
                    }

                    const options = await prepareResponse.json();
                    console.log('[Passkey] Received options:', options);

                    // Step 2: Convert options for navigator.credentials.create
                    options.challenge = _b64ToUint8Array(options.challenge);
                    options.user.id = _b64ToUint8Array(options.user.id);
                    if (options.excludeCredentials) {
                        options.excludeCredentials.forEach(cred => {
                            cred.id = _b64ToUint8Array(cred.id);
                        });
                    }

                    btn.innerHTML = '<span class="animate-pulse">Создайте ключ...</span>';

                    // Step 3: Trigger Browser Prompt
                    const credential = await navigator.credentials.create({
                        publicKey: options
                    });

                    if (!credential) {
                        throw new Error('Не удалось создать ключ доступа.');
                    }

                    console.log('[Passkey] Credential created:', credential.id);
                    btn.innerHTML = '<span class="animate-pulse">Сохранение...</span>';

                    // Step 4: Send credential back to server to finalize
                    const registerPayload = {
                        id: credential.id,
                        rawId: _bufToBase64URL(credential.rawId),
                        response: {
                            clientDataJSON: _bufToBase64URL(credential.response.clientDataJSON),
                            attestationObject: _bufToBase64URL(credential.response.attestationObject),
                        },
                        type: credential.type,
                        clientExtensionResults: credential.getClientExtensionResults() || {},
                    };

                    const storeResponse = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(registerPayload)
                    });

                    if (!storeResponse.ok) {
                        const storeError = await storeResponse.json();
                        throw new Error(storeError.message || 'Ошибка сохранения ключа доступа.');
                    }

                    console.log('[Passkey] Registration success! Redirecting...');
                    btn.innerHTML = 'Готово!';
                    
                    // Step 5: Redirect to focused security onboarding page
                    window.location.href = '{{ route('shop.customers.account.onboarding.security') }}';

                } catch (err) {
                    console.error('[Passkey] Error:', err);
                    if (err.name !== 'NotAllowedError') {
                        alert('Ошибка: ' + err.message);
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }
        </script>
    @endpush
</x-shop::layouts.split-screen>