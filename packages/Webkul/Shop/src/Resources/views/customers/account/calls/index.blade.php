<x-shop::layouts.account :show-back="true">
    <!-- Page Title -->
    <x-slot:title>
        Видеовстреча
    </x-slot:title>

    <div class="mx-4 mt-2 mb-10">
        <v-meeting-inviter 
            action="{{ route('shop.call.store') }}" 
            csrf-token="{{ csrf_token() }}"
            caller-name="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}"
            caller-email="{{ auth()->guard('customer')->user()->email }}"
        >
            <!-- Fallback for no-JS or pre-mount -->
            <div class="p-8 bg-zinc-900 rounded-[2rem] text-white shadow-2xl relative overflow-hidden border border-white/5">
                <div class="absolute inset-0 bg-gradient-to-tr from-[#7C45F5]/20 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-[#7C45F5] rounded-full flex items-center justify-center shadow-lg shadow-[#7C45F5]/30">
                            <span class="text-xs">✉️</span>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-tighter italic">Создать встречу</h3>
                    </div>

                    <form action="{{ route('shop.call.store') }}" method="POST" class="flex flex-col md:flex-row gap-3">
                        @csrf
                        <input type="hidden" name="caller_name" value="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}">
                        <input type="hidden" name="caller_email" value="{{ auth()->guard('customer')->user()->email }}">
                        
                        <div class="flex-grow">
                            <input type="text" name="recipient_emails[]" placeholder="email@example.com или @alias" required
                                class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-600">
                        </div>

                        <button type="submit" 
                            class="bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-95 whitespace-nowrap">
                            Создать встречу
                        </button>
                    </form>
                </div>
            </div>
        </v-meeting-inviter>

        <div class="mt-8 p-6 bg-white/50 border border-zinc-100 text-[13px] text-zinc-500 leading-relaxed rounded-[2rem] shadow-sm">
            <p><strong class="text-zinc-900">Безопасность:</strong> Все видеовстречи осуществляются напрямую между устройствами и не записываются на сервере. Соединение защищено сквозным E2E шифрованием по умолчанию.</p>
        </div>
    </div>
</x-shop::layouts.account>