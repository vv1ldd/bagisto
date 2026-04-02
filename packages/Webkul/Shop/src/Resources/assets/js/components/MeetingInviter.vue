<template>
    <div class="relative group">
        <!-- Hard Black Shadow Layer -->
        <div class="absolute inset-0 bg-black translate-x-3 translate-y-3"></div>
        
        <div class="relative p-6 md:p-10 bg-white border-4 border-black overflow-hidden">
            <!-- Accent Corner Flash -->
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#D6FF00] border-b-4 border-l-4 border-black translate-x-4 -translate-y-4 rotate-45"></div>

            <div class="relative z-10 text-center">
                <div class="flex flex-col items-center mb-8">
                    <!-- Brutalist Icon Container -->
                    <div class="w-16 h-16 bg-[#D6FF00] border-4 border-black flex items-center justify-center mb-4 transition-transform group-hover:-rotate-3 group-hover:scale-105">
                        <svg class="w-8 h-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 0 002-2V8a2 0 00-2-2H5a2 0 00-2 2v8a2 0 002 2z"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-[26px] md:text-[32px] font-black text-black uppercase tracking-tighter italic leading-none mb-2">Создать встречу</h3>
                    <p class="text-[14px] text-black font-bold opacity-70">Пригласите собеседника в защищенную видеокомнату</p>
                </div>

                <form :action="action" method="POST" @submit.prevent="submitForm" class="flex flex-col gap-5 max-w-[420px] mx-auto w-full">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <input type="hidden" name="caller_name" :value="callerName">
                    <input type="hidden" name="caller_email" :value="callerEmail">
                    <input type="hidden" name="recipient_emails[]" :value="email">
    
                    <div class="w-full relative group/input">
                        <label class="absolute -top-3 left-4 bg-white border-2 border-black px-2 py-0.5 text-[10px] font-black uppercase tracking-widest z-20">Получатель</label>
                        <input 
                            type="text" 
                            v-model="email" 
                            placeholder="email@example.com или @alias"
                            class="w-full bg-white border-4 border-black px-6 py-4 text-[16px] text-black focus:outline-none focus:bg-[#D6FF00]/10 transition-all placeholder:text-zinc-400 font-black"
                            required
                        >
                    </div>

                    <div class="relative group/btn">
                        <!-- Button Shadow -->
                        <div class="absolute inset-0 bg-black translate-x-1.5 translate-y-1.5"></div>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="relative w-full bg-[#7C45F5] border-4 border-black text-white px-10 py-5 font-black uppercase tracking-widest text-[14px] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none whitespace-nowrap disabled:opacity-50"
                        >
                            <span v-if="isSubmitting">Отправка...</span>
                            <span v-else>Начать встречу</span>
                        </button>
                    </div>
                </form>
                
                <div class="mt-8">
                    <span class="bg-black text-[#D6FF00] px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em]">P2P ЭНД-ТУ-ЭНД ШИФРОВАНИЕ</span>
                </div>
            </div>
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
