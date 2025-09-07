<?php
defined('ABSPATH') || exit;

?>
<div class="container bg-white p-6 rounded-lg shadow-md mt-6">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <ul id="tabs" class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg " data-tab="cases">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 mr-2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    Case Management
                </button>
            </li>
            <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg " data-tab="clients">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-5 w-5 mr-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Clients
                </button>
            </li>
            <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg " data-tab="analytics">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bar-chart3 h-5 w-5 mr-2"><path d="M3 3v18h18"></path><path d="M18 17V9"></path><path d="M13 17V5"></path><path d="M8 17v-3"></path></svg>
                    Analytics
                </button>
            </li>
            <li class="me-2 m-0">
                <button class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg " data-tab="team">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings h-5 w-5 mr-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Team
                </button>
            </li>
        </ul>
    </div>


    <!-- Tab content -->
    <div class="tab-content mt-4">
        <div id="cases" class="tab-panel">
            <?php include PIXELCODE_PLUGIN_DIR . 'templates/admin/case-management.php'; ?>
        </div>
        <div id="clients" class="tab-panel hidden">
            <?php include PIXELCODE_PLUGIN_DIR . 'templates/admin/client-management.php'; ?>
        </div>
        <div id="analytics" class="tab-panel hidden">
            <?php include PIXELCODE_PLUGIN_DIR . 'templates/admin/analytics.php'; ?>
        </div>
        <div id="team" class="tab-panel hidden">
            <?php include PIXELCODE_PLUGIN_DIR . 'templates/admin/team-management.php'; ?>
        </div>
    </div>
</div>
<?php