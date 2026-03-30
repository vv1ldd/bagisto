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
                    <!-- Section Title -->
                    <h2 class="text-2xl font-black uppercase tracking-[0.2em] mb-10 text-zinc-900 border-b-4 border-zinc-900 pb-4 inline-block">
                        @lang('shop::app.checkout.onepage.address.title')
                    </h2>

                    <!-- Individual Profile / Unified Card -->
                    <div 
                        class="relative border-4 transition-all duration-300 group cursor-pointer w-full max-w-[500px] mb-10"
                        :class="[selectedOrgId === null ? 'border-zinc-900 bg-white shadow-[8px_8px_0px_0px_rgba(124,69,245,1)] translate-x-1 translate-y-1 shadow-none' : 'border-zinc-900 bg-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-[8px_8px_0px_0px_rgba(24,24,27,1)]']"
                        @click="selectedOrgId = null; isB2B = false; selectProfile()"
                    >
                        <div class="relative flex items-center justify-between p-8">
                            <div class="min-w-0">
                                <p class="text-xl font-black uppercase tracking-widest transition-colors duration-300" :class="[selectedOrgId === null ? 'text-[#7C45F5]' : 'text-zinc-900']">
                                    @{{ customerFullName }}
                                </p>
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 mt-2 opacity-60">
                                    Персональный профиль (@{{ customerEmail }})
                                </p>
                            </div>

                            <div 
                                class="flex h-10 w-10 items-center justify-center border-4 transition-all duration-300"
                                :class="[selectedOrgId === null ? 'border-zinc-900 bg-[#7C45F5]' : 'border-zinc-900 bg-white shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]']"
                            >
                                <span v-if="selectedOrgId === null" class="icon-checkmark text-white text-xl font-black"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Organizations Section -->
                    <div v-if="organizations.length" class="mb-14">
                        <div class="flex items-center gap-4 mb-8">
                            <h3 class="text-lg font-black uppercase tracking-[0.15em] text-zinc-900">Выбрать компанию</h3>
                            <div class="flex-1 h-1 bg-zinc-900 opacity-10"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div 
                                v-for="org in organizations"
                                :key="org.id"
                                class="relative border-4 transition-all duration-300 group cursor-pointer"
                                :class="[selectedOrgId == org.id ? 'border-zinc-900 bg-white shadow-none translate-x-1 translate-y-1' : 'border-zinc-900 bg-white shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-[10px_10px_0px_0px_rgba(24,24,27,1)]']"
                                @click="selectedOrgId = org.id; isB2B = true; selectProfile()"
                            >
                                <div 
                                    class="absolute -top-4 -right-4 px-3 py-1 bg-[#7C45F5] border-2 border-zinc-900 text-white text-[9px] font-black uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]"
                                    v-if="selectedOrgId == org.id"
                                >
                                    Выбрано
                                </div>

                                <div class="p-8">
                                    <p class="text-[15px] font-black uppercase tracking-widest text-zinc-900 mb-4">@{{ org.name }}</p>
                                    <div class="space-y-2">
                                        <div class="inline-flex px-3 py-1 bg-zinc-100 border-2 border-zinc-900 text-[10px] font-black text-zinc-900 uppercase">ИНН @{{ org.inn }}</div>
                                        <div v-if="org.kpp" class="inline-flex px-3 py-1 bg-zinc-100 border-2 border-zinc-900 text-[10px] font-black text-zinc-900 uppercase ml-2">КПП @{{ org.kpp }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Addresses (Physical Goods Only) -->
                    <div v-if="cart.have_stockable_items" class="mt-14 pt-14 border-t-4 border-zinc-900">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-2xl font-black uppercase tracking-[0.2em] text-zinc-900">@lang('shop::app.checkout.onepage.address.shipping-address')</h3>
                            <button 
                                class="px-6 py-3 border-4 border-zinc-900 bg-white text-[11px] font-black uppercase tracking-widest text-[#7C45F5] shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-2px] hover:translate-y-1 hover:translate-x-1 transition-all"
                                @click="activeAddressForm = 'shipping'; selectedAddressForEdit = null"
                            >
                                + @lang('shop::app.checkout.onepage.address.add-new-address')
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div 
                                v-for="address in customerSavedAddresses.shipping"
                                :key="address.id"
                                class="relative p-10 border-4 cursor-pointer transition-all duration-300 group"
                                :class="[selectedAddresses.shipping_address_id == address.id ? 'border-zinc-900 bg-white ring-8 ring-[#7C45F5]/10 shadow-none translate-x-1 translate-y-1' : 'border-zinc-900 bg-white shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] hover:shadow-[12px_12px_0px_0px_rgba(24,24,27,1)]']"
                                @click="selectedAddresses.shipping_address_id = address.id"
                            >
                                <div class="flex justify-between items-start mb-8 pb-6 border-b-2 border-zinc-100">
                                    <p class="text-lg font-black uppercase tracking-widest text-[#7C45F5]">@{{ address.first_name }} @{{ address.last_name }}</p>
                                    <span class="icon-edit text-2xl text-zinc-400 hover:text-zinc-900 transition-colors" @click.stop="selectedAddressForEdit = address; activeAddressForm = 'shipping'"></span>
                                </div>
                                <p class="text-[12px] font-black uppercase tracking-[0.15em] text-zinc-500 leading-relaxed">
                                    @{{ address.address.join(', ') }}<br>
                                    @{{ address.city }}, @{{ address.state }} @{{ address.postcode }}<br>
                                    @{{ address.country }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Unified Proceed Button -->
                    <div class="mt-16 flex justify-start">
                        <button
                            class="bg-[#7C45F5] border-4 border-zinc-900 px-20 py-7 text-[18px] font-black uppercase tracking-widest text-white shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] hover:shadow-none hover:translate-x-1.5 hover:translate-y-1.5 transition-all disabled:opacity-50"
                            @click="proceedWithUnifiedCard()"
                            :disabled="isStoring || (cart.have_stockable_items && !selectedAddresses.shipping_address_id)"
                        >
                            <template v-if="!isStoring">@lang('shop::app.checkout.onepage.address.proceed')</template>
                            <svg v-else class="animate-spin h-7 w-7 text-white mx-auto" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form for New/Edit Address -->
                <template v-else>
                    <div class="animate-in slide-in-from-right duration-300">
                        <div class="flex items-center justify-between mb-12 pb-6 border-b-4 border-zinc-900">
                            <h2 class="text-2xl font-black uppercase tracking-widest text-zinc-900">
                                @{{ activeAddressForm == 'billing' ? '@lang('shop::app.checkout.onepage.address.billing-address')' : '@lang('shop::app.checkout.onepage.address.shipping-address')' }}
                            </h2>
                            <button @click="activeAddressForm = null" class="text-[11px] font-black uppercase bg-zinc-100 border-2 border-zinc-900 px-4 py-2 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] hover:shadow-none translate-y-[-2px] hover:translate-y-0 transition-all">
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
                selectProfile() {
                    // Logic to update UI or internal state
                },
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