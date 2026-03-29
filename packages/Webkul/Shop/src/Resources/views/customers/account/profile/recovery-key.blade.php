<x-shop::layouts.auth
    title="Фраза восстановления"
    contentWidth="max-w-[480px]"
>
    <x-slot:header>
        <h1 class="text-white text-2xl md:text-3xl font-black uppercase tracking-tighter text-center leading-none">
            Фраза<br>Восстановления
        </h1>
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-4 text-center">
            Никаких <span class="text-[#FF4D6D]">скриншотов</span> • Только бумага
        </p>
    </x-slot>

    <div class="space-y-6">
        <p class="text-zinc-400 font-bold text-[10px] uppercase tracking-widest text-center leading-relaxed px-4">
            Запишите эти слова в правильном порядке. <br>
            <span class="text-white">Это единственный доступ к вашему счету.</span>
        </p>

        <!-- Compact Word Grid -->
        <div class="grid grid-cols-3 gap-2 w-full">
            @foreach($words as $index => $word)
                <div class="flex items-center gap-2 bg-white/5 border border-white/5 rounded-xl p-2.5 transition-all hover:bg-white/10 group">
                    <span class="text-[9px] font-black text-zinc-600 w-4 select-none group-hover:text-[#7C45F5] transition-colors">{{ $index + 1 }}</span>
                    <span class="text-white font-black tracking-tight text-xs select-all truncate lowercase">{{ $word }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col items-center gap-6 pt-2">
            <a id="finish-btn" href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] px-8 py-5 text-center font-black text-white transition-all hover:bg-[#6b35e4] active:scale-[0.98] rounded-2xl shadow-xl shadow-[#7C45F5]/20 uppercase tracking-[0.2em] text-xs overflow-hidden">
                <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                <span>Я ЗАПИСАЛ(А) СЛОВА</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            
            <p class="text-zinc-500 text-[8px] font-bold uppercase tracking-widest text-center max-w-[280px] leading-relaxed opacity-50">
                Нажимая кнопку, вы подтверждаете полную ответственность за сохранность фразы.
            </p>
        </div>
    </div>
</x-shop::layouts.auth>