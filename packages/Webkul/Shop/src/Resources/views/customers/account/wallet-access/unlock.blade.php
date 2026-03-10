<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="false">
    <x-slot:title>Разблокировка Кошелька</x-slot:title>

    <div class="fixed inset-0 z-[200] bg-zinc-900 flex flex-col items-center justify-center pb-12 animate-fade-in">

        <!-- Cancel Button to go back -->
        <a href="{{ route('shop.customers.account.index') }}"
            class="absolute top-6 left-6 p-4 text-white bg-zinc-800 rounded-full hover:bg-zinc-700 transition active:scale-90">
            <span class="icon-cancel text-xl"></span>
        </a>

        @if($hasPasskey)
            <div class="mb-8 w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center cursor-pointer hover:bg-zinc-700 transition active:scale-95"
                onclick="triggerPasskeyAuth()">
                <span class="text-3xl text-emerald-400">😁</span>
            </div>
            <h2 class="text-[16px] font-medium text-zinc-400 mb-8 cursor-pointer" onclick="triggerPasskeyAuth()">Используйте
                Face ID</h2>
        @else
            <div class="mb-4 w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center text-3xl">
                🔒
            </div>
        @endif

        <h2 class="text-[20px] font-bold text-white mb-8" id="unlock-title">
            @if(session()->has('error'))
                <span class="text-red-400">{{ session('error') }}</span>
            @else
                Введите PIN-код
            @endif
        </h2>

        <!-- PIN Dots -->
        <div class="flex gap-4 mb-16" id="unlock-pin-dots">
            <!-- Rendered by JS -->
        </div>

        <!-- Numpad -->
        <div class="grid grid-cols-3 gap-x-8 gap-y-6 max-w-[280px]">
            @for ($i = 1; $i <= 9; $i++)
                <button type="button" onclick="enterUnlockDigit({{ $i }})"
                    class="w-20 h-20 rounded-full bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 flex items-center justify-center text-[28px] font-medium text-white transition active:bg-zinc-600 focus:outline-none">
                    {{ $i }}
                </button>
            @endfor
            <div></div> <!-- Empty bottom left -->
            <button type="button" onclick="enterUnlockDigit(0)"
                class="w-20 h-20 rounded-full bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 flex items-center justify-center text-[28px] font-medium text-white transition active:bg-zinc-600 focus:outline-none">
                0
            </button>
            <button type="button" onclick="deleteUnlockDigit()"
                class="w-20 h-20 rounded-full flex items-center justify-center text-[24px] text-zinc-400 hover:bg-zinc-800 transition active:bg-zinc-700 focus:outline-none">
                ⌫
            </button>
        </div>

        <form id="unlock-pin-form" action="{{ route('shop.customers.account.wallet.unlock.post') }}" method="POST"
            class="hidden">
            @csrf
            <input type="hidden" name="pin" id="unlock-pin-input">
        </form>

        <!-- Hidden token for WebAuthn -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </div>

    <!-- Passkey JS from standard implementation if needed -->
    <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

    <script>
        let unlockPin = '';
        const MAX_PIN_LENGTH = {{ $pinLength ?? 4 }}; // 4 or 6 based on setup
        const hasPasskey = {{ $hasPasskey ? 'true' : 'false' }};
        const dotsContainer = document.getElementById('unlock-pin-dots');

        function renderDots() {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < MAX_PIN_LENGTH; i++) {
                const dot = document.createElement('div');
                dot.className = 'w-4 h-4 rounded-full border-2 border-zinc-600 transition-all duration-200';
                dotsContainer.appendChild(dot);
            }
        }

        function updateUnlockUI() {
            const unlockDots = dotsContainer.children;
            // Reset error text if typed
            if (unlockPin.length === 1) {
                document.getElementById('unlock-title').innerHTML = 'Введите PIN-код';
                document.getElementById('unlock-title').classList.remove('text-red-400');
                document.getElementById('unlock-title').classList.add('text-white');
            }

            // Update dots
            for (let i = 0; i < MAX_PIN_LENGTH; i++) {
                if (i < unlockPin.length) {
                    unlockDots[i].classList.replace('border-zinc-600', 'bg-white');
                    unlockDots[i].classList.replace('border-2', 'border-white');
                } else {
                    unlockDots[i].classList.replace('bg-white', 'border-zinc-600');
                    unlockDots[i].classList.replace('border-white', 'border-2');
                }
            }

            if (unlockPin.length === MAX_PIN_LENGTH) {
                // Submit form
                document.getElementById('unlock-pin-input').value = unlockPin;

                // Visual feedback before submit
                setTimeout(() => {
                    document.getElementById('unlock-pin-form').submit();
                }, 200);
            }
        }

        window.enterUnlockDigit = function (digit) {
            if (unlockPin.length < MAX_PIN_LENGTH) {
                unlockPin += digit;
                updateUnlockUI();
            }
        }

        window.deleteUnlockDigit = function () {
            if (unlockPin.length > 0) {
                unlockPin = unlockPin.slice(0, -1);
                updateUnlockUI();
            }
        }

        // Passkey Authentication Logic
        window.triggerPasskeyAuth = async function () {
            if (!hasPasskey) return;

            try {
                const { startAuthentication } = SimpleWebAuthnBrowser;

                // 1. Get options from server
                const optionsResp = await fetch("{{ route('passkeys.login-options') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!optionsResp.ok) throw new Error('Не удалось получить настройки Passkey');
                const options = await optionsResp.json();

                // 2. Prompt fingerprint/face
                const asseResp = await startAuthentication(options);

                // 3. Send response to server
                const verificationResp = await fetch("{{ route('passkeys.login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        start_authentication_response: asseResp,
                        remember: false
                    })
                });

                if (verificationResp.ok) {
                    // Success! Reload to let CheckWalletAccess pass us through
                    window.location.reload();
                } else {
                    const err = await verificationResp.json();
                    document.getElementById('unlock-title').innerHTML = `<span class="text-red-400">Ошибка: ${err.message || 'Сбой авторизации'}</span>`;
                }
            } catch (error) {
                console.error(error);
                if (error.name !== 'NotAllowedError') { // Skip if user simply dismissed the prompt
                    document.getElementById('unlock-title').innerHTML = `<span class="text-red-400">Не удалось использовать Passkey</span>`;
                }
            }
        }

        // Auto-trigger passkey if available
        document.addEventListener('DOMContentLoaded', () => {
            if (hasPasskey) {
                // Pre-warm WebAuthn call or auto trigger
                // We shouldn't auto-trigger immediately without user interaction in some browsers, 
                // but for a lock screen it's common to require a click or immediately trigger.
                // Passkey API allows auto-trigger if the system supports it, let's just wait for click for now to be safe, 
                // or we can auto-trigger. 
                setTimeout(triggerPasskeyAuth, 500);
            }
        });
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Lock screen covers footers/headers */
        body {
            overflow: hidden;
        }
    </style>
</x-shop::layouts.account>