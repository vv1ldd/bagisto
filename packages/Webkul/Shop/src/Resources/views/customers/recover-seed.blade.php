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
            <div class="flex flex-col items-center justify-center min-h-[calc(100vh-64px)] py-6 px-4">
                
                <div class="w-full max-w-[480px] bg-white p-8 md:p-12 shadow-2xl shadow-purple-500/10 border border-zinc-100 flex flex-col items-stretch relative overflow-hidden transition-all duration-500">
                    
                    <!-- Flash Messages (Manual check if layout misses them) -->
                    @if (session()->has('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold uppercase tracking-wider animate-in fade-in slide-in-from-top-2">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-bold uppercase tracking-wider animate-in fade-in slide-in-from-top-2">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Progress Header (only for word entry steps) -->
                    <div class="mb-12" v-if="currentStep > 0 && currentStep <= totalSteps">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#7C45F5] mb-1">Meanly Wallet / Recovery</p>
                                <h2 class="text-2xl font-black text-zinc-900 leading-none">Слово <span v-text="currentStep"></span> из <span v-text="totalSteps"></span></h2>
                            </div>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#7C45F5] to-[#B465DA] transition-all duration-700 ease-out"
                                 :style="{ width: (currentStep / totalSteps * 100) + '%' }"></div>
                        </div>
                    </div>

                    <!-- Length Selection Header -->
                    <div class="mb-12 text-center" v-if="currentStep === 0">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#7C45F5] mb-2">Настройка</p>
                        <h2 class="text-2xl font-black text-zinc-900 leading-none">Длина фразы</h2>
                        <p class="text-zinc-400 text-xs mt-4">Выберите количество слов в вашей секретной фразе</p>
                    </div>

                    <!-- Final Confirmation Header -->
                    <div class="mb-8 text-center" v-if="currentStep > totalSteps">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#7C45F5] mb-2">Проверьте фразу</p>
                        <h2 class="text-2xl font-black text-zinc-900 leading-none">Все готово</h2>
                    </div>

                    <!-- Wizard Form -->
                    <form :action="actionUrl" method="POST" @submit="handleSubmit" ref="recoveryForm">
                        <input type="hidden" name="_token" :value="csrfToken">
                        
                        <!-- Invisible fields for submission -->
                        <div v-for="(word, index) in words" :key="'hidden-'+index">
                            <input type="hidden" name="words[]" :value="word">
                        </div>

                        <!-- Step 0: Length Selection Section -->
                        <div v-if="currentStep === 0" class="flex flex-col gap-3 animate-in fade-in zoom-in duration-500">
                            <button v-for="len in [12, 15, 18, 21, 24]" :key="len"
                                @click="selectLength(len)"
                                type="button"
                                class="w-full bg-zinc-50 border-2 border-zinc-100 p-5 text-lg font-bold text-zinc-700 hover:border-[#7C45F5] hover:text-[#7C45F5] hover:bg-purple-50 transition-all flex justify-between items-center group">
                                <span v-text="len + ' слов'"></span>
                                <span class="icon-arrow-right text-zinc-300 group-hover:text-[#7C45F5] transition-all"></span>
                            </button>
                        </div>

                        <!-- Word Input Section -->
                        <div v-if="currentStep > 0 && currentStep <= totalSteps" class="min-h-[200px] flex flex-col items-center justify-center animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <button v-if="currentStep > 1" @click="prevStep" type="button" 
                                    class="text-zinc-300 hover:text-[#7C45F5] transition-colors p-1" title="Назад">
                                    <span class="icon-arrow-left text-xl"></span>
                                </button>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 text-center">
                                    Введите <span v-text="currentStep"></span>-е секретное слово
                                </p>
                            </div>
                            
                            <div class="relative w-full code-input-container">
                                <input 
                                    type="text"
                                    v-model="inputWord"
                                    @input="handleInput"
                                    @keydown="handleKeydown"
                                    ref="wordInput"
                                    placeholder="..."
                                    class="w-full bg-transparent border-b-4 border-zinc-100 p-4 text-3xl font-bold text-center text-zinc-800 placeholder:text-zinc-100 focus:border-[#7C45F5] focus:ring-0 transition-all code-input"
                                    autocomplete="off"
                                    autofocus
                                >

                                <!-- Autocomplete Suggestions -->
                                <div v-if="suggestions.length > 0" 
                                     class="absolute left-0 right-0 top-full mt-4 flex flex-wrap justify-center gap-2 z-20">
                                    <button v-for="sWord in suggestions" 
                                        @click="selectWord(sWord)"
                                        type="button"
                                        class="bg-white border-2 border-zinc-100 px-4 py-2 text-sm font-bold text-zinc-500 hover:border-[#7C45F5] hover:text-[#7C45F5] hover:bg-purple-50 transition-all shadow-sm">
                                        <span v-text="sWord"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Final Confirmation Grid Section -->
                        <div v-if="currentStep > totalSteps" 
                             class="grid gap-2 mb-8 animate-in zoom-in duration-500"
                             :class="totalSteps > 12 ? 'grid-cols-2' : 'grid-cols-1'">
                            <div v-for="(word, i) in words" :key="i" 
                                 @click="jumpToStep(i+1)"
                                 class="flex items-center gap-2 bg-zinc-50 p-2 border border-zinc-100 hover:border-[#7C45F5] cursor-pointer group transition-all">
                                <span class="text-[8px] font-black text-zinc-300 w-4 group-hover:text-[#7C45F5]" v-text="i+1" style="min-width: 14px"></span>
                                <span class="text-[12px] font-bold text-zinc-700 uppercase tracking-wider group-hover:text-zinc-900" v-text="word"></span>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex items-center gap-4 animate-in fade-in duration-500" :class="{'mt-16': currentStep > 0 && currentStep <= totalSteps}">
                            <button @click="prevStep" type="button" v-if="currentStep > 0"
                                class="flex-1 border-2 border-zinc-200 p-5 text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-[#7C45F5] hover:border-[#7C45F5] transition-all bg-zinc-50 hover:bg-white">
                                Назад
                            </button>
                            
                            <button v-if="currentStep > 0 && currentStep <= totalSteps" @click="nextStep" type="button"
                                :disabled="!isWordValid"
                                class="flex-[2] bg-zinc-900 p-5 text-sm font-bold uppercase tracking-widest text-white hover:bg-black transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                                Далее
                            </button>

                            <button v-if="currentStep > totalSteps" type="submit"
                                class="flex-[2] bg-[#7C45F5] p-5 text-sm font-bold uppercase tracking-widest text-white hover:bg-[#6534d4] transition-all shadow-lg shadow-[#7C45F5]/20">
                                Восстановить доступ
                            </button>
                        </div>
                    </form>

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
