import mitt from "mitt";

export default {
    install: (app, options) => {
        const emitter = mitt();

        app.config.globalProperties.$emitter = emitter;

        window.$emitter = emitter;
    },
};
