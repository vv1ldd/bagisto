<x-shop::layouts.account :show-back="false" :has-header="false" :has-footer="false">
    <x-slot:title>Разблокировка Кошелька</x-slot:title>

    <div
        class="fixed inset-0 z-[9999] bg-zinc-950 flex flex-col items-center justify-center pb-12 animate-fade-in overflow-y-auto">

        <!-- Cancel Button to go back -->
        <a href="{{ route('shop.customers.account.index') }}"
            class="absolute top-10 left-10 p-4 text-zinc-400 hover:text-white transition active:scale-90 z-[10000]">
            <span class="icon-cancel text-2xl"></span>
        </a>

        <div class="flex flex-col items-center w-full max-w-sm px-6">
            @if($hasPasskey)
                <div class="mb-6 w-20 h-20 bg-zinc-900 border border-zinc-800 rounded-full flex items-center justify-center cursor-pointer hover:bg-zinc-800 transition shadow-2xl active:scale-95"
                    onclick="triggerPasskeyAuth()">
                    <span class="text-4xl">😁</span>
                </div>
                <h2 class="text-[14px] font-bold uppercase tracking-widest text-zinc-500 mb-10 cursor-pointer hover:text-zinc-300 transition"
                    onclick="triggerPasskeyAuth()">
                    Разблокировать Face ID
                </h2>
            @else
                <div
                    class="mb-10 w-20 h-20 bg-zinc-900 border border-zinc-800 rounded-full flex items-center justify-center text-4xl shadow-2xl">
                    🔒
                </div>
            @endif

            <h2 class="text-[22px] font-bold text-white mb-10 text-center leading-tight" id="unlock-title">
                @if(session()->has('error'))
                    <span class="text-red-400">{{ session('error') }}</span>
                @else
                    Введите PIN-код
                @endif
            </h2>

            <!-- PIN Dots -->
            <div class="flex gap-5 mb-16" id="unlock-pin-dots">
                <!-- Rendered by JS -->
            </div>

            <!-- Numpad -->
            <div class="grid grid-cols-3 gap-x-10 gap-y-8">
                @for ($i = 1; $i <= 9; $i++)
                    <button type="button" onclick="enterUnlockDigit({{ $i }})"
                        class="w-20 h-20 rounded-full bg-zinc-900/50 hover:bg-zinc-800 border border-zinc-800/50 flex items-center justify-center text-[32px] font-medium text-white transition active:scale-90 active:bg-zinc-700/50 focus:outline-none shadow-sm">
                        {{ $i }}
                    </button>
                @endfor
                <div></div> <!-- Empty bottom left -->
                <button type="button" onclick="enterUnlockDigit(0)"
                    class="w-20 h-20 rounded-full bg-zinc-900/50 hover:bg-zinc-800 border border-zinc-800/50 flex items-center justify-center text-[32px] font-medium text-white transition active:scale-90 active:bg-zinc-700/50 focus:outline-none shadow-sm">
                    0
                </button>
                <button type="button" onclick="deleteUnlockDigit()"
                    class="w-20 h-20 rounded-full flex items-center justify-center text-[28px] text-zinc-500 hover:text-white hover:bg-zinc-900/30 transition active:scale-90 focus:outline-none">
                    ⌫
                </button>
            </div>
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