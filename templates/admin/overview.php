<?php
defined('ABSPATH') || exit;

global $wpdb;

// ---------------------------
// Current & Last Month
// ---------------------------
$current_month = date('m');
$current_year  = date('Y');
$last_month = $current_month - 1;
$last_month_year = $last_month == 0 ? $current_year - 1 : $current_year;
$last_month = $last_month == 0 ? 12 : $last_month;

// ---------------------------
// 1. Active Cases
// ---------------------------
$active_cases = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
     WHERE TRIM(case_status) IN ('pending initial review','pending provider review','signed')"
);
$active_cases_display = $active_cases < 10 ? sprintf("%02d", $active_cases) : $active_cases;

// Total cases this month (all cases)
$total_cases_this_month = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
     WHERE MONTH(created_at) = $current_month 
       AND YEAR(created_at) = $current_year"
);

// Display string
$total_cases_display = $total_cases_this_month . ' total cases last month';

// ---------------------------
// 2. Total Clients
// ---------------------------
$total_clients = $wpdb->get_var(
    "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}pixelcode_cases"
);
$total_clients_display = $total_clients < 10 ? sprintf("%02d", $total_clients) : $total_clients;

$total_clients_this_month = $wpdb->get_var(
    "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}pixelcode_cases
     WHERE MONTH(created_at) = $current_month AND YEAR(created_at) = $current_year"
);

$total_clients_last_month = $wpdb->get_var(
    "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}pixelcode_cases
     WHERE MONTH(created_at) = $last_month AND YEAR(created_at) = $last_month_year"
);

$clients_growth = $total_clients_last_month > 0 
    ? round((($total_clients_this_month - $total_clients_last_month) / $total_clients_last_month) * 100) 
    : 100;
$clients_growth_display = ($clients_growth >= 0 ? '+' : '') . $clients_growth . '% since last month';

// ---------------------------
// 3. Monthly Revenue
// ---------------------------
$monthly_revenue = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2))) 
         FROM {$wpdb->prefix}pixelcode_cases 
         WHERE payment_status = 'completed' 
           AND MONTH(payment_date) = %d 
           AND YEAR(payment_date) = %d",
        $current_month, 
        $current_year
    )
);
$monthly_revenue_display = $monthly_revenue ? '$' . number_format($monthly_revenue, 2) : '$0.00';

$revenue_last_month = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2))) 
         FROM {$wpdb->prefix}pixelcode_cases 
         WHERE payment_status = 'completed' 
           AND MONTH(payment_date) = %d 
           AND YEAR(payment_date) = %d",
        $last_month, 
        $last_month_year
    )
);

$revenue_growth = $revenue_last_month > 0 
    ? round((($monthly_revenue - $revenue_last_month) / $revenue_last_month) * 100) 
    : 100;
$revenue_growth_display = ($revenue_growth >= 0 ? '+' : '') . $revenue_growth . '% since last month';

// ---------------------------
// 4. Completion Rate
// ---------------------------
$total_cases = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases");
$completed_cases = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE case_status = 'completed'");
$completion_rate = $total_cases > 0 ? round(($completed_cases / $total_cases) * 100) . '%' : '0%';

$completed_cases_last_month = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
     WHERE case_status = 'completed' 
       AND MONTH(created_at) = $last_month 
       AND YEAR(created_at) = $last_month_year"
);
$completion_rate_last_month = $total_cases > 0 
    ? round(($completed_cases_last_month / $total_cases) * 100) 
    : 0;

$completion_growth = $completion_rate_last_month > 0 
    ? round(str_replace('%','',$completion_rate) - $completion_rate_last_month) 
    : str_replace('%','',$completion_rate);
$completion_growth_display = ($completion_growth >= 0 ? '+' : '') . $completion_growth . '% since last month';

// ---------------------------
// Dashboard Cards Array
// ---------------------------
$dashboard_cards = [
    [
        'title' => 'Active Cases',
        'value' => $active_cases_display,
        'growth' => $total_cases_display,
        'icon_color' => 'text-blue-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>',
    ],
    [
        'title' => 'Total Clients',
        'value' => $total_clients_display,
        'growth' => $clients_growth_display,
        'icon_color' => 'text-green-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="7" r="4"></circle><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>',
    ],
    [
        'title' => 'Monthly Revenue',
        'value' => $monthly_revenue_display,
        'growth' => $revenue_growth_display,
        'icon_color' => 'text-yellow-500',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 1v22"></path><path d="M17 5H9a4 4 0 0 0 0 8h6a4 4 0 0 1 0 8H7"></path></svg>',
    ],
    [
        'title' => 'Completion Rate',
        'value' => $completion_rate,
        'growth' => $completion_growth_display,
        'icon_color' => 'text-purple-600',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20 6L9 17l-5-5"></path></svg>',
    ],
];
?>



<!-- =========================== -->
<!-- Dashboard Header -->
<!-- =========================== -->
<header
    class="flex items-center justify-between border-b border-gray-200 pb-4 bg-white px-6 mt-6 rounded-lg py-4 container">
    <div>
        <h1 class="text-2xl text-gray-800 mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                </path>
            </svg>
            Admin Dashboard
        </h1>
        <p class="text-gray-600 text-lg max-w-3xl">
            Comprehensive system administration - Manage cases, clients, team, and analytics
        </p>
    </div>

    <div class="relative w-full max-w-xs flex justify-end">
        <!-- Notification Button -->
        <button id="dropdownNotificationButton"
            class="relative inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 focus:outline-none">
            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 14 20">
                <path
                    d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
            </svg>
            <div id="notificationDot"
                class="absolute w-3 h-3 bg-red-500 border-2 border-white rounded-full -top-0.5 start-2.5"></div>
        </button>

        <!-- Dropdown -->
        <div id="dropdownNotification"
            class="z-20 hidden w-full max-w-sm bg-white rounded-lg shadow-sm absolute right-0 mt-6">
            <div class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50">
                Notifications
            </div>
            <div id="notificationList"
                class="divide-y divide-gray-100 h-96 scrollbar scrollbar-thin scrollbar-thumb-gray-300 overflow-y-auto">
                <!-- Dynamic notifications will be inserted here -->
            </div>
            <div
                class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100 space-x-2">
                <div id='view-all-notifications' class="inline-flex items-center cursor-pointer">
                    <svg class="w-4 h-4 me-2 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 14">
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
            <div class="p-4 rounded-full bg-gray-100 <?php echo esc_attr($card['icon_color']); ?> mr-4">
                <?php echo $card['icon_svg']; ?>
            </div>
            <div>
                <h2 class="text-gray-600"><?php echo esc_html($card['title']); ?></h2>
                <p class="text-2xl font-bold text-gray-800"><?php echo esc_html($card['value']); ?></p>
                <p class="text-sm text-green-500"><?php echo esc_html($card['growth']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>