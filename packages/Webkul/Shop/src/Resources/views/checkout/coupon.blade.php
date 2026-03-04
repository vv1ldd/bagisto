<!-- Coupon Vue Component -->
<v-coupon :cart="cart" @coupon-applied="getCart" @coupon-removed="getCart">
</v-coupon>

@pushOnce('scripts')
    <script type="text/x-template" id="v-coupon-template">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-zinc-500">
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
                                                    <h2 class="text-xl font-bold text-zinc-800">
                                                        @lang('shop::app.checkout.coupon.apply')
                                                    </h2>
                                                </x-slot>

                                                <!-- Modal Content -->
                                                <x-slot:content class="!px-6 py-4">
                                                    <x-shop::form.control-group class="!mb-0">
                                                        <x-shop::form.control-group.control
                                                            type="text"
                                                            class="rounded-2xl border border-zinc-100 bg-zinc-50 px-5 py-3.5 text-sm font-medium focus:border-[#7C45F5] focus:bg-white focus:ring-[#7C45F5] transition-all"
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
                                                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                                                @lang('shop::app.checkout.coupon.subtotal')
                                                            </p>
                                                            <p class="text-xl font-black text-zinc-900 mt-0.5">
                                                                @{{ cart.formatted_sub_total }}
                                                            </p>
                                                        </div>

                                                        <x-shop::button
                                                            class="primary-button rounded-xl bg-gradient-to-r from-[#7C45F5] to-[#9D6CFF] px-8 py-3.5 text-sm font-bold text-white shadow-[0_8px_20px_-6px_rgba(124,69,245,0.6)] transition-all hover:shadow-[0_12px_25px_-4px_rgba(124,69,245,0.7)] hover:-translate-y-0.5 active:scale-95 border-0"
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
                                        class="flex items-center gap-2 rounded-full bg-[#7C45F5]/10 px-3 py-1.5 text-[#7C45F5] border border-[#7C45F5]/10"
                                        v-if="cart.coupon_code"
                                    >
                                        <p 
                                            class="text-xs font-bold uppercase tracking-wide"
                                            title="@lang('shop::app.checkout.coupon.applied')"
                                        >
                                            @{{ cart.coupon_code }}
                                        </p>

                                        <span 
                                            class="icon-cancel cursor-pointer text-sm transition-opacity hover:opacity-70"
                                            title="@lang('shop::app.checkout.coupon.remove')"
                                            @click="destroyCoupon"
                                        >
                                        </span>
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