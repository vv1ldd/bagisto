        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="profile" />
        @endSection
        @endif

            @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                <div class="ios-settings-wrapper mx-auto w-full">
                    <div class="rounded-[2.5rem] bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-5 md:p-7 flex flex-col items-center relative overflow-hidden w-full shadow-[0_8px_32px_rgba(124,69,245,0.05)] border border-white">
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/10 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#3B82F6]/10 rounded-full blur-3xl"></div>

                        <div class="w-full mx-auto z-10 relative">
                            <h3 class="text-[#4A1D96] text-[26px] md:text-3xl font-extrabold mb-2 text-center tracking-tight leading-tight">Быстрый вход</h3>

                            <div class="space-y-2 mb-8">
                                <p class="text-[14px] text-zinc-600 mb-4 text-center mx-auto max-w-[320px]">
                                    Добавьте это устройство (отпечаток или FaceID) для мгновенного входа без пароля.
                                </p>
                            </div>

                            <div class="w-full relative z-10 max-w-[320px] mx-auto flex flex-col gap-3">
                                <button type="button" id="add-passkey-button"
                                    onclick="window.startPasskeyRegistration()"
                                    class="flex w-full items-center justify-center gap-2 rounded-full bg-[#7C45F5] px-8 py-3.5 text-[15px] font-medium text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 disabled:opacity-50">
                                    <span class="icon-add text-lg"></span>
                                    <span id="add-passkey-button-text">Привязать устройство</span>
                                </button>

                                <a href="{{ route('shop.customers.account.profile.complete_registration_success') }}"
                                    class="flex w-full items-center justify-center rounded-full bg-white/60 px-8 py-3 text-[14px] font-medium text-[#4A1D96] transition-all hover:bg-white focus:ring-2 focus:ring-[#7C45F5] border border-[#7C45F5]/10">
                                    Пропустить этот шаг
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Original Profile Settings View -->
                <div class="px-8 max-md:px-5 mt-4 w-full max-w-[800px] mx-auto">
                    {!! view_render_event('bagisto.shop.customers.account.profile.email.after') !!}

                    <!-- Passkeys & Trusted Devices -->
                    <div class="mt-8">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4 px-1">
                            Passkeys & Безопасность
                        </h3>

                        <div class="bg-zinc-50 border border-zinc-100 rounded-[20px] overflow-hidden">
                            @if ($customer->passkeys->count())
                                @foreach ($customer->passkeys as $passkey)
                                    <div class="flex justify-between items-center px-5 py-4 border-b border-zinc-200/60 last:border-0 max-md:px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-zinc-200/50 rounded-lg">
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
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-100 px-2 py-0.5 rounded-md">Это устройство</span>
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
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white py-4 text-[15px] font-bold text-zinc-700 transition hover:bg-zinc-50 disabled:opacity-50">
                                    <span class="icon-add text-lg"></span>
                                    <span id="add-passkey-button-text">Привязать новое устройство</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    </div>

                    <!-- PIN Code Section -->
                    <div class="mt-8">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4 px-1">
                            Резервный ПИН-код
                        </h3>

                        <div class="bg-zinc-50 border border-zinc-100 rounded-[20px] overflow-hidden">
                            @if ($customer->pin_code)
                                <div class="px-5 py-6 text-center bg-white/50">
                                    <div class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="text-[15px] font-medium text-zinc-900 mb-1">ПИН-код установлен</p>
                                    <p class="text-xs text-zinc-500 mb-4">Вы можете использовать его для входа в Meanly Pay, если Passkey недоступен.</p>
                                    
                                    <button type="button" onclick="document.getElementById('pin-setup-modal').classList.remove('hidden')" class="text-sm font-medium text-[#7C45F5] hover:text-[#6534d4] underline-offset-4 hover:underline">
                                        Изменить ПИН-код
                                    </button>
                                </div>
                            @else
                                <div class="px-5 py-6 text-center bg-white/50">
                                    <div class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-orange-100 text-orange-500 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <p class="text-[15px] font-medium text-zinc-900 mb-1">ПИН-код не задан</p>
                                    <p class="text-xs text-zinc-500 mb-4 max-w-sm mx-auto">Установите 4 или 6-значный ПИН-код в качестве резервного способа авторизации в Meanly Pay.</p>
                                </div>
                                <div class="p-5 bg-white/50 border-t border-zinc-100">
                                    <button type="button" onclick="document.getElementById('pin-setup-modal').classList.remove('hidden')"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white py-4 text-[15px] font-bold text-zinc-700 transition hover:bg-zinc-50">
                                        <span class="icon-add text-lg"></span>
                                        <span>Создать ПИН-код</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- PIN Setup Modal -->
                    <div id="pin-setup-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden relative" onclick="event.stopPropagation()">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-zinc-900">{{ $customer->pin_code ? 'Изменение ПИН-кода' : 'Новый ПИН-код' }}</h3>
                                    <button type="button" onclick="document.getElementById('pin-setup-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600 transition">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                
                                <form action="{{ route('shop.customers.account.passkeys.pin.store') }}" method="POST" class="flex flex-col gap-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 mb-2">Введите 4 или 6 цифр</label>
                                        <input type="password" name="pin_code" id="pin_code_input" inputmode="numeric" pattern="[0-9]{4,6}" autocomplete="off" maxlength="6" class="w-full text-center tracking-[1em] text-3xl font-mono py-4 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-[#7C45F5]/50 focus:border-[#7C45F5] outline-none transition" required placeholder="••••">
                                        <p class="text-xs text-zinc-500 mt-2 text-center">ПИН-код должен содержать только цифры (от 4 до 6 символов).</p>
                                    </div>
                                    <div class="mt-4 flex gap-3">
                                        <button type="button" onclick="document.getElementById('pin-setup-modal').classList.add('hidden')" class="flex-1 py-3.5 px-4 bg-zinc-100 text-zinc-700 font-medium rounded-xl hover:bg-zinc-200 transition">Отмена</button>
                                        <button type="submit" class="flex-1 py-3.5 px-4 bg-[#7C45F5] text-white font-medium rounded-xl hover:bg-[#6534d4] transition shadow-lg shadow-[#7C45F5]/20">Сохранить</button>
                                    </div>
                                </form>
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
                            alert(errorData.message || 'Ошибка сохранения Passkey');
                        }
                    } catch (error) {
                        console.error('Passkey registration error:', error);
                        alert('Ошибка: ' + error.message);
                    } finally {
                        if (button) button.disabled = false;
                        if (buttonText) buttonText.innerText = originalText;
                    }
                };
            </script>
        @endpush
