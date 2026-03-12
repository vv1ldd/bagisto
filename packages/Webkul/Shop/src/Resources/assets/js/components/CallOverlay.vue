<template>
    <div v-if="isActive" class="fixed inset-0 z-[10000] bg-black text-white p-8 flex flex-col justify-between font-sans">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-white/20 pb-6">
            <div>
                <div class="text-[10px] uppercase tracking-[0.3em] opacity-60 mb-2">
                    <span v-if="isConnected">В эфире</span>
                    <span v-else-if="didIInitiate">{{ isRemoteAccepted ? 'Соединение...' : 'Исходящий вызов' }}</span>
                    <span v-else>Входящий вызов</span>
                </div>
                <h2 class="text-4xl font-black uppercase italic tracking-tighter">{{ displayUserName }}</h2>
            </div>
            <div v-if="isConnected" class="bg-[#00FF41] text-black px-4 py-1 font-bold text-xs uppercase tracking-widest animate-fade-in">
                Live
            </div>
        </div>

        <!-- Video Grid -->
        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
            <!-- Remote Video -->
            <div class="relative bg-zinc-900 border border-white/10 aspect-video overflow-hidden">
                <video ref="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <div class="absolute bottom-4 left-4 bg-black px-3 py-1 text-[10px] font-bold border border-white/20 uppercase">
                    {{ remoteUserName || 'Remote' }}
                </div>
                <div v-if="!isConnected" class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-900 z-10">
                    <!-- Avatar placeholder -->
                    <div class="w-24 h-24 rounded-full bg-zinc-800 flex items-center justify-center mb-6 relative">
                        <span class="text-3xl uppercase font-black">{{ (displayUserName || 'U')[0] }}</span>
                        <!-- Ripple effect for calling out state -->
                        <div v-if="didIInitiate" class="absolute inset-0 rounded-full border-2 border-[#00FF41] animate-[ping_2s_cubic-bezier(0,0,0.2,1)_infinite] opacity-20"></div>
                    </div>
                    
                    <span class="text-lg font-bold uppercase tracking-widest mb-2">{{ displayUserName }}</span>
                    
                    <span v-if="didIInitiate" class="text-xs uppercase tracking-[0.2em] text-[#00FF41]">
                        {{ isRemoteAccepted ? 'Установка соединения...' : 'Ожидание ответа...' }}
                    </span>
                    <span v-else-if="isIncoming && !hasAccepted" class="text-xs uppercase tracking-[0.2em] text-white/50 animate-pulse">Нажмите принять, чтобы начать сеанс</span>
                    <span v-else class="text-xs uppercase tracking-[0.2em] text-[#00FF41]">Установка соединения...</span>
                </div>
            </div>

            <!-- Local Video -->
            <div class="relative bg-zinc-800 border border-white/10 aspect-video overflow-hidden">
                <video ref="localVideo" autoplay muted playsinline class="w-full h-full object-cover"></video>
                <div class="absolute bottom-4 left-4 bg-black px-3 py-1 text-[10px] font-bold border border-white/20 uppercase">
                    You (Self)
                </div>
            </div>
        </div>

        <!-- Controls Wrapper -->
        <div class="absolute bottom-8 left-0 right-0 flex flex-col items-center gap-6 z-50">
            <div v-if="isIncoming && !hasAccepted" class="flex flex-wrap justify-center gap-6 py-8 w-full bg-black/90 backdrop-blur-2xl border-t border-white/20 px-6">
                <button 
                    @click="acceptCall" 
                    class="h-20 md:h-24 px-12 md:px-20 bg-green-500 text-white hover:bg-green-600 hover:scale-105 active:scale-95 font-black uppercase tracking-[0.2em] transition-all shadow-[0_0_80px_rgba(34,197,94,0.5)] rounded-full border-4 border-white/40 flex items-center justify-center gap-4 group"
                >
                    <div class="w-3 h-3 rounded-full bg-white animate-ping"></div>
                    <span>Принять вызов</span>
                </button>
                <button 
                    @click="endCall" 
                    class="h-20 md:h-24 px-12 md:px-16 border-4 border-white/30 text-white hover:bg-white hover:text-black font-black uppercase tracking-widest transition-all rounded-full flex items-center justify-center opacity-80 hover:opacity-100"
                >
                    Отклонить
                </button>
            </div>

            <div v-else class="flex justify-center gap-6 pb-4 bg-black/60 backdrop-blur-md px-12 py-6 rounded-3xl border border-white/10 mx-auto w-max">
                <button 
                    @click="toggleMic" 
                    :class="[isMicOn ? 'bg-white text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-16 w-16 rounded-full hover:bg-zinc-200 hover:text-black transition-all flex items-center justify-center border border-white/10"
                >
                    <span class="text-[10px] font-bold uppercase">{{ isMicOn ? 'Mic On' : 'Muted' }}</span>
                </button>
                
                <button 
                    @click="endCall" 
                    class="h-16 px-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-black uppercase tracking-widest transition-all shadow-lg shadow-red-500/20"
                >
                    Завершить
                </button>

                <button 
                    @click="toggleCamera" 
                    :class="[isCameraOn ? 'bg-white text-black' : 'bg-zinc-800 text-white opacity-40']"
                    class="h-16 w-16 rounded-full hover:bg-zinc-200 hover:text-black transition-all flex items-center justify-center border border-white/10"
                >
                    <span class="text-[10px] font-bold uppercase">{{ isCameraOn ? 'Cam On' : 'Off' }}</span>
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
            isIncoming: false,
            hasAccepted: false,
            didIInitiate: false,
            isConnected: false,
            isRemoteAccepted: false,
            isMicOn: true,
            isCameraOn: true,
            remoteUserId: null,
            remoteUserName: '',
            peerConnection: null,
            localStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' },
                    { urls: 'stun:stun2.l.google.com:19302' },
                    { urls: 'stun:stun3.l.google.com:19302' },
                    { urls: 'stun:stun4.l.google.com:19302' }
                ]
            },
            pendingOffer: null,
            pendingCandidates: []
        };
    },
    
    computed: {
        displayUserName() {
            return this.remoteUserName || 'Пользователь';
        }
    },

    mounted() {
        // Listen for incoming call signals (only for logged in users)
        const customerId = this.$shop.customer_id;
        
        if (window.Echo && customerId) {
            window.Echo.private(`user.${customerId}`)
                .listen('.call-signal', (data) => {
                    this.handleSignal(data);
                });

            // Update ICE servers with TURN if provided
            const laravel = window.Laravel || {};
            if (laravel.turnUrl) {
                console.log('WebRTC: Adding TURN server relay');
                this.configuration.iceServers.push({
                    urls: laravel.turnUrl,
                    username: laravel.turnUsername,
                    credential: laravel.turnPassword
                });
            }
        }

        // Global event to start a call
        this.$emitter.on('start-call', (payload) => {
            console.log('CallOverlay: Event [start-call] received', payload);
            if (this.isActive) {
                console.warn('Call already active or incoming');
                return;
            }
            if (!this.$shop.customer_id) {
                console.error('CallOverlay: cannot start call, customer_id missing');
                this.$emitter.emit('add-flash', { 
                    type: 'warning', 
                    message: 'Пожалуйста, войдите в систему, чтобы совершить вызов' 
                });
                return;
            }
            this.initiateCall(payload.userId, payload.userName);
        });

        // Check for auto-join from email link
        const urlParams = new URLSearchParams(window.location.search);
        const callerId = urlParams.get('caller_id');
        if (callerId && this.$shop.customer_id) {
            // Short delay to ensure initialization and allow user to see the UI
            setTimeout(() => {
                this.initiateCall(parseInt(callerId), 'Собеседник (из уведомления)');
            }, 1000);
        }
    },

    methods: {
        async initiateCall(userId, userName) {
            console.log('CallOverlay: Initializing call to', { userId, userName });
            this.remoteUserId = userId;
            this.remoteUserName = userName;
            this.isActive = true;
            this.isIncoming = false;
            this.didIInitiate = true;
            this.hasAccepted = true;

            await this.setupLocalMedia();
            this.createPeerConnection();
            
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);

            console.log('WebRTC: Sending offer signal');
            this.sendSignal({ type: 'offer', sdp: offer.sdp });
        },

        async setupLocalMedia() {
            try {
                this.localStream = await navigator.mediaDevices.getUserMedia({ 
                    video: true, 
                    audio: true 
                });
                this.$refs.localVideo.srcObject = this.localStream;
            } catch (error) {
                console.error('Error accessing media devices:', error);
            }
        },

        createPeerConnection() {
            this.peerConnection = new RTCPeerConnection(this.configuration);

            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });

            this.peerConnection.ontrack = (event) => {
                console.log('WebRTC: Track received!', event);
                this.$refs.remoteVideo.srcObject = event.streams[0];
                this.isConnected = true;
            };

            this.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    console.log('WebRTC: Local ICE candidate found:', event.candidate.type);
                    this.sendSignal({ type: 'candidate', candidate: event.candidate });
                }
            };

            this.peerConnection.onconnectionstatechange = () => {
                console.log('WebRTC: Connection state:', this.peerConnection.connectionState);
                if (this.peerConnection.connectionState === 'connected') {
                    this.isConnected = true;
                }
            };

            this.peerConnection.oniceconnectionstatechange = () => {
                console.log('WebRTC: ICE state:', this.peerConnection.iceConnectionState);
            };
        },

        async handleSignal(data) {
            const signal = data.signal_data;
            this.remoteUserId = data.from_user_id;
            
            if (signal.caller_name) {
                this.remoteUserName = signal.caller_name;
            }

            if (signal.type === 'offer') {
                this.isActive = true;
                this.isIncoming = true;
                this.pendingOffer = signal;
                
                // Play notification sound
                this.playRingtone();
                
            } else if (signal.type === 'call-accepted') {
                console.log('WebRTC: Remote user clicked Accept');
                this.isRemoteAccepted = true;
            } else if (signal.type === 'answer') {
                console.log('WebRTC: Answer received');
                try {
                    const sanitizedSdp = this.sanitizeSDP(signal.sdp);
                    const remoteDesc = new RTCSessionDescription({
                        type: signal.type,
                        sdp: sanitizedSdp
                    });
                    await this.peerConnection.setRemoteDescription(remoteDesc);
                    this.flushPendingCandidates();
                } catch (e) {
                    console.error('WebRTC: Failed to set answer description', e, signal.sdp);
                }
            } else if (signal.type === 'candidate') {
                if (this.peerConnection && this.peerConnection.remoteDescription) {
                    console.log('WebRTC: Remote candidate received and added directly');
                    await this.peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate));
                } else {
                    console.log('WebRTC: Queueing remote candidate');
                    this.pendingCandidates.push(signal.candidate);
                }
            } else if (signal.type === 'hangup') {
                console.log('WebRTC: Hangup received from remote user');
                this.cleanup();
            }
        },

        async acceptCall() {
            this.hasAccepted = true;
            this.sendSignal({ type: 'call-accepted' });
            this.stopRingtone();
            await this.setupLocalMedia();
            this.createPeerConnection();

            try {
                const sanitizedSdp = this.sanitizeSDP(this.pendingOffer.sdp);
                console.log('WebRTC: Setting remote offer', sanitizedSdp.substring(0, 100) + '...');
                
                const remoteDesc = new RTCSessionDescription({
                    type: this.pendingOffer.type,
                    sdp: sanitizedSdp
                });
                await this.peerConnection.setRemoteDescription(remoteDesc);
                this.flushPendingCandidates();
                
                const answer = await this.peerConnection.createAnswer();
                await this.peerConnection.setLocalDescription(answer);
                this.sendSignal({ type: 'answer', sdp: answer.sdp });
            } catch (e) {
                console.error('WebRTC: Failed to set offer description', e, this.pendingOffer.sdp);
                // Optionally alert user or reset state
            }
        },

        sanitizeSDP(sdp) {
            if (!sdp) return '';
            // Aggressive sanitization: remove all \r, split by \n, trim each line, remove empty ones, join with \r\n
            return sdp.replace(/\r/g, '')
                      .split('\n')
                      .map(line => line.trim())
                      .filter(line => line.length > 0)
                      .join('\r\n') + '\r\n';
        },

        sendSignal(signalData) {
            axios.post('/customer/account/calls/signal', {
                to_user_id: this.remoteUserId,
                signal_data: signalData
            }).catch(error => {
                console.error('WebRTC: Signal sending failed:', error.response?.data || error.message);
            });
        },

        toggleMic() {
            this.isMicOn = !this.isMicOn;
            this.localStream.getAudioTracks().forEach(track => track.enabled = this.isMicOn);
        },

        toggleCamera() {
            this.isCameraOn = !this.isCameraOn;
            this.localStream.getVideoTracks().forEach(track => track.enabled = this.isCameraOn);
        },

        endCall() {
            console.log('WebRTC: Ending call, sending hangup to', this.remoteUserId);
            this.sendSignal({ type: 'hangup' });
            this.cleanup();
        },

        cleanup() {
            this.stopRingtone();
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
            }
            if (this.peerConnection) {
                this.peerConnection.close();
            }
            this.isActive = false;
            this.isConnected = false;
            this.isIncoming = false;
            this.hasAccepted = false;
            this.didIInitiate = false;
            this.isRemoteAccepted = false;
            this.localStream = null;
            this.peerConnection = null;
            this.pendingCandidates = [];
        },

        async flushPendingCandidates() {
            if (this.pendingCandidates.length > 0) {
                console.log(`WebRTC: Flushing ${this.pendingCandidates.length} queued candidates`);
                for (const cand of this.pendingCandidates) {
                    try {
                        await this.peerConnection.addIceCandidate(new RTCIceCandidate(cand));
                    } catch (e) {
                        console.error('WebRTC: Error adding queued candidate', e);
                    }
                }
                this.pendingCandidates = [];
            }
        },

        playRingtone() {
            if (!this.ringtoneAudio) {
                // Using a standard data URI beep or link to a file if available
                this.ringtoneAudio = new Audio('https://actions.google.com/sounds/v1/alarms/phone_ringing.ogg');
                this.ringtoneAudio.loop = true;
            }
            this.ringtoneAudio.play().catch(e => console.warn('Autoplay prevented ringtone', e));
        },

        stopRingtone() {
            if (this.ringtoneAudio) {
                this.ringtoneAudio.pause();
                this.ringtoneAudio.currentTime = 0;
            }
        }
    }
};
</script>
