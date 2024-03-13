<?php
/**
 * GeneratePress child theme functions and definitions.
 */

// Define option name
define('VB_POSTAL_CODES_OPTION', 'vb_valid_postal_codes');

// Add menu item
add_action('admin_menu', 'register_postal_code_settings_menu');
function register_postal_code_settings_menu() {
    add_submenu_page('woocommerce', 'Shipping Postal Codes', 'Shipping Postal Codes', 'manage_options', 'vb-postal-validation', 'render_postal_code_settings_page');
}

// Render settings page content
function render_postal_code_settings_page() {
    $valid_postal_codes = get_option(VB_POSTAL_CODES_OPTION);
    ?>
    <div class="wrap">
        <h1>WooCommerce Shipping Postal Codes</h1>
        <form method="post" action="options.php">
            <?php settings_fields('vb_postal_validation_group'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="vb_valid_postal_codes">Postal Codes<br>(comma-separated)</label></th>
                    <td>
                        <textarea name="vb_valid_postal_codes" id="vb_valid_postal_codes" rows="5" cols="50"><?php echo esc_textarea($valid_postal_codes); ?></textarea>
                        <div class="error_vb"></div>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Changes'); ?>
        </form>
        <style>
            .error_vb {
                color: #cc1818;
                font-size: .75em;
                max-width: 100%;
                white-space: normal;
                padding: 4px 0 0;
            }
        </style>
    </div>
    <div class="postal-code-container"><span></span></div>
    <?php
}

// Register settings group
add_action('admin_init', 'register_postal_code_settings_group');
function register_postal_code_settings_group() {
    register_setting('vb_postal_validation_group', VB_POSTAL_CODES_OPTION);
}

/*
 * Validation on backend
 */
$nonce_backend = wp_create_nonce('vb_postal_code_validation_nonce');
wp_enqueue_script('vb-postal-validation-backend', get_stylesheet_directory_uri() . '/js/validate-backend.js', array('jquery'), '1.0.0', true);
wp_localize_script('vb-postal-validation-backend', 'vb_postal_validation_backend', array('ajax_nonce' => $nonce_backend));

add_action('wp_ajax_vb_postal_code_validation', 'validate_postal_code_on_backend');
function validate_postal_code_on_backend() {
    // Check nonce for security
    if (!wp_verify_nonce($_POST['security'], 'vb_postal_code_validation_nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        exit;
    }

    // Sanitize and prepare codes for validation
    $sanitized_codes = strtoupper(sanitize_text_field($_POST['code']));
    $entered_codes = explode(',', $sanitized_codes); // Split into an array

    $error_messages = array();
    $unique_codes = array_unique($entered_codes); // Remove duplicates

    // Check for duplicates
    if (count($entered_codes) !== count($unique_codes)) {
        $error_messages[] = 'Duplicate codes found. Please enter unique postal codes.';
    }

    // Send response based on validation results
    if (!empty($error_messages)) {
        wp_send_json_error(array('message' => $error_messages));
    } else {
        $existing_codes = get_option(VB_POSTAL_CODES_OPTION);
        wp_send_json_success(array('message' => 'Valid postal codes entered.'));
    }
}

/*
 * Validation on frontend  
 */
function enqueue_frontend_scripts() {
    $nonce_frontend = wp_create_nonce('frontend_validation_script_nonce');
    if (is_checkout()) {
        wp_enqueue_script('frontend-validation-script', get_stylesheet_directory_uri() . '/js/validate-frontend.js', array('jquery'), null, true);
    }
    wp_localize_script('frontend-validation-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'ajax_nonce' => $nonce_frontend));
}

add_action('wp_enqueue_scripts', 'enqueue_frontend_scripts');

function validate_postcode() {
    $existing_codes = get_option(VB_POSTAL_CODES_OPTION);
    $postcode = sanitize_text_field($_POST['postcode']);

    // Check if the entered postcode is in the list
    $codes_array = explode(',', $existing_codes);

    if (in_array($postcode, $codes_array)) {
        echo 'ok';
    } else {
        echo 'error';
    }

    die();
}

add_action('wp_ajax_validate_postcode', 'validate_postcode');
add_action('wp_ajax_nopriv_validate_postcode', 'validate_postcode');
