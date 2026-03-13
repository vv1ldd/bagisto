<template>
    <div v-if="!isActive" class="space-y-4">
        <button 
            @click="join"
            class="w-full h-16 bg-[#7C45F5] text-white font-black uppercase tracking-widest text-sm shadow-lg shadow-[#7C45F5]/20 hover:bg-[#6b35e4] transition-all active:scale-[0.98]"
        >
            Войти в чат
        </button>
    </div>
</template>

<script>
export default {
    props: ['uuid', 'userNameInitial'],

    data() {
        return {
            isActive: false
        }
    },

    methods: {
        join() {
            console.log('RoomJoiner: Join clicked for', this.uuid);
            
            this.$emitter.emit('join-room', {
                uuid: this.uuid,
                userName: this.userNameInitial
            });

            this.isActive = true;
            this.$emit('joined');
        }
    }
}
</script>
