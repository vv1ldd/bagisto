<x-shop::layouts.auth
    title="Защитите аккаунт"
    contentWidth="max-w-[500px]"
>
    <x-slot:header>
        <h1 class="text-white text-2xl md:text-3xl font-black uppercase tracking-tighter text-center">
            Защитите<br>Аккаунт
        </h1>
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-2 text-center">
            Дополнительные шаги для <span class="text-[#7C45F5]">безопасного доступа</span>
        </p>
    </x-slot>

    <div class="space-y-4">
        {{-- Security Options Component --}}
        @include('shop::customers.account.security-content', ['isOnboarding' => true])
        
        <div class="mt-6 flex justify-center">
            <a href="{{ session('registration_intended_url', route('shop.customers.account.index')) }}" 
               class="inline-flex items-center gap-3 text-zinc-500 hover:text-white font-black text-[10px] uppercase tracking-[0.3em] transition-all group">
                <span>Настроить позже</span>
                <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</x-shop::layouts.auth>
