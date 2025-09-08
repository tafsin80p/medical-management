<?php
defined('ABSPATH') || exit;

// Hook to add the custom admin menu
add_action('admin_menu', 'pixelcode_admin_menu');

// Function to define the admin menu and page
function pixelcode_admin_menu() {
    add_menu_page(
        'Dashboard Portal',               
        'Nexus Dashboard',                
        'manage_options',                 
        'pixelcode-dashboard',                
        'pixelcode_admin_dashboard_page',     
        'dashicons-analytics',            
        8                                
    );
}

// Callback function to display the admin dashboard page
function pixelcode_admin_dashboard_page() {
    // Include the template file for the admin dashboard
    include(plugin_dir_path(__FILE__) . '../templates/admin/overview.php');
    include(plugin_dir_path(__FILE__) . '../templates/admin/caseslists.php');
    include(plugin_dir_path(__FILE__) . '../templates/admin/case-detail-model.php');
}