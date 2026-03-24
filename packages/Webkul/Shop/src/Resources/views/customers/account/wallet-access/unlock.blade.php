<x-shop::layouts.account :show-back="false" :has-header="true" :has-footer="true" :is-cardless="true">
    <div class="flex flex-col items-center justify-center min-h-[50vh] py-12 px-6 animate-page-entry relative">
        
        {{-- Ambient background glows (Relative to container) --}}
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-indigo-500/5 rounded-full blur-[80px] animate-blob z-0"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-purple-500/5 rounded-full blur-[80px] animate-blob animation-delay-2000 z-0"></div>

        <div class="flex flex-col items-center w-full max-w-[420px] relative z-10">
            <div id="auth-content" class="w-full bg-white border border-zinc-100/80 rounded-[2.5rem] p-10 shadow-[0_24px_80px_rgba(0,0,0,0.06)] flex flex-col items-center group transition-all duration-700">
                
                @if($hasPasskey)
                    {{-- Automated Flow: Minimal UI --}}
                    <div id="auth-status-container" class="flex flex-col items-center w-full">
                        <div class="mb-10 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-3xl flex items-center justify-center cursor-pointer hover:scale-105 transition-all duration-500 shadow-sm relative group/icon"
                            onclick="triggerPasskeyAuth()">
                            
                            {{-- Modern Lock Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-zinc-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            
                            {{-- Scanning animation overlay --}}
                            <div class="absolute inset-0 rounded-3xl border-2 border-[#7C45F5] opacity-0 group-hover/icon:opacity-20 animate-ping animation-slow"></div>
                        </div>
                        
                        <h1 class="text-[22px] font-black text-zinc-900 mb-2 text-center uppercase tracking-tight leading-tight">Защищенный вход</h1>
                        <p id="auth-instruction" class="text-zinc-400 text-[14px] mb-10 text-center leading-relaxed font-bold uppercase tracking-widest transition-all duration-300">
                            Ожидание Face ID...
                        </p>

                        {{-- Fallback button (initially hidden) --}}
                        <div id="retry-container" class="hidden w-full flex flex-col items-center animate-slide-up">
                            <button 
                                onclick="triggerPasskeyAuth()"
                                class="w-full bg-[#7C45F5] text-white font-black py-4 rounded-2xl shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6836d4] transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-[13px] uppercase tracking-widest">
                                <span>Попробовать снова</span>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- No Passkey --}}
                    <div class="mb-8 w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-3xl flex items-center justify-center shadow-sm">
                         <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-[22px] font-black text-zinc-900 mb-3 text-center uppercase tracking-tight leading-tight">Кошелек защищен</h1>
                    <p class="text-zinc-500 text-[14px] mb-10 text-center leading-relaxed font-medium">
                        Настройте ключ доступа для работы с функционалом кошелька.
                    </p>

                    <a href="{{ route('shop.customers.account.passkeys.index') }}"
                        class="w-full bg-zinc-900 text-white font-black py-4 rounded-2xl shadow-lg hover:bg-black transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-[13px] uppercase tracking-widest">
                        <span>Настроить Passkey</span>
                    </a>
                @endif
            </div>

            <div id="unlock-status" class="hidden"></div>

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
                    
                    // Robust base64url conversion for Safari
                    const toBase64Url = (str) => {
                        if (!str || typeof str !== 'string') return str;
                        return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                    };

                    if (options.challenge) options.challenge = toBase64Url(options.challenge);
                    if (options.allowCredentials) {
                        options.allowCredentials.forEach(cred => {
                            if (cred.id) cred.id = toBase64Url(cred.id);
                        });
                    }

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
                        statusEl.innerHTML = '<span class="text-emerald-500 font-black">Успешно!</span>';
                        setTimeout(() => window.location.reload(), 300);
                    } else {
                        const err = await verificationResp.json();
                        instructionEl.innerText = 'Ошибка проверки';
                        instructionEl.classList.remove('animate-pulse', 'active-state');
                        statusEl.innerHTML = `<span class="text-red-500 font-black">Сбой: ${err.message || 'Ошибка'}</span>`;
                        retryContainer.classList.remove('hidden');
                    }
                } catch (error) {
                    instructionEl.classList.remove('animate-pulse', 'active-state');
                    if (error.name !== 'NotAllowedError') {
                        instructionEl.innerText = 'Ошибка Passkey';
                        statusEl.innerHTML = `<span class="text-red-500 font-black">Сбой: Техническая ошибка</span>`;
                    } else {
                        instructionEl.innerText = 'Вход отменен';
                        statusEl.innerText = 'Encryption Active';
                    }
                    retryContainer.classList.remove('hidden');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                if (hasPasskey) {
                    setTimeout(triggerPasskeyAuth, 300);
                }
            });
        </script>

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
                animation: none; /* Removed pageEntry animation */
            }

            .animate-slide-up {
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .animate-blob {
                animation: blob 8s infinite alternate;
            }

            .animation-delay-500 { animation-delay: 0.5s; }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-slow { animation-duration: 3s; }

            #auth-instruction.active-state {
                color: #7C45F5;
            }
        </style>
</x-shop::layouts.account>