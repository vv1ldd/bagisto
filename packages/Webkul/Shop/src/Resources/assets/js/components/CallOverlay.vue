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
                <div v-for="name in peerNames" :key="name" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group">
                    <video :ref="'remoteVideo_' + name" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 transition-all group-hover:bg-[#7C45F5]/80">
                        {{ name }}
                    </div>
                    <div v-if="!peers[name] || !peers[name].connected" class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-20">
                        <div class="flex flex-col items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-[#00FF41] animate-ping"></div>
                             <span class="text-[8px] uppercase font-bold tracking-[0.2em] opacity-60">Соединение...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="peerCount === 0" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none">
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
            },
            presenceInterval: null
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
            return 'grid-cols-2 md:grid-cols-3';
        }
    },

    mounted() {
        this.$emitter.on('join-room', (payload) => {
            if (this.isActive) return;
            this.joinRoom(payload.uuid, payload.userName);
        });

        // Direct call listener (optional for room mode, but keeps it unified)
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

    beforeUnmount() {
        this.stopPresence();
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
                console.log(`Room: Subscribing to call.${uuid}`);
                window.Echo.channel(`call.${uuid}`).listen('.call-signal', (data) => this.handleSignal(data));
                
                // Start periodic presence broadcasting to help catch late joiners or missed signals
                this.startPresence();
            }
        },

        startPresence() {
            this.stopPresence();
            // Initial presence
            this.sendSignal({ type: 'presence' });
            // Periodic presence every 10 seconds if alone (or for robustness)
            this.presenceInterval = setInterval(() => {
                if (this.isActive) this.sendSignal({ type: 'presence' });
            }, 10000);
        },

        stopPresence() {
            if (this.presenceInterval) {
                clearInterval(this.presenceInterval);
                this.presenceInterval = null;
            }
        },

        async setupLocalMedia() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                this.$nextTick(() => { 
                    if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
                });
            } catch (e) { 
                console.warn('Room: Media access denied or not available', e); 
                // Don't die, just continue without stream
            }
        },

        handleSignal(data) {
            const signal = data.signal_data;
            let senderName = data.sender_name || (signal.caller_name);
            
            // For direct calls, sender_name might be missing but from_user_id is there
            if (!senderName && data.from_user_id) senderName = `User ${data.from_user_id}`;
            
            if (!senderName || senderName === this.localUserName) return;

            // Targeting logic
            if (signal.target && signal.target !== this.localUserName) return;

            console.log(`Room: [${signal.type}] from ${senderName}`, signal);

            if (signal.type === 'presence') {
                const shouldIInitiate = this.localUserName.toLowerCase() < senderName.toLowerCase();
                if (shouldIInitiate && !this.peers[senderName]) {
                    this.initiateConnection(senderName);
                } else if (!this.peers[senderName]) {
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
            
            const sanitizedSdp = this.sanitizeSDP(signal.sdp);
            await pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp: sanitizedSdp }));
            
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            this.sendSignal({ type: 'answer', sdp: answer.sdp, target: name });
        },

        async handleAnswer(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                const sanitizedSdp = this.sanitizeSDP(signal.sdp);
                await peer.pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: sanitizedSdp }));
            }
        },

        async handleCandidate(name, signal) {
            const peer = this.peers[name];
            if (peer && peer.pc) {
                try {
                    await peer.pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
                } catch (e) { console.warn('WebRTC: Candidate error', e); }
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
                console.log(`WebRTC: Remote track received from ${name}`);
                this.$set(this.peers[name], 'stream', e.streams[0]);
                this.$set(this.peers[name], 'connected', true);
                
                this.$nextTick(() => {
                    const el = this.$refs['remoteVideo_' + name];
                    if (el && el[0]) {
                        el[0].srcObject = e.streams[0];
                    } else {
                        console.warn(`WebRTC: Video ref not found for ${name}`);
                    }
                });
            };

            pc.onconnectionstatechange = () => {
                console.log(`WebRTC: Connection state with ${name}: ${pc.connectionState}`);
                if (pc.connectionState === 'connected') {
                    this.$set(this.peers[name], 'connected', true);
                } else if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed') {
                    this.removePeer(name);
                }
            };

            return pc;
        },

        removePeer(name) {
            if (this.peers[name]) {
                console.log(`Room: Removing peer ${name}`);
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

        sanitizeSDP(sdp) {
            if (!sdp) return '';
            let clean = sdp.replace(/\\n/g, '\n').replace(/\\r/g, '');
            return clean.split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .join('\r\n') + '\r\n';
        },

        toggleMic() {
            this.isMicOn = !this.isMicOn;
            if (this.localStream) {
                this.localStream.getAudioTracks().forEach(t => t.enabled = this.isMicOn);
            }
        },

        toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            if (this.localStream) {
                this.localStream.getVideoTracks().forEach(t => t.enabled = this.isCameraOn);
            }
        },

        async toggleScreenShare() {
            if (!this.isSharingScreen) {
                try {
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const track = this.screenStream.getVideoTracks()[0];
                    Object.values(this.peers).forEach(p => {
                        if (p.pc) {
                            const sender = p.pc.getSenders().find(s => s.track && s.track.kind === 'video');
                            if (sender) sender.replaceTrack(track);
                        }
                    });
                    this.$refs.localVideo.srcObject = this.screenStream;
                    this.isSharingScreen = true;
                    track.onended = () => this.toggleScreenShare();
                } catch (e) { console.error('Screen share error', e); }
            } else {
                if (this.screenStream) this.screenStream.getTracks().forEach(t => t.stop());
                const track = this.localStream?.getVideoTracks()[0];
                Object.values(this.peers).forEach(p => {
                    if (p.pc && track) {
                        const sender = p.pc.getSenders().find(s => s.track && s.track.kind === 'video');
                        if (sender) sender.replaceTrack(track);
                    }
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
            this.stopPresence();
            if (this.localStream) this.localStream.getTracks().forEach(t => t.stop());
            Object.values(this.peers).forEach(p => {
                if (p.pc) p.pc.close();
            });
            this.peers = {};
            this.isActive = false;
            this.isSharingScreen = false;
            window.location.reload(); // Optional: hard reload to clear all Echo listeners if needed
        }
    }
};
</script>

<style scoped>
.mirror { transform: scaleX(-1); }
</style>
