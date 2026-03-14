<template>
    <div v-if="isActive" 
         @mousemove="userActivity"
         @touchstart="userActivity"
         @click="toggleControls"
         class="fixed inset-0 z-[10000] bg-black text-white font-sans overflow-hidden transition-all duration-300">
        
        <!-- Video Layer (Base) -->
        <div class="absolute inset-0 bg-zinc-950">
            <!-- 1-on-1 Mode -->
            <div v-if="peerCount === 1" class="w-full h-full relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center overflow-hidden touch-none"
                     @touchstart="handleTouchStart"
                     @touchmove="handleTouchMove"
                     @touchend="handleTouchEnd"
                     @wheel.passive="handleWheel">
                    
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
                        
                        <!-- Zoom Slider for Main Local View -->
                        <div v-if="zoomCapabilities && controlsVisible" class="absolute right-8 top-1/2 -translate-y-1/2 flex flex-col items-center gap-4 bg-black/40 backdrop-blur-3xl p-4 rounded-full border border-white/10 z-[60] group pointer-events-auto">
                            <span class="text-[10px] font-bold text-white/40">🔭</span>
                            <div class="h-48 w-1 bg-white/10 rounded-full relative overflow-hidden group-hover:w-2 transition-all">
                                <input type="range" 
                                       :min="zoomCapabilities.min" 
                                       :max="zoomCapabilities.max" 
                                       :step="zoomCapabilities.step || 0.1" 
                                       :value="cameraZoom"
                                       @input="applyCameraZoom($event.target.value)"
                                       class="absolute inset-0 opacity-0 cursor-pointer z-10 [writing-mode:bt-lr] -rotate-180" 
                                       style="appearance: slider-vertical; width: 100%; height: 100%;">
                                <div class="absolute bottom-0 left-0 right-0 bg-[#7C45F5] transition-all" :style="{height: ((cameraZoom - zoomCapabilities.min) / (zoomCapabilities.max - zoomCapabilities.min) * 100) + '%'}"></div>
                            </div>
                            <span class="text-[8px] font-black text-white/60 tracking-tighter">{{ Math.round(cameraZoom * 10) / 10 }}x</span>
                        </div>
                    </template>
                </div>


                <div v-if="!peers[peerIds[0]]?.connected || !peers[peerIds[0]]?.streamReady" 
                    class="absolute inset-0 flex items-center justify-center bg-zinc-950/80 backdrop-blur-md z-30">
                    <div class="flex flex-col items-center gap-6">
                         <div class="w-16 h-16 border-4 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                         <div class="text-center">
                            <h3 class="text-lg font-black uppercase tracking-[0.4em] text-white">Установка связи...</h3>
                         </div>
                    </div>
                </div>
            </div>

            <div v-else-if="peerCount > 1" :class="gridClass" class="grid w-full h-full p-2 md:p-4 gap-2 md:gap-4 transition-all duration-500">
                <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center group/local touch-none"
                     @touchstart="handleTouchStart($event, true)"
                     @touchmove="handleTouchMove($event, true)"
                     @touchend="handleTouchEnd"
                     @wheel.passive="handleWheel($event, true)">
                    <video ref="localVideoGrid" autoplay muted playsinline 
                           :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                           :style="isFocusedOnSelf ? zoomStyle : {}"
                           class="w-full h-full transition-all duration-700"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 text-[8px] font-bold border border-white/10 uppercase tracking-tighter z-10 rounded-lg text-white/60">
                        Вы ({{ localUserName }})
                    </div>

                    <!-- Zoom Slider for Grid Local View -->
                    <div v-if="zoomCapabilities && controlsVisible" class="absolute right-3 top-1/2 -translate-y-1/2 flex flex-col items-center gap-2 bg-black/60 backdrop-blur-xl p-2 rounded-full border border-white/10 z-[60] pointer-events-auto">
                        <div class="h-24 w-1 bg-white/10 rounded-full relative overflow-hidden">
                            <input type="range" 
                                   :min="zoomCapabilities.min" 
                                   :max="zoomCapabilities.max" 
                                   :step="zoomCapabilities.step || 0.1" 
                                   :value="cameraZoom"
                                   @input="applyCameraZoom($event.target.value)"
                                   class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                                   style="appearance: slider-vertical; width: 100%; height: 100%;">
                            <div class="absolute bottom-0 left-0 right-0 bg-[#7C45F5] transition-all" :style="{height: ((cameraZoom - zoomCapabilities.min) / (zoomCapabilities.max - zoomCapabilities.min) * 100) + '%'}"></div>
                        </div>
                    </div>
                </div>
                <div v-for="id in peerIds" :key="id" 
                    class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center">
                    <video :id="'video_' + id" autoplay playsinline 
                           :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                           class="w-full h-full transition-all duration-700"></video>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="peerCount === 0" class="absolute inset-0 flex flex-col items-center justify-center z-10 pointer-events-none">
                <!-- (Same empty state content as before) -->
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
                            <h3 class="text-sm font-black uppercase tracking-[0.3em] text-red-500 mb-2">Ошибка сети</h3>
                            <button @click="retryEcho" :disabled="isRetrying" class="w-full py-4 bg-white text-black text-[10px] font-black uppercase rounded-2xl">Переподключиться</button>
                        </div>
                    </template>
                    <template v-else>
                        <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.3em] text-white">Ожидание участников</h3>
                        <p class="mt-4 text-[8px] uppercase tracking-widest text-zinc-500 font-bold max-w-xs mx-auto leading-relaxed">
                            Поделитесь ссылкой, чтобы начать разговор прямо сейчас
                        </p>
                        <button @click="copyRoomLink" 
                                class="mt-6 px-8 py-3 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-widest text-[#7C45F5] hover:bg-[#7C45F5] hover:text-white transition-all active:scale-95">
                            {{ roomLinkCopied ? 'Ссылка скопирована! ✅' : 'Скопировать ссылку' }}
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Interface Layer (Overlay) -->
        <div class="absolute inset-0 z-50 pointer-events-none flex flex-col justify-between p-4 md:p-8 landscape:flex-row landscape:justify-between items-stretch">
            
            <!-- Top Header (Floating) -->
            <div :class="{'opacity-0 -translate-y-10': !controlsVisible}"
                 class="flex justify-between items-start w-full transition-all duration-700 pointer-events-auto landscape:w-auto landscape:max-w-[300px]">
                <div class="bg-black/40 backdrop-blur-xl p-4 rounded-3xl border border-white/10 border-b-4 border-b-[#7C45F5]/50">
                    <div class="text-[8px] uppercase tracking-[0.3em] opacity-60 mb-1 flex items-center gap-2">
                        <span>{{ peerCount === 1 ? 'Видеозвонок' : (peerCount > 1 ? 'Групповая встреча' : 'Ожидание') }}</span>
                        <span class="w-1.5 h-1.5 rounded-full" :class="statusColor"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black uppercase italic tracking-tighter">{{ isRoomMode ? 'Защищенная комната' : 'Встреча активна' }}</h2>
                </div>
                <div v-if="peerCount > 0" class="bg-[#00FF41] text-black px-4 py-2 font-bold text-[10px] uppercase tracking-widest rounded-2xl ml-4 shadow-[0_0_20px_rgba(0,255,65,0.3)] landscape:hidden">
                    {{ peerCount + 1 }} в сети
                </div>
            </div>

            <!-- Side/Bottom Controls (Adaptive) -->
            <div class="flex flex-col items-center justify-end pointer-events-none landscape:flex-row-reverse landscape:items-center landscape:justify-end gap-6 h-full landscape:w-full">
                
                <!-- Main System Buttons (Floating circle cluster) -->
                <div :class="{'opacity-0 translate-y-10 landscape:translate-x-10 landscape:translate-y-0': !controlsVisible}"
                     class="flex justify-center flex-wrap gap-3 p-3 bg-black/40 backdrop-blur-3xl rounded-[40px] border border-white/10 shadow-2xl transition-all duration-700 pointer-events-auto landscape:flex-col">
                    
                    <button @click.stop="toggleMic" :class="[isMicOn ? 'bg-white text-black' : 'bg-red-500/20 text-red-500 border-red-500/40']"
                        class="h-12 w-12 md:h-14 md:w-14 rounded-full flex items-center justify-center border border-white/10 transition-all hover:scale-110 active:scale-95">
                        <span class="text-[8px] font-black uppercase">{{ isMicOn ? 'On' : 'Off' }}</span>
                    </button>
                    
                    <button @click.stop="endCall" 
                        class="h-12 w-12 md:h-14 md:w-14 rounded-full bg-red-600 text-white font-black flex items-center justify-center shadow-xl shadow-red-500/20 hover:scale-110 active:scale-95">
                        X
                    </button>
     
                    <button @click.stop="toggleCamera" :class="[isCameraOn ? 'bg-white text-black' : 'bg-zinc-800 text-white opacity-40']"
                        class="h-12 w-12 md:h-14 md:w-14 rounded-full flex items-center justify-center border border-white/10 transition-all hover:scale-110 active:scale-95">
                        <span class="text-[8px] font-black uppercase">Cam</span>
                    </button>

                    <button @click.stop="copyRoomLink" :class="[roomLinkCopied ? 'bg-[#7C45F5] text-white scale-110 shadow-[0_0_20px_rgba(124,69,245,0.4)]' : 'bg-zinc-800 text-white']"
                        class="h-12 w-12 md:h-14 md:w-14 rounded-full flex items-center justify-center border border-white/10 transition-all hover:scale-110 active:scale-95"
                        title="Скопировать ссылку">
                        <span class="text-[10px] font-black uppercase leading-none">{{ roomLinkCopied ? '✅' : '🔗' }}</span>
                    </button>

                    <button @click.stop="toggleScreenShare" :class="[isSharingScreen ? 'bg-[#00FF41] text-black' : 'bg-zinc-800 text-white opacity-40']"
                        class="h-12 w-12 md:h-14 md:w-14 rounded-full flex items-center justify-center border border-white/10 transition-all hover:scale-110 active:scale-95 landscape:hidden md:flex">
                        <span class="text-[8px] font-black uppercase">S</span>
                    </button>
                </div>

                <!-- Secondary Control Strip (Scaling, Fullscreen, Swap) -->
                <div :class="{'opacity-0 -translate-x-10': !controlsVisible}"
                     class="flex gap-2 p-2 bg-white/5 backdrop-blur-3xl rounded-full border border-white/10 transition-all duration-700 pointer-events-auto landscape:flex-col">
                    <button @click.stop="scalingMode = (scalingMode === 'cover' ? 'contain' : 'cover')" 
                            class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-all">
                        <span class="text-sm">{{ scalingMode === 'cover' ? '⬛' : '⬜' }}</span>
                    </button>
                    <button @click.stop="toggleFullscreen" 
                            class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-all">
                        <span class="text-sm">{{ isFullscreen ? '◢◣' : '⛶' }}</span>
                    </button>
                    <button v-if="peerCount === 1" @click.stop="isFocusedOnSelf = !isFocusedOnSelf" 
                            class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-all"
                            title="Переключить фокус">
                        <span class="text-sm">🔄</span>
                    </button>
                    <button @click.stop="toggleCameraFacing" 
                            class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-all"
                            title="Сменить камеру">
                        <span class="text-[14px]">📱</span>
                    </button>
                </div>
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
            controlsVisible: true,
            controlsTimeout: null,
            signalingGraceActive: false,
            signalingGraceTimeout: null,
            reconnectAttempts: 0,
            isRetrying: false,
            scalingMode: 'contain', // 'contain' (Fit) by default to avoid initial cropping
            sessionUniqueId: Math.random().toString(36).substring(7),
            isLandscape: window.innerWidth > window.innerHeight,
            cameraZoom: 1,
            initialCameraZoom: 1,
            cameraFacing: 'user', // 'user' or 'environment'
            zoomCapabilities: null,
            roomLinkCopied: false
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
    },

    beforeUnmount() {
        this.stopPresence();
        document.removeEventListener('fullscreenchange', this.handleFullscreenChange);
        window.removeEventListener('resize', this.updateOrientation);
        window.removeEventListener('orientationchange', this.updateOrientation);
        if (this.retryInterval) clearInterval(this.retryInterval);
        if (this.cleanupInterval) clearInterval(this.cleanupInterval);
    },

    methods: {
        // Pinch-to-Zoom Handlers
        handleTouchStart(e, isLocalGrid = false) {
            if (e.touches.length === 2) {
                this.initialDist = this.getDist(e.touches);
                this.initialZoom = this.zoomLevel;
                this.initialCameraZoom = this.cameraZoom;
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

        handleTouchMove(e, isLocalGrid = false) {
            if (e.touches.length === 2 && this.initialDist > 0) {
                e.preventDefault(); 
                const currentDist = this.getDist(e.touches);
                const scale = currentDist / this.initialDist;
                const isTargetingLocal = isLocalGrid || this.isFocusedOnSelf;

                if (isTargetingLocal && this.zoomCapabilities) {
                    // Hardware Zoom
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.initialCameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                } else {
                    // CSS Digital Zoom
                    this.zoomLevel = Math.max(1, Math.min(5, this.initialZoom * scale));
                    const currentCenter = this.getCenter(e.touches);
                    this.panX = this.initialPanX + (currentCenter.x - this.initialCenter.x);
                    this.panY = this.initialPanY + (currentCenter.y - this.initialCenter.y);
                }
            } else if (e.touches.length === 1 && this.zoomLevel > 1) {
                e.preventDefault(); 
                const deltaX = e.touches[0].clientX - this.initialCenter.x;
                const deltaY = e.touches[0].clientY - this.initialCenter.y;
                this.panX = this.initialPanX + deltaX;
                this.panY = this.initialPanY + deltaY;
            }
        },

        handleWheel(e, isLocalGrid = false) {
            // Trackpad pinch is usually ctrl + wheel
            if (e.ctrlKey) {
                e.preventDefault();
                const delta = -e.deltaY;
                const factor = 1.1;
                const scale = delta > 0 ? factor : 1/factor;
                const isTargetingLocal = isLocalGrid || this.isFocusedOnSelf;

                if (isTargetingLocal && this.zoomCapabilities) {
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.cameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                } else {
                    this.zoomLevel = Math.max(1, Math.min(5, this.zoomLevel * scale));
                }
            } else if (this.zoomLevel > 1) {
                e.preventDefault();
                this.panX -= e.deltaX;
                this.panY -= e.deltaY;
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

        async copyRoomLink() {
            try {
                const url = `${window.location.origin}/call/${this.roomUuid}`;
                await navigator.clipboard.writeText(url);
                this.roomLinkCopied = true;
                setTimeout(() => this.roomLinkCopied = false, 3000);
            } catch (e) {
                console.error('Copy room link failed', e);
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
            if (this.controlsVisible && this.isFullscreen) {
                this.controlsVisible = false;
                if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
            } else {
                this.userActivity();
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
                        facingMode: this.cameraFacing,
                        
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
                
                // Detect Zoom capabilities
                this.detectZoomCapabilities();

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

        async toggleCameraFacing() {
            if (this.isSharingScreen) return;
            
            this.cameraFacing = this.cameraFacing === 'user' ? 'environment' : 'user';
            console.log(`Room: Switching camera facing to ${this.cameraFacing}`);
            
            try {
                // Keep the old audio track
                const oldTracks = this.localStream.getTracks();
                const videoTrack = oldTracks.find(t => t.kind === 'video');
                
                // Get new video track with updated facingMode
                const newStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.cameraFacing,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                
                // Replace track in existing peer connections
                Object.values(this.peers).forEach(peer => {
                    if (peer.pc) {
                        const senders = peer.pc.getSenders();
                        const videoSender = senders.find(s => s.track && s.track.kind === 'video');
                        if (videoSender) {
                            videoSender.replaceTrack(newVideoTrack);
                        }
                    }
                });
                
                // Update localStream
                if (videoTrack) {
                    videoTrack.stop();
                    this.localStream.removeTrack(videoTrack);
                }
                this.localStream.addTrack(newVideoTrack);
                
                // Detect Zoom again for the new camera
                this.detectZoomCapabilities();
                
                this.$nextTick(() => this.rebindVideos());
            } catch (e) {
                console.error('Room: Camera flip failed', e);
                // Revert state if failed
                this.cameraFacing = this.cameraFacing === 'user' ? 'environment' : 'user';
            }
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
                this.localStream.getTracks().forEach(t => {
                    let trackToUse = t;
                    if (t.kind === 'video' && this.isSharingScreen && this.screenStream) {
                        trackToUse = this.screenStream.getVideoTracks()[0] || t;
                    }
                    pc.addTrack(trackToUse, this.isSharingScreen ? this.screenStream : this.localStream);
                });
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
            
            const activeLocalStream = this.isSharingScreen ? this.screenStream : this.localStream;
            
            if (localMain && activeLocalStream && localMain.srcObject !== activeLocalStream) {
                localMain.srcObject = activeLocalStream;
                localMain.play().catch(() => {});
            }
            if (localGrid && activeLocalStream && localGrid.srcObject !== activeLocalStream) {
                localGrid.srcObject = activeLocalStream;
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
                    console.log('ScreenShare: Requesting display media...');
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const screenTrack = this.screenStream.getVideoTracks()[0];
                    
                    if (!screenTrack) throw new Error('No screen track obtained');

                    console.log('ScreenShare: Replacing tracks for peers:', Object.keys(this.peers));
                    Object.values(this.peers).forEach(p => {
                        const sender = p.pc?.getSenders().find(s => s.track?.kind === 'video');
                        if (sender) {
                            sender.replaceTrack(screenTrack).catch(err => {
                                console.warn(`ScreenShare: replaceTrack failed for peer`, err);
                            });
                        }
                    });

                    this.isSharingScreen = true;
                    this.$nextTick(() => this.rebindVideos());
                    
                    screenTrack.onended = () => {
                        console.log('ScreenShare: Track ended by user/system');
                        if (this.isSharingScreen) this.toggleScreenShare();
                    };
                } else {
                    console.log('ScreenShare: Stopping display media...');
                    if (this.screenStream) {
                        this.screenStream.getTracks().forEach(t => t.stop());
                        this.screenStream = null;
                    }
                    
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
                    
                    this.isSharingScreen = false;
                    this.$nextTick(() => this.rebindVideos());
                }
            } catch (e) { 
                console.warn('ScreenShare: Failed', e);
                this.isSharingScreen = false;
                if (this.screenStream) this.screenStream.getTracks().forEach(t => t.stop());
                this.screenStream = null;
            }
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
