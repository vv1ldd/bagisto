<x-shop::layouts.split-screen title="Сохранение ключа восстановления">
    <div
        class=" bg-gradient-to-br from-[#F9F7FF] to-[#F1EAFF] p-6 md:p-8 flex flex-col items-center text-center relative overflow-hidden w-full shadow-[0_8px_32px_rgba(124,69,245,0.05)] border border-white">
        <!-- Decorative background elements -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#7C45F5]/10  blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-[#3B82F6]/10  blur-3xl"></div>


        <h3 class="text-[#4A1D96] text-[26px] md:text-3xl font-extrabold mb-4 tracking-tight leading-tight">
            Секретная фраза доступа</h3>

        <div class="space-y-3 mb-8">
            <p class="text-[#4A1D96]/90 text-[16px] leading-relaxed max-w-[480px]">
                Эта фраза из 12 слов — ваш единственный способ восстановить доступ, если вы потеряете Passkey или смените устройство.
            </p>
            <div class="flex flex-col items-center gap-3">
                <p class="inline-block px-5 py-2 bg-red-50 text-red-600 font-bold text-[14px] border border-red-100 shadow-sm">
                    НЕ ДЕЛАЙТЕ СКРИНШОТ
                </p>
                <p class="text-[#4A1D96]/90 text-[14px] leading-relaxed max-w-[480px]">
                    Запишите слова на бумагу в правильном порядке и храните в надежном месте.
                    <b>Она показывается только один раз!</b>
                </p>
            </div>
        </div>

        @php
            $mnemonic = session('recovery_key');
            $words = explode(' ', $mnemonic);
        @endphp

        <!-- Ledger-style Word Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 w-full mb-10">
            @foreach($words as $index => $word)
                <div class="flex items-center gap-3 bg-white border border-[#E9E1FF] p-4 shadow-sm hover:border-[#7C45F5] transition-colors">
                    <span class="text-[10px] font-black text-[#7C45F5]/40 w-4">{{ $index + 1 }}</span>
                    <span class="text-[#4A1D96] font-mono font-bold tracking-tight text-[15px] select-all">{{ $word }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col items-center gap-4 w-full">
            <a href="{{ route('shop.customers.account.profile.complete_registration') }}"
                class="flex w-full max-w-[320px] items-center justify-center gap-3 bg-[#7C45F5] px-8 py-4 text-[16px] font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 uppercase tracking-widest">
                Я записал фразу
            </a>
        </div>
    </div>

    <p class="text-zinc-400 text-center mt-6 text-xs max-w-[400px] mx-auto">
        Нажимая «Продолжить», вы подтверждаете, что сохранили ключ в надежном месте.
    </p>
</x-shop::layouts.split-screen>