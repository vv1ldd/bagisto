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
                <!-- Individual Profile Card -->
                <div 
                    class="relative border-4 transition-all duration-300 group cursor-pointer w-full max-w-[450px] mb-8" style="height: 80px;"
                    :class="[selectedOrgId === null ? 'border-zinc-900 bg-white shadow-[6px_6px_0px_0px_rgba(124,69,245,1)]' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                    @click="selectedOrgId = null; isB2B = false; $parent.$options.name == 'v-direct-checkout' && proceedWithUnifiedCard()"
                >
                    <div class="relative flex items-center justify-between h-full px-6">
                        <div class="min-w-0">
                            <p class="text-lg font-black uppercase tracking-widest transition-colors duration-300" :class="[selectedOrgId === null ? 'text-[#7C45F5]' : 'text-zinc-900']">
                                @{{ customerFullName }}
                            </p>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mt-1">
                                @{{ customerEmail }}
                            </p>
                        </div>

                        <div class="shrink-0 flex items-center gap-4">
                            <div 
                                class="flex h-8 w-8 items-center justify-center border-4 transition-all duration-300"
                                :class="[selectedOrgId === null ? 'border-zinc-900 bg-[#7C45F5]' : 'border-zinc-900 bg-white group-hover:bg-zinc-100']"
                            >
                                <span v-if="selectedOrgId === null" class="icon-checkmark text-white text-lg font-black"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizations Section -->
                <div v-if="organizations.length" class="mb-10 animate-[fadeIn_0.3s_ease-out]">
                    <div class="flex items-center justify-between mb-6 mt-10">
                        <h3 class="text-xl font-black uppercase tracking-[0.2em] text-zinc-900">
                            Ваши компании
                        </h3>
                    </div>

                    <!-- Organization List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div 
                            v-for="org in organizations"
                            :key="org.id"
                            class="relative border-4 transition-all duration-300 group cursor-pointer" style="height: 84px;"
                            :class="[selectedOrgId == org.id ? 'border-zinc-900 bg-white shadow-[6px_6px_0px_0px_rgba(124,69,245,1)]' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                            @click="selectedOrgId = org.id; isB2B = true; $parent.$options.name == 'v-direct-checkout' && proceedWithUnifiedCard()"
                        >
                            <div class="relative flex items-center justify-between h-full px-6">
                                <div class="min-w-0">
                                    <p class="text-sm font-black uppercase tracking-widest transition-colors duration-300" :class="[selectedOrgId == org.id ? 'text-[#7C45F5]' : 'text-zinc-900']">@{{ org.name }}</p>
                                    <div class="flex flex-wrap gap-3 mt-2">
                                        <span class="px-2 py-0.5 border-2 border-zinc-900 bg-zinc-100 text-[9px] font-black text-zinc-900 uppercase tracking-widest">ИНН @{{ org.inn }}</span>
                                        <span v-if="org.kpp" class="px-2 py-0.5 border-2 border-zinc-900 bg-zinc-100 text-[9px] font-black text-zinc-900 uppercase tracking-widest">КПП @{{ org.kpp }}</span>
                                    </div>
                                </div>

                                <div class="shrink-0 ml-4">
                                    <div 
                                        class="flex h-8 w-8 items-center justify-center border-4 transition-all duration-300"
                                        :class="[selectedOrgId == org.id ? 'border-zinc-900 bg-[#7C45F5]' : 'border-zinc-900 bg-white group-hover:bg-zinc-100']"
                                    >
                                        <span v-if="selectedOrgId == org.id" class="icon-checkmark text-white text-lg font-black"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Section (Physical Goods) -->
                <div v-if="cart.have_stockable_items" class="mb-10">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b-4 border-zinc-900">
                        <h3 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900">
                            @lang('shop::app.checkout.onepage.address.shipping-address')
                        </h3>

                        <button 
                            class="px-4 py-2 border-2 border-zinc-900 bg-white text-[11px] font-black uppercase tracking-widest text-[#7C45F5] hover:bg-zinc-50 transition-all shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-2px] hover:translate-y-0"
                            @click="activeAddressForm = 'shipping'; selectedAddressForEdit = null"
                        >
                            @lang('shop::app.checkout.onepage.address.add-new-address')
                        </button>
                    </div>

                    <!-- Shipping Address List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div 
                            v-for="address in customerSavedAddresses.shipping"
                            :key="address.id"
                            class="relative p-8 border-4 cursor-pointer transition-all duration-300 group"
                            :class="[selectedAddresses.shipping_address_id == address.id ? 'border-zinc-900 bg-white shadow-[8px_8px_0px_0px_rgba(124,69,245,1)]' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]']"
                            @click="selectedAddresses.shipping_address_id = address.id"
                        >
                            <div class="relative flex justify-between items-start mb-6">
                                <p class="text-lg font-black uppercase tracking-widest transition-colors duration-300" :class="[selectedAddresses.shipping_address_id == address.id ? 'text-[#7C45F5]' : 'text-zinc-900']">@{{ address.first_name }} @{{ address.last_name }}</p>
                                <div class="flex gap-4">
                                    <span 
                                        class="icon-edit text-3xl text-zinc-400 hover:text-[#7C45F5] transition-all cursor-pointer"
                                        @click.stop="selectedAddressForEdit = address; activeAddressForm = 'shipping'"
                                    ></span>
                                    <div 
                                        class="flex h-8 w-8 items-center justify-center border-4 transition-all duration-300"
                                        :class="[selectedAddresses.shipping_address_id == address.id ? 'border-zinc-900 bg-[#7C45F5]' : 'border-zinc-900 bg-white group-hover:bg-zinc-100']"
                                    >
                                        <span v-if="selectedAddresses.shipping_address_id == address.id" class="icon-checkmark text-white text-lg font-black"></span>
                                    </div>
                                </div>
                            </div>
                            <p class="relative text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 leading-loose border-t-2 border-zinc-100 pt-6">
                                @{{ address.address.join(', ') }}<br>
                                @{{ address.city }}, @{{ address.state }} @{{ address.postcode }}<br>
                                @{{ address.country }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Button — only for physical goods, auto-proceed for digital -->
                <div class="mt-12 flex justify-center" v-if="$parent.$options.name != 'v-direct-checkout' && cart.have_stockable_items">
                    <button
                        class="bg-[#7C45F5] border-4 border-zinc-900 px-24 py-6 text-[18px] font-black uppercase tracking-[0.2em] text-white shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                        @click="proceedWithUnifiedCard()"
                        :disabled="isStoring || (cart.have_stockable_items && !selectedAddresses.shipping_address_id)"
                    >
                        <span v-if="!isStoring">@lang('shop::app.checkout.onepage.address.proceed')</span>
                        <span v-else class="animate-spin h-6 w-6 border-4 border-white border-t-transparent rounded-full mx-auto"></span>
                    </button>
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
                        <div class="mb-10 flex items-center justify-between pb-6 border-b-4 border-zinc-900">
                            <h2 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900">
                                <template v-if="activeAddressForm == 'billing'">
                                    @lang('shop::app.checkout.onepage.address.billing-address')
                                </template>

                                <template v-else>
                                    @lang('shop::app.checkout.onepage.address.shipping-address')
                                </template>
                            </h2>

                            <span
                                class="flex cursor-pointer items-center gap-2 px-4 py-2 border-2 border-zinc-900 bg-white text-[11px] font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-50 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-2px] hover:translate-y-0 transition-all"
                                v-show="customerSavedAddresses.billing.length && ['billing', 'shipping'].includes(activeAddressForm) && cart.have_stockable_items"
                                @click="selectedAddressForEdit = null; activeAddressForm = null"
                            >
                                <span class="icon-arrow-left text-xl"></span>
                                @lang('shop::app.checkout.onepage.address.back')
                            </span>
                        </div>

                        <!-- Address Form Vue Component -->
                        <div class="mb-10">
                            <v-checkout-address-form
                                :control-name="activeAddressForm"
                                :address="selectedAddressForEdit || undefined"
                                :cart="cart"
                            ></v-checkout-address-form>
                        </div>

                        <!-- Save Address to Address Book Checkbox -->
                        <div class="mt-8 p-6 bg-zinc-50 border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] flex items-center gap-4 cursor-pointer select-none"
                            @click="saveAddress = ! saveAddress"
                            v-if="cart.have_stockable_items && $parent.$options.name != 'v-direct-checkout'">
                            
                            <div class="flex h-8 w-8 items-center justify-center border-4 border-zinc-900 transition-all shrink-0"
                                :class="[saveAddress ? 'bg-[#7C45F5]' : 'bg-white']">
                                <span v-if="saveAddress" class="icon-checkmark text-white font-black text-lg"></span>
                            </div>

                            <label class="cursor-pointer text-[12px] font-black uppercase tracking-widest text-zinc-900">
                                @lang('shop::app.checkout.onepage.address.save-address')
                            </label>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-14 pt-10 border-t-4 border-zinc-900 flex justify-end" v-if="$parent.$options.name != 'v-direct-checkout'">
                            <button
                                class="bg-zinc-900 border-4 border-zinc-900 px-16 py-6 text-[16px] font-black uppercase tracking-widest text-white shadow-[8px_8px_0px_0px_rgba(124,69,245,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50"
                                :disabled="isStoring"
                                type="submit"
                            >
                                <span v-if="!isStoring">
                                    @{{ cart.have_stockable_items ? '{{ trans('shop::app.checkout.onepage.address.save') }}' : '{{ trans('shop::app.checkout.onepage.address.proceed') }}' }}
                                </span>
                                <span v-else class="animate-spin h-6 w-6 border-4 border-white border-t-transparent rounded-full mx-auto"></span>
                            </button>
                        </div>
                    </form>
                </x-shop::form>
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

                    isB2B: false,

                    organizations: [],

                    selectedOrgId: null,
                }
            },

            created() {
                if (this.cart?.billing_address) {
                    this.useBillingAddressForShipping = this.cart.billing_address.use_for_shipping;
                }
            },

            computed: {
                firstName() {
                    return this.cart.billing_address?.first_name || '{{ auth()->guard('customer')->user()?->first_name ?? "" }}';
                },

                lastName() {
                    return this.cart.billing_address?.last_name || '{{ auth()->guard('customer')->user()?->last_name ?? "" }}';
                },

                customerFullName() {
                    return `${this.firstName} ${this.lastName}`.trim();
                },

                customerEmail() {
                    return this.cart.billing_address?.email || '{{ auth()->guard('customer')->user()?->email ?? "" }}';
                },

                customerPhone() {
                    return this.cart.billing_address?.phone || '{{ auth()->guard('customer')->user()?->phone ?? "" }}';
                },

                customerCountry() {
                    return this.cart.billing_address?.country || '{{ auth()->guard('customer')->user()?->country_of_residence ?? "" }}';
                },

            },

            mounted() {
                this.getCustomerSavedAddresses();

                this.getOrganizations();
            },

            methods: {
                getCustomerSavedAddresses() {
                    this.$axios.get('{{ route('shop.api.customers.account.addresses.index') }}')
                        .then(response => {
                            this.initializeAddresses('billing', structuredClone(response.data.data));

                            this.initializeAddresses('shipping', structuredClone(response.data.data));
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

                getOrganizations() {
                    this.$axios.get('{{ route('shop.api.customers.account.organizations.index') }}')
                        .then(response => {
                            this.organizations = response.data.data;

                            if (this.organizations.length) {
                                this.selectedOrgId = this.organizations[0].id;
                                this.isB2B = true;
                            }
                        })
                        .catch(error => {
                            console.error('Failed to fetch organizations:', error);
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
                                this.$emitter.emit('after-address-save', response.data.data.data);

                                if (this.cart.have_stockable_items) {
                                    this.$emit('processed', response.data.data.data);
                                } else {
                                    this.$emit('processed', response.data.data.data);
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
                    const selectedOrg = this.isB2B ? this.organizations.find(o => o.id == this.selectedOrgId) : null;

                    if (this.isB2B && !selectedOrg) {
                        this.$emitter.emit('add-flash', {
                            type: 'error',
                            message: 'Пожалуйста, выберите организацию'
                        });
                        return;
                    }

                    this.isStoring = true;

                    let payload = {
                        billing: {
                            first_name: this.isB2B ? selectedOrg.name : this.firstName,
                            last_name: this.isB2B ? '(B2B)' : this.lastName,
                            email: this.customerEmail,
                            company_name: this.isB2B ? selectedOrg.name : '',
                            vat_id: this.isB2B ? selectedOrg.inn : '',
                            country: this.customerCountry,
                            phone: this.customerPhone,
                            address: [this.isB2B ? `ИНН: ${selectedOrg.inn}${selectedOrg.kpp ? ', КПП: ' + selectedOrg.kpp : ''}` : 'Digital'],
                            city: this.isB2B ? (selectedOrg.address || 'B2B') : 'Digital',
                            state: this.isB2B ? 'B2B' : 'Digital',
                            postcode: '000000',
                            use_for_shipping: true
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

                        if (this.isB2B) {
                            payload.billing.company_name = selectedOrg.name;
                            payload.billing.vat_id = selectedOrg.inn;
                            payload.billing.address = [`ИНН: ${selectedOrg.inn}${selectedOrg.kpp ? ', КПП: ' + selectedOrg.kpp : ''}`, ...selectedShipping.address];
                        }

                        payload.shipping = selectedShipping;
                    }

                    this.moveToNextStep();

                    this.$axios.post('{{ route('shop.checkout.onepage.addresses.store') }}', payload)
                        .then((response) => {
                            this.isStoring = false;

                            if (response.data.data.redirect_url) {
                                window.location.href = response.data.data.redirect_url;
                            } else {
                                this.$emitter.emit('after-address-save', response.data.data.data);

                                if (this.cart.have_stockable_items) {
                                    this.$emit('processed', response.data.data.data);
                                } else {
                                    this.$emit('processed', response.data.data.data);
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