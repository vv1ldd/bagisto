<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Сохранение сид-фразы
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4">
        <div class="bg-white p-8 md:p-10 flex flex-col items-center text-center relative overflow-hidden w-full max-w-3xl shadow-xl border border-[#e2d9ff] rounded-[2rem]">
            
            {{-- Protection Icon --}}
            <div class="mb-8 flex flex-col items-center">
                 <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-xl shadow-[#7C45F5]/30 mb-6 transition-transform hover:scale-105 duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-[#1a0050] text-3xl font-black tracking-tight mb-3">Сид-фраза доступа</h1>
                <p class="text-zinc-500 text-base max-w-md mx-auto">Эта фраза — единственный способ восстановить доступ к вашему аккаунту.</p>
            </div>

            <div class="flex flex-col items-center gap-4 mb-10">
                <div class="inline-flex items-center px-4 py-1.5 bg-red-50 text-red-600 font-bold text-xs border border-red-100 rounded-xl shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    НЕ ДЕЛАЙТЕ СКРИНШОТ
                </div>
                <p class="text-zinc-500 text-[15px] leading-relaxed max-w-[520px]">
                    Запишите слова на бумагу в правильном порядке. <br class="hidden md:block">
                    <span class="font-black text-[#1a0050]">Она показывается только один раз!</span>
                </p>
            </div>

            <!-- Word Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full mb-10">
                @foreach($words as $index => $word)
                    <div class="flex items-center gap-4 bg-[#f5f4fc] border border-[#e2d9ff] py-4 px-5 shadow-sm hover:border-[#7C45F5] transition-all group rounded-2xl">
                        <span class="text-xs font-black text-[#7C45F5]/40 select-none group-hover:text-[#7C45F5]/60 transition-colors w-5">{{ $index + 1 }}</span>
                        <span class="text-[#1a0050] font-mono font-bold tracking-tight text-lg select-all break-keep">{{ $word }}</span>
                    </div>
                @endforeach
            </div>

            <div class="w-full flex flex-col items-center gap-6">
                <a href="{{ route('shop.customers.account.profile.verify_recovery_key') }}"
                    class="flex w-full items-center justify-center gap-3 !rounded-2xl bg-[#7C45F5] px-8 py-5 text-center text-base font-bold text-white shadow-2xl shadow-[#7C45F5]/40 transition-all hover:bg-[#6534d4] active:scale-[0.98] max-w-sm group">
                    <span>Я ЗАПИСАЛ СЛОВА</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                
                <p class="text-zinc-400 text-[13px] max-w-sm leading-relaxed">
                    Продолжая, вы подтверждаете, что сохранили ключ в надежном месте.
                </p>
            </div>
        </div>
    </div>
</x-shop::layouts>