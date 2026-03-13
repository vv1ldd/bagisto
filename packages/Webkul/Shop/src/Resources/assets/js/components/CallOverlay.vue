<template>
    <div v-if="isActive" class="fixed inset-0 z-[10000] bg-black text-white p-4 md:p-8 flex flex-col justify-between font-sans overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/20 pb-4 relative z-50">
            <div>
                <div class="text-[8px] md:text-[10px] uppercase tracking-[0.3em] opacity-60 mb-1">
                    <span v-if="Object.keys(peers).length > 0">Групповая встреча</span>
                    <span v-else>Ожидание участников</span>
                </div>
                <h2 class="text-xl md:text-3xl font-black uppercase italic tracking-tighter">{{ isRoomMode ? 'Защищенная комната' : remoteUserName }}</h2>
            </div>
            <div class="flex items-center gap-4">
                <div v-if="Object.keys(peers).length > 0" class="bg-[#00FF41] text-black px-3 md:px-4 py-1 font-bold text-[10px] md:text-xs uppercase tracking-widest animate-pulse">
                    {{ Object.keys(peers).length + 1 }} в сети
                </div>
                <div class="flex -space-x-2">
                    <div v-for="(peer, name) in peers" :key="name" 
                        class="w-6 h-6 rounded-full bg-zinc-800 border-2 border-black flex items-center justify-center text-[10px] font-bold uppercase"
                        :title="name">
                        {{ name[0] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Grid -->
        <div class="flex-grow relative my-4 overflow-hidden rounded-3xl bg-zinc-950">
            <div :class="gridClass" class="grid w-full h-full p-2 md:p-4 gap-2 md:gap-4 transition-all duration-500">
                <!-- Local Video -->
                <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group">
                    <video ref="localVideo" autoplay muted playsinline class="w-full h-full object-cover mirror"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10">
                        Вы ({{ localUserName }})
                    </div>
                </div>

                <!-- Remote Videos -->
                <div v-for="(peer, name) in peers" :key="name" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group">
                    <video :ref="'remoteVideo_' + name" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 transition-all group-hover:bg-[#7C45F5]/80">
                        {{ name }}
                    </div>
                    <div v-if="!peer.connected" class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-20">
                        <div class="flex flex-col items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-[#00FF41] animate-ping"></div>
                             <span class="text-[8px] uppercase font-bold tracking-[0.2em] opacity-60">Соединение...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="Object.keys(peers).length === 0" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none">
                <div class="w-24 h-24 rounded-full bg-zinc-900/50 backdrop-blur-2xl border border-white/5 flex items-center justify-center mb-6 animate-pulse">
                    <span class="text-4xl">👥</span>
                </div>
                <span class="text-[10px] md:text-xs uppercase tracking-[0.4em] text-zinc-500 text-center px-8">
                    Ожидание подключения других участников...
                </span>
            </div>
        </div>

        <!-- Controls -->
        <div class="relative z-50 flex justify-center gap-4 py-4 mt-auto">
             <div class="flex justify-center gap-3 md:gap-6 pb-4 bg-black/60 backdrop-blur-xl px-6 md:px-12 py-4 md:py-6 rounded-full border border-white/10 mx-auto w-max">
                <button @click="toggleMic" :class="[isMicOn ? 'bg-white text-black' : 'bg-red-500/20 text-red-500 border-red-500/40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10">
                    <span class="text-[8px] md:text-[10px] font-black uppercase">{{ isMicOn ? 'On' : 'Off' }}</span>
                </button>
                
                <button @click="endCall" 
                    class="h-12 px-8 md:h-16 md:px-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-black uppercase text-xs md:text-sm tracking-widest transition-all shadow-lg shadow-red-500/20 active:scale-95">
                    Выйти
                </button>
 
                <button @click="toggleScreenShare" :class="[isSharingScreen ? 'bg-[#00FF41] text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10">
                    <span class="text-[8px] md:text-[10px] font-black uppercase">{{ isSharingScreen ? 'Stop' : 'Share' }}</span>
                </button>
 
                <button @click="toggleCamera" :class="[isCameraOn ? 'bg-white text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10">
                    <span class="text-[8px] md:text-[10px] font-black uppercase">{{ isCameraOn ? 'On' : 'Off' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isActive: false,
            localStream: null,
            localUserName: '',
            roomUuid: null,
            isRoomMode: false,
            peers: {}, // { name: { pc, stream, connected } }
            isMicOn: true,
            isCameraOn: true,
            isSharingScreen: false,
            screenStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' },
                ]
            }
        };
    },

    computed: {
        gridClass() {
            const count = Object.keys(this.peers).length + 1;
            if (count === 1) return 'grid-cols-1';
            if (count === 2) return 'grid-cols-1 md:grid-cols-2';
            if (count <= 4) return 'grid-cols-2';
            return 'grid-cols-2 md:grid-cols-3';
        }
    },

    mounted() {
        this.$emitter.on('join-room', (payload) => {
            if (this.isActive) return;
            this.joinRoom(payload.uuid, payload.userName);
        });

        const customerId = this.$shop?.customer_id;
        if (window.Echo && customerId) {
             window.Echo.private(`user.${customerId}`).listen('.call-signal', (data) => this.handleSignal(data));
        }

        const laravel = window.Laravel || {};
        if (laravel.turnUrl) {
            this.configuration.iceServers.unshift({
                urls: [laravel.turnUrl],
                username: laravel.turnUsername,
                credential: laravel.turnPassword
            });
        }
    },

    methods: {
        async joinRoom(uuid, userName) {
            console.log('Room: Joining', uuid, 'as', userName);
            this.roomUuid = uuid;
            this.localUserName = userName;
            this.isRoomMode = true;
            this.isActive = true;

            await this.setupLocalMedia();

            if (window.Echo) {
                window.Echo.channel(`call.${uuid}`).listen('.call-signal', (data) => this.handleSignal(data));
            }

            // Broadcast presence
            this.sendSignal({ type: 'presence' });
        },

        async setupLocalMedia() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                this.$nextTick(() => { 
                    if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
                });
            } catch (e) { console.error('Media access error', e); }
        },

        handleSignal(data) {
            const signal = data.signal_data;
            const senderName = data.sender_name;
            if (!senderName || senderName === this.localUserName) return;

            // Targeting: if target is set, check it
            if (signal.target && signal.target !== this.localUserName) return;

            console.log(`Room: Signal [${signal.type}] from ${senderName}`, signal);

            if (signal.type === 'presence') {
                // Determine if I should initiate based on lexicographical order
                const shouldIInitiate = this.localUserName.toLowerCase() < senderName.toLowerCase();
                if (shouldIInitiate && !this.peers[senderName]) {
                    this.initiateConnection(senderName);
                } else if (!this.peers[senderName]) {
                    // Just record that they exist, wait for their offer
                    this.$set(this.peers, senderName, { pc: null, stream: null, connected: false });
                }
            } else if (signal.type === 'offer') {
                this.handleOffer(senderName, signal);
            } else if (signal.type === 'answer') {
                this.handleAnswer(senderName, signal);
            } else if (signal.type === 'candidate') {
                this.handleCandidate(senderName, signal);
            } else if (signal.type === 'hangup') {
                this.removePeer(senderName);
            }
        },

        async initiateConnection(name) {
            console.log(`WebRTC: Initiating offer to ${name}`);
            const pc = this.createPeerConnection(name);
            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);
            this.sendSignal({ type: 'offer', sdp: offer.sdp, target: name });
        },

        async handleOffer(name, signal) {
            console.log(`WebRTC: Handling offer from ${name}`);
            const pc = this.createPeerConnection(name);
            await pc.setRemoteDescription(new RTCSessionDescription(signal));
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            this.sendSignal({ type: 'answer', sdp: answer.sdp, target: name });
        },

        async handleAnswer(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                await peer.pc.setRemoteDescription(new RTCSessionDescription(signal));
            }
        },

        async handleCandidate(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                try {
                    await peer.pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
                } catch (e) { console.error('Candidate error', e); }
            }
        },

        createPeerConnection(name) {
            if (this.peers[name]?.pc) return this.peers[name].pc;

            const pc = new RTCPeerConnection(this.configuration);
            this.$set(this.peers, name, { pc, stream: null, connected: false });

            if (this.localStream) {
                this.localStream.getTracks().forEach(t => pc.addTrack(t, this.localStream));
            }

            pc.onicecandidate = (e) => {
                if (e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: name });
            };

            pc.ontrack = (e) => {
                console.log(`WebRTC: Track from ${name}`);
                this.peers[name].stream = e.streams[0];
                this.peers[name].connected = true;
                this.$nextTick(() => {
                    const el = this.$refs['remoteVideo_' + name];
                    if (el && el[0]) el[0].srcObject = e.streams[0];
                });
            };

            pc.onconnectionstatechange = () => {
                if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed') {
                    this.removePeer(name);
                }
            };

            return pc;
        },

        removePeer(name) {
            if (this.peers[name]) {
                if (this.peers[name].pc) this.peers[name].pc.close();
                this.$delete(this.peers, name);
            }
        },

        sendSignal(signalData) {
            const payload = {
                signal_data: signalData,
                sender_name: this.localUserName
            };
            const endpoint = this.isRoomMode ? `/call/${this.roomUuid}/signal` : '/customer/account/calls/signal';
            axios.post(endpoint, payload).catch(e => console.error('Signalling failed', e));
        },

        toggleMic() {
            this.isMicOn = !this.isMicOn;
            this.localStream.getAudioTracks().forEach(t => t.enabled = this.isMicOn);
        },

        toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            this.localStream.getVideoTracks().forEach(t => t.enabled = this.isCameraOn);
        },

        async toggleScreenShare() {
            if (!this.isSharingScreen) {
                try {
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const track = this.screenStream.getVideoTracks()[0];
                    Object.values(this.peers).forEach(p => {
                        const sender = p.pc.getSenders().find(s => s.track.kind === 'video');
                        if (sender) sender.replaceTrack(track);
                    });
                    this.$refs.localVideo.srcObject = this.screenStream;
                    this.isSharingScreen = true;
                    track.onended = () => this.toggleScreenShare();
                } catch (e) { console.error('Screen share error', e); }
            } else {
                this.screenStream.getTracks().forEach(t => t.stop());
                const track = this.localStream.getVideoTracks()[0];
                Object.values(this.peers).forEach(p => {
                    const sender = p.pc.getSenders().find(s => s.track.kind === 'video');
                    if (sender) sender.replaceTrack(track);
                });
                this.$refs.localVideo.srcObject = this.localStream;
                this.isSharingScreen = false;
            }
        },

        endCall() {
            this.sendSignal({ type: 'hangup' });
            this.cleanup();
        },

        cleanup() {
            if (this.localStream) this.localStream.getTracks().forEach(t => t.stop());
            Object.values(this.peers).forEach(p => p.pc.close());
            this.peers = {};
            this.isActive = false;
        }
    }
};
</script>

<style scoped>
.mirror { transform: scaleX(-1); }
</style>
