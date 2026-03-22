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
        <div id="login-options-container" class="flex flex-col gap-4 transition-all duration-300">
            <!-- Passkey Login Button (Primary focus) -->
            <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                class="flex w-full items-center justify-center gap-3 !rounded-none bg-[#7C45F5] px-8 py-4 text-center font-bold text-white transition hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 uppercase tracking-widest">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    <circle cx="12" cy="16" r="1.5"></circle>
                </svg>
                Войти с помощью Passkey
            </button>

            <div class="mt-8 pt-6 border-t border-zinc-100 flex flex-col items-center gap-6">
                <p class="text-center text-sm text-zinc-500">
                    Впервые у нас?
                    <a class="font-bold text-[#7C45F5] hover:underline" href="{{ route('shop.customers.register.index') }}">
                        @lang('shop::app.customers.signup-form.button-title')
                    </a>
                </p>

                <div class="h-px w-8 bg-zinc-200"></div>

                <a href="{{ route('shop.customers.recovery.seed') }}" 
                    class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 hover:text-[#7C45F5] transition-colors text-center leading-relaxed max-w-[200px]">
                    Восстановить доступ через секретную фразу
                </a>
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
                            window.location.href = result.redirect_url || '{{ route('shop.customers.account.index') }}';
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