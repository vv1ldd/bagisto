<template>
    <div v-if="isActive" class="fixed inset-0 z-[10000] bg-black text-white p-4 md:p-8 flex flex-col justify-between font-sans overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/20 pb-4 relative z-50">
            <div>
                <div class="text-[8px] md:text-[10px] uppercase tracking-[0.3em] opacity-60 mb-1">
                    <span v-if="peerCount > 0">Групповая встреча</span>
                    <span v-else>Ожидание участников</span>
                </div>
                <h2 class="text-xl md:text-3xl font-black uppercase italic tracking-tighter">{{ isRoomMode ? 'Защищенная комната' : remoteUserName }}</h2>
            </div>
            <div class="flex items-center gap-4">
                <div v-if="peerCount > 0" class="bg-[#00FF41] text-black px-3 md:px-4 py-1 font-bold text-[10px] md:text-xs uppercase tracking-widest animate-pulse">
                    {{ peerCount + 1 }} в сети
                </div>
                <div class="flex -space-x-2">
                    <div v-for="name in peerNames" :key="name" 
                        class="w-6 h-6 rounded-full bg-zinc-800 border-2 border-black flex items-center justify-center text-[10px] font-bold uppercase transition-all"
                        :class="{'border-emerald-500': peers[name].connected}"
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
                <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group shadow-2xl">
                    <video ref="localVideo" autoplay muted playsinline class="w-full h-full object-cover mirror"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 rounded">
                        Вы ({{ localUserName }})
                    </div>
                </div>

                <!-- Remote Videos -->
                <div v-for="name in peerNames" :key="name" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group shadow-2xl">
                    <video :id="'video_' + name" :ref="'remoteVideo_' + name" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 transition-all group-hover:bg-[#7C45F5]/80 rounded">
                        {{ name }}
                    </div>
                    
                    <!-- Connection Overlay -->
                    <div v-if="!peers[name].connected" class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-md z-20 transition-all">
                        <div class="flex flex-col items-center gap-4">
                             <div class="w-10 h-10 border-4 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                             <span class="text-[10px] uppercase font-black tracking-[0.3em] text-[#7C45F5]">Установка связи...</span>
                        </div>
                    </div>
                    
                    <!-- Signal Loss Overlay -->
                    <div v-else-if="!peers[name].streamReady" class="absolute inset-0 flex items-center justify-center bg-zinc-900 z-20">
                         <div class="text-center">
                             <div class="text-2xl mb-2 opacity-40">🎥</div>
                             <span class="text-[8px] uppercase font-bold tracking-widest opacity-40">Ожидание потока...</span>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="peerCount === 0" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none">
                <div class="w-32 h-32 rounded-full bg-zinc-900/50 backdrop-blur-3xl border border-white/5 flex items-center justify-center mb-8 animate-pulse shadow-2xl">
                     <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center text-xl">👤</div>
                        <div class="w-12 h-12 rounded-full bg-[#7C45F5] border-2 border-[#7C45F5]/50 flex items-center justify-center text-xl shadow-lg">👥</div>
                     </div>
                </div>
                <div class="text-center px-8">
                    <h3 class="text-sm font-black uppercase tracking-[0.3em] text-white mb-2">Ожидание участников</h3>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-zinc-500 max-w-xs leading-relaxed">
                        Пригласите коллег, отправив им ссылку на эту комнату.
                    </p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="relative z-50 flex justify-center gap-4 py-4 mt-auto">
             <div class="flex justify-center gap-3 md:gap-6 pb-4 bg-black/60 backdrop-blur-2xl px-6 md:px-12 py-4 md:py-6 rounded-full border border-white/10 mx-auto w-max shadow-2xl">
                <button @click="toggleMic" :class="[isMicOn ? 'bg-white text-black' : 'bg-red-500/20 text-red-500 border-red-500/40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10 group">
                    <span class="text-[8px] md:text-[10px] font-black uppercase group-hover:tracking-widest transition-all">{{ isMicOn ? 'Mic On' : 'Muted' }}</span>
                </button>
                
                <button @click="endCall" 
                    class="h-12 px-8 md:h-16 md:px-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-black uppercase text-xs md:text-sm tracking-widest transition-all shadow-xl shadow-red-500/30 active:scale-95">
                    Выйти
                </button>
 
                <button @click="toggleScreenShare" :class="[isSharingScreen ? 'bg-[#00FF41] text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10 group">
                    <span class="text-[8px] md:text-[10px] font-black uppercase group-hover:tracking-widest transition-all">{{ isSharingScreen ? 'Stop' : 'Share' }}</span>
                </button>
 
                <button @click="toggleCamera" :class="[isCameraOn ? 'bg-white text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-12 w-12 md:h-16 md:w-16 rounded-full hover:scale-105 transition-all flex items-center justify-center border border-white/10 group">
                    <span class="text-[8px] md:text-[10px] font-black uppercase group-hover:tracking-widest transition-all">{{ isCameraOn ? 'Cam On' : 'Cam Off' }}</span>
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
            peers: {}, // { name: { pc, stream, connected, streamReady, iceQueue } }
            isMicOn: true,
            isCameraOn: true,
            isSharingScreen: false,
            screenStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' },
                ]
            },
            presenceInterval: null,
            retryInterval: null
        };
    },

    computed: {
        peerNames() {
            return Object.keys(this.peers);
        },
        peerCount() {
            return this.peerNames.length;
        },
        gridClass() {
            const count = this.peerCount + 1;
            if (count === 1) return 'grid-cols-1';
            if (count === 2) return 'grid-cols-1 md:grid-cols-2';
            if (count <= 4) return 'grid-cols-2';
            return 'grid-cols-2 lg:grid-cols-3';
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

        // Global retry interval to fix missing video elements
        this.retryInterval = setInterval(() => this.rebindVideos(), 3000);
    },

    beforeUnmount() {
        this.stopPresence();
        if (this.retryInterval) clearInterval(this.retryInterval);
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
                this.startPresence();
            }
        },

        startPresence() {
            this.stopPresence();
            this.sendSignal({ type: 'presence' });
            this.presenceInterval = setInterval(() => {
                if (this.isActive) this.sendSignal({ type: 'presence' });
            }, 10000);
        },

        stopPresence() {
            if (this.presenceInterval) clearInterval(this.presenceInterval);
        },

        async setupLocalMedia() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                this.$nextTick(() => { 
                    if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
                });
            } catch (e) { 
                console.warn('Room: Media access denied', e); 
            }
        },

        handleSignal(data) {
            const signal = data.signal_data;
            const senderName = data.sender_name;
            
            if (!senderName || senderName === this.localUserName) return;
            if (signal.target && signal.target !== this.localUserName) return;

            console.log(`Room: [${signal.type}] from ${senderName}`);

            if (signal.type === 'presence') {
                const isInitiator = this.localUserName.toLowerCase() < senderName.toLowerCase();
                if (isInitiator && !this.peers[senderName]) {
                    this.initiateConnection(senderName);
                } else if (!this.peers[senderName]) {
                    this.$set(this.peers, senderName, { pc: null, stream: null, connected: false, streamReady: false, iceQueue: [] });
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
            const pc = this.createPeerConnection(name);
            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);
            this.sendSignal({ type: 'offer', sdp: offer.sdp, target: name });
        },

        async handleOffer(name, signal) {
            const pc = this.createPeerConnection(name);
            await pc.setRemoteDescription(new RTCSessionDescription(signal));
            
            // Process queued candidates
            const peer = this.peers[name];
            while (peer.iceQueue.length > 0) {
                const cand = peer.iceQueue.shift();
                await pc.addIceCandidate(new RTCIceCandidate(cand));
            }

            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            this.sendSignal({ type: 'answer', sdp: answer.sdp, target: name });
        },

        async handleAnswer(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                await peer.pc.setRemoteDescription(new RTCSessionDescription(signal));
                while (peer.iceQueue.length > 0) {
                    const cand = peer.iceQueue.shift();
                    await peer.pc.addIceCandidate(new RTCIceCandidate(cand));
                }
            }
        },

        async handleCandidate(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                if (peer.pc.remoteDescription) {
                    await peer.pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
                } else {
                    peer.iceQueue.push(signal.candidate);
                }
            }
        },

        createPeerConnection(name) {
            if (this.peers[name]?.pc) return this.peers[name].pc;

            const pc = new RTCPeerConnection(this.configuration);
            this.$set(this.peers, name, { 
                pc, 
                stream: null, 
                connected: false, 
                streamReady: false, 
                iceQueue: this.peers[name]?.iceQueue || [] 
            });

            if (this.localStream) {
                this.localStream.getTracks().forEach(t => pc.addTrack(t, this.localStream));
            }

            pc.onicecandidate = (e) => {
                if (e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: name });
            };

            pc.ontrack = (e) => {
                console.log(`WebRTC: Track received from ${name}`);
                const stream = e.streams[0];
                this.$set(this.peers[name], 'stream', stream);
                this.$set(this.peers[name], 'streamReady', true);
                this.$set(this.peers[name], 'connected', true);
                
                this.attachStreamToVideo(name, stream);
            };

            pc.onconnectionstatechange = () => {
                console.log(`WebRTC: State [${name}] -> ${pc.connectionState}`);
                if (pc.connectionState === 'connected') {
                    this.$set(this.peers[name], 'connected', true);
                } else if (['disconnected', 'failed', 'closed'].includes(pc.connectionState)) {
                    this.removePeer(name);
                }
            };

            return pc;
        },

        attachStreamToVideo(name, stream) {
            this.$nextTick(() => {
                const el = document.getElementById('video_' + name) || (this.$refs['remoteVideo_' + name] ? this.$refs['remoteVideo_' + name][0] : null);
                if (el) {
                    if (el.srcObject !== stream) {
                        el.srcObject = stream;
                        console.log(`WebRTC: Stream attached to video_${name}`);
                    }
                } else {
                    console.warn(`WebRTC: Video element for ${name} not found yet`);
                }
            });
        },

        rebindVideos() {
            if (!this.isActive) return;
            Object.keys(this.peers).forEach(name => {
                const p = this.peers[name];
                if (p.stream && p.connected) {
                    this.attachStreamToVideo(name, p.stream);
                }
            });
        },

        removePeer(name) {
            if (this.peers[name]) {
                if (this.peers[name].pc) this.peers[name].pc.close();
                this.$delete(this.peers, name);
            }
        },

        sendSignal(signalData) {
            const payload = { signal_data: signalData, sender_name: this.localUserName };
            const endpoint = this.isRoomMode ? `/call/${this.roomUuid}/signal` : '/customer/account/calls/signal';
            axios.post(endpoint, payload).catch(e => {});
        },

        toggleMic() {
            this.isMicOn = !this.isMicOn;
            if (this.localStream) this.localStream.getAudioTracks().forEach(t => t.enabled = this.isMicOn);
        },

        toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            if (this.localStream) this.localStream.getVideoTracks().forEach(t => t.enabled = this.isCameraOn);
        },

        async toggleScreenShare() {
            try {
                if (!this.isSharingScreen) {
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const track = this.screenStream.getVideoTracks()[0];
                    Object.values(this.peers).forEach(p => {
                        const sender = p.pc?.getSenders().find(s => s.track?.kind === 'video');
                        if (sender) sender.replaceTrack(track);
                    });
                    this.$refs.localVideo.srcObject = this.screenStream;
                    this.isSharingScreen = true;
                    track.onended = () => this.toggleScreenShare();
                } else {
                    if (this.screenStream) this.screenStream.getTracks().forEach(t => t.stop());
                    const track = this.localStream?.getVideoTracks()[0];
                    Object.values(this.peers).forEach(p => {
                        const sender = p.pc?.getSenders().find(s => s.track?.kind === 'video');
                        if (sender && track) sender.replaceTrack(track);
                    });
                    this.$refs.localVideo.srcObject = this.localStream;
                    this.isSharingScreen = false;
                }
            } catch (e) { console.error(e); }
        },

        endCall() {
            this.sendSignal({ type: 'hangup' });
            this.cleanup();
        },

        cleanup() {
            this.stopPresence();
            if (this.localStream) this.localStream.getTracks().forEach(t => t.stop());
            Object.values(this.peers).forEach(p => p.pc?.close());
            this.peers = {};
            this.isActive = false;
            window.location.reload();
        }
    }
};
</script>

<style scoped>
.mirror { transform: scaleX(-1); }
</style>
