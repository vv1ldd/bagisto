<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
    <x-slot:title>Настройка Кошелька</x-slot:title>

    <div
        class="fixed inset-0 z-[9999] backdrop-blur-3xl bg-white/70 flex flex-col items-center justify-center pb-12 animate-fade-in overflow-y-auto overflow-x-hidden">

        <!-- Cancel Button to go back -->
        <a href="{{ route('shop.customers.account.index') }}"
            class="absolute top-10 right-10 p-4 text-zinc-400 hover:text-zinc-900 transition active:scale-90 z-[10000]">
            <span class="icon-cancel text-2xl"></span>
        </a>

        <div class="flex flex-col items-center w-full max-w-sm px-8 text-center">
            <div class="mb-8 w-24 h-24 bg-white border border-zinc-200 rounded-[2.5rem] flex items-center justify-center shadow-xl">
                <span class="text-5xl">🛡️</span>
            </div>

            <h1 class="text-[28px] font-bold text-zinc-900 mb-4 tracking-tight">Безопасный доступ</h1>
            <p class="text-zinc-500 text-[15px] mb-10 leading-relaxed">
                Чтобы использовать Meanly Wallet, необходимо настроить безопасный вход с помощью биометрии или ключа доступа.
            </p>

            <div class="flex flex-col gap-4 w-full">
                <a href="{{ route('shop.customers.account.passkeys.index') }}"
                    class="w-full bg-[#7C45F5] text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[16px]">
                    <span class="text-xl">😃</span>
                    <span>Создать ключ доступа</span>
                </a>

                <p class="text-[13px] font-medium text-zinc-400 mt-6">
                    Это обеспечит безопасность ваших средств и быстрый доступ к транзакциям.
                </p>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                backdrop-filter: blur(0px);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(40px);
            }
        }

        /* Prevent scrolling while setup is shown */
        body {
            overflow: hidden;
        }
    </style>
</x-shop::layouts.account>