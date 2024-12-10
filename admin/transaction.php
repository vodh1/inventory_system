<?php
require_once 'session_check.php';
require_once '../classes/database.class.php';
require_once '../classes/transaction.class.php';

$db = new Database();
$conn = $db->connect();
$transaction = new Transaction();

// Handle mark as returned request
if (isset($_POST['mark_returned']) && isset($_POST['borrowing_id'])) {
    $borrowing_id = intval($_POST['borrowing_id']);

    try {
        $result = $transaction->markAsReturned($borrowing_id);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Transaction marked as returned successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to mark transaction as returned']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error marking transaction as returned: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch user information (assuming user ID is stored in session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_info = null;

if ($user_id) {
    $sql = "SELECT users.username, department.name AS department, role.name AS role FROM users INNER JOIN department ON department.id = users.department_id INNER JOIN role ON role.id = users.role_id WHERE users.id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Filter and Search Parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch all transactions without pagination
$transactions_result = $transaction->fetchAllTransactions($status_filter, $start_date, $end_date, $search_query);
?>
<?php require_once '../includes/header.php' ?>
<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #dc2626 !important;
        color: white !important;
        border: 1px solid #dc2626;
    }
</style>


<body class="bg-gray-50 flex min-h-screen relative overflow-x-hidden">
    <!-- Sidebar -->
    <?php require_once '../includes/side_bar.php'; ?>
    <!-- Main Container -->
    <div class="flex-1 ml-[16rem] w-[calc(100%-16rem)] transition-all duration-300" id="main-container">
        <!-- Top Navigation -->
        <?php require_once '../includes/top_nav.php' ?>

        <!-- Main Content -->
        <main class="p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8 flex-wrap gap-5">
                <div>
                    <h2 class="text-3xl font-bold mb-1">Transactions</h2>
                    <p class="text-gray-600">Track equipment borrowing and returns</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex justify-between mb-6 flex-wrap gap-5">
                <div class="flex gap-3 flex-wrap">
                    <button class="px-5 py-2 rounded-lg <?php echo $status_filter == 'all' ? 'bg-red-700 text-white' : 'border border-gray-200 hover:bg-gray-50'; ?>"
                        onclick="filterTransactions('all')">All</button>
                    <button class="px-5 py-2 rounded-lg <?php echo $status_filter == 'active' ? 'bg-red-700 text-white' : 'border border-gray-200 hover:bg-gray-50'; ?>"
                        onclick="filterTransactions('active')">Active</button>
                    <button class="px-5 py-2 rounded-lg <?php echo $status_filter == 'pending' ? 'bg-red-700 text-white' : 'border border-gray-200 hover:bg-gray-50'; ?>"
                        onclick="filterTransactions('pending')">Pending</button>
                    <button class="px-5 py-2 rounded-lg <?php echo $status_filter == 'returned' ? 'bg-red-700 text-white' : 'border border-gray-200 hover:bg-gray-50'; ?>"
                        onclick="filterTransactions('returned')">Returned</button>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <input type="date" id="startDate" class="px-3 py-2 border border-gray-200 rounded-lg" value="<?php echo $start_date; ?>">
                    <span class="self-center">to</span>
                    <input type="date" id="endDate" class="px-3 py-2 border border-gray-200 rounded-lg" value="<?php echo $end_date; ?>">
                    <button class="px-5 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800" onclick="filterTransactions('date')">Filter by Date</button>
                </div>
            </div>

            <!-- Transaction List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table id="transactionsTable" class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($transactions_result as $transaction): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($transaction['id']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($transaction['equipment_name']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($transaction['category_name'] ?? 'Uncategorized') ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-md">
                                            <?= htmlspecialchars($transaction['unit_code']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($transaction['borrower_username']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?= htmlspecialchars($transaction['department']) ?></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('M d, Y', strtotime($transaction['borrow_date'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('M d, Y', strtotime($transaction['return_date'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'returned' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $statusClass = $statusClasses[strtolower($transaction['status'])] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                            <?= ucfirst(htmlspecialchars($transaction['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <?php
                                        date_default_timezone_set('Asia/Singapore');
                                        $current_date = date('Y-m-d');
                                        $borrow_date = $transaction['borrow_date'];
                                        if ($transaction['status'] == BorrowStatus::Active->value): ?>
                                            <button class="inline-flex disabled:cursor-not-allowed disabled:text-gray-200 items-center px-3 py-1.5 border border-green-600 rounded-md text-sm font-medium text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                <?= $current_date < $borrow_date ? 'disabled' : '' ?> onclick="markAsReturned(<?= $transaction['id'] ?>)">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mark as Returned
                                            </button>
                                        <?php elseif ($transaction['status'] == BorrowStatus::Pending->value): ?>
                                            <div class="flex justify-center gap-2">
                                                <button class="inline-flex items-center px-3 py-1.5 border border-green-600 rounded-md text-sm font-medium text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 approve-btn"
                                                    data-request-id="<?= $transaction['id'] ?>">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Approve
                                                </button>
                                                <button class="inline-flex items-center px-3 py-1.5 border border-red-600 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 reject-btn"
                                                    data-request-id="<?= $transaction['id'] ?>">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Reject
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500">No action needed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    // Initialize DataTable
                    const transactionsTable = $('#transactionsTable').DataTable({
                        responsive: true,
                        processing: true,
                        pageLength: 10,
                        dom: 'rtip',
                        order: [
                            [0, 'desc']
                        ],
                        columnDefs: [{
                            targets: -1,
                            orderable: false
                        }],
                        language: {
                            info: "Showing _START_ to _END_ of _TOTAL_ transactions",
                            lengthMenu: "Show _MENU_ entries per page",
                            search: "Search transactions:",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            }
                        }
                    });

                    // Function to bind event handlers
                    function bindEventHandlers() {
                        // Bind mark as returned buttons
                        $('.mark-returned-btn').off('click').on('click', function(e) {
                            e.preventDefault();
                            const transactionId = $(this).data('id');
                            markAsReturned(transactionId);
                        });
                        // Handle approve button click
                        $('#transactionsTable').on('click', '.approve-btn', function() {
                            var requestId = $(this).data('request-id');
                            $.ajax({
                                url: '../admin/request.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    approve_borrow_request: true,
                                    request_id: requestId
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        alert(response.message);
                                        location.reload(); // Reload the page to refresh the table
                                    } else {
                                        alert('Error: ' + response.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX Error:', status, error);
                                    alert('An error occurred while processing the request.');
                                }
                            });
                        });

                        // Handle reject button click
                        $('#transactionsTable').on('click', '.reject-btn', function() {
                            var requestId = $(this).data('request-id');
                            if (confirm('Are you sure you want to reject this request?')) {
                                $.ajax({
                                    url: '../admin/request.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        reject_borrow_request: true,
                                        request_id: requestId
                                    },
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            alert(response.message);
                                            location.reload(); // Reload the page to refresh the table
                                        } else {
                                            alert('Error: ' + response.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('AJAX Error:', status, error);
                                        alert('An error occurred while processing the request.');
                                    }
                                });
                            }
                        });
                    }


                    // Initial binding
                    bindEventHandlers();
                });

                function markAsReturned(transactionId) {
                    if (confirm('Are you sure you want to mark this transaction as returned?')) {
                        $.ajax({
                            url: 'transaction.php',
                            type: 'POST',
                            data: {
                                mark_returned: true,
                                borrowing_id: transactionId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    alert('Transaction marked as returned successfully!');
                                    location.reload();
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            error: function() {
                                alert('Error marking transaction as returned. Please try again.');
                            }
                        });
                    }
                }

                // Filter transactions
                function filterTransactions(status) {
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;
                    let url = new URL(window.location.href);

                    // Reset page number when filter changes
                    url.searchParams.set('page', '1');

                    if (status !== 'date') {
                        url.searchParams.set('status', status);
                    }

                    if (startDate && endDate) {
                        url.searchParams.set('start_date', startDate);
                        url.searchParams.set('end_date', endDate);
                    }

                    window.location.href = url.toString();
                }

                function handleSearch(event) {
                    event.preventDefault();
                    const searchQuery = document.getElementById('searchInput').value;
                    let url = new URL(window.location.href);

                    // Reset page number when searching
                    url.searchParams.set('page', '1');
                    url.searchParams.set('search', searchQuery);

                    const statusFilter = document.querySelector('.px-5.py-2.rounded-lg.bg-red-700.text-white');
                    if (statusFilter) {
                        url.searchParams.set('status', statusFilter.textContent.trim().toLowerCase());
                    }

                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;
                    if (startDate && endDate) {
                        url.searchParams.set('start_date', startDate);
                        url.searchParams.set('end_date', endDate);
                    }

                    window.location.href = url.toString();
                }

                // Mobile menu toggle functionality
                const menuToggle = document.getElementById('menuToggle');
                const sidebar = document.getElementById('sidebar');
                const mainContainer = document.getElementById('main-container');

                menuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                    mainContainer.classList.toggle('ml-0');
                    mainContainer.classList.toggle('w-full');
                });

                // Add responsive classes for mobile
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('-translate-x-full');
                    mainContainer.classList.add('ml-0', 'w-full');
                    mainContainer.classList.remove('ml-64', 'w-[calc(100%-16rem)]');
                    menuToggle.classList.remove('hidden');
                }

                // Handle resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.add('-translate-x-full');
                        mainContainer.classList.add('ml-0', 'w-full');
                        mainContainer.classList.remove('ml-64', 'w-[calc(100%-16rem)]');
                        menuToggle.classList.remove('hidden');
                    } else {
                        sidebar.classList.remove('-translate-x-full');
                        mainContainer.classList.remove('ml-0', 'w-full');
                        mainContainer.classList.add('ml-64', 'w-[calc(100%-16rem)]');
                        menuToggle.classList.add('hidden');
                    }
                });

                function openTransactionModal(transactionId) {
                    const modal = document.getElementById('transactionDetailsModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    fetchTransactionDetails(transactionId);
                }

                function closeTransactionModal() {
                    const modal = document.getElementById('transactionDetailsModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function fetchTransactionDetails(transactionId) {
                    const content = document.getElementById('transactionDetailsContent');
                    content.innerHTML = `
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                </div>
            `;

                    fetch(`get_transaction_details.php?id=${transactionId}`)
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 'success' && result.data) {
                                const data = result.data;
                                content.innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold mb-4">Equipment Details</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-4">
                                            <img src="${data.image_path}" alt="${data.equipment_name}" class="w-20 h-20 rounded-lg object-cover">
                                            <div>
                                                <p class="font-medium">${data.equipment_name}</p>
                                                <p class="text-sm text-gray-600">Unit: ${data.unit_code}</p>
                                                <p class="text-sm text-gray-600">Category: ${data.category_name}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600">${data.equipment_description || 'No description available'}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-4">Borrower Information</h4>
                                    <div class="space-y-2">
                                        <p><span class="text-gray-600">Name:</span> ${data.borrower_username}</p>
                                        <p><span class="text-gray-600">Department:</span> ${data.department}</p>
                                        <p><span class="text-gray-600">Email:</span> ${data.borrower_email}</p>
                                        <p><span class="text-gray-600">Contact:</span> ${data.borrower_contact}</p>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <h4 class="font-semibold mb-4">Transaction Details</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-gray-600">Borrow Date</p>
                                            <p class="font-medium">${data.borrow_date}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Return Date</p>
                                            <p class="font-medium">${data.return_date}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Status</p>
                                            <p class="font-medium capitalize">${data.status}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-gray-600">Purpose</p>
                                        <p class="mt-1">${data.purpose || 'No purpose specified'}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                            } else {
                                content.innerHTML = `
                            <div class="text-center text-red-500">
                                <p>Error loading transaction details</p>
                                <p class="text-sm">${result.message || 'Please try again later'}</p>
                            </div>
                        `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            content.innerHTML = `
                        <div class="text-center text-red-500">
                            <p>Error loading transaction details</p>
                            <p class="text-sm">Please try again later</p>
                        </div>
                    `;
                        });
                }

                // Add click event listeners to transaction rows
                document.querySelectorAll('[data-transaction-id]').forEach(row => {
                    row.addEventListener('click', (e) => {
                        if (!e.target.closest('button')) { // Only trigger if not clicking a button
                            const transactionId = e.currentTarget.dataset.transactionId;
                            openTransactionModal(transactionId);
                        }
                    });
                });
            </script>

</body>

</html>