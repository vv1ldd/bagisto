<!-- Cart Summary (Right sticky panel) -->
<div class="sticky top-8 w-full" v-if="cart?.items?.length">
    <div class="ios-group p-6 flex flex-col gap-5">

        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.before') !!}
        <h2 class="text-base font-bold text-zinc-900 dark:text-white uppercase tracking-widest text-[11px] opacity-50">
            @lang('shop::app.checkout.cart.summary.cart-summary')
        </h2>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.after') !!}

        <div class="flex flex-col gap-3">

            <!-- Sub Total -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.sub_total.before') !!}
            <div class="flex justify-between text-sm">
                <p class="text-zinc-400 dark:text-zinc-500 font-medium">@lang('shop::app.checkout.cart.summary.sub-total')</p>
                <p class="font-bold text-zinc-900 dark:text-white">
                    <template v-if="displayTax.subtotal == 'including_tax'">@{{ cart.formatted_sub_total_incl_tax }}</template>
                    <template v-else-if="displayTax.subtotal == 'both'">
                        <span>@{{ cart.formatted_sub_total }}</span>
                        <span class="block font-normal text-xs text-zinc-400">(@{{ cart.formatted_sub_total_incl_tax }} incl.)</span>
                    </template>
                    <template v-else>@{{ cart.formatted_sub_total }}</template>
                </p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.sub_total.after') !!}

            <!-- Discount -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.discount_amount.before') !!}
            <div class="flex justify-between text-sm" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
                <p class="text-zinc-400 dark:text-zinc-500 font-medium">@lang('shop::app.checkout.cart.summary.discount-amount')</p>
                <p class="font-bold text-green-500">- @{{ cart.formatted_discount_amount }}</p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.discount_amount.after') !!}

            <!-- Coupon -->
            <div class="py-2 border-y border-zinc-100 dark:border-white/5">
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.before') !!}
                @include('shop::checkout.coupon')
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.after') !!}
            </div>

            <!-- Taxes -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.before') !!}
            <div class="flex justify-between text-sm" v-if="parseFloat(cart.tax_total) > 0">
                <p class="text-zinc-400 dark:text-zinc-500 font-medium">@lang('shop::app.checkout.cart.summary.tax')</p>
                <div class="flex flex-col items-end">
                    <p class="font-bold text-zinc-900 dark:text-white cursor-pointer flex items-center gap-1" @click="cart.show_taxes = !cart.show_taxes">
                        @{{ cart.formatted_tax_total }}
                        <span class="text-base" :class="{'icon-arrow-up': cart.show_taxes, 'icon-arrow-down': !cart.show_taxes}"></span>
                    </p>
                    <div class="flex flex-col gap-1 mt-1" v-show="cart.show_taxes">
                        <div class="flex gap-2 justify-end text-xs" v-for="(amount, index) in cart.applied_taxes">
                            <span class="text-zinc-400">@{{ index }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-white">@{{ amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.after') !!}
        </div>

        <!-- Grand Total -->
        {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.before') !!}
        <div class="pt-4 border-t border-zinc-100 dark:border-white/10 flex items-center justify-between">
            <p class="text-sm font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                @lang('shop::app.checkout.cart.summary.grand-total')
            </p>
            <p class="text-2xl font-black text-[#7C45F5]">
                @{{ cart.formatted_grand_total }}
            </p>
        </div>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.after') !!}

        <!-- CTA -->
        {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.before') !!}
        <a
            href="{{ route('shop.checkout.onepage.index') }}"
            class="primary-button !py-4 !rounded-2xl block w-full text-center"
        >
            @lang('shop::app.checkout.cart.summary.proceed-to-checkout')
        </a>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.after') !!}

        <!-- Digital goods notice -->
        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 leading-relaxed text-center">
            Покупка цифрового товара предоставляет лицензию на его использование на вашем аккаунте.
        </p>
    </div>
</div>