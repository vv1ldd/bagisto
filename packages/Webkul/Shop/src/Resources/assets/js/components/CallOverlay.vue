<template>
    <div v-if="isActive" 
         @mousemove="userActivity"
         @touchstart="handleGlobalTouch"
         @click="toggleControls"
         class="fixed inset-0 z-[10000] bg-black text-white font-sans overflow-hidden transition-all duration-300 touch-none select-none">
        
        <!-- Proximity Dimmer -->
        <div v-if="isProximityClose" @click="dismissProximity" class="absolute inset-0 bg-black z-[20000] flex flex-col items-center justify-center pointer-events-auto">
            <div class="w-16 h-16 rounded-full border-4 border-white/10 border-t-white/60 animate-spin mb-4"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white/40">Режим разговора (Тапните для выхода)</p>
        </div>

        <!-- Video Layer (Base) -->
        <div class="absolute inset-0 bg-zinc-950">
            <!-- 1-on-1 Mode -->
            <div v-if="peerCount === 1" class="w-full h-full relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center overflow-hidden touch-none"
                     @touchstart="handleTouchStart"
                     @touchmove="handleTouchMove"
                     @touchend="handleTouchEnd"
                     @wheel="handleWheel">
                    
                    <template v-if="!isFocusedOnSelf">
                         <video :id="'video_' + peerIds[0]" 
                                autoplay playsinline 
                                :style="zoomStyle"
                                :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                                class="w-full h-full pointer-events-none transition-all duration-700"></video>
                         
                         <div v-if="zoomLevel > 1" @click.stop="resetZoom" class="absolute top-24 left-6 bg-[#7C45F5] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest cursor-pointer animate-bounce z-20">
                             {{ Math.round(zoomLevel * 100) }}% (Reset)
                         </div>
                    </template>

                    <template v-else>
                        <video ref="localVideoMain" 
                               autoplay muted playsinline 
                               :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                               :style="zoomStyle"
                               class="w-full h-full transition-all duration-700"></video>
                    </template>
                </div>

                <!-- Shapik Badge (1-on-1) -->
                <div v-if="peers[peerIds[0]]?.connected" 
                     class="absolute bottom-6 left-6 flex items-center gap-2 z-20 transition-all duration-500"
                     :class="{'opacity-0 translate-y-10': !controlsVisible}">
                    <div class="flex items-center gap-1.5 bg-black/60 backdrop-blur-md border border-white/20 px-2.5 py-1.5 shadow-2xl rounded-sm">
                        <div class="flex h-6 w-6 items-center justify-center bg-[#7C45F5] text-white text-[10px] font-black rounded-sm shadow-sm ring-1 ring-white/20">
                            {{ getLetter(isFocusedOnSelf ? localUserName : peers[peerIds[0]].name) }}
                        </div>
                        <div class="text-[10px] md:text-xs font-black uppercase italic tracking-tighter text-white/90">
                            @{{ isFocusedOnSelf ? cleanLocalName : cleanPeerName(peers[peerIds[0]].name) }}
                        </div>
                    </div>
                </div>



                <div v-if="!peers[peerIds[0]]?.connected" 
                    class="absolute inset-0 flex items-center justify-center bg-zinc-900/60 backdrop-blur-[2px] z-30 transition-all duration-500">
                    <div class="flex flex-col items-center gap-6">
                         <div class="w-12 h-12 border-4 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                         <div class="text-center">
                             <h3 class="text-sm font-black uppercase tracking-[0.4em] text-white/80">Соединение...</h3>
                            <button @click.stop="retryEcho" class="mt-4 px-4 py-2 bg-white/5 border border-white/10 rounded-full text-[8px] font-black uppercase tracking-widest hover:bg-white/10 active:scale-95 transition-all">
                                Повторить сейчас
                            </button>
                         </div>
                    </div>
                </div>

                <!-- No Camera Warning for Peer -->
                <div v-if="peers[peerIds[0]]?.connected && !peers[peerIds[0]]?.streamReady" 
                     class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-900/40 z-20">
                     <div class="w-20 h-20 rounded-full bg-black/40 flex items-center justify-center mb-4">
                        <span class="text-4xl opacity-40">🎥🚫</span>
                     </div>
                     <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Камера участника отключена</p>
                </div>
            </div>

            <div v-else-if="peerCount > 1" :class="gridClass" class="grid w-full h-full p-2 md:p-4 gap-2 md:gap-4 transition-all duration-500">
                <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center group/local touch-none"
                     @touchstart="handleTouchStart($event, true)"
                     @touchmove="handleTouchMove($event, true)"
                     @touchend="handleTouchEnd"
                     @wheel="handleWheel($event, true)">
                    <video ref="localVideoGrid" autoplay muted playsinline 
                           :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                           :style="isFocusedOnSelf ? zoomStyle : {}"
                           class="w-full h-full transition-all duration-700"></video>
                    <!-- Shapik Badge (Grid Local) -->
                    <div class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-md border border-white/20 px-2 py-1 shadow-xl z-20 rounded-sm">
                        <div class="flex h-5 w-5 items-center justify-center bg-[#7C45F5] text-white text-[8px] font-black rounded-sm shadow-sm ring-1 ring-white/10">
                            {{ getLetter(localUserName) }}
                        </div>
                        <div class="text-[9px] md:text-[10px] font-black uppercase italic tracking-tighter text-white/90">
                            @{{ cleanLocalName }}
                        </div>
                    </div>

                </div>
                <div v-for="id in peerIds" :key="id" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center">
                    <video :id="'video_' + id" autoplay playsinline 
                           :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                           class="w-full h-full transition-all duration-700"></video>
                    <!-- Shapik Badge (Grid Peer) -->
                    <div class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-md border border-white/20 px-2 py-1 shadow-xl z-20 rounded-sm">
                        <div class="flex h-5 w-5 items-center justify-center bg-[#7C45F5] text-white text-[8px] font-black rounded-sm shadow-sm ring-1 ring-white/10">
                            {{ getLetter(peers[id].name) }}
                        </div>
                        <div class="text-[9px] md:text-[10px] font-black uppercase italic tracking-tighter text-white/90">
                            @{{ cleanPeerName(peers[id].name) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="peerCount === 0" class="absolute inset-0 flex flex-col items-center justify-center translate-z-0">
                <video ref="localVideoWaiting" autoplay muted playsinline 
                       :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                       class="absolute inset-0 w-full h-full transition-all duration-700 pointer-events-none blur-3xl scale-105 opacity-50"></video>

                <div class="relative z-10 flex flex-col items-center justify-center pointer-events-none">
                    <div class="text-center px-8 pointer-events-auto">
                        <template v-if="(signalingState === 'unavailable' || signalingState === 'failed') && !signalingGraceActive">
                            <div class="bg-black/80 backdrop-blur-3xl p-8 md:p-10 rounded-[40px] border border-red-500/20 flex flex-col items-center max-w-sm mx-auto shadow-[0_0_100px_rgba(239,68,68,0.2)]">
                                <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.3em] text-red-500 mb-2">Ошибка сети</h3>
                                <p class="mb-4 text-[8px] md:text-[10px] text-zinc-500 font-bold uppercase tracking-widest text-center leading-relaxed">
                                    Соединение с сервером потеряно. <br>
                                    <span class="text-zinc-600 font-normal normal-case tracking-normal">Вероятно, сервер ws.meanly.ru временно недоступен или блокируется провайдером.</span>
                                </p>
                                
                                <div class="w-full mb-6 p-3 bg-red-500/5 rounded-xl border border-red-500/10">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[7px] uppercase font-black text-red-500/40">Диагностика</span>
                                        <span class="text-[7px] font-mono text-red-500/60 transition-all duration-300">{{ signalingState }}</span>
                                    </div>
                                    <div class="text-[8px] font-mono text-zinc-600 truncate text-left">
                                        {{ signalingServer.scheme }}://{{ signalingServer.host }}:{{ signalingServer.port }}
                                    </div>
                                </div>

                                <button @click="retryEcho" :disabled="isRetrying" 
                                    class="w-full py-4 bg-white text-black text-[10px] font-black uppercase rounded-2xl transition-all hover:bg-zinc-200 active:scale-95 disabled:opacity-50">
                                    {{ isRetrying ? 'Подключение...' : 'Переподключиться' }}
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.3em] text-white/40">Ожидание других участников</h3>
                            <p class="mt-4 text-[10px] uppercase tracking-widest text-zinc-600 font-bold max-w-xs mx-auto animate-pulse">Звонок начнется автоматически...</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interface Layer (Overlay) -->
        <div class="absolute inset-0 z-50 pointer-events-none flex flex-col justify-between p-4 md:p-8 landscape:flex-row landscape:justify-between items-stretch">
            

            <!-- Vertical Control Bar (Bottom Left) -->
            <div class="absolute bottom-8 left-8 flex flex-col items-center gap-3 z-[100] pointer-events-none">
                <div :class="{'opacity-0 translate-y-10': !controlsVisible}"
                     class="flex flex-col items-center gap-2.5 p-2 bg-black/40 backdrop-blur-3xl rounded-3xl border border-white/10 shadow-2xl transition-all duration-700 pointer-events-auto">
                    
                    <button @click.stop="toggleMic" :class="[isMicOn ? 'bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30' : 'bg-red-500/20 text-red-500 border-red-500/40']"
                        class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 10v1a7 7 0 01-14 0v-1m7 11v-3m0 0H8m4 0h4" /></svg>
                    </button>

                    <button @click.stop="toggleCamera" :class="[isCameraOn ? 'bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30' : 'bg-zinc-800 text-white opacity-40']"
                        class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </button>

                    <button v-if="!isMobile" @click.stop="toggleScreenShare" :class="[isSharingScreen ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-zinc-800 text-white opacity-40']"
                        class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </button>

                    <!-- Camera Swap (Now inside the vertical bar on mobile) -->
                    <button v-if="isMobile" @click.stop="toggleCameraFacing"
                        class="h-11 w-11 rounded-2xl bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30 flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
            </div>

            <!-- Top Right: Only End Call (X) button -->
            <div class="absolute top-8 right-8 z-[100] pointer-events-none">
                <button @click.stop="endCall" :class="{'opacity-0 -translate-y-10': !controlsVisible}"
                    class="h-12 w-12 rounded-2xl bg-red-600 text-white font-black flex items-center justify-center shadow-xl shadow-red-600/30 transition-all duration-700 pointer-events-auto hover:scale-105 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
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
            isCameraDenied: false,
            isSharingScreen: false,
            screenStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' },
                    { urls: 'stun:stun2.l.google.com:19302' },
                    { urls: 'stun:stun3.l.google.com:19302' },
                ],
                iceCandidatePoolSize: 0
            },
            presenceInterval: null,
            cleanupInterval: null,
            retryInterval: null,
            inactivityTimer: null,
            luminanceInterval: null,
            luminanceCooldown: 0,

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
            controlsVisible: true,
            controlsTimeout: null,
            signalingGraceActive: false,
            signalingGraceTimeout: null,
            reconnectAttempts: 0,
            isRetrying: false,
            scalingMode: 'contain',
            sessionUniqueId: Math.random().toString(36).substring(2, 10) + Date.now().toString(36),
            isLandscape: window.innerWidth > window.innerHeight,
            signalingServer: window.$signalingServer || { host: 'unknown', port: '?', scheme: '?' },
            cameraZoom: 1,
            initialCameraZoom: 1,
            cameraFacing: 'user', // 'user' or 'environment'
            zoomCapabilities: null,
            isProximityClose: false,
            lastToggleTime: 0,
            lastTapTime: 0
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
        },
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },
        cleanLocalName() {
            return this.cleanPeerName(this.localUserName);
        }
    },

    watch: {
        isFocusedOnSelf() {
            this.resetZoom();
            this.$nextTick(() => this.rebindVideos());
        },
        peerCount(newCount) {
            this.resetZoom();
            this.$nextTick(() => this.rebindVideos());
            
            if (newCount > 0) {
                this.stopInactivityTimer();
            } else {
                this.startInactivityTimer();
            }
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
        window.addEventListener('resize', this.updateOrientation);
        window.addEventListener('orientationchange', this.updateOrientation);

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
        this.setupProximitySensor();
    },

    beforeUnmount() {
        this.stopPresence();
        this.stopInactivityTimer();
        document.removeEventListener('fullscreenchange', this.handleFullscreenChange);
        window.removeEventListener('resize', this.updateOrientation);
        window.removeEventListener('orientationchange', this.updateOrientation);
        if (this.retryInterval) clearInterval(this.retryInterval);
        if (this.cleanupInterval) clearInterval(this.cleanupInterval);
        if (this.luminanceInterval) clearInterval(this.luminanceInterval);
    },

    methods: {
        cleanPeerName(name) {
            if (!name) return '';
            // Strip everything after first non-word/space/dash character if it looks like technical garbage
            return name.split(/[\s[\]#()\-_{}]/)[0].trim() || 'Участник';
        },

        getLetter(name) {
            const clean = this.cleanPeerName(name);
            return clean.charAt(0).toUpperCase();
        },
        // Pinch-to-Zoom Handlers
        handleTouchStart(e, isLocalGrid = false) {
            if (e.touches.length === 2) {
                this.initialDist = this.getDist(e.touches);
                this.initialZoom = this.zoomLevel;
                this.initialCameraZoom = this.cameraZoom;
                this.initialCenter = this.getCenter(e.touches);
                this.initialPanX = this.panX;
                this.initialPanY = this.panY;
            } else if (e.touches.length === 1) {
                const now = Date.now();
                this.lastTapTime = now;

                if (this.zoomLevel > 1 || (this.cameraZoom > 1 && (isLocalGrid || this.isFocusedOnSelf))) {
                    // One finger pan if zoomed
                    this.initialCenter = { x: e.touches[0].clientX, y: e.touches[0].clientY };
                    this.initialPanX = this.panX;
                    this.initialPanY = this.panY;
                }
            }
        },

        handleTouchMove(e, isLocalGrid = false) {
            if (e.touches.length === 2 && this.initialDist > 0) {
                e.preventDefault(); 
                const currentDist = this.getDist(e.touches);
                const scale = Math.max(0.5, Math.min(2, currentDist / this.initialDist));
                const isTargetingLocal = isLocalGrid || this.isFocusedOnSelf;

                if (isTargetingLocal && this.zoomCapabilities) {
                    // Hardware Zoom
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.initialCameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                    
                    const currentCenter = this.getCenter(e.touches);
                    this.panX = this.initialPanX + (currentCenter.x - this.initialCenter.x);
                    this.panY = this.initialPanY + (currentCenter.y - this.initialCenter.y);
                } else {
                    // CSS Digital Zoom - with point centering
                    const nextZoom = Math.max(1, Math.min(5, this.initialZoom * scale));
                    const currentCenter = this.getCenter(e.touches);
                    
                    // Zoom towards pinch center
                    if (nextZoom > 1) {
                        const zoomRatio = nextZoom / this.zoomLevel;
                        this.panX = (this.panX - (currentCenter.x - window.innerWidth/2)) * zoomRatio + (currentCenter.x - window.innerWidth/2);
                        this.panY = (this.panY - (currentCenter.y - window.innerHeight/2)) * zoomRatio + (currentCenter.y - window.innerHeight/2);
                    } else {
                        this.panX = 0;
                        this.panY = 0;
                    }
                    
                    this.zoomLevel = nextZoom;
                    // Additionally allow panning during zoom
                    const deltaX = currentCenter.x - this.initialCenter.x;
                    const deltaY = currentCenter.y - this.initialCenter.y;
                    this.panX += deltaX * 0.1; // Subtle pan during zoom
                    this.panY += deltaY * 0.1;
                }
                this.clampPan();
            } else if (e.touches.length === 1 && (this.zoomLevel > 1 || this.cameraZoom > 1)) {
                e.preventDefault(); 
                const deltaX = (e.touches[0].clientX - this.initialCenter.x);
                const deltaY = (e.touches[0].clientY - this.initialCenter.y);
                this.panX = this.initialPanX + deltaX;
                this.panY = this.initialPanY + deltaY;
                this.clampPan();
            }
        },

        handleWheel(e, isLocalGrid = false) {
            if (e.ctrlKey) {
                e.preventDefault();
                const delta = -e.deltaY;
                const factor = 1.05;
                const scale = delta > 0 ? factor : 1/factor;
                const isTargetingLocal = isLocalGrid || this.isFocusedOnSelf;

                if (isTargetingLocal && this.zoomCapabilities) {
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.cameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                } else {
                    const nextZoom = Math.max(1, Math.min(5, this.zoomLevel * scale));
                    // Zoom towards mouse point
                    const zoomRatio = nextZoom / this.zoomLevel;
                    this.panX = (this.panX - (e.clientX - window.innerWidth/2)) * zoomRatio + (e.clientX - window.innerWidth/2);
                    this.panY = (this.panY - (e.clientY - window.innerHeight/2)) * zoomRatio + (e.clientY - window.innerHeight/2);
                    this.zoomLevel = nextZoom;
                }
            } else if (this.zoomLevel > 1) {
                e.preventDefault();
                this.panX -= e.deltaX;
                this.panY -= e.deltaY;
            }
            this.clampPan();
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

        clampPan() {
            if (this.zoomLevel <= 1) {
                this.panX = 0;
                this.panY = 0;
                return;
            }

            const maxPanX = (window.innerWidth * this.zoomLevel - window.innerWidth) / 2;
            const maxPanY = (window.innerHeight * this.zoomLevel - window.innerHeight) / 2;

            this.panX = Math.max(-maxPanX, Math.min(maxPanX, this.panX));
            this.panY = Math.max(-maxPanY, Math.min(maxPanY, this.panY));
        },

        handleSignalingStateChange(state) {
            this.signalingState = state;
            console.log(`CallOverlay [${this.sessionUniqueId}]: Signaling state -> ${state}`);

            if (state === 'connected') {
                this.signalingGraceActive = false;
                this.isRetrying = false;
                if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
                this.reconnectAttempts = 0;
                
                if (this.isActive && this.roomUuid) {
                    this.subscribeToChannels();
                    this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                }
            } else if (['unavailable', 'failed', 'disconnected'].includes(state)) {
                // DON'T interfere with Echo's internal retry logic for transient disconnects immediately.
                // Only start a "grace period" UI if it stays broken.
                if (!this.signalingGraceActive) {
                    this.signalingGraceActive = true;
                    console.log('CallOverlay: Signaling connection issue. Waiting for internal recovery...');
                    
                    if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
                    this.signalingGraceTimeout = setTimeout(() => {
                        this.signalingGraceActive = false;
                        
                        // AUTO RECOVERY: If still unavailable after 10s, try one manual reset
                        if (['unavailable', 'failed', 'disconnected'].includes(this.signalingState)) {
                            console.warn('CallOverlay: Internal recovery failed. Attempting automated reconnection...');
                            this.retryEcho();
                        }
                    }, 10000);
                }
            }
        },

        handleFullscreenChange() {
            this.isFullscreen = !!document.fullscreenElement;
            if (!this.isFullscreen) {
                this.controlsVisible = true;
                if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
            }
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                this.isFullscreen = true;
                this.userActivity(); 
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                    this.isFullscreen = false;
                    this.controlsVisible = true;
                }
            }
        },

        updateOrientation() {
            this.isLandscape = window.innerWidth > window.innerHeight;
            this.userActivity();
        },

        handleGlobalTouch() {
            this.userActivity();
            this.lastToggleTime = Date.now();
        },

        userActivity() {
            this.controlsVisible = true;
            if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
            if (this.isFullscreen) {
                this.controlsTimeout = setTimeout(() => {
                    this.controlsVisible = false;
                }, 4000);
            }
        },

        async applyCameraZoom(value) {
            try {
                const videoTrack = this.localStream?.getVideoTracks()[0];
                if (videoTrack && this.zoomCapabilities) {
                    await videoTrack.applyConstraints({
                        advanced: [{ zoom: parseFloat(value) }]
                    });
                    this.cameraZoom = value;
                }
            } catch (e) {
                console.warn('Apply zoom failed', e);
            }
        },


        detectZoomCapabilities() {
            try {
                const videoTrack = this.localStream?.getVideoTracks()[0];
                if (videoTrack && videoTrack.getCapabilities) {
                    const caps = videoTrack.getCapabilities();
                    console.log('Camera capabilities detected:', caps);
                    if (caps.zoom) {
                        this.zoomCapabilities = caps.zoom;
                        // Initialize with current or min
                        const current = videoTrack.getSettings().zoom || caps.zoom.min || 1;
                        this.cameraZoom = current;
                    }
                }
            } catch (e) {
                console.warn('Detect zoom failed', e);
            }
        },

        toggleControls() {
            const now = Date.now();
            // Prevent accidental hide immediately after show by touch
            if (now - this.lastToggleTime < 300) return;

            if (this.controlsVisible && this.isFullscreen) {
                this.controlsVisible = false;
                if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
            } else {
                this.userActivity();
            }
            this.lastToggleTime = now;
        },

        dismissProximity() {
            this.isProximityClose = false;
            this.luminanceCooldown = Date.now() + 30000; // 30 seconds cooldown to allow user to look at the screen
        },

        setupProximitySensor() {
            if (this.luminanceInterval) clearInterval(this.luminanceInterval);
            
            this.proximityCanvas = document.createElement('canvas');
            this.proximityCanvas.width = 10;
            this.proximityCanvas.height = 10;
            this.proximityCtx = this.proximityCanvas.getContext('2d', { willReadFrequently: true });

            this.luminanceInterval = setInterval(() => {
                this.analyzeLuminance();
            }, 1000);
        },

        analyzeLuminance() {
            try {
                // Skip if cooldown is active or signaling isn't connected
                if (Date.now() < this.luminanceCooldown) return;
                
                // Only analyze if call is active and camera is on
                if (!this.isActive || !this.isCameraOn) return;

                const video = this.$refs.localVideoMain || this.$refs.localVideoWaiting || this.$refs.localVideoGrid;
                
                // If video is not actually playing, skip
                if (!video || video.paused || video.ended || video.readyState < 2) return;

                // Draw 10x10 sample to detect darkness
                this.proximityCtx.drawImage(video, 0, 0, 10, 10);
                const data = this.proximityCtx.getImageData(0, 0, 10, 10).data;
                
                let totalLuminance = 0;
                for (let i = 0; i < data.length; i += 4) {
                    // Standard luminance formula
                    totalLuminance += (0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2]);
                }
                const avgLuminance = totalLuminance / (data.length / 4);
                
                // Threshold: If avg brightness < 20 (out of 255), it's very dark (likely at ear)
                const isDark = avgLuminance < 20;
                
                if (isDark && !this.isProximityClose) {
                    this.isProximityClose = true;
                    // Auto-hide controls when getting close to ear
                    this.controlsVisible = false;
                } else if (!isDark && this.isProximityClose) {
                    this.isProximityClose = false;
                }
            } catch (e) {
                // Fail gracefully
            }
        },

        async joinRoom(uuid, userName, hash) {
            console.log(`CallOverlay [${this.sessionUniqueId}]: Joining room ${uuid} as ${userName} (Hash: ${hash})`);
            this.roomUuid = uuid;
            this.localUserName = userName;
            this.localHash = hash || userName; 
            this.isRoomMode = true;
            this.isActive = true;

            // Start signaling and media in parallel to avoid blocking connection on permission prompt
            await this.setupLocalMedia(); 
            this.subscribeToChannels();
            this.startPresence();
            this.startInactivityTimer();
            
            
            const customerId = this.$shop?.customer_id;
            if (window.Echo && customerId) {
                 console.log(`CallOverlay [${this.sessionUniqueId}]: Subscribing to private user.${customerId}`);
                 window.Echo.private(`user.${customerId}`).listen('.call-signal', (data) => this.handleSignal(data));
            }

            // Enter fullscreen by default on join
            this.$nextTick(() => {
                this.toggleFullscreen();
            });
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
            const seenHashes = new Map();

            Object.keys(this.peers).forEach(id => {
                const peer = this.peers[id];
                
                // Aggressive deduplication: if we see two sessions with the same hash, 
                // remove the older one if it's not connected.
                if (peer.hash) {
                    const existingId = seenHashes.get(peer.hash);
                    if (existingId) {
                        const existingPeer = this.peers[existingId];
                        if (!peer.connected && existingPeer.lastSeen > peer.lastSeen) {
                            this.removePeer(id);
                            return;
                        } else if (!existingPeer.connected && peer.lastSeen > existingPeer.lastSeen) {
                            this.removePeer(existingId);
                        }
                    }
                    seenHashes.set(peer.hash, id);
                }

                // Standard stale cleanup
                const timeout = peer.connected ? 45000 : 15000;
                if (peer.lastSeen && now - peer.lastSeen > timeout) {
                    console.log(`Room: Cleaning up stale peer ${id} (${peer.name})`);
                    this.removePeer(id);
                }
            });
        },

        async setupLocalMedia() {
            // 1. Generate local fingerprint ASYNC - don't block rest of setup
            this.generateLocalFingerprint();

            try {
                // 2. Request media
                const constraints = {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 },
                        facingMode: this.cameraFacing,
                        faceFraming: true,
                        focusMode: { ideal: 'continuous' },
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
                    this.isCameraDenied = false;
                } catch (e) {
                    console.warn('Room: Advanced constraints failed, falling back to simple', e);
                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                        this.isCameraDenied = false;
                    } catch (fallbackError) {
                        console.error('Room: Media access denied', fallbackError);
                        this.isCameraDenied = true;
                    }
                }
                
                if (this.localStream) {
                    this.detectZoomCapabilities();

                    this.localStream.getTracks().forEach(track => {
                        console.log(`Room: Local Track ${track.kind} (${track.label}): enabled=${track.enabled}, state=${track.readyState}`);
                    });
                    
                    // NEW: Force sync tracks to all existing peers in case they were created before media was ready
                    this.syncTracksToAllPeers();
                }

                this.$nextTick(() => this.rebindVideos());
            } catch (globalError) { 
                console.error('Room: setupLocalMedia critical failure', globalError); 
            }
        },

        handleSignal(data) {
            const signal = data.signal_data;
            const senderName = data.sender_name;
            const senderHash = signal.sender_hash || senderName; 
            const senderSessionId = signal.sender_session_id;

            // Filter out self-signals
            if (senderSessionId === this.sessionUniqueId) return;
            
            // If targeted and not for us
            if (signal.target && signal.target !== this.sessionUniqueId) {
                // Also check if targeted to legacy hash/name only if target is not a session ID
                const isTargetedToMe = signal.target === this.localHash || signal.target === this.localUserName;
                if (!isTargetedToMe) return;
            }

            console.log(`CallOverlay [${this.sessionUniqueId}]: Receiving ${signal.type} from ${senderName} (Session: ${senderSessionId})`);
            const peerKey = senderSessionId;

            if (signal.type === 'presence') {
                const now = Date.now();
                
                // Aggressive session deduplication: 
                // If we see a newer session ID for the same user hash, immediately remove the old one.
                Object.keys(this.peers).forEach(id => {
                    const p = this.peers[id];
                    if (p.hash === senderHash && id !== senderSessionId) {
                        console.log(`Room: Deduplicating old session ${id} for user ${senderName}`);
                        this.removePeer(id);
                    }
                });

                const isInitiator = this.sessionUniqueId < senderSessionId;
                
                if (!this.peers[peerKey]) {
                    this.peers = {
                        ...this.peers,
                        [peerKey]: { 
                            name: senderName,
                            hash: senderHash,
                            pc: null, stream: null, connected: false, streamReady: false, 
                            iceQueue: [], fingerprint: signal.fingerprint, verified: false,
                            lastSeen: now,
                            makingOffer: false,
                            ignoreOffer: false,
                            watchdog: null
                        }
                    };
                    this.sendSignal({ type: 'presence', target: senderSessionId, fingerprint: this.localFingerprint });
                } else {
                    const p = this.peers[peerKey];
                    p.lastSeen = now;
                    p.name = senderName; 
                    p.hash = senderHash;
                    if (signal.fingerprint) p.fingerprint = signal.fingerprint;
                }

                if (isInitiator) {
                    const peer = this.peers[peerKey];
                    // If no PC yet, or failed, or stuck in 'new' for too long
                    const shouldInitiate = !peer.pc || ['failed', 'closed'].includes(peer.pc.connectionState);
                    
                    if (shouldInitiate) {
                        console.log(`Room: I am initiator for ${senderName} (${peerKey}). Creating session...`);
                        this.createPeerConnection(peerKey, senderName);
                        if (signal.fingerprint) peer.fingerprint = signal.fingerprint;
                        this.startConnectionWatchdog(peerKey);
                    }
                }
            } else if (['offer', 'answer', 'candidate', 'hangup'].includes(signal.type)) {
                if (signal.type === 'offer') this.handleOffer(peerKey, senderName, signal);
                else if (signal.type === 'answer') this.handleAnswer(peerKey, signal);
                else if (signal.type === 'candidate') this.handleCandidate(peerKey, signal);
                else if (signal.type === 'hangup') this.removePeer(peerKey);
            }
        },

        normalizeSDP(sdp) {
            if (!sdp) return '';
            // Minimalist cleanup only - don't break the structure
            return sdp.split(/\r?\n/)
                      .map(line => line.trim())
                      .filter(line => line.length > 0)
                      .join('\r\n') + '\r\n';
        },

        async toggleCameraFacing() {
            if (this.isSharingScreen) return;
            
            const newFacing = this.cameraFacing === 'user' ? 'environment' : 'user';
            console.log(`Room: Attempting to switch camera facing to ${newFacing}`);
            
            try {
                // CRITICAL: Stop existing video tracks BEFORE requesting new ones on mobile
                if (this.localStream) {
                    this.localStream.getVideoTracks().forEach(track => {
                        console.log(`Room: Stopping old ${track.label} track`);
                        track.stop();
                    });
                }

                // Get new video track with updated facingMode
                const newStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: newFacing,
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        zoom: true // Keep zoom capability if possible
                    }
                });
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                this.cameraFacing = newFacing;
                
                // Add the new track to localStream and remove the old one
                const currentVideoTrack = this.localStream.getVideoTracks()[0];
                if (currentVideoTrack) this.localStream.removeTrack(currentVideoTrack);
                this.localStream.addTrack(newVideoTrack);

                // Update zoom capabilities for the new camera
                this.detectZoomCapabilities();

                // Replace track in existing peer connections
                Object.values(this.peers).forEach(peer => {
                    if (peer.pc) {
                        const senders = peer.pc.getSenders();
                        const videoSender = senders.find(s => s.track && s.track.kind === 'video');
                        if (videoSender) {
                            videoSender.replaceTrack(newVideoTrack).catch(err => {
                                console.warn('Room: peer.replaceTrack failed after flip', err);
                            });
                        }
                    }
                });

                this.$nextTick(() => this.rebindVideos());
            } catch (err) {
                console.error('Room: toggleCameraFacing failed', err);
                // Fallback attempt to restore user camera
                if (this.cameraFacing !== 'user') {
                    this.cameraFacing = 'user';
                    this.setupLocalMedia();
                }
            }
        },

        startConnectionWatchdog(id) {
            const peer = this.peers[id];
            if (!peer || peer.watchdog) return;

            console.log(`WebRTC: Starting watchdog for ${id}`);
            peer.watchdog = setTimeout(() => {
                if (this.peers[id]) {
                    const pc = this.peers[id].pc;
                    const state = pc?.connectionState || 'none';
                    
                    if (state !== 'connected' && state !== 'completed') {
                        console.warn(`WebRTC: Watchdog triggered for ${id}. Current state: ${state}. Action: ${state === 'new' ? 'Recreate' : 'Restart ICE'}`);
                        
                        if (state === 'new' || state === 'closed') {
                            // If it never even started, recreate the connection entirely
                            this.removePeer(id);
                            // It will be recreated by presence broadcast or manual poke
                        } else {
                            pc.restartIce();
                        }
                        
                        // Reset watchdog to try again later if still failing
                        peer.watchdog = null;
                        this.startConnectionWatchdog(id);
                    } else {
                        peer.watchdog = null;
                    }
                }
            }, 15000); // 15 second timeout
        },

        async handleOffer(id, name, signal) {
            const peer = this.peers[id];
            if (!peer) return;

            const polite = this.sessionUniqueId < id; // Smaller session ID is polite
            
            try {
                const offerCollision = signal.type === 'offer' && (peer.makingOffer || peer.pc.signalingState !== 'stable');
                peer.ignoreOffer = !polite && offerCollision;

                if (peer.ignoreOffer) {
                    console.log(`WebRTC: Glare detected. Ignoring offer from ${id} (I am impolite)`);
                    return;
                }

                const sdp = this.normalizeSDP(signal.sdp);
                await peer.pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp }));
                
                while (peer.iceQueue.length > 0) {
                    const cand = peer.iceQueue.shift();
                    await peer.pc.addIceCandidate(new RTCIceCandidate(cand)).catch(() => {});
                }

                const answer = await peer.pc.createAnswer();
                const cleanAnswer = this.normalizeSDP(answer.sdp);
                await peer.pc.setLocalDescription({ type: 'answer', sdp: cleanAnswer });
                this.sendSignal({ type: 'answer', sdp: cleanAnswer, target: id, fingerprint: this.localFingerprint });
            } catch (err) {
                console.warn(`WebRTC: handleOffer failed for ${id}`, err);
            }
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
                } catch (err) { 
                    console.warn(`WebRTC: handleAnswer failed for ${id}`, err);
                }
            }
        },

        async handleCandidate(id, signal) {
            const peer = this.peers[id];
            if (!peer || !peer.pc) return;

            try {
                if (signal.candidate) {
                    if (peer.pc.remoteDescription && peer.pc.remoteDescription.type) {
                        await peer.pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
                    } else {
                        peer.iceQueue.push(signal.candidate);
                    }
                }
            } catch (err) {
                if (!peer.ignoreOffer) {
                    console.warn(`WebRTC: handleCandidate failed for ${id}`, err);
                }
            }
        },

        createPeerConnection(id, name = null) {
            // If exists and not closed, reuse
            if (this.peers[id]?.pc && this.peers[id].pc.connectionState !== 'closed') {
                return this.peers[id].pc;
            }

            console.log(`WebRTC: Creating new PeerConnection for ${id}`);
            const pc = new RTCPeerConnection(this.configuration);
            
            // Initialization: Ensure all reactive properties exist from the start
            if (!this.peers[id]) {
                this.peers = {
                    ...this.peers,
                    [id]: { 
                        name: name || id,
                        pc, stream: null, connected: false, streamReady: false, 
                        iceQueue: [], fingerprint: null, verified: false, 
                        lastSeen: Date.now(),
                        makingOffer: false, ignoreOffer: false, watchdog: null
                    }
                };
            } else {
                // Update existing peer object with new PC and reset states
                const p = this.peers[id];
                p.pc = pc;
                p.connected = false;
                p.streamReady = false;
                p.iceQueue = [];
                p.makingOffer = false;
                p.ignoreOffer = false;
                if (p.watchdog) clearTimeout(p.watchdog);
                p.watchdog = null;
            }

            // CRITICAL: Assignment of handlers BEFORE adding tracks
            pc.onnegotiationneeded = async () => {
                const peer = this.peers[id];
                if (!peer || peer.ignoreOffer) return;
                
                try {
                    console.log(`WebRTC: onnegotiationneeded for ${id}`);
                    peer.makingOffer = true;
                    const offer = await pc.createOffer();
                    if (pc.signalingState !== 'stable') return;
                    
                    const cleanSdp = this.normalizeSDP(offer.sdp);
                    await pc.setLocalDescription({ type: 'offer', sdp: cleanSdp });
                    this.sendSignal({ type: 'offer', sdp: cleanSdp, target: id, fingerprint: this.localFingerprint });
                } catch (err) {
                    console.warn(`WebRTC: onnegotiationneeded failed for ${id}`, err);
                } finally {
                    if (this.peers[id]) this.peers[id].makingOffer = false;
                }
            };

            pc.onicecandidate = (e) => {
                if (e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: id });
            };

            pc.ontrack = (e) => {
                console.log(`WebRTC: Received remote track from ${id}`, e.track.kind);
                if (e.streams && e.streams[0]) {
                    this.peers[id].stream = e.streams[0];
                    this.peers[id].streamReady = true;
                    this.rebindVideos();
                }
            };

            pc.onconnectionstatechange = () => {
                const state = pc.connectionState;
                console.log(`WebRTC: Connection state for ${id} -> ${state}`);
                
                if (state === 'connected' || state === 'completed') {
                    this.updatePeerConnectedState(id, 'connected');
                } else if (state === 'failed' || state === 'disconnected') {
                    this.startConnectionWatchdog(id);
                }
            };

            pc.oniceconnectionstatechange = () => {
                const state = pc.iceConnectionState;
                console.log(`WebRTC: ICE state for ${id} -> ${state}`);
                this.updatePeerConnectedState(id, state);
                if (state === 'failed' || state === 'disconnected') {
                    this.startConnectionWatchdog(id);
                }
            };

            // Now add tracks - this will trigger onnegotiationneeded (correctly assigned above)
            if (this.localStream) {
                this.localStream.getTracks().forEach(t => {
                    let trackToUse = t;
                    if (t.kind === 'video' && this.isSharingScreen && this.screenStream) {
                        trackToUse = this.screenStream.getVideoTracks()[0] || t;
                    }
                    pc.addTrack(trackToUse, this.isSharingScreen ? this.screenStream : this.localStream);
                });
            }

            // Negotiation Poke: Fallback for browsers that don't trigger negotiation automatically
            setTimeout(() => {
                const peer = this.peers[id];
                if (peer && peer.pc && peer.pc.signalingState === 'stable' && !peer.connected) {
                    const isInitiator = this.sessionUniqueId < id;
                    if (isInitiator) {
                        console.log(`WebRTC: Poke! Triggering negotiation fallback for ${id}`);
                        pc.dispatchEvent(new Event('negotiationneeded'));
                    }
                }
            }, 3000);

            this.startConnectionWatchdog(id);
            return pc;
        },

        updatePeerConnectedState(id, state) {
            if (!this.peers[id]) return;
            
            if (['connected', 'completed'].includes(state)) {
                this.peers[id].connected = true;
                this.verifySecurity(id);
                this.$nextTick(() => this.rebindVideos());
                
                // Success! Clear watchdog
                if (this.peers[id].watchdog) {
                    clearTimeout(this.peers[id].watchdog);
                    this.peers[id].watchdog = null;
                }
            } else if (['disconnected', 'failed', 'closed'].includes(state)) {
                // Don't immediately set to false on 'disconnected' as it might recover
                if (state !== 'disconnected') {
                    this.peers[id].connected = false;
                }
            }
        },

        syncTracksToAllPeers() {
            if (!this.localStream) return;
            const tracks = this.localStream.getTracks();
            
            Object.keys(this.peers).forEach(id => {
                const peer = this.peers[id];
                if (peer.pc && peer.pc.connectionState !== 'closed') {
                    let needsRenegotiation = false;
                    const senders = peer.pc.getSenders();
                    
                    tracks.forEach(track => {
                        const sender = senders.find(s => s.track && s.track.kind === track.kind);
                        if (!sender) {
                            console.log(`Room: Adding missing track ${track.kind} to peer ${id}`);
                            peer.pc.addTrack(track, this.localStream);
                            needsRenegotiation = true;
                        } else if (sender.track !== track) {
                            console.log(`Room: Updating track ${track.kind} for peer ${id}`);
                            sender.replaceTrack(track);
                        }
                    });

                    if (needsRenegotiation && peer.pc.signalingState === 'stable') {
                        // If we are initiator, we can push a new offer
                        const isInitiator = this.sessionUniqueId < id;
                        if (isInitiator) {
                            console.log(`Room: Renegotiating for missing tracks with ${id}`);
                            // onnegotiationneeded handles this automatically when tracks are added
                        }
                    }
                }
            });
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
            
            const activeLocalStream = this.isSharingScreen ? this.screenStream : this.localStream;
            
            if (localMain && activeLocalStream) {
                if (localMain.srcObject !== activeLocalStream) localMain.srcObject = activeLocalStream;
                if (localMain.paused) localMain.play().catch(() => {});
            }
            if (localGrid && activeLocalStream) {
                if (localGrid.srcObject !== activeLocalStream) localGrid.srcObject = activeLocalStream;
                if (localGrid.paused) localGrid.play().catch(() => {});
            }
            
            const localWaiting = this.$refs.localVideoWaiting;
            if (localWaiting && this.localStream) {
                if (localWaiting.srcObject !== this.localStream) localWaiting.srcObject = this.localStream;
                if (localWaiting.paused) localWaiting.play().catch(() => {});
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

        async generateLocalFingerprint() {
            if (this.localFingerprint) return;
            try {
                const pc = new RTCPeerConnection(this.configuration);
                pc.addTransceiver('video', { direction: 'sendonly' });
                const offer = await pc.createOffer();
                const match = offer.sdp.match(/a=fingerprint:sha-256\s+(.*)/i);
                if (match) {
                    this.localFingerprint = match[1];
                    console.log('Room: Initial fingerprint generated:', this.localFingerprint);
                    if (this.isActive) this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                }
                pc.close();
            } catch (e) { console.warn('Room: Background fingerprint failed', e); }
        },

        removePeer(id) {
            const peer = this.peers[id];
            if (peer) {
                if (peer.pc) peer.pc.close();
                if (peer.watchdog) clearTimeout(peer.watchdog);
                const newPeers = { ...this.peers };
                delete newPeers[id];
                this.peers = newPeers;
            }
            
            if (this.peerCount === 0) {
                this.startInactivityTimer();
            }
        },

        startInactivityTimer() {
            this.stopInactivityTimer();
            if (this.peerCount > 0) return;
            
            console.log('Room: Starting 5-minute inactivity timer');
            this.inactivityTimer = setTimeout(() => {
                console.log('Room: 5-minute inactivity reached. Closing room.');
                this.cleanup();
            }, 5 * 60 * 1000); 
        },

        stopInactivityTimer() {
            if (this.inactivityTimer) {
                clearTimeout(this.inactivityTimer);
                this.inactivityTimer = null;
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
            if (this.isRetrying) return;
            console.log(`CallOverlay [${this.sessionUniqueId}]: Retry initiated. Attempts: ${this.reconnectAttempts}`);
            this.isRetrying = true;
            
            this.signalingGraceActive = true;
            if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
            
            this.signalingGraceTimeout = setTimeout(() => {
                this.signalingGraceActive = false;
                this.isRetrying = false;
            }, 15000);

            // NEW: Clear peers to force re-initiation of WebRTC
            console.log(`CallOverlay [${this.sessionUniqueId}]: Clearing existing peer connections for retry`);
            Object.values(this.peers).forEach(p => p.pc?.close());
            this.peers = {};
            
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                const conn = window.Echo.connector.pusher.connection;
                console.log(`CallOverlay [${this.sessionUniqueId}]: Resetting connection. state was: ${conn.state}`);
                
                try {
                    conn.disconnect();
                    setTimeout(() => {
                        console.log(`CallOverlay [${this.sessionUniqueId}]: Reconnecting...`);
                        conn.connect();
                    }, 500);
                } catch (e) {
                    console.error('Reset connection failed', e);
                    window.location.reload();
                }
            } else {
                console.warn('window.Echo not found, reloading page');
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
                    console.log('ScreenShare: Requesting display media...');
                    // Use standard options for better compatibility
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ 
                        video: { cursor: "always" },
                        audio: false 
                    });
                    
                    const screenTrack = this.screenStream.getVideoTracks()[0];
                    if (!screenTrack) throw new Error('No screen track obtained');

                    this.isSharingScreen = true; // Set state BEFORE replacing tracks

                    console.log('ScreenShare: Replacing tracks for peers:', Object.keys(this.peers));
                    Object.values(this.peers).forEach(p => {
                        if (p.pc) {
                            const senders = p.pc.getSenders();
                            const videoSender = senders.find(s => s.track?.kind === 'video');
                            if (videoSender) {
                                videoSender.replaceTrack(screenTrack).catch(err => {
                                    console.warn(`ScreenShare: replaceTrack failed for peer`, err);
                                });
                            }
                        }
                    });

                    this.$nextTick(() => this.rebindVideos());
                    
                    screenTrack.onended = () => {
                        console.log('ScreenShare: Track ended by user/system');
                        if (this.isSharingScreen) this.stopScreenShare();
                    };
                } else {
                    this.stopScreenShare();
                }
            } catch (e) { 
                console.warn('ScreenShare: Failed', e);
                this.stopScreenShare();
            }
        },

        stopScreenShare() {
            console.log('ScreenShare: Stopping display media...');
            if (this.screenStream) {
                this.screenStream.getTracks().forEach(t => t.stop());
                this.screenStream = null;
            }
            
            this.isSharingScreen = false;
            const camTrack = this.localStream?.getVideoTracks()[0];
            
            if (camTrack) {
                Object.values(this.peers).forEach(p => {
                    const sender = p.pc?.getSenders().find(s => s.track?.kind === 'video');
                    if (sender) {
                        sender.replaceTrack(camTrack).catch(err => {
                            console.warn(`ScreenShare: restore camera track failed`, err);
                        });
                    }
                });
            }
            this.$nextTick(() => this.rebindVideos());
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
                if (p.watchdog) clearTimeout(p.watchdog);
            });
            this.peers = {};
            this.isActive = false;
            
            // Intelligent Redirect: if on dedicated call page, go back or home
            if (window.location.pathname.includes('/call/')) {
                if (document.referrer && !document.referrer.includes('/call/')) {
                    window.location.href = document.referrer;
                } else {
                    window.location.href = '/';
                }
            }
            // If NOT on a dedicated call page (e.g. browsing product), 
            // no need to reload or redirect, just close the overlay (already done via isActive=false)
        }
    }
};
</script>

<style scoped>
.mirror { transform: scaleX(-1); }
* {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    user-select: none;
}
input, textarea {
    user-select: text;
}
</style>
