{!! view_render_event('bagisto.shop.checkout.onepage.address.customer.before') !!}

<v-checkout-address-customer :cart="cart" @processing="stepForward" @processed="stepProcessed">
    <x-shop::shimmer.checkout.onepage.address />
</v-checkout-address-customer>

{!! view_render_event('bagisto.shop.checkout.onepage.address.customer.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-checkout-address-customer-template">
        <div class="animate-in fade-in duration-500">
            <template v-if="isLoading">
                <x-shop::shimmer.checkout.onepage.address />
            </template>

            <template v-else>
                <div v-if="! activeAddressForm">
                    <!-- Compact Section Title -->
                    <h2 class="text-xl font-black uppercase tracking-[0.15em] mb-8 text-zinc-900 border-b-[3px] border-zinc-900 pb-3 inline-block">
                        @lang('shop::app.checkout.onepage.address.title')
                    </h2>

                    <!-- Compact Profile Selection Card -->
                    <div 
                        class="relative border-[3px] transition-all duration-300 group cursor-pointer w-full max-w-[420px] mb-8"
                        :class="[selectedOrgId === null ? 'border-zinc-900 bg-white shadow-none translate-x-0.5 translate-y-0.5' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                        @click="selectedOrgId = null; isB2B = false; selectProfile()"
                    >
                        <div class="relative flex items-center justify-between p-6">
                            <div class="min-w-0">
                                <p class="text-lg font-black uppercase tracking-widest transition-colors duration-300" :class="[selectedOrgId === null ? 'text-[#7C45F5]' : 'text-zinc-900']">
                                    @{{ customerFullName }}
                                </p>
                                <p class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-500 mt-1 opacity-60">
                                    Личный профиль
                                </p>
                            </div>

                            <div 
                                class="flex h-8 w-8 items-center justify-center border-[3px] transition-all duration-300"
                                :class="[selectedOrgId === null ? 'border-zinc-900 bg-[#7C45F5]' : 'border-zinc-900 bg-white shadow-[2px_2px_0px_0px_rgba(24,24,27,1)]']"
                            >
                                <span v-if="selectedOrgId === null" class="icon-checkmark text-white text-lg font-black"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Organizations Section -->
                    <div v-if="organizations.length" class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <h3 class="text-[14px] font-black uppercase tracking-[0.1em] text-zinc-900">Выбрать компанию</h3>
                            <div class="flex-1 h-0.5 bg-zinc-900 opacity-10"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div 
                                v-for="org in organizations"
                                :key="org.id"
                                class="relative border-[3px] transition-all duration-300 group cursor-pointer"
                                :class="[selectedOrgId == org.id ? 'border-zinc-900 bg-white shadow-none translate-x-0.5 translate-y-0.5' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                                @click="selectedOrgId = org.id; isB2B = true; selectProfile()"
                            >
                                <div class="p-6">
                                    <p class="text-[13px] font-black uppercase tracking-widest text-zinc-900 mb-2 truncate">@{{ org.name }}</p>
                                    <div class="flex gap-2">
                                        <div class="inline-flex px-2 py-0.5 bg-zinc-100 border border-zinc-900 text-[8px] font-black text-zinc-900 uppercase">ИНН @{{ org.inn }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Addresses (Physical Goods Only) -->
                    <div v-if="cart.have_stockable_items" class="mt-8 pt-8 border-t-[3px] border-zinc-900">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black uppercase tracking-[0.15em] text-zinc-900">Адрес доставки</h3>
                            <button 
                                class="px-4 py-2 border-[3px] border-zinc-900 bg-white text-[10px] font-black uppercase tracking-widest text-[#7C45F5] shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-1px] hover:translate-y-0.5 hover:translate-x-0.5 transition-all"
                                @click="activeAddressForm = 'shipping'; selectedAddressForEdit = null"
                            >
                                + Добавить новый
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div 
                                v-for="address in customerSavedAddresses.shipping"
                                :key="address.id"
                                class="relative p-6 border-[3px] cursor-pointer transition-all duration-300 group"
                                :class="[selectedAddresses.shipping_address_id == address.id ? 'border-zinc-900 bg-white ring-4 ring-[#7C45F5]/10 shadow-none translate-x-0.5 translate-y-0.5' : 'border-zinc-900 bg-white shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)]']"
                                @click="selectedAddresses.shipping_address_id = address.id"
                            >
                                <div class="flex justify-between items-start mb-4 pb-4 border-b border-zinc-100">
                                    <p class="text-[14px] font-black uppercase tracking-widest text-[#7C45F5]">@{{ address.first_name }} @{{ address.last_name }}</p>
                                    <span class="icon-edit text-xl text-zinc-400 hover:text-zinc-900 transition-colors" @click.stop="selectedAddressForEdit = address; activeAddressForm = 'shipping'"></span>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.1em] text-zinc-500 leading-normal">
                                    @{{ address.address.join(', ') }}<br>
                                    @{{ address.city }}, @{{ address.postcode }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Unified Proceed Button -->
                    <div class="mt-10 flex justify-start">
                        <button
                            class="bg-[#7C45F5] border-[3px] border-zinc-900 px-12 py-5 text-[15px] font-black uppercase tracking-widest text-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition-all disabled:opacity-50"
                            @click="proceedWithUnifiedCard()"
                            :disabled="isStoring || (cart.have_stockable_items && !selectedAddresses.shipping_address_id)"
                        >
                            <template v-if="!isStoring">@lang('shop::app.checkout.onepage.address.proceed')</template>
                            <svg v-else class="animate-spin h-6 w-6 text-white mx-auto" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form for New/Edit Address -->
                <template v-else>
                    <div class="animate-in slide-in-from-right duration-300">
                        <div class="flex items-center justify-between mb-8 pb-4 border-b-[3px] border-zinc-900">
                            <h2 class="text-xl font-black uppercase tracking-widest text-zinc-900">
                                @{{ activeAddressForm == 'billing' ? '@lang('shop::app.checkout.onepage.address.billing-address')' : '@lang('shop::app.checkout.onepage.address.shipping-address')' }}
                            </h2>
                            <button @click="activeAddressForm = null" class="text-[9px] font-black uppercase bg-zinc-100 border border-zinc-900 px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-1px] hover:translate-y-0 transition-all">
                                Назад
                            </button>
                        </div>
                        
                        <v-checkout-address-form
                            :control-name="activeAddressForm"
                            :address="selectedAddressForEdit || undefined"
                            :cart="cart"
                            @processed="onAddressProcessed"
                        ></v-checkout-address-form>
                    </div>
                </template>
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-checkout-address-customer', {
            template: '#v-checkout-address-customer-template',
            props: ['cart'],
            emits: ['processing', 'processed'],
            data() {
                return {
                    customerSavedAddresses: { 'billing': [], 'shipping': [] },
                    activeAddressForm: null,
                    selectedAddressForEdit: null,
                    selectedAddresses: { billing_address_id: null, shipping_address_id: null },
                    isLoading: true,
                    isStoring: false,
                    isB2B: false,
                    organizations: [],
                    selectedOrgId: null,
                }
            },
            computed: {
                firstName() { return this.cart.billing_address?.first_name || '{{ auth()->user()?->first_name }}'; },
                lastName() { return this.cart.billing_address?.last_name || '{{ auth()->user()?->last_name }}'; },
                customerFullName() { return `${this.firstName} ${this.lastName}`.trim(); },
                customerEmail() { return '{{ auth()->user()?->email }}'; },
            },
            mounted() {
                this.getCustomerSavedAddresses();
                this.getOrganizations();
            },
            methods: {
                getCustomerSavedAddresses() {
                    this.$axios.get('{{ route('shop.api.customers.account.addresses.index') }}')
                        .then(response => {
                            this.customerSavedAddresses.shipping = response.data.data;
                            this.customerSavedAddresses.billing = response.data.data;
                            this.isLoading = false;
                        }).catch(() => this.isLoading = false);
                },
                getOrganizations() {
                    this.$axios.get('{{ route('shop.api.customers.account.organizations.index') }}')
                        .then(response => {
                            this.organizations = response.data.data;
                        });
                },
                selectProfile() { },
                proceedWithUnifiedCard() {
                    const org = this.isB2B ? this.organizations.find(o => o.id == this.selectedOrgId) : null;
                    if (this.isB2B && !org) {
                        this.$emitter.emit('add-flash', { type: 'error', message: 'Выберите компанию' });
                        return;
                    }

                    this.isStoring = true;
                    let payload = {
                        billing: {
                            first_name: this.isB2B ? org.name : this.firstName,
                            last_name: this.isB2B ? '(B2B)' : this.lastName,
                            email: this.customerEmail,
                            company_name: this.isB2B ? org.name : '',
                            vat_id: this.isB2B ? org.inn : '',
                            country: 'RU',
                            phone: '79999999999',
                            address: [this.isB2B ? `ИНН: ${org.inn}` : 'Digital'],
                            city: 'Moscow',
                            state: 'MSK',
                            postcode: '000000',
                            use_for_shipping: true
                        }
                    };

                    if (this.cart.have_stockable_items) {
                        const addr = this.customerSavedAddresses.shipping.find(a => a.id == this.selectedAddresses.shipping_address_id);
                        payload.shipping = payload.billing = { ...addr, use_for_shipping: true };
                    }

                    this.$axios.post('{{ route('shop.checkout.onepage.addresses.store') }}', payload)
                        .then(response => {
                            this.isStoring = false;
                            this.$emit('processed', response.data.data);
                            this.$emit('processing', this.cart.have_stockable_items ? 'shipping' : 'payment');
                        }).catch(() => this.isStoring = false);
                }
            }
        });
    </script>
@endPushOnce