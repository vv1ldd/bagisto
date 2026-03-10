import mitt from "mitt";

const emitter = mitt();

console.log('Emitter.js: Initializing global $emitter');
window.$emitter = emitter;
console.log('Emitter.js: window.$emitter is now:', window.$emitter);

export default {
    install: (app, options) => {
        app.config.globalProperties.$emitter = emitter;
    },
};
