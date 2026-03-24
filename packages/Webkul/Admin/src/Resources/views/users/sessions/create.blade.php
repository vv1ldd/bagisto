<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.users.sessions.title')
    </x-slot>

    <div class="flex h-[100vh] items-center justify-center">
        <div class="flex flex-col items-center gap-5">
            <!-- Logo -->            
            @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                <img
                    class="h-10 w-[110px]"
                    src="{{ Storage::url($logo) }}"
                    alt="{{ config('app.name') }}"
                />
            @else
                <img
                    class="w-max" 
                    src="{{ bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                />
            @endif

            <div class="box-shadow flex min-w-[300px] flex-col rounded-md bg-white dark:bg-gray-900">
                <!-- Login Form -->
                <x-admin::form :action="route('admin.session.store')">
                    <p class="p-4 text-xl font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.users.sessions.title')
                    </p>

                    <div class="border-y p-4 dark:border-gray-800">
                        <!-- Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.users.sessions.email')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control 
                                type="email" 
                                class="w-[254px] max-w-full" 
                                id="email"
                                name="email" 
                                rules="required|email" 
                                :label="trans('admin::app.users.sessions.email')"
                                :placeholder="trans('admin::app.users.sessions.email')"
                            />

                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>

                        <!-- Password -->
                        <x-admin::form.control-group class="relative w-full">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.users.sessions.password')
                            </x-admin::form.control-group.label>
                    
                            <x-admin::form.control-group.control 
                                type="password" 
                                class="w-[254px] max-w-full ltr:pr-10 rtl:pl-10" 
                                id="password"
                                name="password" 
                                rules="required|min:6" 
                                :label="trans('admin::app.users.sessions.password')"
                                :placeholder="trans('admin::app.users.sessions.password')"
                            />
                    
                            <span 
                                class="icon-view absolute top-[42px] -translate-y-2/4 cursor-pointer text-2xl ltr:right-2 rtl:left-2"
                                onclick="switchVisibility()"
                                id="visibilityIcon"
                                role="presentation"
                                tabindex="0"
                            >
                            </span>
                    
                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>
                    </div>

                    <div class="flex items-center justify-between p-4">
                        <!-- Forgot Password Link -->
                        <div class="flex flex-col gap-1">
                            <a 
                                class="cursor-pointer text-xs font-semibold leading-6 text-blue-600"
                                href="{{ route('admin.forget_password.create') }}"
                            >
                                @lang('admin::app.users.sessions.forget-password-link')
                            </a>

                            <a 
                                class="cursor-pointer text-xs font-semibold leading-6 text-orange-600"
                                href="{{ route('admin.session.recovery.create') }}"
                            >
                                Восстановить через Seed-фразу
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button
                            class="cursor-pointer rounded-md border border-blue-700 bg-blue-600 px-3.5 py-1.5 font-semibold text-gray-50"
                            aria-label="{{ trans('admin::app.users.sessions.submit-btn')}}"
                        >
                            @lang('admin::app.users.sessions.submit-btn')
                        </button>
                    </div>

                    <div class="flex flex-col gap-3 border-t p-4 dark:border-gray-800">
                        <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                            class="flex w-full items-center justify-center gap-3 rounded-md border border-gray-200 bg-white px-3.5 py-1.5 font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 uppercase tracking-wide text-xs">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                <circle cx="12" cy="16" r="1.5"></circle>
                            </svg>
                            Войти с помощью Passkey
                        </button>
                    </div>
                </x-admin::form>
            </div>

            <!-- Powered By -->
            <div class="text-sm font-normal">
                @lang('admin::app.users.sessions.powered-by-description', [
                    'bagisto' => '<a class="text-blue-600 hover:underline" href="https://bagisto.com/en/">Bagisto</a>',
                    'webkul' => '<a class="text-blue-600 hover:underline" href="https://webkul.com/">Webkul</a>',
                ])
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function switchVisibility() {
                let passwordField = document.getElementById("password");
                let visibilityIcon = document.getElementById("visibilityIcon");

                passwordField.type = passwordField.type === "password" ? "text" : "password";
                visibilityIcon.classList.toggle("icon-view-close");
            }

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

                if (!SimpleWebAuthn) {
                    alert('Библиотека WebAuthn не загружена.');
                    return;
                }

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey (требуется HTTPS).');
                    return;
                }

                button.disabled = true;
                button.innerText = 'Подготовка...';

                try {
                    var response = await fetch('{{ route('admin.passkey.login_options') }}', {
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

                    button.innerText = 'Подтвердите личность...';

                    // Use SimpleWebAuthn library
                    var asseResp = await SimpleWebAuthn.startAuthentication(options);
                    
                    button.innerText = 'Проверка...';

                    var loginResponse = await fetch('{{ route('admin.passkey.login') }}', {
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
                        window.location.href = result.redirect_url || '{{ route('admin.dashboard.index') }}';
                    } else {
                        throw new Error(result.message || 'Ошибка входа.');
                    }
                } catch (err) {
                    console.error('[Passkey] Error:', err);
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена') && err.name !== 'AbortError') {
                        alert(err.message);
                    }
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            }
        </script>
    @endpush
</x-admin::layouts.anonymous>