<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Сохранение фраз восстановления
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 animate-in fade-in duration-700">
        <div class="bg-white p-8 md:p-12 flex flex-col items-center text-center relative overflow-hidden w-full max-w-3xl border-4 border-zinc-900 shadow-[16px_16px_0px_0px_rgba(124,69,245,1)]">
            
            <!-- Header Section -->
            <div class="mb-10 flex flex-col items-center w-full">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#7C45F5] border-3 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] mb-6 rotate-3">
                    <svg class="w-10 h-10 text-white -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-zinc-900 text-3xl md:text-5xl font-black uppercase tracking-tighter mb-4 leading-none">Фразы<br>Восстановления</h1>
                <div class="h-1.5 w-24 bg-[#FF4D6D] border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]"></div>
            </div>

            <div class="flex flex-col items-center gap-4 mb-10 w-full">
                <div class="inline-flex items-center px-4 py-2 bg-[#FF4D6D] text-white font-black text-xs border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] uppercase tracking-widest">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Никаких скриншотов
                </div>
                <p class="text-zinc-600 font-bold text-sm leading-relaxed max-w-[500px] uppercase tracking-wide">
                    Запишите слова на бумагу в правильном порядке. <br class="hidden md:block">
                    <span class="text-zinc-900 font-black underline decoration-[#7C45F5] decoration-4">Это ваш единственный доступ к аккаунту.</span>
                </p>
            </div>

            <!-- Word Grid (Brutalist) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full mb-12">
                @foreach($words as $index => $word)
                    <div class="relative group">
                        <div class="absolute inset-0 bg-zinc-100 border-2 border-zinc-900 translate-x-1 translate-y-1"></div>
                        <div class="relative flex items-center gap-3 bg-white border-2 border-zinc-900 py-3 px-4 transition-all hover:-translate-x-0.5 hover:-translate-y-0.5">
                            <span class="text-[10px] font-black text-[#7C45F5] select-none w-5 text-left border-r border-zinc-100 mr-1">{{ $index + 1 }}</span>
                            <span class="text-zinc-900 font-black tracking-tight text-sm select-all break-all text-left flex-1 lowercase">{{ $word }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Mnemonics Story (Premium Glassmorphism/Brutalist mix) --}}
            @if(isset($story))
                <div class="w-full mb-12 text-left">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-[11px] font-black text-[#7C45F5] uppercase tracking-[0.3em]">Мнемоника</span>
                        <div class="h-0.5 bg-zinc-900 flex-1"></div>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-0 bg-[#7C45F5]/5 border-2 border-zinc-900 translate-x-2 translate-y-2"></div>
                        <div class="relative bg-white border-2 border-zinc-900 p-6 md:p-8 overflow-hidden group">
                            <p class="text-zinc-800 text-lg md:text-xl leading-relaxed font-bold relative z-10 italic">
                                {!! preg_replace('/\*\*(.*?)\*\*/', '<span class="text-[#7C45F5] font-black not-italic border-b-4 border-[#7C45F5]/20">$1</span>', $story) !!}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="w-full flex flex-col items-center gap-6">
                <a id="finish-btn" href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                    class="group relative flex w-full max-w-md items-center justify-center gap-4 bg-[#7C45F5] border-2 border-zinc-900 px-8 py-6 text-center font-black text-white transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)] active:translate-x-0 active:translate-y-0 active:shadow-none shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] uppercase tracking-[0.2em] text-sm overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                    <span>Я ЗАПИСАЛ СЛОВА</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                
                <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest max-w-xs leading-relaxed">
                    Нажимая кнопку, вы подтверждаете полную ответственность за сохранность фразы.
                </p>
            </div>
        </div>
    </div>

</x-shop::layouts>