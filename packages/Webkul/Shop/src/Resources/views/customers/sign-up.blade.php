<x-shop::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

    <v-registration-wizard
        check-username-url="{{ route('shop.customers.register.check_username') }}"
        prepare-passkey-url="{{ route('shop.customers.register.passkey.prepare') }}"
        register-passkey-url="{{ route('passkeys.register') }}"
        onboarding-url="{{ route('shop.customers.account.onboarding.security') }}"
        session-index-url="{{ route('shop.customer.session.index') }}"
    >
        <template v-slot:logo>
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-black text-zinc-900 mb-2 uppercase tracking-tighter leading-none">Создать<br>Аккаунт</h1>
                <p class="text-[11px] text-zinc-600 font-bold uppercase tracking-wider leading-relaxed">
                    Безопасность <span class="text-[#7C45F5]">нового уровня</span> с Passkey.
                </p>
            </div>
        </template>
    </v-registration-wizard>

    {!! view_render_event('bagisto.shop.customers.signup.after') !!}

    @pushOnce('scripts')
        <script type="text/x-template" id="v-registration-wizard-template">
            <div id="registration-wizard" class="animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <!-- Back Button -->
                <div class="mb-6">
                    <a :href="sessionIndexUrl" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-zinc-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-50 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all group">
                        <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M19 12H5m7-7l-7 7 7 7"/>
                        </svg>
                        Назад
                    </a>
                </div>

                <slot name="logo"></slot>

                <!-- Nickname Input -->
                <div class="w-full mb-6 group">
                    <div class="relative h-20 bg-zinc-50 border-3 border-zinc-900 rounded-2xl group-focus-within:bg-[#7C45F5]/5 transition-all duration-300 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]">
                        <div class="h-full flex items-center px-8">
                            <div class="flex-grow flex items-center justify-end relative">
                                <span class="text-zinc-300 mr-3 text-2xl select-none font-black italic">@</span>
                                <input type="text" v-model="nickname" 
                                    class="w-full text-right outline-none bg-transparent text-zinc-900 font-black text-3xl tracking-tighter placeholder:text-zinc-200 placeholder:uppercase"
                                    placeholder="Никнейм" autocomplete="off" @input="debounceCheck">
                                
                                <div class="ml-4 w-7 h-7 flex-shrink-0 flex items-center justify-center transition-opacity" :class="status.iconVisible ? 'opacity-100' : 'opacity-0'">
                                    <div v-if="status.checking" class="w-4 h-4 border-2 border-[#7C45F5] border-r-transparent rounded-full animate-spin"></div>
                                    <span v-else>@{{ status.icon }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-[9px] mt-4 text-center font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200" 
                       :class="[status.textVisible ? 'opacity-100' : 'opacity-0', status.type === 'success' ? 'text-emerald-400' : 'text-[#FF4D6D]']">
                        @{{ status.text }}
                    </p>
                </div>

                <!-- Create Button -->
                <button type="button" @click="handleRegistration" :disabled="!isValid || status.checking || isRegistering"
                    class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-16 font-black uppercase tracking-[0.2em] text-[15px] transition-all hover:bg-[#8A5CF7] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden disabled:opacity-30 disabled:cursor-not-allowed mb-8">
                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                    <svg v-if="!isRegistering" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <circle cx="12" cy="11" r="3"></circle>
                    </svg>
                    <span v-if="isRegistering" class="animate-pulse">@{{ registrationStatus }}</span>
                    <span v-else>Создать через Passkey</span>
                </button>

                <p class="text-center text-[9px] text-zinc-600 font-black uppercase tracking-[0.1em] leading-relaxed max-w-[280px] mx-auto opacity-100">
                    Нажимая кнопку, вы подтверждаете согласие с <a href="#" class="text-zinc-900 underline underline-offset-4 decoration-zinc-900 hover:text-[#7C45F5] transition-colors decoration-2">правилами сервиса</a>.
                </p>
            </div>
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof app === 'undefined') return;

                app.component('v-registration-wizard', {
                    template: '#v-registration-wizard-template',
                    props: ['checkUsernameUrl', 'preparePasskeyUrl', 'registerPasskeyUrl', 'onboardingUrl', 'sessionIndexUrl'],
                    data() {
                        return {
                            nickname: '',
                            isValid: false,
                            isRegistering: false,
                            registrationStatus: 'Подготовка...',
                            status: {
                                checking: false,
                                text: '',
                                icon: '',
                                iconVisible: false,
                                textVisible: false,
                                type: ''
                            },
                            timeout: null
                        }
                    },
                    methods: {
                        debounceCheck() {
                            clearTimeout(this.timeout);
                            this.isValid = false;
                            this.resetStatus();

                            const val = this.nickname.trim();
                            if (!val) return;

                            if (!/^[a-zA-Z0-9_\-\.]{3,30}$/.test(val)) {
                                this.showStatus('❌', 'Некорректный формат', 'error');
                                return;
                            }

                            this.status.checking = true;
                            this.status.iconVisible = true;
                            
                            this.timeout = setTimeout(this.checkUsername, 500);
                        },

                        async checkUsername() {
                            try {
                                const formData = new FormData();
                                formData.append('username', this.nickname.trim());
                                formData.append('_token', '{{ csrf_token() }}');

                                const res = await fetch(this.checkUsernameUrl, {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'Accept': 'application/json' }
                                });

                                const data = await res.json();
                                this.status.checking = false;
                                
                                if (data.available) {
                                    this.showStatus('✅', data.message, 'success');
                                    this.isValid = true;
                                } else {
                                    this.showStatus('❌', data.message, 'error');
                                }
                            } catch (e) {
                                this.status.checking = false;
                                this.showStatus('❌', 'Ошибка проверки', 'error');
                            }
                        },

                        showStatus(icon, text, type) {
                            this.status.icon = icon;
                            this.status.text = text;
                            this.status.type = type;
                            this.status.iconVisible = true;
                            this.status.textVisible = !!text;
                        },

                        resetStatus() {
                            this.status.checking = false;
                            this.status.iconVisible = false;
                            this.status.textVisible = false;
                            this.status.text = '';
                        },

                        async handleRegistration() {
                            if (!this.isValid || this.isRegistering) return;

                            const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                            if (!window.PublicKeyCredential) {
                                alert('Ваш браузер не поддерживает Passkey.');
                                return;
                            }

                            this.isRegistering = true;
                            this.registrationStatus = 'Подготовка...';

                            try {
                                const prepareRes = await fetch(this.preparePasskeyUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ username: this.nickname.trim() })
                                });

                                if (!prepareRes.ok) {
                                    const errData = await prepareRes.json();
                                    throw new Error(errData.message || 'Ошибка инициализации.');
                                }

                                const options = await prepareRes.json();
                                const optionsJSON = options.publicKey ? options.publicKey : options;

                                this.registrationStatus = 'Создайте ключ...';
                                const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);
                                
                                this.registrationStatus = 'Сохранение...';
                                const storeRes = await fetch(this.registerPasskeyUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(attResp)
                                });

                                const data = await storeRes.json();
                                window.location.href = data.redirect_url || this.onboardingUrl;

                            } catch (err) {
                                if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                                    console.error('[Passkey Error]', err);
                                    alert(err.message);
                                }
                                this.isRegistering = false;
                            }
                        }
                    }
                });
            });
        </script>
    @endPushOnce
</x-shop::layouts.auth>