<footer class="mt-auto py-8 bg-white border-t-2 border-zinc-100">
    <div class="container px-4 md:px-[60px]">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            {{-- Left Side: Copyright and Legal --}}
            <div class="flex flex-col gap-1 max-md:text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-900 leading-tight">
                    @php
                        $copyright = core()->getConfigData('general.design.copyright.copyright_text') 
                             ?: '© :year Meanly Pay. Все права защищены.';
                        
                        $copyright = str_replace(
                            [':year', ':company'],
                            [date('Y'), 'Meanly Pay'],
                            $copyright
                        );
                    @endphp
                    {{ $copyright }}
                </p>
                <p class="text-[8px] font-bold uppercase tracking-widest text-zinc-400 leading-tight">
                    Все товарные знаки являются собственностью их соответствующих владельцев
                </p>
            </div>

            {{-- Right Side: Branding --}}
            <div class="flex items-center gap-6">
                <span class="text-[10px] font-black uppercase tracking-widest text-[#7C45F5]">Built on Handshake</span>
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Powered by Bagisto</span>
            </div>
        </div>
    </div>
</footer>
