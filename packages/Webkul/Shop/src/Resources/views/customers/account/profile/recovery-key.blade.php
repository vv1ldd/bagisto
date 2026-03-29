<x-shop::layouts.auth
    title="Фраза восстановления"
    contentWidth="max-w-[480px]"
>
    <x-slot:header>
        <h1 class="text-zinc-900 text-3xl md:text-4xl font-black uppercase tracking-tighter text-center leading-none">
            Фраза<br>Восстановления
        </h1>
        <p class="text-zinc-600 text-[11px] font-black uppercase tracking-widest mt-6 text-center">
            Никаких <span class="text-red-500">скриншотов</span> • Только бумага
        </p>
    </x-slot>

    <div class="space-y-8" x-data="{ step: 1 }">
        <style>
            [x-cloak] { display: none !important; }
        </style>

        <div x-show="step === 1" x-cloak x-transition.opacity>
            <p class="text-zinc-600 font-bold text-[11px] uppercase tracking-widest text-center leading-relaxed px-4">
                ЭТАП 1: ЗАПИШИТЕ ПЕРВУЮ ПОЛОВИНУ <br>
                <span class="text-zinc-900 font-black">СЛОВА С 1 ПО {{ (int) ceil(count($words) / 2) }}</span>
            </p>
        </div>

        <div x-show="step === 2" x-cloak x-transition.opacity>
            <p class="text-zinc-600 font-bold text-[11px] uppercase tracking-widest text-center leading-relaxed px-4 text-purple-600">
                ЭТАП 2: ТЕПЕРЬ ЗАПИШИТЕ ВТОРУЮ ПОЛОВИНУ <br>
                <span class="text-zinc-900 font-black">СЛОВА С {{ (int) ceil(count($words) / 2) + 1 }} ПО {{ count($words) }}</span>
            </p>
        </div>

        @php
            $totalCount = count($words);
            $splitIndex = (int) ceil($totalCount / 2);
        @endphp

        <!-- Compact Word Grid -->
        <div class="flex flex-col gap-6">
            {{-- Step 1 Block --}}
            <div x-show="step === 1" x-cloak class="grid grid-cols-3 gap-3 w-full" x-transition.opacity.scale.95>
                @foreach($words as $index => $word)
                    @if($index < $splitIndex)
                        <div class="flex items-center gap-2 bg-white border-2 border-zinc-900 rounded-xl p-3 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none group">
                            <span class="text-[10px] font-black text-zinc-300 w-5 select-none transition-colors">{{ $index + 1 }}</span>
                            <span class="text-zinc-900 font-black tracking-tight text-xs select-all truncate lowercase">{{ $word }}</span>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Transition Button Step 1 -> 2 --}}
            <div x-show="step === 1" x-cloak class="flex justify-center pt-4">
                <button type="button" @click="step = 2; window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="group flex w-full items-center justify-center gap-4 bg-white border-3 border-zinc-900 px-8 py-6 text-center font-black text-zinc-900 transition-all active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[13px]">
                    <span>Я ЗАПИСАЛ(А) ЭТУ ЧАСТЬ</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>

            {{-- Step 2 Block --}}
            <div x-show="step === 2" x-cloak class="grid grid-cols-3 gap-3 w-full" x-transition.opacity.scale.95>
                @foreach($words as $index => $word)
                    @if($index >= $splitIndex)
                        <div class="flex items-center gap-2 bg-zinc-50 border-2 border-dashed border-zinc-900 rounded-xl p-3 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none group">
                            <span class="text-[10px] font-black text-zinc-300 w-5 select-none transition-colors">{{ $index + 1 }}</span>
                            <span class="text-zinc-900 font-black tracking-tight text-xs select-all truncate lowercase">{{ $word }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex flex-col items-center gap-8 pt-4" x-show="step === 2" x-cloak x-transition.opacity.delay.300>
            <a id="finish-btn" href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-[13px] overflow-hidden">
                <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                <span>Я ЗАПИСАЛ(А) ВСЕ СЛОВА</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            
            <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest text-center max-w-[320px] leading-relaxed opacity-100">
                Нажимая кнопку, вы подтверждаете полную ответственность за сохранность фразы.
            </p>
        </div>
    </div>
</x-shop::layouts.auth>