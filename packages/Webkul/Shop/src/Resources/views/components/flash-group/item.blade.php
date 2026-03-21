<v-flash-item
    v-for='flash in flashes'
    :key='flash.uid'
    :flash="flash"
    @onRemove="remove($event)"
/>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-flash-item-template"
    >
        <div
            class="group relative flex min-w-[320px] max-w-[420px] items-center justify-between gap-4 overflow-hidden bg-white/90 p-4 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] backdrop-blur-xl transition-all duration-300 hover:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.15)] border-l-4"
            :class="[
                flash.type === 'success' ? 'border-[#7C45F5]' : '',
                flash.type === 'error' ? 'border-red-500' : '',
                flash.type === 'warning' ? 'border-amber-500' : '',
                flash.type === 'info' ? 'border-blue-500' : '',
            ]"
        >
            <div class="flex items-center gap-3">
                <div 
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                    :class="[
                        flash.type === 'success' ? 'bg-[#7C45F5]/10 text-[#7C45F5]' : '',
                        flash.type === 'error' ? 'bg-red-50 text-red-500' : '',
                        flash.type === 'warning' ? 'bg-amber-50 text-amber-500' : '',
                        flash.type === 'info' ? 'bg-blue-50 text-blue-500' : '',
                    ]"
                >
                    <span :class="iconClasses[flash.type]" class="text-lg"></span>
                </div>

                <p class="text-[13px] font-bold text-zinc-800 leading-tight">
                    @{{ flash.message }}
                </p>
            </div>

            <button
                @click="remove"
                class="flex h-7 w-7 shrink-0 items-center justify-center text-zinc-400 transition-all hover:bg-zinc-100 hover:text-zinc-600 active:scale-95"
            >
                <span class="icon-cross text-xl"></span>
            </button>

            <!-- Progress bar for auto-hide -->
            <div 
                class="absolute bottom-0 left-0 h-0.5 bg-zinc-200/50 transition-all duration-[6000ms] ease-linear"
                style="width: 100%;"
                ref="progressBar"
            ></div>
        </div>
    </script>

    <script type="module">
        app.component('v-flash-item', {
            template: '#v-flash-item-template',

            props: ['flash'],

            data() {
                return {
                    iconClasses: {
                        success: 'icon-toast-done',

                        error: 'icon-toast-error',

                        warning: 'icon-toast-exclamation-mark',

                        info: 'icon-toast-info',
                    },
                };
            },

            mounted() {
                // Animate progress bar
                if (this.$refs.progressBar) {
                    setTimeout(() => {
                        this.$refs.progressBar.style.width = '0%';
                    }, 50);
                }

                // Show for 6 seconds unless user interacts/clicks
                setTimeout(() => {
                    this.remove();
                }, 6000);
            },

            methods: {
                remove() {
                    this.$emit('onRemove', this.flash)
                }
            }
        });
    </script>
@endpushOnce
