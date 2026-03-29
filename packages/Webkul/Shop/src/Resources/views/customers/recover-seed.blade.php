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
                <div class="mb-10" v-if="currentStep > 0 && currentStep <= totalSteps">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-2 leading-none">Security / Recovery</p>
                            <h2 class="text-2xl font-black text-zinc-900 uppercase tracking-tighter leading-none">Слово <span v-text="currentStep" class="text-[#7C45F5]"></span> <span class="text-zinc-200 px-1">/</span> <span v-text="totalSteps" class="text-zinc-400"></span></h2>
                        </div>
                    </div>
                    <div class="h-2.5 w-full bg-zinc-100 border-2 border-zinc-900 rounded-full overflow-hidden">
                        <div class="h-full bg-[#7C45F5] transition-all duration-700 ease-out"
                             :style="{ width: (currentStep / totalSteps * 100) + '%' }"></div>
                    </div>
                </div>

                <!-- Step 0: Length Selection Header -->
                <div class="mb-10 text-center" v-if="currentStep === 0">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-4">Mnemonic Setup</p>
                    <h2 class="text-4xl font-black text-zinc-900 uppercase tracking-tighter mb-4 leading-none text-center">Длина<br>Фразы</h2>
                    <p class="text-zinc-600 font-bold text-[10px] uppercase tracking-widest">Выберите количество слов в вашей фразе</p>
                </div>

                <!-- Final Confirmation Header -->
                <div class="mb-12 text-center" v-if="currentStep > totalSteps">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-50 border-4 border-emerald-500 rounded-3xl mb-8 group shadow-[6px_6px_0px_0px_rgba(16,185,129,0.3)]">
                        <svg class="w-12 h-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-black text-zinc-900 uppercase tracking-tighter leading-none mb-4">Проверьте<br>Фразу</h2>
                    <p class="text-zinc-600 font-bold text-[11px] uppercase tracking-widest">Убедитесь, что все слова введены верно</p>
                </div>

                <!-- Wizard Form -->
                <form :action="actionUrl" method="POST" @submit="handleSubmit" ref="recoveryForm">
                    <input type="hidden" name="_token" :value="csrfToken">
                    
                    <div v-for="(word, index) in words" :key="'hidden-'+index">
                        <input type="hidden" name="words[]" :value="word">
                    </div>

                    <!-- Step 0: Length Selection -->
                    <div v-if="currentStep === 0" class="flex flex-col gap-4 animate-in fade-in zoom-in-95 duration-500">
                        <button v-for="len in [12, 15, 18, 21, 24]" :key="len"
                            @click="selectLength(len)"
                            type="button"
                            class="group w-full bg-white border-3 border-zinc-900 h-16 rounded-2xl flex justify-between items-center px-8 hover:bg-zinc-50 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] transition-all active:translate-x-1 active:translate-y-1 active:shadow-none">
                            <span class="text-[15px] font-black text-zinc-900 uppercase tracking-widest" v-text="len + ' СЛОВ'"></span>
                            <div class="w-9 h-9 rounded-xl bg-zinc-100 border-2 border-zinc-900 flex items-center justify-center group-hover:bg-[#7C45F5] group-hover:text-white group-hover:border-[#7C45F5] transition-all text-zinc-900">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path d="M5 12h14m-7-7l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Word Input Section -->
                    <div v-if="currentStep > 0 && currentStep <= totalSteps" class="min-h-[260px] flex flex-col items-center justify-center animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <div class="relative w-full">
                            <input 
                                type="text"
                                v-model="inputWord"
                                @input="handleInput"
                                @keydown="handleKeydown"
                                ref="wordInput"
                                placeholder="..."
                                class="w-full bg-transparent border-b-4 border-zinc-900 py-6 text-4xl font-black text-center text-zinc-900 placeholder:text-zinc-200 focus:border-[#7C45F5] outline-none transition-all code-input"
                                autocomplete="off"
                                autofocus
                            >

                            <!-- Autocomplete Suggestions -->
                            <div v-if="suggestions.length > 0" 
                                 class="absolute left-0 right-0 top-full mt-10 flex flex-wrap justify-center gap-3 z-30">
                                <button v-for="sWord in suggestions" 
                                    @click="selectWord(sWord)"
                                    type="button"
                                    class="bg-white border-2 border-zinc-900 px-5 py-3 rounded-xl text-xs font-black text-zinc-900 hover:bg-[#7C45F5] hover:text-white hover:border-[#7C45F5] transition-all shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] uppercase tracking-widest active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                                    <span v-text="sWord"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Final Confirmation Grid -->
                    <div v-if="currentStep > totalSteps" 
                         class="grid gap-4 mb-14 animate-in zoom-in-95 duration-500"
                         :class="totalSteps > 12 ? 'grid-cols-2' : 'grid-cols-2'">
                        <div v-for="(word, i) in words" :key="i" 
                             @click="jumpToStep(i+1)"
                             class="group cursor-pointer">
                            <div class="flex items-center gap-3 bg-white border-2 border-zinc-900 p-4 rounded-xl hover:bg-[#7C45F5]/5 transition-all shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] group-hover:translate-x-0.5 group-hover:translate-y-0.5 group-hover:shadow-none">
                                <span class="text-[10px] font-black text-zinc-300 w-5 pb-0.5" v-text="i+1"></span>
                                <span class="text-sm font-black text-zinc-900 uppercase tracking-widest truncate" v-text="word"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center gap-4" v-if="currentStep > 0">
                        <button @click="prevStep" type="button"
                            class="h-16 flex-1 bg-white border-2 border-zinc-900 rounded-2xl text-[11px] font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-50 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
                            Назад
                        </button>
                        
                        <button v-if="currentStep > 0 && currentStep <= totalSteps" @click="nextStep" type="button"
                            :disabled="!isWordValid"
                            class="h-16 flex-[2] bg-white border-2 border-zinc-900 rounded-2xl text-[11px] font-black uppercase tracking-widest text-zinc-900 transition-all hover:bg-zinc-50 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none disabled:opacity-20">
                            Далее
                        </button>

                        <button v-if="currentStep > totalSteps" type="submit"
                            class="h-16 flex-[2] bg-[#7C45F5] border-2 border-zinc-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all hover:bg-[#8A5CF7] shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none">
                            Восстановить
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center" v-if="currentStep <= totalSteps">
                    <a href="{{ route('shop.customer.session.index') }}"
                        class="text-[9px] font-black uppercase tracking-[0.4em] text-zinc-400 hover:text-zinc-900 transition-colors underline decoration-zinc-100 decoration-2 underline-offset-8">
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
