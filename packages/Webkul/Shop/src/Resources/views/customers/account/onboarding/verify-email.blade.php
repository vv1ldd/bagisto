<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Подтверждение почты
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 text-[#1a0050]">
        
        <div class="w-full max-w-[440px] bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-purple-500/10 border border-[#e2d9ff]">
            
            <div class="mb-8 flex flex-col items-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#F0EFFF] mb-4">
                    <svg class="w-8 h-8 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black tracking-tight mb-2">Подтвердите почту</h1>
                <p class="text-zinc-500 text-sm text-center">Мы отправили 6-значный код на <br><span class="font-bold text-[#7C45F5]">{{ $email }}</span></p>
            </div>

            <form @submit.prevent="submitCode" id="code-form">
                <div class="mb-10">
                    <div class="relative flex justify-center gap-2">
                        @for ($i = 0; $i < 6; $i++)
                            <div class="otp-cell flex h-14 w-11 items-center justify-center bg-zinc-50 border-2 border-zinc-100 rounded-xl transition-all duration-200"
                                id="otp-cell-{{ $i }}">
                                <span class="text-2xl font-black text-zinc-300" id="otp-char-{{ $i }}">_</span>
                            </div>
                        @endfor

                        <input id="otp-input"
                            class="absolute inset-0 h-full w-full cursor-default opacity-0" type="tel"
                            name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                            autofocus 
                            oninput="
                                this.value = this.value.replace(/\D/g, '');
                                const val = this.value;
                                for(let i=0; i<6; i++) {
                                    const charEl = document.getElementById('otp-char-' + i);
                                    const cellEl = document.getElementById('otp-cell-' + i);
                                    if (val[i]) {
                                        charEl.textContent = val[i];
                                        charEl.classList.remove('text-zinc-300');
                                        charEl.classList.add('text-[#1a0050]');
                                        cellEl.classList.add('border-[#7C45F5]', 'bg-white', 'box-shadow-sm');
                                        cellEl.classList.remove('border-zinc-100', 'bg-zinc-50');
                                    } else {
                                        charEl.textContent = '_';
                                        charEl.classList.add('text-zinc-300');
                                        charEl.classList.remove('text-[#1a0050]');
                                        cellEl.classList.remove('border-[#7C45F5]', 'bg-white', 'box-shadow-sm');
                                        cellEl.classList.add('border-zinc-100', 'bg-zinc-50');
                                    }
                                }
                                if(val.length === 6) {
                                    // Auto-submit after small delay
                                    setTimeout(() => document.getElementById('code-form').dispatchEvent(new Event('submit')), 100);
                                }
                            " />
                    </div>
                </div>

                <p id="error-msg" class="text-red-500 text-xs mb-6 hidden font-bold text-center"></p>

                <button 
                    type="submit"
                    id="submit-btn"
                    class="w-full py-4 bg-[#7C45F5] text-white font-black rounded-2xl hover:bg-[#6b39d9] active:scale-[0.98] transition-all box-shadow shadow-[#7C45F5]/20 flex items-center justify-center gap-2"
                >
                    <span>Подтвердить</span>
                </button>
            </form>

            <div class="mt-8 flex flex-col items-center gap-4">
                <a href="{{ route('shop.customers.account.onboarding.add_email') }}" 
                   class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-600 font-bold text-sm transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    <span>Изменить почту</span>
                </a>
                
                <p class="text-xs text-zinc-400">
                    Не пришло письмо? 
                    <button onclick="resendCode()" class="text-[#7C45F5] font-black hover:underline ml-1">Отправить еще раз</button>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('code-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            const errorMsg = document.getElementById('error-msg');
            const otpInput = document.getElementById('otp-input');
            
            if (otpInput.value.length !== 6) return;

            errorMsg.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin rounded-none h-5 w-5 border-2 border-white border-t-transparent"></span>';

            fetch("{{ route('shop.customers.account.onboarding.verify_email.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code: otpInput.value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Неверный код');
                }
            })
            .catch(err => {
                errorMsg.innerText = err.message;
                errorMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<span>Подтвердить</span>';
            });
        });

        function resendCode() {
            // Re-use add_email logic to resend
            fetch("{{ route('shop.customers.account.onboarding.add_email.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: "{{ $email }}" })
            }).then(() => {
                alert('Код отправлен повторно!');
            });
        }
    </script>
    @endpush
</x-shop::layouts>
