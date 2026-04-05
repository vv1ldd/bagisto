<x-shop::layouts.account :is-cardless="true" :title="'Видеовстреча'" :back-link="route('shop.customers.account.index')">
    <div class="mt-0 mb-6 w-full max-w-[800px] mx-auto px-1 leading-none" data-echo-bootstrap>
        <v-call-overlay
            prop-uuid="{{ $roomUuid }}"
            prop-user-name="{{ auth()->guard('customer')->user()->username ?? auth()->guard('customer')->user()->first_name }}"
            prop-user-hash="{{ auth()->guard('customer')->user()->credits_id }}"
            :prop-order-data='@json($orderData)'
            :prop-is-verified="{{ $isMnemonicVerified ? 'true' : 'false' }}"
        ></v-call-overlay>
        
        <v-meeting-inviter 
            action="{{ route('shop.call.store') }}" 
            csrf-token="{{ csrf_token() }}"
            caller-name="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}"
            caller-email="{{ auth()->guard('customer')->user()->email }}"
        >
            <div class="relative group">
                <div class="absolute inset-0 bg-black translate-x-3 translate-y-3"></div>
                
                <div class="relative p-6 md:p-10 bg-white border-4 border-black overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-[#D6FF00] border-b-4 border-l-4 border-black translate-x-4 -translate-y-4 rotate-45"></div>

                    <div class="relative z-10 text-center">
                        <div class="flex flex-col items-center mb-8">
                            <div class="w-16 h-16 bg-[#D6FF00] border-4 border-black flex items-center justify-center mb-4 transition-transform group-hover:-rotate-3 group-hover:scale-105">
                                <svg class="w-8 h-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 0 002-2V8a2 0 00-2-2H5a2 0 00-2 2v8a2 0 002 2z"/>
                                </svg>
                            </div>
                            
                            <h3 class="text-[26px] md:text-[32px] font-black text-black uppercase tracking-tighter italic leading-none mb-2">Создать встречу</h3>
                            <p class="text-[14px] text-black font-bold opacity-70">Пригласите собеседника в защищенную видеокомнату</p>
                        </div>

                        <form action="{{ route('shop.call.store') }}" method="POST" class="flex flex-col gap-5 max-w-[420px] mx-auto w-full">
                            @csrf
                            <input type="hidden" name="caller_name" value="{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}">
                            <input type="hidden" name="caller_email" value="{{ auth()->guard('customer')->user()->email }}">
                            
                            <div class="w-full relative">
                                <label class="absolute -top-3 left-4 bg-white border-2 border-black px-2 py-0.5 text-[10px] font-black uppercase tracking-widest z-20">Получатель</label>
                                <input type="text" name="recipient_emails[]" placeholder="email@example.com или @alias" required
                                    class="w-full bg-white border-4 border-black px-6 py-4 text-[16px] text-black focus:outline-none focus:bg-[#D6FF00]/10 transition-all placeholder:text-zinc-400 font-black">
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 bg-black translate-x-1.5 translate-y-1.5"></div>
                                <button type="submit" 
                                    class="relative w-full bg-[#7C45F5] border-4 border-black text-white px-10 py-5 font-black uppercase tracking-widest text-[14px] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none whitespace-nowrap">
                                    Начать встречу
                                </button>
                            </div>
                        </form>

                        <div class="mt-8">
                            <span class="bg-black text-[#D6FF00] px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em]">P2P ЭНД-ТУ-ЭНД ШИФРОВАНИЕ</span>
                        </div>
                    </div>
                </div>
            </div>
        </v-meeting-inviter>

        <div class="mt-8 p-6 bg-white border-2 border-black border-dashed text-[13px] text-black font-medium text-center max-w-[640px] mx-auto">
            <p><strong class="font-black uppercase italic mr-1">Конфиденциальность:</strong> Видеовстречи осуществляются по технологии P2P. Мы не храним, не записываем и не анализируем ваш трафик. Соединение полностью анонимно.</p>
        </div>
    </div>
</x-shop::layouts.account>