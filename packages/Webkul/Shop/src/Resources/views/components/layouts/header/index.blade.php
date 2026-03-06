{!! view_render_event('bagisto.shop.layout.header.before') !!}

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1)
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="w-full min-h-[48px] sm:min-h-[80px] bg-transparent">
    <v-header-switcher>
        <!-- Desktop Header Shimmer (Matching minimal checkout look) -->
        <div class="flex flex-wrap max-lg:hidden">
            <div class="flex py-6 w-full max-w-7xl mx-auto px-4 sm:px-8 justify-between items-center">
                <!-- Logo Shimmer -->
                <div class="shimmer block h-7 w-32" role="presentation"></div>

                <!-- Profile Shimmer -->
                <div class="shimmer h-8 w-24" role="presentation"></div>
            </div>
        </div>

        <!-- Mobile Header Shimmer -->
        <div class="flex py-3 flex-wrap items-center px-4 lg:hidden">
            <div class="flex w-full items-center justify-between">
                <!-- Logo Shimmer -->
                <div class="shimmer block h-6 w-24" role="presentation"></div>

                <!-- Profile icon shimmer -->
                <div class="shimmer block h-8 w-8" role="presentation"></div>
            </div>
        </div>
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