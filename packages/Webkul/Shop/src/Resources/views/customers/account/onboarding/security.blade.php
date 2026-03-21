<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Защитите аккаунт
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4">
        {{-- Protection Icon --}}
        <div class="mb-8 flex flex-col items-center">
             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-xl shadow-[#7C45F5]/30 mb-4 transition-transform hover:scale-105 duration-300">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h1 class="text-[#1a0050] text-3xl font-black tracking-tight mb-2">Защитите аккаунт</h1>
            <p class="text-zinc-500 text-base max-w-md text-center">Дополнительные шаги для безопасного доступа к вашему аккаунту</p>
        </div>

        <div class="w-full max-w-xl">
            {{-- Security Options Component --}}
            @include('shop::customers.account.security')
            
            <div class="mt-8 flex justify-center">
                <a href="{{ route('shop.customers.account.index') }}" 
                   class="inline-flex items-center gap-2 text-zinc-400 hover:text-[#7C45F5] font-bold text-sm transition-colors group">
                    <span>Перейти в личный кабинет</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-shop::layouts>
