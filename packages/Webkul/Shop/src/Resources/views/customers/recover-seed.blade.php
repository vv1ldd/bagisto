@php
    $wordlist = app(\Webkul\Customer\Services\MnemonicService::class)->getWordlist();
@endphp

<x-shop::layouts.split-screen title="Восстановление доступа">
    {{-- Google Font for Code Mode --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        .code-input {
            font-family: 'Roboto Mono', monospace !important;
            letter-spacing: 0.5em !important;
            padding-left: 0.5em !important; /* Offset for centered letter-spacing */
            text-transform: uppercase;
        }
        
        /* Simulated boxes for characters */
        .code-input-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background-image: linear-gradient(to right, #7C45F5 70%, transparent 70%);
            background-size: 1.25em 100%;
            pointer-events: none;
            opacity: 0.3;
        }
        
        .code-input:focus + .code-input-container::after {
            opacity: 1;
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
            <div class="flex flex-col items-center justify-center min-h-[calc(100vh-64px)] py-6 px-4 animate-in fade-in duration-700">
                
                <div class="w-full max-w-[540px] bg-white p-8 md:p-14 border-4 border-zinc-900 shadow-[20px_20px_0px_0px_rgba(124,69,245,1)] flex flex-col items-stretch relative overflow-hidden transition-all duration-500">
                    
                    <!-- Flash Messages (Manual check if layout misses them) -->
                    @if (session()->has('error'))
                        <div class="mb-8 p-5 bg-[#FF4D6D] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] text-white text-xs font-black uppercase tracking-widest animate-in fade-in slide-in-from-top-2">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="mb-8 p-5 bg-[#00FF94] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] text-zinc-900 text-xs font-black uppercase tracking-widest animate-in fade-in slide-in-from-top-2">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Progress Header -->
                    <div class="mb-14" v-if="currentStep > 0 && currentStep <= totalSteps">
                        <div class="flex justify-between items-end mb-6">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-2 leading-none">Security / Recovery</p>
                                <h2 class="text-3xl font-black text-zinc-900 uppercase tracking-tighter leading-none">Слово <span v-text="currentStep" class="text-[#7C45F5]"></span> <span class="text-zinc-300">/</span> <span v-text="totalSteps"></span></h2>
                            </div>
                        </div>
                        <div class="h-4 w-full bg-zinc-100 border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
                            <div class="h-full bg-[#7C45F5] transition-all duration-700 ease-out"
                                 :style="{ width: (currentStep / totalSteps * 100) + '%' }"></div>
                        </div>
                    </div>

                    <!-- Step 0: Length Selection Header -->
                    <div class="mb-14 text-left" v-if="currentStep === 0">
                        <p class="text-[11px] font-black uppercase tracking-[0.4em] text-[#7C45F5] mb-4">Настройка</p>
                        <h2 class="text-4xl font-black text-zinc-900 uppercase tracking-tighter mb-4 leading-none">Длина<br>Фразы</h2>
                        <div class="h-1.5 w-16 bg-[#FF4D6D] border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] mb-6"></div>
                        <p class="text-zinc-500 font-bold text-xs uppercase tracking-wider">Выберите количество слов в вашей фразе</p>
                    </div>

                    <!-- Final Confirmation Header -->
                    <div class="mb-10 text-center" v-if="currentStep > totalSteps">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-[#00FF94] border-3 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] mb-8 rotate-3">
                            <svg class="w-10 h-10 -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-black text-zinc-900 uppercase tracking-tighter leading-none">Проверьте<br>Фразу</h2>
                    </div>

                    <!-- Wizard Form -->
                    <form :action="actionUrl" method="POST" @submit="handleSubmit" ref="recoveryForm">
                        <input type="hidden" name="_token" :value="csrfToken">
                        
                        <div v-for="(word, index) in words" :key="'hidden-'+index">
                            <input type="hidden" name="words[]" :value="word">
                        </div>

                        <!-- Step 0: Length Selection -->
                        <div v-if="currentStep === 0" class="flex flex-col gap-4 animate-in fade-in zoom-in duration-500">
                            <button v-for="len in [12, 15, 18, 21, 24]" :key="len"
                                @click="selectLength(len)"
                                type="button"
                                class="group w-full bg-white border-2 border-zinc-900 p-6 text-xl font-black text-zinc-900 hover:bg-zinc-900 hover:text-white transition-all flex justify-between items-center shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1">
                                <span v-text="len + ' СЛОВ'"></span>
                                <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path d="M5 12h14m-7-7l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Word Input Section -->
                        <div v-if="currentStep > 0 && currentStep <= totalSteps" class="min-h-[240px] flex flex-col items-center justify-center animate-in fade-in slide-in-from-bottom-5 duration-500">
                            <div class="relative w-full group">
                                <div class="absolute inset-0 bg-zinc-100 border-2 border-zinc-900 translate-x-2 translate-y-2"></div>
                                <input 
                                    type="text"
                                    v-model="inputWord"
                                    @input="handleInput"
                                    @keydown="handleKeydown"
                                    ref="wordInput"
                                    placeholder="..."
                                    class="relative w-full bg-white border-2 border-zinc-900 p-8 text-4xl font-black text-center text-zinc-900 placeholder:text-zinc-100 focus:border-[#7C45F5] focus:ring-0 transition-all code-input uppercase"
                                    autocomplete="off"
                                    autofocus
                                >

                                <!-- Autocomplete Suggestions -->
                                <div v-if="suggestions.length > 0" 
                                     class="absolute left-0 right-0 top-full mt-8 flex flex-wrap justify-center gap-3 z-30">
                                    <button v-for="sWord in suggestions" 
                                        @click="selectWord(sWord)"
                                        type="button"
                                        class="bg-white border-2 border-zinc-900 px-5 py-3 text-sm font-black text-zinc-900 hover:bg-[#7C45F5] hover:text-white hover:border-[#7C45F5] transition-all shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] uppercase tracking-widest">
                                        <span v-text="sWord"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Final Confirmation Grid Section -->
                        <div v-if="currentStep > totalSteps" 
                             class="grid gap-4 mb-10 animate-in zoom-in duration-500"
                             :class="totalSteps > 12 ? 'grid-cols-2' : 'grid-cols-2 text-sm'">
                            <div v-for="(word, i) in words" :key="i" 
                                 @click="jumpToStep(i+1)"
                                 class="relative group cursor-pointer">
                                <div class="absolute inset-0 bg-zinc-50 border border-zinc-900 translate-x-0.5 translate-y-0.5"></div>
                                <div class="relative flex items-center gap-2 bg-white border border-zinc-900 p-3 hover:border-[#7C45F5] transition-all group-hover:-translate-x-0.5 group-hover:-translate-y-0.5">
                                    <span class="text-[8px] font-black text-zinc-300 w-4 group-hover:text-[#7C45F5]" v-text="i+1"></span>
                                    <span class="text-[11px] font-black text-zinc-900 uppercase tracking-widest truncate" v-text="word"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex items-center gap-5" v-if="currentStep > 0">
                            <button @click="prevStep" type="button"
                                class="flex-1 bg-white border-2 border-zinc-900 p-5 text-xs font-black uppercase tracking-[0.3em] text-zinc-900 hover:bg-zinc-50 transition-all shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:shadow-none active:translate-x-1 active:translate-y-1">
                                Назад
                            </button>
                            
                            <button v-if="currentStep > 0 && currentStep <= totalSteps" @click="nextStep" type="button"
                                :disabled="!isWordValid"
                                class="flex-[2] bg-zinc-900 border-2 border-zinc-900 p-5 text-xs font-black uppercase tracking-[0.3em] text-white hover:bg-black transition-all shadow-[8px_8px_0px_0px_rgba(124,69,245,1)] active:shadow-none active:translate-x-1 active:translate-y-1 disabled:opacity-20 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-x-0 disabled:translate-y-0">
                                Далее
                            </button>

                            <button v-if="currentStep > totalSteps" type="submit"
                                class="flex-[2] bg-[#7C45F5] border-2 border-zinc-900 p-5 text-xs font-black uppercase tracking-[0.3em] text-white hover:bg-[#6534d4] transition-all shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] active:shadow-none active:translate-x-1 active:translate-y-1">
                                Восстановить
                            </button>
                        </div>
                    </form>

                    <div class="mt-14 text-center" v-if="currentStep <= totalSteps">
                        <a href="{{ route('shop.customer.session.index') }}"
                            class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-300 hover:text-[#7C45F5] transition-colors">
                            Отмена
                        </a>
                    </div>
                </div>
            </div>
        </script>

                    <div class="mt-12 text-center" v-if="currentStep <= totalSteps">
                        <a href="{{ route('shop.customer.session.index') }}"
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-300 hover:text-[#7C45F5] transition-colors">
                            Вернуться ко входу
                        </a>
                    </div>
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
                        suggestions: []
                    }
                },

                mounted() {
                    console.log('[Recovery] Wizard mounted. Old words:', this.oldWords);
                    // Try to restore state from old input (Laravel back()->withInput())
                    if (this.oldWords && Array.isArray(this.oldWords)) {
                        const filledWords = this.oldWords.filter(w => w && w.trim() !== '');
                        console.log('[Recovery] Filled words found:', filledWords.length);
                        
                        if (filledWords.length > 0) {
                            // Find the best-fitting BIP39 length (12, 15, 18, 21, 24)
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
                            
                            // Map the old words correctly into the array
                            this.oldWords.forEach((w, i) => {
                                if (i < bestLen) this.words[i] = w || '';
                            });

                            // Jump to the final confirmation step so user can see errors and correct them
                            this.currentStep = bestLen + 1;
                            console.log('[Recovery] Jumped to step:', this.currentStep);
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
                        this.$nextTick(() => { this.$refs.wordInput.focus(); });
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
                            // Intuitive backspace to go to previous word
                            e.preventDefault();
                            this.prevStep();
                        }
                    },

                    jumpToStep(step) {
                        this.currentStep = step;
                        this.inputWord = this.words[step - 1];
                        this.suggestions = [];
                        this.$nextTick(() => { this.$refs.wordInput.focus(); });
                    },

                    nextStep() {
                        if (!this.isWordValid) return;
                        
                        this.words[this.currentStep - 1] = this.inputWord.toLowerCase().trim();
                        
                        if (this.currentStep <= this.totalSteps) {
                            this.currentStep++;
                            
                            if (this.currentStep <= this.totalSteps) {
                                this.inputWord = this.words[this.currentStep - 1]; 
                                this.suggestions = [];
                                this.$nextTick(() => { this.$refs.wordInput.focus(); });
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
                                this.$nextTick(() => { this.$refs.wordInput.focus(); });
                            }
                        }
                    },

                    handleSubmit(e) {
                        if (this.words.some(w => !w)) {
                            e.preventDefault();
                            alert('Пожалуйста, заполните все слова вашей фразы.');
                        }
                    }
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts.split-screen>
