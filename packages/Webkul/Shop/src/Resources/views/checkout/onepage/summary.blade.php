<!-- Compact Header -->
<div class="mb-6 pb-4 border-b-[3px] border-zinc-900 flex items-center justify-between">
    <h3 class="text-lg font-black uppercase tracking-[0.15em] text-zinc-900">
        ЛИСТ ПОКУПОК
    </h3>
    <div class="px-3 py-1 bg-zinc-900 border-2 border-zinc-900 text-white text-[9px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(124,69,245,1)]">
        @{{ cart.items.length }} поз.
    </div>
</div>

<!-- Compact Cart Items -->
<div class="space-y-4 pb-6 border-b-[3px] border-zinc-900">
    <div class="flex gap-x-4 group animate-in slide-in-from-right duration-300" v-for="item in cart.items" :key="item.id">
        <div class="relative shrink-0">
            <img class="h-16 w-16 border-[3px] border-zinc-900 object-cover shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] group-hover:shadow-none group-hover:translate-x-0.5 group-hover:translate-y-0.5 transition-all duration-300"
                :src="item.base_image.small_image_url" :alt="item.name" />
        </div>

        <div class="flex flex-1 flex-col justify-center min-w-0">
            <p class="text-[12px] font-black uppercase tracking-widest text-zinc-900 leading-tight mb-2 truncate">
                @{{ item.name }}
            </p>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <button type="button" @click="setItemQuantity(item.id, item.quantity - 1)" class="w-5 h-5 flex items-center justify-center bg-zinc-100 border border-zinc-900 hover:bg-zinc-900 hover:text-white transition-colors" :disabled="isPlacingOrder || item.quantity <= 1">
                            -
                        </button>
                        <span class="min-w-[20px] text-center px-1.5 py-0.5 bg-zinc-100 border border-zinc-900 border-x-0 text-[8px] font-black uppercase tracking-widest text-zinc-900">
                            @{{ item.quantity }}
                        </span>
                        <button type="button" @click="setItemQuantity(item.id, item.quantity + 1)" class="w-5 h-5 flex items-center justify-center bg-zinc-100 border border-zinc-900 hover:bg-zinc-900 hover:text-white transition-colors" :disabled="isPlacingOrder">
                            +
                        </button>
                        
                        <button type="button" @click="removeItem(item.id)" title="Удалить" class="ml-2 w-5 h-5 flex items-center justify-center bg-red-50 border border-red-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors" :disabled="isPlacingOrder">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">
                        @{{ displayTax.prices == 'including_tax' ? item.formatted_price_incl_tax : item.formatted_price }}
                    </p>
                </div>

                <p class="text-[12px] font-black text-zinc-900 tabular-nums">
                    @{{ displayTax.prices == 'including_tax' ? item.formatted_total_incl_tax : item.formatted_total }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Compact Pricing Detail -->
<div class="mt-6 space-y-4">
    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest px-3 py-2 bg-zinc-50 border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.sub-total')</p>
        <p class="text-zinc-900 tabular-nums">
            @{{ displayTax.subtotal == 'including_tax' ? cart.formatted_sub_total_incl_tax : cart.formatted_sub_total }}
        </p>
    </div>

    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest px-3 py-2 bg-green-50 border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(72,187,120,1)]" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
        <p class="text-green-700">Скидка</p>
        <p class="text-green-700 tabular-nums">- @{{ cart.formatted_discount_amount }}</p>
    </div>

    <!-- Compact Delivery Charges -->
    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest px-3 py-2 border-l-4 border-zinc-900 bg-white">
        <p class="text-zinc-400">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>
        <p class="text-zinc-900 tabular-nums">
            @{{ displayTax.shipping == 'including_tax' ? cart.formatted_shipping_amount_incl_tax : cart.formatted_shipping_amount }}
        </p>
    </div>

    <!-- Compact Grand Total -->
    <div class="mt-8 pt-6 border-t-[3px] border-zinc-900 bg-white">
        <div class="flex flex-col gap-1">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Total payable</p>
            <div class="flex items-center justify-between">
                <p class="text-base font-black uppercase tracking-widest text-zinc-900">@lang('shop::app.checkout.onepage.summary.grand-total')</p>
                <p class="text-3xl font-black text-[#7C45F5] tabular-nums tracking-tighter">
                    @{{ cart.formatted_grand_total }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Compact Accepted Methods -->
<div class="mt-8 p-6 bg-[#18181b] border-[3px] border-zinc-900 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(124,69,245,0.4)]">
    <div class="flex items-center gap-2">
        <div class="w-2 h-2 rounded-none bg-green-500 animate-pulse"></div>
        <p class="text-[9px] font-black uppercase tracking-[0.15em] text-white">Secure payment</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <div v-for="tag in ['Visa', 'MC', 'USDT', 'USDC', 'Wallet']" class="px-1.5 py-0.5 bg-white/10 border border-white/20 text-[8px] font-black text-white uppercase tracking-widest">
            @{{ tag }}
        </div>
    </div>
</div>