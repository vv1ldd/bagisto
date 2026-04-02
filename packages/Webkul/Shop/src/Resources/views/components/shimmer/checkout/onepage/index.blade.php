<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
    <div class="space-y-6">
        <div class="bg-white p-6 sm:p-8 box-shadow-sm overflow-hidden flex flex-col gap-10">
            <!-- Billing Address Shimmer -->
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 mt-2">
                    <h2 class="shimmer h-6 w-[180px]"></h2>
                </div>
                <div class="shimmer h-[90px] sm:h-[140px] w-full max-w-[450px]"></div>
            </div>

            <!-- Payment Method Shimmer -->
            <div>
                <h2 class="shimmer h-4 w-32 mb-4 mt-2"></h2>
                <div class="shimmer h-[70px] sm:h-[140px] w-full max-w-[450px]"></div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column (RIGHT) -->
    <div class="hidden lg:block sticky top-8 space-y-4">
        <div class="bg-white p-6 sm:p-8 box-shadow-sm">
            <x-shop::shimmer.checkout.onepage.cart-summary />
        </div>
    </div>
</div>
