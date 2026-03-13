<x-shop::layouts>
    <x-slot:title>
        Начать видеозвонок — Meanly 
    </x-slot:title>

    <div class="flex-grow flex items-center justify-center px-4 py-12 md:py-20">
        <div class="w-full max-w-[460px] bg-white border border-zinc-100 p-8 md:p-12 shadow-[0_24px_80px_rgba(124,69,245,0.08)] relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-[#7C45F5]/5 blur-3xl rounded-full"></div>
            
            <div class="relative z-10">
                <div class="mb-10 text-center">
                    <div class="w-20 h-20 bg-[#7C45F5] flex items-center justify-center mx-auto mb-6 shadow-xl shadow-[#7C45F5]/20 rotate-3">
                        <span class="text-4xl">📞</span>
                    </div>
                    <h1 class="text-3xl font-black uppercase tracking-tighter text-zinc-900 mb-2 italic">Guest Call</h1>
                    <div class="h-1 w-12 bg-[#7C45F5] mx-auto"></div>
                </div>

                <form action="{{ route('shop.call.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2 block">Ваше Имя</label>
                        <input type="text" name="caller_name" required value="{{ old('caller_name') }}"
                            placeholder="Как к вам обращаться"
                            class="w-full h-14 bg-zinc-50 border border-zinc-100 px-6 text-sm font-bold text-zinc-900 focus:border-[#7C45F5] outline-none transition-all placeholder:text-zinc-300">
                        @error('caller_name') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2 block">Ваш Email</label>
                        <input type="email" name="caller_email" required value="{{ old('caller_email') }}"
                            placeholder="Для получения ссылки на звонок"
                            class="w-full h-14 bg-zinc-50 border border-zinc-100 px-6 text-sm font-bold text-zinc-900 focus:border-[#7C45F5] outline-none transition-all placeholder:text-zinc-300">
                        @error('caller_email') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2 block">Email Собеседника</label>
                        <input type="email" name="recipient_email" required value="{{ old('recipient_email') }}"
                            placeholder="Кому отправить приглашение"
                            class="w-full h-14 bg-zinc-50 border border-zinc-100 px-6 text-sm font-bold text-zinc-900 focus:border-[#7C45F5] outline-none transition-all placeholder:text-zinc-300">
                        @error('recipient_email') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full h-16 bg-[#7C45F5] text-white font-black uppercase tracking-widest text-sm shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6b35e4] transition-all active:scale-[0.98]">
                            Пригласить в звонок
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-[11px] text-zinc-400 leading-relaxed max-w-[280px] mx-auto">
                    Вы и ваш собеседник получите защищенную ссылку на электронную почту. Звонок осуществляется P2P.
                </p>
            </div>
        </div>
    </div>
</x-shop::layouts>
