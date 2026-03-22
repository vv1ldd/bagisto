<template>
    <div class="p-10 bg-white rounded-[2rem] border border-[#e2d9ff] shadow-sm relative overflow-hidden group">
        <div class="relative z-10 text-center">
            <div class="flex flex-col items-center mb-10">
                <div class="w-20 h-20 bg-[#7C45F5]/5 text-[#7C45F5] rounded-3xl flex items-center justify-center mb-6 transition-transform group-hover:scale-105">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 0 002-2V8a2 0 00-2-2H5a2 0 00-2 2v8a2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-[24px] font-black text-[#1a0050] uppercase tracking-tighter italic">Создать встречу</h3>
                <p class="text-[14px] text-zinc-500 font-medium mt-2">Пригласите собеседника в защищенную видеокомнату</p>
            </div>

            <form :action="action" method="POST" @submit.prevent="submitForm" class="flex flex-col gap-4 max-w-[400px] mx-auto w-full">
                <input type="hidden" name="_token" :value="csrfToken">
                <input type="hidden" name="caller_name" :value="callerName">
                <input type="hidden" name="caller_email" :value="callerEmail">
                <input type="hidden" name="recipient_emails[]" :value="email">

                <div class="w-full">
                    <input 
                        type="text" 
                        v-model="email" 
                        placeholder="email@example.com или @alias"
                        class="w-full bg-zinc-50 border border-zinc-100 rounded-2xl px-6 py-5 text-[15px] text-[#1a0050] focus:outline-none focus:border-[#7C45F5] transition-all placeholder:text-zinc-400 font-medium"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="w-full bg-[#7C45F5] hover:bg-[#6b35e4] text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-[13px] transition-all shadow-xl shadow-[#7C45F5]/20 active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100"
                >
                    <span v-if="isSubmitting">Отправка...</span>
                    <span v-else>Начать встречу</span>
                </button>
            </form>
            
            <p class="mt-6 text-[10px] text-zinc-400 font-bold uppercase tracking-widest text-center">
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
