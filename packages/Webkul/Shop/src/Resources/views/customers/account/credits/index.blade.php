<x-shop::layouts.account :is-cardless="true">
    {{-- Reactive Wallet Dashboard --}}
    <v-wallet-dashboard :data='@json($walletData)'></v-wallet-dashboard>
</x-shop::layouts.account>