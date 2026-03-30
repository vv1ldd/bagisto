{!! view_render_event('bagisto.shop.checkout.onepage.shipping_methods.before') !!}

<v-shipping-methods :methods="shippingMethods" @processing="stepForward" @processed="stepProcessed">
    <x-shop::shimmer.checkout.onepage.shipping-method />
</v-shipping-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.shipping_methods.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-shipping-methods-template">
        <div class="animate-in fade-in duration-500">
            <template v-if="! methods">
                <x-shop::shimmer.checkout.onepage.shipping-method />
            </template>

            <template v-else>
                <h2 class="text-2xl font-black uppercase tracking-[0.2em] mb-10 text-zinc-900 border-b-4 border-zinc-900 pb-4 inline-block">
                    @lang('shop::app.checkout.onepage.shipping.shipping-method')
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <template v-for="method in methods">
                        <div
                            v-for="rate in method.rates"
                            class="relative border-4 border-zinc-900 transition-all duration-300 cursor-pointer p-8 overflow-hidden"
                            :class="[selectedShippingMethod == rate.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-1 translate-y-1' : 'bg-white text-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)]']"
                            @click="store(rate.method)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-6">
                                    <div 
                                        class="flex h-16 w-16 items-center justify-center border-4"
                                        :class="[selectedShippingMethod == rate.method ? 'border-white bg-[#7C45F5]' : 'border-zinc-900 bg-zinc-50']"
                                    >
                                        <span class="icon-truck text-3xl"></span>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-lg font-black uppercase tracking-widest truncate">@{{ rate.method_title }}</p>
                                        <p class="text-[11px] font-black uppercase tracking-widest opacity-60 mt-1">@{{ rate.method_description }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl font-black tabular-nums mb-2">@{{ rate.base_formatted_price }}</p>
                                    <div class="flex h-6 w-6 ml-auto border-4 transition-all" :class="[selectedShippingMethod == rate.method ? 'border-white bg-white' : 'border-zinc-900 bg-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]']">
                                        <div v-if="selectedShippingMethod == rate.method" class="m-auto h-2.5 w-2.5 bg-[#7C45F5]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-shipping-methods', {
            template: '#v-shipping-methods-template',
            props: ['methods'],
            emits: ['processing', 'processed'],
            data() {
                return {
                    selectedShippingMethod: null,
                }
            },
            methods: {
                store(selectedMethod) {
                    this.selectedShippingMethod = selectedMethod;
                    this.$emit('processing', 'payment');
                    this.$axios.post("{{ route('shop.checkout.onepage.shipping_methods.store') }}", { shipping_method: selectedMethod })
                        .then(response => {
                            this.$emit('processed', response.data);
                        }).catch(() => this.$emit('processing', 'shipping'));
                }
            }
        });
    </script>
@endPushOnce