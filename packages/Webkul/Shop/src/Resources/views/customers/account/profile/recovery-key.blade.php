<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Сохранение фраз восстановления
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-6 px-4">
        <div class="bg-white p-6 md:p-8 flex flex-col items-center text-center relative overflow-hidden w-full max-w-2xl shadow-xl border border-[#e2d9ff] rounded-[2rem]">
            
            {{-- Protection Icon --}}
            <div class="mb-4 flex flex-col items-center">
                 <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-lg shadow-[#7C45F5]/30 mb-3 transition-transform hover:scale-105 duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-[#1a0050] text-xl md:text-2xl font-black tracking-tight mb-1">Фразы восстановления</h1>
                <p class="text-zinc-500 text-sm max-w-xs mx-auto">Единственный способ восстановить доступ.</p>
            </div>

            <div class="flex flex-col items-center gap-2 mb-6">
                <div class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 font-bold text-[10px] border border-red-100 rounded-lg shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    НЕ ДЕЛАЙТЕ СКРИНШОТ
                </div>
                <p class="text-zinc-500 text-[13px] leading-snug max-w-[400px]">
                    Запишите слова на бумагу в правильном порядке. <br class="hidden md:block">
                    <span class="font-black text-[#1a0050]">Она показывается только один раз!</span>
                </p>
            </div>

            <!-- Word Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 w-full mb-8">
                @foreach($words as $index => $word)
                    <div class="flex items-center gap-2.5 bg-[#f5f4fc] border border-[#e2d9ff] py-2 px-3 shadow-sm hover:border-[#7C45F5] transition-all group rounded-xl">
                        <span class="text-[10px] font-black text-[#7C45F5]/40 select-none group-hover:text-[#7C45F5]/60 transition-colors w-4">{{ $index + 1 }}</span>
                        <span class="text-[#1a0050] font-mono font-bold tracking-tight text-sm select-all break-all text-left flex-1">{{ $word }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Mnemonics Story (Memorization Aid) --}}
            @if(isset($story))
                <div class="w-full mb-8 text-left">
                    <div class="flex items-center gap-2 mb-3 px-1">
                        <span class="text-[10px] font-black text-[#7C45F5] uppercase tracking-wider">История для запоминания (Мнемоника)</span>
                        <div class="h-px bg-gradient-to-r from-[#7C45F5]/20 to-transparent flex-1"></div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-[#F8F7FF] to-white border border-[#7C45F5]/10 p-5 md:p-6 rounded-[1.5rem] shadow-inner relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#7C45F5]/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <p class="text-[#1a0050] text-[15px] md:text-base leading-relaxed font-medium relative z-10">
                            {!! preg_replace('/\*\*(.*?)\*\*/', '<span class="text-[#7C45F5] font-black underline decoration-2 decoration-[#7C45F5]/20 underline-offset-4">$1</span>', $story) !!}
                        </p>
                    </div>
                </div>
            @endif

            <div class="w-full flex flex-col items-center gap-4">
                <a id="finish-btn" href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                    class="flex w-full items-center justify-center gap-2 !rounded-xl bg-[#7C45F5] px-6 py-4 text-center text-sm font-bold text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] active:scale-[0.98] max-w-xs group">
                    <span>Я ЗАПИСАЛ СЛОВА</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                
                <p class="text-zinc-400 text-[11px] max-w-xs leading-relaxed">
                    Нажимая кнопку, вы подтверждаете, что сохранили фразу в надежном месте.
                </p>
            </div>
        </div>
    </div>

</x-shop::layouts>