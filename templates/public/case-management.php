<?php
defined('ABSPATH') || exit;


?>
<div class="flex flex-wrap items-center mb-6 bg-white p-4 rounded-lg shadow-md">
    <!-- Search -->
    <div class="relative flex-1 mr-4">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-sm"></i>
        <input type="text" id="case-search" placeholder="Search cases..."
            class="w-full text-sm rounded-lg border border-gray-300 p-3 pl-10 h-10 focus:outline-none focus:ring-1 focus:ring-blue-600">
    </div>

    <!-- Status Filter -->
    <select id="case-status-filter" class="rounded-lg border border-gray-300 px-2 py-2 mr-4 text-sm ">
        <option value="">All Statuses</option>
        <option value="open">Open</option>
        <option value="in-progress">In Progress</option>
        <option value="closed">Closed</option>
    </select>

    <!-- Button -->
    <button id="add-form-button"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium uppercase flex items-center justify-center">
        add case
    </button>
</div>

<!-- Cases Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
            <tr>
                <th class="px-6 py-3">Case Name</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Assigned To</th>
                <th class="px-6 py-3">Progress</th>
                <th class="px-6 py-3">Start Date</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php
            // Example dynamic data, replace with real client-side data
            $cases = [
                [
                    'name' => 'John Smith',
                    'code' => 'NX-2024-001',
                    'type' => 'PTSD',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'assigned_to' => 'Dr. Sarah Johnson',
                    'progress' => 60,
                    'start_date' => '02/15/2024',
                ],
                // Add more cases here
            ];

            foreach ($cases as $case) :
            ?>
            <tr class="bg-white">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?php echo esc_html($case['name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo esc_html($case['code'] . ' • ' . $case['type']); ?></div>
                        </div>
                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php echo $case['priority'] === 'High' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo esc_html($case['priority']); ?>
                        </span>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <?php echo esc_html($case['status']); ?>
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <?php echo esc_html($case['assigned_to']); ?>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo esc_attr($case['progress']); ?>%;"></div>
                        </div>
                        <span class="text-sm text-gray-600"><?php echo esc_html($case['progress'] . '%'); ?></span>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo esc_html($case['start_date']); ?></td>

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                    <button class="text-blue-600 hover:text-blue-900">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button class="text-green-600 hover:text-green-900">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

