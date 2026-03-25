<!-- Coupon Vue Component -->
<v-coupon :cart="cart" @coupon-applied="getCart" @coupon-removed="getCart">
</v-coupon>

@pushOnce('scripts')
    <script type="text/x-template" id="v-coupon-template">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold uppercase tracking-wider text-[10px] text-zinc-500 dark:text-zinc-400">
                                    @{{ cart.coupon_code ? "@lang('shop::app.checkout.coupon.applied')" : "@lang('shop::app.checkout.coupon.discount')" }}
                                </p>

                                {!! view_render_event('bagisto.shop.checkout.cart.coupon.before') !!}

                                <div class="flex items-center">
                                    <!-- Apply Coupon Form -->
                                    <x-shop::form
                                        v-slot="{ meta, errors, handleSubmit }"
                                        as="div"
                                    >
                                        <!-- Apply coupon form -->
                                        <form @submit="handleSubmit($event, applyCoupon)">
                                            {!! view_render_event('bagisto.shop.checkout.cart.coupon.coupon_form_controls.before') !!}

                                            <!-- Apply coupon modal -->
                                            <x-shop::modal ref="couponModel">
                                                <!-- Modal Toggler -->
                                                <x-slot:toggle>
                                                    <span 
                                                        class="cursor-pointer text-[13px] font-bold text-[#7C45F5] transition-all hover:text-[#6b35e4] hover:underline"
                                                        role="button"
                                                        tabindex="0"
                                                        v-if="! cart.coupon_code"
                                                    >
                                                        @lang('shop::app.checkout.coupon.apply')
                                                    </span>
                                                </x-slot>

                                                <!-- Modal Header -->
                                                <x-slot:header class="!p-6 !pb-2">
                                                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                                                        @lang('shop::app.checkout.coupon.apply')
                                                    </h2>
                                                </x-slot>

                                                <!-- Modal Content -->
                                                <x-slot:content class="!px-6 py-4">
                                                    <x-shop::form.control-group class="!mb-0">
                                                        <x-shop::form.control-group.control
                                                            type="text"
                                                            class="border border-zinc-100 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900 px-5 py-3.5 text-sm font-medium text-zinc-900 dark:text-white focus:border-[#7C45F5] focus:bg-white dark:focus:bg-zinc-800 focus:ring-[#7C45F5] transition-all rounded-xl"
                                                            name="code"
                                                            rules="required"
                                                            :placeholder="trans('shop::app.checkout.coupon.enter-your-code')"
                                                        />

                                                        <x-shop::form.control-group.error
                                                            class="mt-2 flex text-xs font-bold text-red-500"
                                                            control-name="code"
                                                        />
                                                    </x-shop::form.control-group>
                                                </x-slot>

                                                <!-- Modal Footer -->
                                                <x-slot:footer class="!px-6 !pb-8 !pt-4">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <div class="flex flex-col">
                                                            <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">
                                                                @lang('shop::app.checkout.coupon.subtotal')
                                                            </p>
                                                            <p class="text-xl font-black text-zinc-900 dark:text-white mt-0.5">
                                                                @{{ cart.formatted_sub_total }}
                                                            </p>
                                                        </div>

                                                        <x-shop::button
                                                            class="primary-button  bg-gradient-to-r from-[#7C45F5] to-[#9D6CFF] px-8 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_-6px_rgba(124,69,245,0.6)] transition-all hover:shadow-[0_12px_25px_-4px_rgba(124,69,245,0.7)] hover:-translate-y-0.5 active:scale-95 border-0"
                                                            :title="trans('shop::app.checkout.coupon.button-title')"
                                                            ::loading="isStoring"
                                                            ::disabled="isStoring"
                                                        />
                                                    </div>
                                                </x-slot>
                                            </x-shop::modal>

                                            {!! view_render_event('bagisto.shop.checkout.cart.coupon.coupon_form_controls.after') !!}
                                        </form>
                                    </x-shop::form>

                                    <!-- Applied Coupon Information Container -->
                                    <div 
                                        class="flex items-center gap-2 bg-[#7C45F5]/10 dark:bg-[#7C45F5]/20 px-3 py-1.5 text-[#7C45F5] border border-[#7C45F5]/10 dark:border-[#7C45F5]/30 rounded-full"
                                        v-if="cart.coupon_code"
                                    >
                                        <p 
                                            class="text-xs font-bold uppercase tracking-wide"
                                            title="@lang('shop::app.checkout.coupon.applied')"
                                        >
                                            @{{ cart.coupon_code }}
                                        </p>

                                        <div 
                                            class="flex h-5 w-5 cursor-pointer items-center justify-center bg-red-500 text-white transition-all hover:bg-red-600 active:scale-95 rounded-full"
                                            title="@lang('shop::app.checkout.coupon.remove')"
                                            @click="destroyCoupon"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {!! view_render_event('bagisto.shop.checkout.cart.coupon.after') !!}
                            </div>
                        </script>

    <script type="module">
        app.component('v-coupon', {
            template: '#v-coupon-template',

            props: ['cart'],

            data() {
                return {
                    isStoring: false,
                }
            },

            methods: {
                applyCoupon(params, { resetForm }) {
                    this.isStoring = true;

                    this.$axios.post("{{ route('shop.api.checkout.cart.coupon.apply') }}", params)
                        .then((response) => {
                            this.isStoring = false;

                            this.$emit('coupon-applied');

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$refs.couponModel.toggle();

                            resetForm();
                        })
                        .catch((error) => {
                            this.isStoring = false;

                            this.$refs.couponModel.toggle();

                            if ([400, 422].includes(error.response.request.status)) {
                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });

                                resetForm();

                                return;
                            }

                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        });
                },

                destroyCoupon() {
                    this.$axios.delete("{{ route('shop.api.checkout.cart.coupon.remove') }}", {
                        '_token': "{{ csrf_token() }}"
                    })
                        .then((response) => {
                            this.$emit('coupon-removed');

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => console.log(error));
                },
            }
        })
    </script>
@endPushOnce