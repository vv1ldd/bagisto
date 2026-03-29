<!-- Header -->
<h1 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900 max-md:hidden border-b-4 border-zinc-900 pb-4">
    @lang('shop::app.checkout.onepage.summary.cart-summary')
</h1>

<!-- Cart Items -->
<div class="mt-8 grid gap-8 border-b-4 border-zinc-900 pb-10">
    <div class="flex gap-x-6" v-for="item in cart.items">
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_image.before') !!}

        <div class="relative shrink-0">
            <img class="h-20 w-20 border-4 border-zinc-900 object-cover shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]"
                :src="item.base_image.small_image_url" :alt="item.name" width="80" height="80" />
        </div>

        {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_image.after') !!}

        <div class="flex flex-1 flex-col justify-center">
            {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_name.before') !!}

            <p class="text-[13px] font-black uppercase tracking-widest text-zinc-900 leading-tight mb-2">
                @{{ item.name }}
            </p>

            {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_name.after') !!}

            <div class="flex items-center justify-between">
                <p class="text-[10px] font-black uppercase opacity-60 tracking-widest">
                    <span class="text-[#7C45F5] opacity-100">@{{ item.quantity }} x</span>
                    <template v-if="displayTax.prices == 'including_tax'">
                        @{{ item.formatted_price_incl_tax }}
                    </template>

                    <template v-else>
                        @{{ item.formatted_price }}
                    </template>
                </p>

                <p class="text-sm font-black text-zinc-900 tabular-nums">
                    <template v-if="displayTax.prices == 'including_tax'">
                        @{{ item.formatted_total_incl_tax }}
                    </template>

                    <template v-else>
                        @{{ item.formatted_total }}
                    </template>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Cart Totals -->
<div class="mt-10 space-y-5">
    <!-- Sub Total -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.sub_total.before') !!}

    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.sub-total')</p>

        <p class="text-zinc-900 tabular-nums">
            <template v-if="displayTax.subtotal == 'including_tax'">
                @{{ cart.formatted_sub_total_incl_tax }}
            </template>
            <template v-else>
                @{{ cart.formatted_sub_total }}
            </template>
        </p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.sub_total.after') !!}

    <!-- Discount -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.discount_amount.before') !!}

    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.discount-amount')</p>
        <p class="text-green-600">- @{{ cart.formatted_discount_amount }}</p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.discount_amount.after') !!}

    <!-- Apply Coupon -->
    <div class="py-4 border-y-2 border-zinc-100 my-4">
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.coupon.before') !!}
        @include('shop::checkout.coupon')
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.coupon.after') !!}
    </div>

    <!-- Shipping Rates -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.before') !!}

    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>

        <p class="text-zinc-900 tabular-nums">
            <template v-if="displayTax.shipping == 'including_tax'">
                @{{ cart.formatted_shipping_amount_incl_tax }}
            </template>
            <template v-else>
                @{{ cart.formatted_shipping_amount }}
            </template>
        </p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.after') !!}

    <!-- Taxes -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.tax.before') !!}

    <div class="flex justify-between text-[11px] font-black uppercase tracking-widest" v-if="parseFloat(cart.tax_total) > 0">
        <p class="text-zinc-500">@lang('shop::app.checkout.onepage.summary.tax')</p>
        <p class="text-zinc-900 tabular-nums">@{{ cart.formatted_tax_total }}</p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.tax.after') !!}

    <!-- Cart Grand Total -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.grand_total.before') !!}

    <div class="mt-6 pt-8 border-t-4 border-zinc-900 flex items-center justify-between">
        <p class="text-lg font-black uppercase tracking-widest text-zinc-900">
            @lang('shop::app.checkout.onepage.summary.grand-total')
        </p>
        <p class="text-3xl font-black text-[#7C45F5] tabular-nums">
            @{{ cart.formatted_grand_total }}
        </p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.grand_total.after') !!}
</div>

<!-- Accepted Payment Methods -->
<div class="mt-10 pt-8 border-t-2 border-zinc-100">
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Accepted secure methods:</p>
    <div class="flex items-center gap-3 flex-wrap">
        <div class="h-8 px-3 bg-white border-2 border-zinc-900 flex items-center shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
            <span class="text-[11px] font-black text-[#1A1F71] uppercase tracking-tighter">VISA</span>
        </div>
        <div class="h-8 px-3 bg-white border-2 border-zinc-900 flex items-center gap-1 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
            <div class="w-4 h-4 rounded-full bg-[#EB001B]"></div>
            <div class="w-4 h-4 rounded-full bg-[#F79E1B] -ml-2.5 opacity-80"></div>
        </div>
        <div class="h-8 px-3 bg-white border-2 border-zinc-900 flex items-center shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
            <span class="text-[10px] font-black text-[#26A17B] uppercase">USDT</span>
        </div>
        <div class="h-8 px-3 bg-white border-2 border-zinc-900 flex items-center shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]">
            <span class="text-[10px] font-black text-[#2775CA] uppercase">USDC</span>
        </div>
    </div>
</div>