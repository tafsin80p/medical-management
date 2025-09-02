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
                case_status VARCHAR(100) DEFAULT 'pending',

                case_package_type VARCHAR(100) NOT NULL,
                case_package_price VARCHAR(100) NOT NULL,
                case_priority VARCHAR(100) NOT NULL,

                payment_status VARCHAR(100) DEFAULT 'pending',
                payment_date DATE DEFAULT NULL,
                payment_amount VARCHAR(100) DEFAULT NULL,
                payment_method VARCHAR(100) DEFAULT NULL,

                rating INT DEFAULT NULL,
                rating_date DATETIME DEFAULT NULL,

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
                start_date DATE,
                end_date DATE,

                PRIMARY KEY (id),
                KEY case_id (case_id),
                FOREIGN KEY (case_id) REFERENCES {$wpdb->prefix}pixelcode_cases(case_id) ON DELETE CASCADE
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
                KEY service_history_id (service_history_id),
                FOREIGN KEY (service_history_id) REFERENCES {$wpdb->prefix}pixelcode_cases_service_history(id) ON DELETE CASCADE
            ) $charset_collate;
        ",

        // VA Claims
        'pixelcode_cases_va_claims' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_cases_va_claims (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                case_id VARCHAR(100) NOT NULL,
                `condition` VARCHAR(255),
                claim_type VARCHAR(20) DEFAULT NULL,
                explanation TEXT,
                linked_condition VARCHAR(255),
                mtf_seen TINYINT(1) DEFAULT 0,
                mtf_details TEXT,
                current_treatment TEXT,
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
                KEY case_id (case_id),
                FOREIGN KEY (case_id) REFERENCES {$wpdb->prefix}pixelcode_cases(case_id) ON DELETE CASCADE
            ) $charset_collate;
        ",

        // Dashboard notifications
        'pixelcode_dashboard_notifications' => "
            CREATE TABLE {$wpdb->prefix}pixelcode_dashboard_notifications (
                id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
                message TEXT NOT NULL,
                type VARCHAR(20) NOT NULL,
                time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                status VARCHAR(20) DEFAULT 'unread',

                PRIMARY KEY (id)
            ) $charset_collate;
        "
    ];

    $results = [];

    foreach ($tables as $suffix => $sql) {
        dbDelta($sql);

        $table_name = str_replace(' ', '', $wpdb->prefix . $suffix);
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$suffix}'") === "{$wpdb->prefix}{$suffix}") {
            $results[$suffix] = [
                'success' => true,
                'message' => "{$wpdb->prefix}{$suffix} created."
            ];

            // Insert notification
            $wpdb->insert(
                "{$wpdb->prefix}pixelcode_dashboard_notifications",
                [
                    'message' => "{$wpdb->prefix}{$suffix} created successfully!",
                    'type'    => 'success',
                    'time'    => current_time('mysql'),
                    'status'  => 'unread'
                ],
                ['%s','%s','%s','%s']
            );

        } else {
            $results[$suffix] = [
                'success' => false,
                'message' => "Failed to create {$wpdb->prefix}{$suffix}. Error: " . $wpdb->last_error
            ];
        }
    }

    wp_send_json_success($results);
});





// --------------------------- AJAX: Dashboard Notifications ---------------------------
add_action('wp_ajax_add_dashboard_notification', function() {
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';

    $message = sanitize_text_field($_POST['message']);
    $type    = sanitize_text_field($_POST['type']);

    $wpdb->insert(
        $table_name,
        [
            'message' => $message,
            'type'    => $type,
            'time'    => current_time('mysql'),
            'status'  => 'unread'
        ],
        ['%s', '%s', '%s', '%s']
    );

    wp_send_json_success(['message' => 'Notification saved!']);
});



// --------------------------- AJAX: Fetch Notifications ---------------------------
add_action('wp_ajax_get_dashboard_notifications', function() {
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';

    $notifications = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time ASC", ARRAY_A);

    wp_send_json_success($notifications);
});



// --------------------------- AJAX: Mark All Notifications Read ---------------------------
add_action('wp_ajax_mark_all_notifications_read', function() {
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';
    $wpdb->update($table_name, ['status' => 'read'], ['status' => 'unread'], ['%s'], ['%s']);
});

