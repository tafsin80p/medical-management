<?php
defined('ABSPATH') || exit;
global $wpdb;

// Get all authors
$users = get_users([
    'role'    => 'Author',
    'orderby' => 'display_name',
    'order'   => 'ASC'
]);
?>

<div class="overflow-x-auto bg-white rounded-lg shadow-sm w-full mt-8">
    <table class="w-full text-sm text-left text-gray-500 table">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Team Member</th>
                <th class="px-6 py-3">Contact</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Case Load</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $users ): ?>
            <?php foreach ( $users as $user ): ?>
            <?php
                    // Join Date
                    $join_date = $user->user_registered 
                        ? date('M d, Y', strtotime($user->user_registered)) 
                        : 'N/A';

                    // Contact
                    $email = $user->user_email;
                    $phone = get_user_meta($user->ID, 'phone_number', true);

                    // Role
                    $roles = implode(', ', $user->roles);

                    // Cases (from pixelcode_cases)
                    $total_cases = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE assigned_dr_id = %d",
                        $user->ID
                    ));
                    $active_cases = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
                         WHERE assigned_dr_id = %d 
                         AND TRIM(case_status) IN ('pending initial review','pending provider review','signed')",
                        $user->ID
                    ));

                    ?>
            <tr class="odd:bg-white even:bg-gray-50 border-b">
                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        <?= esc_html($user->display_name) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Joined <?= esc_html($join_date) ?>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 text-md font-medium text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M20 4H4C2.897 4 2 4.897 2 6v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zM4 8.414l6.707 6.707a.999.999 0 0 0 1.414 0L20 8.414V18H4V8.414zM20 6l-8 8-8-8h16z" />
                        </svg>
                        <a href="mailto:<?= esc_html($email) ?>" class="hover:underline">
                            <?= esc_html($email) ?>
                        </a>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M20.487 17.14l-4.267-1.829a1.003 1.003 0 0 0-1.17.291l-2.2 2.68c-3.071-1.44-5.581-3.949-7.021-7.021l2.68-2.2a1.001 1.001 0 0 0 .291-1.17L6.86 3.513A1 1 0 0 0 5.828 3H4c-1.104 0-2 .896-2 2 0 9.374 7.626 17 17 17 1.104 0 2-.896 2-2v-1.828a1 1 0 0 0-.513-.86z" />
                        </svg>
                        <?php if ($phone): ?>
                        <a href="tel:<?= esc_html($phone) ?>" class="hover:underline">
                            <?= esc_html($phone) ?>
                        </a>
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </div>
                </td>

                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        Doctor
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        <?= intval($active_cases) ?> Active
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <?= intval($total_cases) ?> Total
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">No members found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>