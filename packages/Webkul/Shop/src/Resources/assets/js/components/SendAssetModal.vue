<template>
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-zinc-900/40 backdrop-blur-sm" @click="close"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-md bg-white border-4 border-zinc-900 shadow-[10px_10px_0px_0px_rgba(24,24,27,1)] p-8">
            <button @click="close" class="absolute top-4 right-4 text-2xl font-black text-zinc-400 hover:text-zinc-900 transition-colors">
                ✕
            </button>

            <div class="mb-8 flex items-center gap-4">
                <div class="w-14 h-14 bg-[#D6FF00] border-4 border-zinc-900 flex items-center justify-center text-3xl shadow-[4px_4px_0px_0px_rgba(24,24,27,1)]">
                    📤
                </div>
                <div>
                    <h3 class="text-xl font-black text-zinc-900 uppercase tracking-tighter italic">Transfer Assets</h3>
                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mt-1">Network: {{ network.toUpperCase() }}</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Recipient field -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Recipient</label>
                    <div class="relative">
                        <input v-model="form.recipient" type="text"
                            placeholder="@alias or 0x..."
                            class="w-full bg-zinc-50 border-3 border-zinc-100 p-5 font-mono text-[14px] text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all outline-none">
                    </div>
                </div>

                <!-- Amount field -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Amount</label>
                    <div class="relative">
                        <input v-model.number="form.amount" type="number" step="any" placeholder="0.00"
                            class="w-full bg-zinc-50 border-3 border-zinc-100 p-5 font-mono text-[24px] font-black text-zinc-900 focus:bg-white focus:border-[#7C45F5] transition-all outline-none">
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-400 font-black uppercase">{{ symbol }}</div>
                    </div>
                    <div class="flex justify-between items-center px-1">
                        <p class="text-[10px] text-zinc-400 font-black">Available: {{ formatBalance(balance) }} {{ symbol }}</p>
                        <button @click="form.amount = balance" class="text-[10px] font-black uppercase text-[#7C45F5] hover:underline">MAX</button>
                    </div>
                </div>

                <!-- Action Button -->
                <button @click="handleSend" :disabled="loading"
                    class="w-full bg-zinc-900 hover:bg-[#7C45F5] text-white font-black py-5 px-10 shadow-[6px_6px_0px_0px_rgba(214,255,0,1)] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-4 text-[14px] uppercase tracking-[0.2em]">
                    <span v-if="!loading">Authorize & Send 🔐</span>
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
    network: String,
    symbol: String,
    balance: Number
});

const emit = defineEmits(['close', 'send']);

const loading = ref(false);
const form = reactive({
    recipient: '',
    amount: null
});

const close = () => {
    form.recipient = '';
    form.amount = null;
    emit('close');
};

const handleSend = async () => {
    if (!form.recipient || !form.amount) return;
    
    loading.value = true;
    try {
        // Here we'll pass to main component to handle actual API call
        emit('send', { ...form });
    } finally {
        loading.value = false;
    }
};

const formatBalance = (val) => {
    if (val === undefined || val === null) return '0.00';
    return val.toFixed(8).replace(/\.?0+$/, "");
};
</script>
