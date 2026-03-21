        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="profile" />
        @endSection
        @endif

            @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                <div class="mx-auto w-full px-4 py-8 max-sm:px-0 max-sm:py-0">
                    <div class="bg-white p-10 flex flex-col items-center relative overflow-hidden w-full min-h-[calc(100vh-100px)] sm:min-h-0 sm:border-2 sm:border-zinc-100 shadow-[0_20px_50px_rgba(124,69,245,0.06)] !rounded-none">
                        <!-- Design Accents -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#7C45F5]/5 blur-3xl rounded-full"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-[#FF4D6D]/3 blur-3xl rounded-full"></div>

                        <div class="w-full mx-auto z-10 relative flex flex-col items-center justify-center flex-1">
                            <!-- Icon/Brand Section -->
                            <div class="mb-10 flex flex-col items-center">
                                <div class="relative w-24 h-24 mb-6">
                                    <!-- Premium Fingerprint Passkey Icon -->
                                    <svg width="96" height="96" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 11C12 11 12.6344 9.17208 15.1344 9.17204C17.6344 9.172 18.2688 11 18.2688 11" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M9.1344 9.17204C7.8844 9.17204 6.6344 10.086 5.86877 11.4141" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M12 14.172V15.172" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M12 18.172V21" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M5.7312 14.786C5.25071 15.864 5.37894 17.16 6.0887 18.172" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M18.2688 18.172C18.9786 17.16 19.1068 15.864 18.6263 14.786" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M14.5 14.172C14.5 14.172 14.8844 13 12 13C9.1156 13 9.5 14.172 9.5 14.172V17.172C9.5 17.172 9.1156 18.3441 12 18.3441C14.8844 18.3441 14.5 17.172 14.5 17.172V14.172Z" stroke="url(#passkey_gradient_form)" stroke-width="1.5" />
                                        <path d="M13.8688 6.41406C13.2929 6.14728 12.6616 6.00287 12 6C11.3384 6.00287 10.7071 6.14728 10.1312 6.41406" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M17.1344 6.17204C15.6344 4.17204 12 4.17204 12 4.17204C12 4.17204 8.3656 4.17204 6.8656 6.17204" stroke="url(#passkey_gradient_form)" stroke-width="1.5" stroke-linecap="round" />
                                        <defs>
                                            <linearGradient id="passkey_gradient_form" x1="12" y1="4" x2="12" y2="21" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#7C45F5" />
                                                <stop offset="1" stop-color="#FF4D6D" />
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                                <h3 class="text-zinc-900 text-4xl font-black uppercase tracking-tighter mb-4 text-center leading-[0.9]">Настройка входа</h3>
                                <div class="h-2 w-16 bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D]"></div>
                            </div>

                            <div class="space-y-4 mb-12">
                                <p class="text-[17px] font-medium text-zinc-500 text-center leading-relaxed max-w-[320px]">
                                    Используйте отпечаток или лицо для безопасного доступа к <span class="text-[#7C45F5] font-black uppercase tracking-tight">Meanly</span> без паролей.
                                </p>
                            </div>

                            <!-- Buttons -->
                            <div class="w-full max-w-[320px] flex flex-col items-center gap-6">
                                <button type="button" id="add-passkey-button"
                                    onclick="window.startPasskeyRegistration()"
                                    class="group relative flex w-full items-center justify-center h-20 bg-[#7C45F5] text-white transition-all hover:bg-[#6b35e4] hover:shadow-2xl hover:shadow-[#7C45F5]/30 active:scale-[0.98] shadow-xl shadow-[#7C45F5]/25 overflow-hidden !rounded-none border-none">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                    <span class="icon-add-new text-2xl mr-3 font-bold"></span>
                                    <span id="add-passkey-button-text" class="text-[14px] font-black uppercase tracking-[0.2em]">Создать ключ</span>
                                </button>

                                <a href="{{ route('shop.customers.account.profile.complete_registration_success') }}"
                                    class="text-zinc-400 text-[11px] font-bold uppercase tracking-[0.2em] transition-all hover:text-[#7C45F5] underline underline-offset-4">
                                    Пропустить
                                </a>
                            </div>

                            <div class="mt-10 flex items-center justify-center gap-3">
                                <div class="w-1.5 h-1.5 bg-[#7C45F5] opacity-20"></div>
                                <div class="w-1.5 h-1.5 bg-[#7C45F5]"></div>
                                <div class="w-1.5 h-1.5 bg-[#7C45F5] opacity-20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Original Profile Settings View -->
                <div class="px-8 max-md:px-5 mt-4 w-full max-w-[600px] mx-auto">
                    {!! view_render_event('bagisto.shop.customers.account.profile.email.after') !!}

                    <!-- Passkeys & Trusted Devices -->
                    <div class="mt-8">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4 px-1">
                            Passkeys & Безопасность
                        </h3>

                        <div class="bg-zinc-50 border border-zinc-100  overflow-hidden">
                            @if ($customer->passkeys->count())
                                @foreach ($customer->passkeys as $passkey)
                                    <div class="flex justify-between items-center px-5 py-4 border-b border-zinc-200/60 last:border-0 max-md:px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-zinc-200/50 ">
                                                <svg class="h-5 w-5 text-zinc-600" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-[15px] font-medium text-zinc-900 max-md:text-sm flex items-center gap-2">
                                                    {{ $passkey->name ?: 'Passkey устройство' }}
                                                    @if($passkey->id == session('current_session_passkey_id') || $passkey->id == request()->cookie('current_device_passkey_id'))
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-100 px-2 py-0.5 ">Это устройство</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-zinc-500">Добавлено:
                                                    {{ $passkey->created_at->format('d.m.Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <form action="{{ route('passkeys.destroy', $passkey->id) }}" method="POST"
                                            onsubmit="return confirm('Удалить это устройство?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-2">
                                                <span class="icon-bin text-xl"></span>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-5 py-8 text-center bg-white/50">
                                    <p class="text-[15px] text-zinc-500 mb-2">У вас пока нет привязанных Passkey
                                        устройств.</p>
                                    <p class="text-xs text-zinc-400">Добавьте устройство (отпечаток или FaceID) для
                                        быстрого
                                        входа без пароля и подтверждений по почте.</p>
                                </div>
                            @endif

                            <div class="p-5 bg-white/50 border-t border-zinc-100">
                                <button type="button" id="add-passkey-button"
                                    onclick="window.startPasskeyRegistration()"
                                    class="flex w-full items-center justify-center gap-2  border border-zinc-200 bg-white py-4 text-[15px] font-bold text-zinc-700 transition hover:bg-zinc-50 disabled:opacity-50">
                                    <span class="icon-add text-lg"></span>
                                    <span id="add-passkey-button-text">Привязать новое устройство</span>
                                </button>
                            </div>
                        </div>
                    </div>


                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.before') !!}
                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.after') !!}
                </div>
            @endif
        @push('scripts')
            <script>
                window.startPasskeyRegistration = async function () {
                    console.log('Passkey registration started');
                    const button = document.getElementById('add-passkey-button');
                    const buttonText = document.getElementById('add-passkey-button-text');
                    const originalText = buttonText?.innerText || 'Привязать новое устройство';

                    if (!window.PublicKeyCredential) {
                        alert('Ваш браузер или текущее соединение не поддерживают технологию Passkey (требуется HTTPS).');
                        return;
                    }

                    if (button) button.disabled = true;
                    if (buttonText) buttonText.innerText = 'Подготовка...';

                    function base64ToUint8Array(base64) {
                        const padding = '='.repeat((4 - base64.length % 4) % 4);
                        const b64 = (base64 + padding).replace(/-/g, '+').replace(/_/g, '/');
                        const rawData = window.atob(b64);
                        const outputArray = new Uint8Array(rawData.length);
                        for (let i = 0; i < rawData.length; ++i) {
                            outputArray[i] = rawData.charCodeAt(i);
                        }
                        return outputArray;
                    }

                    function arrayBufferToBase64URL(buffer) {
                        let binary = '';
                        const bytes = new Uint8Array(buffer);
                        const len = bytes.byteLength;
                        for (let i = 0; i < len; i++) {
                            binary += String.fromCharCode(bytes[i]);
                        }
                        return window.btoa(binary)
                            .replace(/\+/g, '-')
                            .replace(/\//g, '_')
                            .replace(/=/g, '');
                    }

                    try {
                        console.log('Fetching registration options...');
                        const response = await fetch('{{ route('passkeys.register-options') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Options fetch failed:', errorText);
                            throw new Error('Не удалось получить настройки с сервера.');
                        }

                        const rawOptions = await response.json();
                        console.log('Options received:', rawOptions);

                        const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                        if (!options || !options.challenge) {
                            throw new Error('Получены некорректные настройки с сервера (отсутствует challenge).');
                        }

                        // Robust Challenge conversion
                        console.log('Raw challenge type:', typeof options.challenge, 'Value:', options.challenge);
                        options.challenge = base64ToUint8Array(options.challenge);
                        console.log('Converted challenge:', options.challenge);

                        options.user.id = base64ToUint8Array(options.user.id);

                        if (options.excludeCredentials) {
                            options.excludeCredentials.forEach(cred => {
                                cred.id = base64ToUint8Array(cred.id);
                            });
                        }

                        if (buttonText) buttonText.innerText = 'Ожидание устройства...';
                        console.log('Calling navigator.credentials.create...');

                        const credential = await navigator.credentials.create({
                            publicKey: options
                        });

                        if (!credential) throw new Error('Отмена пользователем.');
                        console.log('Credential created successfully:', credential);

                        if (buttonText) buttonText.innerText = 'Сохранение...';

                        const transports = (credential.response.getTransports) ? credential.response.getTransports() : [];

                        const registrationData = {
                            id: credential.id,
                            rawId: arrayBufferToBase64URL(credential.rawId),
                            response: {
                                clientDataJSON: arrayBufferToBase64URL(credential.response.clientDataJSON),
                                attestationObject: arrayBufferToBase64URL(credential.response.attestationObject),
                                transports: transports,
                            },
                            type: credential.type,
                            clientExtensionResults: credential.getClientExtensionResults() || {},
                        };

                        console.log('Sending final registration data to server:', registrationData);

                        const registrationResponse = await fetch('{{ route('passkeys.register') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(registrationData)
                        });

                        if (registrationResponse.ok) {
                            console.log('Registration success confirmed by server');
                            @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                                window.location.href = '{!! route('shop.customers.account.profile.complete_registration_success') !!}';
                            @else
                                window.location.reload();
                            @endif
                        } else {
                            const errorData = await registrationResponse.json();
                            console.error('Registration rejected by server:', errorData);
                            window.showAlert('error', 'Ошибка', errorData.message || 'Ошибка сохранения Passkey');
                        }
                    } catch (error) {
                        console.error('Passkey registration error:', error);
                        
                        let message = error.message;
                        let title = 'Ошибка';
                        let type = 'error';

                        // Handle cancellation
                        if (error.name === 'NotAllowedError' || message.includes('отмена') || message.includes('cancelled')) {
                            title = 'Запрос отменен';
                            message = 'Действие отменено пользователем.';
                            type = 'warning';
                        }

                        window.showAlert(type, title, message);
                    } finally {
                        if (button) button.disabled = false;
                        if (buttonText) buttonText.innerText = originalText;
                    }
                };

                // Global alert handler for Meanly style
                window.showAlert = function (type, title, message) {
                    // Try to use Bagisto's flash emitter first
                    if (window.app && window.app.config && window.app.config.globalProperties && window.app.config.globalProperties.$emitter) {
                        window.app.config.globalProperties.$emitter.emit('add-flash', { type, message });
                    } else {
                        // Fallback to Meanly-styled alert
                        const alertBox = document.createElement('div');
                        alertBox.className = `fixed bottom-10 left-1/2 -translate-x-1/2 z-[10001] p-5 font-bold text-white shadow-2xl transition-all border-l-4 min-w-[300px] animate-in slide-in-from-bottom-5 duration-300 ${type === 'success' ? 'bg-zinc-900 border-green-500' : (type === 'warning' ? 'bg-zinc-900 border-orange-500' : 'bg-red-600 border-white')}`;
                        alertBox.innerHTML = `
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <div class="text-[10px] uppercase tracking-[0.2em] opacity-60 mb-1">${title}</div>
                                    <div class="text-[14px] leading-tight">${message}</div>
                                </div>
                                <button onclick="this.parentElement.parentElement.remove()" class="text-white/40 hover:text-white">✕</button>
                            </div>
                        `;
                        document.body.appendChild(alertBox);
                        setTimeout(() => {
                            alertBox.classList.add('opacity-0', 'translate-y-5');
                            setTimeout(() => alertBox.remove(), 300);
                        }, 5000);
                    }
                };
            </script>
        @endpush
