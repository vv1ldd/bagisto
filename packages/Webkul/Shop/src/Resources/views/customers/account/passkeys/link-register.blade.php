<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        Привязка устройства
    </x-slot>

    <div class="min-h-screen flex flex-col items-center justify-center bg-[#F0EFFF] py-12 px-4">
        <div class="bg-white p-8 md:p-10 flex flex-col items-center text-center relative overflow-hidden w-full max-w-md shadow-xl border border-[#e2d9ff] rounded-[2rem]">
            
            {{-- Icon --}}
            <div class="mb-8 flex flex-col items-center">
                 <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-[#7C45F5] to-[#a78bfa] shadow-xl shadow-[#7C45F5]/30 mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-[#1a0050] text-2xl font-black tracking-tight mb-2">Привязка устройства</h1>
                <p class="text-zinc-500 text-sm">Вы привязываете это устройство к аккаунту <span class="font-bold text-[#1a0050]">{{ $user->username }}</span></p>
            </div>

            <div class="w-full space-y-4">
                <button id="register-device-btn"
                    class="flex w-full items-center justify-center gap-3 !rounded-2xl bg-[#7C45F5] px-8 py-4 text-center text-base font-bold text-white shadow-xl shadow-[#7C45F5]/30 transition-all hover:bg-[#6534d4] active:scale-[0.98] group">
                    <span>ПРИВЯЗАТЬ УСТРОЙСТВО</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
                
                <p class="text-zinc-400 text-xs leading-relaxed">
                    После нажатия следуйте системным инструкциям для создания ключа доступа (FaceID, TouchID или PIN).
                </p>
            </div>
        </div>
        
        <a href="/" class="mt-8 text-zinc-400 hover:text-[#7C45F5] font-bold text-sm transition-colors">Вернуться на главную</a>
    </div>

    @push('scripts')
    <script>
        {{-- Copy of the registration logic from security.blade.php, but slightly adapted for this standalone page --}}
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('register-device-btn');
            if (!btn) return;

            const _u8 = (b64) => {
                const b = b64.replace(/-/g, '+').replace(/_/g, '/');
                const bin = atob(b);
                const arr = new Uint8Array(bin.length);
                for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
                return arr;
            };

            const _b64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');

            btn.addEventListener('click', async () => {
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span>ПОДОЖДИТЕ...</span>';

                try {
                    const res = await fetch('{{ route('passkeys.register-options') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });

                    if (!res.ok) throw new Error('Ошибка сервера');
                    const opt = await res.json();

                    opt.challenge = _u8(opt.challenge);
                    opt.user.id = _u8(opt.user.id);
                    if (opt.excludeCredentials) opt.excludeCredentials.forEach(c => c.id = _u8(c.id));

                    const cred = await navigator.credentials.create({ publicKey: opt });
                    if (!cred) throw new Error('Создание ключа отменено.');

                    const saveRes = await fetch('{{ route('passkeys.register') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            id: cred.id,
                            rawId: _b64(cred.rawId),
                            response: { clientDataJSON: _b64(cred.response.clientDataJSON), attestationObject: _b64(cred.response.attestationObject) },
                            type: cred.type,
                            clientExtensionResults: cred.getClientExtensionResults() || {}
                        })
                    });

                    if (!saveRes.ok) throw new Error('Ошибка сохранения на сервере');
                    
                    btn.innerHTML = '<span>ГОТОВО!</span>';
                    setTimeout(() => { window.location.href = '/'; }, 1500);

                } catch (err) {
                    console.error(err);
                    if (err.name !== 'NotAllowedError') alert('Ошибка: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        });
    </script>
    @endpush
</x-shop::layouts>
