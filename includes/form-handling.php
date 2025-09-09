<?php
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
            'case_status' => 'pending initial review',
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
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    $doc_fields = [
        'documents_dd214' => ['required' => true, 'mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] ],
        'documents_medical' => ['required' => true, 'mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] ],
        'documents_rating' => ['required' => true, 'mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] ],
        'documents_decision' => ['required' => true, 'mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] ],
        'documents_optional' => ['required' => false, 'mimes' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] ]
    ];

    $upload_overrides = array( 'test_form' => false );

    foreach($doc_fields as $field => $options) {
        if(!empty($_FILES[$field]['name'][0])) {
            $files = $_FILES[$field];
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );
                    $movefile = wp_handle_upload( $file, $upload_overrides );
                    if ( $movefile && !isset( $movefile['error'] ) ) {
                        $wpdb->insert(
                            "{$wpdb->prefix}pixelcode_cases_case_documents",
                            [
                                'case_id' => $case_id,
                                'document_type' => str_replace('documents_', '', $field),
                                'file_path' => $movefile['url'],
                                'required' => $options['required']
                            ]
                        );
                    } else {
                        wp_send_json_error(['message' => $movefile['error']]);
                    }
                }
            }
        }
    }

    wp_send_json_success(['message' => 'Case submitted successfully!', 'case_id' => $case_id, 'case_data' => $_POST]);
}
