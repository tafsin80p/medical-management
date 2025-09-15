<?php
/*
Plugin Name: Dashboard Portal
Plugin URI: 
Description: A HIPAA-compliant client intake, portal, document delivery, and secure messaging system. This plugin provides a secure platform for managing sensitive client data, supporting features like encrypted messaging, secure document sharing, and client intake forms.
Version: 1.0.2
Author: PIXELCODE
Author URI: https://portfolio-client-y9gw.onrender.com
Text Domain: pixelcode
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.2.2
Tested up to: 6.2.2
Requires PHP: 8.0
Tags: dashboard, client portal, document delivery, encryption, secure client intake, pixelcode
*/

defined('ABSPATH') || exit;

// Define plugin constants
define('PIXELCODE_PLUGIN_DIR', plugin_dir_path(__FILE__)); 
define('PIXELCODE_PLUGIN_URL', plugin_dir_url(__FILE__));


/**
 * Enqueue scripts for Admin Dashboard
 */
function pixelcode_enqueue_for_admin() {

    // Check if we are in admin area
    if ( ! is_admin() ) {
        return;
    }

    // Get current screen
    $screen = get_current_screen();

    // Only enqueue on our plugin's admin page
    if ( $screen && $screen->id === 'toplevel_page_pixelcode-dashboard' ) {

        // Custom admin CSS 
        wp_enqueue_style(
            'pixelcode-admin-css',
            PIXELCODE_PLUGIN_URL . 'assets/css/admin.css',
            [],
            '1.0.0'
        );

        // Custom admin JS
        wp_enqueue_script(
            'pixelcode-admin-js',
            PIXELCODE_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            '1.0.0',
            true
        );

        // Localize admin script
        wp_localize_script('pixelcode-admin-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pixelcode_admin_nonce')
        ]);

        // Custom chat JS
        wp_enqueue_script(
            'pixelcode-chat-js',
            PIXELCODE_PLUGIN_URL . 'assets/js/chat.js',
            ['jquery'],
            '1.0.0',
            true
        );

        // Localize chat script
        wp_localize_script('pixelcode-chat-js', 'chat_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pixelcode_chat_nonce'),
            'isAdmin'  => current_user_can('manage_options')
        ]);
    }
}
// Correct priority set (5 to load before most scripts/styles)
add_action('admin_enqueue_scripts', 'pixelcode_enqueue_for_admin', 1);



/**
 * Enqueue scripts for Client Dashboard (Public Side)
 */
function pixelcode_enqueue_for_client() {

    // Only load on frontend (not in admin)
    if ( is_admin() ) {
        return;
    }

    // Font Awesome
    wp_enqueue_style(
        'pixelcode-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        [],
        '6.5.2'
    );

    // Custom public CSS
    wp_enqueue_style(
        'pixelcode-client-css',
        PIXELCODE_PLUGIN_URL . 'assets/css/client.css',
        ['pixelcode-fontawesome'],
        '1.0.0'
    );

    // Custom public JS
    wp_enqueue_script(
        'pixelcode-client-js',
        PIXELCODE_PLUGIN_URL . 'assets/js/client.js',
        ['jquery'],
        '1.0.0',
        true
    );

    // Localize public script
    wp_localize_script('pixelcode-client-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('pixelcode_client_nonce')
    ]);

    // Custom chat JS
    wp_enqueue_script(
        'pixelcode-chat-js',
        PIXELCODE_PLUGIN_URL . 'assets/js/chat.js',
        ['jquery'],
        '1.0.0',
        true
    );

    // Localize chat script
    wp_localize_script('pixelcode-chat-js', 'chat_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('pixelcode_chat_nonce'),
        'isAdmin'  => false
    ]);
}
add_action('wp_enqueue_scripts', 'pixelcode_enqueue_for_client');


// Include dashboards
include PIXELCODE_PLUGIN_DIR . 'includes/admin-dashboard.php';

// Include dashboards
include PIXELCODE_PLUGIN_DIR . 'includes/client-dashboard.php';

// Include functions
include PIXELCODE_PLUGIN_DIR . './function.php';