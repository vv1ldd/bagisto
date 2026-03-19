<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
    <x-slot:title>Разблокировка Кошелька</x-slot:title>

    <div
        class="fixed inset-0 z-[9999] backdrop-blur-3xl bg-white/70 flex flex-col items-center justify-center pb-12 animate-fade-in overflow-y-auto overflow-x-hidden">

        <div class="flex flex-col items-center w-full max-w-sm px-8">
            <div id="auth-content" class="w-full flex flex-col items-center">
                @if($hasPasskey)
                    {{-- Automated Flow: Minimal UI --}}
                    <div id="auth-status-container" class="flex flex-col items-center animate-fade-in">
                        <div class="mb-12 w-28 h-28 bg-white/10 backdrop-blur-xl border border-white/20 rounded-[3rem] flex items-center justify-center cursor-pointer hover:scale-105 transition-all duration-500 shadow-2xl relative group"
                            onclick="triggerPasskeyAuth()">
                            <span class="text-6xl group-hover:scale-110 transition-transform duration-500">👤</span>
                            {{-- Scanning animation overlay --}}
                            <div class="absolute inset-0 rounded-[3rem] border-2 border-[#7C45F5] opacity-20 group-hover:opacity-40 animate-pulse"></div>
                        </div>
                        
                        <h1 class="text-[26px] font-bold text-zinc-900 mb-3 text-center tracking-tight">Вход в кошелек</h1>
                        <p id="auth-instruction" class="text-zinc-500 text-[15px] mb-12 text-center leading-relaxed max-w-[240px]">
                            Инициация Face ID...
                        </p>

                        {{-- Fallback button (initially hidden) --}}
                        <div id="retry-container" class="hidden w-full transition-all duration-500">
                            <button 
                                onclick="triggerPasskeyAuth()"
                                class="w-full bg-[#7C45F5] text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-[16px] mb-4">
                                <span>Попробовать снова</span>
                            </button>
                            
                            <a href="{{ route('shop.customers.account.index') }}" class="text-[14px] font-semibold text-zinc-400 hover:text-zinc-600 transition block text-center">
                                Назад в профиль
                            </a>
                        </div>
                    </div>
                @else
                    {{-- No Passkey --}}
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

            // Passkey Authentication Logic
            window.triggerPasskeyAuth = async function () {
                if (!hasPasskey) return;
                
                const statusEl = document.getElementById('unlock-status');
                const instructionEl = document.getElementById('auth-instruction');
                const retryContainer = document.getElementById('retry-container');
                
                instructionEl.innerHTML = '<span class="text-[#7C45F5] font-semibold animate-pulse">Ожидание Face ID...</span>';
                retryContainer.classList.add('hidden');

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
                    
                    // Small delay before system prompt for smoother transition
                    const asseResp = await startAuthentication(options);

                    const verificationResp = await fetch("{{ route('passkeys.login') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ start_authentication_response: JSON.stringify(asseResp), remember: false })
                    });

                    if (verificationResp.ok) {
                        instructionEl.innerHTML = '<span class="text-emerald-500">Доступ разрешен</span>';
                        statusEl.innerHTML = '<span class="text-emerald-500">Успешно!</span>';
                        setTimeout(() => window.location.reload(), 300);
                    } else {
                        const err = await verificationResp.json();
                        instructionEl.innerText = 'Ошибка проверки';
                        statusEl.innerHTML = `<span class="text-red-400">Сбой: ${err.message || 'Ошибка'}</span>`;
                        retryContainer.classList.remove('hidden');
                    }
                } catch (error) {
                    if (error.name !== 'NotAllowedError') {
                        instructionEl.innerText = 'Ошибка Passkey';
                        statusEl.innerHTML = `<span class="text-red-400">Техническая ошибка</span>`;
                    } else {
                        instructionEl.innerText = 'Вход отменен';
                        statusEl.innerText = 'Защищено технологией Passkey';
                    }
                    retryContainer.classList.remove('hidden');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                if (hasPasskey) {
                    // Start immediately with a micro-timeout to ensure scripts are ready
                    setTimeout(triggerPasskeyAuth, 300);
                }
            });
        </script>

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

            /* Prevent scrolling while locked */
            body {
                overflow: hidden;
            }
        </style>
</x-shop::layouts.account>