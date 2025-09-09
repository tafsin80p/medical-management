<?php
/**
 * Handle DB Table Creation via Ajax
 */
// --------------------------- AJAX: Create / Check Tables ---------------------------
add_action('wp_ajax_pixelcode_create_tables', function() {
    check_ajax_referer('pixelcode_admin_nonce', 'nonce'); // nonce check

    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset_collate = $wpdb->get_charset_collate();

    $tables = [
        // Main cases table
        'pixelcode_cases' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,

                case_id VARCHAR(100) NOT NULL,
                case_status VARCHAR(100) DEFAULT 'pending initial review',
                assigned_to VARCHAR(100) DEFAULT 'N/A',
                assigned_dr_id VARCHAR(100) DEFAULT 'N/A',

                rating INT DEFAULT NULL,
                rating_date DATETIME DEFAULT NULL,

                package_type VARCHAR(100) NOT NULL,
                package_price VARCHAR(100) NOT NULL,
                priority VARCHAR(100) DEFAULT 'N/A',

                payment_status VARCHAR(100) DEFAULT 'pending',
                payment_date DATE DEFAULT NULL,
                payment_amount VARCHAR(100) DEFAULT NULL,
                payment_method VARCHAR(100) DEFAULT NULL,

                user_id VARCHAR(100) NOT NULL,
                user_name VARCHAR(100) NOT NULL,
                user_email VARCHAR(150) NOT NULL,

                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(30) NOT NULL,
                birth_date DATE NOT NULL,
                va_file_number VARCHAR(100) NOT NULL,
                address VARCHAR(255) NOT NULL,
                city VARCHAR(100) NOT NULL,
                state VARCHAR(50) NOT NULL,
                zip VARCHAR(20) NOT NULL,

                consent_data_collection TINYINT(1) DEFAULT 0,
                consent_privacy_policy TINYINT(1) DEFAULT 0,
                consent_communication TINYINT(1) DEFAULT 0,

                PRIMARY KEY (id),
                UNIQUE KEY case_id (case_id)
            ) $charset_collate;
        ",

        // Service history
        'pixelcode_cases_service_history' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases_service_history (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                case_id VARCHAR(100) NOT NULL,
                branch_of_service VARCHAR(100),
                service_composition VARCHAR(100),
                mos_aoc_rate VARCHAR(10),
                duty_position VARCHAR(100),

                PRIMARY KEY (id),
                KEY case_id (case_id)
            ) $charset_collate;
        ",

        // Deployments
        'pixelcode_cases_deployments' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases_deployments (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                service_history_id MEDIUMINT(9) NOT NULL,
                location VARCHAR(150),
                dates_in_theater VARCHAR(100),
                job VARCHAR(255),

                PRIMARY KEY (id),
                KEY service_history_id (service_history_id)
            ) $charset_collate;
        ",

        // VA Claims
        'pixelcode_cases_va_claims' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases_va_claims (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                case_id VARCHAR(100) NOT NULL,
                `condition` VARCHAR(255) NOT NULL,
                claim_type VARCHAR(20) DEFAULT NULL,              
                primary_event TEXT,                               
                linked_condition VARCHAR(255),                   
                service_explanation TEXT,                         
                mtf_seen TINYINT(1) DEFAULT 0,                   
                mtf_details TEXT,                                
                current_treatment TEXT,                           
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY case_id (case_id)
            ) $charset_collate;
        ",

        // Case Documents
        'pixelcode_cases_case_documents' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases_case_documents (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                case_id VARCHAR(100) NOT NULL,
                document_type VARCHAR(100),
                file_path VARCHAR(255),
                required TINYINT(1) DEFAULT 0,
                PRIMARY KEY (id),
                KEY case_id (case_id)
            ) $charset_collate;
        ",

        // Dashboard notifications
        'pixelcode_dashboard_notifications' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_dashboard_notifications (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                message TEXT NOT NULL,
                type VARCHAR(20) NOT NULL,
                time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                admin_status ENUM('unread','read') DEFAULT 'unread',
                user_status ENUM('unread','read') DEFAULT 'unread',
                PRIMARY KEY (id),
                KEY user_id (user_id)
            ) $charset_collate;
        ",
    ];

    $results = [];

    foreach ($tables as $suffix => $sql) {
        $table_name = $wpdb->prefix . $suffix;

        // First check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
            $results[$suffix] = [
                'success' => true,
                'message' => "{$table_name} already exists."
            ];
            continue;
        }

        // Create new table if not exists
        dbDelta($sql);

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
            $results[$suffix] = [
                'success' => true,
                'message' => "{$table_name} created."
            ];
        } else {
            $results[$suffix] = [
                'success' => false,
                'message' => "Failed to create {$table_name}. Error: " . $wpdb->last_error
            ];
        }
    }

    wp_send_json_success($results);
});
