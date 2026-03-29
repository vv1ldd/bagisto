<x-shop::layouts.account :is-cardless="true" :title="'Видеовстреча'" :back-link="route('shop.customers.account.index')">
    <div class="mt-0 mb-6 w-full max-w-[800px] mx-auto px-1 leading-none" data-echo-bootstrap>
        <v-call-overlay></v-call-overlay>
        <v-meeting-inviter 
            action="{{ route('shop.call.store') }}" 
            csrf-token="{{ csrf_token() }}"
            caller-name="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}"
            caller-email="{{ auth()->guard('customer')->user()->email }}"
        >
            <!-- Fallback for no-JS or pre-mount (Day/Night Theme Support) -->
            <div class="p-6 md:p-10 bg-white dark:bg-white/5 rounded-[2rem] border border-[#e2d9ff] dark:border-white/10 backdrop-blur-2xl shadow-sm relative overflow-hidden group">
                <div class="relative z-10 text-center">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-16 h-16 bg-[#7C45F5]/5 dark:bg-[#7C45F5]/10 text-[#7C45F5] rounded-3xl flex items-center justify-center mb-4 transition-transform group-hover:scale-105">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 1 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-[20px] md:text-[24px] font-black text-[#1a0050] dark:text-white uppercase tracking-tighter italic">Создать встречу</h3>
                        <p class="text-[13px] text-zinc-500 font-medium mt-1">Пригласите собеседника в защищенную видеокомнату</p>
                    </div>

                    <form action="{{ route('shop.call.store') }}" method="POST" class="flex flex-col gap-4 max-w-[400px] mx-auto w-full">
                        @csrf
                        <input type="hidden" name="caller_name" value="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}">
                        <input type="hidden" name="caller_email" value="{{ auth()->guard('customer')->user()->email }}">
                        
                        <div class="w-full">
                            <input type="text" name="recipient_emails[]" placeholder="email@example.com или @alias" required
                                class="w-full bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/10 rounded-2xl px-6 py-4 text-[15px] text-[#1a0050] dark:text-white focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-400 font-medium">
                        </div>

                        <button type="submit" 
                            class="w-full bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-[13px] transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-[0.98] whitespace-nowrap">
                            Начать встречу
                        </button>
                    </form>
                </div>
            </div>
        </v-meeting-inviter>

        <div class="mt-4 p-4 bg-zinc-50/50 dark:bg-white/5 border border-zinc-100 dark:border-white/10 text-[12px] text-zinc-500 leading-tight rounded-[2rem] text-center max-w-[600px] mx-auto">
            <p><strong class="text-zinc-900 dark:text-zinc-300 font-bold">Безопасность:</strong> Все видеовстречи осуществляются напрямую между устройствами и не записываются на сервере. Соединение защищено сквозным E2E шифрованием по умолчанию.</p>
        </div>
    </div>
</x-shop::layouts.account>