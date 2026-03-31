<x-shop::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

    <v-registration-wizard
        check-username-url="{{ route('shop.customers.register.check_username') }}"
        prepare-passkey-url="{{ route('shop.customers.register.passkey.prepare') }}"
        prepare-other-url="{{ route('shop.customers.register.passkey.prepare_other') }}"
        check-status-url="{{ route('shop.customers.register.check_status') }}"
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

                <!-- Action Buttons: Split into This/Other Device -->
                <div class="flex flex-col gap-4 mb-8">
                    <!-- Option 1: Local Device -->
                    <button type="button" @click="handleRegistration" :disabled="!isValid || status.checking || isRegistering"
                        class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-16 font-black uppercase tracking-[0.2em] text-[13px] transition-all hover:bg-[#8A5CF7] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden disabled:opacity-30 disabled:cursor-not-allowed">
                        <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                        <svg v-if="!isRegistering" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <circle cx="12" cy="11" r="3"></circle>
                        </svg>
                        <span v-if="isRegistering && !isModalOpen" class="animate-pulse">@{{ registrationStatus }}</span>
                        <span v-else>На этом устройстве</span>
                    </button>

                    <!-- Option 2: Other Device (QR) -->
                    <button type="button" @click="handleRegistrationOther" :disabled="!isValid || status.checking || isRegistering"
                        class="group relative flex w-full items-center justify-center gap-4 bg-white text-zinc-900 h-16 font-black uppercase tracking-[0.2em] text-[13px] transition-all hover:bg-zinc-50 border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden disabled:opacity-30 disabled:cursor-not-allowed">
                        <svg class="h-5 w-5 text-[#7C45F5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                             <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                             <rect x="7" y="7" width="3" height="3"></rect>
                             <rect x="14" y="7" width="3" height="3"></rect>
                             <rect x="7" y="14" width="3" height="3"></rect>
                             <rect x="14" y="14" width="3" height="3"></rect>
                        </svg>
                        <span>На другом (QR код)</span>
                    </button>
                </div>

                <p class="text-center text-[9px] text-zinc-600 font-black uppercase tracking-[0.1em] leading-relaxed max-w-[280px] mx-auto opacity-100">
                    Нажимая кнопку, вы подтверждаете согласие с <a href="#" class="text-zinc-900 underline underline-offset-4 decoration-zinc-900 hover:text-[#7C45F5] transition-colors decoration-2">правилами сервиса</a>.
                </p>

                <!-- QR Modal -->
                <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 animate-in fade-in duration-300">
                    <div class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm" @click="closeModal"></div>
                    <div class="relative w-full max-w-sm bg-white border-4 border-zinc-900 rounded-[2.5rem] p-10 shadow-[20px_20px_0px_0px_rgba(24,24,27,1)] overflow-hidden scale-100 animate-in zoom-in-95 duration-300">
                        <div class="text-center mb-8">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#7C45F5]/10 text-[#7C45F5] border-2 border-[#7C45F5]/20 mb-6">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black uppercase tracking-tighter mb-2 italic">@{{ nickname }}</h3>
                            <p class="text-zinc-400 text-[10px] font-black uppercase tracking-widest leading-relaxed">Отсканируйте камерой телефона для входа и создания ключа</p>
                        </div>

                        <div id="reg-qrcode" v-show="!isRegistrationComplete" class="mx-auto bg-zinc-50 p-6 border-3 border-zinc-900 rounded-3xl shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] mb-10 flex items-center justify-center w-56 h-56 overflow-hidden">
                            <div v-if="!qrUrl" class="animate-pulse text-zinc-900 font-black text-[11px] uppercase tracking-widest italic">Генерация...</div>
                        </div>

                        <!-- Success Message in Modal -->
                        <div v-if="isRegistrationComplete" class="text-center mb-10 animate-in zoom-in-50 duration-500">
                             <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-500 text-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] mb-6">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                             </div>
                             <div class="text-emerald-500 font-black text-xl uppercase tracking-tighter italic">Успешно!</div>

                             <!-- Cross-Device Info Message -->
                             <p v-if="isContinuingElsewhere" class="mt-6 px-4 text-zinc-500 text-[10px] font-black uppercase tracking-[0.1em] leading-relaxed animate-in slide-in-from-top-2 duration-700">
                                 Вы начали настройку на телефоне.<br>
                                 Вы можете завершить её там или продолжить здесь ниже.
                             </p>
                        </div>

                        <div class="space-y-4">
                            <div v-if="!isRegistrationComplete" class="flex items-center justify-center gap-3 py-2 bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-xl">
                                <div class="w-2 h-2 bg-[#7C45F5] rounded-full animate-ping"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Ожидание регистрации...</span>
                            </div>

                            <button v-if="isRegistrationComplete" @click="proceedToOnboarding" class="group relative flex w-full items-center justify-center gap-4 bg-zinc-900 text-white h-14 font-black uppercase tracking-[0.2em] text-[13px] transition-all hover:bg-black border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden">
                                <span>Продолжить</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <button v-else @click="closeModal" class="w-full py-4 text-zinc-400 hover:text-zinc-900 font-black uppercase tracking-[0.2em] text-[10px] transition-colors underline decoration-2 underline-offset-8">Отмена</button>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof app === 'undefined') return;

                app.component('v-registration-wizard', {
                    template: '#v-registration-wizard-template',
                    props: ['checkUsernameUrl', 'preparePasskeyUrl', 'prepareOtherUrl', 'checkStatusUrl', 'registerPasskeyUrl', 'onboardingUrl', 'sessionIndexUrl'],
                    data() {
                        return {
                            nickname: '',
                            isValid: false,
                            isRegistering: false,
                            isModalOpen: false,
                            isRegistrationComplete: false,
                            isContinuingElsewhere: false,
                            registrationRedirectUrl: '',
                            regToken: '',
                            markContinuingUrl: '',
                            qrUrl: '',
                            registrationStatus: 'Подготовка...',
                            status: {
                                checking: false,
                                text: '',
                                icon: '',
                                iconVisible: false,
                                textVisible: false,
                                type: ''
                            },
                            timeout: null,
                            pollInterval: null
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
                        },

                        async handleRegistrationOther() {
                            if (!this.isValid || this.isRegistering) return;

                            this.isRegistering = true;
                            this.isModalOpen = true;
                            
                            try {
                                const res = await fetch(this.prepareOtherUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ username: this.nickname.trim() })
                                });

                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Ошибка при генерации QR');

                                this.qrUrl = data.url;
                                this.regToken = data.token;
                                this.markContinuingUrl = data.mark_continuing_url;
                                
                                // Render QR Code
                                this.$nextTick(() => {
                                    const qrContainer = document.getElementById('reg-qrcode');
                                    if (qrContainer && window.QRCode) {
                                        qrContainer.innerHTML = '';
                                        new QRCode(qrContainer, {
                                            text: data.url,
                                            width: 180,
                                            height: 180,
                                            colorDark : "#18181b",
                                            colorLight : "#f9fafb",
                                            correctLevel : QRCode.CorrectLevel.H
                                        });
                                    }
                                });

                                // Start Polling
                                this.startPolling();

                            } catch (err) {
                                alert(err.message);
                                this.closeModal();
                            }
                        },

                        startPolling() {
                            this.pollInterval = setInterval(async () => {
                                try {
                                    const res = await fetch(this.checkStatusUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ 
                                            username: this.nickname.trim(),
                                            token: this.regToken,
                                            device: 'pc'
                                        })
                                    });
                                    
                                    const data = await res.json();
                                    if (data.complete) {
                                        this.isRegistrationComplete = true;
                                        this.isContinuingElsewhere = data.is_continuing_elsewhere && data.continuing_device === 'phone';
                                        this.registrationRedirectUrl = data.redirect_url;
                                        clearInterval(this.pollInterval);
                                    }
                                } catch (e) {
                                    console.warn('Polling error', e);
                                }
                            }, 2000);
                        },

                        async proceedToOnboarding() {
                            if (this.markContinuingUrl) {
                                try {
                                    await fetch(this.markContinuingUrl + (this.markContinuingUrl.includes('?') ? '&' : '?') + 'device=pc');
                                } catch (e) {
                                    console.warn('Silent failure marking pc continuation', e);
                                }
                            }
                            
                            // Small delay to ensure cookies are processed by the browser
                            setTimeout(() => {
                                window.location.href = this.registrationRedirectUrl || this.onboardingUrl;
                            }, 200);
                        },

                        closeModal() {
                            this.isModalOpen = false;
                            this.isRegistrationComplete = false;
                            this.isRegistering = false;
                            this.qrUrl = '';
                            clearInterval(this.pollInterval);
                        }
                    }
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    @endPushOnce
</x-shop::layouts.auth>