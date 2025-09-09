<?php
defined('ABSPATH') || exit;
?>

<div class="container bg-white p-6 rounded-lg shadow-md mt-6 pixelcodeCaselists">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <ul id="tabs" class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="me-2 m-0">
                <button
                    class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg"
                    data-tab="cases">
                    <i class="fa-solid fa-folder-open mr-2 text-blue-600"></i>
                    Case Management
                </button>
            </li>
            <!-- <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg" data-tab="clients">
                    <i class="fa-solid fa-users mr-2 text-green-600"></i>
                    Active Cases
                </button>
            </li>
            <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg" data-tab="analytics">
                    <i class="fa-solid fa-chart-bar mr-2 text-purple-600"></i>
                    Complete Cases
                </button>
            </li> -->
        </ul>
    </div>

    <!-- Tab content -->
    <div class="tab-content mt-4">

        <div id="cases" class="tab-panel">
            <?php include(PIXELCODE_PLUGIN_DIR . 'templates/public/case-management.php'); ?>
        </div>
        <div id="clients" class="tab-panel hidden">Content for Clients</div>
        <div id="analytics" class="tab-panel hidden">Content for Analytics</div>
        <div id="team" class="tab-panel hidden">Content for Team</div>
    </div>
</div>