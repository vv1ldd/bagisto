<!-- Header -->
<div class="mb-10 pb-6 border-b-4 border-zinc-900 flex items-center justify-between">
    <h3 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900">
        @lang('shop::app.checkout.onepage.summary.cart-summary')
    </h3>
    <div class="px-4 py-2 bg-zinc-900 border-2 border-zinc-900 text-white text-[10px] font-black uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(124,69,245,1)]">
        @{{ cart.items.length }} поз.
    </div>
</div>

<!-- Cart Items -->
<div class="space-y-8 pb-10 border-b-4 border-zinc-900">
    <div class="flex gap-x-6 group animate-in slide-in-from-right duration-300" v-for="item in cart.items" :key="item.id">
        <div class="relative shrink-0">
            <img class="h-24 w-24 border-4 border-zinc-900 object-cover shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] group-hover:shadow-none group-hover:translate-x-1 group-hover:translate-y-1 transition-all duration-300"
                :src="item.base_image.small_image_url" :alt="item.name" />
        </div>

        <div class="flex flex-1 flex-col justify-center min-w-0">
            <p class="text-[14px] font-black uppercase tracking-widest text-zinc-900 leading-tight mb-3 truncate">
                @{{ item.name }}
            </p>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 bg-zinc-100 border-2 border-zinc-900 text-[10px] font-black uppercase tracking-widest text-zinc-900">
                        @{{ item.quantity }} шт.
                    </span>
                    <p class="text-[11px] font-black uppercase tracking-widest opacity-60">
                        @{{ displayTax.prices == 'including_tax' ? item.formatted_price_incl_tax : item.formatted_price }}
                    </p>
                </div>

                <p class="text-[14px] font-black text-zinc-900 tabular-nums">
                    @{{ displayTax.prices == 'including_tax' ? item.formatted_total_incl_tax : item.formatted_total }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Detail -->
<div class="mt-10 space-y-6">
    <!-- Sub Total -->
    <div class="flex justify-between text-[12px] font-black uppercase tracking-widest px-4 py-3 bg-zinc-50 border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.sub-total')</p>
        <p class="text-zinc-900 tabular-nums">
            @{{ displayTax.subtotal == 'including_tax' ? cart.formatted_sub_total_incl_tax : cart.formatted_sub_total }}
        </p>
    </div>

    <!-- Discount -->
    <div class="flex justify-between text-[12px] font-black uppercase tracking-widest px-4 py-3 bg-green-50 border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(72,187,120,1)]" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
        <p class="text-green-700">@lang('shop::app.checkout.onepage.summary.discount-amount')</p>
        <p class="text-green-700 tabular-nums">- @{{ cart.formatted_discount_amount }}</p>
    </div>

    <!-- Coupons Section -->
    <div class="py-6 border-y-2 border-zinc-100 my-8">
        <v-coupon @processed="getCart()"></v-coupon>
    </div>

    <!-- Shipping Charges -->
    <div class="flex justify-between text-[12px] font-black uppercase tracking-widest px-4 py-3 bg-zinc-50 border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>
        <p class="text-zinc-900 tabular-nums">
            @{{ displayTax.shipping == 'including_tax' ? cart.formatted_shipping_amount_incl_tax : cart.formatted_shipping_amount }}
        </p>
    </div>

    <!-- Grand Total -->
    <div class="mt-12 pt-10 border-t-4 border-zinc-900 bg-white">
        <div class="flex flex-col gap-2">
            <p class="text-[11px] font-black uppercase tracking-[0.3em] text-zinc-400">Total payable</p>
            <div class="flex items-center justify-between">
                <p class="text-lg font-black uppercase tracking-widest text-zinc-900">@lang('shop::app.checkout.onepage.summary.grand-total')</p>
                <p class="text-4xl font-black text-[#7C45F5] tabular-nums tracking-tighter">
                    @{{ cart.formatted_grand_total }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Accepted Methods Banner -->
<div class="mt-12 p-8 bg-[#18181b] border-4 border-zinc-900 flex flex-col gap-6 shadow-[8px_8px_0px_0px_rgba(124,69,245,0.4)]">
    <div class="flex items-center gap-3">
        <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white">Payment methods available</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <div v-for="tag in ['Visa', 'MC', 'USDT', 'USDC', 'Wallet']" class="px-2 py-1 bg-white/10 border border-white/20 text-[9px] font-black text-white uppercase tracking-widest">
            @{{ tag }}
        </div>
    </div>
</div>