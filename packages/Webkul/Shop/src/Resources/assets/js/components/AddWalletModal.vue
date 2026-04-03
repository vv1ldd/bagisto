<template>
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-zinc-900/40 backdrop-blur-sm" @click="close"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-lg bg-white border-4 border-zinc-900 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] p-8 overflow-y-auto max-h-[90vh]">
            <button @click="close" class="absolute top-4 right-4 text-2xl font-black text-zinc-400 hover:text-zinc-900 transition-colors">
                ✕
            </button>

            <div class="mb-8 flex items-center gap-4">
                <div class="w-14 h-14 bg-[#D6FF00] border-4 border-zinc-900 flex items-center justify-center text-3xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                    ➕
                </div>
                <div>
                    <h3 class="text-xl font-black text-zinc-900 uppercase tracking-tighter italic">Add Asset Wallet</h3>
                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mt-1">Connect your crypto addresses</p>
                </div>
            </div>

            <!-- Asset selection -->
            <div v-if="!selectedAsset" class="space-y-6">
                <p class="text-[10px] text-zinc-400 uppercase font-black tracking-[0.2em]">Select Asset</p>
                <div class="grid grid-cols-2 gap-4">
                    <button v-for="(asset, id) in assetsConfig" :key="id"
                        @click="selectedAsset = id"
                        class="flex flex-col items-center justify-center p-6 border-4 border-zinc-200 bg-white hover:border-[#D6FF00] hover:bg-zinc-50 transition-all active:scale-95 group relative overflow-hidden">
                        <span class="text-3xl mb-2 group-hover:scale-110 transition-transform">{{ asset.icon }}</span>
                        <span class="text-[11px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-zinc-900">{{ asset.name }}</span>
                    </button>
                </div>
            </div>

            <!-- Network & Form -->
            <div v-else class="space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div class="flex items-center justify-between">
                    <button @click="selectedAsset = null" class="text-[10px] font-black uppercase tracking-widest text-[#7C45F5] hover:underline flex items-center gap-2">
                        ← Back to Assets
                    </button>
                    <span class="text-[11px] font-black uppercase tracking-widest text-zinc-900 bg-[#D6FF00] px-3 py-1 border-2 border-zinc-900 shadow-[3px_3px_0px_0px_rgba(24,24,27,1)]">{{ assetsConfig[selectedAsset].name }}</span>
                </div>

                <!-- Simple Network logic for now - can be expanded -->
                <div class="space-y-4 pt-4 border-t-2 border-zinc-100">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Wallet Address</label>
                    <div class="relative">
                        <input v-model="form.address" type="text"
                            placeholder="Enter 0x... or network address"
                            class="w-full bg-zinc-50 border-3 border-zinc-100 p-5 font-mono text-[14px] text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all outline-none">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Display Alias (Optional)</label>
                    <div class="relative">
                        <input v-model="form.alias" type="text"
                            placeholder="e.g., My Ledger Wallet"
                            class="w-full bg-zinc-50 border-3 border-zinc-100 p-5 font-mono text-[14px] text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all outline-none">
                    </div>
                </div>

                <button @click="handleSubmit" :disabled="!form.address || loading"
                    class="w-full bg-zinc-900 hover:bg-[#7C45F5] text-white font-black py-5 px-10 shadow-[6px_6px_0px_0px_rgba(214,255,0,1)] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-4 text-[14px] uppercase tracking-[0.2em]">
                    <span v-if="!loading">Connect Wallet 🔗</span>
                    <span v-else class="w-5 h-5 border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    assetsConfig: Object
});

const emit = defineEmits(['close', 'submit']);

const loading = ref(false);
const selectedAsset = ref(null);
const form = reactive({
    address: '',
    alias: ''
});

const close = () => {
    selectedAsset.value = null;
    form.address = '';
    form.alias = '';
    emit('close');
};

const handleSubmit = async () => {
    if (!form.address) return;
    
    loading.value = true;
    try {
        emit('submit', { 
            network: selectedAsset.value, 
            address: form.address, 
            alias: form.alias 
        });
    } finally {
        loading.value = false;
    }
};
</script>
