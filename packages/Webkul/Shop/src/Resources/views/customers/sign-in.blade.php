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
                <button type="button" @click="handleLogin" :disabled="isLoading"
                    class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-14 font-black uppercase tracking-[0.2em] text-sm transition-all hover:bg-[#8A5CF7] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden mb-6">
                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                    <svg v-if="!isLoading" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                    </svg>
                    <span v-if="isLoading" class="animate-pulse">@{{ loadingStatus }}</span>
                    <span v-else>Использовать Passkey</span>
                </button>

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
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof app === 'undefined') return;

                app.component('v-login-wizard', {
                    template: '#v-login-wizard-template',
                    props: ['loginOptionsUrl', 'loginUrl', 'homeUrl', 'registerUrl', 'recoveryUrl'],
                    data() {
                        return {
                            isLoading: false,
                            loadingStatus: 'Подготовка...'
                        }
                    },
                    methods: {
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
                });
            });
        </script>
    @endPushOnce
</x-shop::layouts.auth>