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
            ID) или придумайте PIN-код.
        </p>

        <div class="flex flex-col gap-4 w-full max-w-xs">
            <a href="{{ route('shop.customers.account.passkeys.index') }}"
                class="w-full bg-zinc-900 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-emerald-600 transition-all active:scale-95 flex items-center justify-center gap-3 text-[16px]">
                <span class="text-xl">😃</span>
                <span>Настроить Passkey</span>
            </a>

            <button type="button" onclick="document.getElementById('pin-setup-modal').classList.remove('hidden')"
                class="w-full bg-white border-2 border-zinc-200 text-zinc-900 font-bold py-4 rounded-2xl hover:border-zinc-900 transition-all active:scale-95 text-[16px]">
                Создать PIN-код
            </button>
        </div>
    </div>

    <!-- PIN Setup Modal -->
    <div id="pin-setup-modal"
        class="fixed inset-0 z-[100] bg-white hidden flex-col items-center justify-center pb-12 animate-fade-in">
        <button type="button" onclick="document.getElementById('pin-setup-modal').classList.add('hidden')"
            class="absolute top-6 left-6 p-4 text-zinc-900 bg-zinc-100 rounded-full hover:bg-zinc-200 transition active:scale-90">
            <span class="icon-cancel text-xl"></span>
        </button>

        <h2 class="text-[20px] font-bold text-zinc-900 mb-8">Придумайте PIN-код</h2>

        <!-- PIN Dots -->
        <div class="flex gap-4 mb-8" id="setup-pin-dots">
            <!-- Rendered by JS -->
        </div>

        <button type="button" onclick="togglePinLength()" id="pin-length-toggle"
            class="text-zinc-500 hover:text-zinc-900 font-medium text-[14px] mb-8 transition">
            Параметры кода
        </button>

        <!-- Numpad -->
        <div class="grid grid-cols-3 gap-x-8 gap-y-6 max-w-[280px]">
            @for ($i = 1; $i <= 9; $i++)
                <button type="button" onclick="enterSetupDigit({{ $i }})"
                    class="w-20 h-20 rounded-full bg-zinc-50 hover:bg-zinc-200 flex items-center justify-center text-[28px] font-medium text-zinc-900 transition active:bg-zinc-300 focus:outline-none">
                    {{ $i }}
                </button>
            @endfor
            <div></div> <!-- Empty bottom left -->
            <button type="button" onclick="enterSetupDigit(0)"
                class="w-20 h-20 rounded-full bg-zinc-50 hover:bg-zinc-200 flex items-center justify-center text-[28px] font-medium text-zinc-900 transition active:bg-zinc-300 focus:outline-none">
                0
            </button>
            <button type="button" onclick="deleteSetupDigit()"
                class="w-20 h-20 rounded-full flex items-center justify-center text-[24px] text-zinc-600 hover:bg-zinc-100 transition active:bg-zinc-200 focus:outline-none">
                ⌫
            </button>
        </div>

        <form id="setup-pin-form" action="{{ route('shop.customers.account.wallet.setup.post') }}" method="POST"
            class="hidden">
            @csrf
            <input type="hidden" name="pin" id="setup-pin-input">
        </form>
    </div>

    <script>
        let setupPin = '';
        let MAX_PIN_LENGTH = 4;
        const dotsContainer = document.getElementById('setup-pin-dots');

        function renderDots() {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < MAX_PIN_LENGTH; i++) {
                const dot = document.createElement('div');
                dot.className = 'w-4 h-4 rounded-full border-2 border-zinc-300 transition-all duration-200';
                dotsContainer.appendChild(dot);
            }
        }

        function togglePinLength() {
            MAX_PIN_LENGTH = MAX_PIN_LENGTH === 4 ? 6 : 4;
            setupPin = ''; // Reset
            renderDots();
            updateSetupUI();
            const btn = document.getElementById('pin-length-toggle');
            if (MAX_PIN_LENGTH === 6) {
                btn.innerText = 'Переключить на 4-значный код';
            } else {
                btn.innerText = 'Параметры кода';
            }
        }

        function updateSetupUI() {
            const setupDots = dotsContainer.children;
            // Update dots
            for (let i = 0; i < MAX_PIN_LENGTH; i++) {
                if (i < setupPin.length) {
                    setupDots[i].classList.replace('border-zinc-300', 'bg-zinc-900');
                    setupDots[i].classList.replace('border-2', 'border-zinc-900');
                } else {
                    setupDots[i].classList.replace('bg-zinc-900', 'border-zinc-300');
                    setupDots[i].classList.replace('border-zinc-900', 'border-2');
                }
            }

            if (setupPin.length === MAX_PIN_LENGTH) {
                // Submit form
                document.getElementById('setup-pin-input').value = setupPin;

                // Visual feedback before submit
                setTimeout(() => {
                    document.getElementById('setup-pin-form').submit();
                }, 200);
            }
        }

        window.enterSetupDigit = function (digit) {
            if (setupPin.length < MAX_PIN_LENGTH) {
                setupPin += digit;
                updateSetupUI();
            }
        }

        window.deleteSetupDigit = function () {
            if (setupPin.length > 0) {
                setupPin = setupPin.slice(0, -1);
                updateSetupUI();
            }
        }

        renderDots();
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