<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Активация ваучера
    </x-slot>

    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <x-shop::breadcrumbs name="redeem" />
    </div>

    <div class="flex-auto">
        <div class="max-md:max-w-full">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6">🎁 Активация ваучера</h1>

            <v-redeem-form></v-redeem-form>
        </div>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-redeem-form-template">
            <div class="grid gap-8">
                <!-- Step 1: Voucher Code Entry -->
                <div v-if="step === 1" class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#7C45F5]/20 flex items-center justify-center border border-[#7C45F5]/30 mx-auto mb-6">
                        <span class="text-3xl">🎫</span>
                    </div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">Введите код ваучера</h2>
                    <p class="text-sm text-zinc-400 mb-8 max-w-sm mx-auto">Введите ваш код подтверждения, чтобы получить вознаграждение на свой баланс.</p>
                    
                    <div class="max-w-md mx-auto">
                        <input 
                            type="text" 
                            v-model="voucherCode"
                            placeholder="W1C-XXXX-XXXX-XXXX" 
                            class="w-full text-center bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 px-6 py-4 rounded-xl text-lg font-mono focus:outline-none focus:border-[#7C45F5] transition-all mb-4"
                            @keyup.enter="verifyCode"
                            :disabled="isLoading"
                        >
                        <p v-if="error" class="text-red-500 text-xs mb-4">@{{ error }}</p>

                        <button 
                            @click="verifyCode"
                            class="primary-button w-full !py-4 !rounded-xl text-base font-bold flex items-center justify-center gap-2"
                            :disabled="isLoading || !voucherCode"
                        >
                            <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            @{{ isLoading ? 'Проверка...' : 'Проверить код' }}
                        </button>
                    </div>
                </div>

                <!-- Step 2: Verification / Data Entry -->
                <div v-if="step === 2" class="bg-white/5 border border-white/10 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <button @click="step = 1" class="text-zinc-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Подтверждение активации</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="bg-white/5 p-5 rounded-xl border border-white/10">
                                <p class="text-[10px] uppercase tracking-widest text-zinc-500 font-bold mb-1">Код ваучера</p>
                                <p class="text-sm font-mono text-white">@{{ voucherCode }}</p>
                            </div>

                            <div v-if="voucherInfo" class="bg-[#7C45F5]/10 p-5 rounded-xl border border-[#7C45F5]/20">
                                <p class="text-[10px] uppercase tracking-widest text-[#7C45F5] font-bold mb-1">Вознаграждение</p>
                                <p class="text-xl font-bold text-white">@{{ voucherInfo.amount }} @{{ voucherInfo.currency }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div v-if="!verificationSent">
                                <p class="text-sm text-zinc-400 mb-4">Мы отправим 6-значный код для подтверждения на вашу почту:</p>
                                <div class="flex gap-2">
                                    <input 
                                        type="email" 
                                        v-model="email"
                                        class="flex-1 bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-[#7C45F5]"
                                        placeholder="email@example.com"
                                    >
                                    <button 
                                        @click="sendVerification"
                                        class="primary-button !px-6 !py-3 !rounded-xl"
                                        :disabled="isSending || !email"
                                    >
                                        <span v-if="isSending" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                        <span v-else>Код</span>
                                    </button>
                                </div>
                            </div>

                            <div v-else>
                                <form @submit.prevent="activateVoucher" class="space-y-4">
                                    <p class="text-sm text-green-400/80 mb-4 flex items-center gap-2">
                                        <span>✅</span> Код подтверждения отправлен на почту.
                                    </p>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <label class="text-[10px] text-zinc-500 font-bold uppercase ml-1">Имя</label>
                                            <input type="text" v-model="formData.first_name" placeholder="Имя" class="w-full bg-white/5 border border-white/10 px-4 py-3 rounded-xl text-sm focus:border-[#7C45F5]">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] text-zinc-500 font-bold uppercase ml-1">Фамилия</label>
                                            <input type="text" v-model="formData.last_name" placeholder="Фамилия" class="w-full bg-white/5 border border-white/10 px-4 py-3 rounded-xl text-sm focus:border-[#7C45F5]">
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-zinc-500 font-bold uppercase ml-1">Телефон</label>
                                        <input type="text" v-model="formData.phone" placeholder="+7..." class="w-full bg-white/5 border border-white/10 px-4 py-3 rounded-xl text-sm focus:border-[#7C45F5]">
                                    </div>
                                    
                                    <div class="pt-6 mt-6 border-t border-white/10">
                                        <p class="text-[10px] uppercase tracking-widest text-[#7C45F5] font-bold mb-3 text-center">Шестизначный код из письма</p>
                                        <input 
                                            type="text" 
                                            v-model="formData.verification_code" 
                                            placeholder="••••••" 
                                            maxlength="6"
                                            class="w-full text-center bg-[#7C45F5]/5 border border-[#7C45F5]/30 px-4 py-5 rounded-xl text-3xl font-bold tracking-[0.5em] focus:outline-none focus:border-[#7C45F5] placeholder:opacity-20"
                                        >
                                    </div>

                                    <button 
                                        type="submit"
                                        class="primary-button w-full !py-5 !rounded-xl text-base font-bold mt-6"
                                        :disabled="isActivating || !formData.verification_code"
                                    >
                                       <span v-if="isActivating" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2 inline-block vertical-middle"></span>
                                       @{{ isActivating ? 'Активация...' : 'Активировать вознаграждение' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div v-if="step === 3" class="bg-white/5 border border-white/10 rounded-2xl p-12 text-center">
                    <div class="w-24 h-24 rounded-full bg-green-500/20 flex items-center justify-center border border-green-500/30 mx-auto mb-8">
                        <span class="text-5xl text-green-500">✨</span>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Готово!</h2>
                    <p class="text-zinc-400 mb-10 max-w-md mx-auto line-height-relaxed">Ваш ваучер был успешно активирован. Вознаграждение зачислено на ваш внутренний счет и будет доступно в ближайшее время после подтверждения в сети.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('shop.customers.account.credits.index') }}" class="primary-button !px-10 !py-4 !rounded-xl font-bold">
                            К моим начислениям
                        </a>
                        <button @click="resetForm" class="text-sm text-zinc-500 hover:text-white transition-colors font-bold">
                            Активировать другой код
                        </button>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-redeem-form', {
                template: '#v-redeem-form-template',

                data() {
                    return {
                        step: 1,
                        voucherCode: '',
                        voucherInfo: null,
                        email: '{{ auth()->guard('customer')->user()->email }}',
                        verificationSent: false,
                        isLoading: false,
                        isSending: false,
                        isActivating: false,
                        error: '',
                        formData: {
                            first_name: '{{ auth()->guard('customer')->user()->first_name }}',
                            last_name: '{{ auth()->guard('customer')->user()->last_name }}',
                            phone: '{{ auth()->guard('customer')->user()->phone }}',
                            verification_code: '',
                        }
                    }
                },

                methods: {
                    verifyCode() {
                        if (!this.voucherCode) return;
                        
                        this.isLoading = true;
                        this.error = '';

                        this.$axios.post("{{ route('shop.customers.account.redeem.verify') }}", {
                            code: this.voucherCode
                        })
                        .then(response => {
                            this.voucherInfo = response.data;
                            this.step = 2;
                        })
                        .catch(error => {
                            this.error = error.response.data?.error || 'Неверный или просроченный код ваучера';
                            if (error.response.data?.details) {
                                console.error('API Error:', error.response.data.details);
                            }
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                    },

                    sendVerification() {
                        if (!this.email) return;

                        this.isSending = true;
                        this.$axios.post("{{ route('shop.customers.account.redeem.send_verification') }}", {
                            code: this.voucherCode,
                            email: this.email
                        })
                        .then(response => {
                            this.verificationSent = true;
                        })
                        .catch(error => {
                            window.alert('Ошибка при отправке кода подтверждения. Пожалуйста, попробуйте позже.');
                        })
                        .finally(() => {
                            this.isSending = false;
                        });
                    },

                    activateVoucher() {
                        if (!this.formData.verification_code) return;

                        this.isActivating = true;
                        this.$axios.post("{{ route('shop.customers.account.redeem.activate') }}", {
                            code: this.voucherCode,
                            ...this.formData,
                            email: this.email
                        })
                        .then(response => {
                            this.step = 3;
                        })
                        .catch(error => {
                            window.alert(error.response.data?.error || 'Ошибка активации. Проверьте введенный код.');
                        })
                        .finally(() => {
                            this.isActivating = false;
                        });
                    },

                    resetForm() {
                        this.step = 1;
                        this.voucherCode = '';
                        this.voucherInfo = null;
                        this.verificationSent = false;
                        this.formData.verification_code = '';
                    }
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts.account>
