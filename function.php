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




// ------------------------------- add notification --------------------------------------
add_action('wp_ajax_add_dashboard_notification', function() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';

    $message   = sanitize_text_field($_POST['message']);
    $user_id   = get_current_user_id();

    $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'message' => $message,
            'time'    => current_time('mysql'),
            'admin_status' => 'unread',   
            'user_status'  => 'unread'  
        ],
        ['%d', '%s', '%s', '%s', '%s']
    );

    wp_send_json_success(['message' => 'Notification saved!']);
});




// --------------------------- AJAX: Get Notifications ---------------------------
add_action('wp_ajax_get_dashboard_notifications', function() {    
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';
    $user_id    = get_current_user_id();

    if (current_user_can('manage_options')) {
        $notifications = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY time ASC",
            ARRAY_A
        );
    } else {
        $notifications = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d ORDER BY time ASC",
                $user_id
            ),
            ARRAY_A
        );
    }

    wp_send_json_success($notifications);
});




// --------------------------- AJAX: Mark All Notifications Read ---------------------------
add_action('wp_ajax_mark_all_notifications_read', function() {

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';
    $user_id    = get_current_user_id();

    if ( current_user_can('manage_options') ) {
        $wpdb->update(
            $table_name,
            ['admin_status' => 'read'],
            ['admin_status' => 'unread'],
            ['%s'],
            ['%s']
        );
    } else {
        $wpdb->update(
            $table_name,
            ['user_status' => 'read'],
            ['user_id' => $user_id, 'user_status' => 'unread'],
            ['%s'],
            ['%d','%s']
        );
    }

    wp_send_json_success(['message' => 'All notifications marked as read.']);
});





// --------------------------- form submit function ---------------------------
add_action('wp_ajax_pixelcode_submit_form', 'pixelcode_submit_form');
add_action('wp_ajax_nopriv_pixelcode_submit_form', 'pixelcode_submit_form');

function pixelcode_submit_form() {
    // Check nonce
    check_ajax_referer('pixelcode_client_nonce', 'nonce');

    global $wpdb;

    $current_user = wp_get_current_user();

    if ($current_user->ID != 0) { 
        $user_id    = $current_user->ID;
        $user_name  = $current_user->display_name;
        $user_email = $current_user->user_email; 
    } else {
        echo 'No user is logged in.';
    }

    // Generate unique case_id
    $case_id = 'CASE-' . time() . '-' . wp_rand(1000, 9999);
    

    // ---------------- 1️⃣ Personal Info ----------------
    $wpdb->insert(
        "{$wpdb->prefix}pixelcode_cases",
        [
            'case_id' => $case_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
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





// --------------------------- AJAX: Get Client Cases ---------------------------
add_action('wp_ajax_pixelcode_get_all_cases', 'pixelcode_get_all_cases');
add_action('wp_ajax_nopriv_pixelcode_get_all_cases', 'pixelcode_get_all_cases'); 

function pixelcode_get_all_cases() {
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        return;
    }

    global $wpdb;
    $user_id = get_current_user_id();

    $cases_table       = $wpdb->prefix . 'pixelcode_cases';
    $history_table     = $wpdb->prefix . 'pixelcode_cases_service_history';
    $deployments_table = $wpdb->prefix . 'pixelcode_cases_deployments';
    $claims_table      = $wpdb->prefix . 'pixelcode_cases_va_claims';
    $documents_table   = $wpdb->prefix . 'pixelcode_cases_case_documents';

    // ------------------- Fetch main cases for current user -------------------
    $cases = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $cases_table WHERE user_id = %d ORDER BY created_at DESC",
        $user_id
    ));

    if (empty($cases)) {
        wp_send_json_success(['cases' => []]);
        return;
    }

    // ------------------- Fetch related data in one go -------------------
    $case_ids = wp_list_pluck($cases, 'case_id');

    $service_history = $wpdb->get_results(
        "SELECT * FROM $history_table WHERE case_id IN ('" . implode("','", $case_ids) . "')"
    );

    $history_ids = wp_list_pluck($service_history, 'id');

    $deployments = $wpdb->get_results(
        "SELECT * FROM $deployments_table WHERE service_history_id IN (" . implode(',', $history_ids) . ")"
    );

    $claims = $wpdb->get_results(
        "SELECT * FROM $claims_table WHERE case_id IN ('" . implode("','", $case_ids) . "')"
    );

    $documents = $wpdb->get_results(
        "SELECT * FROM $documents_table WHERE case_id IN ('" . implode("','", $case_ids) . "')"
    );

    // ------------------- Build nested structure -------------------
    $service_map = [];
    foreach ($service_history as $sh) {
        $sh->deployments = [];
        $service_map[$sh->id] = $sh;
    }

    foreach ($deployments as $d) {
        if (isset($service_map[$d->service_history_id])) {
            $service_map[$d->service_history_id]->deployments[] = $d;
        }
    }

    $case_map = [];
    foreach ($cases as $case) {
        $case->service_history = [];
        $case->claims = [];
        $case->documents = [];
        $case_map[$case->case_id] = $case;
    }

    foreach ($service_history as $sh) {
        if (isset($case_map[$sh->case_id])) {
            $case_map[$sh->case_id]->service_history[] = $sh;
        }
    }

    foreach ($claims as $c) {
        if (isset($case_map[$c->case_id])) {
            $case_map[$c->case_id]->claims[] = $c;
        }
    }

    foreach ($documents as $doc) {
        if (isset($case_map[$doc->case_id])) {
            $case_map[$doc->case_id]->documents[] = $doc;
        }
    }

    wp_send_json_success([
        'cases' => array_values($case_map)
    ]);
}





// ----------------------- get model case data -----------------------------------
function pixelcode_get_single_case() {
    global $wpdb;

    $case_id = sanitize_text_field($_POST['case_id'] ?? '');
    if (!$case_id) {
        wp_send_json_error('Case ID missing');
    }

    $cases_table = $wpdb->prefix . 'pixelcode_cases';
    $service_table = $wpdb->prefix . 'pixelcode_cases_service_history';
    $deployments_table = $wpdb->prefix . 'pixelcode_cases_deployments';
    $documents_table = $wpdb->prefix . 'pixelcode_cases_case_documents';
    $va_claims_table = $wpdb->prefix . 'pixelcode_cases_va_claims';

    // Get main case
    $case = $wpdb->get_row($wpdb->prepare("SELECT * FROM $cases_table WHERE case_id=%s", $case_id), ARRAY_A);

    if (!$case) {
        wp_send_json_error('Case not found');
    }

    // Get service history
    $service_history = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $service_table WHERE case_id=%s",
        $case_id
    ), ARRAY_A);

    // Get deployments for each service history
    foreach ($service_history as &$service) {
        $service['deployments'] = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $deployments_table WHERE service_history_id=%d",
            $service['id']
        ), ARRAY_A);
    }

    // Get case documents
    $documents = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $documents_table WHERE case_id=%s",
        $case_id
    ), ARRAY_A);

    // Get VA claims
    $va_claims = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $va_claims_table WHERE case_id=%s",
        $case_id
    ), ARRAY_A);

    // Merge all related data
    $case['service_history'] = $service_history;
    $case['documents'] = $documents;
    $case['va_claims'] = $va_claims;

    wp_send_json_success(['case' => $case]);
}

add_action('wp_ajax_pixelcode_get_single_case', 'pixelcode_get_single_case');
add_action('wp_ajax_nopriv_pixelcode_get_single_case', 'pixelcode_get_single_case');




// Priority update
add_action('wp_ajax_update_case_priority', function () {
    // Check nonce
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');

    global $wpdb;
    $case_id = sanitize_text_field($_POST['case_id']);
    $value   = sanitize_text_field($_POST['priority']);

    $result = $wpdb->update(
        $wpdb->prefix . 'pixelcode_cases',
        ['priority' => $value],
        ['case_id' => $case_id],
        ['%s'],
        ['%s']
    );

    if($result === false){
        wp_send_json_error(['error' => $wpdb->last_error]);
    } else {
        wp_send_json_success(['updated_rows'=>$result]);
    }
});

// Status update
add_action('wp_ajax_update_case_status', function () {
    // Check nonce
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');

    global $wpdb;
    $case_id = sanitize_text_field($_POST['case_id']);
    $status   = sanitize_text_field($_POST['status']);

    
    $result = $wpdb->update(
        $wpdb->prefix . 'pixelcode_cases',
        ['case_status' => $status],
        ['case_id' => $case_id],
        ['%s'],
        ['%s']
    );

    if($result === false){
        wp_send_json_error(['error' => $wpdb->last_error]);
    } else {
        wp_send_json_success(['updated_rows'=>$result]);
    }
});

// Assigned To update
add_action('wp_ajax_update_case_assigned', function () {
    // Check nonce
    check_ajax_referer('pixelcode_admin_nonce', 'nonce');

    global $wpdb;

    $case_id      = sanitize_text_field($_POST['case_id']);
    $assigned_to  = sanitize_text_field($_POST['assigned_to']);
    $assigned_dr_id = isset($_POST['dr_id']) ? intval($_POST['dr_id']) : null;

    // Update main case table
    $result = $wpdb->update(
        $wpdb->prefix . 'pixelcode_cases',
        [
            'assigned_to'   => $assigned_to,
            'assigned_dr_id'=> $assigned_dr_id,
        ],
        ['case_id' => $case_id],
        ['%s','%d'], 
        ['%s']
    );

    if ($result === false) {
        wp_send_json_error(['error' => $wpdb->last_error]);
    } else {
        wp_send_json_success(['updated_rows' => $result]);
    }
});







// ------------------------------------------------------------------------------------------------------------------------------------------------------------------
add_action('wp_ajax_update_dashboard', 'update_dashboard_callback');
add_action('wp_ajax_nopriv_update_dashboard', 'update_dashboard_callback');

function update_dashboard_callback() {
    global $wpdb;

    $days = isset($_GET['days']) ? intval($_GET['days']) : 30;
    $today = current_time('Y-m-d H:i:s');
    $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days", strtotime($today)));

    // Total Revenue
    $revenue_last = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2))) 
         FROM {$wpdb->prefix}pixelcode_cases 
         WHERE payment_status='completed' AND payment_date BETWEEN %s AND %s",
        $start_date, $today
    ));
    $revenue_last = $revenue_last ? '$' . number_format($revenue_last, 2) : '$0';

    // Cases Completed
    $cases_completed = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
         WHERE case_status='completed' AND created_at BETWEEN %s AND %s",
        $start_date, $today
    ));
    $cases_completed = str_pad($cases_completed, 2, '0', STR_PAD_LEFT);

    // Active Clients
    $active_cases = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}pixelcode_cases 
         WHERE TRIM(case_status) IN ('pending initial review','pending provider review','signed') 
           AND created_at BETWEEN %s AND %s",
        $start_date, $today
    ));
    $active_cases_display = $active_cases < 10 ? sprintf("%02d", $active_cases) : $active_cases;

    // Avg Processing Time
    $avg_processing = $wpdb->get_var($wpdb->prepare(
        "SELECT AVG(DATEDIFF(payment_date, created_at)) 
         FROM {$wpdb->prefix}pixelcode_cases 
         WHERE case_status='completed' AND payment_date BETWEEN %s AND %s",
        $start_date, $today
    ));
    $avg_processing_display = $avg_processing ? round($avg_processing,1) . ' days' : '0 days';

    // Send JSON response
    wp_send_json([
        'total_revenue' => $revenue_last,
        'cases_completed' => $cases_completed,
        'active_clients' => $active_cases_display,
        'avg_processing' => $avg_processing_display
    ]);
}




// ------------------------- cases progress ------------------------------------------------
add_action('wp_ajax_update_case_status', 'update_case_status_callback');
add_action('wp_ajax_nopriv_update_case_status', 'update_case_status_callback');

function update_case_status_callback() {
    global $wpdb;
    $table = $wpdb->prefix . "pixelcode_cases";

    $case_id = sanitize_text_field($_POST['case_id']);
    $case_status = sanitize_text_field($_POST['case_status']);

    $updated = $wpdb->update(
        $table,
        ['case_status' => $case_status],
        ['case_id' => $case_id],
        ['%s'],
        ['%s']
    );

    if ($updated !== false) {
        wp_send_json_success("Case status updated successfully");
    } else {
        wp_send_json_error("Failed to update case status");
    }
}

