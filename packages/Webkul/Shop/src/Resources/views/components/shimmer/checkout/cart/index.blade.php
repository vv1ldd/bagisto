@props(['count' => 0])

{{-- Cart shimmer: mirrors the actual cart layout exactly --}}
<div class="flex flex-col xl:flex-row gap-8 animate-pulse">

    {{-- LEFT: Items list --}}
    <div class="flex-1 min-w-0">

        {{-- Header row: "N ITEMS" + "Удалить все" --}}
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/5">
            <div class="shimmer h-4 w-20 rounded"></div>
            <div class="shimmer h-4 w-16 rounded"></div>
        </div>

        {{-- Item rows --}}
        @for ($i = 0; $i < max($count, 1); $i++)
            <div class="flex items-start gap-4 py-5 border-b border-white/5 last:border-0">
                {{-- Product image --}}
                <div class="shimmer w-16 h-16 shrink-0 rounded"></div>

                {{-- Title + details --}}
                <div class="flex-1 min-w-0 space-y-2">
                    <div class="shimmer h-4 w-3/4 rounded"></div>
                    <div class="shimmer h-3 w-1/3 rounded"></div>

                    {{-- Qty control --}}
                    <div class="flex items-center gap-2 mt-3">
                        <div class="shimmer h-8 w-8 rounded"></div>
                        <div class="shimmer h-8 w-8 rounded"></div>
                        <div class="shimmer h-8 w-8 rounded"></div>
                    </div>
                </div>

                {{-- Price + delete --}}
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <div class="shimmer h-5 w-24 rounded"></div>
                    <div class="shimmer h-3 w-12 rounded mt-2"></div>
                </div>
            </div>
        @endfor

        {{-- Bottom buttons: Продолжить покупки + Обновить корзину --}}
        <div class="flex gap-4 mt-6">
            <div class="shimmer h-10 w-44 rounded"></div>
            <div class="shimmer h-10 w-36 rounded"></div>
        </div>
    </div>

    {{-- RIGHT: Order summary card --}}
    <div class="w-full xl:w-80 shrink-0">
        <div class="bg-white/5 border border-white/8 rounded-xl p-5 space-y-4">
            {{-- Title --}}
            <div class="shimmer h-4 w-40 rounded"></div>

            {{-- Subtotal row --}}
            <div class="flex justify-between pt-2">
                <div class="shimmer h-4 w-20 rounded"></div>
                <div class="shimmer h-4 w-24 rounded"></div>
            </div>

            {{-- Coupon row --}}
            <div class="flex justify-between">
                <div class="shimmer h-4 w-28 rounded"></div>
                <div class="shimmer h-4 w-20 rounded"></div>
            </div>

            {{-- Divider --}}
            <div class="shimmer h-px w-full rounded"></div>

            {{-- Total row --}}
            <div class="flex justify-between">
                <div class="shimmer h-5 w-24 rounded"></div>
                <div class="shimmer h-5 w-28 rounded"></div>
            </div>

            {{-- CTA button --}}
            <div class="shimmer h-12 w-full rounded-lg mt-2"></div>

            {{-- Footnote --}}
            <div class="space-y-1.5">
                <div class="shimmer h-3 w-full rounded"></div>
                <div class="shimmer h-3 w-3/4 rounded"></div>
            </div>
        </div>
    </div>
</div>