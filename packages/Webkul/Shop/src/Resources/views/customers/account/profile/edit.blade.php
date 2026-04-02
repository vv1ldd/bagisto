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
    <x-shop::layouts.account :is-cardless="true" :back-link="route('shop.customers.account.index')">
        <div class="relative w-full max-w-[600px] mx-auto">
            {{-- Header with Back Button --}}
            <div class="flex items-center gap-3 mb-2 px-4 pt-2">
                <button type="button" 
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ route('shop.customers.account.index') }}'"
                    class="w-12 h-12 bg-[#D6FF00] border-4 border-black flex items-center justify-center text-black active:scale-95 transition-all box-box-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:box-shadow-none">
                    <span class="icon-arrow-left text-2xl font-black"></span>
                </button>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter mix-blend-difference">{{ $pageTitle }}</h1>
            </div>

            <div class="p-0">
                <!-- Profile Edit Form -->
                <v-profile-edit inline-template>
                    <x-shop::form :action="route('shop.customers.account.profile.update')" enctype="multipart/form-data"
                        class="w-full" id="profile-edit-form" v-slot="{ meta }">
                        @include('shop::customers.account.profile.edit-form', ['customer' => $customer])
                    </x-shop::form>
                </v-profile-edit>
            </div>
        </div>
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
                    username: '{{ $customer->username }}',
                    usernameError: '',
                    debounceTimer: null
                }
            },
            methods: {
                debounceCheckUsername(value) {
                    this.username = value;
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