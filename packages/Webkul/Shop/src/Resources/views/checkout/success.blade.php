<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Успешная оплата!
    </x-slot>

    <style>
        @keyframes orbit {
            from { transform: rotate(0deg) translateX(100px) rotate(0deg); }
            to { transform: rotate(360deg) translateX(100px) rotate(-360deg); }
        }
        .orbit { animation: orbit 10s linear infinite; }
        body {
            background-image: 
                radial-gradient(at 0% 100%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(225,39%,30%,1) 0, transparent 50%);
            background-color: #0c0a09;
            min-height: 100vh;
        }
        .loot-glow {
            box-shadow: 0 0 50px rgba(124, 69, 245, 0.3);
        }
        .success-card {
            border: 6px solid #18181b;
            box-shadow: 20px 20px 0px 0px #7C45F5;
        }
    </style>

    <div id="success-app" class="flex min-h-screen flex-col items-center justify-center px-4 py-12 relative overflow-hidden">
        <v-success-screen :order="{{ json_encode($order) }}"></v-success-screen>
    </div>

    @pushOnce('scripts')
    <script type="text/x-template" id="v-success-screen-template">
        <div class="w-full flex flex-col items-center">
            <!-- Logo -->
            <div class="mb-12 relative z-20">
                <a href="{{ route('shop.home.index') }}" class="flex items-center gap-2">
                    <span class="text-[42px] font-black tracking-tighter text-white drop-shadow-[4px_4px_0px_rgba(124,69,245,1)] italic">
                        {{ core()->getConfigData('general.design.shop_logo.logo_text') ?: 'MEANLY' }}
                    </span>
                </a>
            </div>

            <!-- Main Card -->
            <div class="w-full max-w-[600px] bg-white success-card p-8 md:p-12 relative z-20 transition-all">
                
                <div class="mb-10 flex justify-center">
                    <div class="w-24 h-24 bg-green-400 border-[6px] border-zinc-900 flex items-center justify-center shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] -rotate-3 animate-bounce">
                        <svg class="w-14 h-14 text-zinc-900" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="mb-2 text-4xl font-black text-zinc-900 uppercase tracking-tighter text-center italic">
                    Заказ оплачен!
                </h1>
                
                <div class="mb-10 text-center">
                    <span class="bg-zinc-900 text-white px-6 py-2 font-black text-xl uppercase tracking-widest inline-block skew-x-[-10deg]">
                        #@{{ order.increment_id }}
                    </span>
                </div>

                <!-- Reward Section (Interactive) -->
                <div class="relative mb-10 group">
                    <div v-if="!bonusClaimed" class="bg-zinc-100 border-4 border-black p-8 flex flex-col items-center justify-center gap-4 border-dashed animate-pulse">
                        <div class="text-4xl text-zinc-400">🎁</div>
                        <p class="font-black uppercase tracking-widest text-zinc-400 text-sm italic">Начисляем ваш бонус...</p>
                    </div>

                    <div v-else class="bg-yellow-400 border-4 border-zinc-900 p-6 shadow-[12px_12px_0px_0px_rgba(124,69,245,1)] rotate-1 relative overflow-hidden transition-all scale-105 loot-glow">
                        <div class="flex items-center gap-6 relative z-10">
                            <div class="text-5xl animate-bounce">💎</div>
                            <div>
                                <p class="font-black uppercase text-[12px] tracking-[0.2em] text-zinc-800 mb-1">БОНУС ПОЛУЧЕН!</p>
                                <h2 class="text-4xl font-black text-zinc-900 uppercase tracking-tighter">
                                    +@{{ bonusAmount }} <span class="text-xl">MC</span>
                                </h2>
                            </div>
                        </div>
                        <!-- Transaction Link inside Reward Box -->
                        <div class="mt-4 pt-4 border-t-2 border-black/10 flex justify-between items-center text-[10px] font-black uppercase">
                            <span class="text-zinc-700">Verified on network</span>
                            <a :href="'https://arbiscan.io/tx/' + txBonus" target="_blank" class="underline hover:text-[#7C45F5]">View TX</a>
                        </div>
                    </div>
                </div>

                <!-- Base Transaction (Subtle) -->
                <div v-if="txBase" class="mb-10 border-4 border-black/10 p-3 flex items-center justify-between gap-4 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all">
                    <div class="flex items-center gap-3">
                        <p class="text-[9px] font-black uppercase text-zinc-400">Order Payment Hash</p>
                        <p class="text-[9px] font-mono font-bold text-zinc-500 truncate max-w-[150px]">@{{ txBase }}</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-6">
                    <a href="{{ route('shop.home.index') }}"
                        class="relative flex w-full items-center justify-center bg-[#7C45F5] py-5 text-xl font-black text-white border-4 border-zinc-900 shadow-[8px_8px_0px_0px_rgba(24,24,27,1)] transition-all hover:bg-black hover:text-white active:translate-x-[4px] active:translate-y-[4px] active:shadow-none uppercase tracking-widest italic">
                        Продолжить ⚡️
                    </a>

                    <a v-if="order.customer_id" :href="'/customer/account/orders/view/' + order.id"
                        class="text-sm font-black text-zinc-400 transition-colors hover:text-[#7C45F5] uppercase tracking-widest text-center">
                        Мои заказы
                    </a>
                </div>
            </div>

            <!-- Success Confetti (SVG Symbols) -->
            <div class="absolute inset-0 pointer-events-none opacity-20 z-0 overflow-hidden">
                <div v-for="n in 20" :key="n" class="absolute text-2xl animate-float" 
                     :style="{ 
                        top: Math.random() * 100 + '%', 
                        left: Math.random() * 100 + '%', 
                        animationDelay: (Math.random() * 5) + 's',
                        color: ['#7C45F5', '#FACC15', '#4ADE80'][n % 3] 
                     }">
                    ✦
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-success-screen', {
            template: '#v-success-screen-template',
            props: ['order'],
            data() {
                return {
                    bonusClaimed: false,
                    bonusAmount: 0,
                    txBonus: null,
                    txBase: null
                }
            },
            mounted() {
                // Initial data from order object
                if (this.order.additional) {
                    const additional = typeof this.order.additional === 'string' 
                        ? JSON.parse(this.order.additional) 
                        : this.order.additional;
                    
                    this.txBase = additional.mint_tx_base;
                    
                    if (additional.mint_tx_bonus) {
                        this.bonusClaimed = true;
                        this.bonusAmount = additional.mint_amount_bonus;
                        this.txBonus = additional.mint_tx_bonus;
                    } else {
                        // Trigger minting after a short delay for dramatic effect
                        setTimeout(this.mintReward, 1500);
                    }
                }
            },
            methods: {
                async mintReward() {
                    try {
                        const response = await axios.post(`${window.location.origin}/checkout/sbp/mint-bonus/${this.order.id}`);
                        if (response.data.success) {
                            this.bonusAmount = response.data.amount;
                            this.txBonus = response.data.tx;
                            this.bonusClaimed = true;
                            
                            // Emit success flash message
                            this.$emitter.emit('add-flash', { 
                                type: 'success', 
                                message: 'Бонус начислен на ваш кошелек!' 
                            });
                        }
                    } catch (err) {
                        console.error('Reward Minting Error:', err);
                    }
                }
            }
        });
    </script>
    @endpushOnce
</x-shop::layouts>