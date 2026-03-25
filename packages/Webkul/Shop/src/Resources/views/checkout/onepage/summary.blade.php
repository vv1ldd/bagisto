<!-- Header -->
<h1 class="text-xl font-bold text-zinc-800 max-md:hidden">
    @lang('shop::app.checkout.onepage.summary.cart-summary')
</h1>

<!-- Cart Items -->
<div class="mt-8 grid gap-6 border-b border-zinc-100 pb-8 text-zinc-600">
    <div class="flex gap-x-5" v-for="item in cart.items">
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_image.before') !!}

        <div class="relative">
            <img class="h-16 w-16  border border-zinc-100 object-cover shadow-sm transition-transform hover:scale-105"
                :src="item.base_image.small_image_url" :alt="item.name" width="64" height="64" />
        </div>

        {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_image.after') !!}

        <div class="flex flex-1 flex-col justify-center">
            {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_name.before') !!}

            <p class="text-sm font-semibold text-zinc-800 leading-snug">
                @{{ item.name }}
            </p>

            {!! view_render_event('bagisto.shop.checkout.onepage.summary.item_name.after') !!}

            <div class="mt-1 flex items-center justify-between">
                <p class="text-xs font-medium text-zinc-500">
                    <span class="font-bold text-[#7C45F5]">@{{ item.quantity }} x</span>
                    <template v-if="displayTax.prices == 'including_tax'">
                        @{{ item.formatted_price_incl_tax }}
                    </template>

                    <template v-else>
                        @{{ item.formatted_price }}
                    </template>
                </p>

                <p class="text-sm font-bold text-zinc-900">
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
<div class="mt-8 space-y-4">
    <!-- Sub Total -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.sub_total.before') !!}

    <div class="flex justify-between text-sm">
        <p class="text-zinc-500 font-medium">@lang('shop::app.checkout.onepage.summary.sub-total')</p>

        <p class="font-bold text-zinc-800">
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

    <div class="flex justify-between text-sm" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
        <p class="text-zinc-500 font-medium font-medium">@lang('shop::app.checkout.onepage.summary.discount-amount')</p>
        <p class="font-bold text-green-600">- @{{ cart.formatted_discount_amount }}</p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.discount_amount.after') !!}

    <!-- Apply Coupon -->
    <div class="py-2 border-y border-zinc-50 my-2">
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.coupon.before') !!}
        @include('shop::checkout.coupon')
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.coupon.after') !!}
    </div>

    <!-- Shipping Rates -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.before') !!}

    <div class="flex justify-between text-sm">
        <p class="text-zinc-500 font-medium">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>

        <p class="font-bold text-zinc-800">
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

    <div class="flex justify-between text-sm" v-if="parseFloat(cart.tax_total) > 0">
        <p class="text-zinc-500 font-medium">@lang('shop::app.checkout.onepage.summary.tax')</p>
        <p class="font-bold text-zinc-800">@{{ cart.formatted_tax_total }}</p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.tax.after') !!}

    <!-- Cart Grand Total -->
    {!! view_render_event('bagisto.shop.checkout.onepage.summary.grand_total.before') !!}

    <div class="mt-2 pt-4 border-t border-zinc-100 flex items-center justify-between">
        <p class="text-base font-bold text-zinc-900">
            @lang('shop::app.checkout.onepage.summary.grand-total')
        </p>
        <p class="text-xl font-black text-[#7C45F5]">
            @{{ cart.formatted_grand_total }}
        </p>
    </div>

    {!! view_render_event('bagisto.shop.checkout.onepage.summary.grand_total.after') !!}
</div>

<!-- Accepted Payment Methods -->
<div class="mt-6 pt-5 border-t border-zinc-100 dark:border-white/5">
    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-3">We accept the following secure payment methods:</p>
    <div class="flex items-center gap-2 flex-wrap">
        <div class="h-7 px-2 bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 rounded flex items-center">
            <span class="text-[11px] font-black text-[#1A1F71]">VISA</span>
        </div>
        <div class="h-7 px-2 bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 rounded flex items-center gap-0.5">
            <div class="w-4 h-4 rounded-full bg-[#EB001B]"></div>
            <div class="w-4 h-4 rounded-full bg-[#F79E1B] -ml-2"></div>
        </div>
        <div class="h-7 px-2 bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 rounded flex items-center">
            <span class="text-[10px] font-black text-[#26A17B]">USDT</span>
        </div>
        <div class="h-7 px-2 bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 rounded flex items-center">
            <span class="text-[10px] font-black text-[#2775CA]">USDC</span>
        </div>
    </div>
</div>