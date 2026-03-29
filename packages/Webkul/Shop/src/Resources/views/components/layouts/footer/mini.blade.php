<footer class="py-8 bg-white border-t-2 border-zinc-100 mt-auto">
    <div class="container px-4 text-center">
        @php
            $copyright = core()->getConfigData('general.design.copyright.copyright_text') 
                 ?: '© :year Meanly Pay. Все права защищены.';
            
            $copyright = str_replace(
                [':year', ':company'],
                [date('Y'), 'Meanly Pay'],
                $copyright
            );
        @endphp

        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
            {{ $copyright }}
        </p>

        <div class="mt-2 flex items-center justify-center gap-4 opacity-50">
            <span class="text-[8px] font-bold uppercase tracking-widest text-zinc-300">Meanly &bull; Pay Secure</span>
        </div>
    </div>
</footer>
