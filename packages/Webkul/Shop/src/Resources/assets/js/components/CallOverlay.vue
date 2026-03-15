<template>
    <div v-if="isActive" 
         ref="overlayRoot"
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
                    
                    <div class="w-full h-full relative overflow-hidden" v-show="!isFocusedOnSelf">
                         <video :id="'video_' + peerIds[0]" 
                                autoplay playsinline 
                                :style="zoomStyle"
                                :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                                class="w-full h-full pointer-events-none transition-all duration-700"></video>
                         
                         <div v-if="zoomLevel > 1" @click.stop="resetZoom" class="absolute top-24 left-6 bg-[#7C45F5] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest cursor-pointer animate-bounce z-20">
                             {{ Math.round(zoomLevel * 100) }}% (Reset)
                         </div>
                    </div>

                    <div class="w-full h-full relative overflow-hidden" v-show="isFocusedOnSelf">
                        <video ref="localVideoMain" 
                               autoplay muted playsinline 
                               :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                               :style="zoomStyle"
                               class="w-full h-full transition-all duration-700"></video>
                    </div>
                </div>

                <!-- Unified Identity + End Call Badge (Top Left) -->
                <div v-if="peers[peerIds[0]]?.connected" 
                     class="absolute top-8 left-8 flex items-center gap-2 z-[100] transition-all duration-700 pointer-events-auto"
                     :class="{'opacity-0 -translate-y-10': !controlsVisible}">
                    <div class="flex items-center gap-2 bg-black/40 backdrop-blur-3xl border border-white/10 shadow-2xl pl-1 pr-4 py-1.5 leading-none rounded-2xl pointer-events-auto">
                        <!-- Focus Toggle Button -->
                        <button @click.stop="toggleFocus" 
                                class="flex h-10 w-10 items-center justify-center bg-[#7C45F5] text-white font-black shadow-lg shadow-[#7C45F5]/20 leading-none ring-1 ring-white/10 rounded-xl hover:scale-105 active:scale-95 transition-all group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[12px] font-black uppercase italic tracking-tighter text-white/90 leading-tight">
                                @{{ cleanPeerName(peers[peerIds[0]].name) }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <div class="w-1 h-1 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span class="text-[7px] font-black uppercase tracking-[0.2em] text-white/30">В эфире</span>
                            </div>
                        </div>

                        <!-- Fullscreen Toggle Button -->
                        <button @click.stop="toggleFullscreen" 
                                class="ml-2 flex h-8 w-8 items-center justify-center bg-white/5 text-white/40 hover:text-white border border-white/10 rounded-lg hover:bg-white/10 transition-all">
                            <svg v-if="!isFullscreen" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 15l-6 6m0 0l6-6M3 21v-6m0 6h6m6-6l6 6m0 0l-6-6m6 6v-6m0 6h-6" />
                            </svg>
                        </button>
                    </div>
                </div>



                <!-- Connection/Track Loading Overlay -->
                <div v-if="!peers[peerIds[0]]?.connected || (!peers[peerIds[0]]?.hasVideo && (Date.now() - peers[peerIds[0]]?.connectedAt <= 3000))" 
                    class="absolute inset-0 flex items-center justify-center bg-zinc-900/60 backdrop-blur-[2px] z-30 transition-all duration-500">
                    <div class="flex flex-col items-center gap-6">
                         <div class="w-12 h-12 border-4 border-t-[#7C45F5] border-white/10 rounded-full animate-spin"></div>
                         <div class="text-center">
                             <h3 class="text-sm font-black uppercase tracking-[0.4em] text-white/80">Соединение...</h3>
                         </div>
                    </div>
                </div>

                <!-- No Camera Warning for Peer (with 3s grace period) -->
                <div v-if="peers[peerIds[0]]?.connected && !peers[peerIds[0]]?.hasVideo && (Date.now() - peers[peerIds[0]]?.connectedAt > 3000)" 
                     class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-900/40 z-20">
                     <div class="w-20 h-20 rounded-full bg-black/40 flex items-center justify-center mb-4">
                        <span class="text-4xl opacity-40">🎥🚫</span>
                     </div>
                     <p class="text-[10px] font-black uppercase tracking-widest text-white/40">Камера участника отключена</p>
                </div>
            </div>

            <div v-else-if="peerCount > 1" class="w-full h-full relative p-2 md:p-4 transition-all duration-500">
                <!-- Focused Layout -->
                <div v-if="focusedPeerId" class="flex flex-col w-full h-full gap-2 md:gap-4">
                    <!-- Main Area -->
                    <div class="flex-grow relative overflow-hidden rounded-[2.5rem] bg-zinc-900 border border-white/10 flex items-center justify-center group/main">
                        <template v-if="isFocusedOnSelf">
                            <video ref="localVideoFocused" autoplay muted playsinline 
                                   :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                                   class="w-full h-full transition-all duration-700"></video>
                        </template>
                        <template v-else>
                            <video :id="'video_focused_' + focusedPeerId" autoplay playsinline 
                                   :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                                   class="w-full h-full transition-all duration-700"></video>
                        </template>

                        <!-- Focus Badge (Top Left) -->
                        <div class="absolute top-8 left-8 flex items-center gap-2 z-20">
                            <div class="flex items-center gap-2 bg-black/40 backdrop-blur-3xl border border-white/10 shadow-2xl pl-1 pr-4 py-1.5 leading-none rounded-2xl">
                                <button @click.stop="togglePeerFocus(null)" 
                                        class="flex h-10 w-10 items-center justify-center bg-[#7C45F5] text-white font-black shadow-lg shadow-[#7C45F5]/20 leading-none ring-1 ring-white/10 rounded-xl hover:scale-105 active:scale-95 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7" />
                                    </svg>
                                </button>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-[12px] font-black uppercase italic tracking-tighter text-white/90 leading-tight">
                                        @{{ isFocusedOnSelf ? cleanLocalName : cleanPeerName(peers[focusedPeerId]?.name) }}
                                    </span>
                                    <span class="text-[7px] font-black uppercase tracking-[0.2em] text-[#7C45F5]">В ФОКУСЕ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Strip -->
                    <div class="h-24 md:h-32 flex items-center gap-2 md:gap-4 overflow-x-auto overflow-y-hidden px-1 py-1 no-scrollbar">
                        <!-- Local Thumb -->
                        <div @click="togglePeerFocus('local')" 
                             :class="{'ring-2 ring-[#7C45F5] scale-[1.02]': isFocusedOnSelf}"
                             class="flex-shrink-0 h-full aspect-video rounded-xl bg-zinc-950 border border-white/10 overflow-hidden relative cursor-pointer hover:border-white/20 transition-all">
                            <video ref="localVideoThumb" autoplay muted playsinline 
                                   :class="[{mirror: !isSharingScreen}]"
                                   class="w-full h-full object-cover opacity-60"></video>
                            <div class="absolute top-2 left-2 flex items-center gap-1 bg-black/40 backdrop-blur-md px-1.5 py-0.5 rounded-sm border border-white/5">
                                <div class="flex h-3 w-3 items-center justify-center bg-[#7C45F5] text-white rounded-[2px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Peer Thumbs -->
                        <div v-for="id in peerIds" :key="'thumb_' + id" 
                             @click="togglePeerFocus(id)"
                             :class="{'ring-2 ring-[#7C45F5] scale-[1.02]': focusedPeerId === id}"
                             class="flex-shrink-0 h-full aspect-video rounded-xl bg-zinc-950 border border-white/10 overflow-hidden relative cursor-pointer hover:border-white/20 transition-all">
                            <video :id="'video_thumb_' + id" autoplay playsinline class="w-full h-full object-cover opacity-60"></video>
                            <div class="absolute top-2 left-2 flex items-center gap-1 bg-black/40 backdrop-blur-md px-1.5 py-0.5 rounded-sm border border-white/5 text-[8px] font-black uppercase text-white/90">
                                @{{ cleanPeerName(peers[id].name) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Layout (Normal) -->
                <div v-else :class="gridClass" class="grid w-full h-full gap-2 md:gap-4 transition-all duration-500">
                    <div class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center group/local touch-none cursor-pointer"
                         @click="togglePeerFocus('local')"
                         @touchstart="handleTouchStart($event, true)"
                         @touchmove="handleTouchMove($event, true)"
                         @touchend="handleTouchEnd"
                         @wheel="handleWheel($event, true)">
                        <video ref="localVideoGrid" autoplay muted playsinline 
                               :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain', {mirror: !isSharingScreen}]"
                               :style="isFocusedOnSelf ? zoomStyle : {}"
                               class="w-full h-full transition-all duration-700"></video>
                        <!-- Shapik Badge (Grid Local) -->
                        <div class="absolute top-6 left-6 flex items-center gap-1.5 bg-black/60 backdrop-blur-md border border-white/20 px-2 py-1 shadow-xl z-20 rounded-sm">
                            <div class="flex h-5 w-5 items-center justify-center bg-[#7C45F5] text-white rounded-sm shadow-sm ring-1 ring-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="text-[9px] md:text-[10px] font-black uppercase italic tracking-tighter text-white/90">
                                @{{ cleanLocalName }}
                            </div>
                        </div>
                    </div>
                    <div v-for="id in peerIds" :key="id" 
                        @click="togglePeerFocus(id)"
                        class="relative overflow-hidden rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center cursor-pointer">
                        <video :id="'video_' + id" autoplay playsinline 
                               :class="[scalingMode === 'cover' ? 'object-cover' : 'object-contain']"
                               class="w-full h-full transition-all duration-700"></video>
                        <!-- Shapik Badge (Grid Peer) -->
                        <div class="absolute top-6 left-6 flex items-center gap-1.5 bg-black/60 backdrop-blur-md border border-white/20 px-2 py-1 shadow-xl z-20 rounded-sm">
                            <div class="flex h-5 w-5 items-center justify-center bg-[#7C45F5] text-white rounded-sm shadow-sm ring-1 ring-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="text-[9px] md:text-[10px] font-black uppercase italic tracking-tighter text-white/90">
                                @{{ cleanPeerName(peers[id].name) }}
                            </div>
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
                                    <div class="mt-2 pt-2 border-t border-red-500/10 text-[7px] font-mono text-zinc-500 space-y-1">
                                        <p>Client: window.Laravel.reverbAppKey = {{ window.Laravel?.reverbAppKey ? 'OK' : 'MISSING' }}</p>
                                        <p>Protocol: {{ window.location.protocol === 'https:' ? 'WSS (Secure)' : 'WS' }}</p>
                                        <p>Attempt: {{ reconnectAttempts }} / 5</p>
                                    </div>
                                </div>

                                <button @click="retryEcho" 
                                        :disabled="isRetrying"
                                        class="mt-4 px-6 py-2.5 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-red-500/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                                    {{ isRetrying ? 'Подключение...' : 'Попробовать снова' }}
                                </button>

                            </div>
                        </template>
                        <template v-else>
                            <h3 class="text-xs md:text-sm font-black uppercase tracking-[0.3em] text-white/40">Ожидание других участников</h3>
                            <div class="mt-4 flex flex-col items-center gap-3">
                                <!-- Reconnect button removed -->
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- PIP Self-View (Floating Top-Right) -->
        <div v-show="isActive && (peerCount > 0 || isJoined) && isCameraOn && !isFocusedOnSelf" 
             class="absolute top-8 right-8 w-24 h-32 md:w-40 md:h-56 rounded-2xl bg-zinc-900 border border-white/20 shadow-2xl overflow-hidden z-[200] transition-all duration-700"
             :class="{'opacity-0 translate-y-[-100%]': !controlsVisible}">
             <video ref="localVideoPip" 
                    autoplay muted playsinline 
                    :class="[{mirror: !isSharingScreen}]"
                    class="w-full h-full object-cover"></video>
             
             <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md border border-white/10 px-2 py-1 rounded-sm flex items-center gap-1.5">
                 <div class="flex h-3 w-3 items-center justify-center bg-[#7C45F5] text-white rounded-[2px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                 </div>
                 <span class="text-[8px] font-black uppercase tracking-tighter text-white/90">ВЫ</span>
             </div>
        </div>

        <!-- Interface Layer (Overlay) -->
        <div class="absolute inset-0 z-50 pointer-events-none flex flex-col justify-between p-4 md:p-8 landscape:flex-row landscape:justify-between items-stretch">
            

            <!-- Horizontal Control Bar (Bottom Center) -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex items-center z-[100] pointer-events-none">
                <div :class="{'opacity-0 translate-y-10': !controlsVisible}"
                     class="flex items-center gap-2.5 p-2.5 bg-black/40 backdrop-blur-3xl rounded-[2rem] border border-white/10 shadow-2xl transition-all duration-700 pointer-events-auto">
                    
                    <!-- Mic Toggle -->
                    <div class="flex flex-col items-center gap-1.5">
                        <button @click.stop="toggleMic" :class="[isMicOn ? 'bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30' : 'bg-red-600 text-white shadow-lg shadow-red-600/30']"
                            class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                            <svg v-if="isMicOn" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                        </button>
                        <span class="text-[7px] font-black uppercase tracking-widest text-white/40">Mic</span>
                    </div>

                    <!-- Camera Toggle -->
                    <div class="flex flex-col items-center gap-1.5">
                        <button @click.stop="toggleCamera" :class="[isCameraOn ? 'bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30' : 'bg-red-600 text-white shadow-lg shadow-red-600/30']"
                            class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                            <svg v-if="isCameraOn" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                        </button>
                        <span class="text-[7px] font-black uppercase tracking-widest text-white/40">Cam</span>
                    </div>

                    <!-- Redundant Focus Button Removed (Now in Badge) -->
                    <!-- Screen Share Toggle -->
                    <div v-if="!isMobile" class="flex flex-col items-center gap-1.5">
                        <button @click.stop="toggleScreenShare" :class="[isSharingScreen ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-black/40 text-white']"
                            class="h-11 w-11 rounded-2xl flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </button>
                        <span class="text-[7px] font-black uppercase tracking-widest text-white/40">Share</span>
                    </div>


                    <!-- Camera Swap (Now inside the vertical bar on mobile) -->
                    <div v-if="isMobile" class="flex flex-col items-center gap-1.5">
                        <button @click.stop="toggleCameraFacing"
                            class="h-11 w-11 rounded-2xl bg-[#7C45F5] text-white shadow-lg shadow-[#7C45F5]/30 flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </button>
                        <span class="text-[7px] font-black uppercase tracking-widest text-white/40">Swap</span>
                    </div>

                    <!-- End Call Button (New in Toolbar) -->
                    <div class="flex flex-col items-center gap-1.5">
                        <button @click.stop="endCall" 
                                class="h-11 w-11 rounded-2xl bg-red-600 text-white shadow-lg shadow-red-600/30 flex items-center justify-center border border-white/10 transition-all hover:scale-105 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <span class="text-[7px] font-black uppercase tracking-widest text-white/40">End</span>
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
            isActive: false,
            localStream: null,
            localUserName: '',
            localHash: '', 
            roomUuid: null,
            isRoomMode: false,
            peers: {}, 
            focusedPeerId: null, // 'local' or peerKey
            localFingerprint: null,
            signalingState: (window.Echo?.connector?.pusher?.connection?.state) || 'connecting',
            isMicOn: true,
            isCameraOn: true,
            isCameraDenied: false,
            isSharingScreen: false,
            screenStream: null,
            configuration: {
                iceServers: [
                    { urls: 'stun:stun.meanly.ru:3478' },
                ],
                iceCandidatePoolSize: 0
            },
            presenceInterval: null,
            cleanupInterval: null,
            retryInterval: null,
            interactionTimeout: null,
            isInteracting: false,
            isJoined: false,
            lobbyName: '',
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
            lastTapTime: 0,
            lastSignalReceivedAt: null
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
                transform: `scale(${this.zoomLevel}) translate3d(${this.panX / this.zoomLevel}px, ${this.panY / this.zoomLevel}px, 0)`,
                transition: this.isInteracting ? 'none' : 'transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1)',
                willChange: this.isInteracting ? 'transform' : 'auto',
                pointerEvents: 'none'
            };
        },
        isFocusedOnSelf() {
            return this.focusedPeerId === 'local';
        },
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },
        activeLocalStream() {
            return this.isSharingScreen ? this.screenStream : this.localStream;
        },
        isGuest() {
            return !this.localUserName || this.localUserName === 'Гость';
        },
        cleanLocalName() {
            return this.cleanPeerName(this.localUserName);
        }
    },

    watch: {
        focusedPeerId() {
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
            const now = Date.now();
            if (this.lastTapTime && (now - this.lastTapTime < 300) && e.touches.length === 1) {
                console.log('CallOverlay: Double tap detected -> Swapping camera');
                this.toggleCameraFacing();
                this.lastTapTime = 0;
                return;
            }
            this.lastTapTime = now;

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
            this.setInteracting();
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
            this.setInteracting();
            if (e.ctrlKey || e.metaKey) {
                e.preventDefault();
                // Mac trackpad pinch-to-zoom uses wheel with ctrlKey or metaKey
                const delta = -e.deltaY;
                const factor = 1.02; // More subtle for high-freq wheel events
                const scale = delta > 0 ? factor : 1/factor;
                const isTargetingLocal = isLocalGrid || this.isFocusedOnSelf;

                if (isTargetingLocal && this.zoomCapabilities) {
                    const newZoom = Math.max(this.zoomCapabilities.min, Math.min(this.zoomCapabilities.max, this.cameraZoom * scale));
                    this.applyCameraZoom(newZoom);
                } else {
                    const nextZoom = Math.max(1, Math.min(5, this.zoomLevel * scale));
                    // Zoom towards mouse point
                    const zoomRatio = nextZoom / this.zoomLevel;
                    // Center offsets relative to viewport center
                    const centerX = e.clientX - window.innerWidth/2;
                    const centerY = e.clientY - window.innerHeight/2;
                    
                    this.panX = (this.panX - centerX) * zoomRatio + centerX;
                    this.panY = (this.panY - centerY) * zoomRatio + centerY;
                    this.zoomLevel = nextZoom;
                }
            } else if (this.zoomLevel > 1) {
                e.preventDefault();
                // Natural scroll direction on Mac usually means we subtract
                this.panX -= e.deltaX;
                this.panY -= e.deltaY;
            }
            this.clampPan();
        },

        setInteracting() {
            this.isInteracting = true;
            if (this.interactionTimeout) clearTimeout(this.interactionTimeout);
            this.interactionTimeout = setTimeout(() => {
                this.isInteracting = false;
            }, 150); // Short grace period to maintain "none" transition while scrolling/zooming
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
                return;
            }

            const maxPanX = (window.innerWidth * this.zoomLevel - window.innerWidth) / 2;
            const maxPanY = (window.innerHeight * this.zoomLevel - window.innerHeight) / 2;

            this.panX = Math.max(-maxPanX, Math.min(maxPanX, this.panX));
            this.panY = Math.max(-maxPanY, Math.min(maxPanY, this.panY));
        },

        retryEcho() {
            if (this.isRetrying) return;
            this.isRetrying = true;
            console.log(`CallOverlay [${this.sessionUniqueId}]: Manual signaling retry triggered.`);
            
            if (window.Echo) {
                window.Echo.disconnect();
                setTimeout(() => {
                    window.Echo.connect();
                    this.isRetrying = false;
                    this.reconnectAttempts = 0;
                    this.signalingGraceActive = false;
                    
                    // Force clean peers state
                    Object.keys(this.peers).forEach(id => {
                        this.removePeer(id);
                    });
                    
                    // Re-broadcast presence immediately
                    this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                    
                    // Re-subscribe to channels to ensure fresh listeners
                    this.subscribeToChannels();
                }, 1000);
            } else {
                this.isRetrying = false;
            }
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
                // If we are already retrying manually, don't trigger automatic logic again
                if (this.isRetrying) return;

                if (!this.signalingGraceActive) {
                    this.signalingGraceActive = true;
                    console.log(`CallOverlay [${this.sessionUniqueId}]: Signaling issue detected (${state}). Grace period active.`);
                    
                    if (this.signalingGraceTimeout) clearTimeout(this.signalingGraceTimeout);
                    this.signalingGraceTimeout = setTimeout(() => {
                        this.signalingGraceActive = false;
                        
                        // AUTO RECOVERY: If still unavailable after 15s, try one manual reset with backoff
                        if (['unavailable', 'failed', 'disconnected'].includes(this.signalingState) && this.reconnectAttempts < 5) {
                            console.warn(`CallOverlay [${this.sessionUniqueId}]: Signaling hang detected. Attempting automated recovery...`);
                            this.reconnectAttempts++;
                            this.retryEcho();
                        }
                    }, 15000); // Increased from 10s to 15s to be safer
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
                const element = this.$refs.overlayRoot || document.documentElement;
                if (element.requestFullscreen) {
                    element.requestFullscreen().catch(() => {});
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                }
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
            console.log(`CallOverlay [${this.sessionUniqueId}]: Preparing room ${uuid} as ${userName} (Hash: ${hash})`);
            
            // Auto-generate name for guests if generic
            if (!userName || userName === 'Гость') {
                userName = this.generateBeautifulName();
            } else if (userName.includes('@')) {
                // Derived from email
                userName = userName.split('@')[0];
            }

            this.roomUuid = uuid;
            this.localUserName = userName;
            this.localHash = hash || userName; 
            this.lobbyName = userName;
            this.isRoomMode = true;
            this.isActive = true;
            this.isJoined = false;

            // Start media preview in background (NOT awaited to avoid signaling hang)
            this.setupLocalMedia().then(() => {
                if (this.$refs.localVideoWaiting) {
                    this.$refs.localVideoWaiting.srcObject = this.localStream;
                }
            }); 
            
            // Re-subscribe and start presence as soon as joined
            this.subscribeToChannels();

            // AUTO-JOIN for everyone (Guests no longer need to enter name)
            console.log(`CallOverlay [${this.sessionUniqueId}]: Auto-joining as ${userName}...`);
            this.confirmJoin();
        },

        generateBeautifulName() {
            const words = [
                'Алмаз', 'Сапфир', 'Рубин', 'Изумруд', 'Топаз', 'Опал', 'Агат', 'Аметист', 'Янтарь', 'Никель',
                'Феникс', 'Орион', 'Сириус', 'Вега', 'Альтаир', 'Арктур', 'Спика', 'Регул', 'Марс', 'Юпитер',
                'Лотос', 'Ирис', 'Жасмин', 'Пион', 'Орхидея', 'Лилия', 'Астра', 'Вербена', 'Атлант', 'Титан'
            ];
            const randomWord = words[Math.floor(Math.random() * words.length)];
            const randomNum = Math.floor(100 + Math.random() * 899);
            return `${randomWord} ${randomNum}`;
        },

        confirmJoin() {
            if (this.isGuest && this.lobbyName.trim()) {
                this.localUserName = this.lobbyName.trim();
                this.localHash = this.localUserName;
            }
            
            console.log(`CallOverlay [${this.sessionUniqueId}]: Confirming join as ${this.localUserName}`);
            
            this.subscribeToChannels();
            this.startPresence();
            this.lastSignalReceivedAt = Date.now();
            this.startInactivityTimer();
            
            const customerId = this.$shop?.customer_id;
            if (window.Echo && customerId) {
                 window.Echo.private(`user.${customerId}`).listen('.call-signal', (data) => this.handleSignal(data));
            }

            this.isJoined = true;

            // Enter fullscreen and bind videos
            this.$nextTick(() => {
                this.toggleFullscreen();
                this.rebindVideos();
            });
        },

        subscribeToChannels() {
            if (window.Echo && this.roomUuid) {
                const channelName = `call.${this.roomUuid}`;
                console.log(`CallOverlay [${this.sessionUniqueId}]: Subscribing to ${channelName}`);
                window.Echo.channel(channelName)
                    .stopListening('.call-signal')
                    .listen('.call-signal', (data) => {
                        this.handleSignal(data);
                    });
                
                // Debug log for subscription confirmation
                console.log(`CallOverlay [${this.sessionUniqueId}]: Listening for .call-signal on ${channelName}`);
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
                if (ticks > 10) {
                    this.stopPresence();
                    console.log(`CallOverlay [${this.sessionUniqueId}]: Presence stable, slowing down to 10s`);
                    this.presenceInterval = setInterval(() => {
                        if (this.isActive) this.sendSignal({ type: 'presence', fingerprint: this.localFingerprint });
                    }, 10000);
                }
            }, 1000); // 1s ticks for first 10 seconds
        },

        stopPresence() {
            if (this.presenceInterval) clearInterval(this.presenceInterval);
        },

        cleanupStalePeers() {
            const now = Date.now();
            const seenFingerprints = new Map();
            const seenHashes = new Map();

            Object.keys(this.peers).forEach(id => {
                const peer = this.peers[id];
                
                // Fingerprint-based deduplication
                if (peer.fingerprint) {
                    const existingId = seenFingerprints.get(peer.fingerprint);
                    if (existingId) {
                        const existingPeer = this.peers[existingId];
                        // If both are connected, keep the newer one. If one is connected, keep that one.
                        if (peer.connected && !existingPeer.connected) {
                            this.removePeer(existingId);
                        } else if (!peer.connected && existingPeer.connected) {
                            this.removePeer(id);
                            return;
                        } else if (peer.lastSeen > existingPeer.lastSeen) {
                            this.removePeer(existingId);
                        } else {
                            this.removePeer(id);
                            return;
                        }
                    }
                    seenFingerprints.set(peer.fingerprint, id);
                }

                // Hash-based deduplication (fallback for guests/no-fingerprint)
                if (peer.hash) {
                    const existingId = seenHashes.get(peer.hash);
                    if (existingId && existingId !== id) {
                        const existingPeer = this.peers[existingId];
                        if (peer.lastSeen > existingPeer.lastSeen) {
                            this.removePeer(existingId);
                        } else {
                            this.removePeer(id);
                            return;
                        }
                    }
                    seenHashes.set(peer.hash, id);
                }

                // Standard stale cleanup (45s for connected, 15s for new)
                const timeout = peer.connected ? 45000 : 15000;
                if (peer.lastSeen && now - peer.lastSeen > timeout) {
                    console.log(`Room: Cleaning up stale peer ${id} (${peer.name})`);
                    this.removePeer(id);
                }
            });
        },

        async setupLocalMedia() {
            if (this.localStream && this.localStream.active) {
                console.log('Room: Local stream already active, skipping setup');
                return;
            }

            // Generate fingerprint in background
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

        async handleSignal(data) {
            const signal = data.signal_data;
            const senderName = data.sender_name;
            const senderHash = signal.sender_hash || senderName; 
            const senderSessionId = signal.sender_session_id;

            this.lastSignalReceivedAt = Date.now();
            
            // Filter out self-signals OR signals without session ID (robustness)
            if (!senderSessionId || senderSessionId === this.sessionUniqueId) {
                if (!senderSessionId) console.warn('CallOverlay: Signal missing sender_session_id. Ignoring.');
                return;
            }

            // If targeted and not for us
            if (signal.target && signal.target !== this.sessionUniqueId) {
                const isTargetedToMe = signal.target === this.localHash || signal.target === this.localUserName;
                if (!isTargetedToMe) return;
            }

            console.log(`CallOverlay [${this.sessionUniqueId}]: Incoming signal [${signal.type}] from ${senderName} (Session: ${senderSessionId})`, {
                target: signal.target,
                hasSdp: !!signal.sdp,
                hasCandidate: !!signal.candidate
            });

            const peerKey = senderSessionId;

            if (signal.type === 'presence') {
                const now = Date.now();
                const isInitiator = this.sessionUniqueId < senderSessionId;
                
                if (!this.peers[peerKey]) {
                    // Pre-emptive deduplication on presence
                    Object.keys(this.peers).forEach(id => {
                        const p = this.peers[id];
                        if ((signal.fingerprint && p.fingerprint === signal.fingerprint) || (senderHash && p.hash === senderHash)) {
                            console.log(`Room: Pre-emptively removing duplicate session ${id} for ${senderName}`);
                            if (!p.connected || (signal.fingerprint && p.fingerprint === signal.fingerprint)) {
                                this.removePeer(id);
                            }
                        }
                    });

                    this.peers = {
                        ...this.peers,
                        [peerKey]: { 
                            name: senderName,
                            hash: senderHash,
                            pc: null, stream: null, connected: false, streamReady: false, 
                            hasVideo: false, hasAudio: false, connectedAt: 0,
                            iceQueue: [], fingerprint: signal.fingerprint, verified: false,
                            lastSeen: now,
                            makingOffer: false, ignoreOffer: false, watchdog: null
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
                    if (!peer.pc || ['failed', 'closed'].includes(peer.pc.connectionState)) {
                        console.log(`Room: I am initiator for ${senderName} (${peerKey}). Creating session...`);
                        const pc = this.createPeerConnection(peerKey, senderName);
                        
                        // Adding tracks will now automatically trigger onnegotiationneeded (initiator logic)
                        if (this.activeLocalStream) {
                            this.activeLocalStream.getTracks().forEach(track => pc.addTrack(track, this.activeLocalStream));
                        } else {
                            // If no tracks yet, manually trigger an empty offer to start state machine
                            // This ensures the peer is created and we're ready when tracks arrive
                            peer.makingOffer = true;
                            pc.createOffer().then(offer => {
                                const cleanSdp = this.normalizeSDP(offer.sdp);
                                pc.setLocalDescription({ type: 'offer', sdp: cleanSdp });
                                this.sendSignal({ type: 'offer', sdp: cleanSdp, target: peerKey, fingerprint: this.localFingerprint });
                            }).finally(() => { peer.makingOffer = false; });
                        }

                        // ENSURE WATCHDOG STARTS
                        this.startConnectionWatchdog(peerKey);
                    }
                }
            } else if (['offer', 'answer', 'candidate', 'hangup'].includes(signal.type)) {
                if (signal.type === 'offer') {
                    this.handleOffer(peerKey, senderName, signal);
                }
                else if (signal.type === 'answer') this.handleAnswer(peerKey, signal);
                else if (signal.type === 'candidate') this.handleCandidate(peerKey, signal);
                else if (signal.type === 'hangup') this.removePeer(peerKey);
            }
        },

        normalizeSDP(sdp) {
            if (!sdp) return '';
            // Minimalist cleanup for broad compatibility - ensures CRLF and trims lines
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

        togglePeerFocus(id) {
            if (this.focusedPeerId === id) {
                this.focusedPeerId = null;
            } else {
                this.focusedPeerId = id;
            }
        },

        toggleFocus() {
            if (this.peerCount === 1) {
                this.focusedPeerId = this.isFocusedOnSelf ? null : 'local';
            } else {
                this.focusedPeerId = null; // Exit focus mode in group
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

            const pc = this.createPeerConnection(id, name);
            const isPolite = this.sessionUniqueId > id; 
            
            // Glare handling: if we are also making an offer
            const offerCollision = (signal.type === 'offer') && 
                                   (peer.makingOffer || pc.signalingState !== 'stable');

            peer.ignoreOffer = !isPolite && offerCollision;
            if (peer.ignoreOffer) {
                console.warn(`WebRTC: Glare detected with ${id}. I am IMPOLITE. Ignoring incoming offer.`);
                return;
            }

            console.log(`WebRTC: Handling offer from ${id} (Polite: ${isPolite})`);
            this.startConnectionWatchdog(id);

            try {
                const sdp = this.normalizeSDP(signal.sdp);
                if (!sdp) return;

                await pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp }));
                
                // Add tracks BEFORE creating answer
                if (this.activeLocalStream) {
                    this.activeLocalStream.getTracks().forEach(track => {
                        const senders = pc.getSenders();
                        if (!senders.find(s => s.track && s.track.kind === track.kind)) {
                            pc.addTrack(track, this.activeLocalStream);
                        }
                    });
                }
                
                const answer = await pc.createAnswer();
                const cleanAnswer = this.normalizeSDP(answer.sdp);
                await pc.setLocalDescription({ type: 'answer', sdp: cleanAnswer });
                this.sendSignal({ type: 'answer', sdp: cleanAnswer, target: id, fingerprint: this.localFingerprint });
                
                // Process queued candidates
                while (peer.iceQueue.length > 0) {
                    const cand = peer.iceQueue.shift();
                    await pc.addIceCandidate(new RTCIceCandidate(cand)).catch(() => {});
                }
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
                    if (!sdp) {
                        console.warn(`WebRTC: handleAnswer aborted - invalid SDP from ${id}`);
                        return;
                    }
                    console.log(`WebRTC: Handling answer from ${id}`);
                    try {
                        await peer.pc.setRemoteDescription(new RTCSessionDescription({ type: 'answer', sdp }));
                    } catch (sdpErr) {
                        console.error(`WebRTC: setRemoteDescription (ANSWER) failed for ${id}.`, sdpErr);
                        // If it fails, let watchdog handle it or force restart
                        peer.pc.restartIce();
                        throw sdpErr;
                    }
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
            let peer = this.peers[id];
            if (!peer || !peer.pc) {
                console.log(`WebRTC: Receiving candidate for missing peer - creating PC for ${id}`);
                this.createPeerConnection(id);
                peer = this.peers[id];
            }
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
            if (this.peers[id]?.pc) return this.peers[id].pc;

            console.log(`WebRTC: Creating new PeerConnection for ${id}`);
            const pc = new RTCPeerConnection(this.configuration);
            
            if (!this.peers[id]) {
                this.peers = {
                    ...this.peers,
                    [id]: { 
                        name: name || id,
                        pc, stream: null, connected: false, streamReady: false, 
                        hasVideo: false, hasAudio: false, connectedAt: 0,
                        iceQueue: [], fingerprint: null, verified: false, 
                        lastSeen: Date.now(),
                        makingOffer: false, ignoreOffer: false, watchdog: null
                    }
                };
            } else {
                this.peers[id].pc = pc;
            }

            pc.onicecandidate = (e) => {
                if (e.candidate) this.sendSignal({ type: 'candidate', candidate: e.candidate, target: id });
            };

            pc.onnegotiationneeded = async () => {
                try {
                    const peer = this.peers[id];
                    if (!peer) return;

                    console.log(`WebRTC: Negotiation needed for ${id}`);
                    if (peer.makingOffer || pc.signalingState !== 'stable') return;
                    
                    peer.makingOffer = true;
                    // Reset ignoreOffer state when starting a new negotiation
                    peer.ignoreOffer = false;

                    const offer = await pc.createOffer();
                    
                    // State might change during await
                    if (pc.signalingState !== 'stable') return;

                    const cleanSdp = this.normalizeSDP(offer.sdp);
                    await pc.setLocalDescription({ type: 'offer', sdp: cleanSdp });
                    
                    this.sendSignal({ 
                        type: 'offer', 
                        sdp: cleanSdp, 
                        target: id 
                    });
                } catch (err) {
                    console.error(`WebRTC: Negotiation failed for ${id}`, err);
                } finally {
                    if (this.peers[id]) this.peers[id].makingOffer = false;
                }
            };

            pc.ontrack = (e) => {
                console.log(`WebRTC: Received remote track from ${id}`, e.track.kind);
                
                if (e.track.kind === 'video') {
                    console.log(`WebRTC: Video track confirmed for ${id}`);
                    this.peers[id].hasVideo = true;
                }
                if (e.track.kind === 'audio') {
                    console.log(`WebRTC: Audio track confirmed for ${id}`);
                    this.peers[id].hasAudio = true;
                }

                if (e.streams && e.streams[0]) {
                    this.peers[id].stream = e.streams[0];
                } else {
                    // Safari/Older browsers might not provide streams array
                    if (!this.peers[id].stream) {
                        this.peers[id].stream = new MediaStream();
                    }
                    this.peers[id].stream.addTrack(e.track);
                }

                // Consider stream ready if we have a video track or if we've been connected long enough
                if (this.peers[id].hasVideo) {
                    this.peers[id].streamReady = true;
                }
                
                this.rebindVideos();
            };

            pc.onconnectionstatechange = () => {
                this.updatePeerConnectedState(id, pc.connectionState);
            };

            pc.oniceconnectionstatechange = () => {
                this.updatePeerConnectedState(id, pc.iceConnectionState);
            };

            return pc;
        },

        updatePeerConnectedState(id, state) {
            if (!this.peers[id]) return;
            
            if (['connected', 'completed'].includes(state)) {
                if (!this.peers[id].connected) {
                    this.peers[id].connectedAt = Date.now();
                }
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
            const stream = this.activeLocalStream;
            if (!stream) {
                console.log('Room: No active local stream to sync.');
                return;
            }
            const tracks = stream.getTracks();
            
            Object.keys(this.peers).forEach(id => {
                const peer = this.peers[id];
                if (peer.pc && peer.pc.connectionState !== 'closed') {
                    const senders = peer.pc.getSenders();
                    
                    tracks.forEach(track => {
                        const sender = senders.find(s => s.track && s.track.kind === track.kind);
                        if (!sender) {
                            console.log(`Room: Adding missing track ${track.kind} to peer ${id}`);
                            peer.pc.addTrack(track, stream);
                            // pc.onnegotiationneeded will handle triggering the offer
                        } else if (sender.track !== track) {
                            console.log(`Room: Updating track ${track.kind} for peer ${id}`);
                            sender.replaceTrack(track);
                        }
                    });
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

            const localFocused = this.$refs.localVideoFocused;
            if (localFocused && activeLocalStream) {
                if (localFocused.srcObject !== activeLocalStream) localFocused.srcObject = activeLocalStream;
                if (localFocused.paused) localFocused.play().catch(() => {});
            }

            const localThumb = this.$refs.localVideoThumb;
            if (localThumb && activeLocalStream) {
                if (localThumb.srcObject !== activeLocalStream) localThumb.srcObject = activeLocalStream;
                if (localThumb.paused) localThumb.play().catch(() => {});
            }
            
            const localPip = this.$refs.localVideoPip;
            if (localPip && activeLocalStream) {
                if (localPip.srcObject !== activeLocalStream) localPip.srcObject = activeLocalStream;
                if (localPip.paused) localPip.play().catch(() => {});
            }
            
            const localWaiting = this.$refs.localVideoWaiting;
            if (localWaiting && this.localStream) {
                if (localWaiting.srcObject !== this.localStream) localWaiting.srcObject = this.localStream;
                if (localWaiting.paused) localWaiting.play().catch(() => {});
            }


            // Rebind Peer Streams
            Object.keys(this.peers).forEach(id => {
                const p = this.peers[id];
                if (!p || !p.stream || !p.connected) return;

                const mainEl = document.getElementById('video_' + id);
                if (mainEl && mainEl.srcObject !== p.stream) {
                    mainEl.srcObject = p.stream;
                    mainEl.play().catch(() => {});
                }

                const focusedEl = document.getElementById('video_focused_' + id);
                if (focusedEl && focusedEl.srcObject !== p.stream) {
                    focusedEl.srcObject = p.stream;
                    focusedEl.play().catch(() => {});
                }

                const thumbEl = document.getElementById('video_thumb_' + id);
                if (thumbEl && thumbEl.srcObject !== p.stream) {
                    thumbEl.srcObject = p.stream;
                    thumbEl.play().catch(() => {});
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

        async sendSignal(signalData, attempt = 1) {
            if (!this.isActive || !this.sessionUniqueId) return;
            
            // Add identifying metadata
            signalData.sender_hash = this.localHash;
            signalData.sender_session_id = this.sessionUniqueId;

            const payload = { signal_data: signalData, sender_name: this.localUserName };
            // Ensure endpoint selection is robust: if roomUuid exists, we are likely in room/guest mode
            const endpoint = (this.isRoomMode || this.roomUuid) ? `/call/${this.roomUuid}/signal` : '/customer/account/calls/signal';
            
            try {
                await axios.post(endpoint, payload);
            } catch (err) {
                const status = err.response ? err.response.status : 0;
                console.warn(`CallOverlay [${this.sessionUniqueId}]: sendSignal [${signalData.type}] (Attempt ${attempt}/3) failed with status ${status}`);
                
                // Retry only for server errors (502, 503, 504) or network failures
                if ((status >= 502 || status === 0) && attempt < 3) {
                    const delay = 1000 * Math.pow(2, attempt - 1); // 1s, 2s
                    setTimeout(() => this.sendSignal(signalData, attempt + 1), delay);
                }
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
            if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
                console.warn('ScreenShare: getDisplayMedia not supported');
                // Use a subtle visual feedback instead of alert if possible, but alert is fine for debug
                alert('Ваш браузер или текущее соединение не поддерживают демонстрацию экрана. Проверьте HTTPS.');
                return;
            }

            try {
                if (!this.isSharingScreen) {
                    console.log('ScreenShare: Full start sequence initiated...');
                    
                    // Mac/Safari often requires very clean constraints
                    this.screenStream = await navigator.mediaDevices.getDisplayMedia({ 
                        video: true,
                        audio: false 
                    });
                    
                    const screenTrack = this.screenStream.getVideoTracks()[0];
                    if (!screenTrack) throw new Error('No screen track obtained');

                    console.log(`ScreenShare: Obtained track "${screenTrack.label}"`);

                    this.isSharingScreen = true;

                    // Manual track replacement loop
                    const activePeers = Object.entries(this.peers).filter(([, p]) => p.pc && p.connected);
                    console.log(`ScreenShare: Replacing video tracks for ${activePeers.length} active peers`);
                    
                    for (const [id, p] of activePeers) {
                        try {
                            const videoSender = p.pc.getSenders().find(s => s.track?.kind === 'video');
                            if (videoSender) {
                                console.log(`ScreenShare: Replacing track for peer ${id}...`);
                                await videoSender.replaceTrack(screenTrack);
                                console.log(`ScreenShare: Replacement SUCCESS for peer ${id}`);
                            }
                        } catch (err) {
                            console.warn(`ScreenShare: Replacement FAILED for peer ${id}`, err);
                        }
                    }

                    this.$nextTick(() => this.rebindVideos());
                    
                    screenTrack.onended = () => {
                        console.log('ScreenShare: Track ended by system action');
                        if (this.isSharingScreen) this.stopScreenShare();
                    };
                } else {
                    this.stopScreenShare();
                }
            } catch (e) { 
                console.error('ScreenShare: EXCEPTION during start:', e);
                // On Safari, this often catches "Aborted" or "Interrupted" if user cancels
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
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
