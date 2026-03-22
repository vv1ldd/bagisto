/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Main vue bundler.
 */
console.log('App.js: Loading...');
import { createApp } from "vue/dist/vue.esm-bundler";

/**
 * Echo initialization.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.bootstrapEcho = function() {
    if (window.Echo) return;

    const laravelEnv = window.Laravel || {};

    if (laravelEnv.reverbAppKey || laravelEnv.pusherAppKey) {
        // Enable Pusher logging only in DEV if needed, usually false in PROD
        Pusher.logToConsole = false;

        const wsPort = parseInt(laravelEnv.reverbPort || laravelEnv.pusherPort || 80);
        const wssPort = parseInt(laravelEnv.reverbPort || laravelEnv.pusherPort || 443);
        const forceTLS = (laravelEnv.reverbScheme || laravelEnv.pusherScheme || 'https') === 'https';
        const host = laravelEnv.reverbHost || laravelEnv.pusherHost || `ws-${laravelEnv.pusherCluster}.pusher.com`;

        console.log(`Echo: Connecting to ${forceTLS ? 'wss' : 'ws'}://${host}:${forceTLS ? wssPort : wsPort}`);

        // Store for UI diagnostics
        window.$signalingServer = { host, port: forceTLS ? wssPort : wsPort, scheme: forceTLS ? 'wss' : 'ws' };

        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: laravelEnv.reverbAppKey || laravelEnv.pusherAppKey,
                wsHost: host,
                wsPort: wsPort,
                wssPort: wssPort,
                forceTLS: forceTLS,
                cluster: laravelEnv.reverbAppCluster || laravelEnv.pusherCluster || 'mt1',
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                enableStats: false,
            });

            // Diagnostic logs for Echo
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('Echo STATUS: Connected to signaling server');
            });

            window.Echo.connector.pusher.connection.bind('unavailable', () => {
                console.warn('Echo STATUS: Signaling server unavailable');
            });

            window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                console.log('Echo Connection State Change:', states.previous, '->', states.current);
                window.$emitter.emit('echo-state-change', states.current);
            });
        } catch (e) {
            console.error('Echo: Failed to initialize signaling connection', e);
        }
    } else {
        console.warn('Pusher/Reverb App Key is missing. P2P calls will not work.');
    }
};

// Selective initialization: only start if the page requests it (e.g. video-rooms)
if (document.querySelector('[data-echo-bootstrap]')) {
    window.bootstrapEcho();
}

/**
 * Main root application registry.
 */
window.app = createApp({
    data() {
        return {};
    },

    mounted() {
        this.lazyImages();
    },

    methods: {
        onSubmit() { },

        onInvalidSubmit({ values, errors, results }) {
            setTimeout(() => {
                const errorKeys = Object.entries(errors)
                    .map(([key, value]) => ({ key, value }))
                    .filter(error => error["value"].length);

                if (errorKeys.length > 0) {
                    const errorKey = errorKeys[0]["key"];

                    let scrollTarget = null;

                    // Try to find the input element with the exact name first.
                    let firstErrorElement = document.querySelector('[name="' + errorKey + '"]');

                    // If not found and the key doesn't end with [], try with the [] suffix (for array fields).
                    if (
                        !firstErrorElement
                        && !errorKey.endsWith('[]')
                    ) {
                        firstErrorElement = document.querySelector('[name="' + errorKey + '[]"]');
                    }

                    // If still not found, try to find any element that starts with this name (for nested fields).
                    if (!firstErrorElement) {
                        firstErrorElement = document.querySelector('[name^="' + errorKey + '"]');
                    }

                    // If we found the input element.
                    if (firstErrorElement) {
                        scrollTarget = firstErrorElement;

                        // Check if this is a TinyMCE textarea (hidden by TinyMCE).
                        if (firstErrorElement.tagName === 'TEXTAREA' && firstErrorElement.style.display === 'none') {
                            // Find the TinyMCE editor container.
                            const editorId = firstErrorElement.id;

                            const tinyMCEContainer = document.querySelector('#' + editorId + '_parent');

                            if (tinyMCEContainer) {
                                scrollTarget = tinyMCEContainer;
                            }
                        }
                    } else {
                        // If the input is not found, try to find the error message element itself.
                        // VeeValidate renders error messages with a v-error-message component having a name attribute.
                        const errorMessageElement = document.querySelector('[name="' + errorKey + '"] p, [name="' + errorKey + '[]"] p');

                        if (errorMessageElement) {
                            // Scroll to the parent container of the error message.
                            scrollTarget = errorMessageElement.closest('.border') || errorMessageElement.closest('div[class*="bg-white"]') || errorMessageElement;
                        }
                    }

                    if (scrollTarget) {
                        scrollTarget.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });

                        // Try to focus the element: for TinyMCE, focus the editor; for regular inputs, focus the input.
                        if (firstErrorElement) {
                            if (firstErrorElement.tagName === 'TEXTAREA' && firstErrorElement.style.display === 'none') {
                                // Focus the TinyMCE editor if available.
                                const editorId = firstErrorElement.id;

                                if (window.tinymce && tinymce.get(editorId)) {
                                    tinymce.get(editorId).focus();
                                }
                            } else if (firstErrorElement.focus) {
                                firstErrorElement.focus();
                            }
                        }
                    } else {
                        // If the scroll target is not found, show a flash message with all errors.
                        const allErrors = errorKeys
                            .map(error => {
                                if (Array.isArray(error.value)) {
                                    return error.value.join(', ');
                                }

                                return error.value;
                            })
                            .filter(msg => msg).join(' ');

                        this.$emitter.emit('add-flash', {
                            type: 'error',
                            message: allErrors
                        });
                    }
                }
            }, 100);
        },

        lazyImages() {
            var lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;

                        lazyImage.src = lazyImage.dataset.src;

                        lazyImage.classList.remove('lazy');

                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function (lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        },
    },
});

/**
 * Global plugins registration.
 */
import Axios from "./plugins/axios";
import Emitter from "./plugins/emitter";
import Shop from "./plugins/shop";
import VeeValidate from "./plugins/vee-validate";
import Flatpickr from "./plugins/flatpickr";

[
    Axios,
    Emitter,
    Shop,
    VeeValidate,
    Flatpickr,
].forEach((plugin) => app.use(plugin));

/**
 * Global directives.
 */
import Debounce from "./directives/debounce";
import CallOverlay from "./components/CallOverlay.vue";
import Messenger from "./components/Messenger.vue";
import RoomJoiner from "./components/RoomJoiner.vue";
import MeetingInviter from "./components/MeetingInviter.vue";

app.directive("debounce", Debounce);
app.component("v-call-overlay", CallOverlay);
app.component("v-messenger", Messenger);
app.component("v-room-joiner", RoomJoiner);
app.component("v-meeting-inviter", MeetingInviter);


export default app;
