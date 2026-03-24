@push('meta')
<meta name="description" content="@lang('shop::app.customers.login-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.login-form.page-title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
        </x-slot>

        {!! view_render_event('bagisto.shop.customers.login.before') !!}

        <!-- Initial Login Options (Passkey Only) -->
        <div id="login-options-container" class="flex flex-col items-center w-full max-w-[400px] mx-auto py-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
            <!-- Branding Accent -->
            <div class="mb-12 flex flex-col items-center">
                <div class="relative w-20 h-20 mb-6 group">
                    <div class="absolute inset-0 bg-[#7C45F5] rotate-6 group-hover:rotate-12 transition-transform duration-500"></div>
                    <div class="absolute inset-0 bg-white border-2 border-zinc-900 flex items-center justify-center -rotate-3 group-hover:rotate-0 transition-transform duration-500 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        <svg class="w-10 h-10 text-zinc-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-zinc-900 text-4xl font-black uppercase tracking-tighter mb-2 leading-[0.8]">Вход в Meanly</h1>
                <div class="h-2 w-12 bg-[#7C45F5] shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]"></div>
            </div>

            <p class="text-zinc-500 text-center font-bold text-sm mb-10 uppercase tracking-widest leading-relaxed">
                Доступ через <span class="text-[#7C45F5]">Passkey</span> — мгновенно, надежно, без пароля.
            </p>

            <!-- Passkey Login Button (Brutalist style) -->
            <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-sm overflow-hidden mb-12">
                <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span id="passkey-btn-text">Использовать Passkey</span>
            </button>

            <!-- Footer links -->
            <div class="w-full space-y-8 flex flex-col items-center">
                <div class="flex items-center gap-4 w-full">
                    <div class="h-0.5 flex-1 bg-zinc-100"></div>
                    <span class="text-[10px] font-black text-zinc-300 uppercase tracking-[0.3em]">Или</span>
                    <div class="h-0.5 flex-1 bg-zinc-100"></div>
                </div>

                <div class="flex flex-col items-center gap-6">
                    <a href="{{ route('shop.customers.register.index') }}" 
                       class="text-zinc-900 text-sm font-black uppercase tracking-widest hover:text-[#7C45F5] transition-colors flex items-center gap-3 group">
                        Создать аккаунт
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M5 12h14m-7-7l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('shop.customers.recovery.seed') }}" 
                        class="text-[10px] font-bold uppercase tracking-[0.25em] text-zinc-400 hover:text-red-500 transition-colors text-center max-w-[240px] leading-loose">
                        Восстановить через секретную фразу
                    </a>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.customers.login.after') !!}

        @push('scripts')
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
            <script>
                function showEmailLoginForm() {
                    document.getElementById('email-login-form-button-container').classList.add('hidden');
                    document.getElementById('login-options-separator').classList.add('hidden');
                    document.getElementById('passkey-login-button').classList.add('!hidden');
                    
                    document.getElementById('email-login-form-container').classList.remove('hidden');
                    document.getElementById('back-to-options-container').classList.remove('hidden');
                    document.getElementById('back-to-options-container').classList.add('flex');
                }

                function backToLoginOptions() {
                    document.getElementById('email-login-form-button-container').classList.remove('hidden');
                    document.getElementById('login-options-separator').classList.remove('hidden');
                    document.getElementById('passkey-login-button').classList.remove('!hidden');
                    
                    document.getElementById('email-login-form-container').classList.add('hidden');
                    document.getElementById('back-to-options-container').classList.add('hidden');
                    document.getElementById('back-to-options-container').classList.remove('flex');
                }

                // Password toggle 
                (function () {
                    var passwordInput = document.getElementById('password-input');
                    var toggleButton = document.getElementById('toggle-password');
                    var checkbox = document.getElementById('show-password-checkbox');

                    if (passwordInput) {
                        function toggle() {
                            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
                        }
                        if (toggleButton) toggleButton.addEventListener('click', toggle);
                        if (checkbox) checkbox.addEventListener('change', toggle);
                    }
                })();


                /**
                 * Handle Passkey Login
                 */
                window.handlePasskeyLogin = async function (e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                    var button = document.getElementById('passkey-login-button');
                    var originalContent = button.innerHTML;

                    if (!window.PublicKeyCredential) {
                        alert('Ваш браузер не поддерживает Passkey.');
                        return;
                    }

                    button.disabled = true;
                    button.innerText = 'Подготовка...';

                    try {
                        var response = await fetch('{{ route('passkeys.login-options') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Ошибка связи с сервером (' + response.status + ')');
                        }

                        var rawOptions = await response.json();
                        var options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                        // Robust base64url conversion for Safari
                        const toBase64Url = (str) => {
                            if (!str || typeof str !== 'string') return str;
                            return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                        };

                        if (options.challenge) options.challenge = toBase64Url(options.challenge);
                        if (options.allowCredentials) {
                            options.allowCredentials.forEach(cred => {
                                if (cred.id) cred.id = toBase64Url(cred.id);
                            });
                        }

                        // --- Domain/RP Check ---
                        const currentDomain = window.location.hostname;
                        if (options.rpId && options.rpId !== currentDomain) {
                            console.warn('[Passkey] RP ID mismatch. Server sent:', options.rpId, 'Current domain:', currentDomain);
                        }

                        button.innerText = 'Подтвердите личность...';

                        // Use SimpleWebAuthn library
                        var asseResp = await SimpleWebAuthn.startAuthentication(options);
                        console.log('[Passkey] Authentication response obtained:', asseResp);
                        
                        button.innerText = 'Проверка...';

                        var loginResponse = await fetch('{{ route('passkeys.login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                start_authentication_response: JSON.stringify(asseResp),
                                remember: true
                            })
                        });

                        var result = await loginResponse.json();
                        if (loginResponse.ok) {
                            window.location.href = '{{ route('shop.home.index') }}';
                        } else {
                            throw new Error(result.message || 'Ошибка входа.');
                        }
                    } catch (err) {
                        console.error('[Passkey] Error:', err);
                        if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                            alert(err.message);
                        }
                    } finally {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    }
                }
            </script>
        @endpush
</x-shop::layouts.split-screen>