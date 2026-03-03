@pushOnce('scripts')
    <script type="text/x-template" id="v-checkout-address-form-template">
                                    <div class="mt-2 max-md:mt-3">
                                        <x-shop::form.control-group class="hidden">
                                            <x-shop::form.control-group.control
                                                type="text"
                                                ::name="controlName + '.id'"
                                                ::value="address.id"
                                            />
                                        </x-shop::form.control-group>


                                        <!-- First Name -->
                                        <div class="grid grid-cols-2 gap-x-5 max-md:grid-cols-1">
                                            <x-shop::form.control-group>
                                                <x-shop::form.control-group.label class="required !mt-0">
                                                    @lang('shop::app.checkout.onepage.address.first-name')
                                                </x-shop::form.control-group.label>

                                                <x-shop::form.control-group.control
                                                    type="text"
                                                    ::name="controlName + '.first_name'"
                                                    ::value="address.first_name"
                                                    rules="required"
                                                    :label="trans('shop::app.checkout.onepage.address.first-name')"
                                                    :placeholder="trans('shop::app.checkout.onepage.address.first-name')"
                                                    class="!rounded-[5px]"
                                                />

                                                <x-shop::form.control-group.error ::name="controlName + '.first_name'" />
                                            </x-shop::form.control-group>

                                            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.first_name.after') !!}

                                            <!-- Last Name -->
                                            <x-shop::form.control-group>
                                                <x-shop::form.control-group.label class="required !mt-0">
                                                    @lang('shop::app.checkout.onepage.address.last-name')
                                                </x-shop::form.control-group.label>

                                                <x-shop::form.control-group.control
                                                    type="text"
                                                    ::name="controlName + '.last_name'"
                                                    ::value="address.last_name"
                                                    rules="required"
                                                    :label="trans('shop::app.checkout.onepage.address.last-name')"
                                                    :placeholder="trans('shop::app.checkout.onepage.address.last-name')"
                                                    class="!rounded-[5px]"
                                                />

                                                <x-shop::form.control-group.error ::name="controlName + '.last_name'" />
                                            </x-shop::form.control-group>

                                            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.last_name.after') !!}
                                        </div>

                                        <!-- Email -->
                                        <x-shop::form.control-group>
                                            <x-shop::form.control-group.label class="required !mt-0">
                                                @lang('shop::app.checkout.onepage.address.email')
                                            </x-shop::form.control-group.label>

                                            <x-shop::form.control-group.control
                                                type="email"
                                                ::name="controlName + '.email'"
                                                ::value="address.email"
                                                rules="required|email"
                                                :label="trans('shop::app.checkout.onepage.address.email')"
                                                placeholder="email@example.com"
                                                class="!rounded-[5px]"
                                            />

                                            <x-shop::form.control-group.error ::name="controlName + '.email'" />
                                        </x-shop::form.control-group>

                                        {!! view_render_event('bagisto.shop.checkout.onepage.address.form.email.after') !!}

                                        <!-- Vat ID -->
                                        <template v-if="controlName == 'billing' && (! isVirtualOnly || address.vat_id)">
                                            <x-shop::form.control-group>
                                                <x-shop::form.control-group.label>
                                                    @lang('shop::app.checkout.onepage.address.vat-id')
                                                </x-shop::form.control-group.label>

                                                <x-shop::form.control-group.control
                                                    type="text"
                                                    ::name="controlName + '.vat_id'"
                                                    ::value="address.vat_id"
                                                    :label="trans('shop::app.checkout.onepage.address.vat-id')"
                                                    :placeholder="trans('shop::app.checkout.onepage.address.vat-id')"
                                                    class="!rounded-[5px]"
                                                />
                                                <x-shop::form.control-group.error ::name="controlName + '.vat_id'" />
                                            </x-shop::form.control-group>
                                            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.vat_id.after') !!}
                                        </template>

                                        <!-- Buy as a Gift -->
                                        <template v-if="controlName == 'billing' && isVirtualOnly">
                                            <div class="mb-4">
                                                <x-shop::form.control-group class="!mb-0 flex items-center gap-2.5">
                                                    <x-shop::form.control-group.control
                                                        type="checkbox"
                                                        ::name="controlName + '.is_gift'"
                                                        id="is_gift"
                                                        for="is_gift"
                                                        value="1"
                                                        v-model="isGift"
                                                    />
                                                    <label class="cursor-pointer select-none text-base text-zinc-500 max-md:text-sm max-sm:text-xs" for="is_gift">
                                                        @lang('shop::app.checkout.onepage.address.is-gift')
                                                    </label>
                                                </x-shop::form.control-group>

                                                <div v-if="isGift" class="mt-4">
                                                    <x-shop::form.control-group>
                                                        <x-shop::form.control-group.label class="required">
                                                            @lang('shop::app.checkout.onepage.address.gift-email')
                                                        </x-shop::form.control-group.label>
                                                        <x-shop::form.control-group.control
                                                            type="email"
                                                            ::name="controlName + '.gift_email'"
                                                            v-model="giftEmail"
                                                            rules="required|email"
                                                            :label="trans('shop::app.checkout.onepage.address.gift-email')"
                                                            placeholder="recipient@example.com"
                                                            class="!rounded-[5px]"
                                                        />
                                                        <x-shop::form.control-group.error ::name="controlName + '.gift_email'" />
                                                    </x-shop::form.control-group>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Street Address -->
                                        <template v-if="! isVirtualOnly || controlName != 'billing'">
                                            <x-shop::form.control-group>
                                                <x-shop::form.control-group.label class="required !mt-0">
                                                    @lang('shop::app.checkout.onepage.address.street-address')
                                                </x-shop::form.control-group.label>

                                                <x-shop::form.control-group.control
                                                    type="text"
                                                    ::name="controlName + '.address.[0]'"
                                                    ::value="address.address[0]"
                                                    rules="required|address"
                                                    :label="trans('shop::app.checkout.onepage.address.street-address')"
                                                    :placeholder="trans('shop::app.checkout.onepage.address.street-address')"
                                                    class="!rounded-[5px]"
                                                />
                                                <x-shop::form.control-group.error class="mb-2" ::name="controlName + '.address.[0]'" />

                                                @if (core()->getConfigData('customer.address.information.street_lines') > 1)
                                                    @for ($i = 1; $i < core()->getConfigData('customer.address.information.street_lines'); $i++)
                                                        <x-shop::form.control-group.control
                                                            type="text"
                                                            ::name="controlName + '.address.[{{ $i }}]'"
                                                            rules="address"
                                                            :label="trans('shop::app.checkout.onepage.address.street-address')"
                                                            :placeholder="trans('shop::app.checkout.onepage.address.street-address')"
                                                            class="!rounded-[5px]"
                                                        />
                                                        <x-shop::form.control-group.error class="mb-2" ::name="controlName + '.address.[{{ $i }}]'" />
                                                    @endfor
                                                @endif
                                            </x-shop::form.control-group>
                                            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.address.after') !!}

                                            <div class="grid grid-cols-2 gap-x-5 max-md:grid-cols-1">
                                                <!-- Country -->
                                                <x-shop::form.control-group class="!mb-4">
                                                    <x-shop::form.control-group.label class="{{ core()->isCountryRequired() ? 'required' : '' }} !mt-0">
                                                        @lang('shop::app.checkout.onepage.address.country')
                                                    </x-shop::form.control-group.label>

                                                    <x-shop::form.control-group.control
                                                        type="select"
                                                        ::name="controlName + '.country'"
                                                        ::value="address.country"
                                                        v-model="selectedCountry"
                                                        rules="{{ core()->isCountryRequired() ? 'required' : '' }}"
                                                        :label="trans('shop::app.checkout.onepage.address.country')"
                                                        :placeholder="trans('shop::app.checkout.onepage.address.country')"
                                                        class="!rounded-[5px]"
                                                    >
                                                        <option value="">@lang('shop::app.checkout.onepage.address.select-country')</option>
                                                        <option v-for="country in countries" :value="country.code">@{{ country.name }}</option>
                                                    </x-shop::form.control-group.control>
                                                    <x-shop::form.control-group.error ::name="controlName + '.country'" />
                                                </x-shop::form.control-group>
                                                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.country.after') !!}

                                                <!-- State -->
                                                <x-shop::form.control-group>
                                                    <x-shop::form.control-group.label class="{{ core()->isStateRequired() ? 'required' : '' }} !mt-0">
                                                        @lang('shop::app.checkout.onepage.address.state')
                                                    </x-shop::form.control-group.label>

                                                    <template v-if="states">
                                                        <template v-if="haveStates">
                                                            <x-shop::form.control-group.control
                                                                type="select"
                                                                ::name="controlName + '.state'"
                                                                rules="{{ core()->isStateRequired() ? 'required' : '' }}"
                                                                ::value="address.state"
                                                                :label="trans('shop::app.checkout.onepage.address.state')"
                                                                :placeholder="trans('shop::app.checkout.onepage.address.state')"
                                                                class="!rounded-[5px]"
                                                            >
                                                                <option value="">@lang('shop::app.checkout.onepage.address.select-state')</option>
                                                                <option v-for='(state, index) in states[selectedCountry]' :value="state.code">@{{ state.default_name }}</option>
                                                            </x-shop::form.control-group.control>
                                                        </template>

                                                        <template v-else>
                                                            <x-shop::form.control-group.control
                                                                type="text"
                                                                ::name="controlName + '.state'"
                                                                ::value="address.state"
                                                                rules="{{ core()->isStateRequired() ? 'required' : '' }}"
                                                                :label="trans('shop::app.checkout.onepage.address.state')"
                                                                :placeholder="trans('shop::app.checkout.onepage.address.state')"
                                                                class="!rounded-[5px]"
                                                            />
                                                        </template>
                                                    </template>
                                                    <x-shop::form.control-group.error ::name="controlName + '.state'" />
                                                </x-shop::form.control-group>
                                                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.state.after') !!}
                                            </div>

                                            <div class="grid grid-cols-2 gap-x-5 max-md:grid-cols-1">
                                                <!-- City -->
                                                <x-shop::form.control-group>
                                                    <x-shop::form.control-group.label class="required !mt-0">
                                                        @lang('shop::app.checkout.onepage.address.city')
                                                    </x-shop::form.control-group.label>

                                                    <x-shop::form.control-group.control
                                                        type="text"
                                                        ::name="controlName + '.city'"
                                                        ::value="address.city"
                                                        rules="required"
                                                        :label="trans('shop::app.checkout.onepage.address.city')"
                                                        :placeholder="trans('shop::app.checkout.onepage.address.city')"
                                                        class="!rounded-[5px]"
                                                    />
                                                    <x-shop::form.control-group.error ::name="controlName + '.city'" />
                                                </x-shop::form.control-group>
                                                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.city.after') !!}

                                                <!-- Postcode -->
                                                <x-shop::form.control-group>
                                                    <x-shop::form.control-group.label class="{{ core()->isPostCodeRequired() ? 'required' : '' }} !mt-0">
                                                        @lang('shop::app.checkout.onepage.address.postcode')
                                                    </x-shop::form.control-group.label>

                                                    <x-shop::form.control-group.control
                                                        type="text"
                                                        ::name="controlName + '.postcode'"
                                                        ::value="address.postcode"
                                                        rules="{{ core()->isPostCodeRequired() ? 'required' : '' }}|postcode"
                                                        :label="trans('shop::app.checkout.onepage.address.postcode')"
                                                        :placeholder="trans('shop::app.checkout.onepage.address.postcode')"
                                                        class="!rounded-[5px]"
                                                    />
                                                    <x-shop::form.control-group.error ::name="controlName + '.postcode'" />
                                                </x-shop::form.control-group>
                                                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.postcode.after') !!}
                                            </div>

                                            <!-- Phone Number -->
                                            <x-shop::form.control-group>
                                                <x-shop::form.control-group.label class="required !mt-0">
                                                    @lang('shop::app.checkout.onepage.address.telephone')
                                                </x-shop::form.control-group.label>

                                                <x-shop::form.control-group.control
                                                    type="text"
                                                    ::name="controlName + '.phone'"
                                                    ::value="address.phone"
                                                    rules="required|phone"
                                                    :label="trans('shop::app.checkout.onepage.address.telephone')"
                                                    :placeholder="trans('shop::app.checkout.onepage.address.telephone')"
                                                    class="!rounded-[5px]"
                                                />
                                                <x-shop::form.control-group.error ::name="controlName + '.phone'" />
                                            </x-shop::form.control-group>
                                            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.phone.after') !!}
                                        </template>
                                    </div>
                                </script>

    <script type="module">
        app.component('v-checkout-address-form', {
            template: '#v-checkout-address-form-template',

            props: {
                controlName: {
                    type: String,
                    required: true,
                },

                cart: {
                    type: Object,
                    required: true,
                },

                address: {
                    type: Object,
                    default: () => ({
                        id: 0,
                        company_name: '',
                        first_name: @json(auth()->guard('customer')->user()?->first_name ?? ''),
                        last_name: @json(auth()->guard('customer')->user()?->last_name ?? ''),
                        email: @json(auth()->guard('customer')->user()?->email ?? ''),
                        address: [],
                        country: @json(auth()->guard('customer')->user()?->country_of_residence ?? ''),
                        state: '',
                        city: '',
                        postcode: '',
                        phone: @json(auth()->guard('customer')->user()?->phone ?? ''),
                    }),
                },
            },

            data() {
                return {
                    selectedCountry: this.address.country,
                    countries: [],
                    states: null,
                    isGift: false,
                    giftEmail: '',
                }
            },

            computed: {
                haveStates() {
                    return !!this.states[this.selectedCountry]?.length;
                },

                isVirtualOnly() {
                    return !this.cart.have_stockable_items;
                },
            },

            mounted() {
                this.getCountries();
                this.getStates();
            },

            methods: {
                getCountries() {
                    this.$axios.get("{{ route('shop.api.core.countries') }}")
                        .then(response => {
                            this.countries = response.data.data;
                        })
                        .catch(() => { });
                },

                getStates() {
                    this.$axios.get("{{ route('shop.api.core.states') }}")
                        .then(response => {
                            this.states = response.data.data;
                        })
                        .catch(() => { });
                },

                trans(key) {
                    return this.$root.trans ? this.$root.trans(key) : key;
                },
            }
        });
    </script>
@endPushOnce