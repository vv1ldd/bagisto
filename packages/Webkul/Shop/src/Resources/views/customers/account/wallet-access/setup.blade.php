<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
    <x-slot:title>Защита Кошелька</x-slot:title>

    <div
        class="bg-white border border-zinc-100 mb-6 ios-tile-relative p-6 sm:p-10 flex flex-col items-center justify-center min-h-[60vh] text-center">
        <a href="javascript:window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
            class="absolute top-4 left-4 p-2 text-zinc-400 hover:text-zinc-900 transition">
            <span class="icon-cancel text-xl"></span>
        </a>

        <div class="w-24 h-24 bg-zinc-50 rounded-full flex items-center justify-center mb-6 text-5xl shadow-inner">
            🔐
        </div>

        <h1 class="text-[24px] font-bold text-zinc-900 mb-4 tracking-tight">Защита Кошелька</h1>
        <p class="text-zinc-500 mb-8 max-w-sm text-[15px] leading-relaxed">
            Для доступа к вашим средствам необходимо настроить безопасный вход. Используйте биометрию (Face ID / Touch
            ID) для защиты ваших активов.
        </p>

        <div class="flex flex-col gap-4 w-full max-w-xs">
            <a href="{{ route('shop.customers.account.passkeys.index') }}"
                class="w-full bg-zinc-900 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-emerald-600 transition-all active:scale-95 flex items-center justify-center gap-3 text-[16px]">
                <span class="text-xl">😃</span>
                <span>Настроить Passkey</span>
            </a>
        </div>
    </div>

    <script>
        // Only Passkey setup remains
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-shop::layouts.account>