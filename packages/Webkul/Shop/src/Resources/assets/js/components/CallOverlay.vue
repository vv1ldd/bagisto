<template>
    <div v-if="isActive" 
         :class="[isFullscreen ? 'p-0' : 'p-4 md:p-8']"
         class="fixed inset-0 z-[10000] bg-black text-white flex flex-col justify-between font-sans overflow-hidden transition-all duration-300">
        <!-- Header -->
        <div v-if="!isFullscreen" class="flex justify-between items-center border-b border-white/20 pb-4 relative z-50">
            <div>
                <div class="text-[8px] md:text-[10px] uppercase tracking-[0.3em] opacity-60 mb-1 flex items-center gap-2">
                    <span v-if="peerCount === 1">Видеозвонок</span>
                    <span v-else-if="peerCount > 1">Групповая встреча</span>
                    <span v-else>Ожидание участников</span>
                    <span class="flex items-center gap-1 ml-2 border-l border-white/20 pl-2">
                        <span class="w-1.5 h-1.5 rounded-full" :class="statusColor"></span>
                        <span class="text-[7px] tracking-widest opacity-40">{{ signalingState.toUpperCase() }}</span>
                    </span>
                </div>
                <h2 class="text-xl md:text-3xl font-black uppercase italic tracking-tighter">{{ isRoomMode ? 'Защищенная комната' : (peerCount > 0 ? 'Встреча активна' : 'Комната пуста') }}</h2>
                <div v-if="isRoomMode" class="text-[8px] font-bold text-[#00FF41] uppercase tracking-[0.2em] mt-1 flex items-center gap-1">
                    <span class="w-1 h-1 bg-[#00FF41] rounded-full animate-pulse"></span>
                    Шифрование P2P активно
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div v-if="peerCount > 0" class="bg-[#00FF41] text-black px-3 md:px-4 py-1 font-bold text-[10px] md:text-xs uppercase tracking-widest animate-pulse transition-all">
                    {{ peerCount + 1 }} в сети
                </div>
                <div class="flex -space-x-2">
                    <div v-for="id in peerIds" :key="id" 
                        class="w-6 h-6 rounded-full bg-zinc-800 border-2 border-black flex items-center justify-center text-[10px] font-bold uppercase transition-all"
                        :class="{'border-emerald-500 bg-emerald-950/30': peers[id]?.connected}"
                        :title="peers[id]?.name">
                        {{ peers[id]?.name?.[0] || '?' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Grid Area -->
        <div :class="[isFullscreen ? 'm-0 rounded-none' : 'my-4 rounded-3xl']" 
             class="flex-grow relative overflow-hidden bg-zinc-950 transition-all duration-300">
            
            <!-- 1-on-1 Mode: Cinema Layout with Swap Button -->
            <div v-if="peerCount === 1" class="relative w-full h-full overflow-hidden">
                <!-- Main View Container for Gestures -->
                <div class="absolute inset-0 transition-opacity duration-1000 flex items-center justify-center overflow-hidden touch-none"
                     @touchstart="handleTouchStart"
                     @touchmove="handleTouchMove"
                     @touchend="handleTouchEnd">
                    
                    <!-- If focused on Peer -->
                    <template v-if="!isFocusedOnSelf">
                         <video :id="'video_' + peerIds[0]" 
                                autoplay playsinline 
                                :style="zoomStyle"
                                :class="[isFullscreen ? 'object-cover' : 'object-contain']"
                                class="w-full h-full pointer-events-none"></video>
                         
                         <!-- Subtle Security Indicator in corner -->
                         <div v-if="peers[peerIds[0]].verified" class="absolute top-6 right-6 text-xl opacity-40 z-20" title="Защищено">😉</div>

                         <!-- Zoom Reset Badge -->
                         <div v-if="zoomLevel > 1" @click="resetZoom" class="absolute top-6 left-6 bg-[#7C45F5] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest cursor-pointer animate-bounce z-20">
                             {{ Math.round(zoomLevel * 100) }}% (Reset)
                         </div>
                    </template>

                    <!-- If focused on Self -->
                    <template v-else>
                         <video ref="localVideoMain" 
                                autoplay muted playsinline 
                                :class="[isFullscreen ? 'object-cover' : 'object-contain']"
                                class="w-full h-full mirror"></video>
                    </template>
                </div>

                <!-- Swap & Fullscreen Controls -->
                <div class="absolute bottom-6 right-6 flex flex-col gap-3 z-20">
                    <div @click="toggleFullscreen" 
                         class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-white/5 backdrop-blur-3xl border border-white/10 flex items-center justify-center cursor-pointer hover:bg-white/20 hover:scale-110 active:scale-95 transition-all group"
                         title="Полный экран">
                         <span class="text-lg md:text-xl">{{ isFullscreen ? '◢◣' : '⛶' }}</span>
                    </div>

                    <div @click="isFocusedOnSelf = !isFocusedOnSelf" 
                         class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-white/5 backdrop-blur-3xl border border-white/10 shadow-[0_0_40px_rgba(0,0,0,0.6)] flex items-center justify-center cursor-pointer hover:bg-white/20 hover:scale-110 active:scale-95 transition-all group"
                         title="Сменить вид">
                        <span class="text-xl md:text-2xl group-hover:rotate-180 transition-transform duration-700">🔄</span>
                    </div>
                </div>

                <!-- Waiting for Peer to connect/stream -->
                <div v-if="!peers[peerIds[0]]?.connected || !peers[peerIds[0]]?.streamReady" 
                    class="absolute inset-0 flex items-center justify-center bg-zinc-950/80 backdrop-blur-md z-30 transition-all">
                    <div class="flex flex-col items-center gap-6">
                         <div class="w-16 h-16 border-4 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                         <div class="text-center">
                            <h3 class="text-lg font-black uppercase tracking-[0.4em] text-white">{{ peers[peerIds[0]]?.connected ? 'Получение видео...' : 'Установка связи...' }}</h3>
                            <p class="text-[8px] uppercase tracking-[0.2em] text-zinc-500 mt-2">P2P Тоннель строится напрямую</p>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Group Mode (split screen) -->
            <div v-else :class="gridClass" class="grid w-full h-full p-2 md:p-4 gap-2 md:gap-4 transition-all duration-500">
                <!-- Local Video -->
                <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group shadow-2xl flex items-center justify-center">
                    <video ref="localVideoGrid" autoplay muted playsinline 
                           :class="[isFullscreen ? 'object-cover' : 'object-contain']"
                           class="w-full h-full mirror"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 rounded-lg flex items-center gap-2">
                        <span>Вы ({{ localUserName }})</span>
                        <span v-if="localFingerprint" class="opacity-40" title="Security Fingerprint Verified">🛡️</span>
                    </div>
                </div>

                <!-- Remote Videos -->
                <div v-for="id in peerIds" :key="id" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 group shadow-2xl flex items-center justify-center">
                    <video :id="'video_' + id" autoplay playsinline 
                           :class="[isFullscreen ? 'object-cover' : 'object-contain']"
                           class="w-full h-full"></video>
                    
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 transition-all group-hover:bg-[#7C45F5]/80 rounded-lg flex items-center gap-2">
                        <span>{{ peers[id]?.name }}</span>
                        <span v-if="peers[id]?.verified" class="text-lg animate-bounce duration-1000">😉</span>
                        <span v-else-if="peers[id]?.connected" class="opacity-50 text-[10px]">🔒</span>
                    </div>
                    
                    <div v-if="!peers[id]?.connected" class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-md z-20">
                        <div class="flex flex-col items-center gap-2">
                             <div class="w-6 h-6 border-2 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="peerCount === 0" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none">
                <div class="w-32 h-32 rounded-full bg-zinc-900/50 backdrop-blur-3xl border border-white/5 flex items-center justify-center mb-8 animate-pulse shadow-2xl">
                     <div class="flex -space-x-4">
                        <div v-if="signalingState === 'unavailable'" class="text-4xl">⚠️</div>
                        <template v-else>
                            <div class="w-12 h-12 rounded-full bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center text-xl">👤</div>
                            <div class="w-12 h-12 rounded-full bg-[#7C45F5] border-2 border-[#7C45F5]/50 flex items-center justify-center text-xl shadow-lg">👥</div>
                        </template>
                     </div>
                </div>
                <div class="text-center px-8 pointer-events-auto">
                    <template v-if="(signalingState === 'unavailable' || signalingState === 'failed') && !signalingGraceActive">
                        <div class="bg-black/80 backdrop-blur-3xl p-8 md:p-12 rounded-[40px] border border-red-500/20 flex flex-col items-center max-w-sm mx-auto shadow-[0_0_100px_rgba(239,68,68,0.2)]">
                            <div class="text-6xl mb-6 scale-110">🛰️</div>
                            <h3 class="text-sm md:text-lg font-black uppercase tracking-[0.3em] text-red-500 mb-2">Ошибка сети</h3>
                            <p class="text-[10px] md:text-xs uppercase tracking-[0.2em] text-zinc-400 leading-relaxed mb-8">
                                Сигнальный сервер недоступен. Проверьте соединение или повторите попытку.
                            </p>
                            <button @click="retryEcho" 
                                :disabled="isRetrying"
                                class="w-full py-4 bg-white text-black text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-zinc-200 transition-all active:scale-95 shadow-xl disabled:opacity-50 flex items-center justify-center gap-3">
                                <div v-if="isRetrying" class="w-3 h-3 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
                                {{ isRetrying ? 'Подключение...' : 'Переподключиться' }}
                            </button>
                        </div>
                    </template>
                    <template v-else>
                        <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.3em] text-white">Ожидание участников</h3>
                        <p class="text-[8px] md:text-[10px] uppercase tracking-[0.2em] text-zinc-500 max-w-xs leading-relaxed mt-4 italic">
                            {{ (signalingState === 'unavailable' || signalingState === 'failed') ? 'Восстановление связи...' : 'Передайте ссылку собеседнику для начала разговора.' }}
                        </p>
                    </template>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div v-if="!isFullscreen" class="relative z-50 flex justify-center gap-4 py-4 mt-auto">
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
            localHash: '', 
            roomUuid: null,
            isRoomMode: false,
            peers: {}, 
            isFocusedOnSelf: false, // For 1-on-1 mode swap
            localFingerprint: null,
            signalingState: (window.Echo?.connector?.pusher?.connection?.state) || 'connecting',
            isMicOn: true,
            isCameraOn: true,
            isSharingScreen: false,
            screenStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' },
                    { urls: 'stun:stun2.l.google.com:19302' },
                    { urls: 'stun:stun3.l.google.com:19302' },
                ],
                iceCandidatePoolSize: 10
            },
            presenceInterval: null,
            cleanupInterval: null,
            retryInterval: null,

            // Gesture State
            zoomLevel: 1,
            panX: 0,
            panY: 0,
            initialDist: 0,
            initialZoom: 1,
            initialPanX: 0,
            initialPanY: 0,
            initialCenter: { x: 0, y: 0 },
            isFullscreen: false,
            signalingGraceActive: false,
            signalingGraceTimeout: null,
            reconnectAttempts: 0,
            isRetrying: false,
            sessionUniqueId: Math.random().toString(36).substring(7)
        };
    },

    computed: {
        peerIds() {
            return Object.keys(this.peers);
        },
        peerCount() {
            return this.peerIds.length;
        },
        gridClass() {
            const count = this.peerCount + 1;
            if (count === 1) return 'grid-cols-1';
            if (count === 2) return 'grid-cols-1 md:grid-cols-2';
            if (count <= 4) return 'grid-cols-2';
            return 'grid-cols-2 lg:grid-cols-3';
        },
        statusColor() {
            switch(this.signalingState) {
                case 'connected': return 'bg-emerald-500';
                case 'connecting': return 'bg-amber-500 animate-pulse';
                case 'unavailable': return 'bg-red-500';
                case 'failed': return 'bg-red-500';
                default: return 'bg-zinc-500';
            }
        },
        zoomStyle() {
            return {
                transform: `scale(${this.zoomLevel}) translate(${this.panX / this.zoomLevel}px, ${this.panY / this.zoomLevel}px)`,
                transition: this.initialDist === 0 ? 'transform 0.1s ease-out' : 'none'
            };
        }
    },

    watch: {
        isFocusedOnSelf() {
            this.resetZoom();
            this.$nextTick(() => this.rebindVideos());
        },
        peerCount() {
            this.resetZoom();
            this.$nextTick(() => this.rebindVideos());
        }
    },

    mounted() {
        this.$emitter.on('join-room', (payload) => {
            if (this.isActive) return;
            this.joinRoom(payload.uuid, payload.userName, payload.hash);
        });

        this.$emitter.on('echo-state-change', (state) => {
            console.log('CallOverlay: Echo state change ->', state);
            this.handleSignalingStateChange(state);
        });

        // Handle initial state immediately
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            this.signalingState = window.Echo.connector.pusher.connection.state;
        }
        this.handleSignalingStateChange(this.signalingState);

        document.addEventListener('fullscreenchange', this.handleFullscreenChange);

        const laravel = window.Laravel || {};
        if (laravel.turnUrl) {
            this.configuration.iceServers.unshift({
                urls: [laravel.turnUrl],
                username: laravel.turnUsername,
                credential: laravel.turnPassword
            });
        }

        this.retryInterval = setInterval(() => this.rebindVideos(), 3000);
        this.cleanupInterval = setInterval(() => this.cleanupStalePeers(), 5000);
    },

    beforeUnmount() {
        this.stopPresence();
        document.removeEventListener('fullscreenchange', this.handleFullscreenChange);
        if (this.retryInterval) clearInterval(this.retryInterval);
        if (this.cleanupInterval) clearInterval(this.cleanupInterval);
    },

    methods: {
        // Pinch-to-Zoom Handlers
        handleTouchStart(e) {
            if (e.touches.length === 2) {
                this.initialDist = this.getDist(e.touches);
                this.initialZoom = this.zoomLevel;
                this.initialCenter = this.getCenter(e.touches);
                this.initialPanX = this.panX;
                this.initialPanY = this.panY;
            } else if (e.touches.length === 1 && this.zoomLevel > 1) {
                // One finger pan if zoomed
                this.initialCenter = { x: e.touches[0].clientX, y: e.touches[0].clientY };
                this.initialPanX = this.panX;
                this.initialPanY = this.panY;
            }
        },

        handleTouchMove(e) {
            if (e.touches.length === 2 && this.initialDist > 0) {
                e.preventDefault(); // Block browser native zoom
                const currentDist = this.getDist(e.touches);
                const scale = currentDist / this.initialDist;
                this.zoomLevel = Math.max(1, Math.min(5, this.initialZoom * scale));

                // Adjust pan to follow center
                const currentCenter = this.getCenter(e.touches);
                this.panX = this.initialPanX + (currentCenter.x - this.initialCenter.x);
                this.panY = this.initialPanY + (currentCenter.y - this.initialCenter.y);
            } else if (e.touches.length === 1 && this.zoomLevel > 1) {
                e.preventDefault(); // Block scroll when zoomed
                const deltaX = e.touches[0].clientX - this.initialCenter.x;
                const deltaY = e.touches[0].clientY - this.initialCenter.y;
                this.panX = this.initialPanX + deltaX;
                this.panY = this.initialPanY + deltaY;
            }
        },

        handleTouchEnd() {
            this.initialDist = 0;
            // Bound panning
            if (this.zoomLevel === 1) {
                this.panX = 0;
                this.panY = 0;
            }
        },

        getDist(touches) {
            const dx = touches[0].clientX - touches[1].clientX;
            const dy = touches[0].clientY - touches[1].clientY;
            return Math.sqrt(dx * dx + dy * dy);
        },

        getCenter(touches) {
            return {
                x: (touches[0].clientX + (touches[1] ? touches[1].clientX : touches[0].clientX)) / 2,
                y: (touches[0].clientY + (touches[1] ? touches[1].clientY : touches[0].clientY)) / 2
            };
        },

        resetZoom() {
            this.zoomLevel = 1;
            this.panX = 0;
            this.panY = 0;
        },

        handleSignalingStateChange(state) {
            this.signalingState = state;
            console.log(`CallOverlay [${this.sessionUniqueId}]: Signaling state actually changed to -> ${state}`);

            if (state === 'connected') {
                this.signalingGraceActive = false;
                this.isRetrying = false;
                if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
                this.reconnectAttempts = 0;
                
                if (this.isActive && this.roomUuid) {
                    this.subscribeToChannels();
                    this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                }
            } else if (state === 'unavailable' || state === 'failed') {
                if (!this.signalingGraceActive) {
                    this.signalingGraceActive = true;
                    console.log('CallOverlay: Signaling unavailable, starting grace period...');
                    
                    if (this.reconnectAttempts < 3) {
                        this.reconnectAttempts++;
                        console.log(`CallOverlay: Auto-reconnect attempt ${this.reconnectAttempts}...`);
                        setTimeout(() => this.retryEcho(), 2000);
                    }

                    this.signalingGraceTimeout = setTimeout(() => {
                        this.signalingGraceActive = false;
                        console.log('CallOverlay: Signaling grace period expired.');
                    }, 10000); 
                }
            }
        },

        handleFullscreenChange() {
            this.isFullscreen = !!document.fullscreenElement;
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                this.isFullscreen = true;
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                    this.isFullscreen = false;
                }
            }
        },

        async joinRoom(uuid, userName, hash) {
            console.log(`CallOverlay [${this.sessionUniqueId}]: Joining room ${uuid} as ${userName} (Hash: ${hash})`);
            this.roomUuid = uuid;
            this.localUserName = userName;
            this.localHash = hash || userName; 
            this.isRoomMode = true;
            this.isActive = true;

            await this.setupLocalMedia();
            this.subscribeToChannels();
            this.startPresence();
            
            const customerId = this.$shop?.customer_id;
            if (window.Echo && customerId) {
                 console.log(`CallOverlay [${this.sessionUniqueId}]: Subscribing to private user.${customerId}`);
                 window.Echo.private(`user.${customerId}`).listen('.call-signal', (data) => this.handleSignal(data));
            }
        },

        subscribeToChannels() {
            if (window.Echo && this.roomUuid) {
                console.log(`CallOverlay [${this.sessionUniqueId}]: Subscribing to call.${this.roomUuid}`);
                window.Echo.channel(`call.${this.roomUuid}`)
                    .stopListening('.call-signal')
                    .listen('.call-signal', (data) => {
                        this.handleSignal(data);
                    });
            }
        },

        startPresence() {
            this.stopPresence();
            console.log(`CallOverlay [${this.sessionUniqueId}]: Starting presence ticks...`);
            this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
            let ticks = 0;
            this.presenceInterval = setInterval(() => {
                if (!this.isActive) return;
                this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                ticks++;
                if (ticks > 15) {
                    this.stopPresence();
                    console.log(`CallOverlay [${this.sessionUniqueId}]: Presence stable, slowing down to 10s`);
                    this.presenceInterval = setInterval(() => {
                        if (this.isActive) this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                    }, 10000);
                }
            }, 2000); 
        },

        stopPresence() {
            if (this.presenceInterval) clearInterval(this.presenceInterval);
        },

        cleanupStalePeers() {
            const now = Date.now();
            Object.keys(this.peers).forEach(id => {
                const peer = this.peers[id];
                if (peer.lastSeen && now - peer.lastSeen > 25000 && !peer.connected) {
                    this.removePeer(id);
                }
            });
        },

        async setupLocalMedia() {
            try {
                // Request balanced constraints for broad compatibility
                const constraints = {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 },
                        
                        // macOS/iOS specific (Center Stage) - Keep stable ones
                        faceFraming: true,
                        
                        // Android/Samsung & Generic Mobile advanced controls
                        focusMode: { ideal: 'continuous' },
                        
                        // Standard PTZ where available
                        pan: true,
                        tilt: true,
                        zoom: true
                    },
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    }
                };

                console.log('Room: Requesting media...', constraints);
                try {
                    this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
                } catch (e) {
                    console.warn('Room: Advanced constraints failed, falling back to simple', e);
                    this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                }
                
                // Track states for debugging
                this.localStream.getTracks().forEach(track => {
                    console.log(`Room: Local Track ${track.kind} (${track.label}): enabled=${track.enabled}, state=${track.readyState}`);
                    track.onmute = () => console.warn(`Room: Local Track ${track.kind} muted`);
                    track.onunmute = () => console.log(`Room: Local Track ${track.kind} unmuted`);
                });

                // Log actual capabilities for debugging
                try {
                    const videoTrack = this.localStream.getVideoTracks()[0];
                    if (videoTrack && videoTrack.getCapabilities) {
                        const caps = videoTrack.getCapabilities();
                        console.log('Room: Camera Capabilities:', caps);
                    }
                } catch (capError) { }
                
                const tempPC = new RTCPeerConnection(this.configuration);
                tempPC.addTransceiver('video');
                const offer = await tempPC.createOffer();
                const fingerprintMatch = offer.sdp.match(/a=fingerprint:sha-256\s+(.*)/i);
                if (fingerprintMatch) {
                    this.localFingerprint = fingerprintMatch[1];
                }
                tempPC.close();

                this.$nextTick(() => this.rebindVideos());
            } catch (e) { console.warn('Room: Media access denied', e); }
        },

        handleSignal(data) {
            const signal = data.signal_data;
            const senderName = data.sender_name;
            const senderHash = signal.sender_hash || senderName; 
            const senderSessionId = signal.sender_session_id;

            // Filter out self-signals using sessionUniqueId
            if (senderSessionId === this.sessionUniqueId) return;
            
            // If the signal is targeted and not for us, filter it
            if (signal.target && signal.target !== this.localHash && signal.target !== this.localUserName) return;

            console.log(`CallOverlay [${this.sessionUniqueId}]: Signal ${signal.type} from ${senderName} (Session: ${senderSessionId})`);

            if (signal.type === 'presence') {
                const now = Date.now();
                
                // Solve initiator deadlock for identical hashes (shared URLs)
                const isInitiator = this.localHash.toLowerCase() !== senderHash.toLowerCase()
                    ? this.localHash.toLowerCase() < senderHash.toLowerCase()
                    : this.sessionUniqueId < senderSessionId;
                
                if (!this.peers[senderHash]) {
                    this.peers = {
                        ...this.peers,
                        [senderHash]: { 
                            name: senderName,
                            pc: null, stream: null, connected: false, streamReady: false, 
                            iceQueue: [], fingerprint: signal.fingerprint, verified: false,
                            lastSeen: now
                        }
                    };
                    this.sendSignal({ type: 'presence', target: senderHash, fingerprint: this.localFingerprint });
                } else {
                    this.peers[senderHash].lastSeen = now;
                    this.peers[senderHash].name = senderName; 
                    if (signal.fingerprint) this.peers[senderHash].fingerprint = signal.fingerprint;
                }

                if (isInitiator) {
                    const peer = this.peers[senderHash];
                    if (!peer.pc || ['failed', 'closed'].includes(peer.pc.connectionState)) {
                        this.initiateConnection(senderHash, signal.fingerprint);
                    }
                }
            } else if (signal.type === 'offer') {
                this.handleOffer(senderHash, senderName, signal);
            } else if (signal.type === 'answer') {
                this.handleAnswer(senderHash, signal);
            } else if (signal.type === 'candidate') {
                this.handleCandidate(senderHash, signal);
            } else if (signal.type === 'hangup') {
                this.removePeer(senderHash);
            }
        },

        normalizeSDP(sdp) {
            if (!sdp) return '';
            let lines = sdp.split(/\r?\n/);
            lines = lines.map(line => line.trim()).filter(line => line.length > 0);
            lines = lines.map(line => {
                if (line.startsWith('a=group:BUNDLE')) {
                    return line.replace(/\s+/g, ' ').trim();
                }
                return line;
            });
            return lines.join('\r\n') + '\r\n';
        },

        async initiateConnection(id, remoteFingerprint) {
            const pc = this.createPeerConnection(id);
            if (remoteFingerprint) this.peers[id].fingerprint = remoteFingerprint;
            
            try {
                const offer = await pc.createOffer();
                const cleanSdp = this.normalizeSDP(offer.sdp);
                await pc.setLocalDescription({ type: 'offer', sdp: cleanSdp });
                this.sendSignal({ type: 'offer', sdp: cleanSdp, target: id, fingerprint: this.localFingerprint });
            } catch (e) { console.error('Room: Offer generation failed', e); }
        },

        async handleOffer(id, name, signal) {
            const pc = this.createPeerConnection(id, name);
            if (signal.fingerprint) this.peers[id].fingerprint = signal.fingerprint;

            try {
                const sdp = this.normalizeSDP(signal.sdp);
                await pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp }));
                
                const peer = this.peers[id];
                while (peer.iceQueue.length > 0) {
                    const cand = peer.iceQueue.shift();
                    await pc.addIceCandidate(new RTCIceCandidate(cand)).catch(() => {});
                }

                const answer = await pc.createAnswer();
                const cleanAnswer = this.normalizeSDP(answer.sdp);
                await pc.setLocalDescription({ type: 'answer', sdp: cleanAnswer });
                this.sendSignal({ type: 'answer', sdp: cleanAnswer, target: id, fingerprint: this.localFingerprint });
            } catch (err) { }
        },

        async handleAnswer(id, signal) {
            const peer = this.peers[id];
            if (peer && peer.pc) {
                if (signal.fingerprint) peer.fingerprint = signal.fingerprint;
                try {
                    const sdp = this.normalizeSDP(signal.sdp);
                    await peer.pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp }));
                    while (peer.iceQueue.length > 0) {
                        const cand = peer.iceQueue.shift();
                        await peer.pc.addIceCandidate(new RTCIceCandidate(cand)).catch(() => {});
                    }
                } catch (err) { }
            }
        },

        async handleCandidate(id, signal) {
            const peer = this.peers[id];
            if (!peer) return;

            if (peer.pc && peer.pc.remoteDescription && peer.pc.remoteDescription.type) {
                try {
                    await peer.pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
                } catch (err) { }
            } else {
                peer.iceQueue.push(signal.candidate);
            }
        },

        createPeerConnection(id, name = null) {
            if (this.peers[id]?.pc && this.peers[id].pc.connectionState !== 'closed') {
                return this.peers[id].pc;
            }

            const pc = new RTCPeerConnection(this.configuration);
            
            if (!this.peers[id]) {
                this.peers = {
                    ...this.peers,
                    [id]: { 
                        name: name || id,
                        pc, stream: null, connected: false, streamReady: false, 
                        iceQueue: [], fingerprint: null, verified: false, lastSeen: Date.now()
                    }
                };
            } else {
                this.peers[id].pc = pc;
            }

            if (this.localStream) {
                this.localStream.getTracks().forEach(t => pc.addTrack(t, this.localStream));
            }

            pc.onicecandidate = (e) => {
                if (e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: id });
            };

            pc.ontrack = (e) => {
                const stream = e.streams[0];
                if (this.peers[id]) {
                    this.peers[id].stream = stream;
                    this.peers[id].streamReady = true;
                    this.peers[id].connected = true;
                }
                this.$nextTick(() => this.rebindVideos());
            };

            pc.onconnectionstatechange = () => {
                const state = pc.connectionState;
                console.log(`Room: Peer ${id} connection state: ${state}`);
                if (state === 'connected') {
                    if (this.peers[id]) {
                        this.peers[id].connected = true;
                        this.verifySecurity(id);
                        this.$nextTick(() => this.rebindVideos());
                    }
                } else if (['disconnected', 'failed', 'closed'].includes(state)) {
                    if (this.peers[id]) this.peers[id].connected = false;
                }
            };

            return pc;
        },

        async verifySecurity(id) {
            const peer = this.peers[id];
            if (!peer || !peer.pc || !peer.fingerprint) return;

            try {
                const stats = await peer.pc.getStats();
                let matched = false;
                stats.forEach(report => {
                    if (report.type === 'certificate' && report.fingerprint) {
                        if (report.fingerprint.toLowerCase() === peer.fingerprint.toLowerCase()) matched = true;
                    }
                });
                if (matched) peer.verified = true;
            } catch (e) { }
        },

        rebindVideos() {
            if (!this.isActive) return;
            
            // Rebind Local Streams
            const localMain = this.$refs.localVideoMain;
            const localGrid = this.$refs.localVideoGrid;
            
            if (localMain && this.localStream && localMain.srcObject !== this.localStream) {
                localMain.srcObject = this.localStream;
                localMain.play().catch(() => {});
            }
            if (localGrid && this.localStream && localGrid.srcObject !== this.localStream) {
                localGrid.srcObject = this.localStream;
                localGrid.play().catch(() => {});
            }

            // Rebind Peer Streams
            Object.keys(this.peers).forEach(id => {
                const p = this.peers[id];
                const mainEl = document.getElementById('video_' + id);
                
                if (p && p.stream && p.connected) {
                    if (mainEl && mainEl.srcObject !== p.stream) {
                        mainEl.srcObject = p.stream;
                        mainEl.play().catch(() => {});
                    }
                }
            });
        },

        removePeer(id) {
            const peer = this.peers[id];
            if (peer) {
                if (peer.pc) peer.pc.close();
                const newPeers = { ...this.peers };
                delete newPeers[id];
                this.peers = newPeers;
            }
        },

        sendSignal(signalData) {
            signalData.sender_hash = this.localHash;
            signalData.sender_session_id = this.sessionUniqueId;
            
            const payload = { signal_data: signalData, sender_name: this.localUserName };
            const endpoint = this.isRoomMode ? `/call/${this.roomUuid}/signal` : '/customer/account/calls/signal';
            
            axios.post(endpoint, payload).catch((err) => {
                console.warn(`CallOverlay [${this.sessionUniqueId}]: sendSignal error`, err);
            });
        },

        retryEcho() {
            console.log(`CallOverlay [${this.sessionUniqueId}]: Manual retry initiated`);
            this.isRetrying = true;
            
            // Show "Restoring..." UI immediately
            this.signalingGraceActive = true;
            this.reconnectAttempts = 0;
            if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
            
            this.signalingGraceTimeout = setTimeout(() => {
                this.signalingGraceActive = false;
                this.isRetrying = false;
            }, 10000);

            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                const conn = window.Echo.connector.pusher.connection;
                console.log(`CallOverlay [${this.sessionUniqueId}]: Resetting connection. state was: ${conn.state}`);
                conn.disconnect();
                setTimeout(() => {
                    conn.connect();
                }, 1000);
            } else {
                window.location.reload();
            }
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
                    this.isSharingScreen = true;
                    this.$nextTick(() => this.rebindVideos());
                    track.onended = () => this.toggleScreenShare();
                } else {
                    if (this.screenStream) this.screenStream.getTracks().forEach(t => t.stop());
                    const track = this.localStream?.getVideoTracks()[0];
                    Object.values(this.peers).forEach(p => {
                        const sender = p.pc?.getSenders().find(s => s.track?.kind === 'video');
                        if (sender && track) sender.replaceTrack(track);
                    });
                    this.isSharingScreen = false;
                    this.$nextTick(() => this.rebindVideos());
                }
            } catch (e) { }
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
