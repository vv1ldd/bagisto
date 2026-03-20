<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true">
    <x-slot:title>Разблокировка Кошелька</x-slot:title>

    <div
        class="fixed inset-0 z-[9999] backdrop-blur-[100px] bg-[#F8F9FF]/70 flex flex-col items-center justify-center animate-page-entry overflow-hidden">
        
        {{-- Ambient background glows --}}
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-500/5 rounded-full blur-[120px] animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-500/5 rounded-full blur-[120px] animate-blob animation-delay-2000"></div>

        <div class="flex flex-col items-center w-full max-w-[420px] px-6 relative z-10">
            <div id="auth-content" class="w-full bg-white/40 backdrop-blur-2xl border border-white/60 rounded-[3rem] p-10 shadow-[0_24px_80px_rgba(0,0,0,0.06),inset_0_0_0_1px_rgba(255,255,255,0.4)] flex flex-col items-center group transition-all duration-700 hover:shadow-[0_32px_96px_rgba(124,69,245,0.08)]">
                
                @if($hasPasskey)
                    {{-- Automated Flow: Minimal UI --}}
                    <div id="auth-status-container" class="flex flex-col items-center w-full">
                        <div class="mb-10 w-24 h-24 bg-gradient-to-br from-white to-indigo-50/50 border border-white/80 rounded-[2.5rem] flex items-center justify-center cursor-pointer hover:scale-105 transition-all duration-500 shadow-[0_12px_40px_rgba(0,0,0,0.04)] relative group/icon"
                            onclick="triggerPasskeyAuth()">
                            <span class="text-5xl group-hover/icon:scale-110 transition-transform duration-500 filter drop-shadow-sm select-none">👤</span>
                            
                            {{-- Scanning animation overlay --}}
                            <div class="absolute inset-0 rounded-[2.5rem] border-2 border-[#7C45F5] opacity-0 group-hover/icon:opacity-20 animate-ping animation-slow"></div>
                            <div class="absolute -inset-1 rounded-[2.8rem] border border-[#7C45F5]/10 opacity-0 group-hover/icon:opacity-100 transition-opacity duration-500"></div>
                        </div>
                        
                        <h1 class="text-[24px] font-bold text-[#0F172A] mb-2 text-center tracking-tight leading-tight">Вход в кошелек</h1>
                        <p id="auth-instruction" class="text-slate-500 text-[15px] mb-10 text-center leading-relaxed font-medium transition-all duration-300">
                            Подготовьте Face ID
                        </p>

                        {{-- Fallback button (initially hidden) --}}
                        <div id="retry-container" class="hidden w-full flex flex-col items-center animate-slide-up">
                            <button 
                                onclick="triggerPasskeyAuth()"
                                class="w-full bg-[#7C45F5] text-white font-bold py-4 rounded-[1.2rem] shadow-[0_8px_24px_rgba(124,69,245,0.25)] hover:shadow-[0_12px_32px_rgba(124,69,245,0.35)] hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-[15px] mb-6">
                                <span class="tracking-tight">Попробовать снова</span>
                            </button>
                            
                            <a href="{{ route('shop.customers.account.index') }}" class="text-[13px] font-bold text-slate-400 hover:text-[#7C45F5] transition-colors duration-300 uppercase tracking-widest flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                Назад
                            </a>
                        </div>
                    </div>
                @else
                    {{-- No Passkey --}}
                    <div class="mb-8 w-24 h-24 bg-gradient-to-br from-slate-50 to-slate-100 border border-white rounded-[2.5rem] flex items-center justify-center text-5xl shadow-inner select-none">
                        🔒
                    </div>
                    
                    <h1 class="text-[26px] font-bold text-[#0F172A] mb-3 text-center tracking-tight leading-tight">Кошелек защищен</h1>
                    <p class="text-slate-500 text-[15px] mb-10 text-center leading-relaxed font-medium">
                        Настройте ключ доступа для работы с функционалом кошелька.
                    </p>

                    <a href="{{ route('shop.customers.account.passkeys.index') }}"
                        class="w-full bg-[#0F172A] text-white font-bold py-4 rounded-[1.2rem] shadow-[0_8px_24px_rgba(15,23,42,0.15)] hover:bg-black transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-[15px]">
                        <span class="tracking-tight">Настроить Passkey</span>
                    </a>
                @endif
            </div>

            <div class="mt-8 flex items-center justify-center gap-2 py-2 px-4 bg-white/40 backdrop-blur-md rounded-full border border-white/60 shadow-sm transition-all duration-500 animate-slide-up animation-delay-500">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest" id="unlock-status">
                    @if(session()->has('error'))
                        <span class="text-red-500">{{ session('error') }}</span>
                    @else
                        End-to-end Encryption
                    @endif
                </p>
            </div>

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
                
                instructionEl.innerHTML = 'Ожидание Face ID...';
                instructionEl.classList.add('animate-pulse', 'active-state');
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
                        instructionEl.classList.remove('animate-pulse', 'active-state');
                        statusEl.innerHTML = '<span class="text-emerald-500">Успешно!</span>';
                        setTimeout(() => window.location.reload(), 300);
                    } else {
                        const err = await verificationResp.json();
                        instructionEl.innerText = 'Ошибка проверки';
                        instructionEl.classList.remove('animate-pulse', 'active-state');
                        statusEl.innerHTML = `<span class="text-red-400">Сбой: ${err.message || 'Ошибка'}</span>`;
                        retryContainer.classList.remove('hidden');
                    }
                } catch (error) {
                    instructionEl.classList.remove('animate-pulse', 'active-state');
                    if (error.name !== 'NotAllowedError') {
                        instructionEl.innerText = 'Ошибка Passkey';
                        statusEl.innerHTML = `<span class="text-red-400">Техническая ошибка</span>`;
                    } else {
                        instructionEl.innerText = 'Вход отменен';
                        statusEl.innerText = 'Encryption Active';
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
            @keyframes pageEntry {
                from { opacity: 0; backdrop-filter: blur(0px); }
                to { opacity: 1; backdrop-filter: blur(100px); }
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.95); }
                100% { transform: translate(0px, 0px) scale(1); }
            }

            .animate-page-entry {
                animation: pageEntry 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .animate-slide-up {
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .animate-blob {
                animation: blob 7s infinite alternate;
            }

            .animation-delay-500 { animation-delay: 0.5s; }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-slow { animation-duration: 3s; }

            /* Prevent scrolling while locked */
            body {
                overflow: hidden;
            }

            #auth-instruction.active-state {
                color: #7C45F5;
                font-weight: 700;
                transform: scale(1.02);
            }
        </style>
</x-shop::layouts.account>