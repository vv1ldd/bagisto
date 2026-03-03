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
                                                                    <!-- B2B Toggle -->
                                                                    <div class="mb-6 flex justify-center">
                                                                        <div class="flex p-1 bg-zinc-100/80 rounded-2xl relative w-full max-w-[400px]">
                                                                            <div 
                                                                                class="absolute top-1 bottom-1 transition-all duration-300 bg-white rounded-xl shadow-sm border border-zinc-200"
                                                                                :style="{
                                                                                    left: isB2B ? 'calc(50% + 2px)' : '4px',
                                                                                    width: 'calc(50% - 6px)'
                                                                                }"
                                                                            ></div>

                                                                            <button 
                                                                                class="flex-1 py-3 text-sm font-medium transition-colors relative z-10"
                                                                                :class="[isB2B ? 'text-zinc-500' : 'text-navyBlue']"
                                                                                @click="isB2B = false"
                                                                            >
                                                                                Частное лицо
                                                                            </button>

                                                                            <button 
                                                                                class="flex-1 py-3 text-sm font-medium transition-colors relative z-10"
                                                                                :class="[isB2B ? 'text-navyBlue' : 'text-zinc-500']"
                                                                                @click="isB2B = true"
                                                                            >
                                                                                Юридическое лицо
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Profile Card (Individual Mode) -->
                                                                    <div v-if="! isB2B" class="mb-8">
                                                                        <div class="p-6 rounded-2xl border border-zinc-200 bg-white/50 backdrop-blur-sm shadow-sm relative overflow-hidden group">
                                                                            <div class="absolute inset-0 bg-gradient-to-br from-transparent to-zinc-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                                                            <div class="relative flex items-center justify-between">
                                                                                <div>
                                                                                    <p class="text-lg font-semibold text-navyBlue">
                                                                                        @{{ customerFullName }}
                                                                                    </p>
                                                                                        <p class="text-sm text-zinc-500 mt-1">
                                                                                            @{{ customerEmail }}
                                                                                        </p>
                                                                                        <p class="text-xs text-zinc-400 mt-2 flex items-center gap-1">
                                                                                            <span class="icon-checkout-address text-base"></span>
                                                                                            @{{ customerCountry }}
                                                                                            <template v-if="customerPhone">
                                                                                                • @{{ customerPhone }}
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

                                                                    <!-- Organizations Section (B2B Mode) -->
                                                                    <div v-if="isB2B" class="mb-8 animate-[fadeIn_0.3s_ease-out]">
                                                                        <div class="flex items-center justify-between mb-4">
                                                                            <h3 class="text-xl font-medium text-navyBlue">
                                                                                Ваши компании
                                                                            </h3>

                                                                            <button 
                                                                                class="text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors flex items-center gap-1"
                                                                                @click="isAddingOrganization = true"
                                                                            >
                                                                                <span class="icon-plus text-lg"></span>
                                                                                Добавить компанию
                                                                            </button>
                                                                        </div>

                                                                        <!-- Organization List -->
                                                                        <div v-if="organizations.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                            <div 
                                                                                v-for="org in organizations"
                                                                                :key="org.id"
                                                                                class="relative p-6 rounded-2xl border transition-all duration-300 group cursor-pointer overflow-hidden"
                                                                                :class="[selectedOrgId == org.id ? 'border-purple-500 bg-purple-50/10 ring-1 ring-purple-500 shadow-md' : 'border-zinc-200 hover:border-zinc-300 bg-white/50 shadow-sm']"
                                                                                @click="selectedOrgId = org.id"
                                                                            >
                                                                                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-purple-50/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                                                                <div class="relative flex items-center justify-between">
                                                                                    <div class="min-w-0">
                                                                                        <p class="font-semibold transition-colors duration-300" :class="[selectedOrgId == org.id ? 'text-purple-700' : 'text-navyBlue']">@{{ org.name }}</p>
                                                                                        <div class="flex flex-wrap gap-2 mt-2">
                                                                                            <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">ИНН @{{ org.inn }}</span>
                                                                                            <span v-if="org.kpp" class="px-2 py-0.5 rounded-md bg-zinc-100 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">КПП @{{ org.kpp }}</span>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="shrink-0 ml-4">
                                                                                        <div 
                                                                                            class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                                                                            :class="[selectedOrgId == org.id ? 'border-purple-600 bg-purple-600 scale-110' : 'border-zinc-300 group-hover:border-zinc-400']"
                                                                                        >
                                                                                            <div v-if="selectedOrgId == org.id" class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <p v-if="org.address" class="relative mt-4 text-xs text-zinc-400 truncate border-t border-zinc-100 pt-3">
                                                                                    <span class="icon-checkout-address mr-1"></span>
                                                                                    @{{ org.address }}
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Empty State -->
                                                                        <div v-else class="p-8 rounded-2xl border border-dashed border-zinc-200 flex flex-col items-center justify-center text-center bg-zinc-50/50">
                                                                            <div class="w-16 h-16 rounded-full bg-zinc-100 flex items-center justify-center mb-4 text-zinc-400">
                                                                                <span class="icon-checkout-address text-3xl"></span>
                                                                            </div>
                                                                            <p class="text-zinc-500 max-w-[300px]">У вас пока не добавлено ни одной организации.</p>
                                                                            <button 
                                                                                class="mt-4 text-purple-600 font-medium hover:text-purple-700"
                                                                                @click="isAddingOrganization = true"
                                                                            >
                                                                                + Добавить первую компанию
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Add Organization Form -->
                                                                    <div v-if="isAddingOrganization" class="mb-8 p-6 rounded-2xl border border-zinc-200 bg-white shadow-sm animate-[slideDown_0.3s_ease-out]">
                                                                        <div class="flex items-center justify-between mb-6">
                                                                            <h3 class="text-xl font-medium text-navyBlue">Новая компания</h3>
                                                                            <button @click="isAddingOrganization = false" class="text-zinc-400 hover:text-zinc-600">
                                                                                <span class="icon-dismiss text-2xl"></span>
                                                                            </button>
                                                                        </div>

                                                                        <div class="space-y-4">
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-zinc-700 mb-1">Название организации *</label>
                                                                                    <input v-model="newOrganization.name" type="text" class="w-full p-3 rounded-xl border border-zinc-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all" placeholder="ООО 'Ромашка'">
                                                                                </div>
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-zinc-700 mb-1">ИНН *</label>
                                                                                    <input v-model="newOrganization.inn" type="text" class="w-full p-3 rounded-xl border border-zinc-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all" placeholder="7701234567">
                                                                                </div>
                                                                            </div>

                                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-zinc-700 mb-1">КПП (необязательно)</label>
                                                                                    <input v-model="newOrganization.kpp" type="text" class="w-full p-3 rounded-xl border border-zinc-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all" placeholder="770101001">
                                                                                </div>
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-zinc-700 mb-1">Юридический адрес</label>
                                                                                    <input v-model="newOrganization.address" type="text" class="w-full p-3 rounded-xl border border-zinc-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all" placeholder="г. Москва, ул. Ленина, д. 1">
                                                                                </div>
                                                                            </div>

                                                                            <div class="flex justify-end gap-3 mt-6">
                                                                                <button 
                                                                                    @click="isAddingOrganization = false"
                                                                                    class="px-6 py-2 rounded-xl text-zinc-600 hover:bg-zinc-100 transition-colors"
                                                                                >
                                                                                    Отмена
                                                                                </button>
                                                                                <button 
                                                                                    @click="createOrganization"
                                                                                    class="px-8 py-2 rounded-xl bg-navyBlue text-white hover:bg-navyBlue/90 transition-all shadow-md active:scale-95 disabled:opacity-50"
                                                                                    :disabled="isStoring"
                                                                                >
                                                                                    <template v-if="isStoring">Сохранение...</template>
                                                                                    <template v-else>Сохранить</template>
                                                                                </button>
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

                    isB2B: false,

                    organizations: [],

                    selectedOrgId: null,

                    isAddingOrganization: false,

                    newOrganization: {
                        name: '',
                        inn: '',
                        kpp: '',
                        address: '',
                    },
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

                createOrganization() {
                    if (!this.newOrganization.name || !this.newOrganization.inn) {
                        this.$emitter.emit('add-flash', {
                            type: 'error',
                            message: 'Пожалуйста, заполните название и ИНН'
                        });
                        return;
                    }

                    this.isStoring = true;

                    this.$axios.post('{{ route('shop.api.customers.account.organizations.store') }}', this.newOrganization)
                        .then(response => {
                            this.organizations.push(response.data.data);
                            this.selectedOrgId = response.data.data.id;
                            this.isAddingOrganization = false;
                            this.newOrganization = { name: '', inn: '', kpp: '', address: '' };

                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message
                            });
                        })
                        .catch(error => {
                            console.error('Failed to create organization:', error);
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: 'Ошибка при сохранении организации'
                            });
                        })
                        .finally(() => {
                            this.isStoring = false;
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