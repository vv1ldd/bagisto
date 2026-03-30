<x-shop::layouts.auth
    title="Фраза восстановления"
    contentWidth="max-w-[480px]"
>
    <x-slot:header>
        <h1 class="text-zinc-900 text-2xl md:text-3xl font-black uppercase tracking-tighter text-center leading-none">
            Фраза<br>Восстановления
        </h1>
        <p class="text-zinc-600 text-[10px] font-black uppercase tracking-widest mt-4 text-center">
            Никаких <span class="text-red-500">скриншотов</span> • Только бумага
        </p>
    </x-slot>

    <div class="space-y-5">
        <seed-phrase-reveal :words='@json($words)' verify-url="{{ route('shop.customers.account.profile.verify_recovery_key') }}"></seed-phrase-reveal>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="seed-phrase-reveal-template">
            <div class="space-y-4">
                <!-- Step 1 Instructions -->
                <div v-if="step === 1" class="animate-in fade-in duration-500">
                    <p class="text-zinc-600 font-bold text-[9px] uppercase tracking-widest text-center leading-relaxed px-4">
                        ЭТАП 1: ЗАПИШИТЕ ПЕРВУЮ ПОЛОВИНУ <br>
                        <span class="text-zinc-900 font-black">СЛОВА С 1 ПО @{{ splitIndex }}</span>
                    </p>
                </div>

                <!-- Step 2 Instructions -->
                <div v-if="step === 2" class="animate-in fade-in duration-500">
                    <p class="text-zinc-600 font-bold text-[9px] uppercase tracking-widest text-center leading-relaxed px-4 text-purple-600">
                        ЭТАП 2: ТЕПЕРЬ ЗАПИШИТЕ ВТОРУЮ ПОЛОВИНУ <br>
                        <span class="text-zinc-900 font-black">СЛОВА С @{{ splitIndex + 1 }} ПО @{{ words.length }}</span>
                    </p>
                </div>

                <div class="flex flex-col gap-4">
                    <!-- Step 1 Word Grid -->
                    <div v-if="step === 1" class="grid grid-cols-3 gap-2 w-full animate-in fade-in zoom-in-95 duration-500">
                        <div v-for="(word, index) in firstHalf" :key="index" 
                            class="flex items-center gap-2 bg-white border-2 border-zinc-900 rounded-lg p-2 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none group">
                            <span class="text-[9px] font-black text-zinc-300 w-4 select-none">@{{ index + 1 }}</span>
                            <span class="text-zinc-900 font-black tracking-tight text-[11px] select-all truncate lowercase">@{{ word }}</span>
                        </div>
                    </div>

                    <!-- Transition Button Step 1 -> 2 -->
                    <div v-if="step === 1" class="flex justify-center pt-1">
                        <button type="button" @click="goToStepTwo"
                            class="group flex w-full items-center justify-center gap-4 bg-white border-2 border-zinc-900 px-6 py-4 text-center font-black text-zinc-900 transition-all active:translate-x-1 active:translate-y-1 active:shadow-none rounded-xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[11px]">
                            <span>Я ЗАПИСАЛ(А) ЭТУ ЧАСТЬ</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Step 2 Word Grid -->
                    <div v-if="step === 2" class="grid grid-cols-3 gap-2 w-full animate-in fade-in zoom-in-95 duration-500">
                        <div v-for="(word, index) in secondHalf" :key="index" 
                            class="flex items-center gap-2 bg-zinc-100 border-2 border-dashed border-zinc-900 rounded-lg p-2 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none group">
                            <span class="text-[9px] font-black text-zinc-300 w-4 select-none">@{{ splitIndex + index + 1 }}</span>
                            <span class="text-zinc-900 font-black tracking-tight text-[11px] select-all truncate lowercase">@{{ word }}</span>
                        </div>
                    </div>
                </div>

                <!-- Final Action Section -->
                <div v-if="step === 2" class="flex flex-col items-center gap-4 pt-1 animate-in fade-in slide-in-from-top-4 duration-700 delay-200">
                    <a id="finish-btn" :href="verifyUrl"
                        class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-6 py-4 text-center font-black text-white transition-all hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[11px] overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                        <span>Я ЗАПИСАЛ(А) ВСЕ СЛОВА</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    
                    <p class="text-zinc-500 text-[8px] font-black uppercase tracking-widest text-center max-w-[280px] leading-relaxed">
                        Нажимая кнопку, вы подтверждаете полную ответственность за сохранность фразы.
                    </p>
                </div>
            </div>
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof app === 'undefined') return;

                app.component('seed-phrase-reveal', {
                    template: '#seed-phrase-reveal-template',
                    props: ['words', 'verifyUrl'],
                    data() {
                        return {
                            step: 1
                        }
                    },
                    computed: {
                        splitIndex() {
                            return Math.ceil(this.words.length / 2);
                        },
                        firstHalf() {
                            return this.words.slice(0, this.splitIndex);
                        },
                        secondHalf() {
                            return this.words.slice(this.splitIndex);
                        }
                    },
                    methods: {
                        goToStepTwo() {
                            this.step = 2;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    }
                });
            });
        </script>
    @endPushOnce
</x-shop::layouts.auth>