<x-shop::layouts.account :is-cardless="true" :title="__('Сообщения')">
    <div class="relative w-full h-[600px] bg-white border-4 border-black box-shadow-sm overflow-hidden flex flex-col">
        {{-- Syncing Overlay --}}
        <div id="syncOverlay" class="absolute inset-0 bg-white/90 z-20 flex flex-col items-center justify-center gap-4 transition-opacity duration-500">
            <div class="w-16 h-16 border-8 border-zinc-200 border-t-[#00D1FF] rounded-full animate-spin"></div>
            <p class="text-xl font-black uppercase tracking-tighter">Синхронизация чатов...</p>
            <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider">Настраиваем децентрализованную сеть</p>
        </div>

        {{-- Hydrogen Iframe --}}
        <iframe 
            id="hydrogenIframe"
            src="/chat/#/session/{{ $customer->matrix_access_token ? 'active' : 'login' }}" 
            class="hidden w-full h-full border-none"
            allow="microphone; camera; display-capture; autoplay; encrypted-media">
        </iframe>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const overlay = document.getElementById('syncOverlay');
                const iframe = document.getElementById('hydrogenIframe');

                // 1. Trigger Sync on Backend
                fetch('{{ route('shop.customers.account.matrix.sync') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('Matrix Sync Complete:', data.matrix_id);
                        
                        // 2. Hide overlay and show iframe
                        overlay.style.opacity = '0';
                        setTimeout(() => {
                            overlay.classList.add('hidden');
                            iframe.classList.remove('hidden');
                        }, 500);

                        // Note: In a full implementation, we would inject the access_token 
                        // into the iframe's localStorage or via a postMessage.
                        // For this PoC, we assume the user is already logged in or 
                        // redirection to /chat/ handle the session.
                    } else {
                        overlay.innerHTML = `
                            <div class="text-red-500 text-4xl mb-4">⚠️</div>
                            <p class="text-xl font-black uppercase">Ошибка синхронизации</p>
                            <button onclick="window.location.reload()" class="mt-4 px-6 py-2 bg-black text-white font-black uppercase tracking-tighter shadow-[4px_4px_0px_0px_rgba(0,209,255,1)]">Повторить</button>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Matrix Sync Error:', error);
                    overlay.innerHTML = '<p class="text-red-500 font-black">СЕТЕВАЯ ОШИБКА</p>';
                });
            });
        </script>
    @endpush
</x-shop::layouts.account>
