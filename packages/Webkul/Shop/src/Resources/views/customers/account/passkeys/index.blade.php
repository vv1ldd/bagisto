@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
@endphp

<x-shop::layouts.account :show-back="!$isCompleteRegistration" :show-profile-card="!$isCompleteRegistration"
    :has-header="!$isCompleteRegistration" :has-footer="!$isCompleteRegistration">
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

        <div class="w-full flex flex-col items-center">
            @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                <div class="w-full max-w-[540px] mx-auto z-10 relative">
                    <!-- Site Logo -->
                    <div class="flex justify-center mb-3">
                        <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg', 'shop') }}" alt="{{ config('app.name') }}" class="h-8 md:h-10 object-contain max-w-[200px]">
                    </div>

                    <div class="rounded-[2.5rem] bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-6 md:p-8 flex flex-col items-center text-center relative overflow-hidden w-full shadow-[0_8px_32px_rgba(124,69,245,0.05)] border border-white">
                        <!-- Decorative background elements -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/10 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#3B82F6]/10 rounded-full blur-3xl"></div>

                        <h3 class="text-[#4A1D96] text-[26px] md:text-3xl font-extrabold mb-2 tracking-tight leading-tight">Быстрый вход</h3>

                        <div class="space-y-2 mb-4">
                            <p class="text-[#4A1D96]/90 text-[15px] leading-relaxed max-w-[320px] mx-auto">
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
                                Продолжить с почтой (Magic Link)
                            </a>
                        </div>
                    </div> <!-- End premium registration container -->
                </div> <!-- End absolute center wrapper -->
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

                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.before') !!}
                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.after') !!}
                </div>
            @endif
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
</x-shop::layouts.account>