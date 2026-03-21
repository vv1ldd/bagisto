<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true" :is-cardless="true">
    <x-slot:title>Настройка кошелька</x-slot:title>

    <div class="mx-auto w-full px-4 py-8 max-sm:px-0 max-sm:py-0">
        <div class="bg-white p-10 flex flex-col items-center relative overflow-hidden w-full min-h-[calc(100vh-100px)] sm:min-h-0 sm:border-2 sm:border-zinc-100 shadow-[0_20px_50px_rgba(124,69,245,0.06)] !rounded-none">
            <!-- Design Accents -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#7C45F5]/5 blur-3xl rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-[#FF4D6D]/3 blur-3xl rounded-full"></div>

            <div class="w-full mx-auto z-10 relative flex flex-col items-center justify-center flex-1">
                <!-- Icon/Brand Section -->
                <div class="mb-10 flex flex-col items-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-[#7C45F5] to-[#FF4D6D] flex items-center justify-center mb-10 shadow-2xl shadow-[#7C45F5]/20 !rounded-none">
                        <span class="text-5xl">🔑</span>
                    </div>
                    <h3 class="text-zinc-900 text-4xl font-black uppercase tracking-tighter mb-4 text-center leading-[0.9]">Настройка кошелька</h3>
                    <div class="h-2 w-16 bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D]"></div>
                </div>

                <div class="space-y-4 mb-12">
                    <p class="text-[17px] font-medium text-zinc-500 text-center leading-relaxed max-w-[320px]">
                        Используйте отпечаток или лицо для безопасного доступа к <span class="text-[#7C45F5] font-black uppercase tracking-tight">Meanly Wallet</span> без подтверждений по почте.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="w-full max-w-[320px] flex flex-col gap-4">
                    <button type="button" id="add-passkey-button"
                        onclick="window.startPasskeyRegistration()"
                        class="group relative flex w-full items-center justify-center h-18 bg-[#7C45F5] text-white transition-all hover:bg-[#6b35e4] active:scale-[0.98] shadow-xl shadow-[#7C45F5]/25 overflow-hidden !rounded-none border-none">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <span class="icon-add-new text-2xl mr-3 font-bold"></span>
                        <span id="add-passkey-button-text" class="text-[13px] font-black uppercase tracking-widest">Создать ключ</span>
                    </button>

                    <a href="{{ route('shop.customers.account.index') }}"
                        class="flex w-full items-center justify-center h-16 bg-white text-zinc-400 text-[11px] font-bold uppercase tracking-[0.3em] transition-all hover:bg-zinc-50 hover:text-zinc-600 border border-zinc-100 !rounded-none">
                        На главную
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

    @push('scripts')
        <script>
            window.startPasskeyRegistration = async function () {
                const button = document.getElementById('add-passkey-button');
                const buttonText = document.getElementById('add-passkey-button-text');
                const originalText = buttonText?.innerText || 'Создать ключ';

                if (!window.PublicKeyCredential) {
                    window.showAlert('error', 'Ошибка', 'Ваш браузер не поддерживает Passkey (требуется HTTPS).');
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
                    return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                }

                try {
                    const response = await fetch('{{ route('passkeys.register-options') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) throw new Error('Не удалось получить настройки с сервера.');

                    const rawOptions = await response.json();
                    const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                    options.challenge = base64ToUint8Array(options.challenge);
                    options.user.id = base64ToUint8Array(options.user.id);

                    if (options.excludeCredentials) {
                        options.excludeCredentials.forEach(cred => {
                            cred.id = base64ToUint8Array(cred.id);
                        });
                    }

                    if (buttonText) buttonText.innerText = 'Ожидание устройства...';

                    const credential = await navigator.credentials.create({
                        publicKey: options
                    });

                    if (!credential) throw new Error('Отмена пользователем.');
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

                    const registrationResponse = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(registrationData)
                    });

                    if (registrationResponse.ok) {
                        window.location.reload();
                    } else {
                        const errorData = await registrationResponse.json();
                        window.showAlert('error', 'Ошибка', errorData.message || 'Ошибка сохранения Passkey');
                    }
                } catch (error) {
                    console.error('Passkey registration error:', error);
                    let message = error.message;
                    let type = 'error';

                    if (error.name === 'NotAllowedError' || message.includes('отмена') || message.includes('cancelled')) {
                        message = 'Действие отменено пользователем.';
                        type = 'warning';
                    }

                    window.showAlert(type, 'Ошибка', message);
                } finally {
                    if (button) button.disabled = false;
                    if (buttonText) buttonText.innerText = originalText;
                }
            };

            window.showAlert = function (type, title, message) {
                if (window.app && window.app.config && window.app.config.globalProperties && window.app.config.globalProperties.$emitter) {
                    window.app.config.globalProperties.$emitter.emit('add-flash', { type, message });
                } else {
                    alert(message);
                }
            };
        </script>
    @endpush
</x-shop::layouts.account>