@php
    $isCompleteRegistration = isset($isCompleteRegistration) && $isCompleteRegistration;
    $pageTitle = $isCompleteRegistration ? 'Продолжение регистрации' : 'Профиль';
@endphp

@if ($isCompleteRegistration)
    <x-shop::layouts.split-screen :title="$pageTitle">
        <!-- Profile Edit Form -->
        <v-profile-edit inline-template>
            <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data"
                class="w-full" v-slot="{ meta }">
                <div v-show="false">
                    <span v-if="meta.valid"></span>
                </div>
                @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
            </x-shop::form>
        </v-profile-edit>
    </x-shop::layouts.split-screen>
@else
    <x-shop::layouts.account :is-cardless="true" :title="$pageTitle" :back-link="route('shop.customers.account.index')">
        <!-- Profile Edit Form -->
        <v-profile-edit inline-template>
            <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data"
                class="w-full" id="profile-edit-form" v-slot="{ meta }">
                @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
            </x-shop::form>
        </v-profile-edit>
    </x-shop::layouts.account>
@endif

@push('scripts')
    <script type="text/x-template" id="v-profile-edit-template">
            <div>
                <slot></slot>
            </div>
        </script>

    <script>
        app.component('v-profile-edit', {
            data() {
                return {
                    usernameError: '',
                    debounceTimer: null
                }
            },
            methods: {
                debounceCheckUsername(value) {
                    clearTimeout(this.debounceTimer);
                    this.usernameError = '';

                    if (value.length < 3) return;

                    this.debounceTimer = setTimeout(() => {
                        this.checkUsername(value);
                    }, 500);
                },

                clearDefaultUsername(event) {
                    if (event.target.value.startsWith('user_')) {
                        event.target.value = '';
                    }
                },
                checkUsername(value) {
                    this.$axios.get("{{ route('shop.customers.account.profile.check_username') }}", {
                        params: { username: value }
                    })
                        .then(response => {
                            if (!response.data.available) {
                                this.usernameError = response.data.message || 'Этот псевдоним уже занят';
                            } else {
                                this.usernameError = '';
                            }
                        })
                        .catch(error => {
                            console.error('Error checking username:', error);
                        });
                }
            }
        });
    </script>
@endpush