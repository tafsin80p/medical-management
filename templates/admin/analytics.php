<?php
global $wpdb;

// --- Date ranges (last 30 days) ---
$today = current_time('Y-m-d H:i:s');
$last_30_start = date('Y-m-d H:i:s', strtotime('-30 days', strtotime($today)));


// total revenue
$query = $wpdb->prepare(
    "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2))) 
     FROM {$wpdb->prefix}pixelcode_cases 
     WHERE payment_status='completed' AND payment_date BETWEEN %s AND %s",
    $last_30_start, $today
);

$revenue_last = $wpdb->get_var($query);

// complite cases
$cases_completed = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) 
     FROM {$wpdb->prefix}pixelcode_cases 
     WHERE case_status = 'completed' 
       AND created_at BETWEEN %s AND %s",
    $last_30_start, $today
));

$cases_completed_display = str_pad($cases_completed, 2, '0', STR_PAD_LEFT);



// Count active clients 
$active_clients = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(DISTINCT user_id) 
     FROM {$wpdb->prefix}pixelcode_cases 
     WHERE case_status='active' AND created_at BETWEEN %s AND %s",
    $last_30_start, $today
));

$active_clients_display = str_pad($active_clients, 2, '0', STR_PAD_LEFT);


// active cases
$active_cases = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases
         WHERE TRIM(case_status) IN ('pending initial review','pending provider review','signed')
         AND created_at BETWEEN %s AND %s",
        $last_30_start,
        $today
    )
);

$active_cases_display = $active_cases < 10 ? sprintf("%02d", $active_cases) : $active_cases;



// Calculate average processing time for completed cases
$avg_processing = $wpdb->get_var($wpdb->prepare(
    "SELECT AVG(DATEDIFF(payment_date, created_at)) 
     FROM {$wpdb->prefix}pixelcode_cases 
     WHERE case_status = 'completed' 
       AND payment_date BETWEEN %s AND %s",
    $last_30_start,
    $today
));

$avg_processing_display = $avg_processing ? round($avg_processing, 1) . ' days' : '0 days';



// Prepare months array for last 6 months
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-{$i} months", strtotime($today)));
    $months[] = $month;
}

// Initialize arrays
$monthly_cases = [];
$monthly_revenue = [];

foreach ($months as $month) {
    $start_date = $month . '-01';
    $end_date = date('Y-m-t', strtotime($start_date));

    // Total cases in month
    $cases = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases
         WHERE created_at BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    $monthly_cases[$month] = $cases ? intval($cases) : 0;

    // Revenue in month
    $revenue = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2))) FROM {$wpdb->prefix}pixelcode_cases
         WHERE payment_status='completed' AND payment_date BETWEEN %s AND %s",
        $start_date, $end_date
    ));
    $monthly_revenue[$month] = $revenue ? floatval($revenue) : 0;
}

// Current Case Status counts
$status_counts = $wpdb->get_results(
    "SELECT case_status, COUNT(*) as total FROM {$wpdb->prefix}pixelcode_cases
     GROUP BY case_status", ARRAY_A
);

// Compute total cases for percentages
$total_cases = array_sum(array_column($status_counts, 'total'));



// Get total number of cases for calculating percentages
$total_cases = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases");

// Get counts grouped by case_status
$case_status_counts = $wpdb->get_results(
    "SELECT case_status, COUNT(*) as total
     FROM {$wpdb->prefix}pixelcode_cases
     GROUP BY case_status",
    ARRAY_A
);

// Optional: define colors for each status
$status_colors = [
    'in progress' => 'rgb(59, 130, 246)',      
    'awaiting payment' => 'rgb(245, 158, 11)',
    'submitted' => 'rgb(239, 68, 68)',       
];



// 1. Revenue Growth: sum of completed payments in last 30 days
$revenue_growth = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(CAST(payment_amount AS DECIMAL(10,2)))
     FROM {$wpdb->prefix}pixelcode_cases
     WHERE payment_status='completed' AND payment_date BETWEEN %s AND %s",
    $last_30_start, $today
));
$revenue_growth = $revenue_growth ? '$' . number_format($revenue_growth, 2) : '$0';

// 2. Case Completion: percentage of completed cases
$total_cases = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases WHERE created_at BETWEEN %s AND %s",
    $last_30_start, $today
));

$completed_cases = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pixelcode_cases 
     WHERE case_status='completed' AND created_at BETWEEN %s AND %s",
    $last_30_start, $today
));

$case_completion = $total_cases > 0 ? round(($completed_cases / $total_cases) * 100, 1) . '%' : '0%';

// 3. Client Satisfaction: average rating
$avg_rating = $wpdb->get_var($wpdb->prepare(
    "SELECT AVG(rating) FROM {$wpdb->prefix}pixelcode_cases 
     WHERE rating IS NOT NULL AND rating_date BETWEEN %s AND %s",
    $last_30_start, $today
));
$avg_rating = $avg_rating ? round($avg_rating, 1) . '/5' : '0/5';








?>

<div class="p-6">
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h2>
            <div class="flex space-x-3">
                <select id="dashboard-date-range"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 3 months</option>
                    <option value="180">Last 6 months</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>

        <!-- ------------------------------------------------------------------------------------------------------------------------------ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue Card -->
            <div class="bg-gray-100 rounded-xl shadow p-6 hover:shadow-lg transition-shadow duration-300 border">
                <div class='flex items-center justify-between'>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900 mt-1" id="total-revenue">$ <?= $revenue_last ?>
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-gray-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-dollar-sign h-6 w-6">
                            <line x1="12" x2="12" y1="2" y2="22"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------------------------------------------------------------------------------------------------- -->
            <div class="bg-gray-100 rounded-xl shadow p-6 hover:shadow-lg transition-shadow duration-300 border">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cases Completed</p>
                        <p class="text-2xl font-semibold text-gray-900 mt-1" id="cases-completed">
                            <?= $cases_completed_display ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-gray-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-check-circle h-6 w-6">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                    </div>
                </div>
            </div>



            <!-- ---------------------------------------------------------------------------------------------------------------------------------------- -->
            <div class="bg-gray-100 rounded-xl shadow p-6 hover:shadow-lg transition-shadow duration-300 border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Clients</p>
                        <p class="text-2xl font-semibold text-gray-900 mt-1" id="active-clients">
                            <?= $active_cases_display ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-gray-100 text-purple-600"><svg xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-users h-6 w-6">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>
            </div>



            <!-- --------------------------------------------------------------------------------------------------------------------------------------------- -->
            <div class="bg-gray-100 rounded-xl shadow p-6 hover:shadow-lg transition-shadow duration-300 border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">AVG Processing </p>
                        <p class="text-2xl font-semibold text-gray-900 mt-1" id="avg-processing">
                            <?= $avg_processing_display ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-gray-100 text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-clock h-6 w-6">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="bg-gray-100 border rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Monthly Performance</h3>
                <div class="space-y-4">
                    <?php foreach ($months as $month): 
            $month_name = date('M', strtotime($month . '-01'));
            $cases = $monthly_cases[$month];
            $revenue = $monthly_revenue[$month];
            $width = $total_cases > 0 ? ($cases/$total_cases)*100 : 0; 
        ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-900 w-8"><?= $month_name ?></span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2 w-32">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $width ?>%;"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900"><?= $cases ?> cases</div>
                            <div class="text-sm text-gray-500">$<?= number_format($revenue, 2) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="bg-gray-100 border rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Current Case Status</h3>
                <div class="space-y-4">
                    <?php foreach ($case_status_counts as $status): 
            $count = intval($status['total']);
            $percent = $total_cases > 0 ? round(($count / $total_cases) * 100) : 0;
            $color = isset($status_colors[$status['case_status']]) ? $status_colors[$status['case_status']] : 'gray';
        ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full" style="background-color: <?= $color ?>;"></div>
                            <span
                                class="text-sm font-medium text-gray-900"><?= ucfirst($status['case_status']) ?></span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900"><?= $count ?></div>
                            <div class="text-sm text-gray-500"><?= $percent ?>%</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Performance Insights</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Revenue Growth -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-trending-up h-8 w-8 text-green-600"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                            <polyline points="16 7 22 7 22 13"></polyline>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Revenue Growth</p>
                            <p class="text-2xl font-semibold text-green-900"><?= $revenue_growth ?></p>
                        </div>
                    </div>
                    <p class="text-sm text-green-700 mt-2">Monthly revenue from completed cases</p>
                </div>

                <!-- Case Completion -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-file-text h-8 w-8 text-blue-600"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                            <path d="M10 9H8"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-900">Case Completion</p>
                            <p class="text-2xl font-semibold text-blue-900"><?= $case_completion ?></p>
                        </div>
                    </div>
                    <p class="text-sm text-blue-700 mt-2">High success rate for case completion</p>
                </div>

                <!-- Client Satisfaction -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-users h-8 w-8 text-purple-600"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-900">Client Satisfaction</p>
                            <p class="text-2xl font-semibold text-purple-900"><?= $avg_rating ?></p>
                        </div>
                    </div>
                    <p class="text-sm text-purple-700 mt-2">Based on client feedback surveys</p>
                </div>
            </div>

        </div>
    </div>
</div>
<?php