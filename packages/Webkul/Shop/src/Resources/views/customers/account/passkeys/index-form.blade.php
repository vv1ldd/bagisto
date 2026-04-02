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
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#7C45F5]/5 blur-3xl rounded-none"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-[#FF4D6D]/3 blur-3xl rounded-none"></div>

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
                <!-- Redesigned Profile Settings View -->
                <div class="mt-4 w-full">
                    {!! view_render_event('bagisto.shop.customers.account.profile.email.after') !!}
                    
                    @php
                        $isOnboarding = $isOnboarding ?? false;
                    @endphp

                    <!-- Passkeys & Trusted Devices -->
                    <div class="mb-10">
                        @if (!$isOnboarding)
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-6 px-1">
                                Passkeys & Безопасность
                            </h3>
                        @endif

                        <div class="space-y-4">
                            @if ($customer->passkeys->count())
                                @foreach ($customer->passkeys as $passkey)
                                    <div class="relative group flex items-center p-5 bg-white border-2 border-zinc-900 rounded-2xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transition-all">
                                        <div class="flex items-center gap-5 flex-1 min-w-0">
                                            <span class="w-12 h-12 flex items-center justify-center bg-zinc-50 border-2 border-zinc-900 text-zinc-900 rounded-xl shrink-0 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
                                                @php
                                                    $isMobile = stripos($passkey->user_agent, 'phone') !== false || stripos($passkey->user_agent, 'android') !== false;
                                                @endphp
                                                @if ($isMobile)
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                @endif
                                            </span>
                                            
                                            <div class="flex flex-col min-w-0">
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span class="text-zinc-900 font-black text-sm uppercase tracking-tight">{{ $passkey->name ?: 'Passkey устройство' }}</span>
                                                    @if($passkey->id == session('current_session_passkey_id') || $passkey->id == request()->cookie('current_device_passkey_id'))
                                                        <span class="bg-emerald-50 text-emerald-600 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest border border-emerald-200">это устройство</span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Добавлено: {{ $passkey->created_at->format('d.m.Y') }}</span>
                                            </div>
                                        </div>

                                        <form action="{{ route('passkeys.destroy', $passkey->id) }}" method="POST"
                                            onsubmit="return confirm('Удалить это устройство?')" class="shrink-0 flex items-center">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-zinc-400 hover:text-red-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif

                            {{-- This Device --}}
                            <button type="button" id="add-device-btn" onclick="window.startPasskeyRegistration(this)" 
                                    class="group flex items-center justify-between w-full p-5 bg-white border-3 border-zinc-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
                                <div class="flex items-center gap-5 flex-1 min-w-0">
                                    <span class="w-12 h-12 flex items-center justify-center bg-[#7C45F5] border-2 border-zinc-900 text-white rounded-xl shrink-0 transition-transform group-hover:scale-105 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                        </svg>
                                    </span>
                                    <div class="flex flex-col text-left">
                                        <span id="add-device-text" class="text-zinc-900 font-black text-sm uppercase tracking-tight">Это устройство</span>
                                        <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Привязать текущий телефон/ПК</span>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-900 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Other Device --}}
                            <button type="button" id="link-other-btn" onclick="window.showQrModal()" 
                                    class="group flex items-center justify-between w-full p-5 bg-white border-3 border-zinc-900 rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
                                <div class="flex items-center gap-5 flex-1 min-w-0">
                                    <span class="w-12 h-12 flex items-center justify-center bg-zinc-50 border-2 border-zinc-900 text-zinc-900 rounded-xl shrink-0 transition-transform group-hover:scale-105 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    <div class="flex flex-col text-left">
                                        <span class="text-zinc-900 font-black text-sm uppercase tracking-tight">Другое устройство</span>
                                        <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Перенос аккаунта через QR</span>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-900 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.before') !!}
                    {!! view_render_event('bagisto.shop.customers.account.profile.delete.after') !!}
                </div>
            @endif

        {{-- === QR Code Modal === --}}
        <div id="qr-modal" class="fixed inset-0 z-[9999] hidden">
            <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm" id="qr-modal-overlay"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="bg-white border-4 border-zinc-900 rounded-[2rem] shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] w-full max-w-sm p-8 md:p-10 flex flex-col items-center text-center pointer-events-auto transition-all duration-300 scale-95 opacity-0" id="qr-modal-content">
                    <div class="mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#7C45F5]/10 text-[#7C45F5] border-2 border-[#7C45F5]/20 mb-6">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-zinc-900 text-2xl font-black uppercase tracking-tighter mb-4">Перенос</h3>
                        <p class="text-zinc-600 text-[11px] font-black uppercase tracking-widest leading-relaxed">Отсканируйте камерой телефона, чтобы войти в аккаунт</p>
                    </div>

                    <div id="qrcode-container" class="bg-zinc-50 p-6 border-3 border-zinc-900 rounded-3xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] mb-10 flex items-center justify-center w-52 h-52 overflow-hidden grayscale">
                        <div class="animate-pulse text-zinc-900 font-black text-[12px] uppercase tracking-widest">Создание...</div>
                    </div>

                    <div class="w-full space-y-6">
                        <button id="close-qr-modal" onclick="window.hideQrModal()" class="w-full py-2 text-zinc-400 hover:text-zinc-900 font-black transition-colors text-[10px] uppercase tracking-[0.3em] underline decoration-zinc-100 decoration-2 underline-offset-8">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
            <script>
                (function() {
                    // Global alert handler for Meanly style
                    window.showAlert = function (type, title, message) {
                        if (window.app && window.app.config && window.app.config.globalProperties && window.app.config.globalProperties.$emitter) {
                            window.app.config.globalProperties.$emitter.emit('add-flash', { type, message });
                        } else {
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

                    // Show Modal
                    window.showQrModal = async function() {
                        const qrModal = document.getElementById('qr-modal');
                        const qrModalContent = document.getElementById('qr-modal-content');
                        const qrCodeContainer = document.getElementById('qrcode-container');
                        
                        if (!qrModal || !qrModalContent || !qrCodeContainer) return;

                        qrModal.classList.remove('hidden');
                        setTimeout(() => {
                            qrModalContent.classList.remove('scale-95', 'opacity-0');
                        }, 10);

                        try {
                            const res = await fetch('{{ route('shop.customers.account.passkeys.generate-link') }}');
                            const data = await res.json();
                            
                            qrCodeContainer.innerHTML = '';
                            new QRCode(qrCodeContainer, {
                                text: data.url,
                                width: 160,
                                height: 160,
                                colorDark : "#1a0050",
                                colorLight : "#ffffff",
                                correctLevel : QRCode.CorrectLevel.M
                            });
                        } catch (err) {
                            qrCodeContainer.innerHTML = '<span class="text-red-500 font-bold text-xs text-center">Ошибка<br>загрузки</span>';
                        }
                    };

                    // Close Modal
                    window.hideQrModal = function() {
                        const qrModal = document.getElementById('qr-modal');
                        const qrModalContent = document.getElementById('qr-modal-content');
                        if (!qrModal || !qrModalContent) return;
                        
                        qrModalContent.classList.add('scale-95', 'opacity-0');
                        setTimeout(() => { qrModal.classList.add('hidden'); }, 300);
                    };

                    // Close on overlay click
                    document.addEventListener('click', function(e) {
                        if (e.target.id === 'qr-modal-overlay') {
                            window.hideQrModal();
                        }
                    });

                    window.startPasskeyRegistration = async function (btnElement) {
                        const SimpleWebAuthn = window.SimpleWebAuthnBrowser;

                        if (!SimpleWebAuthn) {
                            window.showAlert('error', 'Ошибка', 'Библиотека WebAuthn не загружена. Пожалуйста, обновите страницу.');
                            return;
                        }

                        if (!window.PublicKeyCredential) {
                            window.showAlert('error', 'Ошибка', 'Ваш браузер не поддерживает Passkey (требуется HTTPS).');
                            return;
                        }

                        // Determine which button triggered it
                        const isMainButton = !(btnElement && btnElement instanceof HTMLElement);
                        const button = isMainButton ? document.getElementById('add-passkey-button') : btnElement;
                        
                        // Try to find a text span inside
                        let buttonText = isMainButton ? document.getElementById('add-passkey-button-text') : button.querySelector('#add-device-text');
                        if (!buttonText) buttonText = button;

                        const originalText = buttonText?.innerText || 'Добавить устройство';

                        if (button) button.disabled = true;
                        if (buttonText) buttonText.innerText = 'Подготовка...';

                        try {
                            const response = await fetch('{{ route('passkeys.register-options') }}', {
                                method: 'POST',
                                headers: { 
                                    'Content-Type': 'application/json', 
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) throw new Error('Не удалось получить настройки с сервера.');

                            const rawOptions = await response.json();
                            // Pass raw server options directly — do NOT modify
                            const optionsJSON = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                            if (!optionsJSON || !optionsJSON.challenge) throw new Error('Некорректные настройки.');

                            // Robust base64url conversion for Safari
                            const toBase64Url = (str) => {
                                if (!str || typeof str !== 'string') return str;
                                return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                            };

                            if (optionsJSON.challenge) optionsJSON.challenge = toBase64Url(optionsJSON.challenge);
                            if (optionsJSON.user && optionsJSON.user.id) optionsJSON.user.id = toBase64Url(optionsJSON.user.id);
                            if (optionsJSON.excludeCredentials) {
                                optionsJSON.excludeCredentials.forEach(cred => {
                                    if (cred.id) cred.id = toBase64Url(cred.id);
                                });
                            }

                            console.log('[Passkey] RP ID:', optionsJSON.rp ? optionsJSON.rp.id : 'N/A');

                            if (buttonText) buttonText.innerText = 'Ожидание устройства...';

                            // Start WebAuthn registration (v8+ format)
                            const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);


                            if (buttonText) buttonText.innerText = 'Сохранение...';

                            const saveRes = await fetch('{{ route('passkeys.register') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify(attResp)
                            });

                            if (saveRes.ok) {
                                @if (isset($isCompleteRegistration) && $isCompleteRegistration)
                                    window.location.href = '{!! route('shop.customers.account.profile.complete_registration_success') !!}';
                                @else
                                    window.location.reload();
                                @endif
                                return;
                            } else {
                                const errorData = await saveRes.json();
                                throw new Error(errorData.message || 'Ошибка сохранения Passkey');
                            }
                        } catch (error) {
                            let message = error.message;
                            let title = 'Ошибка';
                            let type = 'error';

                            if (error.name === 'NotAllowedError' || message.includes('отмена') || message.includes('cancelled') || message.includes('Отмена') || error.name === 'AbortError') {
                                title = 'Отменено';
                                message = 'Действие отменено пользователем.';
                                type = 'warning';
                            }
                            window.showAlert(type, title, message);
                        } finally {
                            if (button) button.disabled = false;
                            if (buttonText) buttonText.innerText = originalText;
                        }
                    };
                })();
            </script>
        @endpush
