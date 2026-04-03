<template>
    <div class="relative w-full max-w-[500px] mx-auto px-4 mt-2 mb-10">
        <!-- Dashboard Header -->
        <div class="mb-8 text-center space-y-4">
             <div class="inline-flex items-center gap-3 px-4 py-2 bg-white border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">
                <span class="w-2 h-2 bg-[#D6FF00] border border-zinc-900 rotate-45"></span>
                <span class="text-[10px] font-black text-zinc-900 uppercase tracking-[0.3em] italic">
                    Meanly Wallet System v2.3
                </span>
            </div>
            <h1 class="text-[42px] font-black text-zinc-900 uppercase tracking-tighter italic leading-none">
                Wallet
            </h1>
        </div>

        <!-- Main Container -->
        <div class="bg-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] overflow-hidden">
            
            <!-- Account Info Header -->
            <div class="p-8 border-b-4 border-zinc-900 bg-zinc-50 flex flex-col items-center gap-6">
                <!-- User Profile -->
                <div class="flex items-center gap-4 w-full px-2">
                    <div class="w-14 h-14 bg-zinc-900 border-4 border-zinc-900 flex items-center justify-center text-2xl text-white shadow-[4px_4px_0px_0px_rgba(214,255,0,1)]">
                        👤
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[14px] font-black text-zinc-900 uppercase tracking-tighter italic truncate">
                                {{ data.user.name }}
                            </span>
                            <span v-if="data.user.is_investor" class="bg-[#D6FF00] text-[9px] font-black text-zinc-900 px-2 py-0.5 border-2 border-zinc-900 shadow-[2px_2px_0px_0px_rgba(24,24,27,1)] uppercase">
                                Investor
                            </span>
                        </div>
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">@{{ data.user.username }}</p>
                    </div>
                </div>

                <!-- Meanly Coin Primary Balance -->
                <div class="w-full bg-white border-4 border-zinc-900 p-6 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] group hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] transition-all flex justify-between items-center relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] text-[120px] font-black italic select-none pointer-events-none group-hover:opacity-[0.07] transition-all">MC</div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-2 font-black italic">Meanly Coin Balance</p>
                        <div class="flex items-end gap-3">
                            <span class="text-4xl font-black text-zinc-900 tracking-tighter italic leading-none">{{ formatAmount(data.user.meanly_coin_balance) }}</span>
                            <span class="text-sm font-black text-[#D6FF00] bg-zinc-900 px-2 py-0.5 border-2 border-zinc-900 uppercase tracking-widest mb-1 shadow-[3px_3px_0px_0px_rgba(214,255,0,1)]">MC</span>
                        </div>
                    </div>
                    <div class="relative z-10 w-16 h-16 bg-[#D6FF00] border-4 border-zinc-900 flex items-center justify-center text-4xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                        🪙
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div id="wallet-tabs" class="flex border-b-4 border-zinc-900 bg-white">
                <button v-for="tab in tabs" :key="tab.id"
                    @click="currentTab = tab.id"
                    :class="['flex-1 py-5 text-[11px] font-black uppercase tracking-[0.22em] transition-all border-r-4 last:border-r-0 border-zinc-900',
                            currentTab === tab.id ? 'bg-[#D6FF00] text-zinc-900' : 'bg-white text-zinc-400 hover:bg-zinc-50']">
                    {{ tab.name }}
                </button>
            </div>

            <div class="p-6 md:p-10 bg-zinc-50/30">
                <!-- DASHBOARD TAB -->
                <transition name="fade" mode="out-in">
                    <div v-if="currentTab === 'dashboard'" class="space-y-12">
                        
                        <!-- Investor Assets Grid (Only for investors) -->
                        <div v-if="data.user.is_investor" class="space-y-6">
                            <div class="flex items-center gap-4">
                                <h3 class="text-[12px] font-black text-zinc-900 uppercase tracking-[0.2em] italic">Unified Assets Grid</h3>
                                <div class="flex-1 h-[2px] bg-zinc-900"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="(config, id) in data.assets_config" :key="id"
                                    class="bg-white border-4 border-zinc-900 p-5 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] group hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] transition-all">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-10 h-10 bg-zinc-900 border-2 border-zinc-900 flex items-center justify-center text-xl text-white shadow-[2px_2px_0px_0px_rgba(214,255,0,1)]">
                                            {{ config.icon }}
                                        </div>
                                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ config.name }}</p>
                                    </div>
                                    <div class="space-y-0.5">
                                        <p class="text-xl font-black text-zinc-900 tracking-tighter">{{ formatBalance(data.balances[id]) || '0.00' }}</p>
                                        <p class="text-[9px] font-black text-zinc-300 uppercase tracking-widest">Available Balance</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Management Trigger (Investor Only) -->
                            <div v-if="data.addresses && data.addresses.length > 0" class="space-y-6">
                                <div v-for="address in data.addresses" :key="address.id"
                                    class="bg-white border-4 border-zinc-900 flex shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] group overflow-hidden">
                                    <div class="flex-1 flex gap-6 p-6 min-w-0 text-left items-center">
                                        <div class="w-14 h-14 bg-zinc-900 border-3 border-zinc-900 flex items-center justify-center text-white text-2xl shadow-[3px_3px_0px_0px_rgba(214,255,0,1)]">
                                            {{ data.assets_config[address.network]?.icon || '📦' }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[14px] font-black text-zinc-900 uppercase tracking-tighter italic truncate">
                                                {{ address.alias || 'Primary Wallet' }}
                                            </p>
                                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1 truncate">{{ address.address }}</p>
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-lg font-black font-mono text-zinc-900">{{ formatBalance(address.balance) }}</span>
                                                <button v-if="address.network.includes('arbitrum')"
                                                    @click="openSendModal(address)"
                                                    class="px-4 py-2 bg-[#D6FF00] border-3 border-zinc-900 text-zinc-900 text-[10px] font-black uppercase tracking-widest shadow-[3px_3px_0px_0px_rgba(24,24,27,1)] active:scale-95">
                                                    Transfer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center px-4 border-l-4 border-zinc-900 bg-zinc-50 hover:bg-red-500 hover:text-white transition-colors cursor-pointer"
                                         @click="deleteWallet(address)">
                                        🗑️
                                    </div>
                                </div>
                            </div>

                            <button @click="isAddWalletOpen = true"
                                class="w-full bg-zinc-900 border-4 border-zinc-900 py-4 px-6 text-white text-[12px] font-black uppercase tracking-widest shadow-[6px_6px_0px_0px_rgba(214,255,0,1)] hover:bg-[#D6FF00] hover:text-zinc-900 transition-all active:scale-95">
                                Connect New Wallet +
                            </button>
                        </div>

                        <!-- Regular Dashboard Placeholder -->
                        <div v-else class="text-center p-10 border-4 border-dashed border-zinc-200">
                             <div class="text-4xl mb-4">💎</div>
                             <h3 class="text-[14px] font-black text-zinc-900 uppercase tracking-[0.2em]">Dashboard Active</h3>
                             <p class="text-[10px] text-zinc-400 mt-2">Check your collection and transactions below.</p>
                        </div>
                    </div>
                </transition>

                <!-- TRANSACTIONS TAB -->
                <!-- (Same as before, simplified for brevity in replacement) -->
                <transition name="fade" mode="out-in">
                    <div v-if="currentTab === 'transactions'" class="space-y-6">
                         <div v-if="data.transactions.length > 0" class="space-y-4">
                             <div v-for="tx in data.transactions" :key="tx.id"
                                class="bg-white border-4 border-zinc-900 p-5 shadow-[4px_4px_0px_0px_rgba(24,24,27,1)] flex items-center gap-4 group">
                                <div :class="['w-12 h-12 border-2 border-zinc-900 flex items-center justify-center text-xl shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]',
                                            tx.type === 'credit' ? 'bg-[#D6FF00]' : 'bg-zinc-900 text-white']">
                                    {{ tx.type === 'credit' ? '+' : '🛒' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="text-[14px] font-black text-zinc-900 uppercase tracking-tighter truncate">{{ tx.description }}</h4>
                                        <span class="text-[14px] font-black text-zinc-900">{{ tx.type === 'credit' ? '+' : '-' }}{{ formatAmount(tx.amount) }} MC</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ tx.formatted_date }}</span>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 bg-zinc-100 border border-zinc-200">{{ tx.status }}</span>
                                    </div>
                                </div>
                             </div>
                         </div>
                    </div>
                </transition>

                <!-- NFT TAB remains same -->
                <transition name="fade" mode="out-in">
                    <div v-if="currentTab === 'nfts'">
                        <div v-if="data.nfts.length > 0" class="grid grid-cols-2 gap-6">
                            <div v-for="order in data.nfts" :key="order.id"
                                class="bg-white border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] group relative">
                                <div class="aspect-square bg-zinc-100 flex items-center justify-center border-b-4 border-zinc-900 overflow-hidden">
                                     <div class="text-6xl group-hover:scale-125 transition-transform duration-500 select-none">🖼️</div>
                                </div>
                                <div class="p-4 bg-zinc-900 border-t border-zinc-800 flex items-center justify-between">
                                    <span class="text-[#D6FF00] font-black text-[10px] uppercase tracking-widest">Asset #{{ order.increment_id }}</span>
                                    <div class="w-2 h-2 bg-[#D6FF00] rotate-45"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Modals -->
        <v-send-asset-modal 
            :is-open="isSendOpen" 
            :network="sendContext.network"
            :symbol="sendContext.symbol"
            :balance="sendContext.balance"
            @close="isSendOpen = false"
            @send="handleSendAction"
        ></v-send-asset-modal>

        <v-add-wallet-modal
            :is-open="isAddWalletOpen"
            :assets-config="data.assets_config"
            @close="isAddWalletOpen = false"
            @submit="handleAddWalletAction"
        ></v-add-wallet-modal>

        <!-- Footnote -->
        <p class="mt-8 text-center text-[10px] text-zinc-400 font-black uppercase tracking-[0.4em] italic opacity-60">
            Powered by Meanly Protocol
        </p>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const currentTab = ref(props.data.current_step || 'dashboard');

// Modal states
const isSendOpen = ref(false);
const isAddWalletOpen = ref(false);
const sendContext = ref({ network: '', symbol: '', balance: 0 });

const tabs = [
    { id: 'dashboard', name: 'Обзор' },
    { id: 'transactions', name: 'Транзакции' },
    { id: 'nfts', name: 'NFT' }
];

const openSendModal = (address) => {
    sendContext.value = { 
        network: address.network, 
        symbol: props.data.assets_config[address.network]?.name || 'ETH',
        balance: parseFloat(address.balance || 0)
    };
    isSendOpen.value = true;
};

const handleSendAction = async (formData) => {
    try {
        // Mock authorization & API call
        console.log('Sending asset:', formData);
        await new Promise(r => setTimeout(r, 1000));
        alert('Transaction submitted to network!');
        isSendOpen.value = false;
    } catch (e) {
        alert('Error: ' + e.message);
    }
};

const handleAddWalletAction = async (formData) => {
    try {
        const response = await axios.post('/account/crypto/store', {
            ...formData,
            _token: document.querySelector('meta[name="csrf-token"]')?.content
        });
        alert('Wallet connected successfully!');
        window.location.reload(); // Simple refresh for now to stay synced with backend
    } catch (e) {
        alert('Error adding wallet: ' + (e.response?.data?.message || e.message));
    }
};

const deleteWallet = async (address) => {
    if (!confirm('Are you sure you want to delete this wallet?')) return;
    try {
        await axios.post(`/account/crypto/delete/${address.id}`, {
            _method: 'DELETE',
            _token: document.querySelector('meta[name="csrf-token"]')?.content
        });
        window.location.reload();
    } catch (e) {
        alert('Error deleting wallet: ' + e.message);
    }
};

const formatAmount = (val) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(val || 0);
};

const formatBalance = (val) => {
    if (val === undefined || val === null) return '0.00';
    return val.toFixed(8).replace(/\.?0+$/, "");
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
