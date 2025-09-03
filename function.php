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

                package_type VARCHAR(100) NOT NULL,
                package_price VARCHAR(100) NOT NULL,
                priority VARCHAR(100) NOT NULL,

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
        $pixelcode_cases_va_claims = "
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
                KEY case_id (case_id),
                FOREIGN KEY (case_id) REFERENCES {$wpdb->prefix}pixelcode_cases(case_id) ON DELETE CASCADE
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
                status VARCHAR(20) DEFAULT 'unread',
                PRIMARY KEY (id),
                KEY user_id (user_id)
            ) $charset_collate;
        ",
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
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';

    $message   = sanitize_text_field($_POST['message']);
    $type      = sanitize_text_field($_POST['type']);
    $user_id   = get_current_user_id();

    $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'message' => $message,
            'type'    => $type,
            'time'    => current_time('mysql'),
            'status'  => 'unread'
        ],
        ['%d', '%s', '%s', '%s', '%s']
    );

    wp_send_json_success(['message' => 'Notification saved!']);
});



// --------------------------- AJAX: Get Notifications ---------------------------
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




// --------------------------- form submit function ---------------------------
add_action('wp_ajax_pixelcode_submit_form', 'pixelcode_submit_form');
add_action('wp_ajax_nopriv_pixelcode_submit_form', 'pixelcode_submit_form');

function pixelcode_submit_form() {
    // Check nonce
    check_ajax_referer('pixelcode_client_nonce', 'nonce');

    global $wpdb;

    // Generate unique case_id
    $case_id = 'CASE-' . time() . '-' . wp_rand(1000, 9999);
    

    // ---------------- 1️⃣ Personal Info ----------------
    $wpdb->insert(
        "{$wpdb->prefix}pixelcode_cases",
        [
            'case_id' => $case_id,
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'birth_date' => sanitize_text_field($_POST['dob']),
            'va_file_number' => sanitize_text_field($_POST['va_file_number']),
            'address' => sanitize_text_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'zip' => sanitize_text_field($_POST['zip_code']),
            'consent_data_collection' => isset($_POST['data_consent']) ? 1 : 0,
            'consent_privacy_policy' => isset($_POST['privacy_consent']) ? 1 : 0,
            'consent_communication' => isset($_POST['communication_consent']) ? 1 : 0,
        ]
    );

    // ---------------- 2️⃣ Service History + Deployments ----------------
    if(!empty($_POST['service_branch'])) {
        foreach($_POST['service_branch'] as $i => $branch) {
            $wpdb->insert(
                "{$wpdb->prefix}pixelcode_cases_service_history",
                [
                    'case_id' => $case_id,
                    'branch_of_service' => sanitize_text_field($branch),
                    'service_composition' => sanitize_text_field($_POST['service_composition'][$i]),
                    'mos_aoc_rate' => sanitize_text_field($_POST['mos_aoc_rate'][$i]),
                    'duty_position' => sanitize_text_field($_POST['duty_position'][$i]),
                ]
            );

            $service_history_id = $wpdb->insert_id;

            if(isset($_POST['deployment_location'][$i])) {
                foreach($_POST['deployment_location'][$i] as $dIndex => $location) {
                    if(empty($location)) continue;
                    $wpdb->insert(
                        "{$wpdb->prefix}pixelcode_cases_deployments",
                        [
                            'service_history_id' => $service_history_id,
                            'location' => sanitize_text_field($location),
                            'dates_in_theater' => sanitize_text_field($_POST['deployment_dates'][$i][$dIndex]),
                            'job' => sanitize_text_field($_POST['deployment_job'][$i][$dIndex]),
                        ]
                    );
                }
            }
        }
    }

    // ---------------- 3️⃣ VA Claims ----------------
    if(!empty($_POST['condition'])) {
        foreach($_POST['condition'] as $i => $cond) {
            $wpdb->insert(
                "{$wpdb->prefix}pixelcode_cases_va_claims",
                [
                    'case_id' => $case_id,
                    'condition' => sanitize_text_field($cond),
                    'claim_type' => sanitize_text_field($_POST['condition_type'][$i]),
                    'primary_event' => sanitize_textarea_field($_POST['primary_event'][$i]),
                    'linked_condition' => sanitize_text_field($_POST['secondary_linked'][$i]),
                    'service_explanation' => sanitize_textarea_field($_POST['service_explanation'][$i]),
                    'mtf_seen' => intval($_POST['mtf_seen'][$i]),
                    'mtf_details' => sanitize_textarea_field($_POST['mtf_details'][$i]),
                    'current_treatment' => sanitize_textarea_field($_POST['current_treatment'][$i]),
                ]
            );
        }
    }

    // ---------------- 4️⃣ File Uploads ----------------
    $doc_fields = [
        'documents_dd214' => 1,
        'documents_medical' => 1,
        'documents_rating' => 1,
        'documents_decision' => 1,
        'documents_optional' => 0
    ];

    $upload_dir = wp_upload_dir()['basedir'] . '/cases/' . $case_id;
    if(!file_exists($upload_dir)) wp_mkdir_p($upload_dir);

    foreach($doc_fields as $field => $is_required) {
        if(!empty($_FILES[$field]['name'])) {
            foreach($_FILES[$field]['name'] as $i => $name) {
                if(empty($name)) continue;
                $tmp_name = $_FILES[$field]['tmp_name'][$i];
                $filename = sanitize_file_name($name);
                $target_file = $upload_dir . '/' . $filename;

                if(move_uploaded_file($tmp_name, $target_file)) {
                    $wpdb->insert(
                        "{$wpdb->prefix}pixelcode_cases_case_documents",
                        [
                            'case_id' => $case_id,
                            'document_type' => str_replace('documents_', '', $field),
                            'file_path' => str_replace(ABSPATH, '/', $target_file),
                            'required' => $is_required
                        ]
                    );
                }
            }
        }
    }

    wp_send_json_success(['message' => 'Case submitted successfully!', 'case_id' => $case_id, 'case_data' => $_POST]);
}