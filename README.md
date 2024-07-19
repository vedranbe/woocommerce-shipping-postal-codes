# Woocommerce Shipping Postal Codes Validation

## Generate Press Child Theme

This repository implements a concise shipping ZIP code validation for WooCommerce, exclusively affecting the checkout process while leaving billing codes unaffected. This was made using VS Code and Local by Flywheel (WP Engine). Products where imported from here: https://github.com/woocommerce/woocommerce/tree/master/sample-data

## Backend Configuration

To manage shipping postal codes, navigate to the WooCommerce menu under "Shipping Postal Codes" in the backend.

- Only numeric codes with precisely 5 digits are accepted.
- Multiple codes should be comma-separated.
- No duplicates are allowed.
- The validation restricts entry of codes that do not adhere to these rules.

## Frontend Implementation

The code validation is integrated into the Checkout page:

- **Trigger:** Validation activates upon entering 5 numeric digits in the designated field.
- **Comparison:** The input value is compared against the `vb_valid_postal_codes` value stored in the `wp_options` table.
- **Hidden Field:** An invisible DOM element (`<input type="hidden" name="no_submit">`) is dynamically added.
- **Form Behavior:** If the hidden field value is 0, the form submission is allowed; if 1, the form is disabled.
- **Invalid Codes:** In case of an invalid code, the submit button is disabled and an error message is displayed.
