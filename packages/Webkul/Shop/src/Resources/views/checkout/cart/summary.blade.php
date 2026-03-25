<!-- Cart Summary -->
<div class="sticky top-8 w-full" v-if="cart?.items?.length">
    <div class="ios-group p-6 sm:p-8 flex flex-col gap-6">
        
        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.before') !!}
        <h1 class="text-xl font-bold text-zinc-900 dark:text-white" role="heading" aria-level="1">
            @lang('shop::app.checkout.cart.summary.cart-summary')
        </h1>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.after') !!}

        <div class="flex flex-col gap-4">
            <!-- Estimate Tax and Shipping -->
            @if (core()->getConfigData('sales.checkout.shopping_cart.estimate_shipping'))
                <template v-if="cart.have_stockable_items">
                    @include('shop::checkout.cart.summary.estimate-shipping')
                </template>
            @endif

            <!-- Sub Total -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.sub_total.before') !!}
            <div class="flex justify-between text-sm">
                <p class="text-zinc-500 dark:text-zinc-400 font-medium font-bold uppercase tracking-wider text-[10px]">@lang('shop::app.checkout.cart.summary.sub-total')</p>

                <p class="font-bold text-zinc-900 dark:text-white">
                    <template v-if="displayTax.subtotal == 'including_tax'">
                        @{{ cart.formatted_sub_total_incl_tax }}
                    </template>
                    <template v-else-if="displayTax.subtotal == 'both'">
                        <div>
                            <span>@{{ cart.formatted_sub_total }}</span><br>
                            <span class="font-normal text-xs">(@{{ cart.formatted_sub_total_incl_tax }} incl.)</span>
                        </div>
                    </template>
                    <template v-else>
                        @{{ cart.formatted_sub_total }}
                    </template>
                </p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.sub_total.after') !!}

            <!-- Discount -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.discount_amount.before') !!}
            <div class="flex justify-between text-sm" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
                <p class="text-zinc-500 dark:text-zinc-400 font-medium font-bold uppercase tracking-wider text-[10px]">@lang('shop::app.checkout.cart.summary.discount-amount')</p>
                <p class="font-bold text-green-600">- @{{ cart.formatted_discount_amount }}</p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.discount_amount.after') !!}

            <!-- Apply Coupon -->
            <div class="py-2 border-y border-zinc-50 dark:border-white/5 my-1">
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.before') !!}
                @include('shop::checkout.coupon')
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.after') !!}
            </div>

            <!-- Shipping Rates -->
            {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.before') !!}
            <div class="flex justify-between text-sm">
                <p class="text-zinc-500 dark:text-zinc-400 font-medium font-bold uppercase tracking-wider text-[10px]">@lang('shop::app.checkout.cart.summary.delivery-charges')</p>

                <p class="font-bold text-zinc-900 dark:text-white">
                    <template v-if="displayTax.shipping == 'including_tax'">
                        @{{ cart.formatted_shipping_amount_incl_tax }}
                    </template>
                    <template v-else-if="displayTax.shipping == 'both'">
                        <div>
                            <span>@{{ cart.formatted_shipping_amount }}</span><br>
                            <span class="font-normal text-xs">(@{{ cart.formatted_shipping_amount_incl_tax }} incl.)</span>
                        </div>
                    </template>
                    <template v-else>
                        @{{ cart.formatted_shipping_amount }}
                    </template>
                </p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.after') !!}

            <!-- Taxes -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.before') !!}
            <div class="flex justify-between text-sm" v-if="parseFloat(cart.tax_total) > 0">
                <p class="text-zinc-500 dark:text-zinc-400 font-medium font-bold uppercase tracking-wider text-[10px]">@lang('shop::app.checkout.cart.summary.tax')</p>

                <div class="flex flex-col items-end">
                    <p class="font-bold text-zinc-900 dark:text-white cursor-pointer flex items-center gap-1" @click="cart.show_taxes = ! cart.show_taxes">
                        @{{ cart.formatted_tax_total }}
                        <span class="text-base" :class="{'icon-arrow-up': cart.show_taxes, 'icon-arrow-down': ! cart.show_taxes}"></span>
                    </p>

                    <div class="flex flex-col gap-1 mt-1" v-show="cart.show_taxes">
                        <div class="flex gap-2 justify-end text-xs" v-for="(amount, index) in cart.applied_taxes">
                            <span class="text-zinc-500">@{{ index }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-white">@{{ amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.after') !!}

            <!-- Cart Grand Total -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.before') !!}
            <div class="mt-2 pt-4 border-t border-zinc-100 dark:border-white/5 flex items-center justify-between">
                <p class="text-base font-bold text-zinc-900 dark:text-white">
                    @lang('shop::app.checkout.cart.summary.grand-total')
                </p>
                <p class="text-xl font-black text-[#7C45F5]">
                    @{{ cart.formatted_grand_total }}
                </p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.after') !!}

            {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.before') !!}
            <a href="{{ route('shop.checkout.onepage.index') }}"
                class="primary-button !py-4 !rounded-2xl block w-full text-center">
                @lang('shop::app.checkout.cart.summary.proceed-to-checkout')
            </a>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.after') !!}
        </div>
    </div>
</div>