<div class="p-3">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">Borrow Requests</h2>
            <p class="text-gray-600">Manage and track equipment borrow requests</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table id="borrowRequestsTable" class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-w-[200px]">Purpose</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($borrow_requests as $request): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($request['equipment_name']) ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($request['category_name']) ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-md">
                                <?= htmlspecialchars($request['unit_code']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($request['borrower_name']) ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm text-gray-900"><?= htmlspecialchars($request['department']) ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?= date('M d, Y', strtotime($request['borrow_date'])) ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?= date('M d, Y', strtotime($request['return_date'])) ?>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900 max-w-[200px] truncate" title="<?= htmlspecialchars($request['purpose']) ?>">
                                <?= htmlspecialchars($request['purpose']) ?>
                            </p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $statusClass = $statusClasses[strtolower($request['approval_status'])] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                <?= ucfirst(htmlspecialchars($request['approval_status'])) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <?php if ($request['approval_status'] == 'pending'): ?>
                                <div class="flex justify-center gap-2">
                                    <button class="inline-flex items-center px-3 py-1.5 border border-green-600 rounded-md text-sm font-medium text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 approve-btn" 
                                            data-request-id="<?= $request['id'] ?>">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1.5 border border-red-600 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 reject-btn" 
                                            data-request-id="<?= $request['id'] ?>">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                            <?php else: ?>
                                <span class="text-sm text-gray-500">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>