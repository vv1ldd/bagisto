<x-shop::layouts.split-screen title="Сохранение ключа восстановления">
    <div class="bg-white p-6 md:p-8 flex flex-col items-center text-center relative overflow-hidden w-full shadow-sm border border-[#e2d9ff]">
        
        {{-- Protection Icon --}}
        <div class="mb-6 flex flex-col items-center">
             <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-lg shadow-[#7C45F5]/20 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h3 class="text-[#1a0050] text-2xl font-black tracking-tight mb-2">Секретная фраза доступа</h3>
            <p class="text-zinc-500 text-sm max-w-sm mx-auto">Эта фраза — единственный способ восстановить доступ к вашему аккаунту.</p>
        </div>

        <div class="flex flex-col items-center gap-3 mb-8">
            <div class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 font-bold text-[11px] border border-red-100 rounded-lg shadow-sm">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                НЕ ДЕЛАЙТЕ СКРИНШОТ
            </div>
            <p class="text-zinc-500 text-sm leading-relaxed max-w-[480px]">
                Запишите слова на бумагу в правильном порядке. <br class="hidden md:block">
                <span class="font-bold text-[#1a0050]">Она показывается только один раз!</span>
            </p>
        </div>

        <!-- Word Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full max-w-[720px] mb-8">
            @foreach($words as $index => $word)
                <div class="flex items-center gap-3 bg-[#f5f4fc] border border-[#e2d9ff] py-3 px-4 shadow-sm hover:border-[#7C45F5] transition-all group rounded-xl">
                    <span class="text-[11px] font-black text-[#7C45F5]/40 select-none group-hover:text-[#7C45F5]/60 transition-colors">{{ $index + 1 }}</span>
                    <span class="text-[#1a0050] font-mono font-bold tracking-tight text-[14px] select-all break-keep">{{ $word }}</span>
                </div>
            @endforeach
        </div>

        <div class="w-full flex justify-center">
            <a href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                class="flex w-full items-center justify-center gap-2 !rounded-xl bg-[#7C45F5] px-8 py-4 text-center text-sm font-bold text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] active:scale-[0.98] max-w-[320px]">
                <span>ПРОДОЛЖИТЬ</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>

    <p class="text-zinc-400 text-center mt-6 text-xs max-w-[400px] mx-auto leading-relaxed">
        Нажимая «Продолжить», вы подтверждаете, что <br class="hidden md:block">
        сохранили ключ в надежном месте.
    </p>
</x-shop::layouts.split-screen>