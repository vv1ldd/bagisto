<template>
    <div v-if="!isActive" class="relative">
        <!-- Close Button (Absolute Top Right) -->
        <button v-if="isGuest" @click="goBack" class="absolute -top-4 -right-4 h-10 w-10 bg-zinc-800 text-white rounded-2xl flex items-center justify-center border border-white/10 hover:bg-zinc-700 active:scale-95 transition-all z-20 shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <div class="space-y-4">
            <!-- Guest Name Input -->
            <div v-if="isGuest" class="animate-fade-in">
                <label class="block text-[8px] uppercase tracking-[0.3em] text-zinc-500 mb-2 font-black">Ваше имя для участия</label>
                <input 
                    type="text" 
                    v-model="customName" 
                    placeholder="Напр. Алексей"
                    class="w-full bg-zinc-900 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-700"
                    @keyup.enter="join"
                >
            </div>

            <button 
                @click="join"
                class="w-full h-16 bg-[#7C45F5] text-white font-black uppercase tracking-widest text-sm shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6b35e4] transition-all active:scale-[0.98] rounded-2xl"
            >
                {{ isGuest ? 'Присоединиться под этим именем' : 'Войти в чат' }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['uuid', 'userNameInitial', 'participantHash'],

    data() {
        return {
            isActive: false,
            customName: ''
        }
    },

    computed: {
        isGuest() {
            return !this.userNameInitial || this.userNameInitial === 'Гость';
        }
    },

    methods: {
        join() {
            let finalName = this.userNameInitial;
            
            if (this.isGuest) {
                finalName = this.customName.trim() || 'Гость';
            }

            // Always add a unique suffix to the name display, but the hash will be the unique ID
            finalName += ' ' + Math.random().toString(36).substring(2, 5).toUpperCase();

            console.log('RoomJoiner: Joining as', finalName, 'with hash', this.participantHash);
            
            this.$emitter.emit('join-room', {
                uuid: this.uuid,
                userName: finalName,
                hash: this.participantHash
            });

            this.isActive = true;
            this.$emit('room-joined');
        },

        goBack() {
            if (document.referrer && !document.referrer.includes('/call/')) {
                window.location.href = document.referrer;
            } else {
                window.location.href = '/';
            }
        }
    }
}
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
