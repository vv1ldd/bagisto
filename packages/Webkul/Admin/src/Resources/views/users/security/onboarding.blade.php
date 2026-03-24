<x-admin::layouts.anonymous>
    <x-slot:title>
        Безопасность учетной записи
    </x-slot>

    <div class="flex h-screen items-center justify-center bg-gray-100 dark:bg-gray-950">
        <div class="w-full max-w-[500px] px-4">
            <div class="rounded-2xl bg-white p-8 shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20">
                        <span class="icon-security text-4xl"></span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Безопасность учетной записи</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Для продолжения работы необходимо настроить дополнительные меры защиты.</p>
                </div>

                <v-security-onboarding></v-security-onboarding>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('admin.session.destroy') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800 dark:hover:text-white transition-all" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Выйти из системы
                </a>

                <form id="logout-form" action="{{ route('admin.session.destroy') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-security-onboarding-template">
            <div>
                <!-- Step 1: Passkey -->
                <div v-if="step === 'passkey'">
                    <div class="flex flex-col gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Шаг 1: Добавьте Passkey
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Использование Passkey позволяет входить в систему без пароля, используя биометрию или ключ безопасности. Это самый надежный способ защиты.
                            </p>
                        </div>

                        <button 
                            @click="registerPasskey"
                            :disabled="isRegistering"
                            class="primary-button w-full py-3 text-base flex justify-center items-center gap-2"
                        >
                            <span v-if="isRegistering" class="icon-shimmer animate-spin"></span>
                            <span>Добавить Passkey</span>
                        </button>

                        <div v-if="passkeyError" class="mt-2 p-3 rounded-lg bg-red-50 text-red-600 text-sm border border-red-100">
                            @{{ passkeyError }}
                        </div>
                    </div>
                </div>

                <!-- Step 2: Mnemonic -->
                <div v-if="step === 'mnemonic'">
                    <div class="flex flex-col gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Шаг 2: Seed-фраза
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Сгенерируйте секретную фразу для восстановления доступа и работы с крипто-операциями. 
                                <strong class="text-gray-800 dark:text-white">Обязательно сохраните её в надежном месте!</strong>
                            </p>
                        </div>

                        <!-- Generation -->
                        <div v-if="!mnemonicGenerated">
                            <button 
                                @click="generateMnemonic"
                                :disabled="isGenerating"
                                class="primary-button w-full py-3 text-base flex justify-center items-center gap-2"
                            >
                                <span v-if="isGenerating" class="icon-shimmer animate-spin"></span>
                                <span>Сгенерировать фразу</span>
                            </button>
                        </div>

                        <!-- Verification -->
                        <div v-else class="flex flex-col gap-4">
                            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 text-center font-mono text-lg tracking-wide text-blue-800 dark:text-blue-300 select-all">
                                @{{ mnemonic }}
                            </div>

                            <p class="text-[12px] text-gray-500 text-center italic">Скопируйте и сохраните фразу, затем введите её ниже для подтверждения.</p>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Введите фразу для подтверждения</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="verify_mnemonic"
                                    v-model="mnemonicVerification"
                                    placeholder="Введите фразы через пробел"
                                />
                            </x-admin::form.control-group>

                            <button 
                                @click="verifyMnemonic"
                                :disabled="isVerifying || !mnemonicVerification"
                                class="primary-button w-full py-3 text-base"
                            >
                                <span v-if="isVerifying" class="icon-shimmer animate-spin"></span>
                                Подтвердить и завершить
                            </button>

                            <div v-if="verificationError" class="p-3 rounded-lg bg-red-50 text-red-600 text-sm border border-red-100">
                                @{{ verificationError }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success -->
                <div v-if="step === 'success'" class="text-center py-4">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-600 dark:bg-green-900/20">
                        <span class="icon-done text-4xl"></span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Готово!</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Настройка безопасности завершена успешно.</p>
                    
                    <button @click="finish" class="primary-button w-full py-3 text-base mt-6">
                        Перейти в панель управления
                    </button>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-security-onboarding', {
                template: '#v-security-onboarding-template',

                data() {
                    return {
                        step: 'passkey',
                        isRegistering: false,
                        passkeyError: null,

                        mnemonic: '',
                        mnemonicGenerated: false,
                        mnemonicVerification: '',
                        isGenerating: false,
                        isVerifying: false,
                        verificationError: null,
                    }
                },

                methods: {
                    async registerPasskey() {
                        this.isRegistering = true;
                        this.passkeyError = null;

                        try {
                            const optionsResponse = await this.$axios.post("{{ route('admin.passkey.register_options') }}");
                            const options = optionsResponse.data;
                            
                            const credential = await SimpleWebAuthnBrowser.startRegistration(options);
                            
                            await this.$axios.post("{{ route('admin.passkey.register') }}", {
                                ...credential,
                                name: 'Default Admin Passkey'
                            });

                            this.step = 'mnemonic';
                        } catch (error) {
                            console.error(error);
                            this.passkeyError = error.response?.data?.message || 'Не удалось зарегистрировать Passkey. Попробуйте еще раз.';
                        } finally {
                            this.isRegistering = false;
                        }
                    },

                    async generateMnemonic() {
                        this.isGenerating = true;
                        try {
                            const response = await this.$axios.post("{{ route('admin.security.onboarding.generate_mnemonic') }}");
                            this.mnemonic = response.data.mnemonic;
                            this.mnemonicGenerated = true;
                        } catch (error) {
                            console.error(error);
                        } finally {
                            this.isGenerating = false;
                        }
                    },

                    async verifyMnemonic() {
                        this.isVerifying = true;
                        this.verificationError = null;
                        try {
                            await this.$axios.post("{{ route('admin.security.onboarding.verify_mnemonic') }}", {
                                mnemonic: this.mnemonicVerification.trim()
                            });
                            this.step = 'success';
                            this.mnemonic = ''; // Clear sensitive data from memory
                            this.mnemonicVerification = '';
                        } catch (error) {
                            this.verificationError = 'Неверная фраза. Пожалуйста, проверьте правильность ввода.';
                        } finally {
                            this.isVerifying = false;
                        }
                    },

                    finish() {
                        this.mnemonic = '';
                        this.mnemonicVerification = '';
                        window.location.href = "{{ route('admin.dashboard.index') }}";
                    }
                }
            });

            // Prevent browser back-forward cache (BFCache) from exposing the view
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts.anonymous>
