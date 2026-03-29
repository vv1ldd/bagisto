<x-shop::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

    <div id="registration-wizard" class="animate-in fade-in slide-in-from-bottom-4 duration-1000">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('shop.customer.session.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-zinc-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-50 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all group">
                <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path d="M19 12H5m7-7l-7 7 7 7"/>
                </svg>
                Назад
            </a>
        </div>

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-zinc-900 mb-2 uppercase tracking-tighter leading-none">Создать<br>Аккаунт</h1>
            <p class="text-[11px] text-zinc-600 font-bold uppercase tracking-wider leading-relaxed">
                Безопасность <span class="text-[#7C45F5]">нового уровня</span> с Passkey.
            </p>
        </div>

        <!-- Nickname Input -->
        <div class="w-full mb-6 group">
            <div class="relative h-20 bg-zinc-50 border-3 border-zinc-900 rounded-2xl group-focus-within:bg-[#7C45F5]/5 transition-all duration-300 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]">
                <div class="h-full flex items-center px-8">
                    <div class="flex-grow flex items-center justify-end relative">
                        <span class="text-zinc-300 mr-3 text-2xl select-none font-black italic">@</span>
                        <input type="text" id="nickname-input" required
                            class="w-full text-right outline-none bg-transparent text-zinc-900 font-black text-3xl tracking-tighter placeholder:text-zinc-200 placeholder:uppercase"
                            placeholder="Никнейм" autocomplete="off" 
                            pattern="^[a-zA-Z0-9_\-\.]{3,30}$">
                        
                        <div id="nickname-icon" class="ml-4 w-7 h-7 flex-shrink-0 flex items-center justify-center opacity-0 transition-opacity"></div>
                    </div>
                </div>
            </div>
            <p id="nickname-status" class="text-[9px] mt-4 text-center font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-0"></p>
        </div>

        <!-- Create Button -->
        <button type="button" id="start-registration-btn" onclick="handlePasskeyRegistration(event)" disabled
            class="group relative flex w-full items-center justify-center gap-4 bg-[#7C45F5] text-white h-16 font-black uppercase tracking-[0.2em] text-[15px] transition-all hover:bg-[#8A5CF7] border-2 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden disabled:opacity-30 disabled:cursor-not-allowed mb-8">
            <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <circle cx="12" cy="11" r="3"></circle>
            </svg>
            Создать через Passkey
        </button>

        <p class="text-center text-[9px] text-zinc-600 font-black uppercase tracking-[0.1em] leading-relaxed max-w-[280px] mx-auto opacity-100">
            Нажимая кнопку, вы подтверждаете согласие с <a href="#" class="text-zinc-900 underline underline-offset-4 decoration-zinc-900 hover:text-[#7C45F5] transition-colors decoration-2">правилами сервиса</a>.
        </p>
    </div>

    {!! view_render_event('bagisto.shop.customers.signup.after') !!}

    @push('scripts')
        <script>
            let checkTimeout;
            let isUsernameValid = false;
            let isChecking = false;

            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'nickname-input') {
                    handleNicknameInput(e.target);
                }
            });

            function handleNicknameInput(inputEl) {
                clearTimeout(checkTimeout);
                
                const nicknameIcon = document.getElementById('nickname-icon');
                const nicknameStatus = document.getElementById('nickname-status');
                
                nicknameIcon.className = 'ml-4 w-6 h-6 flex-shrink-0 flex items-center justify-center opacity-0 transition-opacity';
                nicknameIcon.innerHTML = '';
                nicknameStatus.className = 'text-[10px] mt-4 text-center font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-0';
                
                isUsernameValid = false;
                updateButtonState();

                const val = inputEl.value.trim();
                if (!val) return;

                if (!/^[a-zA-Z0-9_\-\.]{3,30}$/.test(val)) {
                    showStatus('❌', 'Некорректный формат', 'error');
                    return;
                }

                showStatus('<div class="w-4 h-4 border-2 border-[#7C45F5] border-r-transparent rounded-full animate-spin"></div>', '', 'checking');
                isChecking = true;
                updateButtonState();

                checkTimeout = setTimeout(async () => {
                    try {
                        const formData = new FormData();
                        formData.append('username', val);
                        formData.append('_token', '{{ csrf_token() }}');

                        const res = await fetch('{{ route('shop.customers.register.check_username') }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'Accept': 'application/json' }
                        });

                        const data = await res.json();
                        isChecking = false;
                        
                        if (data.available) {
                            showStatus('✅', data.message, 'success');
                            isUsernameValid = true;
                        } else {
                            showStatus('❌', data.message, 'error');
                            isUsernameValid = false;
                        }
                    } catch (e) {
                        isChecking = false;
                        showStatus('❌', 'Ошибка проверки', 'error');
                    }
                    updateButtonState();
                }, 500);
            }

            function showStatus(iconHTML, text, type) {
                const icon = document.getElementById('nickname-icon');
                const status = document.getElementById('nickname-status');

                icon.innerHTML = iconHTML;
                icon.className = 'ml-4 w-6 h-6 flex-shrink-0 flex items-center justify-center opacity-100 transition-opacity';
                
                if (text) {
                    status.textContent = text;
                    status.className = `text-[10px] mt-4 text-center font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-100 ${type === 'success' ? 'text-emerald-400' : 'text-[#FF4D6D]'}`;
                } else {
                    status.className = `text-[10px] mt-4 text-center font-black uppercase tracking-widest min-h-[16px] transition-opacity duration-200 opacity-0`;
                }
            }

            function updateButtonState() {
                const btn = document.getElementById('start-registration-btn');
                if (btn) btn.disabled = !isUsernameValid || isChecking;
            }

            async function handlePasskeyRegistration(e) {
                e.preventDefault();
                if (!isUsernameValid) return;

                const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
                const btn = document.getElementById('start-registration-btn');
                const originalContent = btn.innerHTML;
                const nickname = document.getElementById('nickname-input').value.trim();

                if (!window.PublicKeyCredential) {
                    alert('Ваш браузер не поддерживает Passkey.');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<span class="animate-pulse">Подготовка...</span>';

                try {
                    const prepareRes = await fetch('{{ route('shop.customers.register.passkey.prepare') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username: nickname })
                    });

                    if (!prepareRes.ok) {
                        const errData = await prepareRes.json();
                        throw new Error(errData.message || 'Ошибка инициализации.');
                    }

                    const options = await prepareRes.json();
                    const optionsJSON = options.publicKey ? options.publicKey : options;



                    btn.innerHTML = '<span class="animate-pulse">Создайте ключ...</span>';
                    const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);
                    
                    btn.innerHTML = '<span class="animate-pulse">Сохранение...</span>';
                    const storeRes = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(attResp)
                    });

                    const data = await storeRes.json();
                    window.location.href = data.redirect_url || '{{ route('shop.customers.account.onboarding.security') }}';

                } catch (err) {
                    if (err.name !== 'NotAllowedError' && !err.message.includes('отмена')) {
                        console.error('[Passkey Error]', err);
                        alert(err.message);
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            }
        </script>
    @endpush
</x-shop::layouts.auth>