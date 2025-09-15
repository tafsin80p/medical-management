<?php
defined('ABSPATH') || exit;
global $wpdb;

// Get unique clients from pixelcode_cases
$clients = $wpdb->get_results("
    SELECT DISTINCT user_id
    FROM {$wpdb->prefix}pixelcode_cases
    ORDER BY user_id ASC
");
?>

<div class="overflow-x-auto bg-white rounded-lg shadow-sm w-full mt-8">
    <table class="w-full text-sm text-left text-gray-500 table">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Client Name</th>
                <th class="px-6 py-3">Contact</th>
                <th class="px-6 py-3">Cases</th>
                <th class="px-6 py-3">Last Activity</th>
                <th class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $clients ): ?>
            <?php foreach ( $clients as $client ): ?>
            <?php
                        $user = get_userdata($client->user_id);

                        // Basic info
                        $display_name = $user ? $user->display_name : 'N/A';
                        $email = $user ? $user->user_email : 'N/A';

                        // Join date (registration date)
                        $join_date = $user ? date('M d, Y', strtotime($user->user_registered)) : 'N/A';

                        // Total cases
                        $total_cases = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE user_id = %d",
                            $client->user_id
                        ));

                        // Active cases
                        $active_cases = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
                             WHERE user_id = %d 
                             AND TRIM(case_status) IN ('pending initial review','pending provider review','signed')",
                            $client->user_id
                        ));

                        // Get last form submission date from pixelcode_cases
                        $last_form_date = $wpdb->get_var(
                            $wpdb->prepare("
                                SELECT MAX(created_at) 
                                FROM {$wpdb->prefix}pixelcode_cases 
                                WHERE user_id = %s
                            ", $client->user_id)
                        );

                        // Format date
                        $last_form_date_display = $last_form_date ? date('M d, Y', strtotime($last_form_date)) : 'N/A';

                        // Latest phone number from case
                        $phone_number = $wpdb->get_var($wpdb->prepare(
                            "SELECT phone FROM {$wpdb->prefix}pixelcode_cases 
                             WHERE user_id = %d 
                             ORDER BY created_at DESC LIMIT 1",
                            $client->user_id
                        ));
                    ?>
            <tr class="odd:bg-white even:bg-gray-50 border-b">
                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        <?= esc_html($display_name) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Joined <?= esc_html($join_date) ?>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center text-md font-medium text-gray-900">
                        <!-- Email Icon -->
                        <svg class="w-5 h-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 6l8 6 8-6M4 6v12h16V6" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <a href="mailto:<?= esc_attr($email) ?>" class="hover:underline">
                            <?= esc_html($email) ?>
                        </a>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mt-2">
                        <!-- Phone Icon -->
                        <svg class="w-5 h-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.518 4.55a1 1 0 01-.272 1.06l-1.2 1.2a16.001 16.001 0 007.2 7.2l1.2-1.2a1 1 0 011.06-.272l4.55 1.518a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.163 21 3 14.837 3 7V5z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <a href="tel:<?= esc_attr($phone_number) ?>" class="hover:underline">
                            <?= esc_html($phone_number ?: 'N/A') ?>
                        </a>
                    </div>
                </td>

                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        <?= intval($active_cases) ?> active
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <?= intval($total_cases) ?> total
                    </div>
                </td>
                <td class="px-6 py-4">
                    <?= esc_html($last_form_date_display) ?>
                </td>
                <td class="px-6 py-4 flex gap-4 items-center">
                    <button title='message' class='start-chat' data-user-id='<?= esc_attr($client->user_id) ?>' data-user-name='<?= esc_attr($display_name) ?>'>
                        <!-- message icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4-.832L3 20l1.125-3.374A7.928 7.928 0 0 1 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </button>
                    <button title="email">
                        <a href="mailto:<?= esc_attr($email) ?>">
                            <!-- email icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M4 6h16v12H4z" />
                                <path d="M22 6l-10 7L2 6" />
                            </svg>
                        </a>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">No clients found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>