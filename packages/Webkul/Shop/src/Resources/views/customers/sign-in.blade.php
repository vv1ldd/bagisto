<x-shop::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.login.before') !!}

    <v-login-wizard
        login-options-url="{{ route('passkeys.login-options') }}"
        login-url="{{ route('passkeys.login') }}"
        home-url="{{ route('shop.home.index') }}"
        register-url="{{ route('shop.customers.register.index') }}"
        recovery-url="{{ route('shop.customers.recovery.seed') }}"
    >
        <template v-slot:header>
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-zinc-900 uppercase tracking-tighter mb-2 leading-none">Вход в Meanly</h1>
                <p class="text-[11px] text-zinc-600 font-bold uppercase tracking-widest leading-relaxed">
                    Доступ через <span class="text-[#7C45F5]">Passkey</span> — мгновенно, без пароля.
                </p>
            </div>
        </template>
    </v-login-wizard>

    {!! view_render_event('bagisto.shop.customers.login.after') !!}

    @pushOnce('scripts')
        <script type="text/x-template" id="v-login-wizard-template">
            <div id="login-container" class="animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <slot name="header"></slot>

                <!-- Passkey Login Button -->
                <button type="button" @click="handleLogin" :disabled="isLoading || isQrLoading"
                    class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-14 font-black uppercase tracking-[0.2em] text-sm transition-all hover:bg-[#8A5CF7] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden mb-4">
                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                    <svg v-if="!isLoading" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                    </svg>
                    <span v-if="isLoading" class="animate-pulse">@{{ loadingStatus }}</span>
                    <span v-else>Использовать Passkey</span>
                </button>

                <!-- QR Login Button -->
                <button type="button" @click="handleQrLogin" :disabled="isLoading || isQrLoading"
                    class="group relative flex w-full items-center justify-center gap-4 bg-white text-zinc-900 h-14 font-black uppercase tracking-[0.2em] text-[10px] transition-all hover:bg-zinc-50 border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden mb-6">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Войти через другое устройство</span>
                </button>

                <!-- QR Modal -->
                <div v-if="showQrModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-300" @click.self="closeQrModal">
                    <div class="bg-white border-4 border-zinc-900 shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] w-full max-w-sm p-8 relative animate-in zoom-in-95 duration-300">
                        <button @click="closeQrModal" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-900 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        
                        <div class="text-center mb-8 relative">
                            <h3 class="text-2xl font-black uppercase tracking-tighter mb-2 italic">ВХОД ПО QR</h3>
                            <div class="h-1 w-12 bg-[#D6FF00] mx-auto border border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]"></div>
                        </div>

                        <div class="flex justify-center p-8 bg-zinc-50 border-4 border-zinc-900 mb-8 relative group">
                            <!-- Scanner Decor -->
                            <div class="absolute -top-2 -left-2 w-10 h-10 border-t-8 border-l-8 border-[#D6FF00] z-20"></div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 border-t-8 border-r-8 border-[#D6FF00] z-20"></div>
                            <div class="absolute -bottom-2 -left-2 w-10 h-10 border-b-8 border-l-8 border-[#D6FF00] z-20"></div>
                            <div class="absolute -bottom-2 -right-2 w-10 h-10 border-b-8 border-r-8 border-[#D6FF00] z-20"></div>

                            <div id="login-qrcode" class="bg-white p-4 border-2 border-zinc-900 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] transition-transform group-hover:scale-[1.02] duration-500"></div>
                            
                            <div v-if="qrStatus === 'success'" class="absolute inset-0 bg-[#7C45F5]/95 flex flex-col items-center justify-center text-white p-4 text-center z-30">
                                <svg class="w-20 h-20 mb-2 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-black uppercase tracking-widest text-xl italic">DONE!</span>
                            </div>
                            
                            <div v-if="qrStatus === 'expired'" class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center p-4 text-center z-30">
                                <span class="text-zinc-900 font-black uppercase tracking-widest text-sm mb-6 bg-[#D6FF00] px-3 py-1 border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(39,39,42,1)]">КОД ИСТЕК</span>
                                <button @click="handleQrLogin" class="text-xs font-black uppercase tracking-widest bg-zinc-900 text-white px-8 py-4 border-2 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(39,39,42,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all hover:bg-zinc-800">ОБНОВИТЬ</button>
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div :class="['w-4 h-4 rounded-full animate-pulse border-2 border-zinc-900', qrStatus === 'error' ? 'bg-red-500' : 'bg-[#D6FF00]']"></div>
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-900 italic">
                                    <span v-if="qrStatus === 'authorized'">ПОДТВЕРДИТЕ НА ТЕЛЕФОНЕ</span>
                                    <span v-else-if="qrStatus === 'pending'">ОЖИДАНИЕ СКАНЕРА...</span>
                                    <span v-else-if="qrStatus === 'error'" class="text-red-500">ОШИБКА СВЯЗИ</span>
                                    <span v-else>@{{ qrStatus }}</span>
                                </p>
                            </div>
                            
                            <p class="text-[10px] text-zinc-400 font-bold uppercase text-center leading-relaxed tracking-wider">
                                Отсканируйте код смартфоном, <br> на котором вы уже вошли в Meanly
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 w-full opacity-100">
                        <div class="h-0.5 flex-1 bg-zinc-900"></div>
                        <span class="text-[8px] font-black text-zinc-900 uppercase tracking-[0.4em]">Или</span>
                        <div class="h-0.5 flex-1 bg-zinc-900"></div>
                    </div>

                    <div class="flex flex-col items-center gap-6">
                        <a :href="registerUrl" 
                            class="w-full h-14 bg-white border-2 border-zinc-900 text-zinc-900 flex items-center justify-center gap-3 font-black uppercase tracking-widest text-[11px] rounded-2xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] transition-all hover:bg-zinc-50 active:translate-x-1 active:translate-y-1 active:shadow-none group">
                            Создать аккаунт
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path d="M5 12h14m-7-7l7 7-7 7"/>
                            </svg>
                        </a>

                        <a :href="recoveryUrl" 
                            class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-900 hover:text-[#7C45F5] transition-colors text-center leading-loose underline decoration-zinc-900 underline-offset-4 decoration-2">
                            Восстановить через секретную фразу
                        </a>
                    </div>
                </div>
            </div>
        </script>

        <script>
            window.meanlyComponents.push({
                name: 'v-login-wizard',
                definition: {
                    template: '#v-login-wizard-template',
                    props: ['loginOptionsUrl', 'loginUrl', 'homeUrl', 'registerUrl', 'recoveryUrl'],
                    data() {
                        return {
                            isLoading: false,
                            loadingStatus: 'Подготовка...',
                            // QR Login State
                            showQrModal: false,
                            isQrLoading: false,
                            qrToken: null,
                            qrUrl: null,
                            qrStatus: 'pending',
                            pollInterval: null
                        }
                    },
                    methods: {
                        async handleQrLogin() {
                            this.isQrLoading = true;
                            this.qrStatus = 'pending';
                            
                            try {
                                const res = await fetch('{{ route('shop.customer.login.qr.prepare') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });

                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Ошибка генерации кода');

                                this.qrToken = data.token;
                                this.qrUrl = data.url;
                                this.showQrModal = true;

                                this.$nextTick(() => {
                                    const container = document.getElementById('login-qrcode');
                                    if (container && window.QRCode) {
                                        container.innerHTML = '';
                                        new QRCode(container, {
                                            text: data.url,
                                            width: 320,
                                            height: 320,
                                            colorDark : "#18181b",
                                            colorLight : "#ffffff",
                                            correctLevel : QRCode.CorrectLevel.M
                                        });
                                    }
                                });

                                this.startQrPolling();
                            } catch (err) {
                                alert(err.message);
                            } finally {
                                this.isQrLoading = false;
                            }
                        },

                        startQrPolling() {
                            if (this.pollInterval) clearInterval(this.pollInterval);

                            this.pollInterval = setInterval(async () => {
                                try {
                                    const res = await fetch('{{ route('shop.customer.login.qr.check') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ token: this.qrToken })
                                    });

                                    const data = await res.json();

                                    if (!res.ok) {
                                        throw new Error(data.error || 'Server error');
                                    }

                                    this.qrStatus = data.status;

                                    if (data.status === 'success') {
                                        clearInterval(this.pollInterval);
                                        setTimeout(() => {
                                            window.location.href = data.redirect_url;
                                        }, 1000);
                                    } else if (data.status === 'expired') {
                                        clearInterval(this.pollInterval);
                                    }
                                } catch (e) {
                                    console.warn('QR Polling error', e);
                                    // If multiple errors occur, show a status to user
                                    this.qrStatus = 'ошибка...';
                                }
                            }, 2000);
                        },

                        closeQrModal() {
                            this.showQrModal = false;
                            if (this.pollInterval) clearInterval(this.pollInterval);
                        },
                        async handleLogin() {
                            const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                            if (!window.PublicKeyCredential) {
                                alert('Ваш браузер не поддерживает Passkey.');
                                return;
                            }

                            this.isLoading = true;
                            this.loadingStatus = 'Подготовка...';

                            try {
                                const response = await fetch(this.loginOptionsUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });

                                if (!response.ok) throw new Error('Ошибка связи с сервером (' + response.status + ')');

                                const rawOptions = await response.json();
                                const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                                // Robust base64url conversion for Safari
                                const toBase64Url = (str) => {
                                    if (!str || typeof str !== 'string') return str;
                                    return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                                };

                                if (options.challenge) options.challenge = toBase64Url(options.challenge);
                                if (options.allowCredentials) {
                                    options.allowCredentials.forEach(cred => {
                                        if (cred.id) cred.id = toBase64Url(cred.id);
                                    });
                                }

                                this.loadingStatus = 'Подтвердите личность...';
                                const asseResp = await SimpleWebAuthn.startAuthentication(options);
                                
                                this.loadingStatus = 'Проверка...';
                                const loginResponse = await fetch(this.loginUrl, {
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

                                if (loginResponse.ok) {
                                    const data = await loginResponse.json();
                                    window.location.href = data.redirect_url || this.homeUrl;
                                } else {
                                    const result = await loginResponse.json();
                                    throw new Error(result.message || 'Ошибка входа.');
                                }
                            } catch (err) {
                                if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                                    console.error('[Passkey Error]', err);
                                    alert(err.message);
                                }
                                this.isLoading = false;
                            }
                        }
                    }
                }
            });
        </script>
        </script>
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    @endPushOnce
</x-shop::layouts.auth>