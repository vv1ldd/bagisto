<x-shop::layouts.split-screen title="Восстановление доступа">
    <div class="flex flex-col items-center flex-1 py-10">
        <div class="mb-10 flex flex-col items-center">
            <h1 class="text-zinc-900 text-4xl font-black uppercase tracking-tighter mb-4 text-center leading-[0.9]">Восстановление</h1>
            <div class="h-2 w-16 bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D]"></div>
        </div>

        <div class="w-full max-w-[480px]">
            <x-shop::form :action="route('shop.customers.recovery.seed.post')" v-slot="{ meta }">
                <!-- Seed Phrase Grid -->
                <p class="!text-[10px] !font-bold uppercase tracking-widest text-zinc-400 mb-4">Секретная фраза (12, 15, 18, 21 или 24 слова)</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-x-3 gap-y-3 mb-10">
                    @for($i = 0; $i < 24; $i++)
                        <div class="flex items-center gap-2 bg-white border border-zinc-200 focus-within:border-[#7C45F5] transition-colors p-3 group">
                            <span class="text-[9px] font-black text-zinc-300 group-focus-within:text-[#7C45F5]/40 w-3">{{ $i + 1 }}</span>
                            <input 
                                type="text" 
                                name="words[]" 
                                class="w-full h-full bg-transparent border-none p-0 text-[14px] font-mono font-bold text-zinc-700 focus:ring-0 placeholder:text-zinc-200 placeholder:font-normal"
                                placeholder="..."
                                autocomplete="off"
                            >
                        </div>
                    @endfor
                </div>


                <button
                    class="w-full !rounded-none bg-[#7C45F5] px-8 py-5 text-center font-bold text-white transition-all hover:bg-[#6534d4] focus:ring-2 focus:ring-[#7C45F5] focus:ring-offset-2 shadow-lg shadow-[#7C45F5]/20 uppercase tracking-widest disabled:opacity-50"
                    type="submit" :disabled="!meta.valid">
                    Восстановить доступ
                </button>
            </x-shop::form>

            <div class="mt-8 text-center flex flex-col gap-4">
                <a href="{{ route('shop.customer.session.index') }}"
                    class="text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-[#7C45F5] transition-colors">
                    Назад ко входу
                </a>
            </div>
        </div>
    </div>
</x-shop::layouts.split-screen>
