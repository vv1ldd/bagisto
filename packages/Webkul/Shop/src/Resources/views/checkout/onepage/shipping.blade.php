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
                                        class="relative border-2 border-zinc-900 transition-all duration-200 cursor-pointer overflow-hidden p-6"
                                        :class="[selectedShippingMethod == rate.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-1 translate-y-1' : 'bg-white text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5']"
                                        @click="store(rate.method)"
                                    >
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
                                                <div 
                                                    class="flex h-12 w-12 items-center justify-center border-4 border-zinc-900 shrink-0"
                                                    :class="[selectedShippingMethod == rate.method ? 'bg-white text-[#7C45F5]' : 'bg-zinc-100 text-zinc-900']"
                                                >
                                                    <span class="icon-flate-rate text-3xl"></span>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="text-sm font-black uppercase tracking-widest truncate">
                                                        @{{ rate.method_title }}
                                                    </p>
                                                    <p 
                                                        class="text-[10px] font-black uppercase tracking-widest mt-1 opacity-60"
                                                    >
                                                        @{{ rate.method_description }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4 shrink-0">
                                                <p class="text-xl font-black tabular-nums">
                                                    @{{ rate.base_formatted_price }}
                                                </p>

                                                <div 
                                                    class="flex h-6 w-6 items-center justify-center border-2 border-zinc-900 transition-all duration-200"
                                                    :class="[selectedShippingMethod == rate.method ? 'bg-white' : 'bg-white']"
                                                >
                                                    <div v-if="selectedShippingMethod == rate.method" class="h-3 w-3 bg-[#7C45F5]"></div>
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