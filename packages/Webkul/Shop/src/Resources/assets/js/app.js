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

window.Pusher = Pusher;

const laravelEnv = window.Laravel || {};

if (laravelEnv.reverbAppKey || laravelEnv.pusherAppKey) {
    // Enable Pusher logging
    Pusher.logToConsole = true;

    console.log('Echo: Initializing with host:', laravelEnv.reverbHost || laravelEnv.pusherHost);

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: laravelEnv.reverbAppKey || laravelEnv.pusherAppKey,
        wsHost: laravelEnv.reverbHost || laravelEnv.pusherHost || `ws-${laravelEnv.pusherCluster}.pusher.com`,
        wsPort: parseInt(laravelEnv.reverbPort || laravelEnv.pusherPort || 80),
        wssPort: parseInt(laravelEnv.reverbPort || laravelEnv.pusherPort || 443),
        forceTLS: (laravelEnv.reverbScheme || laravelEnv.pusherScheme || 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        enableStats: false,
    });

    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log('Echo Connection State:', states.current);
    });
} else {
    console.warn('Pusher/Reverb App Key is missing. P2P calls (incoming signals) will not work.');
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

app.config.globalProperties.$emitter.on('start-matrix-call', (data) => {
    console.log('Starting Matrix call...', data);
});

export default app;
