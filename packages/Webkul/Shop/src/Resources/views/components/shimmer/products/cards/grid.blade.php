@props(['count' => 0])

@for ($i = 0;  $i < $count; $i++)
    <div class="group w-full border border-white/40 bg-white/40 backdrop-blur-3xl shadow-sm relative flex flex-col overflow-hidden isolate {{ $attributes["class"] }}" style="isolation: isolate;">
        <!-- Image Area Shimmer -->
        <div class="relative aspect-square w-full overflow-hidden bg-zinc-100 p-2 shimmer">
            <div class="after:content-[' '] relative after:block after:pb-[calc(100%+9px)]"></div>
        </div>

        <!-- Content Area Shimmer -->
        <div class="flex flex-1 flex-col justify-between p-3 bg-transparent text-center">
            <div class="mb-2 flex flex-col items-center gap-1.5">
                <div class="shimmer h-3.5 w-3/4"></div>
                <div class="shimmer h-3.5 w-1/2"></div>
            </div>

            <div class="mt-auto flex flex-col items-center gap-3">
                <!-- Price Shimmer -->
                <div class="shimmer h-5 w-1/3"></div>

                <!-- Buttons Shimmer (matching the Buy Now / Add to Cart layout) -->
                <div class="flex flex-col gap-2 w-full mt-1 max-sm:hidden">
                    <div class="shimmer h-[38px] w-full"></div>
                    <div class="shimmer h-[38px] w-full"></div>
                </div>
            </div>
        </div>
    </div>
@endfor
