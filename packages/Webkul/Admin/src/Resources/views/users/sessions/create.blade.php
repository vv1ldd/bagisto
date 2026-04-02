<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.users.sessions.title')
    </x-slot>

    <div class="flex h-[100vh] items-center justify-center bg-[#F4F4F4] dark:bg-gray-900">
        <div class="flex w-[400px] flex-col items-center gap-8 p-6">
            <!-- Logo -->            
            <div class="mb-2">
                @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                    <img class="h-12 w-auto" src="{{ Storage::url($logo) }}" alt="{{ config('app.name') }}" />
                @else
                    <img class="w-48" src="{{ bagisto_asset('images/logo.svg') }}" alt="{{ config('app.name') }}" />
                @endif
            </div>

            <div class="w-full space-y-6">
                <!-- PRIMARY ACTION: PASSKEY -->
                <button type="button" id="passkey-login-button" onclick="handlePasskeyLogin(event)"
                    class="group relative flex h-16 w-full items-center justify-center gap-4 rounded-2xl border-4 border-black bg-[#7C45F5] text-sm font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none dark:border-white dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.2)]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                    </svg>
                    Войти через Passkey
                </button>

                <div class="relative flex items-center py-2">
                    <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                    <span class="mx-4 flex-shrink text-[10px] font-bold uppercase tracking-widest text-gray-400">ИЛИ</span>
                    <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                </div>

                <!-- SECONDARY ACTION: PASSWORD FORM (HIDDEN BY DEFAULT) -->
                <div x-data="{ showLegacy: false }" class="w-full">
                    <button type="button" @click="showLegacy = !showLegacy" 
                        class="mx-auto flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white transition-colors">
                        <span :class="showLegacy ? 'rotate-180' : ''" class="icon-arrow-down text-lg transition-transform"></span>
                        Вход по паролю
                    </button>

                    <div x-show="showLegacy" x-collapse x-cloak class="mt-6 overflow-hidden rounded-2xl border-4 border-black bg-white p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:border-white dark:bg-gray-800 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.1)]">
                        <x-admin::form :action="route('admin.session.store')">
                            <div class="space-y-4">
                                <!-- Email -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required !text-[10px] !font-black !uppercase !tracking-widest">
                                        @lang('admin::app.users.sessions.email')
                                    </x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="email" name="email" rules="required|email" class="!rounded-xl !border-2 !border-black dark:!border-white" />
                                    <x-admin::form.control-group.error control-name="email" />
                                </x-admin::form.control-group>

                                <!-- Password -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required !text-[10px] !font-black !uppercase !tracking-widest">
                                        @lang('admin::app.users.sessions.password')
                                    </x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="password" name="password" id="password" rules="required|min:6" class="!rounded-xl !border-2 !border-black dark:!border-white" />
                                    <x-admin::form.control-group.error control-name="password" />
                                </x-admin::form.control-group>

                                <button class="flex h-12 w-full items-center justify-center rounded-xl border-2 border-black bg-black font-black uppercase tracking-widest text-white transition-all hover:bg-gray-800 active:scale-95 dark:border-white dark:bg-white dark:text-black">
                                    @lang('admin::app.users.sessions.submit-btn')
                                </button>
                            </div>
                        </x-admin::form>
                    </div>
                </div>

                <!-- RECOVERY -->
                <div class="flex flex-col items-center gap-4 pt-4">
                    <a href="{{ route('admin.forget_password.create') }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-[#7C45F5] transition-colors">
                        @lang('admin::app.users.sessions.forget-password-link')
                    </a>
                    <a href="{{ route('admin.session.recovery.create') }}" class="rounded-none border-2 border-dashed border-orange-200 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-orange-600 hover:border-orange-600 transition-all">
                        Восстановить через Seed-фразу
                    </a>
                </div>
            </div>

            <!-- Powered By -->
            <div class="mt-8 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">
                &copy; {{ date('Y') }} Meanly Admin • Powered by Bagisto
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