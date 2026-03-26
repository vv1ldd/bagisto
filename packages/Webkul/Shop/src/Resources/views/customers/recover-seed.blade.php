@php
    $wordlist = app(\Webkul\Customer\Services\MnemonicService::class)->getWordlist();
@endphp

<x-shop::layouts.auth>
    <x-slot:title>Восстановление доступа</x-slot>

    {{-- Google Font for Code Mode --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        .code-input {
            font-family: 'Roboto Mono', monospace !important;
            letter-spacing: 0.5em !important;
            padding-left: 0.5em !important;
            text-transform: uppercase;
        }
    </style>

    {{-- Inline Vue Component for the Wizard --}}
    <v-recovery-wizard 
        :wordlist='@json($wordlist)'
        :old-words='@json(old("words"))'
        action-url="{{ route('shop.customers.recovery.seed.post') }}"
        csrf-token="{{ csrf_token() }}"
    ></v-recovery-wizard>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-recovery-wizard-template">
            <div class="animate-in fade-in slide-in-from-bottom-4 duration-1000">
                
                <!-- Flash Messages -->
                <div v-if="flashError" class="mb-8 p-4 bg-[#FF4D6D]/10 border border-[#FF4D6D]/20 rounded-xl text-[#FF4D6D] text-[10px] font-black uppercase tracking-widest text-center">
                    <span v-text="flashError"></span>
                </div>

                @if (session()->has('error'))
                    <div class="mb-8 p-4 bg-[#FF4D6D]/10 border border-[#FF4D6D]/20 rounded-xl text-[#FF4D6D] text-[10px] font-black uppercase tracking-widest text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Progress Header -->
                <div class="mb-8" v-if="currentStep > 0 && currentStep <= totalSteps">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <p class="text-[8px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-1.5 leading-none">Security / Recovery</p>
                            <h2 class="text-2xl font-black text-white uppercase tracking-tighter leading-none">Слово <span v-text="currentStep" class="text-[#7C45F5]"></span> <span class="text-white/20 px-1">/</span> <span v-text="totalSteps" class="text-white/40"></span></h2>
                        </div>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-[#7C45F5] transition-all duration-700 ease-out shadow-[0_0_15px_rgba(124,69,245,0.5)]"
                             :style="{ width: (currentStep / totalSteps * 100) + '%' }"></div>
                    </div>
                </div>

                <!-- Step 0: Length Selection Header -->
                <div class="mb-8 text-center" v-if="currentStep === 0">
                    <p class="text-[8px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-3">Mnemonic Setup</p>
                    <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-3 leading-none text-center">Длина<br>Фразы</h2>
                    <p class="text-zinc-500 font-bold text-[9px] uppercase tracking-widest">Выберите количество слов в вашей фразе</p>
                </div>

                <!-- Final Confirmation Header -->
                <div class="mb-10 text-center" v-if="currentStep > totalSteps">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl mb-8 group">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-tighter leading-none mb-3">Проверьте<br>Фразу</h2>
                    <p class="text-zinc-500 font-bold text-[10px] uppercase tracking-widest">Убедитесь, что все слова введены верно</p>
                </div>

                <!-- Wizard Form -->
                <form :action="actionUrl" method="POST" @submit="handleSubmit" ref="recoveryForm">
                    <input type="hidden" name="_token" :value="csrfToken">
                    
                    <div v-for="(word, index) in words" :key="'hidden-'+index">
                        <input type="hidden" name="words[]" :value="word">
                    </div>

                    <!-- Step 0: Length Selection -->
                    <div v-if="currentStep === 0" class="flex flex-col gap-3 animate-in fade-in zoom-in-95 duration-500">
                        <button v-for="len in [12, 15, 18, 21, 24]" :key="len"
                            @click="selectLength(len)"
                            type="button"
                            class="group w-full bg-white/5 border border-white/10 h-16 rounded-2xl flex justify-between items-center px-8 hover:bg-[#7C45F5]/10 hover:border-[#7C45F5]/30 transition-all active:scale-[0.98]">
                            <span class="text-sm font-black text-white uppercase tracking-widest" v-text="len + ' СЛОВ'"></span>
                            <div class="w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-[#7C45F5] group-hover:text-white transition-all text-white/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path d="M5 12h14m-7-7l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Word Input Section -->
                    <div v-if="currentStep > 0 && currentStep <= totalSteps" class="min-h-[220px] flex flex-col items-center justify-center animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <div class="relative w-full">
                            <input 
                                type="text"
                                v-model="inputWord"
                                @input="handleInput"
                                @keydown="handleKeydown"
                                ref="wordInput"
                                placeholder="..."
                                class="w-full bg-transparent border-b-2 border-white/10 py-4 text-3xl font-black text-center text-white placeholder:text-white/5 focus:border-[#7C45F5] outline-none transition-all code-input"
                                autocomplete="off"
                                autofocus
                            >

                            <!-- Autocomplete Suggestions -->
                            <div v-if="suggestions.length > 0" 
                                 class="absolute left-0 right-0 top-full mt-10 flex flex-wrap justify-center gap-2 z-30">
                                <button v-for="sWord in suggestions" 
                                    @click="selectWord(sWord)"
                                    type="button"
                                    class="bg-white/5 border border-white/10 px-4 py-2.5 rounded-xl text-[11px] font-black text-white hover:bg-[#7C45F5] hover:border-[#7C45F5] transition-all shadow-xl uppercase tracking-widest active:scale-[0.95]">
                                    <span v-text="sWord"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Final Confirmation Grid -->
                    <div v-if="currentStep > totalSteps" 
                         class="grid gap-3 mb-12 animate-in zoom-in-95 duration-500"
                         :class="totalSteps > 12 ? 'grid-cols-2' : 'grid-cols-2'">
                        <div v-for="(word, i) in words" :key="i" 
                             @click="jumpToStep(i+1)"
                             class="group cursor-pointer">
                            <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-3 rounded-xl hover:border-[#7C45F5]/50 hover:bg-[#7C45F5]/5 transition-all group-hover:scale-[1.02]">
                                <span class="text-[9px] font-black text-white/20 w-4 pb-0.5" v-text="i+1"></span>
                                <span class="text-xs font-black text-white uppercase tracking-widest truncate" v-text="word"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center gap-4" v-if="currentStep > 0">
                        <button @click="prevStep" type="button"
                            class="h-14 flex-1 bg-white/5 border border-white/10 rounded-2xl text-[11px] font-black uppercase tracking-widest text-zinc-400 hover:text-white hover:bg-white/10 transition-all active:scale-[0.98]">
                            Назад
                        </button>
                        
                        <button v-if="currentStep > 0 && currentStep <= totalSteps" @click="nextStep" type="button"
                            :disabled="!isWordValid"
                            class="h-14 flex-[2] bg-white text-zinc-900 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all hover:bg-zinc-100 shadow-xl active:scale-[0.98] disabled:opacity-20 disabled:scale-[1]">
                            Далее
                        </button>

                        <button v-if="currentStep > totalSteps" type="submit"
                            class="h-14 flex-[2] bg-[#7C45F5] text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all hover:bg-[#8A5CF7] shadow-lg shadow-[#7C45F5]/20 active:scale-[0.98]">
                            Восстановить
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center" v-if="currentStep <= totalSteps">
                    <a href="{{ route('shop.customer.session.index') }}"
                        class="text-[8px] font-black uppercase tracking-[0.4em] text-zinc-600 hover:text-white transition-colors">
                        Отмена
                    </a>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-recovery-wizard', {
                template: '#v-recovery-wizard-template',
                props: ['wordlist', 'actionUrl', 'csrfToken', 'oldWords'],
                
                data() {
                    return {
                        currentStep: 0,
                        totalSteps: 24,
                        words: [],
                        inputWord: '',
                        suggestions: [],
                        flashError: ''
                    }
                },

                mounted() {
                    if (this.oldWords && Array.isArray(this.oldWords)) {
                        const filledWords = this.oldWords.filter(w => w && w.trim() !== '');
                        if (filledWords.length > 0) {
                            const validLengths = [12, 15, 18, 21, 24];
                            let bestLen = filledWords.length;
                            for (let l of validLengths) {
                                if (filledWords.length <= l) {
                                    bestLen = l;
                                    break;
                                }
                            }
                            this.totalSteps = bestLen;
                            this.words = Array(bestLen).fill('');
                            this.oldWords.forEach((w, i) => { if (i < bestLen) this.words[i] = w || ''; });
                            this.currentStep = bestLen + 1;
                        }
                    }
                },

                computed: {
                    isWordValid() {
                        const val = this.inputWord.toLowerCase().trim();
                        return this.wordlist.includes(val);
                    }
                },

                methods: {
                    selectLength(len) {
                        this.totalSteps = len;
                        this.words = Array(len).fill('');
                        this.currentStep = 1;
                        this.$nextTick(() => { if(this.$refs.wordInput) this.$refs.wordInput.focus(); });
                    },

                    handleInput() {
                        const val = this.inputWord.toLowerCase().trim();
                        if (val.length >= 2) {
                            this.suggestions = this.wordlist.filter(w => w.startsWith(val)).slice(0, 5);
                        } else {
                            this.suggestions = [];
                        }
                    },

                    selectWord(word) {
                        this.inputWord = word;
                        this.suggestions = [];
                        this.nextStep();
                    },

                    handleKeydown(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            if (this.suggestions.length > 0) {
                                this.selectWord(this.suggestions[0]);
                            } else if (this.isWordValid) {
                                this.nextStep();
                            }
                        } else if (e.key === 'Backspace' && this.inputWord === '' && this.currentStep > 1) {
                            e.preventDefault();
                            this.prevStep();
                        }
                    },

                    jumpToStep(step) {
                        this.currentStep = step;
                        this.inputWord = this.words[step - 1];
                        this.suggestions = [];
                        this.$nextTick(() => { if(this.$refs.wordInput) this.$refs.wordInput.focus(); });
                    },

                    nextStep() {
                        if (!this.isWordValid) return;
                        this.words[this.currentStep - 1] = this.inputWord.toLowerCase().trim();
                        if (this.currentStep <= this.totalSteps) {
                            this.currentStep++;
                            if (this.currentStep <= this.totalSteps) {
                                this.inputWord = this.words[this.currentStep - 1]; 
                                this.suggestions = [];
                                this.$nextTick(() => { if(this.$refs.wordInput) this.$refs.wordInput.focus(); });
                            }
                        }
                    },

                    prevStep() {
                        if (this.currentStep > 0) {
                            if (this.currentStep <= this.totalSteps) {
                                this.words[this.currentStep - 1] = this.inputWord.toLowerCase().trim();
                            }
                            this.currentStep--;
                            if (this.currentStep > 0 && this.currentStep <= this.totalSteps) {
                                this.inputWord = this.words[this.currentStep - 1];
                                this.suggestions = [];
                                this.$nextTick(() => { if(this.$refs.wordInput) this.$refs.wordInput.focus(); });
                            }
                        }
                    },

                    handleSubmit(e) {
                        if (this.words.some(w => !w)) {
                            e.preventDefault();
                            this.flashError = 'Пожалуйста, заполните все слова вашей фразы.';
                        }
                    }
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts.auth>
