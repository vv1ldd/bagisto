<x-shop::layouts.account :is-cardless="true">
    {{-- Reactive Wallet Dashboard --}}
    <v-wallet-dashboard :data='@json($walletData)'>
        <template v-slot:balance>
            <x-shop::live-balance :user="auth()->guard('customer')->user()" />
        </template>
    </v-wallet-dashboard>
</x-shop::layouts.account>