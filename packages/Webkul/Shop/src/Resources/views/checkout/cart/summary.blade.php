<!-- Cart Summary (Right sticky panel) -->
<div class="sticky top-8 w-full" v-if="cart?.items?.length">
    <div class="bg-white border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] p-8 flex flex-col gap-6">

        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.before') !!}
        <h2 class="text-[12px] font-black uppercase tracking-[0.3em] text-zinc-400">
            @lang('shop::app.checkout.cart.summary.cart-summary')
        </h2>
        <div class="h-1 w-10 bg-zinc-900 -mt-4"></div>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.title.after') !!}

        <div class="flex flex-col gap-4">

            <!-- Sub Total -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.sub_total.before') !!}
            <div class="flex justify-between items-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">@lang('shop::app.checkout.cart.summary.sub-total')</p>
                <p class="text-lg font-black text-zinc-900 tabular-nums tracking-tighter">
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
            <div class="flex justify-between items-center" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">@lang('shop::app.checkout.cart.summary.discount-amount')</p>
                <p class="text-lg font-black text-green-600 tabular-nums tracking-tighter">- @{{ cart.formatted_discount_amount }}</p>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.discount_amount.after') !!}

            <!-- Coupon -->
            <div class="py-4 border-y-2 border-zinc-100">
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.before') !!}
                @include('shop::checkout.coupon')
                {!! view_render_event('bagisto.shop.checkout.cart.summary.coupon.after') !!}
            </div>

            <!-- Taxes -->
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.before') !!}
            <div class="flex justify-between items-start" v-if="parseFloat(cart.tax_total) > 0">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">@lang('shop::app.checkout.cart.summary.tax')</p>
                <div class="flex flex-col items-end">
                    <p class="text-lg font-black text-zinc-900 tabular-nums tracking-tighter cursor-pointer flex items-center gap-1" @click="cart.show_taxes = !cart.show_taxes">
                        @{{ cart.formatted_tax_total }}
                        <span class="text-base" :class="{'icon-arrow-up': cart.show_taxes, 'icon-arrow-down': !cart.show_taxes}"></span>
                    </p>
                    <div class="flex flex-col gap-1 mt-2" v-show="cart.show_taxes">
                        <div class="flex gap-2 justify-end text-[10px] font-bold uppercase tracking-tight" v-for="(amount, index) in cart.applied_taxes">
                            <span class="text-zinc-400">@{{ index }}:</span>
                            <span class="text-zinc-900">@{{ amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.checkout.cart.summary.tax.after') !!}
        </div>

        <!-- Grand Total -->
        {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.before') !!}
        <div class="py-6 border-t-[3px] border-zinc-900 flex items-center justify-between">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-900">
                @lang('shop::app.checkout.cart.summary.grand-total')
            </p>
            <p class="text-3xl font-black text-[#7C45F5] tabular-nums tracking-tighter">
                @{{ cart.formatted_grand_total }}
            </p>
        </div>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.grand_total.after') !!}

        <!-- CTA -->
        {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.before') !!}
        <a
            href="{{ route('shop.checkout.onepage.index') }}"
            class="bg-[#7C45F5] border-[3px] border-zinc-900 py-5 text-[14px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:bg-[#8A5CF7] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all block w-full text-center"
        >
            @lang('shop::app.checkout.cart.summary.proceed-to-checkout')
        </a>
        {!! view_render_event('bagisto.shop.checkout.cart.summary.proceed_to_checkout.after') !!}

        <!-- Digital goods notice -->
        <p class="text-[10px] font-bold text-zinc-400 leading-relaxed text-center uppercase tracking-tight">
            Лицензия на цифровой товар будет привязана к вашему аккаунту.
        </p>
    </div>
</div>
