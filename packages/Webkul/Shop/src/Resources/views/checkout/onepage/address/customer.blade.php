{!! view_render_event('bagisto.shop.checkout.onepage.address.customer.before') !!}

<!-- Customer Address Vue Component -->
<v-checkout-address-customer :cart="cart" @processing="stepForward" @processed="stepProcessed">
    <!-- Billing Address Shimmer -->
    <x-shop::shimmer.checkout.onepage.address />
</v-checkout-address-customer>

{!! view_render_event('bagisto.shop.checkout.onepage.address.customer.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-checkout-address-customer-template">
                                        <template v-if="isLoading">
                                            <!-- Billing Address Shimmer -->
                                            <x-shop::shimmer.checkout.onepage.address />
                                        </template>

                                        <template v-else>
                                            <!-- Unified User View -->
                                            <div v-if="! activeAddressForm">
                                                <!-- Profile Card -->
                                                <div class="mb-8">
                                                    <div class="p-6 rounded-2xl border border-zinc-200 bg-white/50 backdrop-blur-sm shadow-sm relative overflow-hidden group">
                                                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-zinc-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                                        <div class="relative flex items-center justify-between">
                                                            <div class="flex items-center gap-6">
                                                                <div class="w-16 h-16 rounded-full bg-navyBlue flex items-center justify-center text-white text-2xl font-bold">
                                                                    @{{ cart.billing_address?.first_name?.[0] || '{{ auth()->guard('customer')->user()?->first_name[0] ?? "U" }}' }}
                                                                </div>

                                                                <div>
                                                                    <p class="text-lg font-semibold text-navyBlue">
                                                                        @{{ (cart.billing_address?.first_name || '{{ auth()->guard('customer')->user()?->first_name ?? "" }}') + ' ' + (cart.billing_address?.last_name || '{{ auth()->guard('customer')->user()?->last_name ?? "" }}') }}
                                                                    </p>
                                                                    <p class="text-sm text-zinc-500 mt-1">
                                                                        @{{ cart.billing_address?.email || '{{ auth()->guard('customer')->user()?->email ?? "" }}' }}
                                                                    </p>
                                                                    <p class="text-xs text-zinc-400 mt-2 flex items-center gap-1">
                                                                        <span class="icon-checkout-address text-base"></span>
                                                                        @{{ cart.billing_address?.country || '{{ auth()->guard('customer')->user()?->country_of_residence ?? "" }}' }}
                                                                        <template v-if="cart.billing_address?.phone || '{{ auth()->guard('customer')->user()?->phone ?? "" }}'">
                                                                            • @{{ cart.billing_address?.phone || '{{ auth()->guard('customer')->user()?->phone ?? "" }}' }}
                                                                        </template>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <span 
                                                                class="icon-edit text-2xl cursor-pointer text-zinc-400 hover:text-navyBlue transition-colors"
                                                                @click="activeAddressForm = 'billing'; selectedAddressForEdit = cart.billing_address || undefined"
                                                                title="Изменить данные"
                                                            ></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Shipping Address Section (Physical Goods) -->
                                                <div v-if="cart.have_stockable_items" class="mb-8">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-xl font-medium text-navyBlue">
                                                            @lang('shop::app.checkout.onepage.address.shipping-address')
                                                        </h3>

                                                        <button 
                                                            class="text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors"
                                                            @click="activeAddressForm = 'shipping'; selectedAddressForEdit = null"
                                                        >
                                                            + @lang('shop::app.checkout.onepage.address.add-new-address')
                                                        </button>
                                                    </div>

                                                    <!-- Shipping Address List -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div 
                                                            v-for="address in customerSavedAddresses.shipping"
                                                            :key="address.id"
                                                            class="relative p-5 rounded-xl border cursor-pointer transition-all duration-300 group"
                                                            :class="[selectedAddresses.shipping_address_id == address.id ? 'border-purple-500 bg-purple-50/30 ring-1 ring-purple-500' : 'border-zinc-200 hover:border-zinc-300']"
                                                            @click="selectedAddresses.shipping_address_id = address.id"
                                                        >
                                                            <div class="flex justify-between items-start mb-2">
                                                                <p class="font-medium text-navyBlue">@{{ address.first_name }} @{{ address.last_name }}</p>
                                                                <div class="flex gap-2">
                                                                    <span 
                                                                        class="icon-edit text-xl text-zinc-400 hover:text-navyBlue opacity-0 group-hover:opacity-100 transition-opacity"
                                                                        @click.stop="selectedAddressForEdit = address; activeAddressForm = 'shipping'"
                                                                    ></span>
                                                                    <div 
                                                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                                                        :class="[selectedAddresses.shipping_address_id == address.id ? 'border-purple-600 bg-purple-600' : 'border-zinc-300']"
                                                                    >
                                                                        <div v-if="selectedAddresses.shipping_address_id == address.id" class="w-2 h-2 rounded-full bg-white"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-sm text-zinc-500 leading-relaxed">
                                                                @{{ address.address.join(', ') }}<br>
                                                                @{{ address.city }}, @{{ address.state }}<br>
                                                                @{{ address.country }}, @{{ address.postcode }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Button -->
                                                <div class="mt-8 flex justify-end">
                                                    <x-shop::button
                                                        class="primary-button rounded-2xl px-12 py-4 text-lg shadow-lg hover:shadow-xl transition-all"
                                                        ::title="cart.have_stockable_items ? '{{ trans('shop::app.checkout.onepage.address.proceed') }}' : '{{ trans('shop::app.checkout.onepage.address.proceed') }}'"
                                                        ::loading="isStoring"
                                                        ::disabled="isStoring || (cart.have_stockable_items && !selectedAddresses.shipping_address_id)"
                                                        @click="proceedWithUnifiedCard"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Create/Edit Address Form -->
                                            <template v-else>
                                                <x-shop::form
                                                    v-slot="{ meta, errors, handleSubmit }"
                                                    as="div"
                                                >
                                                    <form @submit="handleSubmit($event, updateOrCreateAddress)">
                                                        <!-- Billing Address Header -->
                                                        <div class="mb-4 flex items-center justify-between">
                                                            <h2 class="text-xl font-medium max-md:text-base max-sm:font-normal" v-if="cart.have_stockable_items">
                                                                <template v-if="activeAddressForm == 'billing'">
                                                                    @lang('shop::app.checkout.onepage.address.billing-address')
                                                                </template>

                                                                <template v-else>
                                                                    @lang('shop::app.checkout.onepage.address.shipping-address')
                                                                </template>
                                                            </h2>

                                                            <span
                                                                class="flex cursor-pointer justify-end"
                                                                v-show="customerSavedAddresses.billing.length && ['billing', 'shipping'].includes(activeAddressForm) && cart.have_stockable_items"
                                                                @click="selectedAddressForEdit = null; activeAddressForm = null"
                                                            >
                                                                <span class="icon-arrow-left text-2xl max-md:hidden"></span>

                                                                @lang('shop::app.checkout.onepage.address.back')
                                                            </span>
                                                        </div>

                                                        <!-- Address Form Vue Component -->
                                                        <v-checkout-address-form
                                                            :control-name="activeAddressForm"
                                                            :address="selectedAddressForEdit || undefined"
                                                            :cart="cart"
                                                        ></v-checkout-address-form>

                                                        <!-- Save Address to Address Book Checkbox -->
                                                        <x-shop::form.control-group class="!mb-0 flex items-center gap-2.5" v-if="cart.have_stockable_items">
                                                            <x-shop::form.control-group.control
                                                                type="checkbox"
                                                                ::name="activeAddressForm + '.save_address'"
                                                                id="save_address"
                                                                for="save_address"
                                                                value="1"
                                                                v-model="saveAddress"
                                                                @change="saveAddress = ! saveAddress"
                                                            />

                                                            <label
                                                                class="cursor-pointer select-none text-base text-zinc-500 max-md:text-sm max-sm:text-xs ltr:pl-0 rtl:pr-0"
                                                                for="save_address"
                                                            >
                                                                @lang('shop::app.checkout.onepage.address.save-address')
                                                            </label>
                                                        </x-shop::form.control-group>

                                                        <!-- Save Button -->
                                                        <div class="mt-4 flex justify-end">
                                                            <x-shop::button
                                                                class="primary-button rounded-2xl px-11 py-3 max-md:rounded-lg max-sm:w-full max-sm:max-w-full max-sm:py-1.5"
                                                                ::title="cart.have_stockable_items ? '{{ trans('shop::app.checkout.onepage.address.save') }}' : '{{ trans('shop::app.checkout.onepage.address.proceed') }}'"
                                                                ::loading="isStoring"
                                                                ::disabled="isStoring"
                                                            />
                                                        </div>
                                                    </form>
                                                </x-shop::form>
                                            </template>
                                        </template>
                                    </script>

    <script type="module">
        app.component('v-checkout-address-customer', {
            template: '#v-checkout-address-customer-template',

            props: ['cart'],

            emits: ['processing', 'processed'],

            data() {
                return {
                    customerSavedAddresses: {
                        'billing': [],
                        'shipping': [],
                    },

                    useBillingAddressForShipping: true,

                    activeAddressForm: null,

                    selectedAddressForEdit: null,

                    saveAddress: false,

                    selectedAddresses: {
                        billing_address_id: null,
                        shipping_address_id: null,
                    },

                    isLoading: true,

                    isStoring: false,
                }
            },

            created() {
                if (this.cart?.billing_address) {
                    this.useBillingAddressForShipping = this.cart.billing_address.use_for_shipping;
                }
            },

            mounted() {
                this.getCustomerSavedAddresses();
            },

            methods: {
                getCustomerSavedAddresses() {
                    this.$axios.get('{{ route('shop.api.customers.account.addresses.index') }}')
                        .then(response => {
                            this.initializeAddresses('billing', structuredClone(response.data.data));

                            this.initializeAddresses('shipping', structuredClone(response.data.data));

                            if (!this.customerSavedAddresses.billing.length || !this.cart.have_stockable_items) {
                                this.activeAddressForm = 'billing';
                            }
                        })
                        .catch((error) => {
                            console.error('Failed to fetch addresses:', error);

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Ошибка при загрузке адресов'
                            });
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                initializeAddresses(type, addresses) {
                    this.customerSavedAddresses[type] = addresses;

                    let cartAddress = this.cart[type + '_address'];

                    if (!cartAddress) {
                        addresses.forEach(address => {
                            if (address.default_address) {
                                this.selectedAddresses[type + '_address_id'] = address.id;
                            }
                        });

                        return addresses;
                    }

                    if (cartAddress.parent_address_id) {
                        addresses.forEach(address => {
                            if (address.id == cartAddress.parent_address_id) {
                                this.selectedAddresses[type + '_address_id'] = address.id;
                            }
                        });
                    } else {
                        this.selectedAddresses[type + '_address_id'] = cartAddress.id;

                        addresses.unshift(cartAddress);
                    }

                    return addresses;
                },

                updateOrCreateAddress(params, { setErrors }) {
                    this.$emit('processing', 'address');

                    params = params[this.activeAddressForm];

                    let address = this.customerSavedAddresses[this.activeAddressForm].find(address => {
                        return address.id == params.id;
                    });

                    if (!address) {
                        if (params.save_address) {
                            this.createCustomerAddress(params, { setErrors })
                                .then((response) => {
                                    this.addAddressToList(response.data.data);
                                })
                                .catch((error) => { });
                        } else {
                            this.addAddressToList(params);
                        }

                        return;
                    }

                    if (params.save_address) {
                        if (address.address_type == 'customer') {
                            this.updateCustomerAddress(params.id, params, { setErrors })
                                .then((response) => {
                                    this.updateAddressInList(response.data.data);
                                })
                                .catch((error) => { });
                        } else {
                            this.removeAddressFromList(params);

                            this.createCustomerAddress(params, { setErrors })
                                .then((response) => {
                                    this.addAddressToList(response.data.data);
                                })
                                .catch((error) => { });
                        }
                    } else {
                        this.updateAddressInList(params);
                    }
                },

                addAddressToList(address) {
                    this.cart[this.activeAddressForm + '_address'] = address;

                    this.customerSavedAddresses[this.activeAddressForm].unshift(address);

                    this.selectedAddresses[this.activeAddressForm + '_address_id'] = address.id;

                    this.activeAddressForm = null;
                },

                updateAddressInList(params) {
                    this.customerSavedAddresses[this.activeAddressForm].forEach((address, index) => {
                        if (address.id == params.id) {
                            params = {
                                ...address,
                                ...params,
                            };

                            this.cart[this.activeAddressForm + '_address'] = params;

                            this.customerSavedAddresses[this.activeAddressForm][index] = params;

                            this.selectedAddresses[this.activeAddressForm + '_address_id'] = params.id;

                            this.activeAddressForm = null;
                        }
                    });
                },

                removeAddressFromList(params) {
                    this.customerSavedAddresses[this.activeAddressForm] = this.customerSavedAddresses[this.activeAddressForm].filter(address => address.id != params.id);
                },

                createCustomerAddress(params, { setErrors }) {
                    this.isStoring = true;

                    return this.$axios.post('{{ route('shop.api.customers.account.addresses.store') }}', params)
                        .then((response) => {
                            this.isStoring = false;

                            return response;
                        })
                        .catch(error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                let errors = {};

                                Object.keys(error.response.data.errors).forEach(key => {
                                    errors[this.activeAddressForm + '.' + key] = error.response.data.errors[key];
                                });

                                setErrors(errors);
                            }

                            return Promise.reject(error);
                        });
                },

                updateCustomerAddress(id, params, { setErrors }) {
                    this.isStoring = true;

                    return this.$axios.put('{{ route('shop.api.customers.account.addresses.update') }}/' + id, params)
                        .then((response) => {
                            this.isStoring = false;

                            return response;
                        })
                        .catch(error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                let errors = {};

                                Object.keys(error.response.data.errors).forEach(key => {
                                    errors[this.activeAddressForm + '.' + key] = error.response.data.errors[key];
                                });

                                setErrors(errors);
                            }

                            return Promise.reject(error);
                        });
                },

                addAddressToCart(params, { setErrors }) {
                    let payload = {
                        billing: {
                            ...this.getSelectedAddress('billing', params.billing.id),

                            use_for_shipping: this.useBillingAddressForShipping
                        },
                    };

                    if (params.shipping !== undefined) {
                        payload.shipping = this.getSelectedAddress('shipping', params.shipping.id);
                    }

                    this.isStoring = true;

                    this.moveToNextStep();

                    this.$axios.post('{{ route('shop.checkout.onepage.addresses.store') }}', payload)
                        .then((response) => {
                            this.isStoring = false;

                            if (response.data.data.redirect_url) {
                                window.location.href = response.data.data.redirect_url;
                            } else {
                                if (this.cart.have_stockable_items) {
                                    this.$emit('processed', response.data.data.shippingMethods);
                                } else {
                                    this.$emit('processed', response.data.data.payment_methods);
                                }
                            }
                        })
                        .catch(error => {
                            this.isStoring = false;

                            this.$emit('processing', 'address');

                            if (error.response.status == 422) {
                                const billingRegex = /^billing\./;

                                if (Object.keys(error.response.data.errors).some(key => billingRegex.test(key))) {
                                    setErrors({
                                        'billing.id': error.response.data.message
                                    });
                                } else {
                                    setErrors({
                                        'shipping.id': error.response.data.message
                                    });
                                }
                            }
                        });
                },

                getSelectedAddress(type, id) {
                    let address = Object.assign({}, this.customerSavedAddresses[type].find(address => address.id == id));

                    if (id == 0) {
                        address.id = null;
                    }

                    return {
                        ...address,

                        default_address: 0,
                    };
                },

                proceedWithUnifiedCard() {
                    this.isStoring = true;

                    let payload = {
                        billing: {
                            first_name: '{{ auth()->guard('customer')->user()?->first_name ?? "" }}',
                            last_name: '{{ auth()->guard('customer')->user()?->last_name ?? "" }}',
                            email: '{{ auth()->guard('customer')->user()?->email ?? "" }}',
                            country: '{{ auth()->guard('customer')->user()?->country_of_residence ?? "" }}',
                            phone: '{{ auth()->guard('customer')->user()?->phone ?? "" }}',
                            address: ['Digital'],
                            city: 'Digital',
                            state: 'Digital',
                            postcode: '000000',
                            use_for_shipping: !!this.cart.have_stockable_items
                        },
                    };

                    // If physical goods, override billing with actual data if available, 
                    // and add shipping address
                    if (this.cart.have_stockable_items) {
                        let selectedShipping = this.getSelectedAddress('shipping', this.selectedAddresses.shipping_address_id);

                        payload.billing = {
                            ...selectedShipping,
                            use_for_shipping: true
                        };

                        payload.shipping = selectedShipping;
                    }

                    this.moveToNextStep();

                    this.$axios.post('{{ route('shop.checkout.onepage.addresses.store') }}', payload)
                        .then((response) => {
                            this.isStoring = false;

                            if (response.data.data.redirect_url) {
                                window.location.href = response.data.data.redirect_url;
                            } else {
                                if (this.cart.have_stockable_items) {
                                    this.$emit('processed', response.data.data.shippingMethods);
                                } else {
                                    this.$emit('processed', response.data.data.payment_methods);
                                }
                            }
                        })
                        .catch(error => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Ошибка при сохранении данных'
                            });
                        });
                },

                moveToNextStep() {
                    if (this.cart.have_stockable_items) {
                        this.$emit('processing', 'shipping');
                    } else {
                        this.$emit('processing', 'payment');
                    }
                },
            }
        });
    </script>
@endPushOnce