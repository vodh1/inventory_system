<div class="p-6">
    <!-- Dashboard Header -->
    <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold dark:text-white mb-1">Dashboard</h2>
            <p class="text-gray-600 dark:text-gray-400">Welcome to your dashboard</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-box text-blue-500 dark:text-blue-300"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm">Total Items</h3>
                    <p class="text-2xl font-semibold text-black dark:text-white"><?php echo $total_items; ?></p>
                </div>
            </div>
        </div>

        <!-- Items Borrowed -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-hand-holding text-green-500 dark:text-green-300"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm">Items Borrowed</h3>
                    <p class="text-2xl font-semibold text-black dark:text-white"><?php echo $items_borrowed; ?></p>
                </div>
            </div>
        </div>

        <!-- Overdue Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-clock text-red-500 dark:text-red-300"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm">Overdue Items</h3>
                    <p class="text-2xl font-semibold text-black dark:text-white"><?php echo $overdue_items; ?></p>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-users text-purple-500 dark:text-purple-300"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm">Active Users</h3>
                    <p class="text-2xl font-semibold text-black dark:text-white"><?php echo $active_users; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Borrowing Trends Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-black dark:text-white">Borrowing Trends</h3>
            <div class="relative h-80">
                <canvas id="borrowingTrendsChart"></canvas>
            </div>
        </div>

        <!-- Equipment Categories Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-black dark:text-white">Equipment by Category</h3>
            <div class="relative h-80">
                <canvas id="equipmentCategoriesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-black dark:text-white">Recent Activities</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Equipment</th>
                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Borrower</th>
                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Department</th>
                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Action</th>
                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activities as $activity): ?>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3 text-black dark:text-white"><?php echo htmlspecialchars($activity['equipment_name']); ?></td>
                            <td class="py-3 text-black dark:text-white"><?php echo htmlspecialchars($activity['borrower_username']); ?></td>
                            <td class="py-3 text-black dark:text-white"><?php echo htmlspecialchars($activity['department']); ?></td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $activity['activity_type'] === 'borrowed' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst($activity['activity_type']); ?>
                                </span>
                            </td>
                            <td class="py-3 text-black dark:text-white"><?php echo date('M d, Y', strtotime($activity['borrow_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>