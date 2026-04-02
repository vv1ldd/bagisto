<!DOCTYPE html>

<html class="{{ request()->cookie('dark_mode') ?? 0 ? 'dark' : '' }}" lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}">

<head>
    {!! view_render_event('bagisto.admin.layout.head.before') !!}

    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="currency" content="{{ core()->getBaseCurrency()->toJson() }}">
    <meta name="generator" content="Bagisto">

    @stack('meta')

    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

    <script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet" />

    <link rel="preload" as="image" href="{{ url('cache/logo/bagisto.png') }}">

    @if ($favicon = core()->getConfigData('general.design.admin_logo.favicon'))
        <link type="image/x-icon" href="{{ Storage::url($favicon) }}" rel="shortcut icon" sizes="16x16">
    @else
        <link type="image/x-icon" href="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/favicon.ico') }}" rel="shortcut icon" sizes="16x16" />
    @endif

    @stack('styles')

    <style>
        /* [MEANLY NEOBRUTALISM] Admin Redesign */
        :root {
            --primary-purple: #7C45F5;
            --primary-lime: #D6FF00;
            --black-accent: #18181B;
            --white-pure: #FFFFFF;
            --gray-bg: #F8F8F8;
            --brutalist-border: 4px;
        }

        /* Force sharp corners and thick borders globally */
        * {
            border-radius: 0 !important;
        }

        body {
            background-color: var(--gray-bg);
            color: var(--black-accent);
            overscroll-behavior: none !important;
        }

        html {
            overscroll-behavior: none !important;
        }

        /* Global Table/Card Styling */
        .table, .datagrid-container, .card, .bg-white {
            border: var(--brutalist-border) solid var(--black-accent) !important;
            box-shadow: 4px 4px 0px 0px var(--black-accent) !important;
        }

        /* Button Styling Overrides */
        .primary-button, .secondary-button, button[type="submit"], .btn-primary {
            border: 2px solid var(--black-accent) !important;
            box-shadow: 3px 3px 0px 0px var(--black-accent) !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            transition: all 0.1s ease-in-out !important;
        }

        .primary-button:active, .secondary-button:active, button:active {
            transform: translate(2px, 2px) !important;
            box-shadow: 0px 0px 0px 0px var(--black-accent) !important;
        }

        /* Input Styling */
        input[type="text"], input[type="password"], input[type="email"], select, textarea {
            border: 2px solid var(--black-accent) !important;
            background: var(--white-pure) !important;
            box-shadow: none !important;
        }

        input:focus {
            box-shadow: 3px 3px 0px 0px var(--black-accent) !important;
            outline: none !important;
        }

        /* Dark Mode Adjustments */
        .dark body {
            background-color: #0c0c0e;
        }
        
        .dark .bg-white, .dark .datagrid-container {
            border-color: #FFFFFF !important;
            box-shadow: 4px 4px 0px 0px #FFFFFF !important;
        }

        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {!! view_render_event('bagisto.admin.layout.head.after') !!}
</head>

<body class="h-full dark:bg-gray-950">
    {!! view_render_event('bagisto.admin.layout.body.before') !!}

    <!-- Built With Bagisto -->
    <div id="app" class="h-full">
        <!-- Flash Message Blade Component -->
        <x-admin::flash-group />

        <!-- Confirm Modal Blade Component -->
        <x-admin::modal.confirm />

        {!! view_render_event('bagisto.admin.layout.content.before') !!}

        <!-- Page Header Blade Component -->
        <x-admin::layouts.header />

        <div class="group/container {{ request()->cookie('sidebar_collapsed') ?? 0 ? 'sidebar-collapsed' : 'sidebar-not-collapsed' }} flex flex-col lg:flex-row gap-0 lg:gap-4"
            ref="appLayout">
            <!-- Page Sidebar Blade Component -->
            <div class="lg:fixed lg:top-[62px] lg:left-0 rtl:lg:right-0 rtl:lg:left-auto lg:z-10 w-full lg:w-auto">
                <x-admin::layouts.sidebar />
            </div>

            <div
                class="flex min-h-[calc(100vh-62px)] max-w-full flex-1 flex-col bg-white transition-all duration-300 dark:bg-gray-950 pt-3 px-2 sm:px-4 lg:pt-3 lg:px-4 lg:ltr:pl-[286px] lg:group-[.sidebar-collapsed]/container:ltr:pl-[85px] lg:rtl:pr-[286px] lg:group-[.sidebar-collapsed]/container:rtl:pr-[85px]">
                <!-- Added dynamic tabs for third level menus  -->
                <div class="pb-4 lg:pb-6">
                    <!-- Todo @suraj-webkul need to optimize below statement. -->
                    @if (!request()->routeIs('admin.configuration.index'))
                        <div class="overflow-x-auto">
                            <x-admin::layouts.tabs />
                        </div>
                    @endif

                    <!-- Page Content Blade Component -->
                    <div class="w-full overflow-x-hidden">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Powered By -->
                <div class="mt-auto">
                    <div
                        class="border-t bg-white py-2 text-center text-xs sm:text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                        @lang('admin::app.components.layouts.powered-by.description', [
                            'bagisto' => '<a class="text-blue-600 hover:underline dark:text-darkBlue" href="https://bagisto.com/en/">Bagisto</a>',
                            'webkul' => '<a class="text-blue-600 hover:underline dark:text-darkBlue" href="https://webkul.com/">Webkul</a>',
                        ])
                    </div>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.layout.content.after') !!}
    </div>

    {!! view_render_event('bagisto.admin.layout.body.after') !!}

    @stack('scripts')

    {!! view_render_event('bagisto.admin.layout.vue-app-mount.before') !!}

    <script>
        /**
         * Load event, the purpose of using the event is to mount the application
         * after all of our `Vue` components which is present in blade file have
         * been registered in the app. No matter what `app.mount()` should be
         * called in the last.
         */
        window.addEventListener("load", function (event) {
            app.mount("#app");
        });
    </script>

    {!! view_render_event('bagisto.admin.layout.vue-app-mount.after') !!}
</body>

</html>