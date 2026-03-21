<x-shop::layouts.split-screen title="Сохранение ключа восстановления">
    <div
        class=" bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-4 md:p-6 lg:p-8 flex flex-col items-center text-center relative overflow-hidden w-full shadow-[0_8px_32px_rgba(124,69,245,0.05)] border border-white">
        <!-- Decorative background elements -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/10  blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#3B82F6]/10  blur-3xl"></div>


        <h3 class="text-[#4A1D96] text-[22px] md:text-2xl font-extrabold mb-2 tracking-tight leading-tight">
            Секретная фраза доступа</h3>

        <div class="space-y-2 mb-4">
            <p class="text-[#4A1D96]/90 text-[14px] leading-relaxed max-w-[480px]">
                Эта фраза — ваш единственный способ восстановить доступ, если вы потеряете Passkey или смените устройство.
            </p>
            <div class="flex flex-col items-center gap-2">
                <p class="inline-block px-3 py-1 bg-red-50 text-red-600 font-bold text-[12px] border border-red-100 shadow-sm mt-1">
                    НЕ ДЕЛАЙТЕ СКРИНШОТ
                </p>
                <p class="text-[#4A1D96]/90 text-[13px] leading-relaxed max-w-[480px]">
                    Запишите слова на бумагу в правильном порядке.
                    <b class="max-md:block">Она показывается только один раз!</b>
                </p>
            </div>
        </div>



        <!-- Ledger-style Word Grid -->
        <div class="grid grid-cols-3 lg:grid-cols-4 gap-1.5 md:gap-3 w-full max-w-[600px] mb-6">
            @foreach($words as $index => $word)
                <div class="flex items-center gap-1.5 bg-white border border-[#E9E1FF] p-1.5 md:p-2.5 shadow-sm hover:border-[#7C45F5] transition-colors rounded">
                    <span class="text-[9px] md:text-[10px] font-black text-[#7C45F5]/40 w-3 md:w-3.5 text-right">{{ $index + 1 }}</span>
                    <span class="text-[#4A1D96] font-mono font-bold tracking-tight text-[11px] md:text-[14px] select-all">{{ $word }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col items-center gap-4 w-full">
            <a href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                class="flex w-full items-center justify-center gap-3 !rounded-none bg-[#7C45F5] px-8 py-4 text-center text-sm font-bold text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 uppercase tracking-[0.2em] 
                active:scale-[0.98] disabled:opacity-50 max-w-[400px]">
                ПРОДОЛЖИТЬ
            </a>
        </div>
    </div>

    <p class="text-zinc-400 text-center mt-6 text-xs max-w-[400px] mx-auto">
        Нажимая «Продолжить», вы подтверждаете, что сохранили ключ в надежном месте.
    </p>
</x-shop::layouts.split-screen>