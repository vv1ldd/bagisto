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
                    Использование Passkey для входа
                @endif
            </h2>

            <div class="mt-10">
                <p class="text-zinc-500 text-[14px]">
                    @if($hasPasskey)
                        Используйте биометрические данные для разблокировки кошелька.
                    @else
                        У вас не настроен Passkey.
                        <a href="{{ route('shop.customers.account.passkeys.index') }}"
                            class="text-blue-500 underline">Перейти к настройке</a>
                    @endif
                </p>
            </div>

            <!-- Hidden token for WebAuthn -->
            <meta name="csrf-token" content="{{ csrf_token() }}">
        </div>

        <!-- Passkey JS from standard implementation if needed -->
        <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

        <script>
            const hasPasskey = {{ $hasPasskey ? 'true' : 'false' }};

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