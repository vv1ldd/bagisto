{!! view_render_event('bagisto.shop.checkout.onepage.shipping_methods.before') !!}

<v-shipping-methods :methods="shippingMethods" @processing="stepForward" @processed="stepProcessed">
    <!-- Shipping Method Shimmer Effect -->
    <x-shop::shimmer.checkout.onepage.shipping-method />
</v-shipping-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.shipping_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-shipping-methods-template">
                    <div class="mb-12 max-md:mb-8">
                        <template v-if="! methods">
                            <!-- Shipping Method Shimmer Effect -->
                            <x-shop::shimmer.checkout.onepage.shipping-method />
                        </template>

                        <template v-else>
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-xl font-bold text-zinc-800">
                                    @lang('shop::app.checkout.onepage.shipping.shipping-method')
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template v-for="method in methods">
                                    {!! view_render_event('bagisto.shop.checkout.onepage.shipping_method.before') !!}

                                    <div
                                        v-for="rate in method.rates"
                                        class="relative p-6  border transition-all duration-300 group cursor-pointer overflow-hidden"
                                        :class="[selectedShippingMethod == rate.method ? 'border-[#7C45F5] bg-white ring-1 ring-[#7C45F5] shadow-lg' : 'border-white/60 bg-white/40 backdrop-blur-3xl hover:border-white/80 shadow-sm']"
                                        @click="store(rate.method)"
                                    >
                                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-[#7C45F5]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                        <input 
                                            type="radio"
                                            name="shipping_method"
                                            :id="rate.method"
                                            :value="rate.method"
                                            class="peer hidden"
                                            :checked="selectedShippingMethod == rate.method"
                                        >

                                        <div class="relative flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center  bg-[#7C45F5]/10 text-[#7C45F5]">
                                                    <span class="icon-flate-rate text-3xl"></span>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="font-bold transition-colors duration-300" :class="[selectedShippingMethod == rate.method ? 'text-[#7C45F5]' : 'text-zinc-800']">
                                                        @{{ rate.method_title }}
                                                    </p>
                                                    <p class="text-xs font-medium text-zinc-500 mt-0.5">@{{ rate.method_description }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <p class="text-lg font-black text-zinc-900">
                                                    @{{ rate.base_formatted_price }}
                                                </p>

                                                <div 
                                                    class="flex h-6 w-6 items-center justify-center  border-2 transition-all duration-300"
                                                    :class="[selectedShippingMethod == rate.method ? 'border-[#7C45F5] bg-[#7C45F5] scale-110 shadow-md' : 'border-zinc-300 group-hover:border-zinc-400']"
                                                >
                                                    <div v-if="selectedShippingMethod == rate.method" class="h-2.5 w-2.5  bg-white"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {!! view_render_event('bagisto.shop.checkout.onepage.shipping_method.after') !!}
                                </template>
                            </div>
                        </template>
                    </div>
                </script>

    <script type="module">
        app.component('v-shipping-methods', {
            template: '#v-shipping-methods-template',

            props: {
                methods: {
                    type: Object,
                    required: true,
                    default: () => null,
                },
            },

            emits: ['processing', 'processed'],

            methods: {
                store(selectedMethod) {
                    this.$emit('processing', 'payment');

                    this.$axios.post("{{ route('shop.checkout.onepage.shipping_methods.store') }}", {
                        shipping_method: selectedMethod,
                    })
                        .then(response => {
                            if (response.data.redirect_url) {
                                window.location.href = response.data.redirect_url;
                            } else {
                                this.$emit('processed', response.data);
                            }
                        })
                        .catch(error => {
                            this.$emit('processing', 'shipping');

                            if (error.response.data.redirect_url) {
                                window.location.href = error.response.data.redirect_url;
                            }
                        });
                },
            },
        });
    </script>
@endPushOnce