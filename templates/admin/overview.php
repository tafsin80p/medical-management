<?php
defined('ABSPATH') || exit;

$dashboard_cards = [
    [
        'title' => 'Active Cases',
        'value' => 128,
        'growth' => '+12% since last month',
        'icon_color' => 'text-blue-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>',
    ],
    [
        'title' => 'Total Clients',
        'value' => 542,
        'growth' => '+8% since last month',
        'icon_color' => 'text-green-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><circle cx="12" cy="7" r="4"></circle><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>',
    ],
    [
        'title' => 'Monthly Revenue',
        'value' => '$12,450',
        'growth' => '+15% since last month',
        'icon_color' => 'text-yellow-500',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><path d="M12 1v22"></path><path d="M17 5H9a4 4 0 0 0 0 8h6a4 4 0 0 1 0 8H7"></path></svg>',
    ],
    [
        'title' => 'Completion Rate',
        'value' => '89%',
        'growth' => '+5% since last month',
        'icon_color' => 'text-purple-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><path d="M20 6L9 17l-5-5"></path></svg>',
    ],
];

?>
<header
    class="flex items-center justify-between border-b border-gray-200 pb-4 bg-white px-6 mt-6 rounded-lg py-4 container">
    <div class="">
        <!-- Header -->
        <h1 class="text-2xl text-gray-800 mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-shield h-7 w-7 text-blue-600 __web-inspector-hide-shortcut__">
                <path
                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                </path>
            </svg> Admin Dashboard
        </h1>

        <!-- Subheading / description -->
        <p class="text-gray-600 text-lg max-w-3xl">
            Comprehensive system administration - Manage cases, clients, team, and analytics
        </p>
    </div>


    <div class="relative w-full max-w-xs flex justify-end">
        <!-- Notification Button -->
        <button id="dropdownNotificationButton"
            class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none"
            type="button">
            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 14 20">
                <path
                    d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
            </svg>
            <div id="notificationDot"
                class="absolute w-3 h-3 bg-red-500 border-2 border-white rounded-full -top-0.5 start-2.5 dark:border-gray-900">
            </div>
        </button>

        <!-- Dropdown -->
        <div id="dropdownNotification"
            class="z-20 hidden w-full max-w-sm bg-white rounded-lg shadow-sm absolute right-0 mt-6"
            aria-labelledby="dropdownNotificationButton">
            <div
                class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50 dark:bg-gray-800 dark:text-white">
                Notifications
            </div>
            <div id="notificationList" class="divide-y divide-gray-100 h-96 scrollbar scrollbar-thin scrollbar-thumb-gray-300 overflow-y-auto">
                <!-- Dynamic notifications will be inserted here -->
            </div>
            <div class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100 space-x-2 ">
                <div id='view-all-notifications' class="inline-flex items-center cursor-pointer">
                    <svg class="w-4 h-4 me-2 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                        <path
                            d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                    </svg>
                    View all
                </div>
            </div>
        </div>
    </div>

</header>

<main class="mt-6 container">
    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($dashboard_cards as $card): ?>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="p-4 rounded-full bg-gray-100 <?php echo $card['icon_color']; ?> mr-4">
                <?php echo $card['icon_svg']; ?>
            </div>
            <div>
                <h2 class="text-gray-600"><?php echo $card['title']; ?></h2>
                <p class="text-2xl font-bold text-gray-800"><?php echo $card['value']; ?></p>
                <p class="text-sm text-green-500"><?php echo $card['growth']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</main>
<?php