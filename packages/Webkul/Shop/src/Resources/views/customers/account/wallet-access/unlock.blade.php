<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
    <x-slot:title>Разблокировка Кошелька</x-slot:title>

    <div
        class="fixed inset-0 z-[9999] backdrop-blur-3xl bg-white/70 flex flex-col items-center justify-center pb-12 animate-fade-in overflow-y-auto overflow-x-hidden">

        <!-- Cancel Button to go back -->
        <a href="{{ route('shop.customers.account.index') }}"
            class="absolute top-10 right-10 w-10 h-10 flex items-center justify-center bg-red-500 text-white hover:bg-red-600 transition active:scale-90 z-[10000]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>

        <div class="flex flex-col items-center w-full max-w-sm px-8">
            <div id="auth-content" class="w-full flex flex-col items-center">
                @if($hasPasskey)
                    <div class="mb-8 w-24 h-24 bg-white border border-zinc-200 rounded-[2.5rem] flex items-center justify-center cursor-pointer hover:border-[#7C45F5] hover:shadow-2xl transition-all duration-500 active:scale-95 group shadow-xl"
                        onclick="triggerPasskeyAuth()">
                        <span class="text-5xl group-hover:scale-110 transition-transform">👤</span>
                    </div>
                    
                    <h1 class="text-[28px] font-bold text-zinc-900 mb-2 text-center tracking-tight">Вход в кошелек</h1>
                    <p class="text-zinc-500 text-[15px] mb-10 text-center leading-relaxed">
                        Используйте биометрические данные для быстрого доступа.
                    </p>

                    <button 
                        onclick="triggerPasskeyAuth()"
                        class="w-full bg-[#7C45F5] text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[16px] mb-6">
                        <span>Разблокировать</span>
                    </button>

                    @if($hasPin)
                        <button 
                            onclick="showPinPad()"
                            class="text-[14px] font-semibold text-zinc-400 hover:text-zinc-600 transition tracking-wide">
                            Использовать ПИН-код
                        </button>
                    @endif
                @elseif($hasPin)
                    {{-- Only PIN available --}}
                    <div id="pin-entry-container" class="w-full flex flex-col items-center">
                        <div class="mb-8 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-[2.5rem] flex items-center justify-center text-5xl shadow-inner">
                            🔒
                        </div>
                        
                        <h1 class="text-[28px] font-bold text-zinc-900 mb-6 text-center tracking-tight">Введите ПИН</h1>
                        
                        <!-- PIN Dots -->
                        <div class="flex gap-4 mb-12" id="pin-dots">
                            @for($i=0; $i<$pinLength; $i++)
                                <div class="w-4 h-4 rounded-full border-2 border-zinc-200 transition-all duration-300"></div>
                            @endfor
                        </div>

                        <!-- Numeric Keypad -->
                        <div class="grid grid-cols-3 gap-x-6 gap-y-4 w-full max-w-[280px]">
                            @for($i=1; $i<=9; $i++)
                                <button onclick="handlePinInput('{{ $i }}')" class="pin-key">{{ $i }}</button>
                            @endfor
                            <button onclick="handlePinClear()" class="pin-key !text-[14px] !font-bold text-zinc-400">Сброс</button>
                            <button onclick="handlePinInput('0')" class="pin-key">0</button>
                            <button onclick="handlePinBackspace()" class="pin-key text-zinc-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- No Passkey, No PIN --}}
                    <div class="mb-8 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-[2.5rem] flex items-center justify-center text-5xl shadow-inner">
                        🔒
                    </div>
                    
                     <h1 class="text-[28px] font-bold text-zinc-900 mb-2 text-center tracking-tight">Кошелек защищен</h1>
                     <p class="text-zinc-500 text-[15px] mb-10 text-center leading-relaxed">
                        Настройте ключ доступа для работы с кошельком.
                    </p>

                    <a href="{{ route('shop.customers.account.passkeys.index') }}"
                        class="w-full bg-zinc-900 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-black transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[16px]">
                        <span>Настроить Passkey</span>
                    </a>
                @endif
            </div>

            <p class="text-[13px] font-medium text-zinc-400 mt-12" id="unlock-status">
                @if(session()->has('error'))
                    <span class="text-red-500">{{ session('error') }}</span>
                @else
                    Защищено технологией Passkey
                @endif
            </p>

            <meta name="csrf-token" content="{{ csrf_token() }}">
        </div>

        <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

        <script>
            const hasPasskey = {{ $hasPasskey ? 'true' : 'false' }};
            const pinLength = {{ $pinLength }};
            let currentPin = '';

            // PIN Keypad Logic
            window.handlePinInput = function(num) {
                if (currentPin.length >= pinLength) return;
                currentPin += num;
                updatePinDots();
                if (currentPin.length === pinLength) {
                    verifyPin();
                }
            }

            window.handlePinBackspace = function() {
                currentPin = currentPin.slice(0, -1);
                updatePinDots();
            }

            window.handlePinClear = function() {
                currentPin = '';
                updatePinDots();
            }

            function updatePinDots() {
                const dots = document.querySelectorAll('#pin-dots div');
                dots.forEach((dot, i) => {
                    if (i < currentPin.length) {
                        dot.classList.add('bg-[#7C45F5]', 'border-[#7C45F5]', 'scale-110');
                    } else {
                        dot.classList.remove('bg-[#7C45F5]', 'border-[#7C45F5]', 'scale-110');
                    }
                });
            }

            async function verifyPin() {
                const statusEl = document.getElementById('unlock-status');
                statusEl.innerHTML = '<span class="text-[#7C45F5] animate-pulse">Проверка...</span>';
                
                try {
                    const response = await fetch("{{ route('shop.customers.account.wallet.verify_pin') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ pin: currentPin })
                    });

                    const result = await response.json();
                    if (result.success) {
                        statusEl.innerHTML = '<span class="text-emerald-500">Успешно!</span>';
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        statusEl.innerHTML = '<span class="text-red-500">Неверный ПИН</span>';
                        handlePinClear();
                        setTimeout(() => statusEl.innerText = 'Защищено технологией Passkey', 2000);
                    }
                } catch (e) {
                    statusEl.innerHTML = '<span class="text-red-500">Ошибка связи</span>';
                }
            }

            window.showPinPad = function() {
                // For users with both, allow switching to PIN
                const content = document.getElementById('auth-content');
                content.innerHTML = `
                    <div id="pin-entry-container" class="w-full flex flex-col items-center animate-fade-in">
                        <div class="mb-8 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-[2.5rem] flex items-center justify-center text-5xl shadow-inner">
                            🔒
                        </div>
                        <h1 class="text-[28px] font-bold text-zinc-900 mb-6 text-center tracking-tight">Введите ПИН</h1>
                        <div class="flex gap-4 mb-12" id="pin-dots">
                            ${Array(pinLength).fill('<div class="w-4 h-4 rounded-full border-2 border-zinc-200 transition-all duration-300"></div>').join('')}
                        </div>
                        <div class="grid grid-cols-3 gap-x-6 gap-y-4 w-full max-w-[280px]">
                            ${[1,2,3,4,5,6,7,8,9].map(i => `<button onclick="handlePinInput('${i}')" class="pin-key">${i}</button>`).join('')}
                            <button onclick="handlePinClear()" class="pin-key !text-[14px] !font-bold text-zinc-400">Сброс</button>
                            <button onclick="handlePinInput('0')" class="pin-key">0</button>
                            <button onclick="handlePinBackspace()" class="pin-key text-zinc-400 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                        <button 
                            onclick="window.location.reload()"
                            class="text-[14px] font-semibold text-zinc-400 hover:text-zinc-600 transition mt-10 tracking-wide">
                            Назад к Face ID
                        </button>
                    </div>
                `;
            }

            // Passkey Authentication Logic
            window.triggerPasskeyAuth = async function () {
                if (!hasPasskey) return;
                const statusEl = document.getElementById('unlock-status');
                statusEl.innerHTML = '<span class="text-[#7C45F5] animate-pulse">Ожидание Face ID...</span>';

                try {
                    const { startAuthentication } = SimpleWebAuthnBrowser;
                    const optionsResp = await fetch("{{ route('passkeys.login-options') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    if (!optionsResp.ok) throw new Error();
                    const options = await optionsResp.json();
                    const asseResp = await startAuthentication(options);

                    const verificationResp = await fetch("{{ route('passkeys.login') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ start_authentication_response: asseResp, remember: false })
                    });

                    if (verificationResp.ok) {
                        statusEl.innerHTML = '<span class="text-emerald-500">Успешно!</span>';
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        const err = await verificationResp.json();
                        statusEl.innerHTML = `<span class="text-red-400">Ошибка: ${err.message || 'Сбой'}</span>`;
                    }
                } catch (error) {
                    if (error.name !== 'NotAllowedError') {
                        statusEl.innerHTML = `<span class="text-red-400">Ошибка Passkey</span>`;
                    } else {
                        statusEl.innerText = 'Защищено технологией Passkey';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                if (hasPasskey) {
                    setTimeout(triggerPasskeyAuth, 800);
                }
            });
        </script>

        <style>
            .pin-key {
                @apply w-16 h-16 rounded-full bg-white border border-zinc-100 text-2xl font-medium text-zinc-900 transition-all active:scale-90 active:bg-zinc-50 shadow-sm flex items-center justify-center;
                width: 64px;
                height: 64px;
                border-radius: 9999px;
                background: white;
                border-width: 1px;
                border-color: #f4f4f5;
                font-size: 1.5rem;
                font-weight: 500;
                color: #18181b;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .pin-key:active {
                transform: scale(0.9);
                background: #f4f4f5;
            }

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

            /* Prevent scrolling while locked */
            body {
                overflow: hidden;
            }
        </style>
</x-shop::layouts.account>