<?php
// ------------------------------- add notification --------------------------------------
add_action('wp_ajax_add_dashboard_notification', function () {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';

    $message = sanitize_text_field($_POST['message']);
    // Check if a specific user_id is provided, otherwise default to the current user
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();

    // if user_id is 0, it's an admin notification
    if ($user_id === 0) {
        $user_status = 'read'; // Users should not see admin-only notifications
    } else {
        $user_status = 'unread';
    }

    $wpdb->insert(
        $table_name,
        [
            'user_id'      => $user_id,
            'message'      => $message,
            'time'         => current_time('mysql'),
            'admin_status' => 'unread', // Always unread for admin initially
            'user_status'  => $user_status,
        ],
        ['%d', '%s', '%s', '%s', '%s']
    );

    wp_send_json_success(['message' => 'Notification saved!']);
});




// --------------------------- AJAX: Get Notifications ---------------------------
add_action('wp_ajax_get_dashboard_notifications', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';
    $user_id = get_current_user_id();

    if (current_user_can('manage_options')) {
        // Admin: Get all notifications (for users and admin-specific)
        $notifications = $wpdb->get_results(
            "SELECT * FROM {$table_name} WHERE admin_status = 'unread' ORDER BY time DESC",
            ARRAY_A
        );
    } else {
        // Non-admin user: Get only their notifications
        $notifications = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE user_id = %d AND user_status = 'unread' ORDER BY time DESC",
                $user_id
            ),
            ARRAY_A
        );
    }

    wp_send_json_success($notifications);
});




// --------------------------- AJAX: Mark All Notifications Read ---------------------------
add_action('wp_ajax_mark_all_notifications_read', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pixelcode_dashboard_notifications';
    $user_id = get_current_user_id();

    if (current_user_can('manage_options')) {
        // Admin: Mark all of their visible notifications as read
        $wpdb->update(
            $table_name,
            ['admin_status' => 'read'], // Set admin_status to 'read'
            ['admin_status' => 'unread'], // Where admin_status is 'unread'
            ['%s'],
            ['%s']
        );
    } else {
        // Non-admin user: Mark all of their notifications as read
        $wpdb->update(
            $table_name,
            ['user_status' => 'read'], // Set user_status to 'read'
            ['user_id' => $user_id, 'user_status' => 'unread'], // Where user_id matches and user_status is 'unread'
            ['%s'],
            ['%d', '%s']
        );
    }

    wp_send_json_success(['message' => 'All notifications marked as read.']);
});



// --------------------------- AJAX: Get Client Cases ---------------------------
add_action('wp_ajax_pixelcode_get_all_cases', 'pixelcode_get_all_cases');

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
        "SELECT * FROM $history_table WHERE case_id IN ('" . implode(" ','", $case_ids) . "')"
    );

    $history_ids = wp_list_pluck($service_history, 'id');

    $deployments = $wpdb->get_results(
        "SELECT * FROM $deployments_table WHERE service_history_id IN (" . implode(',', $history_ids) . ")"
    );

    $claims = $wpdb->get_results(
        "SELECT * FROM $claims_table WHERE case_id IN ('" . implode(" ','", $case_ids) . "')"
    );

    $documents = $wpdb->get_results(
        "SELECT * FROM $documents_table WHERE case_id IN ('" . implode(" ','", $case_ids) . "')"
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
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        return;
    }

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

function update_dashboard_callback() {

    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
        return;
    }

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

function update_case_status_callback() {

    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
        return;
    }

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
