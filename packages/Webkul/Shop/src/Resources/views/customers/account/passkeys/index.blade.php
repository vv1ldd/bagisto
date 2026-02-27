<x-shop::layouts.account :show-back="!(isset($isCompleteRegistration) && $isCompleteRegistration)"
    :show-profile-card="!(isset($isCompleteRegistration) && $isCompleteRegistration)">
    <!-- Page Title -->
    <x-slot:title>
        @if (isset($isCompleteRegistration) && $isCompleteRegistration)
            Добавление Passkey
        @else
            Способы входа
        @endif
        </x-slot>

        <!-- Breadcrumbs -->
        @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
        <x-shop::breadcrumbs name="profile" />
        @endSection
        @endif

        <div
            class="flex-auto p-8 max-md:p-5 pt-4 {{ (isset($isCompleteRegistration) && $isCompleteRegistration) ? 'mt-[5vh]' : '' }}">
            @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                <div class="max-w-[600px] mx-auto w-full">
                    <div
                        class="rounded-[2.5rem] bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-10 md:p-14 flex flex-col items-center text-center relative overflow-hidden">
                        <!-- Decorative background elements -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/5 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#7C45F5]/5 rounded-full blur-3xl"></div>

                        <!-- Logo -->
                        <div
                            class="bg-white rounded-3xl shadow-sm flex items-center justify-center mb-10 border border-[#E9E1FF]/50 p-6 relative z-10">
                            <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                                alt="{{ config('app.name') }}" width="140" class="h-auto">
                        </div>

                        <h3 class="text-[#4A1D96] text-3xl font-extrabold mb-6 tracking-tight">Безопасный вход</h3>

                        <div class="space-y-4 mb-10">
                            <p class="text-[#4A1D96]/90 text-[17px] leading-relaxed max-w-[480px]">
                                Добавьте Passkey для мгновенного входа с этого устройства.
                            </p>
                            <p
                                class="text-[#4A1D96]/70 text-sm leading-relaxed max-w-[400px] bg-white/40 p-4 rounded-2xl border border-[#E9E1FF]/40">
                                <span class="block font-bold text-[#7C45F5] mb-1">Важно знать:</span>
                                Для входа с другого устройства вы всегда можете использовать <strong>Magic Link</strong>,
                                который придет вам на почту.
                            </p>
                        </div>
            @endif

                    <!-- Profile Information (Apple Settings Style) -->
                    <div
                        class="{{ (isset($isCompleteRegistration) && $isCompleteRegistration) ? 'w-full px-0' : 'px-8 max-md:px-5 mt-4' }}">

                        {!! view_render_event('bagisto.shop.customers.account.profile.email.after') !!}

                        <!-- Passkeys & Trusted Devices -->
                        <div
                            class="{{ (isset($isCompleteRegistration) && $isCompleteRegistration) ? 'mb-10 text-center w-full' : 'mt-8' }}">
                            <h3
                                class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-4 px-1 {{ (isset($isCompleteRegistration) && $isCompleteRegistration) ? 'hidden' : '' }}">
                                Passkeys & Безопасность
                            </h3>

                            <div
                                class="bg-zinc-50 border border-zinc-100 rounded-[20px] overflow-hidden {{ (isset($isCompleteRegistration) && $isCompleteRegistration) ? 'bg-white shadow-sm border-[#E9E1FF]/50' : 'bg-zinc-50' }}">
                                @if ($customer->passkeys->count())
                                    @foreach ($customer->passkeys as $passkey)
                                        <div
                                            class="flex justify-between items-center px-5 py-4 border-b border-zinc-200/60 last:border-0 max-md:px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-zinc-200/50 rounded-lg">
                                                    <svg class="h-5 w-5 text-zinc-600" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2">
                                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                                    </svg>
                                                </div>
                                                <div class="text-left">
                                                    <p class="text-[15px] font-medium text-zinc-900 max-md:text-sm">
                                                        {{ $passkey->name ?: 'Passkey устройство' }}
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

                        @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                                    <div class="flex justify-center w-full mt-10">
                                        <a href="{{ route('shop.customers.account.index') }}"
                                            class="relative inline-flex items-center justify-center rounded-2xl px-24 py-5 text-center text-[17px] font-bold text-white transition-all duration-300 transform active:scale-95 group overflow-hidden"
                                            style="background: linear-gradient(135deg, #7C45F5 0%, #9061FF 100%); box-shadow: 0 12px 30px rgba(124, 69, 245, 0.35), inset 0 2px 4px rgba(255, 255, 255, 0.2);">
                                            <span class="relative z-10 tracking-wide">Завершить</span>
                                            <div
                                                class="absolute inset-0 translate-y-[100%] bg-gradient-to-br from-[#6534d4] to-[#7C45F5] transition-transform duration-300 group-hover:translate-y-0">
                                            </div>
                                        </a>
                                    </div>
                                </div> <!-- End premium container content -->
                            </div> <!-- End premium registration wrapper -->
                        @endif

                {!! view_render_event('bagisto.shop.customers.account.profile.delete.before') !!}
                {!! view_render_event('bagisto.shop.customers.account.profile.delete.after') !!}
            </div>
        </div>
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
                            window.location.reload();
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
</x-shop::layouts.account>