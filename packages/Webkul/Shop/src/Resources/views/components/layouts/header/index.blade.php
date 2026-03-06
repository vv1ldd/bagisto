{!! view_render_event('bagisto.shop.layout.header.before') !!}

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1)
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="w-full">
    <v-header-switcher>
        <!-- Desktop Header Shimmer -->
        <div class="flex flex-wrap max-lg:hidden">
            <div
                class="flex h-[72px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8 items-center">
                <!-- Logo Shimmer -->
                <span class="shimmer block h-[29px] w-[131px] " role="presentation">
                </span>

                <!-- Profile Icon Shimmer -->
                <span class="shimmer h-8 w-32 " role="presentation">
                </span>
            </div>
        </div>

        <!-- Mobile Header Shimmer -->
        <div class="flex h-[72px] flex-wrap items-center gap-4 px-4 shadow-sm lg:hidden bg-transparent">
            <div class="flex w-full items-center justify-between">
                <!-- Logo Shimmer -->
                <span class="shimmer block h-[29px] w-[131px] " role="presentation">
                </span>

                <!-- Profile Icon Shimmer -->
                <span class="shimmer block h-8 w-32 " role="presentation">
                </span>
            </div>
        </div>
    </v-header-switcher>

    </v-header-switcher>
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-header-switcher-template">
                                                    <v-desktop-header v-if="isDesktop"></v-desktop-header>

                                                    <v-mobile-header v-else></v-mobile-header>
                                                </script>

    <script type="module">
        app.component('v-header-switcher', {
            template: '#v-header-switcher-template',

            data() {
                return {
                    isDesktop: window.innerWidth >= 1024
                }
            },

            mounted() {
                this.media = window.matchMedia('(min-width: 1024px)');

                this.media.addEventListener('change', this.handleMedia);
            },

            beforeUnmount() {
                this.media.removeEventListener('change', this.handleMedia);
            },

            methods: {
                handleMedia(e) {
                    this.isDesktop = e.matches;
                }
            }
        });

        app.component('v-desktop-header', {
            template: '#v-desktop-header-template'
        });

        app.component('v-mobile-header', {
            template: '#v-mobile-header-template'
        });
    </script>

    <script type="text/x-template" id="v-desktop-header-template">
                                                    <x-shop::layouts.header.desktop />
                                                </script>

    <script type="text/x-template" id="v-mobile-header-template">
                                                    <x-shop::layouts.header.mobile />
                                                </script>
@endPushOnce