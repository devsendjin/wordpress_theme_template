let scrollEventListenerThirdArgument = false;
(() => {
    try {
        let options = Object.defineProperty({}, "passive", {
            get: () => {
                scrollEventListenerThirdArgument = {passive: true};
            }
        });
        window.addEventListener("test", null, options);
    } catch(err) {}
})();

// const MOBILE_MAX_WIDTH  = 768;
// const ANIMATION_STEP = 150;
