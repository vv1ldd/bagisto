<template>
    <div class="mt-10 p-8 bg-zinc-900 rounded-[2rem] text-white shadow-2xl relative overflow-hidden border border-white/5">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#7C45F5]/20 to-transparent pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-[#7C45F5] rounded-full flex items-center justify-center shadow-lg shadow-[#7C45F5]/30">
                    <span class="text-xs">✉️</span>
                </div>
                <h3 class="text-lg font-black uppercase tracking-tighter italic">Создать встречу</h3>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex gap-2 animate-fade-in">
                    <div class="flex-grow relative group">
                        <input 
                            type="text" 
                            v-model="email" 
                            placeholder="email@example.com или @alias"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm text-white focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-600 focus:bg-black/60"
                            required
                        >
                    </div>
                </div>
            </div>

            <form :action="action" method="POST" @submit.prevent="submitForm">
                <input type="hidden" name="_token" :value="csrfToken">
                <input type="hidden" name="caller_name" :value="callerName">
                <input type="hidden" name="caller_email" :value="callerEmail">
                <input type="hidden" name="recipient_emails[]" :value="email">

                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="w-full bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-95 disabled:opacity-50 disabled:active:scale-100"
                >
                    <span v-if="isSubmitting">Отправка...</span>
                    <span v-else>Начать встречу</span>
                </button>
            </form>
            
            <p class="mt-4 text-[10px] text-zinc-500 font-bold uppercase tracking-wider text-center">
                Участник получит письмо со ссылкой на защищенную комнату
            </p>


        </div>
    </div>
</template>

<script>
export default {
    props: ['action', 'csrfToken', 'callerName', 'callerEmail'],

    data() {
        return {
            email: '',
            isSubmitting: false,
            roomUuid: this.generateUuid(),
        }
    },

    computed: {
        roomUrl() {
            return `${window.location.origin}/call/${this.roomUuid}`;
        }
    },

    methods: {
        submitForm(e) {
            if (!this.email) {
                return;
            }
            
            // Inject the same roomUuid into the form
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'room_uuid';
            input.value = this.roomUuid;
            e.target.appendChild(input);

            this.isSubmitting = true;
            e.target.submit();
        },

        generateUuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },


    }
}
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
