{!! view_render_event('bagisto.shop.checkout.onepage.address.before') !!}

<!-- If the customer is guest -->
<template v-if="cart.is_guest">
    <div class="mb-10 mt-8 bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] p-8 md:p-12">
        <h2 class="text-3xl font-black uppercase tracking-[0.2em] mb-10 text-zinc-900">
            @lang('shop::app.checkout.onepage.address.title')
        </h2>
        <div class="h-1.5 w-20 bg-zinc-900 -mt-8 mb-12"></div>
        
        @include('shop::checkout.onepage.address.guest')
    </div>
</template>

<!-- If the customer is logged in -->
<template v-else>
    <div>
        @include('shop::checkout.onepage.address.customer')
    </div>
</template>

{!! view_render_event('bagisto.shop.checkout.onepage.address.after') !!}