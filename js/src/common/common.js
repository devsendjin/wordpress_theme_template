document.addEventListener('DOMContentLoaded', () => {
    window.TEMPLATE_URI = document.body.dataset.siteUri + '/wp-content/themes/lamellatrading/';

    //lazyload support for background images:
    document.addEventListener('lazybeforeunveil', function (e) {
        const bg = e.target.getAttribute('data-bg');
        if (bg) {
            e.target.style.backgroundImage = 'url(' + bg + ')';
        }
    });


    //prevent click on SHOP and FLOORING links
    [...document.querySelectorAll('.main-menu .js-prevent a')].forEach(link => {
        link.setAttribute('href', '#');
        link.addEventListener('click', e => {
            e.preventDefault();
        })
    });

    //prices-panel handler
    (() => {
        const panel = document.querySelector('.panel-prices');
        if (!panel) return;
        const panelBtnCalc = panel.querySelector('.js-panel-btn-calc');
        const panelBtnClose = panel.querySelector('.js-close-btn');
        const panelExtended = panel.querySelector('.js-panel-extended');

        if (!panelBtnCalc || !panelBtnClose) {
            console.assert(Boolean(panelBtnCalc || !panelBtnClose), 'Buttons not found');
            return;
        }

        panelBtnCalc.addEventListener('click', function () {
            panel.classList.toggle('is-open');
        });
        panelBtnClose.addEventListener('click', function () {
            panel.classList.toggle('is-open');
        });

        const tabs = [...panelExtended.querySelectorAll('.panel-extended .tab')];
        const tabContent = panelExtended.querySelector('.tab-content');

        if (tabs && tabContent) {
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function() {
                    if (!this.classList.contains('active')) {
                        tabs.forEach(tabLocal => tabLocal.classList.remove('active'))
                        this.classList.add('active');
                    }
                    if (index === 1) {
                        tabContent.classList.remove('first-field-visible')
                        return;
                    }
                    tabContent.classList.add('first-field-visible')
                });
            });
        }

        document.addEventListener('click', (e) => {
            if (!panel.contains(e.target)) {
                panel.classList.remove('is-open');
            }
        });
    })();

    // input animation
    const inputs = [...document.querySelectorAll('input[type=text], input[type=email],input[type=tel], textarea')];
    inputFocus(inputs);

    new Swiper('.products-slider', {
        loop: true,
        slidesPerView: 4,
        spaceBetween: 0,
        navigation: {
            nextEl: '.slider-arrow.arrow-next',
            prevEl: '.slider-arrow.arrow-prev',
        },
    });

    // switch handler
    const switchArray = document.querySelectorAll('.js-switch');
    if (switchArray.length) {
        switchArray.forEach(switchElement => {
            switchElement.addEventListener('click', function () {
                const input = this.parentElement.querySelector('.switch-input');
                const inputValue = input.value;

                input.value = this.dataset.value;
                this.setAttribute('data-value', inputValue);
                this.classList.toggle('switched');
                // console.log(input);
                // console.log(this);
            });
        });
    }

    const searchBtn = document.querySelector('.js-search-btn');
    if (searchBtn) {
        const searchBox = document.querySelector('.control-item.search');
        searchBtn.addEventListener('click', function() {
            this.parentElement.classList.toggle('search-form-visible');
        });
        document.addEventListener('click', (e) => {
            if (!searchBox.contains(e.target)) {
                searchBtn.parentElement.classList.remove('search-form-visible');
            }
        });
    }

    // const switchValue = [...document.querySelectorAll('.panel-settings-top .switch-input')].filter((item) => item.checked );
    // console.log(switchValue[0]);
    // [...document.querySelectorAll('.panel-settings-top .switch-input')].forEach((item) => {
    //     if (item.checked) {
    //         console.log(item);
    //     }
    // })

    /*const addToCartButtons = [...document.querySelectorAll('.add_to_cart_button')];*/
    /*if (addToCartButtons.length) {
        addToCartButtons.forEach(function (btn) {
            btn.addEventListener('click', function() {
                const button = $(this);
                const data = {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: button.data('product_id'),
                    product_sku: button.data('product_sku'),
                    quantity: button.data('quantity'),
                };
                $(document.body).trigger('adding_to_cart', [button, data]);
                $.ajax({
                    type: 'post',
                    url: AJAX_URL,
                    data: data,
                    beforeSend: function (response) {
                        button
                            .removeClass('added')
                            .addClass('loading');
                    },
                    complete: function (response) {
                        button
                            .addClass('added')
                            .removeClass('loading');
                    },
                    success: function (response) {
                        if (response.error && response.product_url) {
                            window.location = response.product_url;
                            return;
                        } else {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, button]);
                        }
                    },
                });
            });
        });
    }*/
});
