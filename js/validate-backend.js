(function (root, $, undefined) {
    $(document).ready(function ($) {
        var submitButton = $('#submit');
        var error = $('.error_vb');

        $('#vb_valid_postal_codes').on('blur keyup', function () {
            var enteredCodes = $(this).val().toUpperCase().split(',');
            var errorMessage = '';
            var hasError = false;

            // Loop through each entered code
            for (var i = 0; i < enteredCodes.length; i++) {
                var code = enteredCodes[i].trim();

                // Check for exactly 5 numbers
                var postalCodeRegex = /^[0-9]{5}$/;
                if (!postalCodeRegex.test(code)) {
                    errorMessage += 'Invalid code: "' + code + '". Please enter codes with exactly 5 numbers, separated by commas.\n';
                    hasError = true;
                }
            }

            // Display or hide error message, enable/disable submit button
            if (hasError) {
                error.text(errorMessage).show();
                submitButton.prop('disabled', true);
            } else {
                error.hide();
                submitButton.prop('disabled', false);

                // AJAX data
                var data = {
                    'action': 'vb_postal_code_validation',
                    'security': vb_postal_validation_backend.ajax_nonce,
                    'code': enteredCodes.join(',')
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            //console.log('Valid postal codes entered.');
                        } else {
                            errorMessage = response.data.message;
                            error.text(errorMessage).show();
                            submitButton.prop('disabled', true);
                        }
                    }
                });
            }
        });
    });
})(this, jQuery);