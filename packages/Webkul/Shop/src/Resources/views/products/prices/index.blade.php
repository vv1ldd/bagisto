@if ($prices['final']['price'] < $prices['regular']['price'])
    {{-- Strikethrough original price --}}
    <p class="text-sm font-medium text-zinc-400 line-through leading-none mb-0.5">
        {{ $prices['regular']['formatted_price'] }}
    </p>
    {{-- Sale price in Meanly purple --}}
    <p class="final-price text-3xl font-black text-[#7C45F5] leading-none tracking-tight">
        {{ $prices['final']['formatted_price'] }}
    </p>
@else
    <p class="final-price text-3xl font-black text-zinc-900 leading-none tracking-tight">
        {{ $prices['regular']['formatted_price'] }}
    </p>
@endif