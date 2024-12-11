<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/borrowing.class.php';
require_once '../classes/equipment.class.php';
require_once '../libs/enums.php';

$conn = new Database();
$borrowing = new Borrowing();
$equipment = new Equipment();

// Fetch user information (assuming user ID is stored in session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_info = null;

if ($user_id) {
    $sql = "SELECT users.username, department.name AS department, role.name AS role FROM users INNER JOIN role ON role.id = users.role_id INNER JOIN department ON department.id = users.department_id WHERE users.id = :user_id";
    $stmt = $conn->connect()->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset=" UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowings - Equipment Borrowing System</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.tailwindcss.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#b90303',
                        'primary-hover': '#a00202',
                    }
                }
            }
        }
    </script>
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
</head>

<body class="bg-gray-50">
    <!-- Mobile Navigation -->
    <?php require_once '../includes/mobile_nav.php' ?>

    <div class="flex min-h-screen">
        <!-- Sidebar - Hidden on mobile -->
        <?php require_once '../includes/side_bar.php' ?>

        <!-- Main Content -->
        <div class="flex-1 ml-[16.2rem] p-4 lg:p-5 pb-20 lg:pb-5 w-full lg:max-w-[calc(100%-16rem)]">
            <!-- Navigation 
            <div class="flex items-center gap-5 p-4 lg:p-5 bg-white rounded-lg shadow-sm sticky top-0 z-40 mb-8">
                <div class="flex-1 relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                    <input type="text" placeholder="Search borrowings..." class="w-full py-2 lg:py-3 px-4 pl-10 border border-gray-200 rounded-lg text-sm">
                </div>

            </div>
-->
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm text-gray-600">Active Borrowings</h3>
                    <p class="text-2xl font-medium mt-1" id="activeCount">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm text-gray-600">Pending Requests</h3>
                    <p class="text-2xl font-medium mt-1" id="pendingCount">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm text-gray-600">Completed Borrowings</h3>
                    <p class="text-2xl font-medium mt-1" id="completedCount">0</p>
                </div>
            </div>

            <!-- Borrowings Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden py-4 px-4">
                <table id="borrowingsTable" class="w-full">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Status</th>
                            <th>Borrow Date</th>
                            <th>Expected Return Date</th>
                            <th>Return Date</th>
                            <th>Purpose of Borrowing</th>
                            <th>Time Remaining</th>
                        </tr>
                    </thead>
                    <tbody id="borrowingsList">
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2 mt-6" id="pagination">
                <!-- Pagination links will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Initialize DataTable
        let borrowingsTable;

        // Mobile navigation drawer functionality
        const profileToggle = document.getElementById('profileToggle');
        const profileDrawer = document.getElementById('profileDrawer');
        const closeProfile = document.getElementById('closeProfile');
        const drawerContent = profileDrawer.querySelector('div:last-child');

        profileToggle.addEventListener('click', () => {
            profileDrawer.classList.remove('hidden');
            setTimeout(() => {
                drawerContent.classList.remove('translate-x-full');
            }, 10);
        });

        function closeDrawer() {
            drawerContent.classList.add('translate-x-full');
            setTimeout(() => {
                profileDrawer.classList.add('hidden');
            }, 300);
        }

        closeProfile.addEventListener('click', closeDrawer);
        profileDrawer.addEventListener('click', (e) => {
            if (e.target === profileDrawer) {
                closeDrawer();
            }
        });

        // AJAX request to fetch borrowings
        function fetchBorrowings(page) {
            $.ajax({
                url: 'get_borrowings.php',
                type: 'GET',
                data: {
                    page: page
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    updateBorrowings(data);
                },
                error: function() {
                    alert('An error occurred while fetching borrowings.');
                }
            });
        }

        // Update the borrowings list and pagination
        function updateBorrowings(data) {
            const borrowingsList = document.getElementById('borrowingsList');
            const activeCount = document.getElementById('activeCount');
            const pendingCount = document.getElementById('pendingCount');
            const completedCount = document.getElementById('completedCount');

            // Update stats
            activeCount.textContent = data.active_count;
            pendingCount.textContent = data.pending_count;
            completedCount.textContent = data.completed_count;

            // Clear existing content
            borrowingsList.innerHTML = '';

            // Update borrowings list
            data.borrowings.forEach(borrowing => {
                let dueStatus = '-';
                if (borrowing.status === 'active') {
                    dueStatus = getTimeRemaining(borrowing.expected_return_date);
                }
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="flex gap-4 items-center py-2">
                            <img src="${borrowing.image_path}" 
                                 alt="${borrowing.equipment_name}" 
                                 class="w-16 h-16 rounded-lg object-cover">
                            <div>
                                <h3 class="font-medium text-gray-900">${borrowing.equipment_name}</h3>
                                <p class="text-sm text-gray-600">Unit Code: ${borrowing.unit_code}</p>
                                <span class="inline-block mt-1 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    Borrow #${borrowing.id.toString().padStart(5, '0')}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(borrowing.status)}">
                            ${capitalize(borrowing.status)}
                        </span>
                    </td>
                    <td>${new Date(borrowing.borrow_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</td>
                    <td>${new Date(borrowing.expected_return_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</td>
                    <td>${ borrowing.return_date ? new Date(borrowing.return_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '-'}</td>
                    <td>${borrowing.purpose}</td>
                    <td class="${getTimeRemainingClass(borrowing.expected_return_date)}">${dueStatus}</td>
                `;
                borrowingsList.appendChild(row);
            });

            // Initialize or refresh DataTable
            if (!borrowingsTable) {
                borrowingsTable = $('#borrowingsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],
                    order: [
                        [2, 'desc']
                    ], // Sort by borrow date by default
                    language: {
                        search: "Search borrowings:",
                        lengthMenu: "Show _MENU_ borrowings per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ borrowings",
                        paginate: {
                            first: '<i class="bx bx-chevrons-left"></i>',
                            last: '<i class="bx bx-chevrons-right"></i>',
                            next: '<i class="bx bx-chevron-right"></i>',
                            previous: '<i class="bx bx-chevron-left"></i>'
                        }
                    },
                    drawCallback: function() {
                        // Re-apply Tailwind classes to elements
                        $('.dataTables_wrapper select, .dataTables_wrapper input[type="search"]').addClass('bg-white');
                    }
                });
            } else {
                borrowingsTable.clear().draw();
                borrowingsTable.rows.add($(borrowingsList).find('tr')).draw();
            }
        }

        // Helper functions
        function getStatusClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-700',
                'pending': 'bg-yellow-100 text-yellow-700',
                'returned': 'bg-gray-100 text-gray-700'
            };
            return classes[status] || '';
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getTimeRemaining(returnDate) {
            const now = new Date();
            const returnDateObj = new Date(returnDate);
            const days = Math.ceil((returnDateObj - now) / (1000 * 60 * 60 * 24));
            if (days > 0) {
                return `${days} days remaining`;
            } else if (days == 0) {
                return 'Due Today';
            } else {
                return `${Math.abs(days)} days overdue`;
            }
        }

        function getTimeRemainingClass(returnDate) {
            const now = new Date();
            const returnDateObj = new Date(returnDate);
            return now >= returnDateObj ? 'text-red-600' : 'text-green-600';
        }

        // Initial fetch
        fetchBorrowings(1);
    </script>
</body>

</html>