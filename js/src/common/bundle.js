document.addEventListener('DOMContentLoaded', () => {
    window.TEMPLATE_URI = document.body.dataset.siteUri + '/wp-content/themes/test/';

    //lazyload support for background images:
    document.addEventListener('lazybeforeunveil', function (e) {
        const bg = e.target.getAttribute('data-bg');
        if (bg) {
            e.target.style.backgroundImage = 'url(' + bg + ')';
        }
    });
});
