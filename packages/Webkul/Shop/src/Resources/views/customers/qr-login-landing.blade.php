<x-shop::layouts.auth>
    <x-slot:title>
        Подтверждение входа
    </x-slot>

    <div class="text-center">
        @if (isset($error))
            <div class="mb-8">
                <div class="w-20 h-20 bg-red-100 text-red-600 rounded-none flex items-center justify-center mx-auto mb-4 border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black uppercase tracking-tighter text-zinc-900 mb-2">Ошибка</h1>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest leading-relaxed px-6">
                    {{ $error }}
                </p>
                <div class="mt-8">
                    <a href="{{ route('shop.home.index') }}" class="inline-block bg-zinc-900 text-white px-8 py-3 font-black uppercase tracking-widest text-xs rounded-xl shadow-[4px_4px_0px_0px_rgba(124,69,245,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                        На главную
                    </a>
                </div>
            </div>
        @else
            <div class="mb-8 mt-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="w-24 h-24 bg-[#7C45F5] text-white rounded-none flex items-center justify-center mx-auto mb-6 border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]">
                    <span class="text-3xl font-black uppercase">{{ substr($customer->name, 0, 1) }}</span>
                </div>
                
                <h1 class="text-2xl font-black uppercase tracking-tighter text-zinc-900 mb-2 leading-none">Подтвердите вход</h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed px-4">
                    Вы пытаетесь войти в Meanly <br> на другом устройстве (ПК). Это вы?
                </p>
            </div>

            <v-qr-authorize 
                authorize-url="{{ route('shop.customer.login.qr.authorize', ['token' => $token]) }}"
                home-url="{{ route('shop.home.index') }}"
            ></v-qr-authorize>
        @endif
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-qr-authorize-template">
            <div class="space-y-4 px-2 animate-in fade-in delay-300 duration-1000 fill-mode-both">
                <button @click="authorize" :disabled="isLoading"
                    class="group relative flex w-full items-center justify-center gap-4 bg-[#D6FF00] text-black h-18 py-5 font-black uppercase tracking-[0.2em] text-sm transition-all border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-2xl overflow-hidden">
                    <span v-if="isLoading">Обработка...</span>
                    <span v-else>ДА, ЭТО Я</span>
                    
                    <div v-if="isSuccess" class="absolute inset-0 bg-emerald-500 flex items-center justify-center text-white animate-in slide-in-from-bottom full duration-500">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </button>

                <button @click="cancel" :disabled="isLoading"
                    class="w-full h-14 bg-white text-zinc-400 font-black uppercase tracking-widest text-[10px] hover:text-red-500 transition-colors">
                    НЕТ, ОТМЕНИТЬ
                </button>
            </div>
        </script>

        <script>
            window.meanlyComponents.push({
                name: 'v-qr-authorize',
                definition: {
                    template: '#v-qr-authorize-template',
                    props: ['authorizeUrl', 'homeUrl'],
                    data() {
                        return {
                            isLoading: false,
                            isSuccess: false
                        }
                    },
                    methods: {
                        async authorize() {
                            this.isLoading = true;
                            try {
                                const res = await fetch(this.authorizeUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });

                                if (res.ok) {
                                    this.isSuccess = true;
                                    setTimeout(() => {
                                        window.location.href = this.homeUrl;
                                    }, 1500);
                                } else {
                                    const data = await res.json();
                                    throw new Error(data.error || 'Ошибка авторизации');
                                }
                            } catch (err) {
                                alert(err.message);
                                this.isLoading = false;
                            }
                        },
                        cancel() {
                            window.location.href = this.homeUrl;
                        }
                    }
                }
            });
        </script>
    @endPushOnce
</x-shop::layouts.auth>
