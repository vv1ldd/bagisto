@push('meta')
<meta name="description" content="@lang('shop::app.customers.login-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.login-form.page-title')" />
@endPush

<x-shop::layouts.split-screen>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
        </x-slot>

        {!! view_render_event('bagisto.shop.customers.login.before') !!}

        <!-- Initial Login Options (Passkey vs Email) -->
        <div id="login-options-container" class="flex flex-col gap-2 transition-all duration-300">
            <!-- Passkey Login Button (Primary focus) -->
            <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                class="flex w-full items-center justify-center gap-3 !rounded-[20px] bg-[#7C45F5] px-8 py-3 text-center font-medium text-white transition hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    <circle cx="12" cy="16" r="1"></circle>
                </svg>
                Войти с помощью Passkey
            </button>
            @php
                $showEmailForm = $errors->any() || session('email');
            @endphp
            <div class="relative my-2 text-center {{ $showEmailForm ? 'hidden' : '' }}">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-zinc-200"></div>
                </div>
                <div class="relative">
                    <span
                        class="bg-white px-4 text-[10px] font-bold uppercase tracking-widest text-zinc-400">@lang('shop::app.customers.login-form.or')</span>
                </div>
            </div>

            <!-- Show Email Login Form Button -->
            <div id="email-login-form-button-container" class="{{ $showEmailForm ? 'hidden' : '' }}">
                <button type="button" id="show-email-form-button"
                    onclick="document.getElementById('email-login-form-button-container').classList.add('hidden'); document.getElementById('email-login-form-container').classList.remove('hidden'); document.getElementById('passkey-login-button').classList.add('!hidden'); document.querySelector('.login-options-container-or').classList.add('hidden');"
                    class="flex w-full items-center justify-center gap-3 !rounded-[20px] border border-zinc-200 bg-white px-8 py-3 text-center font-medium text-zinc-700 transition hover:bg-zinc-50 focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                        </path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Войти через почту
                </button>
            </div>

            <!-- Separator for email view (hidden by default) -->
            <div class="login-options-container-or relative mb-2 text-center hidden">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-zinc-200"></div>
                </div>
                <div class="relative">
                    <span
                        class="bg-white px-4 text-[10px] font-bold uppercase tracking-widest text-zinc-400">@lang('shop::app.customers.login-form.or')</span>
                </div>
            </div>

            {{-- Magic Link Email Form (Hidden by default, auto-shown on errors) --}}
            <div id="email-login-form-container" class="{{ $showEmailForm ? 'flex' : 'hidden' }} flex-col gap-4">
                <x-shop::form :action="route('shop.customer.session.email')" v-slot="{ meta }">
                    <x-shop::form.control-group class="mb-2">
                        <x-shop::form.control-group.label
                            class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                            @lang('shop::app.customers.login-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="email"
                            class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-[12px] focus:!ring-2 focus:!ring-zinc-500 w-full"
                            name="email" rules="required|email" :value="old('email')"
                            :label="trans('shop::app.customers.login-form.email')" placeholder="email@example.com" />

                        {{-- Register suggestion shown when email not found --}}
                        @if($errors->has('email') && str_contains($errors->first('email'), 'не найден'))
                            <p class="mt-2 text-[12px] text-zinc-400">Нет аккаунта с этой почтой —
                                воспользуйтесь кнопкой «Создать аккаунт» ниже.</p>
                        @endif

                    </x-shop::form.control-group>

                    <button
                        class="w-full !rounded-[20px] bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7C45F5]"
                        type="submit" :disabled="!meta.valid">
                        Отправить ссылку для входа
                    </button>
                </x-shop::form>

            </div>

            <p class="mt-6 text-center text-sm text-zinc-500">
                Впервые у нас?
                <a class="font-bold text-[#7C45F5] hover:underline" href="{{ route('shop.customers.register.index') }}">
                    @lang('shop::app.customers.signup-form.button-title')
                </a>
            </p>
        </div>

        {!! view_render_event('bagisto.shop.customers.login.after') !!}

        @push('scripts')
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
            <script>
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

                // Passkey helpers 
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

                function _bufToBase64URL(buffer) {
                    var binary = '';
                    var bytes = new Uint8Array(buffer);
                    for (var i = 0; i < bytes.byteLength; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                }

                /**
                 * Handle Passkey Login
                 */
                window.handlePasskeyLogin = async function (e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    console.log('[Passkey] handlePasskeyLogin started');

                    var button = document.getElementById('passkey-login-button');
                    var originalContent = button.innerHTML;

                    if (!window.PublicKeyCredential) {
                        console.error('[Passkey] PublicKeyCredential not supported');
                        alert('Ваш браузер или соединение (требуется HTTPS) не поддерживают Passkey.');
                        return;
                    }

                    button.disabled = true;
                    button.innerText = 'Подготовка...';

                    try {
                        console.log('[Passkey] Fetching options from:', '{{ route('passkeys.login-options') }}');
                        var response = await fetch('{{ route('passkeys.login-options') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            var errText = await response.text();
                            console.error('[Passkey] Options error:', response.status, errText);
                            throw new Error('Ошибка связи с сервером (' + response.status + ')');
                        }

                        var options = await response.json();
                        console.log('[Passkey] Received options:', options);

                        if (!options || !options.challenge) {
                            throw new Error('Сервер прислал некорректные данные (нет challenge).');
                        }

                        // Convert base64 to buffer
                        options.challenge = _b64ToUint8Array(options.challenge);
                        if (options.allowCredentials) {
                            options.allowCredentials.forEach(function (cred) {
                                cred.id = _b64ToUint8Array(cred.id);
                            });
                        }

                        button.innerText = 'Подтвердите личность...';
                        console.log('[Passkey] Calling navigator.credentials.get with options:', options);

                        var credential = await navigator.credentials.get({
                            publicKey: options
                        });

                        if (!credential) {
                            console.warn('[Passkey] No credential returned (user likely cancelled)');
                            throw new Error('Операция отменена пользователем.');
                        }

                        console.log('[Passkey] Credential obtained, id:', credential.id);
                        button.innerText = 'Проверка...';

                        var payload = {
                            start_authentication_response: JSON.stringify({
                                id: credential.id,
                                rawId: _bufToBase64URL(credential.rawId),
                                response: {
                                    clientDataJSON: _bufToBase64URL(credential.response.clientDataJSON),
                                    authenticatorData: _bufToBase64URL(credential.response.authenticatorData),
                                    signature: _bufToBase64URL(credential.response.signature),
                                    userHandle: credential.response.userHandle ? _bufToBase64URL(credential.response.userHandle) : null,
                                },
                                type: credential.type,
                                clientExtensionResults: credential.getClientExtensionResults() || {},
                            })
                        };

                        console.log('[Passkey] Sending authentication payload to server...');
                        var loginResponse = await fetch('{{ route('passkeys.login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        var result = await loginResponse.json();
                        console.log('[Passkey] Login result:', result);

                        if (loginResponse.ok) {
                            window.location.href = result.redirect_url || '{{ route('shop.customers.account.index') }}';
                        } else {
                            throw new Error(result.message || 'Ошибка проверки Passkey на сервере.');
                        }
                    } catch (err) {
                        console.error('[Passkey] Error during login flow:', err.name, err.message);
                        if (err.name !== 'NotAllowedError' && err.message !== 'Операция отменена пользователем.') {
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