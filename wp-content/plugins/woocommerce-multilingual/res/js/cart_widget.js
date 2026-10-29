document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.matches('.wcml_removed_cart_items_clear')) {
            e.preventDefault();
            wcml_cart_clear_removed_items();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // It has not been created yet, so the “original” code will build it. There is no need to refresh it.
    if (!sessionStorage.getItem('wc_cart_created')) {
        return;
    }

    // WooCommerce refreshes the fragments itself when the cart changes. This is only for
    // a refresh WooCommerce cannot see.
    if (actions.force_reset == 1) {
        setTimeout(wcml_reset_cart_fragments, 0);
    }
});

function wcml_reset_cart_fragments() {
    try {
        document.body.dispatchEvent(new Event('wc_fragment_refresh'));

        if (typeof wc_cart_fragments_params !== 'undefined') {
            sessionStorage.removeItem(wc_cart_fragments_params.fragment_name);
        }
    } catch (err) { }
}

function wcml_cart_clear_removed_items() {
    var xhr = new XMLHttpRequest();
    var formData = new FormData();

    formData.append('action', 'wcml_cart_clear_removed_items');
    formData.append('wcml_nonce', document.querySelector('#wcml_clear_removed_items_nonce').value);

    xhr.open('POST', woocommerce_params.ajax_url);
    xhr.onload = function() {
        if (xhr.status === 200) {
            window.location = window.location.href;
        }
    };
    xhr.send(formData);
}
