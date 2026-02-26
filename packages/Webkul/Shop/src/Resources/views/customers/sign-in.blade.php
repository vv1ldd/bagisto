@push('meta')
<meta name="description" content="@lang('shop::app.customers.login-form.page-title')" />
<meta name="keywords" content="@lang('shop::app.customers.login-form.page-title')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
        </x-slot>

        <div class="flex min-h-screen w-full flex-wrap overflow-hidden bg-white">
            <!-- Left Side: Form -->
            <div
                class="flex w-full flex-col min-h-screen px-8 pt-32 pb-6 md:px-10 md:pt-40 md:pb-10 lg:px-20 lg:pt-48 lg:pb-20 md:w-1/2">
                <!-- Header/Logo -->
                <div class="mb-8 flex items-center justify-between">
                    <a href="{{ route('shop.home.index') }}"
                        aria-label="@lang('shop::app.customers.login-form.bagisto')">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                            alt="{{ config('app.name') }}" width="120" class="h-auto">
                    </a>
                </div>

                <!-- Form Area -->
                <div class="flex flex-grow flex-col justify-center py-10">
                    <div class="mx-auto w-full max-w-[400px]">

                        {!! view_render_event('bagisto.shop.customers.login.before') !!}

                        <!-- Initial Login Options (Passkey vs Email) -->
                        <div id="login-options-container" class="flex flex-col gap-4 transition-all duration-300">
                            <!-- Passkey Login Button (Primary focus) -->
                            <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                                class="flex w-full items-center justify-center gap-3 rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    <circle cx="12" cy="16" r="1"></circle>
                                </svg>
                                Войти с помощью Passkey
                            </button>

                            <div class="relative my-4 text-center">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-zinc-200"></div>
                                </div>
                                <div class="relative">
                                    <span
                                        class="bg-white px-4 text-xs font-bold uppercase tracking-widest text-zinc-400">@lang('shop::app.customers.login-form.or')</span>
                                </div>
                            </div>

                            <!-- Show Email Login Form Button -->
                            <div id="email-login-form-button-container">
                                <button type="button" id="show-email-form-button"
                                    onclick="document.getElementById('email-login-form-button-container').classList.add('hidden'); document.getElementById('email-login-form-container').classList.remove('hidden');"
                                    class="flex w-full items-center justify-center gap-3 rounded-full border border-zinc-200 bg-white px-8 py-4 text-center font-medium text-zinc-700 transition hover:bg-zinc-50 focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                        </path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    Войти через почту
                                </button>
                            </div>

                            <!-- Magic Link Email Form (Hidden by default) -->
                            <div id="email-login-form-container" class="hidden flex-col gap-4">
                                <x-shop::form :action="route('shop.customer.session.email')">
                                    <x-shop::form.control-group class="mb-4">
                                        <x-shop::form.control-group.label
                                            class="required !text-[10px] !font-bold uppercase tracking-widest text-zinc-400">
                                            @lang('shop::app.customers.login-form.email')
                                        </x-shop::form.control-group.label>

                                        <x-shop::form.control-group.control type="email"
                                            class="!border !border-zinc-200 !bg-transparent !px-5 !py-4 !rounded-xl focus:!ring-2 focus:!ring-zinc-500 w-full"
                                            name="email" rules="required|email" :value="old('email')"
                                            :label="trans('shop::app.customers.login-form.email')"
                                            placeholder="email@example.com" />

                                        <x-shop::form.control-group.error control-name="email" />
                                    </x-shop::form.control-group>

                                    <button
                                        class="w-full rounded-full bg-[#7C45F5] px-8 py-4 text-center font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20"
                                        type="submit">
                                        Отправить ссылку для входа
                                    </button>
                                </x-shop::form>

                            </div>

                            <p class="mt-8 text-center text-sm text-zinc-500">
                                Впервые у нас?
                                <a class="font-bold text-zinc-800 hover:underline"
                                    href="{{ route('shop.customers.register.index') }}">
                                    @lang('shop::app.customers.signup-form.button-title')
                                </a>
                            </p>
                        </div>


                        {!! view_render_event('bagisto.shop.customers.login.after') !!}
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-auto pt-10 text-center text-xs text-zinc-400">
                    @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
                </div>
            </div>

            <!-- Right Side: Artistic Image -->
            @php
                $bgConfig = core()->getConfigData('customer.login_page.background_image');
                $bgImageUrl = $bgConfig ? Storage::url($bgConfig) : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2564&auto=format&fit=crop';
            @endphp
            <div class="hidden md:block md:w-1/2">
                <div class="h-full w-full bg-cover bg-center bg-no-repeat"
                    style="background-image: url('{{ $bgImageUrl }}')">
                    <div class="flex h-full w-full items-end bg-black/5 p-12 text-white">
                        <div class="max-w-md">
                            {{-- Optional caption --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
</x-shop::layouts>