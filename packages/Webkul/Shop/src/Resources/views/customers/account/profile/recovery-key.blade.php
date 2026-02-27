<x-shop::layouts.split-screen title="Сохраните резервный ключ!">
    <div class="w-full mx-auto z-10 relative">
        <h3 class="text-3xl font-extrabold text-[#4A1D96] mb-4 text-center tracking-tight">
            Сохраните резервный ключ!
        </h3>

        <div class="space-y-4 mb-8 text-center max-w-[480px] mx-auto">
            <p class="text-[16px] text-zinc-500 leading-relaxed">
                Это ваш супер-секретный ключ доступа.
            </p>
            <p
                class="inline-block px-5 py-2 bg-red-50 text-red-600 rounded-full font-bold text-[15px] border border-red-100 shadow-sm animate-pulse">
                Пожалуйста, НЕ делайте скриншот.
            </p>
            <p class="text-[15px] text-zinc-500 leading-relaxed mt-2">
                Обязательно <strong>запишите его на бумагу</strong> и храните надежно. Это <strong>единственный</strong>
                способ восстановления доступа.
            </p>
            <p class="text-[#7C45F5] font-bold text-xs tracking-widest uppercase mt-4 block">
                Он показывается только один раз!
            </p>
        </div>

        <div class="group relative w-full mb-8">
            <div
                class="absolute -inset-1 bg-[#7C45F5] rounded-3xl blur opacity-20 group-hover:opacity-30 transition duration-1000 group-hover:duration-200">
            </div>
            <div
                class="relative w-full bg-white border border-[#E9E1FF] rounded-2xl p-6 text-[#4A1D96] font-mono font-extrabold tracking-[0.15em] text-xl md:text-2xl text-center select-all break-all shadow-inner">
                {{ session('recovery_key') }}
            </div>
        </div>

        <div class="flex flex-col items-center justify-center w-full">
            <a href="{{ route('shop.customers.account.profile.complete_registration') }}"
                class="flex w-full max-w-[320px] items-center justify-center gap-3 rounded-full bg-[#7C45F5] px-8 py-4 text-[15px] font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-xl shadow-[#7C45F5]/25">
                Продолжить
            </a>

            <p class="text-zinc-400 text-center mt-6 text-xs max-w-[400px] mx-auto">
                * Без этого ключа мы не сможем вам помочь восстановить доступ к аккаунту!
            </p>
        </div>
    </div>
</x-shop::layouts.split-screen>