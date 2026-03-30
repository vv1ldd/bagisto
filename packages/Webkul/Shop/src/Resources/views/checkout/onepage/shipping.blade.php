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
                <h2 class="text-xl font-black uppercase tracking-[0.15em] mb-8 text-zinc-900 border-b-[3px] border-zinc-900 pb-3 inline-block">
                    @lang('shop::app.checkout.onepage.shipping.shipping-method')
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template v-for="method in methods">
                        <div
                            v-for="rate in method.rates"
                            class="relative border-[3px] border-zinc-900 transition-all duration-300 cursor-pointer p-6 overflow-hidden"
                            :class="[selectedShippingMethod == rate.method ? 'bg-[#7C45F5] text-white shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white text-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                            @click="store(rate.method)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div 
                                        class="flex h-12 w-12 items-center justify-center border-[3px]"
                                        :class="[selectedShippingMethod == rate.method ? 'border-white bg-[#7C45F5]' : 'border-zinc-900 bg-zinc-50']"
                                    >
                                        <span class="icon-truck text-2xl"></span>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-[14px] font-black uppercase tracking-widest truncate">@{{ rate.method_title }}</p>
                                        <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mt-0.5">@{{ rate.method_description }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-black tabular-nums">@{{ rate.base_formatted_price }}</p>
                                    <div class="flex h-5 w-5 ml-auto border-[3px] transition-all" :class="[selectedShippingMethod == rate.method ? 'border-white bg-white' : 'border-zinc-900 bg-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]']">
                                        <div v-if="selectedShippingMethod == rate.method" class="m-auto h-2 w-2 bg-[#7C45F5]"></div>
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