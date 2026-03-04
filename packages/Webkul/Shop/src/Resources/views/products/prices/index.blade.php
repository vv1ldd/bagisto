@if ($prices['final']['price'] < $prices['regular']['price'])
    {{-- Strikethrough original price --}}
    <p class="text-[0.7em] font-medium text-zinc-400 line-through leading-none mb-0.5 opacity-80">
        {{ $prices['regular']['formatted_price'] }}
    </p>
    {{-- Sale price in Meanly purple — inherits font-size from parent --}}
    <p class="final-price font-black text-[#7C45F5] leading-none tracking-tight">
        {{ $prices['final']['formatted_price'] }}
    </p>
@else
    <p class="final-price font-black text-zinc-900 leading-none tracking-tight">
        {{ $prices['regular']['formatted_price'] }}
    </p>
@endif