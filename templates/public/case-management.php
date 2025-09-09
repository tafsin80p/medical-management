<?php
defined('ABSPATH') || exit;


?>
<div class="pixelcodeFilter">
    <!-- Search -->
    <div>
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="case-search" placeholder="Search cases...">
    </div>

    <!-- Status Filter -->
    <select id="case-status-filter">
        <option value="">All Statuses</option>
        <option value="Pending Initial Review">Pending Initial Review</option>
        <option value="Pending Provider Review">Pending Provider Review</option>
        <option value="Signed">Signed</option>
        <option value="Completed">Completed</option>
    </select>

    <!-- Button -->
    <button id='add-form-button'>
        add case
    </button>
</div>

<!-- Cases Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow-md pixelcodeTable">
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