<?php
defined('ABSPATH') || exit;


/**
 * Register Client Dashboard Shortcode
 */
function pixelcode_client_dashboard_shortcode() {

    ob_start();
    ?>
    <div class="pixelcode-client-dashboard shadow-md bg-white rounded-lg p-6">
        <?php include(PIXELCODE_PLUGIN_DIR . 'templates/public/overview.php'); ?>
        <?php include(PIXELCODE_PLUGIN_DIR . 'templates/public/caseslists.php'); ?>
        <?php include(PIXELCODE_PLUGIN_DIR . 'templates/public/case-form.php'); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pixelcode_client_dashboard', 'pixelcode_client_dashboard_shortcode');
