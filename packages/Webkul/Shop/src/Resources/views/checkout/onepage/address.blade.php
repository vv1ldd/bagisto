{!! view_render_event('bagisto.shop.checkout.onepage.address.before') !!}

<!-- If the customer is guest -->
<template v-if="cart.is_guest">
    <x-shop::accordion
        class="mb-7 mt-8 overflow-hidden  border border-white/40 bg-white/40 backdrop-blur-3xl shadow-sm !border-b-0 max-md:mb-4 max-md:mt-4">
        <!-- Accordion Header Component Slot -->
        <x-slot:header
            class="!p-0 max-md:!mb-0 max-md: max-md:!p-3 max-md:text-sm max-md:font-medium max-sm:!p-2">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-medium max-md:text-base">
                    @lang('shop::app.checkout.onepage.address.title')
                </h2>
            </div>
            </x-slot>

            <!-- Accordion Content Component Slot -->
            <x-slot:content
                class="mt-8 !p-0 max-md:mt-0 max-md: max-md:border max-md:border-t-0 max-md:!p-4">
                @include('shop::checkout.onepage.address.guest')
            </x-slot:content>
    </x-shop::accordion>
</template>

<!-- If the customer is logged in -->
<template v-else>
    <div>
        @include('shop::checkout.onepage.address.customer')
    </div>
</template>

{!! view_render_event('bagisto.shop.checkout.onepage.address.after') !!}