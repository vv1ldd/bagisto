<x-shop::layouts.account :is-cardless="true" :title="__('Активация Web3-кошелька')">
    <div class="max-w-[500px] mx-auto mt-10 px-4">

        {{-- Back Button --}}
        <a href="{{ route('shop.customers.account.credits.index') }}"
            class="inline-flex items-center gap-2 text-[13px] font-black text-[#7C45F5] mb-8 hover:-translate-x-1 transition-transform">
            <span class="icon-arrow-left text-lg"></span> Назад
        </a>

        <div class="bg-white rounded-3xl shadow-sm border border-[#e2d9ff] overflow-hidden p-8 md:p-12 relative group hover:border-[#7C45F5] transition-all duration-500">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#7C45F5] opacity-[0.03] rounded-full blur-3xl group-hover:opacity-[0.06] transition-opacity pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-[#f8f6ff] text-[#7C45F5] rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-sm">
                    🔐
                </div>

                <h1 class="text-[28px] font-black text-[#1a0050] uppercase tracking-tighter italic leading-none mb-3">
                    Активация Web3-кошелька
                </h1>
                <p class="text-[13px] text-zinc-500 font-medium leading-relaxed mb-8 max-w-sm">
                    Чтобы мы могли управлять вашими NFT (подарками) и безопасно их переводить, введите вашу **сохраненную секретную фразу**. <br><br>Мы зашифруем ключ и сохраним его в безопасности.
                </p>

                <form method="POST" action="{{ route('shop.customers.account.crypto.upgrade_wallet') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-[#1a0050] uppercase tracking-widest mb-3">
                            Ваша Секретная Фраза (12, 15, 18, 21 или 24 слова)
                        </label>
                        <div class="relative">
                            <textarea name="phrase" rows="4" required
                                class="w-full bg-[#fcfbff] border-2 border-[#e2d9ff] rounded-2xl p-5 text-[15px] font-mono text-[#1a0050] placeholder-zinc-300 focus:border-[#7C45F5] focus:ring-0 transition-colors resize-none"
                                placeholder="Например: abandon amount base baby boil ..."></textarea>
                        </div>
                        @error('phrase')
                            <p class="text-red-500 text-[11px] font-bold mt-2 uppercase tracking-wide">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-zinc-100">
                        <button type="submit" class="w-full bg-[#1a0050] hover:bg-[#2a0080] text-white py-5 rounded-xl shadow-lg shadow-[#1a0050]/20 flex items-center justify-center gap-3 transition-all active:scale-[0.98] group/btn">
                            <span class="icon-security text-2xl group-hover/btn:scale-110 transition-transform"></span>
                            <span class="text-[14px] font-black uppercase tracking-[0.2em]">Подтвердить и зашифровать</span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-8 border-t border-zinc-100">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center text-sm shadow-sm">
                                💡
                            </div>
                            <p class="text-[12px] text-zinc-500 font-bold uppercase tracking-tight">Потеряли секретную фразу?</p>
                        </div>
                        
                        <p class="text-[13px] text-zinc-500 leading-relaxed">
                            Если вы не сохранили старую фразу, но у вас есть **привязанный Passkey**, вы можете перевыпустить ключи. Старый адрес ({{ $customer->credits_id }}) будет заменен на новый.
                        </p>

                        <a href="{{ route('shop.customers.account.profile.generate_recovery_key') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#f8f6ff] text-[#7C45F5] border-2 border-[#eee6ff] rounded-xl text-[13px] font-black uppercase tracking-widest hover:bg-[#7C45F5] hover:text-white hover:border-[#7C45F5] transition-all group/reissue">
                           <span class="icon-add-new text-lg group-hover/reissue:rotate-90 transition-transform"></span>
                           Перевыпустить фразу и адрес
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="text-center mt-6">
            <p class="text-[11px] text-zinc-400 font-medium italic">Безопасность вашего кошелька — наш приоритет.</p>
        </div>

    </div>
</x-shop::layouts.account>
