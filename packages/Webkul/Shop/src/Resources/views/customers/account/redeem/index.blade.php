<x-shop::layouts.account>
    <x-slot:title>
        Активация ваучера
    </x-slot>

    <v-redeem-app
        email="{{ auth()->guard('customer')->user()->email ?? '' }}"
        first_name="{{ auth()->guard('customer')->user()->first_name ?? '' }}"
        last_name="{{ auth()->guard('customer')->user()->last_name ?? '' }}"
        phone="{{ auth()->guard('customer')->user()->phone ?? '' }}"
        :has_passkeys="{{ $hasPasskeys ? 'true' : 'false' }}"
    ></v-redeem-app>

    @push('scripts')
    <script type="text/x-template" id="v-redeem-app-template">
        <div class="w-full max-w-[600px] mx-auto px-4 py-2" v-cloak>
            
            <!-- Step Indicator -->
            <div class="flex items-center justify-between mb-4 px-2">
                <div v-for="step in [1, 2, 3]" :key="step" class="flex items-center flex-1 last:flex-none">
                    <div :class="{
                            'bg-[#7C45F5] text-white border-zinc-900 dark:border-white/40': currentStep >= step,
                            'bg-white dark:bg-zinc-950 text-zinc-400 dark:text-zinc-500 border-zinc-200 dark:border-white/10': currentStep < step
                        }" 
                        class="w-10 h-10 rounded-2xl border-2 flex items-center justify-center font-black transition-all duration-300 shadow-sm text-lg">
                        @{{ step }}
                    </div>
                    <div v-if="step < 3" 
                        class="h-1 flex-1 mx-4 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                        <div :style="'width: ' + (currentStep > step ? '100%' : '0%')" 
                             class="h-full bg-[#7C45F5] transition-all duration-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white dark:bg-zinc-900 border-4 border-zinc-900 dark:border-white/10 p-6 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] dark:shadow-[8px_8px_0px_0px_rgba(0,0,0,0.6)] relative overflow-hidden group">
                
                <!-- STEP 1: Code Entry -->
                <div v-if="currentStep === 1">
                    <h2 class="text-2xl font-black uppercase mb-2 tracking-tight dark:text-white">Введите ваш код</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 font-bold text-sm mb-6 uppercase tracking-wider">Введите 12-значный код ваучера W1C</p>
                    
                    <div class="relative mb-6">
                        <input type="text" 
                            v-model="redeem_form.code" 
                            @input="formatCode"
                            placeholder="W1C-XXXX-XXXX-XXXX"
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border-3 border-zinc-900 dark:border-white/20 p-5 text-2xl font-black tracking-[0.2em] dark:text-white placeholder:text-zinc-300 dark:placeholder:text-zinc-700 focus:ring-4 focus:ring-[#7C45F5]/20 focus:outline-none transition-all uppercase"
                        />
                    </div>

                    <button @click="verifyCode" 
                        :disabled="loading"
                        :class="{'opacity-50': !isValidCode && !loading}"
                        class="w-full bg-[#7C45F5] border-3 border-zinc-900 dark:border-white/10 p-5 text-white font-black uppercase tracking-widest text-lg shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] dark:shadow-[4px_4px_0px_0px_rgba(0,0,0,0.4)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="!loading">Проверить код</span>
                        <span v-if="loading" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            @{{ loadingText || 'Загрузка...' }}
                        </span>
                    </button>
                </div>

                <!-- STEP 2: Email & PIN -->
                <div v-if="currentStep === 2">
                    <button @click="currentStep = 1" class="mb-4 text-zinc-400 font-bold text-xs uppercase hover:text-[#7C45F5] transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M15 19l-7-7 7-7"/></svg> Назад
                    </button>
                    <h2 class="text-2xl font-black uppercase mb-2 tracking-tight dark:text-white">Подтверждение</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 font-bold text-sm mb-6 uppercase tracking-wider">Мы отправим код подтверждения на ваш Email</p>

                    <div class="space-y-4 mb-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-black uppercase text-zinc-400">Ваш Email</label>
                                <button v-if="pinSent" 
                                    @click="pinSent = false; redeem_form.verification_code = ''" 
                                    class="text-[10px] font-black uppercase text-[#7C45F5] hover:underline transition-all">
                                    Изменить
                                </button>
                            </div>
                            <input type="email" 
                                v-model="redeem_form.email" 
                                :disabled="pinSent"
                                class="w-full bg-zinc-50 dark:bg-zinc-950 border-3 border-zinc-900 dark:border-white/20 dark:text-white p-4 font-black focus:outline-none transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                            />
                        </div>

                        <div v-if="pinSent">
                            <label class="block text-xs font-black uppercase text-zinc-400 mb-2">Код подтверждения (6 цифр)</label>
                            <input type="text" 
                                v-model="redeem_form.verification_code" 
                                maxlength="6"
                                class="w-full bg-zinc-50 dark:bg-zinc-950 border-3 border-zinc-900 dark:border-white/20 dark:text-white p-4 font-black tracking-[1em] text-center focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <button v-if="!pinSent" @click="sendPin" 
                            :disabled="loading || !redeem_form.email"
                            class="w-full bg-zinc-900 dark:bg-zinc-800 border-3 border-zinc-900 dark:border-white/10 p-5 text-white font-black uppercase tracking-widest text-lg shadow-[4px_4px_0px_0px_rgba(124,69,245,1)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                            Отправить PIN на Email
                        </button>
                    </div>

                    <button v-if="pinSent" @click="verifyPin" 
                        :disabled="loading || redeem_form.verification_code.length < 6"
                        class="w-full bg-[#7C45F5] border-3 border-zinc-900 dark:border-white/10 p-5 text-white font-black uppercase tracking-widest text-lg shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                        Продолжить
                    </button>
                </div>

                <!-- STEP 3: Activation -->
                <div v-if="currentStep === 3">
                    <h2 class="text-2xl font-black uppercase mb-2 tracking-tight dark:text-white">Готовы к активации</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 font-bold text-sm mb-8 uppercase tracking-wider leading-relaxed">Нажмите кнопку ниже, чтобы моментально зачислить средства на ваш баланс.</p>

                    <button @click="activate" 
                            :disabled="loading"
                            class="w-full bg-[#00FF85] hover:bg-[#00E677] text-black font-black py-5 uppercase tracking-widest text-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-1 active:translate-y-1 transition-all flex items-center justify-center gap-3">
                        <span v-if="loading" class="w-5 h-5 border-3 border-black border-t-transparent rounded-full animate-spin"></span>
                        <span v-else>Активировать сейчас</span>
                    </button>
                </div>

                <!-- Error Banner -->
                <div v-if="error" class="mt-6 p-4 bg-red-50 dark:bg-red-950/40 border-2 border-zinc-900 dark:border-red-500/30 text-red-600 dark:text-red-400 font-black text-xs uppercase tracking-tight flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>@{{ error }}</span>
                </div>

                <!-- Patience Hint -->
                <div v-if="loading && showPatienceHint" class="mt-4 p-4 bg-blue-50 dark:bg-blue-950/30 border-2 border-dashed border-blue-200 dark:border-blue-500/20 text-blue-600 dark:text-blue-400 font-bold text-[10px] uppercase tracking-widest text-center animate-pulse">
                    Активация может занять до минуты. Пожалуйста, не закрывайте страницу.
                </div>

                <!-- Success Screen -->
                <div v-if="currentStep === 4" class="text-center py-8">
                    <div class="w-20 h-20 bg-[#00FF94] border-4 border-zinc-900 dark:border-white/20 rounded-full mx-auto mb-6 flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        <svg class="w-10 h-10 text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-3xl font-black uppercase mb-4 tracking-tighter dark:text-white">Успешно!</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 font-bold mb-8 uppercase tracking-wider leading-relaxed">Заявка на активацию отправлена. Мы сообщим вам о результате в Telegram и на Email.</p>
                    <a href="{{ route('shop.customers.account.index') }}" class="inline-block bg-zinc-900 dark:bg-white dark:text-zinc-900 px-8 py-4 font-black uppercase tracking-widest text-sm shadow-[4px_4px_0px_0px_rgba(124,69,245,1)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all">Вернуться в кабинет</a>
                </div>

            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-redeem-app', {
            template: '#v-redeem-app-template',
            
            props: ['email', 'first_name', 'last_name', 'phone', 'has_passkeys'],

            data() {
                return {
                    currentStep: 1,
                    loading: false,
                    error: null,
                    pinSent: false,
                    loadingText: '',
                    showPatienceHint: false,
                    patienceTimer: null,
                    statusInterval: null,
                    redeem_form: {
                        code: '',
                        email: this.email || '',
                        verification_code: '',
                        // Profile data no longer required for activation
                    }
                }
            },

            computed: {

                isValidCode() {
                    return /^W1C-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/.test(this.redeem_form.code);
                }
            },

            watch: {
                currentStep(newStep) {
                    this.$nextTick(() => {
                        const firstInput = this.$el.querySelector('input');
                        if (firstInput) firstInput.focus();
                    });
                },

                loading(isLoading) {
                    if (isLoading) {
                        this.startSwissWatch();
                    } else {
                        this.stopSwissWatch();
                    }
                }
            },

            methods: {
                formatCode() {
                    let val = this.redeem_form.code.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    if (val.startsWith('W1C')) val = val.substring(3);
                    
                    let parts = [];
                    for (let i = 0; i < val.length && i < 12; i += 4) {
                        parts.push(val.substring(i, i + 4));
                    }
                    this.redeem_form.code = 'W1C' + (parts.length ? '-' + parts.join('-') : '');
                },

                async verifyCode() {
                    if (!this.isValidCode) {
                        this.error = 'Введите корректный 12-значный код';
                        return;
                    }

                    this.loading = true;
                    this.loadingText = 'Проверка кода...';
                    this.error = null;
                    try {
                        const res = await this.$axios.post('{{ route('shop.customers.account.redeem.verify') }}', { 
                            code: this.redeem_form.code 
                        });
                        
                        if (res.data.status === 'success') {
                            if (this.redeem_form.email) {
                                // User already has a verified email in profile, skip PIN/Passkey
                                this.redeem_form.verification_code = 'TRUSTED_USER';
                                this.currentStep = 3;
                            } else {
                                this.currentStep = 2;
                            }
                        } else {
                            this.error = res.data.message || 'Ошибка проверки кода';
                        }
                    } catch (e) {
                        this.error = e.response?.data?.message || 'Сетевая ошибка';
                    } finally {
                        this.loading = false;
                        this.loadingText = '';
                    }
                },

                async sendPin() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await this.$axios.post('{{ route('shop.customers.account.redeem.send_verification') }}', { 
                            code: this.redeem_form.code, 
                            email: this.redeem_form.email 
                        });
                        
                        if (res.data.status === 'success') {
                            this.pinSent = true;
                        } else {
                            this.error = res.data.message || 'Ошибка отправки PIN';
                        }
                    } catch (e) {
                        this.error = e.response?.data?.message || 'Сетевая ошибка';
                    } finally {
                        this.loading = false;
                    }
                },

                verifyPin() {
                    if (this.redeem_form.verification_code.length === 6) {
                        this.currentStep = 3;
                    } else {
                        this.error = 'Введите 6-значный код';
                    }
                },


                async authenticatePasskey() {
                    this.loading = true;
                    this.error = null;

                    try {
                        // 1. Get Authentication Options
                        const optionsRes = await this.$axios.post('{{ route('passkeys.login-options') }}');
                        const options = optionsRes.data;

                        // 2. Decode options from Base64 to ArrayBuffer (if needed by browser)
                        if (options.challenge) {
                            options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
                        }
                        if (options.allowCredentials) {
                            options.allowCredentials = options.allowCredentials.map(c => ({
                                ...c,
                                id: Uint8Array.from(atob(c.id.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0))
                            }));
                        }

                        // 3. Trigger Browser Biometrics
                        const credential = await navigator.credentials.get({ publicKey: options });

                        // 4. Encode credential to JSON for server
                        const credentialJson = {
                            id: credential.id,
                            rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                            type: credential.type,
                            response: {
                                authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                                userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '') : null
                            }
                        };

                        // 5. Verify Passkey with Server
                        const verifyRes = await this.$axios.post('{{ route('passkeys.login') }}', {
                            start_authentication_response: credentialJson
                        });

                        if (verifyRes.data.redirect_url) {
                            // Verification successful! Skip PIN and proceed.
                            this.redeem_form.verification_code = 'PASSKEY_AUTH';
                            this.currentStep = 3;
                        }
                    } catch (e) {
                        console.error('Passkey authentication error:', e);
                        this.error = 'Аутентификация не удалась или была отменена';
                    } finally {
                        this.loading = false;
                    }
                },

                async activate() {
                    this.loading = true;
                    this.error = null;
                    
                    try {
                        const res = await this.$axios.post('{{ route('shop.customers.account.redeem.activate') }}', {
                            code: this.redeem_form.code,
                            email: this.redeem_form.email,
                            verification_code: this.redeem_form.verification_code
                        });
                        
                        if (res.data.status === 'success') {
                            this.currentStep = 4;
                        } else {
                            this.error = res.data.message || 'Ошибка активации';
                        }
                    } catch (e) {
                        this.error = e.response?.data?.message || 'Сетевая ошибка';
                    } finally {
                        this.loading = false;
                    }
                },

                startSwissWatch() {
                    this.showPatienceHint = false;
                    
                    // Show patience hint after 15s
                    this.patienceTimer = setTimeout(() => {
                        this.showPatienceHint = true;
                    }, 15000);

                    // Dynamic status messages
                    const statuses = [
                        'Проверка данных...',
                        'Связь с сервером...',
                        'Авторизация транзакции...',
                        'Финальная активация...',
                        'Синхронизация баланса...'
                    ];
                    let i = 0;
                    this.loadingText = statuses[0];
                    
                    this.statusInterval = setInterval(() => {
                        i = (i + 1) % statuses.length;
                        this.loadingText = statuses[i];
                    }, 4000);
                },

                stopSwissWatch() {
                    clearTimeout(this.patienceTimer);
                    clearInterval(this.statusInterval);
                    this.showPatienceHint = false;
                    this.loadingText = '';
                }
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        [v-cloak] { display: none !important; }
    </style>
    @endpush
</x-shop::layouts.account>
