@props(['count' => 0])

@for ($i = 0; $i < $count; $i++)
    <div class="relative flex grid-cols-2 gap-4 overflow-hidden max-sm:flex-wrap isolate {{ $attributes["class"] }}" style="isolation: isolate;">
        <!-- Image Area Shimmer -->
        <div class="shimmer relative min-h-[258px] min-w-[250px] overflow-hidden bg-zinc-100"> 
            <div class="after:content-[' '] relative after:block after:pb-[calc(100%+9px)]"></div>
        </div>

        <!-- Content Area Shimmer -->
        <div class="grid content-start gap-4 flex-1">
            <!-- Title Shimmer -->
            <div class="flex flex-col gap-2">
                <div class="shimmer h-6 w-3/4"></div>
                <div class="shimmer h-6 w-1/2"></div>
            </div>

            <!-- Price Shimmer -->
            <div class="shimmer h-8 w-1/3"></div>

            <!-- Rating Shimmer -->
            <div class="shimmer h-4 w-1/4"></div>

            <!-- Buttons Shimmer -->
            <div class="flex gap-4 mt-2">
                <div class="shimmer h-11 w-[160px]"></div>
                <div class="shimmer h-11 w-[160px]"></div>
            </div>
        </div>
    </div>
@endfor
