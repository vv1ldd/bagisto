<template>
    <div v-if="isActive" 
         ref="overlayRoot"
         @mousemove="userActivity"
         @touchstart="handleGlobalTouch"
         @click="toggleControls"
         class="fixed inset-0 z-[10000] bg-black text-white font-sans overflow-hidden transition-all duration-300 touch-none select-none">
        
        <!-- Proximity Dimmer 🕵️‍♂️🔇🚀 -->
        <div v-if="isProximityClose" @click="dismissProximity" class="absolute inset-0 bg-black z-[20000] flex flex-col items-center justify-center pointer-events-auto">
            <div class="w-16 h-16 rounded-full border-4 border-white/10 border-t-white/60 animate-spin mb-4"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white/40">Режим разговора (Затемнение)</p>
        </div>

        <div class="absolute inset-0 bg-zinc-950">
            <!-- 1-on-1 Mode -->
            <div v-if="peerCount === 1" class="w-full h-full relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center overflow-hidden touch-none"
                     @touchstart="handleTouchStart"
                     @touchmove="handleTouchMove">
                    
                    <div class="w-full h-full relative overflow-hidden" v-show="!isFocusedOnSelf">
                         <video :id="'video_' + activePeerId" 
                                autoplay playsinline 
                                :style="zoomStyle"
                                class="w-full h-full object-cover pointer-events-none"></video>
                    </div>

                    <div class="w-full h-full relative overflow-hidden" v-show="isFocusedOnSelf">
                        <video ref="localVideoMain" 
                               autoplay muted playsinline 
                               :style="zoomStyle"
                               class="w-full h-full object-cover mirror"></video>
                    </div>
                </div>

                <!-- Handshake Pulse -->
                <div v-if="activePeerId && peers[activePeerId]?.isReady && !peers[activePeerId]?.connected" 
                    class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-950/60 backdrop-blur-md z-30">
                    <div class="w-12 h-12 bg-[#7C45F5] border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] animate-spin-robust rotate-45 mb-4"></div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-white/90">Соединение...</h3>
                </div>
            </div>

            <!-- Call Ended Overlay 🏁🛑🚀 -->
            <div v-if="isCallEnded" class="absolute inset-0 z-[150] flex flex-col items-center justify-center bg-zinc-950 text-white animate-fade-in">
                <div class="relative z-10 flex flex-col items-center max-w-sm text-center px-8">
                    <div class="w-24 h-24 bg-zinc-900 border-4 border-zinc-500 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center mb-10 rotate-3">
                         <span class="text-4xl">🛑</span>
                    </div>
                    <h2 class="text-3xl font-black uppercase tracking-[0.15em] mb-4">Звонок окончен</h2>
                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.2em] mb-12">
                        Сессия была завершена. Вы можете вернуться на главную или подождать.
                    </p>
                    <button @click="location.reload()" 
                            class="w-full px-8 py-5 bg-[#7C45F5] text-white text-[11px] font-black uppercase tracking-[0.3em] border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all">
                        На главную
                    </button>
                </div>
            </div>

            <!-- Grid/Multi Mode (Wait/Group) -->
            <div v-else-if="peerCount > 1" class="w-full h-full relative p-2 md:p-4 transition-all duration-500">
                <div :class="gridClass" class="grid w-full h-full gap-2 md:gap-4 transition-all duration-500">
                    <div v-for="id in peerIds" :key="id" 
                        class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center">
                        <video :id="'video_' + id" autoplay playsinline class="w-full h-full object-cover"></video>
                    </div>
                </div>
            </div>

            <!-- Start Conversation Overlay -->
            <div v-if="showStartButton" class="absolute inset-0 z-[120] flex flex-col items-center justify-center bg-zinc-950/80 backdrop-blur-xl">
                <button @click="startConversation" class="group relative flex flex-col items-center gap-6 p-12 rounded-full hover:scale-105 active:scale-95 transition-all">
                    <div class="w-24 h-24 rounded-full bg-[#7C45F5] flex items-center justify-center shadow-[0_0_50px_rgba(124,69,245,0.4)] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black uppercase tracking-[0.3em] text-white">Начать</span>
                </button>
            </div>

            <!-- Empty/Wait State -->
            <div v-show="(!activePeerId || !peers[activePeerId]?.isReady) && !isCallEnded && !showStartButton" class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-950/20">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <video ref="localVideoWaiting" autoplay muted playsinline class="absolute inset-0 w-full h-full blur-xl opacity-40 object-cover"></video>
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-[#7C45F5] border-4 border-zinc-900 shadow-[6px_6px_0px_0px_rgba(24,24,27,1)] animate-spin mb-8 mx-auto rotate-45"></div>
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-white/90">Ожидание собеседника...</h3>
                </div>
            </div>

            <!-- Toolbar -->
            <div v-show="!isCallEnded && !showStartButton" class="absolute bottom-8 left-0 right-0 z-[200] transition-all duration-700 pb-4"
                 :class="{'opacity-0 translate-y-12': !controlsVisible}">
                <div class="flex flex-wrap items-center justify-center gap-4 p-4 max-w-2xl mx-auto pointer-events-auto">
                    <button @click.stop="toggleCamera" :class="[isCameraOn ? 'bg-zinc-800' : 'bg-red-500']" class="h-14 w-14 flex items-center justify-center border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button @click.stop="toggleMic" :class="[isMicOn ? 'bg-[#7C45F5]' : 'bg-red-500']" class="h-14 w-14 flex items-center justify-center border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4" />
                        </svg>
                    </button>
                    <button @click.stop="toggleScreenShare" :class="[isSharingScreen ? 'bg-[#7C45F5]' : 'bg-zinc-800']" class="h-14 w-14 flex items-center justify-center border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button @click.stop="copyInviteLink" class="h-14 w-14 bg-zinc-800 flex flex-col items-center justify-center border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all group relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span v-show="showCopiedTooltip" class="absolute -top-12 left-1/2 -translate-x-1/2 px-3 py-1 bg-green-500 text-[10px] font-black uppercase tracking-widest border-2 border-zinc-900 animate-bounce">OK!</span>
                    </button>
                    <button @click.stop="endCall" class="h-14 w-14 bg-red-500 flex items-center justify-center border-4 border-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            isActive: false,
            localStream: null,
            localUserName: '',
            localHash: '',
            roomUuid: null,
            peers: {},
            focusedPeerId: null,
            localFingerprint: null,
            signalingState: 'connecting',
            isMicOn: true,
            isCameraOn: true,
            isJoined: false,
            showStartButton: true,
            isLocalReady: false,
            isCallEnded: false,
            controlsVisible: true,
            isProximityClose: false,
            cameraFacing: 'user',
            zoomCapabilities: null,
            cameraZoom: 1,
            initialCameraZoom: 1,
            panX: 0, 
            panY: 0,
            zoomLevel: 1,
            isInteracting: false,
            interactionTimeout: null,
            controlsVisible: true,
            controlsTimeout: null,
            luminanceInterval: null,
            luminanceCooldown: 0,
            proximityCanvas: null,
            proximityCtx: null,
            isFullscreen: false,
            sessionUniqueId: Math.random().toString(36).substring(2, 10) + Date.now().toString(36),
            lastToggleTime: 0,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.meanly.ru:3478' },
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun.miwifi.com:3478' }
                ],
                iceCandidatePoolSize: 2
            },
            isSharingScreen: false,
            showCopiedTooltip: false
        };
    },
    computed: {
        peerIds() { return Object.keys(this.peers).sort(); },
        peerCount() { return this.peerIds.length; },
        isFocusedOnSelf() { return this.focusedPeerId === 'local'; },
        zoomStyle() {
            return {
                transform: `scale(${this.zoomLevel}) translate3d(${this.panX}px, ${this.panY}px, 0)`,
                transition: 'transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1)'
            };
        },
        activePeerId() {
            // Find the most recently seen ready peer that is not us 🕵️‍♂️🎯🚀
            return this.peerIds
                .filter(id => this.peers[id]?.isReady)
                .sort((a, b) => (this.peers[b]?.lastSeen || 0) - (this.peers[a]?.lastSeen || 0))[0] || null;
        }
    },
    mounted() {
        this.$emitter.on('join-room', (payload) => {
            this.joinRoom(payload.uuid, payload.userName, payload.hash, payload.remoteName);
        });
        this.cleanupInterval = setInterval(() => this.cleanupStalePeers(), 5000);
    },
    methods: {
        async startConversation() {
            this.showStartButton = false;
            this.isLocalReady = true;
            try {
                await axios.post('/account/calls/ready', {
                    room_uuid: this.roomUuid,
                    session_id: this.sessionUniqueId,
                    is_ready: true,
                    _token: document.querySelector('meta[name="csrf-token"]')?.content
                });
            } catch (e) {
                console.error('Room: Ready relay failed', e);
            }
        },

        async copyInviteLink() {
            const url = `${window.location.origin}/meetings/join/${this.roomUuid}`;
            try {
                await navigator.clipboard.writeText(url);
                this.showCopiedTooltip = true;
                setTimeout(() => { this.showCopiedTooltip = false; }, 2000);
            } catch (e) {
                // Fallback for older browsers or insecure contexts
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                this.showCopiedTooltip = true;
                setTimeout(() => { this.showCopiedTooltip = false; }, 2000);
            }
        },

        async joinRoom(uuid, userName, hash, remoteName) {
            this.roomUuid = uuid;
            this.localUserName = userName;
            this.localHash = hash || userName;
            this.isActive = true;
            await this.setupLocalMedia();
            this.subscribeToChannels();
            this.startPresence();
        },

        subscribeToChannels() {
            if (window.Echo && this.roomUuid) {
                const channelName = `call.${this.roomUuid}`;
                window.Echo.channel(channelName)
                    .stopListening('.call-signal')
                    .listen('.call-signal', (data) => this.handleSignal(data));
                console.log(`WebRTC: Subscribed to ${channelName}`);
            }
        },

        startPresence() {
            this.sendSignal({ type: 'presence', is_ready: this.isLocalReady });
            this.presenceInterval = setInterval(() => {
                if (this.isActive) this.sendSignal({ type: 'presence', is_ready: this.isLocalReady });
            }, 3000);
        },

        stopPresence() { if (this.presenceInterval) clearInterval(this.presenceInterval); },

        async setupLocalMedia() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.cameraFacing, width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: true
                });
                if (this.$refs.localVideoWaiting) this.$refs.localVideoWaiting.srcObject = this.localStream;
                if (this.$refs.localVideoMain) this.$refs.localVideoMain.srcObject = this.localStream;
                
                // AUTO-START SENSORS 🕵️‍♂️🚀
                this.detectZoomCapabilities();
                this.setupProximitySensor();
            } catch (e) { console.error('Media failed', e); }
        },

        handleSignal(payload) {
            const signal = payload.signal_data;
            const peerKey = signal.sender_session_id;
            const fromUserId = payload.from_user_id; // Added in v2 payload 🕵️‍♂️📈🚀
            const now = Date.now();

            if (signal.sessionId && signal.sessionId !== this.roomUuid) return;
            if (signal.timestamp && (now - signal.timestamp > 20000)) return;
            if (!peerKey || peerKey === this.sessionUniqueId) return;

            // Session De-duplication (The Ghost Slayer) 👻🛑
            if (fromUserId) {
                Object.keys(this.peers).forEach(id => {
                    if (id !== peerKey && this.peers[id].user_id === fromUserId) {
                        console.log(`WebRTC: Removing ghost session ${id} for user ${fromUserId}`);
                        if (this.peers[id].pc) this.peers[id].pc.close();
                        delete this.peers[id];
                    }
                });
            }

            if (signal.type === 'presence') {
                if (!this.peers[peerKey]) {
                    this.peers = { ...this.peers, [peerKey]: { 
                        name: signal.sender_name, user_id: fromUserId, pc: null, stream: null, connected: false,
                        iceQueue: [], isReady: signal.is_ready, lastSeen: now, negotiating: false
                    }};
                } else {
                    this.peers[peerKey].lastSeen = now;
                    this.peers[peerKey].isReady = signal.is_ready;
                }
                return;
            }

            if (signal.type === 'session_started') {
                const isInitiator = (signal.initiator_session_id === this.sessionUniqueId);
                this.isJoined = true;
                if (isInitiator) {
                    signal.participants.forEach(p => {
                        if (p.session_id !== this.sessionUniqueId) this.initiateNegotiation(p.session_id, p.name);
                    });
                }
                return;
            }

            if (['offer', 'answer', 'candidate', 'hangup', 'poke'].includes(signal.type)) {
                if (signal.type === 'offer') this.handleOffer(peerKey, signal);
                else if (signal.type === 'answer') this.handleAnswer(peerKey, signal);
                else if (signal.type === 'candidate') this.handleCandidate(peerKey, signal);
                else if (signal.type === 'hangup') this.cleanup('Собеседник вышел');
                else if (signal.type === 'poke') this.initiateNegotiation(peerKey, signal.sender_name);
            }
        },

        initiateNegotiation(peerKey, name) {
            const peer = this.peers[peerKey];
            if (!peer || peer.negotiating) return;
            peer.negotiating = true;
            const pc = this.createPeerConnection(peerKey, name);
            this.localStream.getTracks().forEach(t => pc.addTrack(t, this.localStream));
            this.startWatchdog(peerKey);
        },

        async handleOffer(id, signal) {
            const peer = this.peers[id];
            if (!peer) return;
            const pc = this.createPeerConnection(id, peer.name);
            await pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp: signal.sdp }));
            this.localStream.getTracks().forEach(t => pc.addTrack(t, this.localStream));
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            this.sendSignal({ type: 'answer', sdp: answer.sdp, target: id });
            while(peer.iceQueue.length) pc.addIceCandidate(new RTCIceCandidate(peer.iceQueue.shift()));
        },

        async handleAnswer(id, signal) {
            const pc = this.peers[id]?.pc;
            if (pc) await pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp: signal.sdp }));
        },

        async handleCandidate(id, signal) {
            const pc = this.peers[id]?.pc;
            if (pc?.remoteDescription) await pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
            else this.peers[id]?.iceQueue.push(signal.candidate);
        },

        createPeerConnection(id, name) {
            const pc = new RTCPeerConnection(this.configuration);
            this.peers[id].pc = pc;
            pc.onicecandidate = (e) => { if(e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: id }); };
            pc.ontrack = (e) => { 
                this.peers[id].stream = e.streams[0];
                this.$nextTick(() => {
                    const el = document.getElementById('video_' + id);
                    if (el) el.srcObject = e.streams[0];
                });
            };
            pc.onconnectionstatechange = () => { if(pc.connectionState === 'connected') this.peers[id].connected = true; };
            return pc;
        },

        sendSignal(data) {
            axios.post('/account/calls/signal', {
                signal_data: { ...data, sessionId: this.roomUuid, sender_session_id: this.sessionUniqueId, sender_name: this.localUserName, timestamp: Date.now() }
            });
        },

        cleanupStalePeers() {
            const now = Date.now();
            Object.keys(this.peers).forEach(id => { if (now - this.peers[id].lastSeen > 12000) delete this.peers[id]; });
        },

        cleanup(msg) {
            this.stopPresence();
            Object.values(this.peers).forEach(p => p.pc?.close());
            this.isActive = false;
            if (msg) alert(msg);
            location.reload();
        },

        toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            this.localStream.getVideoTracks().forEach(t => t.enabled = this.isCameraOn);
        },
        toggleMic() {
            this.isMicOn = !this.isMicOn;
            this.localStream.getAudioTracks().forEach(t => t.enabled = this.isMicOn);
        },
        async toggleScreenShare() {
            try {
                if (!this.isSharingScreen) {
                    const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const screenTrack = screenStream.getVideoTracks()[0];
                    
                    Object.values(this.peers).forEach(peer => {
                        if (peer.pc) {
                            const sender = peer.pc.getSenders().find(s => s.track.kind === 'video');
                            if (sender) sender.replaceTrack(screenTrack);
                        }
                    });

                    screenTrack.onended = () => this.toggleScreenShare(); // Auto revert
                    this.isSharingScreen = true;
                    if (this.$refs.localVideoMain) this.$refs.localVideoMain.srcObject = screenStream;
                } else {
                    const videoTrack = this.localStream.getVideoTracks()[0];
                    Object.values(this.peers).forEach(peer => {
                        if (peer.pc) {
                            const sender = peer.pc.getSenders().find(s => s.track.kind === 'video');
                            if (sender) sender.replaceTrack(videoTrack);
                        }
                    });
                    this.isSharingScreen = false;
                    if (this.$refs.localVideoMain) this.$refs.localVideoMain.srcObject = this.localStream;
                }
            } catch (e) { console.error('Screen share failed', e); }
        },
        endCall() { this.sendSignal({ type: 'hangup' }); this.cleanup(); },
        normalizeSDP(sdp) {
            if (!sdp) return '';
            let lines = sdp.split(/\r?\n/).map(line => line.trim()).filter(l => l.length > 0);
            let nl = [];
            for (let l of lines) {
                nl.push(l);
                if (l.startsWith('m=video')) {
                    nl.push('b=AS:2500');
                    nl.push('b=TIAS:2500000');
                }
            }
            return nl.join('\r\n') + '\r\n';
        },

        userActivity() {
            this.controlsVisible = true;
            if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
            this.controlsTimeout = setTimeout(() => {
                this.controlsVisible = false;
            }, 5000);
        },

        setupProximitySensor() {
            if (this.luminanceInterval) clearInterval(this.luminanceInterval);
            this.proximityCanvas = document.createElement('canvas');
            this.proximityCanvas.width = 10;
            this.proximityCanvas.height = 10;
            this.proximityCtx = this.proximityCanvas.getContext('2d', { willReadFrequently: true });
            this.luminanceInterval = setInterval(() => this.analyzeLuminance(), 1000);
        },

        analyzeLuminance() {
            try {
                if (Date.now() < this.luminanceCooldown) return;
                if (!this.isActive || !this.isCameraOn) return;
                const video = this.$refs.localVideoMain || this.$refs.localVideoWaiting;
                if (!video || video.paused || video.readyState < 2) return;

                this.proximityCtx.drawImage(video, 0, 0, 10, 10);
                const data = this.proximityCtx.getImageData(0, 0, 10, 10).data;
                let totalLuminance = 0;
                for (let i = 0; i < data.length; i += 4) {
                    totalLuminance += (0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2]);
                }
                const isDark = (totalLuminance / 100) < 20;
                this.isProximityClose = isDark;
            } catch (e) {}
        },

        handleGlobalTouch() {
            this.userActivity();
        },

        dismissProximity() {
            this.isProximityClose = false;
            this.luminanceCooldown = Date.now() + 30000;
        },

        detectZoomCapabilities() {
            try {
                const videoTrack = this.localStream?.getVideoTracks()[0];
                if (videoTrack?.getCapabilities) {
                    const caps = videoTrack.getCapabilities();
                    if (caps.zoom) this.zoomCapabilities = caps.zoom;
                }
            } catch (e) {}
        },

        async applyCameraZoom(value) {
            try {
                const videoTrack = this.localStream?.getVideoTracks()[0];
                if (videoTrack && this.zoomCapabilities) {
                    await videoTrack.applyConstraints({ advanced: [{ zoom: parseFloat(value) }] });
                    this.cameraZoom = value;
                }
            } catch (e) {}
        },

        handleTouchStart(e) {
            this.lastToggleTime = Date.now();
            if (e.touches.length === 2) {
                this.initialDist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                this.initialZoom = this.zoomLevel;
                this.initialCameraZoom = this.cameraZoom;
            }
        },

        handleTouchMove(e) {
            if (e.touches.length === 2 && this.initialDist > 0) {
                const dist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                const scale = dist / this.initialDist;
                
                if (this.zoomCapabilities) {
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.initialCameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                } else {
                    this.zoomLevel = Math.max(1, Math.min(5, this.initialZoom * scale));
                }
            }
        },

        startWatchdog(id) {
            const peer = this.peers[id];
            if (!peer || peer.watchdog) return;
            peer.watchdog = setTimeout(() => {
                if (this.peers[id] && !this.peers[id].connected) {
                    this.sendSignal({ type: 'poke', target: id });
                    peer.watchdog = null;
                    this.startWatchdog(id);
                }
            }, 6000);
        },
    }
};
</script>
