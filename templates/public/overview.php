<?php
defined('ABSPATH') || exit;

global $wpdb;

// Current user
$current_user_id = get_current_user_id();

// Queries (use get_var for counts directly)
$total_cases = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
         WHERE user_id = %d",
        $current_user_id
    )
);

$active_cases = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) 
         FROM {$wpdb->prefix}pixelcode_cases 
         WHERE user_id = %d 
         AND TRIM(case_status) IN ('pending initial review','pending provider review','signed')",
        $current_user_id
    )
);

$completed_cases = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
         WHERE user_id = %d 
         AND TRIM(case_status) = 'completed'",
        $current_user_id
    )
);

// Config
$card_config = [
    'total' => [
        'title' => 'Total Cases',
        'icon'  => '<i class="fa-regular fa-file"></i>',
        'desc'  => 'All your cases in the system',
        'count' => $total_cases,
    ],
    'active' => [
        'title' => 'Active Cases',
        'icon'  => '<i class="fa-regular fa-clock"></i>',
        'desc'  => 'Cases that are currently active',
        'count' => $active_cases,
    ],
    'complete' => [
        'title' => 'Complete Cases',
        'icon'  => '<i class="fa-solid fa-circle-check"></i>',
        'desc'  => 'Cases that are completed',
        'count' => $completed_cases,
    ],
];

// Final dynamic array
$cards = [];

foreach ($card_config as $item) {
    $cards[] = [
        'title' => $item['title'],
        'count' => empty($item['count']) ? 'N/A' : str_pad($item['count'], 2, '0', STR_PAD_LEFT),
        'icon'  => $item['icon'],
        'desc'  => $item['desc'],
    ];
}
?>
<header
    class="flex items-center justify-between border-b border-gray-200 pb-4 bg-white px-6 mt-6 rounded-lg py-4 container pixelcode">
    <div>
        <!-- Header -->
        <h1 class="text-2xl text-gray-800 mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-shield h-7 w-7 text-blue-600 __web-inspector-hide-shortcut__">
                <path
                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                </path>
            </svg> Nexus pro Client Dashboard
        </h1>

        <!-- Subheading / description -->
        <p class="text-gray-600 text-lg max-w-3xl">
            Powerful client dashboard – Easily manage your cases, clients, team members, and track analytics in one
            place.
        </p>
    </div>

    <div class="relative w-full max-w-xs flex justify-end">

        <!-- Message Button -->
        <button style="margin-right: 20px; cursor: pointer;" id="messageButton"
            class="relative inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 focus:outline-none ml-4">
            <i style="font-size: 1.5rem !important" class="fa-regular fa-message"></i>

            <div id="messageDot"
                class="absolute w-3 h-3 bg-red-500 border-2 border-white rounded-full -top-0.5 start-2.5 hidden"></div>
        </button>


        <!-- Client Notification Button -->
        <button id="clientDropdownNotificationButton"
            class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none"
            type="button" aria-haspopup="true" aria-expanded="false">
            <i style="font-size: 1.5rem !important" class="fa-regular fa-bell"></i>
        </button>




        <!-- Client Dropdown -->
        <div id="clientDropdownNotification"
            class="z-20 hidden w-full max-w-sm bg-white rounded-lg shadow-sm absolute right-0 mt-6" role="menu"
            aria-labelledby="clientDropdownNotificationButton">
            <div class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50 text-sm">
                Notifications
            </div>
            <div id="clientNotificationList"
                class="divide-y divide-gray-100 h-96 scrollbar scrollbar-thin scrollbar-thumb-gray-300 overflow-y-auto">
                <!-- Dynamic client notifications will be inserted here -->
            </div>
            <div
                class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100 space-x-2">
                <div id='clientViewAllNotifications' class="inline-flex items-center cursor-pointer">
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

<main class="mt-6 container pixelcode">
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full rounded-lg my-4">
        <?php foreach ($cards as $card) : ?>
        <div class="p-4 rounded-lg bg-gray-100 shadow-md flex flex-col justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 flex justify-center items-center rounded-full bg-white text-blue-600">
                    <?php echo $card['icon']; ?>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">
                        <?php echo esc_html($card['title']); ?>
                    </h3>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php echo esc_html($card['count']); ?>
                    </p>
                </div>
            </div>
            <?php if (!empty($card['desc'])) : ?>
            <p class="text-gray-600 text-sm">
                <?php echo esc_html($card['desc']); ?>
            </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Chatbox -->
<div id="chatbox" class="fixed bottom-0 right-4 z-50 w-96 bg-white border border-gray-300 rounded-t-lg shadow-lg hidden">
    <div class="flex items-center justify-between p-3 bg-gray-100 border-b border-gray-300 rounded-t-lg">
        <h3 style="font-size: 1.2rem; margin:0;padding:0" class="text-lg font-semibold text-gray-800">Chat</h3>
        <div id="closeChatbox" class="text-gray-500 hover:text-gray-900 cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </div>
    </div>
    <div id="chatboxMessages" class="p-4 h-80 overflow-y-auto">
        <!-- Messages will be dynamically loaded here -->
    </div>
    <div class="p-4 bg-white border-t border-gray-200">
        <div class="flex items-center">
            <input type="file" id="chatboxFile" class="hidden">
            <div id="chatboxFileButton"
                class="mr-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-paperclip"></i>
            </div>
            <textarea style="color: #111" id="chatboxInput"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                rows="1" placeholder="Type a message..."></textarea>
            <div id="sendChatMessage" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-paper-plane"></i>
            </div>
        </div>
    </div>
</div>