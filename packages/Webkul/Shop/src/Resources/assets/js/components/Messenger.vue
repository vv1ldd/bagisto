<template>
    <div v-show="isVisible" class="fixed bottom-4 right-4 w-96 h-[32rem] bg-white shadow-2xl rounded-xl flex flex-col border border-zinc-200 overflow-hidden z-[9999]">
        <!-- Header -->
        <div class="px-4 py-3 bg-[#7C45F5] text-white flex items-center justify-between">
            <h3 class="font-bold flex items-center gap-2">
                <span class="icon-chat text-xl"></span>
                Messenger
            </h3>
            <div class="flex items-center gap-2">
                <button @click="startCall" class="p-1 hover:bg-white/20 rounded transition" title="Start Call">
                    <span class="icon-phone-call text-lg"></span>
                </button>
                <button @click="isVisible = false" class="p-1 hover:bg-white/20 rounded transition">
                    <span class="icon-close text-lg"></span>
                </button>
            </div>
        </div>

        <!-- Room List / Messages -->
        <div class="flex-1 overflow-hidden flex">
            <!-- Sidebar (Rooms) -->
            <div class="w-24 border-r border-zinc-100 bg-zinc-50 overflow-y-auto">
                <div v-for="room in rooms" :key="room.roomId" 
                     @click="selectRoom(room.roomId)"
                     :class="['p-3 cursor-pointer hover:bg-white transition text-center border-b border-zinc-100', activeRoomId === room.roomId ? 'bg-white border-l-4 border-l-[#7C45F5]' : '']">
                    <div class="w-10 h-10 bg-zinc-200 rounded-full mx-auto flex items-center justify-center text-zinc-500 font-bold text-xs overflow-hidden">
                        <img v-if="room.avatarUrl" :src="room.avatarUrl" class="w-full h-full object-cover">
                        <span v-else>{{ room.name.substring(0, 1).toUpperCase() }}</span>
                    </div>
                    <div class="text-[10px] mt-1 text-zinc-600 truncate px-1">{{ room.name }}</div>
                </div>
            </div>

            <!-- Chat Window -->
            <div class="flex-1 flex flex-col bg-white overflow-hidden">
                <div v-if="activeRoomId" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3" ref="messageContainer">
                    <div v-for="msg in messages" :key="msg.id" 
                         :class="['max-w-[85%] p-2.5 rounded-2xl text-sm leading-relaxed', msg.sender === userId ? 'self-end bg-[#7C45F5] text-white rounded-tr-none' : 'self-start bg-zinc-100 text-zinc-800 rounded-tl-none']">
                        <div v-if="msg.sender !== userId" class="text-[10px] opacity-60 mb-1 font-bold">{{ msg.senderName }}</div>
                        {{ msg.body }}
                        <div class="text-[9px] mt-1 opacity-70 text-right">{{ msg.time }}</div>
                    </div>
                </div>
                <div v-else class="flex-1 flex items-center justify-center text-zinc-400 text-sm italic p-6 text-center">
                    Выберите чат для начала общения
                </div>

                <!-- Input -->
                <div v-if="activeRoomId" class="p-3 border-t border-zinc-100 bg-zinc-50">
                    <div class="relative">
                        <input v-model="newMessage" @keyup.enter="sendMessage"
                               type="text" placeholder="Сообщение..."
                               class="w-full bg-white border border-zinc-200 rounded-full py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#7C45F5]/20 focus:border-[#7C45F5] transition-all">
                        <button @click="sendMessage" 
                                class="absolute right-1 top-1 w-8 h-8 rounded-full bg-[#7C45F5] text-white flex items-center justify-center hover:scale-105 transition active:scale-95">
                            <span class="icon-arrow-right text-xs"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isVisible: false,
            userId: '',
            rooms: [],
            activeRoomId: null,
            messages: [],
            newMessage: '',
            syncLimit: 20
        }
    },

    mounted() {
        // Matrix initialization removed. Communication is now via WebSockets/Echo in CallOverlay.vue.
        console.warn('Messenger: Matrix integration has been removed. Use Echo for real-time signaling.');
        
        this.$emitter.on('open-messenger', (data) => {
            this.isVisible = !this.isVisible;
        });
    },

    methods: {
        selectRoom(roomId) {
            this.activeRoomId = roomId;
            // Room loading from Matrix removed.
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;
            
            // Matrix sendEvent removed. 
            // TODO: Implement WebSocket message broadcast if needed.
            this.newMessage = '';
        },

        startCall() {
             // Matrix call signaling removed.
             // CallOverlay already handles signaling via Echo.
             console.log('Messenger: startCall trigger - ensure CallOverlay is active.');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messageContainer) {
                    this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
                }
            });
        }
    }
}
</script>
