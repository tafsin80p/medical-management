<?php
defined('ABSPATH') || exit;

global $wpdb;

// ✅ Fetch cases with joins (optimized query)
$results = $wpdb->get_results("
    SELECT 
        c.case_id,
        c.first_name,
        c.last_name,
        c.priority,
        c.va_file_number,
        c.created_at,
        c.case_status,
        c.assigned_to,
        s.service_composition,
        v.condition
    FROM {$wpdb->prefix}pixelcode_cases AS c
    LEFT JOIN {$wpdb->prefix}pixelcode_cases_service_history AS s ON c.case_id = s.case_id
    LEFT JOIN {$wpdb->prefix}pixelcode_cases_va_claims AS v ON c.case_id = v.case_id
    ORDER BY c.created_at DESC
");

// ✅ Fetch authors once
$authors = get_users([
    'role'    => 'Author',
    'orderby' => 'display_name',
    'order'   => 'ASC'
]);

// ✅ Status → Progress mapping
$status_progress = [
    'pending initial review' => 25,
    'pending provider review' => 50,
    'signed'                 => 75,
    'completed'              => 100,
];

// ✅ Priority → Badge colors
$priorityColors = [
    'low'     => 'bg-gray-100 text-gray-800',
    'high'    => 'bg-red-100 text-red-800',
    'premium' => 'bg-yellow-100 text-yellow-800',
];
?>

<!-- 🔹 Filters -->
<div class="flex items-center mb-6 bg-white p-2">
    <!-- Search -->
    <div class="relative w-9/12 mr-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
        <input style='padding-left: 40px;' type="text" id="case-search" placeholder="Search cases with case ID"
            class="rounded-lg p-3 h-10 w-full border border-gray-200">
    </div>

    <!-- Priority filter -->
    <select id="case-priority-filter" class="rounded-lg px-4 py-2 mr-4 w-2/12 h-10 border border-gray-200">
        <option value="">All Statuses</option>
        <option value="low">Low</option>
        <option value="high">High</option>
        <option value="premium">Premium</option>
    </select>

    <!-- Status filter -->
    <select id="case-status-filter" class="rounded-lg px-4 py-2 mr-4 w-2/12 h-10 border border-gray-200">
        <option value="">All Statuses</option>
        <?php foreach ($status_progress as $status => $progress): ?>
        <option value="<?= esc_attr($status) ?>"><?= esc_html(ucwords($status)) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- DB test button -->
    <button id="test-db-button"
        class="bg-blue-600 w-1/12 text-white h-10 rounded-lg hover:bg-blue-700 flex justify-center items-center uppercase text-sm font-medium">
        Test DB
    </button>
</div>

<!-- 🔹 Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500 table">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Case Name</th>
                <th class="px-6 py-3">Priority</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Assigned To</th>
                <th class="px-6 py-3">Progress</th>
                <th class="px-6 py-3">Start Date</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($results): ?>
            <?php foreach ($results as $row): ?>
            <?php
                        // Case values
                        $case_id   = esc_attr($row->case_id);
                        $priority  = strtolower($row->priority ?: 'low');
                        $status    = strtolower(trim($row->case_status ?: 'pending'));
                        $progress  = $status_progress[$status] ?? 0;
                        $priorityClass = $priorityColors[$priority] ?? $priorityColors['low'];
                    ?>
            <tr class="odd:bg-white even:bg-gray-50 border-b">
                <!-- Case Name -->
                <td class="px-6 py-4">
                    <div class="text-md font-medium text-gray-900">
                        MD NEXUSPROS CASES - <?= esc_html("{$row->first_name} {$row->last_name}") ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Case ID: <span class="text-gray-800 font-medium case-id"><?= esc_html($row->case_id) ?></span>
                    </div>
                </td>

                <!-- Priority -->
                <td class="px-6 py-4">
                    <span id="priority-badge-<?= $case_id ?>"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium priority <?= $priorityClass ?>">
                        <?= esc_html(strtoupper($priority)) ?>
                    </span>
                    <select id="priority-dropdown-<?= $case_id ?>"
                        class="priority-dropdown hidden ml-2 text-sm border rounded px-2 py-1"
                        data-case-id="<?= $case_id ?>">
                        <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="premium" <?= $priority === 'premium' ? 'selected' : '' ?>>Premium</option>
                    </select>
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                    <span id="status-badge-<?= $case_id ?>"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 status">
                        <?= esc_html(strtoupper($status)) ?>
                    </span>
                    <select id="status-dropdown-<?= $case_id ?>"
                        class="status-dropdown ml-2 text-sm border rounded px-2 py-1 hidden"
                        data-case-id="<?= $case_id ?>">
                        <option value="">All Statuses</option>
                        <?php foreach ($status_progress as $st => $pr): ?>
                        <option value="<?= esc_attr($st) ?>" <?= $st === $status ? 'selected' : '' ?>>
                            <?= esc_html(ucwords($st)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>

                <!-- Assigned -->
                <td class="px-6 py-4">
                    <span id="assigned-badge-<?= $case_id ?>"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <?= esc_html(strtoupper($row->assigned_to ?: 'UNASSIGNED')) ?>
                    </span>
                    <select id="assigned-dropdown-<?= $case_id ?>"
                        class="assigned-dropdown hidden ml-2 text-sm border rounded px-2 py-1"
                        data-case-id="<?= $case_id ?>">
                        <option value="">UNASSIGNED</option>
                        <?php foreach ($authors as $author): ?>
                        <option data-dr-id="<?= esc_attr($author->ID) ?>" value="<?= esc_html($author->display_name) ?>"
                            <?= $author->display_name === $row->assigned_to ? 'selected' : '' ?>>
                            <?= esc_html($author->display_name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>

                <!-- Progress -->
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-blue-600 h-2 rounded-full progress-bar" data-case-id="<?= $case_id ?>"
                                style="width: <?= $progress ?>%; transition: width 0.5s;">
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 progress-text" data-case-id="<?= $case_id ?>">
                            <?= $progress ?>%
                        </span>
                    </div>
                </td>

                <!-- Start Date -->
                <td class="px-6 py-4 text-sm text-gray-900">
                    <?= esc_html($row->created_at) ?>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-sm font-medium space-x-4">
                    <!-- View -->
                    <button class="text-blue-600 hover:text-blue-900 admin_view_btn" data-case-id="<?= $case_id ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px"
                            height="20px" viewBox="0 -4 20 20" version="1.1">

                            <title>view_simple [#815]</title>
                            <desc>Created with Sketch.</desc>
                            <defs>

                            </defs>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Dribbble-Light-Preview" transform="translate(-260.000000, -4563.000000)"
                                    fill="#000000">
                                    <g id="icons" transform="translate(56.000000, 160.000000)">
                                        <path
                                            d="M216,4409.00052 C216,4410.14768 215.105,4411.07682 214,4411.07682 C212.895,4411.07682 212,4410.14768 212,4409.00052 C212,4407.85336 212.895,4406.92421 214,4406.92421 C215.105,4406.92421 216,4407.85336 216,4409.00052 M214,4412.9237 C211.011,4412.9237 208.195,4411.44744 206.399,4409.00052 C208.195,4406.55359 211.011,4405.0763 214,4405.0763 C216.989,4405.0763 219.805,4406.55359 221.601,4409.00052 C219.805,4411.44744 216.989,4412.9237 214,4412.9237 M214,4403 C209.724,4403 205.999,4405.41682 204,4409.00052 C205.999,4412.58422 209.724,4415 214,4415 C218.276,4415 222.001,4412.58422 224,4409.00052 C222.001,4405.41682 218.276,4403 214,4403"
                                            id="view_simple-[#815]">

                                        </path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </button>
                    <!-- Edit -->
                    <button class="text-green-600 hover:text-green-900 cases_edit_button"
                        data-case-id="<?= $case_id ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-4">No cases found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>