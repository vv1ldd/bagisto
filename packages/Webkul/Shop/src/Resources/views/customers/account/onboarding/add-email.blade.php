<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Привязать почту
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4 text-[#1a0050]">
        
        <div class="w-full max-w-[440px] bg-white rounded-[32px] p-8 md:p-10 shadow-2xl shadow-purple-500/10 border border-[#e2d9ff]">
            
            <div class="mb-8 flex flex-col items-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#F0EFFF] mb-4">
                    <svg class="w-8 h-8 text-[#7C45F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black tracking-tight mb-2">Привязать почту</h1>
                <p class="text-zinc-500 text-sm text-center">Она понадобится для восстановления доступа и важных уведомлений</p>
            </div>

            <form @submit.prevent="submitEmail" id="email-form">
                <div class="mb-6">
                    <label for="email" class="block text-xs font-black uppercase tracking-wider text-zinc-400 mb-2 ml-1">Email адрес</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        required
                        placeholder="example@mail.com"
                        class="w-full px-5 py-4 bg-zinc-50 border border-zinc-100 rounded-2xl focus:ring-2 focus:ring-[#7C45F5]/20 focus:border-[#7C45F5] outline-none transition-all text-base placeholder:text-zinc-300"
                    >
                    <p id="error-msg" class="text-red-500 text-xs mt-2 hidden font-bold"></p>
                </div>

                <button 
                    type="submit"
                    id="submit-btn"
                    class="w-full py-4 bg-[#7C45F5] text-white font-black rounded-2xl hover:bg-[#6b39d9] active:scale-[0.98] transition-all shadow-lg shadow-[#7C45F5]/20 flex items-center justify-center gap-2"
                >
                    <span>Отправить код</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('shop.customers.account.onboarding.security') }}" 
                   class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-600 font-bold text-sm transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    <span>Назад</span>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function submitEmail(e) {
            const btn = document.getElementById('submit-btn');
            const errorMsg = document.getElementById('error-msg');
            const emailInput = document.getElementById('email');
            
            errorMsg.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>';

            fetch("{{ route('shop.customers.account.onboarding.add_email.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: emailInput.value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Произошла ошибка');
                }
            })
            .catch(err => {
                errorMsg.innerText = err.message;
                errorMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<span>Отправить код</span><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            });
        }
    </script>
    @endpush
</x-shop::layouts>
