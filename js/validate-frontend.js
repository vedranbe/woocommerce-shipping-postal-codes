(function (root, $, undefined) {

    $(window).on('load', function () {

        var checkoutForm = $('form.wc-block-checkout__form');
        checkoutForm.append('<input type="hidden" name="no_submit">');
        var noSubmitField = $('input[name="no_submit"]');

        var button = $('.wc-block-components-checkout-place-order-button');

        var shipping = $('#shipping-postcode');
        shipping.after('<div class="error_vb"></div>');

        var error = $('.error_vb');

        // Validate Shipping ZIP on Load 
        var shippingPostcode = shipping.val();
        if (shippingPostcode.length === 5) {
            validatePostcode(shippingPostcode);
        }
        // Shipping ZIP
        shipping.on('blur keyup', function () {
            var postcode = shipping.val();

            if (postcode.length === 5) {
                validatePostcode(postcode, 'shipping');
            } else {
                error.text('');
                error.hide();
                button.prop("disabled", true);
            }
        });

        // Disable button if noSubmitField.val() === '1'
        checkoutForm.on('mousemove', function () {
            if (noSubmitField.val() === '1') {
                button.prop("disabled", true);
            }
        });

        // Function for validation
        function validatePostcode(postcode) {
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'validate_postcode',
                    postcode: postcode,
                    security: ajax_object.ajax_nonce
                },
                success: function (response) {
                    if (response === 'ok') {
                        //console.log('OK');
                        error.hide();
                        shipping.parent().removeClass('has-error');
                        noSubmitField.val(0);
                        button.prop("disabled", false);
                    } else {
                        //console.log('Error');
                        error.show();
                        error.text('Not matching ZIP code');
                        shipping.parent().addClass('has-error');
                        noSubmitField.val(1);
                        button.prop("disabled", true);
                        $('.wc-block-components-address-card__edit').trigger('click');
                        $('.wc-block-components-address-address-wrapper').addClass('is-editing');
                    }
                }
            });
        }

    });
})(this, jQuery);
