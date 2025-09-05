<?php
defined('ABSPATH') || exit;
global $wpdb;

$users = get_users([
    'role'    => 'Author',
    'orderby' => 'display_name',
    'order'   => 'ASC'
]);
?>

<div class="overflow-x-auto bg-white rounded-lg shadow-sm w-full mt-8">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Member Name</th>
                <th class="px-6 py-3">Active Cases</th>
                <th class="px-6 py-3">Completed Cases</th>
                <th class="px-6 py-3">Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $users ): ?>
                <?php foreach ( $users as $user ): ?>
                    <?php
                        // Active cases
                        $active_cases = $wpdb->get_var( $wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE user_id = %d AND case_status != %s",
                            $user->ID, 'closed'
                        ));

                        // Completed cases
                        $completed_cases = $wpdb->get_var( $wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE user_id = %d AND case_status = %s",
                            $user->ID, 'closed'
                        ));

                        // Rating (avg)
                        $rating = $wpdb->get_var( $wpdb->prepare(
                            "SELECT AVG(rating) FROM {$wpdb->prefix}pixelcode_cases WHERE user_id = %d",
                            $user->ID
                        ));
                        $rating = $rating ? round($rating, 1) : 'N/A';
                    ?>
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <td class="px-6 py-4">
                            <div class="text-md font-medium text-gray-900">
                                <?= esc_html($user->display_name) ?>
                            </div>
                            <div class="text-sm text-gray-500 mt-2">
                                Email: <?= esc_html($user->user_email) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4"><?= esc_html($active_cases) ?></td>
                        <td class="px-6 py-4"><?= esc_html($completed_cases) ?></td>
                        <td class="px-6 py-4"><?= esc_html($rating) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-500">No members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
