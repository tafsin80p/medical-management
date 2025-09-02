<?php
defined('ABSPATH') || exit;

?>
<div class="flex items-center mb-6 bg-white p-2">
    <div class="relative w-9/12 mr-4">
        <!-- Search -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-search absolute left-3 top-3 h-5 w-5 text-gray-400">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
        <input type="text" id="case-search"
            style="padding-left: 2.5rem; border-color: #e5e7eb; outline: none; focus:ring: 0px;"
            placeholder="Search cases... " class="rounded-lg p-3 h-10 w-full">
    </div>


    <!-- Status Filter -->
    <select id="case-status-filter" class="rounded-lg px-4 py-2 mr-4 w-2/12 h-10" style="border-color: #e5e7eb;">
        <option value="">All Statuses</option>
        <option value="open">Open</option>
        <option value="in-progress">In Progress</option>
        <option value="closed">Closed</option>
    </select>

    <!-- Button -->
    <button
        id="test-db-button"
        class="bg-blue-600 w-1/12 text-white h-10 px-4 py-2 rounded-lg hover:bg-blue-700 flex justify-center  items-center uppercase text-sm font-medium">
        text db
    </button>
</div>

<!-- cases table -->
<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Case Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Assigned To
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Progress
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Start Date
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">John Smith</div>
                                    <div class="text-sm text-gray-500">NX-2024-001 • PTSD</div>
                                </div><span
                                    class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">high</span>
                            </div>
                        </div>
                    </td>

                    <!-- status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-clock h-5 w-5 text-gray-500">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dr. Sarah Johnson</span>
                            <select class="ml-2 text-xs border border-gray-300 rounded px-1 w-24 hidden">
                                <option value="submitted">Submitted</option>
                                <option value="under_review">Under Review</option>
                                <option value="in_progress">In Progress</option>
                                <option value="awaiting_payment">Awaiting Payment</option>
                                <option value="complete">Complete</option>
                                <option value="denied">Denied</option>
                            </select>
                        </div>
                    </td>

                    <!-- assigned to -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dr. Sarah Johnson</span>
                        <select class="text-sm border border-gray-300 rounded px-2 py-1 hidden">
                            <option value="">Unassigned</option>
                            <option value="Dr. Sarah Johnson">Dr. Sarah Johnson</option>
                            <option value="Dr. Michael Brown">Dr. Michael Brown</option>
                        </select>
                    </td>

                    <!-- case progress -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 60%;"></div>
                            </div><span class="text-sm text-gray-600">60%</span>
                        </div>
                    </td>

                    <!-- start date -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2/15/2024</td>

                    <!-- action -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                        <button class="text-blue-600 hover:text-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" width="34"
                                height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-5 w-5">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                        <button class="text-green-600 hover:text-green-900">
                            <svg
                                xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-square-pen h-5 w-5">
                                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php