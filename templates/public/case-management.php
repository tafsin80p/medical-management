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
        <option value="pending">Pending</option>
        <option value="open">Open</option>
        <option value="progress">Progress</option>
        <option value="closed">Closed</option>
    </select>

    <!-- Button -->
    <button id='add-form-button'
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
                <th class="px-8 py-3">Status</th>
                <th class="px-6 py-3">Assigned To</th>
                <th class="px-6 py-3">Progress</th>
                <th class="px-6 py-3">Start Date</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody id='case-table-body' class="divide-y divide-gray-200">
            <!-- Dynamic rows will be inserted here -->
        </tbody>
    </table>
</div>