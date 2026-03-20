<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true" :is-cardless="true">
    <x-slot:title>Настройка Кошелька</x-slot:title>

    <div class="flex flex-col items-center justify-center min-h-[50vh] py-12 px-6 animate-page-entry relative">
        
        {{-- Ambient background glows --}}
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-indigo-500/5 rounded-full blur-[80px] animate-blob z-0"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-purple-500/5 rounded-full blur-[80px] animate-blob animation-delay-2000 z-0"></div>

        <div class="flex flex-col items-center w-full max-w-[420px] relative z-10">
            <div class="w-full bg-white border border-zinc-100/80 rounded-[2.5rem] p-10 shadow-[0_24px_80px_rgba(0,0,0,0.06)] flex flex-col items-center text-center">
                
                <div class="mb-8 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-3xl flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-zinc-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>

                <h1 class="text-[22px] font-black text-zinc-900 mb-4 uppercase tracking-tight leading-tight">Безопасный доступ</h1>
                <p class="text-zinc-500 text-[14px] mb-10 leading-relaxed font-medium">
                    Чтобы использовать кошелек, необходимо настроить безопасный вход с помощью биометрии или ключа доступа Face ID / Touch ID.
                </p>

                <div class="flex flex-col gap-4 w-full">
                    <a href="{{ route('shop.customers.account.passkeys.index') }}"
                        class="w-full bg-[#7C45F5] text-white font-black py-4 rounded-2xl shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[13px] uppercase tracking-widest">
                        <span>Создать ключ доступа</span>
                    </a>

                    <a href="{{ route('shop.customers.account.index') }}" class="text-[11px] font-black text-zinc-400 hover:text-[#7C45F5] transition-colors duration-300 uppercase tracking-[0.2em] flex items-center justify-center gap-2 mt-2">
                         <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                         На главную
                    </a>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-center gap-2 py-2 px-5 bg-zinc-50 rounded-full border border-zinc-100 shadow-sm">
                <div class="w-1.5 h-1.5 rounded-full bg-[#7C45F5] animate-pulse"></div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">
                    Wallet Security Setup
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(20px, -30px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.98); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-page-entry {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-blob {
            animation: blob 8s infinite alternate;
        }

        .animation-delay-2000 { animation-delay: 2s; }
    </style>
</x-shop::layouts.account>